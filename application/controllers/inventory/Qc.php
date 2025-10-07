<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Qc extends PS_Controller
{
  public $menu_code = 'ICODQC';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'PICKPACK';
	public $title = 'ตรวจสินค้า';
  public $filter;
  public $segment;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/qc';
    $this->load->model('inventory/qc_model');
    $this->load->model('orders/orders_model');
    $this->load->model('orders/order_state_model');
    $this->load->model('masters/package_model');
    $this->load->helper('warehouse');
    $this->load->helper('channels');
    $this->load->helper('package');
    $this->load->helper('sender');
    $this->load->helper('order');
  }

  public function index()
  {
    $this->title = "รายการรอตรวจ";

    $filter = array(
      'code' => get_filter('code', 'ic_code', ''),
      'customer' => get_filter('customer', 'ic_customer', ''),
      'user' => get_filter('user', 'ic_user', 'all'),
      'role' => get_filter('role', 'ic_role', 'all'),
      'channels'  => get_filter('channels', 'ic_channels', 'all'),
      'shop_id' => get_filter('shop_id', 'ic_shop_id', 'all'),
      'from_date' => get_filter('from_date', 'ic_from_date', ''),
      'to_date' => get_filter('to_date', 'ic_to_date', ''),
      'is_cancled' => get_filter('is_cancled', 'ic_is_cancled', 'all'),
      'id_sender' => get_filter('id_sender', 'ic_id_sender', 'all')
    );

    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      //--- แสดงผลกี่รายการต่อหน้า
      $perpage = get_rows();
      $state = 5; //---- รอตรวจ
      $this->segment  = 4; //-- url segment
      $rows = $this->qc_model->count_rows($filter, $state);
      $init = pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
      $filter['orders'] = $this->qc_model->get_list($filter, $state, $perpage, $this->uri->segment($this->segment));
      $this->pagination->initialize($init);
      $this->load->view('inventory/qc/qc_list', $filter);
    }
  }


  public function ship_order_shopee($reference)
  {
    $sc = TRUE;
    $logs = [];
    $shipment = NULL;
    $tracking = NULL;
    $this->load->library('wrx_shopee_api');
    $order = $this->orders_model->get_order_by_reference($reference);

    if( ! empty($order))
    {
      $status = $this->wrx_shopee_api->get_order_status($reference, $order->shop_id);
      $logs['status'] = $status;

      if($status === 'CANCELLED')
      {
        $sc = FALSE;
        $this->error = "ออเดอร์ถูกยกเลิกในระบบ Shopee แล้ว";
      }

      if($sc === TRUE)
      {
        $pickup_data = $this->wrx_shopee_api->get_shipping_param($reference, $order->shop_id);

        if(empty($pickup_data))
        {
          sleep(1);  //--- wait 1 second and retry to request again

          $pickup_data = $this->wrx_shopee_api->get_shipping_param($reference, $order->shop_id);

          if(empty($pickup_data))
          {
            $sc = FALSE;
            $this->error = "Cannot get param from api";
          }
        }

        $logs['shipping_param'] = $pickup_data;
      }


      if($sc === TRUE && $status === 'READY_TO_SHIP')
      {
        //--- ship order
        if( ! empty($pickup_data))
        {
          if( ! $this->wrx_shopee_api->ship_order($reference, $pickup_data, $order->shop_id))
          {
            $sc = FALSE;
            $this->error = $this->wrx_shopee_api->error;
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Pickup data not found";
        }

        $logs['ship_order'] = $sc === TRUE ? 'success' : $this->error;
      }

      //--- get tracking_number
      if($sc === TRUE)
      {
        $tracking_number = $this->wrx_shopee_api->get_tracking_number($reference, $order->shop_id);

        if(empty($tracking_number))
        {
          $retry = 5;

          while($retry > 0)
          {
            sleep(1);

            $tracking_number = $this->wrx_shopee_api->get_tracking_number($reference, $order->shop_id);

            if( ! empty($tracking_number))
            {
              break;
            }

            $retry--;
          }

          if(empty($tracking_number))
          {
            $sc = FALSE;
            $this->error = "Cannot get tracking number after try {$retry} times";
          }
        }

        if( ! empty($tracking_number))
        {
          $tracking = $tracking_number;
          $this->orders_model->update($order->code, ['shipping_code' => $tracking_number]);
        }

        $logs['get_tracking'] = $sc === TRUE ? $tracking : $this->error;
      }


      //--- cereate shipping document
      if($sc === TRUE)
      {
        if( ! $this->wrx_shopee_api->create_shipping_document($reference, $tracking, $order->shop_id))
        {
          $sc = FALSE;
          $this->error = $this->wrx_shopee_api->error;
        }

        $logs['create'] = $sc === TRUE ? 'success' : $this->error;
      }

      //--- get create document result
      if($sc === TRUE)
      {
        if( ! $this->wrx_shopee_api->shipping_document_result($reference, $order->shop_id))
        {
          sleep(1);

          if( ! $this->wrx_shopee_api->shipping_document_result($reference, $order->shop_id))
          {
            $sc = FALSE;
            $this->error = $this->wrx_shopee_api->error;
          }
        }

        $logs[] = $sc === TRUE ? 'success' : $this->error;
      }

      //--- download_shipping_document
      if($sc === TRUE)
      {
        $res = $this->wrx_shopee_api->shipping_document_download($reference, $order->shop_id);

        if( ! empty($res))
        {
          $shipment = $res;
        }
        else
        {
          $sc = FALSE;
          $this->error = "Failed to download shipping label";
        }

        $logs[] = $sc === TRUE ? $res : $this->error;
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Order {$reference} not found";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $shipment,
      'logs' => $logs
    );

    echo json_encode($arr);
  }


  public function ship_order_tiktok($reference)
  {
    $sc = TRUE;
    $shipment = NULL;
    $this->load->library('wrx_tiktok_api');
    $order = $this->orders_model->get_order_by_reference($reference);

    if( ! empty($order))
    {
      $ds = $this->wrx_tiktok_api->get_order_detail($reference, $order->shop_id);

      if( ! empty($ds))
      {
        if($ds->order_status == '140')
        {
          $sc = FALSE;
          $this->error = "ออเดอร์ถูกยกเลิกในระบบ TIKTOK แล้ว";
        }

        if($sc === TRUE)
        {
          //--- update package id , tracking number
          if( ! empty($ds->package_id) && ! empty($ds->tracking_number))
          {
            $arr = array(
              'shipping_code' => $ds->tracking_number,
              'package_id' => $ds->package_id
            );

            $this->orders_model->update($order->code, $arr);
          }
        }

        //---  ship package
        if($sc === TRUE)
        {
          if( ! $this->wrx_tiktok_api->ship_package($ds->package_id, $order->shop_id))
          {
            $sc = FALSE;
            $this->error = "Failed to ship package : ".$this->wrx_tiktok_api->error;
          }
        }

        if($sc === TRUE)
        {
          $shipment = $this->wrx_tiktok_api->get_shipping_label($ds->package_id, $order->shop_id);

          if( ! empty($shipment))
          {
            if( empty($shipment->trackingNumber))
            {
              sleep(1);
              $shipment = $this->wrx_tiktok_api->get_shipping_label($ds->package_id, $order->shop_id);
            }

            if( empty($shipment->trackingNumber))
            {
              sleep(1);
              $shipment = $this->wrx_tiktok_api->get_shipping_label($ds->package_id, $order->shop_id);
            }

            if( empty($shipment->trackingNumber))
            {
              sleep(1);
              $shipment = $this->wrx_tiktok_api->get_shipping_label($ds->package_id, $order->shop_id);
            }

            if( ! empty($shipment))
            {
              if(empty($ds->tracking_number) && ! empty($shipment->trackingNumber))
              {
                $this->orders_model->update($order->code, ['shipping_code' => $shipment->trackingNumber]);
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = $this->wrx_tiktok_api->error;
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = $this->wrx_tiktok_api->error;
          }
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่สามารถดึงข้อมูลออเดอร์จาก API ได้";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Order {$reference} not found";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $shipment
    );

    echo json_encode($arr);
  }


  public function ship_order_lazada($reference)
  {
    $sc = TRUE;
    $shipment = NULL;
    $this->load->library('wrx_lazada_api');
    $order = $this->orders_model->get_order_by_reference($reference);

    if( ! empty($order))
    {
      $status = $this->wrx_lazada_api->get_order_status($reference, $order->shop_id);

      $logs['status'] = $status;

      if($status === 'canceled')
      {
        $sc = FALSE;
        $this->error = "ออเดอร์ถูกยกเลิกในระบบ Lazada แล้ว";
      }

      if($sc === TRUE)
      {
        $order_item_ids = $this->wrx_lazada_api->get_order_item_id($reference, $order->shop_id);

        if( ! empty($order_item_ids))
        {
          //---- change order status to packed and retrive pack data include tracking number
          $pk = $this->wrx_lazada_api->packed($reference, $order_item_ids, $order->shop_id);
          $packages = [];

          if( ! empty($pk))
          {
            foreach($pk as $p)
            {
              $packages[] = array('packageID' => $p->package_id);
              //---- update tracking number
              if( ! empty($p->tracking_number))
              {
                $this->orders_model->update($order->code, ['shipping_code' => $p->tracking_number, 'package_id' => $p->package_id]);
              }
            }

            //---- ready to ship order
            if( ! empty($packages))
            {
              if($this->wrx_lazada_api->ship_package($packages, $order->shop_id) === TRUE)
              {
                //---- download document
                $data = $this->wrx_lazada_api->get_shipping_label($packages, $order->shop_id);

                if( ! empty($data))
                {
                  $shipment = $data;
                }
                else
                {
                  $sc = FALSE;
                  $this->error = "Cannt get file from api";
                }
              }
              else
              {
                $sc = FALSE;
                $this->error = "Failed to set order status to ready to ship : {$this->wrx_lazada_api->error}";
              }
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "Failed to set ordet status to packed";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Cannot Get Order Item ID from Lazada";
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Order {$reference} not found";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $shipment
    );

    echo json_encode($arr);
  }


  public function ship_order_porlor($code)
  {
    $sc = TRUE;
    $shipment = NULL;
    $this->wms = $this->load->database('wms', TRUE);
    $this->load->model('address/address_model');
    $this->load->library('porlor_api');
    $order = $this->orders_model->get($code);

    if( ! empty($order))
    {
      $addr = $this->address_model->get_shipping_detail($order->id_address);

      if( ! empty($addr))
      {
        $packages = [];

        $boxes = $this->qc_model->get_box_list($order->code);

        if( ! empty($boxes))
        {
          foreach($boxes as $box)
          {
            if($box-> qty > 0)
            {
              $packages[] = (object) array(
                'order_code' => $box->order_code,
                'box_code' => $box->code,
                'box_id' => $box->id,
                'package_name' => $box->name,
                'package_width' => intval($box->width),
                'package_length' => intval($box->length),
                'package_height' => intval($box->height),
                'package_size' => round($box->width + $box->length + $box->height),
                'receiver' => $addr->name,
                'address' => $addr->address,
                'sub_district' => $addr->sub_district,
                'district' => $addr->district,
                'province' => $addr->province,
                'postcode' => $addr->postcode,
                'phone' => $addr->phone,
              );
            }
          }

          if( ! empty($packages))
          {
            $ds = $this->porlor_api->create_parcels($order->code, $packages);

            if( ! empty($ds))
            {
              $tracking_no = NULL;

              foreach($ds as $rs)
              {
                if( ! empty($rs->awb_code) && ! empty($rs->item_sku))
                {
                  $this->qc_model->update_box($rs->item_sku, ['tracking_no' => $rs->awb_code]);
                  $tracking_no = $rs->awb_code;
                }
              }

              if( ! empty($tracking_no))
              {
                $this->orders_model->update($order->code, ['shipping_code' => $tracking_no]);
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "Failed to create parcels : ".$this->porlor_api->error;
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "No package data found !";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "No packing package found !";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่พบที่อยู่จัดส่ง";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Order {$code} not found";
    }

    $this->_response($sc);
  }


  public function ship_order_spx($code)
  {
    $sc = TRUE;
    $shipment = NULL;
    $this->wms = $this->load->database('wms', TRUE);
    $this->load->model('address/address_model');
    $this->load->library('wrx_spx_api');

    $order = $this->orders_model->get($code);
    $batch_no = NULL;
    $shipment = NULL;

    if( ! empty($order))
    {
      $batch = $this->qc_model->get_spx_batch($code);

      if( ! empty($batch))
      {
        $batch_no = $batch->batch_no;
      }
      else
      {
        $addr = $this->address_model->get_shipping_detail($order->id_address);

        if(empty($addr))
        {
          $sc = FALSE;
          $this->error = "ไม่พบที่อยู่จัดส่ง";
        }

        if($sc === TRUE)
        {
          $addr->province = parseProvince($addr->province);
          $addr->sub_district = parseSubDistrict($addr->sub_district, $addr->province);
          $addr->district = parseDistrict($addr->district, $addr->province);
          $addr->phone = parsePhoneNumber($addr->phone, 10);

          $sender = (object) [
            'name' => getConfig('SPX_SENDER_NAME'),
            'phone' => getConfig('SPX_SENDER_PHONE'),
            'address' => getConfig('SPX_SENDER_ADDRESS'),
            'state' => getConfig('SPX_SENDER_STATE'),
            'city' => getConfig('SPX_SENDER_CITY'),
            'district' => getConfig('SPX_SENDER_DISTRICT'),
            'postcode' => getConfig('SPX_SENDER_POST_CODE'),
            'weight' => floatval(getConfig('SPX_DEFAULT_WEIGHT'))
          ];
        }

        $packages = [];

        if($sc === TRUE)
        {
          $boxes = $this->qc_model->get_box_list($order->code);

          if(empty($boxes))
          {
            $sc = FALSE;
            $sc = FALSE;
            $this->error = "No packing package found !";
          }
          else
          {
            foreach($boxes as $box)
            {
              if($box->qty > 0)
              {
                $cod = $order->role === 'S' ? ($order->payment_role == 4 ? 1 : 0) : 0;
                $cod_amount = $cod === 1 ? ($order->cod_amount == 0 ? $doc_total : $order->cod_amount) : 0.00;
                $packages[] = array(
                  'order_id' => $box->code,
                  'sender_info' => [
                    'sender_state' => $sender->state,
                    'sender_city' => $sender->city,
                    'sender_district' => $sender->district,
                    'sender_post_code' => $sender->postcode,
                    'sender_name' => $sender->name,
                    'sender_phone' => $sender->phone,
                    'sender_email' => "",
                    'sender_detail_address' => $sender->address
                  ],
                  'fulfillment_info' => [
                    'cod_collection' => $cod,
                    'cod_amount' => intval($cod_amount),
                    'collect_type' => 1
                  ],
                  'deliver_info' => [
                    'deliver_state' => $addr->province,
                    'deliver_city' => $addr->district,
                    'deliver_district' => $addr->sub_district,
                    'deliver_post_code' => $addr->postcode,
                    'deliver_name' => $addr->name,
                    'deliver_phone' => $addr->phone,
                    'deliver_detail_address' => $addr->address
                  ],
                  'parcel_info' => [
                    'parcel_weight' => $sender->weight,
                    'parcel_length' => intval($box->length),
                    'parcel_width' => intval($box->width),
                    'parcel_height' => intval($box->height),
                    'parcel_item_name' => 'เสื้อผ้า',
                    'parcel_item_quantity' => intval($box->qty),
                    'express_insured_value' => NULL
                  ]
                );
              }
            } // end foreach boxes
          }
        }

        if($sc === TRUE)
        {
          if(empty($packages))
          {
            $sc = FALSE;
            $this->error = "No package data found !";
          }
          else
          {
            $ds = $this->wrx_spx_api->create_parcels($order->code, $packages);

            if(empty($ds))
            {
              $sc = FALSE;
              $this->error = "Failed to create parcels : ".$this->wrx_spx_api->error;
            }
            else
            {
              $batch_no = $ds->batch_no;

              if( ! $this->qc_model->update_spx_batch_no($order->code, $ds->batch_no))
              {
                $sc = FALSE;
                $this->error = "Failed to update spx batch no";
              }
              else
              {
                $tracking_no = NULL;

                if( ! empty($ds->orders))
                {
                  foreach($ds->orders as $rs)
                  {
                    if( ! empty($rs->tracking_no) && ! empty($rs->order_id))
                    {
                      $this->qc_model->update_box($rs->order->id, ['tracking_no' => $rs->tracking_no]);
                      $tracking_no = $rs->tracking_no;
                    }
                  }

                  if( ! empty($tracking_no))
                  {
                    $this->orders_model->update($order->code, ['shipping_code' => $tracking_no]);
                  }
                }
              }
            }
          }
        } // $sc = TRUE; packages
      } // ! empty batch_no

      if($sc === TRUE)
      {
        if( ! empty($batch_no))
        {
          $res = $this->wrx_spx_api->get_shipping_label($code, $batch_no);

          if( ! empty($res))
          {
            $shipment = $res;
          }
          else
          {
            $sc = FALSE;
            $this->error = "Failed to download shipping label";
          }
        }
        else
        {
          $sc = FALSE;
          $ths->error = "Cannot get spx order batch no";
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Order {$code} not found";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $shipment
    );

    echo json_encode($arr);
  }


  public function view_process()
  {
    $this->title = "รายการกำลังตรวจ";

    $filter = array(
      'code' => get_filter('code', 'ic_code', ''),
      'customer' => get_filter('customer', 'ic_customer', ''),
      'user' => get_filter('user', 'ic_user', ''),
      'role' => get_filter('role', 'ic_role', 'all'),
      'channels'  => get_filter('channels', 'ic_channels', 'all'),
      'shop_id' => get_filter('shop_id', 'ic_shop_id', 'all'),
      'from_date' => get_filter('from_date', 'ic_from_date', ''),
      'to_date' => get_filter('to_date', 'ic_to_date', ''),
      'is_cancled' => get_filter('is_cancled', 'ic_is_cancled', 'all'),
      'id_sender' => get_filter('id_sender', 'ic_id_sender', 'all')
    );

    if($this->input->post('search'))
    {
      redirect($this->home . '/view_process');
    }
    else
    {
      //--- แสดงผลกี่รายการต่อหน้า
      $perpage = get_rows();

      $state = 6; //---- รอตรวจ
      $this->segment  = 4; //-- url segment
      $rows = $this->qc_model->count_rows($filter, $state);
      $init = pagination_config($this->home.'/view_process/', $rows, $perpage, $this->segment);
      $filter['orders'] = $this->qc_model->get_list($filter, $state, $perpage, $this->uri->segment($this->segment));
      $this->pagination->initialize($init);
      $this->load->view('inventory/qc/qc_view_process_list', $filter);
    }
  }


  public function close_order()
  {
    $sc = TRUE;
    $code = $this->input->post('order_code');
    $state = $this->orders_model->get_state($code);
    if($state == 6)
    {
      $arr = array(
        'order_code' => $code,
        'state' => 7,
        'update_user' => get_cookie('uname')
      );

      if($this->orders_model->change_state($code, 7))
      {
        $this->order_state_model->add_state($arr);
      }
    }
    else
    {
      $sc = FALSE;
      $message = 'ไม่สามารถปิดออเดอร์ได้ เนื่องจากสถานะออเดอร์ได้ถูกเปลี่ยนไปแล้ว';
    }

    echo $sc === TRUE ? 'success' : $message;
  }


  public function save_qc()
  {
    $sc = TRUE;

    $data = json_decode($this->input->post('data'));

    if( ! empty($data))
    {
      if( ! empty($data->order_code))
      {
        if( ! empty($data->rows))
        {
          $this->load->model('inventory/buffer_model');

          $this->db->trans_begin();

          foreach($data->rows as $row)
          {
            $qty = $row->qty;

            $details = $this->orders_model->get_unvalid_qc_detail($data->order_code, $row->product_code);

            if( ! empty($details))
            {
              foreach($details as $detail)
              {
                if($qty > 0)
                {
                  $Qty = $qty >= $detail->qty ? $detail->qty : $qty; //-- 3
                  $bufferQty = $this->buffer_model->get_sum_buffer_product($detail->order_code, $detail->product_code, $detail->id); //--- 5
                  $qcQty = $this->qc_model->get_sum_qty($detail->order_code, $detail->product_code, $detail->id); //-- 2
                  //--- ยอดที่จัดมาต้องน้อยกว่า หรือ เท่ากับยอดที่สั่ง
                  //--- ถ้ามากกว่าให้ใช้ยอดที่สั่งในการตรวจสอบ

                  //--- ยอดที่จะบันทึกตรวจต้องรวมกันแล้วไม่เกินยอดที่จัดและต้องไม่เกินยอดสั่ง
                  $updateQty = $qcQty + $Qty; //--- 2 + 3

                  if($updateQty > $bufferQty)
                  {
                    $sc = FALSE;
                    $this->error = $detail->product_code.' ยอดตรวจเกินยอดจัดหรือยอดสั่ง';
                  }

                  //--- update ยอดตรวจ
                  if( ! $this->qc_model->update_checked($data->order_code, $detail->product_code, $data->id_box, $Qty, $detail->id))
                  {
                    $sc = FALSE;
                    $this->error = $detail->product_code.' บันทึกยอดตรวจไม่สำเร็จ';
                  }

                  $qty = $qty - $Qty;

                  if($detail->qty == $updateQty)
                  {
                    $this->orders_model->valid_qc($detail->id);
                  }
                }
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "Order item {$row->product_code} not exists";
            }
          } //--- end foreach

          if($sc === TRUE)
          {
            $this->qc_model->drop_zero_qc($data->order_code);
          }

          if($sc === TRUE)
          {
            $this->db->trans_commit();
          }
          else
          {
            $this->db->trans_rollback();
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "No item found";
        }
      }
      else
      {
        $sc = FALSE;
        $this->eror = "Order number not found";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing required parameter";
    }

    echo $sc == TRUE ? 'success' : $this->error;
  }


  public function do_qc()
  {
    $sc = TRUE;
    $ds = json_decode($this->input->post('data'));
    $result = array();

    if( ! empty($ds))
    {
      if( ! empty($ds->order_code))
      {
        if($ds->qty > 0)
        {
          $this->load->model('inventory/buffer_model');

          $details = $this->orders_model->get_unvalid_qc_detail($ds->order_code, $ds->product_code);

          if( ! empty($details))
          {
            $qty = $ds->qty;

            $this->db->trans_begin();

            foreach($details as $detail)
            {
              if($sc === TRUE)
              {
                if($qty > 0)
                {
                  $Qty = $qty >= $detail->qty ? $detail->qty : $qty; //-- 3
                  $bufferQty = $this->buffer_model->get_sum_buffer_product($detail->order_code, $detail->product_code, $detail->id); //--- 5
                  $qcQty = $this->qc_model->get_sum_qty($detail->order_code, $detail->product_code, $detail->id); //-- 2
                  //--- ยอดที่จัดมาต้องน้อยกว่า หรือ เท่ากับยอดที่สั่ง
                  //--- ถ้ามากกว่าให้ใช้ยอดที่สั่งในการตรวจสอบ

                  //--- ยอดที่จะบันทึกตรวจต้องรวมกันแล้วไม่เกินยอดที่จัดและต้องไม่เกินยอดสั่ง
                  $updateQty = $qcQty + $Qty; //--- 2 + 3

                  if($updateQty > $bufferQty)
                  {
                    $sc = FALSE;
                    $this->error = $detail->product_code.' ยอดตรวจเกินยอดจัดหรือยอดสั่ง';
                  }

                  //--- update ยอดตรวจ
                  if($sc === TRUE)
                  {
                    if( ! $this->qc_model->update_checked($ds->order_code, $detail->product_code, $ds->id_box, $Qty, $detail->id))
                    {
                      $sc = FALSE;
                      $this->error = $detail->product_code.' บันทึกยอดตรวจไม่สำเร็จ';
                    }

                    if($sc === TRUE)
                    {
                      $qty = $qty - $Qty;

                      $result[] = array(
                        'detail_id' => $detail->id,
                        'qty' => $Qty
                      );

                      if($detail->qty == $updateQty)
                      {
                        $this->orders_model->valid_qc($detail->id);
                      }
                    }
                  }
                }
              }
            }

            if($sc === TRUE)
            {
              $this->db->trans_commit();
            }
            else
            {
              $this->db->trans_rollback();
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "ไม่พบรายการตรวจสินค้าที่ระบุ";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "จำนวนต้องมากกว่า 0";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Order Number not found";
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'result' => $sc === TRUE ? $result : NULL
    );

    echo json_encode($arr);
  }


  public function is_cancel($reference, $channels, $shop_id)
  {
    $is_cancel = FALSE;

    if($channels == '0009')
    {
      $this->load->library('wrx_tiktok_api');

      $order_status = $this->wrx_tiktok_api->get_order_status($reference, $shop_id);

      if($order_status == '140')
      {
        $is_cancel = TRUE;
      }
    }

    if($channels == 'SHOPEE')
    {
      $this->load->library('wrx_shopee_api');

      $order_status = $this->wrx_shopee_api->get_order_status($reference, $shop_id);

      if($order_status == 'CANCELLED')
      {
        $is_cancel = TRUE;
      }
    }

    if($channels == 'LAZADA')
    {
      $this->load->library('wrx_lazada_api');

      $order_status = $this->wrx_lazada_api->get_order_status($reference, $shop_id);

      if($order_status == 'canceled' OR $order_status == 'CANCELED' OR $order_status == 'Canceled')
      {
        $is_cancel = TRUE;
      }
    }

    return $is_cancel;
  }


  public function process($code, $view = NULL)
  {
    $this->load->model('masters/customers_model');
    $this->load->model('masters/channels_model');
    $this->load->model('inventory/buffer_model');

    $is_cancel = FALSE;

    $order = $this->orders_model->get($code);

    if( ! empty($order))
    {
      if( ! $is_cancel && ! empty($order->reference) && ($order->channels_code == '0009' OR $order->channels_code == 'SHOPEE' OR $order->channels_code == 'LAZADA'))
      {
        $is_cancel = $this->is_cancel($order->reference, $order->channels_code, $order->shop_id);
      }

      if( ! $is_cancel)
      {
        if($order->is_cancled == 1)
        {
          $this->orders_model->update($order->code, ['is_cancled' => 0]);
        }

        $state = $this->orders_model->get_state($code);

        if($state == 5)
        {
          $rs = $this->orders_model->change_state($code, 6);

          if($rs)
          {
            $arr = array(
              'order_code' => $code,
              'state' => 6,
              'update_user' => get_cookie('uname')
            );

            $this->order_state_model->add_state($arr);
            $order->state = 6;
          }
        }

        $order->customer_name = $this->customers_model->get_name($order->customer_code);
        $order->channels_name = $this->channels_model->get_name($order->channels_code);
        $order->warehouse_name = warehouse_name($order->warehouse_code);

        $barcode_list = array();

        $uncomplete = $this->qc_model->get_in_complete_list($code);

        if(!empty($uncomplete))
        {
          foreach($uncomplete as $rs)
          {
            $barcode = $this->get_barcode($rs->product_code);
            $rs->barcode = empty($barcode) ? $rs->product_code : $barcode;
            $bc = new stdClass();
            $bc->barcode = md5($rs->barcode);
            $bc->product_code = $rs->product_code;
            $barcode_list[] = $bc;
            $arr = array(
              'order_code' => $code,
              'product_code' => $rs->product_code,
              'is_count' => $rs->is_count
            );

            $rs->from_zone = $this->get_prepared_from_zone($arr);
          }
        }

        $complete = $this->qc_model->get_complete_list($code);

        if(!empty($complete))
        {
          foreach($complete as $rs)
          {
            $barcode = $this->get_barcode($rs->product_code);
            $rs->barcode = empty($barcode) ? $rs->product_code : $barcode;
            $bc = new stdClass();
            $bc->barcode = md5($rs->barcode);
            $bc->product_code = $rs->product_code;
            $barcode_list[] = $bc;

            $arr = array(
              'order_code' => $code,
              'product_code' => $rs->product_code,
              'is_count' => $rs->is_count
            );

            $rs->from_zone = $this->get_prepared_from_zone($arr);
          }
        }

        $ds = array(
          'order' => $order,
          'uncomplete_details' => $uncomplete,
          'complete_details' => $complete,
          'barcode_list' => $barcode_list,
          'box_list' => $this->qc_model->get_box_list($code),
          'qc_qty' => $this->qc_model->total_qc($code),
          'all_qty' => $this->get_sum_qty($code),
          'finished' => empty($uncomplete) ? TRUE : FALSE,
          'disActive' => $order->state == 6 ? '' : 'disabled',
          'allow_input_qty' => getConfig('ALLOW_QC_INPUT_QTY') == 1 ? TRUE : FALSE
        );

        if( ! empty($view))
        {
          $ds['title'] = $order->code;
          $this->load->view('inventory/qc/qc_process_mobile', $ds);
        }
        else
        {
          $this->load->view('inventory/qc/qc_process', $ds);
        }
      }
      else
      {
        $this->orders_model->update($code, ['is_cancled' => 1]);
        $this->load->view('inventory/prepare/order_cancelled', ['order' => $order]);
      }
    }
    else
    {
      $this->error_page();
    }
  }


  public function get_complete_item($id_order_detail)
  {
    $sc = TRUE;
    $rs = $this->qc_model->get_complete_item($id_order_detail);

    if( ! empty($rs))
    {
      $this->load->model('inventory/buffer_model');

      $rs->qty = round($rs->order_qty, 2);
      $rs->barcode = $this->get_barcode($rs->product_code);
      $rs->prepared = round($rs->prepared, 2);
      $rs->qc = round($rs->qc, 2);

      $arr = array(
        'order_code' => $rs->order_code,
        'product_code' => $rs->product_code,
        'order_detail_id' => $rs->id,
        'is_count' => $rs->is_count
      );

      $rs->from_zone = $this->get_prepared_from_zone($arr);
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบรายการที่ครบแล้ว : {$id_order_detail}";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $sc === TRUE ? $rs : NULL
    );

    echo json_encode($arr);
  }


  public function get_barcode($item_code)
  {
    $this->load->model('masters/products_model');
    return $this->products_model->get_barcode($item_code);
  }


  public function get_sum_qty($code)
  {
    $this->load->model('inventory/prepare_model');

    $order_qty = $this->orders_model->get_order_total_qty($code);
  	$prepared = $this->prepare_model->get_total_prepared($code);

  	return $order_qty < $prepared ? $order_qty : $prepared;
  }


  public function get_prepared_from_zone(array $ds = array())
  {
    $label = "ไม่พบข้อมูล";

    if( ! empty($ds))
    {
      if( ! empty($ds['is_count']))
      {
        $buffer = $this->buffer_model->get_prepared_from_zone($ds['order_code'], $ds['product_code']);

        if( ! empty($buffer))
        {
          $label = "";

          foreach($buffer as $rs)
          {
            $label .= $rs->name.' : '.number($rs->qty).'<br/>';
          }
        }
        else
        {
          $label = "ไม่พบข้อมูล";
        }
      }
      else
      {
        $label = "ไม่นับสต็อก";
      }
    }

  	return $label;
  }


  public function get_box()
  {
    $box_id = FALSE;
    $box_no = 0;
    $code = $this->input->get('order_code');
    $barcode = $this->input->get('barcode');
    $box = $this->qc_model->get_box($code, $barcode);

    if( ! empty($box))
    {
      $box_id = $box->id;
      $box_no = $box->box_no;
    }
    else
    {
      //--- insert new box
      $box_no = $this->qc_model->get_last_box_no($code) + 1;
      $box_id = $this->qc_model->add_new_box($code, $barcode, $box_no);
    }

    $arr = array(
      'status' => $box_id === FALSE ? 'failed' : 'success',
      'message' => $box_id === FALSE ? "เพิ่มกล่องไม่สำเร็จ" : 'success',
      'box_id' => $box_id,
      'box_no' => $box_no
    );

    echo json_encode($arr);
  }


  public function add_new_box()
  {
    $box_id = FALSE;
    $package_id = getConfig('DEFAULT_PACKAGE');
    $box_no = 0;
    $order_code = $this->input->post('order_code');
    $code = $this->get_new_code();
    $box_no = $this->qc_model->get_last_box_no($order_code) + 1;
    $box_id = $this->qc_model->add_new_box($order_code, $code, $box_no, $package_id);

    $arr = array(
      'status' => $box_id === FALSE ? 'failed' : 'success',
      'message' => $box_id === FALSE ? "เพิ่มกล่องไม่สำเร็จ" : 'success',
      'box_code' => $code,
      'box_id' => $box_id,
      'box_no' => $box_no,
      'package_id' => get_null($package_id)
    );

    echo json_encode($arr);
  }


  public function get_box_list()
  {
    $ds = array();
    $code = $this->input->get('order_code');
    $id = $this->input->get('id_box');
    $box_list = $this->qc_model->get_box_list($code);

    if( ! empty($box_list))
    {
      foreach($box_list as $box)
      {
        $arr = array(
          'no' => $box->box_no,
          'code' => $box->code,
          'id_box' => $box->id,
          'package_id' => $box->package_id,
          'package' => select_active_package($box->package_id),
          'qty' => number($box->qty),
          'checked' => $box->id == $id ? 'checked' : '',
          'class' => $box->id == $id ? 'btn-success' : 'btn-default'
        );

        array_push($ds, $arr);
      }
    }

    $arr = array(
      'status' => 'success',
      'box_list' => empty($box_list) ? 'no box' : $ds
    );

    echo json_encode($arr);
  }


  public function update_package_id()
  {
    $sc = TRUE;
    $box_id = $this->input->post('box_id');
    $package_id = $this->input->post('package_id');

    if( ! $this->qc_model->update_box($box_id, ['package_id' => $package_id]))
    {
      $sc = FALSE;
      $this->error = "Failed to update package id";
    }

    $this->_response($sc);
  }


  public function get_checked_table()
  {
    $sc = TRUE;
    $code = $this->input->get('order_code');
    $item_code = $this->input->get('product_code');
    $list = $this->qc_model->get_checked_table($code, $item_code);
    if(!empty($list))
    {
      $ds = array();
      foreach($list as $rs)
      {
        $arr = array(
          'id_qc' => $rs->id,
          'barcode' => $rs->barcode,
          'box_no' => $rs->box_no,
          'qty' => $rs->qty
        );

        array_push($ds, $arr);
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบรายการตรวจสินค้า";
    }

    echo $sc === TRUE ? json_encode($ds) : $this->error;
  }


  public function update_check_qty()
  {
    $sc = TRUE;

    $ds = json_decode($this->input->post('data'));

    if( ! empty($ds))
    {
      $this->db->trans_begin();

      foreach($ds as $rs)
      {
        if($sc === FALSE)
        {
          break;
        }

        $qc = $this->qc_model->get($rs->id);

        if( ! empty($qc))
        {
          if($rs->remove_qty == $qc->qty)
          {
            if( ! $this->qc_model->delete_qc($rs->id))
            {
              $sc = FALSE;
              $this->error = "ลบรายการตรวจนับไม่สำเร็จ";
            }
          }
          else
          {
            if( ! $this->qc_model->update_qty($rs->id, (-1) * $rs->remove_qty))
            {
              $sc = FALSE;
              $this->error = "ปรับปรุงยอดตรวจนับไม่สำเร็จ";
            }
          }

          if($sc === TRUE)
          {
            $this->orders_model->unvalid_qc($qc->order_detail_id);
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "ไม่พบรายการตรวจนับ : {$rs->product_code}";
        }
      }

      if($sc === TRUE)
      {
        $this->db->trans_commit();
      }
      else
      {
        $this->db->trans_rollback();
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $this->_response($sc);
  }


  public function remove_checked_box()
  {
    $sc = TRUE;
    $id = $this->input->post('box_id');
    $order_code = $this->input->post('order_code');

    if( ! empty($id) && ! empty($order_code))
    {
      $order = $this->orders_model->get($order_code);

      if( ! empty($order))
      {
        if($order->state == 6)
        {
          $this->db->trans_begin();

          $details = $this->qc_model->get_details_in_box($order_code, $id);

          if( ! empty($details))
          {
            foreach($details as $rs)
            {
              if($sc === FALSE)
              {
                break;
              }

              if( ! $this->qc_model->delete_qc($rs->id))
              {
                $sc = FALSE;
                $this->error = "ลบรายการตรวจนับไม่สำเร็จ";
              }

              if($sc === TRUE)
              {
                $this->orders_model->unvalid_qc($rs->order_detail_id);
              }
            }
          } //--- end foreach

          if($sc === TRUE)
          {
            if( ! $this->qc_model->delete_box($id))
            {
              $sc = FALSE;
              $this->error = "Failed to delete box";
            }
          }

          if($sc === TRUE)
          {
            $this->db->trans_commit();
          }
          else
          {
            $this->db->trans_rollback();
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Invalid order status";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid order number";
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error
    );

    echo json_encode($arr);
  }


  public function print_box($code, $box_id)
  {
    $this->load->library('printer');
    $this->load->model('masters/customers_model');
    $this->load->library('ixqrcode');

    $order = $this->orders_model->get($code);
    $order->customer_name = $this->customers_model->get_name($order->customer_code);
    $details = $this->qc_model->get_box_details($code, $box_id);
    $box_no = $this->qc_model->get_box_no($box_id);
    $all_box = $this->qc_model->count_box($code);

    $qr = array(
      'data' => $code,
      'size' => 8,
      'level' => 'H',
      'savename' => NULL
    );

    ob_start();
    $this->ixqrcode->generate($qr);
    $qr = base64_encode(ob_get_contents());
    ob_end_clean();

    $ds = array();
    $ds['order'] = $order;
    $ds['details'] = $details;
    $ds['box_no'] = $box_no;
    $ds['all_box'] = $all_box;
    $ds['qrcode'] = $qr;

    $this->load->view('inventory/qc/packing_list', $ds);
  }


  public function print_all_box($code)
  {
    $ds = [];

    $this->load->library('printer');
    $this->load->model('masters/customers_model');
    $this->load->library('ixqrcode');

    $order = $this->orders_model->get($code);

    $qr = array(
      'data' => $code,
      'size' => 8,
      'level' => 'H',
      'savename' => NULL
    );

    ob_start();
    $this->ixqrcode->generate($qr);
    $order->qrcode = base64_encode(ob_get_contents());
    ob_end_clean();

    $order->customer_name = $this->customers_model->get_name($order->customer_code);
    $all_box = $this->qc_model->count_box($code);
    $boxes = $this->qc_model->get_boxes($code);

    $ds = array(
      'order' => $order,
      'all_box' => $all_box,
      'boxes' => []
    );

    if( ! empty($boxes))
    {
      foreach($boxes as $box)
      {
        $ds['boxes'][] = (object) array(
          'box_no' => $box->box_no,
          'box_id' => $box->id,
          'details' => $this->qc_model->get_box_details($code, $box->id)
        );
      }
    }

    $this->load->view('inventory/qc/packing_list_all', $ds);
  }


  public function print_porlor_label($code)
  {
    $ds = [];
    $packages = [];

    $this->load->model('address/address_model');
    $this->load->helper('address');
    $this->load->library('printer');
    $order = $this->orders_model->get($code);

    if( ! empty($order))
    {
      $addr = $this->address_model->get_shipping_detail($order->id_address);

      if( ! empty($addr))
      {
        $boxes = $this->qc_model->get_box_list($order->code);

        if( ! empty($boxes))
        {
          $box_no = 1;
          $sender_name = getConfig('PORLOR_CUSTOMER_NAME');
          $sender_code = getConfig("PORLOR_CUSTOMER_CODE");

          foreach($boxes as $box)
          {
            if($box-> qty > 0 && ! empty($box->tracking_no))
            {
              $packages[] = (object) array(
                'sender_code' => $sender_code,
                'sender_name' => $sender_name,
                'order_code' => $box->order_code,
                'box_code' => $box->code,
                'box_id' => $box->id,
                'box_no' => $box_no,
                'package_name' => $box->name,
                'package_width' => intval($box->width),
                'package_length' => intval($box->length),
                'package_height' => intval($box->height),
                'package_size' => round($box->width + $box->length + $box->height),
                'weight' => floatval(getConfig('PORLOR_DEFAULT_WEIGHT')),
                'receiver' => $addr->name,
                'address' => $addr->address,
                'sub_district' => $addr->sub_district,
                'district' => $addr->district,
                'province' => $addr->province,
                'postcode' => $addr->postcode,
                'phone' => $addr->phone,
                'tracking_no' => $box->tracking_no
              );

              $box_no++;
            }
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "No packing package found !";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่พบที่อยู่จัดส่ง";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเลขที่เอกสาร";
    }

    if( ! empty($packages))
    {
      $ds = array('packages' => $packages);
    }

    $this->load->view('inventory/qc/porlor_shipping_label', $ds);
  }

  public function get_box_details()
  {
    $order_code = $this->input->post('order_code');
    $box_id = $this->input->post('box_id');
    $items = $this->qc_model->get_box_details($order_code, $box_id);
    $box = $this->qc_model->get_box_by_id($box_id);

    $ds = array(
      'box_no' => empty($box) ? NULL : $box->box_no,
      'barcode' => empty($box) ? NULL : $box->code,
      'items' => $items
    );

    echo json_encode($ds);
  }


  public function get_checked_box_details()
  {
    $sc = TRUE;
    $order_code = $this->input->get('order_code');
    $id_box = $this->input->get('id_box');

    if( ! empty($order_code) && ! empty($id_box))
    {
      $items = $this->qc_model->get_details_in_box($order_code, $id_box);

      if( ! empty($items))
      {
        $no = 1;
        foreach($items as $item)
        {
          $item->no = $no;
          $no++;
        }
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $sc === TRUE ? $items : NULL
    );

    echo json_encode($arr);
  }


  public function get_new_code($date = NULL)
  {
    $date = empty($date) ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = 'BN';
    $run_digit = 5;
    $pre = $prefix.$Y.$M;
    $code = $this->qc_model->get_max_code($pre);

    if(! is_null($code))
    {
      $run_no = mb_substr($code, ($run_digit*-1), NULL, 'UTF-8') + 1;
      $new_code = $prefix . $Y . $M . sprintf('%0'.$run_digit.'d', $run_no);
    }
    else
    {
      $new_code = $prefix . $Y . $M . sprintf('%0'.$run_digit.'d', '001');
    }

    return $new_code;
  }


  public function clear_filter()
  {
    $filter = array(
      'ic_code',
      'ic_customer',
      'ic_user',
      'ic_channels',
      'ic_shop_id',
      'ic_role',
      'ic_from_date',
      'ic_to_date',
      'ic_is_cancled',
      'ic_id_sender'
    );

    return clear_filter($filter);
  }

} //--- end Qc
?>

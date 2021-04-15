<?php
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class Orders extends REST_Controller
{
  public $error;
  public $user;
  public $ms;

  public function __construct()
  {
    parent::__construct();
    $this->ms = $this->load->database('ms', TRUE); //--- SAP database

    $this->load->model('orders/orders_model');
    $this->load->model('orders/order_state_model');
    $this->load->model('masters/products_model');
    $this->load->model('masters/customers_model');
    $this->load->model('masters/channels_model');
    $this->load->model('masters/payment_methods_model');
    $this->load->model('address/address_model');
    $this->load->model('stock/stock_model');
    $this->user = 'api@warrix';
  }

  public function test_post()
  {
    //--- Get raw post data
    $data = json_decode(file_get_contents("php://input"));
    $details  = $data->details;
    foreach($details as $rs)
    {
      print_r($rs);
    }
    //$this->response($data, 200);
  }


  public function status_get($code)
  {
    if(empty($code))
    {
      $arr = array(
        'status' => FALSE,
        'error' => "Order Number is required"
      );

      $this->response($arr, 400);
    }

    $state = $this->orders_model->get_state($code);
    if(empty($state))
    {
      $arr = array(
        'status' => FALSE,
        'error' => "Invalid Order Number"
      );

      $this->response($arr, 400);
    }
    else
    {
      //---- status name
      $state_name = array(
        '1' => 'Pending',
        '2' => 'Waiting for payment',
        '3' => 'Processing',
        '4' => 'Picking',
        '5' => 'Picking',
        '6' => 'Packing',
        '7' => 'Shipping',
        '8' => 'Complete',
        '9' => 'Cancel'
      );

      $arr = array(
        'status' => $state_name[$state]
      );

      $this->response($arr, 200);
    }

  }


  public function status_put()
  {
    $data = json_decode(file_get_contents("php://input"));
    if(empty($data))
    {
      $arr = array(
        'status' => FALSE,
        'error' => 'empty data'
      );
      $this->response($arr, 400);
    }

    if(! property_exists($data, 'order_num') OR $data->order_number == '')
    {
      $arr = array(
        'status' => FALSE,
        'error' => 'order_number is required'
      );
      $this->response($arr, 400);
    }

    if( ! property_exists($data, 'status') OR $data->status == '')
    {
      $arr = array(
        'status' => FALSE,
        'error' => 'Invalid status'
      );
      $this->response($arr, 400);
    }

  }





  public function create_post()
  {
    //--- Get raw post data
    $data = json_decode(file_get_contents("php://input"));

    if(empty($data))
    {
      $arr = array(
        'status' => FALSE,
        'error' => 'empty data'
      );
      $this->response($arr, 400);
    }

    $sc = TRUE;

    $count = count((array)$data);

    if(! property_exists($data, 'order_number') OR $data->order_number == '')
    {
      $sc = FALSE;
      $this->error = 'order_number is required';
    }

    if(! property_exists($data, 'customer_code') OR $data->customer_code == '')
    {
      $sc = FALSE;
      $this->error = 'customer_code is required';
    }

    if(! property_exists($data, 'channels'))
    {
      $sc = FALSE;
      $this->error = 'missing channels code';
    }

    if(! property_exists($data, 'payment'))
    {
      $sc = FALSE;
      $this->error = 'missing payment code';
    }

    if(! property_exists($data, 'ship_to'))
    {
      $sc = FALSE;
      $this->error = 'missing shipping address';
    }

    if(! property_exists($data->ship_to, 'name'))
    {
      $sc = FALSE;
      $this->error = 'missing shipping address';
    }

    if(! property_exists($data->ship_to, 'address'))
    {
      $sc = FALSE;
      $this->error = 'missing shipping address';
    }

    if(! property_exists($data->ship_to, 'district'))
    {
      $sc = FALSE;
      $this->error = 'district is required';
    }


    if(! property_exists($data->ship_to, 'province'))
    {
      $sc = FALSE;
      $this->error = 'province is required';
    }

    if(! property_exists($data->ship_to, 'phone'))
    {
      $sc = FALSE;
      $this->error = 'phone is required';
    }


    if($this->orders_model->get_order_code_by_reference($data->order_number) !== FALSE)
    {
      $sc = FALSE;
      $this->error = 'Order number already exists';
    }



    //--- check each item code
    $details = $data->details;

    if(empty($details))
    {
      $sc = FALSE;
      $this->error = "Items not found";
    }


    if(!empty($details))
    {
      foreach($details as $rs)
      {
        if($sc === FALSE)
        {
          break;
        }

        //---- check valid items
        $item = $this->products_model->get($rs->item);
        if(empty($item))
        {
          $sc = FALSE;
          $this->error = "Invalid SKU : {$rs->item}";
        }
      }
    }


    //---- if any error return
    if($sc === FALSE)
    {
      $arr = array(
        'status' => FALSE,
        'message' => $this->error
      );

      $this->response($arr, 400);
    }

    //---- new code start
    if($sc === TRUE)
    {
      //--- รหัสเล่มเอกสาร [อ้างอิงจาก SAP]
      //--- ถ้าเป็นฝากขายแบบโอนคลัง ยืมสินค้า เบิกแปรสภาพ เบิกสินค้า (ไม่เปิดใบกำกับ เปิดใบโอนคลังแทน) นอกนั้น เปิด SO
      $bookcode = getConfig('BOOK_CODE_ORDER');

      $role = 'S';

      $date_add = date('Y-m-d H:i:s');

      //---- กำหนดช่องทางขายสำหรับเว็บไซต์ เพราะมีลูกค้าแยกตามช่องทางการชำระเงินอีกที
      //---- เลยต้องกำหนดลูกค้าแยกตามช่องทางการชำระเงินต่างๆ สำหรับเว็บไซต์เท่านั้น
      //---- เพราะช่องทางอื่นๆในการนำเข้าจะใช้ช่องทางการชำระเงินแบบเดียวทั้งหมด
      //---- เช่น K plus จะจ่ายด้วยบัตรเครดิตทั้งหมด  LAZADA จะไปเรียกเก็บเงินกับทาง ลาซาด้า
      $web_channels = getConfig('WEB_SITE_CHANNELS_CODE');

      //--- รหัสลูกค้าสำหรับ COD เว็บไซต์
      $web_customer_cod = getConfig('CUSTOMER_CODE_COD');

      //--- รหัสลูกค้าสำหรับ 2c2p บนเว็บไซต์
      $web_customer_2c2p = getConfig('CUSTOMER_CODE_2C2P');

      //--- รหัสบูกค้าสำหรับ PayAtStore
      $web_customer_PayAtStore = getConfig('CUSTOMER_CODE_PAYATSTORE');

      //--- รหัสลูกค้าเริ่มต้น หากพอว่าไม่มีการระบุรหัสลูกค้าไว้ จะใช้รหัสนี้แทน
      $default_customer = getConfig('DEFAULT_CUSTOMER');

      $prefix = getConfig('PREFIX_SHIPPING_NUMBER');

      //---- order code from web site
      $ref_code = $data->order_number;

      //--- shipping Number
      $shipping_code = $prefix.$data->order_number;

      //---- กำหนดช่องทางการขายเป็นรหัส
      $channels = $this->channels_model->get($data->channels);

      //--- หากไม่ระบุช่องทางขายมา หรือ ช่องทางขายไม่ถูกต้องใช้ default
      if(empty($channels))
      {
        $channels = $this->channels_model->get_default();
      }

      //--- กำหนดช่องทางการชำระเงิน
      $payment = $this->payment_methods_model->get($data->payment);

      if(empty($payment))
      {
        $payment = $this->payment_methods_model->get_default();
      }

      //-- state ของออเดอร์ จะมีการเปลี่ยนแปลงอีกที
      $state = 3;

      //---- รหัสลูกค้าจะมีการเปลี่ยนแปลงตามเงื่อนไขด้านล่างนี้
      $customer_code = $data->customer_code;

      //---- ตรวจสอบว่าช่องทางขายที่กำหนดมา เป็นเว็บไซต์หรือไม่(เพราะจะมีช่องทางการชำระเงินหลายช่องทาง)
      if($channels->code === $web_channels)
      {
        if($payment->code === '2C2P')
        {
          //---- กำหนดรหัสลูกค้าตามค่าที่ config สำหรับเว็บไซต์ที่ชำระโดยบัตรเครดติ(2c2p)
          $customer_code = $web_customer_2c2p;
        }
        else if($payment->code === 'COD')
        {
          //---- กำหนดรหัสลูกค้าตามค่าที่ config สำหรับเว็บไซต์ที่ชำระแบบ COD
          $customer_code = $web_customer_cod;
        }

      }
      else
      {
        //--- หากไม่ใช่ช่องทางเว็บไซต์
        //--- กำหนดรหัสลูกค้าตามช่องทางขายที่ได้ผูกไว้
        //--- หากไม่มีการผูกไว้ให้
        $customer_code = empty($channels->customer_code) ? $default_customer : $channels->customer_code;
      }

      $customer = $this->customers_model->get($customer_code);

      //---	ถ้าเป็นออเดอร์ขาย จะมี id_sale
      $sale_code = $customer->sale_code;

      //---	หากเป็นออนไลน์ ลูกค้าออนไลน์ชื่ออะไร
      $customer_ref = addslashes(trim($data->ship_to->name));

      //---	ช่องทางการชำระเงิน
      $payment_code = $payment->code;

      //---	ช่องทางการขาย
      $channels_code = $channels->code;

      //--- order code gen จากระบบ
      $order_code = $this->get_new_code($date_add);


      //--- เตรียมข้อมูลสำหรับเพิ่มเอกสารใหม่
      $ds = array(
        'code' => $order_code,
        'role' => $role,
        'bookcode' => $bookcode,
        'reference' => $ref_code,
        'customer_code' => $customer_code,
        'customer_ref' => $customer_ref,
        'channels_code' => $channels_code,
        'payment_code' => $payment_code,
        'sale_code' => $sale_code,
        'state' => $state,
        'is_paid' => 1,
        'is_term' => 0,
        'status' => 1,
        'user' => $this->user,
        'date_add' => $date_add,
        'warehouse_code' => getConfig('WEB_SITE_WAREHOUSE_CODE'),
        'is_api' => 1,
        'order_id' => $data->order_id
      );

    $this->db->trans_begin();

    $rs = $this->orders_model->add($ds);


    if(!$rs)
    {
      $sc = FALSE;
      $this->error = "Order create failed";
    }
    else
    {
      //---- change state
      $arr = array(
        'order_code' => $order_code,
        'state' => 3,
        'update_user' => $this->user
      );

      //--- add state event
      $this->order_state_model->add_state($arr);

      $id_address = $this->address_model->get_id($customer_ref, $data->ship_to->address);

      if($id_address === FALSE)
      {
        $arr = array(
          'code' => $customer_ref,
          'name' => $customer_ref,
          'address' => $data->ship_to->address,
          'sub_district' => $data->ship_to->sub_district,
          'district' => $data->ship_to->district,
          'province' => $data->ship_to->province,
          'postcode' => $data->ship_to->postcode,
          'phone' => $data->ship_to->phone,
          'alias' => 'Home',
          'is_default' => 1
        );

        $id_address = $this->address_model->add_shipping_address($arr);

      }

      $this->orders_model->set_address_id($order_code, $id_address);

      //---- add order details
      $details = $data->details;

      if(! empty($details))
      {
        foreach($details as $rs)
        {
          //--- check item code
          $item = $this->products_model->get($rs->item);

          if(empty($item))
          {
            $sc = FALSE;
            $this->error = "Invalid SKU Code : {$rs->item}";
            break;
          }
          else
          {
            //--- ถ้ายังไม่มีรายการอยู่ เพิ่มใหม่
            $arr = array(
              "order_code"	=> $order_code,
              "style_code"		=> $item->style_code,
              "product_code"	=> $item->code,
              "product_name"	=> $item->name,
              "cost"  => $item->cost,
              "price"	=> $rs->price,
              "qty"		=> $rs->qty,
              "discount1"	=> 0,
              "discount2" => 0,
              "discount3" => 0,
              "discount_amount" => 0,
              "total_amount"	=> round($rs->amount,2),
              "id_rule"	=> NULL,
              "is_count" => $item->count_stock,
              "is_api" => 1
            );

            if( $this->orders_model->add_detail($arr) === FALSE )
            {
              $sc = FALSE;
              $this->error = "Order item insert failed : {$item->code}";
              break;
            }
            else
            {
              $this->update_api_stock($item->code, $item->old_code);
            }
          }

        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Items not found";
      }
    }

    if($sc === TRUE)
    {
      $this->db->trans_commit();

      $arr = array(
        'status' => 'SUCCESS',
        'order_code' => $order_code
      );
      $this->response($arr, 200);
    }
    else
    {
      $this->db->trans_rollback();
      $arr = array(
        'status' => FALSE,
        'message' => $this->error
      );

      $this->response($arr, 200);
    }
  }
}




  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_ORDER');
    $run_digit = getConfig('RUN_DIGIT_ORDER');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->orders_model->get_max_code($pre);
    if(! is_null($code))
    {
      $run_no = mb_substr($code, ($run_digit*-1), NULL, 'UTF-8') + 1;
      $new_code = $prefix . '-' . $Y . $M . sprintf('%0'.$run_digit.'d', $run_no);
    }
    else
    {
      $new_code = $prefix . '-' . $Y . $M . sprintf('%0'.$run_digit.'d', '001');
    }

    return $new_code;
  }



  public function update_api_stock($code, $old_code)
  {
    if(getConfig('SYNC_WEB_STOCK'))
    {
      $this->load->library('api');
      $sell_stock = $this->stock_model->get_sell_stock($code);
      $reserv_stock = $this->orders_model->get_reserv_stock($code);
      $qty = $sell_stock - $reserv_stock;
      $item = empty($old_code) ? $code : $old_code;
      $this->api->update_web_stock($item, $qty);
    }
  }


} //--- end class

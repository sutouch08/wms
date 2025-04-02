<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Prepare extends PS_Controller
{
  public $menu_code = 'ICODPR';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'PICKPACK';
	public $title = 'จัดสินค้า';
  public $filter;
  public $full_mode = TRUE;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/prepare';
    $this->load->model('inventory/prepare_model');
    $this->load->model('orders/orders_model');
    $this->load->model('orders/order_state_model');
    $this->load->model('masters/warehouse_model');

    $this->full_mode = is_true(getConfig('WMS_FULL_MODE'));
  }


  public function index()
  {
    $this->title = "รายการรอจัด";
    $this->load->helper('channels');
    $this->load->helper('payment_method');
    $this->load->helper('warehouse');

    $filter = array(
      'code' => get_filter('code', 'ic_code', ''),
      'customer' => get_filter('customer', 'ic_customer', ''),
      'user' => get_filter('user', 'ic_user', ''),
      'channels' => get_filter('channels', 'ic_channels', 'all'),
      'is_online' => get_filter('is_online', 'ic_is_online', '2'),
      'role' => get_filter('role', 'ic_role', 'all'),
      'from_date' => get_filter('from_date', 'ic_from_date', ''),
      'to_date' => get_filter('to_date', 'ic_to_date', ''),
      'order_by' => get_filter('order_by', 'ic_order_by', ''),
      'sort_by' => get_filter('sort_by', 'ic_sort_by', ''),
      'stated' => get_filter('stated', 'ic_stated', ''),
      'startTime' => get_filter('startTime', 'ic_startTime', ''),
      'endTime' => get_filter('endTime', 'ic_endTime', ''),
      'item_code' => get_filter('item_code', 'ic_item_code', ''),
      'payment' => get_filter('payment', 'ic_payment', 'all'),
      'warehouse' => get_filter('warehouse', 'ic_warehouse', 'all')
    );

    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      //--- แสดงผลกี่รายการต่อหน้า
  		$perpage = get_rows();
  		$segment  = 4; //-- url segment
  		$rows     = $this->prepare_model->count_rows($filter, 3, $this->full_mode);
  		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
  		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
  		$orders   = $this->prepare_model->get_list($filter, $perpage, $this->uri->segment($segment), 3, $this->full_mode);

      $filter['orders'] = $orders;

  		$this->pagination->initialize($init);
      $this->load->view('inventory/prepare/prepare_list', $filter);
    }
  }


  public function view_process()
  {
    $this->title = "รายการกำลังจัด";
    $this->load->helper('channels');
    $this->load->helper('payment_method');
    $this->load->helper('warehouse');

    $filter = array(
      'code' => get_filter('code', 'ic_code', ''),
      'customer' => get_filter('customer', 'ic_customer', ''),
      'user' => get_filter('user', 'ic_user', ''),
      'channels' => get_filter('channels', 'ic_channels', 'all'),
      'is_online' => get_filter('is_online', 'ic_is_online', '2'),
      'role' => get_filter('role', 'ic_role', 'all'),
      'from_date' => get_filter('from_date', 'ic_from_date', ''),
      'to_date' => get_filter('to_date', 'ic_to_date', ''),
      'order_by' => get_filter('order_by', 'ic_order_by', ''),
      'sort_by' => get_filter('sort_by', 'ic_sort_by', ''),
      'stated' => get_filter('stated', 'ic_stated', ''),
      'startTime' => get_filter('startTime', 'ic_startTime', ''),
      'endTime' => get_filter('endTime', 'ic_endTime', ''),
      'item_code' => get_filter('item_code', 'ic_item_code', ''),
      'payment' => get_filter('payment', 'ic_payment', 'all'),
      'warehouse' => get_filter('warehouse', 'ic_warehouse', 'all')
    );

    if($this->input->post('search'))
    {
      redirect($this->home.'/view_process/');
    }
    else
    {
      //--- แสดงผลกี่รายการต่อหน้า
  		$perpage = get_rows();
  		$segment  = 4; //-- url segment
  		$rows     = $this->prepare_model->count_rows($filter, 4, $this->full_mode);
  		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
  		$init	= pagination_config($this->home.'/view_process/', $rows, $perpage, $segment);
  		$orders   = $this->prepare_model->get_list($filter, $perpage, $this->uri->segment($segment), 4, $this->full_mode);

      $filter['orders'] = $orders;

  		$this->pagination->initialize($init);
      $this->load->view('inventory/prepare/prepare_view_process', $filter);
    }
  }


  public function gen_pick_list()
  {
    $ds = json_decode($this->input->post('data'));

    $orders = [];
    $items = [];

    if( ! empty($ds))
    {
      $this->load->library('ixqrcode');
      $this->load->model('masters/products_model');
      $this->load->model('masters/channels_model');

      foreach($ds as $rs)
      {
        $order = $this->orders_model->get($rs->code);

        if( ! empty($order))
        {
          $qr = array(
            'data' => $rs->code,
            'size' => 8,
            'level' => 'H',
            'savename' => NULL
          );

          ob_start();
          $this->ixqrcode->generate($qr);
          $qr = base64_encode(ob_get_contents());
          ob_end_clean();

          $orders[] = (object)['file' => $qr, 'code' => $rs->code, 'channels' => $this->channels_model->get_name($order->channels_code)];

          $uncomplete = $this->orders_model->get_unvalid_details($rs->code);

          if( ! empty($uncomplete))
          {
            $bc = [];

            foreach($uncomplete as $ro)
            {
              $prepared = $this->prepare_model->get_prepared($ro->order_code, $ro->product_code, $ro->id);
              $qty = $ro->qty - $prepared;

              if($qty > 0)
              {
                if(isset($items[$ro->product_code]))
                {
                  $items[$ro->product_code]->qty += $qty;
                }
                else
                {
                  $items[$ro->product_code] = (object) array(
                    'whsCode' => $order->warehouse_code,
                    'code' => $ro->product_code,
                    'barcode' => $this->products_model->get_barcode($ro->product_code),
                    'name' => $ro->product_name,
                    'qty' => $qty
                  );
                }
              }
            } //-- foreach uncompleted
          }// if uncomplete

          $this->add_print_logs($order->code);
        }
      } //-- foreach orders

      if( ! empty($items))
      {
        //--- get_stock in_zone
        foreach($items as $rs)
        {
          $rs->stock_in_zone = $this->get_inline_stock_in_zone($rs->code, $rs->whsCode);
        }
      }

      $this->load->library('printer');

      $pl = array(
        'orders' => $orders,
        'items' => $items
      );

      $this->load->view('print/print_gen_pick_list', $pl);
    }
  }


  public function add_print_logs($code)
  {
    return $this->db->query("REPLACE INTO print_pick_list_logs (order_code) VALUES ('{$code}')");
  }


  public function get_inline_stock_in_zone($item_code, $warehouse = NULL)
  {
    $sc = "ไม่มีสินค้า";
    $this->load->model('stock/stock_model');
    $stock = $this->stock_model->get_stock_in_zone($item_code, $warehouse);
    if(!empty($stock))
    {
      $sc = "";
      foreach($stock as $rs)
      {
        $prepared = $this->prepare_model->get_buffer_zone($item_code, $rs->code);
        $qty = $rs->qty - $prepared;
        if($qty > 0)
        {
          $sc .= $rs->name.' : '.($rs->qty - $prepared).' | ';
        }
      }
    }

    return empty($sc) ? 'ไม่พบสินค้า' : $sc;
  }


  public function is_cancel($reference, $channels)
  {
    $is_cancel = FALSE;

    if($channels == '0009')
    {
      $this->load->library('wrx_tiktok_api');

      $order_status = $this->wrx_tiktok_api->get_order_status($reference);

      if($order_status == '140')
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
    $this->load->helper('warehouse');

    $is_cancel = FALSE;

    $order = $this->orders_model->get($code);

    if( ! empty($order))
    {
      //--- check cancel request
      $is_cancel = $this->orders_model->is_cancel_request($order->code);

      if( ! $is_cancel && ! empty($order->reference) && ($order->channels_code == '0009'))
      {
        $is_cancel = $this->is_cancel($order->reference, $order->channels_code);
      }

      if( ! $is_cancel)
      {
        $state = $this->orders_model->get_state($code);

        if($state == 3)
        {
          $rs = $this->orders_model->change_state($code, 4);

          if($rs)
          {
            $arr = array(
              'order_code' => $code,
              'state' => 4,
              'update_user' => $this->_user->uname
            );

            $this->order_state_model->add_state($arr);
            $order->state = 4;
          }
        }

        $order->customer_name = $this->customers_model->get_name($order->customer_code);
        $order->channels_name = $this->channels_model->get_name($order->channels_code);

        $whs = $this->warehouse_model->get($order->warehouse_code);
        $order->warehouse_name = empty($whs) ? NULL : $whs->name;
        $order->allow_prepare = $whs->prepare;

        $orderQty = 0;
        $pickedQty = 0;

        $uncomplete = $this->orders_model->get_unvalid_details($code);

        if(!empty($uncomplete))
        {
          foreach($uncomplete as $rs)
          {
            $orderQty += $rs->qty;
            $rs->barcode = $this->get_barcode($rs->product_code);
            $rs->prepared = $this->prepare_model->get_prepared($rs->order_code, $rs->product_code, $rs->id);
            $rs->stock_in_zone = $this->get_stock_in_zone($rs->product_code, get_null($order->warehouse_code));
          }
        }

        $complete = $this->orders_model->get_valid_details($code);

        if(!empty($complete))
        {
          foreach($complete as $rs)
          {
            $rs->barcode = $this->get_barcode($rs->product_code);
            $rs->prepared = $rs->is_count == 1 ? $this->prepare_model->get_prepared($rs->order_code, $rs->product_code, $rs->id) : $rs->qty;
            $orderQty += $rs->qty;
            $pickedQty += $rs->prepared;

            $arr = array(
              'order_code' => $rs->order_code,
              'product_code' => $rs->product_code,
              'order_detail_id' => $rs->id,
              'is_count' => $rs->is_count
            );

            $rs->from_zone = $this->get_prepared_from_zone($arr);
          }
        }

        $ds = array(
          'order' => $order,
          'uncomplete_details' => $uncomplete,
          'complete_details' => $complete,
          'finished' => empty($uncomplete) ? TRUE : FALSE,
          'orderQty' => number($orderQty),
          'pickedQty' => number($pickedQty)
        );

        if( ! empty($view))
        {
          $ds['title'] = $order->code . '<br/>' . $order->warehouse_code .' : '.$order->warehouse_name;
          $this->load->view('inventory/prepare/prepare_process_mobile', $ds);
        }
        else
        {
          $this->load->view('inventory/prepare/prepare_process', $ds);
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
    $rs = $this->orders_model->get_valid_item($id_order_detail);

    if( ! empty($rs))
    {
      $rs->qty = round($rs->qty, 2);
      $rs->barcode = $this->get_barcode($rs->product_code);
      $rs->prepared = $rs->is_count == 1 ? $this->prepare_model->get_prepared($rs->order_code, $rs->product_code, $rs->id) : $rs->qty;
      $rs->balance = $rs->qty - $rs->prepared;

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


  public function get_incomplete_item()
  {
    $sc = TRUE;
    $id = $this->input->post('id');
    $whsCode = $this->input->post('warehouse_code');

    $rs = $this->orders_model->get_invalid_item($id);

    if( ! empty($rs))
    {
      $rs->qty = round($rs->qty, 2);
      $rs->barcode = $this->get_barcode($rs->product_code);
      $rs->prepared = $rs->is_count == 1 ? $this->prepare_model->get_prepared($rs->order_code, $rs->product_code, $rs->id) : $rs->qty;
      $rs->balance = $rs->qty - $rs->prepared;

      $arr = array(
        'order_code' => $rs->order_code,
        'product_code' => $rs->product_code,
        'order_detail_id' => $rs->id,
        'is_count' => $rs->is_count
      );

      $rs->stock_in_zone = $this->get_stock_in_zone($rs->product_code, get_null($whsCode));
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบรายการที่ครบแล้ว : {$id}";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $sc === TRUE ? $rs : NULL
    );

    echo json_encode($arr);
  }


  public function do_prepare()
  {
    $sc = TRUE;
    $valid = 0;
    if($this->input->post('order_code'))
    {
      $this->load->model('masters/products_model');

      $order_code = $this->input->post('order_code');
      $zone_code  = $this->input->post('zone_code');
      $barcode    = $this->input->post('barcode');
      $qty        = $this->input->post('qty');

      $state = $this->orders_model->get_state($order_code);
      //--- ตรวจสอบสถานะออเดอร์ 4 == กำลังจัดสินค้า
      if($state == 4)
      {
        $item = $this->products_model->get_product_by_barcode($barcode);

        if(empty($item))
        {
          $item = $this->products_model->get($barcode);
        }

        //--- ตรวจสอบบาร์โค้ดที่ยิงมา
        if(!empty($item))
        {
          if($item->count_stock == 1)
          {
            //---- มีสินค้านี้อยู่ในออเดอร์หรือไม่ ถ้ามี รวมยอดมา อาจมีมาก
            $ds = $this->orders_model->get_unvalid_order_detail($order_code, $item->code);
            //$orderQty = $this->orders_model->get_sum_item_qty($order_code, $item->code);

            if( ! empty($ds))
            {
              //--- ดึงยอดที่จัดแล้ว
              // $prepared = $this->get_prepared($order_code, $item->code);
              $prepared = $this->prepare_model->get_prepared($order_code, $item->code, $ds->id);

              //--- ยอดคงเหลือค้างจัด
              $bQty = $ds->qty - $prepared;

              //---- ตรวจสอบยอดที่ยังไม่ครบว่าจัดเกินหรือเปล่า
              if( $bQty < $qty)
              {
                $sc = FALSE;
                $this->error = "สินค้าเกิน กรุณาคืนสินค้าแล้วจัดสินค้าใหม่อีกครั้ง";
              }
              else
              {
                $stock = $this->get_stock_zone($zone_code, $item->code); //1000;

                if($stock < $qty)
                {
                  $sc = FALSE;
                  $this->error = "สินค้าไม่เพียงพอ กรุณากำหนดจำนวนสินค้าใหม่";
                }
                else
                {
                  $this->db->trans_begin();

                  if( ! $this->prepare_model->update_buffer($order_code, $item->code, $zone_code, $qty, $ds->id))
                  {
                    $sc = FALSE;
                    $this->error = "Failed to update buffer";
                  }

                  if($sc === TRUE)
                  {
                    if( ! $this->prepare_model->update_prepare($order_code, $item->code, $zone_code, $qty, $ds->id))
                    {
                      $sc = FALSE;
                      $this->error = "Failed to update prepare";
                    }
                  }

                  if($sc === TRUE)
                  {
                    $this->db->trans_commit();
                  }
                  else
                  {
                    $this->trans_rollback();
                  }

                  if($sc === TRUE)
                  {
                    $preparedQty = $prepared + $qty;

                    if($preparedQty == $ds->qty)
                    {
                      $this->orders_model->valid_detail($ds->id);
                      $valid = 1;
                    }
                  }
                }
              }

            }
            else
            {
              $sc = FALSE;
              $this->error = 'สินค้าไม่ตรงกับออเดอร์';
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = 'สินค้าไม่นับสต็อก ไม่จำเป็นต้องจัดสินค้านี้';
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = 'บาร์โค้ดไม่ถูกต้อง กรุณาตรวจสอบ';
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = 'สถานะออเดอร์ถูกเปลี่ยน ไม่สามารถจัดสินค้าต่อได้';
      }
    }

    echo $sc === TRUE ? json_encode(array("id" => $ds->id, "qty" => $qty, "valid" => $valid)) : $this->error;
  }


  public function get_barcode($item_code)
  {
    $this->load->model('masters/products_model');
    return $this->products_model->get_barcode($item_code);
  }


  public function get_prepared($order_code, $item_code, $detail_id)
  {
    return $this->prepare_model->get_prepared($order_code, $item_code, $detail_id);
  }


  public function get_zone_code()
  {
    $ds = array();
    $sc = TRUE;
    $zone_code = trim($this->input->get('zone_code'));
    $warehouse_code = trim($this->input->get('warehouse_code'));

    if($zone_code)
    {
      $this->load->model('masters/zone_model');

      $zone = $this->zone_model->get_zone($zone_code, $warehouse_code);

      if( ! empty($zone))
      {
        $ds = array(
          'code' => $zone->code,
          'name' => $zone->name
        );
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่พบโซน";
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
      'zone' => $sc === TRUE ? $ds : NULL
    );

    echo json_encode($arr);
  }


  public function get_prepared_from_zone(array $ds = array())
  {
    $label = "ไม่พบข้อมูล";

    if( ! empty($ds))
    {
      if( ! empty($ds['is_count']))
      {
        $buffer = $this->prepare_model->get_prepared_from_zone($ds['order_code'], $ds['product_code'], $ds['order_detail_id']);

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


  public function get_stock_in_zone($item_code, $warehouse = NULL)
  {
    $sc = "ไม่มีสินค้า";
    $this->load->model('stock/stock_model');
    $stock = $this->stock_model->get_stock_in_zone($item_code, $warehouse);
    if(!empty($stock))
    {
      $sc = "";
      foreach($stock as $rs)
      {
        $prepared = $this->prepare_model->get_buffer_zone($item_code, $rs->code);
        $qty = $rs->qty - $prepared;
        if($qty > 0)
        {
          $sc .= $rs->name.' : '.($rs->qty - $prepared).'<br/>';
        }

      }
    }

    return empty($sc) ? 'ไม่พบสินค้า' : $sc;
  }


  public function reload_stock_in_zone()
  {
    $sc = TRUE;
    $item_code = $this->input->get('product_code');
    $whs_code = $this->input->get('warehouse_code');

    $result = $this->get_stock_in_zone($item_code, $whs_code);

    $arr = array(
      'status' => 'success',
      'message' => 'success',
      'result' => $result
    );

    echo json_encode($arr);
  }

  //---- สินค้าคงเหลือในโซน ลบด้วย สินค้าที่จัดไปแล้ว
  public function get_stock_zone($zone_code, $item_code)
  {
    $this->load->model('stock/stock_model');
    $this->load->model('masters/warehouse_model');
    $this->load->model('masters/zone_model');

    $zone = $this->zone_model->get($zone_code);
    $wh = $this->warehouse_model->get($zone->warehouse_code);
    $gb_auz = getConfig('ALLOW_UNDER_ZERO');
    $wh_auz = $wh->auz == 1 ? TRUE : FALSE;
    $auz = $gb_auz == 1 ? TRUE : $wh_auz;

    if($auz === TRUE)
    {
      return 1000000;
    }

    //---- สินค้าคงเหลือในโซน
    $stock = $this->stock_model->get_stock_zone($zone_code, $item_code);

    //--- ยอดจัดสินค้าที่จัดออกจากโซนนี้ไปแล้ว แต่ยังไม่ได้ตัด
    $prepared = $this->prepare_model->get_prepared_zone($zone_code, $item_code);


    return $stock - $prepared;
  }


  public function set_zone_label($value)
  {
    $this->input->set_cookie(array('name' => 'showZone', 'value' => $value, 'expire' => 3600 , 'path' => '/'));
  }

  public function finish_prepare()
  {
    $code = $this->input->post('order_code');
    $sc = TRUE;

    $state = $this->orders_model->get_state($code);

    //---	ถ้าสถานะเป็นกำลังจัด (บางทีอาจมีการเปลี่ยนสถานะตอนเรากำลังจัดสินค้าอยู่)
    if( $state == 4)
    {
      $this->db->trans_start();

      //--- mark all detail as valid
      $this->orders_model->valid_all_details($code);

      //---	เปลียน state ของออเดอร์ เป็น รอแพ็คสินค้า
      $this->orders_model->change_state($code, 5);

      $arr = array(
        'order_code' => $code,
        'state' => 5,
        'update_user' => $this->_user->uname
      );

      //--- add state event
      $this->order_state_model->add_state($arr);

      $this->db->trans_complete();

      if($this->db->trans_status() === FALSE)
      {
        $sc = FALSE;
        $message = "ปิดออเดอร์ไม่สำเร็จ กรุณาลองใหม่อีกครั้ง";
      }

    }

    echo $sc === TRUE ? 'success' : $message;
  }


  public function check_state()
  {
    $code = $this->input->get('order_code');
    $rs = $this->orders_model->get_state($code);
    echo $rs;
  }


  public function pull_order_back()
  {
    $code = $this->input->post('order_code');
    $state = $this->orders_model->get_state($code);
    if($state == 4)
    {
      $arr = array(
        'order_code' => $code,
        'state' => 3,
        'update_user' => $this->_user->uname
      );

      $this->orders_model->change_state($code, 3);
      $this->order_state_model->add_state($arr);
    }

    echo 'success';
  }


  function remove_buffer()
  {
    $sc = TRUE;
    $this->load->model('inventory/buffer_model');
    $order_code = $this->input->post('order_code');
    $item_code = $this->input->post('product_code');
    $detail_id = $this->input->post('order_detail_id');

    $this->db->trans_begin();

    if( ! $this->buffer_model->remove_buffer($order_code, $item_code, $detail_id))
    {
      $sc = FALSE;
      $this->error = "Failed to delete buffer";
    }

    if( $sc === TRUE)
    {
      if( ! $this->prepare_model->remove_prepare($order_code, $item_code, $detail_id))
      {
        $sc = FALSE;
        $this->error = "Failed to delete prepare logs";
      }
    }

    if($sc === TRUE)
    {
      if( ! $this->orders_model->unvalid_detail($detail_id) )
      {
        $sc = FALSE;
        $this->error = "Failed to rollback item status (unvalid)";
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

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function clear_filter()
  {
    $filter = array(
      'ic_code',
      'ic_customer',
      'ic_user',
      'ic_channels',
      'ic_is_online',
      'ic_role',
      'ic_from_date',
      'ic_to_date',
      'ic_order_by',
      'ic_sort_by',
      'ic_stated',
      'ic_startTime',
      'ic_endTime',
      'ic_item_code',
      'ic_display_name',
      'ic_payment',
      'ic_warehouse'
    );

    clear_filter($filter);
  }


} //--- end class
?>

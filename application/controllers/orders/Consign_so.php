<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Consign_so extends PS_Controller
{
  public $menu_code = 'SOCCSO';
	public $menu_group_code = 'SO';
  public $menu_sub_group_code = 'ORDER';
	public $title = 'ฝากขาย(ใบกำกับ)';
  public $filter;
  public $role = 'C';
  public $wmsApi;
  public $sokoApi;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'orders/consign_so';
    $this->load->model('orders/orders_model');
    $this->load->model('masters/customers_model');
    $this->load->model('orders/order_state_model');
    $this->load->model('masters/product_tab_model');
    $this->load->model('stock/stock_model');
    $this->load->model('masters/product_style_model');
    $this->load->model('masters/products_model');
    $this->load->model('masters/zone_model');

    $this->load->helper('order');
    $this->load->helper('customer');
    $this->load->helper('users');
    $this->load->helper('state');
    $this->load->helper('product_images');
    $this->load->helper('discount');
    $this->load->helper('zone');
    $this->load->helper('warehouse');

    $this->filter = getConfig('STOCK_FILTER');

    $this->wmsApi = is_true(getConfig('WMS_API'));
    $this->sokoApi = is_true(getConfig('SOKOJUNG_API'));
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'consign_code', ''),
      'customer' => get_filter('customer', 'consign_customer', ''),
      'user' => get_filter('user', 'consign_user', ''),
      'zone_code' => get_filter('zone', 'consign_zone', ''),
      'from_date' => get_filter('fromDate', 'consign_fromDate', ''),
      'to_date' => get_filter('toDate', 'consign_toDate', ''),
      'notSave' => get_filter('notSave', 'consign_notSave', NULL),
      'onlyMe' => get_filter('onlyMe', 'consign_onlyMe', NULL),
      'isExpire' => get_filter('isExpire', 'consign_isExpire', NULL),
      'isApprove' => get_filter('isApprove', 'consign_isApprove', 'all'),
			'warehouse' => get_filter('warehouse', 'consign_warehouse', ''),
			'wms_export' => get_filter('wms_export', 'consign_wms_export', 'all'),
      'is_backorder' => get_filter('is_backorder', 'consign_is_backorder', 'all'),
      'sap_status' => get_filter('sap_status', 'consign_sap_status', 'all')
    );

    $state = array(
      '1' => get_filter('state_1', 'consign_state_1', 'N'),
      '2' => get_filter('state_2', 'consign_state_2', 'N'),
      '3' => get_filter('state_3', 'consign_state_3', 'N'),
      '4' => get_filter('state_4', 'consign_state_4', 'N'),
      '5' => get_filter('state_5', 'consign_state_5', 'N'),
      '6' => get_filter('state_6', 'consign_state_6', 'N'),
      '7' => get_filter('state_7', 'consign_state_7', 'N'),
      '8' => get_filter('state_8', 'consign_state_8', 'N'),
      '9' => get_filter('state_9', 'consign_state_9', 'N')
    );

    $state_list = array();

    $button = array();

    for($i =1; $i <= 9; $i++)
    {
    	if($state[$i] === 'Y')
    	{
    		$state_list[] = $i;
    	}

      $btn = 'state_'.$i;
      $button[$btn] = $state[$i] === 'Y' ? 'btn-info' : '';
    }

    $button['not_save'] = empty($filter['notSave']) ? '' : 'btn-info';
    $button['only_me'] = empty($filter['onlyMe']) ? '' : 'btn-info';
    $button['is_expire'] = empty($filter['isExpire']) ? '' : 'btn-info';


    $filter['state_list'] = empty($state_list) ? NULL : $state_list;




		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->orders_model->count_rows($filter, 'C');
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$orders   = $this->orders_model->get_data($filter, $perpage, $this->uri->segment($segment), 'C');
    $ds       = array();
    if(!empty($orders))
    {
      foreach($orders as $rs)
      {
        $rs->customer_name = $this->customers_model->get_name($rs->customer_code);
        $rs->total_amount  = $this->orders_model->get_order_total_amount($rs->code);
        $rs->state_name    = get_state_name($rs->state);
        $rs->zone_name     = $this->zone_model->get_name($rs->zone_code);
        $ds[] = $rs;
      }
    }

    $filter['orders'] = $ds;
    $filter['state'] = $state;
    $filter['btn'] = $button;

		$this->pagination->initialize($init);
    $this->load->view('order_consign/consign_list', $filter);
  }

  //---- รายการรออนุมัติ
  public function get_un_approve_list()
  {
    $role = 'C'; //--- ฝากขายเปิดใบกำกับ
    $rows = $this->orders_model->count_un_approve_rows($role);
    $limit = empty($this->input->get('limit')) ? 20 : intval($this->input->get('limit'));
    $list = $this->orders_model->get_un_approve_list($role, $limit);
    $result_rows = empty($list) ? 0 :count($list);

    $ds = array();
    if(!empty($list))
    {
      foreach($list as $rs)
      {
        $arr = array(
          'date_add' => thai_date($rs->date_add),
          'code' => $rs->code,
          'customer' => $rs->customer_name
        );

        array_push($ds, $arr);
      }
    }

    $data = array(
      'result_rows' => $result_rows,
      'rows' => $rows,
      'data' => $ds
    );

    echo json_encode($data);
  }



  public function add_new()
  {
    $this->load->view('order_consign/consign_add');
  }


  public function add()
  {
    $sc = TRUE;

    $this->load->model('masters/warehouse_model');

    $data = json_decode($this->input->post('data'));

    if( ! empty($data))
    {
      $wmsWh = getConfig('WMS_WAREHOUSE');
      $sokoWh = getConfig('SOKOJUNG_WAREHOUSE');
      $book_code = getConfig('BOOK_CODE_CONSIGN_SO');

      $date_add = db_date($data->date_add);

      $code = $this->get_new_code($date_add);

      $role = 'C'; //--- C = ฝากขายเปิดใบกำกับ

      $zone = $this->zone_model->get($data->zone_code);

      if( ! empty($zone))
      {
        if( $zone->is_consignment == 1 )
        {
          $wh = $this->warehouse_model->get($data->warehouse_code);

          if( ! empty($wh))
          {
            $isSoko = $wh->code == $sokoWh ? TRUE : FALSE;
            $isWms = $wh->code == $wmsWh ? TRUE : FALSE;

            $is_wms = $isWms ? 1 : ($isSoko ? 2 : 0);

            $customer = $this->customers_model->get($data->customer_code);

            if( ! empty($customer))
            {
              $gp = $data->unit == '%' ? $data->gp.'%' : $data->gp;

              $ds = array(
                'date_add' => $date_add,
                'code' => $code,
                'role' => $role,
                'bookcode' => $book_code,
                'customer_code' => $customer->code,
                'customer_name' => $customer->name,
                'gp' => $gp,
                'user' => $this->_user->uname,
                'remark' => get_null($data->remark),
                'zone_code' => $zone->code,
                'warehouse_code' => $wh->code,
                'is_wms' => $is_wms
              );

              if( ! $this->orders_model->add($ds))
              {
                $sc = FALSE;
                $this->error = "เพิ่มเอกสารไม่สำเร็จ กรุณาลองใหม่อีกครั้ง";
              }
              else
              {
                $arr = array(
                'order_code' => $code,
                'state' => 1,
                'update_user' => $this->_user->uname
                );

                $this->order_state_model->add_state($arr);
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "Invalid customer code";
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "Invalid warehouse code";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "โซนที่ระบุไม่ใช่โซนฝากขายเทียม";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid zone code : โซนฝากขายปลายทางไม่ถูกต้อง";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing required parameter";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'code' => $sc === TRUE ? $code : NULL
    );

    echo json_encode($arr);
  }



  public function edit_order($code, $approve_view = NULL)
  {
    $this->load->model('approve_logs_model');
		$this->load->model('address/address_model');
		$this->load->helper('sender');

    $ds = array();

    $rs = $this->orders_model->get($code);

    if(!empty($rs))
    {
      $rs->customer_name = empty($rs->customer_name) ? $this->customers_model->get_name($rs->customer_code) : $rs->customer_name;
      $rs->total_amount  = $rs->doc_total <= 0 ? $this->orders_model->get_order_total_amount($rs->code) : $rs->doc_total;
      $rs->user          = $this->user_model->get_name($rs->user);
      $rs->state_name    = get_state_name($rs->state);
      $rs->zone_name = $this->zone_model->get_name($rs->zone_code);
    }

    $state = $this->order_state_model->get_order_state($code);
    $ost = array();
    if(!empty($state))
    {
      foreach($state as $st)
      {
        $ost[] = $st;
      }
    }

		$ship_to = $this->address_model->get_ship_to_address($rs->customer_code);
    $approve_logs = $this->approve_logs_model->get($code);
    $details = $this->orders_model->get_order_details($code);
    $tracking = $this->orders_model->get_order_tracking($code);
    $backlogs = $rs->is_backorder == 1 ? $this->orders_model->get_backlog_details($rs->code) : NULL;

    $ds['approve_view'] = $approve_view;
    $ds['approve_logs'] = $approve_logs;
    $ds['state'] = $ost;
    $ds['order'] = $rs;
    $ds['details'] = $details;
		$ds['addr']  = $ship_to;
    $ds['tracking'] = $tracking;
    $ds['backlogs'] = $backlogs;
		$ds['cancle_reason'] = ($rs->state == 9 ? $this->orders_model->get_cancle_reason($code) : NULL);
    $ds['allowEditDisc'] = getConfig('ALLOW_EDIT_DISCOUNT') == 1 ? TRUE : FALSE;
    $ds['allowEditPrice'] = getConfig('ALLOW_EDIT_PRICE') == 1 ? TRUE : FALSE;
    $ds['edit_order'] = TRUE; //--- ใช้เปิดปิดปุ่มแก้ไขราคาสินค้าไม่นับสต็อก
    $ds['is_api'] = is_api($rs->is_wms, $this->wmsApi, $this->sokoApi);
    $this->load->view('order_consign/consign_edit', $ds);
  }


  public function edit_detail($code)
  {
    $this->load->helper('product_tab');
    $ds = array();
    $rs = $this->orders_model->get($code);
    if($rs->state <= 3)
    {
      $rs->customer_name = $this->customers_model->get_name($rs->customer_code);
      $rs->zone_name = $this->zone_model->get_name($rs->zone_code);
      $ds['order'] = $rs;

      $details = $this->orders_model->get_order_details($code);
      $ds['details'] = $details;
      $ds['allowEditDisc'] = getConfig('ALLOW_EDIT_DISCOUNT') == 1 ? TRUE : FALSE;
      $ds['allowEditPrice'] = getConfig('ALLOW_EDIT_PRICE') == 1 ? TRUE : FALSE;
      $ds['edit_order'] = FALSE; //--- ใช้เปิดปิดปุ่มแก้ไขราคาสินค้าไม่นับสต็อก
      $this->load->view('order_consign/consign_edit_detail', $ds);
    }
  }




  public function update_order()
  {
    $sc = TRUE;

    $data = json_decode($this->input->post('data'));

    if( ! empty($data))
    {
			$this->load->model('masters/warehouse_model');
      $sokoWh = getConfig('SOKOJUNG_WAREHOUSE');
      $wmsWh = getConfig('WMS_WAREHOUSE');

      $code = $data->code;

      if( ! empty($code))
      {
        $wh = $this->warehouse_model->get($data->warehouse_code);

        if( ! empty($wh))
        {
          $isSoko = $wh->code == $sokoWh ? TRUE : FALSE;
          $isWms = $wh->code == $wmsWh ? TRUE : FALSE;

          $is_wms = $isWms ? 1 : ($isSoko ? 2 : 0);

          $zone = $this->zone_model->get($data->zone_code);

          if( ! empty($zone))
          {
            if( $zone->is_consignment)
            {
              $ds = array(
                'customer_code' => $data->customer_code,
                'customer_name' => $data->customer_name,
                'gp' => $data->gp,
                'date_add' => db_date($data->date_add),
                'remark' => get_null($data->remark),
                'zone_code' => $data->zone_code,
                'warehouse_code' => $wh->code,
      					'is_wms' => $is_wms,
      					'id_address' => NULL,
      					'id_sender' => NULL
              );

              if( ! $this->orders_model->update($code, $ds))
              {
                $sc = FALSE;
                $this->error = "Failed to update data";
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "โซนที่ระบุไม่ใช่โซนฝากขายเทียม";
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "Invlid zone code";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Invalid warehouse code";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid document number";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing requiered parameter";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }



  public function save($code)
  {
    $sc = TRUE;
    $order = $this->orders_model->get($code);
    //--- ถ้าออเดอร์เป็นแบบเครดิต
    if($order->is_term == 1)
    {
      //---- check credit balance
      $amount = $this->orders_model->get_order_total_amount($code);
      //--- creadit used
      $credit_used = $this->orders_model->get_sum_not_complete_amount($order->customer_code);
      //--- credit balance from sap
      $credit_balance = $this->customers_model->get_credit($order->customer_code);

      if($credit_used > $credit_balance)
      {
        $diff = $credit_used - $credit_balance;
        $sc = FALSE;
        $message = 'เครดิตคงเหลือไม่พอ (ขาด : '.number($diff, 2).')';
      }
    }

		if(empty($order->id_address))
		{
			$this->load->model('address/address_model');
			$id_address = NULL;

			if(!empty($order->customer_ref))
			{
				$id_address = $this->address_model->get_shipping_address_id_by_code($order->customer_ref);
			}
			else
			{
				$id_address = $this->address_model->get_default_ship_to_address_id($order->customer_code);
			}

			if(!empty($id_address))
			{
				$arr = array(
					'id_address' => $id_address
				);

				$this->orders_model->update($order->code, $arr);
			}
		}


		if(empty($order->id_sender))
		{
			$this->load->model('masters/sender_model');
			$id_sender = NULL;

			$sender = $this->sender_model->get_customer_sender_list($order->customer_code);

			if(!empty($sender))
			{
				if(!empty($sender->main_sender))
				{
					$id_sender = $sender->main_sender;
				}
			}

			if(!empty($id_sender))
			{
				$arr = array(
					'id_sender' => $id_sender
				);

				$this->orders_model->update($order->code, $arr);
			}
		}


    if($sc === TRUE)
    {
      $rs = $this->orders_model->set_status($code, 1);
      if($rs === FALSE)
      {
        $sc = FALSE;
        $message = 'บันทึกออเดอร์ไม่สำเร็จ';
      }
    }

    echo $sc === TRUE ? 'success' : $message;
  }


  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_CONSIGN_SO');
    $run_digit = getConfig('RUN_DIGIT_CONSIGN_SO');
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


  public function clear_filter()
  {
    $filter = array(
      'consign_code',
      'consign_customer',
      'consign_user',
      'consign_zone',
      'consign_fromDate',
      'consign_toDate',
      'consign_isApprove',
			'consign_warehouse',
			'consign_wms_export',
      'consign_is_backorder',
      'consign_sap_status',
      'consign_notSave',
      'consign_onlyMe',
      'consign_isExpire',
      'consign_state_1',
      'consign_state_2',
      'consign_state_3',
      'consign_state_4',
      'consign_state_5',
      'consign_state_6',
      'consign_state_7',
      'consign_state_8',
      'consign_state_9'
    );

    clear_filter($filter);
  }
}
?>

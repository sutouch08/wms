<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lend extends PS_Controller
{
  public $menu_code = 'ICLEND';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'REQUEST';
	public $title = 'เบิกยืมสินค้า';
  public $filter;
  public $error;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/lend';
    $this->load->model('orders/orders_model');
    $this->load->model('inventory/lend_model');
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
    $this->load->helper('warehouse');
  }


  public function index()
  {
    $filter = array(
      'code'      => get_filter('code', 'lens_code', ''),
      'empName'  => get_filter('empName', 'lend_emp', ''),
      'user'      => get_filter('user', 'lend_user', ''),
      'user_ref'  => get_filter('user_ref', 'lend_user_ref', ''),
      'from_date' => get_filter('fromDate', 'lend_fromDate', ''),
      'to_date'   => get_filter('toDate', 'lend_toDate', ''),
      'isApprove' => get_filter('isApprove', 'lend_isApprove', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

    $role     = 'L'; //--- L = เบิกยืมสินค้า
		$segment  = 4; //-- url segment
		$rows     = $this->orders_model->count_rows($filter, $role);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$orders   = $this->orders_model->get_data($filter, $perpage, $this->uri->segment($segment), $role);
    $ds       = array();
    if(!empty($orders))
    {
      foreach($orders as $rs)
      {
        $rs->total_amount  = $this->orders_model->get_order_total_amount($rs->code);
        $rs->state_name    = get_state_name($rs->state);
        $ds[] = $rs;
      }
    }

    $filter['orders'] = $ds;

		$this->pagination->initialize($init);
    $this->load->view('lend/lend_list', $filter);
  }


  public function add_new()
  {
    $this->load->view('lend/lend_add');
  }



  public function add()
  {
    if($this->input->post('empID'))
    {
      $book_code = getConfig('BOOK_CODE_LEND');
      $date_add = db_date($this->input->post('date'));

      if($this->input->post('code'))
      {
        $code = $this->input->post('code');
      }
      else
      {
        $code = $this->get_new_code($date_add);
      }

      $role = 'L'; //--- L = ยืมสินค้า
      $has_term = 1; //--- ถือว่าเป็นเครดิต
      $zone = $this->zone_model->get($this->input->post('zone_code'));

      $ds = array(
        'date_add' => $date_add,
        'code' => $code,
        'role' => $role,
        'bookcode' => $book_code,
        'customer_code' => NULL,
        'user' => get_cookie('uname'),
        'user_ref' => $this->input->post('user_ref'),
        'remark' => $this->input->post('remark'),
        'empID' => $this->input->post('empID'),
        'empName' => $this->input->post('empName'),
        'zone_code' => $zone->code, //---- zone ที่จะโอนสินค้าไปเก็บ
        'warehouse_code' => $this->input->post('warehouse') //--- คลังที่จะจัดสินค้าออก
      );

      if($this->orders_model->add($ds) === TRUE)
      {
        $arr = array(
          'order_code' => $code,
          'state' => 1,
          'update_user' => get_cookie('uname')
        );

        $this->order_state_model->add_state($arr);

        redirect($this->home.'/edit_detail/'.$code);
      }
      else
      {
        set_error('เพิ่มเอกสารไม่สำเร็จ กรุณาลองใหม่อีกครั้ง');
        redirect($this->home.'/add_new');
      }
    }
    else
    {
      set_error('ไม่พบข้อมูลลูกค้า กรุณาตรวจสอบ');
      redirect($this->home.'/add_new');
    }
  }



  public function edit_order($code, $approve_view = NULL)
  {
    $this->load->model('approve_logs_model');
    $ds = array();
    $rs = $this->orders_model->get($code);
    if(!empty($rs))
    {
      $rs->total_amount  = $this->orders_model->get_order_total_amount($rs->code);
      $rs->user          = $this->user_model->get_name($rs->user);
      $rs->state_name    = get_state_name($rs->state);
      $rs->zone_name     = $this->zone_model->get_name($rs->zone_code);
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

    $details = $this->orders_model->get_order_details($code);
    $ds['state'] = $ost;
    $ds['order'] = $rs;
    $ds['details'] = $details;
    $ds['approve_view'] = $approve_view;
    $ds['approve_logs'] = $this->approve_logs_model->get($code);
    $this->load->view('lend/lend_edit', $ds);
  }



  public function update_order()
  {
    $sc = TRUE;

    if($this->input->post('order_code'))
    {
      $code = $this->input->post('order_code');
      $order = $this->orders_model->get($code);
      if(!empty($order))
      {
        if($order->state > 1)
        {
          $ds = array(
            'remark' => trim($this->input->post('remark'))
          );
        }
        else
        {
          $ds = array(
            'empID' => $this->input->post('empID'),
            'empName' => $this->input->post('empName'),
            'date_add' => db_date($this->input->post('date_add')),
            'user_ref' => $this->input->post('user_ref'),
            'zone_code' => $this->input->post('zone_code'),
            'remark' => $this->input->post('remark'),
            'warehouse_code' => $this->input->post('warehouse_code'),
            'status' => 0
          );
        }

        if(! $this->orders_model->update($code, $ds))
        {
          $sc = FALSE;
          $this->error = "ปรับปรุงข้อมูลไม่สำเร็จ";
        }

      }
      else
      {
        $sc = FALSE;
        $this->error = "เลขที่เอกสารไม่ถูกต้อง : {$code}";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = 'ไม่พบเลขที่เอกสาร';
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }



  public function edit_detail($code)
  {
    $this->load->helper('product_tab');
    $ds = array();
    $rs = $this->orders_model->get($code);
    if($rs->state <= 3)
    {
      $rs->zone_name = $this->zone_model->get_name($rs->zone_code);
      $details = $this->orders_model->get_order_details($code);
      $ds['order'] = $rs;
      $ds['details'] = $details;
      $this->load->view('lend/lend_edit_detail', $ds);
    }
  }



  public function save($code)
  {
    $sc = TRUE;
    $order = $this->orders_model->get($code);

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
    $prefix = getConfig('PREFIX_LEND');
    $run_digit = getConfig('RUN_DIGIT_LEND');
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



  public function set_never_expire()
  {
    $code = $this->input->post('order_code');
    $option = $this->input->post('option');
    $rs = $this->orders_model->set_never_expire($code, $option);
    echo $rs === TRUE ? 'success' : 'ทำรายการไม่สำเร็จ';
  }


  public function un_expired()
  {
    $code = $this->input->post('order_code');
    $rs = $this->orders_model->un_expired($code);
    echo $rs === TRUE ? 'success' : 'ทำรายการไม่สำเร็จ';
  }

  public function clear_filter()
  {
    $filter = array(
      'lend_code',
      'lend_emp',
      'lend_user',
      'lend_user_ref',
      'lend_fromDate',
      'lend_toDate',
      'lend_isApprove'
    );

    clear_filter($filter);
  }
}
?>

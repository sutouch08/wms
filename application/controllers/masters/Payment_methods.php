<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_methods extends PS_Controller
{
  public $menu_code = 'DBPAYM';
	public $menu_group_code = 'DB';
	public $title = 'Payment channels';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/payment_methods';
    $this->load->model('masters/payment_methods_model');
    $this->load->helper('payment_method');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'payment_code', ''),
      'name' => get_filter('name', 'payment_name', ''),
      'term' => get_filter('term', 'payment_term', 'all'),
      'role' => get_filter('role', 'payment_role', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();

		$segment = 4; //-- url segment
		$rows = $this->payment_methods_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$filter['data'] = $this->payment_methods_model->get_list($filter, $perpage, $this->uri->segment($segment));

		$this->pagination->initialize($init);
    $this->load->view('masters/payment_methods/payment_methods_list', $filter);
  }


  public function add_new()
  {
    $this->load->view('masters/payment_methods/payment_methods_add');
  }


  public function add()
  {
    $sc = TRUE;

    if($this->input->post('code'))
    {
      $code = $this->input->post('code');
      $name = $this->input->post('name');
      $role = $this->input->post('role');
      $has_term = ($role == 4 OR $role == 5) ? 1 : 0;

      $ds = array(
        'code' => $code,
        'name' => $name,
        'has_term' => $has_term,
        'role' => $role
      );

      if($sc === TRUE && $this->payment_methods_model->is_exists($code))
      {
        $sc = FALSE;
        set_error('exists', $code);
      }

      if($sc === TRUE && $this->payment_methods_model->is_exists_name($name))
      {
        $sc = FALSE;
        set_error('exists', $name);
      }

      if($sc === TRUE)
      {
        if( ! $this->payment_methods_model->add($ds))
        {
          $sc = FALSE;
          set_error('insert');
        }
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $this->_response($sc);
  }



  public function edit($code)
  {
    $data = $this->payment_methods_model->get_payment_methods($code);

    $this->load->view('masters/payment_methods/payment_methods_edit', $data);
  }



  public function update()
  {
    $sc = TRUE;

    if($this->input->post('id'))
    {
      $id = $this->input->post('id');
      $code = $this->input->post('code');
      $name = trim($this->input->post('name'));
      $role = $this->input->post('role');
      $has_term = ($role == 4 OR $role == 5) ? 1 : 0;

      $ds = array(
        'code' => $code,
        'name' => $name,
        'role' => $role,
        'has_term' => $has_term
      );

      if($sc === TRUE && $this->payment_methods_model->is_exists($code, $id))
      {
        $sc = FALSE;
        set_error('exists', $code);
      }

      if($sc === TRUE && $this->payment_methods_model->is_exists_name($name, $id))
      {
        $sc = FALSE;
        set_error('exists', $name);
      }

      if($sc === TRUE)
      {
        if( ! $this->payment_methods_model->update_by_id($id, $ds))
        {
          $sc = FALSE;
          set_error('update');
        }
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    $this->_response($sc);
  }



  public function delete($code)
  {
    if($code != '')
    {
      if($this->payment_methods_model->delete($code))
      {
        set_message('Payment channels deleted');
      }
      else
      {
        set_error('Cannot delete payment channels');
      }
    }
    else
    {
      set_error('payment channels not found');
    }

    redirect($this->home);
  }



  //--- เช็คว่าการชำระเงินเป็นแบบเครดิตหรือไม่
  public function is_credit_payment($code)
  {
    //---- ตรวจสอบว่าเป็นเครดิตหรือไม่
    $rs = $this->payment_methods_model->has_term($code);
    echo $rs === TRUE ? 1 : 0;
  }


  public function clear_filter()
	{
		clear_filter(array('payment_code', 'payment_name', 'payment_term', 'payment_role'));
		echo 'done';
	}

}//--- end class
 ?>

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Return_request extends PS_Controller
{
  public $menu_code = 'ICRTRQ';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'RETURN';
	public $title = 'ใบสั่งคืนสินค้า';
  public $filter;
  public $error;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/return_request';
    $this->load->model('inventory/return_request_model');
    $this->load->model('masters/products_model');
  }


  public function index()
  {
    $filter = array(
      'code'    => get_filter('code', 'rq_code', ''),
      'from_date' => get_filter('from_date', 'rq_from_date', ''),
      'to_date' => get_filter('to_date', 'rq_to_date', ''),
      'status' => get_filter('status', 'rq_status', 'all'),
			'remark' => get_filter('remark', 'rq_remark', '')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->return_request_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$docs = $this->return_request_model->get_list($filter, $perpage, $this->uri->segment($segment));

		if(!empty($docs))
		{
			foreach($docs as $doc)
			{
				$doc->qty = $this->return_request_model->get_sum_request_qty($doc->code);
			}
		}

    $filter['docs'] = $docs;
		$this->pagination->initialize($init);
    $this->load->view('inventory/return_request/return_request_list', $filter);
  }



	public function add_new()
	{
		if($this->pm->can_add)
		{
			$this->load->view('inventory/return_request/return_request_add');
		}
		else
		{
			$this->deny_page();
		}
	}


	public function add()
	{
		$date_add = db_date($this->input->post('date_add'));
		$remark = get_null(trim($this->input->post('remark')));

		$code = $this->get_new_code($date_add);

		$arr = array(
			'code' => $code,
			'date_add' => $date_add,
			'remark' => $remark,
			'user' => get_cookie('uname')
		);

		if(!$this->return_request_model->add($arr))
		{
			echo "Insert failed";
		}
		else
		{
			$arr = array('code' => $code);
			echo json_encode($arr);
		}
	}


	public function edit($code)
	{
		if($this->pm->can_add OR $this->pm->can_edit)
		{
			$doc = $this->return_request_model->get($code);
			if(!empty($doc))
			{
				$details = $this->return_request_model->get_details($code);

				$ds = array(
					'doc' => $doc,
					'details' => $details
				);

				$this->load->view('inventory/return_request/return_request_edit', $ds);
			}
			else
			{
				$this->error_page('Invalid Document Code : '.$code);
			}
		}
		else
		{
			$this->deny_page();
		}
	}



	public function update()
	{
		$sc = TRUE;
		$code = $this->input->post('code');
		$date_add = db_date($this->input->post('date_add'));
		$remark = get_null(trim($this->input->post('remark')));

		if(!empty($code))
		{
			$arr = array(
				'date_add' => $date_add,
				'remark' => $remark
			);

			if(! $this->return_request_model->update($code, $arr))
			{
				$sc = FALSE;
				$error = $this->db->error();
				$this->error = "Update failed : ".$error['message'];
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter : code";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}






  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_RETURN_REQUEST');
    $run_digit = getConfig('RUN_DIGIT_RETURN_REQUEST');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->return_request_model->get_max_code($pre);

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
      'rq_code',
			'rq_from_date',
			'rq_to_date',
			'rq_status',
			'rq_remark'
    );

    clear_filter($filter);
  }


} //--- end class
?>

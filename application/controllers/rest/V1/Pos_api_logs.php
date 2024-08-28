<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pos_api_logs extends PS_Controller
{
	public $title = 'POS API Logs';
	public $menu_code = 'POSAPILOG';
	public $menu_group_code = 'POSLOG';
  public $menu_sub_group_code = '';
  public $filter;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'rest/V1/pos_api_logs';
		$this->logs = $this->load->database('logs', TRUE); //--- Temp database
  	$this->load->model('rest/V1/pos_api_logs_model');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'pos_logs_code', ''),
      'status' => get_filter('status', 'pos_logs_status', 'all'),
			'type' => get_filter('type', 'pos_logs_type', 'all'),
			'action' => get_filter('action', 'pos_logs_action', 'all'),
			'from_date' => get_filter('from_date', 'pos_logs_from_date', ''),
			'to_date' => get_filter('to_date', 'pos_logs_to_date', '')
    );

		if($this->input->post('search'))
		{
			redirect($this->home);
		}
		else
		{
			//--- แสดงผลกี่รายการต่อหน้า
			$perpage = get_rows();

			$segment  = 5; //-- url segment
			$rows     = $this->pos_api_logs_model->count_api_rows($filter);
			//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
			$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
			$logs   = $this->pos_api_logs_model->get_api_list($filter, $perpage, $this->uri->segment($segment));

			$filter['logs'] = $logs;

			$this->pagination->initialize($init);
			$this->load->view('rest/V1/pos/api_logs_view', $filter);
		}

  }


	public function view_detail($id)
	{
		$ds = $this->pos_api_logs_model->get_api_logs($id);

		$this->load->view('rest/V1/sokojung/api_logs_detail', $ds);
	}

	public function clear_filter()
	{
		$filter = array(
			'pos_logs_code',
			'pos_logs_status',
			'pos_logs_type',
			'pos_logs_action',
			'pos_logs_from_date',
			'pos_logs_to_date'
		);

		clear_filter($filter);
		echo "done";
	}

} //--- end classs
?>

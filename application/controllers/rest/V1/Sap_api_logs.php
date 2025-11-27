<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sap_api_logs extends PS_Controller
{
	public $title = 'API Logs';
	public $menu_code = 'SAPAPILOG';
	public $menu_group_code = 'APILOG';
  public $menu_sub_group_code = '';
  public $filter;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'rest/V1/sap_api_logs';
  	$this->load->model('rest/V1/sap_api_logs_model');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'sap_logs_code', ''),
			'api_path' => get_filter('api_path', 'sap_logs_path', 'all'),
      'status' => get_filter('status', 'sap_logs_status', 'all'),
			'type' => get_filter('type', 'sap_logs_type', 'all'),
			'action' => get_filter('action', 'sap_logs_action', 'all'),
			'from_date' => get_filter('from_date', 'sap_from_date', ''),
			'to_date' => get_filter('to_date', 'sap_to_date', '')
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
			$rows     = $this->sap_api_logs_model->count_rows($filter);
			//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
			$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
			$logs   = $this->sap_api_logs_model->get_list($filter, $perpage, $this->uri->segment($segment));

			$filter['logs'] = $logs;

			$this->pagination->initialize($init);
			$this->load->view('rest/V1/sap/api_logs_view', $filter);
		}
  }


	public function view_detail($id)
	{
		$ds = $this->sap_api_logs_model->get_logs($id);

		$this->load->view('rest/V1/sap/api_logs_detail', $ds);
	}

	public function clear_filter()
	{
		$filter = array(
			'sap_logs_code',
			'sap_logs_path',
			'sap_logs_status',
			'sap_logs_type',
			'sap_logs_action',
			'sap_from_date',
			'sap_to_date'
		);

		return clear_filter($filter);
	}

} //--- end classs
?>

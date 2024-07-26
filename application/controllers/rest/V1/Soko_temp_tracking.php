<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Soko_temp_tracking extends PS_Controller
{
	public $menu_code = 'SOKODOTK';
	public $menu_group_code = 'SOKOJUNG';
  public $menu_sub_group_code = '';
	public $title = 'SOKO Delivery Tracking';
  public $filter;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'rest/V1/soko_temp_tracking';
		$this->wms = $this->load->database('wms', TRUE); //--- Temp database
		$this->load->model('rest/V1/soko_temp_tracking_model');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'do_code', ''),
      'product_code' => get_filter('product_code', 'pd_code', ''),
			'carton_code' => get_filter('carton_code', 'carton_code', ''),
			'tracking_no' => get_filter('tracking_no', 'tracking_no', ''),
			'courier' => get_filter('courier', 'courier', ''),
			'from_date' => get_filter('from_date', 'do_from_date', ''),
			'to_date' => get_filter('to_date', 'do_to_date', '')
    );

		if($this->input->post('search'))
		{
			redirect($this->home);
		}
		else
		{
			//--- แสดงผลกี่รายการต่อหน้า
			$perpage = get_rows();
			//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
			if($perpage > 300)
			{
				$perpage = 20;
			}

			$segment  = 5; //-- url segment
			$rows     = $this->soko_temp_tracking_model->count_rows($filter);
			//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
			$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
			$list   = $this->soko_temp_tracking_model->get_list($filter, $perpage, $this->uri->segment($segment));

	    $filter['list'] = $list;

			$this->pagination->initialize($init);

	    $this->load->view('rest/V1/temp_tracking/temp_tracking_list', $filter);
		}

  }


	public function clear_filter()
	{
		$filter = array(
			'do_code',
			'pd_code',
			'carton_code',
			'tracking_no',
			'courier',
			'do_from_date',
			'do_to_date'
		);

		clear_filter($filter);

		return TRUE;
	}

} //--- end classs
?>

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Wms_temp_delivery extends PS_Controller
{
	public $menu_code = 'WMSTDO';
	public $menu_group_code = 'WMS';
  public $menu_sub_group_code = '';
	public $title = 'WMS Temp Delivery';
  public $filter;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'rest/V1/wms_temp_delivery';
		$this->wms = $this->load->database('wms', TRUE); //--- Temp database
  	$this->load->model('rest/V1/wms_error_logs_model');
		$this->load->model('rest/V1/wms_temp_order_model');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'do_code', ''),
      'status' => get_filter('status', 'do_status', 'all'),
			'reference' => get_filter('reference', 'do_reference', '')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 5; //-- url segment
		$rows     = $this->wms_temp_order_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$orders   = $this->wms_temp_order_model->get_list($filter, $perpage, $this->uri->segment($segment));

    $filter['orders'] = $orders;

		$this->pagination->initialize($init);

    $this->load->view('rest/V1/temp_delivery/temp_delivery_list', $filter);
  }



	  public function get_detail($id)
	  {
			$order = $this->wms_temp_order_model->get($id);

	    $ds['details'] = $this->wms_temp_order_model->get_details($id);
			$ds['code'] = !empty($order) ? $order->code : NULL;
	    $this->load->view('rest/V1/temp_delivery/temp_delivery_detail', $ds);
	  }



	public function clear_filter()
	{
		$filter = array(
			'do_code',
			'do_status',
			'do_reference'
		);

		clear_filter($filter);
		echo "done";
	}


	public function delete($id) {
		$sc = TRUE;
		$rs = $this->wms_temp_order_model->delete($id);
		if(! $rs)
		{
			$sc = FALSE;
			$this->error = "Delete failed";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}

} //--- end classs
?>
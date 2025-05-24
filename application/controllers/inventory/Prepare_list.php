<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Prepare_list extends PS_Controller
{
  public $menu_code = 'ICPREP';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'CHECK';
	public $title = 'ตรวจสอบ รายการจัดสินค้า';
  public $filter;
  public $error;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/prepare_list';
    $this->load->model('inventory/prepare_list_model');
    $this->load->model('masters/zone_model');
    $this->load->helper('warehouse');
  }


  public function index()
  {
    $filter = array(
      'order_code' => get_filter('order_code', 'order_code', ''),
      'warehouse_code' => get_filter('warehouse_code', 'warehouse_code', 'all'),
      'zone_code' => get_filter('zone_code', 'zone_code', ''),
      'pd_code' => get_filter('pd_code', 'pd_code'),
      'user' => get_filter('user', 'user', 'all'),
      'from_date' => get_filter('from_date', 'from_date', ''),
      'to_date' => get_filter('to_date', 'to_date', '')
    );

    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      $perpage = get_rows();
      $segment = 4; //-- url segment
  		$rows = $this->prepare_list_model->count_rows($filter);
  		$init = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
  		$this->pagination->initialize($init);
      $filter['data'] = $this->prepare_list_model->get_list($filter, $perpage, $this->uri->segment($segment));
      $this->load->view('inventory/prepare_list/prepare_list_view', $filter);
    }
  }


  public function export_filter()
  {
    // This also needs to be increased in some cases. Can be changed to a higher value as per need)
    ini_set('memory_limit','2048M');

    $token = $this->input->post('token');

    $ds = array(
      'order_code' => $this->input->post('order_code'),
      'product_code' => $this->input->post('product_code'),
      'warehouse_code' => $this->input->post('warehouse_code'),
      'zone_code' => $this->input->post('zone_code'),
      'from_date' => $this->input->post('from_date'),
      'to_date' => $this->input->post('to_date'),
      'user' => $this->input->post('user')
    );
  
    $header = array(
      'OrderDate', 'Order No', 'Reference', 'Channels', 'Item', 'Qty', 'Status', 'Picking Date', 'Warehouse', 'Zone', 'User'
    );

    $list = $this->prepare_list_model->get_export_data($ds);

    // Create a file pointer
    $f = fopen('php://memory', 'w');
    $delimiter = ",";
    fputs($f, $bom = ( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
    fputcsv($f, $header, $delimiter);

    if( ! empty($list))
    {
      $stateName = $this->prepare_list_model->state_name_array();
      $channelsName = $this->prepare_list_model->channels_name_array();
      $userName = [];
      $orders = [];

      foreach($list as $rs)
      {
        if(empty($orders[$rs->order_code]))
        {
          $orders[$rs->order_code] = $this->prepare_list_model->get_order($rs->order_code);
        }

        if( ! empty($orders[$rs->order_code]))
        {
          if(empty($userName[$rs->user]))
          {
            $userName[$rs->user] = $this->user_model->get_name($rs->user);
          }

          $order = $orders[$rs->order_code];

          $arr = array(
            thai_date($order->date_add),
            $order->code,
            $order->reference,
            empty($channelsName[$order->channels_code]) ? $order->channels_code : $channelsName[$order->channels_code],
            $rs->product_code,
            $rs->qty,
            $stateName[$order->state],
            thai_date($rs->date_upd, TRUE),
            $rs->warehouse_code,
            $rs->zone_code,
            empty($userName[$rs->user]) ? $rs->user : $userName[$rs->user]
          );

          fputcsv($f, $arr, $delimiter);
        }
      }
      // $memuse = (memory_get_usage() / 1024) / 1024;
      // $arr = array('memory usage', round($memuse, 2).' MB');
      // fputcsv($f, $arr, $delimiter);
    }

    //--- Move to begin of file
    fseek($f, 0);

    setToken($token);

    $file_name = "Picking details.csv";
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="'.$file_name.'"');

    //output all remaining data on a file pointer
    fpassthru($f); ;

    exit();
  }


  function clear_filter(){
    $filter = array('order_code', 'pd_code', 'warehouse_code', 'zone_code', 'user', 'from_date', 'to_date');
    clear_filter($filter);
  }


} //--- end class
?>

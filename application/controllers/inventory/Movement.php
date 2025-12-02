<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Movement extends PS_Controller
{
  public $menu_code = 'ICCKMV';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'CHECK';
	public $title = 'ตรวจสอบ Movement';
  public $filter;
  public $error;
  public $segment = 4;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/movement';
    $this->load->model('inventory/movement_model');
    $this->load->helper('warehouse');
  }


  public function index()
  {
    $filter = array(
      'reference' => get_filter('reference', 'mv_reference', ''),
      'warehouse_code' => get_filter('warehouse_code', 'mv_warehouse_code', 'all'),
      'zone_code' => get_filter('zone_code', 'mv_zone_code', ''),
      'product_code' => get_filter('product_code', 'mv_product_code', ''),
      'from_date' => get_filter('from_date', 'mv_from_date', ''),
      'to_date' => get_filter('to_date', 'mv_to_date', ''),
      'range' => get_filter('range', 'mv_range', 'all')
    );

    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      $filter['id'] = $filter['range'] == 'all' ? NULL : $this->movement_model->get_max_id($filter['range']);
      $perpage = get_rows();
      $rows = $this->movement_model->count_rows($filter);
      $init = pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
      $this->pagination->initialize($init);
      $filter['data'] = $this->movement_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
      $this->load->view('inventory/movement/movement_list', $filter);
    }
  }


  public function export_filter()
  {
    // This also needs to be increased in some cases. Can be changed to a higher value as per need)
    ini_set('memory_limit','2048M');

    $token = $this->input->post('token');
    $range = $this->input->post('range');

    $id = $range == 'all' ? NULL : $this->movement_model->get_max_id($range);

    $ds = array(
      'reference' => $this->input->post('reference'),
      'product_code' => $this->input->post('product_code'),
      'warehouse_code' => $this->input->post('warehouse_code'),
      'zone_code' => $this->input->post('zone_code'),
      'from_date' => $this->input->post('from_date'),
      'to_date' => $this->input->post('to_date'),
      'id' => $id
    );


    $header = array(
      'Reference', 'Item', 'Warehouse', 'Zone', 'Move In', 'Move Out', 'Move Date'
    );

    $list = $this->movement_model->get_export_data($ds);

    // Create a file pointer
    $f = fopen('php://memory', 'w');
    $delimiter = ",";
    fputs($f, $bom = ( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
    fputcsv($f, $header, $delimiter);

    if( ! empty($list))
    {
      foreach($list as $rs)
      {
        $arr = array(
          $rs->reference,
          $rs->product_code,
          $rs->warehouse_code,
          $rs->zone_code,
          $rs->move_in,
          $rs->move_out,
          thai_date($rs->date_upd, TRUE)
        );

        fputcsv($f, $arr, $delimiter);
      }
      // $memuse = (memory_get_usage() / 1024) / 1024;
      // $arr = array('memory usage', round($memuse, 2).' MB');
      // fputcsv($f, $arr, $delimiter);
    }

    //--- Move to begin of file
    fseek($f, 0);

    setToken($token);

    $file_name = "Stock_movement.csv";
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="'.$file_name.'"');

    //output all remaining data on a file pointer
    fpassthru($f); ;

    exit();
  }



  public function clear_filter()
  {
    $filter = array(
      'mv_reference',
      'mv_warehouse_code',
      'mv_zone_code',
      'mv_product_code',
      'mv_from_date',
      'mv_to_date',
      'mv_range'
    );

    return clear_filter($filter);
  }

} // end class
?>

<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Packing_video extends PS_Controller
{
  public $menu_code = 'ICVIDEO';
  public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'CHECK';
  public $title = 'Packing Video';
  public $filter;
  public $error;
  public $segment = 4; 

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url() . 'inventory/packing_video';
    $this->load->model('inventory/packing_video_model');
  }


  public function index()
  {
    $filter = array(
      'order_code' => get_filter('order_code', 'v_order_code', ''),
      'role' => get_filter('role', 'v_role', 'all'),      
      'from_date' => get_filter('from_date', 'v_from_date', ''),
      'to_date' => get_filter('to_date', 'v_to_date', '')
    );
    
    $perpage = get_rows();        
    $rows = $this->packing_video_model->count_rows($filter);
    $filter['data'] = $this->packing_video_model->get_list($filter, $perpage, $this->uri->segment($this->segment));    
    $init = pagination_config($this->home . '/index/', $rows, $perpage, $this->segment);    
    $this->pagination->initialize($init);
    $this->load->view('inventory/packing_video/packing_video_list', $filter);
  }
  

  function clear_filter()
  {
    $filter = array('v_order_code', 'v_role', 'v_from_date', 'v_to_date');
    return clear_filter($filter);
  }
} //--- end class

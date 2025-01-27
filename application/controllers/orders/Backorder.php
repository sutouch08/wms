<?php
class Backorder extends PS_Controller
{
  public $menu_code = 'SOBACK';
	public $menu_group_code = 'SO';
  public $menu_sub_group_code = 'ORDER';
	public $title = 'Backorder';
  public $filter;
  public $error;
  public $segment = 4;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'orders/backorder';
    $this->load->model('orders/backorder_model');
    $this->load->model('masters/channels_model');

    $this->load->helper('order');
    $this->load->helper('channels');
    $this->load->helper('warehouse');
    $this->load->helper('state');
  }


  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'bo_code', ''),
      'customer' => get_filter('customer', 'bo_customer', ''),
      'from_date' => get_filter('from_date', 'bo_from_date', ''),
      'to_date' => get_filter('to_date', 'bo_to_date', ''),
      'warehouse' => get_filter('warehouse', 'bo_warehouse', 'all'),
      'role' => get_filter('role', 'bo_role', 'all'),
      'channels' => get_filter('channels', 'bo_channels', 'all')
    );

    if($this->input->post('search'))
    {
      redirect($this->home);
    }
    else
    {
      //--- แสดงผลกี่รายการต่อหน้า
  		$perpage = get_rows();
      $rows = $this->backorder_model->count_rows($filter);
      $init = pagination_config($this->home.'/index/', $rows, $perpage, $this->segment);
      $this->pagination->initialize($init);
      $filter['orders'] = $this->backorder_model->get_list($filter, $perpage, $this->uri->segment($this->segment));
      $filter['channelsList'] = $this->channels_model->get_channels_array();

      $this->load->view('backorder/backorder_list', $filter);
    }

  }


}
?>

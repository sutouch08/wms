<?php
class Auto_check_tiktok_status extends CI_Controller
{
  public $home;
  public $mc;
  public $ms;
  public $isViewer = FALSE;
  public $notibars = FALSE;
  public $menu_code = NULL;
  public $menu_group_code = NULL;
  public $pm;
  public $error;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'auto/Auto_check_tiktok_status';
    $this->load->model('orders/orders_model');
    $this->pm = new stdClass();
    $this->pm->can_view = 1;
  }

  public function index()
  {
    $max_id = $this->get_max_order_id();

    $id = $max_id > 100000 ? $max_id - 10000;

    $this->db
    ->select('code, reference')
    ->where('id >', $id)
    ->where('role', 'S')
    ->where('channels_code', '0009')
    ->where('is_cancled', 0)
    ->where_in('state', [3, 4, 5, 6, 7])
    ->order_by('id', 'DESC')
    ->limit(100)
    ->
  }


  public function get_max_order_id()
  {
    $rs = $this->db->select_max('id')->get('orders');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->id;
    }

    return 1000000;
  }

  public function update_status()
	{
    $sc = TRUE;
    $code = $this->input->post('code');
    $status = $this->input->post('status');
    $message = $this->input->post('message');

    $ds = array(
      'status' => $status,
      'message' => $message
    );

		if( ! $this->db->where('code', $code)->update('auto_send_to_sap_order', $ds))
    {
      $sc = FALSE;
      $this->error = "Update false";
    }

    echo $sc === TRUE ? 'success' : $this->error;
	}

} //--- end class
 ?>

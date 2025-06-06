<?php
class Auto_cancle_orders extends CI_Controller
{
  public $home;
  public $mc;
  public $ms;
  public $title = "Auto Cancel orders";
  public $isViewer = FALSE;
  public $notibars = FALSE;
  public $menu_code = NULL;
  public $menu_group_code = NULL;
  public $pm;
  public $error;

  public function __construct()
  {
    parent::__construct();
    $this->ms = $this->load->database('ms', TRUE); //--- SAP database
    $this->mc = $this->load->database('mc', TRUE); //--- Temp Database
    $this->home = base_url().'auto/auto_cancle_orders';   
    $this->load->model('orders/orders_model');
    $this->load->model('orders/order_state_model');
    $this->load->model('inventory/prepare_model');
    $this->load->model('inventory/qc_model');
    $this->load->model('inventory/buffer_model');
    $this->load->model('inventory/cancle_model');
    $this->load->model('inventory/movement_model');
    $this->load->model('masters/products_model');

    $this->pm = new stdClass();
    $this->pm->can_view = 1;
  }

  public function index()
  {
    $ds['data'] = NULL;
    $all = $this->db->where('status !=', 1)->count_all_results('auto_send_to_sap_order');
    $rs = $this->db->where('status !=', 1)->limit(50)->get('auto_send_to_sap_order');

    $ds['count'] = $rs->num_rows();
    $ds['all'] = $all;
    $ds['data'] = $rs->result();

    $this->load->view('auto/auto_cancle_orders', $ds);
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

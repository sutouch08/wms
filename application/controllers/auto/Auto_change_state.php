<?php
class Auto_change_state extends CI_Controller
{
  public $home;
  public $mc;
  public $ms;
  public $title = "Auto comfirm order";
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
    $this->home = base_url().'auto/auto_change_state';
    $this->load->model('inventory/delivery_order_model');
    $this->load->model('orders/orders_model');
    $this->load->model('orders/order_state_model');
		$this->load->library('export');
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

    $this->load->view('auto/auto_change_state', $ds);
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

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller
{
  public $menu_code = '';
	public $menu_group_code = '';
  public $menu_sub_group_code = '';
  public $ms;
  public $title = "Dashboard";
  public $home;
  public $isViewer = FALSE;
  public $notibars = 0;
  public $pm;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url()."inventory/dashboard";
    $this->load->model('inventory/dashboard_model');
    $this->load->library('user_agent');

    $this->is_mobile = $this->agent->is_mobile();
    $this->pm = (object) array('can_view' => 1);
  }


  public function index()
  {
    // if($this->is_mobile)
    // {
    //   $this->title = "Inventory Orders";
    //   $this->load->view('inventory/dashboard/mobile/dashboard_mobile');
    // }
    // else
    // {
    //   $this->load->view('inventory/dashboard/dashboard');
    // }

    $this->load->view('inventory/dashboard/dashboard');
  }


  public function count_orders()
  {
    $sc = TRUE;

    $rows = 0;

    $channels = array(
      'offline' => 'offline',
      'online' => 'online',
      'tiktok' => '0009',
      'shopee' => 'SHOPEE',
      'lazada' => 'LAZADA'
    );

    $state = $this->input->get('state');
    $ch = $this->input->get('channels');

    if( ! empty($channels[$ch]))
    {
      $rows = $this->dashboard_model->count_orders_state($channels[$ch], $state);
    }
    else
    {
      $sc = FALSE;
      $this->error = "Invalid channels";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'rows' => $rows
    );

    echo json_encode($arr);
  }



} //--- end class
?>

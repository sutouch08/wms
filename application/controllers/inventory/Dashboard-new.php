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
    if($this->is_mobile)
    {
      $this->title = "Inventory Orders";
      $this->load->view('inventory/dashboard/mobile/dashboard_mobile');
    }
    else
    {      
      $data = [
        'total_0' => 0,
        'total_3' => 0,
        'total_4' => 0,
        'total_5' => 0,
        'total_6' => 0,
        'total_7' => 0,
        'total_8' => 0
      ];

      $channels = [
        'offline' => 'offline',
        'online' => 'online',
        'tiktok' => '0009',
        'shopee' => 'SHOPEE',
        'lazada' => 'LAZADA'
      ];

      $state = ['0', '3', '4', '5', '6', '7', '8'];
      

      foreach($channels as $ch => $code)
      {
        foreach($state as $st)
        {
          $count = $this->dashboard_model->count_orders_state($code, $st);
          $data["{$ch}_{$st}"] = $count;
          if($ch == 'offline' OR $ch == 'online')
          {
            $data["total_{$st}"] += $count;
          }
        }
      }

      $ds = array(
        'd1' => $this->dashboard_model->getShippedLastDays(1),
        'd2' => $this->dashboard_model->getShippedLastDays(2),
        'd3' => $this->dashboard_model->getShippedLastDays(3),
        'd4' => $this->dashboard_model->getShippedLastDays(4),
        'd5' => $this->dashboard_model->getShippedLastDays(5),
        'd6' => $this->dashboard_model->getShippedLastDays(6),
        'd7' => $this->dashboard_model->getShippedLastDays(7),
        'data' => (object) $data
      );

      $this->load->view('inventory/dashboard/dashboard', $ds);
    }
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


  public function get_order_data()
  {
    $sc = TRUE;

    $data = [
      'total_0' => 0,
      'total_3' => 0,
      'total_4' => 0,
      'total_5' => 0,
      'total_6' => 0,
      'total_7' => 0,
      'total_8' => 0
    ];

    $channels = [
      'offline' => 'offline',
      'online' => 'online',
      'tiktok' => '0009',
      'shopee' => 'SHOPEE',
      'lazada' => 'LAZADA'
    ];

    $state = ['0', '3', '4', '5', '6', '7', '8'];


    foreach ($channels as $ch => $code)
    {
      foreach ($state as $st)
      {
        $count = $this->dashboard_model->count_orders_state($code, $st);
        $data["{$ch}_{$st}"] = $count;
        if ($ch == 'offline' or $ch == 'online')
        {
          $data["total_{$st}"] += $count;
        }
      }
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $data
    );

    echo json_encode($arr);
  }

} //--- end class
?>

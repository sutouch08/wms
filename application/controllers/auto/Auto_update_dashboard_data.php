<?php
class Auto_update_dashboard_data extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('inventory/dashboard_model');
  }


  public function index()
  {
    $channels = [
      'offline' => 'offline',
      'online' => 'online',
      'tiktok' => '0009',
      'shopee' => 'SHOPEE',
      'lazada' => 'LAZADA'
    ];

    $states = [
      'state-3' => 3,
      'state-4' => 4,
      'state-5' => 5,
      'state-6' => 6,
      'state-7' => 7,
      'state-8' => 8,
      'backorder' => 0
    ];

    foreach ($channels as $channel)
    {
      $arr = [
        'state-3' => 0,
        'state-4' => 0,
        'state-5' => 0,
        'state-6' => 0,
        'state-7' => 0,
        'state-8' => 0,
        'backorder' => 0
      ];

      foreach ($states as $key => $state)
      {
        $arr[$key] = $this->dashboard_model->count_orders_state($channel, $state);
      }

      $this->update_dashboard_data($channel, $arr);
    }
  }

  public function update_dashboard_data($channel, $data)
  {
    return $this->db->where('channels', $channel)->update('dashboard', $data);
  }
}

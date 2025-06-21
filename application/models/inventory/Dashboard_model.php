<?php
class Dashboard_model extends CI_Model
{
  private $tb = "orders";

  public function __construct()
  {
    parent::__construct();
  }

  public function count_orders_state($channels = 'offline', $state = 3)
  {
    $qr  = "SELECT COUNT(*) AS num_rows ";
    $qr .= "FROM orders AS o ";
    $qr .= "LEFT JOIN channels AS ch ON o.channels_code = ch.code ";
    $qr .= "WHERE o.status = 1 ";
    $qr .= "AND o.is_cancled = 0 ";

    if($state == 8)
    {
      $from_date = date('Y-m-d 00:00:00');
      $to_date = date('Y-m-d 23:59:59');

      $qr .= "AND (o.state = 8 OR (o.state = 7 AND o.dispatch_id > 0)) ";
      $qr .= "AND o.real_shipped_date >= '{$from_date}' ";
      $qr .= "AND o.real_shipped_date <= '{$to_date}' ";
    }
    else if($state == 7)
    {
      $qr .= "AND (o.state = 7 AND o.dispatch_id IS NULL) ";
    }
    else
    {
      $qr .= "AND o.state = {$state} ";
    }

    if($channels == 'offline')
    {
      $qr .= "AND (ch.is_online = 0 OR o.channels_code IS NULL) ";
    }
    elseif($channels == 'online')
    {
      $qr .= "AND (o.channels_code IS NOT NULL AND ch.is_online = 1) ";
    }
    else
    {
      $qr .= "AND o.channels_code = '{$channels}' ";
    }

    $rs = $this->db->query($qr);

    return $rs->row()->num_rows;

    // $this->db
    // ->from('orders AS o')
    // ->join('channels AS ch', 'o.channels_code = ch.code', 'left')
    // ->where('o.status', 1)
    // ->where('o.is_cancled', 0);
    //
    // if($state == 8)
    // {
    //   $from_date = date('Y-m-d 00:00:00');
    //   $to_date = date('Y-m-d 23:59:59');
    //
    //   $this->db
    //   ->group_start()
    //   ->where('o.state', 8)
    //   ->or_where('o.state', 7)
    //   ->where('o.dispatch_id >', 0)
    //   ->group_end()
    //   ->where('o.real_shipped_date >=', $from_date)
    //   ->where('o.real_shipped_date <=', $to_date);
    // }
    // else if($state == 7)
    // {
    //   $this->db->where('o.state', 7)->where('o.dispatch_id IS NULL', NULL, FALSE);
    // }
    // else
    // {
    //   $this->db->where('o.state', $state);
    // }
    //
    // if($channels == 'offline')
    // {
    //   $this->db
    //   ->group_start()
    //   ->where('ch.is_online', 0)
    //   ->or_where('o.channels_code IS NULL', NULL, FALSE)
    //   ->group_end();
    // }
    // elseif($channels == 'online')
    // {
    //   $this->db
    //   ->where('o.channels_code IS NOT NULL', NULL, FALSE)
    //   ->where('ch.is_online', 1);
    // }
    // else
    // {
    //   $this->db->where('o.channels_code', $channels);
    // }

    // echo $this->db->get_compiled_select();
    // return $this->db->count_all_results();
  }
} // end class

 ?>

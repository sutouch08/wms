<?php
class Dashboard_model extends CI_Model
{
  private $tb = "orders";

  public function __construct()
  {
    parent::__construct();
  }

  public function getShippedLastDays($days)
  {
    $id = $this->get_max_id();

    $qr  = "SELECT COUNT(o.id) AS num_rows ";
    $qr .= "FROM orders AS o ";
    $qr .= "LEFT JOIN channels AS ch ON o.channels_code = ch.code ";
    $qr .= "WHERE o.id > {$id} AND o.status = 1 ";
    $qr .= "AND o.is_cancled = 0 ";
    $qr .= "AND o.warehouse_code = '".getConfig('DEFAULT_WAREHOUSE')."' ";

    $from_date = date('Y-m-d 00:00:00', strtotime("-{$days}"));
    $to_date = date('Y-m-d 23:59:59', strtotime("-{$days}"));

    $qr .= "AND (o.state = 8 OR (o.state = 7 AND o.dispatch_id > 0)) ";
    $qr .= "AND o.real_shipped_date >= '{$from_date}' ";
    $qr .= "AND o.real_shipped_date <= '{$to_date}' ";
    $rs = $this->db->query($qr);

    return $rs->row()->num_rows;
  }


  public function count_orders_state($channels = 'offline', $state = 3)
  {
    $id = $this->get_max_id();

    $qr  = "SELECT COUNT(o.id) AS num_rows ";
    $qr .= "FROM orders AS o ";
    $qr .= "LEFT JOIN channels AS ch ON o.channels_code = ch.code ";
    $qr .= "WHERE o.id > {$id} AND o.status = 1 ";
    $qr .= "AND o.is_cancled = 0 ";
    $qr .= "AND o.warehouse_code = '".getConfig('DEFAULT_WAREHOUSE')."' ";

    if($state == 0)
    {
      $qr .= "AND o.state > 2 AND o.state < 5 AND o.is_backorder = 1 ";
    }
    else
    {
      $qr .= "AND o.is_backorder = 0 ";
    }


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
      if($state != 0)
      {
        $qr .= "AND o.state = {$state} ";
      }
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
  }



  public function get_max_id()
  {
    $limit = $this->get_limit_rows();
    $rs = $this->db->query("SELECT MAX(id) AS id FROM orders");

    if($rs->num_rows() === 1)
    {
      return $rs->row()->id - $limit;
    }

    return $limit;
  }


  public function get_limit_rows()
  {
    $rs = $this->db->query("SELECT value FROM config WHERE code = 'FILTER_RESULT_LIMIT'");

    if($rs->num_rows() === 1)
    {
      return intval($rs->row()->value);
    }

    return 0;
  }
} // end class

 ?>

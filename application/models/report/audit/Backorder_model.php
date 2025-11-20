<?php
class Backorder_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get_report(array $ds = array())
  {
    $this->db
    ->select('b.*, o.channels_code, o.warehouse_code, o.role')
    ->from('order_backlog_details AS b')
    ->join('orders AS o', 'b.order_code = o.code', 'left');

    if( ! empty($ds['from_date']))
    {
      $this->db->where('b.date_upd >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('b.date_upd <=', to_date($ds['to_date']));
    }

    if(isset($ds['allRole']) && $ds['allRole'] != '1' && ! empty($ds['role']))
    {
      $this->db->where_in('o.role', $ds['role']);
    }

    if(isset($ds['allChannels']) && $ds['allChannels'] != '1' && ! empty($ds['channels']))
    {
      $this->db->where_in('o.channels_code', $ds['channels']);
    }

    if(isset($ds['allWarehouse']) && $ds['allWarehouse'] != '1' && ! empty($ds['warehouse']))
    {
      $this->db->where_in('o.warehouse_code', $ds['warehouse']);
    }

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

} //-- end class

 ?>

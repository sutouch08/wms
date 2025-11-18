<?php
class Backorder_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get_report(array $ds = array())
  {
    if( ! empty($ds['from_date']))
    {
      $this->db->where('date_upd >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('date_upd <=', to_date($ds['to_date']));
    }

    $rs = $this->db->get('order_backlog_details');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

} //-- end class

 ?>

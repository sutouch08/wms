<?php
class Consign_acception_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get_list(array $ds = array())
  {
    $this->db
    ->select('o.date_add, o.shipped_date, o.customer_code, o.customer_name, o.zone_code, o.inv_code, o.is_valid')
    ->select('s.reference, s.product_code, s.product_name')
    ->select_sum('s.qty')
    ->from('order_sold AS s')
    ->join('orders AS o', 's.reference = o.code', 'left')
    ->where('s.role', 'N');

    if($ds['date_type'] == 'S')
    {
      $this->db->where('o.shipped_date >=', from_date($ds['from_date']));
      $this->db->where('o.shipped_date <=', to_date($ds['to_date']));
    }

    if($ds['date_type'] == 'D')
    {
      $this->db->where('o.date_add >=', from_date($ds['from_date']));
      $this->db->where('o.date_add <=', to_date($ds['to_date']));
    }

    if( isset($ds['is_accept']) && $ds['is_accept'] != 'all')
    {
      $this->db->where('o.is_valid', $ds['is_accept']);
    }

    if( isset($ds['is_complete']) && $ds['is_complete'] != 'all')
    {
      if( ! empty($ds['is_complete']))
      {
        $this->db->where('o.inv_code IS NOT NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('o.inv_code IS NULL', NULL, FALSE);
      }
    }

    if( ! empty($ds['customer_code']))
    {
      $this->db->where('o.customer_code', $ds['customer_code']);
    }

    if( ! empty($ds['zone_code']))
    {
      $this->db->where('o.zone_code', $ds['zone_code']);
    }

    $rs = $this->db
    ->group_by(array('s.reference', 's.product_code'))
    ->order_by('s.reference', 'ASC')
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

} //-- end class

 ?>

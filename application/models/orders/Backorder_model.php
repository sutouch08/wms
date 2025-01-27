<?php
class Backorder_model extends CI_Model
{
  private $tb = "orders";
  private $td = "order_details";

  public function __construct()
  {
    parent::__construct();
  }

  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $this->db->where('is_backorder', 1);
    $this->db->where_in('state', [1, 2, 3, 4]);

    if( ! empty($ds['role']) && $ds['role'] != 'all')
    {
      $this->where('role', $ds['role']);
    }

    if( ! empty($ds['warehouse']) && $ds['warehouse'] != 'all')
    {
      $this->db->where('warehouse_code', $ds['warehouse']);
    }

    if( ! empty($ds['channels']) && $ds['channels'] != 'all')
    {
      $this->db->where('channels_code', $ds['channels']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    if( ! empty($ds['code']))
    {
      $this->db->like('code', $code);
    }

    if( ! empty($ds['customer']))
    {
      $this->db
      ->group_start()
      ->like('customer_code', $ds['customer'])
      ->or_like('customer_name', $ds['customer'])
      ->or_like('customer_ref', $ds['customer'])
      ->group_end();
    }

    $this->db->order_by('date_add', 'DESC')->limit($perpage, $offset);

    $rs = $this->db->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function count_rows(array $ds = array())
  {
    $this->db->where('is_backorder', 1);
    $this->db->where_in('state', [1, 2, 3, 4]);

    if( ! empty($ds['role']) && $ds['role'] != 'all')
    {
      $this->where('role', $ds['role']);
    }

    if( ! empty($ds['warehouse']) && $ds['warehouse'] != 'all')
    {
      $this->db->where('warehouse_code', $ds['warehouse']);
    }

    if( ! empty($ds['channels']) && $ds['channels'] != 'all')
    {
      $this->db->where('channels_code', $ds['channels']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    if( ! empty($ds['code']))
    {
      $this->db->like('code', $code);
    }

    if( ! empty($ds['customer']))
    {
      $this->db
      ->group_start()
      ->like('customer_code', $ds['customer'])
      ->or_like('customer_name', $ds['customer'])
      ->or_like('customer_ref', $ds['customer'])
      ->group_end();
    }

    return $this->db->count_all_results($this->tb);
  }


} //--- end class

 ?>

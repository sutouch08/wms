<?php
class Packing_video_model extends CI_Model
{
  private $tb = "order_pack_video";

  public function __construct()
  {
    parent::__construct();
  }

  public function count_rows(array $ds = array())
  {
    if( !empty($ds['order_code']))
    {
      $this->db->like('order_code', $ds['order_code']);
    }

    if(isset($ds['role']) && $ds['role'] != 'all')
    {
      $this->db->where('role', $ds['role']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('create_date >=', from_date($ds['from_date']));
    }
    
    if( ! empty($ds['to_date']))
    {
      $this->db->where('create_date <=', to_date($ds['to_date']));
    }

    return $this->db->count_all_results($this->tb);
  }

  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    if( !empty($ds['order_code']))
    {
      $this->db->like('order_code', $ds['order_code']);
    }

    if(isset($ds['role']) && $ds['role'] != 'all')
    {
      $this->db->where('role', $ds['role']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('create_date >=', from_date($ds['from_date']));
    }
    
    if( ! empty($ds['to_date']))
    {
      $this->db->where('create_date <=', to_date($ds['to_date']));
    }

    $rs = $this->db->order_by('id', 'DESC')->limit($perpage, $offset)->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }
} //-- end class

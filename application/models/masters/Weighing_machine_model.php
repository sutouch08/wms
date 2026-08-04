<?php
class Weighing_machine_model extends CI_Model
{
  private $tb = "weighing_machine";

  public function __construct()
  {
    parent::__construct();
  }


  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get($this->tb);

    if ($rs->num_rows() === 1) 
    {
      return $rs->row();
    }

    return NULL;
  }

  public function get_by_device_id($device_id)
  {
    $rs = $this->db->where('device_id', $device_id)->get($this->tb);

    if ($rs->num_rows() === 1) 
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_by_id($id)
  {
    $rs = $this->db->where('id', $id)->get($this->tb);

    if ($rs->num_rows() === 1) 
    {
      return $rs->row();
    }

    return NULL;
  }

  public function get_all_active()
  {
    $rs = $this->db->where('active', 1)->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

  public function get_all_exclude($excludes = array())
  {
    if(!empty($excludes))
    {
      $this->db->where_not_in('device_id', $excludes);
    }

    $rs = $this->db->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function is_exists($code, $id = NULL)
  {
    if(!empty($id))
    {
      $this->db->where('id !=', $id);
    }

    return $this->db->where('code', $code)->count_all_results($this->tb) > 0;
  }


  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      if($this->db->insert($this->tb, $ds))
      {
        return $this->db->insert_id();
      }
    }

    return FALSE;
  }


  public function update($id, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('id', $id)->update($this->tb, $ds);
    }

    return FALSE;
  }

  public function delete($id)
  {
    return $this->db->where('id', $id)->delete($this->tb);
  }


  public function count_rows(array $ds = array())
  {
    if(!empty($ds['device_id']))
    {
      $this->db->like('device_id', $ds['device_id']);
    }

    if (!empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if (!empty($ds['name']))
    {
      $this->db->like('name', $ds['name']);
    }

    if(isset($ds['active']) && $ds['active'] !== 'all')
    {
      $this->db->where('active', $ds['active']);
    }

    return $this->db->count_all_results($this->tb);
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    if(!empty($ds['device_id']))
    {
      $this->db->like('device_id', $ds['device_id']);
    }
    
    if (!empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if (!empty($ds['name']))
    {
      $this->db->like('name', $ds['name']);
    }

    if(isset($ds['active']) && $ds['active'] !== 'all')
    {
      $this->db->where('active', $ds['active']);
    }

    $rs = $this->db
    ->order_by('code', 'ASC')
    ->limit($perpage, $offset)
    ->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }
}
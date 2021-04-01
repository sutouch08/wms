<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Warehouse_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get($code)
  {

    $rs = $this->db
    ->select('warehouse.*, warehouse_role.name AS role_name')
    ->from('warehouse')
    ->join('warehouse_role', 'warehouse.role = warehouse_role.id', 'left')
    ->where('warehouse.code', $code)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function get_name($code)
  {
    $rs = $this->db->where('code', $code)->get('warehouse');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }



  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('warehouse', $ds);
    }

    return FALSE;
  }


  public function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      $this->db->where('code', $code);
      return $this->db->update('warehouse', $ds);
    }

    return FALSE;
  }


  public function delete($code)
  {
    return $this->db->where('code', $code)->delete('warehouse');
  }



  public function get_all_role()
  {
    $rs = $this->db->get('warehouse_role');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function count_rows(array $ds = array())
  {
    if(!empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if(!empty($ds['name']))
    {
      $this->db->like('name', $ds['name']);
    }

    if(!empty($ds['role']))
    {
      $this->db->where('role', $ds['role']);
    }

    return $this->db->count_all_results('warehouse');
  }


  public function get_list(array $ds = array(), $perpage = '', $offset = '')
  {
    $this->db->select('warehouse.*, warehouse_role.name AS role_name');
    $this->db->from('warehouse')->join('warehouse_role', 'warehouse.role = warehouse_role.id');

    if(!empty($ds['code']))
    {
      $this->db->like('warehouse.code', $ds['code']);
    }

    if(!empty($ds['name']))
    {
      $this->db->like('warehouse.name', $ds['name']);
    }

    if(!empty($ds['role']))
    {
      $this->db->where('warehouse.role', $ds['role']);
    }

    if(!empty($perpage))
    {
      $offset = $offset === NULL ? 0 : $offset;
      $this->db->limit($perpage, $offset);
    }

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



  //--- เอาเฉพาะคลังซื้อขาย
  public function get_sell_warehouse_list()
  {
    $rs = $this->db->where('role', 1)->where('active', 1)->where('sell', 1)->get('warehouse');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  ///---- คลังฝากขายทั้งหมด
  public function get_consign_warehouse_list()
  {
    $rs = $this->db->where('role', 2)->where('active', 1)->get('warehouse');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

  //---- เอาเฉพาะคลังฝากขายแท้
  public function get_consign_list()
  {
    $rs = $this->db
    ->where('role', 2)
    ->group_start()
    ->where('is_consignment IS NULL', NULL, FALSE)
    ->or_where('is_consignment', 0)
    ->group_end()
    ->where('active', 1)
    ->get('warehouse');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  //---- เอาเฉพาะคลังฝากขายเทียม
  public function get_consignment_list()
  {
    $rs = $this->db
    ->where('role', 2)
    ->where('is_consignment', 1)
    ->where('active', 1)
    ->get('warehouse');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

  public function count_zone($code)
  {
    return $this->db->where('warehouse_code', $code)->count_all_results('zone');
  }


  public function get_role_name($id)
  {
    $rs = $this->db->select('name')->where('id', $id)->get('warehouse_role');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }



  public function get_last_sync_date()
  {
    $rs = $this->db->select_max('last_sync')->get('warehouse');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->last_sync === NULL ? date('2019-01-01 00:00:00') : $rs->row()->last_sync;
    }

    return date('2019-01-01 00:00:00');
  }


  public function get_new_data($last_sync)
  {
    $this->ms->select('WhsCode AS code, WhsName AS name, U_OLDWHSCODE AS old_code');
    $this->ms->where('createDate >=', sap_date($last_sync));
    $this->ms->or_where('updateDate >=', sap_date($last_sync));
    $rs = $this->ms->get('OWHS');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function get_all_warehouse()
  {
    $this->ms->select('WhsCode AS code, WhsName AS name');
    $this->ms->select('createDate, updateDate');
    $rs = $this->ms->get('OWHS');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function has_zone($code)
  {
    //--- return number of result rows like 25
    $rs = $this->db->where('warehouse_code', $code)->count_all_results('zone');
    if($rs > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function is_exists($code)
  {
    $rs = $this->db->where('code', $code)->get('warehouse');
    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function is_sap_exists($code)
  {
    $rs = $this->ms->select('WhsCode')->where('WhsCode', $code)->get('OWHS');
    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function is_auz($code)
  {
    $rs = $this->db->select('auz')->where('code', $code)->where('auz', 1)->get('warehouse');
    if($rs->num_rows() === 1)
    {
      return TRUE;
    }

    return FALSE;
  }
}
 ?>

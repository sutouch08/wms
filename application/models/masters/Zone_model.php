<?php
class Zone_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  //--- add new zone (use with sync only)
  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('zone', $ds);
    }

    return FALSE;
  }


  //--- update zone with sync only
  public function update($id, $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('id', $id)->update('zone', $ds);
    }

    return FALSE;
  }


  //--- add new customer to zone
  public function add_customer(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('zone_customer', $ds);
    }

    return FALSE;
  }



  //--- add new customer to zone
  public function add_employee(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('zone_employee', $ds);
    }

    return FALSE;
  }



  //--- remove customer from connected zone
  public function delete_customer($id)
  {
    return $this->db->where('id', $id)->delete('zone_customer');
  }


  //--- remove customer from connected zone
  public function delete_employee($id)
  {
    return $this->db->where('id', $id)->delete('zone_employee');
  }


  //---- delete zone  must use only mistake on sap and delete zone in SAP already
  public function delete($code)
  {
    return $this->db->where('code', $code)->delete('zone');
  }

  //--- check zone exists or not
  public function is_exists($code)
  {
    if($this->db->where('code', $code)->count_all_results('zone') > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  //--- check zone exists by id
  public function is_exists_id($id)
  {
    if($this->db->where('id', $id)->count_all_results('zone') > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  //--- check customer exists in zone or not
  public function is_exists_customer($zone_code, $customer_code)
  {
    $rs = $this->db
    ->where('zone_code', $zone_code)
    ->where('customer_code', $customer_code)
    ->count_all_results('zone_customer');

    if($rs > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  //--- check customer exists in zone or not
  public function is_exists_employee($zone_code, $empID)
  {
    $rs = $this->db
    ->where('zone_code', $zone_code)
    ->where('empID', $empID)
    ->count_all_results('zone_employee');

    if($rs > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function is_sap_exists($code)
  {
    if($this->ms->where('BinCode', $code)->count_all_results('OBIN') > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function count_rows(array $ds = array())
  {
    if(!empty($ds['customer']))
    {
      return $this->count_rows_customer($ds);
    }

    if(!empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if(!empty($ds['name']))
    {
      $this->db->like('name', $ds['name']);
    }

    if(!empty($ds['warehouse']))
    {
      $this->db->where('warehouse_code', $ds['warehouse']);
    }

    return $this->db->count_all_results('zone');
  }




  private function count_rows_customer(array $ds = array())
  {
    $this->db
    ->from('zone_customer')
    ->join('zone', 'zone.code = zone_customer.zone_code')
    ->join('customers', 'zone_customer.customer_code = customers.code')
    ->like('customers.code', $ds['customer'])
    ->or_like('customers.name', $ds['customer']);

    if(!empty($ds['code']))
    {
      $this->db->like('zone.code', $ds['code']);
    }

    if(!empty($ds['name']))
    {
      $this->db->like('zone.name', $ds['name']);
    }

    if(!empty($ds['warehouse']))
    {
      $this->db->where('zone.warehouse_code', $ds['warehouse']);
    }

    return $this->db->count_all_results();
  }





  public function get_list(array $ds = array(), $perpage = NULL, $offset = NULL)
  {
    //--- if search for customer
    if(!empty($ds['customer']))
    {
      return $this->get_list_customer($ds);
    }

    $this->db
    ->select('zone.code AS code, zone.name AS name, zone.warehouse_code, warehouse.name AS warehouse_name, zone.old_code')
    ->from('zone')
    ->join('warehouse', 'warehouse.code = zone.warehouse_code', 'left');

    if(!empty($ds['code']))
    {
      $this->db->like('zone.code', $ds['code']);
    }

    if(!empty($ds['name']))
    {
      $this->db->like('zone.name', $ds['name']);
    }

    if(!empty($ds['warehouse']))
    {
      $this->db->where('zone.warehouse_code', $ds['warehouse']);
    }

    $this->db->order_by('zone.date_upd', 'DESC');
    $this->db->order_by('zone.code', 'ASC');

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






  private function get_list_customer(array $ds = array(), $perpage = NULL, $offset = NULL)
  {
    $this->db
    ->select('zone.code AS code, zone.name AS name, warehouse.name AS warehouse_name, zone.old_code')
    ->select('customers.code AS customer_code, customers.name AS customer_name')
    ->from('zone_customer')
    ->join('zone', 'zone.code = zone_customer.zone_code')
    ->join('customers', 'zone_customer.customer_code = customers.code')
    ->join('warehouse', 'zone.warehouse_code = warehouse.code', 'left')
    ->like('customers.code', $ds['customer'])
    ->or_like('customers.name', $ds['customer']);

    if(!empty($ds['code']))
    {
      $this->db->like('zone.code', $ds['code']);
    }

    if(!empty($ds['name']))
    {
      $this->db->like('zone.name', $ds['name']);
    }

    if(!empty($ds['warehouse']))
    {
      $this->db->where('zone.warehouse_code', $ds['warehouse']);
    }

    $this->db->group_by('zone.code');

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






  public function count_customer($code)
  {
    return $this->db->where('zone_code', $code)->count_all_results('zone_customer');
  }


  public function get_customers($zone_code)
  {

    $rs = $this->db->where('zone_code', $zone_code)->get('zone_customer');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function get_employee($zone_code)
  {

    $rs = $this->db->where('zone_code', $zone_code)->get('zone_employee');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }




  public function get($code)
  {
    $rs = $this->db
    ->select('zone.code, zone.name, zone.warehouse_code')
    ->select('warehouse.name AS warehouse_name, warehouse.role, warehouse_role.name AS role_name')
    ->from('zone')
    ->join('warehouse', 'zone.warehouse_code = warehouse.code', 'left')
    ->join('warehouse_role', 'warehouse.role = warehouse_role.id', 'left')
    ->where('zone.code', $code)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }






  public function get_warehouse_code($zone_code)
  {
    $rs = $this->db->select('warehouse_code')->where('code', $zone_code)->get('zone');
    //$rs = $this->ms->select('WhsCode AS warehouse_code')->where('BinCode', $zone_code)->get('OBIN');
    if($rs->num_rows() == 1)
    {
      return $rs->row()->warehouse_code;
    }

    return FALSE;
  }






  public function get_name($code)
  {
    $rs = $this->db->select('name')->where('code', $code)->get('zone');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }



  public function get_zone_detail_in_warehouse($code, $warehouse)
  {
    $rs = $this->db
    ->where('warehouse_code', $warehouse)
    ->group_start()
    ->where('code', $code)
    ->or_where('old_code', $code)
    ->group_end()
    ->get('zone');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function search($txt, $warehouse_code = NULL)
  {
    $limit = 50;
    if(!empty($warehouse_code))
    {
      $this->db->where('warehouse_code', $warehouse_code);
    }

    if($txt != '*')
    {
      $this->db->group_start();
      $this->db->like('code', $txt)->or_like('name', $txt);
      $this->db->group_end();
    }

    $this->db->order_by('code', 'ASC');
    $rs = $this->db->limit($limit)->get('zone');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function get_last_sync_date()
  {
    $rs = $this->db->select_max('last_sync')->get('zone');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->last_sync === NULL ? date('2019-01-01 00:00:00') : db_date($rs->row()->last_sync);
    }

    return date('2019-01-01 00:00:00');
  }


  public function get_new_data($last_sync)
  {
    $this->ms->select('AbsEntry AS id, BinCode AS code, Descr AS name, WhsCode AS warehouse_code, SL1Code AS old_code');
    //$this->ms->where('SysBin', 'N');
    //$this->ms->group_start();
    $this->ms->where('createDate >=', sap_date($last_sync));
    $this->ms->or_where('updateDate >=', sap_date($last_sync));
    //$this->ms->group_end();
    $rs = $this->ms->get('OBIN');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function get_all_zone()
  {
    $this->ms->select('AbsEntry AS id, BinCode AS code, Descr AS name, SL1Code AS old_code, WhsCode AS warehouse_code');
    $this->ms->select('createDate, updateDate');
    //$this->ms->where('SysBin', 'N');
    $rs = $this->ms->get('OBIN');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  //--- ใช้จัดสินค้า
  public function get_zone_code($barcode)
  {
    $rs = $this->db->select('code')->where('old_code', $barcode)->or_where('code', $barcode)->get('zone');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->code;
    }

    return FALSE;
  }
} //--- end class

 ?>

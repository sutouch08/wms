<?php
class Production_transfer_model extends CI_Model
{
  private $tb = "production_transfer";
  private $td = "production_transfer_details";
  private $tm = "production_transfer_batch";

  public function __construct()
  {
    parent::__construct();
  }


  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_details($code)
  {
    $rs = $this->db->where('transfer_code', $code)->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_batch_rows($detail_id)
  {
    $rs = $this->db->where('transfer_detail_id', $detail_id)->get($this->tm);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function add(array $ds = array())
  {
    if( ! empty($ds))
    {
      if($this->db->insert($this->tb, $ds))
      {
        return $this->db->insert_id();
      }
    }

    return FALSE;
  }


  public function add_detail(array $ds = array())
  {
    if( ! empty($ds))
    {
      if($this->db->insert($this->td, $ds))
      {
        return $this->db->insert_id();
      }
    }

    return FALSE;
  }


  public function add_batch_rows(array $ds = array())
  {
    if( ! empty($ds))
    {
      if($this->db->insert($this->tm, $ds))
      {
        return $this->db->insert_id();
      }
    }

    return FALSE;
  }


  public function update($code, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('code', $code)->update($this->tb, $ds);
    }

    return FALSE;
  }


  public function update_detail($id, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('id', $id)->update($this->td, $ds);
    }

    return FALSE;
  }


  public function update_details($code, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('transfer_code', $code)->update($this->td, $ds);
    }

    return FALSE;
  }


  public function update_batch($id, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('id', $id)->update($this->tm, $ds);
    }

    return FALSE;
  }


  public function update_batch_rows($detail_id , array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('transfer_detail_id', $detail_id)->update($this->tm, $ds);
    }

    return FALSE;
  }


  //--- update all batch rows in document
  public function update_batches($code, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('transfer_code', $code)->update($this->tm, $ds);
    }

    return FALSE;
  }


  public function delete_details($code)
  {
    return $this->db->where('transfer_code', $code)->delete($this->td);
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['reference']))
    {
      $this->db->like('reference', $ds['reference']);
    }

    if( isset($ds['fromWhsCode']) && $ds['fromWhsCode'] != 'all')
    {
      $this->db->where('fromWhsCode', $ds['fromWhsCode']);
    }

    if( isset($ds['toWhsCode']) && $ds['toWhsCode'] != 'all')
    {
      $this->db->where('toWhsCode', $ds['toWhsCode']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    if( isset($ds['status']) && $ds['status'] != 'all')
    {
      $this->db->where('Status', $ds['status']);
    }

    if(isset($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('user', $ds['user']);
    }

    if(isset($ds['is_exported']) && $ds['is_exported'] != 'all')
    {
      $this->db->where('is_exported', $ds['is_exported']);
    }

    $rs = $this->db->order_by('code', 'DESC')->limit($perpage, $offset)->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function count_rows(array $ds = array())
  {
    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['reference']))
    {
      $this->db->like('reference', $ds['reference']);
    }

    if( isset($ds['fromWhsCode']) && $ds['fromWhsCode'] != 'all')
    {
      $this->db->where('fromWhsCode', $ds['fromWhsCode']);
    }

    if( isset($ds['toWhsCode']) && $ds['toWhsCode'] != 'all')
    {
      $this->db->where('toWhsCode', $ds['toWhsCode']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    if( isset($ds['status']) && $ds['status'] != 'all')
    {
      $this->db->where('Status', $ds['status']);
    }

    if(isset($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('user', $ds['user']);
    }

    if(isset($ds['is_exported']) && $ds['is_exported'] != 'all')
    {
      $this->db->where('is_exported', $ds['is_exported']);
    }

    return $this->db->count_all_results($this->tb);
  }


  public function get_production_order($code)
  {
    $rs = $this->ms
    ->select('DocEntry, DocNum, ItemCode, Status')
    ->where('DocNum', $code)
    ->where_in('Status', ['R', 'L'])
    ->get('OWOR');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_production_order_details($DocEntry)
  {
    $rs = $this->ms
    ->select('o.DocEntry, p.DocNum, o.LineNum, o.ItemCode, o.BaseQty, o.PlannedQty, o.IssuedQty, o.IssueType, o.wareHouse')
    ->select('o.UomEntry, o.UomCode, u.UomName, o.ItemType')
    ->select('i.ItemName, i.ManBtchNum')
    ->from('WOR1 AS o')
    ->join('OWOR AS p', 'o.DocEntry = p.DocEntry', 'left')
    ->join('OITM AS i', 'o.ItemCode = i.ItemCode', 'left')
    ->join('OUOM AS u', 'o.UomEntry = u.UomEntry', 'left')
    ->where('o.DocEntry', $DocEntry)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

  
  public function get_item_batch_rows($ItemCode, array $filter = array())
  {
    $this->ms
    ->select('S.ItemCode, S.DistNumber AS BatchNum, S.MnfSerial AS BatchAttr1, S.LotNumber AS BatchAttr2')
    ->select('B.BinCode, Q.OnHandQty AS Qty, Q.WhsCode')
    ->from('OBTN AS S')
    ->join('OBBQ AS Q', 'S.ItemCode = Q.ItemCode AND S.AbsEntry = Q.SnBMDAbs', 'left')
    ->join('OBIN AS B', 'Q.BinAbs = B.AbsEntry', 'left')
    ->where('S.ItemCode', $ItemCode)
    ->where('Q.OnHandQty >', 0, FALSE);

    if( ! empty($filter['WhsCode']) && $filter['WhsCode'] != 'all')
    {
      $this->ms->where('Q.WhsCode', $filter['WhsCode']);
    }

    if( ! empty($filter['BatchNum']))
    {
      $this->ms->like('S.DistNumber', $filter['BatchNum']);
    }

    if( ! empty($filter['BatchAttr1']))
    {
      $this->ms->like('S.MnfSerial', $filter['BatchAttr1']);
    }

    if( ! empty($filter['BatchAttr2']))
    {
      $this->ms->like('S.LotNumber', $filter['BatchAttr2']);
    }

    $rs = $this->ms->order_by('Q.WhsCode', 'ASC')->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_production_order_doc_num($code)
  {
    $rs = $this->db->select('inv_code')->where('code', $code)->where('inv_code IS NOT NULL', NULL, FALSE)->get('production_orders');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->inv_code;
    }

    return NULL;
  }


  public function is_exists_in_sap($code)
  {
    $count = $this->ms->where('U_ECOMNO', $code)->where('CANCELED', 'N')->count_all_results('OWTR');

    return $count > 0 ? TRUE : FALSE;
  }


  public function get_max_code($pre)
  {
    $rs = $this->db
    ->select_max('code')
    ->like('code', $pre, 'after')
    ->order_by('code', 'DESC')
    ->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->code;
    }

    return NULL;
  }


} //-- end class

 ?>

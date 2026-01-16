<?php
class Production_receipt_model extends CI_Model
{
  private $tb = "production_receipt";
  private $td = "production_receipt_details";
  private $tm = "production_receipt_batch";

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
    $rs = $this->db->where('receipt_code', $code)->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_batch_rows($detail_id)
  {
    $rs = $this->db->where('receipt_detail_id', $detail_id)->get($this->tm);

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
      return $this->db->where('receipt_code', $code)->update($this->td, $ds);
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
      return $this->db->where('receipt_detail_id', $detail_id)->update($this->tm, $ds);
    }

    return FALSE;
  }


  //--- update all batch rows in document
  public function update_batches($code, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('receipt_code', $code)->update($this->tm, $ds);
    }

    return FALSE;
  }


  public function delete_details($code)
  {
    return $this->db->where('receipt_code', $code)->delete($this->td);
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

    if( ! empty($ds['order_ref']))
    {
      $this->db->like('orderRef', $ds['order_ref']);
    }

    if( ! empty($ds['item_code']))
    {
      $this->db->like('ItemCode', $ds['item_code']);
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

    if( ! empty($ds['order_ref']))
    {
      $this->db->like('orderRef', $ds['order_ref']);
    }

    if( ! empty($ds['item_code']))
    {
      $this->db->like('ItemCode', $ds['item_code']);
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
    ->select('DocEntry, DocNum, ItemCode')
    ->where('DocNum', $code)
    ->where('Status', 'R')
    ->get('OWOR');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_production_order_data($code)
  {
    $rs = $this->ms
    ->select('o.DocEntry, o.DocNum, o.Type, o.ItemCode, o.ProdName AS ItemName, o.Warehouse AS WhsCode, o.Status')
    ->select('o.PostDate, o.DueDate, o.RlsDate AS ReleaseDate, o.OriginNum, o.OriginType, o.CardCode')
    ->select('o.PlannedQty, o.CmpltQty AS CompleteQty, o.RjctQty AS RejectQty, o.Uom, o.UomEntry, u.UomCode, i.ManBtchNum')
    ->from('OWOR AS o')
    ->join('OITM AS i', 'o.ItemCode = i.ItemCode', 'left')
    ->join('OUOM AS u', 'o.UomEntry = u.UomEntry', 'left')
    ->where('o.DocNum', $code)
    ->where('o.Status', 'R')
    ->get();

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
    $count = $this->ms->where('U_ECOMNO', $code)->where('CANCELED', 'N')->count_all_results('OIGN');

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

<?php
class Production_order_model extends CI_Model
{
  private $tb = "production_orders";
  private $td = "production_order_details";

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
    $rs = $this->db->where('order_code', $code)->get($this->td);

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
      return $this->db->insert($this->td, $ds);
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


  public function delete_details($code)
  {
    return $this->db->where('order_code', $code)->delete($this->td);
  }


  public function get_bom($code)
  {
    $rs = $this->ms
    ->select('b.*, i.InvntryUom AS Uom')
    ->from('OITT AS b')
    ->join('OITM AS i', 'b.Code = i.ItemCode', 'left')
    ->where('b.Code', $code)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_bom_details($code)
  {
    $rs = $this->ms
    ->select('b.*, i.ItemName AS Name, i.InvntryUom AS IUom, i.IUoMEntry, u.UomCode')
    ->from('ITT1 AS b')
    ->join('OITM AS i', 'b.Code = i.ItemCode', 'left')
    ->join('OUOM AS u', 'i.IUoMEntry = u.UomEntry', 'left')
    ->where('b.Father', $code)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_item_data($code)
  {
    $rs = $this->ms
    ->select('i.ItemCode, i.ItemName, i.InvntryUom AS Uom, i.IUoMEntry AS UomEntry, u.UomCode')
    ->from('OITM AS i')
    ->join('OUOM AS u', 'i.IUoMEntry = u.UomEntry', 'left')
    ->where('i.ItemCode', $code)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_issue_qty_by_item($ItemCode, $DocEntry, $LineNum)
  {
    $rs = $this->ms
    ->select_sum('Quantity')
    ->where('ItemCode', $ItemCode)
    ->where('BaseType', 202)
    ->where('BaseEntry', $DocEntry)
    ->where('BaseLine', $LineNum)
    ->where('LineStatus', 'O')
    ->get('IGE1');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->Quantity;
    }

    return 0;
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['inv_code']))
    {
      $this->db->like('inv_code', $ds['inv_code']);
    }

    if( ! empty($ds['product_code']))
    {
      $this->db->like('ItemCode', $ds['product_code']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('PostDate >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('PostDate <=', to_date($ds['to_date']));
    }

    if( isset($ds['status']) && $ds['status'] != 'all')
    {
      $this->db->where('Status', $ds['status']);
    }

    if( isset($ds['is_exported']) && $ds['is_exported'] != 'all')
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

    if( ! empty($ds['inv_code']))
    {
      $this->db->like('inv_code', $ds['inv_code']);
    }

    if( ! empty($ds['product_code']))
    {
      $this->db->like('ItemCode', $ds['product_code']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('PostDate >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('PostDate <=', to_date($ds['to_date']));
    }

    if( isset($ds['status']) && $ds['status'] != 'all')
    {
      $this->db->where('Status', $ds['status']);
    }

    if( isset($ds['is_exported']) && $ds['is_exported'] != 'all')
    {
      $this->db->where('is_exported', $ds['is_exported']);
    }

    return $this->db->count_all_results($this->tb);
  }


  public function get_transfer_ref($code)
  {
    $rs = $this->db
    ->select('code')
    ->where('orderRef', $code)
    ->where('Status', 'C')
    ->get('production_transfer');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_bom_list()
  {
    $rs = $this->ms->select('Code, Name')->get('OITT');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function is_exists_in_sap($code)
  {
    $count = $this->ms->where('U_ECOMNO', $code)->where('Status !=', 'C')->count_all_results('OWOR');

    return $count > 0 ? TRUE : FALSE;
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

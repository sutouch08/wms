<?php
class Receive_material_model extends CI_Model
{
  private $tb = "receive_material";
  private $td = "receive_material_detail";
  private $tm = "receive_material_batch";

  public function __construct()
  {
    parent::__construct();
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


  public function update($code, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('code', $code)->update($this->tb, $ds);
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


  public function add_batch_row(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert($this->tm, $ds);
    }

    return FALSE;
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


  public function get_detail($id)
  {
    $rs = $this->db->where('id', $id)->get($this->td);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_details($code)
  {
    $rs = $this->db->where('receive_code', $code)->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_batch_details($code)
  {
    $rs = $this->db->where('receive_code', $code)->get($this->tm);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_batch_item_by_id($receive_detail_id)
  {
    $rs = $this->db->where('receive_detail_id', $receive_detail_id)->get($this->tm);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_batch_row($id)
  {
    $rs = $this->db->where('id', $id)->get($this->tm);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


	public function update_detail($id, $ds = array())
	{
		if( ! empty($ds))
		{
			return $this->db->where('id', $id)->update($this->td, $ds);
		}

		return FALSE;
	}


  public function update_receive_qty($id, $qty)
  {
    return $this->db->set("ReceiveQty", "ReceiveQty + {$qty}", FALSE)->where('id', $id)->update($this->td);
  }


  public function update_details($code, $ds = array())
	{
		if( ! empty($ds))
		{
			return $this->db->where('receive_code', $code)->update($this->td, $ds);
		}

		return FALSE;
	}


  public function delete_details($code)
  {
    return $this->db->where('receive_code', $code)->delete($this->td);
  }


  public function delete_batch_details($code)
  {
    return $this->db->where('receive_code', $code)->delete($this->tm);
  }


  public function get_po($po_code)
  {
    $rs = $this->ms
    ->select('DocEntry, DocNum, DocStatus, CardCode, CardName, DocCur, DocRate, DiscPrcnt')
    ->where('DocNum', $po_code)
    ->where('CANCELED', 'N')
    ->get('OPOR');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_po_details($po_code)
  {
    $rs = $this->ms
    ->select('d.DocEntry, d.LineNum, d.ItemCode, d.Dscription')
    ->select('d.Quantity, d.LineStatus, d.OpenQty, d.Price')
    ->select('d.PriceBefDi, d.PriceAfVAT, d.DiscPrcnt, d.INMPrice')
		->select('d.Currency, d.Rate, d.VatGroup, d.VatPrcnt, d.unitMsr')
    ->select('d.unitMsr, d.NumPerMsr, d.unitMsr2, d.NumPerMsr2')
    ->select('d.UomEntry, d.UomEntry2, d.UomCode, d.UomCode2')
    ->select('i.ManBtchNum, i.invntItem')
    ->from('POR1 AS d')
    ->join('OPOR AS o', 'd.DocEntry = o.DocEntry', 'left')
    ->join('OITM AS i', 'd.ItemCode = i.ItemCode', 'left')
    ->where('o.DocNum', $po_code)
    ->where('o.DocStatus', 'O')
    ->where('d.LineStatus', 'O')
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_po_details_by_item($po_code, $item_code)
  {
    $rs = $this->ms
    ->select('d.DocEntry, d.LineNum, d.ItemCode, d.Dscription')
    ->select('d.Quantity, d.LineStatus, d.OpenQty, d.Price')
    ->select('d.PriceBefDi, d.PriceAfVAT, d.DiscPrcnt, d.INMPrice')
		->select('d.Currency, d.Rate, d.VatGroup, d.VatPrcnt, d.unitMsr')
    ->select('d.unitMsr, d.NumPerMsr, d.unitMsr2, d.NumPerMsr2')
    ->select('d.UomEntry, d.UomEntry2, d.UomCode, d.UomCode2')
    ->select('i.ManBtchNum, i.invntItem')
    ->from('POR1 AS d')
    ->join('OPOR AS o', 'd.DocEntry = o.DocEntry', 'left')
    ->join('OITM AS i', 'd.ItemCode = i.ItemCode', 'left')
    ->where('o.DocNum', $po_code)
    ->where('o.DocStatus', 'O')
    ->where('d.ItemCode', $item_code)
    ->where('d.LineStatus', 'O')
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


	 public function get_po_row($docEntry, $lineNum)
  {
    $rs = $this->ms
    ->select('d.DocEntry, d.LineNum, d.ItemCode, d.Dscription')
    ->select('d.Quantity, d.LineStatus, d.OpenQty, d.Price')
    ->select('d.PriceBefDi, d.PriceAfVAT, d.DiscPrcnt, d.INMPrice')
		->select('d.Currency, d.Rate, d.VatGroup, d.VatPrcnt, d.unitMsr')
    ->select('d.unitMsr, d.NumPerMsr, d.unitMsr2, d.NumPerMsr2')
    ->select('d.UomEntry, d.UomEntry2, d.UomCode, d.UomCode2')
    ->select('i.ManBtchNum, i.invntItem')
    ->from('POR1 AS d')
    ->join('OITM AS i', 'd.ItemCode = i.ItemCode', 'left')
    ->where('d.DocEntry', $docEntry)
    ->where('d.LineNum', $lineNum)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_po_data($po_code)
  {
    $rs = $this->ms
    ->select('POR1.Currency, POR1.VatGroup, POR1.VatPrcnt')
    ->from('POR1')
    ->join('OPOR', 'POR1.DocEntry = OPOR.DocEntry', 'left')
    ->where('OPOR.DocNum', $po_code)
    ->limit(1)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_on_order_qty($itemCode, $poCode, $baseEntry, $baseLine)
  {
    $rs = $this->db
    ->select_sum('rd.Qty')
    ->from('receive_material_detail AS rd')
    ->join('receive_material AS ro', 'rd.receive_code = ro.code', 'left')
    ->where('ro.po_code', $poCode)
    ->where('ro.status !=', 'D')
    ->where('ro.inv_code IS NULL', NULL, FALSE)
    ->where('rd.baseEntry', $baseEntry)
    ->where('rd.baseLine', $baseLine)
    ->where('rd.ItemCode', $itemCode)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row()->Qty > 0 ? $rs->row()->Qty : 0;
    }

    return 0;
  }


  public function get_sap_receive_doc($code)
  {
    $rs = $this->ms
    ->select('DocEntry, DocStatus')
    ->where('U_ECOMNO', $code)
    ->where('CANCELED', 'N')
    ->order_by('DocEntry', 'DESC')
    ->get('OPDN');

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function is_exists_in_sap($code)
  {
    $count = $this->ms
    ->where('U_ECOMNO', $code)
    ->where('CANCELED', 'N')
    ->count_all_results('OPDN');

    return $count > 0 ? TRUE : FALSE;
  }


  public function count_rows(array $ds = array())
  {
    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['po_code']))
    {
      $this->db->like('po_code', $ds['po']);
    }

    if( ! empty($ds['invoice']))
    {
      $this->db->like('invoice_code', $ds['invoice']);
    }

    if( ! empty($ds['vendor']))
    {
      $this->db
      ->group_start()
      ->like('vendor_code', $ds['vendor'])
      ->or_like('vendor_name', $ds['vendor'])
      ->group_end();
    }

    if( ! ($ds['from_date']) && ! empty($ds['to_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }


    if(isset($ds['status']) && $ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    if(isset($ds['is_export']) && $ds['is_export'] != 'all')
    {
      $this->db->where('is_export', $ds['is_export']);
    }


    if(isset($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('user', $ds['user']);
    }

    if( isset($ds['warehouse']) && $ds['warehouse'] != 'all')
    {
      $this->db->where('warehouse_code', $ds['warehouse']);
    }

    return $this->db->count_all_results($this->tb);
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['po_code']))
    {
      $this->db->like('po_code', $ds['po']);
    }

    if( ! empty($ds['invoice']))
    {
      $this->db->like('invoice_code', $ds['invoice']);
    }

    if( ! empty($ds['vendor']))
    {
      $this->db
      ->group_start()
      ->like('vendor_code', $ds['vendor'])
      ->or_like('vendor_name', $ds['vendor'])
      ->group_end();
    }

    if( ! ($ds['from_date']) && ! empty($ds['to_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }


    if(isset($ds['status']) && $ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    if(isset($ds['is_export']) && $ds['is_export'] != 'all')
    {
      $this->db->where('is_export', $ds['is_export']);
    }


    if(isset($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('user', $ds['user']);
    }

    if( isset($ds['warehouse']) && $ds['warehouse'] != 'all')
    {
      $this->db->where('warehouse_code', $ds['warehouse']);
    }

    $this->db->order_by('date_add', 'DESC');
    $this->db->order_by('code', 'DESC');

    $rs = $this->db->limit($perpage, $offset)->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_max_code($code)
  {
    $rs = $this->db
    ->select_max('code')
    ->like('code', $code)
    ->order_by('code', 'DESC')
    ->get($this->tb);

    if($rs->num_rows() == 1)
    {
      return $rs->row()->code;
    }

    return FALSE;
  }


  public function get_vender_by_po($po_code)
  {
    $rs = $this->ms->select('CardCode, CardName')->where('DocNum', $po_code)->get('OPOR');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }

}

 ?>

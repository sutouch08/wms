<?php
class Receive_po_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }



  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('receive_product', $ds);
    }

    return FALSE;
  }



  public function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('code', $code)->update('receive_product', $ds);
    }

    return FALSE;
  }


  public function add_detail(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('receive_product_detail', $ds);
    }

    return FALSE;
  }



  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get('receive_product');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }



  public function get_details($code)
  {
    $rs = $this->db->where('receive_code', $code)->get('receive_product_detail');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function drop_details($code)
  {
    return $this->db->where('receive_code', $code)->delete('receive_product_detail');
  }



  public function cancle_details($code)
  {
    return $this->db->set('is_cancle', 1)->where('receive_code', $code)->update('receive_product_detail');
  }



  public function get_po_details($po_code)
  {
    $rs = $this->ms
    ->select('POR1.LineNum, POR1.ItemCode, POR1.Dscription, POR1.Quantity, POR1.LineStatus, POR1.OpenQty, POR1.PriceAfVAT AS price')
    ->from('POR1')
    ->join('OPOR', 'POR1.DocEntry = OPOR.DocEntry', 'left')
    ->where('OPOR.DocNum', $po_code)
    ->where('OPOR.DocStatus', 'O')
    //->where('POR1.LineStatus', 'O')
    ->get();

    if(!empty($rs))
    {
      return $rs->result();
    }

    return FALSE;
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


  public function get_sap_receive_doc($code)
  {
    $rs = $this->ms
    ->select('DocEntry, DocStatus')
    ->where('U_ECOMNO', $code)
    ->where('CANCELED', 'N')
    ->get('OPDN');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function is_middle_exists($code)
  {
    $rs = $this->mc->select('U_ECOMNO')->where('U_ECOMNO', $code)->get('OPDN');
    if($rs->num_rows() === 1)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function get_middle_receive_po($code)
  {
    $rs = $this->mc
    ->select('DocEntry')
    ->where('U_ECOMNO', $code)
    ->group_start()
    ->where('F_Sap', 'N')
    ->or_where('F_Sap IS NULL', NULL, FALSE)
    ->group_end()
    ->get('OPDN');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function add_sap_receive_po(array $ds = array())
  {
    $rs = $this->mc->insert('OPDN', $ds);
    if($rs)
    {
      return $this->mc->insert_id();
    }

    return FALSE;
  }


  public function update_sap_receive_po($code, $ds)
  {
    return $this->mc->where('U_ECOMNO', $code)->update('OPDN', $ds);
  }


  public function add_sap_receive_po_detail(array $ds = array())
  {
    return $this->mc->insert('PDN1', $ds);
  }


  public function drop_sap_received($docEntry)
  {
    $this->mc->trans_start();
    $this->mc->where('DocEntry', $docEntry)->delete('PDN1');
    $this->mc->where('DocEntry', $docEntry)->delete('OPDN');
    $this->mc->trans_complete();
    return $this->mc->trans_status();
  }


  public function get_doc_status($code)
  {
    $rs = $this->ms
    ->select('DocStatus')
    ->where('U_ECOMNO', $code)
    ->where('CANCELED', 'N')
    ->get('OPDN');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->DocStatus;
    }

    return 'O';
  }


  public function get_sum_qty($code)
  {
    $rs = $this->db->select_sum('qty', 'qty')
    ->where('receive_code', $code)
    ->get('receive_product_detail');

    return intval($rs->row()->qty);
  }



  public function get_sum_amount($code)
  {
    $rs = $this->db->select_sum('amount')->where('receive_code', $code)->get('receive_product_detail');
    return $rs->row()->amount === NULL ? 0.00 : $rs->row()->amount;
  }




  public function set_status($code, $status)
  {
    return $this->db->set('status', $status)->where('code', $code)->update('receive_product');
  }



  public function count_rows(array $ds = array())
  {

    //---- เลขที่เอกสาร
    if($ds['code'] != '')
    {
      $this->db->group_start();
      $this->db->like('code', $ds['code']);
      $this->db->or_like('inv_code', $ds['code']);
      $this->db->group_end();
    }

    //--- ใบสั่งซื้อ
    if($ds['po'] != '')
    {
      $this->db->like('po_code', $ds['po']);
    }

    //---- invoice
    if($ds['invoice'] != '')
    {
      $this->db->like('invoice_code', $ds['invoice']);
    }

    if($ds['from_date'] != '' && $ds['to_date'] != '')
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    if($ds['status'] !== 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    if($ds['sap'] !== 'all')
    {
      if($ds['sap'] == '0')
      {
        $this->db->where('inv_code IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('inv_code IS NOT NULL', NULL, FALSE);
      }
    }


    return $this->db->count_all_results('receive_product');
  }





  public function get_data(array $ds = array(), $perpage = '', $offset = '', $role = 'S')
  {
    //---- เลขที่เอกสาร
    if($ds['code'] != '')
    {
      $this->db->group_start();
      $this->db->like('code', $ds['code']);
      $this->db->or_like('inv_code', $ds['code']);
      $this->db->group_end();
    }

    //--- ใบสั่งซื้อ
    if($ds['po'] != '')
    {
      $this->db->like('po_code', $ds['po']);
    }

    //---- invoice
    if($ds['invoice'] != '')
    {
      $this->db->like('invoice_code', $ds['invoice']);
    }


    //--- vendor
    if($ds['vendor'] != '')
    {
      $this->db->like('vendor_code', $ds['vendor']);
      $this->db->or_like('vendor_name', $ds['vendor']);
    }


    if($ds['from_date'] != '' && $ds['to_date'] != '')
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    if($ds['status'] !== 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    if($ds['sap'] !== 'all')
    {
      if($ds['sap'] == '0')
      {
        $this->db->where('inv_code IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('inv_code IS NOT NULL', NULL, FALSE);
      }
    }

    $this->db->order_by('date_add', 'DESC');
    $this->db->order_by('code', 'DESC');

    if($perpage != '')
    {
      $offset = $offset === NULL ? 0 : $offset;
      $this->db->limit($perpage, $offset);
    }

    $rs = $this->db->get('receive_product');
    return $rs->result();
  }


  public function get_max_code($code)
  {
    $rs = $this->db
    ->select_max('code')
    ->like('code', $code)
    ->order_by('code', 'DESC')
    ->get('receive_product');

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


  public function is_exists($code)
  {
    $rs = $this->db->select('status')->where('code', $code)->get('receive_product');
    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function get_non_inv_code($limit = 100)
  {
    $rs = $this->db
    ->select('code')
    ->where('inv_code IS NULL', NULL, FALSE)
    ->limit($limit)
    ->get('receive_product');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function get_sap_doc_num($code)
  {
    $rs = $this->ms->select('DocNum')->where('U_ECOMNO', $code)->get('OPDN');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->DocNum;
    }

    return FALSE;
  }


  public function update_inv($code, $doc_num)
  {
    return $this->db->set('inv_code', $doc_num)->where('code', $code)->update('receive_product');
  }


}

 ?>

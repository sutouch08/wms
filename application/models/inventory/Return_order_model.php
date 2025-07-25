<?php
class Return_order_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  //--- เพิ่มเอกสารใหม่เข้าถังกลาง
  public function add_sap_return_order(array $ds = array())
  {
    if(!empty($ds))
    {
      $rs = $this->mc->insert('ORDN', $ds);
      if($rs)
      {
        return $this->mc->insert_id();
      }
    }

    return FALSE;
  }


  public function get_middle_return_doc($code)
  {
    $rs = $this->mc
    ->select('DocEntry')
    ->where('U_ECOMNO', $code)
    ->group_start()
    ->where('F_Sap IS NULL', NULL, FALSE)
    ->or_where('F_Sap', 'N')
    ->group_end()
    ->get('ORDN');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  //--- เพิ่มรายการรับคืน
  public function add_sap_return_detail(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->mc->insert('RDN1', $ds);
    }

    return FALSE;
  }


  //---- อัพเดตเอกสารในถังกลาง
  public function update_sap_return_order($code, $ds = array())
  {
    if(! empty($code) && ! empty($ds))
    {
      return $this->mc->where('U_ECOMNO', $code)->update('ORDN', $ds);
    }

    return FALSE;
  }


  //---- ดึงข้อมูลจากถังกลางมาเช็คสถานะ
  public function get_sap_return_order($code)
  {
    $rs = $this->ms
    ->select('DocEntry')
    ->where('U_ECOMNO', $code)
    ->where('CANCELED', 'N')
    ->get('ORDN');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_sap_doc_num($code)
  {
    $rs = $this->ms
    ->select('DocNum')
    ->where('U_ECOMNO', $code)
    ->where('CANCELED', 'N')
    ->get('ORDN');

    if($rs->num_rows() > 0)
    {
      return $rs->row()->DocNum;
    }

    return NULL;
  }


  public function get_total_return($code)
  {
    $rs = $this->db
    ->select_sum('amount')
    ->where('return_code', $code)
    ->get('return_order_detail');

    return $rs->row()->amount === NULL ? 0 : $rs->row()->amount;
  }


  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('return_order', $ds);
    }

    return FALSE;
  }


  public function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('code', $code)->update('return_order', $ds);
    }

    return FALSE;
  }


	public function update_detail($id, $ds = array())
	{
		if(!empty($ds))
		{
			return $this->db->where('id', $id)->update('return_order_detail', $ds);
		}

		return FALSE;
	}


  public function update_details($code, $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('return_code', $code)->update('return_order_detail', $ds);
    }

    return FALSE;
  }


  public function update_inv($code, $doc_num)
  {
    return $this->db->set('inv_code', $doc_num)->where('code', $code)->update('return_order');
  }


  public function add_detail(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('return_order_detail', $ds);
    }

    return FALSE;
  }


  public function get_non_inv_code($limit = 100)
  {
    $rs = $this->db
    ->select('code')
    ->where('status', 1)
		->where('is_approve', 1)
    ->where('inv_code IS NULL', NULL, FALSE)
    ->get('return_order');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get($code)
  {
    $rs = $this->db
    ->select('r.*')
    ->select('c.name AS customer_name')
    ->select('wh.name AS warehouse_name, z.name AS zone_name')
    ->select('u.uname, u.name AS display_name')
    ->from('return_order AS r')
    ->join('customers AS c', 'r.customer_code = c.code', 'left')
    ->join('warehouse AS wh', 'r.warehouse_code = wh.code', 'left')
    ->join('zone AS z', 'r.zone_code = z.code', 'left')
    ->join('user AS u', 'z.user_id = u.id', 'left')
    ->where('r.code', $code)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_details($code)
  {
    $rs = $this->db
		->select('rd.*, pd.unit_code AS unit_code, pd.count_stock, pd.barcode')
		->from('return_order_detail AS rd')
		->join('products AS pd', 'rd.product_code = pd.code', 'left')
		->where('rd.return_code', $code)
		->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_count_item_details($code)
  {
    $rs = $this->db
		->select('rd.*, pd.unit_code AS unit_code')
		->from('return_order_detail AS rd')
		->join('products AS pd', 'rd.product_code = pd.code', 'left')
		->where('rd.return_code', $code)
    ->where('pd.count_stock', 1)
		->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


	public function get_non_count_details($code)
	{
		$rs = $this->db
		->select('rd.*, pd.unit_code AS unit_code')
		->from('return_order_detail AS rd')
		->join('products AS pd', 'rd.product_code = pd.code', 'left')
		->where('rd.return_code', $code)
    ->where('pd.count_stock', 0)
		->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
	}


	public function get_detail_by_product($code, $product_code)
	{
		$rs = $this->db
		->select('rd.*, pd.unit_code')
		->from('return_order_detail AS rd')
		->join('products AS pd', 'rd.product_code = pd.code', 'left')
		->where('rd.return_code', $code)
		->where('rd.product_code', $product_code)
		->get();

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


	public function drop_not_valid_details($code)
	{
		return $this->db->where('return_code', $code)->where('valid', 0)->delete('return_order_detail');
	}


  public function drop_sap_exists_details($code)
  {
    return $this->mc->where('U_ECOMNO', $code)->delete('RDN1');
  }


  public function drop_middle_exits_data($docEntry)
  {
    $this->mc->trans_start();
    $this->mc->where('DocEntry', $docEntry)->delete('RDN1');
    $this->mc->where('DocEntry', $docEntry)->delete('ORDN');
    $this->mc->trans_complete();

    return $this->mc->trans_status();
  }


  public function get_invoice_details($invoice)
  {
    $qr = "SELECT ivd.DocEntry, ivd.LineNum, ivd.U_ECOMNO AS order_code, iv.DocNum, iv.NumAtCard,
    ivd.ItemCode AS product_code, ivd.Dscription AS product_name,
    (SELECT SUM(Quantity) FROM INV1 WHERE DocEntry = ivd.DocEntry AND ItemCode = ivd.ItemCode AND U_ECOMNO = ivd.U_ECOMNO GROUP BY U_ECOMNO) AS qty,
    ivd.PriceBefDi AS price, ivd.DiscPrcnt AS discount
    FROM INV1 AS ivd
    LEFT JOIN OINV AS iv ON ivd.DocEntry = iv.DocEntry
    WHERE iv.DocNum = '{$invoice}'";

    $rs = $this->ms->query($qr);
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_filter_invoice_detail($invoice, $order_code = NULL, $product_code = NULL)
  {
    $this->ms
    ->select('ivd.DocEntry, ivd.LineNum, ivd.U_ECOMNO AS order_code')
    ->select('ivd.ItemCode AS product_code, ivd.Dscription AS product_name')
    ->select('ivd.Quantity AS qty, ivd.PriceBefDi AS price, ivd.DiscPrcnt AS discount')
    ->select('iv.DocNum AS code, iv.CardCode AS customer_code, iv.CardName AS customer_name')
    ->from('INV1 AS ivd')
    ->join('OINV AS iv', 'ivd.DocEntry = iv.DocEntry')
    ->where('iv.DocNum', $invoice);

    if( ! empty($order_code))
    {
      $this->ms->like('ivd.U_ECOMNO', $order_code);
    }

    if( ! empty($product_code))
    {
      $this->ms->like('ivd.ItemCode', $product_code);
    }

    $rs = $this->ms->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_invoice_detail_by_order_item($order_code, $item_code)
  {
    $rs = $this->ms
    ->select('ivd.DocEntry, ivd.LineNum, ivd.U_ECOMNO AS order_code')
    ->select('ivd.ItemCode AS product_code, ivd.Dscription AS product_name')
    ->select('ivd.Quantity AS qty, ivd.PriceBefDi AS price, ivd.DiscPrcnt AS discount')
    ->select('iv.DocNum AS code, iv.CardCode AS customer_code, iv.CardName AS customer_name, iv.NumAtCard')
    ->from('INV1 AS ivd')
    ->join('OINV AS iv', 'ivd.DocEntry = iv.DocEntry')
    ->where('ivd.U_ECOMNO', $order_code)
    ->where('ivd.ItemCode', $item_code)
    ->limit(1)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_total_return_vat($code)
  {
    $rs = $this->db
    ->select_sum('vat_amount', 'amount')
    ->where('return_code', $code)
    ->get('return_order_detail');

    return $rs->row()->amount === NULL ? 0 : $rs->row()->amount;
  }


  public function get_customer_invoice($invoice)
  {
    $rs = $this->ms->select('CardCode AS customer_code, CardName AS customer_name')->where('DocNum', $invoice)->get('OINV');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function delete_detail($id)
  {
    return $this->db->where('id', $id)->delete('return_order_detail');
  }


  public function drop_details($code)
  {
    return $this->db->where('return_code', $code)->delete('return_order_detail');
  }


  public function cancle_details($code)
  {
    return $this->db->set('is_cancle', 1)->where('return_code', $code)->update('return_order_detail');
  }


  //--- จำนวนรวมของสินค้าที่เคยคืนไปแล้ว ในใบกำกับนี้
  public function get_returned_qty($invoice, $order_code, $product_code)
  {
    if( ! empty($invoice))
    {
      $rs = $this->db
      ->select_sum('qty')
      ->where('invoice_code IS NOT NULL', NULL, FALSE)
      ->where('invoice_code', $invoice)
      ->where('order_code', $order_code)
      ->where('product_code', $product_code)
      ->where('is_cancle', 0)
      ->get('return_order_detail');

      return $rs->row()->qty === NULL ? 0 : $rs->row()->qty;
    }

    return 0;
  }


  public function get_sum_qty($code)
  {
    $rs = $this->db->select_sum('qty', 'qty')
    ->where('return_code', $code)
    ->get('return_order_detail');

    return $rs->row()->qty === NULL ? 0 : $rs->row()->qty;
  }


  public function get_sum_amount($code)
  {
    $rs = $this->db->select_sum('amount')
    ->where('return_code', $code)
    ->get('return_order_detail');

    return $rs->row()->amount === NULL ? 0 : $rs->row()->amount;
  }


  public function set_status($code, $status)
  {
    return $this->db->set('status', $status)->where('code', $code)->update('return_order');
  }


  public function approve($code)
  {
    $arr = array('is_approve' => 1, 'approver' => get_cookie('uname'));
    return $this->db->where('code', $code)->update('return_order', $arr);
  }


  public function unapprove($code)
  {
    $arr = array('is_approve' => 0, 'approver' => NULL);
    return $this->db->where('code', $code)->update('return_order', $arr);
  }


  public function count_rows(array $ds = array())
  {
    $this->db
    ->from('return_order AS r')
    ->join('customers AS c', 'r.customer_code = c.code', 'left')
    ->join('zone AS z', 'r.zone_code = z.code', 'left');

    if( ! empty($ds['order_code']))
    {
      $this->db
      ->join('return_order_detail AS d', 'r.code = d.return_code', 'left')
      ->like('d.order_code', $ds['order_code']);
    }

    //---- เลขที่เอกสาร
    if(!empty($ds['code']))
    {
      $this->db->like('r.code', $ds['code']);
    }

    //---- invoice
    if( ! empty($ds['invoice']))
    {
      $this->db
      ->group_start()
      ->like('r.invoice', $ds['invoice'])
      ->or_like('r.bill_code', $ds['invoice'])
      ->group_end();
    }

    //--- customer
    if(!empty($ds['customer_code']))
    {
      $this->db
      ->group_start()
      ->like('r.customer_code', $ds['customer_code'])
      ->or_like('c.name', $ds['customer_code'])
      ->group_end();
    }

    if(isset($ds['warehouse']) && $ds['warehouse'] != 'all')
    {
      $this->db->where('r.warehouse_code', $ds['warehouse']);
    }

		if( ! empty($ds['zone']))
    {
      $this->db
      ->group_start()
      ->like('r.zone_code', $ds['zone'])
      ->or_like('z.name', $ds['zone'])
      ->group_end();
    }

    if($ds['status'] != 'all')
    {
      if($ds['status'] == 5)
      {
        $this->db->where('r.is_expire', 1);
      }
      else
      {
        $this->db->where('r.is_expire', 0)->where('r.status', $ds['status']);
      }
    }

    if($ds['approve'] != 'all')
    {
      $this->db->where('r.is_approve', $ds['approve']);
    }

    if($ds['must_accept'] != 'all')
    {
      $this->db->where('r.must_accept', $ds['must_accept']);
    }


		if(isset($ds['api']) && $ds['api'] !== 'all')
		{
			$this->db->where('r.api', $ds['api']);
		}

    if(isset($ds['is_pos_api']) && $ds['is_pos_api'] !== 'all')
    {
      $this->db->where('r.is_pos_api', $ds['is_pos_api']);
    }

    if(isset($ds['wms_export']) && $ds['wms_export'] != 'all')
    {
      if($ds['wms_export'] == '0')
      {
        $this->db
        ->group_start()
        ->where('r.wms_export IS NULL', NULL, FALSE)
        ->or_where('r.wms_export', 0)
        ->group_end();
      }
      else
      {
        $this->db->where('r.wms_export', $ds['wms_export']);
      }
    }

    if(!empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db->where('r.date_add >=', from_date($ds['from_date']));
      $this->db->where('r.date_add <=', to_date($ds['to_date']));
    }

    if(isset($ds['sap']) && $ds['sap'] != 'all')
    {
      if($ds['sap'] == 0)
      {
        $this->db->where('r.inv_code IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('r.inv_code IS NOT NULL', NULL, FALSE);
      }
    }

    return $this->db->count_all_results();
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $this->db
    ->select('r.*, c.name AS customer_name, z.name AS zone_name, u.name AS display_name')
    ->from('return_order AS r')
    ->join('customers AS c', 'r.customer_code = c.code', 'left')
    ->join('zone AS z', 'r.zone_code = z.code', 'left')
    ->join('user AS u', 'r.user = u.uname', 'left');

    if( ! empty($ds['order_code']))
    {
      $this->db
      ->join('return_order_detail AS d', 'r.code = d.return_code', 'left')
      ->like('d.order_code', $ds['order_code']);
    }

    //---- เลขที่เอกสาร
    if(!empty($ds['code']))
    {
      $this->db->like('r.code', $ds['code']);
    }

    //---- invoice
    if( ! empty($ds['invoice']))
    {
      $this->db
      ->group_start()
      ->like('r.invoice', $ds['invoice'])
      ->or_like('r.bill_code', $ds['invoice'])
      ->group_end();
    }

    //--- customer
    if(!empty($ds['customer_code']))
    {
      $this->db
      ->group_start()
      ->like('r.customer_code', $ds['customer_code'])
      ->or_like('c.name', $ds['customer_code'])
      ->group_end();
    }

    if(isset($ds['warehouse']) && $ds['warehouse'] != 'all')
    {
      $this->db->where('r.warehouse_code', $ds['warehouse']);
    }

		if( ! empty($ds['zone']))
    {
      $this->db
      ->group_start()
      ->like('r.zone_code', $ds['zone'])
      ->or_like('z.name', $ds['zone'])
      ->group_end();
    }

    if($ds['status'] != 'all')
    {
      if($ds['status'] == 5)
      {
        $this->db->where('r.is_expire', 1);
      }
      else
      {
        $this->db->where('r.is_expire', 0)->where('r.status', $ds['status']);
      }
    }

    if($ds['approve'] != 'all')
    {
      $this->db->where('r.is_approve', $ds['approve']);
    }

    if($ds['must_accept'] != 'all')
    {
      $this->db->where('r.must_accept', $ds['must_accept']);
    }


		if(isset($ds['api']) && $ds['api'] !== 'all')
		{
			$this->db->where('r.api', $ds['api']);
		}

    if(isset($ds['is_pos_api']) && $ds['is_pos_api'] !== 'all')
    {
      $this->db->where('r.is_pos_api', $ds['is_pos_api']);
    }

    if(isset($ds['wms_export']) && $ds['wms_export'] != 'all')
    {
      if($ds['wms_export'] == '0')
      {
        $this->db
        ->group_start()
        ->where('r.wms_export IS NULL', NULL, FALSE)
        ->or_where('r.wms_export', 0)
        ->group_end();
      }
      else
      {
        $this->db->where('r.wms_export', $ds['wms_export']);
      }
    }

    if(!empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db->where('r.date_add >=', from_date($ds['from_date']));
      $this->db->where('r.date_add <=', to_date($ds['to_date']));
    }

    if(isset($ds['sap']) && $ds['sap'] != 'all')
    {
      if($ds['sap'] == 0)
      {
        $this->db->where('r.inv_code IS NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('r.inv_code IS NOT NULL', NULL, FALSE);
      }
    }

    $rs = $this->db->order_by('r.code', 'DESC')->limit($perpage, $offset)->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function is_exists_pos_ref($pos_ref)
  {
    $count = $this->db
    ->where('pos_ref', $pos_ref)
    ->where('status !=', 2)
    ->count_all_results('return_order');

    return $count > 0 ? TRUE : FALSE;
  }



  public function get_max_code($code)
  {
    $rs = $this->db
    ->select_max('code')
    ->like('code', $code, 'after')
    ->order_by('code', 'DESC')
    ->get('return_order');

    if($rs->num_rows() == 1)
    {
      return $rs->row()->code;
    }

    return FALSE;
  }


  public function customer_in($txt)
  {
    $sc = array('0');
    $rs = $this->db
    ->select('code')->
    like('code', $txt)
    ->or_like('name', $txt)
    ->get('customers');

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $rs)
      {
        $sc[] = $rs->code;
      }
    }

    return $sc;
  }

}

 ?>

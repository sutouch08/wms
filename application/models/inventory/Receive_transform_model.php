<?php
class Receive_transform_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get_sap_receive_transform($code)
  {
    $rs = $this->ms
    ->select('DocEntry, DocStatus')
    ->where('U_ECOMNO', $code)
    ->where('CANCELED', 'N')
    ->get('OIGN');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function is_middle_exists($code)
  {
    $rs = $this->mc->select('U_ECOMNO')->where('U_ECOMNO', $code)->get('OIGN');
    if($rs->num_rows() === 1)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function get_middle_receive_transform($code)
  {
    $rs = $this->mc
    ->select('DocEntry')
    ->where('U_ECOMNO', $code)
    ->group_start()
    ->where('F_Sap', 'N')
    ->or_where('F_Sap IS NULL', NULL, FALSE)
    ->group_end()
    ->get('OIGN');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function drop_middle_exits_data($docEntry)
  {
    $this->mc->trans_start();
    $this->mc->where('DocEntry', $docEntry)->delete('IGN1');
    $this->mc->where('DocEntry', $docEntry)->delete('OIGN');
    $this->mc->trans_complete();

    return $this->mc->trans_status();
  }


  public function add_sap_receive_transform(array $ds = array())
  {
    $rs = $this->mc->insert('OIGN', $ds);
    if($rs)
    {
      return $this->mc->insert_id();
    }

    return FALSE;
  }


  public function update_sap_receive_transform($code, $ds)
  {
    return $this->mc->where('U_ECOMNO', $code)->update('OIGN', $ds);
  }


  public function add_sap_receive_transform_detail(array $ds = array())
  {
    return $this->mc->insert('IGN1', $ds);
  }


  public function drop_sap_exists_details($code)
  {
    return $this->mc->where('U_ECOMNO', $code)->delete('IGN1');
  }



  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('receive_transform', $ds);
    }

    return FALSE;
  }



  public function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('code', $code)->update('receive_transform', $ds);
    }

    return FALSE;
  }


  public function add_detail(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('receive_transform_detail', $ds);
    }

    return FALSE;
  }



  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get('receive_transform');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }



  public function get_details($code)
  {
    $rs = $this->db
    ->select('receive_transform_detail.*, products.barcode')
    ->from('receive_transform_detail')
    ->join('products', 'products.code = receive_transform_detail.product_code', 'left')
    ->where('receive_code', $code)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function drop_details($code)
  {
    return $this->db->where('receive_code', $code)->delete('receive_transform_detail');
  }



  public function cancle_details($code)
  {
    return $this->db->set('is_cancle', 1)->where('receive_code', $code)->update('receive_transform_detail');
  }




  public function get_transform_details($order_code)
  {
    $rs = $this->db
    ->select('order_transform_detail.*, products.name, products.cost AS price, products.barcode, products.unit_code')
    ->select_sum('order_transform_detail.sold_qty', 'sold_qty')
    ->select_sum('order_transform_detail.receive_qty', 'receive_qty')
    ->from('order_transform_detail')
    ->join('order_transform', 'order_transform.order_code = order_transform_detail.order_code', 'left')
    ->join('products', 'products.code = order_transform_detail.product_code', 'left')
    ->where('order_transform_detail.order_code', $order_code)
    ->where('order_transform.is_closed', 0)
    ->group_by('order_transform_detail.product_code')
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function get_sum_qty($code)
  {
    $rs = $this->db->select_sum('qty', 'qty')
    ->where('receive_code', $code)
    ->get('receive_transform_detail');

    return intval($rs->row()->qty);
  }



  public function get_sum_amount($code)
  {
    $rs = $this->db->select_sum('amount')->where('receive_code', $code)->get('receive_transform_detail');
    return $rs->row()->amount === NULL ? 0.00 : $rs->row()->amount;
  }




	public function get_transform_backlogs($code, $product_code)
	{
		$rs = $this->db
		->select_sum('sold_qty')
		->select_sum('receive_qty')
		->where('order_code', $code)
		->where('product_code', $product_code)
		->get('order_transform_detail');

		if($rs->num_rows() === 1)
		{
			return $rs->row();
		}

		return NULL;
	}


  public function set_status($code, $status)
  {
    return $this->db->set('status', $status)->where('code', $code)->update('receive_transform');
  }



  public function count_rows(array $ds = array())
  {
    $this->db->select('status');

    //---- เลขที่เอกสาร
    if($ds['code'] != '')
    {
      $this->db->like('code', $ds['code']);
    }

    //--- ใบสั่งซื้อ
    if($ds['order_code'] != '')
    {
      $this->db->like('order_code', $ds['order_code']);
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

		if($ds['is_wms'] !== 'all')
		{
			$this->db->where('is_wms', $ds['is_wms']);
		}


    $rs = $this->db->get('receive_transform');


    return $rs->num_rows();
  }





  public function get_data(array $ds = array(), $perpage = '', $offset = '')
  {
    //---- เลขที่เอกสาร
    if($ds['code'] != '')
    {
      $this->db->like('code', $ds['code']);
    }

    //--- ใบสั่งซื้อ
    if($ds['order_code'] != '')
    {
      $this->db->like('order_code', $ds['order_code']);
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

		if($ds['is_wms'] !== 'all')
		{
			$this->db->where('is_wms', $ds['is_wms']);
		}

    $this->db->order_by('code', 'DESC');
    if($perpage != '')
    {
      $offset = $offset === NULL ? 0 : $offset;
      $this->db->limit($perpage, $offset);
    }

    $rs = $this->db->get('receive_transform');
    return $rs->result();
  }


  public function get_max_code($code)
  {
    $rs = $this->db
    ->select_max('code')
    ->like('code', $code, 'after')
    ->order_by('code', 'DESC')
    ->get('receive_transform');

    if($rs->num_rows() == 1)
    {
      return $rs->row()->code;
    }

    return FALSE;
  }


  public function is_exists($code)
  {
    $rs = $this->db->select('status')->where('code', $code)->get('receive_transform');
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
    ->where('status', 1)
    ->where('inv_code IS NULL', NULL, FALSE)
    ->get('receive_transform');

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
    ->get('OIGN');

    if($rs->num_rows() > 0)
    {
      return $rs->row()->DocNum;
    }

    return NULL;
  }



  public function update_inv($code, $doc_num)
  {
    return $this->db->set('inv_code', $doc_num)->where('code', $code)->update('receive_transform');
  }

}

 ?>

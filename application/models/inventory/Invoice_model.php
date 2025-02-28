<?php
class Invoice_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get_billed_detail($code)
  {
    $qr = "SELECT o.id, o.product_code, o.product_name, o.qty AS order_qty, o.is_count, ";
    $qr .= "o.price, o.discount1, o.discount2, o.discount3, ";
    $qr .= "(o.discount_amount / o.qty) AS discount_amount, ";
    $qr .= "(o.total_amount/o.qty) AS final_price ";
    $qr .= "FROM order_details AS o ";
    $qr .= "WHERE o.order_code = '{$code}'";

    $qs = $this->db->query($qr);

    if($qs->num_rows() > 0)
    {
      $details = $qs->result();

      foreach($details as $rs)
      {
        $rs->prepared = $this->get_sum_prepared($code, $rs->product_code, $rs->id);
        $rs->qc = $this->get_sum_qc($code, $rs->product_code, $rs->id);
        $rs->sold = $this->get_sum_order_sold($code, $rs->product_code, $rs->id);
      }

      return $details;
    }

    return NULL;
  }


  public function get_sum_order_sold($order_code, $product_code, $order_detail_id)
  {
    $rs = $this->db
    ->select_sum('qty')
    ->where('reference', $order_code)
    ->where('product_code', $product_code)
    ->group_start()
    ->where('order_detail_id', $order_detail_id)
    ->or_where('order_detail_id IS NULL', NULL, FALSE)
    ->group_end()
    ->get('order_sold');

    if($rs->num_rows() > 0)
    {
      return $rs->row()->qty;
    }

    return 0;
  }

  public function get_sum_prepared($order_code, $product_code, $order_detail_id)
  {
    $rs = $this->db
    ->select_sum('qty')
    ->where('order_code', $order_code)
    ->where('product_code', $product_code)
    ->group_start()
    ->where('order_detail_id', $order_detail_id)
    ->or_where('order_detail_id IS NULL', NULL, FALSE)
    ->group_end()
    ->get('prepare');

    if($rs->num_rows() > 0)
    {
      return $rs->row()->qty;
    }

    return 0;
  }


  public function get_sum_qc($order_code, $product_code, $order_detail_id)
  {
    $rs = $this->db
    ->select_sum('qty')
    ->where('order_code', $order_code)
    ->where('product_code', $product_code)
    ->group_start()
    ->where('order_detail_id', $order_detail_id)
    ->where('order_detail_id', $order_detail_id)
    ->or_where('order_detail_id IS NULL', NULL, FALSE)
    ->group_end()
    ->get('qc');

    if($rs->num_rows() > 0)
    {
      return $rs->row()->qty;
    }

    return 0;
  }


  public function get_billed_amount($code)
  {
    $rs = $this->db
    ->select_sum('total_amount')
    ->where('reference', $code)
    ->get('order_sold');

    return $rs->row()->total_amount;
  }

  //----- get sold qty from order sold
  public function get_billed_detail_qty($code)
  {
    $rs = $this->db
    ->select('product_code, product_name')
    ->select_sum('qty')
    ->where('is_count', 1)
    ->where('reference', $code)
    ->group_by('product_code')
    ->order_by('product_code', 'ASC')
    ->get('order_sold');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_details($code)
  {
    $rs = $this->db->where('reference', $code)->get('order_sold');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  //--- for print order
  public function get_sum_details($code)
  {
    $rs = $this->db
    ->select('reference, product_code, product_name, price, discount_label')
    ->select_sum('discount_amount')
    ->select_sum('qty')
    ->select_sum('total_amount')
    ->where('reference', $code)
    ->group_by('order_detail_id')
    ->get('order_sold');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

  //-- use in API
  public function get_details_summary_group_by_item($code)
  {
    $rs = $this->db
    ->select('reference AS code, product_code, product_name')
    ->select_sum('qty')
    ->where('reference', $code)
    ->group_by('product_code')
    ->get('order_sold');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_total_sold_qty($code)
  {
    $rs = $this->db->select_sum('qty')->where('reference', $code)->get('order_sold');
    return intval($rs->row()->qty);
  }



  public function get_item_sold_qty($code, $product_code)
  {
    $rs = $this->db->select_sum('qty')->where('reference', $code)->where('product_code', $product_code)->get('order_sold');

    if($rs->num_rows() === 1)
    {
      return intval($rs->row()->qty);
    }

    return 0;
  }



  public function drop_sold($id)
  {
    return $this->db->where('id', $id)->delete('order_sold');
  }


  public function drop_all_sold($code)
  {
    return $this->db->where('reference', $code)->delete('order_sold');
  }



  public function is_over_due($customer_code)
  {
    $is_strict = getConfig('STRICT_OVER_DUE');
    if($is_strict == 0)
    {
      return FALSE;
    }
    else
    {
      $control_day = getConfig('OVER_DUE_DATE');
			$control_day++;

      $rs = $this->ms
      ->select('DocEntry', FALSE)
      ->where('CardCode', $customer_code)
      ->where('DocTotal >', 'PaidToDate', FALSE)
      ->where("DATEADD(day,{$control_day}, DocDueDate) < ", "GETDATE()", FALSE)
      ->get('OINV');

      if($rs->num_rows() > 0)
      {
        return TRUE;
      }
    }

    return FALSE;
  }


  public function is_received($code)
  {
    $rs = $this->mc
    ->select('F_Receipt')
    ->where('U_ECOMNO', $code)
    ->group_start()
    ->where('F_Sap IS NULL', NULL, FALSE)
    ->or_where('F_Sap', 'N')
    ->group_end()
    ->get('DFOWTR');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->F_Receipt;
    }

    return FALSE;
  }

} //--- end class

 ?>

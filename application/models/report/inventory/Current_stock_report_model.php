<?php
class Current_stock_report_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get_stock_summary()
  {
    $qr  = "SELECT SUM(OIBQ.OnHandQty) AS qty, ";
    $qr .= "SUM(OIBQ.OnHandQty * ITM1.Price) AS amount ";
    $qr .= "FROM OIBQ ";
    $qr .= "LEFT JOIN ITM1 ON OIBQ.ItemCode = ITM1.ItemCode ";
    $qr .= "AND ITM1.PriceList = 13 ";
    $qr .= "WHERE OIBQ.OnHandQty != 0 ";

    $rs = $this->ms->query($qr);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function get_stock_summary_by_group($code)
  {
    $qr  = "SELECT SUM(OIBQ.OnHandQty) AS qty, ";
    $qr .= "SUM(OIBQ.OnHandQty * ITM1.Price) AS amount ";
    $qr .= "FROM OIBQ ";
    $qr .= "LEFT JOIN OITM ON OIBQ.ItemCode = OITM.ItemCode ";
    $qr .= "LEFT JOIN ITM1 ON OIBQ.ItemCode = ITM1.ItemCode ";
    $qr .= "AND ITM1.PriceList = 13 ";
    $qr .= "WHERE OIBQ.OnHandQty != 0 ";
    $qr .= "AND OITM.U_GROUP = '{$code}' ";

    $rs = $this->ms->query($qr);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function get_style_summary($code)
  {
    $qr  = "SELECT SUM(OIBQ.OnHandQty) AS qty, ";
    $qr .= "SUM(OIBQ.OnHandQty * ITM1.Price) AS amount ";
    $qr .= "FROM OIBQ ";
    $qr .= "LEFT JOIN OITM ON OIBQ.ItemCode = OITM.ItemCode ";
    $qr .= "LEFT JOIN ITM1 ON OIBQ.ItemCode = ITM1.ItemCode ";
    $qr .= "AND ITM1.PriceList = 13 ";
    $qr .= "WHERE OIBQ.OnHandQty != 0 ";
    $qr .= "AND OITM.U_MODEL = '{$code}' ";

    $rs = $this->ms->query($qr);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;

  }

  public function get_count_style()
  {
    return $this->db->count_all_results('product_style');
  }


  public function get_count_style_by_group($code)
  {
    return $this->db->where('group_code', $code)->count_all_results('product_style');
  }


  public function get_count_item()
  {
    return $this->db->count_all_results('products');
  }

  public function get_count_item_by_group($code)
  {
    return $this->db->where('group_code', $code)->count_all_results('products');
  }




  public function get_style_by_group($code)
  {
    $rs = $this->db
    ->select('code')
    ->where('group_code', $code)
    ->order_by('year', 'DESC')
    ->get('product_style');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

} //--- end class


 ?>

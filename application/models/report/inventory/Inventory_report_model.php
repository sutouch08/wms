<?php
class Inventory_report_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function getStock($option, $limit = 100, $offset = 0)
  {
    $this->ms
    ->select('OITW.ItemCode')
    ->select_sum('OITW.OnHand')
    ->from('OITW')
    ->join('OITM', 'OITW.ItemCode = OITM.ItemCode', 'left')
    ->where('OITW.OnHand >', 0, FALSE);

    if($option->allProduct == 0 && ! empty($option->pdFrom) && ! empty($option->pdTo))
    {
      $this->ms->where('OITM.U_MODEL >=', $option->pdFrom)->where('OITM.U_MODEL <=', $option->pdTo);
    }

    if($option->allWhouse == 0 && ! empty($option->whsList))
    {
      $this->ms->where_in('OITW.WhsCode', $option->whsList);
    }

    $rs = $this->ms->group_by('OITW.ItemCode')->order_by('OITW.ItemCode', 'ASC')->limit($limit, $offset)->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_current_stock_balance($allProduct, $pdFrom, $pdTo, $allWhouse, $warehouse)
  {
    $this->ms
    ->select('OITW.ItemCode AS product_code')
    ->select_sum('OITW.OnHand', 'qty')
    ->from('OITW')
    ->join('OITM', 'OITW.ItemCode = OITM.ItemCode', 'left')
    ->where('OITW.OnHand >', 0, FALSE);

    if($allProduct == 0 && !empty($pdFrom) && !empty($pdTo))
    {
      $this->ms->where('OITM.U_MODEL >=', $pdFrom)->where('OITM.U_MODEL <=', $pdTo);
    }

    if($allWhouse == 0 && !empty($warehouse))
    {
      $this->ms->where_in('OITW.WhsCode', $warehouse);
    }

    $this->ms->group_by('OITW.ItemCode');
    $this->ms->order_by('OITW.ItemCode', 'ASC');
    $rs = $this->ms->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function get_reserv_stock($item_code, $warehouse = NULL)
  {
    $this->db
    ->select_sum('order_details.qty', 'qty')
    ->from('order_details')
    ->join('orders', 'order_details.order_code = orders.code', 'left')
    ->where('order_details.product_code', $item_code)
    ->where('order_details.is_complete', 0)
    ->where('order_details.is_expired', 0)
		->where('order_details.is_cancle', 0)
    ->where('order_details.is_count', 1);

    if($warehouse !== NULL)
    {
      $this->db->where_in('orders.warehouse_code', $warehouse);
    }

    $rs = $this->db->get();

    if($rs->num_rows() == 1)
    {
      return empty($rs->row()->qty) ? 0 : $rs->row()->qty;
    }

    return 0;
  }

}
 ?>

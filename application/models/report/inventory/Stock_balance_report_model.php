<?php
class Stock_balance_report_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get_stock_balance_zone($allProduct, $pdFrom, $pdTo, $allWhouse, $warehouse, $allZone, $zoneCode)
  {
    $this->ms
    ->select('OITM.ItemCode AS product_code')
    ->select('OITM.ItemName AS product_name')
    ->select('ITM1.Price AS price')
    ->select('OBIN.WhsCode AS warehouse_code')
    ->select('OBIN.BinCode AS zone_code')
    ->select('OBIN.Descr AS zone_name')
    ->select('OIBQ.OnHandQty AS qty')
    ->from('OIBQ')
    ->join('OITM', 'OIBQ.ItemCode = OITM.ItemCode', 'left')
    ->join('ITM1', 'OITM.ItemCode = ITM1.ItemCode AND ITM1.PriceList = 11')
    ->join('OBIN', 'OIBQ.BinAbs = OBIN.AbsEntry','left')
    ->where('OIBQ.OnHandQty !=', 0, FALSE);

    if($allProduct == 0 && !empty($pdFrom) && !empty($pdTo))
    {
      $this->ms->where('OITM.U_MODEL >=', $pdFrom)->where('OITM.U_MODEL <=', $pdTo);
    }

    if($allZone == 1 && empty($zoneCode))
    {
      if($allWhouse == 0 && !empty($warehouse))
      {
        $this->ms->where_in('OIBQ.WhsCode', $warehouse);
      }
    }

    if($allZone == 0 && !empty($zoneCode))
    {
      $this->ms->where('OBIN.BinCode', $zoneCode);
    }

		 $this->ms->order_by('OITM.ItemCode', 'ASC');
    $this->ms->order_by('OBIN.WhsCode', 'ASC');
    $this->ms->order_by('OBIN.BinCode', 'ASC');


    $rs = $this->ms->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function get_warehouse_stock($whsCode, $limit = 100, $offset = 0)
  {
    $rs = $this->ms
    ->select('W.WhsCode, W.OnHand, WH.WhsName, I.ItemCode, I.ItemName, P.Price AS Cost')
    ->from('OITW AS W')
    ->join('OWHS AS WH', 'W.WhsCode = WH.WhsCode', 'left')
    ->join('OITM AS I', 'W.ItemCode = I.ItemCode', 'left')
    ->join('ITM1 AS P', 'I.ItemCode = P.ItemCode AND P.PriceList = 13', 'left')
    ->where('W.WhsCode', $whsCode)
    ->where('W.OnHand >', 0, FALSE)
    ->order_by('W.ItemCode', 'ASC')
    ->limit($limit, $offset)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

}
 ?>

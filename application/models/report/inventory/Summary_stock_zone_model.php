<?php
class Summary_stock_zone_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function getStockZone($whsCode, $rowCode, $option = 'A')
  {
    $zone = $whsCode.'-'.$rowCode;

    $qr  = "SELECT SUM(Qty) AS Qty, BinName ";
    $qr .= "FROM (SELECT SUM(Q.OnHandQty) AS Qty, B.SL1Code AS BinName FROM OIBQ Q INNER JOIN OBIN B ON Q.BinAbs = B.AbsEntry ";
    $qr .= "WHERE Q.WhsCode = '{$whsCode}' AND B.BinCode LIKE '{$zone}%' GROUP BY Q.BinAbs, B.SL1Code) AS S ";
    $qr .= "GROUP BY BinName ";


    if($option == 'E')
    {
      $qr .= "HAVING SUM(Qty) = 0 ";
    }

    if($option == 'S')
    {
      $qr .= "HAVING SUM(Qty) < 1000 ";
    }

    $qr .= "ORDER BY BinName ASC";

    $rs = $this->ms->query($qr);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }
}
 ?>

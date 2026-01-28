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

    if($option == 'E')
    {
      $qr  = "SELECT SUM(Q.OnHandQty) AS Qty, B.SL1Code AS BinName ";
      $qr .= "FROM OIBQ Q INNER JOIN OBIN B ON Q.BinAbs = B.AbsEntry ";
      $qr .= "WHERE Q.WhsCode = '{$whsCode}' ";
      $qr .= "AND B.BinCode LIKE '{$zone}%' ";
      $qr .= "AND Q.OnHandQty = 0 ";
      $qr .= "GROUP BY Q.BinAbs, B.SL1Code ";
      $qr .= "ORDER BY B.SL1Code ASC";
    }

    if($option == 'S')
    {
      $qr  = "SELECT SUM(Qty) AS Qty, BinName ";
      $qr .= "FROM (SELECT SUM(Q.OnHandQty) AS Qty, B.SL1Code AS BinName FROM OIBQ Q INNER JOIN OBIN B ON Q.BinAbs = B.AbsEntry ";
      $qr .= "WHERE Q.WhsCode = '{$whsCode}' ";
      $qr .= "AND B.BinCode LIKE '{$zone}%' ";
      $qr .= "GROUP BY Q.BinAbs, B.SL1Code) AS S ";
      $qr .= "GROUP BY BinName HAVING SUM(Qty) < 1000 ";
      $qr .= "ORDER BY BinName ASC";
    }

    if($option == 'A')
    {
      $qr  = "SELECT SUM(Q.OnHandQty) AS Qty, B.SL1Code AS BinName ";
      $qr .= "FROM OIBQ Q INNER JOIN OBIN B ON Q.BinAbs = B.AbsEntry ";
      $qr .= "WHERE Q.WhsCode = '{$whsCode}' ";
      $qr .= "AND B.BinCode LIKE '{$zone}%' ";
      $qr .= "GROUP BY Q.BinAbs, B.SL1Code ";
      $qr .= "ORDER BY B.SL1Code ASC";
    }

    $rs = $this->ms->query($qr);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }
}
 ?>

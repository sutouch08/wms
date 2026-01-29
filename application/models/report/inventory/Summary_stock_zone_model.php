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

    $qr  = "SELECT SUM(Qty) AS Qty, BinAbs, BinName ";
    $qr .= "FROM (SELECT SUM(Q.OnHandQty) AS Qty, Q.BinAbs, B.SL1Code AS BinName FROM OIBQ Q INNER JOIN OBIN B ON Q.BinAbs = B.AbsEntry ";
    $qr .= "WHERE Q.WhsCode = '{$whsCode}' AND B.BinCode LIKE '{$zone}%' GROUP BY Q.BinAbs, B.SL1Code) AS S ";
    $qr .= "GROUP BY BinAbs, BinName ";


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

  public function getItemStockZone($BinAbs)
  {
    $rs = $this->ms->where('BinAbs', $BinAbs)->where('OnHandQty >', 0)->get('OIBQ');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function getBinName($binAbs)
  {
    $rs = $this->ms->select('BinCode, SL1Code AS BinName')->where('AbsEntry', $binAbs)->get('OBIN');

    if($rs->num_rows() === 1)
    {
      return empty($rs->row()->BinName) ? $rs->row()->BinCode : $rs->row()->BinName;
    }

    return NULL;
  }
}
 ?>

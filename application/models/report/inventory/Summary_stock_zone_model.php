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


  public function getStockZoneInclude($option = 'A', array $binAbs = array())
  {
    if( ! empty($binAbs))
    {
      $binAbsIn = $this->parseBinAbs($binAbs);

      if( ! empty($binAbsIn))
      {
        $qr  = "SELECT SUM(Qty) AS Qty, BinAbs, BinName ";
        $qr .= "FROM (SELECT SUM(Q.OnHandQty) AS Qty, Q.BinAbs, B.SL1Code AS BinName FROM OIBQ Q INNER JOIN OBIN B ON Q.BinAbs = B.AbsEntry ";
        $qr .= "WHERE Q.BinAbs IN(".$binAbsIn.") GROUP BY Q.BinAbs, B.SL1Code) AS S ";
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
    }

    return NULL;
  }


  public function parseBinAbs(array $ds = array())
  {
    $ids = "";

    if( ! empty($ds))
    {
      $i = 1;

      foreach($ds as $id)
      {
        $ids .= $i == 1 ? $id : ", {$id}";
        $i++;
      }
    }

    return $ids;
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


  public function getBinAbsByItem($whsCode, $rowCode, $itemCode)
  {
    $binAbs = [];
    $zone = $whsCode.'-'.$rowCode;
    $rs = $this->ms
    ->select('Q.BinAbs')
    ->from('OIBQ AS Q')
    ->join('OBIN AS B', 'Q.BinAbs = B.AbsEntry', 'left')
    ->where('Q.ItemCode', $itemCode)
    ->where('Q.WhsCode', $whsCode)
    ->like('B.BinCode', $zone, 'after')
    ->where('Q.OnHandQty >', 0)
    ->get();

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $ro)
      {
        $binAbs[] = $ro->BinAbs;
      }
    }

    return $binAbs;
  }
}
 ?>

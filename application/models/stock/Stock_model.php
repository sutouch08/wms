<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class stock_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get_style_sell_stock($style_code, $warehouse = NULL)
  {
    $this->ms->select_sum('OIBQ.OnHandQty', 'qty')
    ->from('OBIN')
    ->join('OIBQ', 'OBIN.WhsCode = OIBQ.WhsCode AND OBIN.AbsEntry = OIBQ.BinAbs', 'left')
    ->join('OITM', 'OIBQ.ItemCode = OITM.ItemCode', 'left')
    ->join('OWHS', 'OWHS.WhsCode = OBIN.WhsCode', 'left')
    ->where('OWHS.U_MAIN', 'Y');

		if(getConfig('SYSTEM_BIN_LOCATION') == 0)
		{
			$this->ms->where('OBIN.SysBin', 'N');
		}


    if($warehouse !== NULL)
    {
      $this->ms->where('OWHS.WhsCode', $warehouse);
    }

    $this->ms->where('OITM.U_MODEL', $style_code);

    $rs = $this->ms->get();
    if($rs->num_rows() == 1)
    {
      return intval($rs->row()->qty);
    }

    return 0;
  }


  public function count_items_zone($zone_code)
  {
    $this->ms
    ->from('OBIN')
    ->join('OIBQ', 'OBIN.WhsCode = OIBQ.WhsCode AND OBIN.AbsEntry = OIBQ.BinAbs', 'left')
    ->where('OBIN.BinCode', $zone_code)
    ->where('OIBQ.OnHandQty >', 0, FALSE);

    return $this->ms->count_all_results();
  }


  public function count_items_consignment_zone($zone_code)
  {
    $this->cn
    ->from('OIBQ')
    ->join('OBIN', 'OBIN.WhsCode = OIBQ.WhsCode AND OIBQ.BinAbs = OBIN.AbsEntry', 'left')
    ->where('OBIN.BinCode', $zone_code);

    return $this->cn->count_all_results();
  }


  public function get_stock_zone($zone_code, $pd_code)
  {
    $this->ms->select_sum('OIBQ.OnHandQty', 'qty')
    ->from('OBIN')
    ->join('OIBQ', 'OBIN.WhsCode = OIBQ.WhsCode AND OBIN.AbsEntry = OIBQ.BinAbs', 'left')
    ->where('OIBQ.ItemCode', $pd_code)
    ->where('OBIN.BinCode', $zone_code);

    $rs = $this->ms->get();

    if($rs->num_rows() == 1)
    {
      return intval($rs->row()->qty);
    }

    return 0;
  }


  public function get_consign_stock_zone($zone_code, $pd_code)
  {
    $this->cn
		->select_sum('OIBQ.OnHandQty', 'qty')
    ->from('OBIN')
    ->join('OIBQ', 'OBIN.WhsCode = OIBQ.WhsCode AND OBIN.AbsEntry = OIBQ.BinAbs', 'left')
    ->where('OIBQ.ItemCode', $pd_code)
    ->where('OBIN.BinCode', $zone_code);

    $rs = $this->cn->get();

    if($rs->num_rows() == 1)
    {
      return intval($rs->row()->qty);
    }

    return 0;
  }


  //---- ยอดรวมสินค้าในคลังที่สั่งได้ ยอดในโซน
  public function get_sell_stock($item, $warehouse = NULL, $zone = NULL, $sysBin = NULL)
  {
    $sysBin = $sysBin === NULL ? getConfig('SYSTEM_BIN_LOCATION') : $sysBin;

    $this->ms
    ->select_sum('OnHandQty', 'qty')
    ->from('OIBQ')
    ->join('OBIN', 'OBIN.WhsCode = OIBQ.WhsCode AND OBIN.AbsEntry = OIBQ.BinAbs', 'left')
    ->join('OWHS', 'OWHS.WhsCode = OBIN.WhsCode', 'left')
    ->where('OIBQ.ItemCode', $item);
    // ->where('OWHS.U_MAIN', 'Y');

		if( $sysBin == 0)
		{
			$this->ms->where('OBIN.SysBin', 'N');
		}

    if( ! empty($warehouse))
    {
      $this->ms->where('OWHS.WhsCode', $warehouse);
    }
    else
    {
      $this->ms->where('OWHS.U_MAIN', 'Y');
    }

    if( ! empty($zone))
    {
      $this->ms->where('OBIN.BinCode', $zone);
    }

    $rs = $this->ms->get();

    return intval($rs->row()->qty);
  }


  //--- ยอดรวมสินค้าทั้งหมดทุกคลัง (รวมฝากขาย)
  public function get_stock($item)
  {
    $this->ms
    ->select_sum('OIBQ.OnHandQty', 'qty')
    ->from('OIBQ')
    ->join('OBIN', 'OIBQ.BinAbs = OBIN.AbsEntry', 'left')
		->where('ItemCode', $item);

		if(getConfig('SYSTEM_BIN_LOCATION') == 0)
		{
			$this->ms->where('OBIN.SysBin', 'N');
		}

    $rs = $this->ms->get();

    return intval($rs->row()->qty);
  }


  //---- ยอดรวมสินค้าในคลังที่สั่งได้ ยอดในโซน
  public function get_sell_items_stock(array $item = array(), $warehouse = NULL)
  {
    $sysBin = getConfig('SYSTEM_BIN_LOCATION');

    $this->ms
    ->select('OIBQ.ItemCode')
    ->select_sum('OnHandQty', 'qty')
    ->from('OIBQ')
    ->join('OBIN', 'OBIN.WhsCode = OIBQ.WhsCode AND OBIN.AbsEntry = OIBQ.BinAbs', 'left')
    ->join('OWHS', 'OWHS.WhsCode = OBIN.WhsCode', 'left')
    ->where_in('OIBQ.ItemCode', $item);
    // ->where('OWHS.U_MAIN', 'Y');

		if( $sysBin == 0)
		{
			$this->ms->where('OBIN.SysBin', 'N');
		}

    if( ! empty($warehouse))
    {
      $this->ms->where('OWHS.WhsCode', $warehouse);
    }
    else
    {
      $this->ms->where('OWHS.U_MAIN', 'Y');
    }

    if( ! empty($zone))
    {
      $this->ms->where('OBIN.BinCode', $zone);
    }

    $rs = $this->ms->group_by('OIBQ.ItemCode')->get();

    if($rs->num_rows() > 0)
    {
      $stock = [];

      foreach($rs->result() as $ro)
      {
        $stock[$ro->ItemCode] = $ro->qty;
      }

      return $stock;
    }

    return NULL;
  }


	//--- ยอดรวมสินค้าทั้งหมดในคลังฝากขายเทียมเท่านั้น
  public function get_consignment_stock($item)
  {
    $rs = $this->cn
    ->select_sum('OIBQ.OnHandQty', 'qty')
    ->from('OIBQ')
    ->join('OBIN', 'OIBQ.BinAbs = OBIN.AbsEntry', 'left')
    ->where('ItemCode', $item)
    ->get();

    return intval($rs->row()->qty);
  }


  public function get_available_stock_in_zone($ItemCode, $WhsCode)
  {
    $rs = $this->ms
    ->select('OBIN.BinCode, OBIN.Descr AS BinName, OBIN.WhsCode, OIBQ.OnHandQty')
    ->from('OIBQ')
    ->join('OBIN', 'OBIN.WhsCode = OIBQ.WhsCode AND OBIN.AbsEntry = OIBQ.BinAbs', 'left')
    ->where('OIBQ.ItemCode', $ItemCode)
    ->where('OIBQ.WhsCode', $WhsCode)
    ->where('OIBQ.OnHandQty !=', 0)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  //---- ยอดสินค้าคงเหลือในแต่ละโซน
  public function get_stock_in_zone($item, $warehouse = NULL)
  {
    $this->ms
    ->select('OBIN.BinCode AS code, OBIN.Descr AS name, OIBQ.OnHandQty AS qty')
    ->from('OIBQ')
    ->join('OBIN', 'OBIN.WhsCode = OIBQ.WhsCode AND OBIN.AbsEntry = OIBQ.BinAbs', 'left')
    ->join('OWHS', 'OWHS.WhsCode = OBIN.WhsCode', 'left')
    ->where('ItemCode', $item);

		if(getConfig('SYSTEM_BIN_LOCATION') == 0)
		{
			$this->ms->where('OBIN.SysBin', 'N');
		}

    if($warehouse !== NULL)
    {
      $this->ms->where('OWHS.WhsCode', $warehouse);
    }
    else
    {
      $this->ms->where('OWHS.U_MAIN', 'Y');
    }

    $rs = $this->ms->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return array();
  }


  //---- สินค้าทั้งหมดที่อยู่ในโซน (ใช้โอนสินค้าระหว่างคลัง)
  public function get_all_stock_in_zone($zone_code, $item_code = NULL)
  {
    $this->ms
    ->select('OIBQ.ItemCode AS product_code, OIBQ.OnHandQty AS qty')
    ->select('OITM.ItemName AS product_name, OITM.CodeBars AS barcode')
    ->select('ITM1.Price AS cost, ITM2.Price AS price')
    ->from('OIBQ')
    ->join('OBIN', 'OBIN.WhsCode = OIBQ.WhsCode AND OBIN.AbsEntry = OIBQ.BinAbs', 'left')
    ->join('OITM', 'OIBQ.ItemCode = OITM.ItemCode', 'left')
    ->join('ITM1 AS ITM1', '(ITM1.ItemCode = OITM.ItemCode AND ITM1.PriceList = 13)')
    ->join('ITM1 AS ITM2', '(ITM2.ItemCode = OITM.ItemCode AND ITM2.PriceList = 11)')
    ->where('OBIN.BinCode', $zone_code)
    ->where('OIBQ.OnHandQty !=', 0);

    if( ! empty($item_code))
    {
      $this->ms
      ->group_start()
      ->like('OIBQ.ItemCode', $item_code)
      ->or_like('OITM.CodeBars', $item_code)
      ->group_end();
    }

    $rs = $this->ms->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  //-- for inventory transfer
  public function getStockList($WhsCode, $BinCode, $ItemCode)
  {
    $ItemCode = $ItemCode == '*' ? NULL : $ItemCode;

    $this->ms
    ->select('OBIN.BinCode AS zone_code')
    ->select('OIBQ.ItemCode AS product_code, OIBQ.OnHandQty AS qty')
    ->select('OITM.ItemName AS product_name, OITM.CodeBars AS barcode')
    ->from('OIBQ')
    ->join('OBIN', 'OBIN.WhsCode = OIBQ.WhsCode AND OBIN.AbsEntry = OIBQ.BinAbs', 'left')
    ->join('OITM', 'OIBQ.ItemCode = OITM.ItemCode', 'left')
    ->where('OIBQ.WhsCode', $WhsCode);

    if( ! empty($BinCode))
    {
      $this->ms->where('OBIN.BinCode', $BinCode);
    }

    if( ! empty($ItemCode))
    {
      $this->ms->like('OIBQ.ItemCode', $ItemCode);
    }

    $rs = $this->ms->where('OIBQ.OnHandQty !=', 0)->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  //---- สินค้าทั้งหมดที่อยู่ในโซน POS API
  public function getAllStockInZone($zone_code, $limit = 10000, $offset = 0)
  {
    $rs = $this->ms
    ->select('OIBQ.ItemCode AS product_code, OIBQ.OnHandQty AS qty')
    ->from('OIBQ')
    ->join('OBIN', 'OBIN.WhsCode = OIBQ.WhsCode AND OBIN.AbsEntry = OIBQ.BinAbs', 'left')
    ->where('OBIN.BinCode', $zone_code)
    ->where('OIBQ.OnHandQty !=', 0)
    ->order_by('OIBQ.ItemCode', 'ASC')
    ->limit($limit, $offset)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  //---- สินค้าทั้งหมดที่อยู่ในโซน POS API
  public function getAllStockInConsignmentZone($zone_code, $limit = 10000, $offset = 0)
  {
    $rs = $this->cn
    ->select('OIBQ.ItemCode AS product_code, OIBQ.OnHandQty AS qty')
    ->from('OIBQ')
    ->join('OBIN', 'OBIN.WhsCode = OIBQ.WhsCode AND OBIN.AbsEntry = OIBQ.BinAbs', 'left')
    ->where('OBIN.BinCode', $zone_code)
    ->where('OIBQ.OnHandQty !=', 0)
    ->order_by('OIBQ.ItemCode', 'ASC')
    ->limit($limit, $offset)
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_all_stock_consignment_zone($zone_code, $item_code = NULL)
  {
    $this->cn
    ->select('OIBQ.ItemCode AS product_code, OIBQ.OnHandQty AS qty')
    ->select('OITM.ItemName AS product_name, OITM.CodeBars AS barcode')
    ->select('ITM1.Price AS cost, ITM2.Price AS price')
    ->from('OIBQ')
    ->join('OBIN', 'OBIN.WhsCode = OIBQ.WhsCode AND OBIN.AbsEntry = OIBQ.BinAbs', 'left')
    ->join('OITM', 'OIBQ.ItemCode = OITM.ItemCode', 'left')
    ->join('ITM1 AS ITM1', '(ITM1.ItemCode = OITM.ItemCode AND ITM1.PriceList = 13)')
    ->join('ITM1 AS ITM2', '(ITM2.ItemCode = OITM.ItemCode AND ITM2.PriceList = 11)')
    ->where('OBIN.BinCode', $zone_code)
    ->where('OIBQ.OnHandQty !=', 0);

    if( ! empty($item_code))
    {
      $this->cn
      ->group_start()
      ->like('OIBQ.ItemCode', $item_code)
      ->or_like('OITM.CodeBars', $item_code)
      ->group_end();
    }

    $rs = $this->cn->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

	//--- for compare stock
	public function get_items_stock($warehouse_code)
	{
		$rs = $this->ms
		->select('OITM.ItemCode AS code, OITM.ItemName AS name, OITM.CodeBars AS barcode, OITM.InvntryUom AS unit_code')
		->select('OITW.OnHand AS qty')
		->from('OITW')
		->join('OITM', 'OITW.ItemCode = OITM.ItemCode', 'left')
		->where('OITW.WhsCode', $warehouse_code)
		->where('OITM.InvntItem', 'Y')
		->get();

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


  public function get_item_stock($ItemCode, $WhsCode, $BinCode = NULL)
  {
    if( ! empty($BinCode))
    {
      $rs = $this->ms
      ->select('OIBQ.OnHandQty AS OnHand')
      ->from('OIBQ')
      ->join('OBIN', 'OBIN.WhsCode = OIBQ.WhsCode AND OBIN.AbsEntry = OIBQ.BinAbs', 'left')
      ->where('OBIN.BinCode', $BinCode)
      ->where('OIBQ.ItemCode', $ItemCode)
      ->where('OIBQ.OnHandQty !=', 0)
      ->get();
    }
    else
    {
      $rs = $this->ms
      ->select('OnHand')
      ->where('ItemCode', $ItemCode)
      ->where('WhsCode', $WhsCode)
      ->get('OITW');
    }

    if($rs->num_rows() === 1)
    {
      return $rs->row()->OnHand;
    }

    return 0;
  }


  public function get_item_batch_qty($ItemCode, $BatchNum, $WhsCode, $BinCode)
  {
    $rs = $this->ms
    ->select('Q.OnHandQty AS Qty')
    ->from('OBTN AS S')
    ->join('OBBQ AS Q', 'S.ItemCode = Q.ItemCode AND S.AbsEntry = Q.SnBMDAbs', 'left')
    ->join('OBIN AS B', 'Q.BinAbs = B.AbsEntry', 'left')
    ->where('S.ItemCode', $ItemCode)
    ->where('S.DistNumber', $BatchNum)
    ->where('Q.WhsCode', $WhsCode)
    ->where('B.BinCode', $BinCode)
    ->get();

    if($rs->num_rows() == 1)
    {
      return $rs->row()->Qty;
    }

    return 0;
  }
}//--- end class

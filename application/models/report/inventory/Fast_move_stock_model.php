<?php
class Fast_move_stock_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get_fast_move_zone($zone_code = NULL)
  {
    $this->db
    ->select('code, name')
    ->where('is_fast_move', 1);

    if( ! empty($zone_code))
    {
      $this->db->like('code', $zone_code);
    }

    $rs = $this->db->get('zone');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_item_list($item_code = NULL)
  {
    $items = [];

    if( ! empty($item_code))
    {
      $rs = $this->db
      ->select('code')
      ->like('code', $item_code)
      ->get('products');

      if($rs->num_rows() > 0)
      {
        foreach($rs->result() as $rd)
        {
          $items[] = $rd->code;
        }
      }
    }

    return $items;
  }


  public function get_stock($zone_code, $is_min = 1, $min_stock = 50, $product_code = NULL)
  {
    $this->ms->select('OIBQ.ItemCode AS product_code, OIBQ.OnHandQty AS qty, OITM.ItemName AS product_name')
    ->from('OIBQ')
    ->join('OITM', 'OIBQ.ItemCode = OITM.ItemCode', 'left')
    ->join('OBIN', 'OBIN.WhsCode = OIBQ.WhsCode AND OBIN.AbsEntry = OIBQ.BinAbs', 'left')
    ->where('OBIN.BinCode', $zone_code)
    ->where('OIBQ.OnHandQty >', 0);

    if( ! empty($product_code))
    {
      $this->ms->like('OIBQ.ItemCode', $product_code);
    }

    if(is_true($is_min))
    {
      $this->ms->where('OIBQ.OnHandQty <=', $min_stock);
    }

    $rs = $this->ms->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }
}
 ?>

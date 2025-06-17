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
      $option = 'both'; // like option
      $first = $zone_code[0]; //-- ตัวแรก
      $last = $zone_code[strlen($zone_code)-1]; // ตัวสุดท้าย

      if($first == '*' OR $last == '*')
      {
        if($first == '*' && $last != '*')
        {
          $option = 'before';
        }

        if($first != '*' && $last == '*')
        {
          $option = 'after';
        }
      }

      $this->db->like('code', trim($zone_code, '*'), $option);
    }

    $rs = $this->db->get('zone');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_stock_zone(array $zone, $is_min = 1, $min_stock = 50, $product_code = NULL)
  {
    $this->ms
    ->select('OBIN.BinCode AS zone_code, OBIN.Descr AS zone_name, OIBQ.ItemCode AS product_code, OIBQ.OnHandQty AS qty, OITM.ItemName AS product_name')
    ->from('OIBQ')
    ->join('OITM', 'OIBQ.ItemCode = OITM.ItemCode', 'left')
    ->join('OBIN', 'OBIN.WhsCode = OIBQ.WhsCode AND OBIN.AbsEntry = OIBQ.BinAbs', 'left')
    ->where_in('OBIN.BinCode', $zone)
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

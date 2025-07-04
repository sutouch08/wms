<?php
class Reserv_stock_model extends CI_Model
{
  private $tb = "reserv_stock";
  private $td = "reserv_stock_details";

  public function __construct()
  {
    parent::__construct();
  }


  public function get_reserv_stock($product_code, $warehouse_code = NULL, $is_mkp = FALSE)
  {
    $date = date('Y-m-d');

    $this->db
    ->select_sum('d.reserv_qty', 'qty')
    ->from('reserv_stock_details AS d')
    ->join('reserv_stock AS o', 'd.reserv_id = o.id', 'left')
    ->where('o.status', 'A')
    ->where('o.active', 1)
    ->where('o.start_date <=', $date)
    ->where('o.end_date >=', $date);

    if( ! empty($warehouse_code))
    {
      $this->db->where('o.warehouse_code', $warehouse_code);
    }

    //---- ไม่เอา mkp
    if($is_mkp)
    {
      $this->db->where('o.is_mkp', 0);
    }

    $rs = $this->db->where('d.product_code', $product_code)->get();

    if($rs->num_rows() === 1)
    {
      return intval($rs->row()->qty);
    }

    return 0;
  }


  public function get_items_reserv_stock(array $items = array(), $warehouse_code = NULL, $is_mkp = FALSE)
  {
    $date = date('Y-m-d');

    if( ! empty($items))
    {
      $this->db
      ->select('d.product_code')
      ->select_sum('d.reserv_qty', 'qty')
      ->from('reserv_stock_details AS d')
      ->join('reserv_stock AS o', 'd.reserv_id = o.id', 'left')
      ->where('o.status', 'A')
      ->where('o.active', 1)
      ->where('o.start_date <=', $date)
      ->where('o.end_date >=', $date);

      if( ! empty($warehouse_code))
      {
        $this->db->where('o.warehouse_code', $warehouse_code);
      }

      //---- ไม่เอา mkp
      if($is_mkp)
      {
        $this->db->where('o.is_mkp', 0);
      }

      $rs = $this->db
      ->where_in('d.product_code', $items)
      ->group_by('d.product_code')
      ->get();

      if($rs->num_rows() > 0)
      {
        $reserv = [];

        foreach($rs->result() as $ro)
        {
          $reserv[$ro->product_code] = $ro->qty;
        }

        return $reserv;
      }
    }

    return NULL;
  }


  public function get($id)
  {
    $rs = $this->db->where('id', $id)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return  NULL;
  }


  public function get_details($id)
  {
    $rs = $this->db->where('reserv_id', $id)->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_detail($id)
  {
    $rs = $this->db->where('id', $id)->get($this->td);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_detail_by_product($reserv_id, $product_code)
  {
    $rs = $this->db->where('reserv_id', $reserv_id)->where('product_code', $product_code)->get($this->td);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_details_by_product($item_code, $warehouse_code = NULL, $is_mkp = FALSE)
  {
    $date = date('Y-m-d');
    $is_mkp = $is_mkp ? 1 : 0;

    $this->db
    ->select('d.*')
    ->from('reserv_stock_details AS d')
    ->join('reserv_stock AS o', 'd.reserv_id = o.id', 'left')
    ->where('d.product_code', $item_code)
    ->where('o.status', 'A')
    ->where('o.active', 1)
    ->where('o.start_date <=', $date)
    ->where('o.end_date >=', $date)
    ->where('o.is_mkp', $is_mkp);

    if( ! empty($warehouse_code))
    {
      $this->db->where('o.warehouse_code', $warehouse_code);
    }

    $rs = $this->db->order_by('o.id', 'ASC')->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function add(array $ds = array())
  {
    if( ! empty($ds))
    {
      if($this->db->insert($this->tb, $ds))
      {
        return $this->db->insert_id();
      }
    }

    return FALSE;
  }


  public function add_detail(array $ds = array())
  {
    if( ! empty($ds))
    {
      if($this->db->insert($this->td, $ds))
      {
        return $this->db->insert_id();
      }
    }

    return FALSE;
  }


  public function update($id, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('id', $id)->update($this->tb, $ds);
    }

    return FALSE;
  }


  public function update_detail($id, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('id', $id)->update($this->td, $ds);
    }

    return FALSE;
  }


  public function deduct_reserv_qty($item_code, $qty, $warehouse_code = NULL, $is_mkp = FALSE)
  {
    $rows = $this->get_details_by_product($item_code, $warehouse_code, $is_mkp);

    if( ! empty($rows))
    {
      foreach($rows as $rs)
      {
        if($qty > 0)
        {
          $Qty =  $rs->reserv_qty >= $qty ? $qty : $rs->reserv_qty;

          $dQty = $Qty * (-1);

          if($this->db->set("reserv_qty", "reserv_qty + {$dQty}", FALSE)->where('id', $rs->id)->update($this->td))
          {
            $qty -= $Qty;
          }
        }
      }
    }

    return TRUE;
  }


  public function remove_items(array $ds = array())
  {
    //--- ds = array('id', 'id', 'id')

    if( ! empty($ds))
    {
      return $this->db->where_in('id', $ds)->delete($this->td);
    }

    return FALSE;
  }


  public function remove_item($id)
  {
    return $this->db->where('id', $id)->delete($this->td);
  }


  public function count_sku($id)
  {
    $count = $this->db->where('reserv_id', $id)->count_all_results($this->td);

    return $count;
  }


  public function get_sum_qty($id)
  {
    $rs = $this->db
    ->select_sum('qty')
    ->where('reserv_id', $id)
    ->get($this->td);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->qty;
    }

    return 0;
  }


  public function count_rows(array $ds = array())
  {
    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['name']))
    {
      $this->db->like('name', $ds['name']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    if( ! empty($ds['start_date']))
    {
      $this->db->where('start_date >=', from_date($ds['start_date']));
    }

    if( ! empty($ds['end_date']))
    {
      $this->db->where('end_date <=', to_date($ds['end_date']));
    }

    if( isset($ds['status']) && $ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    if( isset($ds['active']) && $ds['active'] != 'all')
    {
      $this->db->where('active', $ds['active']);
    }

    return $this->db->count_all_results($this->tb);
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['name']))
    {
      $this->db->like('name', $ds['name']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    if( ! empty($ds['start_date']))
    {
      $this->db->where('start_date >=', from_date($ds['start_date']));
    }

    if( ! empty($ds['end_date']))
    {
      $this->db->where('end_date <=', to_date($ds['end_date']));
    }

    if( isset($ds['status']) && $ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    if( isset($ds['active']) && $ds['active'] != 'all')
    {
      $this->db->where('active', $ds['active']);
    }

    $rs = $this->db->order_by('code', 'DESC')->limit($perpage, $offset)->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_max_code($code)
  {
    $rs = $this->db
    ->select_max('code')
    ->like('code', $code)
    ->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->code;
    }

    return NULL;
  }
} //--- end class


 ?>

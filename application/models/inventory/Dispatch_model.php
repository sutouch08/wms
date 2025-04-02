<?php
class Dispatch_model extends CI_Model
{
  private $tb = "dispatch";
  private $td = "dispatch_details";
  private $to = "orders";

  public function __construct()
  {
    parent::__construct();
  }


  public function add(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert($this->tb, $ds);
    }

    return FALSE;
  }


  public function update($code, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('code', $code)->update($this->tb, $ds);
    }

    return FALSE;
  }


  public function update_detail($id , array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('id', $id)->update($this->td, $ds);
    }

    return FALSE;
  }


  public function update_details($code, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('dispatch_code', $code)->update($this->td, $ds);
    }

    return FALSE;
  }


  public function add_detail(array $ds = array())
  {
    if( ! empty($ds))
    {
      if( $this->db->insert($this->td, $ds))
      {
        return $this->db->insert_id();
      }
    }

    return FALSE;
  }


  public function add_carplate(array $ds = array())
  {
    return $this->db->insert('dispatch_cars', $ds);
  }


  public function add_driver($name)
  {
    $ds = ['name' => $name];

    return $this->db->insert('dispatch_driver', $ds);
  }


  public function delete_detail($id)
  {
    return $this->db->where('id', $id)->delete($this->td);
  }


  public function delete_details($code)
  {
    return $this->db->where('dispatch_code', $code)->delete($this->td);
  }


  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_details($code)
  {
    $rs = $this->db->where('dispatch_code', $code)->get($this->td);

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


  public function get_detail_by_order($dispatch_code, $order_code)
  {
    $rs = $this->db
    ->where('dispatch_code', $dispatch_code)
    ->group_start()
    ->where('order_code', $order_code)
    ->or_where('reference', $order_code)
    ->group_end()
    ->get($this->td);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['plate_no']))
    {
      $this->db->like('plate_no', $ds['plate_no']);
    }

    if( isset($ds['sender']) && $ds['sender'] != 'all')
    {
      $this->db->where('sender_code', $ds['sender']);
    }

    if(isset($ds['channels']) && $ds['channels'] != 'all')
    {
      if(isset($ds['channels']) && $ds['channels'] != 'all')
      {
        $this->db->where('channels_code', $ds['channels']);
      }
    }

    if( ! empty($ds['status']) && $ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    if( ! empty($ds['from_date']))
    {
      $thsi->db->where('date_add >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    $rs = $this->db
    ->order_by('code', 'DESC')
    ->limit($perpage, $offset)
    ->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function count_rows(array $ds = array())
  {
    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if( ! empty($ds['plate_no']))
    {
      $this->db->like('plate_no', $ds['plate_no']);
    }

    if( isset($ds['sender']) && $ds['sender'] != 'all')
    {
      $this->db->where('sender_code', $ds['sender']);
    }

    if(isset($ds['channels']) && $ds['channels'] != 'all')
    {
      $this->db->where('channels_code', $ds['channels']);
    }

    if( ! empty($ds['status']) && $ds['status'] != 'all')
    {
      $this->db->where('status', $ds['status']);
    }

    if( ! empty($ds['from_date']))
    {
      $thsi->db->where('date_add >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    return $this->db->count_all_results($this->tb);
  }


  public function get_order_list(array $ds = array())
  {

    $this->db
    ->select('id, code, reference, customer_code, customer_name, customer_ref, channels_code, date_add, role')
    ->where('state', '7')
    ->where('dispatch_id IS NULL', NULL, FALSE);

    if(isset($ds['role']) && $ds['role'] != 'all')
    {
      $this->db->where('role', $ds['role']);
    }

    if( ! empty($ds['code']))
    {
      $this->db
      ->group_start()
      ->like('code', $ds['code'])
      ->or_like('reference', $ds['code'])
      ->group_end();
    }

    if(isset($ds['channels']) && $ds['channels'] != 'all')
    {
      $this->db->where('channels_code', $ds['channels']);
    }

    if( ! empty($ds['customer']))
    {
      $this->db
      ->group_start()
      ->like('customer_code', $ds['customer'])
      ->or_like('customer_name', $ds['customer'])
      ->or_like('customer_ref', $ds['customer'])
      ->group_end();
    }

    if(isset($ds['warehouse']) && $ds['warehouse'] != 'all')
    {
      $this->db->where('warehouse_code', $ds['warehouse']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    $rs = $this->db->order_by('code', 'ASC')->get($this->to);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function update_order($order_code, $id)
  {
    return $this->db->set('dispatch_id', $id)->where('code', $order_code)->update($this->to);
  }


  public function is_exists_detail($order_code)
  {
    $count = $this->db->where('order_code', $order_code)->count_all_results($this->td);

    return $count > 0 ? TRUE : FALSE;
  }


  public function is_exists_carplate($plate_no, $province)
  {
    $count = $this->db
    ->where('plate_no', $plate_no)
    ->where('province', $province)
    ->count_all_results('dispatch_cars');

    return $count > 0 ? TRUE : FALSE;
  }


  public function is_exists_driver($name)
  {
    $count = $this->db
    ->where('name', $name)
    ->count_all_results('dispatch_driver');

    return $count > 0 ? TRUE : FALSE;
  }


  public function count_orders_by_channels($channels_code)
  {
    $state_in = $channels_code === 'SHOPEE' ? ['8', '7'] : ['8'];

    $this->db->where('is_wms', 0)->where_in('state', $state_in)->where('dispatch_id IS NULL', NULL, FALSE);

    if( ! empty($channels_code))
    {
      $this->db->where('channels_code', $channels_code);
    }
    else
    {
      $this->db->where_not_in('channels_code', ['0009', 'LAZADA', 'SHOPEE']);
    }

    return $this->db->count_all_results($this->to);
  }


  public function get_peding_order_by_channels($channels_code)
  {
    $state_in = $channels_code === 'SHOPEE' ? ['8', '7'] : ['8'];

    $this->db
    ->select('code, reference, customer_code, customer_name, channels_code')
    ->where('is_wms', 0)
    ->where_in('state', $state_in)
    ->where('dispatch_id IS NULL', NULL, FALSE);

    if( ! empty($channels_code))
    {
      $this->db->where('channels_code', $channels_code);
    }
    else
    {
      $this->db->where_not_in('channels_code', ['0009', 'LAZADA', 'SHOPEE']);
    }

    $rs = $this->db->get($this->to);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function count_order_box($order_code)
  {
    return $this->db->where('order_code', $order_code)->count_all_results('qc_box');
  }


  public function get_max_code($pre)
  {
    $rs = $this->db
    ->select_max('code')
    ->like('code', $pre, 'after')
    ->order_by('code', 'DESC')
    ->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->code;
    }

    return NULL;
  }
}
 ?>

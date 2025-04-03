<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pick_list_model extends CI_Model
{
  private $tb = "pick_list";
  private $td = "pick_details";
  private $tr = "pick_rows";
  private $to = "pick_orders";
  private $ts = "pick_transection";

  public function __construct()
  {
    parent::__construct();
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

  public function get_by_id($id)
  {
    $rs = $this->db->where('id', $id)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_details($code)
  {
    $rs = $this->db->where('pick_code', $code)->get($this->td);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_pick_orders($code)
  {
    $rs = $this->db
    ->where('pick_code', $code)
    ->order_by('order_code', 'ASC')
    ->get($this->to);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_pick_rows($code)
  {
    $rs = $this->db
    ->where('pick_code', $code)
    ->order_by('product_code', 'ASC')
    ->get($this->tr);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_pick_row($code, $product_code)
  {
    $rs = $this->db
    ->where('pick_code', $code)
    ->where('product_code', $product_code)
    ->get($this->tr);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_pick_transections($code)
  {
    $rs = $this->db
    ->where('pick_code', $code)
    ->order_by('product_code', 'ASC')
    ->get($this->ts);

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


  public function get_row($id)
  {
    $rs = $this->db->where('id', $id)->get($this->tr);

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_transection($id)
  {
    $rs = $this->db->where('id', $id)->get($this->ts);

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_exists_transection($pick_id, $product_code, $zone_code)
  {
    $rs = $this->db
    ->where('pick_id', $pick_id)
    ->where('product_code', $product_code)
    ->where('zone_code', $zone_code)
    ->get($this->ts);

    if($rs->num_rows() > 0)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_incomplete_rows($code)
  {
    $rs = $this->db
    ->where('pick_code', $code)
    ->where('valid', 0)
    ->order_by('product_code', 'ASC')
    ->get($this->tr);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_complete_rows($code)
  {
    $rs = $this->db
    ->where('pick_code', $code)
    ->where('valid', 1)
    ->order_by('product_code', 'ASC')
    ->get($this->tr);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_picked_zone($zone_code, $product_code)
  {
    $rs = $this->db
    ->select_sum('qty')
    ->where('is_complete', 0)
    ->where('zone_code', $zone_code)
    ->where('product_code', $product_code)
    ->get($this->ts);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->qty;
    }

    return 0;
  }


  public function get_sum_release_qty($code)
  {
    $rs = $this->db->select_sum('release_qty')->where('pick_code', $code)->get($this->tr);

    if($rs->num_rows() > 0)
    {
      return $rs->row()->release_qty;
    }

    return 0;
  }


  public function get_total_process_qty($code)
  {
    $rs = $this->db
    ->select_sum('release_qty')
    ->select_sum('pick_qty')
    ->where('pick_code', $code)
    ->get($this->tr);

    if($rs->num_rows() > 0)
    {
      return $rs->row();
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


  public function add_pick_order(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert($this->to, $ds);
    }

    return FALSE;
  }


  public function add_row(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert($this->tr, $ds);
    }

    return FALSE;
  }


  public function add_transection(array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->insert($this->ts, $ds);
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


  public function update_detail($id, array $ds = array())
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
      return $this->db->where('pick_code', $code)->update($this->td, $ds);
    }

    return FALSE;
  }


  public function update_transection($id, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('id', $id)->update($this->ts, $ds);
    }

    return FALSE;
  }


  public function update_transections($code, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('pick_code', $code)->update($this->ts, $ds);
    }

    return FALSE;
  }


  public function update_row($id, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('id', $id)->update($this->tr, $ds);
    }

    return FALSE;
  }


  public function update_rows($code, array $ds = array())
  {
    if( ! empty($ds))
    {
      return $this->db->where('pick_code', $code)->update($this->tr, $ds);
    }

    return FALSE;
  }


  public function delete_transection($id)
  {
    return $this->db->where('id', $id)->delete($this->ts);
  }


  public function delete_row($id)
  {
    return $this->db->where('id', $id)->delete($this->tr);
  }


  public function delete_order($code, $order_code)
  {
    return $this->db->where('pick_code', $code)->where('order_code', $order_code)->delete($this->to);
  }


  public function delete_detail($id)
  {
    return $this->db->where('id', $id)->delete($this->td);
  }


  public function delete_detail_by_order($code, $order_code)
  {
    return $this->db->where('pick_code', $code)->where('order_code', $order_code)->delete($this->td);
  }


  public function delete_rows($code)
  {
    return $this->db->where('pick_code', $code)->delete($this->tr);
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0, $is_mobile = FALSE)
  {
    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if(isset($ds['warehouse']) && $ds['warehouse'] != 'all')
    {
      $this->db->where('warehouse_code', $ds['warehouse']);
    }

    if(isset($ds['zone']) && $ds['zone'] != 'all')
    {
      $this->db->where('zone_code', $ds['zone']);
    }

    if(isset($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('user', $ds['user']);
    }

    if(isset($ds['channels']) && $ds['channels'] != 'all')
    {
      $this->db->where('channels_code', $ds['channels']);
    }

    if($is_mobile)
    {
      $this->db->where_in('status', ['R', 'Y']);

      if(isset($ds['status']) && ($ds['status'] == 'Y' OR $ds['status'] == 'R'))
      {
        $this->db->where('status', $ds['status']);
      }
    }
    else
    {
      if(isset($ds['status']) && $ds['status'] != 'all')
      {
        $this->db->where('status', $ds['status']);
      }
    }

    if(isset($ds['is_exported']) && $ds['is_exported'] != 'all')
    {
      $this->db->where('is_exported', $ds['is_exported']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }

    $rs = $this->db->order_by('id', 'DESC')->limit($perpage, $offset)->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function count_rows(array $ds = array(), $is_mobile = FALSE)
  {
    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if(isset($ds['warehouse']) && $ds['warehouse'] != 'all')
    {
      $this->db->where('warehouse_code', $ds['warehouse']);
    }

    if(isset($ds['zone']) && $ds['zone'] != 'all')
    {
      $this->db->where('zone_code', $ds['zone']);
    }

    if(isset($ds['user']) && $ds['user'] != 'all')
    {
      $this->db->where('user', $ds['user']);
    }

    if(isset($ds['channels']) && $ds['channels'] != 'all')
    {
      $this->db->where('channels_code', $ds['channels']);
    }

    if($is_mobile)
    {
      $this->db->where_in('status', ['R', 'Y']);

      if(isset($ds['status']) && ($ds['status'] == 'Y' OR $ds['status'] == 'R'))
      {
        $this->db->where('status', $ds['status']);
      }
    }
    else
    {
      if(isset($ds['status']) && $ds['status'] != 'all')
      {
        $this->db->where('status', $ds['status']);
      }
    }

    if(isset($ds['is_exported']) && $ds['is_exported'] != 'all')
    {
      $this->db->where('is_exported', $ds['is_exported']);
    }

    if( ! empty($ds['from_date']))
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
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
    ->select('o.id, o.code, o.customer_code, o.customer_name, o.channels_code, o.pick_list_id, o.date_add, c.name AS channels_name')
    ->from('orders AS o')
    ->join('channels AS c', 'o.channels_code = c.code', 'left')
    ->where('o.state', 3)
    ->where('o.is_cancled', 0)
    ->where('o.warehouse_code', $ds['warehouse_code']);

    if( ! empty($ds['from_date']))
    {
      $this->db->where('o.date_add >=', from_date($ds['from_date']));
    }

    if( ! empty($ds['to_date']))
    {
      $this->db->where('o.date_add <=', to_date($ds['to_date']));
    }

    if( isset($ds['channels']) && $ds['channels'] != 'all')
    {
      $this->db->where('o.channels_code', $ds['channels']);
    }

    if(isset($ds['is_pick_list']) && $ds['is_pick_list'] != 'all')
    {
      if($ds['is_pick_list'] == 1)
      {
        $this->db->where('o.pick_list_id IS NOT NULL', NULL, FALSE);
      }
      else
      {
        $this->db->where('o.pick_list_id IS NULL', NULL, FALSE);
      }
    }

    if( ! empty($ds['code']))
    {
      $this->db->like('o.code', $ds['code']);
    }

    if( ! empty($ds['customer']))
    {
      $this->db
      ->group_start()
      ->like('o.customer_code', $ds['customer'])
      ->or_like('o.customer_name', $ds['customer'])
      ->group_end();
    }

    $rs = $this->db->order_by('o.id', 'ASC')->limit(100)->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function is_order_in_correct_state($order_code)
  {
    $count = $this->db->where('code', $order_code)->where('state', '3')->where('is_cancled', 0)->count_all_results('orders');

    return $count === 1 ? TRUE : FALSE;
  }


  public function get_order_details($order_code)
  {
    $rs = $this->db
    ->select('order_code, product_code, product_name')
    ->select_sum('qty')
    ->where('order_code', $order_code)
    ->group_by('product_code')
    ->get('order_details');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function is_exists_order_detail($pick_id, $order_code, $product_code)
  {
    $count = $this->db
    ->where('pick_id', $pick_id)
    ->where('order_code', $order_code)
    ->where('product_code', $product_code)
    ->where('line_status !=', 'D')
    ->count_all_results($this->td);

    return $count > 0 ? TRUE : FALSE;
  }


  public function is_exists_transectons($pick_code)
  {
    $count = $this->db->where('pick_code', $pick_code)->count_all_results($this->ts);

    return $count > 0 ? TRUE : FALSE;
  }


  public function get_status_by_id($id)
  {
    $rs = $this->db->select('status')->where('id', $id)->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->status;
    }

    return NULL;
  }


  public function remove_order_pick_list_id($pick_list_id)
  {
    return $this->db->set('pick_list_id', NULL)->where('pick_list_id', $pick_list_id)->update('orders');
  }


  public function get_max_code($pre)
  {
    $rs = $this->db->select_max('code')->like('code', $pre, 'after')->get($this->tb);

    if($rs->num_rows() === 1)
    {
      return $rs->row()->code;
    }

    return NULL;
  }


  public function get_non_inv_code($limit = 100)
  {
    $rs = $this->db
    ->select('code')
    ->where('status', 'C')
    ->where('inv_code IS NULL', NULL, FALSE)
    ->limit($limit)
    ->get($this->tb);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

  public function get_sap_doc_num($code)
  {
    $rs = $this->ms
    ->select('DocNum')
    ->where('U_ECOMNO', $code)
    ->where('CANCELED', 'N')
    ->get('OWTR');

    if($rs->num_rows() > 0)
    {
      return $rs->row()->DocNum;
    }

    return NULL;
  }


  public function update_inv($code, $doc_num)
  {
    return $this->db->set('inv_code', $doc_num)->where('code', $code)->update($this->tb);
  }


  public function set_complete($code)
  {
    return $this->db->set('is_complete', 1)->where('pick_code', $code)->update($this->ts);
  }


  public function get_sap_move_doc($code)
  {
    $rs = $this->ms
    ->select('DocEntry, DocStatus')
    ->where('U_ECOMNO', $code)
    ->where('CANCELED', 'N')
    ->get('OWTR');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function is_middle_exists($code)
  {
    $rs = $this->mc->select('DocStatus')->where('U_ECOMNO', $code)->get('OWTR');
    if($rs->num_rows() === 1)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function get_middle_move_doc($code)
  {
    $rs = $this->mc
    ->select('DocEntry')
    ->where('U_ECOMNO', $code)
    ->group_start()
    ->where('F_Sap', 'N')
    ->or_where('F_Sap IS NULL', NULL, FALSE)
    ->group_end()
    ->get('OWTR');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function add_sap_move_doc(array $ds = array())
  {
    if(!empty($ds))
    {
      $rs = $this->mc->insert('OWTR', $ds);
      if($rs)
      {
        return $this->mc->insert_id();
      }
    }

    return FALSE;
  }


  public function update_sap_move_doc($code, $ds = array())
  {
    if(! empty($code) && ! empty($ds))
    {
      return $this->mc->where('U_ECOMNO', $code)->update('OWTR', $ds);
    }

    return FALSE;
  }


  public function add_sap_move_detail(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->mc->insert('WTR1', $ds);
    }

    return FALSE;
  }


  public function drop_sap_exists_details($code)
  {
    return $this->mc->where('U_ECOMNO', $code)->delete('WTR1');
  }


  public function drop_middle_exits_data($docEntry)
  {
    $this->mc->trans_start();
    $this->mc->where('DocEntry', $docEntry)->delete('WTR1');
    $this->mc->where('DocEntry', $docEntry)->delete('OWTR');
    $this->mc->trans_complete();

    return $this->mc->trans_status();
  }
} //--- end class


 ?>

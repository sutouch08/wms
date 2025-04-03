<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Prepare_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get_details($order_code)
  {
    $rs = $this->db->where('order_code', $order_code)->get('prepare');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_warehouse_code($zone_code)
  {
    $rs = $this->db->select('warehouse_code')->where('code', $zone_code)->get('zone');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->warehouse_code;
    }

    return  NULL;
  }


  public function update_buffer($order_code, $product_code, $zone_code, $qty, $detail_id = NULL)
  {
    if( ! $this->is_exists_buffer($order_code, $product_code, $zone_code, $detail_id))
    {
      $arr = array(
        'order_code' => $order_code,
        'product_code' => $product_code,
        'warehouse_code' => $this->get_warehouse_code($zone_code),
        'zone_code' => $zone_code,
        'qty' => $qty,
        'order_detail_id' => $detail_id,
        'user' => get_cookie('uname')
      );

      return $this->db->insert('buffer', $arr);
    }
    else
    {
      $this->db
      ->set("qty", "qty + {$qty}", FALSE)
      ->where('order_code', $order_code)
      ->where('product_code', $product_code)
      ->where('zone_code', $zone_code)
      ->group_start()
      ->where('order_detail_id', $detail_id)
      ->or_where('order_detail_id IS NULL', NULL, FALSE)
      ->group_end();

      return $this->db->update('buffer');
    }

    return FALSE;
  }


  public function is_exists_buffer($order_code, $item_code, $zone_code, $detail_id = NULL)
  {
    $this->db
    ->where('order_code', $order_code)
    ->where('product_code', $item_code)
    ->where('zone_code', $zone_code)
    ->group_start()
    ->where('order_detail_id', $detail_id)
    ->or_where('order_detail_id IS NULL', NULL, FALSE)
    ->group_end();

    $count = $this->db->count_all_results('buffer');

    if($count > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


	public function add(array $ds = array())
	{
		return $this->db->insert('prepare', $ds);
	}


	public function drop_prepare($order_code)
	{
		return $this->db->where('order_code', $order_code)->delete('prepare');
	}


  public function update_prepare($order_code, $product_code, $zone_code, $qty, $detail_id = NULL)
  {
    if( ! $this->is_exists_prepare($order_code, $product_code, $zone_code, $detail_id))
    {
      $arr = array(
        'order_code' => $order_code,
        'product_code' => $product_code,
        'zone_code' => $zone_code,
        'qty' => $qty,
        'order_detail_id' => $detail_id,
        'user' => $this->_user->uname
      );

      return $this->db->insert('prepare', $arr);
    }
    else
    {
      $this->db
      ->set("qty", "qty + {$qty}", FALSE)
      ->where('order_code', $order_code)
      ->where('product_code', $product_code)
      ->where('zone_code', $zone_code)
      ->group_start()
      ->where('order_detail_id', $detail_id)
      ->or_where('order_detail_id IS NULL', NULL, FALSE)
      ->group_end();

      return $this->db->update('prepare');
    }

    return FALSE;
  }


  public function is_exists_prepare($order_code, $item_code, $zone_code, $detail_id = NULL)
  {
    $this->db
    ->where('order_code', $order_code)
    ->where('product_code', $item_code)
    ->where('zone_code', $zone_code)->group_start()
    ->where('order_detail_id', $detail_id)
    ->or_where('order_detail_id IS NULL', NULL, FALSE)
    ->group_end();

    if($this->db->count_all_results('prepare') > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function is_exists_print_logs($code)
  {
    return $this->db->where('order_code', $code)->count_all_results('print_pick_list_logs') > 0 ? TRUE : FALSE;
  }


  public function get_prepared($order_code, $product_code, $detail_id = NULL)
  {
    $this->db
    ->select_sum('qty')
    ->where('order_code', $order_code)
    ->where('product_code', $product_code)
    ->group_start()
    ->where('order_detail_id', $detail_id)
    ->or_where('order_detail_id IS NULL', NULL, FALSE)
    ->group_end();

    $rs = $this->db->get('buffer');

    if($rs->num_rows() > 0)
    {
      return $rs->row()->qty;
    }

    return 0;
  }


  public function remove_prepare($order_code, $item_code, $order_detail_id = NULL)
  {
    $this->db
    ->where('order_code', $order_code)
    ->where('product_code', $item_code)
    ->group_start()
    ->where('order_detail_id', $order_detail_id)
    ->or_where('order_detail_id IS NULL', NULL, FALSE)
    ->group_end();

    return $this->db->delete('prepare');
  }

  public function get_total_prepared($order_code)
  {
    $rs = $this->db
    ->select_sum('qty')
    ->where('order_code', $order_code)
    ->get('buffer');

    return is_null($rs->row()->qty) ? 0 : $rs->row()->qty;
  }


  //---- แสดงสินค้าว่าจัดมาจากโซนไหนบ้าง
  public function get_prepared_from_zone($order_code, $item_code, $detail_id = NULL)
  {
    $this->db
    ->select('buffer.*, zone.name')
    ->from('buffer')
    ->join('zone', 'zone.code = buffer.zone_code')
    ->where('order_code', $order_code)
    ->where('product_code', $item_code)
    ->group_start()
    ->where('order_detail_id', $detail_id)
    ->or_where('order_detail_id IS NULL', NULL, FALSE)
    ->group_end();

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  //--- แสดงยอดรวมสินค้าที่ถูกจัดไปแล้วจากโซนนี้
  public function get_prepared_zone($zone_code, $item_code)
  {
    $rs = $this->db->select_sum('qty')
    ->where('zone_code', $zone_code)
    ->where('product_code', $item_code)
    ->get('buffer');

    return $rs->row()->qty;
  }





  public function get_buffer_zone($item_code, $zone_code)
  {
    $rs = $this->db->select_sum('qty')
    ->where('product_code', $item_code)
    ->where('zone_code', $zone_code)
    ->get('buffer');

    return $rs->row()->qty;
  }


  public function count_rows(array $ds = array(), $state = 3, $full_mode = TRUE)
  {
    $this->db
    ->select('o.state')
    ->from('orders AS o')
    ->join('channels AS ch', 'ch.code = o.channels_code','left');
    if( ! empty($ds['item_code']))
    {
      $this->db->join('order_details AS od', 'o.code = od.order_code','left');
    }

    if($state == 4)
    {
      $this->db->join('user AS u', 'u.uname = o.update_user', 'left');
    }

    if($state == 3)
    {
      $this->db->join('user AS u', 'u.uname = o.user', 'left');
    }

    $this->db
    ->where('o.state', $state)
    ->where('o.status', 1);

		if($full_mode === TRUE)
		{
			$this->db->where('o.is_wms', 0);
		}

    if(!empty($ds['code']))
    {
      $this->db
			->group_start()
			->like('o.code', $ds['code'])
			->or_like('o.reference', $ds['code'])
			->group_end();
    }

    if(!empty($ds['item_code']))
    {
      $this->db->like('od.product_code', $ds['item_code']);
    }

    if( ! empty($ds['customer']))
    {
      $this->db
      ->group_start()
      ->like('o.customer_code', $ds['customer'])
      ->or_like('o.customer_name', $ds['customer'])
      ->or_like('o.customer_ref', $ds['customer'])
      ->group_end();
    }


    if($ds['warehouse'] !== 'all' && !empty($ds['warehouse']))
    {
      $this->db->where('o.warehouse_code', $ds['warehouse']);
    }


    //---- user name / display name
    if($state == 3 && !empty($ds['user']))
    {
      $this->db->group_start();
      $this->db->like('u.uname', $ds['user']);
      $this->db->or_like('u.name', $ds['user']);
      $this->db->group_end();
    }

    if($state == 4 && !empty($ds['display_name']))
    {
      $this->db->group_start();
      $this->db->like('u.uname', $ds['display_name']);
      $this->db->or_like('u.name', $ds['display_name']);
      $this->db->group_end();
    }


    if( ! empty($ds['channels']) && $ds['channels'] != 'all')
    {
      $this->db->where('o.channels_code', $ds['channels']);
    }

    if($ds['is_online'] != '2')
    {
      if($ds['is_online'] == 1)
      {
        $this->db->where('ch.is_online', $ds['is_online']);
      }
      else
      {
        $this->db->group_start()
        ->where('ch.is_online !=', 1)
        ->or_where('ch.is_online IS NULL', NULL, FALSE)
        ->group_end();
      }
    }


    if(!empty($ds['payment']) && $ds['payment'] !== 'all')
    {
      $this->db->where('o.payment_code', $ds['payment']);
    }


    if($ds['role'] != 'all')
    {
      $this->db->where('o.role', $ds['role']);
    }



    if( ! empty($ds['from_date']) && ! empty($ds['to_date']))
    {
      if(!empty($ds['stated']))
      {
        $from_date = from_date($ds['from_date']);
        $to_date = to_date($ds['to_date']);
        $array = $this->getOrderStateChangeIn($ds['stated'], $from_date, $to_date, $ds['startTime'], $ds['endTime'] );
        $this->db->where_in('o.code', $array);
      }
      else
      {
        $this->db->where('o.date_add >=', from_date($ds['from_date']));
        $this->db->where('o.date_add <=', to_date($ds['to_date']));
      }
    }

    $this->db->group_by('o.code');

    if( ! empty($ds['order_by']))
    {
      $order_by = "o.{$ds['order_by']}";
      $this->db->order_by($order_by, $ds['sort_by']);
    }
    else
    {
      $this->db->order_by('o.date_add', 'DESC');
    }

    return $this->db->count_all_results();
  }


  public function get_sum_order_qty($code)
  {
    $rs =  $this->db->select_sum('qty')->where('order_code', $code)->get('order_details');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->qty;
    }

    return 0;
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0, $state = 3, $full_mode = TRUE)
  {
    $this->db
		->select('o.id, o.code, o.role, o.reference, o.customer_code, o.customer_name, o.customer_ref, o.date_add, o.channels_code, o.is_cancled')
    ->select('o.warehouse_code, o.empName, o.user')
    ->select('ch.name AS channels_name')
    ->select('u.name AS display_name')
    ->from('orders AS o')
    ->join('channels AS ch', 'ch.code = o.channels_code','left');

    if( ! empty($ds['item_code']))
    {
      $this->db->join('order_details AS od', 'o.code = od.order_code','left');
    }

		if($full_mode === TRUE)
		{
			$this->db->where('o.is_wms', 0);
		}

    if($state == 4)
    {
      $this->db->join('user AS u', 'u.uname = o.update_user', 'left');
    }

    if($state == 3)
    {
      $this->db->join('user AS u', 'u.uname = o.user', 'left');
    }

    $this->db
    ->where('o.state', $state)
    ->where('o.status', 1);

    if(!empty($ds['code']))
    {
      $this->db
			->group_start()
			->like('o.code', $ds['code'])
			->or_like('o.reference', $ds['code'])
			->group_end();
    }

    if(!empty($ds['item_code']))
    {
      $this->db->like('od.product_code', $ds['item_code']);
    }

    if( ! empty($ds['customer']))
    {
      $this->db
      ->group_start()
      ->like('o.customer_code', $ds['customer'])
      ->or_like('o.customer_name', $ds['customer'])
      ->or_like('o.customer_ref', $ds['customer'])
      ->group_end();
    }


    if($ds['warehouse'] !== 'all' && !empty($ds['warehouse']))
    {
      $this->db->where('o.warehouse_code', $ds['warehouse']);
    }


    //---- user name / display name
    if($state == 3 && !empty($ds['user']))
    {
      $this->db->group_start();
      $this->db->like('u.uname', $ds['user']);
      $this->db->or_like('u.name', $ds['user']);
      $this->db->group_end();
    }

    if($state == 4 && !empty($ds['display_name']))
    {
      $this->db->group_start();
      $this->db->like('u.uname', $ds['display_name']);
      $this->db->or_like('u.name', $ds['display_name']);
      $this->db->group_end();
    }

    if( ! empty($ds['channels']) && $ds['channels'] != 'all')
    {
      $this->db->where('o.channels_code', $ds['channels']);
    }

    if($ds['is_online'] != '2')
    {
      if($ds['is_online'] == 1)
      {
        $this->db->where('ch.is_online', $ds['is_online']);
      }
      else
      {
        $this->db->group_start()
        ->where('ch.is_online !=', 1)
        ->or_where('ch.is_online IS NULL', NULL, FALSE)
        ->group_end();
      }
    }


    if(!empty($ds['payment']) && $ds['payment'] !== 'all')
    {
      $this->db->where('o.payment_code', $ds['payment']);
    }


    if($ds['role'] != 'all')
    {
      $this->db->where('o.role', $ds['role']);
    }

    if( ! empty($ds['from_date']) && ! empty($ds['to_date']))
    {
      if(!empty($ds['stated']))
      {
        $from_date = from_date($ds['from_date']);
        $to_date = to_date($ds['to_date']);
        $array = $this->getOrderStateChangeIn($ds['stated'], $from_date, $to_date, $ds['startTime'], $ds['endTime'] );
        $this->db->where_in('o.code', $array);
      }
      else
      {
        $this->db->where('o.date_add >=', from_date($ds['from_date']));
        $this->db->where('o.date_add <=', to_date($ds['to_date']));
      }
    }

    $this->db->group_by('o.code');

    if( ! empty($ds['order_by']))
    {
      $order_by = "o.{$ds['order_by']}";
      $this->db->order_by($order_by, $ds['sort_by']);
    }
    else
    {
      $this->db->order_by('o.id', 'ASC');
    }

    $rs = $this->db->limit($perpage, $offset)->get();
    //echo $this->db->get_compiled_select();
    return $rs->result();
  }


  private function getOrderStateChangeIn($state, $fromDate, $toDate, $startTime, $endTime)
  {
    $qr  = "SELECT order_code FROM order_state_change ";
    $qr .= "WHERE state = {$state} ";
    $qr .= "AND date_upd >= '{$fromDate}' ";
    $qr .= "AND date_upd <= '{$toDate}' ";
    $qr .= "AND time_upd >= '{$startTime}' ";
    $qr .= "AND time_upd <= '{$endTime}' ";
    $qr .= "LIMIT 1000";
    $rs = $this->db->query($qr);

  	$sc = array();

  	if($rs->num_rows() > 0)
  	{
  		foreach($rs->result() as $row)
  		{
  			$sc[] = $row->order_code;
  		}

      return $sc;
  	}

  	return 'xx';
  }


  public function clear_prepare($code)
  {
    return $this->db->where('order_code', $code)->delete('prepare');
  }



} //--- end class


 ?>

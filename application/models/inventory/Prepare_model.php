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
    $rs = $this->ms->select('WhsCode')->where('BinCode', $zone_code)->get('OBIN');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->WhsCode;
    }

    return  NULL;
  }

  public function update_buffer($order_code, $product_code, $zone_code, $qty)
  {
    if(!$this->is_exists_buffer($order_code, $product_code, $zone_code))
    {
      $arr = array(
        'order_code' => $order_code,
        'product_code' => $product_code,
        'warehouse_code' => $this->get_warehouse_code($zone_code),
        'zone_code' => $zone_code,
        'qty' => $qty,
        'user' => get_cookie('uname')
      );

      return $this->db->insert('buffer', $arr);
    }
    else
    {

      $qr  = "UPDATE buffer SET qty = qty + {$qty} ";
      $qr .= "WHERE order_code = '{$order_code}' ";
      $qr .= "AND product_code = '{$product_code}' ";
      $qr .= "AND zone_code = '{$zone_code}' ";

      return $this->db->query($qr);
    }

    return FALSE;
  }


  public function is_exists_buffer($order_code, $item_code, $zone_code)
  {
    $rs = $this->db->where('order_code', $order_code)
    ->where('product_code', $item_code)
    ->where('zone_code', $zone_code)
    ->get('buffer');

    if($rs->num_rows() > 0)
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


  public function update_prepare($order_code, $product_code, $zone_code, $qty)
  {
    if(!$this->is_exists_prepare($order_code, $product_code, $zone_code))
    {
      $arr = array(
        'order_code' => $order_code,
        'product_code' => $product_code,
        'zone_code' => $zone_code,
        'qty' => $qty,
        'user' => get_cookie('uname')
      );

      return $this->db->insert('prepare', $arr);
    }
    else
    {
      $qr  = "UPDATE prepare SET qty = qty + {$qty} ";
      $qr .= "WHERE order_code = '{$order_code}' ";
      $qr .= "AND product_code = '{$product_code}' ";
      $qr .= "AND zone_code = '{$zone_code}' ";

      return $this->db->query($qr);
    }

    return FALSE;
  }



  public function is_exists_prepare($order_code, $item_code, $zone_code)
  {
    $rs = $this->db->where('order_code', $order_code)
    ->where('product_code', $item_code)
    ->where('zone_code', $zone_code)
    ->get('prepare');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }





  public function get_prepared($order_code, $item_code)
  {
    $rs = $this->db->select_sum('qty')
    ->where('order_code', $order_code)
    ->where('product_code', $item_code)
    ->get('buffer');

    return is_null($rs->row()->qty) ? 0 : $rs->row()->qty;
  }


  public function get_total_prepared($order_code)
  {
    $rs = $this->db->select_sum('qty')
    ->where('order_code', $order_code)
    ->get('buffer');

    return is_null($rs->row()->qty) ? 0 : $rs->row()->qty;
  }


  //---- แสดงสินค้าว่าจัดมาจากโซนไหนบ้าง
  public function get_prepared_from_zone($order_code, $item_code)
  {
    $rs = $this->db->select('buffer.*, zone.name')
    ->from('buffer')
    ->join('zone', 'zone.code = buffer.zone_code')
    ->where('order_code', $order_code)
    ->where('product_code', $item_code)
    ->get();

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


  public function count_rows(array $ds = array(), $state = 3)
  {
		$full_mode = getConfig('WMS_FULL_MODE') == 1 ? TRUE : FALSE;

    $this->db->select('state')
    ->from('orders')
    ->join('channels', 'channels.code = orders.channels_code','left')
    ->join('customers', 'customers.code = orders.customer_code', 'left')
    ->join('order_details', 'orders.code = order_details.order_code', 'left')
    ->join('products', 'order_details.product_code = products.code', 'left')
    ->where('orders.state', $state)
    ->where('orders.status', 1);

		if($full_mode === TRUE)
		{
			$this->db->where('orders.is_wms', 0);
		}

		if(!empty($ds['code']))
    {
      $this->db
			->group_start()
			->like('orders.code', $ds['code'])
			->or_like('orders.reference', $ds['code'])
			->group_end();
    }

    if(!empty($ds['item_code']))
    {
      $this->db->group_start();
      $this->db->like('products.code', $ds['item_code']);
      $this->db->or_like('products.old_code', $ds['item_code']);
      $this->db->group_end();
    }

    if(!empty($ds['customer']))
    {
      $this->db->group_start();
      $this->db->like('customers.name', $ds['customer']);
      $this->db->or_like('orders.customer_ref', $ds['customer']);
      $this->db->group_end();
    }


    if($ds['warehouse'] !== 'all' && !empty($ds['warehouse']))
    {
      $this->db->where('warehouse_code', $ds['warehouse']);
    }

    //---- user name / display name
    if(!empty($ds['user']))
    {
      $users = user_in($ds['user']);
      $this->db->group_start();
      $this->db->where_in('user', $users);
      $this->db->or_like('orders.empName', $ds['user']);
      $this->db->group_end();
    }

    if(!empty($ds['channels']))
    {
      $this->db->where('orders.channels_code', $ds['channels']);
    }

    if(!empty($ds['payment']) && $ds['payment'] !== 'all')
    {
      $this->db->where('orders.payment_code', $ds['payment']);
    }

    if($ds['is_online'] != '2')
    {
      if($ds['is_online'] == 1)
      {
        $this->db->where('channels.is_online', $ds['is_online']);
      }
      else
      {
        $this->db->group_start()
        ->where('channels.is_online !=', 1)
        ->or_where('channels.is_online IS NULL', NULL, FALSE)
        ->group_end();
      }
    }


    if($ds['role'] != 'all')
    {
      $this->db->where('orders.role', $ds['role']);
    }

    if( ! empty($ds['from_date']) && ! empty($ds['to_date']))
    {
      if(!empty($ds['stated']))
      {
        $from_date = from_date($ds['from_date']);
        $to_date = to_date($ds['to_date']);
        $array = $this->getOrderStateChangeIn($ds['stated'], $from_date, $to_date, $ds['startTime'], $ds['endTime'] );
        $this->db->where_in('orders.code', $array);
      }
      else
      {
        $this->db->where('orders.date_add >=', from_date($ds['from_date']));
        $this->db->where('orders.date_add <=', to_date($ds['to_date']));
      }
    }

    $this->db->group_by('orders.code');

    return $this->db->count_all_results();
  }



  public function get_data(array $ds = array(), $perpage = '', $offset = '', $state = 3)
  {
		$full_mode = getConfig('WMS_FULL_MODE') == 1 ? TRUE : FALSE;

    $this->db
		->select('orders.*, channels.name AS channels_name')
    ->select('customers.name AS customer_name, user.name AS display_name')
    ->select_sum('order_details.qty', 'qty')
    ->from('orders')
    ->join('channels', 'channels.code = orders.channels_code','left')
    ->join('customers', 'customers.code = orders.customer_code', 'left')
    ->join('order_details', 'orders.code = order_details.order_code','left')
    ->join('products', 'order_details.product_code = products.code', 'left');

		if($full_mode === TRUE)
		{
			$this->db->where('orders.is_wms', 0);
		}

    if($state == 4)
    {
      $this->db->join('user', 'user.uname = orders.update_user', 'left');
    }

    if($state == 3)
    {
      $this->db->join('user', 'user.uname = orders.user', 'left');
    }

    $this->db
    ->where('orders.state', $state)
    ->where('orders.status', 1);

    if(!empty($ds['code']))
    {
      $this->db
			->group_start()
			->like('orders.code', $ds['code'])
			->or_like('orders.reference', $ds['code'])
			->group_end();
    }

    if(!empty($ds['item_code']))
    {
      $this->db->group_start();
      $this->db->like('products.code', $ds['item_code']);
      $this->db->or_like('products.old_code', $ds['item_code']);
      $this->db->group_end();
    }

    if(!empty($ds['customer']))
    {
      $this->db->group_start();
      $this->db->like('customers.name', $ds['customer']);
      $this->db->or_like('orders.customer_ref', $ds['customer']);
      $this->db->group_end();
    }


    if($ds['warehouse'] !== 'all' && !empty($ds['warehouse']))
    {
      $this->db->where('warehouse_code', $ds['warehouse']);
    }


    //---- user name / display name
    if($state == 3 && !empty($ds['user']))
    {
      $this->db->group_start();
      $this->db->like('user.uname', $ds['user']);
      $this->db->or_like('user.name', $ds['user']);
      $this->db->group_end();
    }

    if($state == 4 && !empty($ds['display_name']))
    {
      $this->db->group_start();
      $this->db->like('user.uname', $ds['display_name']);
      $this->db->or_like('user.name', $ds['display_name']);
      $this->db->group_end();
    }


    if(!empty($ds['channels']))
    {
      $this->db->where('orders.channels_code', $ds['channels']);
    }

    if($ds['is_online'] != '2')
    {
      if($ds['is_online'] == 1)
      {
        $this->db->where('channels.is_online', $ds['is_online']);
      }
      else
      {
        $this->db->group_start()
        ->where('channels.is_online !=', 1)
        ->or_where('channels.is_online IS NULL', NULL, FALSE)
        ->group_end();
      }
    }


    if(!empty($ds['payment']) && $ds['payment'] !== 'all')
    {
      $this->db->where('orders.payment_code', $ds['payment']);
    }


    if($ds['role'] != 'all')
    {
      $this->db->where('orders.role', $ds['role']);
    }



    if( ! empty($ds['from_date']) && ! empty($ds['to_date']))
    {
      if(!empty($ds['stated']))
      {
        $from_date = from_date($ds['from_date']);
        $to_date = to_date($ds['to_date']);
        $array = $this->getOrderStateChangeIn($ds['stated'], $from_date, $to_date, $ds['startTime'], $ds['endTime'] );
        $this->db->where_in('orders.code', $array);
      }
      else
      {
        $this->db->where('orders.date_add >=', from_date($ds['from_date']));
        $this->db->where('orders.date_add <=', to_date($ds['to_date']));
      }
    }

    $this->db->group_by('orders.code');

    if(!empty($ds['order_by']))
    {
      $order_by = "orders.{$ds['order_by']}";
      $this->db->order_by($order_by, $ds['sort_by']);
    }
    else
    {
      $this->db->order_by('orders.date_add', 'DESC');
    }


    if($perpage != '')
    {
      $offset = $offset === NULL ? 0 : $offset;
      $this->db->limit($perpage, $offset);
    }

    $rs = $this->db->get();
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

<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Orders_model extends CI_Model
{

  public function __construct()
  {
    parent::__construct();
  }



  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('orders', $ds);
    }

    return FALSE;
  }



  public function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('code', $code)->update('orders', $ds);
    }

    return FALSE;
  }


  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get('orders');
    if($rs->num_rows() == 1)
    {
      return $rs->row();
    }

    return FALSE;
  }



  public function add_detail(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('order_details', $ds);
    }

    return FALSE;
  }




  public function update_detail($id, array $ds = array())
  {
    return $this->db->where('id', $id)->update('order_details', $ds);
  }




  public function remove_detail($id)
  {
    return $this->db->where('id', $id)->delete('order_details');
  }




  public function is_exists_detail($order_code, $item_code)
  {
    $rs = $this->db->select('id')
    ->where('order_code', $order_code)
    ->where('product_code', $item_code)
    ->get('order_details');
    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function is_exists_order($code, $old_code = NULL)
  {
    if($old_code !== NULL)
    {
      $this->db->where('code !=', $old_code);
    }

    $rs = $this->db->where('code', $code)->get('orders');
    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }




  public function get_order_detail($order_code, $item_code)
  {
    $rs = $this->db
    ->where('order_code', $order_code)
    ->where('product_code', $item_code)
    ->get('order_details');

    if($rs->num_rows() == 1)
    {
      return $rs->row();
    }

    return FALSE;
  }



  public function get_detail($id)
  {
    $rs = $this->db->where('id', $id)->get('order_details');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }




  public function get_order_details($code)
  {
    $rs = $this->db
    ->select('order_details.*')
    ->from('order_details')
    ->join('products', 'order_details.product_code = products.code', 'left')
    ->join('product_size', 'products.size_code = product_size.code', 'left')
    ->where('order_code', $code)
    ->order_by('products.style_code', 'ASC')
    ->order_by('products.color_code', 'ASC')
    ->order_by('product_size.position', 'ASC')
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function get_unvalid_details($code)
  {
    $rs = $this->db
    ->select('ods.*, pd.old_code')
    ->from('order_details AS ods')
    ->join('products AS pd', 'ods.product_code = pd.code', 'left')
    ->join('product_size AS size', 'pd.size_code = size.code', 'left')
    ->where('ods.order_code', $code)
    ->where('ods.valid', 0)
    ->where('ods.is_count', 1)
    ->order_by('pd.color_code', 'ASC')
    ->order_by('size.position', 'ASC')
    ->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function get_valid_details($code)
  {
    $rs = $this->db
    ->select('ods.*, pd.old_code')
    ->from('order_details AS ods')
    ->join('products AS pd', 'ods.product_code = pd.code', 'left')
    ->where('ods.order_code', $code)
    ->group_start()
    ->where('ods.valid', 1)
    ->or_where('ods.is_count', 0)
    ->group_end()
    ->get();
    //
    // $qr  = "SELECT * FROM order_details
    //         WHERE order_code = '{$code}'
    //         AND (valid = 1 OR is_count = 0)";
    // $rs = $this->db->query($qr);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function get_state($code)
  {
    $rs = $this->db->select('state')->where('code', $code)->or_where('reference', $code)->get('orders');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->state;
    }

    return FALSE;
  }



  public function get_order_code_by_reference($reference)
  {
    $rs = $this->db->select('code')->where('reference', $reference)->get('orders');
    if($rs->num_rows() > 0)
    {
      return $rs->row()->code;
    }

    return FALSE;
  }



  public function valid_detail($id)
  {
    return $this->db->set('valid', 1)->where('id', $id)->update('order_details');
  }

  public function unvalid_detail($order_code, $item_code)
  {
    return $this->db->set('valid', 0)->where('order_code', $order_code)->where('product_code', $item_code)->update('order_details');
  }



  public function valid_all_details($code)
  {
    return $this->db->set('valid', 1)->where('order_code', $code)->update('order_details');
  }




  public function change_state($code, $state)
  {
    $arr = array(
      'state' => $state,
      'update_user' => get_cookie('uname')
    );

    return $this->db->where('code', $code)->update('orders', $arr);
  }




  public function update_shipping_code($code, $ship_code)
  {
    return $this->db->set('shipping_code', $ship_code)->where('code', $code)->update('orders');
  }




  public function set_never_expire($code, $option)
  {
    return $this->db->set('never_expire', $option)->where('code', $code)->update('orders');
  }


  public function un_expired($code)
  {
    $this->db->trans_start();
    $this->db->set('is_expired', 0)->where('code', $code)->update('orders');
    $this->db->set('is_expired', 0)->where('order_code', $code)->update('order_details');
    $this->db->trans_complete();
    if($this->db->trans_status() === FALSE)
    {
      return FALSE;
    }
    else
    {
      return TRUE;
    }

  }


  //---- เปิดบิลใน SAP เรียบร้อยแล้ว
  public function set_complete($code)
  {
    return $this->db->set('is_complete', 1)->where('order_code', $code)->update('order_details');
  }



  public function un_complete($code)
  {
    return $this->db->set('is_complete', 0)->where('order_code', $code)->update('order_details');
  }

  public function clear_inv_code($code)
  {
    return $this->db->set('inv_code', NULL)->where('code', $code)->update('orders');
  }


  public function paid($code, $paid)
  {
    $paid = $paid === TRUE ? 1 : 0;
    return $this->db->set('is_paid', $paid)->where('code', $code)->update('orders');
  }



  public function count_rows(array $ds = array(), $role = 'S')
  {
    $this->db
    ->from('orders')
    ->join('customers', 'orders.customer_code = customers.code', 'left')
    ->join('zone', 'orders.zone_code = zone.code', 'left')
    ->join('user', 'orders.user = user.uname', 'left');

    if( ! empty($ds['from_date']) && ! empty($ds['to_date']) && ! empty($ds['stated']))
    {
      $this->db->join('order_state_change AS st', 'st.order_code = orders.code', 'left');
    }

    $this->db->where('role', $role);

    //---- เลขที่เอกสาร
    if( ! empty($ds['code']))
    {
      $this->db->like('orders.code', $ds['code']);
    }

    if(!empty($ds['qt_no']))
    {
      $this->db->like('orders.quotation_no', $ds['qt_no']);
    }

    //--- รหัส/ชื่อ ลูกค้า
    if( ! empty($ds['customer']))
    {
      $this->db->group_start();
      $this->db->like('customers.code', $ds['customer']);
      $this->db->or_like('customers.name', $ds['customer']);
      $this->db->group_end();
    }

    //---- user name / display name
    if( ! empty($ds['user']))
    {
      $this->db->group_start();
      $this->db->like('user.uname', $ds['user']);
      $this->db->or_like('user.name', $ds['user']);
      $this->db->group_end();
    }

    //---- เลขที่อ้างอิงออเดอร์ภายนอก
    if( ! empty($ds['reference']))
    {
      $this->db->like('orders.reference', $ds['reference']);
    }

    //---เลขที่จัดส่ง
    if( ! empty($ds['ship_code']))
    {
      $this->db->like('orders.shipping_code', $ds['ship_code']);
    }

    //--- ช่องทางการขาย
    if( ! empty($ds['channels']))
    {
      $this->db->where('orders.channels_code', $ds['channels']);
    }

    //--- ช่องทางการชำระเงิน
    if( ! empty($ds['payment']))
    {
      $this->db->where('orders.payment_code', $ds['payment']);
    }


    if( ! empty($ds['zone_code']))
    {
      $this->db->group_start();
      $this->db->like('zone.code', $ds['zone_code']);
      $this->db->or_like('zone.name', $ds['zone_code']);
      $this->db->group_end();
    }

    if( !empty($ds['user_ref']))
    {
      $this->db->like('orders.user_ref', $ds['user_ref']);
    }

    if(!empty($ds['empName']))
    {
      $this->db->like('orders.empName', $ds['empName']);
    }

    if( ! empty($ds['from_date']) && ! empty($ds['to_date']))
    {
      if(!empty($ds['stated']))
      {
        $this->db
        ->where('st.state', $ds['stated'])
        ->where('st.date_upd >=', from_date($ds['from_date']))
        ->where('st.date_upd <=', to_date($ds['to_date']))
        ->where('st.time_upd >=', $ds['startTime'])
        ->where('st.time_upd <=', $ds['endTime']);
      }
      else
      {
        $this->db->where('orders.date_add >=', from_date($ds['from_date']));
        $this->db->where('orders.date_add <=', to_date($ds['to_date']));
      }
    }

    if(!empty($ds['warehouse']))
    {
      $this->db->where('orders.warehouse_code', $ds['warehouse']);
    }

    if(!empty($ds['notSave']))
    {
      $this->db->where('orders.status', 0);
    }
    else
    {
      if(isset($ds['isApprove']))
      {
        if($ds['isApprove'] !== 'all')
        {
          $this->db->where('orders.status', 1);
        }
      }
    }

    if(!empty($ds['onlyMe']))
    {
      $this->db->where('orders.user', get_cookie('uname'));
    }

    if(!empty($ds['isExpire']))
    {
      $this->db->where('orders.is_expired', 1);
    }

    if(!empty($ds['state_list']))
    {
      $this->db->where_in('orders.state', $ds['state_list']);
    }

    //--- ใช้กับเอกสารที่ต้อง approve เท่านั้น
    if(isset($ds['isApprove']))
    {
      if($ds['isApprove'] !== 'all')
      {
        $this->db->where('orders.is_approved', $ds['isApprove']);
      }
    }

    //--- ใช้กับเอกสารที่ต้อง ว่ารับสินค้าเข้าปลายทางหรือยัง เท่านั้น
    if(isset($ds['isValid']))
    {
      if($ds['isValid'] !== 'all')
      {
        $this->db->where('orders.is_valid', $ds['isValid']);
      }
    }

    return $this->db->count_all_results();
  }





  public function get_data(array $ds = array(), $perpage = '', $offset = '', $role = 'S')
  {
    $this->db
    ->distinct()
    ->select('orders.*')
    ->from('orders')
    ->join('customers', 'orders.customer_code = customers.code', 'left')
    ->join('zone', 'orders.zone_code = zone.code', 'left')
    ->join('user', 'orders.user = user.uname', 'left');

    if( ! empty($ds['from_date']) && ! empty($ds['to_date']) && ! empty($ds['stated']))
    {
      $this->db->join('order_state_change AS st', 'st.order_code = orders.code', 'left');
    }

    $this->db->where('role', $role);

    //---- เลขที่เอกสาร
    if( ! empty($ds['code']))
    {
      $this->db->like('orders.code', $ds['code']);
    }


    if(!empty($ds['qt_no']))
    {
      $this->db->like('orders.quotation_no', $ds['qt_no']);
    }

    
    //--- รหัส/ชื่อ ลูกค้า
    if( ! empty($ds['customer']))
    {
      $this->db->group_start();
      $this->db->like('customers.code', $ds['customer']);
      $this->db->or_like('customers.name', $ds['customer']);
      $this->db->group_end();
    }

    //---- user name / display name
    if( ! empty($ds['user']))
    {
      $this->db->group_start();
      $this->db->like('user.uname', $ds['user']);
      $this->db->or_like('user.name', $ds['user']);
      $this->db->group_end();
    }

    //---- เลขที่อ้างอิงออเดอร์ภายนอก
    if( ! empty($ds['reference']))
    {
      $this->db->like('orders.reference', $ds['reference']);
    }

    //---เลขที่จัดส่ง
    if( ! empty($ds['ship_code']))
    {
      $this->db->like('orders.shipping_code', $ds['ship_code']);
    }

    //--- ช่องทางการขาย
    if( ! empty($ds['channels']))
    {
      $this->db->where('orders.channels_code', $ds['channels']);
    }

    //--- ช่องทางการชำระเงิน
    if( ! empty($ds['payment']))
    {
      $this->db->where('orders.payment_code', $ds['payment']);
    }


    if( ! empty($ds['zone_code']))
    {
      $this->db->group_start();
      $this->db->like('zone.code', $ds['zone_code']);
      $this->db->or_like('zone.name', $ds['zone_code']);
      $this->db->group_end();
    }

    if( !empty($ds['user_ref']))
    {
      $this->db->like('orders.user_ref', $ds['user_ref']);
    }

    if(!empty($ds['empName']))
    {
      $this->db->like('orders.empName', $ds['empName']);
    }

    if( ! empty($ds['from_date']) && ! empty($ds['to_date']))
    {
      if(!empty($ds['stated']))
      {
        $this->db
        ->where('st.state', $ds['stated'])
        ->where('st.date_upd >=', from_date($ds['from_date']))
        ->where('st.date_upd <=', to_date($ds['to_date']))
        ->where('st.time_upd >=', $ds['startTime'])
        ->where('st.time_upd <=', $ds['endTime']);
      }
      else
      {
        $this->db->where('orders.date_add >=', from_date($ds['from_date']));
        $this->db->where('orders.date_add <=', to_date($ds['to_date']));
      }
    }

    if(!empty($ds['warehouse']))
    {
      $this->db->where('orders.warehouse_code', $ds['warehouse']);
    }

    if(!empty($ds['notSave']))
    {
      $this->db->where('orders.status', 0);
    }
    else
    {
      if(isset($ds['isApprove']))
      {
        if($ds['isApprove'] !== 'all')
        {
          $this->db->where('orders.status', 1);
        }
      }
    }

    if(!empty($ds['onlyMe']))
    {
      $this->db->where('orders.user', get_cookie('uname'));
    }

    if(!empty($ds['isExpire']))
    {
      $this->db->where('orders.is_expired', 1);
    }

    if(!empty($ds['state_list']))
    {
      $this->db->where_in('orders.state', $ds['state_list']);
    }

    //--- ใช้กับเอกสารที่ต้อง approve เท่านั้น
    if(isset($ds['isApprove']))
    {
      if($ds['isApprove'] !== 'all')
      {
        $this->db->where('orders.is_approved', $ds['isApprove']);
      }
    }

    //--- ใช้กับเอกสารที่ต้อง ว่ารับสินค้าเข้าปลายทางหรือยัง เท่านั้น
    if(isset($ds['isValid']))
    {
      if($ds['isValid'] !== 'all')
      {
        $this->db->where('orders.is_valid', $ds['isValid']);
      }
    }


    if(!empty($ds['order_by']))
    {
      $order_by = "orders.{$ds['order_by']}";
      $this->db->order_by($order_by, $ds['sort_by']);
    }
    else
    {
      $this->db->order_by('orders.code', 'DESC');
    }

    if($perpage != '')
    {
      $offset = $offset === NULL ? 0 : $offset;
      $this->db->limit($perpage, $offset);
    }

    $rs = $this->db->get();
    //echo $this->db->get_compiled_select();
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  private function getOrderStateChangeIn($state, $fromDate, $toDate, $startTime, $endTime)
  {
    $qr  = "SELECT order_code FROM order_state_change ";
    $qr .= "WHERE state = {$state} ";
    $qr .= "AND date_upd >= '{$fromDate}' ";
    $qr .= "AND date_upd <= '{$toDate}' ";
    $qr .= "AND time_upd >= '{$startTime}' ";
    $qr .= "AND time_upd <= '{$endTime}' ";

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


  public function get_un_approve_list($role = 'C', $perpage = '')
  {
    $this->db
    ->select('orders.date_add, orders.code, customers.name AS customer_name, empName')
    ->from('orders')
    ->join('customers', 'orders.customer_code = customers.code', 'left')
    ->where('orders.role', $role)
    ->where('orders.status', 1)
    ->where('orders.state <', 3)
    ->where('orders.is_expired', 0)
    ->where('orders.is_cancled', 0)
    ->where('orders.is_approved', 0)
    ->order_by('orders.date_add', 'ASC')
    ->order_by('orders.code', 'ASC');

    if($perpage != '')
    {
      $this->db->limit($perpage);
    }

    $rs = $this->db->get();
    //echo $this->db->get_compiled_select('orders');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function count_un_approve_rows($role = 'C')
  {
    $this->db
    ->where('role', $role)
    ->where('status', 1)
    ->where('state <', 3)
    ->where('is_expired', 0)
    ->where('is_cancled', 0)
    ->where('is_approved', 0);

    return $this->db->count_all_results('orders');
  }



  public function get_un_received_list($perpage = '', $offset = '')
  {
    $this->db
    ->select('orders.date_add, orders.code, customers.name AS customer_name')
    ->from('orders')
    ->join('customers', 'orders.customer_code = customers.code', 'left')
    ->where('orders.role', 'N')
    ->where('orders.status', 1)
    ->where('orders.state', 8)
    ->where('orders.is_expired', 0)
    ->where('orders.is_cancled', 0)
    ->where('orders.is_approved', 1)
    ->where('orders.is_valid', 0)
    ->order_by('orders.date_add', 'ASC')
    ->order_by('orders.code', 'ASC');

    if($perpage != '')
    {
      $offset = $offset === NULL ? 0 : $offset;
      $this->db->limit($perpage, $offset);
    }

    $rs = $this->db->get();
    //echo $this->db->get_compiled_select('orders');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }



  public function count_un_receive_rows()
  {
    $this->db
    ->where('role', 'N')
    ->where('status', 1)
    ->where('state', 8)
    ->where('is_expired', 0)
    ->where('is_cancled', 0)
    ->where('is_approved', 1)
    ->where('is_valid', 0);

    return $this->db->count_all_results('orders');
  }





  public function get_max_code($code)
  {
    $qr = "SELECT MAX(code) AS code FROM orders WHERE code LIKE '".$code."%' ORDER BY code DESC";
    $rs = $this->db->query($qr);
    return $rs->row()->code;
  }




  public function get_order_total_amount($code)
  {
    $this->db->select_sum('total_amount', 'amount');
    $this->db->where('order_code', $code);
    $rs = $this->db->get('order_details');
    return $rs->row()->amount;
  }


  public function get_bill_total_amount($code)
  {
    $rs = $this->db
    ->select_sum('total_amount', 'amount')
    ->where('reference', $code)
    ->get('order_sold');

    return $rs->row()->amount;
  }



  public function get_order_total_qty($code)
  {
    $this->db->select_sum('qty', 'qty');
    $this->db->where('order_code', $code);
    $rs = $this->db->get('order_details');
    return $rs->row()->qty;
  }


  //--- ใช้คำนวนยอดเครดิตคงเหลือ
  public function get_sum_not_complete_amount($customer_code)
  {
    $rs = $this->db
    ->select_sum('order_details.total_amount', 'amount')
    ->from('order_details')
    ->join('orders', 'orders.code = order_details.order_code', 'left')
    ->where_in('orders.role', array('S', 'C', 'N'))
    ->where('orders.customer_code', $customer_code)
    ->where('order_details.is_complete', 0)
    ->where('orders.is_expired', 0)
    ->get();

    if($rs->num_rows() === 1)
    {
      return $rs->row()->amount;
    }

    return 0.00;
  }



  public function get_bill_discount($code)
  {
    $rs = $this->db->select('bDiscAmount')
    ->where('code', $code)
    ->get('orders');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->bDiscAmount;
    }

    return 0;
  }


  public function get_sum_style_qty($order_code, $style_code)
  {
    $rs = $this->db->select_sum('qty')
    ->where('order_code', $order_code)
    ->where('style_code', $style_code)
    ->get('order_detils');

    return $rs->row()->qty;
  }




  public function get_reserv_stock($item_code, $warehouse = NULL, $zone = NULL)
  {
    $this->db
    ->select_sum('order_details.qty', 'qty')
    ->from('order_details')
    ->join('orders', 'order_details.order_code = orders.code', 'left')
    ->where('order_details.product_code', $item_code)
    ->where('order_details.is_complete', 0)
    ->where('order_details.is_expired', 0)
    ->where('order_details.is_count', 1);

    if($warehouse !== NULL)
    {
      $this->db->where('orders.warehouse_code', $warehouse);
    }

    if($zone !== NULL)
    {
      $this->db->where('orders.zone_code', $zone);
    }

    $rs = $this->db->get();

    if($rs->num_rows() == 1)
    {
      return $rs->row()->qty;
    }

    return 0;
  }



  public function get_reserv_stock_by_style($style_code, $warehouse = NULL)
  {
    $this->db
    ->select_sum('order_details.qty', 'qty')
    ->from('order_details')
    ->join('orders', 'order_details.order_code = orders.code', 'left')
    ->where('order_details.style_code', $style_code)
    ->where('order_details.is_complete', 0)
    ->where('order_details.is_expired', 0)
    ->where('order_details.is_count', 1);
    if($warehouse !== NULL)
    {
      $this->db->where('warehouse_code', $warehouse);
    }
    $rs = $this->db->get();
    if($rs->num_rows() == 1)
    {
      return $rs->row()->qty;
    }

    return 0;
  }


  public function set_status($code, $status)
  {
    return $this->db->set('status', $status)->where('code', $code)->update('orders');
  }


  public function set_report_status($code, $status)
  {
    //--- NULL = not sent, 1 = sent, 3 = error;
    return $this->db->set('is_report', $status)->where('code', $code)->update('orders');
  }



  public function update_approver($code, $user)
  {
    return $this->db
    ->set('approver', $user)
    ->set('approve_date', now())
    ->set('is_approved', 1)
    ->where('code', $code)
    ->update('orders');
  }



  public function un_approver($code, $user)
  {
    return $this->db
    ->set('approver', NULL)
    ->set('approve_date', now())
    ->set('is_approved', 0)
    ->where('code', $code)
    ->update('orders');
  }

  //---- ระบุที่อยู่จัดส่งในออเดอร์นั้นๆ
  public function set_address_id($code, $id_address)
  {
    return $this->db->set('id_address', $id_address)->where('code', $code)->update('orders');
  }



  public function clear_order_detail($code)
  {
    return $this->db->where('order_code', $code)->delete('order_details');
  }



  //--- Set is_valid = 1 when transfer draft is confirmed (use in Controller inventory/transfer->confirm_receipted)
  public function valid_transfer_draft($code)
  {
    return $this->db->set('is_valid', 1)->where('code', $code)->update('orders');
  }


  public function get_order_non_inv_code($limit = 100)
  {
    $rs = $this->db
    ->select('code')
    ->where_in('role', 'S')
    ->where('state', 8)
    ->where('status', 1)
    ->where('is_cancled', 0)
    ->where('is_expired', 0)
    ->where('inv_code IS NULL', NULL, FALSE)
    ->limit($limit)
    ->get('orders');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_sponsor_non_inv_code($limit = 100)
  {
    $rs = $this->db
    ->select('code')
    ->where_in('role', array('P', 'U'))
    ->where('state', 8)
    ->where('status', 1)
    ->where('is_cancled', 0)
    ->where('is_expired', 0)
    ->where('inv_code IS NULL', NULL, FALSE)
    ->limit($limit)
    ->get('orders');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }




  public function get_consignment_non_inv_code($limit = 100)
  {
    $rs = $this->db
    ->select('code')
    ->where('role', 'C')
    ->where('state', 8)
    ->where('status', 1)
    ->where('is_cancled', 0)
    ->where('is_expired', 0)
    ->where('inv_code IS NULL', NULL, FALSE)
    ->limit($limit)
    ->get('orders');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

  //--- WT
  public function get_order_transfer_non_inv_code($limit = 100)
  {
    $rs = $this->db
    ->select('code')
    ->where('role', 'N')
    ->where('state', 8)
    ->where('status', 1)
    ->where('is_cancled', 0)
    ->where('is_expired', 0)
    ->where('is_valid', 1)
    ->where('inv_code IS NULL', NULL, FALSE)
    ->limit($limit)
    ->get('orders');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  //---- WL
  public function get_order_lend_non_inv_code($limit = 100)
  {
    $rs = $this->db
    ->select('code')
    ->where('role', 'L')
    ->where('state', 8)
    ->where('status', 1)
    ->where('is_cancled', 0)
    ->where('is_expired', 0)
    ->where('inv_code IS NULL', NULL, FALSE)
    ->limit($limit)
    ->get('orders');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  //--- WQ, WV
  public function get_order_transform_non_inv_code($limit = 100)
  {
    $rs = $this->db
    ->select('code')
    ->where_in('role', array('Q','T'))
    ->where('state', 8)
    ->where('status', 1)
    ->where('is_cancled', 0)
    ->where('is_expired', 0)
    ->where('inv_code IS NULL', NULL, FALSE)
    ->limit($limit)
    ->get('orders');

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
    ->get('ODLN');

    if($rs->num_rows() > 0)
    {
      return $rs->row()->DocNum;
    }

    return NULL;
  }


  public function update_inv($code, $doc_num)
  {
    return $this->db->set('inv_code', $doc_num)->where('code', $code)->update('orders');
  }


  public function set_exported($code, $status, $error)
  {
    return $this->db->set('is_exported', $status)->set('export_error', $error)->where('code', $code)->update('orders');
  }


  public function get_expire_list($date, array $role = array('S'))
  {
    $rs = $this->db
    ->select('code')
    ->where('date_add <', $date)
    ->where_in('role', $role)
    ->where_in('state', array(1,2))
    ->where('is_paid', 0)
    ->where('never_expire', 0)
    ->get('orders');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

  public function set_expire_order($code)
  {
    if(!empty($code))
    {
      return $this->db
      ->set('is_expired', 1)
      ->where('code', $code)
      ->update('orders');
    }

    return FALSE;
  }

  public function set_expire_order_details($code)
  {
    if(!empty($code))
    {
      return $this->db
      ->set('is_expired', 1)
      ->where('order_code', $code)
      ->update('order_details');
    }

    return FALSE;
  }
} //--- End class


 ?>

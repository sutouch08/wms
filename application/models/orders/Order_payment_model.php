<?php
class Order_payment_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function count_rows(array $ds = array())
  {
    $this->db
    ->from('order_payment')
    ->join('orders', 'orders.code = order_payment.order_code', 'left')
    ->join('customers', 'customers.code = orders.customer_code', 'left')
    ->join('channels','orders.channels_code = channels.code', 'left');


    if( isset($ds['valid']) && $ds['valid'] != 'all')
    {
      $this->db->where('order_payment.valid', $ds['valid']);
    }

    //---- เลขที่เอกสาร
    if( ! empty($ds['code']))
    {
      $this->db->like('order_payment.order_code', $ds['code']);
    }


    if(!empty($ds['channels']) && $ds['channels'] !== 'all')
    {
      $this->db->where('orders.channels_code', $ds['channels']);
    }

    if(!empty($ds['customer']))
    {
      $this->db->group_start();
      $this->db->like('customers.name', $ds['customer']);
      $this->db->or_like('orders.customer_ref', $ds['customer']);
      $this->db->group_end();
    }


    if(! empty($ds['account']) && $ds['account'] != 'all')
    {
      $this->db->where('id_account', $ds['account']);
    }


		//---- user name / display name
    if(!empty($ds['user']))
    {
      $users = user_in($ds['user']);
      $this->db->where_in('order_payment.user', $users);
    }

    if($ds['from_date'] != '' && $ds['to_date'] != '')
    {
      $this->db->where('pay_date >=', from_date($ds['from_date']));
      $this->db->where('pay_date <=', to_date($ds['to_date']));
    }

    if( isset($ds['is_pre_order']) && $ds['is_pre_order'] != 'all')
    {
      $this->db->where('order_payment.is_pre_order', $ds['is_pre_order']);
    }

    return $this->db->count_all_results();
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
    $this->db
    ->select('order_payment.*')
    ->select('customers.name AS customer_name, orders.customer_ref, channels.name AS channels')
    ->from('order_payment')
    ->join('orders', 'orders.code = order_payment.order_code', 'left')
    ->join('customers', 'customers.code = orders.customer_code', 'left')
    ->join('channels','orders.channels_code = channels.code', 'left');

    if( isset($ds['valid']) && $ds['valid'] != 'all')
    {
      $this->db->where('order_payment.valid', $ds['valid']);
    }

    //---- เลขที่เอกสาร
    if(!empty($ds['code']))
    {
      $this->db->like('order_payment.order_code', $ds['code']);
    }


    if(!empty($ds['channels']) && $ds['channels'] != 'all')
    {
      $this->db->where('orders.channels_code', $ds['channels']);
    }


    if(!empty($ds['customer']))
    {
      $this->db->group_start();
      $this->db->like('customers.name', $ds['customer']);
      $this->db->or_like('orders.customer_ref', $ds['customer']);
      $this->db->group_end();
    }

    //--- รหัส/ชื่อ ลูกค้า
    if(!empty($ds['account']) && $ds['account'] != 'all')
    {
      $this->db->where('order_payment.id_account', $ds['account']);
    }

    //---- user name / display name
    if(!empty($ds['user']))
    {
      $users = user_in($ds['user']);
      $this->db->where_in('order_payment.user', $users);
    }


    if(!empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db->where('order_payment.pay_date >=', from_date($ds['from_date']));
      $this->db->where('order_payment.pay_date <=', to_date($ds['to_date']));
    }

    if( isset($ds['is_pre_order']) && $ds['is_pre_order'] != 'all')
    {
      $this->db->where('order_payment.is_pre_order', $ds['is_pre_order']);
    }

    $this->db->order_by('order_payment.order_code', 'ASC');

    $rs = $this->db->limit($perpage, $offset)->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }



  public function get_data(array $ds = array())
  {
    $this->db
    ->select('order_payment.*')
    ->select('customers.name AS customer_name, orders.customer_ref, channels.name AS channels')
    ->from('order_payment')
    ->join('orders', 'orders.code = order_payment.order_code', 'left')
    ->join('customers', 'customers.code = orders.customer_code', 'left')
    ->join('channels','orders.channels_code = channels.code', 'left');

    if( isset($ds['valid']) && $ds['valid'] != 'all')
    {
      $this->db->where('order_payment.valid', $ds['valid']);
    }

    //---- เลขที่เอกสาร
    if(!empty($ds['code']))
    {
      $this->db->like('order_payment.order_code', $ds['code']);
    }


    if(!empty($ds['channels']) && $ds['channels'] != 'all')
    {
      $this->db->where('orders.channels_code', $ds['channels']);
    }


    if(!empty($ds['customer']))
    {
      $this->db->group_start();
      $this->db->like('customers.name', $ds['customer']);
      $this->db->or_like('orders.customer_ref', $ds['customer']);
      $this->db->group_end();
    }

    //--- รหัส/ชื่อ ลูกค้า
    if(!empty($ds['account']) && $ds['account'] != 'all')
    {
      $this->db->where('order_payment.id_account', $ds['account']);
    }

    //---- user name / display name
    if(!empty($ds['user']))
    {
      $users = user_in($ds['user']);
      $this->db->where_in('order_payment.user', $users);
    }


    if(!empty($ds['from_date']) && !empty($ds['to_date']))
    {
      $this->db->where('order_payment.pay_date >=', from_date($ds['from_date']));
      $this->db->where('order_payment.pay_date <=', to_date($ds['to_date']));
    }

    if( isset($ds['is_pre_order']) && $ds['is_pre_order'] != 'all')
    {
      $this->db->where('order_payment.is_pre_order', $ds['is_pre_order']);
    }

    $this->db->order_by('order_payment.order_code', 'ASC');

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->replace('order_payment', $ds);
    }

    return FALSE;
  }




  public function get($code)
  {
    $rs = $this->db->where('order_code', $code)->get('order_payment');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }



  public function get_detail($id)
  {
    $rs = $this->db->where('id', $id)->get('order_payment');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  public function valid_payment($id)
  {
    $arr = array(
      'valid' => 1,
      'validate_by' => $this->_user->uname
    );

    return $this->db->where('id', $id)->update('order_payment', $arr);
  }


  public function un_valid_payment($id)
  {
    return $this->db->set('valid', 0)->where('id', $id)->update('order_payment');
  }

  public function delete($id)
  {
    return $this->db->where('id', $id)->delete('order_payment');
  }




  public function is_exists($code)
  {
    $rs = $this->db->select('order_code')
    ->where('order_code', $code)
    ->get('order_payment');
    if($rs->num_rows() === 1)
    {
      return TRUE;
    }

    return FALSE;
  }


	//---- for check transection
	public function has_account_transection($id_account)
	{
		$rs = $this->db->where('id_account', $id_account)->count_all_results('order_payment');

		if($rs > 0)
		{
			return TRUE;
		}

		return FALSE;
	}

} //--- end class
?>

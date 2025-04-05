<?php
class Delivery_order_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }



  public function get_sold_details($reference)
  {
    $rs = $this->db->where('reference', $reference)->get('order_sold');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function count_rows(array $ds = array(), $state = 7)
  {
    $this->db->where('state', $state);

    if($ds['from_date'] != '' && $ds['to_date'] != '')
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }
    else
    {
      $this->db->where('id >', $this->get_max_id());
      // $this->db->where('date_add >=', from_date($this->_dataDate));
    }

    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if(! empty($ds['customer']))
    {
      $this->db
      ->group_start()
			->like('customer_code', $ds['customer'])
      ->or_like('customer_name', $ds['customer'])
      ->or_like('customer_ref', $ds['customer'])
      ->group_end();
    }

    //---- user name / display name
    if( ! empty($ds['user']))
    {
      $users = user_in($ds['user']);
      $this->db->where_in('user', $users);
    }


    if(!empty($ds['role']))
    {
      $this->db->where('role', $ds['role']);
    }

    if(! empty($ds['channels']))
    {
      $this->db->where('channels_code', $ds['channels']);
    }


    if(!empty($ds['warehouse']) && $ds['warehouse'] != 'all')
    {
      $this->db->where('warehouse_code', $ds['warehouse']);
    }


    if(isset($ds['is_valid']) && $ds['is_valid'] != 'all')
    {
      $this->db->where('is_valid', $ds['is_valid']);
    }

		if(isset($ds['is_exported']) && $ds['is_exported'] != 'all')
		{
      if($ds['is_exported'] == '3')
      {
        $this->db->where('inv_code IS NULL');
      }

			$this->db->where('is_exported', $ds['is_exported']);
		}


    if(isset($ds['sap_status']) && $ds['sap_status'] != 'all')
    {
      if($ds['sap_status'] == 'Y')
      {
        $this->db->where('inv_code IS NOT NULL', NULL, FALSE);
      }

      if($ds['sap_status'] == 'N')
      {
        $this->db->where('inv_code IS NULL', NULL, FALSE);
      }
    }

    if(isset($ds['ship_from_date']) && $ds['ship_from_date'] != '')
    {
      $this->db->where('shipped_date >=', from_date($ds['ship_from_date']));
    }

    if(isset($ds['ship_to_date']) && $ds['ship_to_date'] != '')
    {
      $this->db->where('shipped_date <=', to_date($ds['ship_to_date']));
    }

    if(isset($ds['is_hold']) && $ds['is_hold'] != 'all')
    {
      $this->db->where('is_hold', $ds['is_hold']);
    }

    return $this->db->count_all_results('orders');
  }


  public function get_list(array $ds = array(), $perpage = 20, $offset = 0, $state = 7)
  {
    $this->db
    ->select('id, code, role, reference, customer_code, customer_name, customer_ref')
    ->select('channels_code, payment_code, date_add, shipped_date, user, doc_total, inv_code, empID, empName, is_cancled, is_hold')
    ->where('state', $state);

    if($ds['from_date'] != '' && $ds['to_date'] != '')
    {
      $this->db->where('date_add >=', from_date($ds['from_date']));
      $this->db->where('date_add <=', to_date($ds['to_date']));
    }
    else
    {
      $this->db->where('id >', $this->get_max_id());
      // $this->db->where('date_add >=', from_date($this->_dataDate));
    }

    if( ! empty($ds['code']))
    {
      $this->db->like('code', $ds['code']);
    }

    if(! empty($ds['customer']))
    {
      $this->db
      ->group_start()
			->like('customer_code', $ds['customer'])
      ->or_like('customer_name', $ds['customer'])
      ->or_like('customer_ref', $ds['customer'])
      ->group_end();
    }

    //---- user name / display name
    if( ! empty($ds['user']))
    {
      $users = user_in($ds['user']);
      $this->db->where_in('user', $users);
    }


    if(!empty($ds['role']))
    {
      $this->db->where('role', $ds['role']);
    }

    if(! empty($ds['channels']))
    {
      $this->db->where('channels_code', $ds['channels']);
    }


    if(!empty($ds['warehouse']) && $ds['warehouse'] != 'all')
    {
      $this->db->where('warehouse_code', $ds['warehouse']);
    }


    if(isset($ds['is_valid']) && $ds['is_valid'] != 'all')
    {
      $this->db->where('is_valid', $ds['is_valid']);
    }

		if(isset($ds['is_exported']) && $ds['is_exported'] != 'all')
		{
      if($ds['is_exported'] == '3')
      {
        $this->db->where('inv_code IS NULL');
      }

			$this->db->where('is_exported', $ds['is_exported']);
		}

    if(isset($ds['sap_status']) && $ds['sap_status'] != 'all')
    {
      if($ds['sap_status'] == 'Y')
      {
        $this->db->where('inv_code IS NOT NULL', NULL, FALSE);
      }

      if($ds['sap_status'] == 'N')
      {
        $this->db->where('inv_code IS NULL', NULL, FALSE);
      }
    }

    if(isset($ds['ship_from_date']) && $ds['ship_from_date'] != '')
    {
      $this->db->where('shipped_date >=', from_date($ds['ship_from_date']));
    }

    if(isset($ds['ship_to_date']) && $ds['ship_to_date'] != '')
    {
      $this->db->where('shipped_date <=', to_date($ds['ship_to_date']));
    }

    if(isset($ds['is_hold']) && $ds['is_hold'] != 'all')
    {
      $this->db->where('is_hold', $ds['is_hold']);
    }

    $order_by = $state == '7' ? 'ASC' : 'DESC';

    $rs = $this->db->order_by('date_add', $order_by)->order_by('id', $order_by)->limit($perpage, $offset)->get('orders');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }


  public function get_sum_prepared($order_code, $product_code, $order_detail_id)
  {
    $rs = $this->db
    ->select_sum('qty')
    ->where('order_code', $order_code)
    ->where('product_code', $product_code)
    ->group_start()
    ->where('order_detail_id', $order_detail_id)
    ->or_where('order_detail_id IS NULL', NULL, FALSE)
    ->group_end()
    ->get('prepare');

    if($rs->num_rows() > 0)
    {
      return $rs->row()->qty;
    }

    return 0;
  }


  public function get_sum_qc($order_code, $product_code, $order_detail_id)
  {
    $rs = $this->db
    ->select_sum('qty')
    ->where('order_code', $order_code)
    ->where('product_code', $product_code)
    ->group_start()
    ->where('order_detail_id', $order_detail_id)
    ->or_where('order_detail_id IS NULL', NULL, FALSE)
    ->group_end()
    ->get('qc');

    if($rs->num_rows() > 0)
    {
      return $rs->row()->qty;
    }

    return 0;
  }


  public function get_billed_amount($code)
  {
    $rs = $this->db
    ->select_sum('total_amount')
    ->where('reference', $code)
    ->get('order_sold');

    return $rs->row()->total_amount;
  }


    //------------------ สำหรับแสดงยอดที่มีการบันทึกขายไปแล้ว -----------//
    //--- รายการสั้งซื้อ รายการจัดสินค้า รายการตรวจสินค้า
    //--- เปรียบเทียบยอดที่มีการสั่งซื้อ และมีการตรวจสอนค้า
    //--- เพื่อให้ได้ยอดที่ต้องเปิดบิล บันทึกขายจริงๆ
    //--- ผลลัพธ์จะได้ยอดสั่งซื้อเป็นหลัก หากไม่มียอดตรวจ จะได้ยอดตรวจ เป็น NULL
    //--- กรณีสินค้าเป็นสินค้าที่ไม่นับสต็อกจะบันทึกตามยอดที่สั่งมา
    public function get_billed_detail($code)
    {
      $qr = "SELECT o.id, o.product_code, o.product_name, o.qty AS order_qty, o.is_count, ";
      $qr .= "o.price, o.discount1, o.discount2, o.discount3, ";
      $qr .= "(o.discount_amount / o.qty) AS discount_amount, ";
      $qr .= "(o.total_amount/o.qty) AS final_price ";
      $qr .= "FROM order_details AS o ";
      $qr .= "WHERE o.order_code = '{$code}'";

      $qs = $this->db->query($qr);

      if($qs->num_rows() > 0)
      {
        $details = $qs->result();

        foreach($details as $rs)
        {
          $rs->prepared = $this->get_sum_prepared($code, $rs->product_code, $rs->id);
          $rs->qc = $this->get_sum_qc($code, $rs->product_code, $rs->id);
        }

        return $details;
      }

      return NULL;
    }




    //------------- สำหรับใช้ในการบันทึกขาย ---------//
    //--- รายการสั้งซื้อ รายการจัดสินค้า รายการตรวจสินค้า
    //--- เปรียบเทียบยอดที่มีการสั่งซื้อ และมีการตรวจสอนค้า
    //--- เพื่อให้ได้ยอดที่ต้องเปิดบิล บันทึกขายจริงๆ
    //--- ผลลัพธ์จะไม่ได้ยอดที่มีการสั่งซื้อแต่ไม่มียอดตรวจ หรือ มียอดตรวจแต่ไม่มียอดสั่งซื้อ (กรณีมีการแก้ไขออเดอร์)

    public function get_bill_detail($code)
    {
      $qr = "SELECT o.id, o.style_code, o.product_code, o.product_name, o.qty AS order_qty, ";
      $qr .= "o.cost, o.price, o.discount1, o.discount2, o.discount3, ";
      $qr .= "o.id_rule, ru.id_policy, o.is_count, ";
      $qr .= "(o.discount_amount / o.qty) AS discount_amount, ";
      $qr .= "(o.total_amount/o.qty) AS final_price ";
      $qr .= "FROM order_details AS o ";
      $qr .= "LEFT JOIN discount_rule AS ru ON ru.id = o.id_rule ";
      $qr .= "WHERE o.order_code = '{$code}' ";
      $qr .= "AND o.is_count = 1 ";

      $rs = $this->db->query($qr);

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }

      return FALSE;
    }


    public function get_non_count_bill_detail($code)
    {
      $qr  = "SELECT o.id, o.product_code, o.product_name, o.style_code, o.qty, ";
      $qr .= "o.cost, o.price, o.discount1, o.discount2, o.discount3, ";
      $qr .= "o.id_rule, ru.id_policy, o.is_count, ";
      $qr .= "(o.discount_amount / o.qty) AS discount_amount, ";
      $qr .= "(o.total_amount/o.qty) AS final_price ";
      $qr .= "FROM order_details AS o ";
      $qr .= "LEFT JOIN discount_rule AS ru ON ru.id = o.id_rule ";
      $qr .= "WHERE o.order_code = '{$code}' ";
      $qr .= "AND o.is_count = 0 ";

      $rs = $this->db->query($qr);
      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }

      return FALSE;
    }


    public function sold(array $ds = array())
    {
      if(!empty($ds))
      {
        return $this->db->insert('order_sold', $ds);
      }

      return FALSE;
    }


    public function add_sap_delivery_order(array $ds = array())
    {
      $rs = $this->mc->insert('ODLN', $ds);
      if($rs)
      {
        return $this->mc->insert_id();
      }

      return FALSE;
    }


    public function update_sap_delivery_order($code, $ds)
    {
      return $this->mc->where('U_ECOMNO', $code)->update('ODLN', $ds);
    }


    public function add_delivery_row(array $ds = array())
    {
      return $this->mc->insert('DLN1', $ds);
    }


    public function is_doc_exists($code)
    {
      $rs = $this->mc->select('U_ECOMNO')->where('U_ECOMNO', $code)->get('ODLN');
      if($rs->num_rows() > 0)
      {
        return TRUE;
      }

      return FALSE;
    }


    public function get_middle_delivery_order($code)
    {
      $rs = $this->mc
      ->select('DocEntry')
      ->where('U_ECOMNO', $code)
      ->group_start()
      ->where('F_Sap', 'N')
      ->or_where('F_Sap IS NULL', NULL, FALSE)
      ->group_end()
      ->get('ODLN');

      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }

      return NULL;
    }



    public function get_sap_delivery_order($code)
    {
      $rs = $this->ms
      ->select('DocEntry, DocStatus')
      ->where('U_ECOMNO', $code)
      ->where('CANCELED', 'N')
      ->get('ODLN');
      if($rs->num_rows() > 0)
      {
        return $rs->result();
      }

      return NULL;
    }


		public function exists_sap_delivery_order($code)
    {
      $rs = $this->ms
      ->where('U_ECOMNO', $code)
      ->where('CANCELED', 'N')
      ->count_all_results('ODLN');
      if($rs > 0)
      {
        return TRUE;
      }

      return FALSE;
    }



    public function sap_exists_details($code)
    {
      $rs = $this->mc->select('LineNum')->where('U_ECOMNO', $code)->get('DLN1');
      if($rs->num_rows() > 0)
      {
        return TRUE;
      }

      return FALSE;
    }


    //--- ลบรายการที่ค้างใน middle ที่ยังไม่ได้เอาเข้า SAP ออก
    public function drop_middle_exits_data($docEntry)
    {
      $this->mc->trans_start();
      $this->mc->where('DocEntry', $docEntry)->delete('DLN1');
      $this->mc->where('DocEntry', $docEntry)->delete('ODLN');
      $this->mc->trans_complete();
      return $this->mc->trans_status();
    }


    public function drop_sap_exists_details($code)
    {
      return $this->mc->where('U_ECOMNO', $code)->delete('DLN1');
    }




    public function update_delivery_row($DocEntry, $line, $ds = array())
    {

      return $this->mc->where('DocEntry', $DocEntry)->where('LineNum', $line)->update('DLN1', $ds);
    }



    public function getDocEntry($code)
    {
      $rs = $this->mc->select_max('DocEntry')->where('U_ECOMNO', $code)->get('ODLN');
      if($rs->num_rows() === 1)
      {
        return $rs->row()->DocEntry;
      }

      return FALSE;
    }

    private function get_max_id()
    {
      $rs = $this->db->query("SELECT MAX(id) AS id FROM orders");

      if($rs->num_rows() === 1)
      {
        return $rs->row()->id - 200000;
      }

      return 2000000;
    }
}

 ?>

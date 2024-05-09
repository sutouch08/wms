<?php
class Auto_confirm_order extends CI_Controller
{
  public $home;
  public $mc;
  public $ms;
  public $title = "Auto comfirm order";
  public $isViewer = FALSE;
  public $notibars = FALSE;
  public $menu_code = NULL;
  public $menu_group_code = NULL;
  public $pm;
  public $error;

  public function __construct()
  {
    parent::__construct();
    $this->ms = $this->load->database('ms', TRUE); //--- SAP database
    $this->mc = $this->load->database('mc', TRUE); //--- Temp Database
    $this->home = base_url().'auto/auto_delivery_order';
    $this->load->model('inventory/delivery_order_model');
    $this->load->model('orders/orders_model');
    $this->load->model('orders/order_state_model');
		$this->load->library('export');
    $this->pm = new stdClass();
    $this->pm->can_view = 1;
  }

  public function index()
  {
    $ds['data'] = NULL;
    $all = $this->db->where('status !=', 1)->count_all_results('auto_send_to_sap_order');
    $rs = $this->db->where('status !=', 1)->limit(50)->get('auto_send_to_sap_order');

    $ds['count'] = $rs->num_rows();
    $ds['all'] = $all;
    $ds['data'] = $rs->result();

    $this->load->view('auto/auto_confirm_order', $ds);
  }


  private function export_order($code)
  {
    $sc = TRUE;
    $this->load->library('export');
    if(! $this->export->export_order($code))
    {
      $sc = FALSE;
      $this->error = trim($this->export->error);
    }

    return $sc;
  }


  private function export_transfer_order($code)
  {
    $sc = TRUE;
    $this->load->library('export');
    if(! $this->export->export_transfer_order($code))
    {
      $sc = FALSE;
      $this->error = trim($this->export->error);
    }

    return $sc;
  }


  private function export_transfer_draft($code)
  {
    $sc = TRUE;
    $this->load->library('export');
    if(! $this->export->export_transfer_draft($code))
    {
      $sc = FALSE;
      $this->error = trim($this->export->error);
    }

    return $sc;
  }


  private function export_transform($code)
  {
    $sc = TRUE;
    $this->load->library('export');
    if(! $this->export->export_transform($code))
    {
      $sc = FALSE;
      $this->error = trim($this->export->error);
    }

    return $sc;
  }


  //--- manual export by client
  public function do_export($code)
  {
    $sc = TRUE;

    $order = $this->orders_model->get($code);

    if(!empty($order))
    {
      switch($order->role)
      {
        case 'C' : //--- Consign (SO)
          $sc = $this->export_order($code);
          break;

        case 'L' : //--- Lend
          $sc = $this->export_transfer_order($code);
          break;

        case 'N' : //--- Consign (TR)
          $sc = $this->export_transfer_draft($code);
          break;

        case 'P' : //--- Sponsor
          $sc = $this->export_order($code);
          break;

        case 'Q' : //--- Transform for stock
          $sc = $this->export_transform($code);
          break;

        case 'S' : //--- Sale order
          $sc = $this->export_order($code);
          break;

        case 'T' : //--- Transform for sell
          $sc = $this->export_transform($code);
          break;

        case 'U' : //--- Support
          $sc = $this->export_order($code);
          break;

        default : ///--- sale order
          $sc = $this->export_order($code);
          break;
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเลขที่เอกสาร {$code}";
    }

    return $sc;
  }

  public function confirm_order()
  {
    $sc = TRUE;
    $message = 'Update failed';
    $this->load->model('masters/products_model');
    $this->load->model('inventory/buffer_model');
    $this->load->model('inventory/cancle_model');
    $this->load->model('inventory/movement_model');
    $this->load->helper('discount');
    $uname = get_cookie('uname');

    $code = $this->input->post('order_code');

    if($code)
    {
      $order = $this->orders_model->get($code);

      $date_add = getConfig('ORDER_SOLD_DATE') == 'D' ? $order->date_add : now();

      if($order->role == 'T' OR $order->role == 'Q')
      {
        $this->load->model('inventory/transform_model');
      }

      if($order->role == 'L')
      {
        $this->load->model('inventory/lend_model');
      }

      if($order->state == 7)
      {
        $this->db->trans_start();

        //--- change state
       $this->orders_model->change_state($code, 8);

       if(empty($order->shipped_date))
       {
         $this->orders_model->update($code, array('shipped_date' => now())); //--- update shipped date
       }


        //--- add state event
        $arr = array(
          'order_code' => $code,
          'state' => 8,
          'update_user' => $uname
        );

        $this->order_state_model->add_state($arr);

        //---- รายการทีรอการเปิดบิล
        $bill = $this->delivery_order_model->get_bill_detail($code);

        if(!empty($bill))
        {
          foreach($bill as $rs)
          {
            //--- ถ้ามีรายการที่ไมสำเร็จ ออกจาก loop ทันที
            if($sc === FALSE)
            {
              break;
            }

            //--- ถ้ายอดตรวจ น้อยกว่า หรือ เท่ากับ ยอดสั่ง ใช้ยอดตรวจในการตัด buffer
            //--- ถ้ายอดตวจ มากกว่า ยอดสั่ง ให้ใช้ยอดสั่งในการตัด buffer (บางทีอาจมีการแก้ไขออเดอร์หลังจากมีการตรวจสินค้าแล้ว)
            $sell_qty = ($rs->order_qty >= $rs->qc) ? $rs->qc : $rs->order_qty;

            //--- ดึงข้อมูลสินค้าที่จัดไปแล้วตามสินค้า
            $buffers = $this->buffer_model->get_details($code, $rs->product_code);

            if(!empty($buffers))
            {
              $no = 0;
              foreach($buffers as $rm)
              {
                if($sell_qty > 0)
                {
                //--- ถ้ายอดใน buffer น้อยกว่าหรือเท่ากับยอดสั่งซื้อ (แยกแต่ละโซน น้อยกว่าหรือเท่ากับยอดสั่ง (ซึ่งควรเป็นแบบนี้))
                  $buffer_qty = $rm->qty <= $sell_qty ? $rm->qty : $sell_qty;

                  //--- ทำยอดให้เป็นลบเพื่อตัดยอดออก เพราะใน function  ใช้การบวก
                  $qty = $buffer_qty * (-1);

                  //--- 1. ตัดยอดออกจาก buffer
                  //--- นำจำนวนติดลบบวกกลับเข้าไปใน buffer เพื่อตัดยอดให้น้อยลง

                  if($this->buffer_model->update($rm->order_code, $rm->product_code, $rm->zone_code, $qty) !== TRUE)
                  {
                    $sc = FALSE;
                    $message = 'Update buffer failed';
                    break;
                  }

                  //--- ลดยอด sell qty ลงตามยอด buffer ทีลดลงไป
                  $sell_qty += $qty;

                  //--- 2. update movement
                  $arr = array(
                    'reference' => $order->code,
                    'warehouse_code' => $rm->warehouse_code,
                    'zone_code' => $rm->zone_code,
                    'product_code' => $rm->product_code,
                    'move_in' => 0,
                    'move_out' => $buffer_qty,
                    'date_add' => $date_add
                  );

                  if($this->movement_model->add($arr) === FALSE)
                  {
                    $sc = FALSE;
                    $message = 'Update movement failed';
                    break;
                  }

                  $item = $this->products_model->get($rs->product_code);
                  //--- ข้อมูลสำหรับบันทึกยอดขาย
                  $total_amount = $rs->final_price * $buffer_qty;
                  $totalFrgn = convertFC($total_amount, $rs->rate, 1);

                  $total_cost = $rs->cost * $buffer_qty;

                  $arr = array(
                          'reference' => $order->code,
                          'role'   => $order->role,
                          'payment_code'   => $order->payment_code,
                          'channels_code'  => $order->channels_code,
                          'product_code'  => $rs->product_code,
                          'product_name'  => $item->name,
                          'product_style' => $item->style_code,
                          'cost'  => $rs->cost,
                          'price'  => $rs->price,
                          'sell'  => $rs->final_price,
                          'qty'   => $buffer_qty,
                          'currency' => $rs->currency,
                          'rate' => $rs->rate,
                          'discount_label'  => discountLabel($rs->discount1, $rs->discount2, $rs->discount3),
                          'discount_amount' => ($rs->discount_amount * $buffer_qty),
                          'total_amount'   => $total_amount,
                          'total_cost'   => $total_cost,
                          'totalFrgn' => $totalFrgn,
                          'margin'  =>  $totalFrgn > 0 ? $totalFrgn - $total_cost : $total_amount - $total_cost,
                          'id_policy'   => $rs->id_policy,
                          'id_rule'     => $rs->id_rule,
                          'customer_code' => $order->customer_code,
                          'customer_ref' => $order->customer_ref,
                          'sale_code'   => $order->sale_code,
                          'user' => $order->user,
                          'date_add'  => $date_add, //---- เปลี่ยนไปตาม config ORDER_SOLD_DATE
                          'zone_code' => $rm->zone_code,
                          'warehouse_code'  => $rm->warehouse_code,
                          'update_user' => $uname,
                          'budget_code' => $order->budget_code,
                          'empID' => $order->empID,
                          'empName' => $order->empName,
                          'approver' => $order->approver
                  );

                  //--- 3. บันทึกยอดขาย
                  if($this->delivery_order_model->sold($arr) !== TRUE)
                  {
                    $sc = FALSE;
                    $message = 'Insert sale record failed';
                    break;
                  }
                } //--- end if sell_qty > 0
              } //--- end foreach $buffers
            } //--- end if wmpty ($buffers)


            //------ ส่วนนี้สำหรับโอนเข้าคลังระหว่างทำ
            //------ หากเป็นออเดอร์เบิกแปรสภาพ
            if($order->role == 'T' OR $order->role == 'Q')
            {
              //--- ตัวเลขที่มีการเปิดบิล
              $sold_qty = ($rs->order_qty >= $rs->qc) ? $rs->qc : $rs->order_qty;

              //--- ยอดสินค้าที่มีการเชื่อมโยงไว้ในตาราง tbl_order_transform_detail (เอาไว้โอนเข้าคลังระหว่างทำ รอรับเข้า)
              //--- ถ้ามีการเชื่อมโยงไว้ ยอดต้องมากกว่า 0 ถ้ายอดเป็น 0 แสดงว่าไม่ได้เชื่อมโยงไว้
              $trans_list = $this->transform_model->get_transform_product($rs->id);

              if(!empty($trans_list))
              {
                //--- ถ้าไม่มีการเชื่อมโยงไว้
                foreach($trans_list as $ts)
                {
                  //--- ถ้าจำนวนที่เชื่อมโยงไว้ น้อยกว่า หรือ เท่ากับ จำนวนที่ตรวจได้ (ไม่เกินที่สั่งไป)
                  //--- แสดงว่าได้ของครบตามที่ผูกไว้ ให้ใช้ตัวเลขที่ผูกไว้ได้เลย
                  //--- แต่ถ้าได้จำนวนที่ผูกไว้มากกว่าที่ตรวจได้ แสดงว่า ได้สินค้าไม่ครบ ให้ใช้จำนวนที่ตรวจได้แทน
                  $move_qty = $ts->order_qty <= $sold_qty ? $ts->order_qty : $sold_qty;

                  if( $move_qty > 0)
                  {
                    //--- update ยอดเปิดบิลใน tbl_order_transform_detail field sold_qty
                    if($this->transform_model->update_sold_qty($ts->id, $move_qty) === TRUE )
                    {
                      $sold_qty -= $move_qty;
                    }
                    else
                    {
                      $sc = FALSE;
                      $message = 'Failed to update transform item qty';
                    }
                  }
                }
              }
            }


            //--- if lend
            if($order->role == 'L')
            {
              //--- ตัวเลขที่มีการเปิดบิล
              $sold_qty = ($rs->order_qty >= $rs->qc) ? $rs->qc : $rs->order_qty;

              $arr = array(
                'order_code' => $code,
                'product_code' => $rs->product_code,
                'product_name' => $rs->product_name,
                'qty' => $sold_qty,
                'empID' => $order->empID
              );

              if($this->lend_model->add_detail($arr) === FALSE)
              {
                $sc = FALSE;
                $message = 'Failed to update lend item qty';
              }
            }

          } //--- end foreach $bill
        } //--- end if empty($bill)




        //--- เคลียร์ยอดค้างที่จัดเกินมาไปที่ cancle หรือ เคลียร์ยอดที่เป็น 0
        $buffer = $this->buffer_model->get_all_details($code);
        //--- ถ้ายังมีรายการที่ค้างอยู่ใน buffer เคลียร์เข้า cancle
        if(!empty($buffer))
        {
          foreach($buffer as $rs)
          {
            if($rs->qty != 0)
            {
              $arr = array(
                'order_code' => $rs->order_code,
                'product_code' => $rs->product_code,
                'warehouse_code' => $rs->warehouse_code,
                'zone_code' => $rs->zone_code,
                'qty' => $rs->qty,
                'user' => $uname
              );

              if($this->cancle_model->add($arr) === FALSE)
              {
                $sc = FALSE;
                $message = 'Insert Cancel item failed';
                break;
              }
            }

            if($this->buffer_model->delete($rs->id) === FALSE)
            {
              $sc = FALSE;
              $message = 'Delete buffer failed';
              break;
            }
          }
        }


        //--- บันทึกขายรายการที่ไม่นับสต็อก
        $bill = $this->delivery_order_model->get_non_count_bill_detail($order->code);

        if(!empty($bill))
        {
          foreach($bill as $rs)
          {
            //--- ข้อมูลสำหรับบันทึกยอดขาย
            $arr = array(
                    'reference' => $order->code,
                    'role'   => $order->role,
                    'payment_code'   => $order->payment_code,
                    'channels_code'  => $order->channels_code,
                    'product_code'  => $rs->product_code,
                    'product_name'  => $rs->product_name,
                    'product_style' => $rs->style_code,
                    'cost'  => $rs->cost,
                    'price'  => $rs->price,
                    'sell'  => $rs->final_price,
                    'qty'   => $rs->qty,
                    'discount_label'  => discountLabel($rs->discount1, $rs->discount2, $rs->discount3),
                    'discount_amount' => ($rs->discount_amount * $rs->qty),
                    'total_amount'   => $rs->final_price * $rs->qty,
                    'total_cost'   => $rs->cost * $rs->qty,
                    'margin'  => ($rs->final_price * $rs->qty) - ($rs->cost * $rs->qty),
                    'id_policy'   => $rs->id_policy,
                    'id_rule'     => $rs->id_rule,
                    'customer_code' => $order->customer_code,
                    'customer_ref' => $order->customer_ref,
                    'sale_code'   => $order->sale_code,
                    'user' => $order->user,
                    'date_add'  => $date_add, //--- เปลี่ยนตาม Config ORDER_SOLD_DATE
                    'zone_code' => NULL,
                    'warehouse_code'  => NULL,
                    'update_user' => $uname,
                    'budget_code' => $order->budget_code,
                    'is_count' => 0,
                    'empID' => $order->empID,
                    'empName' => $order->empName,
                    'approver' => $order->approver
            );

            //--- 3. บันทึกยอดขาย
            if($this->delivery_order_model->sold($arr) !== TRUE)
            {
              $sc = FALSE;
              $message = 'Insert sale record failed';
              break;
            }
          }

        }

        $this->db->trans_complete();

        if($this->db->trans_status() === FALSE)
        {
          $sc = FALSE;
        }
      } //--- end if state == 7
      else
      {
        $sc = FALSE;
      }
    }
    else
    {
      $sc = FALSE;
      $message = 'Order code not found';
    }

    if($sc === TRUE)
    {
      $this->do_export($code);
    }
    else
    {
      //--- ถ้า error
      $this->orders_model->set_exported($code, 3, $message);
    }

    if($sc === FALSE)
    {
      $arr = array(
        'status' => 3,
        'message' => $message
      );

      $this->update_status($code, $arr);
    }
    else
    {
      $arr = array(
        'status' => 1
      );

      $this->update_status($code, $arr);
    }

    echo $sc === TRUE ? 'success' : $message;
  }


	private function update_status($code, array $ds = array())
	{
		return $this->db->where('code', $code)->update('auto_send_to_sap_order', $ds);
	}

} //--- end class
 ?>

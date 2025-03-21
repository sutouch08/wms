<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Confirm_order
{
  protected $ci;
  public $error;

	public function __construct()
	{
    // Assign the CodeIgniter super-object
    $this->ci =& get_instance();
	}


  public function confirm($code, $uname = NULL)
  {
    $sc = TRUE;
    $this->ci->load->model('inventory/delivery_order_model');
    $this->ci->load->model('inventory/buffer_model');
    $this->ci->load->model('inventory/qc_model');
    $this->ci->load->model('inventory/cancle_model');
    $this->ci->load->model('inventory/movement_model');
    $this->ci->load->model('orders/orders_model');
    $this->ci->load->model('orders/order_state_model');
    $this->ci->load->model('masters/products_model');
    $this->ci->load->helper('discount');

    $order = $this->ci->orders_model->get($code);

    if( ! empty($order))
    {
			$date_add = getConfig('ORDER_SOLD_DATE') == 'D' ? $order->date_add : now();

      if($order->role == 'T' OR $order->role == 'Q')
      {
        $this->ci->load->model('inventory/transform_model');
      }

      if($order->role == 'L')
      {
        $this->ci->load->model('inventory/lend_model');
      }

      if($order->state == 7)
      {
        $this->ci->db->trans_begin();

        //--- change state
       $this->orders_model->change_state($code, 8);

			 if(empty($order->shipped_date))
			 {
				 $this->ci->orders_model->update($code, array('shipped_date' => now())); //--- update shipped date
			 }

        //--- add state event
        $arr = array(
          'order_code' => $code,
          'state' => 8,
          'update_user' => $this->ci->('uname')
        );

        $this->ci->order_state_model->add_state($arr);

        //---- รายการทีรอการเปิดบิล
        $bill = $this->ci->delivery_order_model->get_bill_detail($code);

        if( ! empty($bill))
        {
          foreach($bill as $rs)
          {
            //--- ถ้ามีรายการที่ไมสำเร็จ ออกจาก loop ทันที
            if($sc === FALSE)
            {
              break;
            }

            //--- get prepare and qc
            $rs->qc = $this->ci->qc_model->get_sum_qty($code, $rs->product_code, $rs->id);

            if($rs->qc > 0)
            {
              //--- ถ้ายอดตรวจ น้อยกว่า หรือ เท่ากับ ยอดสั่ง ใช้ยอดตรวจในการตัด buffer
              //--- ถ้ายอดตวจ มากกว่า ยอดสั่ง ให้ใช้ยอดสั่งในการตัด buffer (บางทีอาจมีการแก้ไขออเดอร์หลังจากมีการตรวจสินค้าแล้ว)
              $sell_qty = ($rs->order_qty >= $rs->qc) ? $rs->qc : $rs->order_qty;

              //--- ดึงข้อมูลสินค้าที่จัดไปแล้วตามสินค้า
              $buffers = $this->ci->buffer_model->get_details($code, $rs->product_code, $rs->id);

              if( ! empty($buffers))
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

                    if($this->ci->buffer_model->update($rm->order_code, $rm->product_code, $rm->zone_code, $qty, $rs->id) !== TRUE)
                    {
                      $sc = FALSE;
                      $this->error = 'ปรับยอดใน buffer ไม่สำเร็จ';
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

                    if($this->ci->movement_model->add($arr) === FALSE)
                    {
                      $sc = FALSE;
                      $message = 'บันทึก movement ขาออกไม่สำเร็จ';
                      break;
                    }

                    $item = $this->ci->products_model->get($rs->product_code);

                    //--- ข้อมูลสำหรับบันทึกยอดขาย
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
                      'discount_label'  => discountLabel($rs->discount1, $rs->discount2, $rs->discount3),
                      'discount_amount' => ($rs->discount_amount * $buffer_qty),
                      'total_amount'   => $rs->final_price * $buffer_qty,
                      'total_cost'   => $rs->cost * $buffer_qty,
                      'margin'  =>  ($rs->final_price * $buffer_qty) - ($rs->cost * $buffer_qty),
                      'id_policy'   => $rs->id_policy,
                      'id_rule'     => $rs->id_rule,
                      'customer_code' => $order->customer_code,
                      'customer_ref' => $order->customer_ref,
                      'sale_code'   => $order->sale_code,
                      'user' => $order->user,
                      'date_add'  => $date_add, //---- เปลี่ยนไปตาม config ORDER_SOLD_DATE
                      'zone_code' => $rm->zone_code,
                      'warehouse_code'  => $rm->warehouse_code,
                      'update_user' => get_cookie('uname'),
                      'budget_code' => $order->budget_code,
                      'empID' => $order->empID,
                      'empName' => $order->empName,
                      'approver' => $order->approver,
                      'order_detail_id' => $rs->id
                    );

                    //--- 3. บันทึกยอดขาย
                    if($this->ci->delivery_order_model->sold($arr) !== TRUE)
                    {
                      $sc = FALSE;
                      $this->error = 'บันทึกขายไม่สำเร็จ';
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
                $trans_list = $this->ci->transform_model->get_transform_product($rs->id);

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
                      if($this->ci->transform_model->update_sold_qty($ts->id, $move_qty) === TRUE )
                      {
                        $sold_qty -= $move_qty;
                      }
                      else
                      {
                        $sc = FALSE;
                        $this->error = 'ปรับปรุงยอดรายการค้างรับไม่สำเร็จ';
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

                if($this->ci->lend_model->add_detail($arr) === FALSE)
                {
                  $sc = FALSE;
                  $this->error = 'เพิ่มรายการค้างรับไม่สำเร็จ';
                }
              }
            }
          } //--- end foreach $bill
        } //--- end if empty($bill)

        //--- เคลียร์ยอดค้างที่จัดเกินมาไปที่ cancle หรือ เคลียร์ยอดที่เป็น 0
        $buffer = $this->ci->buffer_model->get_all_details($code);
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
                'user' => get_cookie('uname'),
                'order_detail_id' => $rs->order_detail_id
              );

              if($this->ci->cancle_model->add($arr) === FALSE)
              {
                $sc = FALSE;
                $this->error = 'เคลียร์ยอดค้างเข้า cancle ไม่สำเร็จ';
                break;
              }
            }

            if($this->ci->buffer_model->delete($rs->id) === FALSE)
            {
              $sc = FALSE;
              $this->error = 'ลบ Buffer ที่ค้างอยู่ไม่สำเร็จ';
              break;
            }
          }
        }

        //--- บันทึกขายรายการที่ไม่นับสต็อก
        $bill = $this->ci->delivery_order_model->get_non_count_bill_detail($order->code);

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
              'update_user' => get_cookie('uname'),
              'budget_code' => $order->budget_code,
              'is_count' => 0,
              'empID' => $order->empID,
              'empName' => $order->empName,
              'approver' => $order->approver
            );

            //--- 3. บันทึกยอดขาย
            if($this->ci->delivery_order_model->sold($arr) !== TRUE)
            {
              $sc = FALSE;
              $this->error = 'บันทึกขายไม่สำเร็จ';
              break;
            }
          }
        }

        if($sc === TRUE)
        {
          $doc_total = $this->ci->delivery_order_model->get_billed_amount($code);

          if( ! $this->ci->orders_model->update($code, array('doc_total' => $doc_total)))
          {
            $sc = FALSE;
            $this->error = "Failed to update doc total";
          }
        }

        if($sc === TRUE)
        {
          $this->ci->db->trans_commit();
        }
        else
        {
          $this->ci->db->trans_rollback();
        }
      } //--- end if state == 7
      else
      {
        $sc = FALSE;
        $this->error = "Invalid order status";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = 'order code not found';
    }
  }

  return $sc;
} //--- end class

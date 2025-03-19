<?php
class Soko_auto_delivery extends CI_Controller
{
  public $home;
	public $wms;
  public $mc;
  public $ms;
	public $user;
	public $test_mode = FALSE;
	public $warehouse_code;
	public $zone_code;

  public function __construct()
  {
    parent::__construct();
		$this->wms = $this->load->database('wms', TRUE);
    $this->ms = $this->load->database('ms', TRUE); //--- SAP database
    $this->mc = $this->load->database('mc', TRUE); //--- Temp Database
    $this->home = base_url().'auto/soko_auto_delivery';
		$this->warehouse_code  = getConfig('SOKOJUNG_WAREHOUSE'); //--- คลัง wms
		$this->zone_code = getConfig('SOKOJUNG_ZONE'); //--- โซน wms

		$this->load->model('rest/V1/soko_temp_order_model');
    $this->load->model('rest/V1/soko_temp_tracking_model');
		$this->load->model('orders/orders_model');
    $this->load->model('orders/order_state_model');
    $this->load->model('masters/products_model');
		$this->load->model('masters/channels_model');
		$this->load->model('inventory/prepare_model');
		$this->load->model('inventory/buffer_model');
		$this->load->model('inventory/qc_model');
		$this->load->model('inventory/movement_model');
		$this->load->model('inventory/delivery_order_model');
		$this->load->model('discount/discount_rule_model');
		$this->load->helper('discount');
		$this->user = 'api@sokochan';
  }

  public function index()
  {
		$sc = TRUE;
		$limit = 100;

		$list = $this->soko_temp_order_model->get_unprocess_list($limit);

		if(!empty($list))
		{
      $orderList = array();
      foreach($list as $ro)
      {
        $orderList[] = $ro->code;
      }

      //--- update status to processing
      $this->soko_temp_order_model->processing_status($orderList);

			foreach($list as $data)
			{
				$order = $this->orders_model->get($data->code);

				if( ! empty($order))
				{
					if($order->state == 8 OR $order->state == 7)
					{
						$sc = FALSE;
						$this->error = "Order already delivered";
						$this->soko_temp_order_model->update_status($order->code, 3, $this->error);
					}
					else if($order->state == 9)
					{
						$sc = FALSE;
						$this->error = "Invalid status : Order already canceled";
						$this->soko_temp_order_model->update_status($order->code, 3, $this->error);
					}
					else
					{
						$details = $this->soko_temp_order_model->get_details($data->id);

						if( ! empty($details))
						{
							$channels = $this->channels_model->get($order->channels_code);

              $order->shipped_date = empty($order->shipped_date) ? ( empty($data->shipped_date) ? now() : $data->shipped_date) : $order->shipped_date;

							if((! empty($channels) && $channels->is_online == 1)) // OR ($order->role == 'N' OR $order->role == 'Q' OR $order->role == 'T'))
							{

								//--- บันทึกขาย เซ็ต state = 8  export delivery

                // $this->process_delivery($order, $details);

                if($order->channels_code == 'SHOPEE')
                {
                  if($this->orders_model->has_zero_price($order->code))
                  {
                    //---- ทำให้เป็นรอเปิดบิล
                    $is_hold = 1;
                    $this->process_pre_delivery($order, $details, $is_hold);
                  }
                  else
                  {
                    $this->process_delivery($order, $details);
                  }
                }
                else
                {
                  $this->process_delivery($order, $details);
                }
							}
							else
							{
								//---- ทำให้เป็นรอเปิดบิล
								$this->process_pre_delivery($order, $details);
							}

              $tracking_list = $this->soko_temp_tracking_model->get_tracking_list_by_order_code($order->code);

              if( ! empty($tracking_list))
              {
                //---- add tracking to table order_tracking
                 //-- drop _eixsts
                 if($this->orders_model->drop_tracking_list($order->code))
                 {
                   $last_tk = NULL;

                   foreach($tracking_list as $tk)
                   {
                     $arr = array(
                       'order_code' => $tk->order_code,
                       'tracking_no' => $tk->tracking_no,
                       'carton_code' => $tk->carton_code,
                       'product_code' => $tk->product_code,
                       'qty' => $tk->qty,
                       'courier_code' => $tk->courier_code,
                       'courier_name' => $tk->courier_name
                     );

                     $this->orders_model->add_tracking($arr);

                     $last_tk = $tk->tracking_no;
                   }

                   if( ! empty($last_tk))
                   {
                     $this->orders_model->update($order->code, ['shipping_code' => $last_tk]);
                   }
                 }
              } //--- if ! empty tracking list
						}
						else
						{
							$sc = FALSE;

							$this->soko_temp_order_model->update_status($data->code, 3, "Order not found");
						}
					}

				}
				else  //--- end if !empty($order)
				{
					$this->soko_temp_order_model->update_status($data->code, 3, "Order not found");
				}//--- end if !empty($order)

			} //-- end foreach $list as $data
		}

		return $sc;
  }







	//---- set state to 7 รอจัดส่ง
	public function process_pre_delivery($order, $details, $is_hold = 0)
	{
		$sc = TRUE;

		$this->db->trans_begin();

	  //--- change state
    $arr = array(
      'state' => 7,
      'shipped_date' => $order->shipped_date,
      'update_user' => $this->user,
      'is_hold' => $is_hold
    );

    $this->orders_model->update($order->code, $arr);

		//--- add state event
		$arr = array(
			'order_code' => $order->code,
			'state' => 7,
			'update_user' => $this->user
		);

		$this->order_state_model->add_state($arr);

		//--- drop current prepare
		$this->prepare_model->drop_prepare($order->code);

		//--- drop current buffer
		$this->buffer_model->drop_buffer($order->code);

		//--- drop current qc
		$this->qc_model->drop_qc($order->code);

		foreach($details as $rs)
		{

			if($sc === FALSE)
			{
				break;
			}

			$ds = $this->orders_model->get_order_detail($order->code, $rs->product_code);

			if( ! empty($ds))
			{
				$item = $this->products_model->get($rs->product_code);

				if(!empty($item))
				{
          $qty = $rs->qty;

          foreach($ds as $ro)
          {
            if($qty > 0)
            {
              $orderQty = $qty >= $ro->qty ? $ro->qty : $qty;

              $qty = $qty - $orderQty;

              //--- add prepare
              if($sc === TRUE)
              {
                $prepare = array(
                  'order_code' => $order->code,
                  'product_code' => $item->code,
                  'zone_code' => $this->zone_code,
                  'qty' => $orderQty,
                  'user' => $this->user,
                  'order_detail_id' => $ro->id
                );

                if(! $this->prepare_model->add($prepare))
                {
                  $sc = FALSE;
                  $this->error = "Insert Prepare failed {$order->code} : {$item->code}";
                }
              }

              //--- Add buffer
              if($sc === TRUE)
              {
                $buffer = array(
                  'order_code' => $order->code,
                  'product_code' => $item->code,
                  'warehouse_code' => $this->warehouse_code,
                  'zone_code' => $this->zone_code,
                  'qty' => $orderQty,
                  'user' => $this->user,
                  'order_detail_id' => $ro->id
                );

                if(! $this->buffer_model->add($buffer))
                {
                  $sc = FALSE;
                  $this->error = "Insert buffer failed : {$order->code} : {$rs->product_code}";
                }
              }

              //---- insert Qc
    					if($sc === TRUE)
    					{
    						$qc = array(
    							'order_code' => $order->code,
    							'product_code' => $item->code,
    							'qty' => $orderQty,
    							'box_id' => NULL,
    							'user' => $this->user,
                  'order_detail_id' => $ro->id
    						);

    						if(!$this->qc_model->add($qc))
    						{
    							$sc = FALSE;
    							$this->error = "Insert Qc data failed : {$order->code} : {$rs->product_code}";
    						}
    					}
            } //--- end if qty > 0
          } //-- end foreach
				}
				else
				{
					$sc = FALSE;
					$this->error = "Invalid SKU : {$rs->product_code}";
					break;
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Update failed : No Item Code '{$rs->product_code}' In Order List";
				break;
			}

		} //--- end foreach


		if($sc === TRUE)
		{
			$this->db->trans_commit();
			$this->soko_temp_order_model->update_status($order->code, 1, NULL);
		}
		else
		{
			$this->db->trans_rollback();
			$this->soko_temp_order_model->update_status($order->code, 3, $this->error);
		}

		return $sc;
	}






	//--- set state to 8 จัดส่งแล้ว
	public function process_delivery($order, $details)
	{
		$sc = TRUE;

		if($order->role == 'T' OR $order->role == 'Q')
		{
			$this->load->model('inventory/transform_model');
		}

		if($order->role == 'L')
		{
			$this->load->model('inventory/lend_model');
		}

		$this->db->trans_begin();

		$date_add = getConfig('ORDER_SOLD_DATE') == 'D' ? $order->date_add : (empty($order->shipped_date) ? now() : $order->shipped_date);
		//--- change state
	  $this->orders_model->change_state($order->code, 8);

		//--- add state event
		$arr = array(
			'order_code' => $order->code,
			'state' => 8,
			'update_user' => $this->user
		);

		$this->order_state_model->add_state($arr);

		//--- drop current prepare
		$this->prepare_model->drop_prepare($order->code);

		//--- drop current buffer
		$this->buffer_model->drop_buffer($order->code);

		//--- drop current qc
		$this->qc_model->drop_qc($order->code);

		foreach($details as $rs)
		{

			if($sc === FALSE)
			{
				break;
			}

			$ds = $this->orders_model->get_order_detail($order->code, $rs->product_code);

			if( ! empty($ds))
			{
				$item = $this->products_model->get($rs->product_code);

				if(!empty($item))
				{
          $qty = $rs->qty;

          foreach($ds as $ro)
          {
            if($qty > 0)
            {
              $orderQty = $qty >= $ro->qty ? $ro->qty : $qty;
              $qty = $qty - $orderQty;

              //--- add prepare
              if($sc === TRUE)
              {
                $prepare = array(
                  'order_code' => $order->code,
                  'product_code' => $item->code,
                  'zone_code' => $this->zone_code,
                  'qty' => $orderQty,
                  'user' => $this->user,
                  'order_detail_id' => $ro->id
                );

                if(! $this->prepare_model->add($prepare))
                {
                  $sc = FALSE;
                  $this->error = "Insert Prepare failed {$order->code} : {$item->code}";
                }
              }

              //---- insert Qc
              if($sc === TRUE)
              {
                $qc = array(
                  'order_code' => $order->code,
                  'product_code' => $item->code,
                  'qty' => $orderQty,
                  'box_id' => NULL,
                  'user' => $this->user,
                  'order_detail_id' => $ro->id
                );

                if(!$this->qc_model->add($qc))
                {
                  $sc = FALSE;
                  $this->error = "Insert Qc data failed : {$order->code} : {$rs->product_code}";
                }
              }

              $sell_price = ($ro->qty > 0) ? round($ro->total_amount/$ro->qty, 2) : $ro->price;
              $discount_amount = ($ro->qty > 0) ? round($ro->discount_amount/$ro->qty, 2) : 0;
              $id_policy = empty($ro->id_rule) ? NULL : $this->discount_rule_model->get_policy_id($ro->id_rule);

              //--- ข้อมูลสำหรับบันทึกยอดขาย
              $arr = array(
                'reference' => $order->code,
                'role' => $order->role,
                'payment_code'   => $order->payment_code,
                'channels_code'  => $order->channels_code,
                'product_code'  => $ro->product_code,
                'product_name'  => $ro->product_name,
                'product_style' => $ro->style_code,
                'cost'  => $ro->cost,
                'price'  => $ro->price,
                'sell'  => $sell_price,
                'qty'   => $orderQty,
                'discount_label'  => discountLabel($ro->discount1, $ro->discount2, $ro->discount3),
                'discount_amount' => ($discount_amount * $orderQty),
                'total_amount'   => ($sell_price * $orderQty),
                'total_cost'   => ($ro->cost * $orderQty),
                'margin'  =>  ($sell_price * $orderQty) - ($ro->cost * $orderQty),
                'id_policy'   => $id_policy,
                'id_rule'     => $ro->id_rule,
                'customer_code' => $order->customer_code,
                'customer_ref' => $order->customer_ref,
                'sale_code'   => $order->sale_code,
                'user' => $order->user,
                'date_add'  => $date_add,
                'zone_code' => $this->zone_code,
                'warehouse_code'  => $this->warehouse_code,
                'update_user' => $this->user,
                'budget_code' => $order->budget_code,
                'is_count' => 1,
                'empID' => $order->empID,
                'empName' => $order->empName,
                'approver' => $order->approver,
                'order_detail_id' => $ro->id
              );

              //--- 3. บันทึกยอดขาย
              if(! $this->delivery_order_model->sold($arr))
              {
                $sc = FALSE;
                $this->error = "Insert sale data failed : {$order->code} : {$ro->product_code}";
                break;
              }

              if($sc === TRUE)
              {
                //--- 2. update movement
                $arr = array(
                  'reference' => $order->code,
                  'warehouse_code' => $this->warehouse_code,
                  'zone_code' => $this->zone_code,
                  'product_code' => $ro->product_code,
                  'move_in' => 0,
                  'move_out' => $orderQty,
                  'date_add' => $date_add
                );

                if(! $this->movement_model->add($arr))
                {
                  $sc = FALSE;
                  $this->error = "Insert Movement failed";
                  break;
                }
              }
            } //--- end if qty > 0
          } //--- end foreach $ds
				}
				else
				{
					$sc = FALSE;
					$this->error = "Invalid SKU : {$rs->product_code}";
					break;
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Update failed : No Item Code '{$rs->product_code}' In Order List";
				break;
			}

			//------ ส่วนนี้สำหรับโอนเข้าคลังระหว่างทำ
			//------ หากเป็นออเดอร์เบิกแปรสภาพ
			if($order->role == 'T' OR $order->role == 'Q')
			{
				//--- ตัวเลขที่มีการเปิดบิล
				$sold_qty = $rs->qty;

        if( ! empty($ds))
        {
          foreach($ds as $ro)
          {
            //--- ยอดสินค้าที่มีการเชื่อมโยงไว้ในตาราง tbl_order_transform_detail (เอาไว้โอนเข้าคลังระหว่างทำ รอรับเข้า)
            //--- ถ้ามีการเชื่อมโยงไว้ ยอดต้องมากกว่า 0 ถ้ายอดเป็น 0 แสดงว่าไม่ได้เชื่อมโยงไว้
            $trans_list = $this->transform_model->get_transform_product($ro->id);

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
                  if($this->transform_model->update_sold_qty($ts->id, $move_qty))
                  {
                    $sold_qty -= $move_qty;
                  }
                  else
                  {
                    $sc = FALSE;
                    $this->error = 'ปรับปรุงยอดรายการค้างรับไม่สำเร็จ';
                    break;
                  }
                }
              }
            }
          }
        }
			}

			//--- if lend
			if($order->role == 'L')
			{
				//--- ตัวเลขที่มีการเปิดบิล
				$sold_qty = $rs->qty;

				$arr = array(
					'order_code' => $order->code,
					'product_code' => $ds->product_code,
					'product_name' => $ds->product_name,
					'qty' => $sold_qty,
					'empID' => $order->empID
				);

				if($this->lend_model->add_detail($arr) === FALSE)
				{
					$sc = FALSE;
					$this->error = 'เพิ่มรายการค้างรับไม่สำเร็จ';
				}
			}
		} //---- end count detaail


		$uncount_details = $this->orders_model->get_order_uncount_details($order->code);

		if(!empty($uncount_details))
		{
			foreach($uncount_details as $ds)
			{
				$sell_price = ($ds->qty > 0) ? round($ds->total_amount/$ds->qty, 2) : $ds->price;
				$discount_amount = ($ds->qty > 0) ? round($ds->discount_amount/$ds->qty, 2) : 0;
				$id_policy = empty($ds->id_rule) ? NULL : $this->discount_rule_model->get_policy_id($ds->id_rule);

        //--- ข้อมูลสำหรับบันทึกยอดขาย
        $arr = array(
          'reference' => $order->code,
          'role'   => $order->role,
          'payment_code'   => $order->payment_code,
          'channels_code'  => $order->channels_code,
          'product_code'  => $ds->product_code,
          'product_name'  => $ds->product_name,
          'product_style' => $ds->style_code,
          'cost'  => $ds->cost,
          'price'  => $ds->price,
          'sell'  => $sell_price,
          'qty'   => $ds->qty,
          'discount_label'  => discountLabel($ds->discount1, $ds->discount2, $ds->discount3),
          'discount_amount' => ($discount_amount * $ds->qty),
          'total_amount'   => ($sell_price * $ds->qty),
          'total_cost'   => ($ds->cost * $ds->qty),
          'margin'  =>  ($sell_price * $ds->qty) - ($ds->cost * $ds->qty),
          'id_policy'   => $id_policy,
          'id_rule'     => $ds->id_rule,
          'customer_code' => $order->customer_code,
          'customer_ref' => $order->customer_ref,
          'sale_code'   => $order->sale_code,
          'user' => $order->user,
          'date_add'  => $date_add,
          'zone_code' => NULL,
          'warehouse_code'  => NULL,
          'update_user' => $this->user,
          'budget_code' => $order->budget_code,
          'is_count' => 0,
          'empID' => $order->empID,
          'empName' => $order->empName,
          'approver' => $order->approver,
          'order_detail_id' => $ds->id
        );

				//--- 3. บันทึกยอดขาย
				if(! $this->delivery_order_model->sold($arr))
				{
					$sc = FALSE;
					$this->error = "Insert sale data failed : {$order->code} : {$ds->product_code}";
					break;
				}
			} //--- end foreach non count
		} //--- end if ! empty non count detail

		if($sc === TRUE)
		{
			$this->orders_model->update($order->code, array('shipped_date' => $date_add)); //--- update shipped
		}

		if($sc === TRUE)
		{
			$this->db->trans_commit();
			$this->soko_temp_order_model->update_status($order->code, 1, NULL);
		}
		else
		{
			$this->db->trans_rollback();
			$this->soko_temp_order_model->update_status($order->code, 3, $this->error);
		}

		if($sc === TRUE)
		{
			$this->do_export($order->code);
		}

		return $sc;
	}


	public function do_delivery()
	{
		$sc = $this->index();

		echo $sc === TRUE ? 'success' : $this->error;
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
		$order = $this->orders_model->get($code);
		$sc = TRUE;
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
} //--- end class
 ?>

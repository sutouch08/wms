<?php
class Wms_auto_delivery_order extends CI_Controller
{
  public $home;
	public $wms;
  public $mc;
  public $ms;
	public $user;
	public $test_mode = FALSE;

  public function __construct()
  {
    parent::__construct();
		$this->wms = $this->load->database('wms', TRUE);
    $this->ms = $this->load->database('ms', TRUE); //--- SAP database
    $this->mc = $this->load->database('mc', TRUE); //--- Temp Database
    $this->home = base_url().'auto/wms_auto_delivery_order';

		$this->load->model('rest/V1/wms_temp_order_model');
		$this->load->model('orders/orders_model');
    $this->load->model('orders/order_state_model');
    $this->load->model('masters/products_model');
		$this->load->model('inventory/movement_model');
		$this->load->model('inventory/delivery_order_model');
		$this->load->model('discount/discount_rule_model');
		$this->load->model('rest/V1/wms_order_import_logs_model');
		$this->load->helper('discount');

		$this->user = 'api@wms';
  }

  public function index()
  {
		$sc = TRUE;
		$limit = 10;

		$list = $this->wms_temp_order_model->get_unprocess_list($limit);

		if(!empty($list))
		{
			foreach($list as $data)
			{
				$order = $this->orders_model->get($data->code);

				if(!empty($order))
				{

					if($order->state == 8)
					{
						$sc = FALSE;
						$this->error = "Order already delivered";
						$this->wms_order_import_logs_model->add($order->code, 'E', $this->error);
						$this->wms_temp_order_model->update_status($order->code, 3, $this->error);
					}
					else if($order->state == 9)
					{
						$sc = FALSE;
						$this->error = "Invalid status : Order already canceled";
						$this->wms_order_import_logs_model->add($order->code, 'E', $this->error);
						$this->wms_temp_order_model->update_status($order->code, 3, $this->error);
					}
					else
					{
						$details = $this->wms_temp_order_model->get_details($data->id);

						if(!empty($details))
						{
							if($order->role == 'T' OR $order->role == 'Q')
				      {
				        $this->load->model('inventory/transform_model');
				      }

				      if($order->role == 'L')
				      {
				        $this->load->model('inventory/lend_model');
				      }

							$this->db->trans_begin();

							//--- change state
						 $this->orders_model->change_state($order->code, 8);

							//--- add state event
							$arr = array(
								'order_code' => $order->code,
								'state' => 8,
								'update_user' => $this->user
							);

							$this->order_state_model->add_state($arr);

							$warehouse_code = getConfig('WMS_WAREHOUSE'); //--- คลัง wms
							$zone_code = getConfig('WMS_ZONE'); //--- โซน wms

							foreach($details as $rs)
							{

								if($sc === FALSE)
								{
									break;
								}

								$ds = $this->orders_model->get_order_detail($order->code, $rs->product_code);
								if(!empty($ds))
								{
									$item = $this->products_model->get($rs->product_code);

									if(!empty($item))
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
														'qty'   => $rs->qty,
														'discount_label'  => discountLabel($ds->discount1, $ds->discount2, $ds->discount3),
														'discount_amount' => ($discount_amount * $rs->qty),
														'total_amount'   => ($sell_price * $rs->qty),
														'total_cost'   => ($ds->cost * $rs->qty),
														'margin'  =>  ($sell_price * $rs->qty) - ($ds->cost * $rs->qty),
														'id_policy'   => $id_policy,
														'id_rule'     => $ds->id_rule,
														'customer_code' => $order->customer_code,
														'customer_ref' => $order->customer_ref,
														'sale_code'   => $order->sale_code,
														'user' => $order->user,
														'date_add'  => $order->date_add,
														'zone_code' => $zone_code,
														'warehouse_code'  => $warehouse_code,
														'update_user' => $this->user,
														'budget_code' => $order->budget_code,
														'is_count' => 1,
														'empID' => $order->empID,
														'empName' => $order->empName,
														'approver' => $order->approver
													);

										//--- 3. บันทึกยอดขาย
										if(! $this->delivery_order_model->sold($arr))
										{
											$sc = FALSE;
											$this->error = "Insert sale data failed : {$order->code} : {$ds->product_code}";
											$this->wms_order_import_logs_model->add($order->code, 'E', $this->error);
											break;
										}

										if($sc === TRUE)
										{
											//--- 2. update movement
											$arr = array(
												'reference' => $order->code,
												'warehouse_code' => $warehouse_code,
												'zone_code' => $zone_code,
												'product_code' => $ds->product_code,
												'move_in' => 0,
												'move_out' => $rs->qty,
												'date_add' => $order->date_add
											);

											if(! $this->movement_model->add($arr))
											{
												$sc = FALSE;
												$this->error = "Insert Movement failed";
												$this->wms_order_import_logs_model->add($order->code, 'E', $this->error);
												break;
											}
										}
									}
									else
									{
										$sc = FALSE;
										$this->error = "Invalid SKU : {$rs->product_code}";
										$this->wms_order_import_logs_model->add($order->code, 'E', $this->error);
										break;
									}

								}
								else
								{
									$sc = FALSE;
									$this->error = "Update failed : No Item Code '{$rs->product_code}' In Order List";
									$this->wms_order_import_logs_model->add($order->code, 'E', $this->error);
									break;
								}

								//------ ส่วนนี้สำหรับโอนเข้าคลังระหว่างทำ
								//------ หากเป็นออเดอร์เบิกแปรสภาพ
								if($order->role == 'T' OR $order->role == 'Q')
								{
									//--- ตัวเลขที่มีการเปิดบิล
									$sold_qty = $rs->qty;

									//--- ยอดสินค้าที่มีการเชื่อมโยงไว้ในตาราง tbl_order_transform_detail (เอาไว้โอนเข้าคลังระหว่างทำ รอรับเข้า)
									//--- ถ้ามีการเชื่อมโยงไว้ ยอดต้องมากกว่า 0 ถ้ายอดเป็น 0 แสดงว่าไม่ได้เชื่อมโยงไว้
									$trans_list = $this->transform_model->get_transform_product($ds->id);

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
													$this->wms_order_import_logs_model->add($order->code, 'E', $this->error);
													break;
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
										$this->wms_order_import_logs_model->add($order->code, 'E', $this->error);
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
													'date_add'  => $order->date_add,
													'zone_code' => NULL,
													'warehouse_code'  => NULL,
													'update_user' => $this->user,
													'budget_code' => $order->budget_code,
													'is_count' => 0,
													'empID' => $order->empID,
													'empName' => $order->empName,
													'approver' => $order->approver
												);

									//--- 3. บันทึกยอดขาย
									if(! $this->delivery_order_model->sold($arr))
									{
										$sc = FALSE;
										$this->error = "Insert sale data failed : {$order->code} : {$ds->product_code}";
										$this->wms_order_import_logs_model->add($order->code, 'E', $this->error);
										break;
									}
								} //--- end foreach non count
							} //--- end if ! empty non count detail


							if($sc === TRUE)
							{
								$this->db->trans_commit();
								$this->wms_order_import_logs_model->add($order->code, 'S', NULL);
								$this->wms_temp_order_model->update_status($order->code, 1, NULL);
							}
							else
							{
								$this->db->trans_rollback();
								$this->wms_order_import_logs_model->add($order->code, 'S', NULL);
								$this->wms_temp_order_model->update_status($order->code, 3, $this->error);
							}

							if($sc === TRUE)
					    {
					      $this->do_export($order->code);
					    }
						}
						else
						{
							$sc = FALSE;
							$this->wms_order_import_logs_model->add($order->code, 'E', "No Items In Order List");
							$this->wms_temp_order_model->update_status($data->code, 3, "Order not found");
						}
					}

				}
				else  //--- end if !empty($order)
				{
					$this->wms_temp_order_model->update_status($data->code, 3, "Order not found");
					$this->wms_order_import_logs_model->add($data->code, 'E', "Order not found");
				}//--- end if !empty($order)

			} //-- end foreach $list as $data
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

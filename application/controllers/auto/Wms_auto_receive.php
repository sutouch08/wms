<?php
class Wms_auto_receive extends CI_Controller
{
  public $home;
	public $wms;
  public $mc;
  public $ms;
	public $user;
	public $test_mode = FALSE;
	public $error;

  public function __construct()
  {
    parent::__construct();
		$this->wms = $this->load->database('wms', TRUE);
    $this->ms = $this->load->database('ms', TRUE); //--- SAP database
    $this->mc = $this->load->database('mc', TRUE); //--- Temp Database
    $this->home = base_url().'auto/wms_auto_receive';

		$this->load->model('masters/products_model');
		$this->load->model('inventory/movement_model');
		$this->load->model('rest/V1/wms_temp_receive_model');
		$this->load->model('rest/V1/wms_receive_import_logs_model');

		$this->user = 'api@wms';
  }

  public function index()
  {
		$limit = 10;

		$list = $this->wms_temp_receive_model->get_unprocess_list($limit);

		if(!empty($list))
		{
			foreach($list as $data)
			{
				switch($data->type)
				{
					case 'RT' :
						$this->receive_transform($data);
						break;

					case 'RN' :
						$this->return_lend($data);
						break;

					case 'SM' :
						$this->return_order($data);
						break;

					case 'WR' :
						$this->receive_po($data);
						break;

					case 'WW' :
						$this->transfer($data);
						break;

					case 'WX' :
						$this->consign_check($data);
				}
			} //-- end foreach $list as $data
		}

  }


	private function receive_po($data)
	{
		$this->load->model('inventory/receive_po_model');

		$code = $data->code;
		$order = $this->receive_po_model->get($code);

		if(!empty($order))
		{
			$sc = TRUE;

			if($order->status == 1)
			{
				$sc = FALSE;
				$this->error = "Document already received";
				$this->wms_receive_import_logs_model->add($order->code, 'E', $this->error);
				$this->wms_temp_receive_model->update_status($order->code, 3, $this->error);
			}
			else if($order->status == 2)
			{
				$sc = FALSE;
				$this->error = "Invalid status : Document already canceled";
				$this->wms_receive_import_logs_model->add($order->code, 'E', $this->error);
				$this->wms_temp_receive_model->update_status($order->code, 3, $this->error);
			}
			else if($order->status == 0)
			{
				$sc = FALSE;
				$this->error = "Invalid status : Document not saved";
				$this->wms_receive_import_logs_model->add($order->code, 'E', $this->error);
				$this->wms_temp_receive_model->update_status($order->code, 3, $this->error);
			}
			else
			{
				$details = $this->wms_temp_receive_model->get_details($data->id);

				if(!empty($details))
				{
					$this->db->trans_begin();

					$warehouse_code = getConfig('WMS_WAREHOUSE'); //--- คลัง wms
					$zone_code = getConfig('WMS_ZONE'); //--- โซน wms

					foreach($details as $rs)
					{
						if($sc === FALSE)
						{
							break;
						}

						if($rs->qty > 0 && $sc === TRUE)
						{
							$row = $this->receive_po_model->get_detail_by_product($order->code, $rs->product_code);

							if(!empty($row))
							{
								$amount = $row->price * $rs->qty;
								$after_backlogs = $row->before_backlogs - $rs->qty;
								$arr = array(
									'qty' => $rs->qty,
									'amount' => round($amount, 2),
									'after_backlogs' => $after_backlogs,
									'valid' => 1
								);


								if(! $this->receive_po_model->update_detail($row->id, $arr))
								{
									$sc = FALSE;
									$this->error = "Error : Update detail failed : {$rs->product_code}";
								}

								//--- add movement
								if($sc === TRUE)
								{
									$ds = array(
										'reference' => $order->code,
										'warehouse_code' => $warehouse_code,
										'zone_code' => $zone_code,
										'product_code' => $rs->product_code,
										'move_in' => $rs->qty,
										'date_add' => db_date($order->date_add, TRUE)
									);

									if($this->movement_model->add($ds) === FALSE)
									{
										$sc = FALSE;
										$this->error = 'บันทึก movement ไม่สำเร็จ';
									}
								}

							}
							else
							{
								$sc = FALSE;
								$this->error = "Invalid Product code : {$rs->product_code} OR product code not in document";
							}
						}//--- end if qty > 0
					} //--- end foreach

					//--- drop not valid detail
					if($sc === TRUE)
					{
						if(! $this->receive_po_model->drop_not_valid_details($order->code))
						{
							$sc = FALSE;
							$this->error = "Drop not valid detail failed";
						}
					}

					//--- change document status
					if($sc === TRUE)
					{
						if(! $this->receive_po_model->set_status($order->code, 1))
						{
							$sc = FALSE;
							$this->error = "Change document status failed";
						}
					}

					if($sc === TRUE)
					{
						$this->db->trans_commit();
						$this->wms_temp_receive_model->update_status($order->code, 1, 'success');
						$this->wms_receive_import_logs_model->add($order->code, 'S', 'success');
					}
					else
					{
						$this->db->trans_rollback();
						$this->wms_temp_receive_model->update_status($order->code, 3, $this->error);
						$this->wms_receive_import_logs_model->add($order->code, 'E', $this->error);
					}

					if($sc === TRUE)
					{
						$this->export_receive($order->code);
					}

				}
				else
				{
					$sc = FALSE;
					$this->wms_temp_receive_model->update_status($order->code, 3, "No Items In Order List");
					$this->wms_receive_import_logs_model->add($order->code, 'E', "No Items In Order List");
				}
			}
		}
		else
		{
			$this->wms_temp_receive_model->update_status($code, 3, "Order not found");
			$this->wms_receive_import_logs_model->add($code, 'E', "Order not found");
		}//--- end if !empty($order)
	}



	private function return_order($data)
	{
		$this->load->model('inventory/return_order_model');
		$code = $data->code;
		$order = $this->return_order_model->get($code);

		if(!empty($order))
		{
			$sc = TRUE;

			if($order->status == 1)
			{
				$sc = FALSE;
				$this->error = "Document already received";
				$this->wms_receive_import_logs_model->add($order->code, 'E', $this->error);
				$this->wms_temp_receive_model->update_status($order->code, 3, $this->error);
			}
			else if($order->status == 2)
			{
				$sc = FALSE;
				$this->error = "Invalid status : Document already canceled";
				$this->wms_receive_import_logs_model->add($order->code, 'E', $this->error);
				$this->wms_temp_receive_model->update_status($order->code, 3, $this->error);
			}
			else if($order->status == 0)
			{
				$sc = FALSE;
				$this->error = "Invalid status : Document not saved";
				$this->wms_receive_import_logs_model->add($order->code, 'E', $this->error);
				$this->wms_temp_receive_model->update_status($order->code, 3, $this->error);
			}
			else
			{
				$details = $this->wms_temp_receive_model->get_details($data->id);

				if(!empty($details))
				{

					$this->db->trans_begin();

					foreach($details as $rs)
					{
						if($rs->qty > 0 && $sc === TRUE)
						{
							$row = $this->return_order_model->get_detail_by_product($order->code, $rs->product_code);

							if(!empty($row))
							{
	              $disc_amount = $row->discount_percent == 0 ? 0 : $rs->qty * ($row->price * ($row->discount_percent * 0.01));
	              $amount = ($rs->qty * $row->price) - $disc_amount;

	              $arr = array(
	                'qty' => round($rs->qty, 2),
	                'amount' => $amount,
	                'vat_amount' => get_vat_amount($amount),
									'valid' => 1
	              );


								if($this->return_order_model->update_detail($row->id, $arr) === FALSE)
								{
									$sc = FALSE;
									$this->error = 'Update detail failed';
									break;
								}

								//--- update movement
								if($sc === TRUE)
								{
									$ds = array(
										'reference' => $order->code,
										'warehouse_code' => $order->warehouse_code,
										'zone_code' => $order->zone_code,
										'product_code' => $row->product_code,
										'move_in' => $rs->qty,
										'date_add' => db_date($order->date_add, TRUE)
									);

									if($this->movement_model->add($ds) === FALSE)
									{
										$sc = FALSE;
										$this->error = 'บันทึก movement ไม่สำเร็จ';
									}
								}
							} //---
						}//--- end if qty > 0
					} //--- end foreach

					if($sc === TRUE)
					{
						//---- drop not valid detail
						if(! $this->return_order_model->drop_not_valid_details($order->code))
						{
							$sc = FALSE;
							$this->error = "ลบรายการที่ไม่มีจำนวนไม่สำเร็จ";
						}
						else
						{
							//--- เปลี่ยนสถานะเอกสาร
							$arr = array(
								'status' => 1,
								'is_complete' => 1
							);

							$this->return_order_model->update($order->code, $arr);
						}
					}

					if($sc === TRUE)
					{
						$this->db->trans_commit();
						$this->wms_receive_import_logs_model->add($order->code, 'S', 'success');
						$this->wms_temp_receive_model->update_status($order->code, 1, 'success');
					}
					else
					{
						$this->db->trans_rollback();
						$this->wms_temp_receive_model->update_status($order->code, 3, $this->error);
						$this->wms_receive_import_logs_model->add($order->code, 'E', $this->error);
					}

					if($sc === TRUE)
					{
						$this->export_return($order->code);
					}
				}
				else
				{
					$sc = FALSE;
					$this->wms_temp_receive_model->update_status($order->code, 3, "No Items In Order List");
					$this->wms_receive_import_logs_model->add($order->code, 'E', "No Items In Order List");
				}
			}
		}
		else
		{
			$this->wms_temp_receive_model->update_status($code, 3, "Order not found");
			$this->wms_receive_import_logs_model->add($code, 'E', "Order not found");
		}//--- end if !empty($order)
	}



	private function return_lend($data)
	{
		$this->load->model('inventory/return_lend_model');
		$code = $data->code;
		$order = $this->return_lend_model->get($code);

		if(!empty($order))
		{
			$sc = TRUE;

			if($order->status == 1)
			{
				$sc = FALSE;
				$this->error = "Document already received";
				$this->wms_receive_import_logs_model->add($order->code, 'E', $this->error);
				$this->wms_temp_receive_model->update_status($order->code, 3, $this->error);
			}
			else if($order->status == 2)
			{
				$sc = FALSE;
				$this->error = "Invalid status : Document already canceled";
				$this->wms_receive_import_logs_model->add($order->code, 'E', $this->error);
				$this->wms_temp_receive_model->update_status($order->code, 3, $this->error);
			}
			else if($order->status == 0)
			{
				$sc = FALSE;
				$this->error = "Invalid status : Document not saved";
				$this->wms_receive_import_logs_model->add($order->code, 'E', $this->error);
				$this->wms_temp_receive_model->update_status($order->code, 3, $this->error);
			}
			else
			{
				$details = $this->wms_temp_receive_model->get_details($data->id);

				if(!empty($details))
				{

					$this->db->trans_begin();

					foreach($details as $rs)
					{
						if($rs->qty > 0 && $sc === TRUE)
						{
							$row = $this->return_lend_model->get_detail_by_product($order->code, $rs->product_code);

							if(!empty($row))
							{
	              $amount = ($rs->qty * $row->price);

	              $arr = array(
	                'qty' => round($rs->qty, 2),
	                'amount' => $amount,
	                'vat_amount' => get_vat_amount($amount),
									'valid' => 1
	              );


								if($this->return_lend_model->update_detail($row->id, $arr) === FALSE)
								{
									$sc = FALSE;
									$this->error = 'Update detail failed';
									break;
								}

								//--- update movement in / out
								if($sc === TRUE)
								{
									$move_out = array(
										'reference' => $order->code,
										'warehouse_code' => $order->from_warehouse,
										'zone_code' => $order->from_zone,
										'product_code' => $row->product_code,
										'move_out' => $rs->qty,
										'date_add' => db_date($order->date_add, TRUE)
									);

									$move_in = array(
										'reference' => $order->code,
										'warehouse_code' => $order->to_warehouse,
										'zone_code' => $order->to_zone,
										'product_code' => $row->product_code,
										'move_in' => $rs->qty,
										'date_add' => db_date($order->date_add, TRUE)
									);

									if($this->movement_model->add($move_out) === FALSE)
									{
										$sc = FALSE;
										$this->error = 'บันทึก movement ออกไม่สำเร็จ';
									}

									if($this->movement_model->add($move_in) === FALSE)
									{
										$sc = FALSE;
										$this->error = 'บันทึก movement เข้าไม่สำเร็จ';
									}

									if(!$this->return_lend_model->update_receive($order->lend_code, $row->product_code, $rs->qty))
		              {
		                $sc = FALSE;
		                $this->error = "Update ยอดรับไม่สำเร็จ {$rs->product_code}";
		              }
								}
							} //---
						}//--- end if qty > 0
					} //--- end foreach

					if($sc === TRUE)
					{
						//---- drop not valid detail
						if(! $this->return_lend_model->drop_not_valid_details($order->code))
						{
							$sc = FALSE;
							$this->error = "ลบรายการที่ไม่มีจำนวนไม่สำเร็จ";
						}
						else
						{
							//--- เปลี่ยนสถานะเอกสาร
							$arr = array(
								'status' => 1
							);

							$this->return_lend_model->update($order->code, $arr);
						}
					}

					if($sc === TRUE)
					{
						$this->db->trans_commit();
						$this->wms_temp_receive_model->update_status($order->code, 1, 'success');
						$this->wms_receive_import_logs_model->add($order->code, 'S', 'success');
					}
					else
					{
						$this->db->trans_rollback();
						$this->wms_receive_import_logs_model->add($order->code, 'E', $this->error);
						$this->wms_temp_receive_model->update_status($order->code, 3, $this->error);
					}

					if($sc === TRUE)
					{
						$this->export_return_lend($order->code);
					}
				}
				else
				{
					$sc = FALSE;
					$this->wms_temp_receive_model->update_status($order->code, 3, "No Items In Order List");
					$this->wms_receive_import_logs_model->add($order->code, 'E', "No Items In Order List");
				}
			}
		}
		else
		{
			$this->wms_temp_receive_model->update_status($code, 3, "Order not found");
			$this->wms_receive_import_logs_model->add($code, 'E', "Order not found");
		}//--- end if !empty($order)
	}


	private function receive_transform($data)
	{
		$this->load->model('inventory/receive_transform_model');
		$this->load->model('inventory/transform_model');
		$code = $data->code;
		$order = $this->receive_transform_model->get($code);

		if(!empty($order))
		{
			$sc = TRUE;

			if($order->status == 1)
			{
				$sc = FALSE;
				$this->error = "Document already received";
				$this->wms_receive_import_logs_model->add($order->code, 'E', $this->error);
				$this->wms_temp_receive_model->update_status($order->code, 3, $this->error);
			}
			else if($order->status == 2)
			{
				$sc = FALSE;
				$this->error = "Invalid status : Document already canceled";
				$this->wms_receive_import_logs_model->add($order->code, 'E', $this->error);
				$this->wms_temp_receive_model->update_status($order->code, 3, $this->error);
			}
			else if($order->status == 0)
			{
				$sc = FALSE;
				$this->error = "Invalid status : Document not saved";
				$this->wms_receive_import_logs_model->add($order->code, 'E', $this->error);
				$this->wms_temp_receive_model->update_status($order->code, 3, $this->error);
			}
			else
			{
				$details = $this->wms_temp_receive_model->get_details($data->id);

				if(!empty($details))
				{
					$this->db->trans_begin();

					$warehouse_code = getConfig('WMS_WAREHOUSE'); //--- คลัง wms
					$zone_code = getConfig('WMS_ZONE'); //--- โซน wms

					//--- ลบรายการเก่าก่อนเพิ่มรายการใหม่
					$this->receive_transform_model->drop_details($order->code);

					foreach($details as $rs)
					{
						if($rs->qty > 0 && $sc === TRUE)
						{
							$pd = $this->products_model->get($rs->product_code);
							if(!empty($pd))
							{
								$price = $this->get_avg_cost($pd->code);
								$cost = $price == 0 ? $pd->cost : $price;
								$ds = array(
									'receive_code' => $order->code,
									'style_code' => $pd->style_code,
									'product_code' => $pd->code,
									'product_name' => $pd->name,
									'price' => $cost,
									'qty' => $rs->qty,
									'amount' => $rs->qty * $cost
								);

								if($this->receive_transform_model->add_detail($ds) === FALSE)
								{
									$sc = FALSE;
									$this->error = 'Add Receive Row Fail';
									break;
								}

								if($sc === TRUE)
								{
									$ds = array(
										'reference' => $order->code,
										'warehouse_code' => $warehouse_code,
										'zone_code' => $zone_code,
										'product_code' => $pd->code,
										'move_in' => $rs->qty,
										'date_add' => db_date($order->date_add, TRUE)
									);

									if($this->movement_model->add($ds) === FALSE)
									{
										$sc = FALSE;
										$this->error = 'บันทึก movement ไม่สำเร็จ';
									}
								}


								//--- update receive_qty in order_transform_detail
								if($sc === TRUE)
								{
									$this->update_transform_receive_qty($order->order_code, $pd->code, $rs->qty);
								}
							}
							else
							{
								$sc = FALSE;
								$this->error = "Invalid Product code : {$rs->product_code}";
							}
						}//--- end if qty > 0
					} //--- end foreach

					if($sc === TRUE)
					{
						$this->receive_transform_model->set_status($order->code, 1);

						if($this->transform_model->is_complete($order->order_code) === TRUE)
						{
							$this->transform_model->close_transform($order->order_code);
						}
					}

					if($sc === TRUE)
					{
						$this->db->trans_commit();
						$this->wms_temp_receive_model->update_status($order->code, 1, 'success');
						$this->wms_receive_import_logs_model->add($order->code, 'S', 'success');
					}
					else
					{
						$this->db->trans_rollback();
						$this->wms_temp_receive_model->update_status($order->code, 3, $this->error);
						$this->wms_receive_import_logs_model->add($order->code, 'E', $this->error);
					}

					if($sc === TRUE)
					{
						$this->export_receive_transform($order->code);
					}

				}
				else
				{
					$sc = FALSE;
					$this->wms_temp_receive_model->update_status($order->code, 3, "No Items In Order List");
					$this->wms_receive_import_logs_model->add($order->code, 'E', "No Items In Order List");
				}
			}
		}
		else
		{
			$this->wms_temp_receive_model->update_status($code, 3, "Order not found");
			$this->wms_receive_import_logs_model->add($code, 'E', "Order not found");
		}//--- end if !empty($order)
	}


	private function transfer($data)
	{
		$this->load->model('inventory/transfer_model');
		$code = $data->code;
		$order = $this->transfer_model->get($code);

		if(!empty($order))
		{
			$sc = TRUE;

			if($order->status == 1)
			{
				$sc = FALSE;
				$this->error = "Invalid status : Document already received";
				$this->wms_receive_import_logs_model->add($order->code, 'E', $this->error);
				$this->wms_temp_receive_model->update_status($order->code, 3, $this->error);
			}
			else if($order->status == 2)
			{
				$sc = FALSE;
				$this->error = "Invalid status : Document already canceled";
				$this->wms_receive_import_logs_model->add($order->code, 'E', $this->error);
				$this->wms_temp_receive_model->update_status($order->code, 3, $this->error);
			}
			else if($order->status == 0)
			{
				$sc = FALSE;
				$this->error = "Invalid status : Document not saved";
				$this->wms_receive_import_logs_model->add($order->code, 'E', $this->error);
				$this->wms_temp_receive_model->update_status($order->code, 3, $this->error);
			}
			else
			{
				$details = $this->wms_temp_receive_model->get_details($data->id);

				if(!empty($details))
				{
					$this->db->trans_begin();

					foreach($details as $rs)
					{
						if($rs->qty > 0 && $sc === TRUE)
						{
							$des = $this->transfer_model->get_detail_by_product($order->code, $rs->product_code); //--- อาจได้มากกว่า 1 รายการ
							if(!empty($des))
							{
								$all_qty = $rs->qty;

								foreach($des as $de)
								{
									if($all_qty > 0)
									{
										$qty = ($de->qty <= $all_qty) ? $de->qty : $all_qty;

										if($qty > 0)
										{
											$arr = array(
												'wms_qty' => $qty,
												'valid' => $qty == $de->qty ? 1 : 0
											);

											if(! $this->transfer_model->update_detail($de->id, $arr))
											{
												$sc = FALSE;
												$this->error = "Update failed : {$rs->product_code}";
												break;
											}
											else
											{
												//--- add_movement
												//--- 2. update movement
												$move_out = array(
													'reference' => $order->code,
													'warehouse_code' => $order->from_warehouse,
													'zone_code' => $de->from_zone,
													'product_code' => $de->product_code,
													'move_in' => 0,
													'move_out' => $qty,
													'date_add' => $order->date_add
												);

												$move_in = array(
													'reference' => $order->code,
													'warehouse_code' => $order->to_warehouse,
													'zone_code' => $de->to_zone,
													'product_code' => $de->product_code,
													'move_in' => $qty,
													'move_out' => 0,
													'date_add' => $order->date_add
												);

												//--- move out
												if(! $this->movement_model->add($move_out))
												{
													$sc = FALSE;
													$this->error = 'บันทึก movement ขาออกไม่สำเร็จ';
													break;
												}

												//--- move in
												if(! $this->movement_model->add($move_in))
												{
													$sc = FALSE;
													$this->error = 'บันทึก movement ขาเข้าไม่สำเร็จ';
													break;
												}
											}
										}

										$all_qty = $all_qty - $qty;
									} //--- end if all_qty > 0

								} //--- end foreach
							}
							else
							{
								$sc = FALSE;
								$this->error = "Invalid Product code : {$rs->product_code}";
							}
						}//--- end if qty > 0
					} //--- end foreach

					if($sc === TRUE)
					{
						if(!$this->transfer_model->set_status($order->code, 1))
						{
							$sc = FALSE;
							$this->error = "Update failed : change document status failed";
						}
					}

					if($sc === TRUE)
					{
						$this->db->trans_commit();
						$this->wms_temp_receive_model->update_status($order->code, 1, 'success');
						$this->wms_receive_import_logs_model->add($order->code, 'S', 'success');
					}
					else
					{
						$this->db->trans_rollback();
						$this->wms_temp_receive_model->update_status($order->code, 3, $this->error);
						$this->wms_receive_import_logs_model->add($order->code, 'E', $this->error);
					}

					if($sc === TRUE)
					{
						$this->export_transfer($order->code);
					}
				}
				else
				{
					$sc = FALSE;
					$this->wms_temp_receive_model->update_status($order->code, 3, "No Items In Order List");
					$this->wms_receive_import_logs_model->add($order->code, 'E', "No Items In Order List");
				}
			}
		}
		else
		{
			$this->wms_temp_receive_model->update_status($code, 3, "Order not found");
			$this->wms_receive_import_logs_model->add($code, 'E', "Order not found");
		}//--- end if !empty($order)
	}



	private function consign_check($data)
	{
		$this->load->model('inventory/consign_check_model');
		$code = $data->code;
		$order = $this->consign_check_model->get($code);

		if(!empty($order))
		{
			$sc = TRUE;

			if($order->status == 1)
			{
				$sc = FALSE;
				$this->error = "Invalid status : Document already received";
				$this->wms_receive_import_logs_model->add($order->code, 'E', $this->error);
				$this->wms_temp_receive_model->update_status($order->code, 3, $this->error);
			}
			else if($order->status == 2)
			{
				$sc = FALSE;
				$this->error = "Invalid status : Document already canceled";
				$this->wms_receive_import_logs_model->add($order->code, 'E', $this->error);
				$this->wms_temp_receive_model->update_status($order->code, 3, $this->error);
			}
			else if($order->status == 0)
			{
				$sc = FALSE;
				$this->error = "Invalid status : Document not saved";
				$this->wms_receive_import_logs_model->add($order->code, 'E', $this->error);
				$this->wms_temp_receive_model->update_status($order->code, 3, $this->error);
			}
			else
			{
				$details = $this->wms_temp_receive_model->get_details($data->id);

				if(!empty($details))
				{
					$this->db->trans_begin();

					foreach($details as $rs)
					{
						if($rs->qty > 0 && $sc === TRUE)
						{
							$id = $this->consign_check_model->get_detail_by_product($order->code, $rs->product_code);
							if(!empty($id))
							{
								$arr = array('qty' => $rs->qty);

								if(! $this->consign_check_model->update_detail($id, $arr))
								{
									$sc = FALSE;
									$this->error = "Update failed : {$rs->product_code}";
								}
							}
							else
							{
								//--- ถ้าไม่มีในเอกสาร ก็เพิ่มรายการเข้าเอกสาร แต่ยอดตั้งต้นเป็น 0
								$arr = array(
	              'check_code' => $order->code,
	              'product_code' => $rs->product_code,
	              'product_name' => $this->products_model->get_name($rs->product_code),
	              'stock_qty' => 0,
								'qty' => $rs->qty
	              );

								$this->consign_check_model->add_detail($arr);
							}
						}//--- end if qty > 0
					} //--- end foreach

					if($sc === TRUE)
					{
						if(!$this->consign_check_model->change_status($order->code, 1))
						{
							$sc = FALSE;
							$this->error = "Update failed : change document status failed";
						}
					}

					if($sc === TRUE)
					{
						$this->db->trans_commit();
						$this->wms_temp_receive_model->update_status($order->code, 1, 'success');
						$this->wms_receive_import_logs_model->add($order->code, 'S', 'success');
					}
					else
					{
						$this->db->trans_rollback();
						$this->wms_temp_receive_model->update_status($order->code, 3, $this->error);
						$this->wms_receive_import_logs_model->add($order->code, 'E', $this->error);
					}
				}
				else
				{
					$sc = FALSE;
					$this->wms_temp_receive_model->update_status($order->code, 3, "No Items In Order List");
					$this->wms_receive_import_logs_model->add($order->code, 'E', "No Items In Order List");
				}
			}
		}
		else
		{
			$this->wms_temp_receive_model->update_status($code, 3, "Order not found");
			$this->wms_receive_import_logs_model->add($code, 'E', "Order not found");
		}//--- end if !empty($order)
	}


	private function export_receive($code)
	{
		$sc = TRUE;
		$this->load->library('export');
		if(! $this->export->export_receive($code))
		{
			$sc = FALSE;
			$this->error = trim($this->export->error);
		}

		return $sc;
	}


	private function export_receive_transform($code)
	{
		$sc = TRUE;
		$this->load->library('export');
		if(! $this->export->export_receive_transform($code))
		{
			$sc = FALSE;
			$this->error = trim($this->export->error);
		}

		return $sc;
	}


	private function export_return($code)
	{
		$sc = TRUE;
		$this->load->library('export');
		if(! $this->export->export_return($code))
		{
			$sc = FALSE;
			$this->error = trim($this->export->error);
		}

		return $sc;
	}



	private function export_return_lend($code)
	{
		$sc = TRUE;
		$this->load->library('export');
		if(! $this->export->export_return_lend($code))
		{
			$sc = FALSE;
			$this->error = trim($this->export->error);
		}

		return $sc;
	}


	private function export_transfer($code)
	{
		$sc = TRUE;
		$this->load->library('export');
		if(! $this->export->export_transfer($code))
		{
			$sc = FALSE;
			$this->error = trim($this->export->error);
		}
		else
		{
			$this->transfer_model->set_export($code, 1);
		}

		return $sc;
	}


	private function get_avg_cost($code)
	{
		$this->load->model('masters/products_model');
		$cost = $this->products_model->get_sap_item_avg_cost($code);

		if(empty($cost))
		{
			$cost = $this->products_model->get_product_cost($code);
		}

		return $cost;
	}


	//--- update receive_qty in order_transform_detail
  public function update_transform_receive_qty($order_code, $product_code, $qty)
  {
    $sc = TRUE;
    $list = $this->transform_model->get_transform_product_by_code($order_code, $product_code);
    if(!empty($list))
    {
      foreach($list as $rs)
      {
        if($qty > 0)
        {
          $diff = $rs->sold_qty - $rs->receive_qty;
          if($diff > 0 )
          {
            //--- ถ้า dif มากกว่ายอดที่รับมาให้ใช้ยอดรับ
            //--- หากยอดค้าง มี 2 แถว แถวแรก 5 แถวที่ 2 อีก 5 รวมเป็น 10
            //--- แต่รับเข้ามา 8
            //--- รอบแรก ยอด diff = 5 ซึ่งน้อยกว่า ยอดรับ ให้ใช้ยอด diff (ยอดค้างรับของแถวนั้น)
            //--- รอบสอง ยอด diff = 5 แต่ยอดรับจะเหลือ 3 เพราะถูกตัดออกไปรอบแรก 5 (จากยอดรับ 8)
            //--- รอบสองจึงต้องใช้ยอดรับที่เหลือในการ update
            $valid = $qty >= $diff ? TRUE : FALSE;
            $diff = $diff > $qty ? $qty : $diff;
            $this->transform_model->update_receive_qty($rs->id, $diff);
            $qty -= $diff;
            //--- เมื่อลบยอดค้างรับออกแล้วยังเหลือยอดอีกแสดงว่าแถวนี้รับครบแล้ว ให้ update valid เป็น 1
            if($valid)
            {
              $this->transform_model->valid_detail($rs->id);
            }
          }
        } //--- end if qty > 0
      } //--- endforeach
    }
  }

} //--- end class
 ?>

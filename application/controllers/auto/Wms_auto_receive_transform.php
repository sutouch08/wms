<?php
class Wms_auto_receive_transform extends CI_Controller
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
    $this->home = base_url().'auto/wms_auto_receive_transform';

		$this->load->model('rest/V1/wms_temp_receive_model');
		$this->load->model('inventory/receive_transform_model');
    $this->load->model('masters/products_model');
		$this->load->model('inventory/movement_model');
		$this->load->model('rest/V1/wms_receive_import_logs_model');
		$this->load->model('inventory/transform_model');

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
				$order = $this->receive_transform_model->get($data->code);

				if(!empty($order))
				{
					$sc = TRUE;

					if($order->status == 1)
					{
						$sc = FALSE;
						$this->error = "Document already received";
						$this->wms_receive_import_logs_model->add($order->code, 'E', $this->error);
					}
					else if($order->status == 2)
					{
						$sc = FALSE;
						$this->error = "Invalid status : Document already canceled";
						$this->wms_receive_import_logs_model->add($order->code, 'E', $this->error);
					}
					else if($order->status == 0)
					{
						$sc = FALSE;
						$this->error = "Invalid status : Document not saved";
						$this->wms_receive_import_logs_model->add($order->code, 'E', $this->error);
					}
					else
					{
						$details = $this->wms_temp_receive_model->get_details($order->code);

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
			                $message = 'Add Receive Row Fail';
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
			                  $message = 'บันทึก movement ไม่สำเร็จ';
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
							}
							else
							{
								$this->db->trans_rollback();
							}

							$this->export_receive($order->code);
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
					$this->wms_temp_receive_model->update_status($data->code, 3, "Order not found");
					$this->wms_receive_import_logs_model->add($data->code, 'E', "Order not found");
				}//--- end if !empty($order)

			} //-- end foreach $list as $data
		}
  }



	private function export_receive($code)
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

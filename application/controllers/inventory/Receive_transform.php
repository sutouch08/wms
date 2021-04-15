<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Receive_transform extends PS_Controller
{
  public $menu_code = 'ICTRRC';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'RECEIVE';
	public $title = 'รับสินค้าจากการแปรสภาพ';
  public $filter;
  public $error;
	public $wms;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/receive_transform';
    $this->load->model('inventory/receive_transform_model');
    $this->load->model('inventory/transform_model');
  }


  public function index()
  {
    $this->load->helper('channels');
    $filter = array(
      'code'    => get_filter('code', 'trans_code', ''),
      'invoice' => get_filter('invoice', 'trans_invoice', ''),
      'order_code' => get_filter('order_code', 'trans_order_code', ''),
      'from_date' => get_filter('from_date', 'trans_from_date', ''),
      'to_date' => get_filter('to_date', 'trans_to_date', ''),
      'status' => get_filter('status', 'trans_status', 'all'),
			'is_wms' => get_filter('is_wms', 'trans_is_wms', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->receive_transform_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$document = $this->receive_transform_model->get_data($filter, $perpage, $this->uri->segment($segment));

    if(!empty($document))
    {
      foreach($document as $rs)
      {
        $rs->qty = $this->receive_transform_model->get_sum_qty($rs->code);
      }
    }

    $filter['document'] = $document;

		$this->pagination->initialize($init);
    $this->load->view('inventory/receive_transform/receive_transform_list', $filter);
  }



  public function view_detail($code)
  {
    $this->load->model('masters/zone_model');
    $this->load->model('masters/products_model');

    $doc = $this->receive_transform_model->get($code);
    if(!empty($doc))
    {
      $doc->zone_name = $this->zone_model->get_name($doc->zone_code);
    }

    $details = $this->receive_transform_model->get_details($code);

    $ds = array(
      'doc' => $doc,
      'details' => $details
    );

    $this->load->view('inventory/receive_transform/receive_transform_detail', $ds);
  }



  public function print_detail($code)
  {
    $this->load->library('printer');
    $this->load->model('masters/zone_model');
    $this->load->model('masters/products_model');
    $this->load->model('orders/orders_model');

    $doc = $this->receive_transform_model->get($code);
    //$order = $this->orders_model->get($doc->order_code);
    if(!empty($doc))
    {
      $zone = $this->zone_model->get($doc->zone_code);
      $doc->zone_name = $zone->name;
      $doc->warehouse_name = $zone->warehouse_name;
      //$doc->requester = $this->user_model->get_name($order->user);
      $doc->user_name = $this->user_model->get_name($doc->user);
    }

    $details = $this->receive_transform_model->get_details($code);

    $ds = array(
      'doc' => $doc,
      'details' => $details
    );

    $this->load->view('print/print_received_transform', $ds);
  }


	public function save_wms()
	{
		$sc = TRUE;
		$this->load->model('masters/products_model');

		$code = trim($this->input->post('receive_code'));
		$order_code = trim($this->input->post('order_code'));
		$invoice = trim($this->input->post('invoice'));
		$receive = $this->input->post('receive');
		$backlogs = $this->input->post('backlogs');
		$prices = $this->input->post('prices');

		$doc = $this->receive_transform_model->get($code);

		if(!empty($doc))
		{
			if($doc->status == 0)
			{
				if($doc->is_wms == 1)
				{
					//$details = $this->receive_transform_model->get_transform_details($order_code);

					if(!empty($receive))
					{
						$details = array();

						foreach($receive as $item => $qty)
						{
							if($qty > 0)
							{
								$pd = $this->products_model->get($item);
								$price = $this->get_avg_cost($item);
								$cost = $price == 0 ? $pd->cost : $price;
								$ds = new stdClass;
								$ds->receive_code = $code;
								$ds->style_code = $pd->style_code;
								$ds->product_code = $pd->code;
								$ds->product_name = $pd->name;
								$ds->unit_code = $pd->unit_code;
								$ds->price = $cost;
								$ds->qty = $qty;
								$ds->amount = $qty * $cost;

								$details[] = $ds;
							}
						}


						$this->load->library('wms_receive_api');

						$rs = $this->wms_receive_api->export_receive_transform($doc, $order_code, $invoice, $details);

						if($rs === TRUE)
						{

							//--- ลบรายการเก่าก่อนเพิ่มรายการใหม่
		          $this->receive_transform_model->drop_details($code);

							//--- เพิ่มรายการเข้า list
		          foreach($details as $rs)
		          {

								$arr = array(
									'receive_code' => $rs->receive_code,
									'style_code' => $rs->style_code,
									'product_code' => $rs->product_code,
									'product_name' => $rs->product_name,
									'price' => $rs->price,
									'qty' => $rs->qty,
									'amount' => $rs->amount
								);

								$this->receive_transform_model->add_detail($arr);
		          } //--- end foreach


							//--- update document
							$arr = array(
								'order_code' => $order_code,
								'invoice_code' => $invoice,
								'zone_code' => getConfig('WMS_ZONE'),
								'warehouse_code' => getConfig('WMS_WAREHOUSE'),
								'update_user' => get_cookie('uname'),
								'status' => 3 //--- 0 = not save, 1 = saved, 2 = cancle , 3 = on process at wms
							);

							if(! $this->receive_transform_model->update($code, $arr))
							{
								$sc = FALSE;
								$this->error = "Update document failed";
							}
						}
						else
						{
							$sc = FALSE;
							$this->error = trim($this->wms_receive_api->error);
						}
					}
					else
					{
						$sc = FALSE;
						$this->error = "No data";
					}

				}
				else
				{
					$sc = FALSE;
					$this->error = "This document must receive by Warrix";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Invalid document status";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Invalid document number";
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}



  public function save()
  {
    $sc = TRUE;
    $message = 'ทำรายการไม่สำเร็จ';
    $this->load->model('inventory/movement_model');
    if($this->input->post('receive_code'))
    {
      $this->load->model('masters/products_model');
      $this->load->model('masters/zone_model');

      $code = $this->input->post('receive_code');
      $doc = $this->receive_transform_model->get($code);
      $order_code = $this->input->post('order_code');
      $invoice = $this->input->post('invoice');
      $zone_code = $this->input->post('zone_code');
      $warehouse_code = $this->zone_model->get_warehouse_code($zone_code);
      $receive = $this->input->post('receive');
      $backlogs = $this->input->post('backlogs');
      $prices = $this->input->post('prices');

      $arr = array(
        'order_code' => $order_code,
        'invoice_code' => $invoice,
        'zone_code' => $zone_code,
        'warehouse_code' => $warehouse_code,
        'update_user' => get_cookie('uname')
      );

      $this->db->trans_start();

      if($this->receive_transform_model->update($code, $arr) === FALSE)
      {
        $sc = FALSE;
        $message = 'Update Document Fail';
      }

      //--- If update success
      if($sc === TRUE)
      {
        if(!empty($receive))
        {
          //--- ลบรายการเก่าก่อนเพิ่มรายการใหม่
          $this->receive_transform_model->drop_details($code);

          foreach($receive as $item => $qty)
          {
            if($qty != 0 && $sc === TRUE)
            {
              $pd = $this->products_model->get($item);
							$price = $this->get_avg_cost($item);
							$cost = $price == 0 ? $pd->cost : $price;
              $ds = array(
                'receive_code' => $code,
                'style_code' => $pd->style_code,
                'product_code' => $item,
                'product_name' => $pd->name,
                'price' => $cost,
                'qty' => $qty,
                'amount' => $qty * $cost
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
                  'reference' => $code,
                  'warehouse_code' => $warehouse_code,
                  'zone_code' => $zone_code,
                  'product_code' => $item,
                  'move_in' => $qty,
                  'date_add' => db_date($doc->date_add, TRUE)
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
                $this->update_transform_receive_qty($order_code, $item, $qty);
              }

            }//--- end if qty > 0
          } //--- end foreach

          if($sc === TRUE)
          {
            $this->receive_transform_model->set_status($code, 1);
            if($this->transform_model->is_complete($order_code) === TRUE)
            {
              $this->transform_model->close_transform($order_code);
            }
          }
        } //--- end if !empty($receive)
      } //--- if $sc === TRUE

      $this->db->trans_complete();

      if($this->db->trans_status() === FALSE)
      {
        $sc = FALSE;
      }
    }
    else
    {
      $sc = FALSE;
      $message = 'ไม่พบข้อมูล';
    }

    if($sc === TRUE)
    {
      $this->export_receive($code);
    }

    echo $sc === TRUE ? 'success' : $message;
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



  //--- update receive_qty in order_transform_detail
  public function unreceive_product($order_code, $product_code, $qty)
  {
    $sc = TRUE;
    $list = $this->transform_model->get_transform_product_by_code($order_code, $product_code);
    if(!empty($list))
    {
      foreach($list as $rs)
      {
        if($qty > 0 && $rs->receive_qty > 0)
        {
          $diff = $rs->receive_qty - $qty;
          if($diff >= 0 )
          {
            //--- ถ้า dif มากกว่ายอดที่รับมาให้ใช้ยอดรับ
            //--- หากยอดค้าง มี 2 แถว แถวแรก 5 แถวที่ 2 อีก 5 รวมเป็น 10
            //--- แต่รับเข้ามา 8
            //--- รอบแรก ยอด diff = 5 ซึ่งน้อยกว่า ยอดรับ ให้ใช้ยอด diff (ยอดค้างรับของแถวนั้น)
            //--- รอบสอง ยอด diff = 5 แต่ยอดรับจะเหลือ 3 เพราะถูกตัดออกไปรอบแรก 5 (จากยอดรับ 8)
            //--- รอบสองจึงต้องใช้ยอดรับที่เหลือในการ update
            if(!$this->transform_model->update_receive_qty($rs->id, (-1) * $qty))
            {
              $sc = FALSE;
            }

            //--- เมื่อลบยอดค้างรับออกแล้วยังเหลือยอดอีกแสดงว่าแถวนี้รับครบแล้ว ให้ update valid เป็น 1
            if(!$this->transform_model->unvalid_detail($rs->id))
            {
              $sc = FALSE;
            }

            $qty -= $diff;
          }
        } //--- end if qty > 0
      } //--- endforeach
    }

    return $sc;
  }



  public function cancle_received()
  {
    $sc = TRUE;

    if($this->input->post('receive_code'))
    {
      $this->load->model('inventory/movement_model');
      $code = $this->input->post('receive_code');
      $doc = $this->receive_transform_model->get($code);
      if(!empty($doc))
      {
        $this->db->trans_begin();
        if( ! $this->receive_transform_model->cancle_details($code) )
        {
          $sc = FALSE;
          $this->error = "ยกเลิกรายการไม่สำเร็จ";
        }

        if(! $this->receive_transform_model->set_status($code, 2)) //--- 0 = ยังไม่บันทึก 1 = บันทึกแล้ว 2 = ยกเลิก
        {
          $sc = FALSE;
          $this->error = "เปลี่ยนสถานะเอกสารไม่สำเร็จ";
        }

        if(! $this->movement_model->drop_movement($code))
        {
          $sc = FALSE;
          $this->error = "ลบ movement ไม่สำเร็จ";
        }

        if($sc === TRUE)
        {
          $details = $this->receive_transform_model->get_details($code);
          if(!empty($details))
          {
            foreach($details as $rs)
            {
              if(!$this->unreceive_product($doc->order_code, $rs->product_code, $rs->qty))
              {
                $sc = FALSE;
                $this->error = "Update ยอดค้างรับไม่สำเร็จ";
                break;
              }
            }
          }

          //--- unclose WQ
          $this->transform_model->unclose_transform($doc->order_code);
        }

        if($sc === TRUE)
        {
          $this->db->trans_commit();
        }
        else
        {
          $this->db->trans_rollback();
        }

      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่พบเลขที่เอกสาร";
      }

    }
    else
    {
      $sc = FALSE;
      $this->error = 'ไม่พบเลขทีเอกสาร';
    }

    echo $sc === TRUE ? 'success' : $this->error;
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


  public function get_transform_detail()
  {
    $sc = '';
    $code = $this->input->get('order_code');
    $details = $this->receive_transform_model->get_transform_details($code);
    $ds = array();
    if(!empty($details))
    {
      $no = 1;
      $totalQty = 0;
      $totalBacklog = 0;

      foreach($details as $rs)
      {
        $diff = $rs->sold_qty - $rs->receive_qty;
				$cost = $this->get_avg_cost($rs->product_code);
				$cost = $cost == 0 ? $rs->price : $cost;
        $arr = array(
          'no' => $no,
          'barcode' => $rs->barcode,
          'pdCode' => $rs->product_code,
          'pdName' => $rs->name,
          'qty' => round($rs->sold_qty,2),
          'price' => round($cost,2),
          'limit' => $diff,
          'backlog' => number($diff)
        );
        array_push($ds, $arr);
        $no++;
        $totalQty += $rs->sold_qty;
        $totalBacklog += $diff;
      }

      $arr = array(
        'qty' => number($totalQty),
        'backlog' => number($totalBacklog)
      );
      array_push($ds, $arr);

      $sc = json_encode($ds);
    }
    else
    {
      $sc = 'ใบเบิกสินค้าไม่ถูกต้องหรือถูกปิดไปแล้ว';
    }

    echo $sc;
  }



  public function edit($code)
  {
    $document = $this->receive_transform_model->get($code);

		if(!empty($document))
		{
			$ds['document'] = $document;

			if($document->is_wms)
			{
				$ds['details'] = !empty($document) ? $this->receive_transform_model->get_details($code) : NULL;
			}

	    $this->load->view('inventory/receive_transform/receive_transform_edit', $ds);
		}
		else
		{
			$this->page_error();
		}
  }


  public function update_header(){
		$sc = TRUE;
    $code = $this->input->post('code');
    $date = db_date($this->input->post('date_add'));
		$is_wms = $this->input->post('is_wms');
    $remark = get_null($this->input->post('remark'));

    if(!empty($code))
    {
			$arr = array(
				'is_wms' => $is_wms,
	      'date_add' => $date,
	      'remark' => $remark
	    );

	    if(!$this->receive_transform_model->update($code, $arr))
	    {
	      $sc = FALSE;
				$this->error = "ปรับปรุงข้อมูลไม่สำเร็จ";
	    }

    }
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter : code";
		}

		echo $sc === TRUE ? 'success' : $this->error;
  }



  //--- check exists document code
  public function is_exists($code)
  {
    $ext = $this->receive_transform_model->is_exists($code);
    if($ext)
    {
      echo 'เลขที่เอกสารซ้ำ';
    }
    else
    {
      echo 'not_exists';
    }
  }



  public function add_new()
  {
    $this->load->view('inventory/receive_transform/receive_transform_add');
  }


  public function add()
  {

    if($this->input->post('date_add'))
    {
      $date_add = db_date($this->input->post('date_add'), TRUE);
      $code = $this->input->post('code') ? $this->input->post('code') : $this->get_new_code($date_add);

      $arr = array(
        'code' => $code,
        'bookcode' => getConfig('BOOK_CODE_RECEIVE_TRANSFORM'),
        'order_code' => NULL,
        'invoice_code' => NULL,
        'remark' => $this->input->post('remark'),
        'date_add' => $date_add,
        'user' => get_cookie('uname'),
				'is_wms' => $this->input->post('is_wms')
      );

      $rs = $this->receive_transform_model->add($arr);

      if($rs)
      {
        $arr = array('code' => $code);

				echo json_encode($arr);
      }
      else
      {
        echo 'เพิ่มเอกสารไม่สำเร็จ กรุณาลองใหม่อีกครั้ง';
      }
    }
		else
		{
			echo "Missing required parameter";
		}
  }



  public function do_export($code)
  {
    $rs = $this->export_receive($code);
    echo $rs === TRUE ? 'success' : $this->error;
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
  //--- end export transform



  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_RECEIVE_TRANSFORM');
    $run_digit = getConfig('RUN_DIGIT_RECEIVE_TRANSFORM');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->receive_transform_model->get_max_code($pre);
    if(!empty($code))
    {
      $run_no = mb_substr($code, ($run_digit*-1), NULL, 'UTF-8') + 1;
      $new_code = $prefix . '-' . $Y . $M . sprintf('%0'.$run_digit.'d', $run_no);
    }
    else
    {
      $new_code = $prefix . '-' . $Y . $M . sprintf('%0'.$run_digit.'d', '001');
    }

    return $new_code;
  }



	public function get_transform_backlogs($code, $product_code)
	{
		return $this->receive_transform_model->get_transform_backlogs($code, $product_code);
	}


  public function clear_filter()
  {
    $filter = array('trans_code','trans_invoice','trans_order_code','trans_from_date','trans_to_date', 'trans_status', 'trans_is_wms');
    clear_filter($filter);
  }

} //--- end class

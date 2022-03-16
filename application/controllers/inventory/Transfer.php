<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transfer extends PS_Controller
{
  public $menu_code = 'ICTRWH';
	public $menu_group_code = 'IC';
  public $menu_sub_group_code = 'TRANSFER';
	public $title = 'โอนสินค้าระหว่างคลัง';
  public $filter;
  public $error;
	public $isAPI;

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/transfer';
    $this->load->model('inventory/transfer_model');
    $this->load->model('masters/warehouse_model');
    $this->load->model('masters/zone_model');
    $this->load->model('stock/stock_model');

		$this->isAPI = is_true(getConfig('WMS_API'));
  }


  public function index()
  {
    $filter = array(
      'code'      => get_filter('code', 'tr_code', ''),
      'from_warehouse'  => get_filter('from_warehouse', 'tr_from_warehouse', ''),
      'user'      => get_filter('user', 'tr_user', ''),
      'to_warehouse'  => get_filter('to_warehouse', 'tr_to_warehouse', ''),
      'from_date' => get_filter('fromDate', 'tr_fromDate', ''),
      'to_date'   => get_filter('toDate', 'tr_toDate', ''),
      'status' => get_filter('status', 'tr_status', 'all'),
      'is_export' => get_filter('is_export', 'tr_is_export', 'all'),
			'api' => get_filter('api', 'tr_api', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->transfer_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$docs     = $this->transfer_model->get_data($filter, $perpage, $this->uri->segment($segment));

    if(!empty($docs))
    {
      foreach($docs as $rs)
      {
        $rs->from_warehouse_name = $this->warehouse_model->get_name($rs->from_warehouse);
        $rs->to_warehouse_name = $this->warehouse_model->get_name($rs->to_warehouse);
      }
    }

    $filter['docs'] = $docs;
		$this->pagination->initialize($init);
    $this->load->view('transfer/transfer_list', $filter);
  }



  public function view_detail($code)
  {
    $doc = $this->transfer_model->get($code);
    if(!empty($doc))
    {
      $doc->from_warehouse_name = $this->warehouse_model->get_name($doc->from_warehouse);
      $doc->to_warehouse_name = $this->warehouse_model->get_name($doc->to_warehouse);
    }

    $details = $this->transfer_model->get_details($code);
    if(!empty($details))
    {
      foreach($details as $rs)
      {
        $rs->from_zone_name = $this->zone_model->get_name($rs->from_zone);
        $rs->to_zone_name = $this->zone_model->get_name($rs->to_zone);
        $rs->temp_qty = $this->transfer_model->get_temp_qty($code, $rs->product_code, $rs->from_zone);
      }
    }

    $ds = array(
      'doc' => $doc,
      'details' => $details,
      'barcode' => FALSE
    );

    $this->load->view('transfer/transfer_view', $ds);
  }


  public function add_new()
  {
    $this->load->view('transfer/transfer_add');
  }


  public function add()
  {
    if($this->input->post('date'))
    {
      $date_add = db_date($this->input->post('date'), TRUE);
      $from_warehouse = $this->input->post('from_warehouse_code');
      $to_warehouse = $this->input->post('to_warehouse_code');
			$wx_code = get_null(trim($this->input->post('wx_code')));
      $remark = $this->input->post('remark');
      $bookcode = getConfig('BOOK_CODE_TRANSFER');
      $isManual = getConfig('MANUAL_DOC_CODE');

			$api = $this->input->post('api'); //--- 1 = ส่งข้อมูลไป wms ตามหลักการ 0 = ไม่ส่งข้อมูลไป WMS

			$fromWh = $this->warehouse_model->get($from_warehouse);
			$toWh = $this->warehouse_model->get($to_warehouse);

			$is_wms = $fromWh->is_wms == 1 ? 1 : ($toWh->is_wms == 1 ? 1 : 0);

			//---- direction 0 = wrx to wrx, 1 = wrx to wms , 2 = wms to wrx
			$direction = $toWh->is_wms == 1 ? 1 :($fromWh->is_wms == 1 ? 2 : 0);

      if($isManual == 1 && $this->input->post('code'))
      {
        $code = $this->input->post('code');
      }
      else
      {
        $code = $this->get_new_code($date_add);
      }


      $ds = array(
        'code' => $code,
        'bookcode' => $bookcode,
        'from_warehouse' => $from_warehouse,
        'to_warehouse' => $to_warehouse,
        'remark' => trim($remark),
        'user' => get_cookie('uname'),
        'date_add' => $date_add,
				'is_wms' => $is_wms,
				'direction' => $direction,
				'api' => $api,
				'wx_code' => $wx_code
      );

      $rs = $this->transfer_model->add($ds);

      if($rs === TRUE)
      {
        redirect($this->home.'/edit/'.$code);
      }
      else
      {
        set_error('เพิ่มเอกสารไม่สำเร็จ กรุณาลองใหม่อีกครั้ง');
        redirect($this->home.'/add_new');
      }
    }
    else
    {
      set_error('ไม่พบข้อมูลเอกสาร กรุณาตรวจสอบ');
      redirect($this->home.'/add_new');
    }
  }



  public function edit($code, $barcode = '')
  {
    $doc = $this->transfer_model->get($code);
    if(!empty($doc))
    {
      $doc->from_warehouse_name = $this->warehouse_model->get_name($doc->from_warehouse);
      $doc->to_warehouse_name = $this->warehouse_model->get_name($doc->to_warehouse);
    }

    $details = $this->transfer_model->get_details($code);
    if(!empty($details))
    {
      foreach($details as $rs)
      {
        $rs->from_zone_name = $this->zone_model->get_name($rs->from_zone);
        $rs->to_zone_name = $this->zone_model->get_name($rs->to_zone);
        $rs->temp_qty = $this->transfer_model->get_temp_qty($code, $rs->product_code, $rs->from_zone);
      }
    }

    $ds = array(
      'doc' => $doc,
      'details' => $details,
      'barcode' => $barcode == '' ? FALSE : TRUE
    );

    $this->load->view('transfer/transfer_edit', $ds);
  }



  public function update($code)
  {
		$fromWh = $this->warehouse_model->get($this->input->post('from_warehouse'));
		$toWh = $this->warehouse_model->get($this->input->post('to_warehouse'));

		$is_wms = $fromWh->is_wms == 1 ? 1 : ($toWh->is_wms == 1 ? 1 : 0);
		$api = $this->input->post('api'); //--- 1 = ส่งข้อมูลไป wms ตามหลักการ 0 = ไม่ส่งข้อมูลไป WMS
		$wx_code = get_null(trim($this->input->post('wx_code')));

		//---- direction 0 = wrx to wrx, 1 = wrx to wms , 2 = wms to wrx
		$direction = $toWh->is_wms == 1 ? 1 :($fromWh->is_wms == 1 ? 2 : 0);


    $arr = array(
      'date_add' => db_date($this->input->post('date_add'), TRUE),
      'from_warehouse' => $fromWh->code,
      'to_warehouse' => $toWh->code,
      'remark' => get_null(trim($this->input->post('remark'))),
			'is_wms' => $is_wms,
			'direction' => $direction,
			'api' => $api,
			'wx_code' => $wx_code,
      'update_user' => get_cookie('uname')
    );

    $rs = $this->transfer_model->update($code, $arr);

    if($rs)
    {
      echo 'success';
    }
    else
    {
      echo 'ปรับปรุงข้อมูลไม่สำเร็จ';
    }
  }




  public function check_temp_exists($code)
  {
    $temp = $this->transfer_model->is_exists_temp($code);
    if($temp === TRUE)
    {
      echo 'exists';
    }
    else
    {
      echo 'not_exists';
    }
  }



	public function save_transfer($code)
  {
    $sc = TRUE;

		$doc = $this->transfer_model->get($code);

		if(!empty($doc))
		{
			$date_add = getConfig('ORDER_SOLD_DATE') == 'D' ? $doc->date_add : now();
			if($doc->status == 0)
			{
				$details = $this->transfer_model->get_details($code);

				if(!empty($details))
				{
					//--- ถ้าต้อง process ที่ wms แค่เปลี่ยนสถานะเป็น 3 แล้ส่งข้อมูลออกไป wms
					if($this->isAPI === TRUE && $doc->is_wms == 1 && $doc->api == 1)
					{
						$this->wms = $this->load->database('wms', TRUE);
						//---- direction 0 = wrx to wrx, 1 = wrx to wms , 2 = wms to wrx
						if($doc->direction == 1)
						{
							$this->load->library('wms_receive_api');

							$rs = $this->wms_receive_api->export_transfer($doc, $details);

							if($rs)
							{
								$this->transfer_model->set_status($doc->code, 3); //--- on wms process
							}
							else
							{
								$sc = FALSE;
								$this->error = "Error! - ".$this->wms_receive_api->error;
							}
						}

						if($doc->direction == 2)
						{
							$this->load->library('wms_order_api');

							$rs = $this->wms_order_api->export_transfer_order($doc, $details);

							if($rs)
							{
								$this->transfer_model->set_status($doc->code, 3); //--- on wms process
							}
							else
							{
								$sc = FALSE;
								$this->error = "Error! - ".$this->wms_order_api->error;
							}
						}
					}
					else
					{
						$this->load->model('inventory/movement_model');
						$this->db->trans_begin();

						$this->transfer_model->update($code, array('shipped_date' => now())); //--- update transferd date

						foreach($details as $rs)
						{
							//--- 2. update movement
							$move_out = array(
								'reference' => $code,
								'warehouse_code' => $doc->from_warehouse,
								'zone_code' => $rs->from_zone,
								'product_code' => $rs->product_code,
								'move_in' => 0,
								'move_out' => $rs->qty,
								'date_add' => $date_add
							);

							$move_in = array(
								'reference' => $code,
								'warehouse_code' => $doc->to_warehouse,
								'zone_code' => $rs->to_zone,
								'product_code' => $rs->product_code,
								'move_in' => $rs->qty,
								'move_out' => 0,
								'date_add' => $date_add
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
						} //--- end foreach

						if($sc === TRUE && ! $this->transfer_model->set_status($code, 1))
						{
							$sc = FALSE;
							$this->error = "เปลี่ยนสถานะเอกสารไม่สำเร็จ";
						}

						if($sc === TRUE && ! $this->transfer_model->valid_all_detail($code, 1))
						{
							$sc = FALSE;
							$this->error = "เปลี่ยนสถานะรายการไม่สำเร็จ";
						}

						if($sc === TRUE)
						{
							$this->db->trans_commit();

							$this->do_export($code);
						}
						else
						{
							$this->db->trans_rollback();
						}


					} //-- end if is_wms
				}
				else
				{
					$sc = FALSE;
					$this->error = "ไม่พบรายการโอนย้าย";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "สถานะเอกสารไม่ถูกต้อง";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "เลขที่เอกสารไม่ถูกต้อง";
		}


    echo $sc === TRUE ? 'success' : $this->error;
  }


	public function send_to_wms($code)
  {
    $sc = TRUE;

		$doc = $this->transfer_model->get($code);

		if(!empty($doc))
		{
			if($doc->status == 0) //--- if($doc->status != 3)
			{
				$sc = FALSE;
				$this->error = "Invalid Document status";
			}
			else
			{
				$details = $this->transfer_model->get_details($code);

				if(!empty($details))
				{
					//--- ถ้าต้อง process ที่ wms แค่เปลี่ยนสถานะเป็น 3 แล้ส่งข้อมูลออกไป wms
					if($doc->is_wms == 1 && $doc->api == 1)
					{
						$this->wms = $this->load->database('wms', TRUE);
						//---- direction 0 = wrx to wrx, 1 = wrx to wms , 2 = wms to wrx
						if($doc->direction == 1)
						{
							$this->load->library('wms_receive_api');

							$rs = $this->wms_receive_api->export_transfer($doc, $details);

							if(! $rs)
							{
								$sc = FALSE;
								$this->error = "Error! - ".$this->wms_receive_api->error;
							}
						}

						if($doc->direction == 2)
						{
							$this->load->library('wms_order_api');

							$rs = $this->wms_order_api->export_transfer_order($doc, $details);

							if(! $rs)
							{
								$sc = FALSE;
								$this->error = "Error! - ".$this->wms_order_api->error;
							}
						}
					}
					else
					{
						$sc = FALSE;
						$this->error = "Document must process at Warrix";
					} //-- end if is_wms
				}
				else
				{
					$sc = FALSE;
					$this->error = "ไม่พบรายการโอนย้าย";
				}
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "เลขที่เอกสารไม่ถูกต้อง";
		}

    echo $sc === TRUE ? 'success' : $this->error;
  }



  public function unsave_transfer($code)
  {
    $sc = TRUE;
    $this->load->model('inventory/movement_model');
    //--- check Transfer doc exists in SAP
    $doc = $this->transfer_model->get_sap_transfer_doc($code);
    if(!empty($doc))
    {
      $sc = FALSE;
      $this->error = "เอกสารเข้า SAP แล้วไม่อนุญาติให้ยกเลิก";
    }
    else
    {

      //--- check middle doc delete it if exists
      $middle = $this->transfer_model->get_middle_transfer_doc($code);
      if(!empty($middle))
      {
        foreach($middle as $rs)
        {
          $this->transfer_model->drop_middle_exits_data($rs->DocEntry);
        }
      }


      $this->db->trans_start();
      //--- change state to 1
      $this->transfer_model->set_status($code, 0);
      $this->transfer_model->valid_all_detail($code, 0);
      $this->movement_model->drop_movement($code);
      $this->db->trans_complete();

      if($this->db->trans_status() === FALSE)
      {
        $sc = FALSE;
        $this->error = $this->db->error();
      }
    }



    echo $sc === TRUE ? 'success' : $this->error;
  }



	public function add_to_transfer()
  {
    $sc = TRUE;

		$data = json_decode($this->input->post('data'));

		if(!empty($data))
		{
			if(! empty($data->transfer_code))
	    {
	      $this->load->model('masters/products_model');

				$code = $data->transfer_code;
	      $from_zone = $data->from_zone;
	      $to_zone = $data->to_zone;
	      $items = $data->items;

	      if(!empty($items))
	      {
	        $this->db->trans_start();

	        foreach($items as $item)
	        {
	          $id = $this->transfer_model->get_id($code, $item->item_code, $from_zone, $to_zone);

	          if(!empty($id))
	          {
	            $this->transfer_model->update_qty($id, $item->qty);
	          }
	          else
	          {
	            $arr = array(
	              'transfer_code' => $code,
	              'product_code' => $item->item_code,
	              'product_name' => $this->products_model->get_name($item->item_code),
	              'from_zone' => $from_zone,
	              'to_zone' => $to_zone,
	              'qty' => $item->qty
	            );

	            $this->transfer_model->add_detail($arr);
	          }
	        }

	        $this->db->trans_complete();

	        if($this->db->trans_status() === FALSE)
	        {
	          $sc = FALSE;
	          $this->error = 'เพิ่มข้อมูลไม่สำเร็จ';
	        }
	      }
				else
				{
					$sc = FALSE;
					$this->error = "ไม่พบรายการสินค้า";
				}
	    }
			else
			{
				$sc = FALSE;
				$this->error = "Missing document code";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing form data";
		}

    echo $sc === TRUE ? 'success' : $this->error;

  }


  // public function add_to_transfer()
  // {
  //   $sc = TRUE;
  //   $code = $this->input->post('transfer_code');
  //   if($code)
  //   {
  //     $this->load->model('masters/products_model');
	//
  //     $from_zone = $this->input->post('from_zone');
  //     $to_zone = $this->input->post('to_zone');
  //     $trans_products = $this->input->post('trans_products');
  //     if(!empty($trans_products))
  //     {
  //       $this->db->trans_start();
  //       foreach($trans_products as $item => $qty)
  //       {
  //         $id = $this->transfer_model->get_id($code, $item, $from_zone, $to_zone);
  //         if(!empty($id))
  //         {
  //           $this->transfer_model->update_qty($id, $qty);
  //         }
  //         else
  //         {
  //           $arr = array(
  //             'transfer_code' => $code,
  //             'product_code' => $item,
  //             'product_name' => $this->products_model->get_name($item),
  //             'from_zone' => $from_zone,
  //             'to_zone' => $to_zone,
  //             'qty' => $qty
  //           );
	//
  //           $this->transfer_model->add_detail($arr);
  //         }
  //       }
	//
  //       $this->db->trans_complete();
	//
  //       if($this->db->trans_status() === FALSE)
  //       {
  //         $sc = FALSE;
  //         $message = 'เพิ่มข้อมูลไม่สำเร็จ';
  //       }
  //     }
  //   }
	//
  //   echo $sc === TRUE ? 'success' : $message;
	//
  // }




  public function add_to_temp()
  {
    $sc = TRUE;

    if($this->input->post('transfer_code'))
    {
      $this->load->model('masters/products_model');

      $code = $this->input->post('transfer_code');
      $zone_code = $this->input->post('from_zone');
      $barcode = trim($this->input->post('barcode'));
      $qty = $this->input->post('qty');

      $item = $this->products_model->get_product_by_barcode($barcode);
      if(!empty($item))
      {
        $product_code = $item->code;
        $stock = $this->stock_model->get_stock_zone($zone_code, $product_code);
        //--- จำนวนที่อยู่ใน temp
        $temp_qty = $this->transfer_model->get_temp_qty($code, $product_code, $zone_code);
        //--- จำนวนที่อยู่ใน transfer_detail และยังไม่ valid
        $transfer_qty = $this->transfer_model->get_transfer_qty($code, $product_code, $zone_code);
        //--- จำนวนที่โอนได้คงเหลือ
        $cqty = $stock - ($temp_qty + $transfer_qty);

        if($qty <= $cqty)
        {
          $arr = array(
            'transfer_code' => $code,
            'product_code' => $product_code,
            'zone_code' => $zone_code,
            'qty' => $qty
          );

          if($this->transfer_model->update_temp($arr) === FALSE)
          {
            $sc = FALSE;
            $message = 'ย้ายสินค้าเข้า temp ไม่สำเร็จ';
          }

        }
        else
        {
          $sc = FALSE;
          $message = 'ยอดในโซนไม่เพียงพอ';
        }
      }
      else
      {
        $sc = FALSE;
        $message = 'บาร์โค้ดไม่ถูกต้อง';
      }
    }
    else
    {
      $sc = FALSE;
      $message = 'ไม่พบเลขที่เอกสาร';
    }

    echo $sc === TRUE ? 'success' : $message;
  }




  public function move_to_zone()
  {
    $sc = TRUE;
    if($this->input->post('transfer_code'))
    {
      $this->load->model('masters/products_model');

      $code = $this->input->post('transfer_code');
      $barcode = trim($this->input->post('barcode'));
      $to_zone = $this->input->post('zone_code');
      $qty = $this->input->post('qty');

      $item = $this->products_model->get_product_by_barcode($barcode);
      if(!empty($item))
      {
        //--- ย้ายจำนวนใน temp มาเพิ่มเข้า transfer detail
        //--- โดยเอา temp ออกมา(อาจมีหลายรายการ เพราะอาจมาจากหลายโซน
        //--- ดึงรายการจาก temp ตามรายการสินค้า (อาจมีหลายบรรทัด)
        $temp = $this->transfer_model->get_temp_product($code, $item->code);
        if(!empty($temp))
        {
          //--- เริ่มใช้งาน transction
          $this->db->trans_begin();
          foreach($temp as $rs)
          {
            if($qty > 0 && $rs->qty > 0)
            {
              //---- ยอดที่ต้องการย้าย น้อยกว่าหรือเท่ากับ ยอดใน temp มั้ย
              //---- ถ้าใช่ ใช้ยอดที่ต้องการย้ายได้เลย
              //---- แต่ถ้ายอดที่ต้องการย้ายมากว่ายอดใน temp แล้วยกยอดที่เหลือไปย้ายในรอบถัดไป(ถ้ามี)
              $temp_qty = $qty <= $rs->qty ? $qty : $rs->qty;
              $id = $this->transfer_model->get_id($code, $item->code, $rs->zone_code, $to_zone);
              //--- ถ้าพบไอดีให้แก้ไขจำนวน
              if(!empty($id))
              {
                if($this->transfer_model->update_qty($id, $temp_qty) === FALSE)
                {
                  $sc = FALSE;
                  $message = 'แก้ไขยอดในรายการไม่สำเร็จ';
                  break;
                }
              }
              else
              {
                //--- ถ้ายังไม่มีรายการ ให้เพิ่มใหม่
                $ds = array(
                  'transfer_code' => $code,
                  'product_code' => $item->code,
                  'product_name' => $item->name,
                  'from_zone' => $rs->zone_code,
                  'to_zone' => $to_zone,
                  'qty' => $temp_qty
                );

                if($this->transfer_model->add_detail($ds) === FALSE)
                {
                  $sc = FALSE;
                  $message = 'เพิ่มรายการไม่สำเร็จ';
                  break;
                }
              }
              //--- ถ้าเพิ่มหรือแก้ไข detail เสร็จแล้ว ทำการ ลดยอดใน temp ตามยอดที่เพิ่มเข้า detail
              if($this->transfer_model->update_temp_qty($rs->id, ($temp_qty * -1)) === FALSE)
              {
                $sc = FALSE;
                $message = 'แก้ไขยอดใน temp ไม่สำเร็จ';
                break;
              }

              //--- ตัดยอดที่ต้องการย้ายออก เพื่อยกยอดไปรอบต่อไป
              $qty -= $temp_qty;
            }
            else
            {
              break;
            } //-- end if qty > 0

            //--- ลบ temp ที่ยอดเป็น 0
            $this->transfer_model->drop_zero_temp();
          } //--- end foreach


          //--- เมื่อทำงานจนจบแล้ว ถ้ายังเหลือยอด แสดงว่ายอดที่ต้องการย้ายเข้า มากกว่ายอดที่ย้ายออกมา
          //--- จะให้ทำกร roll back แล้วแจ้งกลับ
          if($qty > 0)
          {
            $sc = FALSE;
            $message = 'ยอดที่ย้ายเข้ามากกว่ายอดที่ย้ายออกมา';
          }

          if($sc === FALSE)
          {
            $this->db->trans_rollback();
          }
          else
          {
            $this->db->trans_commit();
          }
        }
        else
        {
          $sc = FALSE;
          $message = 'ไม่พบรายการใน temp';
        }
      }
      else
      {
        $sc = FALSE;
        $message = 'บาร์โค้ดไม่ถูกต้อง';
      }
    }
    else
    {
      $sc = FALSE;
      $message = 'ไม่พบเลขที่เอกสาร';
    }

    echo $sc === TRUE ? 'success' : $message;
  }



  public function is_exists($code, $old_code = NULL)
  {
    $exists = $this->transfer_model->is_exists($code, $old_code);
    if($exists)
    {
      echo 'เลขที่เอกสารซ้ำ';
    }
    else
    {
      echo 'not_exists';
    }
  }


  public function is_exists_detail($code)
  {
    $detail = $this->transfer_model->is_exists_detail($code);
    $temp = $this->transfer_model->is_exists_temp($code);

    if($detail === FALSE && $temp === FALSE)
    {
      echo 'not_exists';
    }
    else
    {
      echo 'exists';
    }
  }



  public function get_temp_table($code)
  {
    $ds = array();
    $temp = $this->transfer_model->get_transfer_temp($code);
    if(!empty($temp))
    {
      $no = 1;
      foreach($temp as $rs)
      {
        $arr = array(
          'no' => $no,
          'id' => $rs->id,
          'barcode' => $rs->barcode,
          'products' => $rs->product_code,
          'from_zone' => $rs->zone_code,
          'fromZone' => $this->zone_model->get_name($rs->zone_code),
          'qty' => $rs->qty
        );

        array_push($ds, $arr);
        $no++;
      }
    }
    else
    {
      array_push($ds, array('nodata' => 'nodata'));
    }

    echo json_encode($ds);
  }




  public function get_transfer_table($code)
  {
    $ds = array();
    $details = $this->transfer_model->get_details($code);

    if(!empty($details))
    {
      $no = 1;
      $total_qty = 0;
      foreach($details as $rs)
      {
        $btn_delete = '';
        if($this->pm->can_add OR $this->pm->can_edit && $rs->valid == 0)
        {
          $btn_delete .= '<button type="button" class="btn btn-minier btn-danger" ';
          $btn_delete .= 'onclick="deleteMoveItem('.$rs->id.', \''.$rs->product_code.'\')">';
          $btn_delete .= '<i class="fa fa-trash"></i></button>';
        }

        $arr = array(
          'id' => $rs->id,
          'no' => $no,
          'barcode' => $rs->barcode,
          'products' => $rs->product_code,
          'from_zone' => $this->zone_model->get_name($rs->from_zone),
          'to_zone' => $this->zone_model->get_name($rs->to_zone),
          'qty' => number($rs->qty),
          'btn_delete' => $btn_delete
        );

        array_push($ds, $arr);
        $no++;
        $total_qty += $rs->qty;
      } //--- end foreach

      $arr = array(
        'total' => number($total_qty)
      );

      array_push($ds, $arr);
    }
    else
    {
      array_push($ds, array('nodata' => 'nodata'));
    }

    echo json_encode($ds);
  }



  public function get_transfer_zone($warehouse = '')
  {
    $txt = $_REQUEST['term'];
    $sc = array();
    $zone = $this->zone_model->search($txt, $warehouse);
    if(!empty($zone))
    {
      foreach($zone as $rs)
      {
        $sc[] = $rs->code.' | '.$rs->name;
      }
    }
    else
    {
      $sc[] = 'ไม่พบโซน';
    }

    echo json_encode($sc);
  }



  public function get_product_in_zone()
  {
    $sc = array();

    if($this->input->get('zone_code'))
    {
      $this->load->model('masters/products_model');

      $zone_code = $this->input->get('zone_code');
      $transfer_code = $this->input->get('transfer_code');
      $stock = $this->stock_model->get_all_stock_in_zone($zone_code);
      if(!empty($stock))
      {
        $no = 1;
        foreach($stock as $rs)
        {
          //--- จำนวนที่อยู่ใน temp
          $temp_qty = $this->transfer_model->get_temp_qty($transfer_code, $rs->product_code, $zone_code);
          //--- จำนวนที่อยู่ใน transfer_detail และยังไม่ valid
          $transfer_qty = $this->transfer_model->get_transfer_qty($transfer_code, $rs->product_code, $zone_code);
          //--- จำนวนที่โอนได้คงเหลือ
          $qty = $rs->qty - ($temp_qty + $transfer_qty);

          if($qty > 0)
          {
            $arr = array(
              'no' => $no,
              'barcode' => $this->products_model->get_barcode($rs->product_code),
              'products' => $rs->product_code,
              'qty' => $qty
            );

            array_push($sc, $arr);
            $no++;
          }
        }
      }
      else
      {
        array_push($sc, array("nodata" => "nodata"));
      }
      echo json_encode($sc);
    }
  }





  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_TRANSFER');
    $run_digit = getConfig('RUN_DIGIT_TRANSFER');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->transfer_model->get_max_code($pre);
    if(! is_null($code))
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




  public function delete_detail($id)
  {
    $rs = $this->transfer_model->drop_detail($id);
    if($rs === TRUE)
    {
      echo 'success';
    }
    else
    {
      echo $this->db->error();
    }
  }




  public function delete_transfer($code)
  {
    $this->load->model('inventory/movement_model');

    $this->db->trans_start();

    //--- clear temp
    $this->transfer_model->drop_all_temp($code);
    //--- delete detail
    $this->transfer_model->drop_all_detail($code);
    //--- drop movement
    $this->movement_model->drop_movement($code);
    //--- change status to 2 (cancled)
    $this->transfer_model->set_status($code, 2);

    $this->db->trans_complete();
    if($this->db->trans_status() === FALSE)
    {
      echo $this->db->error();
    }
    else
    {
      echo 'success';
    }
  }




  public function print_transfer($code)
  {
    $this->load->library('printer');
    $doc = $this->transfer_model->get($code);
    if(!empty($doc))
    {
      $doc->from_warehouse_name = $this->warehouse_model->get_name($doc->from_warehouse);
      $doc->to_warehouse_name = $this->warehouse_model->get_name($doc->to_warehouse);
    }

    $details = $this->transfer_model->get_details($code);
    if(!empty($details))
    {
      foreach($details as $rs)
      {
        // $rs->from_zone_name = $this->zone_model->get_name($rs->from_zone);
        // $rs->to_zone_name = $this->zone_model->get_name($rs->to_zone);
        $rs->temp_qty = $this->transfer_model->get_temp_qty($code, $rs->product_code, $rs->from_zone);
      }
    }

    $ds = array(
      'doc' => $doc,
      'details' => $details
    );

    $this->load->view('print/print_transfer', $ds);
  }

	public function print_wms_transfer($code)
  {
    $this->load->library('xprinter');
    $doc = $this->transfer_model->get($code);
		if(!empty($doc))
    {
      $doc->from_warehouse_name = $this->warehouse_model->get_name($doc->from_warehouse);
      $doc->to_warehouse_name = $this->warehouse_model->get_name($doc->to_warehouse);
    }

    $details = $this->transfer_model->get_details($code);

    $ds = array(
      'order' => $doc,
      'details' => $details
    );

    $this->load->view('print/print_wms_transfer', $ds);
  }



  private function do_export($code)
  {
    $sc = TRUE;
    $this->load->library('export');
    if( ! $this->export->export_transfer($code))
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



  public function export_transfer($code)
  {
    if($this->do_export($code) === TRUE)
    {
      echo 'success';
    }
    else
    {
      echo $this->error;
    }
  }

  //---- Update status transfer draft to receipted
  public function confirm_receipted()
  {
    $sc = TRUE;
    $code = trim($this->input->post('code'));
    if(!empty($code))
    {
      $this->load->model('orders/orders_model');

      //--- check ว่ามีเลขที่เอกสารนี้ใน transfer draft หรือไม่
      $draft = $this->transfer_model->get_transfer_draft($code);
      if(!empty($draft))
      {
        if(empty($draft->F_Receipt) OR $draft->F_Receipt == 'N' OR $draft->F_Receipt == 'D')
        {
          //---- ยืนยันรับสินค้า
          if($this->transfer_model->confirm_draft_receipted($draft->DocEntry))
          {
            $this->orders_model->valid_transfer_draft($code);
          }
          else
          {
            $sc = FALSE;
            $this->error = "ยืนยันการรับสินค้าใน Transfer Draft ไม่สำเร็จ";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "เอกสารถูกยืนยันไปแล้ว";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่พบเอกสาร Transfer draft";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบเลขที่เอกสาร";
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function clear_filter()
  {
    $filter = array(
      'tr_code',
      'tr_from_warehouse',
      'tr_user',
      'tr_to_warehouse',
      'tr_fromDate',
      'tr_toDate',
      'tr_status',
      'tr_is_export',
			'tr_api'
    );

    clear_filter($filter);

    echo 'done';
  }


} //--- end class
?>

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
	public $isAPI;
  public $wmsApi;
  public $sokoApi;
  public $required_remark = TRUE; //--- บังคับใส่หมายเหตุ

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'inventory/receive_transform';
    $this->load->model('inventory/receive_transform_model');
    $this->load->model('inventory/transform_model');

		$this->isAPI = is_true(getConfig('WMS_API'));
    $this->wmsApi = is_true(getConfig('WMS_API'));
    $this->sokoApi = is_true(getConfig('SOKOJUNG_API'));
  }


  public function index()
  {
    $this->load->helper('channels');
    $this->load->helper('warehouse');

    $filter = array(
      'code'    => get_filter('code', 'trans_code', ''),
      'invoice' => get_filter('invoice', 'trans_invoice', ''),
      'order_code' => get_filter('order_code', 'trans_order_code', ''),
      'from_date' => get_filter('from_date', 'trans_from_date', ''),
      'to_date' => get_filter('to_date', 'trans_to_date', ''),
      'must_accept' => get_filter('must_accept', 'trans_must_accept', 'all'),
      'status' => get_filter('status', 'trans_status', 'all'),
			'is_wms' => get_filter('is_wms', 'trans_is_wms', 'all'),
      'sap_status' => get_filter('sap_status', 'trans_sap_status', 'all'),
      'warehouse' => get_filter('warehouse', 'trans_warehouse', 'all'),
      'zone' => get_filter('zone', 'trans_zone', '')
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

  public function rollback_status()
  {
    $sc = TRUE;
    $code = $this->input->post('code');

    if( ! empty($code))
    {
      $doc = $this->receive_transform_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status == 4)
        {
          $arr = array(
            'status' => 0
          );

          if( ! $this->receive_transform_model->update($code, $arr))
          {
            $sc = FALSE;
            $this->error = "เปลี่ยนสถานะเอกสารไม่สำเร็จ";
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
        $this->error = "ไม่พบเลขที่เอกสาร";
      }
    }
    else
    {
      $sc = FALSE;
      set_error('required');
    }

    echo $sc === TRUE ? 'success' : $this->error;
  }


  public function accept_confirm()
  {
    $sc = TRUE;
    $this->load->model('inventory/movement_model');
    $code = $this->input->post('code');
    $remark = trim($this->input->post('accept_remark'));

    $ex = 0;

    if( ! empty($code))
    {
      $doc = $this->receive_transform_model->get($code);

      if( ! empty($doc))
      {
        if( $doc->status == 4)
        {
          $status = $doc->is_wms == 0 ? 1 : ($doc->is_wms == 1 && $this->wmsApi ? 3 : ($doc->is_wms == 2 && $this->sokoApi ? 3 : 1));
          $date_add = getConfig('ORDER_SOLD_DATE') == 'D' ? $doc->date_add : now();
          $ship_date = $doc->is_wms == 0 ? $date_add : ($doc->is_wms == 1 && $this->wmsApi ? NULL :($doc->is_wms == 2 && $this->sokoApi ? NULL : $date_add));
          $arr = array(
            "status" => $status,
            "shipped_date" => $ship_date,
            "is_accept" => 1,
            "accept_by" => $this->_user->uname,
            "accept_on" => now(),
            "accept_remark" => $remark
          );

          $details = $this->receive_transform_model->get_details($code);

          $this->db->trans_begin();

          if( ! $this->receive_transform_model->update($code, $arr))
          {
            $sc = FALSE;
            $this->error = "Update Acception failed";
          }

          if($sc === TRUE)
          {

            if($doc->is_wms == 0 OR (($doc->is_wms == 1 && ! $this->wmsApi) OR ($doc->is_wms == 2 && ! $this->sokoApi)))
            {

              if(! empty($details))
              {
                foreach($details as $rs)
                {
                  if($sc === FALSE)
                  {
                    break;
                  }

                  $arr = array(
                    'receive_qty' => $rs->qty
                  );

                  if( ! $this->receive_transform_model->update_detail($rs->id, $arr))
                  {
                    $sc = FALSE;
                    $this->error = "Receive item failed";
                  }

                  //--- stock movement
                  if($sc === TRUE)
                  {
                    $arr = array(
                      'reference' => $doc->code,
                      'warehouse_code' => $doc->warehouse_code,
                      'zone_code' => $doc->zone_code,
                      'product_code' => $rs->product_code,
                      'move_in' => $rs->qty,
                      'date_add' => db_date($shipped_date, TRUE)
                    );

                    if($this->movement_model->add($arr) === FALSE)
                    {
                      $sc = FALSE;
                      $this->error = 'บันทึก movement ไม่สำเร็จ';
                    }
                  }

                  //--- update receive_qty in order_transform_detail
                  if($sc === TRUE)
                  {
                    $this->update_transform_receive_qty($doc->order_code, $rs->product_code, $rs->qty);
                  }
                } //--- end foreach
              }
              else
              {
                $sc = FALSE;
                $this->error = "No items in document";
              }
            } //--- if is_wms == 0
          }

          if($sc === TRUE)
          {
            $this->db->trans_commit();
          }
          else
          {
            $this->db->trans_rollback();
          }

          if($sc === TRUE)
          {
            if($doc->is_wms == 0 OR (($doc->is_wms == 1 && ! $this->wmsApi) OR ($doc->is_wms == 2 && ! $this->sokoApi)))
            {
              if($this->transform_model->is_complete($doc->order_code))
              {
                $this->transform_model->close_transform($doc->order_code);
              }

              //---- send to SAP Temp
              $this->load->library('export');

              if(! $this->export->export_receive_transform($doc->code))
              {
                $ex = 1; //--- export error
                $this->error = trim($this->export->error);
              }
            }
            else
            {
              if($doc->is_wms == 1 && $this->wmsApi)
              {
                $this->wms = $this->load->database('wms', TRUE);
                $this->load->library('wms_receive_api');

                if( ! $this->wms_receive_api->export_receive_transform($doc, $doc->order_code, $doc->invoice, $details))
                {
                  $ex = 1;
                  $this->error = "บันทึกเอกสารสำเร็จ แต่ส่งข้อมูลไป Pioneer ไม่สำเร็จ : {$this->wms_receive_api->error}";
                }
              }

              if($doc->is_wms == 2 && $this->sokoApi)
              {
                $this->wms = $this->load->database('wms', TRUE);
                $this->load->library('soko_receive_api');

                if( ! $this->soko_receive_api->create_receive_transform($doc, $doc->order_code, $doc->invoice, $details))
                {
                  $ex = 1;
                  $this->error = "บันทึกเอกสารสำเร็จ แต่ส่งข้อมูลไป SOKOCHAN ไม่สำเร็จ : {$this->soko_receive_api->error}";
                }
              }
            }
          } //--- end export
        }
        else
        {
          $sc = FALSE;
          $this->error = "Invalid Document Status";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid Document Number";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing required parameter";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'ex' => $ex
    );

    echo json_encode($arr);
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


	public function send_to_wms($code)
	{
		$sc = TRUE;

    if($this->wmsApi)
    {
      $doc = $this->receive_transform_model->get($code);

      if(!empty($doc))
      {
        if($doc->status == 3)
        {
          if($doc->is_wms == 1)
          {
            $details = $this->receive_transform_model->get_details($code);

            if( ! empty($details))
            {
              $this->wms = $this->load->database('wms', TRUE);
              $this->load->library('wms_receive_api');

              $ex = $this->wms_receive_api->export_receive_transform($doc, $doc->order_code, $doc->invoice_code, $details);

              if(!$ex)
              {
                $sc = FALSE;
                $thiis->error = "ส่งข้อมูลไป WMS ไม่สำเร็จ <br/>{$this->wms_receive_api->error}";
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "No items in document";
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "Invalid Fulfillment API";
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
        $this->error = "Invalid document code";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "API is not enabled";
    }

		echo $sc === TRUE ? 'success' : $this->error;
	}


  public function send_to_soko($code)
	{
		$sc = TRUE;

    if($this->sokoApi)
    {
      $doc = $this->receive_transform_model->get($code);

      if( ! empty($doc))
      {
        if($doc->status == 3)
        {
          if($doc->is_wms == 2)
          {
            $details = $this->receive_transform_model->get_details($code);

            if( ! empty($details))
            {
              $this->wms = $this->load->database('wms', TRUE);
              $this->load->library('soko_receive_api');

              $ex = $this->soko_receive_api->create_receive_transform($doc, $doc->order_code, $doc->invoice_code, $details);

              if( ! $ex)
              {
                $sc = FALSE;
                $this->error = "ส่งข้อมูลไป SOKOCHAN ไม่สำเร็จ <br/>{$this->soko_receive_api->error}";
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "No items in document";
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "Invalid Fulfillment API";
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
        $this->error = "Invalid document code";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Api is not enabled";
    }

		echo $sc === TRUE ? 'success' : $this->error;
	}


  public function save()
  {
    $sc = TRUE;
    $ex = 0;
    $data = json_decode($this->input->post('data'));

    if(! empty($data))
    {
      $this->load->model('masters/products_model');
      $this->load->model('masters/zone_model');
			$this->load->model('masters/warehouse_model');
			$this->load->model('inventory/movement_model');

      $code = $data->receive_code;
      $doc = $this->receive_transform_model->get($code);

      $wmsZone = getConfig('WMS_ZONE');
      $sokoZone = getConfig('SOKOJUNG_ZONE');

			if(!empty($doc))
			{
				$zone = $this->zone_model->get($data->zone_code);

				$date_add = getConfig('ORDER_SOLD_DATE') == 'D' ? $doc->date_add : now();

        if($doc->is_wms == 1 && $zone->code != $wmsZone)
        {
          $sc = FALSE;
          $this->error = "เอกสารต้องรับเข้าที่โซน {$wmsZone}";
        }

        if($doc->is_wms == 2 && $zone->code != $sokoZone)
        {
          $sc = FALSE;
          $this->error = "เอกสารต้องรับเข้าที่โซน {$sokoZone}";
        }

        if($doc->is_wms == 0 && ($zone->code == $wmsZone OR $zone->code == $sokoZone))
        {
          $sc = FALSE;
          $this->error = "เอกสารต้องรับเข้าที่โซนของ WARRIX";
        }

        if($sc === TRUE)
        {
          $zone_code = $zone->code;
          $warehouse_code = $zone->warehouse_code;
          $must_accept = (empty($zone->user_id) ? FALSE : TRUE);
  	      $receive = $data->items;

  	      $arr = array(
  	        'order_code' => $data->order_code,
  	        'invoice_code' => $data->invoice,
  	        'zone_code' => $zone_code,
  	        'warehouse_code' => $warehouse_code,
  	        'update_user' => $this->_user->uname,
            'must_accept' => $must_accept ? 1 : 0,
            'status' => $must_accept ? 4 : ($doc->is_wms == 0 ? 1 : ($doc->is_wms == 1 && $this->wmsApi ? 3 : ($doc->is_wms == 2 && $this->sokoApi ? 3 : 1))),
            'shipped_date' => $must_accept ? NULL : ($doc->is_wms == 0 ? $date_add : ($doc->is_wms == 1 && $this->wmsApi ? NULL : ($doc->is_wms == 2 && $this->sokoApi ? NULL : $date_add))),
            'is_accept' => 0,
            'accept_by' => NULL,
            'accept_on' => NULL
  	      );

  	      $this->db->trans_begin();

  	      if($this->receive_transform_model->update($code, $arr) === FALSE)
  	      {
  	        $sc = FALSE;
  	        $this->error = 'Update Document Failed';
  	      }

  	      //--- If update success
  	      if($sc === TRUE)
  	      {
  	        if( ! empty($receive))
  	        {
  	          //--- ลบรายการเก่าก่อนเพิ่มรายการใหม่
  	          $this->receive_transform_model->drop_details($code);

  						$details = array();

  	          foreach($receive as $rs)
  	          {
  							if($sc === FALSE)
  							{
  								break;
  							}

  	            if($rs->qty > 0)
  	            {
  	              $pd = $this->products_model->get($rs->product_code);

  								if( ! empty($pd))
  								{
  									$cost = $rs->price == 0 ? $pd->cost : $rs->price;

  									if($must_accept === FALSE && $doc->is_wms != 0 && ($this->wmsApi OR $this->sokoApi))
  									{
  										$de = new stdClass;
  										$de->receive_code = $code;
  										$de->style_code = $pd->style_code;
  										$de->product_code = $pd->code;
  										$de->product_name = $pd->name;
  										$de->unit_code = $pd->unit_code;
  										$de->price = $cost;
  										$de->qty = $rs->qty;
  										$de->amount = $rs->qty * $cost;

  										$details[] = $de;
  									}

  		              $ds = array(
  		                'receive_code' => $code,
  		                'style_code' => $pd->style_code,
  		                'product_code' => $pd->code,
  		                'product_name' => $pd->name,
  		                'price' => $cost,
  		                'qty' => $rs->qty,
                      'receive_qty' => $must_accept === TRUE ? 0 : ($doc->is_wms == 1 && $this->wmsApi ? 0 : ($doc->is_wms == 2 && $this->sokoApi ? 0 : $rs->qty)),
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
                      if($must_accept === FALSE && ($doc->is_wms == 0 OR ($doc->is_wms == 1 && ! $this->wmsApi) OR ($doc->is_wms == 2 && ! $this->sokoApi)))
                      {
                        $ds = array(
                          'reference' => $code,
                          'warehouse_code' => $warehouse_code,
                          'zone_code' => $zone_code,
                          'product_code' => $pd->code,
                          'move_in' => $rs->qty,
                          'date_add' => db_date($date_add, TRUE)
                        );

                        if( ! $this->movement_model->add($ds))
                        {
                          $sc = FALSE;
                          $this->error = 'บันทึก movement ไม่สำเร็จ';
                        }

                        //--- update receive_qty in order_transform_detail
                        if($sc === TRUE)
                        {
                          $this->update_transform_receive_qty($data->order_code, $pd->code, $rs->qty);
                        }
                      }
                    }
  								}
  								else
  								{
  									$sc = FALSE;
  									$this->error = "ไม่พบรหัสสินค้า : {$rs->product_code}";
  								}

  	            }//--- end if qty > 0
  	          } //--- end foreach

              //--- หากไม่ต้องกดรับ
              if($sc === TRUE)
              {
                if( ! $must_accept)
                {
                  if($doc->is_wms == 0 OR (($doc->is_wms == 1 && ! $this->wmsApi) OR ($doc->is_wms == 2 && ! $this->sokoApi)))
                  {
                    if($this->transform_model->is_complete($doc->order_code))
                    {
                      $this->transform_model->close_transform($data->order_code);
                    }

                    //---- send to SAP Temp
                    $this->load->library('export');

                    if(! $this->export->export_receive_transform($code))
                    {
                      $ex = 1; //--- export error
                      $this->error = trim($this->export->error);
                    }
                  }
                  else
                  {
                    if($doc->is_wms == 1 && $this->wmsApi)
                    {
                      $this->wms = $this->load->database('wms', TRUE);
                      $this->load->library('wms_receive_api');

                      if( ! $this->wms_receive_api->export_receive_transform($doc, $data->order_code, $data->invoice, $details))
                      {
                        $ex = 1;
                        $this->error = "บันทึกเอกสารสำเร็จ แต่ส่งข้อมูลไป Pioneer ไม่สำเร็จ : {$this->wms_receive_api->error}";
                      }
                    }

                    if($doc->is_wms == 2 && $this->sokoApi)
                    {
                      $this->wms = $this->load->database('wms', TRUE);
                      $this->load->library('soko_receive_api');

                      if( ! $this->soko_receive_api->create_receive_transform($doc, $data->order_code, $data->invoice, $details))
                      {
                        $ex = 1;
                        $this->error = "บันทึกเอกสารสำเร็จ แต่ส่งข้อมูลไป SOKOCHAN ไม่สำเร็จ : {$this->soko_receive_api->error}";
                      }
                    }
                  }
                }
              }
  	        } //--- end if !empty($receive)

  	      } //--- if $sc === TRUE

  	      if($sc === TRUE)
  				{
  					$this->db->trans_commit();
  				}
  				else
  				{
  					$this->db->trans_rollback();
  				}
        }
			}
			else
			{
				$sc = FALSE;
				$this->error = "เลขที่เอกสารไม่ถูกต้อง";
			}
    }
    else
    {
      $sc = FALSE;
      $this->error = 'ไม่พบข้อมูล';
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'ex' => $ex
    );

    echo json_encode($arr);
  }


  //--- update receive_qty in order_transform_detail
  public function update_transform_receive_qty($order_code, $product_code, $qty)
  {
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
      if($this->input->post('reason'))
      {
        $this->load->model('inventory/movement_model');
        $code = $this->input->post('receive_code');
        $reason = trim($this->input->post('reason'));
        $force_cancel = $this->input->post('force_cancel') == 1 ? TRUE : FALSE;

        $doc = $this->receive_transform_model->get($code);

        if( ! empty($doc))
        {
          if($this->pm->can_delete)
          {
            if($doc->status != 2)
            {
              if($doc->status == 1)
              {
                $sap = $this->receive_transform_model->get_sap_doc_num($code);

                if(! empty($sap))
                {
                  $sc = FALSE;
                  $this->error = "กรุณายกเลิกเอกสาร Goods Receipt บน SAP ก่อน (สร้างเอกสาร Goods Issue กลับรายการ แล้วแก้ไขเลข RT โดยเติม -X ต่อท้าย)";
                }

                if($sc === TRUE)
                {
                  $middle = $this->receive_transform_model->get_middle_receive_transform($code);

                  if(! empty($middle))
                  {
                    foreach($middle as $mid)
                    {
                      if($sc === FALSE)
                      {
                        break;
                      }

                      if(! $this->receive_transform_model->drop_middle_exits_data($mid->DocEntry))
                      {
                        $sc = FALSE;
                        $this->error = "Drop Temp data failed";
                      }
                    }
                  }
                }
              }

              if($sc === TRUE && $doc->status == 3 && ! $force_cancel)
              {
                if($doc->is_wms == 2 && ! empty($doc->soko_code) && $this->sokoApi)
                {
                  $this->wms = $this->load->database('wms', TRUE);
                  $this->load->library('soko_receive_api');

                  $ex = $this->soko_receive_api->cancel_receive_transform($doc);

                  if( ! $ex)
                  {
                    $sc = FALSE;
                    $this->error = "ยกเลิกเอกสารที่ SOKOCHAN ไม่สำเร็จ กรุณาติดต่อเจ้าหน้าที่ <br/>{$this->soko_receive_api->error}";
                  }
                }
              }

              if($sc === TRUE)
              {
                $this->db->trans_begin();

                if( ! $this->receive_transform_model->cancle_details($code) )
                {
                  $sc = FALSE;
                  $this->error = "ยกเลิกรายการไม่สำเร็จ";
                }

                $arr = array(
                  'status' => 2,
                  'cancle_reason' => $reason
                );

                if(! $this->receive_transform_model->update($code, $arr)) //--- 0 = ยังไม่บันทึก 1 = บันทึกแล้ว 2 = ยกเลิก
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
                  if($doc->status == 1)
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
            }
            else
            {
              $sc = FALSE;
              $this->error = "เอกสารถูกยกเลิกไปแล้ว";
            }
          }
          else
          {
            $sc = FALSE;
            set_error('permission');
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "ไม่พบเลขที่เอกสาร";
        }
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
    $receive_code = $this->input->get('receive_code');
    $code = $this->input->get('order_code');
    $details = $this->receive_transform_model->get_transform_details($code);
    $pm = get_permission('ICRTCOST', $this->_user->uid, $this->_user->id_profile);
    $can_edit_price = ($pm->can_view + $pm->can_add + $pm->can_edit + $pm->can_delete + $pm->can_approve) > 0 ? TRUE : FALSE;
    $ds = array();
    if(!empty($details))
    {
      $no = 1;
      $totalQty = 0;
      $totalReceive = 0;
      $totalUncomplete = 0;
      $totalBacklog = 0;

      foreach($details as $rs)
      {
        $uncomplete_qty = $this->receive_transform_model->get_sum_uncomplete_qty($code, $rs->product_code, $receive_code);
        $diff = $rs->sold_qty - ($rs->receive_qty + $uncomplete_qty);
        $diff = $diff < 0 ? 0 : $diff;
				$cost = $this->get_avg_cost($rs->product_code);
				$cost = $cost == 0 ? $rs->price : $cost;
        $arr = array(
          'no' => $no,
          'barcode' => $rs->barcode,
          'pdCode' => $rs->product_code,
          'pdName' => $rs->name,
          'qty' => round($rs->sold_qty,2),
          'received' => round($rs->receive_qty,2),
          'uncomplete' => round($uncomplete_qty, 2),
          'price' => round($cost,2),
          'limit' => $diff,
          'backlog' => number($diff),
          'disabled' => $can_edit_price ? "" : "disabled"
        );

        array_push($ds, $arr);
        $no++;
        $totalQty += $rs->sold_qty;
        $totalReceive += $rs->receive_qty;
        $totalUncomplete += $uncomplete_qty;
        $totalBacklog += $diff;
      }

      $arr = array(
        'qty' => number($totalQty),
        'received' => number($totalReceive),
        'uncomplete' => number($totalUncomplete),
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
    $doc = $this->receive_transform_model->get($code);

    if(!empty($doc))
    {
      $pm = get_permission('ICRTCOST', $this->_user->uid, $this->_user->id_profile);
      $can_edit_price = ($pm->can_view + $pm->can_add + $pm->can_edit + $pm->can_delete + $pm->can_approve) > 0 ? TRUE : FALSE;

      $ds = array(
        'doc' => $doc,
        'details' => array(),
      );

      $zone_code = $doc->is_wms == 1 ? getConfig('WMS_ZONE') : ($doc->is_wms == 2 ? getConfig('SOKOJUNG_ZONE') : $doc->zone_code);
      $zone_name = "";
      $zone = NULL;

      if($zone_code != "" && $zone_code != NULL)
      {
        $this->load->model('masters/zone_model');
        $zone = $this->zone_model->get($zone_code);
        $zone_code = empty($zone) ? "" : $zone->code;
        $zone_name = empty($zone) ? "" : $zone->name;
      }

      $doc->zone_code = $zone_code;
      $doc->zone_name = $zone_name;

      $details = $this->receive_transform_model->get_transform_details($doc->order_code);

      if( ! empty($details))
      {
        $no = 1;
        $totalQty = 0;
        $totalReceive = 0;
        $totalUncomplete = 0;
        $totalBacklog = 0;
        $totalInputQty = 0;
        $totalAmount = 0;

        foreach($details as $rs)
        {
          $row = $this->receive_transform_model->get_detail_row($doc->code, $rs->product_code);

          $uncomplete_qty = $this->receive_transform_model->get_sum_uncomplete_qty($rs->order_code, $rs->product_code, $doc->code);
          $diff = $rs->sold_qty - ($rs->receive_qty + $uncomplete_qty);
          $diff = $diff < 0 ? 0 : $diff;
  				$cost = empty($row[0]) ? $this->get_avg_cost($rs->product_code) : $row[0]->price;
  				$cost = $cost == 0 ? $rs->price : $cost;
          $receive_qty = empty($row[0]) ? 0 : round($row[0]->qty); //( ! empty($row) ? round($row->qty, 2) : 0);
          $amount = round($cost * $receive_qty, 2);

          $arr = array(
            'no' => $no,
            'barcode' => $rs->barcode,
            'product_code' => $rs->product_code,
            'product_name' => limitText($rs->name, 50),
            'qty' => round($rs->sold_qty,2),
            'received' => round($rs->receive_qty,2),
            'uncomplete' => round($uncomplete_qty, 2),
            'receive_qty' => $receive_qty,
            'price' => round($cost,2),
            'amount' => $amount,
            'limit' => $diff,
            'backlog' => number($diff),
            'disabled' => $can_edit_price ? "" : "disabled"
          );

          array_push($ds['details'], (object)$arr);

          $no++;
          $totalInputQty += $receive_qty;
          $totalQty += $rs->sold_qty;
          $totalReceive += $rs->receive_qty;
          $totalUncomplete += $uncomplete_qty;
          $totalBacklog += $diff;
        }

        $ds['totalQty'] = $totalQty;
        $ds['totalReceived'] = $totalReceive;
        $ds['totalUncomplete'] = $totalUncomplete;
        $ds['totalBacklog'] = $totalBacklog;
        $ds['totalInputQty'] = $totalInputQty;
        $ds['totalAmount'] = $totalAmount;
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
    $shipped_date = empty($this->input->post('shipped_date')) ? NULL : db_date($this->input->post('shipped_date'), TRUE);
		$is_wms = $this->input->post('is_wms');
    $remark = get_null($this->input->post('remark'));

    if(!empty($code))
    {
			$arr = array(
				'is_wms' => $is_wms,
	      'date_add' => $date,
        'shipped_date' => $shipped_date,
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
      $shipped_date = empty($this->input->post('shipped_date')) ? NULL : db_date($this->input->post('shipped_date'), TRUE);
      $code = $this->input->post('code') ? $this->input->post('code') : $this->get_new_code($date_add);

      $arr = array(
        'code' => $code,
        'bookcode' => getConfig('BOOK_CODE_RECEIVE_TRANSFORM'),
        'order_code' => NULL,
        'invoice_code' => NULL,
        'remark' => $this->input->post('remark'),
        'date_add' => $date_add,
        'shipped_date' => $shipped_date,
        'user' => $this->_user->uname,
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
    $filter = array(
      'trans_code',
      'trans_invoice',
      'trans_order_code',
      'trans_from_date',
      'trans_to_date',
      'trans_status',
      'trans_must_accept',
      'trans_is_wms',
      'trans_sap_status',
      'trans_zone',
      'trans_warehouse'
    );
    clear_filter($filter);
  }

} //--- end class

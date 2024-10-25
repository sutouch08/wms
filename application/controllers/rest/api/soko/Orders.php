<?php
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class Orders extends REST_Controller
{
  public $error;
  public $user;
  public $ms;
  public $mc;
  public $wms;
	public $api_path = "rest/api/soko/orders";
	public $logs;
	public $log_json = FALSE;
	public $api = FALSE;

  public function __construct()
  {
    parent::__construct();
		$this->api = is_true(getConfig('SOKOJUNG_API'));

		if($this->api)
		{
      $this->wms = $this->load->database('wms', TRUE); //--- Temp database
      $this->ms = $this->load->database('ms', TRUE);
      $this->mc = $this->load->database('mc', TRUE);
      $this->load->model('rest/V1/soko_api_logs_model');

	    $this->load->model('orders/orders_model');
	    $this->load->model('orders/order_state_model');
	    $this->load->model('masters/products_model');
	    $this->load->model('masters/customers_model');
	    $this->load->model('masters/channels_model');
			$this->load->model('masters/sender_model');
	    $this->load->model('masters/payment_methods_model');
			$this->load->model('masters/warehouse_model');
	    $this->load->model('address/address_model');
			$this->load->helper('sender');

	    $this->user = 'api@sokochan';
			$this->logs_json = is_true(getConfig('SOKOJUNG_LOG_JSON'));
		}
		else
		{
			$arr = array(
				'status' => FALSE,
				'error' => "Access denied"
			);

			$this->response($arr, 400);
		}
  }


  public function cancel_put()
  {
    $sc = TRUE;

    if( ! $this->api)
    {
      if($this->logs_json)
      {
        $arr = array(
          'status' => FALSE,
          'error' => 'API Not Enabled'
        );

        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' =>'ORDER',
          'code' => NULL,
          'action' => 'cancel',
          'status' => 'failed',
          'message' => 'API Not Enabled',
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->soko_api_logs_model->add_api_logs($logs);
      }

      $this->response($arr, 400);
    }

    $json = file_get_contents("php://input");

    $data = json_decode($json);

    $this->api_path."/cancel";

    if(empty($data))
    {
      $arr = array(
        'status' => FALSE,
        'error' => 'empty data'
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' =>'ORDER',
          'code' => NULL,
          'action' => 'cancel',
          'status' => 'failed',
          'message' => 'empty data',
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->soko_api_logs_model->add_api_logs($logs);
      }

      $this->response($arr, 400);
    }


    if(empty($data->order_number))
    {
      $this->error = 'order_number is required';
      $this->soko_api_logs_model->add("", "E", $this->error, "");

      $arr = array(
        'status' => FALSE,
        'error' => $this->error
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' =>'ORDER',
          'code' => NULL,
          'action' => 'cancel',
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->soko_api_logs_model->add_api_logs($logs);
      }

      $this->response($arr, 400);
    }

    $code = $data->order_number;

    $order = $this->orders_model->get($code);

    if( ! empty($order))
    {
      if($order->state < 8 && $order->state != 9 && $order->is_wms == 2)
      {
        $this->load->model('inventory/prepare_model');
        $this->load->model('inventory/qc_model');
        $this->load->model('inventory/transform_model');
        $this->load->model('inventory/transfer_model');
        $this->load->model('inventory/delivery_order_model');
        $this->load->model('inventory/invoice_model');
        $this->load->model('inventory/buffer_model');
        $this->load->model('inventory/cancle_model');
    		$this->load->model('inventory/movement_model');
        $this->load->model('masters/zone_model');

        $this->db->trans_begin();

        $reason = array(
          'code' => $order->code,
          'reason_id' => empty($data->reason_group_id) ? NULL : $data->reason_group_id,
          'reason' => empty($data->cancel_reason) ? "No reason found from sokochan" : $data->cancel_reason,
          'user' => $this->user
        );

        if( ! $this->orders_model->add_cancle_reason($reason))
        {
          $sc = FALSE;
          $this->error = "Failed to add cancel reason";
        }

        if($sc === TRUE && $order->state > 3)
        {
          //--- put prepared product to cancle zone
          $prepared = $this->prepare_model->get_details($code);

          if(! empty($prepared))
          {
            foreach($prepared AS $rs)
            {
              if($sc === FALSE)
              {
                break;
              }

              $zone = $this->zone_model->get($rs->zone_code);

              $arr = array(
                'order_code' => $rs->order_code,
                'product_code' => $rs->product_code,
                'warehouse_code' => empty($zone->warehouse_code) ? NULL : $zone->warehouse_code,
                'zone_code' => $rs->zone_code,
                'qty' => $rs->qty,
                'user' => $this->user,
                'order_detail_id' => $rs->order_detail_id
              );

              if( ! $this->cancle_model->add($arr) )
              {
                $sc = FALSE;
                $this->error = "Move Items to Cancle failed";
              }
            }
          }

          //--- drop sold data
          if($sc === TRUE)
          {
            if( ! $this->invoice_model->drop_all_sold($code))
            {
              $sc = FALSE;
              $this->error = "Drop shipped data failed";
            }
          }
        }

        if($sc === TRUE)
        {
          //---- เมื่อมีการยกเลิกออเดอร์
          //--- 1. เคลียร์ buffer
          if(! $this->buffer_model->delete_all($code) )
          {
            $sc = FALSE;
            $this->error = "Delete buffer failed";
          }

          //--- 2. ลบประวัติการจัดสินค้า
          if($sc === TRUE)
          {
            if(! $this->prepare_model->clear_prepare($code) )
            {
              $sc = FALSE;
              $this->error = "Delete prepared data failed";
            }
          }


          //--- 3. ลบประวัติการตรวจสินค้า
          if($sc === TRUE)
          {
            if(! $this->qc_model->clear_qc($code) )
            {
              $sc = FALSE;
              $this->error = "Delete QC failed";
            }
          }

    			//--- remove movement
    	    if($sc === TRUE)
    	    {
    	      if(! $this->movement_model->drop_movement($code) )
    	      {
    	        $sc = FALSE;
    	        $this->error = "Drop movement failed";
    	      }
    	    }


          //--- 4. set รายการสั่งซื้อ ให้เป็น ยกเลิก
          if($sc === TRUE)
          {
            if(! $this->orders_model->cancle_order_detail($code) )
            {
              $sc = FALSE;
              $this->error = "Cancle Order details failed";
            }
          }


          //--- 5. ยกเลิกออเดอร์
          if($sc === TRUE)
          {
            $arr = array(
              'state' => 9,
              'status' => 2,
              'inv_code' => NULL,
              'is_exported' => 0,
              'is_report' => NULL
            );

            if( ! $this->orders_model->update($code, $arr) )
            {
              $sc = FALSE;
              $this->error = "Change order status failed";
            }
          }

          //--- 6. add order state change
          if($sc === TRUE)
          {
            $arr = array(
              'order_code' => $code,
              'state' => 9,
              'update_user' => $this->user
            );

            if( ! $this->order_state_model->add_state($arr) )
            {
              $sc = FALSE;
              $this->error = "Add state failed";
            }
          }


          if($sc === TRUE)
          {
            //--- 6. ลบรายการที่ผู้ไว้ใน order_transform_detail (กรณีเบิกแปรสภาพ)
            if($order->role == 'T' OR $order->role == 'Q')
            {
              if(! $this->transform_model->clear_transform_detail($code) )
              {
                $sc = FALSE;
                $this->error = "Clear Transform backlogs failed";
              }

              $this->transform_model->close_transform($code);
            }

            //-- หากเป็นออเดอร์ยืม
            if($order->role == 'L')
            {
              if(! $this->lend_model->drop_backlogs_list($code) )
              {
                $sc = FALSE;
                $this->error = "Drop Lend backlogs failed";
              }
            }

            //---- ถ้าเป็นฝากขายโอนคลัง ตามไปลบ transfer draft ที่ยังไม่เอาเข้าด้วย
            if($order->role == 'N')
            {
              $middle = $this->transfer_model->get_middle_transfer_draft($code);

              if( ! empty($middle))
              {
                foreach($middle as $rows)
                {
                  $this->transfer_model->drop_middle_transfer_draft($rows->DocEntry);
                }
              }
            }
            else if($order->role == 'T' OR $order->role == 'Q' OR $order->role == 'L')
            {
              $middle = $this->transfer_model->get_middle_transfer_doc($code);

              if( ! empty($middle))
              {
                foreach($middle as $rows)
                {
                  $this->transfer_model->drop_middle_exits_data($rows->DocEntry);
                }
              }
            }
            else
            {
              //---- ถ้าออเดอร์ยังไม่ถูกเอาเข้า SAP ลบออกจากถังกลางด้วย
              $middle = $this->delivery_order_model->get_middle_delivery_order($code);

              if( ! empty($middle))
              {
                foreach($middle as $rows)
                {
                  $this->delivery_order_model->drop_middle_exits_data($rows->DocEntry);
                }
              }
            }
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
      else
      {
        if($order->state == 8 && $order->is_wms == 2)
        {
          $sc = FALSE;
          $this->error = "Order number {$code} already shipped cannot be cancel.";
        }

        if($order->state < 8 && $order->is_wms != 2)
        {
          $sc = FALSE;
          $this->error = "Order number {$code} are not interface to Sokochan cannot be cancel.";
        }

        if($order->state == 9 && $order->is_wms == 2)
        {
          $arr = array(
            'order_code' => $code,
            'state' => 9,
            'update_user' => $this->user
          );

          $this->order_state_model->add_state($arr);
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Invalid order_number: {$code}";
    }

    if($sc === TRUE)
    {
      $this->soko_api_logs_model->add("cancel", "S", NULL, $code);
      //--- logs result
      $arr = array(
        'status' => 'success',
        'message' => "The Order {$code} Successfully Cancelled.",
        'order_number' => $code
      );

      if($this->logs_json)
			{
				$logs = array(
					'trans_id' => genUid(),
					'api_path' => $this->api_path,
					'type' => 'ORDER',
					'code' => $code,
					'action' => 'cancel',
					'status' => 'success',
					'message' => 'success',
					'request_json' => $json,
					'response_json' => json_encode($arr)
				);

				$this->soko_api_logs_model->add_api_logs($logs);
			}

			$this->response($arr, 200);
    }
    else
    {
      $this->soko_api_logs_model->add("cancel", "E", NULL, $code);
      //--- logs result
      $arr = array(
        'status' => FALSE,
        'message' => $this->error,
        'order_number' => $code
      );

      if($this->logs_json)
			{
				$logs = array(
					'trans_id' => genUid(),
					'api_path' => $this->api_path,
					'type' => 'ORDER',
					'code' => $code,
					'action' => 'cancel',
					'status' => 'failed',
					'message' => $this->error,
					'request_json' => $json,
					'response_json' => json_encode($arr)
				);

				$this->soko_api_logs_model->add_api_logs($logs);
			}

			$this->response($arr, 200);
    }
  } //--- end cancel


  public function create_post()
  {
    $this->api_path = $this->api_path."/create";
    //--- Get raw post data
    $json = file_get_contents("php://input");

    if( ! $this->api)
    {
      $arr = array(
        'status' => FALSE,
        'error' => 'API Not Enabled'
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' =>'ORDER',
          'code' => NULL,
          'action' => 'create',
          'status' => 'failed',
          'message' => 'API Not Enabled',
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->soko_api_logs_model->add_api_logs($logs);
      }

      $this->response($arr, 400);
    }

    $data = json_decode($json);

    if(empty($data))
    {
      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' =>'ORDER',
          'code' => NULL,
          'action' => 'create',
          'status' => 'failed',
          'message' => 'empty data',
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->soko_api_logs_model->add_api_logs($logs);
      }

      $arr = array(
      'status' => FALSE,
      'error' => 'empty data'
      );

      $this->response($arr, 400);
    }

    if(! property_exists($data, 'order_number') OR $data->order_number == '')
    {
      $this->error = 'order_number is required';
      $this->soko_api_logs_model->add("", "E", $this->error, "");

      $arr = array(
      'status' => FALSE,
      'error' => $this->error
      );

      if($this->logs_json)
      {
        $logs = array(
        'trans_id' => genUid(),
        'api_path' => $this->api_path,
        'type' =>'ORDER',
        'code' => NULL,
        'action' => 'create',
        'status' => 'failed',
        'message' => $this->error,
        'request_json' => $json,
        'response_json' => json_encode($arr)
        );

        $this->soko_api_logs_model->add_api_logs($logs);
      }

      $this->response($arr, 400);
    }

    $sc = $this->verify_data($data);

    //---- if any error return
    if($sc === FALSE)
    {
      $arr = array(
      'status' => FALSE,
      'error' => $this->error
      );

      $this->soko_api_logs_model->add($data->order_number, "E", $this->error, "");

      if($this->logs_json)
      {
        $logs = array(
        'trans_id' => genUid(),
        'api_path' => $this->api_path,
        'type' =>'ORDER',
        'code' => $data->order_number,
        'action' => 'create',
        'status' => 'failed',
        'message' => $this->error,
        'request_json' => $json,
        'response_json' => json_encode($arr)
        );

        $this->soko_api_logs_model->add_api_logs($logs);
      }

      $this->response($arr, 400);
    }

    //--- check each item code
    $details = $data->details;

    if(empty($details))
    {
      $sc = FALSE;
      $this->error = "Items not found";
      $this->soko_api_logs_model->add($data->order_number, "E", $this->error, "");

      $arr = array(
        'status' => FALSE,
        'error' => $this->error
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' =>'ORDER',
          'code' => $data->order_number,
          'action' => 'create',
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->soko_api_logs_model->add_api_logs($logs);
      }

      $this->response($arr, 400);
    }


    if( ! empty($details))
    {
      foreach($details as $rs)
      {
        //---- check valid items
        $item = $this->products_model->get_with_old_code($rs->item);

        if(empty($item))
        {
          $sc = FALSE;
          $this->error = "Invalid SKU : {$rs->item}";
        }
        else
        {
          $rs->item = $item;
        }
      }
    }


    //---- if any error return
    if($sc === FALSE)
    {
      $this->soko_api_logs_model->add($data->order_number, "E", $this->error, "");

      $arr = array(
      'status' => FALSE,
      'error' => $this->error
      );

      if($this->logs_json)
      {
        $logs = array(
        'trans_id' => genUid(),
        'api_path' => $this->api_path,
        'type' =>'ORDER',
        'code' => $data->order_number,
        'action' => 'create',
        'status' => 'failed',
        'message' => $this->error,
        'request_json' => $json,
        'response_json' => json_encode($arr)
        );

        $this->soko_api_logs_model->add_api_logs($logs);
      }

      $this->response($arr, 400);
    }

    //---- new code start
    if($sc === TRUE)
    {
      //---- check duplicate order number
      $order = $this->orders_model->get_active_order_by_reference($data->order_number);

      //--- รหัสเล่มเอกสาร [อ้างอิงจาก SAP]
      //--- ถ้าเป็นฝากขายแบบโอนคลัง ยืมสินค้า เบิกแปรสภาพ เบิกสินค้า (ไม่เปิดใบกำกับ เปิดใบโอนคลังแทน) นอกนั้น เปิด SO
      $bookcode = getConfig('BOOK_CODE_ORDER');

      $role = 'S';

      $date_add = date('Y-m-d H:i:s');

      $ref_code = $data->order_number;

      $customer = $this->customers_model->get($data->customer_code);

      $sale_code = empty($customer) ? -1 : $customer->sale_code;

      $state = 3;

      $warehouse_code = getConfig('SOKOJUNG_WAREHOUSE');

      $is_wms = 2;

      //---- id_sender
      $sender = $this->sender_model->get_id($data->shipping);

      $id_sender = empty($sender) ? NULL : $sender;

      //--- order code gen จากระบบ
      $order_code = empty($order) ? $this->get_new_code($date_add) : $order->code;

      $tracking = $data->tracking_no;

      $total_amount = 0;

      if(empty($order))
      {
        //--- เตรียมข้อมูลสำหรับเพิ่มเอกสารใหม่
        $ds = array(
          'code' => $order_code,
          'role' => $role,
          'bookcode' => $bookcode,
          'reference' => $data->order_number,
          'customer_code' => $data->customer_code,
          'customer_name' => $data->customer_name,
          'customer_ref' => $data->customer_ref,
          'channels_code' => $data->channel,
          'payment_code' => $data->payment_method,
          'sale_code' => $sale_code,
          'state' => 3,
          'is_term' => $data->payment_method === "COD" ? 1 : 0,
          'status' => 1,
          'shipping_code' => $tracking,
          'user' => $this->user,
          'date_add' => $date_add,
          'warehouse_code' => $warehouse_code,
          'is_api' => 1,
          'id_sender' => $id_sender,
          'is_wms' => $is_wms,
          'wms_export' => 1
        );
      }
      else
      {
        //--- เตรียมข้อมูลสำหรับเพิ่มเอกสารใหม่
        $ds = array(
          'customer_code' => $data->customer_code,
          'customer_name' => $data->customer_name,
          'customer_ref' => $data->customer_ref,
          'channels_code' => $data->channel,
          'payment_code' => $data->payment_method,
          'sale_code' => $sale_code,
          'state' => 3,
          'is_term' => $data->payment_method === "COD" ? 1 : 0,
          'shipping_code' => $tracking,
          'user' => $this->user,
          'date_add' => $date_add,
          'warehouse_code' => $warehouse_code,
          'is_api' => 1,
          'id_sender' => $id_sender,
          'is_wms' => $is_wms,
          'wms_export' => 1
        );
      }

      $this->db->trans_begin();

      if(empty($order))
      {
        if(  ! $this->orders_model->add($ds))
        {
          $sc = FALSE;
          $this->error = "Order create failed";
        }
      }
      else
      {
        if( ! $this->orders_model->update($order->code, $ds))
        {
          $sc = FALSE;
          $this->error = "Failed to update order";
        }
      }


      if($sc === TRUE)
      {
        $arr = array(
          'order_code' => $order_code,
          'state' => 1,
          'update_user' => $this->user
        );

        //--- add state event
        $this->order_state_model->add_state($arr);

        $id_address = $this->address_model->get_id($data->customer_ref, $data->ship_to->address);

        if($id_address === FALSE)
        {
          $arr = array(
            'code' => $data->customer_ref,
            'name' => $data->ship_to->name,
            'address' => $data->ship_to->address,
            'sub_district' => $data->ship_to->sub_district,
            'district' => $data->ship_to->district,
            'province' => $data->ship_to->province,
            'postcode' => $data->ship_to->postcode,
            'phone' => $data->ship_to->phone,
            'email' => $data->ship_to->email,
            'alias' => empty($data->alias) ? 'Home' : $data->alias,
            'is_default' => 1
          );

          $id_address = $this->address_model->add_shipping_address($arr);
        }

        $this->orders_model->set_address_id($order_code, $id_address);

        //---- add order details
        $details = $data->details;

        if(! empty($details))
        {
          if( ! empty($order))
          {
            if( ! $this->orders_model->remove_all_details($order->code))
            {
              $sc = FALSE;
              $this->error = "Failed to delete previous order items";
            }
          }

          if($sc === TRUE)
          {
            foreach($details as $rs)
            {
              if($sc === FALSE)
              {
                break;
              }

              if( ! empty($rs->item))
              {
                //--- check item code
                $item = $rs->item;
                $disc = $rs->discount > 0 ? $rs->discount/$rs->qty : 0;
                //--- ถ้ายังไม่มีรายการอยู่ เพิ่มใหม่
                $arr = array(
                  "order_code"	=> $order_code,
                  "style_code"		=> $item->style_code,
                  "product_code"	=> $item->code,
                  "product_name"	=> $item->name,
                  "cost"  => $item->cost,
                  "price"	=> $rs->price, //--- price bef disc
                  "qty"		=> $rs->qty,
                  "discount1"	=> round($disc, 2),
                  "discount2" => 0,
                  "discount3" => 0,
                  "discount_amount" => $rs->discount, //--- discount per item * qty
                  "total_amount"	=> round($rs->amount, 2),
                  "id_rule"	=> NULL,
                  "is_count" => $item->count_stock,
                  "is_api" => 1
                );

                if( ! $this->orders_model->add_detail($arr))
                {
                  $sc = FALSE;
                  $this->error = "Order item insert failed : {$item->code}";
                  break;
                }
                else
                {
                  $total_amount += round($rs->amount, 2);
                }
              } //--- end if item
            }  //--- endforeach add details
          }

          if($sc === TRUE)
          {
            if($this->orders_model->change_state($order_code, 3))
            {
              $arr = array(
                'order_code' => $order_code,
                'state' => 3,
                'update_user' => $this->user
              );

              $this->order_state_model->add_state($arr);
            }
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Items not found";
        }
      } //--- if add order

      if($sc === TRUE)
      {
        $this->db->trans_commit();

        $this->soko_api_logs_model->add($data->order_number, "S", "success", $order_code);

        $arr = array(
          'status' => 'success',
          'message' => 'success',
          'order_code' => $order_code
        );

        if($this->logs_json)
        {
          $logs = array(
            'trans_id' => genUid(),
            'api_path' => $this->api_path,
            'type' =>'ORDER',
            'code' => $data->order_number,
            'action' => 'create',
            'status' => 'success',
            'message' => 'success',
            'request_json' => $json,
            'response_json' => json_encode($arr)
          );

          $this->soko_api_logs_model->add_api_logs($logs);
        }

        $this->soko_api_logs_model->add($data->order_number, 'S', 'success', $order_code);

        $this->response($arr, 200);
      }
      else
      {
        $this->db->trans_rollback();
        $this->soko_api_logs_model->add($data->order_number, "E", $this->error, "");

        $arr = array(
          'status' => FALSE,
          'error' => $this->error
        );

        if($this->logs_json)
        {
          $logs = array(
            'trans_id' => genUid(),
            'api_path' => $this->api_path,
            'type' =>'ORDER',
            'code' => $data->order_number,
            'action' => 'create',
            'status' => 'failed',
            'message' => $this->error,
            'request_json' => $json,
            'response_json' => json_encode($arr)
          );

          $this->soko_api_logs_model->add_api_logs($logs);
        }

        $this->soko_api_logs_model->add($arr);

        $this->response($arr, 200);
      }
    }
  } //--- create_post


  public function update_status_put()
  {
    $sc = TRUE;
    //--- Get raw post data
    $json = file_get_contents("php://input");

    $data = json_decode($json);

    $this->api_path."/update_status";

    if(empty($data))
    {
      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' =>'ORDER',
          'code' => NULL,
          'action' => 'Update',
          'status' => 'failed',
          'message' => 'empty data',
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->soko_api_logs_model->add_api_logs($logs);
      }

      $arr = array(
        'status' => FALSE,
        'error' => 'empty data'
      );

      $this->response($arr, 400);
    }

    if(! property_exists($data, 'order_number') OR $data->order_number == '')
    {
      $this->error = 'order_number is required';
      $this->soko_api_logs_model->add("", "E", $this->error, "");

      $arr = array(
        'status' => FALSE,
        'error' => $this->error
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' =>'ORDER',
          'code' => NULL,
          'action' => 'Update',
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->soko_api_logs_model->add_api_logs($logs);
      }

      $this->response($arr, 400);
    }

    if(! property_exists($data, 'order_status') OR empty($data->order_status))
    {
      $this->error = 'order_status is required';
      $this->soko_api_logs_model->add("", "E", $this->error, "");

      $arr = array(
      'status' => FALSE,
      'error' => $this->error
      );

      if($this->logs_json)
      {
        $logs = array(
        'trans_id' => genUid(),
        'api_path' => $this->api_path,
        'type' =>'ORDER',
        'code' => NULL,
        'action' => 'Update',
        'status' => 'failed',
        'message' => $this->error,
        'request_json' => $json,
        'response_json' => json_encode($arr)
        );

        $this->soko_api_logs_model->add_api_logs($logs);
      }

      $this->response($arr, 400);
    }

    $order = $this->orders_model->get($data->order_number);

    if( ! empty($order))
    {
      $stateList = array(
        'Pending' => 30,
        'Processing' => 31,
        'Processed' => 32,
        'Picked' => 33,
        'Packed' => 34,
        'Shipped' => 35,
        'Cancelled' => 36,
        'pending' => 30,
        'processing' => 31,
        'processed' => 32,
        'picked' => 33,
        'packed' => 34,
        'shipped' => 35,
        'cancelled' => 36
      );

      if( ! empty($stateList[$data->order_status]))
      {
        $state = $stateList[$data->order_status];

        $arr = array(
          'order_code' => $order->code,
          'state' => $state,
          'update_user' => $this->user,
          'date_upd' => date('Y-m-d H:i:s', strtotime($data->update_time))
        );

        if( ! $this->order_state_model->add_wms_state($arr))
        {
          $sc = FALSE;
          $this->error = "Failed to update order status";
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid order status";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Order number does not exist";
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : FALSE,
      'message' => $sc === TRUE ? 'Order status updated' : NULL,
      'error' => $sc === TRUE ? NULL : $this->error
    );

    $this->response($arr, 200);
  }


  public function get_new_code($date)
  {
    $date = $date == '' ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_ORDER');
    $run_digit = getConfig('RUN_DIGIT_ORDER');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->orders_model->get_max_code($pre);
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



  public function verify_data($data)
	{
    $paymentList = [
      'COD' => 'COD',
      'CARD' => 'CARD'
    ];

    $channelsList = [
      'LAZADA' => 'LAZADA',
      'SHOPEE' => 'Shopee',
      '0009' => 'TIKTOK'
    ];

    $custList = [
      'CLON04-0001' => 'บริษัท ช้อปปี้ (ประเทศไทย) จำกัด สำนักงานใหญ่',
      'CLON01-0001' => 'บริษัท ลาซาด้า จำกัด (สำนักงานใหญ่)',
      'CLON13-0001' => 'TIK TOK PTE. LTD.',
      'CLON16-0001' => 'TIKTok Shop (Thailand) Ltd.'
    ];

    if(! property_exists($data, 'customer_code') OR $data->customer_code == '')
    {
      $this->error = 'customer_code is required';
			return FALSE;
    }


		if(! property_exists($data, 'customer_ref') OR $data->customer_ref == '')
		{
			$this->error = "customer_ref is required";
			return FALSE;
		}

    if(! property_exists($data, 'channel') OR (empty($channelsList[$data->channel])))
    {
      $this->error = "Invalid channels code : {$data->channel}";
			return FALSE;
    }

    if( ! property_exists($data, 'payment_method') OR (empty($paymentList[$data->payment_method])))
    {
      $this->error = 'Invalic payment_method code';
			return FALSE;
    }

		if( ! empty($data->customer_code) && (empty($custList[$data->customer_code])))
		{
      $this->error = "Invalid Customer Code";
      return FALSE;
		}


		if(! property_exists($data, 'shipping'))
		{
			$this->error = "Invalid Shipping is required";
			return FALSE;
		}

    if(! property_exists($data, 'ship_to'))
    {
      $this->error = 'ship_to is required';
			return FALSE;
    }

    if(! property_exists($data->ship_to, 'name'))
    {
      $this->error = 'shipping name is required';
			return FALSE;
    }

    if(! property_exists($data->ship_to, 'address'))
    {
      $this->error = 'shipping address is required';
			return FALSE;
    }

    if(! property_exists($data->ship_to, 'district'))
    {
      $this->error = 'district is required';
			return FALSE;
    }


    if(! property_exists($data->ship_to, 'province'))
    {
      $this->error = 'province is required';
			return FALSE;
    }

    if(! property_exists($data->ship_to, 'phone'))
    {
      $this->error = 'phone is required';
			return FALSE;
    }


    if($this->orders_model->is_active_order_reference($data->order_number) !== FALSE)
    {
      $this->error = 'Order number already exists';
			return FALSE;
    }


		return TRUE;
	}


} //--- end class

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
	public $api_path = "rest/api/ix/orders";
	public $logs;
	public $log_json = FALSE;
	public $api = FALSE;
  public $checkBackorder = FALSE;
  public $sync_api_stock = FALSE;
  private $type = 'ORDER';

  public function __construct()
  {
    parent::__construct();
		$this->api = is_true(getConfig('IX_API'));

		if($this->api)
		{
      $this->wms = $this->load->database('wms', TRUE); //--- Temp database
      $this->ms = $this->load->database('ms', TRUE);
      $this->mc = $this->load->database('mc', TRUE);
      $this->load->model('rest/V1/ix_api_logs_model');

	    $this->load->model('orders/orders_model');
	    $this->load->model('orders/order_state_model');
	    $this->load->model('masters/products_model');
	    $this->load->model('masters/customers_model');
	    $this->load->model('masters/channels_model');
			$this->load->model('masters/sender_model');
	    $this->load->model('masters/payment_methods_model');
			$this->load->model('masters/warehouse_model');
      $this->load->model('masters/zone_model');
	    $this->load->model('address/address_model');
      $this->load->model('stock/stock_model');
      $this->load->model('orders/reserv_stock_model');
			$this->load->helper('sender');
      $this->load->helper('discount');

	    $this->user = 'api@warrix';
			$this->logs_json = is_true(getConfig('IX_LOG_JSON'));
      $this->checkBackorder = is_true(getConfig('IX_BACK_ORDER'));
      $this->sync_api_stock = is_true(getConfig('SYNC_IX_STOCK'));
		}
		else
		{
			$arr = array(
				'status' => FALSE,
				'error' => "Access denied",
        'retry' => FALSE
			);

			$this->response($arr, 400);
		}
  }


  public function create_post()
  {
    $sc = TRUE;
    $action = 'create';
    $this->api_path = $this->api_path."/create";
    //--- Get raw post data
    $json = file_get_contents("php://input");

    if( ! $this->api)
    {
      $arr = array(
        'status' => FALSE,
        'error' => 'API Not Enabled',
        'retry' => FALSE
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => NULL,
          'action' => $action,
          'status' => 'failed',
          'message' => 'API Not Enabled',
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->ix_api_logs_model->add_logs($logs);
      }

      $this->response($arr, 400);
    }

    $data = json_decode($json);

    if(empty($data))
    {
      $arr = array(
        'status' => FALSE,
        'error' => 'empty data',
        'retry' => FALSE
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => NULL,
          'action' => $action,
          'status' => 'failed',
          'message' => 'empty data',
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->ix_api_logs_model->add_logs($logs);
      }

      $this->response($arr, 400);
    }

    if(! property_exists($data, 'order_number') OR $data->order_number == '')
    {
      $this->error = 'order_number is required';

      $arr = array(
        'status' => FALSE,
        'error' => $this->error,
        'retry' => FALSE
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => NULL,
          'action' => $action,
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->ix_api_logs_model->add_logs($logs);
      }

      $this->response($arr, 400);
    }

    $role = 'S';
    $bookcode = getConfig('BOOK_CODE_ORDER');
    $warehouse_code = (isset($data->warehouse) && ! empty($data->warehouse)) ? $data->warehouse : getConfig('IX_WAREHOUSE');
    $customer = NULL;
    $zone_code = NULL;
    $channels_code = NULL;
    $payment_code = NULL;
    $payment_role = NULL;
    $is_term = 0;
    $GP = '0.00';
    $user_ref = NULL;
    $tax_status = empty($data->tax_status) ? 0 : ($data->tax_status == 'Y' ? 1 : 0);
    $is_etax = empty($data->ETAX) ? 0 : ($data->ETAX == 'Y' && $tax_status == 1 ? 1 : 0);
    $bill_to = empty($data->bill_to) ? NULL : (array) $data->bill_to;
    $ship_to = empty($data->ship_to) ? NULL : (array) $data->ship_to;
    $customer_ref = empty(trim($data->customer_ref)) ? NULL : get_null(trim($data->customer_ref));

    $taxType = array(
      'NIDN' => 'NIDN', //-- บุคคลธรรมดา
      'TXID' => 'TXID', //-- นิติบุคคล
      'CCPT' => 'CCPT', //--- Passport
      'OTHR' => 'OTHR' //--- N/A
    );

    $warehouse = $this->warehouse_model->get($warehouse_code);

    if(empty($warehouse))
    {
      $sc = FALSE;
      $this->error = "Invalid warehouse_code";
    }

    if($sc === TRUE)
    {
      if(isset($data->order_type))
      {
        $ot = $data->order_type;

        if($ot != 'WO' && $ot != 'WQ')
        {
          $sc = FALSE;
          $this->error = "Invalid order_type : allowed WO, WQ";
        }

        if($sc === TRUE)
        {
          $role = $ot == 'WQ' ? 'T' : ($ot == 'WC' ? 'C' : 'S');
          $bookcode = $ot == 'WQ' ? getConfig('BOOK_CODE_TRANSFORM') : ($ot == 'WC' ? getConfig('BOOK_CODE_CONSIGN_SO') : getConfig('BOOK_CODE_ORDER'));
        }
      }
    }

    if($sc === TRUE)
    {
      $sc = $this->verify_data($data, $role);
    }

    //-- check bill_to
    if($sc === TRUE)
    {
      if($tax_status)
      {
        if( ! empty($bill_to))
        {
          $bill_to = (object) $bill_to;

          if(
            empty($bill_to->tax_id)
            OR empty($bill_to->name)
            OR empty($bill_to->address)
            OR empty($bill_to->sub_district)
            OR empty($bill_to->district)
            OR empty($bill_to->province)
          )
          {
            $sc = FALSE;
            $this->error = "You must fill in all required fields [tax_id, name, address, sub_district, district, province]";
          }

          $email = empty($bill_to->email) ? NULL : $bill_to->email;

          if($is_etax == 1 && empty($email))
          {
            $sc = FALSE;
            $this->error = "Email is required for E-TAX";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "bill_to is required for tax_status = Y";
        }
      }
      else
      {
        $bill_to['tax_type'] = NULL;
        $bill_to['tax_id'] = NULL;
        $bill_to['name'] = NULL;
        $bill_to['branch_code'] = NULL;
        $bill_to['branch_name'] = NULL;
        $bill_to['address'] = NULL;
        $bill_to['sub_district'] = NULL;
        $bill_to['district'] = NULL;
        $bill_to['province'] = NULL;
        $bill_to['postcode'] = NULL;
        $bill_to['phone'] = NULL;
        $bill_to['email'] = NULL;

        $bill_to = (object) $bill_to;
      }
    }

    //---- if any error return
    if($sc === FALSE)
    {
      $arr = array(
        'status' => FALSE,
        'error' => $this->error,
        'retry' => FALSE
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => $data->order_number,
          'action' => $action,
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->ix_api_logs_model->add_logs($logs);
      }

      $this->response($arr, 400);
    }

    //--- check each item code
    $details = $data->details;

    if(empty($details))
    {
      $sc = FALSE;
      $this->error = "Items not found";

      $arr = array(
        'status' => FALSE,
        'error' => $this->error,
        'retry' => FALSE
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => $data->order_number,
          'action' => $action,
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->ix_api_logs_model->add_logs($logs);
      }

      $this->response($arr, 400);
    }

    //---- for check duplicate order number
    $order = $this->orders_model->get_active_order_by_reference(trim($data->order_number));

    if($sc === TRUE && ! empty($order))
    {
      if($order->role != $role)
      {
        $sc = FALSE;
        $this->error = "Failed to update order : order_type mismatch to current active order";
      }
    }

    $docTotal = 0;

    if($sc === TRUE && ! empty($details))
    {
      foreach($details as $rs)
      {
        if($sc === FALSE) { break; }
        //---- check valid items
        $item = $this->products_model->get($rs->item);
        $item = empty($item) ? $this->products_model->get_by_old_code($rs->item) : $item;

        if(empty($item))
        {
          $sc = FALSE;
          $this->error = "Invalid SKU : {$rs->item}";
        }
        else
        {
          $rs->item = $item;
          $docTotal += $rs->amount;
        }
      }
    }

    //---- if any error return with status code 200
    if($sc === FALSE)
    {
      $arr = array(
        'status' => FALSE,
        'error' => $this->error,
        'retry' => FALSE
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => $data->order_number,
          'action' => $action,
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->ix_api_logs_model->add_logs($logs);
      }

      $this->response($arr, 200);
    }

    //--- check customer
    if($sc === TRUE)
    {
      $customer = $this->customers_model->get($data->customer_code);

      if(empty($customer))
      {
        $sc = FALSE;
        $this->error = "Invalid customer code or customer not found";
      }

      if( ! empty($customer) && $customer->active == 0)
      {
        $sc = FALSE;
        $this->error = "Customer code '{$data->customer_code}' is inactive";
      }
    }

    //--- check channels
    if($sc === TRUE && $role == 'S')
    {
      $channels = $this->channels_model->get($data->channel);

      if(empty($channels))
      {
        $sc = FALSE;
        $this->error = "Invalid channel code";
      }
      else
      {
        $channels_code = $channels->code;
      }
    }

    //--- check payment_method
    if($sc === TRUE && $role == 'S')
    {
      $pm = $this->payment_methods_model->get($data->payment_method);

      if( ! empty($pm))
      {
        $payment_code = $pm->code;
        $payment_role = $pm->role;
        $is_term = $pm->has_term;
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid payment_method";
      }
    }

    //--- check over due
    if($sc === TRUE && $role == 'S')
    {
      //--- check over due
      if(is_true(getConfig('STRICT_OVER_DUE')))
      {
        $this->load->model('inventory/invoice_model');

        $overDue = ! $customer->skip_overdue ? $this->invoice_model->is_over_due($data->customer_code) : FALSE;

        //--- ถ้ามียอดค้างชำระ และ เป็นออเดอร์แบบเครดิต
        //--- ไม่ให้เพิ่มออเดอร์
        if($overDue)
        {
          $sc = FALSE;
          $this->error = 'There is an outstanding balance that is past due. Sales are not permitted.';
        }
      }
    }

    //--- check zone and customer and gp
    if($sc === TRUE && $role == 'C')
    {
      $zone_code = $data->zone_code;
      $is_exists = $this->zone_model->is_exists_customer($zone_code, $data->customer_code);

      if( ! $is_exists)
      {
        $sc = FALSE;
        $this->error = "Invalid zone_code OR zone_code not link to customer";
      }

      if($sc === TRUE)
      {
        $gp = explode('%', $data->GP);

        $GP = empty($gp[0]) ? '0.00' : trim($gp[0]).'%';
      }
    }

    //-- check credit limit
    if($sc === TRUE && (($role == 'S' && $payment_role == 5) OR $role == 'C'))
    {
      if( ! is_true(getConfig('CONTROL_CREDIT')))
      {
        //--- creadit used
        $credit_used = 0;

        if(empty($order))
        {
          $credit_used = round($this->orders_model->get_sum_not_complete_amount($data->customer_code), 2);
        }
        else
        {
          $credit_used = round($this->orders_model->get_sum_not_complete_amount_exclude($data->customer_code, $order->code), 2);
        }

        $credit_used += $docTotal;

        //--- credit balance from sap
        $credit_balance = round($this->customers_model->get_credit($data->customer_code), 2);

        if($credit_used > $credit_balance)
        {
          $sc = FALSE;
          $diff = $credit_used - $credit_balance;
          $this->error = "Insufficient credit balance ".number($diff, 2)." more needed.";
        }
      }
    } //--- check credit limit

    //--- check user_ref for WQ
    if($sc === TRUE && $role == 'T')
    {
      $user_ref = $this->user_model->get_name($data->uname);

      if(empty($user_ref))
      {
        $sc = FALSE;
        $this->error = "Invalid uname or uname not found";
      }

      $zone_code = $sc === TRUE ? getConfig('TRANSFORM_ZONE') : NULL;
    }

    //---- if any error return with status code 200
    if($sc === FALSE)
    {
      $arr = array(
        'status' => FALSE,
        'error' => $this->error,
        'retry' => FALSE
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => $data->order_number,
          'action' => $action,
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->ix_api_logs_model->add_logs($logs);
      }

      $this->response($arr, 200);
    }

    //---- new code start
    if($sc === TRUE)
    {
      $date_add = date('Y-m-d H:i:s');
      $doc_date = empty($data->doc_date) ? NULL : db_date($data->doc_date, TRUE);
      $due_date = empty($data->due_date) ? NULL : db_date($data->due_date, TRUE);

      $ref_code = trim($data->order_number);

      $sale_code = empty($customer) ? -1 : $customer->sale_code;

      $state = $role == 'T' ? 1 : 3;

      $is_wms = 0;

      //---- id_sender
      $sender = $this->sender_model->get_id($data->shipping);

      $id_sender = empty($sender) ? NULL : $sender;
      $id_address = NULL;

      //--- order code gen จากระบบ
      $order_code = empty($order) ? $this->get_new_code($date_add, $role) : $order->code;

      $tracking = get_null($data->tracking_no);

      $total_amount = 0;
      $is_hold = empty($data->on_hold) ? 0 : ($data->on_hold == 'Y' ? 1 : 0);
      $is_pre_order = empty($data->is_pre_order) ? FALSE : (($data->is_pre_order == 'Y' OR $data->is_pre_order == 'y') ? TRUE : FALSE);
      $is_backorder = FALSE;
      $backorderList = [];
      $sync_stock = []; //--- keep product to sync stock

      if(empty($order))
      {
        //--- เตรียมข้อมูลสำหรับเพิ่มเอกสารใหม่
        $ds = array(
          'code' => $order_code,
          'role' => $role,
          'bookcode' => $bookcode,
          'reference' => $ref_code,
          'customer_code' => $customer->code,
          'customer_name' => $customer->name,
          'customer_ref' => $customer_ref,
          'channels_code' => $channels_code,
          'payment_code' => $payment_code,
          'sale_code' => $sale_code,
          'state' => $state,
          'is_term' => $is_term,
          'status' => 1,
          'shipping_code' => $tracking,
          'gp' => $GP,
          'user' => $this->user,
          'date_add' => $date_add,
          'doc_date' => $doc_date,
          'due_date' => $due_date,
          'warehouse_code' => $warehouse_code,
          'zone_code' => $zone_code,
          'user_ref' => $user_ref,
          'is_api' => 1,
          'is_pre_order' => $is_pre_order ? 1 : 0,
          'id_sender' => $id_sender,
          'is_wms' => $is_wms,
          'wms_export' => 0,
          'tax_status' => $tax_status,
          'is_etax' => $is_etax,
          'tax_type' => empty($taxType[$bill_to->tax_type]) ? "NIDN" : $bill_to->tax_type,
          'tax_id'=> get_null($bill_to->tax_id),
          'name'=> get_null($bill_to->name),
          'branch_code'=> empty($bill_to->branch_code) ? "00000" : $bill_to->branch_code,
          'branch_name'=> empty($bill_to->branch_name) ? "สำนักงานใหญ่" : $bill_to->branch_name,
          'address'=> get_null($bill_to->address),
          'sub_district'=> get_null($bill_to->sub_district),
          'district'=> get_null($bill_to->district),
          'province'=> get_null($bill_to->province),
          'postcode'=> get_null($bill_to->postcode),
          'phone'=> get_null($bill_to->phone),
          'email'=> get_null($bill_to->email)
        );
      }
      else
      {
        $ds = array(
          'customer_code' => $customer->code,
          'customer_name' => $customer->name,
          'customer_ref' => $customer_ref,
          'channels_code' => $channels_code,
          'payment_code' => $payment_code,
          'sale_code' => $sale_code,
          'state' => $state,
          'is_term' => $is_term,
          'status' => 1,
          'shipping_code' => $tracking,
          'gp' => $GP,
          'user' => $this->user,
          'date_add' => $date_add,
          'doc_date' => $doc_date,
          'due_date' => $due_date,
          'warehouse_code' => $warehouse_code,
          'zone_code' => $zone_code,
          'user_ref' => $user_ref,
          'is_api' => 1,
          'is_pre_order' => $is_pre_order ? 1 : 0,
          'id_sender' => $id_sender,
          'is_wms' => $is_wms,
          'wms_export' => 0,
          'tax_status' => $tax_status,
          'is_etax' => $is_etax,
          'tax_type' => empty($taxType[$bill_to->tax_type]) ? "NIDN" : $bill_to->tax_type,
          'tax_id'=> get_null($bill_to->tax_id),
          'name'=> get_null($bill_to->name),
          'branch_code'=> empty($bill_to->branch_code) ? "00000" : $bill_to->branch_code,
          'branch_name'=> empty($bill_to->branch_name) ? "สำนักงานใหญ่" : $bill_to->branch_name,
          'address'=> get_null($bill_to->address),
          'sub_district'=> get_null($bill_to->sub_district),
          'district'=> get_null($bill_to->district),
          'province'=> get_null($bill_to->province),
          'postcode'=> get_null($bill_to->postcode),
          'phone'=> get_null($bill_to->phone),
          'email'=> get_null($bill_to->email)
        );
      }

      $this->db->trans_begin();

      if(empty($order))
      {
        if(  ! $this->orders_model->add($ds))
        {
          $sc = FALSE;
          $this->error = "Failed to create order";
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
          'state' => $state,
          'update_user' => $this->user
        );

        //--- add state event
        $this->order_state_model->add_state($arr);


        if( ! empty($customer_ref) && ! empty($ship_to) && ! empty($ship_to->address))
        {
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
        }

        //---- add order details
        $details = $data->details;

        if( ! empty($details))
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

                if($role == 'S' && $data->channel == 'SHOPEE' && $rs->price == 0)
                {
                  $is_hold = 1;
                }

                //--- ถ้ายังไม่มีรายการอยู่ เพิ่มใหม่
                $arr = array(
                  "order_code"	=> $order_code,
                  "style_code"		=> $item->style_code,
                  "product_code"	=> $item->code,
                  "product_name"	=> $item->name,
                  "cost"  => $item->cost,
                  "price"	=> $rs->price, //--- price bef disc
                  "qty"		=> $rs->qty,
                  "discount1"	=> $role == 'C' ? discountAmountToPercent($rs->discount, $rs->qty, $rs->price).' %' : round($disc, 2),
                  "discount2" => 0,
                  "discount3" => 0,
                  "discount_amount" => $rs->discount, //--- discount per item * qty
                  "total_amount"	=> round($rs->amount, 2),
                  "id_rule"	=> NULL,
                  "is_count" => $item->count_stock,
                  "is_api" => 1,
                  "is_free" => isset($rs->is_free) ? ($rs->is_free == 'Y' ? 1 : 0) : 0
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

                  if($item->count_stock && ! $is_pre_order && ($this->sync_api_stock OR $this->checkBackorder))
                  {
                    $available = $this->get_available_stock($item->code, $warehouse_code);
                  }

                  if($this->checkBackorder && $item->count_stock && ! $is_pre_order)
                  {
                    if($available < $rs->qty)
                    {
                      $is_backorder = TRUE;

                      $backorderList[] = (object) array(
                        'order_code' => $order_code,
                        'product_code' => $item->code,
                        'order_qty' => $rs->qty,
                        'available_qty' => $available
                      );
                    }
                  }

                  if($this->sync_api_stock && $item->count_stock && $item->is_api && ! $is_pre_order)
          				{
                    $sync_stock[] = (object) array('code' => $item->code, 'rate' => $item->api_rate);
          				}
                }
              } //--- end if item
            }  //--- endforeach add details
          }

          if($sc === TRUE)
          {
            $arr = array(
              'doc_total' => $total_amount,
              'is_backorder' => $is_backorder == TRUE ? 1 : 0,
              'is_hold' => $is_hold
            );

            $this->orders_model->update($order_code, $arr);

            if($this->checkBackorder && ! empty($backorderList))
            {
              $this->orders_model->drop_backlogs_list($order_code);

              foreach($backorderList as $rs)
              {
                $backlogs = array(
                  'order_code' => $rs->order_code,
                  'product_code' => $rs->product_code,
                  'order_qty' => $rs->order_qty,
                  'available_qty' => $rs->available_qty
                );

                $this->orders_model->add_backlogs_detail($backlogs);
              }
            }

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

        $arr = array(
          'status' => 'success',
          'message' => 'success',
          'order_code' => $order_code,
          'url' => site_url("orders/orders/edit_order/{$order_code}")
        );

        if($this->logs_json)
        {
          $logs = array(
            'trans_id' => genUid(),
            'api_path' => $this->api_path,
            'type' => $this->type,
            'code' => $data->order_number,
            'action' => $action,
            'status' => 'success',
            'message' => 'success',
            'request_json' => $json,
            'response_json' => json_encode($arr)
          );

          $this->ix_api_logs_model->add_logs($logs);
        }

        if($this->sync_api_stock && ! empty($sync_stock))
        {
          $this->load->library('wrx_stock_api');
          $warehouse_code = getConfig('IX_WAREHOUSE');

          $i = 0;
          $j = 0;

          $items = [];

          foreach($sync_stock as $rs)
          {
            if($i == 20)
            {
              $i = 0;
              $j++;
            }

            $items[$j][$i] = $rs;
            $i++;
          }

          foreach($items as $item)
          {
            $this->wrx_stock_api->update_available_stock($item, $warehouse_code);
          }
        }

        $this->response($arr, 200);
      }
      else
      {
        $this->db->trans_rollback();

        $arr = array(
          'status' => FALSE,
          'error' => $this->error,
          'retry' => TRUE
        );

        if($this->logs_json)
        {
          $logs = array(
            'trans_id' => genUid(),
            'api_path' => $this->api_path,
            'type' => $this->type,
            'code' => $data->order_number,
            'action' => $action,
            'status' => 'failed',
            'message' => $this->error,
            'request_json' => $json,
            'response_json' => json_encode($arr)
          );

          $this->ix_api_logs_model->add_logs($logs);
        }

        $this->response($arr, 200);
      }
    }
  } //--- create_post


  public function cancel_put()
  {
    $sc = TRUE;
    $action = 'cancel';

    if( ! $this->api)
    {
      if($this->logs_json)
      {
        $arr = array(
          'status' => FALSE,
          'error' => 'API Not Enabled',
          'retry' => FALSE
        );

        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => NULL,
          'action' => $action,
          'status' => 'failed',
          'message' => 'API Not Enabled',
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->ix_api_logs_model->add_logs($logs);
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
        'error' => 'empty data',
        'retry' => FALSE
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => NULL,
          'action' => $action,
          'status' => 'failed',
          'message' => 'empty data',
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->ix_api_logs_model->add_logs($logs);
      }

      $this->response($arr, 400);
    }


    if(empty($data->order_number) && empty($data->order_code))
    {
      $this->error = 'order_number is required';

      $arr = array(
        'status' => FALSE,
        'error' => $this->error,
        'retry' => FALSE
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => NULL,
          'action' => $action,
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->ix_api_logs_model->add_logs($logs);
      }

      $this->response($arr, 400);
    }

    $code = empty($data->order_number) ? $data->order_code : $data->order_number;

    $order = empty($data->order_number) ? $this->orders_model->get($code) : $this->orders_model->get_order_by_reference($code);

    if(empty($order))
    {
      $this->error = "Invalid order_number: {$code}";

      $arr = array(
        'status' => FALSE,
        'error' => $this->error,
        'retry' => FALSE
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => NULL,
          'action' => $action,
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->ix_api_logs_model->add_logs($logs);
      }

      $this->response($arr, 400);
    }


    if($order->is_wms != 0)
    {
      $this->error = "This order belong to other fulfilment cannot cancel by this api";

      $arr = array(
        'status' => FALSE,
        'error' => $this->error,
        'retry' => FALSE
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => NULL,
          'action' => $action,
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->ix_api_logs_model->add_logs($logs);
      }

      $this->response($arr, 400);
    }


    if($sc === TRUE)
    {
      if($order->state < 8 && $order->state != 9)
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
          'reason' => empty($data->cancel_reason) ? "No reason for cancellation" : $data->cancel_reason,
          'user' => $this->user
        );

        $this->orders_model->add_cancle_reason($reason);

        if($sc === TRUE && $order->state > 3)
        {
          //--- put prepared product to cancle zone
          $prepared = $this->prepare_model->get_details($order->code);

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
            if( ! $this->invoice_model->drop_all_sold($order->code))
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
          if(! $this->buffer_model->delete_all($order->code) )
          {
            $sc = FALSE;
            $this->error = "Delete buffer failed";
          }

          //--- 2. ลบประวัติการจัดสินค้า
          if($sc === TRUE)
          {
            if(! $this->prepare_model->clear_prepare($order->code) )
            {
              $sc = FALSE;
              $this->error = "Delete prepared data failed";
            }
          }


          //--- 3. ลบประวัติการตรวจสินค้า
          if($sc === TRUE)
          {
            if(! $this->qc_model->clear_qc($order->code) )
            {
              $sc = FALSE;
              $this->error = "Delete QC failed";
            }
          }

    			//--- remove movement
    	    if($sc === TRUE)
    	    {
    	      if(! $this->movement_model->drop_movement($order->code) )
    	      {
    	        $sc = FALSE;
    	        $this->error = "Drop movement failed";
    	      }
    	    }


          //--- 4. set รายการสั่งซื้อ ให้เป็น ยกเลิก
          if($sc === TRUE)
          {
            if(! $this->orders_model->cancle_order_detail($order->code) )
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
              'is_backorder' => 0,
              'inv_code' => NULL,
              'is_exported' => 0,
              'is_report' => NULL
            );

            if( ! $this->orders_model->update($order->code, $arr) )
            {
              $sc = FALSE;
              $this->error = "Change order status failed";
            }
          }

          //--- 6. add order state change
          if($sc === TRUE)
          {
            $arr = array(
              'order_code' => $order->code,
              'state' => 9,
              'update_user' => $this->user
            );

            if( ! $this->order_state_model->add_state($arr) )
            {
              $sc = FALSE;
              $this->error = "Add state failed";
            }
          }

          //--- remove backorder details
          if($sc === TRUE && $order->is_backorder)
          {
            $this->orders_model->drop_backlogs_list($order->code);
          }


          if($sc === TRUE)
          {
            //--- 6. ลบรายการที่ผู้ไว้ใน order_transform_detail (กรณีเบิกแปรสภาพ)
            if($order->role == 'T' OR $order->role == 'Q')
            {
              if(! $this->transform_model->clear_transform_detail($order->code) )
              {
                $sc = FALSE;
                $this->error = "Clear Transform backlogs failed";
              }

              $this->transform_model->close_transform($order->code);
            }

            //-- หากเป็นออเดอร์ยืม
            if($order->role == 'L')
            {
              if(! $this->lend_model->drop_backlogs_list($order->code) )
              {
                $sc = FALSE;
                $this->error = "Drop Lend backlogs failed";
              }
            }

            //---- ถ้าเป็นฝากขายโอนคลัง ตามไปลบ transfer draft ที่ยังไม่เอาเข้าด้วย
            if($order->role == 'N')
            {
              $middle = $this->transfer_model->get_middle_transfer_draft($order->code);

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
              $middle = $this->transfer_model->get_middle_transfer_doc($order->code);

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
              $middle = $this->delivery_order_model->get_middle_delivery_order($order->code);

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


          if($this->sync_api_stock && ! $order->is_pre_order)
          {
            $ix_warehouse = getConfig('IX_WAREHOUSE');

            if($order->warehouse_code == $ix_warehouse)
            {
              $details = $this->orders_model->get_order_details($order->code);

              if( ! empty($details))
              {
                $sync_stock = [];

                foreach($details as $rs)
                {
                  if($rs->is_count && $rs->is_api)
                  {
                    $sync_stock[] = (object) array('code' => $rs->product_code, 'rate' => $rs->api_rate);
                  }
                }

                if( ! empty($sync_stock))
                {
                  $this->load->library('wrx_stock_api');

                  $i = 0;
                  $j = 0;

                  $items = [];

                  foreach($sync_stock as $rs)
                  {
                    if($i == 20)
                    {
                      $i = 0;
                      $j++;
                    }

                    $items[$j][$i] = $rs;
                    $i++;
                  }

                  foreach($items as $item)
                  {
                    $this->wrx_stock_api->update_available_stock($item, $ix_warehouse);
                  }
                }
              }
            }
          }
        }
        else
        {
          $this->db->trans_rollback();
        }
      }
      else
      {
        //--- add to cancel request
        if($order->state >= 8 && $order->state != 9)
        {
          $arr = array(
            'reference' => $order->reference,
            'order_code' => $order->code,
            'user' => $this->user
          );

          if( ! $this->orders_model->add_cancel_request($arr))
          {
            $sc = FALSE;
            $this->error = "Failed to create cancellation request";
          }
          else
          {
            $this->orders_model->update($order->code, ['is_cancled' => 1]);

            $arr = array(
              'order_code' => $order->code,
              'state' => 36, //-- Cancelled
              'update_user' => $this->user
            );

            $this->order_state_model->add_state($arr);
          }
        }

        if($order->state == 9)
        {
          $arr = array(
            'order_code' => $order->code,
            'state' => 9,
            'update_user' => $this->user
          );

          $this->order_state_model->add_state($arr);
        }
      }
    }

    if($sc === TRUE)
    {
      //--- logs result
      $arr = array(
        'status' => 'success',
        'message' => "Order {$code} Cancellation Successful.",
        'order_number' => $code
      );

      if($this->logs_json)
			{
				$logs = array(
					'trans_id' => genUid(),
					'api_path' => $this->api_path,
					'type' => 'ORDER',
					'code' => $code,
					'action' => $action,
					'status' => 'success',
					'message' => 'success',
					'request_json' => $json,
					'response_json' => json_encode($arr)
				);

				$this->ix_api_logs_model->add_logs($logs);
			}

			$this->response($arr, 200);
    }
    else
    {
      //--- logs result
      $arr = array(
        'status' => FALSE,
        'message' => $this->error,
        'order_number' => $code,
        'retry' => TRUE
      );

      if($this->logs_json)
			{
				$logs = array(
					'trans_id' => genUid(),
					'api_path' => $this->api_path,
					'type' => 'ORDER',
					'code' => $code,
					'action' => $action,
					'status' => 'failed',
					'message' => $this->error,
					'request_json' => $json,
					'response_json' => json_encode($arr)
				);

				$this->ix_api_logs_model->add_logs($logs);
			}

			$this->response($arr, 200);
    }
  }
  //--- end cancel


  public function update_price_put()
  {
    $sc = TRUE;

    $action = 'update';

    $this->api_path = $this->api_path."/update_price";
    //--- Get raw post data
    $json = file_get_contents("php://input");

    if( ! $this->api)
    {
      $arr = array(
        'status' => FALSE,
        'error' => 'API Not Enabled',
        'retry' => FALSE
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => NULL,
          'action' => $action,
          'status' => 'failed',
          'message' => 'API Not Enabled',
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->ix_api_logs_model->add_logs($logs);
      }

      $this->response($arr, 400);
    }

    $data = json_decode($json);

    if(empty($data))
    {
      $arr = array(
        'status' => FALSE,
        'error' => 'empty data',
        'retry' => FALSE
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => NULL,
          'action' => $action,
          'status' => 'failed',
          'message' => 'empty data',
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->ix_api_logs_model->add_logs($logs);
      }

      $this->response($arr, 400);
    }

    if(! property_exists($data, 'order_number') OR $data->order_number == '')
    {
      $this->error = 'order_number is required';

      $arr = array(
        'status' => FALSE,
        'error' => $this->error,
        'retry' => FALSE
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => NULL,
          'action' => $action,
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->ix_api_logs_model->add_logs($logs);
      }

      $this->response($arr, 400);
    }

    $order_code = $this->orders_model->get_active_order_code_by_reference($data->order_number);

    if(empty($order_code))
    {
      $this->error = "Active order number not found for {$data->order_number}";

      $arr = array(
        'status' => FALSE,
        'error' => $this->error,
        'retry' => FALSE
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => $data->order_number,
          'action' => $action,
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->ix_api_logs_model->add_logs($logs);
      }

      $this->response($arr, 400);
    }

    //--- check each item code
    $details = $data->details;

    if(empty($details))
    {
      $sc = FALSE;
      $this->error = "Items not found";

      $arr = array(
        'status' => FALSE,
        'error' => $this->error,
        'retry' => FALSE
      );

      if($this->logs_json)
      {
        $logs = array(
        'trans_id' => genUid(),
        'api_path' => $this->api_path,
        'type' => $this->type,
        'code' => $data->order_number,
        'action' => $action,
        'status' => 'failed',
        'message' => $this->error,
        'request_json' => $json,
        'response_json' => json_encode($arr)
        );

        $this->ix_api_logs_model->add_logs($logs);
      }

      $this->response($arr, 400);
    }

    $this->db->trans_begin();

    if( ! empty($details))
    {
      $row_change = 0;

      foreach($details as $rs)
      {
        if($sc === FALSE) { break; }
        //---- check valid items
        $item = $this->products_model->get($rs->item);
        $item = empty($item) ? $this->products_model->get_by_old_code($rs->item) : $item;

        $rs->item = empty($item) ? $rs->item : $item->code;

        $row = $this->orders_model->get_detail_by_product($order_code, $rs->item);

        if(empty($row))
        {
          $sc = FALSE;
          $this->error = "Order item {$rs->item} not found in {$order_code}";
        }

        if($sc === TRUE)
        {
          if($row->qty != $rs->qty)
          {
            $sc = FALSE;
            $this->error = "Quantity mismatch - try to update price with Qty : {$rs->qty} but order qty is {$row->qty} on {$rs->item}";
          }
        }

        if($sc === TRUE && $rs->price != $row->price)
        {
          $disc = $rs->discount > 0 ? $rs->discount/$rs->qty : 0;

          $arr = array(
            'price' => $rs->price,
            'discount1' => round($disc, 2),
            'discount_amount' => $rs->discount,
            'total_amount' => round($rs->amount)
          );

          if( ! $this->orders_model->update_detail($row->id, $arr))
          {
            $sc = FALSE;
            $this->error = "Failed to update order row";
          }

          $row_change++;
        }
      }

      if($sc === TRUE && $row_change > 0)
      {
        $doc_total = $this->orders_model->get_order_total_amount($order_code);
        $arr = array(
          'doc_total' => $doc_total,
          'is_hold' => 0,
          'update_user' =>  $this->user
        );

        $this->orders_model->update($order_code, $arr);
      }
    }

    if($sc === TRUE)
    {
      $this->db->trans_commit();

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
          'type' => $this->type,
          'code' => $data->order_number,
          'action' => $action,
          'status' => 'success',
          'message' => 'success',
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->ix_api_logs_model->add_logs($logs);
      }

      $this->response($arr, 200);
    }
    else
    {
      $this->db->trans_rollback();

      $arr = array(
        'status' => FALSE,
        'error' => $this->error,
        'retry' => TRUE
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => $data->order_number,
          'action' => $action,
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->ix_api_logs_model->add_logs($logs);
      }

      $this->response($arr, 200);
    }
  } //--- end update price


  public function complete_order_get($order_number)
  {
    $sc = TRUE;

    $action = 'complete';

    $this->api_path = $this->api_path."/complete_order/{$order_number}";
    //--- Get raw post data
    $json = NULL;

    if( ! $this->api)
    {
      $arr = array(
        'status' => FALSE,
        'error' => 'API Not Enabled',
        'retry' => FALSE
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => NULL,
          'action' => $action,
          'status' => 'failed',
          'message' => 'API Not Enabled',
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->ix_api_logs_model->add_logs($logs);
      }

      $this->response($arr, 400);
    }

    $data = json_decode($json);

    if(empty($order_number))
    {
      $this->error = 'Missing required parameter : order_number';
      $arr = array(
        'status' => FALSE,
        'error' => $this->error,
        'retry' => FALSE
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => NULL,
          'action' => $action,
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->ix_api_logs_model->add_logs($logs);
      }

      $this->response($arr, 400);
    }

    $order_code = $this->orders_model->get_active_order_code_by_reference($order_number);

    if(empty($order_code))
    {
      $this->error = "Active order number not found for {$order_number}";

      $arr = array(
        'status' => FALSE,
        'error' => $this->error,
        'retry' => FALSE
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => $order_number,
          'action' => $action,
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->ix_api_logs_model->add_logs($logs);
      }

      $this->response($arr, 200);
    }

    $order = $this->orders_model->get($order_code);

    if(empty($order))
    {
      $sc = FALSE;
      $this->error = "Oredr not found";

      $arr = array(
        'status' => FALSE,
        'error' => $this->error,
        'retry' => FALSE
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => $order_number,
          'action' => $action,
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->ix_api_logs_model->add_logs($logs);
      }

      $this->response($arr, 400);
    }

    if($order->state != 7)
    {
      $sc = FALSE;

      $err = $order->state < 7 ? 'Order has been roll back' : ($order->state == 9 ? 'Order already Cancelled' : ($order->state == '8' ? 'Order already shipped' : 'Unknow order state'));
      $this->error = "Invalid order state : ".$err;

      $arr = array(
        'status' => FALSE,
        'error' => $this->error,
        'retry' => FALSE
      );

      if($this->logs_json)
      {
        $logs = array(
        'trans_id' => genUid(),
        'api_path' => $this->api_path,
        'type' => $this->type,
        'code' => $data->order_number,
        'action' => $action,
        'status' => 'failed',
        'message' => $this->error,
        'request_json' => $json,
        'response_json' => json_encode($arr)
        );

        $this->ix_api_logs_model->add_logs($logs);
      }

      $this->response($arr, 400);
    }

    if($sc === TRUE && ! empty($order))
    {
      //--- remove on hold
      $arr = array(
        'is_hold' => 0,
        'update_user' => $this->user
      );

      if( ! $this->orders_model->update($order_code, $arr))
      {
        $sc = FALSE;
        $this->error = "Failed to update order state";
      }

      //--- if update success try to Delivery order
      if($sc === TRUE)
      {
        //---- delivery order here
        // $this->load->library('confirm_order');
        // $this->confirm_order->confirm($order_code);
      }
    }

    if($sc === TRUE)
    {
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
          'type' => $this->type,
          'code' => $order_number,
          'action' => $action,
          'status' => 'success',
          'message' => 'success',
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->ix_api_logs_model->add_logs($logs);
      }

      $this->response($arr, 200);
    }
    else
    {
      $arr = array(
        'status' => FALSE,
        'error' => $this->error,
        'retry' => TRUE
      );

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => $order_number,
          'action' => $action,
          'status' => 'failed',
          'message' => $this->error,
          'request_json' => $json,
          'response_json' => json_encode($arr)
        );

        $this->ix_api_logs_model->add_logs($logs);
      }

      $this->response($arr, 200);
    }
  } //--- end confirm_order


  public function get_new_code($date, $role)
  {
    $date = empty($date) ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = $role == 'C' ? getConfig('PREFIX_CONSIGN_SO') : ($role == 'T' ? getConfig('PREFIX_TRANSFORM') : getConfig('PREFIX_ORDER'));
    $run_digit = $role == 'C' ? getConfig('RUN_DIGIT_CONSIGN_SO') : ($role == 'T' ? getConfig('RUN_DIGIT_TRANSFORM') : getConfig('RUN_DIGIT_ORDER'));
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->orders_model->get_max_code($pre);

    if( ! is_null($code))
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


  public function verify_data($data, $role = 'S')
	{
    if($this->orders_model->is_active_order_reference(trim($data->order_number)) !== FALSE)
    {
      $this->error = "Order number '{$data->order_number}' already exists";
			return FALSE;
    }

    if( ! property_exists($data, 'customer_code') OR trim($data->customer_code) == '' OR $data->customer_code == NULL)
    {
      $this->error = 'Missing required parameter : customer_code';
			return FALSE;
    }

    if($role == 'S')
    {
      if( ! property_exists($data, 'channel') OR trim($data->channel) == '' OR $data->channel == NULL)
      {
        $this->error = "Missing required parameter : channel";
        return FALSE;
      }

      if( ! property_exists($data, 'payment_method') OR trim($data->payment_method) == '' OR $data->payment_method == NULL)
      {
        $this->error = "Missing required parameter : payment_method";
        return FALSE;
      }
    }

    if($role == 'C')
    {
      if( ! property_exists($data, 'zone_code') OR trim($data->zone_code) == '' OR $data->zone_code == NULL)
      {
        $this->error = "Missing required parameter : zone_code";
        return FALSE;
      }

      if( ! property_exists($data, 'GP') OR trim($data->GP) == '' OR $data->GP == NULL)
      {
        $this->error = "Missing required parameter : GP";
        return FALSE;
      }
    }

    if($role == 'T')
    {
      if( ! property_exists($data, 'uname'))
      {
        $this->error = "Missing required parameter : uname";
        return FALSE;
      }
    }

		return TRUE;
	}


  public function get_available_stock($item_code, $warehouse_code)
  {
    //---- สต็อกคงเหลือในคลัง
    $sell_stock = $this->stock_model->get_sell_stock($item_code, $warehouse_code);

    //---- ยอดจองสินค้า ไม่รวมรายการที่กำหนด
    $ordered = $this->orders_model->get_reserv_stock($item_code, $warehouse_code);

    $reserv_stock = $this->reserv_stock_model->get_reserv_stock($item_code, $warehouse_code);

    $available = $sell_stock - $ordered - $reserv_stock;

    return $available < 0 ? 0 : $available;
  }
} //--- end class

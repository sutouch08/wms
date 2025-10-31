<?php
public function create_orders_post()
{
  $sc = TRUE;
  $action = 'create';
  $this->api_path = $this->api_path."/create_orders";
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

  $datas = json_decode($json);

  if(empty($datas))
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

  if( ! property_exists($datas, 'orders') OR empty($datas->orders))
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


  $countOrders = count($data->orders);
  $successList = [];
  $failedList = [];
  $orders = [];

  $index = 0;

  foreach($datas->orders as $data)
  {
    $st = TRUE;
    $this->error = "";
    $role = 'S';

    if( ! property_exists($data, 'order_number') OR $data->order_number == '')
    {
      $st = FALSE;
      $this->error = "order_number is required";
    }

    if($st === TRUE)
    {
      if(isset($data->order_type))
      {
        $ot = $data->order_type;

        if($ot != 'WO' && $ot != 'WQ')
        {
          $st = FALSE;
          $this->error = "Invalid order_type : allowed WO, WQ";
        }

        if($st === TRUE)
        {
          $role = $ot == 'WQ' ? 'T' : ($ot == 'WC' ? 'C' : 'S');
          $data->bookcode = $ot == 'WQ' ? getConfig('BOOK_CODE_TRANSFORM') : ($ot == 'WC' ? getConfig('BOOK_CODE_CONSIGN_SO') : getConfig('BOOK_CODE_ORDER'));
        }
      }
    }

    if($st === TRUE)
    {
      if( ! $this->verify_data($data, $role))
      {
        $st = FALSE;
      }
    }

    //-- check bill_to
    if($st === TRUE)
    {
      $tax_status = empty($data->tax_status) ? 0 : ($data->tax_status == 'Y' ? 1 : 0);

      if($tax_status)
      {
        $is_etax = empty($data->ETAX) ? 0 : ($data->ETAX == 'Y' && $tax_status == 1 ? 1 : 0);
        $bill_to = empty($data->bill_to) ? NULL : (array) $data->bill_to;

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
            $st = FALSE;
            $this->error = "You must fill in all required fields [tax_id, name, address, sub_district, district, province]";
          }

          $email = empty($bill_to->email) ? NULL : $bill_to->email;

          if($is_etax == 1 && empty($email))
          {
            $st = FALSE;
            $this->error = "Email is required for E-TAX";
          }
        }
        else
        {
          $st = FALSE;
          $this->error = "bill_to is required for tax_status = Y";
        }
      }
    }

    if($st === TRUE)
    {
      if(empty($data->details))
      {
        $st = FALSE;
        $this->error = "Items not found";
      }
    }

    if($st === TRUE)
    {
      //---- for check duplicate order number
      $order = $this->orders_model->get_active_order_by_reference(trim($data->order_number));

      if( ! empty($order))
      {
        if($order->role != $role)
        {
          $st = FALSE;
          $this->error = "Failed to update order : order_type mismatch to current active order";
        }
      }
    }

    if($st === TRUE && ! empty($data->details))
    {
      $docTotal = 0;

      foreach($data->details as $rs)
      {
        if($st === FALSE) { break; }
        //---- check valid items
        $item = $this->products_model->get($rs->item);
        $item = empty($item) ? $this->products_model->get_by_old_code($rs->item) : $item;

        if(empty($item))
        {
          $st = FALSE;
          $this->error = "Invalid SKU : {$rs->item}";
        }
        else
        {
          $rs->item = $item;
          $docTotal += $rs->amount;
        }
      }

      $data->docTotal = $docTotal;
    }





    if($st == FALSE)
    {
      $failedList[] = array(
        'index' => $index,
        'order_number' => $data->order_number,
        'message' => $this->error,
        'json' => $data
      );
    }
  } //--- end foreach


  $role = 'S';
  $bookcode = getConfig('BOOK_CODE_ORDER');
  $warehouse_code = (isset($data->warehouse) && ! empty($data->warehouse)) ? $data->warehouse : getConfig('IX_WAREHOUSE');
  $customer = NULL;
  $zone_code = NULL;
  $channels_code = empty($data->channel) ? NULL : $data->channel;
  $shop_id = empty($data->shop_id) ? NULL : $data->shop_id;
  $is_mkp = FALSE;
  $is_reserv = isset($data->is_reserv) ? ($data->is_reserv == 'Y' ? TRUE : FALSE) : FALSE;
  $payment_code = NULL;
  $payment_role = NULL;
  $cod_amount = empty($data->cod_amount) ? 0 : floatval($data->cod_amount);
  $is_term = 0;
  $GP = '0.00';
  $user_ref = NULL;
  $this->user = empty($data->owner) ? $this->user : $data->owner;
  $tax_status = empty($data->tax_status) ? 0 : ($data->tax_status == 'Y' ? 1 : 0);
  $is_etax = empty($data->ETAX) ? 0 : ($data->ETAX == 'Y' && $tax_status == 1 ? 1 : 0);
  $bill_to = empty($data->bill_to) ? NULL : (array) $data->bill_to;
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
        'channels' => $channels_code,
        'shop_id' => $shop_id,
        'status' => 'failed',
        'message' => $this->error,
        'request_json' => $json,
        'response_json' => json_encode($arr)
      );

      $this->ix_api_logs_model->add_logs($logs);
    }

    $this->response($arr, 400);
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
        'channels' => $channels_code,
        'shop_id' => $shop_id,
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

      if($channels_code == '0009' OR $channels_code == 'SHOPEE' OR $channels_code == 'LAZADA')
      {
        $is_mkp = TRUE;
      }
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
      $is_term = $payment_role == 4 ? 0 : $pm->has_term;
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
      if($is_term)
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
    if(is_true(getConfig('CONTROL_CREDIT')))
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

  //--- check pay slip
  if($sc === TRUE && ($role == 'S' && ($payment_role == 1 OR $payment_role == 2)))
  {
    if(empty($data->payslip))
    {
      $sc = FALSE;
      $this->error = "payslip is required for transfer payment";
    }
    else if(empty($data->payment_date_time))
    {
      $sc = FALSE;
      $this->error = "payment_date_time is required for transfer payment";
    }
    else if(empty($data->account_no))
    {
      $sc = FALSE;
      $this->error = "account_no is required for transfer payment";
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
        'channels' => $channels_code,
        'shop_id' => $shop_id,
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

    if($role == 'S')
    {
      if($payment_role == 1 OR $payment_role == 2)
      {
        $state = 2;
      }
    }

    //$state = $role == 'S' ? (empty($data->payslip) ? 3 : 2) : $state;

    $is_wms = 0;

    //---- id_sender
    $sender = $this->sender_model->get_id($data->shipping);

    $id_sender = empty($sender) ? NULL : $sender;
    $id_address = NULL;

    //--- order code gen จากระบบ
    $order_code = empty($order) ? $this->get_new_code($date_add, $role) : $order->code;

    $tracking = get_null($data->tracking_no);

    $total_amount = 0;
    $total_sku = [];
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
        'remark' => empty($data->remark) ? NULL : get_null($data->remark),
        'cod_amount' => $cod_amount,
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
        'email'=> get_null($bill_to->email),
        'shop_id' => $shop_id
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
        'remark' => empty($data->remark) ? NULL : get_null($data->remark),
        'cod_amount' => $cod_amount,
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
        'email'=> get_null($bill_to->email),
        'shop_id' => $shop_id
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


      if( ! empty($customer_ref) && ! empty($data->ship_to) && ! empty($data->ship_to->address))
      {
        $sh = $data->ship_to;
        $id_address = $this->address_model->get_id($data->customer_ref, $sh->address, $sh->sub_district, $sh->district, $sh->province, $sh->name, $sh->phone);

        if($id_address === FALSE)
        {
          $arr = array(
            'code' => $data->customer_ref,
            'name' => $sh->name,
            'address' => $sh->address,
            'sub_district' => $sh->sub_district,
            'district' => $sh->district,
            'province' => $sh->province,
            'postcode' => $sh->postcode,
            'phone' => $sh->phone,
            'email' => $sh->email,
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
                "discount1"	=> $is_mkp ? round($disc, 2) : round(discountAmountToPercent($rs->discount, $rs->qty, $rs->price), 2).' %',
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

                if( ! isset($total_sku[$item->code]))
                {
                  $total_sku[$item->code] = 1;
                }

                if($item->count_stock && $is_reserv)
                {
                  $this->reserv_stock_model->deduct_reserv_qty($item->code, $rs->qty, $warehouse_code, $is_mkp);
                }

                if($item->count_stock && ! $is_pre_order && ($this->sync_api_stock OR $this->checkBackorder))
                {
                  $available = $this->get_available_stock($item->code, $warehouse_code, $is_mkp);
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

        //-- แนบสลิป
        if($state  == 2)
        {
          //--- if has pay slip
          if( ! empty($data->payslip))
          {
            $img = explode(',', $data->payslip);

            if(count($img) == 1)
            {
              $imageData = base64_decode($img[0]);
            }
            else
            {
              $imageData = base64_decode($img[1]);
            }

            $path = $this->config->item('image_file_path')."payments/";

            if( ! empty($imageData))
            {
              $source = imagecreatefromstring($imageData);

              if($source !== FALSE)
              {
                $name = "{$path}{$order_code}.jpg";
                $save = imagejpeg($source, $name, 100);
                imagedestroy($source);
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "Invalid payslip data";
            }
          }

          //---- create payment
          $this->load->model('masters/bank_model');
          $this->load->model('orders/order_payment_model');

          $pay_date = now();

          if( ! empty($data->payment_date_time))
          {
            $pay_date = date('Y-m-d H:i:s', strtotime($data->payment_date_time));
          }

          if( ! empty($data->account_no))
          {
            $id_account = $this->bank_model->get_id($data->account_no);

            if( ! empty($id_account))
            {
              $arr = array(
                'order_code' => $order_code,
                'order_amount' => $total_amount,
                'pay_amount' => $total_amount,
                'pay_date' => $pay_date,
                'id_account' => $id_account,
                'acc_no' => $data->account_no,
                'user' =>$this->user
              );

              if(!$this->order_payment_model->add($arr))
              {
                $sc = FALSE;
                $this->error = "Insert Payment data failed";
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "Invalid account no";
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "Account no is required for transfer payment";
          }
        }

        if($sc === TRUE)
        {
          $arr = array(
            'doc_total' => $total_amount,
            'total_sku' => count($total_sku),
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
          'channels' => $channels_code,
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
          'channels' => $channels_code,
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
} //--- create_orders

 ?>

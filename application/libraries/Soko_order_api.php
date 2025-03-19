<?php
class Soko_order_api
{
  private $url;
	public $wms;
	protected $ci;
  public $error;
  public $backorder = 0;
	public $log_json;
  public $test = FALSE;
	public $type = 'OB';

  public function __construct()
  {
    $this->ci =& get_instance();
		$this->ci->load->model('rest/V1/soko_api_logs_model');
    $this->ci->load->model('masters/products_model');
		$this->url = getConfig('SOKOJUNG_API_HOST');
    $this->key = getConfig('SOKOJUNG_API_CREDENTIAL');
		$this->log_json = is_true(getConfig('SOKOJUNG_LOG_JSON'));
    $this->test = is_true(getConfig('SOKOJUNG_TEST'));
  }

  //---- export
  public function export_order($code)
  {
    $this->ci->load->model('orders/orders_model');
    $this->ci->load->model('address/address_model');
    $this->ci->load->model('masters/sender_model');
    $this->ci->load->model('masters/channels_model');
    $this->ci->load->model('masters/payment_methods_model');


    $sc = TRUE;

    $role_type_list = array(
      'S' => 'WO', //--- check channels type_code
      'P' => 'WS',
      'U' => 'WU',
      'C' => 'WC',
      'N' => 'WT',
      'Q' => 'WV',
      'T' => 'WQ',
      'L' => 'WL'
    );

    $order = $this->ci->orders_model->get($code);

    if(!empty($order))
    {
      if(empty($order->id_address))
      {
        $sc = FALSE;
        $this->error = "ไม่พบที่อยู่จัดส่ง";
      }
      else
      {
        $addr = $this->ci->address_model->get_shipping_detail($order->id_address);

        if(empty($addr))
        {
          $sc = FALSE;
          $this->error = "ไม่พบที่อยู่จัดส่ง";
        }

        $sender = $this->ci->sender_model->get($order->id_sender);

        if(empty($sender))
        {
          $sc = FALSE;
          $this->error = "ไม่ได้ระบุขนส่ง";
        }
      }

      if($sc === TRUE)
      {
        $this->type = $role_type_list[$order->role];
        $details = $this->ci->orders_model->get_only_count_stock_details($code);
        $channels = $order->role === 'S' ? $this->ci->channels_model->get($order->channels_code) : NULL;
        $channels_code = !empty($channels) ? $order->channels_code : $role_type_list[$order->role];
        $channels_name = !empty($channels) ? $channels->name : "";
        $isOnline = ! empty($channels) ? $channels->is_online : 0;
        $doc_total = $order->doc_total <= 0 ? $this->ci->orders_model->get_order_total_amount($order->code) : $order->doc_total;
        $cod = $order->role === 'S' ? ($order->payment_role == 4 ? 'COD' : 'NON-COD') : 'NON-COD';
        $cod_amount = $cod === 'COD' ? ($order->cod_amount == 0 ? $doc_total : $order->cod_amount) : 0.00;

        $spx = $sender->code == "SPX" ? TRUE : FALSE;
        $addr->province = $spx ? parseProvince($addr->province) : $addr->province;
        $addr->sub_district = $spx ? parseSubDistrict($addr->sub_district, $addr->province) : $addr->sub_district;
        $addr->district = $spx ? parseDistrict($addr->district, $addr->province) : $addr->district;
        $addr->phone = $spx ? parsePhoneNumber($addr->phone, 10) : $addr->phone;

        $printBill = $order->role == 'S' ? ($isOnline ? 0 : 1) :(($order->role == 'P' OR $order->role == 'C') ? 1 : 0);

        if( ! empty($details))
        {
          $ds = array(
          'external_id' => $order->code,
          'order_number' => $order->code,
          'comment' => $order->remark,
          'stores' => 1,
          'special_order' => "",
          'channel' => empty($channels) ?  "UE" : $order->channels_code,
          'shipping' => (!empty($sender) ? $sender->code : ""),
          'tracking_no' => $order->shipping_code,
          'print_bill' => $printBill,
          'order_type' => $this->type,
          'order_mode' => $isOnline ? 1 : 0,
          'customer' => [
          'code' => empty($addr->code) ? $order->customer_code : $addr->code,
          'name' => $addr->name,
          'address' => $addr->address,
          'sub_district' => $addr->sub_district,
          'district' => $addr->district,
          'province' => $addr->province,
          'postal_code' => $addr->postcode,
          'mobile_no' => $addr->phone,
          'phone_no' => "",
          'email' => $addr->email
          ],
          'payment' => [
          'shipping_fee_original' => 0,
          'shipping_fee_discount_platform' => 0,
          'shipping_fee_discount_seller' => 0,
          'shipping_fee' => 0,
          'voucher_seller' => 0,
          'voucher_amount' => 0,
          'price' => round($cod_amount, 2),
          'cod_amount' => round($cod_amount, 2)
          ],
          'order_items' => []
          );

          foreach($details as $rs)
          {
            if($rs->is_count)
            {
              $item = [
              'item_sku' => $rs->product_code,
              'item_code' => "",
              'marketplace_sku' => "",
              'item_qty' => round($rs->qty, 2),
              'item_name' => $rs->product_name,
              'selling_price' => round($rs->price, 2),
              'paid_price' => round($rs->price, 2),
              'voucher_platform' => 0,
              'voucher_seller' => 0
              ];

              array_push($ds['order_items'], $item);
            }
          }

          $isUpdate = $order->wms_export == 1 ? TRUE : FALSE;
          $action = $isUpdate ? 'update' : 'create';

          $api_path = $isUpdate ? $this->url."orders/@{$order->code}" : $this->url."orders";
          $url = $api_path;
          $method = $isUpdate ? "PUT" : "POST";

          $headers = array(
          "Content-Type: application/json",
          "Authorization: Basic {$this->key}"
          );

          $json = json_encode($ds);

          if( ! $this->test)
          {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($curl);

            curl_close($curl);

            $res = json_decode($response);

            if( ! empty($res))
            {
              if(empty($res->error))
              {
                if($res->status != 'success')
                {
                  $dup_msg = "external_id is duplicated in the order";

                  if($res->message == $dup_msg)
                  {
                    $arr = array(
                    'wms_export' => 1,
                    'wms_export_error' => $res->message
                    );
                  }
                  else
                  {
                    $sc = FALSE;
                    $this->error = $res->message;

                    $arr = array(
                    'wms_export' => $isUpdate ? 1 : 3,
                    'wms_export_error' => $res->message
                    );
                  }

                  $this->ci->orders_model->update($code, $arr);
                }
                else
                {
                  if( ! empty($res->back_order))
                  {
                    $is_backorder = $res->back_order == 'Y' ? 1 : 0;
                    $this->backorder = $is_backorder;

                    if($res->back_order == 'Y')
                    {
                      if( ! empty($res->details))
                      {
                        $this->ci->orders_model->drop_backlog_list($code);

                        foreach($res->details as $rs)
                        {
                          $backlogs = array(
                          'order_code' => $code,
                          'product_code' => $rs->item_sku,
                          'order_qty' => $rs->order_qty,
                          'available_qty' => $rs->available
                          );

                          $this->ci->orders_model->add_backlogs_detail($backlogs);
                        }
                      }
                    }

                    $arr = array(
                    'wms_export' => 1,
                    'wms_export_error' => NULL,
                    'is_backorder' => $is_backorder
                    );

                    $this->ci->orders_model->update($code, $arr);

                  }
                  else
                  {
                    $arr = array(
                    'wms_export' => 1,
                    'wms_export_error' => NULL
                    );

                    $this->ci->orders_model->update($code, $arr);
                  }
                }
              }
              else
              {
                $sc = FALSE;
                $res->status = "failed";
                $res->message = $res->error;
                $this->error = $response;
              }

              if($this->log_json)
              {
                $logs = array(
                'trans_id' => genUid(),
                'type' => $this->type,
                'api_path' => $api_path,
                'code' => $order->code,
                'action' => $action,
                'status' => $res->status == 'success' ? 'success' : 'failed',
                'message' => $res->message,
                'request_json' => $json,
                'response_json' => $response
                );

                $this->ci->soko_api_logs_model->add_api_logs($logs);
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "No response";

              if($this->log_json)
              {
                $logs = array(
                'trans_id' => genUid(),
                'type' => $this->type,
                'api_path' => $api_path,
                'code' => $order->code,
                'action' => $action,
                'status' => 'failed',
                'message' => 'No response',
                'request_json' => $json,
                'response_json' => NULL
                );

                $this->ci->soko_api_logs_model->add_api_logs($logs);
              }
            }
          }
          else
          {
            $logs = array(
            'trans_id' => genUid(),
            'type' => $this->type,
            'api_path' => $api_path,
            'code' => $order->code,
            'action' => $action,
            'status' => 'test',
            'message' => 'Test api',
            'request_json' => $json,
            'response_json' => NULL
            );

            $this->ci->soko_api_logs_model->add_api_logs($logs);
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "ไม่พบรายการสินค้าในออเดอร์";
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "เลขที่ออเดอร์ไม่ถูกต้อง";
    }

    if($sc === TRUE)
    {
      $this->ci->soko_api_logs_model->add($code, 'S', NULL, $this->type);
    }
    else
    {
      $this->ci->soko_api_logs_model->add($code, 'E', $this->error, $this->type);
    }

    return $sc;
  }


  //---- export
  public function create_transfer_order($order, $details)
  {
    $this->ci->load->model('orders/orders_model');
    $this->ci->load->model('inventory/transfer_model');

    $code = $order->code;

    $sc = TRUE;

    $this->type = "WW";

    if(!empty($order))
    {
      if( ! empty($details))
      {
        $ds = array(
          'external_id' => $order->code,
          'order_number' => $order->code,
          'comment' => $order->remark,
          'stores' => 1,
          'special_order' => "",
          'channel' => "UE",
          'shipping' => "WARRIX",
          'tracking_no' => "",
          'print_bill' => 0,
          'order_type' => $this->type,
          'order_mode' => 0,
          'customer' => array(
            'code' => $order->to_warehouse,
            'name' => $order->to_warehouse_name,
            'address' => "xx",
            'sub_district' => "xx",
            'district' => "xx",
            'province' => "xx",
            'postal_code' => "10110",
            'mobile_no' => "xx",
            'phone_no' => "xx",
            'email' => "xx"
          ),
          'payment' => array(
            'shipping_fee_original' => 0,
            'shipping_fee_discount_platform' => 0,
            'shipping_fee_discount_seller' => 0,
            'shipping_fee' => 0,
            'voucher_seller' => 0,
            'voucher_amount' => 0,
            'price' => 0,
            'cod_amount' => 0
          ),
          'order_items' => array()
        );

        foreach($details as $rs)
        {
          $item = array(
            'item_sku' => $rs->product_code,
            'item_code' => "",
            'marketplace_sku' => "",
            'item_qty' => round($rs->qty, 2),
            'item_name' => $rs->product_name,
            'selling_price' => 0,
            'paid_price' => 0,
            'voucher_platform' => 0,
            'voucher_seller' => 0
          );

          array_push($ds['order_items'], $item);
        }

        $isUpdate = $order->wms_export == 1 ? TRUE : FALSE;
        $action = $isUpdate ? 'update' : 'create';
        $api_path = $isUpdate ? $this->url."orders/@{$order->code}" : $this->url."orders";
        $url = $api_path;
        $method = $isUpdate ? "PUT" : "POST";

        $headers = array(
        "Content-Type: application/json",
        "Authorization: Basic {$this->key}"
        );

        $json = json_encode($ds);

        if( ! $this->test)
        {
          $curl = curl_init();
          curl_setopt($curl, CURLOPT_URL, $url);
          curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
          curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
          curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
          curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

          $response = curl_exec($curl);

          curl_close($curl);

          $res = json_decode($response);

          if( ! empty($res))
          {
            if(empty($res->error))
            {
              if($res->status != 'success')
              {
                $sc = FALSE;

                $this->error = $res->message;

                if( ! empty($res->code) && ($res->code == "SOKO-13" OR $res->code == "SOKO-14"))
                {
                  if( ! $isUpdate)
                  {
                    $arr = array(
                      'wms_export' => 1,
                      'wms_export_error' => $res->message
                    );

                    $this->ci->transfer_model->update($code, $arr);
                  }
                }
                else
                {
                  $arr = array(
                    'wms_export' => 3,
                    'wms_export_error' => $res->message
                  );

                  $this->ci->transfer_model->update($code, $arr);
                }
              }
              else
              {
                $arr = array(
                  'wms_export' => 1,
                  'wms_export_error' => NULL
                );

                $this->ci->transfer_model->update($code, $arr);
              }
            }
            else
            {
              $sc = FALSE;
              $res->status = "failed";
              $res->message = $res->error;
              $this->error = $response;
            }

            if($this->log_json)
            {
              $logs = array(
              'trans_id' => genUid(),
              'type' => $this->type,
              'api_path' => $api_path,
              'code' => $order->code,
              'action' => $action,
              'status' => $res->status == 'success' ? 'success' : 'failed',
              'message' => $res->message,
              'request_json' => $json,
              'response_json' => $response
              );

              $this->ci->soko_api_logs_model->add_api_logs($logs);
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "No response";

            if($this->log_json)
            {
              $logs = array(
              'trans_id' => genUid(),
              'type' => $this->type,
              'api_path' => $api_path,
              'code' => $order->code,
              'action' => $action,
              'status' => 'failed',
              'message' => 'No response',
              'request_json' => $json,
              'response_json' => NULL
              );

              $this->ci->soko_api_logs_model->add_api_logs($logs);
            }
          }
        }
        else
        {
          $logs = array(
          'trans_id' => genUid(),
          'type' => $this->type,
          'api_path' => $api_path,
          'code' => $order->code,
          'action' => $action,
          'status' => 'test',
          'message' => 'Test api',
          'request_json' => $json,
          'response_json' => NULL
          );

          $this->ci->soko_api_logs_model->add_api_logs($logs);
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "ไม่พบรายการสินค้าในออเดอร์";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "เลขที่ออเดอร์ไม่ถูกต้อง";
    }

    if($sc === TRUE)
    {
      $this->ci->soko_api_logs_model->add($order->code, 'S', NULL, $this->type);
    }
    else
    {
      $this->ci->soko_api_logs_model->add($code, 'E', $this->error, $this->type);
    }

    return $sc;
  } //--- end function


	public function get_type_code($channels_code)
	{
		$this->ci->load->model('masters/channels_model');

		$channels = $this->ci->channels_model->get($channels_code);

		if(!empty($channels))
		{
			return $channels->type_code;
		}

		return NULL;
	}


  public function get_order_status($code)
  {
    $api_path = $this->url."orders/@".$code;
    $url = $api_path;
    $method = "GET";

    $headers = array(
      "Authorization: Basic {$this->key}"
    );

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($curl);

    curl_close($curl);

    $res = json_decode($response);

    return $res;
  }


  public function cancel($code, $role = 'S')
  {
    $role_type_list = array(
			'S' => 'WO', //--- check channels type_code
			'P' => 'WS',
			'U' => 'WU',
			'C' => 'WC',
			'N' => 'WT',
			'Q' => 'WV',
			'T' => 'WQ',
			'L' => 'WL'
		);

    $this->type = $role_type_list[$role];

    $sc = TRUE;

    if( ! empty($code))
		{
      $api_path = $this->url."orders/@{$code}/cancel";
      $url = $api_path;
			$method = "PUT";

			$headers = array(
				"Content-Type: application/json",
        "Authorization: Basic {$this->key}"
			);

      if( ! $this->test)
      {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($curl);

        curl_close($curl);

        $res = json_decode($response);

        if( ! empty($res))
        {
          if(empty($res->error))
          {
            if(empty($res->status))
            {
              if(! empty($res->id))
              {
                $res->status = 'success';
                $res->message = $response;
              }
              else
              {
                $sc = FALSE;
                $this->error = $response;
                $res->status = 'failed';
                $res->message = $response;
              }
            }
            else
            {
              if($res->status != 'success' && empty($res->id))
              {
                $sc = FALSE;
                $this->error = $res->message;
              }
            }
          }
          else
          {
            $sc = FALSE;
            $res->status = "failed";
            $res->message = $res->error;
            $this->error = $response;
          }

          if($this->log_json)
          {
            $logs = array(
              'trans_id' => genUid(),
              'type' => $this->type,
              'api_path' => $api_path,
              'code' => $code,
              'action' => 'cancel',
              'status' => $res->status == 'success' ? 'success' : 'failed',
              'message' => $res->message,
              'request_json' => NULL,
              'response_json' => $response
            );

            $this->ci->soko_api_logs_model->add_api_logs($logs);
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "No response";

          if($this->log_json)
          {
            $logs = array(
              'trans_id' => genUid(),
              'type' => $this->type,
              'api_path' => $api_path,
              'code' => $code,
              'action' => 'cancel',
              'status' => 'failed',
              'message' => 'No response',
              'request_json' => NULL,
              'response_json' => NULL
            );

            $this->ci->soko_api_logs_model->add_api_logs($logs);
          }
        }
      }
      else
      {
        $logs = array(
          'trans_id' => genUid(),
          'type' => $this->type,
          'api_path' => $api_path,
          'code' => $code,
          'action' => 'cancel',
          'status' => 'test',
          'message' => 'Test api',
          'request_json' => NULL,
          'response_json' => NULL
        );

        $this->ci->soko_api_logs_model->add_api_logs($logs);
      }
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter : order code";
		}

    if($sc === TRUE)
		{
			$this->ci->soko_api_logs_model->add($code, 'S', NULL, 'Cancel');
		}
		else
		{
			$this->ci->soko_api_logs_model->add($code, 'E', $this->error, 'Cancel');
		}

		return $sc;
  }


  public function cancel_transfer_order($code)
  {
    $this->type = "WW";

    $sc = TRUE;

    if( ! empty($code))
		{
      $api_path = $this->url."orders/@{$code}/cancel";
      $url = $api_path;
			$method = "PUT";

			$headers = array(
				"Content-Type: application/json",
        "Authorization: Basic {$this->key}"
			);

      if( ! $this->test)
      {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($curl);

        curl_close($curl);

        $res = json_decode($response);

        if( ! empty($res))
        {
          if(empty($res->error))
          {
            if(empty($res->status))
            {
              $sc = FALSE;
              $this->error = $response;
              $res->status = 'failed';
              $res->message = $response;
            }
            else
            {
              if($res->status != 'success' && empty($res->id))
              {
                $sc = FALSE;
                $this->error = $res->message;
              }
            }
          }
          else
          {
            $sc = FALSE;
            $res->status = "failed";
            $res->message = $res->error;
            $this->error = $response;
          }

          if($this->log_json)
          {
            $logs = array(
              'trans_id' => genUid(),
              'type' => $this->type,
              'api_path' => $api_path,
              'code' => $code,
              'action' => 'cancel',
              'status' => ($res->status == 'success' OR $res->status == 'Success') ? 'success' : 'failed',
              'message' => $res->message,
              'request_json' => NULL,
              'response_json' => $response
            );

            $this->ci->soko_api_logs_model->add_api_logs($logs);
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "No response";

          if($this->log_json)
          {
            $logs = array(
              'trans_id' => genUid(),
              'type' => $this->type,
              'api_path' => $api_path,
              'code' => $code,
              'action' => 'cancel',
              'status' => 'failed',
              'message' => 'No response',
              'request_json' => NULL,
              'response_json' => NULL
            );

            $this->ci->soko_api_logs_model->add_api_logs($logs);
          }
        }
      }
      else
      {
        $logs = array(
          'trans_id' => genUid(),
          'type' => $this->type,
          'api_path' => $api_path,
          'code' => $code,
          'action' => 'cancel',
          'status' => 'test',
          'message' => 'Test api',
          'request_json' => NULL,
          'response_json' => NULL
        );

        $this->ci->soko_api_logs_model->add_api_logs($logs);
      }
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter : order code";
		}

    if($sc === TRUE)
		{
			$this->ci->soko_api_logs_model->add($code, 'S', NULL, 'Cancel');
		}
		else
		{
			$this->ci->soko_api_logs_model->add($code, 'E', $this->error, 'Cancel');
		}

		return $sc;
  }


  public function test_export_order($code)
  {
    $this->ci->load->model('orders/orders_model');
    $this->ci->load->model('address/address_model');
    $this->ci->load->model('masters/sender_model');
    $this->ci->load->model('masters/channels_model');
    $this->ci->load->model('masters/payment_methods_model');


    $sc = TRUE;
    $res = "Not found";

    $role_type_list = array(
      'S' => 'WO', //--- check channels type_code
      'P' => 'WS',
      'U' => 'WU',
      'C' => 'WC',
      'N' => 'WT',
      'Q' => 'WV',
      'T' => 'WQ',
      'L' => 'WL'
    );

    $order = $this->ci->orders_model->get($code);

    if(!empty($order))
    {
      if(empty($order->id_address))
      {
        $sc = FALSE;
        $this->error = "ไม่พบที่อยู่จัดส่ง";
      }
      else
      {
        $addr = $this->ci->address_model->get_shipping_detail($order->id_address);

        if(empty($addr))
        {
          $sc = FALSE;
          $this->error = "ไม่พบที่อยู่จัดส่ง";
        }

        $sender = $this->ci->sender_model->get($order->id_sender);

        if(empty($sender))
        {
          $sc = FALSE;
          $this->error = "ไม่ได้ระบุขนส่ง";
        }
      }

      if($sc === TRUE)
      {
        $this->type = $role_type_list[$order->role];
        $details = $this->ci->orders_model->get_only_count_stock_details($code);
        $channels = $order->role === 'S' ? $this->ci->channels_model->get($order->channels_code) : NULL;
        $channels_code = !empty($channels) ? $order->channels_code : $role_type_list[$order->role];
        $channels_name = !empty($channels) ? $channels->name : "";
        $isOnline = ! empty($channels) ? $channels->is_online : 0;
        $doc_total = $order->doc_total <= 0 ? $this->ci->orders_model->get_order_total_amount($order->code) : $order->doc_total;
        $cod = $order->role === 'S' ? ($order->payment_role == 4 ? 'COD' : 'NON-COD') : 'NON-COD';
        $cod_amount = $cod === 'COD' ? ($order->cod_amount == 0 ? $doc_total : $order->cod_amount) : 0.00;

        $spx = $sender->code == "SPX" ? TRUE : FALSE;
        $addr->province = $spx ? parseProvince($addr->province) : $addr->province;
        $addr->sub_district = $spx ? parseSubDistrict($addr->sub_district, $addr->province) : $addr->sub_district;
        $addr->district = $spx ? parseDistrict($addr->district, $addr->province) : $addr->district;
        $addr->phone = $spx ? parsePhoneNumber($addr->phone, 10) : $addr->phone;

        $printBill = $order->role == 'S' ? ($isOnline ? 0 : 1) :(($order->role == 'P' OR $order->role == 'C') ? 1 : 0);

        if( ! empty($details))
        {
          $ds = array(
            'external_id' => $order->code,
            'order_number' => $order->code,
            'comment' => $order->remark,
            'stores' => 1,
            'special_order' => "",
            'channel' => empty($channels) ?  "UE" : $order->channels_code,
            'shipping' => (!empty($sender) ? $sender->code : ""),
            'tracking_no' => $order->shipping_code,
            'print_bill' => $printBill,
            'order_type' => $this->type,
            'order_mode' => $isOnline ? 1 : 0,
            'customer' => [
              'code' => empty($addr->code) ? $order->customer_code : $addr->code,
              'name' => $addr->name,
              'address' => $addr->address,
              'sub_district' => $addr->sub_district,
              'district' => $addr->district,
              'province' => $addr->province,
              'postal_code' => $addr->postcode,
              'mobile_no' => $addr->phone,
              'phone_no' => "",
              'email' => $addr->email
              ],
            'payment' => [
              'shipping_fee_original' => 0,
              'shipping_fee_discount_platform' => 0,
              'shipping_fee_discount_seller' => 0,
              'shipping_fee' => 0,
              'voucher_seller' => 0,
              'voucher_amount' => 0,
              'price' => round($cod_amount, 2),
              'cod_amount' => round($cod_amount, 2)
              ],
            'order_items' => []
          );

          foreach($details as $rs)
          {
            if($rs->is_count)
            {
              $item = [
                'item_sku' => $rs->product_code,
                'item_code' => "",
                'marketplace_sku' => "",
                'item_qty' => round($rs->qty, 2),
                'item_name' => $rs->product_name,
                'selling_price' => round($rs->price, 2),
                'paid_price' => round($rs->price, 2),
                'voucher_platform' => 0,
                'voucher_seller' => 0
              ];

              array_push($ds['order_items'], $item);
            }
          }

          $res = $ds;
        }
        else
        {
          $sc = FALSE;
          $this->error = "ไม่พบรายการสินค้าในออเดอร์";
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "เลขที่ออเดอร์ไม่ถูกต้อง";
    }

    return $sc === TRUE ? $res : $this->error;
  }
}
?>

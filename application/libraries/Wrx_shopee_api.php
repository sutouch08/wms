<?php

class Wrx_shopee_api
{  
  private $api;
  protected $ci;
  public $error;
  public $logs_json = FALSE;
  public $test = FALSE;
  public $type = NULL;
  public $api_path = NULL;

  public function __construct()
  {
    $this->ci =& get_instance();
		$this->ci->load->model('rest/V1/ix_api_logs_model');
    $this->ci->load->model('orders/orders_model');
    $this->ci->load->model('inventory/qc_model');
    
    $this->api = getWrxApiConfig();
    $this->logs_json = is_true($this->api['WRX_LOG_JSON']);
    $this->test = is_true($this->api['WRX_API_TEST']);
  }

  public function test()
  {
    print_r($this->api);
  }


  public function get_order_status($reference, $shop_id)
  {
    $action = "shipped";
    $this->type = "shipping";
    $url = $this->api['WRX_API_HOST'];
    $url .= "shopee/{$shop_id}/order/{$reference}";
    $this->api_path = $url;

    $headers = array("Authorization:Bearer {$this->api['WRX_API_CREDENTIAL']}");
    $apiUrl = str_replace(" ","%20",$url);
    $method = 'GET';

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $apiUrl);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $start_date = now();
    $response = curl_exec($curl);
    curl_close($curl);
    $res = json_decode($response);
    $end_date = now();

    $status = FALSE;
  
    if( ! empty($res) && ! empty($res->code))
    {
      if($res->code == 200 && $res->status == 'success')
      {
        if( ! empty($res->data))
        {
          /*
            return status text
            - UNPAID:Order is created, buyer has not paid yet.
            - READY_TO_SHIP:Seller can arrange shipment.
            - PROCESSED:Seller has arranged shipment online and got tracking number from 3PL.
            - RETRY_SHIP:3PL pickup parcel fail. Need to re arrange shipment.
            - SHIPPED:The parcel has been drop to 3PL or picked up by 3PL.
            - TO_CONFIRM_RECEIVE:The order has been received by buyer.
            - IN_CANCEL:The order's cancelation is under processing.
            - CANCELLED:The order has been canceled.
            - TO_RETURN:The buyer requested to return the order and order's return is processing.
            - COMPLETED:The order has been completed.
          */
          $status = $res->data[0]->order_status;
        }
      }

      if ($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => $reference,
          'action' => $action,
          'channels' => 'SHOPEE',
          'status' => $res->code == 200 ? 'success' : 'failed',
          'message' => isset($res->serviceMessage) ? $res->serviceMessage : 'No response',
          'request_json' => NULL,
          'response_json' => $response,
          'start_date' => $start_date,
          'end_date' => $end_date
        );

        $this->ci->ix_api_logs_model->add_logs($logs);
      }
    }
    else
    {
      if ($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => $reference,
          'action' => $action,
          'channels' => 'SHOPEE',
          'status' => 'failed',
          'message' => 'No response',
          'request_json' => NULL,
          'response_json' => NULL,
          'start_date' => $start_date,
          'end_date' => $end_date
        );

        $this->ci->ix_api_logs_model->add_logs($logs);
      }
    }

    return $status;
  }


  //--- for shopee
  public function get_shipping_param($reference, $shop_id)
  {
    $action = "shipped";
    $this->type = "shipping";
    $url = $this->api['WRX_API_HOST'];
    $url .= "shopee/{$shop_id}/shipping-parameter?orderSN={$reference}";
    $this->api_path = $url;

    $headers = array("Authorization:Bearer {$this->api['WRX_API_CREDENTIAL']}");
    $apiUrl = str_replace(" ","%20",$url);
    $method = 'GET';

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $apiUrl);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $start_date = now();
    $response = curl_exec($curl);
    curl_close($curl);
    $res = json_decode($response);
    $end_date = now();
    $ds = FALSE;

    if( ! empty($res) && ! empty($res->code) && $res->code == 200)
    {
      if( ! empty($res->data) && ! empty($res->data->pickup) && ! empty($res->data->pickup->address_list))
      {
        $address_id = 200081907;

        $ds = [
          'address_id' => 200081907,
          'pickup_time_id' => "",
          'tracking_number' => ""
        ];

        foreach($res->data->pickup->address_list as $ad)
        {
          if($ad->address_id == $address_id)
          {
            $ds['pickup_time_id'] = $ad->time_slot_list[0]->pickup_time_id;
          }
        }        
      }

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => $reference,
          'action' => $action,
          'channels' => 'SHOPEE',
          'status' => $res->code == 200 ? 'success' : 'failed',
          'message' => isset($res->serviceMessage) ? $res->serviceMessage : 'No response',
          'request_json' => NULL,
          'response_json' => $response,
          'start_date' => $start_date,
          'end_date' => $end_date
        );

        $this->ci->ix_api_logs_model->add_logs($logs);
      }
    }
    else 
    {
      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => $reference,
          'action' => $action,
          'channels' => 'SHOPEE',
          'status' => 'failed',
          'message' => isset($res->serviceMessage) ? $res->serviceMessage : 'No response',
          'request_json' => NULL,
          'response_json' => $response,
          'start_date' => $start_date,
          'end_date' => $end_date
        );

        $this->ci->ix_api_logs_model->add_logs($logs);
      }
    }

    return $ds;
  }


  public function ship_order($reference, $pickup_data, $shop_id)
  {
    $action = "ship_order";
    $this->type = "shipping";
    $url = $this->api['WRX_API_HOST'];
    $url .= "shopee/{$shop_id}/ship-order";
    $this->api_path = $url;

    $headers = array("Content-Type:application/json","Authorization:Bearer {$this->api['WRX_API_CREDENTIAL']}");
    $apiUrl = str_replace(" ","%20",$url);
    $method = 'POST';

    $req = array(
      "orderSN" => $reference,
      "packageNumber" => "",
      "pickup" => array(
        "addressID" => $pickup_data['address_id'],
        "pickupTimeID" => $pickup_data['pickup_time_id'],
        "trackingNumber" => ""
      )
    );

    $json = json_encode($req);

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $apiUrl);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $start_date = now();
    $response = curl_exec($curl);
    curl_close($curl);
    $res = json_decode($response);
    $end_date = now();

    $status = FALSE;

    if( ! empty($res) && ! empty($res->code))
    {
      if($res->code == 200)
      {
        $status = TRUE;
      }
      else
      {
        $this->error = $res->serviceMessage;
      }

      if ($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => $reference,
          'action' => $action,
          'channels' => 'SHOPEE',
          'status' => $status === TRUE ? 'success' : 'failed',
          'message' => isset($res->serviceMessage) ? $res->serviceMessage : 'No response',
          'request_json' => $json,
          'response_json' => $response,
          'start_date' => $start_date,
          'end_date' => $end_date
        );

        $this->ci->ix_api_logs_model->add_logs($logs);
      }
    }
    else 
    {
      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => $reference,
          'action' => $action,
          'channels' => 'SHOPEE',
          'status' => 'failed',
          'message' => isset($res->serviceMessage) ? $res->serviceMessage : 'No response',
          'request_json' => $json,
          'response_json' => $response,
          'start_date' => $start_date,
          'end_date' => $end_date
        );

        $this->ci->ix_api_logs_model->add_logs($logs);
      }
    }

    return $status;
  }


  public function get_tracking_number($reference, $shop_id)
  {
    $action = "shipped";
    $this->type = "shipping";
    $url = $this->api['WRX_API_HOST'];
    $url .= "shopee/{$shop_id}/tracking-number?orderSN={$reference}";
    $this->api_path = $url;

    $headers = array("Authorization:Bearer {$this->api['WRX_API_CREDENTIAL']}");
    $apiUrl = str_replace(" ","%20",$url);
    $method = 'GET';

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $apiUrl);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $start_date = now();
    $response = curl_exec($curl);
    curl_close($curl);
    $res = json_decode($response);
    $end_date = now();

    $status = FALSE;
    $tracking_number = "";

    if( ! empty($res) && ! empty($res->code) && $res->code == 200)
    {
      if( ! empty($res->data))
      {
        $tracking_number = $res->data->tracking_number;
        $status = TRUE;
      }

      if ($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => $reference,
          'action' => $action,
          'channels' => 'SHOPEE',
          'status' => $status === TRUE ? 'success' : 'failed',
          'message' => isset($res->serviceMessage) ? $res->serviceMessage : 'No response',
          'request_json' => NULL,
          'response_json' => $response,
          'start_date' => $start_date,
          'end_date' => $end_date
        );

        $this->ci->ix_api_logs_model->add_logs($logs);
      }
    }
    else 
    {
      if ($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => $reference,
          'action' => $action,
          'channels' => 'SHOPEE',
          'status' => 'failed',
          'message' => isset($res->serviceMessage) ? $res->serviceMessage : 'No response',
          'request_json' => NULL,
          'response_json' => $response,
          'start_date' => $start_date,
          'end_date' => $end_date
        );

        $this->ci->ix_api_logs_model->add_logs($logs);
      }
    }

    return $status === TRUE ? $tracking_number : FALSE;
  }


  public function create_shipping_document($reference, $tracking_number, $shop_id)
  {
    $action = "shipped";
    $this->type = "shipping";
    $url = $this->api['WRX_API_HOST'];
    $url .= "shopee/{$shop_id}/shipping-document-create";
    $this->api_path = $url;

    $headers = array("Content-Type:application/json","Authorization:Bearer {$this->api['WRX_API_CREDENTIAL']}");
    $apiUrl = str_replace(" ","%20",$url);
    $method = 'POST';

    $req = array(
      "orderList" => array(
        (object) array(
          "orderSN" => $reference,
          "trackingNumber" => $tracking_number,
          "shippingDocumentType" => "NORMAL_AIR_WAYBILL"
        )
      )
    );

    $json = json_encode($req);

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $apiUrl);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $start_date = now();
    $response = curl_exec($curl);
    curl_close($curl);
    $res = json_decode($response);
    $end_date = now();

    $status = FALSE;

    if( ! empty($res) && ! empty($res->code))
    {
      if($res->code == 200)
      {
        if( ! empty($res->data) && ! empty($res->data->result_list))
        {
          $ods = $res->data->result_list[0]->order_sn;
          $status = ($ods == $reference ? TRUE : FALSE);
        }
      }

      if($res->code == 500)
      {
        if( ! empty($res->data) && ! empty($res->data->result_list))
        {
          $this->error = $res->data->result_list[0]->fail_message;
        }
        else
        {
          $this->error = $res->serviceMessage;
        }
      }

      if ($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => $reference,
          'action' => $action,
          'channels' => 'SHOPEE',
          'status' => $status === TRUE ? 'success' : 'failed',
          'message' => isset($res->serviceMessage) ? $res->serviceMessage : 'No response',
          'request_json' => $json,
          'response_json' => $response,
          'start_date' => $start_date,
          'end_date' => $end_date
        );

        $this->ci->ix_api_logs_model->add_logs($logs);
      }
    }
    else
    {
      if ($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => $reference,
          'action' => $action,
          'channels' => 'SHOPEE',
          'status' => 'failed',
          'message' => isset($res->serviceMessage) ? $res->serviceMessage : 'No response',
          'request_json' => $json,
          'response_json' => $response,
          'start_date' => $start_date,
          'end_date' => $end_date
        );

        $this->ci->ix_api_logs_model->add_logs($logs);
      }
    }

    return $status;
  }


  public function shipping_document_result($reference, $shop_id)
  {
    $action = "shipped";
    $this->type = "shipping";
    $url = $this->api['WRX_API_HOST'];
    $url .= "shopee/{$shop_id}/shipping-document-result";
    $this->api_path = $url;

    $headers = array("Content-Type:application/json","Authorization:Bearer {$this->api['WRX_API_CREDENTIAL']}");
    $apiUrl = str_replace(" ","%20",$url);
    $method = 'POST';

    $req = array(
      "orderList" => array(
        (object) array(
          "orderSN" => $reference,
          "shippingDocumentType" => "NORMAL_AIR_WAYBILL"
        )
      )
    );

    $json = json_encode($req);

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $apiUrl);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $start_date = now();
    $response = curl_exec($curl);
    curl_close($curl);
    $res = json_decode($response);
    $end_date = now();

    $status = FALSE;

    if( ! empty($res) && ! empty($res->code))
    {
      if($res->code == 200)
      {
        if( ! empty($res->data) && ! empty($res->data->result_list))
        {          
          if($res->data->result_list[0]->status != "READY")
          {
            $this->error = $res->data->result_list[0]->fail_message;
          }

          $status = $res->data->result_list[0]->status === "READY" ? TRUE : FALSE;
        }
      }
      else
      {
        $this->error = $res->serviceMessage;
      }

      if ($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => $reference,
          'action' => $action,
          'channels' => 'SHOPEE',
          'status' => $status === TRUE ? 'success' : 'failed',
          'message' => isset($res->serviceMessage) ? $res->serviceMessage : 'No response',
          'request_json' => $json,
          'response_json' => $response,
          'start_date' => $start_date,
          'end_date' => $end_date
        );

        $this->ci->ix_api_logs_model->add_logs($logs);
      }
    }
    else
    {
      $this->error = "No response";

      if ($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => $reference,
          'action' => $action,
          'channels' => 'SHOPEE',
          'status' => 'failed',
          'message' => isset($res->serviceMessage) ? $res->serviceMessage : 'No response',
          'request_json' => $json,
          'response_json' => $response,
          'start_date' => $start_date,
          'end_date' => $end_date
        );

        $this->ci->ix_api_logs_model->add_logs($logs);
      }
    }

    return $status;
  }


  public function shipping_document_download($reference, $shop_id)
  {
    $action = "shipped";
    $this->type = "shipping";
    $url = $this->api['WRX_API_HOST'];
    $url .= "shopee/{$shop_id}/shipping-document-download";
    $this->api_path = $url;

    $headers = array("Content-Type:application/json","Authorization:Bearer {$this->api['WRX_API_CREDENTIAL']}");
    $apiUrl = str_replace(" ","%20",$url);
    $method = 'POST';

    $req = array(
      "shippingDocumentType" => "NORMAL_AIR_WAYBILL",
      "orderList" => array(
        (object) array(
          "orderSN" => $reference,
        )
      )
    );

    $json = json_encode($req);

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $apiUrl);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $start_date = now();
    $response = curl_exec($curl);
    curl_close($curl);
    $res = json_decode($response);
    $end_date = now();
    $status = FALSE;
    $ds = NULL;

    if( ! empty($res) && ! empty($res->code))
    {
      if($res->code == 200)
      {
        if( ! empty($res->data))
        {
          $status = TRUE;
          $ds = $res->data;
        }
      }
      else
      {
        $this->error = $res->serviceMessage;
      }

      if ($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => $reference,
          'action' => $action,
          'channels' => 'SHOPEE',
          'status' => $status === TRUE ? 'success' : 'failed',
          'message' => isset($res->serviceMessage) ? $res->serviceMessage : 'No response',
          'request_json' => $json,
          'response_json' => $response,
          'start_date' => $start_date,
          'end_date' => $end_date
        );

        $this->ci->ix_api_logs_model->add_logs($logs);
      }
    }
    else
    {
      $this->error = "No response";

      if ($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $this->api_path,
          'type' => $this->type,
          'code' => $reference,
          'action' => $action,
          'channels' => 'SHOPEE',
          'status' => 'failed',
          'message' => isset($res->serviceMessage) ? $res->serviceMessage : 'No response',
          'request_json' => $json,
          'response_json' => $response,
          'start_date' => $start_date,
          'end_date' => $end_date
        );

        $this->ci->ix_api_logs_model->add_logs($logs);
      }
    }

    return $status === TRUE ? $ds : FALSE;
  }

} //-- end class


 ?>

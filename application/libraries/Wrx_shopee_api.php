<?php

class Wrx_shopee_api
{
  private $url;
  private $token;
  private $api;
  protected $ci;
  public $error;
  public $logs_json = FALSE;
  public $test = FALSE;

  public function __construct()
  {
    $this->ci =& get_instance();
		$this->ci->load->model('rest/V1/wrx_api_logs_model');
    $this->ci->load->model('orders/orders_model');
    $this->ci->load->model('inventory/qc_model');

    $this->api = getWrxApiConfig();
  }

  public function test()
  {
    print_r($this->api);
  }


  public function get_order_status($reference)
  {
    $action = "get_order_detail";
    $type = "status";
    $url = $this->api['WRX_API_HOST'];
    $url .= "shopee/order/{$reference}";
    $api_path = $url;

    $headers = array("Authorization:Bearer {$this->api['WRX_API_CREDENTIAL']}");
    $apiUrl = str_replace(" ","%20",$url);
    $method = 'GET';

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($curl);
    curl_close($curl);
    $res = json_decode($response);

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
          return $res->data[0]->order_status;
        }
      }
    }

    return FALSE;
  }


  //--- for shopee
  public function get_shipping_param($reference)
  {
    $action = "get_shipping_param";
    $type = "shipping";
    $url = $this->api['WRX_API_HOST'];
    $url .= "shopee/shipping-parameter?orderSN={$reference}";
    $api_path = $url;

    $headers = array("Authorization:Bearer {$this->api['WRX_API_CREDENTIAL']}");
    $apiUrl = str_replace(" ","%20",$url);
    $method = 'GET';

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($curl);
    curl_close($curl);
    $res = json_decode($response);

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

        return $ds;
      }
    }

    return FALSE;
  }


  public function ship_order($reference, $pickup_data)
  {
    $action = "ship_order";
    $type = "shipping";
    $url = $this->api['WRX_API_HOST'];
    $url .= "shopee/ship-order";
    $api_path = $url;

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
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($curl);
    curl_close($curl);
    $res = json_decode($response);

    if( ! empty($res) && ! empty($res->code))
    {
      if($res->code == 200)
      {
        return TRUE;
      }
      else
      {
        $this->error = $res->serviceMessage;
      }
    }

    return FALSE;
  }


  public function get_tracking_number($reference)
  {
    $action = "get_tracking_number";
    $type = "shipping";
    $url = $this->api['WRX_API_HOST'];
    $url .= "shopee/tracking-number?orderSN={$reference}";
    $api_path = $url;

    $headers = array("Authorization:Bearer {$this->api['WRX_API_CREDENTIAL']}");
    $apiUrl = str_replace(" ","%20",$url);
    $method = 'GET';

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($curl);
    curl_close($curl);
    $res = json_decode($response);

    if( ! empty($res) && ! empty($res->code) && $res->code == 200)
    {
      if( ! empty($res->data))
      {
        return $res->data->tracking_number;
      }
    }

    return FALSE;
  }


  public function create_shipping_document($reference, $tracking_number)
  {
    $action = "create_shipping_document";
    $type = "shipping";
    $url = $this->api['WRX_API_HOST'];
    $url .= "shopee/shipping-document-create";
    $api_path = $url;

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
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($curl);
    curl_close($curl);
    $res = json_decode($response);

    if( ! empty($res) && ! empty($res->code))
    {
      if($res->code == 200)
      {
        if( ! empty($res->data) && ! empty($res->data->result_list))
        {
          $ods = $res->data->result_list[0]->order_sn;

          return $ods == $reference ? TRUE : FALSE;
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
    }

    return FALSE;
  }

  public function shipping_document_result($reference)
  {
    $action = "shipping_document_result";
    $type = "shipping";
    $url = $this->api['WRX_API_HOST'];
    $url .= "shopee/shipping-document-result";
    $api_path = $url;

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
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($curl);
    curl_close($curl);
    $res = json_decode($response);

    if( ! empty($res) && ! empty($res->code))
    {
      if($res->code == 200)
      {
        if( ! empty($res->data) && ! empty($res->data->result_list))
        {
          $status = $res->data->result_list[0]->status;

          if($status != "READY")
          {
            $this->error = $res->data->result_list[0]->fail_message;
          }

          return $status === "READY" ? TRUE : FALSE;
        }
      }
      else
      {
        $this->error = $res->serviceMessage;
      }
    }
    else
    {
      $this->error = "No response";
    }

    return FALSE;
  }


  public function shipping_document_download($reference)
  {
    $action = "shipping_document_result";
    $type = "shipping";
    $url = $this->api['WRX_API_HOST'];
    $url .= "shopee/shipping-document-download";
    $api_path = $url;

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
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($curl);
    curl_close($curl);
    $res = json_decode($response);

    if( ! empty($res) && ! empty($res->code))
    {
      if($res->code == 200)
      {
        if( ! empty($res->data))
        {
          return $res->data;
        }
      }
      else
      {
        $this->error = $res->serviceMessage;
      }
    }
    else
    {
      $this->error = "No response";
    }

    return FALSE;
  }

} //-- end class


 ?>

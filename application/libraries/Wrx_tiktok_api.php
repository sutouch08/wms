<?php

class Wrx_tiktok_api
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


  public function get_order_detail($reference, $shop_id)
  {
    $action = "get_order_detail";
    $type = "status";
    $url = $this->api['WRX_API_HOST'];
    $url .= "tiktok/{$shop_id}/order/{$reference}";
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
          $ds = $res->data[0];
          $package_id = empty($ds->packages[0]->id) ? $ds->package_list[0]->package_id : $ds->packages[0]->id;
          $status = empty($ds->status) ? $ds->order_status : $ds->status;

          return (object) array(
            'package_id' => $package_id, //$ds->packages[0]->id,
            'order_status' => $status, //$ds->status,
            'tracking_number' => empty($ds->tracking_number) ? NULL : $ds->tracking_number
          );
        }
      }
    }

    $this->error = $response;

    return FALSE;
  }


  public function ship_package($package_id, $shop_id)
  {
    $action = "ship-package";
    $type = "Shipping";
    $url = $this->api['WRX_API_HOST'];
    $url .= "tiktok/{$shop_id}/ship-package";
    $api_path = $url;

    $headers = array("Content-Type:application/json","Authorization:Bearer {$this->api['WRX_API_CREDENTIAL']}");
    $apiUrl = str_replace(" ","%20",$url);
    $method = 'POST';

    $json = '{"shipPackages":[{"packageID":"'.$package_id.'","handoverMethod":"PICKUP"}]}';

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
      if($res->code == 200 && $res->status == 'success')
      {
        return TRUE;
      }
      else
      {
        $this->error = $res->serviceMessage;
        return FALSE;
      }
    }

    $this->error = $response;

    return FALSE;
  }


  public function get_shipping_label($package_id, $shop_id)
  {
    $action = "get-ship-document";
    $type = "Shipping";
    $url = $this->api['WRX_API_HOST'];
    $url .= "tiktok/{$shop_id}/ship-document";
    $api_path = $url;

    $headers = array("Content-Type:application/json","Authorization:Bearer {$this->api['WRX_API_CREDENTIAL']}");
    $apiUrl = str_replace(" ","%20",$url);
    $method = 'POST';

    $req = array(
      "packageID" => $package_id,
      "documentType" => "SHIPPING_LABEL",
      "documentSize" => "A5"
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
      if($res->code === 200 && $res->status === 'success')
      {
        return $res->data;
      }
      else
      {
        $this->error = $res->serviceMessage;
        return FALSE;
      }
    }
    else
    {
      $this->error = "Cannot get data from Tiktok api at this time";
      return FALSE;
    }

    return FALSE;
  }


  public function get_order_status($reference, $shop_id)
  {
    $action = "get_order_detail";
    $type = "status";
    $url = $this->api['WRX_API_HOST'];
    $url .= "tiktok/{$shop_id}/order/{$reference}";
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
            100 Unpaid
            105 On hold
            111 AWAITING_SHIPMENT : Awaiting the seller to place a logistic order. OK
            112 AWAITING_COLLECTION : The logistic order was placed. At least one item in the order is still waiting to be collected by the carrier. OK
            114 Partial shipment
            121 IN_TRANSIT : All items have been collected by the carrier. At least one package is has yet to be delivered to the buyer.
            122 DELIVERED : All items have been delivered to the buyer.
            130 COMPLETED : The order has been completed. Completed orders can no longer be returned or refunded.
            140 CANCELLED : The order has been canceled. The order can be canceled by the buyer, the seller, the TikTok SYSTEM, or a TikTok OPERATOR.
          */
          /*
            The new  order status.
            Possible values:
            - UNPAID: The order is placed, but payment is not yet completed.
            - ON_HOLD: The order is accepted and is waiting for fulfillment so the buyer may still cancel without the seller’s approval.
                       If order_type=PRE_ORDER, it also means the product is still awaiting release so payment will only be authorized 1 day before the release,
                       but the seller should start preparing for the release.
            - AWAITING_SHIPMENT: The order is ready for shipment, but no items are shipped yet.
            - PARTIALLY_SHIPPING: Some items in the order are shipped, but not all.
            - AWAITING_COLLECTION: The shipment is arranged, but the package is waiting to be collected by the carrier.
            - IN_TRANSIT: The package is collected by the carrier and delivery is in progress.
            - DELIVERED: The package is delivered to buyer.
            - COMPLETED: The order is completed, and no further returns or refunds are allowed.
            - CANCELLED: The order is cancelled.
          */

          return empty($res->data[0]->status) ? $res->data[0]->order_status : $res->data[0]->status;
        }
      }
    }

    return FALSE;
  }
} //-- end class


 ?>

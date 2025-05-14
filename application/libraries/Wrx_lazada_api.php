<?php

class Wrx_lazada_api
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
    $action = "get_order_status";
    $type = "status";
    $url = $this->api['WRX_API_HOST'];
    $url .= "lazada/order/{$reference}";
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
        if( ! empty($res->data->statuses))
        {
          return $res->data->statuses[0];
        }
      }
    }

    return FALSE;
  }


  public function get_order_item_id($reference)
  {
    $action = "get_order_item_id";
    $type = "status";
    $url = $this->api['WRX_API_HOST'];
    $url .= "lazada/order/item/{$reference}";
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
          $arr = [];

          foreach($res->data as $rs)
          {
            $arr[] = $rs->order_item_id;
          }

          return $arr; //$res->data[0]->order_item_id;
        }
      }
    }

    return FALSE;
  }


  public function packed($reference, $order_item_ids)
  {
    $action = "order packed";
    $type = "shipping";
    $url = $this->api['WRX_API_HOST'];
    $url .= "lazada/order/packed";
    $api_path = $url;

    $headers = array("Content-Type:application/json","Authorization:Bearer {$this->api['WRX_API_CREDENTIAL']}");
    $apiUrl = str_replace(" ","%20",$url);
    $method = 'POST';
    $req = array(
      'packOrderList' => array(
        array(
          'orderID' => intval($reference),
          'orderItemIDs' => $order_item_ids
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
      if($res->code == 200 &&  ! empty($res->data->pack_order_list[0]->order_item_list[0]))
      {
        $pk = [];

        foreach($res->data->pack_order_list[0]->order_item_list as $rs)
        {

          $pk[$rs->package_id] = (object) array('package_id' => $rs->package_id, 'tracking_number' => $rs->tracking_number);
        }

        return $pk;
      }
      else
      {
        $this->error = $res->serviceMessage;
      }
    }

    return FALSE;
  }


  public function ship_package($packages)
  {
    $action = "ship_order";
    $type = "shipping";
    $url = $this->api['WRX_API_HOST'];
    $url .= "lazada/ship-package";
    $api_path = $url;

    $headers = array("Content-Type:application/json","Authorization:Bearer {$this->api['WRX_API_CREDENTIAL']}");
    $apiUrl = str_replace(" ","%20",$url);
    $method = 'POST';

    $req = array(
      "packages" => array($packages)
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


  public function get_shipping_label($packages)
  {
    $action = "get_shipping_label";
    $type = "shipping";
    $url = $this->api['WRX_API_HOST'];
    $url .= "lazada/ship-document";
    $api_path = $url;

    $headers = array("Content-Type:application/json","Authorization:Bearer {$this->api['WRX_API_CREDENTIAL']}");
    $apiUrl = str_replace(" ","%20",$url);
    $method = 'POST';

    $req = array(
      "packages" => array($packages),
      "printItemList" => FALSE
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
      if($res->code == 200 && ! empty($res->data))
      {
        return $res->data;
      }
      else
      {
        $this->error = $res->serviceMessage;
      }
    }

    return FALSE;
  }

} //-- end class


 ?>

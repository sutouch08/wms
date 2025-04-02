<?php

class Wrx_api
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

    if( ! empty($res))
    {

    }
    else
    {
      $this->error = "No response";

      if($this->logs_json)
      {
        $logs = array(
          'trans_id' => genUid(),
          'type' => $type,
          'api_path' => $api_path,
          'code' => $reference,
          'action' => $action,
          'status' => 'failed',
          'message' => 'No response',
          'request_json' => NULL,
          'response_json' => NULL
        );

        $this->ci->wrx_api_logs_model->add_api_logs($logs);
      }

      return FALSE;
    }

    return $res;
  }
  
} //-- end class


 ?>

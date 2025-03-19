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


  public function cancel_transfer($code)
  {
    if( ! empty($code))
    {
      $token = $this->token;
      $url = "https://warrix.com/rest/V1/eol/erp/ww/cancel";
      $api_path = $url;

      $setHeaders = array("Content-Type:application/json","Authorization:Bearer {$token}");
	    $apiUrl = str_replace(" ","%20",$url);
	    $method = 'POST';

      $ds = array(
        'ref_code' => $code
      );

      $json = json_encode($ds);

      if( ! $this->test)
      {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $setHeaders);
        $response = curl_exec($ch);
        curl_close($ch);

        $res = json_decode($response);

        if( ! empty($res))
        {
          if( ! isset($res->status))
          {
            $message = empty($res->message) ? $response : $res->message;
            $this->error = $message;

            if($this->logs_json)
            {
              $logs = array(
                'trans_id' => genUid(),
                'type' => "WW",
                'api_path' => $api_path,
                'code' => $code,
                'action' => 'cancel',
                'status' => 'failed',
                'message' => $message,
                'request_json' => $json,
                'response_json' => $response
              );

              $this->ci->pos_api_logs_model->add_api_logs($logs);
            }

            return FALSE;
          }
          else
          {
            if(is_true($res->status))
            {
              if($this->logs_json)
              {
                $logs = array(
                  'trans_id' => genUid(),
                  'type' => "WW",
                  'api_path' => $api_path,
                  'code' => $code,
                  'action' => 'cancel',
                  'status' => 'success',
                  'message' => $res->message,
                  'request_json' => $json,
                  'response_json' => $response
                );

                $this->ci->pos_api_logs_model->add_api_logs($logs);
              }

              return TRUE;
            }
            else
            {
              if($this->logs_json)
              {
                $logs = array(
                  'trans_id' => genUid(),
                  'type' => "WW",
                  'api_path' => $api_path,
                  'code' => $code,
                  'action' => 'cancel',
                  'status' => 'failed',
                  'message' => $res->message,
                  'request_json' => $json,
                  'response_json' => $response
                );

                $this->ci->pos_api_logs_model->add_api_logs($logs);
              }

              return FALSE;
            }
          }
        }
        else
        {
          $this->error = "No response";

          if($this->logs_json)
          {
            $logs = array(
              'trans_id' => genUid(),
              'type' => "WW",
              'api_path' => $api_path,
              'code' => $code,
              'action' => 'create',
              'status' => 'failed',
              'message' => 'No response',
              'request_json' => $json,
              'response_json' => NULL
            );

            $this->ci->pos_api_logs_model->add_api_logs($logs);
          }

          return FALSE;
        }
      }
      else
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $api_path,
          'type' =>'WW',
          'code' => $doc->code,
          'action' => 'create',
          'status' => 'test',
          'message' => 'test',
          'request_json' => $json,
          'response_json' => NULL
        );

        $this->ci->pos_api_logs_model->add_api_logs($logs);

        return TRUE;
      }
    }

    return FALSE;
  }

} //-- end class


 ?>

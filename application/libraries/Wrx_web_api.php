<?php

class Wrx_web_api
{
  private $url;
  private $token;
  private $api;
  protected $ci;
  public $error;
  public $logs_json = FALSE;
  public $test = FALSE;
  public $type = "tracking";

  public function __construct()
  {
    $this->ci =& get_instance();
		$this->ci->load->model('rest/V1/ix_api_logs_model');
    $this->ci->load->model('orders/orders_model');

    $this->api = getWrxApiConfig();
    $this->logs_json = is_true($this->api['WRX_LOG_JSON']);
    $this->test = is_true($this->api['WRX_API_TEST']);
  }


  public function create_shipment($code, $tracking)
  {
    $action = "create";
    $this->type = "tracking";
    $url = $this->api['WRX_API_HOST'];
    $url .= "magento/order/ship";
    $api_path = $url;

    $headers = array("Content-Type:application/json","Authorization:Bearer {$this->api['WRX_API_CREDENTIAL']}");
    $apiUrl = str_replace(" ","%20",$url);
    $method = 'POST';

    $playload = array(
      'orderID' => $code,
      'email' => "",
      'tracking' => array(
        array('trackNo' => $tracking)
      )
    );

    if( ! empty($playload))
    {
      $json = json_encode($playload);

      if($this->test === TRUE)
      {
        if($this->logs_json)
        {
          $logs = array(
            'trans_id' => genUid(),
            'type' => $this->type,
            'api_path' => $api_path,
            'code' => $code,
            'action' => 'test',
            'status' => 'test',
            'message' => 'test',
            'request_json' => $json,
            'response_json' => NULL
          );

          $this->ci->ix_api_logs_model->add_logs($logs);
        }

        return TRUE;
      }
      else
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

        if( ! empty($res) && ! empty($res->code))
        {
          if($res->code == 200 && $res->status == 'success')
          {
            if( ! empty($res->data))
            {
              if($this->logs_json)
              {
                $logs = array(
                  'trans_id' => genUid(),
                  'api_path' => $api_path,
                  'type' => $this->type,
                  'code' => $code,
                  'action' => $action,
                  'status' => 'success',
                  'message' => 'success',
                  'request_json' => $json,
                  'response_json' => $response
                );

                $this->ci->ix_api_logs_model->add_logs($logs);
              }

              return $res->data->success;
            }
            else
            {
              $this->error = empty($res->message) ? $res->message : "Unknow error";

              if($this->logs_json)
              {
                $logs = array(
                  'trans_id' => genUid(),
                  'api_path' => $api_path,
                  'type' => $this->type,
                  'code' => $code,
                  'action' => $action,
                  'status' => 'failed',
                  'message' => $this->error,
                  'request_json' => $json,
                  'response_json' => $response
                );

                $this->ci->ix_api_logs_model->add_logs($logs);
              }

              return FALSE;
            }
          }
          else
          {
            $this->error = empty($res->data->message) ? $res->data->message : $res->serviceMessage;

            if($this->logs_json)
            {
              $logs = array(
                'trans_id' => genUid(),
                'api_path' => $api_path,
                'type' => $this->type,
                'code' => $code,
                'action' => $action,
                'status' => 'failed',
                'message' => $this->error,
                'request_json' => $json,
                'response_json' => $response
              );

              $this->ci->ix_api_logs_model->add_logs($logs);
            }

            return FALSE;
          }
        }

        $this->error = $response;

        return FALSE;
      }
    }
    else
    {
      $this->error = "Missing required parameter";
      return FALSE;
    }
  }


  public function send_tracking($code, $tracking)
  {
    $action = "create";
    $this->type = "tracking";
    $url = $this->api['WRX_API_HOST'];
    $url .= "magento/order/ship";
    $api_path = $url;

    $headers = array("Content-Type:application/json","Authorization:Bearer {$this->api['WRX_API_CREDENTIAL']}");
    $apiUrl = str_replace(" ","%20",$url);
    $method = 'POST';

    $playload = array(
      'orderID' => $code,
      'email' => "",
      'tracking' => array(
        array('trackNo' => $tracking)
      )
    );

    if( ! empty($playload))
    {
      $json = json_encode($playload);

      if($this->test === TRUE)
      {
        if($this->logs_json)
        {
          $logs = array(
            'trans_id' => genUid(),
            'type' => $this->type,
            'api_path' => $api_path,
            'code' => $code,
            'action' => 'test',
            'status' => 'test',
            'message' => 'test',
            'request_json' => $json,
            'response_json' => NULL
          );

          $this->ci->ix_api_logs_model->add_logs($logs);
        }

        return TRUE;
      }
      else
      {
        $logs = array(
          'trans_id' => genUid(),
          'type' => $this->type,
          'api_path' => $api_path,
          'code' => $code,
          'action' => $action,
          'status' => 'success',
          'message' => NULL,
          'request_json' => $json,
          'response_json' => NULL
        );

        $this->ci->ix_api_logs_model->add_logs($logs);

        $cmd = "curl -X POST {$apiUrl}"
        ." -H 'Content-Type:application/json'"
        ." -H 'Authorization:Bearer {$this->api['WRX_API_CREDENTIAL']}'"
        ." -d '" . $json . "'"
        ." > /dev/null 2>&1 &";
        exec($cmd);
        return TRUE;
      }
    }
    else
    {
      $this->error = "Missing required parameter";
      return FALSE;
    }
  }
} //-- end class


 ?>

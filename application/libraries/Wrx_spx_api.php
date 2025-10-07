<?php

class Wrx_spx_api
{
  private $url;
  private $token;
  private $api;
  protected $ci;
  public $error;
  public $logs_json = FALSE;
  public $test = FALSE;
  public $type = "shipping";

  public function __construct()
  {
    $this->ci =& get_instance();
		$this->ci->load->model('rest/V1/ix_api_logs_model');
    $this->ci->load->model('orders/orders_model');
    $this->ci->load->model('inventory/qc_model');

    $this->api = getWrxApiConfig();
    $this->logs_json = is_true(getConfig('SPX_LOG_JSON'));
    $this->test = is_true(getConfig('SPX_API_TEST'));
  }
  
  public function get_pickup_time()
  {
    $action = "get_pickup_time";
    $this->type = "setup";
    $url = $this->api['WRX_API_HOST'];
    $url .= "spx/pickup-time";
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
          $ds = $res->data->data[0];

          return (object) array(
            'date' => $ds->date,
            'pickup_time' => $ds->pickup_time,
            'pickup_time_range_id' => $ds->slots[0]->pickup_time_range_id,
            'pickup_time_range' => $ds->slots[0]->pickup_time_range
          );
        }
      }
    }

    $this->error = $response;

    return FALSE;
  }


  public function create_parcels($code, $packages)
  {
    $action = "ship-package";
    $this->type = "Shipping";
    $url = $this->api['WRX_API_HOST'];
    $url .= "spx/order";
    $api_path = $url;

    $headers = array("Content-Type:application/json","Authorization:Bearer {$this->api['WRX_API_CREDENTIAL']}");
    $apiUrl = str_replace(" ","%20",$url);
    $method = 'POST';

    if( ! empty($packages))
    {
      $req = array(
        "orders" => $packages
      );

      $json = json_encode($req);

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

              return $res->data;
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
            $this->error = $res->serviceMessage;

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


  public function get_shipping_label($code, $batch_no)
  {
    $action = "get-ship-document";
    $this->type = "Shipping";
    $url = $this->api['WRX_API_HOST'];
    $url .= "spx/awb/{$batch_no}";
    $api_path = $url;

    $headers = array("Content-Type:application/json","Authorization:Bearer {$this->api['WRX_API_CREDENTIAL']}");
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
      if($res->code === 200 && $res->status === 'success')
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
            'request_json' => NULL,
            'response_json' => $response
          );

          $this->ci->ix_api_logs_model->add_logs($logs);
        }

        return $res->data;
      }
      else
      {
        if($this->logs_json)
        {
          $logs = array(
            'trans_id' => genUid(),
            'api_path' => $api_path,
            'type' => $this->type,
            'code' => $code,
            'action' => $action,
            'status' => 'failed',
            'message' => $res->serviceMessage,
            'request_json' => NULL,
            'response_json' => $response
          );

          $this->ci->ix_api_logs_model->add_logs($logs);
        }

        $this->error = $res->serviceMessage;
        return FALSE;
      }
    }
    else
    {
      $this->error = "Cannot get data from SPX api at this time";
      return FALSE;
    }

    return FALSE;
  }
} //-- end class


 ?>

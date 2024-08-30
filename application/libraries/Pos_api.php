<?php
class Pos_api
{
  private $web_url;
  private $token;
  protected $ci;
  public $error;
  public $logs_json = FALSE;
  public $test = FALSE;

  public function __construct()
  {
    $this->ci =& get_instance();
		$this->ci->load->model('rest/V1/pos_api_logs_model');
    $this->ci->load->model('inventory/transfer_model');
    $this->token = "7psnl3j0ies0kvh28xhb4bovzk1kwoa0";

    $this->logs_json = TRUE;
    $this->test = getConfig('POS_WW_TEST');
  }

  public function export_transfer($doc, $details)
  {
    if( ! empty($doc) && ! empty($details))
    {
      $token = $this->token;
      $url = "https://warrix.com/rest/V1/eol/erp/ww/create";
      $api_path = $url;

      $setHeaders = array("Content-Type:application/json","Authorization:Bearer {$token}");
	    $apiUrl = str_replace(" ","%20",$url);
	    $method = 'POST';

      $ds = array(
        'ref_code' => $doc->code,
        'warehouse_code_from' => $doc->from_warehouse,
        'warehouse_code_to' => $doc->to_warehouse,
        'date' => $doc->date_add,
        'remark' => $doc->remark,
        'items' => array()
      );


      if( ! empty($details))
      {
        $items = [];

        foreach($details as $rs)
        {
          $items[] = array(
            'product_code' => $rs->product_code,
            'qty' => floatval($rs->qty)
          );
        }

        $ds['items'] = $items;
      }

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
                'code' => $doc->code,
                'action' => 'create',
                'status' => 'failed',
                'message' => $message,
                'request_json' => $json,
                'response_json' => $response
              );

              $this->ci->pos_api_logs_model->add_api_logs($logs);
            }

            $arr = array(
              'pos_export' => $doc->pos_export == 1 ? 1 : 3,
              'pos_export_error' => $message
            );

            $this->ci->transfer_model->update($doc->code, $arr);

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
                  'code' => $doc->code,
                  'action' => 'create',
                  'status' => 'success',
                  'message' => $res->message,
                  'request_json' => $json,
                  'response_json' => $response
                );

                $this->ci->pos_api_logs_model->add_api_logs($logs);
              }

              $arr = array(
                'pos_export' => 1,
                'pos_export_error' => NULL
              );

              $this->ci->transfer_model->update($doc->code, $arr);

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
                  'code' => $doc->code,
                  'action' => 'create',
                  'status' => 'failed',
                  'message' => $res->message,
                  'request_json' => $json,
                  'response_json' => $response
                );

                $this->ci->pos_api_logs_model->add_api_logs($logs);
              }

              $arr = array(
                'pos_export' => $doc->pos_export == 1 ? 1 : 3,
                'pos_export_error' => $res->message
              );

              $this->ci->transfer_model->update($doc->code, $arr);

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
              'code' => $doc->code,
              'action' => 'create',
              'status' => 'failed',
              'message' => 'No response',
              'request_json' => $json,
              'response_json' => NULL
            );

            $this->ci->pos_api_logs_model->add_api_logs($logs);
          }

          $arr = array(
            'pos_export' => $doc->pos_export == 1 ? 1 : 3,
            'pos_export_error' => $this->error
          );

          $this->ci->transfer_model->update($doc->code, $arr);

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

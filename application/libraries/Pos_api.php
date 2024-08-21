<?php
class Pos_api
{
  private $web_url;
  private $token;
  protected $ci;
  public $error;
  public $logs_json = FALSE;
  public $test = TRUE;

  public function __construct()
  {
    $this->ci =& get_instance();
		$this->ci->load->model('rest/V1/pos_api_logs_model');
    $this->token = "t0dr68gqxi0iuiaogi6k89oiu3c5yqxb";
    $this->logs_json = TRUE;
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

            return FALSE;
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

}


 ?>

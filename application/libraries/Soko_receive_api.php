<?php
class Soko_receive_api
{

  private $url;
  public $home;
	public $wms;
	protected $ci;
  public $type;
  public $error;
	public $log_json;
  public $test = FALSE;

  public function __construct()
  {
		$this->ci =& get_instance();
		$this->ci->load->model('rest/V1/soko_api_logs_model');
    $this->ci->load->model('masters/products_model');
		$this->url = getConfig('SOKOJUNG_API_HOST');
    $this->key = getConfig('SOKOJUNG_API_CREDENTIAL');
		$this->log_json = is_true(getConfig('SOKOJUNG_LOG_JSON'));
    $this->test = is_true(getConfig('SOKOJUNG_TEST'));
  }

	//---- export receive po
	public function create_receive_po($doc, $po_code, $invoice, $details)
	{
		$sc = TRUE;
    $this->ci->load->model('inventory/receive_po_model');

		$this->type = "WR";

		$ds = array(
      'external_id' => $doc->code,
      'expect_date' => date('Y-m-d'),
      'information_number' => $invoice,
      'ix_status' => "",
      'type' => $this->type,
      'comment' => $doc->remark,
      'items' => []
    );


		if( ! empty($details))
		{
			foreach($details as $rs)
			{

				if($rs->qty > 0)
				{
          $arr = array(
            'item_sku' => $rs->product_code,
            'item_qty' => round($rs->qty, 2),
            'lot' => $po_code,
            'comment' => ''
          );

          array_push($ds['items'], $arr);
				}
			}

      $api_path = $this->url."advices";
      $url = $api_path;
			$method = "POST";

			$headers = array(
				"Content-Type: application/json",
        "Authorization: Basic {$this->key}"
			);

      $json = json_encode($ds);

      if( ! $this->test)
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

        if( ! empty($res))
        {
          if(empty($res->error))
          {
            if(empty($res->status))
            {
              if(! empty($res->id))
              {
                $res->status = 'success';
                $res->message = $response;
                $this->ci->receive_po_model->update($doc->code, ['soko_code' => $res->id]);
              }
              else
              {
                $sc = FALSE;
                $this->error = $response;
                $res->status = 'failed';
                $res->message = $response;
              }
            }
            else
            {
              if($res->status != 'success' && empty($res->id))
              {
                $sc = FALSE;
                $this->error = $res->message;
              }
              else
              {
                $this->ci->receive_po_model->update($doc->code, ['soko_code' => $res->id]);
              }
            }
          }

          else
          {
            $sc = FALSE;
            $res->status = "failed";
            $res->message = $res->error;
            $this->error = $response;
          }

          if($this->log_json)
          {
            $logs = array(
              'trans_id' => genUid(),
              'api_path' => $api_path,
              'code' => $doc->code,
              'action' => 'create',
              'status' => $res->status == 'success' ? 'success' : 'failed',
              'message' => $res->message,
              'request_json' => $json,
              'response_json' => $response
            );

            $this->ci->soko_api_logs_model->add_api_logs($logs);
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "No response";

          if($this->log_json)
          {
            $logs = array(
              'trans_id' => genUid(),
              'api_path' => $api_path,
              'code' => $doc->code,
              'action' => 'create',
              'status' => 'failed',
              'message' => 'No response',
              'request_json' => $json,
              'response_json' => NULL
            );

            $this->ci->soko_api_logs_model->add_api_logs($logs);
          }
        }
      }
      else
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $api_path,
          'code' => $doc->code,
          'action' => 'create',
          'status' => 'test',
          'message' => 'Test api',
          'request_json' => $json,
          'response_json' => NULL
        );

        $this->ci->soko_api_logs_model->add_api_logs($logs);
      }
		}
		else
		{
			$sc = FALSE;
			$this->error = "No data";
		}

    if($sc === TRUE)
		{
			$this->ci->soko_api_logs_model->add($doc->code, 'S', NULL, $this->type);
		}
		else
		{
			$this->ci->soko_api_logs_model->add($doc->code, 'E', $this->error, $this->type);
		}

		return $sc;
	}


  //---- cancel receive po
	public function cancel_receive_po($doc)
	{
		$sc = TRUE;
    $this->ci->load->model('inventory/receive_po_model');

		$this->type = "WR";

		if( ! empty($doc->soko_code))
		{
      $api_path = $this->url."advices/{$doc->soko_code}/cancel";
      $url = $api_path;
			$method = "PUT";

			$headers = array(
				"Content-Type: application/json",
        "Authorization: Basic {$this->key}"
			);

      if( ! $this->test)
      {
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
          if(empty($res->error))
          {
            if(empty($res->status))
            {
              if(! empty($res->id))
              {
                $res->status = 'success';
                $res->message = $response;
              }
              else
              {
                $sc = FALSE;
                $this->error = $response;
                $res->status = 'failed';
                $res->message = $response;
              }
            }
            else
            {
              if($res->status != 'success' && empty($res->id))
              {
                $sc = FALSE;
                $this->error = $res->message;
              }
            }
          }
          else
          {
            $sc = FALSE;
            $res->status = "failed";
            $res->message = $res->error;
            $this->error = $response;
          }

          if($this->log_json)
          {
            $logs = array(
              'trans_id' => genUid(),
              'api_path' => $api_path,
              'code' => $doc->code,
              'action' => 'cancel',
              'status' => $res->status == 'success' ? 'success' : 'failed',
              'message' => $res->message,
              'request_json' => NULL,
              'response_json' => $response
            );

            $this->ci->soko_api_logs_model->add_api_logs($logs);
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "No response";

          if($this->log_json)
          {
            $logs = array(
              'trans_id' => genUid(),
              'api_path' => $api_path,
              'code' => $doc->code,
              'action' => 'cancel',
              'status' => 'failed',
              'message' => 'No response',
              'request_json' => NULL,
              'response_json' => NULL
            );

            $this->ci->soko_api_logs_model->add_api_logs($logs);
          }
        }
      }
      else
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $api_path,
          'code' => $doc->code,
          'action' => 'cancel',
          'status' => 'test',
          'message' => 'Test api',
          'request_json' => NULL,
          'response_json' => NULL
        );

        $this->ci->soko_api_logs_model->add_api_logs($logs);
      }
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing Soko advice id";
		}

    if($sc === TRUE)
		{
			$this->ci->soko_api_logs_model->add($doc->code, 'S', NULL, 'Cancel');
		}
		else
		{
			$this->ci->soko_api_logs_model->add($doc->code, 'E', $this->error, 'Cancel');
		}

		return $sc;
	}


  //---- export receive transform
	public function create_receive_transform($doc, $order_code, $invoice, $details)
	{
		$sc = TRUE;
    $this->ci->load->model('inventory/receive_transform_model');

		$this->type = "RT";

		$ds = array(
      'external_id' => $doc->code,
      'expect_date' => date('Y-m-d'),
      'information_number' => $invoice,
      'ix_status' => "",
      'type' => $this->type,
      'comment' => $doc->remark,
      'items' => []
    );

		if( ! empty($details))
		{
			foreach($details as $rs)
			{
				if($rs->qty > 0)
				{
          $arr = array(
            'item_sku' => $rs->product_code,
            'item_qty' => round($rs->qty, 2),
            'lot' => $order_code,
            'comment' => ''
          );

          array_push($ds['items'], $arr);
				}
			}

      $api_path = $this->url."advices";
      $url = $api_path;
			$method = "POST";

			$headers = array(
				"Content-Type: application/json",
        "Authorization: Basic {$this->key}"
			);

      $json = json_encode($ds);

      if( ! $this->test)
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

        if( ! empty($res))
        {
          if(empty($res->error))
          {
            if(empty($res->status))
            {
              if(! empty($res->id))
              {
                $res->status = 'success';
                $res->message = $response;
                $this->ci->receive_transform_model->update($doc->code, ['soko_code' => $res->id]);
              }
              else
              {
                $sc = FALSE;
                $this->error = $response;
                $res->status = 'failed';
                $res->message = $response;
              }
            }
            else
            {
              if($res->status != 'success' && empty($res->id))
              {
                $sc = FALSE;
                $this->error = $res->message;
              }
              else
              {
                $this->ci->receive_transform_model->update($doc->code, ['soko_code' => $res->id]);
              }
            }
          }
          else
          {
            $sc = FALSE;
            $res->status = "failed";
            $res->message = $res->error;
            $this->error = $response;
          }

          if($this->log_json)
          {
            $logs = array(
              'trans_id' => genUid(),
              'api_path' => $api_path,
              'code' => $doc->code,
              'action' => 'create',
              'status' => $res->status == 'success' ? 'success' : 'failed',
              'message' => $res->message,
              'request_json' => $json,
              'response_json' => $response
            );

            $this->ci->soko_api_logs_model->add_api_logs($logs);
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "No response";

          if($this->log_json)
          {
            $logs = array(
              'trans_id' => genUid(),
              'api_path' => $api_path,
              'code' => $doc->code,
              'action' => 'create',
              'status' => 'failed',
              'message' => 'No response',
              'request_json' => $json,
              'response_json' => NULL
            );

            $this->ci->soko_api_logs_model->add_api_logs($logs);
          }
        }
      }
      else
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $api_path,
          'code' => $doc->code,
          'action' => 'create',
          'status' => 'test',
          'message' => 'Test api',
          'request_json' => $json,
          'response_json' => NULL
        );

        $this->ci->soko_api_logs_model->add_api_logs($logs);
      }
		}
		else
		{
			$sc = FALSE;
			$this->error = "No data";
		}

    if($sc === TRUE)
		{
			$this->ci->soko_api_logs_model->add($doc->code, 'S', NULL, $this->type);
		}
		else
		{
			$this->ci->soko_api_logs_model->add($doc->code, 'E', $this->error, $this->type);
		}

		return $sc;
	}

  //---- cancel receive transform
	public function cancel_receive_transform($doc)
	{
		$sc = TRUE;

		$this->type = "RT";

		if( ! empty($doc->soko_code))
		{
      $api_path = $this->url."advices/{$doc->soko_code}/cancel";
      $url = $api_path;
			$method = "PUT";

			$headers = array(
				"Content-Type: application/json",
        "Authorization: Basic {$this->key}"
			);

      if( ! $this->test)
      {
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
          if(empty($res->error))
          {
            if(empty($res->status))
            {
              if(! empty($res->id))
              {
                $res->status = 'success';
                $res->message = $response;
              }
              else
              {
                $sc = FALSE;
                $this->error = $response;
                $res->status = 'failed';
                $res->message = $response;
              }
            }
            else
            {
              if($res->status != 'success' && empty($res->id))
              {
                $sc = FALSE;
                $this->error = $res->message;
              }
            }
          }
          else
          {
            $sc = FALSE;
            $res->status = "failed";
            $res->message = $res->error;
            $this->error = $response;
          }

          if($this->log_json)
          {
            $logs = array(
              'trans_id' => genUid(),
              'api_path' => $api_path,
              'code' => $doc->code,
              'action' => 'cancel',
              'status' => $res->status == 'success' ? 'success' : 'failed',
              'message' => $res->message,
              'request_json' => NULL,
              'response_json' => $response
            );

            $this->ci->soko_api_logs_model->add_api_logs($logs);
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "No response";

          if($this->log_json)
          {
            $logs = array(
              'trans_id' => genUid(),
              'api_path' => $api_path,
              'code' => $doc->code,
              'action' => 'cancel',
              'status' => 'failed',
              'message' => 'No response',
              'request_json' => NULL,
              'response_json' => NULL
            );

            $this->ci->soko_api_logs_model->add_api_logs($logs);
          }
        }
      }
      else
      {
        $logs = array(
          'trans_id' => genUid(),
          'api_path' => $api_path,
          'code' => $doc->code,
          'action' => 'cancel',
          'status' => 'test',
          'message' => 'Test api',
          'request_json' => NULL,
          'response_json' => NULL
        );

        $this->ci->soko_api_logs_model->add_api_logs($logs);
      }
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing Soko advice id";
		}

    if($sc === TRUE)
		{
			$this->ci->soko_api_logs_model->add($doc->code, 'S', NULL, 'Cancel');
		}
		else
		{
			$this->ci->soko_api_logs_model->add($doc->code, 'E', $this->error, 'Cancel');
		}

		return $sc;
	}
} //--- end class
?>

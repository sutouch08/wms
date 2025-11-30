<?php
class Sap_api
{
  private $url;
  protected $ci;
	public $error;
  protected $timeout = 0; //-- time out in seconds;
  private $test = FALSE;
  private $token = NULL;
  private $logs_json = TRUE;
  private $endpoint = NULL;
  private $type = NULL;

  public function __construct()
  {
		$this->ci =& get_instance();
    $this->endpoint = getConfig('SAP_API_HOST');
    $this->logs_json = is_true(getConfig('SAP_LOG_JSON'));
    $this->test = is_true(getConfig('SAP_API_TEST'));
    $this->token = getConfig('SAP_API_CREDENTIAL');
    $this->ci->load->model('rest/V1/sap_api_logs_model');
  }


  public function renewToken()
  {
    $sc = TRUE;
    $this->type = "TOKEN";
    $action = "get";
    $username = getConfig('SAP_API_USERNAME');
    $pwd = getConfig('SAP_API_PWD');
    $api_path = $this->endpoint."/token";
    $method = 'POST';
    $req_start = now();
    $req_end = now();

    if( ! empty($username) && ! empty($pwd) && ! empty($this->endpoint))
    {
      $postfields = "grant_type=password&username={$username}&password={$pwd}";

      if($this->test)
      {
        $action = "test";
      }
      else
      {
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $api_path,
          CURLOPT_RETURNTRANSFER => TRUE,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => TRUE,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => $method,
          CURLOPT_POSTFIELDS => $postfields,
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded'
          )
        ));

        $response = curl_exec($curl);

        if($response === FALSE)
        {
          $response = curl_error($curl);
        }

        curl_close($curl);

        $req_end = now();

        $res = json_decode($response);

        if( ! empty($res) && ! empty($res->access_token))
        {
          if( ! setConfig('SAP_API_CREDENTIAL', $res->access_token))
          {
            $sc = FALSE;
            $this->error = "Cannot replace current access credential";
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "Error : {$response}";
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Missing required parater";
    }

    if($this->logs_json)
    {
      $json = empty($postfields) ? NULL : $postfields;

      $logs = array(
        'trans_id' => genUid(),
        'type' => $this->type,
        'api_path' => $api_path,
        'code' => NULL,
        'action' => $action,
        'status' => $this->test ? 'test' : ($sc === TRUE ? 'success' : 'failed'),
        'message' => NULL,
        'req_start' => $req_start,
        'req_end' => $req_end,
        'request_json' => $json,
        'response_json' => $this->test ? NULL : (empty($response) ? NULL : $response)
      );

      $this->ci->sap_api_logs_model->add_logs($logs);
    }

    return $sc;
  }


  public function exportGRPO($code)
	{
    $sc = TRUE;
    $this->type = "GRPO";
    $action = "create";
    $api_path = $this->endpoint."/api/grpo";
    $headers = array("Content-Type:application/json","Authorization:Bearer {$this->token}");
    $method = 'POST';

    $req_start = now();
    $req_end = now();

    $this->ci->load->model('inventory/receive_material_model');
    $doc = $this->ci->receive_material_model->get($code);

    if( ! empty($doc))
    {
      if($doc->status === 'C')
      {
        $details = $this->ci->receive_material_model->get_details($code);

        if( ! empty($details))
        {
          foreach($details as $rs)
          {
            $rs->batchRows = $this->ci->receive_material_model->get_batch_item_by_id($rs->id);
          }
        }
        else
        {
          $sc = FALSE;
          $this->error = "No item found";
        }

        if($sc === TRUE)
        {
          $req = array(
            'BaseRef' => $doc->po_code,
            'CardCode' => $doc->vendor_code,
            'DocDate' => sap_date($doc->date_add, FALSE),
            'DocDueDate' => sap_date($doc->shipped_date, FALSE),
            'DocCur' => $doc->Currency,
            'NumAtCard' => $doc->invoice_code,
            'U_ECOMNO' => $doc->code,
            'GRPOdetails' => []
          );

          if( ! empty($details))
          {
            foreach($details as $rs)
            {
              $row = array(
                'BaseLine' => $rs->baseLine,
                'ItemCode' => $rs->ItemCode,
                'Quantity' => floatval($rs->Qty),
                'PriceBefDi' => floatval($rs->PriceBefDi),
                'WhsCode' => $rs->WhsCode,
                'FisrtBin' => $rs->BinCode,
                'GRPOBatchs' => []
              );

              if( ! empty($rs->batchRows))
              {
                foreach($rs->batchRows as $br)
                {
                  $row['GRPOBatchs'][] = array(
                    'BatchNum' => $br->BatchNum,
                    'BatchQuantity' => floatval($br->Qty),
                    'BatchAttribute1' => $br->BatchAttr1,
                    'BatchAttribute2' => $br->BatchAttr2
                  );
                }
              }

              $req['GRPOdetails'][] = $row;
            }
          }

          $json = json_encode($req);

          if($this->test)
          {
            if($this->logs_json)
            {
              $logs = array(
                'trans_id' => genUid(),
                'type' => $this->type,
                'api_path' => $api_path,
                'code' => $code,
                'action' => "test",
                'status' => "success",
                'message' => NULL,
                'req_start' => $req_start,
                'req_end' => $req_end,
                'request_json' => $json,
                'response_json' => NULL
              );

              $this->ci->sap_api_logs_model->add_logs($logs);
            }
          }
          else
          {
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => $api_path,
              CURLOPT_RETURNTRANSFER => TRUE,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => TRUE,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => $method,
              CURLOPT_POSTFIELDS => $json,
              CURLOPT_HTTPHEADER => $headers
            ));

            $req_start = now();
            $response = curl_exec($curl);
            curl_close($curl);
            $req_end = now();
            $res = json_decode($response);

            if( ! empty($res) && ! empty($res->Code))
            {
              if($res->Code == 200 && ! empty($res->DocNum))
              {
                $arr = array(
                  'is_export' => 'Y',
                  'inv_code' => $res->DocNum
                );

                $this->ci->receive_material_model->update($code, $arr);
              }
              else
              {
                $sc = FALSE;
                $this->error = "Error : {$res->Message}";

                if(empty($doc->inv_code))
                {
                  $arr = array(
                    'is_export' => 'E'
                  );

                  $this->ci->receive_material_model->update($code, $arr);
                }
              }
            }
            else
            {
              $sc = FALSE;
              $this->error = "No response data";
            }

            if($this->logs_json)
            {
              $logs = array(
                'trans_id' => genUid(),
                'type' => $this->type,
                'api_path' => $api_path,
                'code' => $code,
                'action' => $action,
                'status' => $sc === TRUE ? 'success' : 'failed',
                'message' => $sc === TRUE ? 'success' : $this->error,
                'req_start' => $req_start,
                'req_end' => $req_end,
                'request_json' => $json,
                'response_json' => $response
              );

              $this->ci->sap_api_logs_model->add_logs($logs);
            }
          }
        } //-- $sc == TRUE
      }
      else
      {
        $sc = FALSE;
        $this->error = "Invalid document status";
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "Invalid document number";
    }

		return $sc;
	}

} //-- end class

 ?>

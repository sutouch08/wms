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
        if($this->ci->receive_material_model->is_exists_in_sap($code))
        {
          $sc = FALSE;
          $this->error = "เอกสารนี้เข้าระบบ SAP แล้ว หากต้องการแก้ไข กรุณายกเลิกเอกสารบน SAP ก่อน";
        }

        if($sc === TRUE)
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


  //--- Production order
  public function exportProductionOrder($code)
	{
    $sc = TRUE;
    $this->type = "PDOR";
    $action = "create";
    $api_path = $this->endpoint."/api/Production";
    $headers = array("Content-Type:application/json","Authorization:Bearer {$this->token}");
    $method = 'POST';

    $req_start = now();
    $req_end = now();

    $this->ci->load->model('productions/production_order_model');

    $doc = $this->ci->production_order_model->get($code);

    if( ! empty($doc))
    {
      if($doc->Status === 'R' OR $doc->Status == 'C')
      {
        if($this->ci->production_order_model->is_exists_in_sap($code))
        {
          $sc = FALSE;
          $this->error = "เอกสารนี้เข้าระบบ SAP แล้ว หากต้องการแก้ไข กรุณายกเลิกเอกสารบน SAP ก่อน";
        }

        if($sc === TRUE)
        {
          $details = $this->ci->production_order_model->get_details($code);

          if(empty($details))
          {
            $sc = FALSE;
            $this->error = "No item found";
          }
        }

        if($sc === TRUE)
        {
          $req = array(
            'U_ECOMNO' => $doc->code,
            'OrderDate' => sap_date($doc->PostDate, FALSE),
            'StartDate' => sap_date($doc->ReleaseDate, FALSE),
            'DueDate' => sap_date($doc->DueDate, FALSE),
            'ProductNo' => $doc->ItemCode,
            'PlannedQuantity' => floatval($doc->PlannedQty),
            'Status' => 'R', //--- P = Planned, R = Released, L = Closed, C = Canceled
            'Comments' => get_null($doc->Comments),
            'Warehouse' => $doc->Warehouse,
            'Priority' => 1,
            'Productiondetails' => []
          );

          if( ! empty($details))
          {
            foreach($details as $rs)
            {
              $req['Productiondetails'][] = array(
                'ItemCode' => $rs->ItemCode,
                'Quantity' => floatval($rs->BaseQty),
                'PlannedQuantity' => floatval($rs->PlannedQty),
                'Warehouse' => $rs->WhsCode
              );
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

            if( ! empty($res))
            {
              if( ! empty($res->Code))
              {
                if($res->Code == 200 && ! empty($res->DocNum))
                {
                  $arr = array(
                    'is_exported' => 'Y',
                    'inv_code' => $res->DocNum
                  );

                  $this->ci->production_order_model->update($code, $arr);
                }
                else
                {
                  $sc = FALSE;
                  $this->error = "Error : {$res->Message}";

                  if(empty($doc->inv_code))
                  {
                    $arr = array(
                      'is_exported' => 'E'
                    );

                    $this->ci->production_order_model->update($code, $arr);
                  }
                }
              }
              else
              {
                if( ! empty($res->Message))
                {
                  $sc = FALSE;
                  $this->error = "Error : {$res->Message}";

                  if(empty($doc->inv_code))
                  {
                    $arr = array(
                      'is_exported' => 'E'
                    );

                    $this->ci->production_order_model->update($code, $arr);
                  }
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


  //--- Cancel Production order
  public function cancelProductionOrder($code)
	{
    $sc = TRUE;
    $this->type = "PDOR";
    $action = "cancel";
    $api_path = $this->endpoint."/api/Production/Cancel";
    $headers = array("Content-Type:application/json","Authorization:Bearer {$this->token}");
    $method = 'POST';

    $req_start = now();
    $req_end = now();

    $this->ci->load->model('productions/production_order_model');

    $doc = $this->ci->production_order_model->get($code);

    if( ! empty($doc))
    {
      if($doc->Status === 'R' OR $doc->Status == 'C')
      {
        $req = array('U_ECOMNO' => $doc->code);

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
            if($res->Code != 200)
            {
              $sc = FALSE;
              $this->error = "Error : {$res->Message}";
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


  public function closeProductionOrder($code)
	{
    $sc = TRUE;
    $this->type = "PDOR";
    $action = "update";
    $api_path = $this->endpoint."/api/Production/Close";
    $headers = array("Content-Type:application/json","Authorization:Bearer {$this->token}");
    $method = 'POST';

    $req_start = now();
    $req_end = now();

    $this->ci->load->model('productions/production_order_model');

    $doc = $this->ci->production_order_model->get($code);

    if( ! empty($doc))
    {
      if($doc->Status === 'R')
      {
        $req = array('U_ECOMNO' => $doc->code);

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
            if($res->Code != 200)
            {
              $sc = FALSE;
              $this->error = "Error : {$res->Message}";
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


  //--- Production Transfer
  public function exportProductionTransfer($code)
	{
    $sc = TRUE;
    $this->type = "PDTR";
    $action = "create";
    $api_path = $this->endpoint."/api/transfer";
    $headers = array("Content-Type:application/json","Authorization:Bearer {$this->token}");
    $method = 'POST';

    $req_start = now();
    $req_end = now();

    $this->ci->load->model('productions/production_transfer_model');

    $doc = $this->ci->production_transfer_model->get($code);

    if( ! empty($doc))
    {
      if($doc->Status == 'C')
      {
        if($this->ci->production_transfer_model->is_exists_in_sap($code))
        {
          $sc = FALSE;
          $this->error = "เอกสารนี้เข้าระบบ SAP แล้ว หากต้องการแก้ไข กรุณายกเลิกเอกสารบน SAP ก่อน";
        }

        if($sc === TRUE)
        {
          $details = $this->ci->production_transfer_model->get_details($code);

          if( ! empty($details))
          {
            foreach($details as $rs)
            {
              $rs->batchRows = $this->ci->production_transfer_model->get_batch_rows($rs->id);
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "No item found";
          }
        }

        if($sc === TRUE)
        {
					$date_add = getConfig('ORDER_SOLD_DATE') == 'D' ? $doc->date_add : (empty($doc->shipped_date) ? now() : $doc->shipped_date);

          $req = array(
            'DocDate' => sap_date($date_add, TRUE),
            'Comments' => limitText($doc->remark, 250),
            'U_ECOMNO' => $doc->code,
            'CardCode' => $doc->CardCode,
            'CardName' => $doc->CardName,
            'FromWarehouse' => $doc->fromWhsCode,
            'ToWarehouse' => $doc->toWhsCode,
            'ToBinCode' => $doc->toBinCode,
            'Transferdetails' => []
          );

          if( ! empty($details))
          {
            foreach($details as $rs)
            {
              $row = array(
                'ItemCode' => $rs->ItemCode,
                'Quantity' => floatval($rs->Qty),
                'FromWarehouse' => $rs->fromWhsCode,
                'ToWarehouse' => $rs->toWhsCode
              );

              if(empty($rs->batchRows))
              {
                $row['F_FROM_BIN'] = $rs->fromBinCode;
                $row['F_TO_BIN'] = $rs->toBinCode;
              }

              if( ! empty($rs->batchRows))
              {
                foreach($rs->batchRows as $br)
                {
                  $row['TransferBatchs'][] = array(
                    'BatchNum' => $br->BatchNum,
                    'BatchQuantity' => floatval($br->Qty),
                    'BatchAttribute1' => $br->BatchAttr1,
                    'BatchAttribute2' => $br->BatchAttr2,
                    'FromWarehouse' => $br->fromWhsCode,
                    'ToWarehouse' => $br->toWhsCode,
                    'F_FROM_BIN' => $br->fromBinCode,
                    'F_TO_BIN' => $br->toBinCode
                  );
                }
              }

              $req['Transferdetails'][] = $row;
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
                  'is_exported' => 'Y',
                  'inv_code' => $res->DocNum
                );

                $this->ci->production_transfer_model->update($code, $arr);
              }
              else
              {
                $sc = FALSE;
                $this->error = "Error : {$res->Message}";

                if(empty($doc->inv_code))
                {
                  $arr = array(
                    'is_exported' => 'E'
                  );

                  $this->ci->production_transfer_model->update($code, $arr);
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


  //--- Production Issue
  public function exportProductionIssue($code)
  {
    $sc = TRUE;
    $this->type = "PDGI";
    $action = "create";
    $api_path = $this->endpoint."/api/Production/Issue";
    $headers = array("Content-Type:application/json","Authorization:Bearer {$this->token}");
    $method = 'POST';

    $req_start = now();
    $req_end = now();

    $this->ci->load->model('productions/production_issue_model');

    $doc = $this->ci->production_issue_model->get($code);

    if( ! empty($doc))
    {
      if($doc->Status == 'C')
      {
        if($this->ci->production_issue_model->is_exists_in_sap($code))
        {
          $sc = FALSE;
          $this->error = "เอกสารนี้เข้าระบบ SAP แล้ว หากต้องการแก้ไข กรุณายกเลิกเอกสารบน SAP ก่อน";
        }

        if($sc === TRUE)
        {
          $details = $this->ci->production_issue_model->get_details($code);

          if( ! empty($details))
          {
            foreach($details as $rs)
            {
              $rs->batchRows = $this->ci->production_issue_model->get_batch_rows($rs->id);
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "No item found";
          }
        }

        if($sc === TRUE)
        {
          $date_add = getConfig('ORDER_SOLD_DATE') == 'D' ? $doc->date_add : (empty($doc->shipped_date) ? now() : $doc->shipped_date);

          $req = array(
            'U_ECOMNO' => $doc->code,
            'DocDate' => sap_date($date_add, TRUE),
            'Comments' => limitText($doc->remark, 250),
            'ProductionIssueReceiptDetails' => []
          );

          if( ! empty($details))
          {
            foreach($details as $rs)
            {
              $row = array(
                'ItemCode' => $rs->ItemCode,
                'Quantity' => floatval($rs->Qty),
                'Warehouse' => get_null($rs->WhsCode),
                'FisrtBin' => get_null($rs->BinCode),
                'BaseType' => $rs->BaseType,
                'BaseEntry' => intval($rs->BaseEntry),
                'BaseLine' => intval($rs->BaseLine),
                'ProductionIssueReceiptBatchs' => []
              );

              if( ! empty($rs->batchRows))
              {
                foreach($rs->batchRows as $br)
                {
                  $row['ProductionIssueReceiptBatchs'][] = array(
                    'BatchNum' => $br->BatchNum,
                    'BatchQuantity' => floatval($br->Qty),
                    'BatchAttribute1' => $br->BatchAttr1,
                    'BatchAttribute2' => $br->BatchAttr2,
                    'Warehouse' => $br->WhsCode,
                    'FisrtBin' => $br->BinCode
                  );
                }
              }

              $req['ProductionIssueReceiptDetails'][] = $row;
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
                  'is_exported' => 'Y',
                  'inv_code' => $res->DocNum
                );

                $this->ci->production_issue_model->update($code, $arr);
              }
              else
              {
                $sc = FALSE;
                $this->error = "Error : {$res->Message}";

                if(empty($doc->inv_code))
                {
                  $arr = array(
                    'is_exported' => 'E'
                  );

                  $this->ci->production_issue_model->update($code, $arr);
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


  //--- Production Issue
  public function exportProductionReceipt($code)
  {
    $sc = TRUE;
    $this->type = "PDGR";
    $action = "create";
    $api_path = $this->endpoint."/api/Production/Receipt";
    $headers = array("Content-Type:application/json","Authorization:Bearer {$this->token}");
    $method = 'POST';

    $req_start = now();
    $req_end = now();

    $this->ci->load->model('productions/production_receipt_model');

    $doc = $this->ci->production_receipt_model->get($code);

    if( ! empty($doc))
    {
      if($doc->Status == 'C')
      {
        if($this->ci->production_receipt_model->is_exists_in_sap($code))
        {
          $sc = FALSE;
          $this->error = "เอกสารนี้เข้าระบบ SAP แล้ว หากต้องการแก้ไข กรุณายกเลิกเอกสารบน SAP ก่อน";
        }

        if($sc === TRUE)
        {
          $details = $this->ci->production_receipt_model->get_details($code);

          if( ! empty($details))
          {
            foreach($details as $rs)
            {
              $rs->batchRows = $this->ci->production_receipt_model->get_batch_rows($rs->id);
            }
          }
          else
          {
            $sc = FALSE;
            $this->error = "No item found";
          }
        }

        if($sc === TRUE)
        {
          $date_add = getConfig('ORDER_SOLD_DATE') == 'D' ? $doc->date_add : (empty($doc->shipped_date) ? now() : $doc->shipped_date);

          $req = array(
            'DocDate' => sap_date($date_add, TRUE),
            'Comments' => limitText($doc->remark, 250),
            'U_ECOMNO' => $doc->code,
            'ProductionIssueReceiptDetails' => []
          );

          if( ! empty($details))
          {
            foreach($details as $rs)
            {
              $row = array(
                'ItemCode' => $rs->ItemCode,
                'Quantity' => floatval($rs->Qty),
                'Warehouse' => get_null($rs->WhsCode),
                'FisrtBin' => get_null($rs->BinCode),
                'BaseType' => $rs->BaseType,
                'BaseRef' => intval($rs->BaseRef),
                'BaseEntry' => intval($rs->BaseEntry),
                'BaseLine' => NULL,
                'TranType' => $rs->TranType,
                'ProductionIssueReceiptBatchs' => []
              );

              if( ! empty($rs->batchRows))
              {
                foreach($rs->batchRows as $br)
                {
                  $row['ProductionIssueReceiptBatchs'][] = array(
                    'BatchNum' => $br->BatchNum,
                    'BatchQuantity' => floatval($br->Qty),
                    'BatchAttribute1' => $br->BatchAttr1,
                    'BatchAttribute2' => $br->BatchAttr2,
                    'Warehouse' => $br->WhsCode,
                    'FisrtBin' => $br->BinCode
                  );
                }
              }

              $req['ProductionIssueReceiptDetails'][] = $row;
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
                  'is_exported' => 'Y',
                  'inv_code' => $res->DocNum
                );

                $this->ci->production_receipt_model->update($code, $arr);
              }
              else
              {
                $sc = FALSE;
                $this->error = "Error : {$res->Message}";

                if(empty($doc->inv_code))
                {
                  $arr = array(
                    'is_exported' => 'E'
                  );

                  $this->ci->production_receipt_model->update($code, $arr);
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

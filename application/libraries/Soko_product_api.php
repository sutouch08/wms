<?php
class Soko_product_api
{
  private $url;
  public $home;
	public $wms;
	protected $ci;
  public $error;
	public $log_json;

  public function __construct()
  {
		$this->ci =& get_instance();
		$this->ci->load->model('rest/V1/soko_api_logs_model');
    $this->ci->load->model('masters/products_model');
		$this->url = getConfig('SOKOJUNG_API_HOST');
    $this->key = getConfig('SOKOJUNG_API_CREDENTIAL');
		$this->log_json = is_true(getConfig('SOKOJUNG_LOG_JSON'));
  }


	//---- create item
  public function create_item($item_code, $item = NULL)
  {
    $sc = TRUE;

    if( ! empty($item)) //--- sku object
    {
      $ds = array(
        'sku' => $item->code,
        'number' => $item->old_code,
        'barcode' => $item->barcode,
        'name' => $item->name,
        'item_color' => $item->color_code,
        'item_size' => $item->size_code,
        'width' => "",
        'height' => "",
        'length' => "",
        'weight' => "",
        'cost' => 0.00,
        'selling_price' => round($item->price, 2),
        'description' => "",
        'ean' => ""
      );

      $api_path = $this->url."products";
      $url = $api_path;
			$method = "POST";

			$headers = array(
				"Content-Type: application/json",
        "Authorization: Basic {$this->key}"
			);

      $json = json_encode($ds);

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
            if(empty($res->item_code))
            {
              $sc = FALSE;
              $this->error = $res->message;
              $res->status = 'failed';
            }
            else
            {
              $res->status = 'success';
              $this->ci->products_model->update($item->code, ['soko_code' => $res->item_code]);
            }
          }
          else
          {
            if($res->status != 'success' && empty($res->item_code))
            {
              $sc = FALSE;
              $this->error = $res->message;
            }
            else
            {
              $this->ci->products_model->update($item->code, ['soko_code' => $res->item_code]);
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
            'type' => 'products',
            'api_path' => $api_path,
            'code' => $item->code,
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
            'type' => 'products',
            'api_path' => $api_path,
            'code' => $item->code,
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
      $sc = FALSE;
      $this->error = "No data";
    }

    if($sc === TRUE)
		{
			$this->ci->soko_api_logs_model->add($item_code, 'S', NULL);
		}
		else
		{
			$this->ci->soko_api_logs_model->add($item_code, 'E', $this->error);
		}

    return $sc;
  }


  //---- update item
  public function update_item($item_code, $item = NULL)
  {
    $sc = TRUE;

    if( ! empty($item)) //--- sku object
    {
      $ds = array(
        'sku' => $item->code,
        'number' => $item->old_code,
        'barcode' => $item->barcode,
        'name' => $item->name,
        'item_color' => $item->color_code,
        'item_size' => $item->size_code,
        'width' => "",
        'height' => "",
        'length' => "",
        'weight' => "",
        'cost' => 0.00,
        'selling_price' => round($item->price, 2),
        'description' => "",
        'ean' => ""
      );

      $api_path = $this->url."products/".$item->soko_code;
      $url = $api_path;

      $method = "PUT";

      $headers = array(
        "Content-Type: application/json",
        "Authorization: Basic {$this->key}"
      );

      $json = json_encode($ds);

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
          if($res->status != 'success')
          {
            $sc = FALSE;
            $this->error = $res->message;
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
            'type' => 'products',
            'api_path' => $api_path,
            'code' => $item->code,
            'action' => 'update',
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

        if($thsi->log_json)
        {
          $logs = array(
            'trans_id' => genUid(),
            'type' => 'products',
            'api_path' => $api_path,
            'code' => $item->code,
            'action' => 'update',
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
      $sc = FALSE;
      $this->error = "No data";
    }

    if($sc === TRUE)
		{
			$this->ci->soko_api_logs_model->add($item_code, 'S', NULL);
		}
		else
		{
			$this->ci->soko_api_logs_model->add($item_code, 'E', $this->error);
		}

    return $sc;
  }

} //--- end class
?>

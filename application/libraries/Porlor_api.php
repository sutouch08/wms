<?php
class Porlor_api
{
  private $url;
	protected $ci;
  protected $api;
  public $error;
	public $log_json;
  public $test = FALSE;
  public $type = "PORLOR";
  public $endpoint;
  public $customerCode;
  public $customerName;
  public $customerAddress;
  public $customerPhone;
  public $customerProvince;

  public function __construct()
  {
    $this->ci =& get_instance();
		$this->ci->load->model('rest/V1/ix_api_logs_model');
    $this->ci->load->model('orders/orders_model');
    $this->ci->load->model('inventory/qc_model');

    $conf = getPorlorApiConfig();

    $this->api = is_true($conf['PORLOR_API']);
    $this->endpoint = $conf['PORLOR_API_ENDPOINT'];
    $this->test = is_true($conf['PORLOR_API_TEST']);
    $this->log_json = is_true($conf['PORLOR_LOG_JSON']);
    $this->customerCode = $conf['PORLOR_CUSTOMER_CODE'];
    $this->customerName = $conf['PORLOR_CUSTOMER_NAME'];
    $this->customerAddress = $conf['PORLOR_CUSTOMER_ADDRESS'];
    $this->customerPhone = $conf['PORLOR_CUSTOMER_PHONE'];
    $this->customerProvince = $conf["PORLOR_CUSTOMER_PROVINCE"];
  }


  public function create_shipment($code, $packages)
  {
    $action = "create";
    $url = $this->endpoint;
    $url .= "/saveParcels";
    $api_path = $url;

    $headers = array("Content-Type:application/json");
    $apiUrl = str_replace(" ","%20",$url);
    $method = 'POST';

    $req = array(
      "sender" => array(
        "amphoe" => "",
        "amphur_shop" => "",
        "ref_no" => $code,
        "citizenID" => "",
        "custCode" => $this->customerCode,
        "customerGroup" => "",
        "district" => "",
        "fullName" => $this->customerName,
        "homeNumber" => $this->customerAddress,
        "phoneNumber" => $this->customerPhone,
        "province" => $this->customerProvince,
        "province_shop" => "",
        "typeSender" => "",
        "zipcode" => ""
      ),
      "recipient" => NULL
    );

    if( ! empty($packages))
    {
      $recipient = [];

      foreach($packages as $rs)
      {
        $recipient[] = array(
          "item_desc" => $rs->order_code,
          "item_sku" => $rs->box_code,
          "amphoe" => $rs->district,
          "bankName" => "",
          "deposit_fullname" => "",
          "deposit_phone" => "",
          "deposit_type" => "",
          "district" => $rs->sub_district,
          "fullName" => $rs->receiver,
          "homeNumber" => $rs->address,
          "materialAccountName" => "",
          "materialAccountNumber" => "",
          "materialCode" => FALSE,
          "materialPriceCode" => 0,
          "materialSize" => $rs->package_size,
          "materialSizeHigh" => $rs->package_height,
          "materialSizeLong" => $rs->package_length,
          "materialSizeWide" => $rs->package_width,
          "materialWeight" => 1,
          "phoneNumber" => $rs->phone,
          "province" => $rs->province,
          "serviceCod" => "",
          "total" => "",
          "totalNet" => "",
          "zipcode" => $rs->postcode
        );
      }

      $req['recipient'] = $recipient;
    }


    $json = json_encode($req);

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
        if($res->status == 1)
        {
          if($this->log_json)
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
          $sc = FALSE;
          $this->error = empty($res->message) ? $res->message : "Unknow error";

          if($this->log_json)
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
      }
    }
    else
    {
      $logs = array(
        'trans_id' => genUid(),
        'api_path' => $api_path,
        'type' => $this->type,
        'code' => $code,
        'action' => $action,
        'status' => 'test',
        'message' => $this->error,
        'request_json' => $json,
        'response_json' => NULL
      );

      $this->ci->ix_api_logs_model->add_logs($logs);
    }

    return FALSE;
  }

}
//--- end class
 ?>

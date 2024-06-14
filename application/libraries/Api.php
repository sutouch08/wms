<?php
class Api
{
  private $web_url;
  private $token;
  protected $ci;

  public function __construct()
  {
    $this->token = getConfig('WEB_API_TOKEN');
    $this->web_url = getConfig('WEB_API_HOST');
  }

  public function create_shipment($code, array $ds = array())
  {
    if( ! empty($ds))
    {
      $token = $this->token;
      $url = "https://bof.warrix.co.th/rest/V1/eol/order/{$code}/ship";

      $setHeaders = array("Content-Type:application/json","Authorization:Bearer {$token}");
	    $apiUrl = str_replace(" ","%20",$url);
	    $method = 'POST';

      $data_string = json_encode($ds);

      $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $apiUrl);
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $setHeaders);
	    $result = curl_exec($ch);
	    curl_close($ch);

      return $result;
    }

    return FALSE;
  }


  public function update_web_stock(array $ds = array())
  {
		if(!empty($ds))
		{

			$token = $this->token;
	    $url = $this->web_url."products/{$item}/stockItems/1";

	    $setHeaders = array("Content-Type:application/json","Authorization:Bearer {$token}");
	    $apiUrl = str_replace(" ","%20",$url);
	    $method = 'PUT';

	    $data_string = json_encode($ds);
	    //echo $data_string;
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $apiUrl);
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $setHeaders);
	    $result = curl_exec($ch);
	    curl_close($ch);
	    return $result;
		}

  }


  public function update_order_status($order_id, $current_state, $status)
  {
    $token = $this->token;
    $url = $this->web_url."mi/order/{$order_id}/status";
    $setHeaders = array("Content-Type:application/json","Authorization:Bearer {$token}");
    $apiUrl = str_replace(" ","%20",$url);
    $method = 'PUT';

    //---- ไม่สามารถย้อนสถานะได้ เดินหน้าได้อย่างเดียว
    if($status > $current_state)
    {
      //---- status name
      $state = array(
        '4' => 'Picking',
        '6' => 'Packing',
        '7' => 'Shipping',
        '8' => 'Complete',
        '9' => 'Cancel'
      );

      if( isset($state[$status]))
      {
        $data = array(
          "status" => $state[$status]
        );


        $data_string = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $setHeaders);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
      }

    }

    return TRUE;

  }


} //-- end class

 ?>

<?php
class Wms_order_api extends CI_Controller
{

  private $url = "http://plcig.pioneer.co.th:8099/plcwhapi_uat/wob";
  private $WH_NO = "PLCWH-01"; //--- Wharehouse no from WMS
	private $CUS_CODE = "PLC-00207"; //---- Customer No from WMS
	private $ORDER_LIST_NO = "";  //---- will be generate
  public $home;
	public $wms;

  public function __construct()
  {
    parent::__construct();
		$this->wms = $this->load->database('wms', TRUE);

		$this->load->model('orders/orders_model');
		$this->load->model('address/address_model');
		$this->load->model('rest/V1/wms_error_logs_model');
    $this->home = base_url().'api/wms/wms_order_api';
  }

  public function index()
  {
    //$this->load->view('auto/sales_report_api');
  }


	public function resend()
	{
		//$this->load->view('auto/resend_error');
	}



  public function do_export()
  {
		$code = $this->input->post('code');
		$sc = TRUE;

		$role_type_list = array(
			'S' => 'ORDER', //--- check channels type_code
			'P' => 'WS',
			'U' => 'WU',
			'C' => 'WC',
			'N' => 'WT',
			'Q' => 'WQ',
			'T' => 'WV',
			'L' => 'WL'
		);

		$xml = "";


    $order = $this->orders_model->get($code);

		if(!empty($order))
		{
			if(empty($order->id_address))
			{
				$sc = FALSE;
				$this->error = "No Shipping Address";
			}
			else
			{
				$addr = $this->address_model->get_shipping_detail($order->id_address);
				if(!empty($addr))
				{
					$address1 = $addr->address;
					$address2 = $addr->sub_district.' '.$addr->district.' '.$addr->province.' '.$addr->postcode;
				}
				else
				{
					$sc = FALSE;
					$this->error = "No Shipping Address";
				}
			}

			if($sc === TRUE)
			{
				$details = $this->orders_model->get_only_count_stock_details($code);
				$order_type = $order->role === 'S' ? $this->get_type_code($order->channels_code) : $role_type_list[$order->role];
				if(!empty($details))
				{
					$xml .= "<WOB>";

					//--- Header_list section
					$xml .= "<HEADER_LIST>";
					$xml .=   "<WH_NO>".$this->WH_NO."</WH_NO>";
					$xml .=   "<CUST_CODE>".$this->CUS_CODE."</CUST_CODE>";
					$xml .=   "<ORDER_LIST_NO>".$order->code."</ORDER_LIST_NO>";
					$xml .= "</HEADER_LIST>";
					//---- End header_list section

					//--- Header section
					$xml .= "<ORDER_LIST>";

						//--- Order Start
						$xml .= "<ORDER>";
						$xml .=  "<HEADER>";
						$xml .=   "<ORDER_NO>".$order->code."</ORDER_NO>";
						$xml .=   "<ORDER_TYPE>".$order_type."</ORDER_TYPE>";
						$xml .=   "<SHIPMENT_DATE>".date('Y/m/d', strtotime($order->date_add))."</SHIPMENT_DATE>";
						$xml .=   "<SHIP_TO_CODE>".(!empty($addr) ? $addr->address_code : "")."</SHIP_TO_CODE>";
						$xml .=   "<SHIP_TO_NAME>".(!empty($addr) ? $addr->name : "")."</SHIP_TO_NAME>";
						$xml .=   "<SHIP_TO_ADDRESS1>".(!empty($addr) ? $address1 : "")."</SHIP_TO_ADDRESS1>";
						$xml .=   "<SHIP_TO_ADDRESS2>".(!empty($addr) ? $address2 : "")."</SHIP_TO_ADDRESS2>";
						$xml .=   "<REF_NO1>".$order->reference."</REF_NO1>";
						$xml .=   "<REF_NO2></REF_NO2>";
						$xml .=   "<REMARK>".$order->remark."</REMARK>";
						$xml .=  "</HEADER>";

						//--- Item start
						$xml .= "<ITEMS>";

						foreach($details as $rs)
						{
							if($rs->is_count)
							{
								$xml .= "<ITEM>";
							  $xml .= "<ITEM_NO>".$rs->product_code."</ITEM_NO>";
								$xml .= "<ITEM_DESC>".$rs->product_name."</ITEM_DESC>";
								$xml .= "<VARIANT></VARIANT>";
								$xml .= "<LOT_NO></LOT_NO>";
								$xml .= "<SERIAL_NO></SERIAL_NO>";
								$xml .= "<QUANTITY>".round($rs->qty,2)."</QUANTITY>";
								$xml .= "<UOM>".$rs->unit_code."</UOM>";
								$xml .= "</ITEM>";
							}

						}

						$xml .= "</ITEMS>";
					$xml .= "</ORDER>";
					$xml .= "</ORDER_LIST>";
					//--- End header section
					$xml .= "</WOB>";
				}
				else
				{
					$sc = FALSE;
					$this->error = "No item in this order";
				}
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Invalid Order Code";
		}


    if($sc === TRUE && !empty($xml))
    {
      $ch = curl_init();

      curl_setopt($ch, CURLOPT_URL, $this->url);
      curl_setopt($ch, CURLOPT_POST, TRUE);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));

      $response = curl_exec($ch);

      curl_close($ch);


      $res = json_decode(json_encode(simplexml_load_string($response)));


			if(!empty($res))
			{

				if($res->SERVICE_RESULT->RESULT_STAUS != 'SUCCESS')
				{
					$sc = FALSE;
					$this->error = $res->SERVICE_RESULT->ERROR_CODE.' : '.$res->SERVICE_RESULT->ERROR_MESSAGE;
					$this->wms_error_logs_model->add($order->code, 'E', 'Error-'.$res->SERVICE_RESULT->ERROR_CODE.' : '.$res->SERVICE_RESULT->ERROR_MESSAGE);
				}
			}
			else
			{
				$this->wms_error_logs_model->add($order->code, 'S', 'No response');
			}
    }


		if($sc === TRUE)
		{
			$this->wms_error_logs_model->add($order->code, 'S', NULL);
		}
		else
		{
			$this->wms_error_logs_model->add($code, 'E', $this->error);
		}

		echo $sc === TRUE ? 'success' : $this->error;
  }




	public function get_type_code($channels_code)
	{
		$this->load->model('masters/channels_model');

		$channels = $this->channels_model->get($channels_code);
		if(!empty($channels))
		{
			return $channels->type_code;
		}

		return NULL;
	}


	public function test()
	{
		$xml = simplexml_load_string(file_get_contents("php://input"));
		$res = json_decode(json_encode($xml));

		echo 'Error-'.$res->SERVICE_RESULT->ERROR_CODE.' : '.$res->SERVICE_RESULT->ERROR_MESSAGE;
	}

}
?>

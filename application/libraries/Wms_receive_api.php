<?php
class Wms_receive_api
{

  private $url = "http://plcig.pioneer.co.th:8099/plcwhapi_uat/wib";
  private $WH_NO = "PLCWH-01"; //--- Wharehouse no from WMS
	private $CUS_CODE = "PLC-00207"; //---- Customer No from WMS
	private $ORDER_LIST_NO = "";  //---- will be generate
  public $home;
	public $wms;
	protected $ci;
  public $error;

  public function __construct()
  {
		
  }


	//---- export receive transform
  public function export_receive_transform($doc, $order_code, $invoice, $details)
  {
		$sc = TRUE;
		$order_type = "RT";
		$xml = "";


    if(!empty($details))
		{
			$xml .= "<WIB>";

			//--- Header_list section
			$xml .= "<HEADER>";
			$xml .=   "<WH_NO>".$this->WH_NO."</WH_NO>";
			$xml .=   "<CUST_CODE>".$this->CUS_CODE."</CUST_CODE>";
			$xml .= "</HEADER>";
			//---- End header_list section

			//--- Order Start
			$xml .= "<ORDER>";
			$xml .=   "<ORDER_NO>".$doc->code."</ORDER_NO>";
			$xml .=   "<ORDER_TYPE>".$order_type."</ORDER_TYPE>";
			$xml .=   "<ORDER_DATE>".date('Y/m/d')."</ORDER_DATE>";
			$xml .=   "<SUPPLIER_CODE></SUPPLIER_CODE>";
			$xml .=   "<SUPPLIER_NAME></SUPPLIER_NAME>";
			$xml .=   "<SUPPLIER_ADDRESS1></SUPPLIER_ADDRESS1>";
			$xml .=   "<SUPPLIER_ADDRESS2></SUPPLIER_ADDRESS2>";
			$xml .=   "<REF_NO1>".$order_code."</REF_NO1>";
			$xml .=   "<REF_NO2>".$invoice."</REF_NO2>";
			$xml .=   "<REMARK>".$doc->remark."</REMARK>";
			$xml .= "</ORDER>";
				//--- Item start
			$xml .= "<ITEMS>";

			foreach($details as $rs)
			{

				if($rs->qty > 0)
				{
					$xml .= "<ITEM>";
					$xml .= "<ITEM_NO>".$rs->product_code."</ITEM_NO>";
					$xml .= "<ITEM_DESC>".$rs->product_name."</ITEM_DESC>";
					$xml .= "<VARIANT></VARIANT>";
					$xml .= "<LOT_NO></LOT_NO>";
					$xml .= "<EXP_DATE></EXP_DATE>";
					$xml .= "<SERIAL_NO></SERIAL_NO>";
					$xml .= "<QUANTITY>".round($rs->qty,2)."</QUANTITY>";
					$xml .= "<UOM>".$rs->unit_code."</UOM>";
					$xml .= "</ITEM>";
				}
			}

			$xml .= "</ITEMS>";
			//--- End header section
			$xml .= "</WIB>";


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
					}
				}
	    }
		}
		else
		{
			$this->error = "No data";
		}

		return $sc;
  }

}
?>

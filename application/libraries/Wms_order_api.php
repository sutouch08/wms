<?php
class Wms_order_api
{
  private $url;
  private $WH_NO; //--- Wharehouse no from WMS
	private $CUS_CODE; //---- Customer No from WMS
	public $wms;
	protected $ci;
  public $error;
	public $log_xml;
  public $test;
	public $type = 'OB';

  public function __construct()
  {
		$this->ci =& get_instance();
		$this->ci->load->model('rest/V1/wms_error_logs_model');
		$this->url = getConfig('WMS_OB_URL');
		$this->WH_NO = getConfig('WMS_WH_NO');
		$this->CUS_CODE = getConfig('WMS_CUST_CODE');
		$this->log_xml = getConfig('LOG_XML');
    $this->test = getConfig('WMS_TEST') ? TRUE : FALSE;
  }


	//---- export
	public function export_order($code)
  {
		$this->ci->load->model('orders/orders_model');
		$this->ci->load->model('address/address_model');
    $this->ci->load->model('masters/sender_model');
		$this->ci->load->model('masters/channels_model');
    $this->ci->load->model('masters/payment_methods_model');


		$sc = TRUE;

		$role_type_list = array(
			'S' => 'WO', //--- check channels type_code
			'P' => 'WS',
			'U' => 'WU',
			'C' => 'WC',
			'N' => 'WT',
			'Q' => 'WQ',
			'T' => 'WV',
			'L' => 'WL'
		);

		$xml = "";


    $order = $this->ci->orders_model->get($code);

		if(!empty($order))
		{
			if(empty($order->id_address))
			{
				$sc = FALSE;
				$this->error = "ไม่พบที่อยู่จัดส่ง";
			}
			else
			{
				$addr = $this->ci->address_model->get_shipping_detail($order->id_address);

        if(empty($addr))
				{
					$sc = FALSE;
					$this->error = "ไม่พบที่อยู่จัดส่ง";
				}

				$sender = $this->ci->sender_model->get($order->id_sender);

				if(empty($sender))
				{
					$sc = FALSE;
					$this->error = "ไม่ได้ระบุขนส่ง";
				}
			}

			if($sc === TRUE)
			{
				$details = $this->ci->orders_model->get_only_count_stock_details($code);
				$channels = $order->role === 'S' ? $this->ci->channels_model->get($order->channels_code) : NULL;
				$order_type = !empty($channels) ? $channels->type_code : $role_type_list[$order->role];
				$channels_code = !empty($channels) ? $order->channels_code : $role_type_list[$order->role];
				$channels_name = !empty($channels) ? $channels->name : "";
        $doc_total = $order->doc_total <= 0 ? $this->ci->orders_model->get_order_total_amount($order->code) : $order->doc_total;
				$cod = $order->role === 'S' ? ($order->payment_role == 4 ? 'COD' : 'NON-COD') : 'NON-COD';
        $cod_amount = $cod === 'COD' ? ($order->cod_amount == 0 ? $doc_total : $order->cod_amount) : 0.00;

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
          $spx = $sender->code == "SPX" ? TRUE : FALSE;
          $addr->province = $spx ? parseProvince($addr->province) : $addr->province;
          $addr->sub_district = $spx ? parseSubDistrict($addr->sub_district, $addr->province) : $addr->sub_district;
          $addr->district = $spx ? parseDistrict($addr->district, $addr->province) : $addr->district;
          $addr->phone = $spx ? parsePhoneNumber($addr->phone, 10) : $addr->phone;
					//--- Header section
					$xml .= "<ORDER_LIST>";

						//--- Order Start
						$xml .= "<ORDER>";
						$xml .=  "<HEADER>";
						$xml .=   "<ORDER_NO>".$order->code."</ORDER_NO>";
						$xml .=   "<ORDER_TYPE>".$order_type."</ORDER_TYPE>";
						$xml .=   "<SHIPMENT_DATE>".date('Y/m/d', strtotime($order->date_add))."</SHIPMENT_DATE>";
						$xml .=   "<SHIP_TO_CODE>".(!empty($sender) ? $sender->code : "")."</SHIP_TO_CODE>";
						$xml .=   "<SHIP_TO_NAME><![CDATA[".(!empty($sender) ? $sender->name : "")."]]></SHIP_TO_NAME>";
						$xml .=   "<SHIP_TO_ADDRESS1><![CDATA[".(!empty($sender) ? $sender->address1 : "")."]]></SHIP_TO_ADDRESS1>";
						$xml .=   "<SHIP_TO_ADDRESS2><![CDATA[".(!empty($sender) ? $sender->address2 : "")."]]></SHIP_TO_ADDRESS2>";
            $xml .=   "<RECEIPT_NAME><![CDATA[".(!empty($addr) ? $addr->name : "")."]]></RECEIPT_NAME>";
            $xml .=   "<RECEIPT_MOBILENO>".(!empty($addr) ? $addr->phone : "")."</RECEIPT_MOBILENO>";
            $xml .=   "<RECEIPT_EMAIL>".(!empty($addr) ? $addr->email : "")."</RECEIPT_EMAIL>";
            $xml .=   "<RECEIPT_FULLSHIPPINGADDRESS><![CDATA[".(!empty($addr) ? $addr->address : "")."]]></RECEIPT_FULLSHIPPINGADDRESS>";
            $xml .=   "<RECEIPT_STREET></RECEIPT_STREET>";
            $xml .=   "<RECEIPT_SUBDISTRICT><![CDATA[".(!empty($addr) ? $addr->sub_district : "")."]]></RECEIPT_SUBDISTRICT>";
            $xml .=   "<RECEIPT_DISTRICT><![CDATA[".(!empty($addr) ? $addr->district : "")."]]></RECEIPT_DISTRICT>";
            $xml .=   "<RECEIPT_PROVINCE><![CDATA[".(!empty($addr) ? $addr->province : "")."]]></RECEIPT_PROVINCE>";
            $xml .=   "<RECEIPT_POSTCODE>".(!empty($addr) ? $addr->postcode : "")."</RECEIPT_POSTCODE>";
            $xml .=   "<PAYMENT_METHOD>".$cod."</PAYMENT_METHOD>";
            $xml .=   "<COD_AMOUNT>".round($cod_amount,2)."</COD_AMOUNT>";
						$xml .=   "<SALES_CHANNEL_CODE>".$channels_code."</SALES_CHANNEL_CODE>";
						$xml .=   "<SALES_CHANNEL_NAME><![CDATA[".$channels_name."]]></SALES_CHANNEL_NAME>";
						$xml .=   "<REF_NO1>".$order->reference."</REF_NO1>";
						$xml .=   "<REF_NO2>".$order->shipping_code."</REF_NO2>";
						$xml .=   "<TRANSFORMED_ITEMS>".($order->transformed == 1 ? "Yes" : "No")."</TRANSFORMED_ITEMS>";
						$xml .=   "<REMARK><![CDATA[".$order->remark."]]></REMARK>";
            $xml .=   "<INSURANCE_COLLECTION>YES</INSURANCE_COLLECTION>";
						$xml .=  "</HEADER>";

						//--- Item start
						$xml .= "<ITEMS>";

						foreach($details as $rs)
						{
							if($rs->is_count)
							{
								$xml .= "<ITEM>";
							  $xml .= "<ITEM_NO>".$rs->product_code."</ITEM_NO>";
								$xml .= "<ITEM_DESC><![CDATA[".$rs->product_name."]]></ITEM_DESC>";
								$xml .= "<VARIANT></VARIANT>";
								$xml .= "<LOT_NO></LOT_NO>";
								$xml .= "<SERIAL_NO></SERIAL_NO>";
								$xml .= "<QUANTITY>".round($rs->qty,2)."</QUANTITY>";
								$xml .= "<UOM>".$rs->unit_code."</UOM>";
                $xml .= "<UNITPRICE_INCLUDE_VAT>".round($rs->price, 2)."</UNITPRICE_INCLUDE_VAT>";
                $xml .= "<DISCOUNT_AMOUNT>".(round(($rs->discount_amount/$rs->qty), 2))."</DISCOUNT_AMOUNT>";
                $xml .= "<TOTAL_WITH_DISCOUNT>".(round($rs->total_amount, 2))."</TOTAL_WITH_DISCOUNT>";
								$xml .= "</ITEM>";
							}

						}

						$xml .= "</ITEMS>";
					$xml .= "</ORDER>";
					$xml .= "</ORDER_LIST>";
					//--- End header section
					$xml .= "</WOB>";

					if($this->log_xml)
					{
						$arr = array(
							'order_code' => $code,
							'xml_text' => $xml
						);

						$this->ci->wms_error_logs_model->log_xml($arr);
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "ไม่พบรายการสินค้าในออเดอร์";
				}
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "เลขที่ออเดอร์ไม่ถูกต้อง";
		}

		if( ! $this->test)
		{
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
        else
        {
          $sc = FALSE;
          $this->error = "No response";
        }
      }
		}

		if($sc === TRUE)
		{
			$this->ci->wms_error_logs_model->add($order->code, 'S', NULL, $this->type);
		}
		else
		{

			$this->ci->wms_error_logs_model->add($code, 'E', $this->error, $this->type);
		}

		return $sc;
  }



	//---- export
  public function export_transfer_order($order, $details)
  {
		$sc = TRUE;
		$xml = "";
		$order_type = "WW";

		if(!empty($order))
		{

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
					$xml .=   "<SHIP_TO_CODE>WARRIX</SHIP_TO_CODE>";
					$xml .=   "<SHIP_TO_NAME></SHIP_TO_NAME>";
					$xml .=   "<SHIP_TO_ADDRESS1></SHIP_TO_ADDRESS1>";
					$xml .=   "<SHIP_TO_ADDRESS2></SHIP_TO_ADDRESS2>";
					$xml .=   "<RECEIPT_NAME></RECEIPT_NAME>";
					$xml .=   "<RECEIPT_MOBILENO></RECEIPT_MOBILENO>";
					$xml .=   "<RECEIPT_EMAIL></RECEIPT_EMAIL>";
					$xml .=   "<RECEIPT_FULLSHIPPINGADDRESS></RECEIPT_FULLSHIPPINGADDRESS>";
					$xml .=   "<RECEIPT_STREET></RECEIPT_STREET>";
					$xml .=   "<RECEIPT_SUBDISTRICT></RECEIPT_SUBDISTRICT>";
					$xml .=   "<RECEIPT_DISTRICT></RECEIPT_DISTRICT>";
					$xml .=   "<RECEIPT_PROVINCE></RECEIPT_PROVINCE>";
					$xml .=   "<RECEIPT_POSTCODE></RECEIPT_POSTCODE>";
					$xml .=   "<PAYMENT_METHOD>NON-COD</PAYMENT_METHOD>";
					$xml .=   "<COD_AMOUNT>0.00</COD_AMOUNT>";
					$xml .=   "<SALES_CHANNEL_CODE></SALES_CHANNEL_CODE>";
					$xml .=   "<SALES_CHANNEL_NAME></SALES_CHANNEL_NAME>";
					$xml .=   "<REF_NO1>{$order->pallet_no}</REF_NO1>";
					$xml .=   "<REF_NO2></REF_NO2>";
					$xml .=   "<TRANSFORMED_ITEMS>NO</TRANSFORMED_ITEMS>";
					$xml .=   "<REMARK><![CDATA[".$order->remark."]]></REMARK>";
					$xml .=  "</HEADER>";

					//--- Item start
					$xml .= "<ITEMS>";

					foreach($details as $rs)
					{
						$xml .= "<ITEM>";
						$xml .= "<ITEM_NO>".$rs->product_code."</ITEM_NO>";
						$xml .= "<ITEM_DESC><![CDATA[".$rs->product_name."]]></ITEM_DESC>";
						$xml .= "<VARIANT></VARIANT>";
						$xml .= "<LOT_NO></LOT_NO>";
						$xml .= "<SERIAL_NO></SERIAL_NO>";
						$xml .= "<QUANTITY>".round($rs->qty,2)."</QUANTITY>";
						$xml .= "<UOM>".$rs->unit_code."</UOM>";
						$xml .= "<UNITPRICE_INCLUDE_VAT>0.00</UNITPRICE_INCLUDE_VAT>";
						$xml .= "<DISCOUNT_AMOUNT>0.00</DISCOUNT_AMOUNT>";
						$xml .= "<TOTAL_WITH_DISCOUNT>0.00</TOTAL_WITH_DISCOUNT>";
						$xml .= "</ITEM>";
					}

					$xml .= "</ITEMS>";
				$xml .= "</ORDER>";
				$xml .= "</ORDER_LIST>";
				//--- End header section
				$xml .= "</WOB>";

				if($this->log_xml)
				{
					$arr = array(
						'order_code' => $order->code,
						'xml_text' => $xml
					);

					$this->ci->wms_error_logs_model->log_xml($arr);
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Transfer items not found";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Empty order data";
		}

		if( ! $this->test)
    {
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
        else
        {
          $sc = FALSE;
          $this->error = "No response";
        }
      }
    }


		if($sc === TRUE)
		{
			$this->ci->wms_error_logs_model->add($order->code, 'S', NULL, $this->type);
		}
		else
		{
			$this->ci->wms_error_logs_model->add($order->code, 'E', $this->error, $this->type);
		}

		return $sc;
  }



	public function get_type_code($channels_code)
	{
		$this->ci->load->model('masters/channels_model');

		$channels = $this->ci->channels_model->get($channels_code);

		if(!empty($channels))
		{
			return $channels->type_code;
		}

		return NULL;
	}

  //---- export
  public function test_export_order($code)
  {
    $this->ci->load->model('orders/orders_model');
    $this->ci->load->model('address/address_model');
    $this->ci->load->model('masters/sender_model');
    $this->ci->load->model('masters/channels_model');
    $this->ci->load->model('masters/payment_methods_model');


    $sc = TRUE;

    $role_type_list = array(
      'S' => 'WO', //--- check channels type_code
      'P' => 'WS',
      'U' => 'WU',
      'C' => 'WC',
      'N' => 'WT',
      'Q' => 'WQ',
      'T' => 'WV',
      'L' => 'WL'
    );

    $xml = "";


    $order = $this->ci->orders_model->get($code);

    if(!empty($order))
    {
      if(empty($order->id_address))
      {
        $sc = FALSE;
        $this->error = "ไม่พบที่อยู่จัดส่ง";
      }
      else
      {
        $addr = $this->ci->address_model->get_shipping_detail($order->id_address);

        if(empty($addr))
        {
          $sc = FALSE;
          $this->error = "ไม่พบที่อยู่จัดส่ง";
        }

        $sender = $this->ci->sender_model->get($order->id_sender);

        if(empty($sender))
        {
          $sc = FALSE;
          $this->error = "ไม่ได้ระบุขนส่ง";
        }
      }

      if($sc === TRUE)
      {
        $details = $this->ci->orders_model->get_only_count_stock_details($code);
        $channels = $order->role === 'S' ? $this->ci->channels_model->get($order->channels_code) : NULL;
        $order_type = !empty($channels) ? $channels->type_code : $role_type_list[$order->role];
        $channels_code = !empty($channels) ? $order->channels_code : $role_type_list[$order->role];
        $channels_name = !empty($channels) ? $channels->name : "";
        $doc_total = $order->doc_total <= 0 ? $this->ci->orders_model->get_order_total_amount($order->code) : $order->doc_total;
        $cod = $order->role === 'S' ? ($order->payment_role == 4 ? 'COD' : 'NON-COD') : 'NON-COD';
        $cod_amount = $cod === 'COD' ? ($order->cod_amount == 0 ? $doc_total : $order->cod_amount) : 0.00;

        if( ! empty($details))
        {
          $xml .= "<WOB>\r\n";

          //--- Header_list section
          $xml .= "<HEADER_LIST>\r\n";
          $xml .=   "<WH_NO>".$this->WH_NO."</WH_NO>\r\n";
          $xml .=   "<CUST_CODE>".$this->CUS_CODE."</CUST_CODE>\r\n";
          $xml .=   "<ORDER_LIST_NO>".$order->code."</ORDER_LIST_NO>\r\n";
          $xml .= "</HEADER_LIST>\r\n";
          //---- End header_list section
          $spx = $sender->code == "SPX" ? TRUE : FALSE;
          $addr->province = $spx ? parseProvince($addr->province) : $addr->province;
          $addr->sub_district = $spx ? parseSubDistrict($addr->sub_district, $addr->province) : $addr->sub_district;
          $addr->district = $spx ? parseDistrict($addr->district, $addr->province) : $addr->district;
          $addr->phone = $spx ? parsePhoneNumber($addr->phone, 10) : $addr->phone;
          //--- Header section
          $xml .= "<ORDER_LIST>\r\n";

            //--- Order Start
            $xml .= "<ORDER>\r\n";
            $xml .=  "<HEADER>\r\n";
            $xml .=   "<ORDER_NO>".$order->code."</ORDER_NO>\r\n";
            $xml .=   "<ORDER_TYPE>".$order_type."</ORDER_TYPE>\r\n";
            $xml .=   "<SHIPMENT_DATE>".date('Y/m/d', strtotime($order->date_add))."</SHIPMENT_DATE>\r\n";
            $xml .=   "<SHIP_TO_CODE>".(!empty($sender) ? $sender->code : "")."</SHIP_TO_CODE>\r\n";
            $xml .=   "<SHIP_TO_NAME><![CDATA[".(!empty($sender) ? $sender->name : "")."]]></SHIP_TO_NAME>\r\n";
            $xml .=   "<SHIP_TO_ADDRESS1><![CDATA[".(!empty($sender) ? $sender->address1 : "")."]]></SHIP_TO_ADDRESS1>\r\n";
            $xml .=   "<SHIP_TO_ADDRESS2><![CDATA[".(!empty($sender) ? $sender->address2 : "")."]]></SHIP_TO_ADDRESS2>\r\n";
            $xml .=   "<RECEIPT_NAME><![CDATA[".(!empty($addr) ? $addr->name : "")."]]></RECEIPT_NAME>\r\n";
            $xml .=   "<RECEIPT_MOBILENO>".(!empty($addr) ? $addr->phone : "")."</RECEIPT_MOBILENO>\r\n";
            $xml .=   "<RECEIPT_EMAIL>".(!empty($addr) ? $addr->email : "")."</RECEIPT_EMAIL>\r\n";
            $xml .=   "<RECEIPT_FULLSHIPPINGADDRESS><![CDATA[".(!empty($addr) ? $addr->address : "")."]]></RECEIPT_FULLSHIPPINGADDRESS>\r\n";
            $xml .=   "<RECEIPT_STREET></RECEIPT_STREET>\r\n";
            $xml .=   "<RECEIPT_SUBDISTRICT><![CDATA[".(!empty($addr) ? $addr->sub_district : "")."]]></RECEIPT_SUBDISTRICT>\r\n";
            $xml .=   "<RECEIPT_DISTRICT><![CDATA[".(!empty($addr) ? $addr->district : "")."]]></RECEIPT_DISTRICT>\r\n";
            $xml .=   "<RECEIPT_PROVINCE><![CDATA[".(!empty($addr) ? $addr->province : "")."]]></RECEIPT_PROVINCE>\r\n";
            $xml .=   "<RECEIPT_POSTCODE>".(!empty($addr) ? $addr->postcode : "")."</RECEIPT_POSTCODE>\r\n";
            $xml .=   "<PAYMENT_METHOD>".$cod."</PAYMENT_METHOD>\r\n";
            $xml .=   "<COD_AMOUNT>".round($cod_amount,2)."</COD_AMOUNT>\r\n";
            $xml .=   "<SALES_CHANNEL_CODE>".$channels_code."</SALES_CHANNEL_CODE>\r\n";
            $xml .=   "<SALES_CHANNEL_NAME><![CDATA[".$channels_name."]]></SALES_CHANNEL_NAME>\r\n";
            $xml .=   "<REF_NO1>".$order->reference."</REF_NO1>\r\n";
            $xml .=   "<REF_NO2>".$order->shipping_code."</REF_NO2>\r\n";
            $xml .=   "<TRANSFORMED_ITEMS>".($order->transformed == 1 ? "Yes" : "No")."</TRANSFORMED_ITEMS>\r\n";
            $xml .=   "<REMARK><![CDATA[".$order->remark."]]></REMARK>\r\n";
            $xml .=   "<INSURANCE_COLLECTION>YES</INSURANCE_COLLECTION>\r\n";
            $xml .=  "</HEADER>\r\n";

            //--- Item start
            $xml .= "<ITEMS>\r\n";

            foreach($details as $rs)
            {
              if($rs->is_count)
              {
                $xml .= "<ITEM>\r\n";
                $xml .= "<ITEM_NO>".$rs->product_code."</ITEM_NO>\r\n";
                $xml .= "<ITEM_DESC><![CDATA[".$rs->product_name."]]></ITEM_DESC>\r\n";
                $xml .= "<VARIANT></VARIANT>\r\n";
                $xml .= "<LOT_NO></LOT_NO>\r\n";
                $xml .= "<SERIAL_NO></SERIAL_NO>\r\n";
                $xml .= "<QUANTITY>".round($rs->qty,2)."</QUANTITY>\r\n";
                $xml .= "<UOM>".$rs->unit_code."</UOM>\r\n";
                $xml .= "<UNITPRICE_INCLUDE_VAT>".round($rs->price, 2)."</UNITPRICE_INCLUDE_VAT>\r\n";
                $xml .= "<DISCOUNT_AMOUNT>".(round(($rs->discount_amount/$rs->qty), 2))."</DISCOUNT_AMOUNT>\r\n";
                $xml .= "<TOTAL_WITH_DISCOUNT>".(round($rs->total_amount, 2))."</TOTAL_WITH_DISCOUNT>\r\n";
                $xml .= "</ITEM>\r\n";
              }

            }

            $xml .= "</ITEMS>\r\n";
          $xml .= "</ORDER>\r\n";
          $xml .= "</ORDER_LIST>\r\n";
          //--- End header section
          $xml .= "</WOB>";

          $res = $xml;
        }
        else
        {
          $sc = FALSE;
          $this->error = "ไม่พบรายการสินค้าในออเดอร์";
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "เลขที่ออเดอร์ไม่ถูกต้อง";
    }

    return $sc === TRUE ? $res : $this->error;
  }

}
?>

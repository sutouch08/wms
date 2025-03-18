<?php
$this->load->helper('print');
$total_row 	= empty($details) ? 0 :count($details);
$config 		= array(
	"row" => 15,
	"total_row" => $total_row,
	"font_size" => 11,
	"title_size" => 18,
	"text_color" => "",
	// "table_class" => "table-bordered",
	"table_style" => "",
	"row_style" => ""
);

$this->xprinter->config($config);

$page  = '';
$page .= $this->xprinter->doc_header();

$this->xprinter->add_title('ใบคุมการจัดส่ง');


$header		= array();

//---- Header block Company details On Left side
$header['left'] = array();

$header['left']['A'] = array(
	'company_name' => "<span style='font-size:".($this->xprinter->font_size + 5)."px; font-weight:bolder;'>บริษัท วอริกซ์ สปอร์ต จำกัด (มหาชน)</span>",
	'address1' => "849/6-8 ถนนพระราม 6 แขวงวังใหม่",
	'address2' => "เขตปทุมวัน กรุงเทพมหานคร 10330",
	'phone' => "โทร. 0-2117-1300 แฟ็กซ์. 0-2117-1308",
	'taxid' => "เลขประจำตัวผู้เสียภาษีอากร 0107565000255"
);


$header['left']['B'] = array(
	"client" => "<span style='font-size:".($this->xprinter->font_size + 1)."px; font-weight:bolder;'>ผู้รับ</span>",
	"customer" => "<span style='font-size:".($this->xprinter->font_size + 1)."px; font-weight:bolder;'>{$doc->driver_name}</span>",
	"address1" => "ทะเบียนรถ : {$doc->plate_no}  {$doc->plate_province}",
	// "address2" => "ต.สำโรงเหนือ อ.เมือง สมุทรปราการ 10270"
	// "phone" => "โทร. -",
	// "taxid" => "เลขประจำตัวผู้เสียภาษีอากร 0105564000233"
);



//--- Header block  Document details On the right side
$header['right'] = array();

$header['right']['A'] = array(
	array('label' => 'เลขที่', 'value' => $doc->code),
	array('label' => 'วันที่', 'value' => thai_date($doc->date_add, FALSE, '/'))
);

$header['right']['B'] = array(
	array('label' => 'ช่องทางขาย', 'value' => empty($doc->channels_code) ? 'ไม่ระบุ' : ( ! empty($doc->channels_name) ? $doc->channels_name : $this->channels_model->get_name($doc->channels_code))),
	array('label' => 'ขนส่ง', 'value' => empty($doc->sender_code) ? 'ไม่ระบุ' : ( ! empty($doc->sender_name) ? $doc->sender_name : $this->sender_model->get_name($doc->sender_code)))
);

$this->xprinter->add_header($header);

$subtotal_row = 4;


$row 	= $this->xprinter->row;
$total_page  = $this->xprinter->total_page;
$total_carton = 0;
$total_shipped = 0;

//**************  กำหนดหัวตาราง  ******************************//
$thead	= array(
	array("#", "width:10mm; text-align:center; border:solid 1px #333;"),
	array("เลขที่", "width:30mm; text-align:center; border:solid 1px #333;"),
	array("อ้างอิง", "width:35mm; text-align:center; border:solid 1px #333;"),
	array("ช่องทาง", "width:35mm; text-align:center; border:solid 1px #333;"),
	array("ลูกค้า", "width:65mm; text-align:center; border:solid 1px #333;"),
	array("จำนวน(กล่อง)", "width:15mm; text-align:center; border:solid 1px #333;")
);

$this->xprinter->add_subheader($thead);


//***************************** กำหนด css ของ td *****************************//
$pattern = array(
	"text-align:center; border:solid 1px #333;",
	"text-align:left; border:solid 1px #333;",
	"text-aligh:left; border:solid 1px #333; padding:3px 8px;",
	"text-align:center; border:solid 1px #333;",
	"text-align:center; border:solid 1px #333;",
	"text-align:center; border:solid 1px #333;"
);

$this->xprinter->set_pattern($pattern);


//*******************************  กำหนดช่องเซ็นของ footer *******************************//
$footer	= array(
	array("พนักงานขนส่ง", "","วันที่"),
	array(NULL, NULL,NULL),
	array("พนักงาน Warrix", "","วันที่")
);

$this->xprinter->set_footer($footer);


$n = 1;
$index = 0;
while($total_page > 0 )
{
  $page .= $this->xprinter->page_start();
  $page .= $this->xprinter->top_page();
  $page .= $this->xprinter->content_start();
  $page .= $this->xprinter->table_start();
  $i = 0;

  while($i < $row)
  {
    $rs = isset($details[$index]) ? $details[$index] : FALSE;

    if( ! empty($rs) )
    {
      //--- เตรียมข้อมูลไว้เพิ่มลงตาราง
			$channels_name = ! empty($rs->channels_name) ? $rs->channels_name : (empty($rs->channels_code) ? NULL : $this->channels_model->get_name($rs->channels_code));
			$data = array(
				$n,
				$rs->order_code,
				$rs->reference,
				inputRow($channels_name),
				inputRow($rs->customer_name),
				$rs->carton_shipped
			);

			$total_carton += $rs->carton_qty;
			$total_shipped += $rs->carton_shipped;
    }
    else
    {
      $data = array("", "", "", "", "", "");
    }

    $page .= $this->xprinter->print_row($data);

    $n++;
    $i++;
    $index++;
  }

	if($this->xprinter->current_page == $this->xprinter->total_page)
  {
		$totalCarton = number($total_carton);
		$totalShipped = number($total_shipped);
  }
  else
  {
		$totalCarton = "";
		$totalShipped = "";
  }

	//--- จำนวนรวม   ตัว
	$page .= "<tr style='font-size:10px; height:31px;'>";
  $page .= '<td colspan="5" class="text-right" style="border:solid 1px #333;">';
	$page .= 'Total';
  $page .= '</td>';
  $page .= '<td class="text-center" style="border:solid 1px #333;">';
  $page .=  '<strong>'.$totalShipped.'</strong>';
  $page .= '</td>';
	$page .= '</tr>';

  $page .= $this->xprinter->table_end();

  $page .= $this->xprinter->content_end();
	$page .= "<div class='divider-hidden'></div>";
	$page .= "<div class='divider-hidden'></div>";
	$page .= "<div class='divider-hidden'></div>";
	$page .= "<div class='divider-hidden'></div>";
	$page .= "<div class='divider-hidden'></div>";
  $page .= $this->xprinter->footer;
  $page .= $this->xprinter->page_end();

  $total_page --;
  $this->xprinter->current_page++;
}

$page .= $this->xprinter->doc_footer();

echo $page;
 ?>

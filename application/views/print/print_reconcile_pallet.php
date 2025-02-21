<?php
$this->load->helper('print');
$total_row 	= empty($details) ? 0 :count($details);
$config 		= array(
	"row" => 17,
	"total_row" => $total_row,
	"font_size" => 10,
	"title_size" => 18,
	"text_color" => "",
	// "table_class" => "table-bordered",
	"table_style" => "",
	"row_style" => ""
);

$this->xprinter->config($config);

$page  = '';
$page .= $this->xprinter->doc_header();

$this->xprinter->add_title('WARRIX RECONCILE PALLET');


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
	"client" => "<span style='font-size:".($this->xprinter->font_size + 1)."px; font-weight:bolder;'>ผู้ส่งมอบ</span>",
	"customer" => "<span style='font-size:".($this->xprinter->font_size + 1)."px; font-weight:bolder;'>บริษัท โซโกะจัน จำกัด</span>",
	"address1" => "888 หมู่ที่ 5 ตึกวีจีอาร์ ชั้น 4 ถ.ศรีนครินทร์",
	"address2" => "ต.สำโรงเหนือ อ.เมือง สมุทรปราการ 10270"
	// "phone" => "โทร. -",
	// "taxid" => "เลขประจำตัวผู้เสียภาษีอากร 0105564000233"
);



//--- Header block  Document details On the right side
$header['right'] = array();

$header['right']['A'] = array(
	array('label' => 'เลขที่', 'value' => $order->code),
	array('label' => 'วันที่', 'value' => thai_date($order->date_add, FALSE, '/'))
);

$header['right']['B'] = array(
	array('label' => 'ต้นทาง', 'value' => $order->from_warehouse_name),
	array('label' => 'ปลายทาง', 'value' => $order->to_warehouse_name),
	array('label' => 'Pallet No.', 'value' => $order->pallet_no)
);

$this->xprinter->add_header($header);

$subtotal_row = 4;


$row 	= $this->xprinter->row;
$total_page  = $this->xprinter->total_page;
$total_soko = 0;
$total_wrx = 0;
$total_diff = 0;

//**************  กำหนดหัวตาราง  ******************************//
$thead	= array(
	array("#", "width:10mm; text-align:center; border:solid 1px #333;"),
	array("รหัส", "width:45mm; text-align:left; border:solid 1px #333;"),
	array("สินค้า", "width:90mm; text-align:left; border:solid 1px #333;"),
	array("Soko", "width:15mm; text-align:center; border:solid 1px #333;"),
	array("Warrix", "width:15mm; text-align:center; border:solid 1px #333;"),
	array("Diff", "width:15mm; text-align:center; border:solid 1px #333;")
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
	array("พนักงาน Sokochan", "","วันที่"),
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
			$diff = $rs->wms_qty - $rs->qty;

			$data = array(
				$n,
				$rs->product_code,
				inputRow($rs->product_name),
				number($rs->qty),
				number($rs->wms_qty),
				($diff == 0 ? "-" : $diff)
			);

      $total_soko += $rs->qty;
			$total_wrx += $rs->wms_qty;
			$total_diff += $diff;
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
    $totalSoko  = number($total_soko);
		$totalWarrix = number($total_wrx);
		$totalDiff = number($total_diff);
  }
  else
  {
		$totalSoko  = "";
		$totalWarrix = "";
		$totalDiff = "";
  }

	//--- จำนวนรวม   ตัว
	$page .= "<tr style='font-size:10px; height:31px;'>";
  $page .= '<td colspan="3" class="text-right" style="border:solid 1px #333;">';
	$page .= 'Total';
  $page .= '</td>';
  $page .= '<td class="text-center" style="border:solid 1px #333;">';
  $page .=  '<strong>'.$totalSoko.'</strong>';
  $page .= '</td>';
	$page .= '<td class="text-center" style="border:solid 1px #333;">';
  $page .=  '<strong>'.$totalWarrix.'</strong>';
  $page .= '</td>';
  $page .= '<td class="text-center" style="border:solid 1px #333;">';
  $page .=  '<strong>'.$totalDiff.'</strong>';
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

<?php
  $this->load->helper('print');

  $page = '';
  $page .= $this->printer->doc_header();
	$this->printer->add_title("ใบรับวัติถุดิบ");
	$header	= array(
    'เลขที่' => $doc->code,
    'วันที่'  => thai_date($doc->date_add, FALSE, '/'),
    'ใบสั่งซื้อ' => $doc->po_code,
    'ใบรับสินค้า' => $doc->invoice_code,
    'ผู้ขาย' => $doc->vendor_code.' : '.$doc->vendor_name,
    'โซน' => $doc->zone_code,
    'คลัง' => $doc->warehouse_code,
    'พนักงาน' => $this->user_model->get_name($doc->user)
	);

  if($doc->remark != '')
  {
    $header['หมายเหตุ'] = $doc->remark;
  }

	$this->printer->add_header($header);

	$total_row = empty($details) ? 1 : count($details);

	$config = array(
    'total_row' => $total_row,
    'font_size' => 10,
    'sub_total_row' => 1
  );

	$this->printer->config($config);

	$row = $this->printer->row;
	$total_page = $this->printer->total_page;

  $thead	= array(
    array("ลำดับ", "width:10mm; text-align:center; border-top:0px; border-top-left-radius:10px;"),
    array("รหัส", "width:40mm; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
    array("สินค้า", "width:50mm; text-align:center;border-left: solid 1px #ccc; border-top:0px;"),
    array("ราคา", "width:25mm; text-align:center;border-left: solid 1px #ccc; border-top:0px;"),
    array("จำนวน", "width:40mm; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
    array("มูลค่า", "width:25mm; text-align:center; border-left: solid 1px #ccc; border-top:0px; border-top-right-radius:10px")
  );

	$this->printer->add_subheader($thead);

  $pattern = array(
    "text-align: center; border-top:0px;",
    "border-left: solid 1px #ccc; border-top:0px;",
    "border-left: solid 1px #ccc; border-top:0px;",
    "border-left: solid 1px #ccc; border-top:0px; text-align:right;",
    "border-left: solid 1px #ccc; border-top:0px; text-align:right;",
    "border-left: solid 1px #ccc; border-top:0px; text-align:right;"
  );

  $this->printer->set_pattern($pattern);


	// กำหนดช่องเซ็นของ footer
	$d = date('d', strtotime($doc->date_add) );
	$m = date('m', strtotime($doc->date_add) );
	$Y = date('Y', strtotime($doc->date_add) );
  $footer	= array(
    array("ผู้รับ", "", "วันที่ ............................."),
    array("ผู้ตรวจสอบ", "","วันที่ ............................."),
    array("ผู้อนุมัติ", "","วันที่ .............................")
  );

  $this->printer->set_footer($footer);

  $totalQty = "";
  $docTotal = "";
  $remark = "";

	$n = 1;
  $index = 0;

	while($total_page > 0 )
	{
    $page .= $this->printer->page_start();
    $page .= $this->printer->top_page();
    $page .= $this->printer->content_start();
    $page .= $this->printer->table_start();

    if($doc->status == 'D')
    {
      $page .= '
      <div style="width:100%; height:100%; position:absolute; left:0; top:0; color:red; z-index:0; opacity:0.1; display:flex; justify-content:center; align-items:center;">
        <span style="font-size:120px; line-height:1; border-color:red; border:solid 10px; border-radius:20px; padding:20px; display:flex; justify-content:center; align-items:center;">ยกเลิก</span>
      </div>';
    }

    $i = 0;

    while($i < $row)
    {
      $rs = isset($details[$index]) ? $details[$index] : array();

      if( ! empty($rs))
      {
        $data = array(
          $n,
          inputRow($rs->ItemCode),
          inputRow($rs->ItemName),
          number($rs->Price, 2),
          number($rs->Qty, 2)." {$rs->unitMsr}",
          number($rs->LineTotal, 2)
        );
      }
      else
      {
        $data = array("", "", "", "","", "");
      }

      $page .= $this->printer->print_row($data);
      $n++;
      $i++;
      $index++;
    }

    $page .= $this->printer->table_end();

    if($this->printer->current_page == $this->printer->total_page)
    {
      $totalQty = number($doc->TotalQty, 2);
      $docTotal = number($doc->DocTotal, 2);
      $remark = $doc->remark;
    }

    $sub_total = array(
      array(
        "<td style='height:".$this->printer->row_height."mm; border: solid 1px #ccc;
        border-bottom:0px; border-left:0px; text-align:right; width:124.6mm;'>
        <strong>รวม</strong>
        </td>
        <td style='height:".$this->printer->row_height."mm; border: solid 1px #ccc;
        border-right:0px; border-bottom:0px; text-align:right; width:39.5mm;'>
        ".$totalQty."</td>
        <td style='height:".$this->printer->row_height."mm; border: solid 1px #ccc;
        border-right:0px; border-bottom:0px; border-bottom-right-radius:10px;
        text-align:right;'>".$docTotal."</td>")
      );

			$page .= $this->printer->print_sub_total($sub_total);
			$page .= $this->printer->content_end();
			$page .= $this->printer->footer;
		  $page .= $this->printer->page_end();
		  $total_page --;
      $this->printer->current_page++;
	}

	$page .= $this->printer->doc_footer();

  echo $page;
?>

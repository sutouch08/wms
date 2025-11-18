<?php
  $this->load->helper('print');
  $page = '';
  $page .= $this->printer->doc_header();
	$this->printer->add_title("ใบรายงานตรวจนับสินค้าฝากขาย");
	$header	= array(
    'เลขที่' => $doc->code,
    'วันที่'  => thai_date($doc->date_add, FALSE, '/'),
    'กล่องที่' => $box->box_no,
    'ผู้ทำรายการ' => $this->user_model->get_name($doc->user)
	);

	$this->printer->add_header($header);

	$total_row 	= empty($details) ? 0 : count($details);
	$config = array(
    'total_row' => $total_row,
    'font_size' => 14,
    'header_rows' => 2,
    'row' => 10,
    'row_height' => 16,
    'sub_total_row' => 1,
    'footer' => FALSE
  );

	$this->printer->config($config);

	$row 	= $this->printer->row;
	$total_page = $this->printer->total_page;
	$total_qty 	= 0;

  $thead	= array(
    array("ลำดับ", "width:10mm; text-align:center; border-top:0px; border-top-left-radius:10px;"),
    array("Barcode", "width:60mm; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
    array("สินค้า", "width:90mm; text-align:center;border-left: solid 1px #ccc; border-top:0px;"),
    array("จำนวน", "width:30mm; text-align:center; border-left: solid 1px #ccc; border-top:0px; border-top-right-radius:10px")
  );

	$this->printer->add_subheader($thead);

  $pattern = array(
    "text-align:center; border-top:0px;",
    "text-align:center; border-left:solid 1px #ccc; border-top:0px;",
    "border-left: solid 1px #ccc; border-top:0px;",
    "text-align:right; border-left: solid 1px #ccc; border-top:0px;"
  );

	$this->printer->set_pattern($pattern);

  // $footer	= array(
  //   array("ผู้จัดทำ", "", "วันที่ ............................."),
  //   array("ผู้ตรวจสอบ", "","วันที่ .............................")
  // );
  //
  // $this->printer->set_footer($footer);

	$n = 1;
  $index = 0;
	while($total_page > 0 )
	{
		$page .= $this->printer->page_start();
			$page .= $this->printer->top_page();
			$page .= $this->printer->content_start();
				$page .= $this->printer->table_start();
				if($doc->status == 2)
				{
					$page .= '
				  <div style="width:0px; height:0px; position:relative; left:30%; line-height:0px; top:300px;color:red; text-align:center; z-index:100000; opacity:0.1; transform:rotate(-45deg)">
				      <span style="font-size:150px; border-color:red; border:solid 10px; border-radius:20px; padding:0 20 0 20;">ยกเลิก</span>
				  </div>';
				}

				$i = 0;

				while($i < $row)
        {
					$rs = isset($details[$index]) ? $details[$index] : array();
					if(!empty($rs))
          {
            $bc_height = 12; // mm
            $bc_width = NULL; // mm
            $bc_fontsize = 12; //px
            $data = array(
              $n,
							barcodeImage($rs->barcode, $bc_height, $bc_width, $bc_fontsize),
							inputRow($rs->product_code),
              ac_format($rs->qty)
						);

            $total_qty += $rs->qty;
          }
          else
          {
            $data = array("", "", "", "");
          }
					$page .= $this->printer->print_row($data);
					$n++;
          $i++;
          $index++;
				}

				$page .= $this->printer->table_end();

        $is_last = FALSE;

				if($this->printer->current_page == $this->printer->total_page)
				{
          $is_last = TRUE;
        }

        $sum_qty = $is_last === TRUE ? number($total_qty) : '';

				$sub_total = array(
          array(
          "<td style='height:".$this->printer->row_height."mm; border: solid 1px #ccc;
          border-bottom:0px; border-left:0px; text-align:right;
          width:159.25mm;'>
          <strong>รวม</strong>
          </td>
          <td style='height:".$this->printer->row_height."mm; border: solid 1px #ccc;
          border-right:0px; border-bottom:0px; border-bottom-right-radius:10px;
          text-align:right;'>".ac_format($sum_qty)."</td>")

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

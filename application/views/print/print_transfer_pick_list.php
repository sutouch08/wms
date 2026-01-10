<?php

  $this->load->helper('print');
  $page = '';
  $page .= $this->printer->doc_header();
  $page .= "<style>
    .input-xs {
      font-size: 11px;
      border:0 !important;
      box-shadow: none;
    }

    .pd-row {
      padding:3px !important;
      border-top:0px !important;
      border-left: solid 1px #ccc;
    }

    .batch-row {
      padding:3px !important;
      border-top:0px !important;
    }

    .pd-row, .batch-row {
      border-left: solid 1px #ccc;
    }

    .pd-row:first-child, .batch-row:first-child {
      border-left: 0px !important;
    }

    .last-batch > td {
      border-bottom:solid 1px #ccc !important;
    }
  </style>";

	$this->printer->add_title("Transfer for Production");

  $rows = 1;

  if( ! empty($details))
  {
    foreach($details as $rs)
    {
      if($rs->hasBatch && ! empty($rs->batchRows))
      {
        $rows += count($rs->batchRows);
      }

      $rows++;
    }
  }

	$total_row 	= $rows;

	$config = array(
    'page_width' => 282,
    'page_height' => 200,
    'content_width' => 270,
    'row_height' => 8,
    'row' => 15,
    'total_row' => $total_row,
    'font_size' => 10,
    'header_rows' => 3,
    'sub_total_row' => 0,
    'footer' => false
  );

	$this->printer->config($config);

	$rows 	= $this->printer->row;
	$total_page = $this->printer->total_page;
	$total_qty 	= 0;

  $thead	= array(
    array("#", "width:10mm; text-align:center; border-top:0px; border-top-left-radius:10px;"),
    array("Item", "width:145mm; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
    array("From Whs","width:25mm; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
    array("From Bin", "width:50mm; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
    array("จำนวน", "width:20mm; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
    array("หน่วย", "width:20mm; text-align:center; border-left: solid 1px #ccc; border-top:0px; border-top-right-radius:10px")
  );

	$this->printer->add_subheader($thead);

  $pattern = array(
    "border-top:0px;",
    "border-left: solid 1px #ccc; border-top:0px;",
    "border-left: solid 1px #ccc; border-top:0px;",
    "border-left: solid 1px #ccc; border-top:0px;",
    "border-left: solid 1px #ccc; border-top:0px;",
    "border-left: solid 1px #ccc; border-top:0px;",
    "border-left: solid 1px #ccc; border-top:0px;"
  );

  $this->printer->set_pattern($pattern);

  $header_style = "font-size:12px; min-height:{$this->printer->row_height}mm; line-height:{$this->printer->row_height}mm; float:left; padding-left:10px; padding-right:10px; white-space:nowrap; overflow:hidden;";

  $header  = '<div style="width:33%; '.$header_style.'">เลขที่ : '.$doc->code.'</div>';
  $header .= '<div style="width:33%; '.$header_style.'">วันที่ : '.thai_date($doc->date_add, FALSE, '/').'</div>';
  $header .= '<div style="width:34%; '.$header_style.'">ใบผลิต : '.$doc->reference.' | '.$doc->ItemCode.'</div>';
  $header .= '<div style="width:33%; '.$header_style.'">คลังต้นทาง : '.$doc->fromWhsCode.'</div>';
  $header .= '<div style="width:33%; '.$header_style.'">คลังปลายทาง : '.$doc->toWhsCode.'</div>';
  $header .= '<div style="width:34%; '.$header_style.'">โซนปลายทาง : '.$doc->toBinCode.'</div>';
  $header .= '<div style="width:100%; '.$header_style.'">หมายเหตุ : '.$doc->remark.'</div>';

  $this->printer->add_custom_header($header);

	$n = 1;
  $index = 0;
	while($total_page > 0 )
	{
		$page .= $this->printer->page_start();
			$page .= $this->printer->top_page();
			$page .= $this->printer->content_start();
				$page .= $this->printer->table_start();

				$i = 0;

				while($i < $rows)
        {
					$rs = isset($details[$index]) ? $details[$index] : array();

					if( ! empty($rs))
          {
            $fromWhsCode = empty($rs->batchRows) ? $rs->fromWhsCode : NULL;
            $fromBinCode = empty($rs->batchRows) ? $rs->fromBinCode : NULL;

            $row  = '<tr style="font-size:'.$this->printer->font_size.'px; height:'.$this->printer->row_height.'mm;">';
            $row .= '<td class="middle text-center pd-row">'.$n.'</td>';
            $row .= '<td class="pd-row"><input type="text" class="form-control input-xs" value="'.$rs->ItemCode.' &nbsp;|&nbsp; '.$rs->ItemName.'" disabled /></td>';
            $row .= '<td class="pd-row"><input type="text" class="form-control input-xs text-center" value="'.$fromWhsCode.'" disabled /></td>';
            $row .= '<td class="pd-row"><input type="text" class="form-control input-xs " value="'.$fromBinCode.'" disabled /></td>';
            $row .= '<td class="pd-row"><input type="text" class="form-control input-xs text-center" value="'.(number($rs->Qty, 2)).'" disabled /></td>';
            $row .= '<td class="pd-row"><input type="text" class="form-control input-xs text-center" value="'.$rs->unitMsr.'" disabled /></td>';
            $row .= '</tr>';

            $page .= $row;

            if( ! empty($rs->batchRows))
            {
              $c = count($rs->batchRows);
              $d = 1;

              foreach($rs->batchRows as $ro)
              {
                $batchText  = 'Batch: '.$ro->BatchNum;
                $batchText .= ',&nbsp;&nbsp;&nbsp;&nbsp; Attr1: '.(empty($ro->BatchAttr1) ? '-' : $ro->BatchAttr1);
                $batchText .= ',&nbsp;&nbsp;&nbsp;&nbsp; Attr2: '.(empty($ro->BatchAttr2) ? '-' : $ro->BatchAttr2);

                $l = $d == $c ? 'last-batch' : '';

                $row  = '<tr class="'.$l.'" style="font-size:'.$this->printer->font_size.'px; height:'.$this->printer->row_height.'mm;">';
                $row .= '<td class="middle text-right batch-row"></td>';
                $row .= '<td class="batch-row"><input type="text" class="form-control input-xs text-label" value="-  '.$batchText.'" readonly /></td>';
                $row .= '<td class="batch-row"><input type="text" class="form-control input-xs text-label text-center" value="'.$ro->fromWhsCode.'" readonly /></td>';
                $row .= '<td class="batch-row"><input type="text" class="form-control input-xs text-label" value="'.$ro->fromBinCode.'" readonly /></td>';
                $row .= '<td class="batch-row"><input type="text" class="form-control input-xs text-label text-center" value="'.(number($ro->Qty, 2)).'" readonly /></td>';
                $row .= '<td class="batch-row"><input type="text" class="form-control input-xs text-label text-center" value="'.$rs->unitMsr.'" readonly /></td>';
                $row .= '</tr>';
                $page .= $row;
                $d++;
                $i++;
              }
            }
          }
          else
          {
            $data = array("", "", "", "", "", "");
            $page .= $this->printer->print_row($data);
          }

          $n++;
          $i++;
          $index++;
				}

				$page .= $this->printer->table_end();


			$page .= $this->printer->content_end();
		  $page .= $this->printer->page_end();
		  $total_page --;
      $this->printer->current_page++;
	}

	$page .= $this->printer->doc_footer();

  echo $page;
?>

<style type="text/css" media="print">
 @page{
   margin:0;
   size:A4 landscape;
 }
 </style>

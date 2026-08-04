<?php
if(!empty($details)) :

$sc = '';
//--- print HTML document header
$sc .= $this->printer->doc_header();

//--- Set Document title
$this->printer->add_title('Packing List');

$header	 = '<table style="width:100%; border:0px;">';
$header .= '<tr>';
$header .= '<td style="width:65%; height:20mm; font-size:50px; font-weight:bold; padding-left:10px; border-bottom:solid 1px #ccc; text-align:center;">'.$order->code.'</td>';
$header .= '<td class="middle text-center bold" style="width:20%; border-left:solid 1px #CCC; border-bottom:solid 1px #ccc;">
              <span class="display-block" style="font-size:12px; text-align:center">Box no.</span>
              <span class="display-block" style="font-size:40px; text-align:center">'.$box_no.'/'.$all_box.'</span>
              <span class="display-block" style="font-size:12px; text-align:center">Ref: '.$code.'</span></td>';
$header .= '<td style="width:15%; border-left:solid 1px #ccc; border-bottom:solid 1px #ccc; text-align:center;">';
$header .= '<image src="data:image/png;base64, '.$qrcode.'" style="width:20mm;"/>';
$header .= '</td>';
$header .= '</tr>';
$header .= '<tr>';
$header .= '<td colspan="3" style="width:100%; height:30mm; font-size:40px; padding-left:10px; text-align:center;">';
$header .=  ($order->customer_ref != '' ? $order->customer_ref : $order->customer_name);
$header .= '</td>';
$header .= '</tr>';
$header .= '</table>';

$this->printer->add_custom_header($header);

//--- all rows of qc reuslt
$total_row = count($details);


//--- initial config for print page
$config = array(
  "row" => 19,
  "total_row" => $total_row,
  "font_size" => 14,
  //"font_weight" => 'bold',
  "sub_total_row" => 6,
  "header_rows" => 6,
  "footer_row" => 4,
  "footer" => false,
  "border" => 'border:solid 2px #555;'
);

$this->printer->config($config);

//--- rows per page (exclude header, footer, table header)
$row = $this->printer->row;

//---  total of page will be display on top right of pages as page of page(s)
$total_page = $this->printer->total_page;

//--- กำหนดหัวตาราง
$thead	= array(
  array("No.", "width:10%; text-align:center; border-top:0px; border-top-left-radius:10px;"),
  array("Item description", "width:75%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
  array("Qty", "width:15%; text-align:center; border-left: solid 1px #ccc; border-top:0px; border-top-right-radius:10px")
);

$this->printer->add_subheader($thead);

//--- กำหนด css ของ td
$pattern = array(
  "text-align: center; border-top:0px;",
  "border-left: solid 1px #ccc; border-top:0px;",
  "text-align:center; border-left: solid 1px #ccc; border-top:0px;"
);

$this->printer->set_pattern($pattern);

$n = 1;
$i = 0;
$total_qty = 0;
while( $total_page > 0 )
{
  $sc .= $this->printer->page_start();
  $sc .= $this->printer->top_page();
  $sc .= $this->printer->content_start();

  //--- เปิดหัวตาราง
  $sc .= $this->printer->table_start();

  //--- row no in page;
  $x = 0;
  while( $x < $row )
  {
    $rs = isset($details[$i]) ? $details[$i] : array();

    if( ! empty($rs))
    {
      $arr = array(
        $n,
        '<input type="text" class="width-100 no-border" style="border:none; background-color:transparent !important;" value="'.$rs->product_code.' : '.$rs->product_name.'" disabled/>',
        number($rs->qty)
      );

      $total_qty += $rs->qty;
    }
    else
    {
      $arr = array('','<input type="text" class="width-100 no-border" style="border:none; background-color:transparent !important;" value="" disabled />','');
    }


    $sc .= $this->printer->print_row($arr);

    $i++;
    $x++;
    $n++;
  } //--- end while $i < $row

  //--- ปิดหัวตาราง
  $sc .= $this->printer->table_end();

  $qty = $this->printer->current_page == $this->printer->total_page ? number($total_qty) : '';


  $sub  = '<td class="subtotal-first subtotal-last" style="height:20mm; font-size:36px; ">';
  $sub .= '<span class="width-50 pull-left blod">Weight  : ' . number($weight, 2) . ' Kgs.</span>';
  $sub .= '<span class="width-50 pull-right blod text-right">Total  : '.number($total_qty).'</span>';
  $sub .= '</td>';

  $sub2  = '<td class="subtotal-first subtotal-last font-size-14" style="height:'.($this->printer->row_height *2).'mm;">';
  $sub2 .= 'Remark : '.$order->remark;
  $sub2 .= '</td>';

  $sub3  = '<td class="subtotal-first subtotal-last font-size-14 text-right" style="height:'.($this->printer->row_height).'mm;">';
  $sub3 .= 'Printed by : '.$this->_user->uname. '  Date : '.date('d/m/Y H:i');
  $sub3 .= '</td>';

  $sub_total = array(
    array($sub),
    array($sub2),
    array($sub3)
  );


  $sc .= $this->printer->print_sub_total($sub_total);


  $sc .= $this->printer->content_end();
  $sc .= $this->printer->page_end();
  $total_page--;
  $this->printer->current_page++;

} //--- end while total_page > 0

$sc .= $this->printer->doc_footer();
echo $sc;
endif;
?>

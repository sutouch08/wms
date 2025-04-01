<?php
$this->load->helper('print');
$sc = '';
//--- print HTML document header
$sc .= $this->printer->doc_header();

//--- Set Document title
$this->printer->add_title('Picking Order List');

$sc .= $this->printer->page_start();

if( ! empty($orders))
{
  $sc .= '<table class="table table-bordered">';
  $col = 5;
  $c = 1;

  foreach($orders as $rs)
  {
    if($c == 1)
    {
      $sc .= '<tr>';
    }

    $sc .= '<td class="text-center width-20">
              <image src="data:image/png;base64,'.$rs->file.'" style="width:20mm;"/>
              <span class="display-block font-size-18">'.$rs->code.'</span>
              <span class="display-block font-size-18">'.$rs->channels.'</span>
            </td>';

    $c++;

    if($c > $col)
    {
      $sc .= '</tr>';
      $c = 1;
    }
  }
  $sc .= '</table>';
}

$sc .= $this->printer->page_end();

echo $sc;

 ?>

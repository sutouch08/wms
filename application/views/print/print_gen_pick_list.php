<?php
$this->load->helper('print');
$sc = '';
//--- print HTML document header
$sc .= $this->printer->doc_header();

//--- Set Document title
$this->printer->add_title('Packing List');

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

if( ! empty($items))
{
  $no = 1;
  $sc .= '<table class="table table-bordered">';
  $sc .= '<thead>
            <tr>
              <th class="fix-width-40 text-center">#</th>
              <th class="fix-width-200">SKU</th>
              <th class="fix-width-120">Barcode</th>
              <th class="fix-width-100 text-center">Qty</th>
              <th class="min-width-100">Stock In Zone</th>
            </tr>
          </thead>';

  foreach($items as $rs)
  {
    $sc .= '<tr>
              <td class="text-center">'.$no.'</td>
              <td>'.$rs->code.'</td>
              <td>'.$rs->barcode.'</td>
              <td class="text-center">'.number($rs->qty).'</td>
              <td>'.$rs->stock_in_zone.'</td>
            </tr>';
    $no++;
  }

  $sc .= '</table>';
}

$sc .= $this->printer->page_end();

echo $sc;

 ?>

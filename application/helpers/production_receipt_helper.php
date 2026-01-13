<?php
function production_receipt_status_text($status = 'P')
{
  $txt = [
    'P' => 'Draft',
    'R' => 'Released',
    'C' => 'Closed',
    'D' => 'Canceled'
  ];

  return empty($txt[$status]) ? 'Draft' : $txt[$status];
}


function receipt_status_color($status = 'P')
{
  $color = [
    'P' => '', //-- draft
    'R' => 'background-color:#fbe4ff;', //-- released
    'C' => 'background-color:#f4ffe7;', //-- closed
    'D' => 'background-color:#f7c3bf;'
  ];

  return empty($color[$status]) ? '' : $color[$status];
}

 ?>

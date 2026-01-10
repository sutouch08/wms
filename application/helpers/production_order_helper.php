<?php
function production_order_status_text($status = 'P')
{
  $txt = [
    'P' => 'Planned',
    'R' => 'Released',
    'C' => 'Closed',
    'D' => 'Canceled'
  ];

  return empty($txt[$status]) ? 'Planned' : $txt[$status];
}


function production_order_status_color($status = 'P')
{
  $color = [
    'P' => '', //-- draft
    'R' => 'background-color:#fbe4ff;', //-- released
    'C' => 'background-color:#f4ffe7;', //-- closed
    'D' => 'background-color:#f7c3bf;'
  ];

  return empty($color[$status]) ? '' : $color[$status];
}


function originTypeName($type = 'M')
{
  $txt = [
    'M' => 'Manual',
    'S' => 'Sales Order',
    'R' => 'MRP',
    'U' => 'Upgrade'
  ];

  return empty($txt[$type]) ? 'Manual' : $txt[$type];
}


function itemTypeName($type = '4')
{
  $type = empty($type) ? '4' : strval($type);

  $txt = [
    '4' => 'Item',
    '290' => 'Resource',
    '-18' => 'Text'
  ];

  return empty($txt[$type]) ? 'Item' : $txt[$type];
}


function get_ratio($Qty = 1)
{
  $ratio = $Qty > 0 ? (round(1 / $Qty, 2)) : 1;
  $ratio = $ratio > 1 ? "1/{$ratio}" : "1";

  return $ratio;
}

 ?>

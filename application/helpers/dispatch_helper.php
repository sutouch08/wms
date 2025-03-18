<?php
function dispatch_status($status = NULL)
{
  $ds = array(
    'P' => 'Pending',
    'R' => 'Released',
    'S' => 'Shipped',
    'C' => 'Closed',
    'D' => 'Canceled'
  );

  return empty($ds[$status]) ? 'Unknow' : $ds[$status];
}

 ?>

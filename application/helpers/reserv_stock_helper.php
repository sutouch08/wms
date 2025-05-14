<?php
  function reserv_stock_status_text($status = 'D')
  {
    $arr = array(
      'D' => 'Draft',
      'P' => 'Pending Approve',
      'A' => 'Approved',
      'R' => 'Rejected',
      'C' => 'Canceled'
    );

    return  empty($arr[$status]) ? 'Draft' : $arr[$status];
  }

 ?>

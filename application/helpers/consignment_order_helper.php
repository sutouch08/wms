<?php 
  function consignment_order_status_label($status = '0')
  {
    $label = [
      '0' => '<span class="blue">NC</span>',
      '2' => '<span class="red">CN</span>'
    ];

    return empty($label[$status]) ? "" : $label[$status];
  }

?>
<?php
  function receive_material_status_label($status = 'P')
  {
    $label = [
      'P' => '<span class="blue"><strong>DF</strong></span>',
      'O' => '<span class="purple"><strong>OP</strong></span>',
      'C' => '<span class="green"><strong>OK</strong></span>',
      'D' => '<span class="read"><strong>CN</strong></span>'
    ];

    return empty($label[$status]) ? 'Unknow' : $label[$status];
  }


  function receive_material_status_color($status = 'P')
  {
    $color = [
      'P' => '#ddf0f9;',
      'O' => '#fbe4ff',
      'C' => '#f4ffe7',
      'D' => '#f7c3bf'
    ];

    return empty($color[$status]) ? $color['P'] : $color[$status];
  }

  function receive_material_status_text($status = 'P')
  {
    $label = [
      'P' => 'Draft',
      'O' => 'Pending',
      'C' => 'Closed',
      'D' => 'Canceled'
    ];

    return empty($label[$status]) ? 'Unknow' : $label[$status];
  }
 ?>

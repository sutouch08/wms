<?php
function receive_po_status_label($status, $is_expire = 0)
{
  $label = array(
    '0' => '<span class="blue"><strong>DF</strong></span>',
    '1' => '<span class="green"><strong>OK</strong></span>',
    '2' => '<span class="red"><strong>CN</strong></span>',
    '3' => '<span class="purple"><strong>OP</strong></span>',
    '4' => '<span class="orange"><strong>WC</strong></span>'
  );

  if($is_expire && $status != 2)
  {
    return '<span class="dark"><strong>EXP</strong></span>';
  }

  return empty($label[$status]) ? '' : $label[$status];
}


function receive_po_status_text($status, $is_expire = 0)
{
  $label = array(
    '0' => 'Draft',
    '1' => 'Closed',
    '2' => 'Canceled',
    '3' => 'On Process',
    '4' => 'Acceptance'
  );

  if($is_expire && $status != 2)
  {
    return 'Expired';
  }

  return empty($label[$status]) ? '' : $label[$status];
}
?>
<?php
function select_payment_method($code = '')
{
  $sc = '';
  $CI =& get_instance();
  $CI->load->model('masters/payment_methods_model');
  $payments = $CI->payment_methods_model->get_data();
  if(!empty($payments))
  {
    foreach($payments as $rs)
    {
      $sc .= '<option value="'.$rs->code.'" '.is_selected($rs->code, $code).' data-role="'.$rs->role.'">'.$rs->name.'</option>';
    }
  }

  return $sc;
}

function select_payment_role($role = NULL)
{
  $ds  = '<option value="1" '.is_selected('1', $role).'>เงินสด</option>';
  $ds .= '<option value="2" '.is_selected('2', $role).'>เงินโอน</option>';
  $ds .= '<option value="3" '.is_selected('3', $role).'>บัตรเครดิต</option>';
  $ds .= '<option value="4" '.is_selected('4', $role).'>เก็บเงินปลายทาง</option>';
  $ds .= '<option value="5" '.is_selected('5', $role).'>เครดิตเทอม</option>';

  return $ds;
}


function payment_name_array($code)
{
  $ds = [];
  $ci =& get_instance();
  $ci->load->model('masters/payment_methods_model');
  $payments = $ci->payment_methods_model->get_data();

  if( ! empty($payments))
  {
    foreach($payments as $rs)
    {
      $ds[$rs->code] = $rs->name;
    }
  }

  return $ds;
}

function payment_role_name($role)
{
  $ds = array(
    '1' => 'เงินสด',
    '2' => 'เงินโอน',
    '3' => 'บัตรเครดิต',
    '4' => 'เก็บเงินปลายทาง',
    '5' => 'เครดิตเทอม'
  );

  return isset($ds[$role]) ? $ds[$role] : 'Unknow';
}

 ?>

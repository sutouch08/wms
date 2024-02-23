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
      $sc .= '<option value="'.$rs->code.'" '.is_selected($rs->code, $code).'>'.$rs->name.'</option>';
    }
  }

  return $sc;
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

 ?>

<?php
function select_channels($code = '')
{
  $sc = '';
  $CI =& get_instance();
  $CI->load->model('masters/channels_model');
  $channels = $CI->channels_model->get_data();
  if(!empty($channels))
  {
    foreach($channels as $rs)
    {
      $sc .= '<option value="'.$rs->code.'" '.is_selected($rs->code, $code).'>'.$rs->name.'</option>';
    }
  }

  return $sc;
}


function select_channels_type($code = NULL)
{
	$sc  = '<option value="WO" '.is_selected('WO', $code).'>WO</option>';
	$sc .= '<option value="WO-B2C" '.is_selected('WO-B2C', $code).'>WO-B2C</option>';
	$sc .= '<option value="WO-Made2Order" '.is_selected('WO-Made2Order', $code).'>WO-Made2Order</option>';

	return $sc;
}

function get_channels_array()
{
  $ci =& get_instance();
  $ci->load->model('masters/channels_model');
  return $ci->channels_model->get_channels_array();
}

function select_dispatch_channels($code = NULL)
{
  $sc = '';
  $ci =& get_instance();
  $ci->load->model('masters/channels_model');
  $channels = $ci->channels_model->get_all();

  if(!empty($channels))
  {
    foreach($channels as $rs)
    {
      $sc .= '<option value="'.$rs->code.'" data-role="S" data-name="'.$rs->name.'" '.is_selected($rs->code, $code).'>'.$rs->name.'</option>';
    }

    $sc .= '<option value="WU" data-role="U" data-name="เบิกอภินันท์" '.is_selected($code, 'WU').'>WU</option>';
    $sc .= '<option value="WS" data-role="P" data-name="สปอนเซอร์" '.is_selected($code, 'WS').'>WS</option>';
    $sc .= '<option value="WC" data-role="C" data-name="ฝากขายเทียม" '.is_selected($code, 'WC').'>WC</option>';
    $sc .= '<option value="WT" data-role="N" data-name="ฝากขายแท้" '.is_selected($code, 'WT').'>WT</option>';
    $sc .= '<option value="WQ" data-role="T" data-name="แปรสภาพ(ขาย)" '.is_selected($code, 'WQ').'>WQ</option>';
    $sc .= '<option value="WV" data-role="Q" data-name="แปรสภาพ(สต็อก)" '.is_selected($code, 'WV').'>WV</option>';
    $sc .= '<option value="WW" data-role="L" data-name="ยืมสินค้า" '.is_selected($code, 'WL').'>WL</option>';
  }

  return $sc;
}
 ?>

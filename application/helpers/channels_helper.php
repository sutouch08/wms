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
 ?>

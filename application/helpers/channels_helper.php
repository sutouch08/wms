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
      if($rs->type_code == 'WO-B2C')
      {
        $sc .= '<option value="'.$rs->code.'" data-name="'.$rs->name.'" '.is_selected($rs->code, $code).'>'.$rs->name.'</option>';
      }
    }
  }

  return $sc;
}


function channels_name($code = NULL)
{
  $ci =& get_instance();
  $ci->load->model('masters/channels_model');

  return $ci->channels_model->get_name($code);
}


function channels_array()
{
  $ds = [];
  $ci =& get_instance();
  $ci->load->model('masters/channels_model');

  $channels = $ci->channels_model->get_all();

  if( ! empty($channels))
  {
    foreach($channels as $rs)
    {
      $ds[$rs->code] = $rs->name;
    }
  }

  return $ds;
}

function online_channels_array()
{
  $arr = ['x'];
  $ci =& get_instance();
  $ci->load->model('masters/channels_model');
  $qr = "SELECT code FROM channels WHERE is_online = 1";
  $chs = $ci->db->query($qr); 
  
  if($chs->num_rows() > 0)
  {
    foreach($chs->result() as $r)
    {
      $arr[] = $r->code;
    }
  }

  return $arr;
}

function offline_channels_array()
{
  $arr = ['x'];
  $ci =& get_instance();
  $ci->load->model('masters/channels_model');
  $qr = "SELECT code FROM channels WHERE is_online = 0 OR is_online IS NULL";
  $chs = $ci->db->query($qr); 

  if($chs->num_rows() > 0)
  {
    foreach($chs->result() as $r)
    {
      $arr[] = $r->code;
    }
  }

  return $arr;
}
 ?>

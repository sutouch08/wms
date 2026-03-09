<?php
function select_unit($code = '')
{
  $sc = '';
  $CI =& get_instance();
  $CI->load->model('masters/unit_model');
  $options = $CI->unit_model->get_data(); //--- OUOM

  if(!empty($options))
  {
    foreach($options as $rs)
    {
      $sc .= '<option value="'.$rs->code.'" data-id="'.$rs->id.'" data-group="'.$rs->group_id.'" '.is_selected($code, $rs->code).'>'.$rs->code.' | '.$rs->name.'</option>';
    }
  }

  return $sc;
}


function unit_array()
{
  $ds = [];

  $ci =& get_instance();
  $ci->load->model('masters/unit_model');
  $data = $ci->unit_model->get_data();

  if( ! empty($data))
  {
    foreach($data as $rs)
    {
      $ds[$rs->code] = ['code' => $rs->code, 'id' => $rs->id, 'group' => $rs->group_id, 'name' => $rs->name];
    }
  }

  return $ds;
}

 ?>

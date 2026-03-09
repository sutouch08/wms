<?php
function select_item_group($code = NULL)
{
  $sc = '';
  $CI =& get_instance();
  $CI->load->model('masters/item_group_model');
  $options = $CI->item_group_model->get_data(); //--- OUOM

  if(!empty($options))
  {
    foreach($options as $rs)
    {
      $sc .= '<option value="'.$rs->code.'" '.is_selected(strval($code), strval($rs->code)).'>'.$rs->code.' | '.$rs->name.'</option>';
    }
  }

  return $sc;
}


function item_group_array()
{
  $ds = [];
  $ci =& get_instance();
  $ci->load->model('masters/item_group_model');
  $data = $ci->item_group_model->get_data();

  if( ! empty($data))
  {
    foreach($data as $rs)
    {
      $ds[$rs->name] = ['code' => $rs->code, 'name' => $rs->name];
    }
  }

  return $ds;
}


 ?>

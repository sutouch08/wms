<?php

function select_cancel_reason($id = NULL)
{
  $ds = "";
  $ci =& get_instance();
  $ci->load->model('masters/cancel_reason_model');

  $list = $ci->cancel_reason_model->get_all_active();

  if( ! empty($list))
  {
    foreach($list as $rs)
    {
      $ds .= '<option value="'.$rs->id.'" '.is_selected($rs->id, $id).'>'.$rs->name.'</option>';
    }
  }

  return $ds;
}
 ?>

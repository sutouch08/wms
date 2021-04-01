<?php

function select_saleman($id = "")
{
  $CI =& get_instance();
  $CI->load->model('masters/slp_model');
  $result = $CI->slp_model->get_data();
  $ds = '';
  if(!empty($result))
  {
    foreach($result as $rs)
    {
      $ds .= '<option value="'.$rs->id.'" '.is_selected($rs->id, $id).'>'.$rs->name.'</option>';
    }
  }

  return $ds;
}
 ?>

<?php
function select_active_package($id = NULL)
{
  $ds = "";
  $ci =& get_instance();
  $ci->load->model('masters/package_model');
  $active = 1;
  $id = empty($id) ? getConfig('DEFAULT_PACKAGE') : $id;

  $options = $ci->package_model->get_all($active);

  if( ! empty($options))
  {
    foreach($options as $rs)
    {
      $ds .= '<option value="'.$rs->id.'" '.is_selected($rs->id, $id).'>'.$rs->name.' | '.$rs->width.'x'.$rs->length.'x'.$rs->height.'</option>';
    }
  }

  return $ds;
}

 ?>

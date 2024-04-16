<?php
function select_active_collection($code = NULL)
{
  $CI =& get_instance();
  $CI->load->model('masters/product_collection_model');
  $active = 1;
  $result = $CI->product_collection_model->get_all();
  $ds = '';
  if(!empty($result))
  {
    foreach($result as $rs)
    {
      if($rs->active == 1 OR $rs->code == $code)
      {
        $ds .= '<option value="'.$rs->code.'" '.is_selected($rs->code, $code).'>'.$rs->name.'</option>';        
      }
    }
  }

  return $ds;
}


function select_all_collection($code = NULL)
{
  $CI =& get_instance();
  $CI->load->model('masters/product_collection_model');
  $result = $CI->product_collection_model->get_all();
  $ds = '';
  if(!empty($result))
  {
    foreach($result as $rs)
    {
      $ds .= '<option value="'.$rs->code.'" '.is_selected($rs->code, $code).'>'.$rs->name.'</option>';
    }
  }

  return $ds;
}


 ?>

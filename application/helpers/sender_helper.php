<?php
function sender_in($txt)
{
  $sc = "9999999";
  $CI =& get_instance();
  $CI->load->model('masters/sender_model');
  $ds = $CI->sender_model->search($txt);

  if(!empty($ds))
  {
    foreach($ds as $rs)
    {
      $sc .= ", {$rs->id}";
    }
  }

  return $sc;
}


 ?>

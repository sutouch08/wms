<?php
function select_running($min = 3, $max = 6, $se = NULL)
{
  $option = "";
  $se = intval($se);
  
  while($min <= $max)
  {
    $option .= '<option value="'.$min.'" '.is_selected($min, $se).'>'.$min.'</option>';
    $min++;
  }

  return $option;
}

 ?>

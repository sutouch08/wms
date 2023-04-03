<?php
function setToken($token)
{
	$CI =& get_instance();
	$cookie = array(
		'name' => 'file_download_token',
		'value' => $token,
		'expire' => 3600,
		'path' => '/'
	);

	return $CI->input->set_cookie($cookie);
}


//---	ตัดข้อความแล้วเติม ... ข้างหลัง
function limitText($str, $length)
{
	$txt = '...';
	if( strlen($str) >= $length)
	{
		return mb_substr($str, 0, $length).$txt;
	}
	else
	{
		return $str;
	}
}




function is_selected($val, $select)
{
  return $val === $select ? 'selected' : '';
}




function is_checked($val1, $val2)
{
  return $val1 == $val2 ? 'checked' : '';
}



function is_active($val)
{
  return $val == 1 ? '<i class="fa fa-check green"></i>' : '<i class="fa fa-times red"></i>';
}




function get_filter($postName, $cookieName, $defaultValue = "")
{
  $CI =& get_instance();
  $sc = '';

  if($CI->input->post($postName) !== NULL)
  {
    $sc = trim($CI->input->post($postName));
    $CI->input->set_cookie(array('name' => $cookieName, 'value' => $sc, 'expire' => 3600 , 'path' => '/'));
  }
  else if($CI->input->cookie($cookieName) !== NULL)
  {
    $sc = $CI->input->cookie($cookieName);
  }
  else
  {
    $sc = $defaultValue;
  }

	return $sc;
}




function clear_filter($cookies)
{
  if(is_array($cookies))
  {
    foreach($cookies as $cookie)
    {
      delete_cookie($cookie);
    }
  }
  else
  {
    delete_cookie($cookies);
  }
}




function set_rows($value = 20)
{
  $value = $value > 300 ? 300 : $value;

  $arr = array(
    'name' => 'rows',
    'value' => $value,
    'expire' => 259200,
    'path' => '/'
  );

  return set_cookie($arr);
}





function get_rows()
{
  $rows = get_cookie('rows');

	return $rows <= 0 ? 20 : ($rows > 300 ? 300 : $rows);
}




function number($val, $digit = 0)
{
  return number_format($val, $digit);
}




function ac_format($val, $digit = 0)
{
	return $val == 0 ? '-' : number_format($val, $digit);
}


function getConfig($code)
{
  $CI =& get_instance();
  $rs = $CI->db->select('value')->where('code', $code)->get('config');
  if($rs->num_rows() == 1)
  {
    return $rs->row()->value;
  }

	return NULL;
}



function get_vat_amount($amount, $vat = NULL)
{
	if($vat === NULL)
	{
		$vat = getConfig('SALE_VAT_RATE');
	}

	$re_vat = ($amount * $vat) / (100+$vat);

	return round($re_vat,6);
}



function remove_vat($amount, $vat = NULL)
{
	if($vat === NULL)
	{
		$vat = getConfig('SALE_VAT_RATE'); //-- 7
	}

	if( $vat != 0 )
	{
		$re_vat	= ($vat + 100) / 100;
		return round($amount/$re_vat, 6);
	}

	return round($amount, 6);
}

//---- remove discount percent return price after discount
function get_price_after_discount($price, $disc = 0)
{
	$find = array('%', ' ');
	$replace = array('', '');
	$disc = str_replace($find, $replace, $disc);

	if($disc > 0 && $disc <= 100)
	{
		$price = $price - ($price *($disc * 0.01));
	}

	return $price;
}


//--- return discount amount calculate from price and discount percentage
function get_discount_amount($price, $disc = 0)
{
	$find = array('%', ' ');
	$replace = array('', '');
	$disc = str_replace($find, $replace, $disc);

	if($disc > 0 && $disc <= 100)
	{
		$amount = $price * ($disc * 0.01);
	}
	else
	{
		$amount = 0;
	}

	return $amount;
}




function add_vat($amount, $vat = NULL)
{
	if($vat === NULL)
	{
		$vat = getConfig('SALE_VAT_RATE'); //-- 7
	}

	if( $vat != 0 )
	{
		$re_vat = $vat * 0.01;
		return round(($amount * $re_vat) + $amount, 6);
	}

	return round($amount, 6);
}



function set_error($message)
{
  $CI =& get_instance();
  $CI->session->set_flashdata('error', $message);
}


function set_message($message)
{
  $CI =& get_instance();
  $CI->session->set_flashdata('success', $message);
}


//--- return null if blank value
function get_null($value)
{
	return $value === '' ? NULL : $value;
}

//--- return TRUE if value ==  1 else return FALSE;
function is_true($value)
{
	if($value === 1 OR $value === '1' OR $value === 'Y' OR $value === TRUE)
	{
		return TRUE;
	}

	return FALSE;
}


function get_zero($value)
{
	return $value === NULL ? 0 : $value;
}


function pagination_config( $base_url, $total_rows = 0, $perpage = 20, $segment = 3)
{
    $rows = get_rows();
    $input_rows  = '<p class="pull-right pagination hidden-xs">';
    $input_rows .= 'ทั้งหมด '.number($total_rows).' รายการ | แสดง';
    $input_rows .= '<input type="number" name="set_rows" id="set_rows" class="input-mini text-center margin-left-15 margin-right-10" value="'.$rows.'" />';
    $input_rows .= 'ต่อหน้า ';
    $input_rows .= '<buton class="btn btn-success btn-xs" type="button" onClick="set_rows()">แสดง</button>';
    $input_rows .= '</p>';

		$config['full_tag_open'] 		= '<nav><ul class="pagination">';
		$config['full_tag_close'] 		= '</ul>'.$input_rows.'</nav><hr>';
		$config['first_link'] 				= 'First';
		$config['first_tag_open'] 		= '<li>';
		$config['first_tag_close'] 		= '</li>';
		$config['next_link'] 				= 'Next';
		$config['next_tag_open'] 		= '<li>';
		$config['next_tag_close'] 	= '</li>';
		$config['prev_link'] 			= 'prev';
		$config['prev_tag_open'] 	= '<li>';
		$config['prev_tag_close'] 	= '</li>';
		$config['last_link'] 				= 'Last';
		$config['last_tag_open'] 		= '<li>';
		$config['last_tag_close'] 		= '</li>';
		$config['cur_tag_open'] 		= '<li class="active"><a href="#">';
		$config['cur_tag_close'] 		= '</a></li>';
		$config['num_tag_open'] 		= '<li>';
		$config['num_tag_close'] 		= '</li>';
		$config['uri_segment'] 		= $segment;
		$config['per_page']			= $perpage;
		$config['total_rows']			= $total_rows != false ? $total_rows : 0 ;
		$config['base_url']				= $base_url;
		return $config;
}


function convert($txt)
{
	//return iconv('UTF-8', 'CP850', $txt);
	return $txt;
}

function statusBackgroundColor($is_expire, $status, $is_approve = 1)
{
	$bk_color = "";

	if($is_expire == 1)
	{
		$bk_color = "#dbdbdb";
	}
	else
	{
		switch($status)
		{
			case -1 :
				$bk_color = "#fff4d5";
				break;
			case 0 :
				$bk_color = "#ddf0f9";
				break;
			case 1 :
				$bk_color = $is_approve == 1 ? "#f4ffe7" : "#ffe3b9";
				break;
			case 2 :
				$bk_color = "#f7c3bf";
				break;
			case 3 :
				$bk_color = "#fbe4ff";
				break;
      case 4 :
        $bk_color = "#ffe3b9";
        break;
		}
	}

	return "background-color:{$bk_color};";
}

 ?>

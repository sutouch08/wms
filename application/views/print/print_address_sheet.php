<?php

	/*********  Sender  ***********/
	$sender			= '<div class="col-lg-12" style="font-size:12px; padding-top:15px; padding-bottom:30px;">';
	$sender			.= '<span style="display:block;">'.$cName.'</span>';
	$sender			.= '<span style="width:70%; display:block;">'.$cAddress.' '.$cPostCode.'</span>';
	$sender			.= '<span style="display:block"> โทร. '.$cPhone.'</span>';
	$sender			.= '</div>';
	/********* / Sender *************/



	/*********** Receiver  **********/
	$receiver		= '<div class="col-lg-12" style="font-size:24px; padding-left: 100px; padding-right:100px; padding-top:15px; padding-bottom:40px;">';
	$receiver		.= '<span style="display:block; margin-bottom:10px;">'.$ad->name.'</span>';
	$receiver		.= '<span style="display:block;">'.$ad->address.'</span>';
	$receiver		.= '<span style="display:block;"> ต. '.$ad->sub_district.' อ. '.$ad->district.'</span>';
	$receiver		.= '<span style="display:block;">จ. '.$ad->province.' '.$ad->postcode.'</span>';
	$receiver		.= $ad->phone == '' ? '' : '<span style="display:block;">โทร. '.$ad->phone.'</span>';
	$receiver		.= '</div>';
	/********** / Receiver ***********/

	/********* Transport  ***********/
	$transport = '';

	if( $sd !== FALSE )
	{
		$transport	= '<table style="width:100%; border:0px; margin-left: 30px; position: relative; bottom:1px;">';
		$transport	.= '<tr style="font-18px;"><td>'. $sd->name .'</td></tr>';
		$transport	.= '<tr style="font-18px;"><td>'. $sd->address1 .' '.$sd->address2.'</td></tr>';
		$transport	.= '<tr style="font-18px;"><td>โทร. '. $sd->phone.' เวลาทำการ : '.date('H:i', strtotime($sd->open)).' - '.date('H:i', strtotime($sd->close)).' น. - ( '.$sd->type.')</td></tr>';
		$transport 	.= '</table>';
	}

	/*********** / transport **********/

	$total_page		= $boxes <= 1 ? 1 : ($boxes)/2;
	$Page = '';

	$config = array("row" => 16, "header_row" => 0, "footer_row" => 0, "sub_total_row" => 0);
	$this->printer->config($config);

	// $barcode	= "<img src='".base_url()."assets/barcode/barcode.php?text=".$reference."' style='height:15mm; margin-top:10px;' />";
	$barcode = '<image src="data:image/png;base64, '.$qrcode.'" class="pull-right" style="width:20mm;"/>';
	$Page .= $this->printer->doc_header();
	$n = 1;
	while($total_page > 0 )
	{
		$Page .= $this->printer->page_start();

		if( $n < ($boxes+1) )
		{
			$Page .= $this->printer->content_start();
			$Page .= '<div class="col-lg-6-harf col-md-6-harf col-sm-6-harf col-xs-6-harf padding-5">'.$sender.'</div>';
			$Page .= '<div class="col-lg-3-harf col-md-3-harf col-sm-3-harf col-xs-3-harf padding-5 text-right margin-top-10">
								<span class="font-size-24 pull-right text-right">'.$reference.'</span>
								<br/><span class="pull-right font-size-24">กล่องที่ '.$n.' / '.$boxes.'</span></div>';
			$Page .= '<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 text-right margin-top-10">'.$barcode.'</div>';
			$Page .= '<div class="divider-hidden"></div>';
			$Page .= $receiver;
			$Page .= $transport;
			$Page .= $this->printer->content_end();
			$n++;
		}

		if( $n < ($boxes+1) )
		{
			$Page .= $this->printer->content_start();
			$Page .= '<div class="col-lg-6-harf col-md-6-harf col-sm-6-harf col-xs-6-harf padding-5">'.$sender.'</div>';
			$Page .= '<div class="col-lg-3-harf col-md-3-harf col-sm-3-harf col-xs-3-harf padding-5 text-right margin-top-10">
								<span class="font-size-24 pull-right text-right">'.$reference.'</span>
								<br/><span class="pull-right font-size-24">กล่องที่ '.$n.' / '.$boxes.'</span></div>';
			$Page .= '<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 text-right margin-top-10">'.$barcode.'</div>';
			$Page .= '<div class="divider-hidden"></div>';
			$Page .= $receiver;
			$Page .= $transport;
			$Page .= $this->printer->content_end();
			$n++;
		}

		$Page .= $this->printer->page_end();

		$total_page--;
	}
	$Page .= $this->printer->doc_footer();
	echo $Page;

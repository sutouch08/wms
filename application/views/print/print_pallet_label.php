<?php
$this->load->helper('print');
$pageWidth = 210; //getConfig('PALLET_LABEL_WIDTH', 80);
$pageHeight = 297; //getConfig('PALLET_LABEL_HEIGHT', 80);
$contentWidth = 75; //getConfig('PALLET_LABEL_CONTENT_WIDTH', 75);
$fontSize = 24; //getConfig('PALLET_LABEL_FONT_SIZE', 24);
$currentPage = 1;
$totalPage = count($list);
?>
<!DOCTYPE html>
<html>
  <head>
  	<meta charset="utf-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1.0">
  	<link rel="icon" href="<?php echo base_url(); ?>assets/images/icons/favicon.ico" type="image/x-icon" />
  	<title><?php echo $this->title; ?></title>
  	<link href="<?php echo base_url(); ?>assets/fonts/fontawesome-5/css/all.css" rel="stylesheet" />
  	<link href="<?php echo base_url(); ?>assets/css/bootstrap.css" rel="stylesheet" />
  	<link href="<?php echo base_url(); ?>assets/css/template.css" rel="stylesheet" />
  	<link href="<?php echo base_url(); ?>assets/css/print.css" rel="stylesheet" />
  	<script src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script>
  	<script src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script>
    <style>
      .page_layout{
        border: solid 1px #aaa;
        border-radius:0px;
      }

      .content-table > tbody > tr {
        height:5mm;
      }
      .content-table > tbody > tr:last-child {
        height: auto;
      }

      @media print{
        .page_layout{ border: none; }
      }
    </style>
  	</head>
  	<body>
    	<div class="hidden-print" style="margin-top:10px; padding-bottom:10px; padding-right:5mm; width:200mm; margin-left:auto; margin-right:auto; text-align:right">
    	   <button class="btn btn-primary" onclick="print()"><i class="fa fa-print"></i>&nbspพิมพ์</button>
    	</div>
      <div style="width:100%">
        <?php if(! empty($list)) : ?>
          <?php foreach($list as $rs) : ?>
        <?php $pageBreak = ""; //($currentPage == $totalPage) ? "" : "page-break-after:always;"; ?>
        <!-- Page Start -->
    		<div class="page_layout" style="position:relative; width: <?php echo $pageWidth; ?>mm; padding-top:5mm; height:<?php echo $pageHeight; ?>mm; margin:auto; margin-bottom:10px; <?php echo $pageBreak; ?>">
          <div class="border-1 padding-5 text-center" style="width:80mm; height:80mm; float:left;">
            <image src="<?php echo $file; ?>" style="width:60mm;"/>
            <image src="<?php echo base_url().'assets/barcode/barcode.php?text='.$zone; ?>" style="width:75mm;" />
          </div>

          <div style="width:mm; margin:auto; padding-bottom:10px;">
            <table class="table" style="margin-bottom:0px;">
              <tr>
                <td class="text-center" style="font-size:<?php echo $fontSize; ?>px; border:0px; padding:0px;">
                  <image src="<?php echo base_url().$rs->file; ?>" style="width:60mm;"/>
                  <image src="<?php echo base_url().'assets/barcode/barcode.php?text='.$rs->code; ?>" style="width:75mm;" />
                </td>
              </tr>
            </table>
          </div>
        </div>
        <?php $currentPage++; ?>
        <?php endforeach; ?>
      <?php endif; //-- end if(!empty($boxes))?>
      </div>
    </body>
  </html>

<script>
//   $(document).ready(function () {
//     window.print();
// });
</script>

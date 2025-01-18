<?php $this->load->helper('print'); ?>
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
    .view-port {
      display: flex;
      flex-direction: column;
      flex-wrap: wrap;
      align-items:center;
    }

    .sticker {
      /* display: flex; */
      border:solid 1px #ddd;
      width: 100mm;
      height:80mm;
      padding-left: 2mm;
      padding-right: 2mm;
      padding-top: 1mm;
      padding-bottom: 1mm
    }

    .sticker-label {
      border:solid 1px #ccc;
      width:96mm;
      min-height:76mm;
      border-radius: 5px;
      padding:2mm;
    }

    .label-space {
      width:0;
      height:100%;
    }

    .sticker-content {
      width: 100%;
      height:100%;
      border:1px;
      border-style: dashed;
      border-color:rgba(3,169,244,0.5);
      font-size:8px;
      font-weight: bold;
    }

    @media print {
      .sticker {
        border:none;
      }

      .sticker-label {
        border:none;
      }

      .sticker-content {
        border:none;
      }
    }
    </style>
  	</head>
  	<body>
      <div class="hidden-print" style="height:50px;">&nbsp;</div>
      <div class="hidden-print text-center" style="margin-bottom:30px;">
        <button type="button" class="btn btn-lg btn-info btn-100" onclick="window.print()">พิมพ์</button>
      </div>
      <div class="col-lg-12 view-port">
    <?php if( ! empty($list)) : ?>
      <?php foreach($list as $rs) : ?>
        <div class="sticker">
          <div class="sticker-label">
            <div class="sticker-content">
              <table class="width-100">
                <tr><td class="text-center"><image src="<?php echo base_url().$rs->file; ?>" style="width:55mm;"/></td></tr>
                <tr><td class="text-center"><image src="<?php echo base_url().'assets/barcode/barcode.php?text='.$rs->code; ?>" style="width:70mm;" /></td></tr>
              </table>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
      </div>
    </body>
  </html>

<script>

</script>

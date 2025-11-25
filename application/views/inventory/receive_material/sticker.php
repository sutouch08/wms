
<!DOCTYPE html>
<html>
  <head>
  	<meta charset="utf-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="<?php echo base_url(); ?>assets/img/favicon.ico">
  	<title><?php echo $this->title; ?></title>
  	<link href="<?php echo base_url(); ?>assets/css/bootstrap.css" rel="stylesheet" />
  	<link href="<?php echo base_url(); ?>assets/css/template.css" rel="stylesheet" />
  	<link href="<?php echo base_url(); ?>assets/css/print.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto&family=Kanit:wght@200;300;400;500;600&display=swap" rel="stylesheet"/>
  	<script src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script>
  	<script src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script>
    <style>
    body { font-family:Roboto, Kanit;}
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
      height: 150mm;
      padding-left: 2mm;
      padding-right: 2mm;
      padding-top: 1mm;
      padding-bottom: 1mm
    }

    .sticker-label {
      border:solid 1px #ccc;
      width:96mm;
      min-height:146mm;
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
      font-size:12px;
      font-weight: normal;
    }

    td {
      padding:2px;
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
<?php
// $total = 20;
// $ds = [];
//
// $ds[] = (object)[
//   'pcsNo' => 1,
//   'grade' => 'B',
//   'po' => 'POK-2505014',
//   'lot' => '195',
//   'sku' => 'MF-PL-PA1601-PR6507072ALT',
//   'barcode' => '8850000200575',
//   'qty' => 19.22,
//   'unit' => 'kgs.'
// ];

?>




      <div class="hidden-print" style="height:50px;">&nbsp;</div>
      <div class="hidden-print text-center" style="margin-bottom:30px;">
        <button type="button" class="btn btn-lg btn-info btn-100" onclick="window.print()">พิมพ์</button>
      </div>
      <div class="col-lg-12 view-port">
    <?php if( ! empty($ds)) : ?>
      <?php foreach($ds as $rs) : ?>
        <div class="sticker">
          <div class="sticker-label">
            <div class="sticker-content">
              <table class="width-100">
                <tr><td class="text-center font-size-18 bold"><?php echo $rs->po.'/'.$rs->lot; ?></td></tr>
                <tr>
                  <td class="text-center">
                    <image src="<?php echo base_url().'assets/barcode/barcode.php?text='.$rs->po.'&font_size=0'; ?>" style="height:15mm;" />
                  </td>
                </tr>
                <tr><td class="text-center">&nbsp;</td></tr>
                <tr><td class="text-center font-size-18 bold"><?php echo $rs->sku; ?></td></tr>
                <tr>
                  <td class="text-center">
                    <image src="<?php echo base_url().'assets/barcode/barcode.php?text='.$rs->barcode.'&font_size=16'; ?>" style="height:15mm;" />
                  </td>
                </tr>
                <tr><td class="text-center">&nbsp;</td></tr>
                <tr><td class="text-center font-size-18 bold">พับที่ <?php echo $rs->pcsNo; ?>/<?php echo $total; ?> - Grade (<?php echo $rs->grade; ?>) - นน. <?php echo $rs->qty . '  '.$rs->unit; ?></td></tr>
                <tr><td class="text-center">&nbsp;</td></tr>
                <tr>
                  <td class="text-center">
                    <?php $label = str_replace('-', '', $rs->po).'-'.(sprintf('%03d', $rs->pcsNo)).' | '.$rs->po.'/'.$rs->lot.' | '.$rs->qty; ?>
                    <image src="<?php echo base_url().'assets/barcode/barcode.php?text='.$label.'&font_size=0'; ?>" style="width:80mm; height:15mm;" />
                  </td>
                </tr>
                <tr><td class="text-center font-size-12 bold"><?php echo $label; ?></td></tr>
                <tr><td class="text-center">&nbsp;</td></tr>
                <tr><td class="text-center">วันที่พิมพ์  <?php echo thai_date(now(), FALSE, '/'); ?></td></tr>
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

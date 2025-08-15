<?php $this->load->helper('print'); ?>
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
      <div class="hidden-print" style="height:50px;">&nbsp;</div>
      <div class="hidden-print text-center" style="margin-bottom:30px;">
        <button type="button" class="btn btn-lg btn-info btn-100" onclick="window.print()">พิมพ์</button>
      </div>
      <div class="col-lg-12 view-port">
    <?php if( ! empty($packages)) : ?>
      <?php foreach($packages as $rs) : ?>
        <?php
            $addr = (object) array(
              'address' => $rs->address,
              'sub_district' => $rs->sub_district
            );
        ?>
        <div class="sticker">
          <div class="sticker-label">
            <div class="sticker-content">
              <table class="width-100">
                <tr><td>ผู้รับ : <?php echo $rs->receiver; ?></td></tr>
                <tr><td>ที่อยู่ : <?php echo parse_address($addr); ?></td></tr>
                <tr>
                  <td>
                    <span class="pull-left"><strong><?php echo parseDistrict($rs->district, $rs->province); ?></strong></span>
                    <span class="pull-right"><strong><?php echo parseProvince($rs->province); ?></strong></span>
                  </td>
                </tr>
                <tr><td class="text-center">Tel : <?php echo $rs->phone; ?></td></tr>
                <tr><td class="text-center">&nbsp;</td></tr>
                <tr><td>ผู้ส่ง : <?php echo $rs->sender_name; ?></td></tr>
                <tr><td style="border-bottom:solid 2px #000;">&nbsp;</td></tr>
                <tr><td style="padding-top:10px;">รหัส : <?php echo $rs->sender_code; ?></td></tr>
                <tr><td class="text-center">&nbsp;</td></tr>
                <tr><td>ลำดับ <?php echo $rs->box_no; ?></td></tr>
                <tr><td class="text-center"><image src="<?php echo base_url().'images/PorlorLogoBW.png'; ?>" style="width:30mm;"/></td></tr>
                <tr>
                  <td class="text-center" style="padding-top:10px;">
                    <image src="<?php echo base_url().'assets/barcode/barcode.php?text='.$rs->tracking_no.'&font_size=0'; ?>" style="width:80mm; height:10mm;" />
                  </td>
                </tr>
                <tr><td class="text-center"><?php echo $rs->tracking_no; ?></td></tr>
                <tr><td class="text-center">&nbsp;</td></tr>
                <tr><td class="text-center">Kt./Kg. <?php echo $rs->weight; ?></td></tr>
                <tr><td class="text-center">ซ.ม. <?php echo "{$rs->package_length} * {$rs->package_width} * {$rs->package_height}"; ?></td></tr>
                <tr><td class="text-center"><?php echo $rs->receiver; ?></td></tr>
                <tr><td>วันที่พิมพ์  <?php echo thai_date(now(), FALSE, '/'); ?></td></tr>
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

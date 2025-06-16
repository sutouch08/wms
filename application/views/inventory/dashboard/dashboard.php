<!DOCTYPE html>
<html lang="th">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="utf-8" />
		<title><?php echo $this->title; ?></title>
		<meta name="description" content="" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
		<link rel="shortcut icon" href="<?php echo base_url(); ?>assets/img/favicon.ico">
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap.css" />
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/font-awesome.css" />
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/ace-fonts.css" />
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/ace.css" class="ace-main-stylesheet" id="main-ace-style" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/jquery-ui-1.10.4.custom.min.css " />
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/template.css?v=<?php echo date('Ymd'); ?>"/>
		<!-- ace settings handler -->
		<script src="<?php echo base_url(); ?>assets/js/ace-extra.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script>
  	<script src="<?php echo base_url(); ?>assets/js/jquery-ui-1.10.4.custom.min.js"></script>
	  <script src="<?php echo base_url(); ?>assets/js/bootstrap.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/ace/ace.js"></script>
		<script src="<?php echo base_url(); ?>assets/js/ace-elements.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/ace/elements.fileinput.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/sweet-alert.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/handlebars-v3.js"></script>
		<script src="<?php echo base_url(); ?>assets/js/select2.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/chosen.jquery.js"></script>
	  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/sweet-alert.css">
    <style>
    .head-line {
      font-size:1.5vw;
      text-align: center;
      font-weight: bolder;
      writing-mode: vertical-lr;
    }

    .h-box {
      width: 16.5%;
      text-align: center;
      color: #333333;
      font-size: 2vw;
    }

    .v-box {
      text-align: center;
      color: #333333;
      font-size: 2vw;
      vertical-align:middle !important;
    }


    .h-box.i-3 { background-color: #a59df3;}
    .h-box.i-4 { background-color: #FBB57F;}
    .h-box.i-5 { background-color: #d990ef;}
    .h-box.i-6 { background-color: #13b161;}
    .h-box.i-7 { background-color: #e7a9cd;}
    .h-box.i-8 { background-color: #92cd88;}

    .v-box.i-3 { background-color: #d3cffb;}
    .v-box.i-4 { background-color: #ffd3b1;}
    .v-box.i-5 { background-color: #e7bdf3;}
    .v-box.i-6 { background-color: #89e1b5;}
    .v-box.i-7 { background-color: #e9c9dc;}
    .v-box.i-8 { background-color: #cfedca;}

    .v-box.i-32 { background-color: #ADA9D4;}
    .v-box.i-42 { background-color: #efba92;}
    .v-box.i-52 { background-color: #e1adef;}
    .v-box.i-62 { background-color: #6ed9a3;}
    .v-box.i-72 { background-color: #e3b4d0;}
    .v-box.i-82 { background-color: #bce1b6;}
    </style>
	</head>
  <body>
    <div id="loader">
      <div class="loader"></div>
    </div>

    <div id="loader-backdrop" style="position: fixed; width:100vw; height:100vh; background-color:white; opacity:0.3; display:none; z-index:9;"></div>

    <div class="main-container" id="main-container">
      <div class="main-content">
        <div class="main-content-inner">
          <div class="page-content">
            <div class="row">
              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 text-center">
                <h1>Dashboard</h1>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
                <table class="table no-boder">
                  <tr>
                    <td class="head-line i-3">
                      &nbsp;
                    </td>
                    <td class="h-box i-3">รอจัด</td>
                    <td class="h-box i-4">กำลังจัด</td>
                    <td class="h-box i-5">รอตรวจ</td>
                    <td class="h-box i-6">กำลังตรวจ</td>
                    <td class="h-box i-7">รอส่ง</td>
                    <td class="h-box i-8">ส่งแล้ว</td>
                  </tr>
                  <tr>
                    <td class="head-line i-3">OFFLINE</td>
                    <td class="v-box i-3">2,000</td>
                    <td class="v-box i-4">2,000</td>
                    <td class="v-box i-5">2,000</td>
                    <td class="v-box i-6">2,000</td>
                    <td class="v-box i-7">2,000</td>
                    <td class="v-box i-8">2,000</td>
                  </tr>
                  <tr>
                    <td class="head-line">ONLINE</td>
                    <td class="v-box i-32">2,000</td>
                    <td class="v-box i-42">2,000</td>
                    <td class="v-box i-52">2,000</td>
                    <td class="v-box i-62">2,000</td>
                    <td class="v-box i-72">2,000</td>
                    <td class="v-box i-82">2,000</td>
                  </tr>
                  <tr>
                    <td class="head-line">TIKTOK</td>
                    <td class="v-box i-3">2,000</td>
                    <td class="v-box i-4">2,000</td>
                    <td class="v-box i-5">2,000</td>
                    <td class="v-box i-6">2,000</td>
                    <td class="v-box i-7">2,000</td>
                    <td class="v-box i-8">2,000</td>
                  </tr>
                  <tr>
                    <td class="head-line">SHOPEE</td>
                    <td class="v-box i-32">2,000</td>
                    <td class="v-box i-42">2,000</td>
                    <td class="v-box i-52">2,000</td>
                    <td class="v-box i-62">2,000</td>
                    <td class="v-box i-72">2,000</td>
                    <td class="v-box i-82">2,000</td>
                  </tr>
                  <tr>
                    <td class="head-line">LAZADA</td>
                    <td class="v-box i-3">2,000</td>
                    <td class="v-box i-4">2,000</td>
                    <td class="v-box i-5">2,000</td>
                    <td class="v-box i-6">2,000</td>
                    <td class="v-box i-7">2,000</td>
                    <td class="v-box i-8">2,000</td>
                  </tr>
                </table>
              </div>
            </div>
          </div>
          <div class="footer hidden-print">
            <div class="footer-inner">
              <!-- #section:basics/footer -->
              <div class="footer-content">
                <span class="bigger-120 orange">
                  &copy; <?php echo getConfig('COMPANY_FULL_NAME');?>
                </span>
                <a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
                  <i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script src="<?php echo base_url(); ?>assets/js/ace/ace.sidebar.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/ace/ace.sidebar-scroll-1.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/ace/ace.submenu-hover.js"></script>
    <script src="<?php echo base_url(); ?>scripts/beep.js"></script>
    <script src="<?php echo base_url(); ?>scripts/template.js?v=2<?php echo date('Ymd'); ?>"></script>
    <script>

    </script>
  </body>
  </html>

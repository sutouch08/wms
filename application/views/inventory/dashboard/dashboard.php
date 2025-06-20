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
			vertical-align:middle !important;
      /* writing-mode: vertical-lr; */
    }

    .h-box {
      width: 16.5%;
      text-align: center;
      color: #333333;
      font-size: 2vw;
			vertical-align:middle !important;
    }

    .v-box {
			position: relative;
			min-height: 200px !important;
      text-align: center;
      color: #333333;
      font-size: 2vw;
      vertical-align:middle !important;
    }

		.pre-load {
			margin:0;
			position: absolute;
			top: 50%;
			right: 0;
			-ms-transform: translate(-50%, -50%);
  		transform: translate(-50%, -50%);
		}

		.load-out {
			transition: opacity 0.5s ease-in-out;
			opacity: 0;
		}

		.load-in {
			transition: opacity 0.5s ease-in-out;
			opacity: 0.6;
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
		.total { background-color: #3f3e43; color: white;}
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
              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <table class="table border-1">
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
                    <td class="v-box i-3"><span id="offline-3">-</span><div class="pre-load load-out" id="pre-load-offline-3"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-4"><span id="offline-4">-</span><div class="pre-load load-out" id="pre-load-offline-4"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-5"><span id="offline-5">-</span><div class="pre-load load-out" id="pre-load-offline-5"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-6"><span id="offline-6">-</span><div class="pre-load load-out" id="pre-load-offline-6"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-7"><span id="offline-7">-</span><div class="pre-load load-out" id="pre-load-offline-7"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-8"><span id="offline-8">-</span><div class="pre-load load-out" id="pre-load-offline-8"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                  </tr>
                  <tr>
                    <td class="head-line">ONLINE</td>
                    <td class="v-box i-32"><span id="online-3">-</span><div class="pre-load load-out" id="pre-load-online-3"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-42"><span id="online-4">-</span><div class="pre-load load-out" id="pre-load-online-4"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-52"><span id="online-5">-</span><div class="pre-load load-out" id="pre-load-online-5"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-62"><span id="online-6">-</span><div class="pre-load load-out" id="pre-load-online-6"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-72"><span id="online-7">-</span><div class="pre-load load-out" id="pre-load-online-7"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-82"><span id="online-8">-</span><div class="pre-load load-out" id="pre-load-online-8"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                  </tr>
									<tr>
                    <td class="head-line total">Total</td>
                    <td class="v-box total"><span id="total-3">-</span></td>
                    <td class="v-box total"><span id="total-4">-</span></td>
                    <td class="v-box total"><span id="total-5">-</span></td>
                    <td class="v-box total"><span id="total-6">-</span></td>
                    <td class="v-box total"><span id="total-7">-</span></td>
                    <td class="v-box total"><span id="total-8">-</span></td>
                  </tr>
                  <tr>
                    <td class="head-line">TIKTOK</td>
                    <td class="v-box i-3"><span id="tiktok-3">-</span><div class="pre-load load-out" id="pre-load-tiktok-3"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-4"><span id="tiktok-4">-</span><div class="pre-load load-out" id="pre-load-tiktok-4"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-5"><span id="tiktok-5">-</span><div class="pre-load load-out" id="pre-load-tiktok-5"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-6"><span id="tiktok-6">-</span><div class="pre-load load-out" id="pre-load-tiktok-6"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-7"><span id="tiktok-7">-</span><div class="pre-load load-out" id="pre-load-tiktok-7"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-8"><span id="tiktok-8">-</span><div class="pre-load load-out" id="pre-load-tiktok-8"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                  </tr>
                  <tr>
                    <td class="head-line">SHOPEE</td>
                    <td class="v-box i-32"><span id="shopee-3">-</span><div class="pre-load load-out" id="pre-load-shopee-3"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-42"><span id="shopee-4">-</span><div class="pre-load load-out" id="pre-load-shopee-4"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-52"><span id="shopee-5">-</span><div class="pre-load load-out" id="pre-load-shopee-5"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-62"><span id="shopee-6">-</span><div class="pre-load load-out" id="pre-load-shopee-6"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-72"><span id="shopee-7">-</span><div class="pre-load load-out" id="pre-load-shopee-7"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-82"><span id="shopee-8">-</span><div class="pre-load load-out" id="pre-load-shopee-8"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                  </tr>
                  <tr>
                    <td class="head-line">LAZADA</td>
                    <td class="v-box i-3"><span id="lazada-3">-</span><div class="pre-load load-out" id="pre-load-lazada-3"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-4"><span id="lazada-4">-</span><div class="pre-load load-out" id="pre-load-lazada-4"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-5"><span id="lazada-5">-</span><div class="pre-load load-out" id="pre-load-lazada-5"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-6"><span id="lazada-6">-</span><div class="pre-load load-out" id="pre-load-lazada-6"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-7"><span id="lazada-7">-</span><div class="pre-load load-out" id="pre-load-lazada-7"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-8"><span id="lazada-8">-</span><div class="pre-load load-out" id="pre-load-lazada-8"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
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
		<input type="hidden" id="refresh-time-ms" value="60000" />
    <script src="<?php echo base_url(); ?>assets/js/ace/ace.sidebar.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/ace/ace.sidebar-scroll-1.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/ace/ace.submenu-hover.js"></script>
    <script src="<?php echo base_url(); ?>scripts/beep.js"></script>
    <script src="<?php echo base_url(); ?>scripts/template.js?v=2<?php echo date('Ymd'); ?>"></script>
    <script>
			const HOME = '<?php echo $this->home; ?>/';
			const syncChannels = ['offline', 'online', 'tiktok', 'shopee', 'lazada'];
			const syncState = ['3', '4', '5', '6', '7', '8'];

			window.addEventListener('load', () => {
				let delay = parseDefault(parseInt($('#refresh-time-ms').val()), 300000); //--- refresh every 5 minutes
				getData();
				var sync = setInterval(function() {
					getData();
				}, delay);
			});

			function getData() {
				syncState.forEach(function(state) {
					syncChannels.forEach(function(channels) {
						console.log('channels : '+channels + ', state : '+state);
						count_orders(channels, state);
					})
				})
			}


			function count_orders(channels, state) {
				let preload = $('#pre-load-'+channels+'-'+state);
				preload.addClass('load-in');

				setTimeout(() => {
					$.ajax({
						url:HOME + 'count_orders',
						type:'GET',
						cache:false,
						data:{
							'channels' : channels,
							'state' : state
						},
						success:function(rs) {
							preload.removeClass('load-in');

							if(isJson(rs)) {
								let ds = JSON.parse(rs);

								if(ds.status == 'success') {
									let rows = parseDefault(parseInt(ds.rows), 0);
									rows = rows > 0 ? addCommas(rows) : '-';
									$('#'+channels+'-'+state).text(rows);

									if(channels == 'offline' || channels == 'online') {
										setTimeout(() => {
											calcTotal(state);
										}, 200);
									}
								}
							}
						},
						error:function(rs) {

						}
					})
				}, 1000);
			}

			function calcTotal(state) {
				let offline = parseDefault(parseInt(removeCommas($('#offline-'+state).text())), 0);
				let online = parseDefault(parseInt(removeCommas($('#online-'+state).text())), 0);
				let total = offline + online;
				total = total > 0 ? addCommas(total) : '-';

				$('#total-'+state).text(total);
			}
    </script>
  </body>
  </html>

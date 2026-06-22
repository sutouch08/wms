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
		<?php $this->load->view('inventory/dashboard/style'); ?>
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
              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
                <h3 class="title" style="font-size:2.5vw;">Inventory Dashboard</h3>
								<div class="toggle-header">
									<a class="toggle-header-icon" onclick="showSetting()"><i class="fa fa-cogs fa-2x"></i></a>
								</div>
              </div>
            </div>
						<?php $this->load->view('inventory/dashboard/setting_panel'); ?>
            <hr>
            <div class="row">
              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <table class="table border-1">
                  <tr>
                    <td class="width-20">
                      &nbsp;
                    </td>
										<td class="h-box i-0 c-0">Back</td>
                    <td class="h-box i-3 c-3">รอจัด</td>
                    <td class="h-box i-4 c-4">กำลังจัด</td>
                    <td class="h-box i-5 c-5">รอตรวจ</td>
                    <td class="h-box i-6 c-6">กำลังตรวจ</td>
                    <td class="h-box i-7 c-7">รอส่ง</td>
                    <td class="h-box i-8 c-8">ส่งแล้ว</td>
                  </tr>
                  <tr id="r-offline">
                    <td class="head-line">OFFLINE</td>
										<td class="v-box i-0 c-0"><span id="offline-0">-</span><div class="pre-load load-out" id="pre-load-offline-0"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-3 c-3"><span id="offline-3">-</span><div class="pre-load load-out" id="pre-load-offline-3"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-4 c-4"><span id="offline-4">-</span><div class="pre-load load-out" id="pre-load-offline-4"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-5 c-5"><span id="offline-5">-</span><div class="pre-load load-out" id="pre-load-offline-5"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-6 c-6"><span id="offline-6">-</span><div class="pre-load load-out" id="pre-load-offline-6"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-7 c-7"><span id="offline-7">-</span><div class="pre-load load-out" id="pre-load-offline-7"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-8 c-8"><span id="offline-8">-</span><div class="pre-load load-out" id="pre-load-offline-8"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                  </tr>
                  <tr id="r-online">
                    <td class="head-line">ONLINE</td>
										<td class="v-box i-02 c-0"><span id="online-0">-</span><div class="pre-load load-out" id="pre-load-online-0"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-32 c-3"><span id="online-3">-</span><div class="pre-load load-out" id="pre-load-online-3"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-42 c-4"><span id="online-4">-</span><div class="pre-load load-out" id="pre-load-online-4"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-52 c-5"><span id="online-5">-</span><div class="pre-load load-out" id="pre-load-online-5"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-62 c-6"><span id="online-6">-</span><div class="pre-load load-out" id="pre-load-online-6"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-72 c-7"><span id="online-7">-</span><div class="pre-load load-out" id="pre-load-online-7"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-82 c-8"><span id="online-8">-</span><div class="pre-load load-out" id="pre-load-online-8"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                  </tr>
									<tr id="r-total">
                    <td class="head-line total">Total</td>
										<td class="v-box total c-0"><span id="total-0">-</span></td>
                    <td class="v-box total c-3"><span id="total-3">-</span></td>
                    <td class="v-box total c-4"><span id="total-4">-</span></td>
                    <td class="v-box total c-5"><span id="total-5">-</span></td>
                    <td class="v-box total c-6"><span id="total-6">-</span></td>
                    <td class="v-box total c-7"><span id="total-7">-</span></td>
                    <td class="v-box total c-8"><span id="total-8">-</span></td>
                  </tr>
                  <tr id="r-tiktok">
                    <td class="head-line">TIKTOK</td>
										<td class="v-box i-0 c-0"><span id="tiktok-0">-</span><div class="pre-load load-out" id="pre-load-tiktok-0"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-3 c-3"><span id="tiktok-3">-</span><div class="pre-load load-out" id="pre-load-tiktok-3"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-4 c-4"><span id="tiktok-4">-</span><div class="pre-load load-out" id="pre-load-tiktok-4"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-5 c-5"><span id="tiktok-5">-</span><div class="pre-load load-out" id="pre-load-tiktok-5"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-6 c-6"><span id="tiktok-6">-</span><div class="pre-load load-out" id="pre-load-tiktok-6"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-7 c-7"><span id="tiktok-7">-</span><div class="pre-load load-out" id="pre-load-tiktok-7"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-8 c-8"><span id="tiktok-8">-</span><div class="pre-load load-out" id="pre-load-tiktok-8"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                  </tr>
                  <tr id="r-shopee">
                    <td class="head-line">SHOPEE</td>
										<td class="v-box i-02 c-0"><span id="shopee-0">-</span><div class="pre-load load-out" id="pre-load-shopee-0"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-32 c-3"><span id="shopee-3">-</span><div class="pre-load load-out" id="pre-load-shopee-3"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-42 c-4"><span id="shopee-4">-</span><div class="pre-load load-out" id="pre-load-shopee-4"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-52 c-5"><span id="shopee-5">-</span><div class="pre-load load-out" id="pre-load-shopee-5"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-62 c-6"><span id="shopee-6">-</span><div class="pre-load load-out" id="pre-load-shopee-6"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-72 c-7"><span id="shopee-7">-</span><div class="pre-load load-out" id="pre-load-shopee-7"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-82 c-8"><span id="shopee-8">-</span><div class="pre-load load-out" id="pre-load-shopee-8"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                  </tr>
                  <tr id="r-lazada">
                    <td class="head-line">LAZADA</td>
										<td class="v-box i-0 c-0"><span id="lazada-0">-</span><div class="pre-load load-out" id="pre-load-lazada-0"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-3 c-3"><span id="lazada-3">-</span><div class="pre-load load-out" id="pre-load-lazada-3"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-4 c-4"><span id="lazada-4">-</span><div class="pre-load load-out" id="pre-load-lazada-4"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-5 c-5"><span id="lazada-5">-</span><div class="pre-load load-out" id="pre-load-lazada-5"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-6 c-6"><span id="lazada-6">-</span><div class="pre-load load-out" id="pre-load-lazada-6"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-7 c-7"><span id="lazada-7">-</span><div class="pre-load load-out" id="pre-load-lazada-7"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                    <td class="v-box i-8 c-8"><span id="lazada-8">-</span><div class="pre-load load-out" id="pre-load-lazada-8"><i class="fa fa-refresh fa-spin fa-fw"></i></div></td>
                  </tr>
                </table>
              </div>
            </div>
						<div class="divider"></div>

						<div class="row">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<table class="table table-bordered border-1">
									<tr>
										<td colspan="7" class="h-box i-8 text-center">จัดส่งแล้ว 7 วันล่าสุด</td>
									</tr>
									<tr>
										<td class="h-box i-8 text-center"><?php echo date('d/m', strtotime('-7 day')); ?></td>
										<td class="h-box i-8 text-center"><?php echo date('d/m', strtotime('-6 day')); ?></td>
										<td class="h-box i-8 text-center"><?php echo date('d/m', strtotime('-5 day')); ?></td>
										<td class="h-box i-8 text-center"><?php echo date('d/m', strtotime('-4 day')); ?></td>
										<td class="h-box i-8 text-center"><?php echo date('d/m', strtotime('-3 day')); ?></td>
										<td class="h-box i-8 text-center"><?php echo date('d/m', strtotime('-2 day')); ?></td>
										<td class="h-box i-8 text-center"><?php echo date('d/m', strtotime('-1 day')); ?></td>
									</tr>
									<tr>
										<td class="v-box i-8 text-center"><?php echo ac_format($d7); ?></td>
										<td class="v-box i-8 text-center"><?php echo ac_format($d6); ?></td>
										<td class="v-box i-8 text-center"><?php echo ac_format($d5); ?></td>
										<td class="v-box i-8 text-center"><?php echo ac_format($d4); ?></td>
										<td class="v-box i-8 text-center"><?php echo ac_format($d3); ?></td>
										<td class="v-box i-8 text-center"><?php echo ac_format($d2); ?></td>
										<td class="v-box i-8 text-center"><?php echo ac_format($d1); ?></td>
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
			const HOME = '<?php echo $this->home; ?>/';
			var syncChannels = []; //['offline', 'online', 'tiktok', 'shopee', 'lazada'];
			var syncState = []; //['0', '3', '4', '5', '6', '7', '8'];

			window.addEventListener('load', () => {

				let settings = localStorage.getItem('WarrixDashboard');

				if(settings === null || settings === undefined) {
					if( ! setDefault())
					{
						return false;
					}
					else
					{
						settings = localStorage.getItem('WarrixDashboard');
					}
				}


				if(settings !== null && settings !== undefined) {
					let conf = JSON.parse(settings);

					$('#refresh-time-ms').val(conf.refreshRate);
					let channels = conf.channels;
					let states = conf.state;
					let showTotal = 2;

					Object.entries(channels).forEach(([key, value]) => {
						toggleChannels(key, value);
						if(value == 1) {
							syncChannels.push(key);
						}
						else {
							$('#r-'+key).remove();

							if(key == 'offline' || key == 'online') {
								showTotal--;
							}
						}
					});

					if(showTotal <= 0) {
						$('#r-total').remove();
					}

					Object.entries(states).forEach(([key, value]) => {
						toggleState(key, value);
						if(value == 1) {
							syncState.push(key);
						}
						else {
							$('.c-'+key).remove();
						}
					});
				}

				let delay = parseDefault(parseInt($('#refresh-time-ms').val()), 120000);

				getData();
				var sync = setInterval(function() {
					getData();
				}, delay);
			});

			function getData() {
				syncState.forEach(function(state) {
					syncChannels.forEach(function(channels) {
						//console.log('channels : '+channels + ', state : '+state);
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


			function showSetting() {
				$('#setting-panel').addClass('move-in');
			}


			function closeSetting() {
				$('#setting-panel').removeClass('move-in');
			}


			function setDefault() {
				let setting = {
					"refreshRate" : 120000, //-- refresh reate in ms
					"channels" : {
						"offline" : 1,
						"online" : 1,
						"tiktok" : 1,
						"shopee" : 1,
						"lazada" : 1
					},
					"state" : {
						"0" : 1,
						"3" : 1,
						"4" : 1,
						"5" : 1,
						"6" : 1,
						"7" : 1,
						"8" : 1
					}
				}

		    localStorage.setItem('WarrixDashboard', JSON.stringify(setting));
			}


			function saveSetting() {
				let setting = {
					"refreshRate" : $('#refresh-time-ms').val(),
					"channels" : {
						"offline" : $('#setting-offline').val(),
						"online" : $('#setting-online').val(),
						"tiktok" : $('#setting-tiktok').val(),
						"shopee" : $('#setting-shopee').val(),
						"lazada" : $('#setting-lazada').val()
					},
					"state" : {
						"0" : $('#setting-state-0').val(),
						"3" : $('#setting-state-3').val(),
						"4" : $('#setting-state-4').val(),
						"5" : $('#setting-state-5').val(),
						"6" : $('#setting-state-6').val(),
						"7" : $('#setting-state-7').val(),
						"8" : $('#setting-state-8').val()
					}
				}

		    localStorage.setItem('WarrixDashboard', JSON.stringify(setting));

				closeSetting();
				load_in();

				setTimeout(() => {
					refresh();
				}, 1000);
			}


			function toggleOption(el) {
				let name = el.data('name');
				let option = el.is(':checked') ? 1 : 0;
				$("input[name='"+name+"']").val(option);
			}


			function toggleChannels(name, value) {
				let chk = value == 1 ? true : false;
				$('#setting-'+name).val(value);
				$('#channels-'+name).prop('checked', chk);
			}


			function toggleState(name, value) {
				let chk = value == 1 ? true : false;
				$('#setting-state-'+name).val(value);
				$('#state-'+name).prop('checked', chk);
			}

    </script>
  </body>
  </html>

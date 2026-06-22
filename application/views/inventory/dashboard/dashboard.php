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
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/template.css?v=<?php echo date('Ymd'); ?>" />
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
							<!-- <div class="toggle-header">
								<a class="toggle-header-icon" onclick="showSetting()"><i class="fa fa-cogs fa-2x"></i></a>
							</div> -->
						</div>
					</div>
					<?php //$this->load->view('inventory/dashboard/setting_panel'); 
					?>
					<hr>
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<table class="table border-1">
								<thead>
									<tr>
										<th class="width-20">
											&nbsp;
										</th>
										<th class="h-box i-0 c-0">Back</th>
										<th class="h-box i-3 c-3">รอจัด</th>
										<th class="h-box i-4 c-4">กำลังจัด</th>
										<th class="h-box i-5 c-5">รอตรวจ</th>
										<th class="h-box i-6 c-6">กำลังตรวจ</th>
										<th class="h-box i-7 c-7">รอส่ง</th>
										<th class="h-box i-8 c-8">ส่งแล้ว</th>
									</tr>
								</thead>
								<tbody id="data-table">
									<tr id="r-offline">
										<td class="head-line">OFFLINE</td>
										<td class="v-box i-0 c-0"><span id="offline-0"><?php echo $data->offline_0; ?></span></td>
										<td class="v-box i-3 c-3"><span id="offline-3"><?php echo $data->offline_3; ?></span></td>
										<td class="v-box i-4 c-4"><span id="offline-4"><?php echo $data->offline_4; ?></span></td>
										<td class="v-box i-5 c-5"><span id="offline-5"><?php echo $data->offline_5; ?></span></td>
										<td class="v-box i-6 c-6"><span id="offline-6"><?php echo $data->offline_6; ?></span></td>
										<td class="v-box i-7 c-7"><span id="offline-7"><?php echo $data->offline_7; ?></span></td>
										<td class="v-box i-8 c-8"><span id="offline-8"><?php echo $data->offline_8; ?></span></td>
									</tr>
									<tr id="r-online">
										<td class="head-line">ONLINE</td>
										<td class="v-box i-02 c-0"><span id="online-0"><?php echo $data->online_0; ?></span></td>
										<td class="v-box i-32 c-3"><span id="online-3"><?php echo $data->online_3; ?></span></td>
										<td class="v-box i-42 c-4"><span id="online-4"><?php echo $data->online_4; ?></span></td>
										<td class="v-box i-52 c-5"><span id="online-5"><?php echo $data->online_5; ?></span></td>
										<td class="v-box i-62 c-6"><span id="online-6"><?php echo $data->online_6; ?></span></td>
										<td class="v-box i-72 c-7"><span id="online-7"><?php echo $data->online_7; ?></span></td>
										<td class="v-box i-82 c-8"><span id="online-8"><?php echo $data->online_8; ?></span></td>
									</tr>
									<tr id="r-total">
										<td class="head-line total">Total</td>
										<td class="v-box total c-0"><span id="total-0"><?php echo $data->total_0; ?></span></td>
										<td class="v-box total c-3"><span id="total-3"><?php echo $data->total_3; ?></span></td>
										<td class="v-box total c-4"><span id="total-4"><?php echo $data->total_4; ?></span></td>
										<td class="v-box total c-5"><span id="total-5"><?php echo $data->total_5; ?></span></td>
										<td class="v-box total c-6"><span id="total-6"><?php echo $data->total_6; ?></span></td>
										<td class="v-box total c-7"><span id="total-7"><?php echo $data->total_7; ?></span></td>
										<td class="v-box total c-8"><span id="total-8"><?php echo $data->total_8; ?></span></td>
									</tr>
									<tr id="r-tiktok">
										<td class="head-line">TIKTOK</td>
										<td class="v-box i-0 c-0"><span id="tiktok-0"><?php echo $data->tiktok_0; ?></span></td>
										<td class="v-box i-3 c-3"><span id="tiktok-3"><?php echo $data->tiktok_3; ?></span></td>
										<td class="v-box i-4 c-4"><span id="tiktok-4"><?php echo $data->tiktok_4; ?></span></td>
										<td class="v-box i-5 c-5"><span id="tiktok-5"><?php echo $data->tiktok_5; ?></span></td>
										<td class="v-box i-6 c-6"><span id="tiktok-6"><?php echo $data->tiktok_6; ?></span></td>
										<td class="v-box i-7 c-7"><span id="tiktok-7"><?php echo $data->tiktok_7; ?></span></td>
										<td class="v-box i-8 c-8"><span id="tiktok-8"><?php echo $data->tiktok_8; ?></span></td>
									</tr>
									<tr id="r-shopee">
										<td class="head-line">SHOPEE</td>
										<td class="v-box i-02 c-0"><span id="shopee-0"><?php echo $data->shopee_0; ?></span></td>
										<td class="v-box i-32 c-3"><span id="shopee-3"><?php echo $data->shopee_3; ?></span></td>
										<td class="v-box i-42 c-4"><span id="shopee-4"><?php echo $data->shopee_4; ?></span></td>
										<td class="v-box i-52 c-5"><span id="shopee-5"><?php echo $data->shopee_5; ?></span></td>
										<td class="v-box i-62 c-6"><span id="shopee-6"><?php echo $data->shopee_6; ?></span></td>
										<td class="v-box i-72 c-7"><span id="shopee-7"><?php echo $data->shopee_7; ?></span></td>
										<td class="v-box i-82 c-8"><span id="shopee-8"><?php echo $data->shopee_8; ?></span></td>
									</tr>
									<tr id="r-lazada">
										<td class="head-line">LAZADA</td>
										<td class="v-box i-0 c-0"><span id="lazada-0"><?php echo $data->lazada_0; ?></span></td>
										<td class="v-box i-3 c-3"><span id="lazada-3"><?php echo $data->lazada_3; ?></span></td>
										<td class="v-box i-4 c-4"><span id="lazada-4"><?php echo $data->lazada_4; ?></span></td>
										<td class="v-box i-5 c-5"><span id="lazada-5"><?php echo $data->lazada_5; ?></span></td>
										<td class="v-box i-6 c-6"><span id="lazada-6"><?php echo $data->lazada_6; ?></span></td>
										<td class="v-box i-7 c-7"><span id="lazada-7"><?php echo $data->lazada_7; ?></span></td>
										<td class="v-box i-8 c-8"><span id="lazada-8"><?php echo $data->lazada_8; ?></span></td>
									</tr>
								</tbody>
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
								&copy; <?php echo getConfig('COMPANY_FULL_NAME'); ?>
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

<script id="order-template" type="text/x-handlebarsTemplate">
	<tr id="r-offline">
		<td class="head-line">OFFLINE</td>
		<td class="v-box i-0 c-0"><span id="offline-0">{{offline_0}}</span></td>
		<td class="v-box i-3 c-3"><span id="offline-3">{{offline_3}}</span></td>
		<td class="v-box i-4 c-4"><span id="offline-4">{{offline_4}}</span></td>
		<td class="v-box i-5 c-5"><span id="offline-5">{{offline_5}}</span></td>
		<td class="v-box i-6 c-6"><span id="offline-6">{{offline_6}}</span></td>
		<td class="v-box i-7 c-7"><span id="offline-7">{{offline_7}}</span></td>
		<td class="v-box i-8 c-8"><span id="offline-8">{{offline_8}}</span></td>
	</tr>
	<tr id="r-online">
		<td class="head-line">ONLINE</td>
		<td class="v-box i-02 c-0"><span id="online-0">{{online_0}}</span></td>
		<td class="v-box i-32 c-3"><span id="online-3">{{online_3}}</span></td>
		<td class="v-box i-42 c-4"><span id="online-4">{{online_4}}</span></td>
		<td class="v-box i-52 c-5"><span id="online-5">{{online_5}}</span></td>
		<td class="v-box i-62 c-6"><span id="online-6">{{online_6}}</span></td>
		<td class="v-box i-72 c-7"><span id="online-7">{{online_7}}</span></td>
		<td class="v-box i-82 c-8"><span id="online-8">{{online_8}}</span></td>
	</tr>
	<tr id="r-total">
		<td class="head-line total">Total</td>
		<td class="v-box total c-0"><span id="total-0">{{total_0}}</span></td>
		<td class="v-box total c-3"><span id="total-3">{{total_3}}</span></td>
		<td class="v-box total c-4"><span id="total-4">{{total_4}}</span></td>
		<td class="v-box total c-5"><span id="total-5">{{total_5}}</span></td>
		<td class="v-box total c-6"><span id="total-6">{{total_6}}</span></td>
		<td class="v-box total c-7"><span id="total-7">{{total_7}}</span></td>
		<td class="v-box total c-8"><span id="total-8">{{total_8}}</span></td>
	</tr>
	<tr id="r-tiktok">
		<td class="head-line">TIKTOK</td>
		<td class="v-box i-0 c-0"><span id="tiktok-0">{{tiktok_0}}</span></td>
		<td class="v-box i-3 c-3"><span id="tiktok-3">{{tiktok_3}}</span></td>
		<td class="v-box i-4 c-4"><span id="tiktok-4">{{tiktok_4}}</span></td>
		<td class="v-box i-5 c-5"><span id="tiktok-5">{{tiktok_5}}</span></td>
		<td class="v-box i-6 c-6"><span id="tiktok-6">{{tiktok_6}}</span></td>
		<td class="v-box i-7 c-7"><span id="tiktok-7">{{tiktok_7}}</span></td>
		<td class="v-box i-8 c-8"><span id="tiktok-8">{{tiktok_8}}</span></td>
	</tr>
	<tr id="r-shopee">
		<td class="head-line">SHOPEE</td>
		<td class="v-box i-02 c-0"><span id="shopee-0">{{shopee_0}}</span></td>
		<td class="v-box i-32 c-3"><span id="shopee-3">{{shopee_3}}</span></td>
		<td class="v-box i-42 c-4"><span id="shopee-4">{{shopee_4}}</span></td>
		<td class="v-box i-52 c-5"><span id="shopee-5">{{shopee_5}}</span></td>
		<td class="v-box i-62 c-6"><span id="shopee-6">{{shopee_6}}</span></td>
		<td class="v-box i-72 c-7"><span id="shopee-7">{{shopee_7}}</span></td>
		<td class="v-box i-82 c-8"><span id="shopee-8">{{shopee_8}}</span></td>
	</tr>
	<tr id="r-lazada">
		<td class="head-line">LAZADA</td>
		<td class="v-box i-0 c-0"><span id="lazada-0">{{lazada_0}}</span></td>
		<td class="v-box i-3 c-3"><span id="lazada-3">{{lazada_3}}</span></td>
		<td class="v-box i-4 c-4"><span id="lazada-4">{{lazada_4}}</span></td>
		<td class="v-box i-5 c-5"><span id="lazada-5">{{lazada_5}}</span></td>
		<td class="v-box i-6 c-6"><span id="lazada-6">{{lazada_6}}</span></td>
		<td class="v-box i-7 c-7"><span id="lazada-7">{{lazada_7}}</span></td>
		<td class="v-box i-8 c-8"><span id="lazada-8">{{lazada_8}}</span></td>
	</tr>
</script>

	<script src="<?php echo base_url(); ?>assets/js/ace/ace.sidebar.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/ace/ace.sidebar-scroll-1.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/ace/ace.submenu-hover.js"></script>
	<script src="<?php echo base_url(); ?>scripts/beep.js"></script>
	<script src="<?php echo base_url(); ?>scripts/template.js?v=2<?php echo date('Ymd'); ?>"></script>

	<script>
		const HOME = '<?php echo $this->home; ?>/';
		window.addEventListener('load', () => {
			setTimeout(() => {
				//getOrderData();
				refresh();
			}, 180000);
		});

		function getOrderData() {
			load_in();

			$.ajax({
				url: HOME + 'get_order_data',
				type: 'GET',
				cache: false,
				success: function(rs) {
					load_out();
					if (isJson(rs)) {
						let ds = JSON.parse(rs);

						if (ds.status == 'success') {
							let data = ds.data;
							let source = $('#order-template').html();
							let output = $('#data-table');
							render(source, data, output);
						}
					} 
					else {
						showError(rs);
						console.log(rs);
					}
				},
				error: function(rs) {
					showError(rs);
					console.log(rs);
				}
			})
		}
	</script>	
</body>

</html>
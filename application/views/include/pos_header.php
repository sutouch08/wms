
<!DOCTYPE html>
<html lang="th">
	<head>
		<meta charset="utf-8" />

		<title><?php echo $this->title; ?></title>
		<meta name="description" content="" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=10.0" />
		<link rel="shortcut icon" href="<?php echo base_url(); ?>assets/img/favicon.ico">

		<?php $this->load->view('include/header_include'); ?>

		<style>
			.ui-helper-hidden-accessible {
				display:none;
			}

			.ui-autocomplete {
		    max-height: 250px;
		    overflow-y: auto;
		    /* prevent horizontal scrollbar */
		    overflow-x: hidden;
			}

			.ui-widget {
				width:auto;
			}
	</style>
	</head>
	<body class="no-skin" onload="checkError()">
		<div id="loader" style="position:absolute; padding: 15px 25px 15px 25px; background-color:#fff; opacity:0.0; box-shadow: 0px 0px 25px #CCC; top:-20px; display:none; z-index:10;">
        <center><i class="fa fa-spinner fa-5x fa-spin blue"></i></center><center>กำลังทำงาน....</center>
		</div>

		<!-- #section:basics/navbar.layout -->
		<div id="navbar" class="navbar navbar-default">
			<script type="text/javascript">
				var BASE_URL = '<?php echo base_url(); ?>';
			</script>
			<div class="navbar-container no-padding" id="navbar-container">

				<!-- #section:basics/sidebar.mobile.toggle -->
				<button type="button" class="navbar-toggle menu-toggler pull-left" id="menu-toggler" data-target="#sidebar">
					<span class="sr-only">Toggle sidebar</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>

				<div class="navbar-header pull-left">
					<a href="#" class="navbar-brand">
						<small>
							<?php echo $this->title; ?>
						</small>
					</a>
				</div>

			</div><!-- /.navbar-container -->
		</div>

		<!-- /section:basics/navbar.layout -->
		<div class="main-container" id="main-container">
					<!-- /section:basics/sidebar -->
			<div class="main-content">
				<div class="main-content-inner">

					<div class="page-content">

								<!-- PAGE CONTENT BEGINS -->

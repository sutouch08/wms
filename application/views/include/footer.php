<!-- PAGE CONTENT ENDS -->
							</div><!-- /.col -->
						</div><!-- /.row -->
					</div><!-- /.page-content -->


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
		</div><!-- /.footer -->
		</div><!-- /.main-container -->
	</div>

		<!-- page specific plugin scripts -->

		<!-- ace scripts -->
		<script type="text/javascript">
			window.jQuery || document.write("<script src='<?php echo base_url(); ?>assets/js/jquery.js'>"+"<"+"/script>");
		</script>

		<script src="<?php echo base_url(); ?>assets/js/ace/ace.sidebar.js"></script>
		<script src="<?php echo base_url(); ?>assets/js/ace/ace.sidebar-scroll-1.js"></script>
		<script src="<?php echo base_url(); ?>assets/js/ace/ace.submenu-hover.js"></script>
		<script src="<?php echo base_url(); ?>scripts/beep.js"></script>
		<script src="<?php echo base_url(); ?>scripts/template.js?v=2<?php echo date('Ymd'); ?>"></script>
		<script>

			function changeUserPwd(uname)
			{
				window.location.href = BASE_URL +'user_pwd/change/'+uname;
			}
		</script>

	</body>

</html>

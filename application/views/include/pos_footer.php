<!-- PAGE CONTENT ENDS -->
							</div><!-- /.col -->
						</div><!-- /.row -->
					</div><!-- /.page-content -->


		<!-- page specific plugin scripts -->


		<script src="<?php echo base_url(); ?>assets/js/ace/ace.sidebar.js"></script>
		<script src="<?php echo base_url(); ?>assets/js/ace/ace.sidebar-scroll-1.js"></script>
		<script src="<?php echo base_url(); ?>assets/js/ace/ace.submenu-hover.js"></script>
		<script src="<?php echo base_url(); ?>scripts/beep.js"></script>
		<script src="<?php echo base_url(); ?>scripts/template.js?v=2<?php echo date('YmdH'); ?>"></script>
		<script>

			function changeUserPwd(uname)
			{
				window.location.href = BASE_URL +'user_pwd/change/'+uname;
			}
		</script>
	</body>

</html>

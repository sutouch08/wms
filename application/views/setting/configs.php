<?php $this->load->view('include/header'); ?>
<style>
	.li-block {
		border-bottom: solid 1px #ccc;
		background-color: #f5f5f5;
	}

</style>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    	<h4 class="title"><?php echo $this->title; ?></h4>
	</div>
</div>
<hr style="border-color:#CCC; margin-top: 15px; margin-bottom:0px;" />

<div class="row">
	<div class="col-lg-2 col-md-2 col-sm-2 padding-5 padding-right-0" style="padding-right:0; height:600px; overflow:auto;">
		<?php
		$tab1 = $tab == 'general' ? 'active in' : '';
		$tab2 = $tab == 'company' ? 'active in' : '';
		$tab3 = $tab == 'system' ? 'active in' : '';
		$tab4 = $tab == 'inventory' ? 'active in' : '';
		$tab5 = $tab == 'order' ? 'active in' : '';
		$tab6 = $tab == 'document' ? 'active in' : '';
		$tab7 = $tab == 'bookcode' ? 'active in' : '';
		$tab8 = $tab == 'SAP' ? 'active in' : '';
		$tab9 = $tab == 'WMS' ? 'active in' : '';
		$tab10 = $tab == 'sokojung' ? 'active in' : '';
		$tab11 = $tab == 'chatbot' ? 'active in' : '';
		$tab12 = $tab == 'web' ? 'active in' : '';
		$tab13 = $tab == 'ix' ? 'active in' : '';
		$tab14 = $tab == 'wrx' ? 'active in' : '';

		?>
		<ul id="myTab1" class="setting-tabs" style="margin-left:0;">
			<li class="li-block <?php echo $tab1; ?>" onclick="changeURL('general')"><a href="#general" data-toggle="tab">ทั่วไป</a></li>
			<li class="li-block <?php echo $tab2; ?>" onclick="changeURL('company')"><a href="#company" data-toggle="tab">ข้อมูลบริษัท</a></li>
			<li class="li-block <?php echo $tab3; ?>" onclick="changeURL('system')"><a href="#system" data-toggle="tab">ระบบ</a></li>
			<li class="li-block <?php echo $tab4; ?>" onclick="changeURL('inventory')"><a href="#inventory" data-toggle="tab">คลังสินค้า</a></li>
			<li class="li-block <?php echo $tab5; ?>" onclick="changeURL('order')"><a href="#order" data-toggle="tab">ออเดอร์</a></li>
			<li class="li-block <?php echo $tab6; ?>" onclick="changeURL('document')"><a href="#document" data-toggle="tab">เลขที่เอกสาร</a></li>
			<li class="li-block <?php echo $tab7; ?>" onclick="changeURL('bookcode')"><a href="#bookcode" data-toggle="tab">เล่มเอกสาร</a></li>
			<li class="li-block <?php echo $tab8; ?>" onclick="changeURL('SAP')"><a href="#SAP" data-toggle="tab">ข้อมูล SAP</a></li>
		<?php if($this->_SuperAdmin) : ?>
			<li class="li-block <?php echo $tab9; ?>" onclick="changeURL('WMS')"><a href="#WMS" data-toggle="tab">ข้อมูล WMS</a></li>
			<li class="li-block <?php echo $tab11; ?>" onclick="changeURL('chatbot')"><a href="#chatbot" data-toggle="tab">ข้อมูล CHATBOT</a></li>
		<?php endif; ?>
			<li class="li-block <?php echo $tab10; ?>" onclick="changeURL('sokojung')"><a href="#sokojung" data-toggle="tab">ข้อมูล SOKOJUNG</a></li>
			<li class="li-block <?php echo $tab12; ?>" onclick="changeURL('web')"><a href="#web" data-toggle="tab">ข้อมูล Magento</a></li>
			<li class="li-block <?php echo $tab13; ?>" onclick="changeURL('ix')"><a href="#ix" data-toggle="tab">ข้อมูล IX API</a></li>
			<li class="li-block <?php echo $tab14; ?>" onclick="changeURL('wrx')"><a href="#wrx" data-toggle="tab">ข้อมูล WRX API</a></li>

		</ul>
	</div>
	<div class="col-lg-10 col-md-10 col-sm-10 border-1" style="padding-top:15px; border-top:0px !important; height:600px; overflow:auto;">
		<div class="tab-content" style="border:0px;">
			<!---  ตั้งค่าทั่วไป  ----------------------------------------------------->
			<div class="tab-pane fade <?php echo $tab1; ?>" id="general">
				<?php $this->load->view('setting/setting_general'); ?>
			</div>
			<!---  ตั้งค่าบริษัท  ------------------------------------------------------>
			<div class="tab-pane fade <?php echo $tab2; ?>" id="company">
				<?php $this->load->view('setting/setting_company'); ?>
			</div>
			<!---  ตั้งค่าระบบ  ----------------------------------------------------->
			<div class="tab-pane fade <?php echo $tab3; ?>" id="system">
				<?php	$this->load->view('setting/setting_system'); ?>
			</div>
			<div class="tab-pane fade <?php echo $tab4; ?>" id="inventory">
				<?php $this->load->view('setting/setting_inventory'); ?>
			</div>
			<!---  ตั้งค่าออเดอร์  --------------------------------------------------->
			<div class="tab-pane fade <?php echo $tab5; ?>" id="order">
				<?php $this->load->view('setting/setting_order'); ?>
			</div>
			<!---  ตั้งค่าเอกสาร  --------------------------------------------------->
			<div class="tab-pane fade <?php echo $tab6; ?>" id="document">
				<?php $this->load->view('setting/setting_document'); ?>
			</div>

			<div class="tab-pane fade <?php echo $tab7; ?>" id="bookcode">
				<?php $this->load->view('setting/setting_bookcode'); ?>
			</div>

			<div class="tab-pane fade <?php echo $tab8; ?>" id="SAP">
				<?php $this->load->view('setting/setting_sap'); ?>
			</div>

			<div class="tab-pane fade <?php echo $tab9; ?>" id="WMS">
				<?php $this->load->view('setting/setting_wms'); ?>
			</div>


			<div class="tab-pane fade <?php echo $tab12; ?>" id="web">
				<?php $this->load->view('setting/setting_web'); ?>
			</div>

			<div class="tab-pane fade <?php echo $tab13; ?>" id="ix">
				<?php $this->load->view('setting/setting_ix_api'); ?>
			</div>

			<div class="tab-pane fade <?php echo $tab14; ?>" id="wrx">
				<?php $this->load->view('setting/setting_wrx_api'); ?>
			</div>

			<?php if($this->_SuperAdmin) : ?>
				<div class="tab-pane fade <?php echo $tab11; ?>" id="chatbot">
					<?php $this->load->view('setting/setting_chatbot'); ?>
				</div>

				<div class="tab-pane fade <?php echo $tab10; ?>" id="sokojung">
					<?php $this->load->view('setting/setting_sokojung'); ?>
				</div>
			<?php endif; ?>
		</div><!--/ tab-content-->
	</div><!--/ col-sm-9  -->
</div><!--/ row  -->


<script src="<?php echo base_url(); ?>scripts/setting/setting.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/setting/setting_document.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>

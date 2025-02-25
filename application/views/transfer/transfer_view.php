<?php $this->load->view('include/header'); ?>
<?php
	$pm = get_permission('APACWW', $this->_user->uid, $this->_user->id_profile);
	$canAccept = FALSE;
	$accept_user = FALSE;

	if( ! empty($pm))
	{
		$canAccept = (($pm->can_add + $pm->can_edit + $pm->can_delete + $pm->can_approve) > 0  OR $this->_SuperAdmin) ? TRUE : FALSE;
	}

	if( ! empty($accept_list))
	{
		foreach($accept_list as $au)
		{
			if($au->uname == $this->_user->uname && $au->is_accept == 0)
			{
				$accept_user = TRUE;
			}
		}
	}

	$pos_api = is_true(getConfig('POS_API_WW'));

	if($pos_api === TRUE)
	{
		$fWh = $this->warehouse_model->get($doc->from_warehouse);
		$tWh = $this->warehouse_model->get($doc->to_warehouse);

		if( ! empty($fWh) && ! empty($tWh))
		{
			if( $fWh->is_pos == 0 && $tWh->is_pos == 0)
			{
				$pos_api = FALSE;
			}
		}
		else
		{
			$pos_api = FALSE;
		}
	}
	?>
<div class="row">
	<div class="col-lg-4 col-md-4 col-sm-4 hidden-xs padding-5">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
  </div>
	<div class="col-xs-12 visible-xs">
		<h3 class="title-xs"><?php echo $this->title; ?></h3>
	</div>
  <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5">
    	<p class="pull-right top-p">
				<button type="button" class="btn btn-xs btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
				<?php if($doc->status == 4 && ($accept_user OR $canAccept)) : ?>
					<button type="button" class="btn btn-xs btn-success top-btn" onclick="accept()"><i class="fa fa-check-circle"></i> ยืนยันการรับสินค้า</button>
				<?php endif; ?>
				<?php if($doc->status != 2) : ?>
					<?php if($doc->status != 1 && $this->pm->can_delete OR $this->_SuperAdmin) : ?>
						<button type="button" class="btn btn-xs btn-danger" onclick="goDelete('<?php echo $doc->code; ?>', <?php echo $doc->status; ?>)">
							<i class="fa fa-trash"></i> ยกเลิก
						</button>
					<?php endif; ?>
				<?php endif; ?>
		    <?php if($doc->status == 1) : ?>
		      <button type="button" class="btn btn-xs btn-info top-btn" onclick="doExport()"><i class="fa fa-send"></i> Send to SAP</button>
					<?php if($pos_api && ! $doc->is_pos) : ?>
						<button type="button" class="btn btn-xs btn-success top-btn" onclick="sendToPos()"><i class="fa fa-send"></i> Send to POS</button>
					<?php endif; ?>
		    <?php endif; ?>
				<?php if($doc->is_wms > 0 && $doc->api == 1 && $doc->is_expire == 0 && $doc->status != 2 && ($doc->status == 3 OR $this->_SuperAdmin)) : ?>
					<?php if($this->wmsApi && ($doc->from_warehouse == $this->wmsWh OR $doc->to_warehouse == $this->wmsWh)) : ?>
						<button type="button" class="btn btn-xs btn-success top-btn" onclick="sendToPlc()"><i class="fa fa-send"></i> Send to PLC</button>
					<?php endif; ?>
					<?php if($this->sokoApi && ($doc->from_warehouse == $this->sokoWh OR $doc->to_warehouse == $this->sokoWh)) : ?>
						<button type="button" class="btn btn-xs btn-success top-btn" onclick="sendToSoko()"><i class="fa fa-send"></i> Send to Soko</button>
					<?php endif; ?>
				<?php endif; ?>

				<?php if($doc->is_wms < 1 && $doc->is_expire == 0 && $doc->status == 3 && $this->pm->can_approve) : ?>
					<button type="button" class="btn btn-xs btn-primary" onclick="pullBack('<?php echo $doc->code; ?>')">ย้อนสถานะกลับมาแก้ไข</button>
				<?php endif; ?>
				<?php if($doc->is_wms < 1 && $doc->is_expire == 0 && $doc->status == -1 && $this->pm->can_edit) : ?>
					<button type="button" class="btn btn-xs btn-warning" onclick="goEdit('<?php echo $doc->code; ?>')"><i class="fa fa-pencil"></i> &nbsp; แก้ไข</button>
				<?php endif; ?>
				<?php if($doc->status == 0 && $doc->must_approve == 1 && $doc->is_approve == 0 && ($this->pm->can_approve OR $this->_SuperAdmin)) : ?>
					<button type="button" class="btn btn-xs btn-success top-btn" onclick="doApprove()"><i class="fa fa-check-circle"></i> อนุมัติ</button>
					<button type="button" class="btn btn-xs btn-danger top-btn" onclick="doReject()"><i class="fa fa-times-circle"></i> ไม่อนุมัติ</button>
				<?php endif; ?>
				<button type="button" class="btn btn-xs btn-primary top-btn hidden-xs" onclick="printTransfer()"><i class="fa fa-print"></i> ใบโอน</button>
				<?php if($doc->is_wms == '-1') : ?>
				<button type="button" class="btn btn-xs btn-primary top-btn" onclick="printWmsTransfer()"><i class="fa fa-print"></i> Reconcile</button>
				<?php endif; ?>
      </p>
    </div>
</div><!-- End Row -->
<input type="hidden" id="transfer_code" name="transfer_code" value="<?php echo $doc->code; ?>" />
<input type="hidden" id="can-accept" name="can_accept" value="<?php echo $canAccept ? 1 : 0; ?>" />
<hr/>
<?php
	if($doc->is_expire == 1 OR $doc->status == 2)
	{
		if($doc->status == 2)
		{
			$this->load->view('cancle_watermark');
		}
		else
		{
			$this->load->view('expire_watermark');
		}
	}
	else
	{
		if($doc->status == 3)
		{
			$this->load->view('on_process_watermark');
		}

		if($doc->status == 0 && $doc->is_approve == 3)
		{
			$this->load->view('reject_watermark');
		}

		if($doc->status == 4)
		{
			$this->load->view('accept_watermark');
		}
	}

	$this->load->view('transfer/transfer_view_header');
	?>

	<?php if($doc->is_pos == 1) : ?>
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<p class="red text-center">** เอกสารนี้ถูกสร้างโดยระบบ POS จึงไม่สามารถแก้ไขรายการได้ **</p>
			</div>
		</div>
	<?php endif; ?>
	<?php
	$this->load->view('transfer/transfer_view_detail');
	$this->load->view('accept_modal');
	$this->load->view('cancle_modal');
?>

<script src="<?php echo base_url(); ?>scripts/transfer/transfer.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/transfer/transfer_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/transfer/transfer_control.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/transfer/transfer_detail.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>

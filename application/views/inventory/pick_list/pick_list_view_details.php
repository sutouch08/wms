<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-sm btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
	<?php if(($this->pm->can_add OR $this->pm->can_edit) && $doc->status == 'P') : ?>
		<button type="button" class="btn btn-sm btn-primary top-btn" onclick="releasePickList()">Release Pick List</button>
		<button type="button" class="btn btn-sm btn-warning top-btn" onclick="goEdit('<?php echo $doc->code; ?>')">แก้ไข</button>
	<?php endif; ?>
	<?php if($this->pm->can_edit && ($doc->status == 'R' OR $doc->status == 'Y')) : ?>
		<button type="button" class="btn btn-sm btn-danger top-btn" onclick="unReleasePickList()">ย้อนสถานะ</button>
	<?php endif; ?>
	<?php if($doc->status != 'D') : ?>
		<button type="button" class="btn btn-sm btn-info top-btn" onclick="printPickListQr()">Print Order</button>
	<?php endif; ?>	
  </div>
</div><!-- End Row -->
<hr class=""/>
<div class="row">
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
		<input type="text" class="width-100 text-center" id="code" value="<?php echo $doc->code; ?>" disabled />
    <input type="hidden" id="id" value="<?php echo $doc->id; ?>" />
  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label>วันที่</label>
    <input type="text" class="width-100 text-center e" id="date" value="<?php echo thai_date($doc->date_add, FALSE); ?>" readonly disabled />
  </div>

	<div class="col-lg-4-harf col-md-5 col-sm-5 col-xs-12 padding-5">
		<label>คลังสินค้าต้นทาง</label>
		<select class="width-100 e" id="warehouse" disabled>
			<option value="">เลือกคลัง</option>
			<?php echo select_common_warehouse($doc->warehouse_code); ?>
		</select>
	</div>

	<div class="col-lg-2-harf col-md-3-harf col-sm-3-harf col-xs-12 padding-5">
		<label>โซนปลายทาง</label>
		<select class="width-100 e" id="zone" disabled>
			<option value="">เลือกโซน</option>
			<?php echo select_pickface_zone($doc->zone_code); ?>
		</select>
	</div>

	<div class="col-lg-2-harf col-md-2-harf col-sm-2-harf col-xs-12 padding-5">
		<label>ช่องทางขาย</label>
		<select class="width-100 e" id="channels" disabled>
			<option value="">เลือกช่องทางขาย</option>
			<?php echo select_channels($doc->channels_code); ?>
		</select>
	</div>
  <div class="col-lg-11 col-md-8 col-sm-8 col-xs-9 padding-5">
    <label>หมายเหตุ</label>
		<input type="text" class="width-100 e" id="remark" value="<?php echo $doc->remark; ?>" disabled/>
  </div>
	<?php $statusLabel = $doc->status == 'D' ? 'Canceled' : ($doc->status == 'C' ? 'Closed' : ($doc->status == 'R' ? 'Released' : 'Pending')); ?>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label class="display-block not-show">x</label>
		<input type="text" class="width-100" value="<?php echo $statusLabel; ?>" disabled />
  </div>
</div>
<hr class="padding-5 margin-top-15">
<?php $this->load->view('inventory/pick_list/pick_list_details'); ?>

<script>
	$('#warehouse').select2();
	$('#zone').select2();
	$('#channels').select2();
  $('#channels-code').select2();

	function printPickListQr() {
		let code = $('#code').val();
	  let center = ($(document).width() - 800) /2;
	  let target = HOME + 'print_order_list/'+code;
		window.open(target, "_blank", "width=800, height=900. left="+center+", scrollbars=yes");
	}

	function unReleasePickList() {
		let code = $('#code').val();

		swal({
			title:'Unrelease',
			text:'ต้องการย้อนสถานะเอกสารหรือไม่ ?',
			type:'warning',
			html:true,
			showCancelButton:true,
			cancelButtonText:'No',
			confirmButtonText:'Yes',
			closeOnConfirm:true
		}, function() {
			load_in();

			setTimeout(() => {
				$.ajax({
					url:HOME + 'unrelease_pick_list/'+code,
					type:'POST',
					cache:false,
					success:function(rs) {
						load_out();

						if(rs.trim() === 'success') {
							window.location.reload();
						}
						else {
							beep();
							showError(rs);
						}
					},
					error:function(rs) {
						beep();
						showError(rs);
					}
				})
			}, 100)
		})
	}
</script>
<script src="<?php echo base_url(); ?>scripts/inventory/pick_list/pick_list.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/pick_list/pick_list_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/pick_list/pick_list_control.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>

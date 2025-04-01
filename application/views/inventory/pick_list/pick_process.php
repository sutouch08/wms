<?php $this->load->view('include/header'); ?>
<style>
	.scroll {
		white-space: nowrap;
		overflow-x: auto;
	}

</style>

<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-sm btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
	<?php if($doc->status == 'P') : ?>
		<button type="button" class="btn btn-sm btn-primary top-btn" onclick="releasePickList()">Release Pick List</button>
	<?php endif; ?>
	<?php if($doc->status == 'R') : ?>
		<button type="button" class="btn btn-sm btn-danger top-btn" onclick="unReleasePickList()">ย้อนสถานะ</button>
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
		<select class="form-control input-sm" id="warehouse" disabled>
			<option value="">เลือกคลัง</option>
			<?php echo select_common_warehouse($doc->warehouse_code); ?>
		</select>
	</div>

	<div class="col-lg-2-harf col-md-3-harf col-sm-3-harf col-xs-12 padding-5">
		<label>โซนปลายทาง</label>
		<select class="form-control input-sm" id="zone" disabled>
			<option value="">เลือกโซน</option>
			<?php echo select_pickface_zone($doc->zone_code); ?>
		</select>
	</div>

	<div class="col-lg-2-harf col-md-2-harf col-sm-2-harf col-xs-12 padding-5">
		<label>ช่องทางขาย</label>
		<select class="form-control input-sm" id="channels" disabled>
			<option value="">เลือกช่องทางขาย</option>
			<?php echo select_channels($doc->channels_code); ?>
		</select>
	</div>
  <div class="col-lg-11 col-md-8 col-sm-8 col-xs-9 padding-5">
    <label>หมายเหตุ</label>
		<input type="text" class="width-100 e" id="remark" value="<?php echo $doc->remark; ?>" disabled/>
  </div>
	<?php $statusLabel = $doc->status == 'D' ? 'Canceled' : ($doc->status == 'C' ? 'Closed' : ($doc->status == 'R' ? 'Released' : 'Pending')); ?>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-12 padding-5">
    <label class="display-block not-show">x</label>
		<input type="text" class="width-100" value="<?php echo $statusLabel; ?>" disabled />
  </div>
</div>
<hr class="padding-5 margin-top-15">
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12">
		<div class="row">
			<div class="col-lg-2 col-md-2 col-sm-2-harf col-xs-6 padding-5">
				<label>โซนต้นทาง</label>
				<input type="text" class="width-100" id="zone-code" placeholder="ยิงบาร์โค้ดโซน" autofocus/>
			</div>

			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 padding-5">
				<label class="display-block not-shohw">โซนต้นทาง</label>
				<input type="text" class="width-100" id="zone-name" readonly />
			</div>

			<div class="col-lg-1 col-md-1-harf col-sm-1-harf padding-5">
				<label class="display-block not-show">newZone</label>
				<button type="button" class="btn btn-xs btn-info btn-block" id="btn-change-zone" onclick="changeZone()" >เปลี่ยนโซน</button>
			</div>

			<div class="col-lg-1 col-md-1 col-sm-1 padding-5">
				<label>จำนวน</label>
				<input type="number" class="form-control input-sm text-center" id="item-qty" value="1"  />
			</div>

			<div class="col-lg-2 col-md-3 col-sm-3 padding-5">
				<label>บาร์โค้ดสินค้า</label>
				<input type="text" class="form-control input-sm" id="barcode-item" placeholder="ยิงบาร์โค้ดเพื่อย้ายสินค้าออก"  />
			</div>
		</div>
	</div>
</div>
<hr>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<div class="fix-width-200 text-center" style="background-color: black; color:white; font-size: 20px; position:absolute; top:0px; right: 5px;">
			<span id="total-picked"><?php echo number($totalPickQty); ?></span>&nbsp; / <span id="total-release"><?php echo number($totalReleaseQty); ?></span>
		</div>

    <div class="tabbable">
      <ul class="nav nav-tabs" id="myTab">
        <li class="active"><a data-toggle="tab" href="#incomplete-tab" aria-expanded="true">รายการยังไม่ครบ</a></li>
				<li class=""><a data-toggle="tab" href="#complete-tab" aria-expanded="true">รายการครบแล้ว</a></li>
        <li class=""><a data-toggle="tab" href="#transection-tab" aria-expanded="false">transection</a></li>
      </ul>
      <div class="tab-content" style="padding:0px;">
        <?php $this->load->view('inventory/pick_list/pick_process_tab_incomplete'); ?>
				<?php $this->load->view('inventory/pick_list/pick_process_tab_complete'); ?>
        <?php $this->load->view('inventory/pick_list/pick_process_tab_trans'); ?>
      </div>
    </div>
  </div>
</div>



<script>

</script>
<script src="<?php echo base_url(); ?>scripts/inventory/pick_list/pick_list.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/pick_list/pick_list_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/pick_list/pick_process.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>

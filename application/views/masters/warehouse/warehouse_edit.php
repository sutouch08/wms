<?php $this->load->view('include/header'); ?>
<?php
$sell_yes = $ds->sell == 1 ? 'btn-success' : '';
$sell_no = $ds->sell == 0 ? 'btn-danger' : '';
$prepare_yes = $ds->prepare == 1 ? 'btn-success' : '';
$prepare_no = $ds->prepare == 0 ? 'btn-danger' : '';
$lend_yes = $ds->lend == 1 ? 'btn-success' : '';
$lend_no = $ds->lend == 0 ? 'btn-danger' : '';
$auz_yes = $ds->auz == 1 ? 'btn-success' : '';
$auz_no = $ds->auz == 0 ? 'btn-danger' : '';
$btn_active = $ds->active == 1 ? 'btn-success' : 'btn-danger';
$active_yes = $ds->active == 1 ? 'btn-success' : '';
$active_no = $ds->active == 0 ? 'btn-danger' : '';
$cm_yes = $ds->is_consignment == 1 ? 'btn-success' : '';
$cm_no = empty($ds->is_consignment) ? 'btn-danger' : '';
$pos_yes = $ds->is_pos == 1 ? 'btn-success' : '';
$pos_no = $ds->is_pos == 0 ? 'btn-primary' : '';
?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> Back</button>
	</div>
</div><!-- End Row -->
<hr />
<div class="form-horizontal margin-top-30">

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">รหัสคลัง</label>
		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-12">
			<input type="text" class="form-control input-sm" value="<?php echo $ds->code; ?>" disabled />
		</div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">ชื่อคลัง</label>
		<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
			<input type="text" class="form-control input-sm" value="<?php echo $ds->name; ?>" disabled />
		</div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">ประเภทคลัง</label>
		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-12">
			<select class="form-control input-sm" name="role" id="role">
				<option value="">กรุณาเลือก</option>
				<?php echo select_warehouse_role($ds->role); ?>
			</select>
		</div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">มูลค่าสูงสุด</label>
		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-12">
			<input type="text" class="form-control input-sm" value="<?php echo number($ds->limit_amount, 2); ?>" disabled />
		</div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-12 control-label no-padding-right">Default Bin Location</label>
		<div class="col-lg-1-harf col-md-2-harf col-sm-3 col-xs-12">
			<input type="text" class="form-control input-sm" id="default-zone-code" value="<?php echo $ds->default_zone; ?>" />
		</div>
		<div class="col-lg-5 col-md-6 col-sm-6 col-xs-12 hidden-xs">
			<input type="text" class="form-control input-sm" id="default-zone-name" value="<?php echo empty($ds->default_zone) ? '' : zone_name($ds->default_zone ?? ''); ?>" readonly />
		</div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-6 control-label control-label-xs no-padding-right">ฝากขายเทียม</label>
		<div class="col-lg-8 col-md-8 col-sm-8 col-xs-6 text-right-xs">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="is_consignment" type="checkbox" value="1" <?php echo is_checked($ds->is_consignment, '1'); ?> onchange="toggleOption($(this))" />
				<span class="lbl margin-left-0" data-lbl="NO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;YES"></span>
			</label>
			<input type="hidden" name="is_consignment" id="is-consignment" value="<?php echo $ds->is_consignment; ?>" />
		</div>
	</div>


	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-6 control-label control-label-xs no-padding-right">อนุญาติให้ขาย</label>
		<div class="col-lg-8 col-md-8 col-sm-8 col-xs-6 text-right-xs">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="sell" type="checkbox" value="1" <?php echo is_checked($ds->sell, '1'); ?> onchange="toggleOption($(this))" />
				<span class="lbl margin-left-0" data-lbl="NO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;YES"></span>
			</label>
			<input type="hidden" name="sell" id="sell" value="<?php echo $ds->sell; ?>" />
		</div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-6 control-label control-label-xs no-padding-right">อนุญาติให้จัด</label>
		<div class="col-lg-8 col-md-8 col-sm-8 col-xs-6 text-right-xs">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="prepare" type="checkbox" value="1" <?php echo is_checked($ds->prepare, '1'); ?> onchange="toggleOption($(this))" />
				<span class="lbl margin-left-0" data-lbl="NO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;YES"></span>
			</label>
			<input type="hidden" name="prepare" id="prepare" value="<?php echo $ds->prepare; ?>" />
		</div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-6 control-label control-label-xs no-padding-right">อนุญาติให้ยืม</label>
		<div class="col-lg-8 col-md-8 col-sm-8 col-xs-6 text-right-xs">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="lend" type="checkbox" value="1" <?php echo is_checked($ds->lend, '1'); ?> onchange="toggleOption($(this))" />
				<span class="lbl margin-left-0" data-lbl="NO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;YES"></span>
			</label>
			<input type="hidden" name="lend" id="lend" value="<?php echo $ds->lend; ?>" />
		</div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-6 control-label control-label-xs no-padding-right">อนุญาติให้ติดลบ</label>
		<div class="col-lg-8 col-md-8 col-sm-8 col-xs-6 text-right-xs">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="auz" type="checkbox" value="1" <?php echo is_checked($ds->auz, '1'); ?> onchange="toggleOption($(this))" />
				<span class="lbl margin-left-0" data-lbl="NO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;YES"></span>
			</label>
			<input type="hidden" name="auz" id="auz" value="<?php echo $ds->auz; ?>" />
		</div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-6 control-label control-label-xs no-padding-right">POS API</label>
		<div class="col-lg-8 col-md-8 col-sm-8 col-xs-6 text-right-xs">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="is_pos" type="checkbox" value="1" <?php echo is_checked($ds->is_pos, '1'); ?> onchange="toggleOption($(this))" />
				<span class="lbl margin-left-0" data-lbl="NO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;YES"></span>
			</label>
			<input type="hidden" name="is_pos" id="is-pos" value="<?php echo $ds->is_pos; ?>" />
		</div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-6 control-label control-label-xs no-padding-right">Auto Shipping</label>
		<div class="col-lg-8 col-md-8 col-sm-8 col-xs-6 text-right-xs">
			<label style="padding-top:5px; margin-bottom:0px;">
				<input class="ace ace-switch ace-switch-7" data-name="auto_shipping" type="checkbox" value="1" <?php echo is_checked($ds->auto_shipping, '1'); ?> onchange="toggleOption($(this))" />
				<span class="lbl margin-left-0" data-lbl="NO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;YES"></span>
			</label>
			<input type="hidden" name="auto_shipping" id="auto-shipping" value="<?php echo $ds->auto_shipping; ?>" />
		</div>
	</div>

	<div class="form-group">
		<label class="col-lg-3 col-md-3 col-sm-3 col-xs-6 control-label control-label-xs no-padding-right">สถานะ</label>
		<div class="col-lg-8 col-md-8 col-sm-8 col-xs-6 text-right-xs">
			<label style="padding-top:5px; margin-bottom:0px;">
				<?php if ($ds->active == 1) : ?>
					<span class="lbl green"><i class="fa fa-check fa-lg"></i>&nbsp;&nbsp;Active</span>
				<?php else : ?>
					<span class="lbl red"><i class="fa fa-times fa-lg"></i>&nbsp;&nbsp;Inactive</span>
				<?php endif; ?>
			</label>
		</div>
	</div>

	<div class="divider-hidden"></div>
	<div class="divider-hidden"></div>
	<div class="divider-hidden"></div>

	<div class="form-group">
		<div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-12">
			<button type="button" class="btn btn-white btn-success btn-100 btn-block-xs" onclick="update()"><i class="fa fa-save"></i>&nbsp; Save</button>
		</div>
	</div>
	<input type="hidden" name="code" id="code" value="<?php echo $ds->code; ?>">
</div>

<script src="<?php echo base_url(); ?>scripts/masters/warehouse.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
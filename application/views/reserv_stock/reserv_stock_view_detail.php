<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
    <button type="button" class="btn btn-white btn-warning top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
		<button type="button" class="btn btn-white btn-purple top-btn" onclick="exportData()"><i class="fa fa-file-excel-o"></i> Export to Excel</button>
  <?php if(($this->pm->can_approve) && ($doc->status == 'P')) : ?>
		<button type="button" class="btn btn-white btn-danger top-btn" onclick="rejected()"><i class="fa fa-times"></i> &nbsp; Reject</button>
    <button type="button" class="btn btn-white btn-success top-btn" onclick="approve()"><i class="fa fa-check"></i> &nbsp; Approve</button>
  <?php endif; ?>
  </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<div class="row">
  <div class="col-lg-2 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>เลขที่</label>
    <input type="text" class="form-control input-sm text-center"  id="code" value="<?php echo $doc->code; ?>" disabled />
  </div>

  <div class="col-lg-4 col-md-5 col-sm-5 col-xs-12 padding-5">
    <label>คลัง</label>
    <select class="width-100 rq" id="warehouse" disabled>
      <option value="">เลือกคลัง</option>
      <?php echo select_warehouse($doc->warehouse_code); ?>
    </select>
  </div>

  <div class="col-lg-6 col-md-5 col-sm-5 col-xs-8 padding-5">
    <label>Description</label>
    <input type="text" class="form-control input-sm rq" id="name" value="<?php echo $doc->name; ?>" disabled/>
  </div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>Reserv For</label>
    <select id="is-mkp" class="form-control input-sm" disabled>
			<option value="">Select</option>
      <option value="1" <?php echo is_selected('1', $doc->is_mkp); ?>>Marketplace</option>
      <option value="0" <?php echo is_selected('0', $doc->is_mkp); ?>>All</option>
    </select>
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>Active</label>
    <select id="active" class="form-control input-sm" disabled>
      <option value="1" <?php echo is_selected('1', $doc->active); ?>>Active</option>
      <option value="0" <?php echo is_selected('0', $doc->active); ?>>Inactive</option>
    </select>
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>Start Date</label>
		  <input type="text" class="form-control input-sm text-center rq" id="start_date" value="<?php echo thai_date($doc->start_date); ?>"  disabled/>
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>End Date</label>
    <input type="text" class="form-control input-sm text-center rq" id="end_date" value="<?php echo thai_date($doc->end_date); ?>" disabled />
  </div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>Status</label>
		<input type="text" class="form-control input-sm text-center" value="<?php echo reserv_stock_status_text($doc->status); ?>" disabled />
	</div>

	<input type="hidden" id="id" value="<?php echo $doc->id; ?>" />
</div>
<hr class="margin-top-15 margin-bottom-15">
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-stripped border-1">
      <thead>
        <tr class="font-size-11">
          <th class="fix-width-40 text-center">#</th>
          <th class="fix-width-200">SKU</th>
          <th class="fix-width-100 text-center">Reserv Qty</th>
					<th class="fix-width-100 text-center">Reserv BL.</th>
          <th class="min-width-200">Description</th>
        </tr>
      </thead>
      <tbody id="result-table">
  <?php if( ! empty($details)) : ?>
    <?php $no = 1; ?>
    <?php foreach($details as $rs) : ?>
      <tr class="font-size-11" id="row-<?php echo $rs->id; ?>">
        <td class="middle text-center no"><?php echo $no; ?></td>
        <td class="middle"><?php echo $rs->product_code; ?></td>
        <td class="middle text-center"><?php echo number($rs->qty); ?></td>
				<td class="middle text-center"><?php echo number($rs->reserv_qty); ?></td>
        <td class="middle"><?php echo $rs->product_name; ?></td>
      </tr>
      <?php $no++; ?>
    <?php endforeach; ?>
  <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<form id="export-form" method="post" action="<?php echo $this->home; ?>/export_data/">
  <input type="hidden" name="code" value="<?php echo $doc->code; ?>" />
  <input type="hidden" name="id" value="<?php echo $doc->id; ?>" />
  <input type="hidden" name="token" id="token" />
</form>
<script>
  $('#warehouse').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/reserv_stock/reserv_stock.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>

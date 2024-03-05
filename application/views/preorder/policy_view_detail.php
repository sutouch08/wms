<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 padding-5 hidden-xs">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
  <div class="col-xs-12 padding-5 visible-xs">
    <h4 class="title-xs"><?php echo $this->title; ?></h4>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
    	<p class="pull-right top-p">
        <button type="button" class="btn btn-sm btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
      </p>
    </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<div class="row">
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>Doc No.</label>
    <input type="text" class="form-control input-sm text-center"  id="code" value="<?php echo $doc->code; ?>" disabled />
  </div>

  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
    <label>Description</label>
    <input type="text" class="form-control input-sm rq" id="name" value="<?php echo $doc->name; ?>" disabled/>
  </div>

  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>Status</label>
    <select id="status" class="form-control input-sm" disabled>
      <option value="1" <?php echo is_selected('1', $doc->status); ?>>Active</option>
      <option value="0" <?php echo is_selected('0', $doc->status); ?>>Inactive</option>
    </select>
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>Start Date</label>
		  <input type="text" class="form-control input-sm text-center rq" id="start_date" value="<?php echo thai_date($doc->start_date); ?>" disabled />
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>End Date</label>
    <input type="text" class="form-control input-sm text-center rq" id="end_date" value="<?php echo thai_date($doc->end_date); ?>"  disabled/>
  </div>
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label>User</label>
    <input type="text" class="form-control input-sm" value="<?php echo $doc->user; ?>" disabled />
  </div>
</div>
<hr class="margin-top-15 margin-bottom-15">
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-stripped border-1">
      <thead>
        <th class="fix-width-40 text-center">#</th>
        <th class="fix-width-200">SKU</th>
				<th class="fix-width-100 text-center">Booked</th>
        <th class="min-width-200">Description</th>
      </thead>
      <tbody id="result-table">
  <?php if( ! empty($details)) : ?>
    <?php $no = 1; ?>
    <?php foreach($details as $rs) : ?>
      <tr id="row-<?php echo $rs->id; ?>">
        <td class="middle text-center no"><?php echo $no; ?></td>
        <td class="middle"><?php echo $rs->product_code; ?></td>
				<td class="middle text-center"><?php echo number($this->pre_order_policy_model->count_items_on_order($rs->id)); ?></td>
        <td class="middle"><?php echo $rs->product_name; ?></td>
      </tr>
      <?php $no++; ?>
    <?php endforeach; ?>
  <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="<?php echo base_url(); ?>scripts/preorder/preorder.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>

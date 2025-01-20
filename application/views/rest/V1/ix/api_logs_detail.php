<?php $this->load->view('include/header'); ?>
<style>

	label {
		margin-top:10px;
	}
</style>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<label>Date Time</label>
		<input type="text" class="form-control input-sm" value="<?php echo thai_date($date_upd, TRUE); ?>" disabled />
	</div>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<label>TransId</label>
		<input type="text" class="form-control input-sm" value="<?php echo $trans_id; ?>" disabled />
	</div>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<label>Api path</label>
		<input type="text" class="form-control input-sm" value="<?php echo $api_path; ?>" disabled />
	</div>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<label>Code</label>
		<input type="text" class="form-control input-sm" value="<?php echo $code; ?>" disabled />
	</div>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<label>Type</label>
		<input type="text" class="form-control input-sm" value="<?php echo $type; ?>" disabled />
	</div>

	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<label>Action</label>
		<input type="text" class="form-control input-sm" value="<?php echo $action; ?>" disabled />
	</div>

	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<label>Status</label>
		<input type="text" class="form-control input-sm" value="<?php echo $status; ?>" disabled />
	</div>

	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<label>Message</label>
		<textarea class="form-control input-sm" disabled><?php echo $message; ?></textarea>		
	</div>

	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<label>Request Json</label>
		<pre><?php echo json_encode(json_decode($request_json), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?></pre>
	</div>

	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<label>Response Json</label>
		<pre><?php echo json_encode(json_decode($response_json), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?></pre>
	</div>
</div>

<?php $this->load->view('include/footer'); ?>

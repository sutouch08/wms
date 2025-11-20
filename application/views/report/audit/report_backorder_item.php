<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5 padding-top-5">
    <h3 class="title">
      <i class="fa fa-bar-chart"></i>
      <?php echo $this->title; ?>
    </h3>
  </div>
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-white btn-success top-btn" onclick="getReport()"><i class="fa fa-bar-chart"></i> รายงาน</button>
		<button type="button" class="btn btn-white btn-primary top-btn" onclick="doExport()"><i class="fa fa-file-excel-o"></i> ส่งออก</button>
	</div>
</div><!-- End Row -->
<hr />
<div class="row">
	<div class="col-lg-2-harf col-md-3-harf col-sm-3-harf col-xs-6 padding-5">
		<label>วันที่</label>
		<div class="input-daterange input-group width-100">
			<input type="text" class="form-control input-sm width-50 r text-center from-date" id="fromDate" placeholder="เริ่มต้น" value="" />
			<input type="text" class="form-control input-sm width-50 r text-center" id="toDate" placeholder="สิ้นสุด" value=""/>
		</div>
	</div>

	<div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
    <label class="display-block">ประเภทเอกสาร</label>
    <div class="btn-group width-100" style="height:30px;">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-role-all" onclick="toggleAllRole(1)">ทั้งหมด</button>
      <button type="button" class="btn btn-sm width-50" id="btn-role-range" onclick="toggleAllRole(0)">เลือก</button>
    </div>
  </div>

	<div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
    <label class="display-block">ช่องทางการขาย</label>
    <div class="btn-group width-100" style="height:30px;">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-channels-all" onclick="toggleAllChannels(1)">ทั้งหมด</button>
      <button type="button" class="btn btn-sm width-50" id="btn-channels-range" onclick="toggleAllChannels(0)">เลือก</button>
    </div>
  </div>

	<div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
    <label class="display-block">คลังสินค้า</label>
    <div class="btn-group width-100" style="height:30px;">
      <button type="button" class="btn btn-sm btn-primary width-50" id="btn-wh-all" onclick="toggleAllWarehouse(1)">ทั้งหมด</button>
      <button type="button" class="btn btn-sm width-50" id="btn-wh-range" onclick="toggleAllWarehouse(0)">เลือก</button>
    </div>
  </div>


	<input type="hidden" id="allRole" name="allRole" value="1" />
  <input type="hidden" id="allChannels" name="allChannels" value="1" />
  <input type="hidden" id="allWarehouse" name="allWarehouse" value="1" />
</div>

<div class="modal fade" id="channels-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width:300px; max-width:95%; margin-left:auto; margin-right:auto;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="title">ระบุช่องทางการขาย</h4>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-top:15px; max-height:60vh; overflow:auto;">
        <?php if(!empty($channels_list)) : ?>
          <?php foreach($channels_list as $rs) : ?>
              <label class="display-block">
                <input type="checkbox" class="ace ch-chk" name="channels[]" value="<?php echo $rs->code; ?>"/>
                <span class="lbl">&nbsp; <?php echo $rs->name; ?></span>
              </label>
          <?php endforeach; ?>
        <?php endif;?>
						</div>
        		<div class="divider" ></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-block" data-dismiss="modal">ตกลง</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="warehouse-modal" tabindex="-1" role="dialog" aria-labelledby="warehouse-modal" aria-hidden="true">
	<div class="modal-dialog" style="max-width:95%; margin-left:auto; margin-right:auto;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="title">ระบุคลังสินค้า</h4>
			</div>
			<div class="modal-body" style="padding:0px;">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-top:15px; max-height: 60vh; overflow:auto;">
					<?php if(!empty($warehouse_list)) : ?>
						<?php foreach($warehouse_list as $rs) : ?>
							<label class="display-block">
								<input type="checkbox" class="ace wh-chk" name="warehouse[]" value="<?php echo $rs->code; ?>" />
								<span class="lbl">&nbsp; <?php echo $rs->code.' | '.$rs->name; ?></span>
							</label>
						<?php endforeach; ?>
					<?php endif;?>
				</div>

				<div class="divider" ></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default btn-block" data-dismiss="modal">ตกลง</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="role-modal" tabindex="-1" role="dialog" aria-labelledby="role-modal" aria-hidden="true">
	<div class="modal-dialog" style="width:250px; max-width:95%; margin-left:auto; margin-right:auto;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="title">เลือกประเภทเอกสาร</h4>
			</div>
			<div class="modal-body" style="padding:0px;">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-top:15px;">
					<label class="display-block"><input type="checkbox" class="ace role-chk" name="role[]" value="S" /><span class="lbl">  WO</span></label>
					<label class="display-block"><input type="checkbox" class="ace role-chk" name="role[]" value="C" /><span class="lbl">  WC</span></label>
					<label class="display-block"><input type="checkbox" class="ace role-chk" name="role[]" value="N" /><span class="lbl">  WT</span></label>
					<label class="display-block"><input type="checkbox" class="ace role-chk" name="role[]" value="P" /><span class="lbl">  WS</span></label>
					<label class="display-block"><input type="checkbox" class="ace role-chk" name="role[]" value="U" /><span class="lbl">  WU</span></label>
					<label class="display-block"><input type="checkbox" class="ace role-chk" name="role[]" value="T" /><span class="lbl">  WQ</span></label>
					<label class="display-block"><input type="checkbox" class="ace role-chk" name="role[]" value="Q" /><span class="lbl">  WV</span></label>
					<label class="display-block"><input type="checkbox" class="ace role-chk" name="role[]" value="L" /><span class="lbl">  WL</span></label>
				</div>
				<div class="divider" ></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default btn-block" data-dismiss="modal">ตกลง</button>
			</div>
		</div>
	</div>
</div>

<form id="exportForm" method="post" action="<?php echo $this->home; ?>/do_export">
	<input type="hidden" id="filter" name="filter" value="" />
	<input type="hidden" id="token" name="token"  value="<?php echo uniqid(); ?>">
</form>
<hr>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive" style="min-height:200px; max-height:500px; overflow:auto;">
		<table class="table table-striped table-bordered tableFixHead border-1" style="min-width:900px;">
			<thead>
				<tr class="font-size-11">
					<th class="fix-width-50 text-center fix-header">#</th>
					<th class="fix-width-100 text-center fix-header">วันที่</th>
					<th class="fix-width-100 text-center fix-header">ออเดอร์</th>
					<th class="min-width-200 fix-header">รหัสสินค้า</th>
					<th class="fix-width-150 text-right fix-header">ช่องทางขาย</th>
					<th class="fix-width-100 text-right fix-header">คลัง</th>
					<th class="fix-width-100 text-right fix-header">Order Qty</th>
					<th class="fix-width-100 text-center fix-header">Available Qty</th>
				</tr>
			</thead>
			<tbody id="result-table">

			</tbody>
		</table>
	</div>
</div>

<script id="template" type="text/x-handlebars-template">
	{{#each this}}
		{{#if nodata}}
			<tr><td colspan="8" class="text-center">--- ไม่พบรายการตามเงื่อนไขที่กำหนด ---</td></tr>
		{{else}}
		<tr class="font-size-11">
			<td class="text-center">{{no}}</td>
			<td class="text-center">{{date_upd}}</td>
			<td class="">{{order_code}}</td>
			<td class="">{{product_code}}</td>
			<td class="">{{channels}}</td>
			<td class="">{{warehouse}}</td>
			<td class="text-right">{{order_qty}}</td>
			<td class="text-right">{{available_qty}}</td>
		</tr>
		{{/if}}
	{{/each}}
</script>
<script src="<?php echo base_url(); ?>scripts/report/audit/backorder_item.js?v=<?php date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>

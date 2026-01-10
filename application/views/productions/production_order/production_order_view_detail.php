<?php $this->load->view('include/header'); ?>
<?php $this->load->view('productions/production_order/style'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
    <button type="button" class="btn btn-white btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i>&nbsp; กลับ</button>
		<div class="btn-group">
      <button data-toggle="dropdown" class="btn btn-primary btn-white dropdown-toggle margin-top-5" aria-expanded="false">
        Actions
        <i class="ace-icon fa fa-angle-down icon-on-right"></i>
      </button>
      <ul class="dropdown-menu dropdown-menu-right">
				<?php if($doc->Status == 'R' OR $doc->Status == 'C') : ?>
					<?php if( empty($doc->DocNum)) : ?>
						<li class="success">
							<a href="javascript:sendToSap('<?php echo $doc->code; ?>')"><i class="fa fa-send"></i> Send To SAP</a>
						</li>
					<?php endif; ?>
					<li class="info">
						<a href="javascript:printOrder('<?php echo $doc->code; ?>')"><i class="fa fa-print"></i> Print</a>
					</li>
					<?php if($doc->Status == 'R' && $this->pm->can_edit) : ?>
						<li class="success">
		          <a href="javascript:closeOrder('<?php echo $doc->code; ?>')"><i class="fa fa-check"></i> Close</a>
		        </li>
					<?php endif; ?>
					<?php if($doc->Status != 'D' && $this->pm->can_delete) : ?>
		        <li class="danger">
		          <a href="javascript:cancelOrder('<?php echo $doc->code; ?>')"><i class="fa fa-times"></i> Cancel</a>
		        </li>
					<?php endif; ?>
					<?php if($doc->Status != 'P' && $doc->Status != 'D' && ! empty($doc->inv_code)) : ?>
						<li class="divider"></li>
						<li class="primary">
		          <a href="javascript:createProductionTransfer('<?php echo $doc->code; ?>')"><i class="fa fa-plus"></i> Transfer Components</a>
		        </li>
						<li class="primary">
		          <a href="javascript:createGoodsIssue('<?php echo $doc->code; ?>')"><i class="fa fa-plus"></i> Issue Components</a>
		        </li>
						<li class="primary">
		          <a href="javascript:reportCompletion('<?php echo $doc->code; ?>')"><i class="fa fa-plus"></i> Report Completion</a>
		        </li>
					<?php endif; ?>
				<?php endif; ?>
      </ul>
    </div>
	</div>
</div><!-- End Row -->
<hr class=""/>
<div class="row">
  <!-- Left column -->
  <?php $this->load->view('productions/production_order/production_order_view_header_left'); ?>
  <!-- Right Column -->
  <?php $this->load->view('productions/production_order/production_order_view_header_right'); ?>

</div>
<hr class="padding-5">

<div class="row">
	<div class="col-lg-6 col-md-3 col-sm-3 hidden-xs">&nbsp;</div>
	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
		<label class="font-size-11">Transfer <?php echo empty($transferRef) ? '' : "&nbsp;[ ".count($transferRef)." ]"; ?></label>
		<div class="input-group">
			<select class="form-control input-xs" id="tq-list">
				<?php if( ! empty($transferRef)) : ?>
					<?php foreach($transferRef as $tr) : ?>
						<option value="<?php echo $tr->code; ?>"><?php echo $tr->code; ?></option>
					<?php endforeach; ?>
				<?php else : ?>
					<option value="">Not Found</option>
				<?php endif; ?>
			</select>
			<span class="input-group-btn padding-left-5">
				<button type="button" class="btn btn-minier btn-info link-button fix-width-40" onclick="viewTQ()"><i class="fa fa-external-link"></i></button>
			</span>
		</div>
	</div>
	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
		<label class="font-size-11">Goods Issue</label>
		<div class="input-group">
			<select class="form-control input-xs" id="gi-list">
				<option value="">Not Found</option>
			</select>
			<span class="input-group-btn padding-left-5">
				<button type="button" class="btn btn-minier btn-info link-button fix-width-40" onclick="viewGI()"><i class="fa fa-external-link"></i></button>
			</span>
		</div>
	</div>

	<div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
		<label class="font-size-11">Goods Receipt</label>
		<div class="input-group">
			<select class="form-control input-xs" id="gr-list">
				<option value="">Not Found</option>
			</select>
			<span class="input-group-btn padding-left-5">
				<button type="button" class="btn btn-minier btn-info link-button fix-width-40" onclick="viewGR()"><i class="fa fa-external-link"></i></button>
			</span>
		</div>
	</div>
</div>
<hr class="padding-5">
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive" style="min-height:300px; max-height:600px; overflow:scroll; padding:0px; border:solid 1px #dddddd;">
		<table class="table table-bordered tableFixHead" style="min-width:1255px; margin-bottom:20px;">
			<thead>
				<tr class="font-size-11">
					<th class="fix-width-25 middle text-center fix-no fix-header">#</th>
					<th class="fix-width-100 middle">Type</th>
					<th class="fix-width-200 middle">Item Code</th>
					<th class="min-width-250 middle">Item Description.</th>
					<th class="fix-width-80 middle">Base Qty.</th>
					<th class="fix-width-80 middle">Base Ratio</th>
					<th class="fix-width-80 middle">Planned Qty</th>
					<th class="fix-width-80 middle">Issued</th>
					<th class="fix-width-80 middle">Available</th>
					<th class="fix-width-80 middle">Uom</th>
					<th class="fix-width-100 middle">Warehouse</th>
					<th class="fix-width-100 middle">Issue Method</th>
				</tr>
			</thead>
			<tbody id="details-table">
				<?php $no = 1; ?>
				<?php if( ! empty($details)) : ?>
					<?php foreach($details as $rs) : ?>
						<?php $uid = $rs->uid; ?>
						<?php $issued = $this->production_order_model->get_issue_qty_by_item($rs->ItemCode, $doc->DocEntry, $rs->LineNum); ?>
						<?php $available = $this->stock_model->get_item_stock($rs->ItemCode, $rs->WhsCode); ?>
						<tr id="row-<?php echo $uid; ?>" data-uid="<?php echo $uid; ?>" class="font-size-11">
							<td class="middle text-center fix-no no" scope="row"><?php echo $no; ?></td>
							<td class="middle">
								<select class="form-control input-xs text-label" id="type-<?php echo $uid; ?>" disabled>
									<option value="4" <?php echo is_selected('4', strval($rs->ItemType)); ?>>Item</option>
									<option value="290" <?php echo is_selected('290', strval($rs->ItemType)); ?>>Resource</option>
									<option value="-18" <?php echo is_selected('-18', strval($rs->ItemType)); ?>>Text</option>
								</select>
							</td>
							<td class="middle">
								<input type="text" class="form-control input-xs text-label item-code r" data-uid="<?php echo $uid; ?>" id="item-code-<?php echo $uid; ?>" value="<?php echo $rs->ItemCode; ?>" readonly/>
							</td>
							<td class="middle">
								<input type="text" class="form-control input-xs text-label item-name" data-uid="<?php echo $uid; ?>" id="item-name-<?php echo $uid; ?>" value="<?php echo $rs->ItemName; ?>" readonly/>
							</td>
							<td class="middle">
								<input type="text" class="form-control input-xs text-label text-right base-qty r" data-uid="<?php echo $uid; ?>" id="base-qty-<?php echo $uid; ?>" value="<?php echo round($rs->BaseQty, 2); ?>" onchange="recalQty('<?php echo $uid; ?>')" readonly/>
							</td>
							<td class="middle">
								<input type="text" class="form-control input-xs text-label text-right" data-uid="<?php echo $uid; ?>" id="base-ratio-<?php echo $uid; ?>" value="<?php echo get_ratio($rs->BaseQty); ?>"  disabled/>
							</td>
							<td class="middle">
								<input type="text" class="form-control input-xs text-label text-right planned-qty r" data-uid="<?php echo $uid; ?>" id="planned-qty-<?php echo $uid; ?>" value="<?php echo round($rs->PlannedQty, 2); ?>"  readonly/>
							</td>
							<td class="middle">
								<input type="text" class="form-control input-xs text-label text-right" data-uid="<?php echo $uid; ?>" id="issued-<?php echo $uid; ?>" value="<?php echo number($issued, 2); ?>" disabled />
							</td>
							<td class="middle">
								<input type="text" class="form-control input-xs text-label text-right" data-uid="<?php echo $uid; ?>" id="available-<?php echo $uid; ?>" value="<?php echo number($available, 2); ?>" disabled />
							</td>
							<td class="middle">
								<input type="text" class="form-control input-xs text-label"
									data-uid="<?php echo $uid; ?>"
									id="uom-<?php echo $uid; ?>"
									data-uomentry="<?php echo $rs->UomEntry; ?>"
									data-uomcode="<?php echo $rs->UomCode; ?>"
									value="<?php echo $rs->Uom; ?>"  disabled/>
							</td>
							<td class="middle">
								<div class="width-100 wh">
									<input type="text" class="form-control input-xs text-label wh-input r" data-uid="<?php echo $uid; ?>" id="warehouse-<?php echo $uid; ?>" value="<?php echo $rs->WhsCode; ?>" readonly/>
								</div>
							</td>
							<td class="middle">
								<select class="form-control input-xs text-label" data-uid="<?php echo $uid; ?>" id="issue-type-<?php echo $uid; ?>" disabled>
									<option value="M" <?php echo is_selected('M', $rs->IssueType); ?>>Manual</option>
									<option value="B" <?php echo is_selected('B', $rs->IssueType); ?>>Backflush</option>
								</select>
							</td>
						</tr>
						<?php $no++; ?>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<div class="divider-hidden"></div>

<div class="row" style="margin-left:0px; margin-right:0px;">
	<div class="col-lg-6 col-md-6 col-sm-6">
		<div class="form-horizontal">
			<div class="form-group">
				<label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 padding-0">User</label>
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
					<input type="text" id="user" class="form-control input-xs" value="<?php echo $this->_user->uname; ?>" disabled/>
				</div>
			</div>
		</div>
		<div class="form-horizontal">
			<div class="form-group">
				<label class="col-lg-2 col-md-2 col-sm-3 col-xs-12 padding-0">Remark</label>
				<div class="col-lg-10 col-md-10 col-sm-9 col-xs-12 padding-5">
					<textarea class="form-control input-xs" id="remark" rows="2" disabled><?php echo $doc->Comments; ?></textarea>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="<?php echo base_url(); ?>scripts/productions/production_order/production_order.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/productions/production_order/production_order_add.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>

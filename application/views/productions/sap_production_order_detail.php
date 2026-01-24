
<?php $this->load->view('include/header'); ?>
<?php $this->load->view('productions/production_order/style'); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 padding-top-5">
		<h3 class="title text-center">Production Order</h3>
	</div>
</div>
<hr class=""/>
<div class="row">
  <!-- Left Column -->
  <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 padding-right-15" style="padding-left:17px;">
    <div class="form-horizontal">
			<div class="form-group">
  			<label class="sap-label col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-0">SAP No.</label>
  			<div class="col-lg-2 col-md-3 col-sm-3 col-xs-8 padding-5">
  				<input type="text" class="form-control input-xs" value="<?php echo $doc->DocNum; ?>" disabled/>
  			</div>
  		</div>

      <div class="form-group">
  			<label class="sap-label col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-0">IX No.</label>
  			<div class="col-lg-2 col-md-3 col-sm-3 col-xs-8 padding-5">
  				<input type="text" id="code" class="form-control input-xs" value="<?php echo empty($doc->U_ECOMNO) ? "" : $doc->U_ECOMNO; ?>" disabled/>
  			</div>
  		</div>

  		<div class="form-group">
        <label class="sap-label col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-0">Product No.</label>
        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-8 padding-5">
          <input type="text" id="product-code" class="form-control input-xs r" maxlength="50" value="<?php echo $doc->ItemCode; ?>" disabled />
        </div>
      </div>

  		<div class="form-group">
        <label class="sap-label col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-0">Description</label>
        <div class="col-lg-8 col-md-10 col-sm-10 col-xs-8 padding-5">
          <input type="text" class="form-control input-xs" maxlength="100" value="<?php echo $doc->ItemName; ?>" disabled/>
        </div>
      </div>

      <div class="form-group">
        <label class="sap-label col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-0">Planned Qty</label>
        <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-3 padding-left-5 padding-right-15">
          <input type="text" class="form-control input-xs text-right" value="<?php echo number($doc->PlannedQty, 2); ?>" disabled/>
        </div>

        <label class="sap-label col-lg-1 col-md-1-harf col-sm-1 col-xs-1-harf padding-0">Complete</label>
        <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-3 padding-left-5 padding-right-15">
          <input type="text" class="form-control input-xs text-right" value="<?php echo number($doc->CompleteQty, 2); ?>" disabled/>
        </div>

        <label class="sap-label col-lg-1 col-md-1-harf col-sm-1 col-xs-1-harf padding-0">Recject</label>
        <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-3 padding-left-5 padding-right-15">
          <input type="text"  class="form-control input-xs text-right" value="<?php echo number($doc->RejectQty, 2) ?>" disabled/>
        </div>
      </div>

      <div class="form-group">
        <label class="sap-label col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-0">Warehouse</label>
        <div class="col-lg-6 col-md-8 col-sm-8 col-xs-8 padding-5">
          <input type="text" class="form-control input-xs" value="<?php echo $doc->WhsCode .' | '.$doc->WhsName; ?>" disabled />
        </div>
      </div>
    </div>
  </div>

  <!-- Right Column -->
  <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 padding-left-15">
  	<div class="form-horizontal">
			<div class="form-group">
        <label class="sap-label col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-0">Type</label>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
					<input type="text" class="form-control input-xs" value="<?php echo status_type_text($doc->Type); ?>" disabled />
        </div>
      </div>

      <div class="form-group">
        <label class="sap-label col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-0">Status</label>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
					<input type="text" class="form-control input-xs" value="<?php echo production_order_sap_status_text($doc->Status); ?>" disabled />
        </div>
      </div>

  		<div class="form-group">
  			<label class="sap-label col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-0">Order Date</label>
  			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
  				<input type="text" class="form-control input-xs r" value="<?php echo thai_date($doc->PostDate); ?>" disabled/>
  			</div>
  		</div>

  		<div class="form-group">
  			<label class="sap-label col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-0">Start Date</label>
  			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
  				<input type="text" class="form-control input-xs r" value="<?php echo thai_date($doc->ReleaseDate); ?>" disabled/>
  			</div>
  		</div>

  		<div class="form-group">
  			<label class="sap-label col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-0">Due Date</label>
  			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
  				<input type="text" class="form-control input-xs r" value="<?php echo thai_date($doc->DueDate); ?>" disabled/>
  			</div>
  		</div>


  	</div>
  </div>

</div>
<hr class="padding-5">
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive" style="min-height:300px; max-height:600px; overflow:scroll; padding:0px; border:solid 1px #dddddd;">
		<table class="table table-bordered tableFixHead" style="min-width:1100px; margin-bottom:20px;">
			<thead>
				<tr class="font-size-11">
					<th class="fix-width-25 middle text-center fix-no fix-header">#</th>
					<th class="fix-width-60 middle">Type</th>
					<th class="fix-width-200 middle">Item Code</th>
					<th class="min-width-250 middle">Item Description.</th>
					<th class="fix-width-60 middle">Base Qty.</th>
					<th class="fix-width-60 middle">Ratio</th>
					<th class="fix-width-80 middle">Planned</th>
					<th class="fix-width-80 middle">Issued</th>
					<th class="fix-width-80 middle">Uom</th>
					<th class="fix-width-100 middle">Warehouse</th>
				</tr>
			</thead>
			<tbody id="details-table">
				<?php $no = 1; ?>
				<?php if( ! empty($details)) : ?>
					<?php foreach($details as $rs) : ?>
						<tr class="font-size-11">
							<td class="middle text-center fix-no no" scope="row"><?php echo $no; ?></td>
							<td class="middle text-center"><?php echo $rs->ItemType == '290' ? 'Resource' : 'Item'; ?></td>
							<td class="middle">
								<input type="text" class="form-control input-xs text-label" value="<?php echo $rs->ItemCode; ?>" readonly/>
							</td>
							<td class="middle">
								<input type="text" class="form-control input-xs text-label" value="<?php echo $rs->ItemName; ?>" readonly/>
							</td>
							<td class="middle text-right"><?php echo number($rs->BaseQty, 2); ?></td>
							<td class="middle text-right"><?php echo get_ratio($rs->BaseQty); ?></td>
							<td class="middle text-right"><?php echo number($rs->PlannedQty, 2); ?></td>
							<td class="middle text-right"><?php echo number($rs->issued, 2); ?></td>
							<td class="middle text-center"><?php echo $rs->UomName; ?></td>
							<td class="middle text-center"><?php echo $rs->wareHouse; ?></td>
						</tr>
						<?php $no++; ?>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<?php $this->load->view('include/footer'); ?>

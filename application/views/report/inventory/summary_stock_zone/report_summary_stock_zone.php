<?php $this->load->view('include/header'); ?>
<?php $this->load->view('report/inventory/summary_stock_zone/style.php'); ?>
<div class="row hidden-print">
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><i class="fa fa-bar-chart"></i>  <?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5 text-right">
    <button type="button" class="btn btn-sm btn-success top-btn" onclick="getReport()"><i class="fa fa-bar-chart"></i> รายงาน</button>
  </div>
</div><!-- End Row -->
<hr class="hidden-print"/>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<label>
			<span class="label label-white middle">
				<input type="radio" class="ace chk-stock" name="stockOption" value="E" checked />
				<span class="lbl">&nbsp; เฉพาะโซนที่ว่าง</span>
			</span>
		</label>
		<label>
			<span class="label label-white middle">
				<input type="radio" class="ace chk-stock" name="stockOption" value="S"/>
				<span class="lbl">&nbsp; เฉพาะโซนที่น้อยกว่า 1,000</span>
			</span>
		</label>
		<label style="margin-right:15px;">
			<span class="label label-white middle">
				<input type="radio" class="ace chk-stock" name="stockOption" value="A" />
				<span class="lbl">&nbsp; ทุกโซน</span>
			</span>
		</label>

		<label>
			<span class="label label-purple label-white middle">
				<input type="checkbox" class="ace" onchange="toggleOption($(this))" />
				<span class="lbl">&nbsp; เลือกทั้งหมด</span>
			</span>
		</label>
		<?php if( ! empty($rows)) : ?>
			<?php foreach($rows as $row) : ?>
				<label>
					<span class="label label-purple label-white middle">
						<input type="checkbox" class="ace chk-row" value="<?php echo $row; ?>" />
						<span class="lbl">&nbsp; ROW - <?php echo $row; ?></span>
					</span>
				</label>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>

	<div class="divider-hidden"></div>
	<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 padding-5">
		<div class="input-group">
			<span class="input-group-addon font-size-11" style="padding:3px 6px;">
				<label style="font-size: 11px; margin:0;">
					<input type="checkbox" class="ace" id="item-option" value="1" /><span class="lbl">&nbsp; รหัสสินค้า</span>
				</label>
			</span>
			<input type="text" class="form-control input-xs font-size-11" style="height:28px;" id="item-code" />
		</div>
	</div>

	<div class="col-lg-8 col-md-8 col-sm-6 col-xs-12 padding-5 text-right hidden-xs">
		<label class="label-1000">จำนวนมากกว่า 1,000</label>
		<label class="label-100">จำนวนน้อยกว่า 1,000</label>
		<label class="label-0">โซนว่าง</label>
	</div>
</div>


<hr class="">

<div class="row" style="margin-left:-6px; margin-right:-6px;">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding:10px;" id="result">

	</div>
</div>

<script id="template" type="text/x-handlebarsTemplate">
  {{#each this}}
		<div class="pointer box {{color}}" {{{link}}}>{{name}}<br/>{{qty}}</div>
  {{/each}}
</script>


<script src="<?php echo base_url(); ?>scripts/report/inventory/summary_stock_zone.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer'); ?>

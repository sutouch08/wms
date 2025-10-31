	 <div class="nav-title">
	 	<span class="back-link" onclick="closeTab()"><i class="fa fa-angle-left fa-lg"></i> ย้อนกลับ</span>
	 	<span class="font-size-14 text-left">IX API Setting</span>
	 </div>
	<form id="ixForm" class="margin-top-60" method="post" action="<?php echo $this->home; ?>/update_config">
  	<div class="row">
			<div class="col-xs-8 padding-top-5">IX API</div>
			<div class="col-xs-4 text-right">
				<label style="padding-top:5px; margin-bottom:0px;">
					<input class="ace ace-switch ace-switch-7" data-name="IX_API" type="checkbox" value="1" <?php echo is_checked($IX_API , '1'); ?> onchange="toggleOption($(this))"/>
					<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
				</label>
				<input type="hidden" name="IX_API" id="ix-api" value="<?php echo $IX_API; ?>" />
			</div>
			<div class="divider"></div>

			<div class="col-xs-12">IX Warehouse</div>
			<div class="col-xs-12">
				<select class="width-100" id="ix-warehouse" name="IX_WAREHOUSE">
					<option value="">เลือกคลัง</option>
					<?php echo select_sell_warehouse($DEFAULT_WAREHOUSE); ?>
				</select>
			</div>
			<div class="divider-hidden"></div>

			<div class="col-xs-6 padding-top-5">IX Zone</div>
			<div class="col-xs-6 text-right">
				<input type="text" class="width-100 text-center" id="ix-zone" name="IX_ZONE" value="<?php echo $IX_ZONE; ?>"/>
			</div>
			<div class="divider"></div>

			<div class="col-xs-12">IX Return Warehouse</div>
			<div class="col-xs-12">
				<select class="width-100" id="ix-return-warehouse" name="IX_RETURN_WAREHOUSE">
					<option value="">เลือกคลัง</option>
					<?php echo select_sell_warehouse($IX_RETURN_WAREHOUSE); ?>
				</select>
			</div>
			<div class="divider-hidden"></div>

			<div class="col-xs-6 padding-top-5">IX Return Zone</div>
			<div class="col-xs-6 text-right">
				<input type="text" class="width-100 text-center" id="ix-return-zone" name="IX_RETURN_ZONE" value="<?php echo $IX_RETURN_ZONE; ?>"/>
			</div>
			<div class="divider"></div>

			<div class="col-xs-8 padding-top-5">SYNC API STOCK</div>
			<div class="col-xs-4 text-right">
				<label style="padding-top:5px; margin-bottom:0px;">
					<input class="ace ace-switch ace-switch-7" data-name="SYNC_IX_STOCK" type="checkbox" value="1" <?php echo is_checked($SYNC_IX_STOCK , '1'); ?> onchange="toggleOption($(this))"/>
					<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
				</label>
				<input type="hidden" name="SYNC_IX_STOCK" id="sync-ix-stock" value="<?php echo $SYNC_IX_STOCK; ?>" />
			</div>
			<div class="divider-hidden"></div>

			<div class="col-xs-8 padding-top-5">GLOBAL STOCK RATE</div>
			<div class="col-xs-4 text-right">
				<div class="input-group">
					<input type="number" class="form-control input-sm text-center" id="ix-stock-rate" name="IX_STOCK_RATE" value="<?php echo $IX_STOCK_RATE; ?>" onchange="checkValidRate()" />
					<span class="input-group-addon">%</span>
				</div>
			</div>
			<div class="divider-hidden"></div>

			<div class="col-xs-7 padding-top-5">STOCK RATE BY</div>
			<div class="col-xs-5 text-right">
				<select class="width-100" id="ix-stock-rate-type" name="IX_STOCK_RATE_TYPE">
					<option value="0" <?php echo is_selected('0', $IX_STOCK_RATE_TYPE); ?>>GLOBAL RATE</option>
					<option value="1" <?php echo is_selected('1', $IX_STOCK_RATE_TYPE); ?>>ITEM RATE</option>
				</select>
			</div>
			<div class="divider"></div>


			<div class="col-xs-8 padding-top-5">Logs Json</div>
			<div class="col-xs-4 text-right">
				<label style="padding-top:5px; margin-bottom:0px;">
					<input class="ace ace-switch ace-switch-7" data-name="IX_LOG_JSON" type="checkbox" value="1" <?php echo is_checked($IX_LOG_JSON , '1'); ?> onchange="toggleOption($(this))"/>
					<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
				</label>
				<input type="hidden" name="IX_LOG_JSON" id="ix-log-json" value="<?php echo $IX_LOG_JSON; ?>" />
			</div>
			<div class="divider"></div>

			<div class="col-xs-8 padding-top-5">Test Mode</div>
			<div class="col-xs-4 text-right">
				<label style="padding-top:5px; margin-bottom:0px;">
					<input class="ace ace-switch ace-switch-7" data-name="IX_TEST" type="checkbox" value="1" <?php echo is_checked($IX_TEST , '1'); ?> onchange="toggleOption($(this))"/>
					<span class="lbl margin-left-0" data-lbl="OFF&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ON"></span>
				</label>
				<input type="hidden" name="IX_TEST" id="ix-test" value="<?php echo $IX_TEST; ?>" />
			</div>
			<div class="divider"></div>
			<div class="divider-hidden"></div>
			<div class="divider-hidden"></div>

			<div class="col-xs-12">
				<?php if($this->pm->can_add OR $this->pm->can_edit) : ?> <?php //if($this->_SuperAdmin) : ?>
        <button type="button" class="btn btn-sm btn-success btn-block" onClick="updateConfig('ixForm')">SAVE</button>
				<?php endif; ?>
      </div>
      <div class="divider-hidden"></div>

  	</div><!--/ row -->
  </form>

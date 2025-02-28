<div class="move-table" id="move-table">
	<div class="nav-title">รายการโอนย้าย</div>
	<div class="col-xs-12 padding-0 table-responsive" style="margin-bottom:180px; border-bottom:solid 1px #ccc;">
		<table class="table table-striped table-bordered" style="min-width:630px;">
			<thead>
				<tr>
					<th class="fix-width-40 text-center">#</th>
					<th class="fix-width-40"></th>
					<th class="min-width-150">สินค้า</th>
					<th class="fix-width-100 text-center">ยอดตั้ง</th>
					<th class="fix-width-100 text-center">ยอดรับ</th>
					<th class="fix-width-100">ต้นทาง</th>
					<th class="fix-width-100">ปลายทาง</th>
				</tr>
			</thead>
			<tbody id="move-list">
				<?php $no = 1; ?>
				<?php $move_total = 0; ?>
				<?php $wms_total = 0; ?>
				<?php if( ! empty($details)) : ?>
					<?php foreach($details as $rs) : ?>
						<tr class="font-size-12" id="row-<?php echo $rs->id; ?>">
							<td class="middle text-center mo"><?php echo $no; ?></td>
							<td class="middle text-center">
								<?php if($rs->valid == 0) : ?>
									<button type="button" class="btn btn-minier btn-danger"
									onclick="rollBackToTemp(<?php echo $rs->id; ?>, '<?php echo $rs->product_code; ?>')">
				          <i class="fa fa-trash"></i></button>
								<?php endif; ?>
							</td>
							<td class="middle"><?php echo $rs->product_code; ?></td>
							<td class="middle text-center qty"><?php echo number($rs->qty); ?></td>
							<td class="middle text-center wms" id="wms-<?php echo $rs->id; ?>"><?php echo number($rs->wms_qty); ?></td>
							<td class="middle hide-text"><?php echo $rs->from_zone; ?></td>
							<td class="middle hide-text"><?php echo $rs->to_zone; ?></td>
						</tr>
						<?php $no++; ?>
						<?php $move_total += $rs->qty; ?>
						<?php $wms_total += $rs->wms_qty; ?>
					<?php endforeach; ?>
				<?php else : ?>
					<tr>
						<td colspan="8" class="text-center"><h4>ไม่พบรายการ</h4></td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
	<div class="col-xs-12 text-center total-move">
		<span class="pull-left">Total</span><span id="wms-total"><?php echo number($wms_total); ?></span> / <span id="move-total"><?php echo number($move_total); ?></span>
	</div>
</div>

<script id="moveTableTemplate" type="text/x-handlebars-template">
{{#each this}}
	{{#if nodata}}
	<tr>
		<td colspan="8" class="text-center"><h4>ไม่พบรายการ</h4></td>
	</tr>
	{{else}}
		{{#if @last}}
		<tr style="font-size:18px;">
			<td colspan="3" class="text-right">Total</td>
			<td class="middle text-center" id="move-table-total-qty">0</td>
			<td class="middle text-center" id="move-table-total-wms">0</td>
			<td colspan="2"></td>
		</tr>
		{{else}}
		<tr class="font-size-12" id="row-{{ id }}">
			<td class="middle text-center mo">{{ no }}</td>
			<td class="middle text-center">{{{ btn_delete }}}</td>
			<td class="middle">{{ product_code }}</td>
			<td class="middle text-center qty">{{ qty_label }}</td>
			<td class="middle text-center wms" id="wms-{{id}}">{{ wms_qty_label }}</td>
			<td class="middle hide-text">{{ from_zone }}</td>
			<td class="middle hide-text">{{{ to_zone }}}</td>
		</tr>
		{{/if}}
	{{/if}}
{{/each}}
</script>


<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-sm-12">
		<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
		<button type="button" class="btn btn-sm btn-primary top-btn" onclick="newItems()">สร้างรายการสินค้า</button>
		<button type="button" class="btn btn-sm btn-info top-btn" onclick="setImages()">เชื่อมโยงรูปภาพ</button>
		<button type="button" class="btn btn-sm btn-warning top-btn" onclick="setBarcodeForm()">Generate Barcode</button>
		<button type="button" class="btn btn-sm btn-purple top-btn" onclick="downloadBarcode('<?php echo $style->code; ?>')">Download Barcode</button>
		<button type="button" class="btn btn-sm btn-info top-btn" onclick="doExport('<?php echo $style->code; ?>')"><i class="fa fa-send"></i> ส่งไป SAP </button>

		<button type="button" class="btn btn-sm btn-yellow top-btn" onclick="checkOldCode('<?php echo $style->code; ?>','<?php echo $style->old_code; ?>')">
			Generate รหัสเก่า
		</button>

		<?php if(is_true(getConfig('WEB_API')) === TRUE) : ?>
			<button type="button" class="btn btn-sm btn-success top-btn" onclick="sendToWeb('<?php echo $style->code; ?>')"><i class="fa fa-send"></i> ส่งไป Magento</button>
		<?php endif; ?>
		<?php endif; ?>
	</div>
</div>
<hr/>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-hover" style="min-width:1150px;">
			<thead>
				<tr>
					<th class="fix-width-60 text-center">รูปภาพ</th>
					<th class="fix-width-200">รหัสสินค้า</th>
					<th class="fix-width-200">รหัสเก่า</th>
					<th class="fix-width-120">บาร์โค้ด</th>
					<th class="fix-width-60 text-center">สี</th>
					<th class="fix-width-60 text-center">ไซส์</th>
					<th class="fix-width-100 text-right">ทุน</th>
					<th class="fix-width-100 text-right">ราคา</th>
					<th class="fix-width-50 text-center">ขาย</th>
					<th class="fix-width-50 text-center">เปิด</th>
					<th class="fix-width-50 text-center">API</th>
					<th class="min-width-100"></th>
				</tr>
			</thead>
			<tbody>
<?php if(!empty($items)) : ?>
	<?php foreach($items as $item) : ?>
		<?php $img = get_product_image($item->code, 'mini'); ?>
				<tr id="row-<?php echo $item->code; ?>" style="font-size:12px;">
					<td class="middle text-center">
						<img src="<?php echo $img; ?>" style="width:50px;" />
					</td>
					<td class="middle"><?php echo $item->code; ?></td>
					<td class="middle"><?php echo $item->old_code; ?></td>

					<td class="middle">
						<span class="lb" id="bc-lbl-<?php echo $item->code; ?>"><?php echo $item->barcode; ?></span>
						<input type="text"
						class="form-control input-sm barcode edit hide tooltip-error"
						name="bc[<?php echo $item->code; ?>]"
						id="bc-<?php echo $item->code; ?>"
						value="<?php echo $item->barcode; ?>"
						data-toggle="tooltip" data-placement="right" title=""
						/>
					</td>
					<td class="middle text-center"><?php echo $item->color_code; ?></td>
					<td class="middle text-center"><?php echo $item->size_code; ?></td>
					<td class="middle text-right">
						<span class="lb" id="cost-lbl-<?php echo $item->code; ?>">
						<?php echo number($item->cost, 2); ?>
						</span>
						<input type="number"
						class="form-control input-sm text-center cost edit hide"
						name="cost[<?php echo $item->code; ?>]"
						id="cost-<?php echo $item->code; ?>"
						value="<?php echo $item->cost; ?>"
						/>
					</td>
					<td class="middle text-right">
						<span class="lb" id="price-lbl-<?php echo $item->code; ?>">
						<?php echo number($item->price, 2); ?>
						</span>
						<input type="number"
						class="form-control input-sm text-center price edit hide"
						name="price[<?php echo $item->code; ?>]"
						id="price-<?php echo $item->code; ?>"
						value="<?php echo $item->price; ?>"
						 />
					</td>

					<td class="middle text-center">
						<?php if($this->pm->can_edit) : ?>
							<a href="javascript:void(0)" class="can-sell" data-code="<?php echo $item->code; ?>">
								<?php echo is_active($item->can_sell); ?>
							</a>
						<?php else : ?>
						<?php echo is_active($item->can_sell); ?>
						<?php endif; ?>
					</td>

					<td class="middle text-center">
						<?php if($this->pm->can_edit) : ?>
							<a href="javascript:void(0)" class="act" data-code="<?php echo $item->code; ?>">
								<?php echo is_active($item->active); ?>
							</a>
						<?php else : ?>
						<?php echo is_active($item->active); ?>
						<?php endif; ?>
					</td>

					<td class="middle text-center">
						<?php if($this->pm->can_edit) : ?>
							<a href="javascript:void(0)" class="api" data-code="<?php echo $item->code; ?>">
								<?php echo is_active($item->is_api); ?>
							</a>
						<?php else : ?>
						<?php echo is_active($item->is_api); ?>
						<?php endif; ?>
					</td>
					<td class="middle text-right">
						<?php if($this->pm->can_edit) : ?>
							<button type="button" class="btn btn-mini btn-warning lb" id="btn-edit-<?php echo $item->code; ?>" onclick="editItem('<?php echo $item->code; ?>')">
								<i class="fa fa-pencil"></i>
							</button>
							<button type="button" class="btn btn-mini btn-success edit hide" id="btn-update-<?php echo $item->code; ?>" onclick="updateItem('<?php echo $item->code; ?>')">
								<i class="fa fa-save"></i>
							</button>
						<?php endif; ?>
						<?php if($this->pm->can_delete) : ?>
							<button type="button" class="btn btn-mini btn-danger" onclick="deleteItem('<?php echo $item->code; ?>', '<?php echo $style->code; ?>')">
								<i class="fa fa-trash"></i>
							</button>
						<?php endif; ?>
					</td>
				</tr>
	<?php endforeach; ?>
<?php else : ?>
				<tr>
					<td colspan="11" class="text-center">---- No Item -----</td>
				</tr>
<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>


<form id="mappingForm" method="post" action="<?php echo $this->home; ?>/mapping_image">
	<input type="hidden" name="styleCode" value="<?php echo $style->code; ?>" />
	<div class="modal fade" id="imageMappingTable" tabindex="-1" role="dialog" aria-labelledby="mapping" aria-hidden="true">
		<div class="modal-dialog" style="width:1000px">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">จับคู่รูปภาพกับสินค้า</h4>
				</div>
				<div class="modal-body">
					<div class="table-responsive" id="mappingBody"></div>

					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">ปิด</button>
						<button type="submit" class="btn btn-sm btn-primary">ดำเนินการ</button>
					</div>
				</div>
			</div>
		</div>
</form>


<div class="modal fade" id="barcodeOption" tabindex="-1" role="dialog" aria-labelledby="bcGen" aria-hidden="true">
	<div class="modal-dialog" style="width:500px;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Generate Barcode</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12 text-center">
						<label style="margin:20px;"><input type="radio" class="ace" name="barcodeType" value="1" checked /><span class="lbl"> บาร์โค้ดภายใน</span></label>
						<label><input type="radio" class="ace" name="barcodeType" value="2" /><span class="lbl"> บาร์โค้ดสากล</span></label>
					</div>
				</div>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">ปิด</button>
					<button type="button" class="btn btn-sm btn-primary" onclick="startGenerate()">ดำเนินการ</button>
				</div>
			</div>
		</div>
	</div>

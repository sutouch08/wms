<?php $this->load->view('include/header'); ?>
<style>
	.table-narrow thead tr th,
	.table-narrow tbody tr td {
		font-size:11px !important;
		padding: 3px;
	}
</style>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
    <button type="button" class="btn btn-white btn-default top-btn" onclick="window.close()"><i class="fa fa-times"></i> ปิด</button>
		<button type="button" class="btn btn-white btn-purple top-btn" onclick="removeFromPending()"><i class="fa fa-times"></i> Remove from Pending</button>
	</div>
</div><!-- End Row -->
<hr class=""/>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-bordered table-narrow border-1">
      <thead>
        <tr>
					<th class="fix-width-40 text-center fix-header">
						<label>
							<input type="checkbox" class="ace chk-all" onchange="checkAll($(this))" />
							<span class="lbl"></span>
						</label>
					</th>
          <th class="fix-width-50 text-center fix-header">#</th>
          <th class="fix-width-150 fix-header">เลขที่</th>
					<th class="fix-width-100 fix-header">คลัง</th>
          <th class="fix-width-150 fix-header">อ้างอิง</th>
					<th class="fix-width-150 fix-header">ช่องทางขาย</th>
					<th class="fix-width-100 fix-header">แพ็คโดย</th>
					<th class="fix-width-120 fix-header">เวลาแพ็ค</th>
          <th class="min-width-200 fix-header">ลูกค้า</th>
					<th class="fix-width-100 fix-header">จำนวน(กล่อง)</th>
        </tr>
      </thead>
      <tbody id="dispatch-table">
				<?php $totalQty = 0; ?>
        <?php if( ! empty($orders)) : ?>
          <?php $no = 1; ?>
          <?php $channels = get_channels_array(); ?>
          <?php foreach($orders as $rs) : ?>
						<?php $qty = $this->dispatch_model->count_order_box($rs->code); ?>
						<?php $state_time = $this->dispatch_model->get_order_state_timestamp($rs->code); ?>
            <tr class="font-size-11" id="row-<?php echo $no; ?>">
							<td class="middle text-center">
								<label>
									<input type="checkbox" class="ace chk" value="<?php echo $rs->code; ?>" data-no="<?php echo $no; ?>" />
									<span class="lbl"></span>
								</label>
							</td>
              <td class="middle text-center no"><?php echo $no; ?></td>
              <td class="middle"><a href="javascript:viewOrderDetail('<?php echo $rs->code; ?>', '<?php echo $rs->role; ?>')"><?php echo $rs->code; ?>&nbsp; <i class="fa fa-external-link"></i></a></td>							
							<td class="middle"><?php echo $rs->warehouse_code; ?></td>
              <td class="middle"><?php echo $rs->reference; ?></td>
              <td class="middle"><?php echo empty($channels[$rs->channels_code]) ? NULL : $channels[$rs->channels_code]; ?></td>
							<td class="middle"><?php echo empty($state_time) ? NULL : $state_time->update_user; ?></td>
							<td class="middle"><?php echo empty($state_time) ? NULL : thai_date($state_time->date_upd, TRUE); ?></td>
							<td class="middle"><?php echo $rs->customer_code." : ".$rs->customer_name; ?></td>
							<td class="middle text-center">
								<input type="number" class="form-control input-xs text-label text-center carton-qty" value="<?php echo $qty; ?>" readonly />
							</td>
            </tr>
            <?php $no++; ?>
						<?php $totalQty += $qty; ?>
          <?php endforeach; ?>
        <?php else : ?>
          <tr>
            <td colspan="10" class="text-center">---- ไม่พบรายการ ----</td>
          </tr>
        <?php endif; ?>
      </tbody>
			<tfoot>
				<tr>
					<td colspan="9" class="text-right">รวม</td>					
					<td style="padding:0px;"><input type="number" class="form-control input-sm text-label text-center" id="total-carton" value="<?php echo $totalQty; ?>" readonly/></td>
				</tr>
			</tfoot>
    </table>
  </div>
</div>

<script src="<?php echo base_url(); ?>scripts/inventory/dispatch/dispatch.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/dispatch/dispatch_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script>
	function checkAll(el) {
		if(el.is(':checked')) {
			$('.chk').prop('checked', true);
		}
		else {
			$('.chk').prop('checked', false);
		}
	}

	function removeFromPending() {
		let h = [];
		let rows = [];

		$('.chk:checked').each(function() {
			let code = $(this).val().trim();
			let no = $(this).data('no');

			h.push(code);
			rows.push(no);
		});

		if(h.length) {
			load_in();

			$.ajax({
				url:HOME + 'remove_orders_from_pending',
				type:'POST',
				cache:false,
				data:{
					'data' : JSON.stringify(h)
				},
				success:function(rs) {
					load_out();

					if(rs.trim() === 'success') {
						if(rows.length) {
							rows.forEach(function(no) {
								$('#row-'+no).remove();
							});
						}

						reIndex();

						recalTotal();
					}
					else {
						showError(rs);
					}
				},
				error:function(rs) {
					showError(rs);
				}
			})
		}
	}

	function recalTotal() {
		let total = 0;

		$('.carton-qty').each(function() {
			let qty = parseDefaultInt($(this).val(), 0);
			total += qty;
		});

		$('#total-carton').val(total);
	}	
</script>

<?php $this->load->view('include/footer'); ?>

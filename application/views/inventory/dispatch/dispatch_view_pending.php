<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
    <button type="button" class="btn btn-white btn-default top-btn" onclick="window.close()"><i class="fa fa-times"></i> ปิด</button>
	</div>
</div><!-- End Row -->
<hr class=""/>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-bordered">
      <thead>
        <tr>
          <th class="fix-width-50 text-center fix-header">#</th>
          <th class="fix-width-150 fix-header">เลขที่</th>
					<th class="fix-width-100 fix-header">จำนวน(กล่อง)</th>
          <th class="fix-width-150 fix-header">อ้างอิง</th>
					<th class="fix-width-150 fix-header">ช่องทางขาย</th>
          <th class="min-width-200 fix-header">ลูกค้า</th>
        </tr>
      </thead>
      <tbody id="dispatch-table">
				<?php $totalQty = 0; ?>
        <?php if( ! empty($orders)) : ?>
          <?php $no = 1; ?>
          <?php $channels = get_channels_array(); ?>
          <?php foreach($orders as $rs) : ?>
						<?php $qty = $this->dispatch_model->count_order_box($rs->code); ?>
            <tr class="font-size-11">
              <td class="text-center"><?php echo $no; ?></td>
              <td><?php echo $rs->code; ?></td>
							<td class="text-center"><?php echo $qty; ?></td>
              <td><?php echo $rs->reference; ?></td>
              <td><?php echo empty($channels[$rs->channels_code]) ? NULL : $channels[$rs->channels_code]; ?></td>
							<td><?php echo $rs->customer_code." : ".$rs->customer_name; ?></td>
            </tr>
            <?php $no++; ?>
						<?php $totalQty += $qty; ?>
          <?php endforeach; ?>
        <?php else : ?>
          <tr>
            <td colspan="6" class="text-center">---- ไม่พบรายการ ----</td>
          </tr>
        <?php endif; ?>
      </tbody>
			<tfoot>
				<tr>
					<td colspan="2" class="text-right">รวม</td>
					<td style="padding:0px;"><input type="number" class="form-control input-sm text-label text-center" id="total-carton" value="<?php echo $totalQty; ?>" readonly/></td>
					<td colspan="3" class="text-right"></td>
				</tr>
			</tfoot>
    </table>
  </div>
</div>

<script src="<?php echo base_url(); ?>scripts/inventory/dispatch/dispatch.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/dispatch/dispatch_add.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>

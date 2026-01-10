<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive padding-5" id="transfer-table">
  	<table class="table table-striped border-1" style="min-width:1150px;">
    	<thead>
      	<tr class="font-size-11">
        	<th class="fix-width-50 text-center">#</th>
          <th class="fix-width-100">Barcode</th>
          <th class="fix-width-150">Item</th>
					<th class="min-width-250">Description</th>
          <th class="fix-width-200">ต้นทาง</th>
          <th class="fix-width-200">ปลายทาง</th>
          <th class="fix-width-80 text-center">จำนวนออก</th>
					<th class="fix-width-80 text-center">จำนวนเข้า</th>
        </tr>
      </thead>

      <tbody id="transfer-list">
<?php if(!empty($details)) : ?>
<?php		$no = 1;						?>
<?php   $total_qty = 0; ?>
<?php 	$total_receive = 0; ?>
<?php		foreach($details as $rs) : 	?>
	<?php $color = $rs->valid == 0 ? 'invalid' : ''; ?>
				<tr class="font-size-11 <?php echo $color; ?>" id="row-<?php echo $rs->id; ?>">
	      	<td class="middle text-center"><?php echo $no; ?></td>
	        <td class="middle"><?php echo $rs->barcode; ?></td>
	        <td class="middle"><input type="text" class="form-control input-xs text-label <?php echo $color; ?>" value="<?php echo $rs->product_code; ?>" readonly /></td>
					<td class="middle"><input type="text" class="form-control input-xs text-label <?php echo $color; ?>" value="<?php echo $rs->product_name; ?>" readonly /></td>
	        <td class="middle"><input type="text" class="form-control input-xs text-label <?php echo $color; ?>" value="<?php echo $rs->from_zone.' | '.$rs->from_zone_name; ?>" readonly /></td>
	        <td class="middle"><input type="text" class="form-control input-xs text-label <?php echo $color; ?>" value="<?php echo $rs->to_zone.' | '.$rs->to_zone_name; ?>" readonly /></td>
					<td class="middle text-center"><?php echo number($rs->qty); ?></td>
					<td class="middle text-center"><?php echo number($rs->wms_qty); ?></td>
	      </tr>
<?php			$no++;			?>
<?php     $total_qty += $rs->qty; ?>
<?php 		$total_receive += $rs->wms_qty; ?>
<?php		endforeach;			?>
				<tr class="font-size-11">
					<td colspan="6" class="middle text-right"><strong>รวม</strong></td>
					<td class="middle text-center"><strong><?php echo number($total_qty); ?></strong></td>
					<td class="middle text-center"><strong><?php echo number($total_receive); ?></strong></td>
				</tr>
<?php	else : ?>
 				<tr>
        	<td colspan="8" class="text-center"><h4>ไม่พบรายการ</h4></td>
        </tr>
<?php	endif; ?>
      </tbody>
    </table>
  </div>

	<div class="col-lg-6 col-md-5 col-sm-6 col-xs-12 padding-5">

		<?php if( ! empty($doc->must_accept == 1 &&  ! empty($accept_list))) : ?>
			<?php if($doc->is_accept == 1 && $doc->accept_by != NULL) : ?>
				<p class="green">ยืนยันโดย : <?php echo $doc->display_name; ?> @ <?php echo thai_date($doc->accept_on, TRUE); ?><br/>
					Note : <?php echo $doc->accept_remark; ?></p>
			<?php else : ?>
				<?php foreach($accept_list as $ac) : ?>
					<?php if($ac->is_accept == 1) : ?>
						<p class="green">ยืนยันโดย : <?php echo $ac->display_name; ?> @ <?php echo thai_date($ac->accept_on, TRUE); ?></p>
					<?php endif; ?>
				<?php endforeach; ?>
			<?php endif; ?>
		<?php endif; ?>
	</div>
	<div class="col-lg-6 col-md-5 col-sm-6 col-xs-12 padding-5 text-right">
	<?php if( ! empty($approve_logs)) : ?>
			<?php foreach($approve_logs as $logs) : ?>
				<?php if($logs->approve == 1) : ?>
					<p class="green">อนุมัติโดย : <?php echo $logs->approver; ?> @ <?php echo thai_date($logs->date_upd, TRUE); ?></p>
				<?php endif; ?>
				<?php if($logs->approve == 3) : ?>
					<p class="red">Rejected โดย : <?php echo $logs->approver; ?> @ <?php echo thai_date($logs->date_upd, TRUE); ?></p>
				<?php endif; ?>
			<?php endforeach; ?>
	<?php endif; ?>
	</div>
</div>

<div class="row">
	<div class="col-sm-12 padding-5" id="transfer-table">
  	<table class="table table-striped border-1">
    	<thead>
      	<tr>
        	<th colspan="7" class="text-center">รายการโอนย้าย</th>
        </tr>

      	<tr>
        	<th class="width-5 text-center">ลำดับ</th>
          <th class="width-15">บาร์โค้ด</th>
          <th class="width-20">สินค้า</th>
          <th class="width-20">ต้นทาง</th>
          <th class="width-20">ปลายทาง</th>
          <th class="width-10 text-right">จำนวนออก</th>
					<th class="width-10 text-right">จำนวนเข้า</th>
        </tr>
      </thead>

      <tbody id="transfer-list">
<?php if(!empty($details)) : ?>
<?php		$no = 1;						?>
<?php   $total_qty = 0; ?>
<?php 	$total_receive = 0; ?>
<?php		foreach($details as $rs) : 	?>
	<?php $color = $rs->valid == 0 ? 'color:red;' : ''; ?>

				<tr class="font-size-12" id="row-<?php echo $rs->id; ?>" style="<?php echo $color; ?>">
	      	<td class="middle text-center">
						<?php echo $no; ?>
					</td>
					<!--- บาร์โค้ดสินค้า --->
	        <td class="middle">
						<?php echo $rs->barcode; ?>
					</td>
					<!--- รหัสสินค้า -->
	        <td class="middle">
						<?php echo $rs->product_code; ?>
					</td>
					<!--- โซนต้นทาง --->
	        <td class="middle">
	      		<input type="hidden" class="row-zone-from" id="row-from-<?php echo $rs->id; ?>" value="<?php echo $rs->from_zone; ?>" />
						<?php echo $rs->from_zone_name; ?>
	        </td>
	        <td class="middle" id="row-label-<?php echo $rs->id; ?>">
						<?php 	echo $rs->to_zone_name; 	?>
	        </td>

					<td class="middle text-right" >
						<?php echo number($rs->qty); ?>
					</td>
					<td class="middle text-right" >
						<?php echo ($doc->is_wms && $doc->api ? number($rs->wms_qty) : number($rs->qty)); ?>
					</td>
	      </tr>
<?php			$no++;			?>
<?php     $total_qty += $rs->qty; ?>
<?php 		$total_receive += ($doc->is_wms && $doc->api ? $rs->wms_qty : $rs->qty); ?>
<?php		endforeach;			?>
				<tr>
					<td colspan="5" class="middle text-right"><strong>รวม</strong></td>
					<td class="middle text-right"><strong><?php echo number($total_qty); ?></strong></td>
					<td class="middle text-right"><strong><?php echo number($total_receive); ?></strong></td>
				</tr>
<?php	else : ?>
 				<tr>
        	<td colspan="7" class="text-center"><h4>ไม่พบรายการ</h4></td>
        </tr>
<?php	endif; ?>
      </tbody>
    </table>
  </div>
</div>

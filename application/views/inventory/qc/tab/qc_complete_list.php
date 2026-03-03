<div class="row" style="margin:0;">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-0 border-1" style="height: 300px; overflow:auto;">
    <table class="table table-striped tableFixHead">
      <thead>        
        <tr class="font-size-11">
          <th class="fix-width-150">บาร์โค้ด</th>
          <th class="min-width-200">สินค้า</th>
          <th class="fix-width-100 text-center">จำนวนที่สั่ง</th>
          <th class="fix-width-100 text-center">จำนวนที่จัด</th>
          <th class="fix-width-100 text-center">ตรวจแล้ว</th>
          <th class="fix-width-80 text-center">จากโซน</th>
        </tr>
      </thead>
      <tbody id="complete-table">
<?php  if(!empty($complete_details)) : ?>
<?php   foreach($complete_details as $rs) : ?>
      <tr class="" id="row-<?php echo $rs->id; ?>">
        <td class="middle"><?php echo $rs->barcode; ?></td>
        <td class="middle hide-text">
          <?php echo $rs->product_code; ?> :
          <?php echo $rs->product_name; ?>
        </td>
        <td class="middle text-center"><?php echo number($rs->order_qty); ?></td>
        <td class="middle text-center" id="prepared-<?php echo $rs->id; ?>"><?php echo number($rs->prepared); ?></td>
        <td class="middle text-center" id="qc-<?php echo $rs->id; ?>"><?php echo number($rs->qc); ?></td>
        <td class="middle text-center">
          <?php if(($rs->qc > $rs->prepared OR $rs->qc > $rs->order_qty) && $this->pm->can_delete) : ?>
            <button type="button" class="btn btn-xs btn-warning must-edit" onclick="showEditOption('<?php echo $order->code; ?>', '<?php echo $rs->product_code; ?>')">
              <i class="fa fa-pencil"></i> แก้ไข
            </button>
          <?php endif; ?>
          <button
            type="button"
            class="btn btn-minier btn-link btn-pop"
            data-container="body"
            data-toggle="popover"
            data-placement="left"
            data-trigger="focus"
            data-content="<?php echo $rs->from_zone; ?>"
            data-original-title=""
            title=""><i class="fa fa-external-link"></i> 
            ที่เก็บ
          </button>
          <input type="hidden" id="id-<?php echo $rs->id; ?>" value="<?php echo $rs->id; ?>" />
        </td>
      </tr>

<?php   endforeach; ?>
<?php endif; ?>

      </tbody>
    </table>
  </div>
</div>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped table-narrow border-1" style="min-width:700px;">
      <thead>
        <tr><th colspan="6" class="text-center">รายการรอตรวจ</th></tr>
        <tr>
          <th class="fix-width-120">บาร์โค้ด</th>
          <th class="min-width-200">สินค้า</th>
          <th class="fix-width-80 text-center">จำนวนที่สั่ง</th>
          <th class="fix-width-80 text-center">จำนวนที่จัด</th>
          <th class="fix-width-80 text-center">ตรวจแล้ว</th>
          <th class="fix-width-100 text-right">จากโซน</th>
        </tr>
      </thead>
      <tbody id="incomplete-table">
<?php   $show_close = !empty($uncomplete_details) ? 'hide' : ''; ?>
<?php   $show_force = !empty($uncomplete_details) ? '' : 'hide'; ?>
<?php  if(!empty($uncomplete_details)) : ?>
<?php   foreach($uncomplete_details as $rs) : ?>
<?php   $id = md5($rs->barcode); ?>
      <tr class="incomplete" id="row-<?php echo $id; ?>">
        <td class="middle td bc"><?php echo $rs->barcode; ?></td>
        <td class="middle td">
          <?php echo $rs->product_code; ?> :
          <?php if(empty($rs->old_code) OR $rs->old_code == $rs->product_code) : ?>
          <?php     echo $rs->product_name; ?>
          <?php else : ?>
          <?php     echo $rs->old_code; ?>
          <?php endif; ?>
        </td>
        <td class="middle text-center td"><?php echo number($rs->order_qty); ?></td>
        <td class="middle text-center td" id="prepared-<?php echo $id; ?>"> <?php echo number($rs->prepared); ?></td>
        <td class="middle text-center td" id="qc-<?php echo $id; ?>"><?php echo number($rs->qc); ?></td>
        <td class="middle text-right td">
          <button
            type="button"
            class="btn btn-default btn-minier btn-pop"
            data-container="body"
            data-toggle="popover"
            data-placement="left"
            data-trigger="focus"
            data-content="<?php echo $rs->from_zone; ?>"
            data-original-title=""
            title="">
            ที่เก็บ
          </button>
          <input type="hidden" class="hidden-qc" id="<?php echo $id; ?>" data-code="<?php echo $rs->product_code; ?>" value="0"/>
          <input type="hidden" id="id-<?php echo $id; ?>" value="<?php echo $id; ?>" />
        </td>
      </tr>

<?php   endforeach; ?>

<?php else : ?>
      <tr><td colspan="6" class="text-center"><h4>ไม่พบรายการ</td></tr>
<?php endif; ?>
        <tr>
          <td colspan="6" class="text-center">
            <?php if($order->state == 6) : ?>
            <div id="force-bar" class="<?php echo $show_force; ?>">
              <button type="button" class="btn btn-xs btn-danger not-show close-order" id="btn-force-close" onclick="forceClose()">
                บังคับจบ
              </button>
              <label style="margin-left:25px;">
                <input type="checkbox" class="close-order ace" style="margin-right:10px;" id="chk-force-close"  />
                <span class="lbl">  สินค้าไม่ครบ</span>
              </label>
            </div>
            <div class="<?php echo $show_close; ?>" id="close-bar">
              <button type="button" class="btn btn-xs btn-success close-order" id="btn-close" onclick="closeOrder()">
                ตรวจเสร็จแล้ว (F2)
              </button>
            </div>
            <?php endif; ?>
            <?php if($order->state == 6 OR $order->state == 7) : ?>
            <div class="<?php echo $order->state == 7 ? '' : 'hide'; ?>" id="bill-bar">
              <button type="button" class="btn btn-xs btn-primary close-order" id="btn-bill" onclick="confirmOrder('<?php echo $order->code; ?>')">
                เปิดบิลตัดสต็อก (F3)
              </button>
            </div>
            <?php endif; ?>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

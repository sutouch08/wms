<div class="incomplete-box" id="incomplete-box">
  <?php  if(!empty($uncomplete)) : ?>
    <?php $no = 1; ?>
    <?php   foreach($uncomplete as $rs) : ?>
      <div class="col-xs-12 receive-item unvalid" id="receive-item-<?php echo $rs->id; ?>">
        <div class="width-100" style="padding: 3px 3px 3px 10px;">
          <div class="margin-bottom-3 pre-wrap b-click hide"><?php echo $rs->barcode; ?></div>
          <div class="margin-bottom-3 pre-wrap"><?php echo $rs->product_code; ?></div>
          <div class="margin-bottom-3 pre-wrap hide-text"><?php echo $rs->product_name; ?></div>
          <div class="margin-bottom-3 pre-wrap">
            <table class="table receive-table">
              <tr>
                <td class="width-33 text-center">จำนวน</td>
                <td class="width-33 text-center">รับแล้ว</td>
                <td class="width-33 text-center">คงเหลือ</td>
              </tr>
              <tr>
                <td style="width:33%;">
                  <input type="text" class="width-100 text-label text-center" value="<?php echo number($rs->qty); ?>" readonly />
                </td>
                <td style="width:33%;">
                  <input type="text"
        						class="form-control input-sm text-center text-label receive-qty"
        						id="receive-qty-<?php echo $rs->id; ?>"
        						data-id="<?php echo $rs->id; ?>"
        						data-limit="<?php echo $rs->qty; ?>"
        						data-price="<?php echo $rs->price; ?>"
        						data-baseentry="<?php echo $rs->baseEntry; ?>"
        						data-baseline="<?php echo $rs->baseLine; ?>"
        						data-code="<?php echo $rs->product_code; ?>"
        						data-name="<?php echo $rs->product_name; ?>"
        						data-vatcode="<?php echo $rs->vatGroup; ?>"
        						data-vatrate="<?php echo $rs->vatRate; ?>"
        						data-currency="<?php echo $rs->currency; ?>"
        						data-rate="<?php echo $rs->rate; ?>"
        						value="<?php echo number($rs->receive_qty); ?>" readonly/>
                </td>
                <td style="width:33%;">
                  <input type="text" class="width-100 text-label text-center" id="balance-<?php echo $rs->id; ?>" value="<?php echo number($rs->qty - $rs->receive_qty); ?>" readonly />
                </td>
              </tr>
            </table>
          </div>
        </div>
        <input type="hidden" class="buffer <?php echo $rs->barcode; ?>"
        id="buffer-<?php echo $rs->id; ?>"
        data-code="<?php echo $rs->product_code; ?>"
        data-limit="<?php echo $rs->qty; ?>"
        data-id="<?php echo $rs->id; ?>"
        value="0"	/>

        <button class="btn btn-minier btn-white btn-warning"
        style="position:absolute; top:10px; right:10px; border-radius:4px !important;"
        onclick="decreaseReceived(<?php echo $rs->id; ?>)">
        <i class="fa fa-minus"></i>
      </button>
      </div>
      <?php $no++; ?>
    <?php endforeach; ?>

    <div id="close-bar" class="width-100 text-center <?php echo $finished ? '' : 'hide'; ?>">
      <button type="button" class="btn btn-lg btn-success btn-block" onclick="finishReceive()"><i class="fa fa-check"></i> รับเสร็จแล้ว</button>
    </div>

  <?php else : ?>
    <div class="width-100 text-center" id="close-bar">
      <button type="button" class="btn btn-lg btn-success btn-block" onclick="finishReceive()"><i class="fa fa-check"></i> รับเสร็จแล้ว</button>
    </div>
  <?php endif; ?>
</div>

<div id="orders-tab" class="tab-pane fade" >
  <div class="row" style="margin:0px;">

    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-0" style="height:400px; overflow:auto;">
      <table class="table table-striped tableFixHead">
        <thead>
          <tr>
            <th class="fix-width-40 text-center fix-header">#</th>
          <?php if($doc->status == 'P') : ?>
            <th class="fix-width-50 text-center fix-header">
              <label>
                <input type="checkbox" class="ace chk-od-all" onchange="chkOrderTabAll($(this))" />
                <span class="lbl"></span>
              </label>
            </th>
        <?php endif; ?>
            <th class="fix-width-200 fix-header">เลขที่</th>
            <th class="min-width-100 fix-header">
              <?php if($doc->status == 'P') : ?>
                <button type="button" class="btn btn-sm btn-danger btn-100 pull-right" onclick="deleteOrders()">ลบออเดอร์</button>
              <?php endif; ?>
            </th>
          </tr>
        </thead>
        <tbody>
          <?php if( ! empty($orders)) : ?>
            <?php $no = 1; ?>
            <?php foreach($orders as $rs) : ?>
              <tr>
                <td class="text-center"><?php echo $no; ?></td>
              <?php if($doc->status == 'P') : ?>
                <td class="text-center">
                  <label>
                    <input type="checkbox" class="ace chk-od" value="<?php echo $rs->order_code; ?>"/>
                    <span class="lbl"></span>
                  </label>
                </td>
              <?php endif; ?>
                <td class=""><?php echo $rs->order_code; ?></td>
                <td class=""></td>
              </tr>
              <?php $no++; ?>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

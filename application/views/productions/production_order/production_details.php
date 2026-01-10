<div class="tab-pane fade active in" id="components">
  <div class="row" style="margin-left:0; margin-right:0;">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive" style="min-height:300px; max-height:600px; overflow:scroll; padding:0px; border:solid 1px #dddddd;">
      <table class="table table-bordered tableFixHead" style="min-width:1255px; margin-bottom:20px;">
        <thead>
          <tr class="font-size-11">
            <th class="fix-width-25 middle text-center fix-no fix-header">#</th>
            <th class="fix-width-100 middle">Type</th>
            <th class="fix-width-200 middle">Item Code</th>
            <th class="min-width-250 middle">Item Description.</th>
            <th class="fix-width-80 middle">Base Qty.</th>
            <th class="fix-width-80 middle">Base Ratio</th>
            <th class="fix-width-80 middle">Planned Qty</th>
            <th class="fix-width-80 middle">Issued</th>
            <th class="fix-width-80 middle">Available</th>
            <th class="fix-width-80 middle">Uom</th>
            <th class="fix-width-100 middle">Warehouse</th>
            <th class="fix-width-100 middle">Issue Method</th>
          </tr>
        </thead>
        <tbody id="details-table">
          <?php $no = 1; ?>
          <?php if( ! empty($details)) : ?>
            <?php foreach($details as $rs) : ?>
              <?php $uid = $rs->uid; ?>
              <?php $issued = $this->production_order_model->get_issue_qty_by_item($rs->ItemCode, $doc->DocEntry, $rs->LineNum); ?>
              <?php $available = $this->stock_model->get_item_stock($rs->ItemCode, $rs->WhsCode); ?>
              <tr id="row-<?php echo $uid; ?>" data-uid="<?php echo $uid; ?>" class="font-size-11">
                <td class="middle text-center fix-no no" scope="row"><?php echo $no; ?></td>
                <td class="middle">
                  <select class="form-control input-xs text-label" id="type-<?php echo $uid; ?>" disabled>
                    <option value="4" <?php echo is_selected('4', strval($rs->ItemType)); ?>>Item</option>
                    <option value="290" <?php echo is_selected('290', strval($rs->ItemType)); ?>>Resource</option>
                    <option value="-18" <?php echo is_selected('-18', strval($rs->ItemType)); ?>>Text</option>
                  </select>
                </td>
                <td class="middle">
                  <input type="text" class="form-control input-xs text-label item-code r" data-uid="<?php echo $uid; ?>" id="item-code-<?php echo $uid; ?>" value="<?php echo $rs->ItemCode; ?>" readonly/>
                </td>
                <td class="middle">
                  <input type="text" class="form-control input-xs text-label item-name" data-uid="<?php echo $uid; ?>" id="item-name-<?php echo $uid; ?>" value="<?php echo $rs->ItemName; ?>" readonly/>
                </td>
                <td class="middle">
                  <input type="text" class="form-control input-xs text-label text-right base-qty r" data-uid="<?php echo $uid; ?>" id="base-qty-<?php echo $uid; ?>" value="<?php echo round($rs->BaseQty, 2); ?>" onchange="recalQty('<?php echo $uid; ?>')" readonly/>
                </td>
                <td class="middle">
                  <input type="text" class="form-control input-xs text-label text-right" data-uid="<?php echo $uid; ?>" id="base-ratio-<?php echo $uid; ?>" value="<?php echo get_ratio($rs->BaseQty); ?>"  disabled/>
                </td>
                <td class="middle">
                  <input type="text" class="form-control input-xs text-label text-right planned-qty r" data-uid="<?php echo $uid; ?>" id="planned-qty-<?php echo $uid; ?>" value="<?php echo round($rs->PlannedQty, 2); ?>"  readonly/>
                </td>
                <td class="middle">
                  <input type="text" class="form-control input-xs text-label text-right" data-uid="<?php echo $uid; ?>" id="issued-<?php echo $uid; ?>" value="<?php echo number($issued, 2); ?>" disabled />
                </td>
                <td class="middle">
                  <input type="text" class="form-control input-xs text-label text-right" data-uid="<?php echo $uid; ?>" id="available-<?php echo $uid; ?>" value="<?php echo number($available, 2); ?>" disabled />
                </td>
                <td class="middle">
                  <input type="text" class="form-control input-xs text-label"
                    data-uid="<?php echo $uid; ?>"
                    id="uom-<?php echo $uid; ?>"
                    data-uomentry="<?php echo $rs->UomEntry; ?>"
                    data-uomcode="<?php echo $rs->UomCode; ?>"
                    value="<?php echo $rs->Uom; ?>"  disabled/>
                </td>
                <td class="middle">
                  <div class="width-100 wh">
                    <input type="text" class="form-control input-xs text-label wh-input r" data-uid="<?php echo $uid; ?>" id="warehouse-<?php echo $uid; ?>" value="<?php echo $rs->WhsCode; ?>" readonly/>
                  </div>
                </td>
                <td class="middle">
                  <select class="form-control input-xs text-label" data-uid="<?php echo $uid; ?>" id="issue-type-<?php echo $uid; ?>" disabled>
                    <option value="M" <?php echo is_selected('M', $rs->IssueType); ?>>Manual</option>
                    <option value="B" <?php echo is_selected('B', $rs->IssueType); ?>>Backflush</option>
                  </select>
                </td>
              </tr>
              <?php $no++; ?>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

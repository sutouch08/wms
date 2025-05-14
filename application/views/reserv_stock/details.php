<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-stripped border-1">
      <thead>
        <tr class="font-size-11">
          <th class="fix-width-60 text-center">
            <label>
              <input type="checkbox" class="ace" id="chk-all" />
              <span class="lbl"></span>
            </label>
          </th>
          <th class="fix-width-40 text-center">#</th>
          <th class="fix-width-200">SKU</th>
          <th class="fix-width-100 text-center">Qty</th>
          <th class="min-width-200">Description</th>
        </tr>
      </thead>
      <tbody id="result-table">
  <?php if( ! empty($details)) : ?>
    <?php $no = 1; ?>
    <?php foreach($details as $rs) : ?>
      <tr class="font-size-11" id="row-<?php echo $rs->id; ?>">
        <td class="middle text-center">
          <label>
            <input type="checkbox" class="ace chk" value="<?php echo $rs->id; ?>" />
            <span class="lbl"></span>
          </label>
        </td>
        <td class="middle text-center no"><?php echo $no; ?></td>
        <td class="middle"><?php echo $rs->product_code; ?></td>
        <td class="middle text-center"><?php echo number($rs->qty, 2); ?></td>
        <td class="middle"><?php echo $rs->product_name; ?></td>
      </tr>
      <?php $no++; ?>
    <?php endforeach; ?>
  <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>


<script id="item-template" type="text/x-handlebarsTemplate">
  <tr class="font-size-11" id="row-{{id}}">
    <td class="middle text-center">
      <label>
        <input type="checkbox" class="ace chk" value="{{id}}" />
        <span class="lbl"></span>
      </label>
    </td>
    <td class="middle text-center no"></td>
    <td class="middle">{{product_code}}</td>
    <td class="middle text-center">{{qty}}</td>
    <td class="middle">{{product_name}}</td>
  </tr>
</script>

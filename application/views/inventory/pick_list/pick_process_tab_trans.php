<div id="transection-tab" class="tab-pane fade" style="height:350px; overflow:auto;">
  <div class="">

  </div>
  <table class="table table-striped tableFixHead" style="min-width:990px;">
    <thead>
      <tr>
        <th class="fix-width-40 text-center fix-header">
          <button type="button" class="btn btn-minier btn-info" onclick="reloadTransection()"><i class="fa fa-refresh"></i></button>
        </th>
        <th class="fix-width-40 text-center fix-header">#</th>
        <th class="fix-width-150 fix-header">รหัส</th>
        <th class="min-width-250 fix-header">สินค้า</th>
        <th class="fix-width-100 text-center fix-header">จำนวน</th>
        <th class="fix-width-150 fix-header">โซน</th>
        <th class="fix-width-150 fix-header">User</th>
        <th class="fix-width-150 fix-header">เวลา</th>
      </tr>
    </thead>
    <tbody id="transection-table">
  <?php if( ! empty($transection)) : ?>
    <?php $no = 1; ?>
    <?php foreach($transection as $rs) : ?>
      <tr class="font-size-11" id="trans-<?php echo $rs->id; ?>">
        <td class="text-center">
          <button type="button" class="btn btn-minier btn-danger"
            onclick="removeTransection(<?php echo $rs->id; ?>, <?php echo $rs->qty; ?>, '<?php echo $rs->product_code; ?>', '<?php echo $rs->zone_code; ?>')">
            <i class="fa fa-trash"></i>
          </button>
        </td>
        <td class="text-center t-no"><?php echo $no; ?></td>
        <td><?php echo $rs->product_code; ?></td>
        <td class="hide-text"><?php echo $rs->product_name; ?></td>
        <td class="text-center"><?php echo number($rs->qty); ?></td>
        <td><?php echo $rs->zone_code; ?></td>
        <td><?php echo $rs->user; ?></td>
        <td><?php echo thai_date($rs->date_upd, TRUE); ?></td>
      </tr>
      <?php $no++; ?>
    <?php endforeach; ?>
  <?php endif; ?>
    </tbody>
  </table>
</div>


<script id="transections-template" type="text/x-handlebarsTemplate">
  {{#each this}}
    {{#if nodata}}
      <tr class="font-size-11">
        <td colspan="8" class="text-center">--- no transections ---</td>
      </tr>
    {{else}}
      <tr class="font-size-11" id="trans-{{id}}">
        <td class="text-center">
          {{#if valid}}
          <button type="button" class="btn btn-minier btn-danger" onclick="removeTransection({{id}}, {{qty}}, '{{product_code}}', '{{zone_code}}')">
            <i class="fa fa-trash"></i>
          </button>
          {{/if}}
        </td>
        <td class="text-center t-no">{{no}}</td>
        <td>{{product_code}}</td>
        <td class="hide-text">{{product_name}}</td>
        <td class="text-center">{{qty}}</td>
        <td>{{zone_code}}</td>
        <td>{{user}}</td>
        <td>{{date_upd}}</td>
      </tr>
    {{/if}}
  {{/each}}
</script>

<div class="padding-5 incomplete-box" id="incomplete-box">
  <?php  if(!empty($incomplete)) : ?>
    <?php $no = 1; ?>
    <?php   foreach($incomplete as $rs) : ?>
      <div class="col-xs-12 incomplete-item" id="incomplete-<?php echo $rs->id; ?>">
        <div class="width-100" style="padding: 3px 3px 3px 10px;">
          <div class="margin-bottom-3 pre-wrap b-click " id="b-click-<?php echo $rs->id; ?>">
            <?php if( ! empty($rs->barcode)) : ?>
              <input type="hidden" id="<?php echo $rs->barcode; ?>"
                data-id="<?php echo $rs->id; ?>"
                data-code="<?php echo $rs->product_code; ?>"
                data-name="<?php echo $rs->product_name; ?>"
                data-release="<?php echo $rs->release_qty; ?>" />
            <?php endif; ?>
            <?php echo $rs->barcode; ?>
          </div>
          <div class="margin-bottom-3 pre-wrap"><?php echo $rs->product_code; ?></div>
          <div class="margin-bottom-3 pre-wrap hide-text"><?php echo $rs->product_name; ?></div>
          <div class="margin-bottom-3 pre-wrap">
            <div class="width-33 float-left">จำนวน : <span id="release-qty-<?php echo $rs->id; ?>"><?php echo number($rs->release_qty); ?></span></div>
            <div class="width-33 float-left">จัดแล้ว : <span class="picked-qty" id="pick-qty-<?php echo $rs->id; ?>"><?php echo number($rs->pick_qty); ?></span></div>
            <div class="width-33 float-left">คงเหลือ : <span id="balance-qty-<?php echo $rs->id; ?>"><?php echo number($rs->balance); ?></span></div>
          </div>
          <div class="divider margin-top-10 margin-bottom-10"></div>
          <button type="button" class="btn btn-minier btn-info stock-reload" onclick="reloadPickRow(<?php echo $rs->id; ?>)"><i class="fa fa-refresh"></i></button>
          <div class="margin-bottom-3 pre-wrap" id="stock-in-zone-<?php echo $rs->id; ?>">Location : <?php echo $rs->stock_in_zone; ?></div>
        </div>
        <span class="badge-qty" id="badge-qty-<?php echo $rs->id; ?>"><?php echo number($rs->balance); ?></span>
      </div>
      <?php $no++; ?>
    <?php endforeach; ?>

    <div id="close-bar" class="text-center <?php echo $finished ? '' : 'hide'; ?>">
      <button type="button" class="btn btn-lg btn-success" onclick="finishPick()">จัดเสร็จแล้ว</button>
    </div>

  <?php else : ?>
    <div class="text-center" id="close-bar">
      <button type="button" class="btn btn-lg btn-success" onclick="finishPick()">จัดเสร็จแล้ว</button>
    </div>
  <?php endif; ?>
</div>

<script id="incomplete-template" type="text/x-handlebarsTemplate">
  {{#each this}}
    <div class="col-xs-12 incomplete-item" id="incomplete-{{id}}">
      <div class="width-100" style="padding: 3px 3px 3px 10px;">
        <div class="margin-bottom-3 pre-wrap b-click " id="b-click-{{id}}">
          {{barcode}}
          {{#if barcode}}
            <input type="hidden" id="{{barcode}}"
              data-id="{{id}}"
              data-code="{{product_code}}"
              data-name="{{product_name}}"
              data-release="{{release_qty}}" />
          {{/if}}
        </div>
        <div class="margin-bottom-3 pre-wrap">{{product_code}}</div>
        <div class="margin-bottom-3 pre-wrap hide-text">{{product_name}}</div>
        <div class="margin-bottom-3 pre-wrap">
          <div class="width-33 float-left">จำนวน : <span id="release-qty-{{id}}">{{release_qty}}</span></div>
          <div class="width-33 float-left">จัดแล้ว : <span class="picked-qty" id="pick-qty-{{id}}">{{pick_qty}}</span></div>
          <div class="width-33 float-left">คงเหลือ : <span id="balance-qty-{{id}}">{{balance}}</span></div>
        </div>
        <div class="divider margin-top-10 margin-bottom-10"></div>
        <button type="button" class="btn btn-minier btn-info stock-reload" onclick="reloadPickRow({{id}})"><i class="fa fa-refresh"></i></button>
        <div class="margin-bottom-3 pre-wrap" id="stock-in-zone-{{id}}">Location : {{{stock_in_zone}}}</div>
      </div>
      <span class="badge-qty" id="badge-qty-{{id}}">{{balance}}</span>
    </div>
  {{/each}}
</script>

<script id="incomplete-row-template" type="text/x-handlebarsTemplate">
  <div class="width-100" style="padding: 3px 3px 3px 10px;">
    <div class="margin-bottom-3 pre-wrap b-click " id="b-click-{{id}}">
      {{barcode}}
      {{#if barcode}}
        <input type="hidden" id="{{barcode}}"
          data-id="{{id}}"
          data-code="{{product_code}}"
          data-name="{{product_name}}"
          data-release="{{release_qty}}" />
      {{/if}}
    </div>
    <div class="margin-bottom-3 pre-wrap">{{product_code}}</div>
    <div class="margin-bottom-3 pre-wrap hide-text">{{product_name}}</div>
    <div class="margin-bottom-3 pre-wrap">
      <div class="width-33 float-left">จำนวน : <span id="release-qty-{{id}}">{{release_qty}}</span></div>
      <div class="width-33 float-left">จัดแล้ว : <span class="picked-qty" id="pick-qty-{{id}}">{{pick_qty}}</span></div>
      <div class="width-33 float-left">คงเหลือ : <span id="balance-qty-{{id}}">{{balance}}</span></div>
    </div>
    <div class="divider margin-top-10 margin-bottom-10"></div>
    <button type="button" class="btn btn-minier btn-info stock-reload" onclick="reloadPickRow({{id}})"><i class="fa fa-refresh"></i></button>
    <div class="margin-bottom-3 pre-wrap" id="stock-in-zone-{{id}}">Location : {{{stock_in_zone}}}</div>
  </div>
  <span class="badge-qty" id="badge-qty-{{id}}">{{balance}}</span>
</script>

<div class="padding-5 complete-box move-out" id="complete-box">
  <div class="nav-title">
    <a class="pull-left margin-left-10" onclick="closeCompleteBox()"><i class="fa fa-angle-left fa-2x"></i></a>
    <div class="font-size-18 text-center">รายการที่ครบแล้ว</div>
  </div>
  <?php  if( ! empty($complete)) : ?>
    <?php $no = 1; ?>
    <?php   foreach($complete as $rs) : ?>
      <div class="col-xs-12 complete-item" id="complete-<?php echo $rs->id; ?>" data-id="<?php echo $rs->id; ?>">
        <div class="width-100" style="padding: 3px 3px 3px 10px;">
          <div class="margin-bottom-3 pre-wrap"><?php echo $rs->product_code; ?></div>
          <div class="margin-bottom-3 pre-wrap hide-text"><?php echo $rs->product_name; ?></div>
          <div class="margin-bottom-3 pre-wrap">
            <div class="width-33 float-left">จำนวน : <span id="release-qty-<?php echo $rs->id; ?>"><?php echo number($rs->release_qty); ?></span></div>
            <div class="width-33 float-left">จัดแล้ว : <span class="picked-qty" id="pick-qty-<?php echo $rs->id; ?>"><?php echo number($rs->pick_qty); ?></span></div>
            <div class="width-33 float-left">คงเหลือ : <span id="balance-qty-<?php echo $rs->id; ?>">0</span></div>
          </div>
        </div>
      </div>
    <?php $no++; ?>
  <?php endforeach; ?>
<?php endif; ?>
</div>


<script id="complete-template" type="text/x-handlebarsTemplate">
  <div class="nav-title">
    <a class="pull-left margin-left-10" onclick="closeCompleteBox()"><i class="fa fa-angle-left fa-2x"></i></a>
    <div class="font-size-18 text-center">รายการที่ครบแล้ว</div>
  </div>
  {{#each this}}
    <div class="col-xs-12 complete-item" id="complete-{{id}}">
      <div class="width-100" style="padding: 3px 3px 3px 10px;">
        <div class="margin-bottom-3 pre-wrap">{{product_code}}</div>
        <div class="margin-bottom-3 pre-wrap hide-text">{{product_name}}</div>
        <div class="margin-bottom-3 pre-wrap">
          <div class="width-33 float-left">จำนวน : <span id="release-qty-{{id}}">{{releaseQty}}</span></div>
          <div class="width-33 float-left">จัดแล้ว : <span class="picked-qty" id="pick-qty-{{id}}">{{pickQtty}}</span></div>
          <div class="width-33 float-left">คงเหลือ : <span id="balance-qty-{{id}}">0</span></div>
        </div>
      </div>
    </div>
  {{/each}}
</script>


<script id="complete-row-template" type="text/x-handlebarsTemplate">
  <div class="col-xs-12 complete-item" id="complete-{{id}}">
    <div class="width-100" style="padding: 3px 3px 3px 10px;">
      <div class="margin-bottom-3 pre-wrap">{{product_code}}</div>
      <div class="margin-bottom-3 pre-wrap hide-text">{{product_name}}</div>
      <div class="margin-bottom-3 pre-wrap">
        <div class="width-33 float-left">จำนวน : <span id="release-qty-{{id}}">{{releaseQty}}</span></div>
        <div class="width-33 float-left">จัดแล้ว : <span class="picked-qty" id="pick-qty-{{id}}">{{pickQtty}}</span></div>
        <div class="width-33 float-left">คงเหลือ : <span id="balance-qty-{{id}}">0</span></div>
      </div>
    </div>
  </div>
</script>

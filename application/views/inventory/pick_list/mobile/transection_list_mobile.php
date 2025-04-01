<div class="padding-5 trans-box move-out" id="trans-box">
  <div class="nav-title">
    <a class="pull-left margin-left-10" onclick="closeTransBox()"><i class="fa fa-angle-left fa-2x"></i></a>
    <div class="font-size-18 text-center">Transection</div>
  </div>
  <?php  if( ! empty($transection)) : ?>
    <?php $no = 1; ?>
    <?php   foreach($transection as $rs) : ?>
      <div class="col-xs-12 trans-item" id="trans-<?php echo $rs->id; ?>" data-id="<?php echo $rs->id; ?>">
        <div class="width-100" style="padding: 3px 3px 3px 10px;">
          <div class="margin-bottom-3 pre-wrap"><?php echo $rs->product_code; ?></div>
          <div class="margin-bottom-3 pre-wrap hide-text"><?php echo $rs->product_name; ?></div>
          <div class="margin-bottom-3 pre-wrap" style="display:flex;">
            <div class="width-30">จำนวน : <?php echo number($rs->qty); ?></div>
            <div class="width-70">โซน : <?php echo $rs->zone_code; ?></div>
          </div>
          <div class="margin-bottom-3" style="display:flex;">
            <div class="width-50 hide-text">User : <?php echo $rs->user; ?></div>
            <div class="width-50 hide-text">วันที่ : <?php echo thai_date($rs->date_upd, TRUE); ?></div>
          </div>
        </div>
        <button type="button" class="btn btn-minier btn-danger btn-trans-del"
          onclick="removeTransection(<?php echo $rs->id; ?>, <?php echo $rs->qty; ?>, '<?php echo $rs->product_code; ?>', '<?php echo $rs->zone_code; ?>')">
          <i class="fa fa-trash"></i>
        </button>
      </div>
    <?php $no++; ?>
  <?php endforeach; ?>
<?php endif; ?>
</div>

<script id="trans-template" type="text/x-handlebarsTemplate">
  <div class="nav-title">
    <a class="pull-left margin-left-10" onclick="closeTransBox()"><i class="fa fa-angle-left fa-2x"></i></a>
    <div class="font-size-18 text-center">Transection</div>
  </div>
  {{#each this}}
    <div class="col-xs-12 trans-item" id="trans-{{id}}" data-id="{{id}}">
      <div class="width-100" style="padding: 3px 3px 3px 10px;">
        <div class="margin-bottom-3 pre-wrap">{{product_code}}</div>
        <div class="margin-bottom-3 pre-wrap hide-text">{{product_name}}</div>
        <div class="margin-bottom-3 pre-wrap" style="display:flex;">
          <div class="width-30">จำนวน : {{qty}}</div>
          <div class="width-70">โซน : {{zone_code}}</div>
        </div>
        <div class="margin-bottom-3" style="display:flex;">
          <div class="width-50 hide-text">User : {{user}}</div>
          <div class="width-50 hide-text">วันที่ : {{date_upd}}</div>
        </div>
      </div>
      <button type="button" class="btn btn-minier btn-danger btn-trans-del"
        onclick="removeTransection({{id}}, {{qty}}, '{{product_code}}', '{{zone_code}}')">
        <i class="fa fa-trash"></i>
      </button>
    </div>
  {{/each}}
</script>

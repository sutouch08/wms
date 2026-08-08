<!-- แสดงผลกล่อง  -->
<div id="qc-box" class="row">
  <div id="box-row">
    <?php if (!empty($box_list)) : ?>
      <?php foreach ($box_list as $rs) : ?>
        <div class="box-control">
          <label class="box-label">
            <?php if ($order->state == 6) : ?>
              <input type="radio" class="ace box-radio" name="box"
                id="box-<?php echo $rs->id; ?>" value="<?php echo $rs->id; ?>"
                onchange="confirmSaveBeforeChangeBox(<?php echo $rs->id; ?>)" <?php echo $rs->id == $active_box_id ? 'checked' : ''; ?> />
              <span class="lbl font-size-14">&nbsp;กล่องที่ <?php echo $rs->box_no; ?> | </span>
              <span class="font-size-11" style=""><?php echo $rs->code; ?></span>
            <?php else : ?>
              <span class="lbl font-size-14">&nbsp;กล่องที่ <?php echo $rs->box_no; ?> | </span>
              <span class="font-size-11" style=""><?php echo $rs->code; ?></span>
            <?php endif; ?>
            <select class="box-package form-control intput-xs focus" id="package-<?php echo $rs->id; ?>" onchange="updatePackageId(<?php echo $rs->id; ?>)">
              <?php echo select_active_package($rs->package_id); ?>
            </select>
          </label>

          <span class="box-count pull-left text-center">
            <span class="display-block font-size-11 padding-top-5">QTY</span>
            <span class="display-block font-size-16 padding-top-5" id="<?php echo $rs->id; ?>"><?php echo number($rs->qty); ?></span>
          </span>

          <?php if ($this->weight_on_pack) : ?>
            <span class="box-weight pull-left text-center">
              <span class="display-block font-size-11 padding-top-5">Weight (kgs)</span>
              <span class="display-block font-size-16 padding-top-5" id="weight-<?php echo $rs->id; ?>"><?php echo number($rs->weight, 2); ?></span>
            </span>
          <?php endif; ?>

          <?php if ($order->state == 6) : ?>
            <div class="btn-group">
              <button data-toggle="dropdown" class="btn btn-link btn-info dropdown-toggle box-menu" style="padding: 0px !important;" aria-expanded="false">
                <i class="ace-icon fa fa-angle-down icon-on-right"></i>
              </button>
              <ul class="dropdown-menu dropdown-menu-right">
                <li class="primary">
                  <a href="javascript:printBox(<?php echo $rs->id; ?>)"><i class="fa fa-print"></i> &nbsp; Packing list</a>
                </li>
                <li class="primary">
                  <a href="javascript:printBoxEng(<?php echo $rs->id; ?>)"><i class="fa fa-print"></i> &nbsp; Packing list (Eng)</a>
                </li>
                <li class="warning">
                  <a href="javascript:editBox(<?php echo $rs->id; ?>, '<?php echo $rs->code; ?> : กล่องที่ <?php echo $rs->box_no; ?>')"><i class="fa fa-pencil"></i> &nbsp; Edit</a>
                </li>
                <li class="danger">
                  <a href="javascript:removeBox(<?php echo $rs->id; ?>, '<?php echo $rs->code; ?> : กล่องที่ <?php echo $rs->box_no; ?>')"><i class="fa fa-times"></i> &nbsp; Delete</a>
                </li>
              </ul>
            </div>
          <?php else : ?>
            <?php if ($rs->qty > 0) : ?>
              <button class="btn btn-link btn-info dropdown-toggle box-menu" style="padding: 0px !important;" onclick="printBox(<?php echo $rs->id; ?>)">
                <i class="ace-icon fa fa-print icon-on-right"></i>
              </button>
            <?php else : ?>
              <span class="">&nbsp;</span>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <?php if ($this->weight_on_pack) : ?>
    <!-- แสดงผลน้ำหนักล่าสุด -->
    <div id="weight-box" class="hide">
      <div id="weight-content">
        <span id="latest-weight" class="text-center" style="display: inline-block; width: 130px;">0.00</span>
        <span class="text-center" style="display:inline-block; width:50px;">kg.</span>
        <span class="text-center" style="display:inline-block; width:50px;">
          <button class="btn btn-default btn-white btn-xs weight-btn" onclick="getWeight()">
            <i class="ace-icon fa fa-check bigger-160"></i>
          </button>
        </span>
        <input type="text" class="form-control input-sm text-center text-label"
          style="color:white !important; border:solid 1px #ddd !important; margin-top:5px;" id="device-label" placeholder="เครื่องชั่ง" readonly />
      </div>
    </div>
  <?php endif; ?>
</div>

<hr />

<script id="box-template" type="text/x-handlebars-template">
  {{#each this}}
    <div class="box-control">
      <label class="box-label">
        <input type="radio" class="ace box-radio" name="box"
          id="box-{{id_box}}" value="{{id_box}}"
          onchange="selectBox('{{id_box}}')" {{checked}} />
        <span class="lbl font-size-14">&nbsp;กล่องที่ {{no}} | </span>
        <span class="font-size-11">{{code}}</span>
        <select class="box-package form-control intput-xs" id="package-{{id_box}}" onchange="updatePackageId('{{id_box}}')">
          {{{package}}}
        </select>
      </label>

      <span class="box-count pull-left text-center">
        <span class="display-block font-size-11 padding-top-5">QTY</span>
        <span class="display-block font-size-16 padding-top-5" id="{{id_box}}">{{qty}}</span>
      </span>

      {{#if show_weight}}
        <span class="box-weight pull-left text-center">
          <span class="display-block font-size-11 padding-top-5">Weight (kgs)</span>
          <span class="display-block font-size-16 padding-top-5" id="weight-{{id_box}}">{{weight}}</span>
        </span>
      {{/if}}

      <div class="btn-group">
        <button data-toggle="dropdown" class="btn btn-link btn-info dropdown-toggle box-menu" style="padding: 0px !important;" aria-expanded="false">
          <i class="ace-icon fa fa-angle-down icon-on-right"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
          <li class="primary">
            <a href="javascript:printBox({{id_box}})"><i class="fa fa-print"></i> &nbsp; Packing list</a>
          </li>
          <li class="warning">
            <a href="javascript:editBox({{id_box}}, '{{code}} : กล่องที่ {{no}}')"><i class="fa fa-pencil"></i> &nbsp; Edit</a>
          </li>
          <li class="danger">
            <a href="javascript:removeBox({{id_box}}, '{{code}} : กล่องที่ {{no}}')"><i class="fa fa-times"></i> &nbsp; Delete</a>
          </li>
        </ul>
      </div>
    </div>
  {{/each}}
</script>

<!-- แสดงผลกล่อง  -->
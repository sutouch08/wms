<style>
  .box-control {
    position: relative;
    float: left;
    height: 60px;
    padding: 5px;
    background-color: #eee;
    border: solid 1px #ddd;
    border-radius:5px;
    margin-left:5px;
  }

  .box-label {
    float: left;
    margin-bottom: 0;
    padding-right: 10px;
  }

  .box-package {
    margin-top: 2px;
    display: block;
    font-size:11px;
    height: auto;
    background-color: #eee;
    border: 0px;
  }

  .box-count {
    font-size:16px;
    width:60px;
    height: 60px;
    margin-top:-5px;
    border-left:solid 1px #e0e0e0;
    border-right:solid 1px #e0e0e0;
    padding-left:10px;
    padding-right:10px;
  }

  .box-menu {
    width: 25px;
    height: 45px;
    margin: 0;
  }

</style>
<!-- แสดงผลกล่อง  -->
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5" id="box-row">
    <?php if(!empty($box_list)) : ?>
      <?php   foreach($box_list as $rs) : ?>
        <div class="box-control">
          <label class="box-label">
            <?php if($order->state == 6) : ?>
              <input type="radio" class="ace box-radio" name="box"
              id="box-<?php echo $rs->id; ?>" value="<?php echo $rs->id; ?>"
              onchange="confirmSaveBeforeChangeBox(<?php echo $rs->id; ?>)" />
              <span class="lbl font-size-14">&nbsp;กล่องที่ <?php echo $rs->box_no; ?> | </span>
              <span class="font-size-11" style=""><?php echo $rs->code; ?></span>
            <?php else : ?>
              <span class="lbl font-size-14">&nbsp;กล่องที่ <?php echo $rs->box_no; ?> | </span>
              <span class="font-size-11" style=""><?php echo $rs->code; ?></span>
            <?php endif; ?>
            <select class="box-package form-control intput-xs" id="package-<?php echo $rs->id; ?>" onchange="updatePackageId(<?php echo $rs->id; ?>)">
              <?php echo select_active_package($rs->package_id); ?>
            </select>
          </label>
          
          <span class="box-count pull-left text-center">
            <span class="display-block font-size-11 padding-top-5">QTY</span>
            <span class="display-block font-size-16 padding-top-5" id="<?php echo $rs->id; ?>"><?php echo number($rs->qty); ?></span>
          </span>

          <?php if( $order->state == 6) : ?>
            <div class="btn-group">
              <button data-toggle="dropdown" class="btn btn-link btn-info dropdown-toggle box-menu" style="padding: 0px !important;" aria-expanded="false">
                <i class="ace-icon fa fa-angle-down icon-on-right"></i>
              </button>
              <ul class="dropdown-menu dropdown-menu-right">
                <li class="primary">
                  <a href="javascript:printBox(<?php echo $rs->id; ?>)"><i class="fa fa-print"></i> &nbsp; Packing list</a>
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
            <?php if($rs->qty > 0) : ?>
              <button class="btn btn-link btn-info dropdown-toggle box-menu" style="padding: 0px !important;" onclick="printBox(<?php echo $rs->id; ?>)">
                <i class="ace-icon fa fa-print icon-on-right"></i>
              </button>
            <?php else : ?>
              <span class="">&nbsp;</span>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      <?php   endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<hr/>

<script id="box-template" type="text/x-handlebarsTemplate">
  {{#each this}}
    <div class="box-control">
      <label class="box-label">
          <input type="radio" class="ace box-radio" name="box"
          id="box-{{id_box}}" value="{{id_box}}"
          onchange="selectBox({{id_box}})" {{checked}}/>
          <span class="lbl font-size-14">&nbsp;กล่องที่ {{no}} | </span>
          <span class="font-size-11">{{code}}</span>
        <select class="box-package form-control intput-xs" id="package-{{id_box}}" onchange="updatePackageId({{id_box}})">
          {{{package}}}
        </select>
      </label>

      <span class="box-count pull-left text-center">
        <span class="display-block font-size-11 padding-top-5">QTY</span>
        <span class="display-block font-size-16 padding-top-5" id="{{id_box}}">{{qty}}</span>
      </span>

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

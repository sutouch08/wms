
<!-- แสดงผลกล่อง  -->
<div class="row" id="box-row">
    <?php if(!empty($box_list)) : ?>
      <?php   foreach($box_list as $rs) : ?>
        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 padding-5 padding-top-5">
          <div class="input-group width-100">
            <span class="input-group-addon" style="text-align:left;">
              <label>
                <?php if($order->state == 6) : ?>
                <input type="radio" class="ace box-radio" name="box"
                id="box-<?php echo $rs->id; ?>" value="<?php echo $rs->id; ?>"
                onchange="confirmSaveBeforeChangeBox(<?php echo $rs->id; ?>)" />
              <?php endif; ?>
                <span class="lbl">
                  <?php if( ! $order->state == 6) : ?>
                    <i class="fa fa-cube fa-lg"></i>
                  <?php endif; ?>
                  &nbsp;<?php echo $rs->code; ?> : กล่องที่  <?php echo $rs->box_no; ?>
                </span>
              </label>
            </span>
            <span class="input-group-addon font-size-12" id="<?php echo $rs->id; ?>">
              <?php echo number($rs->qty); ?>
            </span>
        <?php if( $order->state == 6) : ?>
            <div class="btn-group">
        			<button data-toggle="dropdown" class="btn btn-info dropdown-toggle" style="padding-left:5px;" aria-expanded="false">
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
            <span class="input-group-addon" onclick="printBox(<?php echo $rs->id; ?>)"><i class="fa fa-print blue font-size-18 pointer"></i></span>
          <?php else : ?>
            <span class="input-group-addon">&nbsp;</span>
          <?php endif; ?>
        <?php endif; ?>
          </div>
        </div>
      <?php   endforeach; ?>
    <?php else : ?>
      <div class="col-lg-12 col-md-12 col-sm-12 col-sm-12 padding-5"><span id="no-box-label">ยังไม่มีการตรวจสินค้า</span></div>
    <?php endif; ?>
</div>

<hr/>

<script id="box-template" type="text/x-handlebarsTemplate">
  {{#each this}}
    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 padding-5 padding-top-5">
      <div class="input-group width-100">
        <span class="input-group-addon" style="text-align:left;">
          <label>
            <input type="radio" class="ace box-radio" name="box" id="box-{{id_box}}" value="{{id_box}}" onchange="selectBox({{id_box}})" {{checked}}/>
            <span class="lbl">&nbsp;{{code}} : กล่องที่  {{no}}</span>
          </label>
        </span>
        <span class="input-group-addon" id="{{id_box}}">{{qty}}</span>
        <div class="btn-group">
          <button data-toggle="dropdown" class="btn btn-info dropdown-toggle" style="padding-left:5px;" aria-expanded="false">
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
    </div>
  {{/each}}
</script>

<!-- แสดงผลกล่อง  -->

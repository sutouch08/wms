<div class="goback">
  <a class="goback-icon pull-left" onclick="goBack()"><i class="fa fa-angle-left fa-2x"></i></a>
</div>
<div class="toggle-header">
  <a class="toggle-header-icon" onclick="toggleHeader()"><i class="fa fa-ellipsis-v fa-2x"></i></a>
</div>
<div class="form-horizontal filter-pad move-out" id="header-pad">
  <div class="nav-title">
    <a class="pull-left" onclick="closeHeader()"><i class="fa fa-angle-left fa-2x"></i></a>
    <div class="font-size-18 text-center">ข้อมูลเอกสาร</div>
  </div>
  <div class="form-group margin-top-20">
    <div class="col-xs-6 padding-5">
      <label>เลขที่เอกสาร</label>
      <input type="text" class="width-100" id="code" value="<?php echo $doc->code; ?>" disabled/>
      <input type="hidden" id="id" value="<?php echo $doc->id; ?>" />
    </div>
    <div class="col-xs-6 padding-5">
      <label>วันที่</label>
      <input type="text" class="width-100 text-center" value="<?php echo thai_date($doc->date_add); ?> " disabled/>
    </div>
  </div>
  <div class="form-group">
    <div class="col-xs-12 padding-5">
      <label>คลังสินค้าต้นทาง</label>
      <select class="form-control input-sm" id="warehouse" disabled>
        <option value="">เลือกคลัง</option>
        <?php echo select_common_warehouse($doc->warehouse_code); ?>
      </select>      
    </div>
  </div>
  <div class="form-group">
    <div class="col-xs-12 padding-5">
      <label>โซนปลายทาง</label>
  		<select class="form-control input-sm" id="zone" disabled>
  			<option value="">เลือกโซน</option>
  			<?php echo select_pickface_zone($doc->zone_code); ?>
  		</select>
    </div>
  </div>
  <div class="form-group">
    <div class="col-xs-6 padding-5">
      <label>ช่องทางขาย</label>
  		<select class="form-control input-sm" id="channels" disabled>
  			<option value="">เลือกช่องทางขาย</option>
  			<?php echo select_channels($doc->channels_code); ?>
  		</select>
    </div>
    <div class="col-xs-6 padding-5">
      <label>Owner</label>
      <input type="text" class="width-100" value="<?php echo $doc->user; ?>" disabled/>
    </div>
  </div>

  <div class="form-group">
    <div class="col-xs-12 padding-5">
      <label>หมายเหตุ</label>
      <textarea class="form-control input-sm" rows="4" disabled><?php echo $doc->remark; ?></textarea>
    </div>
  </div>
</div><!-- end from-horizontal -->
<input type="hidden" id="code" value="<?php echo $doc->code; ?>" />
<input type="hidden" id="warehouse-code" value="<?php echo $doc->warehouse_code; ?>" />
<hr class="margin-top-15 margin-bottom-15"/>

<div class="goback">
  <a class="goback-icon pull-left" onclick="goBack()"><i class="fa fa-angle-left fa-2x"></i></a>
</div>
<div class="toggle-header">
  <a class="toggle-header-icon" onclick="showHeader()"><i class="fa fa-bars fa-2x"></i></a>
</div>
<div class="form-horizontal filter-pad move-out" id="header-pad">
  <div class="nav-title">
    <a class="pull-left" onclick="closeHeader()"><i class="fa fa-angle-left fa-2x"></i></a>
    <div class="font-size-18 text-center">ข้อมูลเอกสาร</div>
  </div>
  <div class="form-group margin-top-20">
    <div class="col-xs-6 padding-5">
      <label>เลขที่</label>
      <input type="text" class="form-control input-sm text-center" id="code" value="<?php echo $doc->code; ?>" disabled/>
  		<input type="hidden" id="id" value="<?php echo $doc->id; ?>" />
    </div>
    <div class="col-xs-6 padding-5">
      <label>วันที่</label>
      <input type="text" class="form-control input-sm text-center" id="date-add" value="<?php echo thai_date($doc->date_add, FALSE); ?>" disabled/>
    </div>
  </div>
  <div class="form-group">
    <div class="col-xs-12 padding-5">
      <label>ช่องทางขาย</label>
  		<select class="form-control input-sm e" id="channels" disabled>
  			<option value="" data-name="">เลือก</option>
  			<?php echo select_dispatch_channels($doc->channels_code); ?>
  		</select>
    </div>
  </div>
  <div class="form-group">
    <div class="col-xs-12 padding-5">
      <label>ผู้จัดส่ง</label>
  		<select class="form-control input-sm e" id="sender" disabled>
  			<option value="">เลือก</option>
  			<?php echo select_sender($doc->sender_code); ?>
  		</select>
    </div>
  </div>
  <div class="form-group">
    <div class="col-xs-6 padding-5">
      <label>ทะเบียนรถ</label>
      <input type="text" class="form-control input-sm e" id="plate-no" value="<?php echo $doc->plate_no; ?>" disabled/>
    </div>
    <div class="col-xs-6 padding-5">
      <label>จังหวัด</label>
      <input type="text" class="form-control input-sm e" id="province" value="<?php echo $doc->plate_province; ?>" disabled/>
    </div>
  </div>
  <div class="form-group">
    <div class="col-xs-12 padding-5">
      <label>ชื่อคนขับ</label>
      <input type="text" class="form-control input-sm e" id="driver-name" value="<?php echo $doc->driver_name; ?>" disabled/>
    </div>
  </div>
  <div class="form-group">
    <div class="col-xs-12 padding-5">
      <label>หมายเหตุ</label>
      <textarea class="form-control input-sm  e" id="remark" disabled><?php echo $doc->remark; ?></textarea>
    </div>
  </div>
  <div class="divider"></div>
  <div class="form-group">
    <div class="col-xs-12 padding-5">
      <button type="button" class="btn btn-xs btn-warning btn-block" id="btn-edit" onclick="getEdit()"><i class="fa fa-pencil"></i>  แก้ไข</button>
      <button type="button" class="btn btn-xs btn-success btn-block hide" id="btn-update" onclick="update()"><i class="fa fa-save"></i> Update</button>
    </div>
  </div>
</div><!-- end from-horizontal -->
<hr class="margin-top-15 margin-bottom-15"/>

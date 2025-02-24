<div class="goback">
  <a class="goback-icon pull-left" onclick="goBack()"><i class="fa fa-angle-left fa-2x"></i></a>
</div>
<div class="toggle-header">
  <!-- <a class="toggle-header-icon" onclick="toggleHeader()"><i class="fa fa-bars fa-2x"></i></a> -->
  <div class="btn-group">
    <button data-toggle="dropdown" class="btn btn-link dropdown-toggle" aria-expanded="false">
      <i class="ace-icon fa fa-bars fa-2x white"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-right">
      <li>
        <a href="javascript:save()"><i class="fa fa-save"></i>&nbsp;&nbsp;&nbsp;ปิดเอกสาร</a>
      </li>      
    </ul>
  </div>
</div>
<div class="form-horizontal filter-pad move-out" id="header-pad">
  <div class="nav-title">
    <a class="pull-left" onclick="closeHeader()"><i class="fa fa-angle-left fa-2x"></i></a>
    <div class="font-size-18 text-center">ข้อมูลเอกสาร</div>
  </div>
  <div class="form-group margin-top-20">
    <div class="col-xs-6 padding-5">
      <label>เลขที่เอกสาร</label>
      <input type="text" class="width-100" id="code" value="<?php echo $doc->code; ?> " readonly/>
    </div>
    <div class="col-xs-6 padding-5">
      <label>Pallet No</label>
      <input type="text" class="width-100" value="<?php echo $doc->pallet_no; ?> " readonly/>
    </div>
  </div>
  <div class="form-group">
    <div class="col-xs-6 padding-5">
      <label>วันที่</label>
      <input type="text" class="width-100 text-center" value="<?php echo thai_date($doc->date_add); ?> " readonly/>
    </div>
    <div class="col-xs-6 padding-5">
      <label>การดำเนินการ</label>
      <input type="text" class="width-100" value="WARRIX" readonly/>
    </div>
  </div>
  <div class="form-group">
    <div class="col-xs-12 padding-5">
      <label>ต้นทาง</label>
      <input type="text" class="width-100" value="<?php echo $doc->from_warehouse; ?> | <?php echo $doc->from_warehouse_name; ?>" readonly/>
    </div>
  </div>
  <div class="form-group">
    <div class="col-xs-12 padding-5">
      <label>ปลายทาง</label>
      <input type="text" class="width-100" value="<?php echo $doc->to_warehouse; ?> | <?php echo $doc->to_warehouse_name; ?>" readonly/>
    </div>
  </div>
  <div class="form-group">
    <div class="col-xs-12 padding-5">
      <label>หมายเหตุ</label>
      <textarea class="width-100" readonly><?php echo $doc->remark; ?></textarea>
    </div>
  </div>
</div><!-- end from-horizontal -->
<input type="hidden" id="transfer-code" value="<?php echo $doc->code; ?>" />
<input type="hidden" id="from-warehouse-code" value="<?php echo $doc->from_warehouse; ?>" />
<input type="hidden" id="to-warehouse-code" value="<?php echo $doc->to_warehouse; ?>" />
<input type="hidden" id="active-focus" value="F" />
<hr class="margin-top-15 margin-bottom-15"/>

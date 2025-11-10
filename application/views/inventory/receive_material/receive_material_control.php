<!--  Search Product -->
<div class="row">
	<div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
		<label>ใบสั่งซื้อ</label>
		<input type="text" class="form-control input-sm text-center"	id="po-code" value="<?php echo $doc->po_code; ?>" placeholder="ค้นหาใบสั่งซื้อ" />
	</div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-3 padding-5">
		<label class="display-block not-show">confirm</label>
		<button type="button" class="btn btn-xs btn-primary btn-block" id="btn-get-po" onclick="getPoDetail()">Load</button>
	</div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-3 padding-5">
		<label class="display-block not-show">confirm</label>
		<button type="button" class="btn btn-xs btn-warning btn-block" id="btn-clear-po" onclick="clearPo()">Clear</button>
	</div>

	<div class="divider visible-xs"></div>

	<div class="col-lg-7-harf col-md-6 col-sm-5 col-xs-8">&nbsp;</div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label class="display-block not-show hidden-xs">confirm</label>
		<button type="button" class="btn btn-xs btn-danger btn-block" onclick="removeChecked()">ลบรายการ</button>
	</div>
</div>

<div class="divider-hidden">	</div>

<div class="modal fade" id="poGrid" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:900px; max-width:95vw;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <center style="margin-bottom:10px;"><h4 class="modal-title" id="po-title">ใบสั่งซื้อ</h4></center>
      </div>
      <div class="modal-body" style="max-width:94vw; min-height:300px; max-height:70vh; overflow:auto;">
        <table class="table table-striped table-bordered" style="font-size:11px; table-layout: fixed; min-width:840px;">
          <thead>
            <th class="fix-width-40 text-center">#</th>
            <th class="fix-width-100 text-center">รหัส</th>
            <th class="min-width-300 text-center">สินค้า</th>
						<th class="fix-width-100 text-center">หน่วยนับ</th>
						<th class="fix-width-100 text-center">ราคา</th>
						<th class="fix-width-100 text-center">ส่วนลด</th>
            <th class="fix-width-100 text-center">ค้างรับ</th>
            <th class="fix-width-100 text-center">จำนวน</th>
          </thead>
          <tbody id="po-body">

          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default top-btn" id="btn_close" data-dismiss="modal">ปิด</button>
				<button type="button" class="btn btn-yellow top-btn" onclick="receiveAll()">รับยอดค้างทั้งหมด</button>
				<button type="button" class="btn btn-purple top-btn" onclick="clearAll()">เคลียร์ตัวเลขทั้งหมด</button>
        <button type="button" class="btn btn-primary top-btn" onclick="addPoItems()">เพิ่มในรายการ</button>
       </div>
    </div>
  </div>
</div>


<div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
	<div class="modal-dialog input-xlarge">
    <div class="modal-content">
      <div class="modal-header">
      	<button type='button' class='close' data-dismiss='modal' aria-hidden='true'> &times; </button>
		    <h4 class='modal-title-site text-center' > ผู้มีอำนาจอนุมัติรับสินค้าเกิน </h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-sm-12">
          	<input type="password" class="form-control input-sm text-center" id="sKey" />
            <span class="help-block red text-center" id="approvError">&nbsp;</span>
          </div>
          <div class="col-sm-12">
            <button type="button" class="btn btn-sm btn-primary btn-block" onclick="doApprove()">อนุมัติ</button>
          </div>
        </div>
    	 </div>
      </div>
    </div>
</div>

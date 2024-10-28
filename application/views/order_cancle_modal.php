<?php $this->load->helper('cancel_reason'); ?>
<?php $hide = $this->_SuperAdmin ? '' : 'hide'; ?>

<div class="modal fade" id="cancle-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
 <div class="modal-dialog" style="max-width:95%; margin-left:auto; margin-right:auto;">
   <div class="modal-content">
       <div class="modal-header" style="border-bottom:solid 1px #ddd;">
       <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
       <h4 class="modal-title text-center">เหตุผลในการยกเลิก</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <label>เหตุผลในการยกเลิก (จำเป็น)</label>
            <select class="width-100" id="reason-id">
              <option value="">กรุณาเลือก</option>
              <?php echo select_cancel_reason(); ?>
            </select>
          </div>
          <div class="divider-hidden"></div>
          <div class="divider-hidden"></div>
          <div class="divider-hidden"></div>
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <label>คำอธิบาย (จำเป็น)</label>
            <textarea class="form-control input-sm" id="cancle-reason" maxlength="250" style="min-height:100px;" placeholder="กรุณาระบุเหตุผลในการยกเลิกอย่างน้อย 10 ตัวอักษร"></textarea>
            <input type="hidden" id="cancle-code" value="" />
          </div>
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-top-15 <?php echo $hide; ?>">
            <label>
              <input type="checkbox" class="ace" name="force_cancel" id="force-cancel" value="1" />
              <span class="lbl">&nbsp;&nbsp; Force cancel</span>
            </label>
          </div>
        </div>
       </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-info" onclick="doCancle()">Submit</button>
      </div>
   </div>
 </div>
</div>

<script>//$('#reason-id').select2(); </script>

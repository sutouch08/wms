<div class="modal fade" id="cancle-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
 <div class="modal-dialog" style="width:500px;">
   <div class="modal-content">
       <div class="modal-header">
       <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
       <h4 class="modal-title">เหตุผลในการยกเลิก</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-sm-9 padding-5">
            <input type="text" class="form-control input-sm" id="cancle-reason" value=""/>
						<input type="hidden" name="cancle_code" id="cancle-code" value="" />
          </div>
          <div class="col-sm-3 padding-5">
            <button type="button" class="btn btn-xs btn-info btn-block" onclick="doCancle()">ตกลง</button>
          </div>
        </div>

       </div>
      <div class="modal-footer">

      </div>
   </div>
 </div>
</div>

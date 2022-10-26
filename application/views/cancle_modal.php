<div class="modal fade" id="cancle-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
 <div class="modal-dialog" style="max-width:95%; margin-left:auto; margin-right:auto;">
   <div class="modal-content">
       <div class="modal-header">
       <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
       <h4 class="modal-title">เหตุผลในการยกเลิก</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <input type="text" class="form-control input-sm" id="cancle-reason" maxlength="100" value=""/>
            <input type="hidden" id="cancle-code" value="" />
          </div>
        </div>
       </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-info" onclick="doCancle()">Submit</button>
      </div>
   </div>
 </div>
</div>

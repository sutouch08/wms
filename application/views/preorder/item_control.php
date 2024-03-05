<!--  Search Product -->
<div class="row">
	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-8 padding-5 margin-bottom-10">
    <input type="text" class="form-control input-sm text-center item-control" id="model-code" placeholder="รุ่นสินค้า" autofocus/>
  </div>
  <div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-4 padding-5 margin-bottom-10">
  	<button type="button" class="btn btn-xs btn-primary btn-block item-control" onclick="addByModel()">Add</button>
  </div>

	<div class="divider visible-xs"></div>

  <div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5 margin-bottom-10">
    <input type="text" class="form-control input-sm text-center item-control" id="item-code" placeholder="รหัสสินค้า">
		<input type="hidden" id="item-data" />
  </div>

  <div class="col-sm-1 col-xs-2 padding-5 margin-bottom-10">
    <button type="button" class="btn btn-xs btn-primary btn-block item-control" onclick="addItem()">Add</button>
  </div>

	<div class="divider visible-xs"></div>

	<div class="col-lg-1-harf col-md-1 col-sm-1 col-xs-8">&nbsp;</div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<button type="button" class="btn btn-xs btn-danger btn-block" onclick="removeChecked()">ลบรายการ</button>
	</div>
</div>

<div class="divider-hidden">	</div>



<div class="modal fade" id="itemGrid" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" id="modal" style="min-width:300px; min-height:400px; max-width:95vw; max-height:95vh;">
		<div class="modal-content">
  			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="modalTitle">title</h4>
			 </div>
			 <div class="modal-body">
         <div class="row">
           <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5"
           id="modalBody"
           style="position:relative; min-width:250px; min-height:400px; max-width:100%; max-height:60vh; overflow:auto;">

           </div>
         </div>
       </div>
			 <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
				<button type="button" class="btn btn-primary" onClick="addItems()" >เพิ่มในรายการ</button>
			 </div>
		</div>
	</div>
</div>

<hr/>

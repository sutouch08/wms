<div class="row">
  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>วันที่</label>
    <div class="input-group width-100">
      <input type="text" class="form-control input-sm width-50 text-center from-date" id="order-from-date" value="" />
      <input type="text" class="form-control input-sm width-50 text-center" id="order-to-date" value="" />
    </div>
  </div>
  <div class="col-lg-2-harf col-md-2-harf col-sm-2-harf col-xs-12 padding-5">
		<label>ช่องทางขาย</label>
		<select class="width-100" id="channels-code" <?php echo empty($doc->channels_code) ? "" : "disabled"; ?>>
			<option value="all">เลือกช่องทางขาย</option>
			<?php echo select_channels($doc->channels_code); ?>
		</select>
	</div>
  <div class="col-lg-2 col-md-2 col-sm-3 col-xs-6 padding-5">
    <label>ลูกค้า</label>
    <input type="text" class="width-100" id="customer" />
  </div>
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>เลขที่ออเดอร์</label>
    <input type="text" class="width-100 text-center" id="order-code" />
  </div>
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>Pick List</label>
    <select class="width-100" id="is-pick-list">
      <option value="0">ยังไม่เคยเปิด</option>
      <option value="1">เคยเปิดแล้ว</option>
      <option value="all">ทั้งหมด</option>
    </select>
  </div>
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label class="display-block not-show">x</label>
    <button type="button" class="btn btn-xs btn-primary btn-block" onclick="getOrderList()">แสดงรายการ</button>
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">x</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearOrderList()">Clear</button>
  </div>
</div>
<hr class="margin-bottom-15"/>


<div class="modal fade" id="orderListModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:900px; max-width:90vw; max-height:80vh;">
    <div class="modal-content">
        <div class="modal-header" style="border-bottom:solid 1px #ccc;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">ออเดอร์รอจัด</h4>
       </div>
       <div class="modal-body">
         <div class="row">
           <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5" style="max-height:60vh; overflow:auto;">
             <table class="table table-striped border-1 tableFixHead" style="min-width:700px;">
               <thead>
                 <tr>
                   <th class="fix-width-50 text-center fix-header">
                     <label>
                       <input type="checkbox" class="ace chk-all" id="chk-all" onchange="checkOrderAll($(this))" />
                       <span class="lbl"></span>
                     </label>
                   </th>
                   <th class="fix-width-50 text-center fix-header">#</th>
                   <th class="fix-width-100 text-center fix-header">วันที่</th>
                   <th class="fix-width-150 fix-header">เลขที่</th>
                   <th class="fix-width-150 fix-header">ช่องทางขาย</th>
                   <th class="min-width-200 fix-header">ลูกค้า</th>
                 </tr>
               </thead>
               <tbody id="order-list">

               </tbody>
             </table>
           </div>
         </div>
       </div>
       <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
        <button type="button" class="btn btn-primary" onClick="addToPickList()" >เพิ่มในรายการ</button>
       </div>
    </div>
  </div>
</div>


<script id="order-template" type="text/x-handlebarsTemplate">
  {{#each this}}
    <tr class="font-size-11" id="order-{{id}}">
      <td class="middle text-center">
        <label>
          <input type="checkbox" class="ace chk-list" value="{{code}}"/>
          <span class="lbl">{{#if pick_list_id}}<span class="red">*</span>{{/if}}</span>
        </label>
      </td>
      <td class="text-center">{{no}}</td>
      <td class="text-center">{{date_add}}</td>
      <td class="">{{code}}</td>
      <td class="">{{channels}}</td>
      <td class="">{{customer}}</td>
    </tr>
  {{/each}}
</script>

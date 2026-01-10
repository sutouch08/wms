<div class="modal fade" id="bom-list" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width:650px; max-width:95vw;">
		<div class="modal-content">
  			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">List of Bill of Materials</h4>
			 </div>
			 <div class="modal-body">
         <div class="row" style="margin:0px;">
           <label class="sap-label fix-width-100 float-left">Find</label>
           <input type="text" class="float-left form-control input-xs input-xlarge" id="bom-filter" />
           <div class="divider-hidden"></div>
           <div class="divider-hidden"></div>
           <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 border-1" style="height:300px; max-height:80vh; padding:0; overflow:scroll;;" id="bom-list-table">
             <table class="table table-bordered tableFixHead dataTable" id="bom-table" style="min-width:600px; margin-left:-1px;">
               <thead>
                 <tr>
                   <th class="fix-width-40 text-center fix-header">#</th>
                   <th class="fix-width-250 fix-header">Item No.</th>
                   <th class="min-width-300 fix-header">Item Description</th>
                 </tr>
               </thead>
               <tbody>
                 <?php if( ! empty($bomList)) : ?>
                   <?php $ne = 1; ?>
                   <?php foreach($bomList as $rs) : ?>
                     <tr class="font-size-11 bom-rows" id="bon-<?php echo $ne; ?>" onclick="toggleBoxSelect(<?php echo $ne; ?>)">
                       <td class="middle text-center">
                         <?php echo $ne; ?>
                         <input type="checkbox" class="bom-chk hide" id="bom-chk-<?php echo $ne; ?>" data-code="<?php echo $rs->Code; ?>" data-name="<?php echo $rs->Name; ?>" />
                       </td>
                       <td class="middle text-center"><input type="text" class="form-control input-xs text-label" value="<?php echo $rs->Code; ?>" readonly /></td>
                       <td class="middle text-center"><input type="text" class="form-control input-xs text-label" value="<?php echo $rs->Name; ?>" readonly /></td>
                     </tr>
                     <?php $ne++; ?>
                   <?php endforeach; ?>
                 <?php endif; ?>
               </tbody>
             </table>
           </div>
         </div>
       </div>
			 <div class="modal-footer">
         <button type="button" class="btn btn-xs btn-primary" onClick="addToOrder()" >Choose</button>
				<button type="button" class="btn btn-xs btn-default" data-dismiss="modal">Cancel</button>
			 </div>
		</div>
	</div>


	<div class="modal fade" id="wh-list" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" style="width:650px; max-width:95vw;">
			<div class="modal-content">
	  			<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">Warehouse List</h4>
				 </div>
				 <div class="modal-body">
	         <div class="row" style="margin:0px;">	           
	           <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 border-1" style="height:300px; max-height:80vh; padding:0; overflow:scroll;;" id="bom-list-table">
	             <table class="table table-bordered tableFixHead dataTable" id="bom-table" style="min-width:600px; margin-left:-1px;">
	               <thead>
	                 <tr>
	                   <th class="fix-width-40 text-center fix-header">#</th>
	                   <th class="fix-width-250 fix-header">Item No.</th>
	                   <th class="min-width-300 fix-header">Item Description</th>
	                 </tr>
	               </thead>
	               <tbody>
	                 <?php if( ! empty($bomList)) : ?>
	                   <?php $ne = 1; ?>
	                   <?php foreach($bomList as $rs) : ?>
	                     <tr class="font-size-11 bom-rows" id="bon-<?php echo $ne; ?>" onclick="toggleBoxSelect(<?php echo $ne; ?>)">
	                       <td class="middle text-center">
	                         <?php echo $ne; ?>
	                         <input type="checkbox" class="bom-chk hide" id="bom-chk-<?php echo $ne; ?>" data-code="<?php echo $rs->Code; ?>" data-name="<?php echo $rs->Name; ?>" />
	                       </td>
	                       <td class="middle text-center"><input type="text" class="form-control input-xs text-label" value="<?php echo $rs->Code; ?>" readonly /></td>
	                       <td class="middle text-center"><input type="text" class="form-control input-xs text-label" value="<?php echo $rs->Name; ?>" readonly /></td>
	                     </tr>
	                     <?php $ne++; ?>
	                   <?php endforeach; ?>
	                 <?php endif; ?>
	               </tbody>
	             </table>
	           </div>
	         </div>
	       </div>
				 <div class="modal-footer">
	         <button type="button" class="btn btn-xs btn-primary" onClick="addToOrder()" >Choose</button>
					<button type="button" class="btn btn-xs btn-default" data-dismiss="modal">Cancel</button>
				 </div>
			</div>
		</div>
	</div>
</div>

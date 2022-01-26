// JavaScript Document
function getItemGrid(){
	var itemCode 	= $("#item-code").val();
	var whCode = $('#warehouse').val();
	var isView = $('#view').length;
	if( itemCode.length > 0  ){
		$.ajax({
			url:BASE_URL + 'orders/orders/get_item_grid',
			type:'GET',
			cache:false,
			data:{
				'warehouse_code' : whCode,
				'itemCode' : itemCode,
				'isView' : isView
			},
			success:function(rs){
				var rs = rs.split(' | ');
				if(rs[0] === 'success'){
					$('#stock-qty').val(rs[2]);
					$('#input-qty').val('').focus();
				}else{
					$('#stock-qty').val('');
					$('#input-qty').val('');
					swal(rs[1]);
				}
			}
		})
	}
}



// // JavaScript Document
// function getProductGrid(){
// 	var pdCode 	= $("#pd-box").val();
// 	var whCode = $('#warehouse').val();
// 	var isView = $('#view').length;
// 	if( pdCode.length > 0  ){
// 		load_in();
// 		$.ajax({
// 			url: BASE_URL + 'orders/orders/get_order_grid',
// 			type:"GET",
// 			cache:"false",
// 			data:{
// 				"style_code" : pdCode,
// 				"warehouse_code" : whCode,
// 				"isView" : isView
// 			},
// 			success: function(rs){
// 				load_out();
// 				var ars = rs.split(' | ');
// 				if( ars.length == 5 ){
// 					var grid = ars[0];
// 					var width = ars[1];
// 					var pdCode = ars[2] + ' | ' + ars[3] + '<br/>' + ars[4];
// 					//var style = rs[3];
// 					if(grid == 'notfound'){
// 						swal("ไม่พบสินค้า");
// 						return false;
// 					}
// 					$("#modal").css("width", width +"px");
// 					$("#modalTitle").html(pdCode);
// 					//$("#id_style").val(style);
// 					$("#modalBody").html(grid);
// 					$("#orderGrid").modal('show');
// 				}else{
// 					swal("สินค้าไม่ถูกต้อง");
// 				}
// 			}
// 		});
// 	}
// }


// JavaScript Document
function getProductGrid(){
	var pdCode 	= $("#pd-box").val();
	var whCode = $('#warehouse').val();
	var isView = $('#view').length;
	if( pdCode.length > 0  ){
		load_in();
		$.ajax({
			url: BASE_URL + 'orders/orders/get_order_grid',
			type:"GET",
			cache:"false",
			data:{
				"style_code" : pdCode,
				"warehouse_code" : whCode,
				"isView" : isView
			},
			success: function(rs){
				load_out();
				if(isJson(rs)) {
					let ds = $.parseJSON(rs);
					$('#modal').css('width', ds.tableWidth + 'px');
					$('#modalTitle').html(ds.styleCode + ' | ' + ds.styleOldCode + '<br/>' + ds.styleName);
					$('#modalBody').html(ds.table);
					$('#orderGrid').modal('show');
				}
				else {
					swal(rs);
				}
			}
		});
	}
}



function getOrderGrid(styleCode){
	var whCode = $('#warehouse').val();
	var isView = $('#view').length;
	load_in();
	$.ajax({
		url: BASE_URL + 'orders/orders/get_order_grid',
		type:"GET",
		cache:"false",
		data:{
			"style_code" : styleCode,
			"warehouse_code" : whCode,
			"isView" : isView
		},
		success: function(rs){
			load_out();
			if(isJson(rs)) {
				let ds = $.parseJSON(rs);
				$('#modal').css('width', ds.tableWidth + 'px');
				$('#modalTitle').html(ds.styleCode + ' | ' + ds.styleOldCode + '<br/>' + ds.styleName);
				$('#modalBody').html(ds.table);
				$('#orderGrid').modal('show');
			}
			else {
				swal(rs);
			}
		}
	});
}


function valid_qty(el, qty){
	var order_qty = el.val();
	if(parseInt(order_qty) > parseInt(qty) )	{
		swal('สั่งได้ '+qty+' เท่านั้น');
		el.val('');
		el.focus();
	}
}

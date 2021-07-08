function sendToWMS()
{
	var order_code = $('#order_code').val();

	load_in();
	$.ajax({
		url:BASE_URL + 'orders/orders/send_to_wms/',
		type:'POST',
		cache:false,
		data:{
			'code' : order_code
		},
		success:function(rs) {
			load_out();
			var rs = $.trim(rs);
			if(rs === 'success') {
				swal({
					title:'Success',
					type:'success',
					timer:1000
				})

				setTimeout(function(){
					window.location.reload();
				}, 1200);
			}
			else {
				swal({
					title:'Error!',
					text:rs,
					type:'error',
					html:true
				});
			}
		},
		error:function(xhr, status, error) {
			load_out();
			swal({
				title:'Error!',
				text:'Error - '+xhr.responseText,
				type:'error',
				html:true
			});
		}
	})
}


function setWms(order_code)
{
	$.ajax({
		url:BASE_URL + 'orders/orders/set_order_wms',
		type:'POST',
		cache:false,
		data:{
			'order_code' : order_code
		},
		success:function(rs) {
			console.log(rs);
		}
	})
}


// window.addEventListener('load', () => {
//   let height = $(window).height();
// 	let pageContentHeight = height - 128;
// 	// header = 80, hr = 15, table margin = 10, footer 170, margin-bottom = 15
// 	let itemTableHeight = pageContentHeight - (112);
//
// 	$('.page-content').css('height', pageContentHeight + 'px');
// 	$('#order-table').css('height', itemTableHeight + 'px');
//
// })

$(document).ready(function() {
	//---	reload ทุก 5 นาที
	setTimeout(function(){ goBack(); }, 300000);
});


function sendToWms(code) {
	load_in();
	$.ajax({
		url:BASE_URL + 'orders/orders/send_to_wms',
		type:'POST',
		cache:false,
		data:{
			'code' : code
		},
		success:function(rs) {
			load_out();
			var rs = $.trim(rs);
			if(rs === 'success') {
				swal({
					title:'Success',
					type:'success',
					timer:1000
				});
			}
			else {
				swal({
					title:"Error",
					text:rs,
					type:"error",
					html:true
				}, function() {
          window.location.reload();
        });
			}
		},
		error:function(xhr, status, error) {
			load_out();
			swal({
				title:'Error!!',
				type:'error',
				text:xhr.responseText,
				html:true
			}, function() {
        window.location.reload();
      })
		}
	})
}


$('#chk-all').change(function() {
	if($(this).is(':checked')) {
		$('.chk-wms').prop('checked', true);
	}
	else {
		$('.chk-wms').prop('checked', false);
	}
})


function sendOrdersToWms() {

	let count = $('.chk-wms:checked').length;

	if(count > 0) {
		let orders = [];

		$('.chk-wms:checked').each(function() {
			let code = $(this).data('code');
			orders.push(code);
		});

		if(orders.length > 0) {
			load_in();

			$.ajax({
				url:BASE_URL + 'orders/orders/send_multiple_orders_to_wms',
				type:'POST',
				cache:false,
				data: {
					'orders' : JSON.stringify(orders)
				},
				success:function(rs) {
					load_out();

					if(rs === 'success') {
						swal({
							title:'Success',
							type:'success',
							timer:1000
						})

						setTimeout(() => {
							goBack();
						}, 1200);
					}
					else {
						swal({
							title:'Error!',
							text:rs,
							type:'error',
							html:true
						}, function() {
              window.location.reload();
            })
					}
				},
				error:function(xhr, status, error) {
					load_out();
					swal({
						title:'Error!!',
						type:'error',
						text:xhr.responseText,
						html:true
					}, function() {
            window.location.reload();
          })
				}
			})
		}
	}
}

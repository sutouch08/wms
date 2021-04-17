
var ROLE = $('#role').val();
if(ROLE == 'Q')
{
  var HOME = BASE_URL + 'inventory/transform_stock/';
}
else
{
  var HOME = BASE_URL + 'inventory/transform/';
}



function addNew(){
  window.location.href = HOME + 'add_new';
}



function goBack(){
  window.location.href = HOME;
}



function editDetail(){
  var code = $('#order_code').val();
  window.location.href = HOME + 'edit_detail/'+ code;
}


function editOrder(code){
  window.location.href = HOME + 'edit_order/'+ code;
}



function clearFilter(){
  var url = HOME + 'clear_filter';
  $.get(url, function(rs){ goBack(); });
}



function getSearch(){
  $('#searchForm').submit();
}



$('.search').keyup(function(e){
  if(e.keyCode == 13){
    getSearch();
  }
});


$("#fromDate").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(ds){
		$("#toDate").datepicker("option", "minDate", ds);
	}
});

$("#toDate").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(ds){
		$("#fromDate").datepicker("option", "maxDate", ds);
	}
});

function approve()
{
  var order_code = $('#order_code').val();

	var is_wms = $('#is_wms').val();

	if(is_wms) {
		var id_address = $('#address_id').val();
		var id_sender = $('#id_sender').val();

		if(id_address == "") {
			swal("กรุณาระบุที่อยู่จัดส่ง");
			return false;
		}

		if(id_sender == "") {
			swal("กรุณาระบุผู้จัดส่ง");
			return false;
		}
	}

	load_in();
  $.ajax({
    url:BASE_URL + 'orders/orders/do_approve/'+order_code,
    type:'POST',
    cache:false,
    success:function(rs){
      if(rs === 'success'){
        change_state();
      }else{
				load_out();
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
				text:xhr.responseText,
				type:'error',
				html:true
			})
		}
  });
}



function unapprove()
{
  var order_code = $('#order_code').val();
  $.ajax({
    url:BASE_URL + 'orders/orders/un_approve/'+order_code,
    type:'POST',
    cache:false,
    success:function(rs){
      if(rs === 'success'){
        swal({
          title:'Success',
          text:'ยกเลิกการอนุมัติแล้ว',
          type:'success',
          timer:1000
        });

        setTimeout(function(){
          window.location.reload();
        }, 1500);
      }else{
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        });
      }
    }
  });
}

function change_state(){
  var order_code = $('#order_code').val();

  $.ajax({
    url:BASE_URL + 'orders/orders/order_state_change',
    type:'POST',
    cache:false,
    data:{
      'order_code' : order_code,
      'state' : 3
    },
    success:function(rs){
			load_out();
      if(rs === 'success'){
        swal({
          title:'Success',
          text:'ปล่อยจัดสินค้าเรียบร้อยแล้ว',
          type:'success',
          timer:1000
        });

        setTimeout(function(){
          window.location.reload();
        }, 1500);
      }else{
        swal({
          title:'Error!!',
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
				text:xhr.responseText,
				type:'error',
				html:true
			})
		}
  });
}

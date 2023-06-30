var HOME = BASE_URL + 'masters/items/';

function addNew(){
  window.location.href = HOME + 'add_new';
}



function goBack(){
  window.location.href = HOME;
}


function getEdit(code){
	$('#item-code').val(code);
	$('#edit-form').submit();
  //window.location.href = HOME + 'edit/'+code;
}


function update() {
	let error = 0;

	let data = {};

	data.code = $('#code').val().trim();
	data.old_code = $('#old_code').val().trim();
	data.name = $('#name').val().trim(); // required
	data.style = $('#style').val().trim(); // required
	data.old_style = $('#old_style').val().trim();
	data.color = $('#color').val().trim(); // required
	data.size = $('#size').val().trim(); // required
	data.barcode = $('#barcode').val().trim();
	data.cost = parseDefault(parseFloat($('#cost').val()), 0);
	data.price = parseDefault(parseFloat($('#price').val()), 0);
	data.unit_code = $('#unit_code').val(); // required
	data.brand_code = $('#brand').val();
	data.group_code = $('#group').val();
	data.main_group_code = $('#mainGroup').val(); // required
	data.sub_group_code = $('#subGroup').val();
	data.category_code = $('#category').val();
	data.kind_code = $('#kind').val();
	data.type_code = $('#type').val();
	data.year = $('#year').val();
	data.count_stock = $('#count_stock').is(':checked') ? 1 : 0;
	data.can_sell = $('#can_sell').is(':checked') ? 1 : 0;
	data.is_api = $('#is_api').is(':checked') ? 1 : 0;
	data.active = $('#active').is(':checked') ? 1 : 0;

	if(data.name.length === 0) {
		set_error($('#name'), $('#name-error'), "required");
		error++;
	}
	else {
		clear_error($('#name'), $('#name-error'));
	}

	if(data.style.length === 0) {
		set_error($('#style'), $('#style-error'), "required");
		error++;
	}
	else {
		clear_error($('#style'), $('#style-error'));
	}

	if(data.color.length === 0) {
		set_error($('#color'), $('#color-error'), "required");
		error++;
	}
	else {
		clear_error($('#color'), $('#color-error'));
	}

	if(data.size.length === 0) {
		set_error($('#size'), $('#size-error'), "required");
		error++;
	}
	else {
		clear_error($('#size'), $('#size-error'));
	}

	if(data.unit_code.length === 0) {
		set_error($('#unit_code'), $('#unit-error'), "required");
		error++;
	}
	else {
		clear_error($('#unit_code'), $('#unit-error'));
	}

	if(data.main_group_code.length === 0) {
		set_error($('#mainGroup'), $('#mainGroup-error'), "required");
		error++;
	}
	else {
		clear_error($('#mainGroup'), $('#mainGroup-error'));
	}

	if(error > 0) {
		return false;
	}

	load_in();

	$.ajax({
		url:HOME + 'update',
		type:'POST',
		cache:false,
		data:{
			"data" : JSON.stringify(data)
		},
		success:function(rs) {
			load_out();
			var rs = rs.trim();
			if(rs == 'success') {
				swal({
					title:"Success",
					type:'success',
					timer:1000
				});
			}
			else {
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				})
			}
		},
		error:function(xhr) {
			load_out();
			swal({
				title:"Error!",
				text:'Error : '+xhr.responseText,
				type:'error',
				html:true
			})
		}
	})



}

function duplicate(code){
  window.location.href = HOME + 'duplicate/'+code;
}



$('#style').autocomplete({
  source: BASE_URL + 'auto_complete/get_style_code',
  autoFocus:true,
  close:function() {
    let rs = $(this).val();
    let arr = rs.split(' | ');
    if(arr.length == 2) {
      $(this).val(arr[0]);
    }
    else {
      $(this).val('');
    }
  }
});

$('#color').autocomplete({
  source: BASE_URL + 'auto_complete/get_color_code_and_name',
  autoFocus:true,
  close:function(){
    var rs = $(this).val();
    var err = rs.split(' | ');
    if(err.length == 2){
      $(this).val(err[0]);
    }else{
      $(this).val('');
    }
  }
});


$('#size').autocomplete({
  source:BASE_URL + 'auto_complete/get_size_code_and_name',
  autoFocus:true,
  close:function(){
    var rs = $(this).val();
    var err = rs.split(' | ');
    if(err.length == 2){
      $(this).val(err[0]);
    }else{
      $(this).val('');
    }
  }
});


function checkAdd(){
  var code = $('#code').val();
  if(code.length > 0){
    $.ajax({
      url:HOME + 'is_exists_code/'+code,
      type:'GET',
      cache:false,
      success:function(rs){
        if(rs != 'ok'){
          set_error($('#code'), $('#code-error'), rs);
          return false;
        }else{
          clear_error($('#code'), $('#code-error'));
          $('#btn-submit').click();
        }
      }
    })
  }
}



function clearFilter(){
  var url = HOME + 'clear_filter';
  var page = BASE_URL + 'masters/products';
  $.get(url, function(){
    goBack();
  });
}


function getDelete(code){
  swal({
    title:'Are sure ?',
    text:'ต้องการลบ ' + code + ' หรือไม่ ?',
    type:'warning',
    showCancelButton: true,
		confirmButtonColor: '#FA5858',
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
  },function(){
    $.ajax({
      url: BASE_URL + 'masters/items/delete_item/' + code,
      type:'GET',
      cache:false,
      success:function(rs){
        if(rs === 'success'){
          swal({
            title:'Deleted',
            text:'ลบรุ่นสินค้าเรียบร้อยแล้ว',
            type:'success',
            timer:1000
          });

          $('#row-'+code).remove();
        }else{
          swal({
            title:'Error!',
            text:rs,
            type:'error'
          });
        }
      }
    })

  })
}

function getTemplate(){
  var token	= new Date().getTime();
	get_download(token);
	window.location.href = BASE_URL + 'masters/items/download_template/'+token;
}

function getSearch(){
  $('#searchForm').submit();
}


function sendToWms(code) {
	load_in();
	$.ajax({
		url:BASE_URL + 'masters/items/send_to_wms',
		type:'POST',
		cache:false,
		data:{
			'code' : code
		},
		success:function(rs) {
			load_out();
			if(rs === 'success') {
				swal({
					title:'Success',
					type:'success',
					timer:1000
				});
			}
			else {
				swal({
					title:'Error',
					text:rs,
					type:'error'
				})
			}
		}
	})
}

var wms_warehouse = "";

function goSetting(tab) {
	closeTab();
	setTimeout(() => {
		$('#'+tab).addClass('move-in');
	}, 100);
}

function closeTab() {
	$('.setting-panel').removeClass('move-in');
}

function toggleOption(el) {
	let name = el.data('name');
	let option = el.is(':checked') ? 1 : 0;
	$("input[name='"+name+"']").val(option);
	console.log(name+' : ' + $("input[name='"+name+"']").val());
}

$('#default-warehouse').select2();
$('#transform-warehouse').select2();
$('#lend-warehouse').select2();
$('#ix-warehouse').select2();
$('#ix-return-warehouse').select2();
$('#lnw-shop-warehouse').select2();

function updateConfig(formName) {
	load_in();
	var formData = $("#"+formName).serialize();
	$.ajax({
		url: BASE_URL + "setting/configs/update_config",
		type:"POST",
    cache:"false",
    data: formData,
		success: function(rs){
			load_out();
      rs = $.trim(rs);
      if(rs == 'success'){
        swal({
          title:'Updated',
          type:'success',
          timer:1000
        });
      }else{
        swal('Error!', rs, 'error');
      }
		}
	});
}


function openSystem() {
	$("#closed").val(0);
	$("#btn-close").removeClass('btn-danger');
	$('#btn-freze').removeClass('btn-warning');
	$("#btn-open").addClass('btn-success');
}


function closeSystem() {
	$("#closed").val(1);
	$("#btn-open").removeClass('btn-success');
	$('#btn-freze').removeClass('btn-warning');
	$("#btn-close").addClass('btn-danger');
}


function frezeSystem() {
	$("#closed").val(2);
	$("#btn-open").removeClass('btn-success');
	$("#btn-close").removeClass('btn-danger');
	$('#btn-freze').addClass('btn-warning');
}


$('#default-zone').autocomplete({
	source: BASE_URL + 'auto_complete/get_zone_code_and_name',
	autoFocus:true,
	close:function(){
		let rs = $(this).val();
		let arr = rs.split(' | ');

		if(arr[0] === 'not found'){
			$(this).val('');
		}else{
			$(this).val(arr[0]);
		}
	}
})

$('#ix-zone').autocomplete({
	source: BASE_URL + 'auto_complete/get_zone_code_and_name/',
	autoFocus:true,
	close:function() {
		let rs = $(this).val();
		let arr = rs.split(' | ');

		if(arr[0] === 'ไม่พบรายการ') {
			$(this).val('');
		}
		else {
			$(this).val(arr[0]);
		}
	}
})


$('#ix-return-zone').autocomplete({
	source: BASE_URL + 'auto_complete/get_zone_code_and_name/',
	autoFocus:true,
	close:function() {
		let rs = $(this).val();
		let arr = rs.split(' | ');

		if(arr[0] === 'ไม่พบรายการ') {
			$(this).val('');
		}
		else {
			$(this).val(arr[0]);
		}
	}
})


$('#web-tracking-date').datepicker({
	dateFormat:'yyy-mm-dd'
});

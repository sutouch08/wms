var HOME = BASE_URL + 'report/audit/outbound_document_qty_audit/';

window.addEventListener('load', () => {
	resizeDisplay();
})

window.addEventListener('resize', () => {
	resizeDisplay();
});

function resizeDisplay() {
	let height = $(window).height();
	let navHeight = 45;
  let headerRow = $('#header-row').height();
	let searchHeight = $('#search-row').height() + navHeight + headerRow;
	let pageContentHeight = height - (navHeight + 75);
	let billTableHeight = pageContentHeight - (searchHeight + 0);
	let minHeight = 300;

	billTableHeight = billTableHeight < minHeight ? minHeight : billTableHeight;

	$('.page-content').css('height', pageContentHeight + 'px');
	$('#result').css('height', billTableHeight + 'px');
}


function toggleAllDocument(option){
  $('#allDoc').val(option);
  if(option == 1){
    $('#btn-doc-all').addClass('btn-primary');
    $('#btn-doc-range').removeClass('btn-primary');
    $('#docFrom').val('');
    $('#docFrom').attr('disabled', 'disabled');
    $('#docTo').val('');
    $('#docTo').attr('disabled', 'disabled');
    return
  }

  if(option == 0){
    $('#btn-doc-all').removeClass('btn-primary');
    $('#btn-doc-range').addClass('btn-primary');
    $('#docFrom').removeAttr('disabled');
    $('#docTo').removeAttr('disabled');
    $('#docFrom').focus();
  }
}


function toggleAllRole(option) {
	$('#allRole').val(option);
	if(option == 1) {
		$('#btn-role-all').addClass('btn-primary');
		$('#btn-role-range').removeClass('btn-primary');
		$('.chk').prop('checked', false);
		return;
	}

	if(option == 0) {
		$('#btn-role-all').removeClass('btn-primary');
		$('#btn-role-range').addClass('btn-primary');
		$('#role-modal').modal('show');
		return;
	}
}

function toggleAllState(option) {
	$('#allState').val(option);
	if(option == 1) {
		$('#btn-state-all').addClass('btn-primary');
		$('#btn-state-range').removeClass('btn-primary');
		$('.state').prop('checked', false);
		return;
	}

	if(option == 0) {
		$('#btn-state-all').removeClass('btn-primary');
		$('#btn-state-range').addClass('btn-primary');
		$('#state-modal').modal('show');
		return;
	}
}



//--- Date picker
$('#fromDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd){
    $('#toDate').datepicker('option', 'minDate', sd);
  }
});


$('#toDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd){
    $('#fromDate').datepicker('option','maxDate', sd);
  }
});


function getReport() {
  var is_wms = $('#is_wms').val();
  var fromDate = $('#fromDate').val();
  var toDate = $('#toDate').val();

	if(!isDate(fromDate) || !isDate(toDate)) {
		$('#fromDate').addClass('has-error');
		$('#toDate').addClass('has-error');
		return false;
	}
	else {
		$('#fromDate').removeClass('has-error');
		$('#toDate').removeClass('has-error');
	}

  var allRole = $('#allRole').val();
	if(allRole == 0) {
		var count = $('.chk:checked').length;
		if(count == 0) {
			$('#role-modal').modal('show');
			return false;
		}
	}

	var allState = $('#allState').val();
	if(allState == 0) {
		var count = $('.state:checked').length;
		if(count == 0) {
			$('#state-modal').modal('show');
			return false;
		}
	}

	var channels = $('#channels').val();

  var data = [
    {'name' : 'is_wms', 'value' : is_wms},
    {'name' : 'fromDate', 'value' : fromDate},
    {'name' : 'toDate', 'value' : toDate},
    {'name' : 'allRole', 'value' : allRole},
		{'name' : 'allState', 'value' : allState},
		{'name' : 'channels', 'value' : channels}
  ];

	if(allRole == 0){
    $('.chk').each(function(index, el) {
      if($(this).is(':checked')){
        let names = 'role['+index+']';
        data.push({'name' : names, 'value' : $(this).val() });
      }
    });
  }

	if(allState == 0){
    $('.state').each(function(index, el) {
      if($(this).is(':checked')){
        let names = 'state['+index+']';
        data.push({'name' : names, 'value' : $(this).val() });
      }
    });
  }

  load_in();

  $.ajax({
    url:HOME + 'get_report',
    type:'GET',
    cache:'false',
    data:data,
    success:function(rs){
      load_out();
      var rs = $.trim(rs);
      if(isJson(rs)){
        var source = $('#template').html();
        var data = $.parseJSON(rs);
        var output = $('#rs');
        render(source,  data, output);
      }
			else {
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				})
			}
    }
  });

}


function doExport(){
  var is_wms = $('#is_wms').val();
	var fromDate = $('#fromDate').val();
  var toDate = $('#toDate').val();

	if(!isDate(fromDate) || !isDate(toDate)) {
		$('#fromDate').addClass('has-error');
		$('#toDate').addClass('has-error');
		return false;
	}
	else {
		$('#fromDate').removeClass('has-error');
		$('#toDate').removeClass('has-error');
	}

  var allRole = $('#allRole').val();
	if(allRole == 0) {
		var count = $('.chk:checked').length;
		if(count == 0) {
			$('#role-modal').modal('show');
			return false;
		}
	}

	var allState = $('#allState').val();
	if(allState == 0) {
		var count = $('.state:checked').length;
		if(count == 0) {
			$('#state-modal').modal('show');
			return false;
		}
	}

	var channels = $('#channels').val();

  var data = [
    {'name' : 'is_wms', 'value' : is_wms},
    {'name' : 'fromDate', 'value' : fromDate},
    {'name' : 'toDate', 'value' : toDate},
    {'name' : 'allRole', 'value' : allRole},
		{'name' : 'allState', 'value' : allState},
		{'name' : 'channels', 'value' : channels}
  ];

	if(allRole == 0){
    $('.chk').each(function(index, el) {
      if($(this).is(':checked')){
        let names = 'role['+index+']';
        data.push({'name' : names, 'value' : $(this).val() });
      }
    });
  }

	if(allState == 0){
    $('.state').each(function(index, el) {
      if($(this).is(':checked')){
        let names = 'state['+index+']';
        data.push({'name' : names, 'value' : $(this).val() });
      }
    });
  }

  var token = generateUID();
  $('#token').val(token);
  
  get_download(token);

  $('#reportForm').submit();

}

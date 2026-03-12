var click = 0;

function addNew(){
  window.location.href = BASE_URL + 'masters/products/add_new';
}


function goBack(){
  window.location.href = BASE_URL + 'masters/products';
}


function getEdit(code){
  url = BASE_URL + 'masters/products/edit/' + encodeURIComponent(code);
  window.location.href = url;
}


function add() {
  if(click === 0) {
    click = 1;

    clearErrorByClass('e');

    let h = {
      'code' : $('#code').val().trim(),
      'name' : $('#name').val().trim(),      
      'cost' : parseDefaultFloat($('#cost').val(), 0),
      'price' : parseDefaultFloat($('#price').val(), 0),
      'unit_code' : $('#unit-code').val(),
      'unit_id' : $('#unit-code option:selected').data('id'),
      'unit_group_id' : $('#unit-code option:selected').data('group'),
      'has_batch' : $('#has-batch').val(),
      'item_group' : $('#item-group').val(),
      'main_group_code' : $('#main-group').val(),
      'group_code' : $('#group').val(),
      'sub_group_code' : $('#sub-group').val(),
      'category_code' : $('#category').val(),
      'kind_code' : $('#kind').val(),
      'type_code' : $('#type').val(),
      'brand_code' : $('#brand').val(),
      'collection_code' : $('#collection').val(),
      'year' : $('#year').val(),
      'api_rate' : parseDefaultFloat($('#api-rate').val(), 0),
      'count_stock' : $('#count-stock').is(':checked') ? 1 : 0,
      'is_api' : $('#is-api').is(':checked') ? 1 : 0,
      'can_sell' : $('#can-sell').is(':checked') ? 1 : 0,
      'active' : $('#active').is(':checked') ? 1 : 0
    };

    let valid = $('#valid').val();

    if(h.code.length === 0) {
      $('#code').hasError('Required');
      click = 0;
      return false;
    }

    if(h.name.length === 0) {
      $('#name').hasError('Required');
      click = 0;
      return false;
    }

    if(h.unit_code == "") {
      $('#unit-code').hasError('Required');
      click = 0;
      return false;
    }

    if(h.item_group == "") {
      $('#item-group').hasError('Required');
      click = 0;
      return false;
    }

    if(h.main_group_code == "") {
      $('#main-group').hasError('Required');
      click = 0;
      return false;
    }

    if(h.group_code == "") {
      $('#group').hasError('Required');
      click = 0;
      return false;
    }

    if(h.category_code == "") {
      $('#category').hasError('Required');
      click = 0;
      return false;
    }

    if(h.kind_code == '') {
      $('#kind').hasError('Required');
      click = 0;
      return false;
    }

    if(h.type_code == '') {
      $('#type').hasError('Required');
      click = 0;
      return false;
    }

    if(valid != 1) {
      click = 0;
      return false;
    }

    load_in();

    $.ajax({
      url:BASE_URL + 'masters/products/add_style',
      type:'POST',
      cache:false,
      data:{
        'data' : JSON.stringify(h)
      },
      success:function(rs) {
        click = 0;
        load_out();

        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status === 'success') {
            getEdit(ds.id);
          }
          else {
            showError(ds.message);
          }
        }
        else {
          showError(rs);
        }
      },
      error:function(rs) {
        click = 0;
        showError(rs);
      }
    })
  }
}


function update() {
  if(click === 0) {
    click = 1;

    clearErrorByClass('e');

    let h = {
      'id' : $('#id').val(),
      'code' : $('#code').val().trim(),
      'name' : $('#name').val().trim(),      
      'cost' : parseDefaultFloat($('#cost').val(), 0),
      'price' : parseDefaultFloat($('#price').val(), 0),
      'unit_code' : $('#unit-code').val(),
      'unit_id' : $('#unit-code option:selected').data('id'),
      'unit_group_id' : $('#unit-code option:selected').data('group'),
      'has_batch' : $('#has-batch').val(),
      'item_group' : $('#item-group').val(),
      'main_group_code' : $('#main-group').val(),
      'group_code' : $('#group').val(),
      'sub_group_code' : $('#sub-group').val(),
      'category_code' : $('#category').val(),
      'kind_code' : $('#kind').val(),
      'type_code' : $('#type').val(),
      'brand_code' : $('#brand').val(),
      'collection_code' : $('#collection').val(),
      'year' : $('#year').val(),
      'api_rate' : parseDefaultFloat($('#api-rate').val(), 0),
      'count_stock' : $('#count-stock').is(':checked') ? 1 : 0,
      'is_api' : $('#is-api').is(':checked') ? 1 : 0,
      'can_sell' : $('#can-sell').is(':checked') ? 1 : 0,
      'active' : $('#active').is(':checked') ? 1 : 0,
      'cost_update' : $('#cost-update').is(':checked') ? 1 : 0,
      'price_update' : $('#price-update').is(':checked') ? 1 : 0
    };
    
    if(h.code.length === 0) {
      $('#code').hasError('Required');
      click = 0;
      return false;
    }

    if(h.name.length === 0) {
      $('#name').hasError('Required');
      click = 0;
      return false;
    }

    if(h.unit_code == "") {
      $('#unit-code').hasError('Required');
      click = 0;
      return false;
    }

    if(h.item_group == "") {
      $('#item-group').hasError('Required');
      click = 0;
      return false;
    }

    if(h.main_group_code == "") {
      $('#main-group').hasError('Required');
      click = 0;
      return false;
    }

    if(h.group_code == "") {
      $('#group').hasError('Required');
      click = 0;
      return false;
    }

    if(h.category_code == "") {
      $('#category').hasError('Required');
      click = 0;
      return false;
    }

    if(h.kind_code == '') {
      $('#kind').hasError('Required');
      click = 0;
      return false;
    }

    if(h.type_code == '') {
      $('#type').hasError('Required');
      click = 0;
      return false;
    }
    
    load_in();

    $.ajax({
      url:BASE_URL + 'masters/products/update_style',
      type:'POST',
      cache:false,
      data:{
        'data' : JSON.stringify(h)
      },
      success:function(rs) {
        click = 0;
        load_out();

        if(rs.trim() === 'success') {
          swal({
            title:'Success',
            type:'success',
            timer:1000
          });
        }
        else {
          showError(rs);
        }        
      },
      error:function(rs) {
        click = 0;
        showError(rs);
      }
    })
  }
}


function changeURL(style, tab, a)
{
	var url = BASE_URL + 'masters/products/edit/' + style + '/' + tab;
	var stObj = { stage: 'stage' };
	window.history.pushState(stObj, 'products', url);
  if( a !== undefined )
  {
    $('#'+tab+'-a').click();
  }
}


function toggleTab(tabName) {
  $('.tab-pane').removeClass('in');
  $('.tab-pane').removeClass('active');

  $('#'+tabName).addClass('active');
  $('#'+tabName).addClass('in');
}


$('#api-rate').change(function() {
  let rate = parseDefault(parseFloat($(this).val()), 0);

  if(rate < 0) {
    rate = 0;
  }

  if(rate > 100) {
    rate = 100;
  }

  $(this).val(rate.toFixed(2));
})


function newItems(){  
  let id = $('#id').val();
  window.location.href = BASE_URL + 'masters/products/item_gen/' + id;
}


function clearFilter(){
  var url = BASE_URL + 'masters/products/clear_filter';
  var page = BASE_URL + 'masters/products';
  $.get(url, function(rs){
    window.location.href = page;
  });
}


function export_filter(){
  let code = $('#code').val();
  let name = $('#name').val();
  let group = $('#group').val();
  let main_group = $('#main_group').val();
  let sub_group = $('#sub_group').val();
  let category = $('#category').val();
  let kind = $('#kind').val();
  let type = $('#type').val();
  let brand = $('#brand').val();
  let collection = $('#collection').val();
  let year = $('#year').val();
  let sell = $('#sell').val();
  let active = $('#active').val();
  let token	= new Date().getTime();

  $('#export_code').val(code);
  $('#export_name').val(name);
  $('#export_group').val(group);
  $('#export_main_group').val(main_group);
  $('#export_sub_group').val(sub_group);
  $('#export_category').val(category);
  $('#export_kind').val(kind);
  $('#export_type').val(type);
  $('#export_brand').val(brand);
  $('#export_collection').val(collection);
  $('#export_year').val(year);
  $('#export_sell').val(sell);
  $('#export_active').val(active);
  $('#token').val(token);

  get_download(token);

  $('#export_filter_form').submit();
}


function getDelete(code, name, no){
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
      url: BASE_URL + 'masters/products/delete_style/' + encodeURIComponent(code),
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

          $('#row-' + no).remove();
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


function getSearch(){
  $('#searchForm').submit();
}


function doExport(code){
  load_in();
  $.ajax({
    url:BASE_URL + 'masters/products/export_products/'+code,
    type:'POST',
    cache:false,
    success:function(rs){
      load_out();
      if(rs === 'success'){
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });
      }else{
        swal({
          title:'Error',
          text:rs,
          type:'error'
        });
      }
    }
  })
}


function sendToSap(code){
  load_in();
  $.ajax({
    url:BASE_URL + 'masters/products/export_products/'+code,
    type:'POST',
    cache:false,
    success:function(rs){
      load_out();
      if(rs === 'success'){
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });
      }else{
        swal({
          title:'Error',
          text:rs,
          type:'error'
        });
      }
    }
  })
}


function sendToWms(code) {
	load_in();
	$.ajax({
		url:BASE_URL + 'masters/products/send_to_wms',
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
					title:'Error!',
					text:rs,
					type:'error'
				})
			}
		}
	})
}


function sendToSoko(code) {
	load_in();
	$.ajax({
		url:BASE_URL + 'masters/products/send_to_soko',
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
					title:'Error!',
					text:rs,
					type:'error'
				})
			}
		}
	})
}

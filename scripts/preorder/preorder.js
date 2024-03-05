var HOME = BASE_URL + 'orders/pre_order_policy/';

function goBack() {
  window.location.href = HOME;
}


function addNew() {
  window.location.href = HOME + 'add_new';
}


function edit(id) {
  window.location.href = HOME + 'edit/'+id;
}


function getSearch() {
  $('#search-form').submit();
}


function clearFilter() {
  $.get(HOME + 'clear_filter', () => {
    goBack();
  })
}


$('#start_date').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#end_date').datepicker('option', 'minDate', sd);
  }
});


$('#end_date').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
     $('#start_date').datepicker('option', 'maxDate', sd);
  }
});


function add() {
  $('.rq').removeClass('has-error');
  let name = $('#name').val();
  let status = $('#status').val();
  let startDate = $('#start_date').val();
  let endDate = $('#end_date').val();

  if(name.length == 0) {
    $('#name').addClass('has-error');
    return false;
  }

  if( ! isDate(startDate)) {
    $('#start_date').addClass('has-error');
    return false;
  }

  if( ! isDate(endDate)) {
    $('#end_date').addClass('has-error');
    return false;
  }

  $.ajax({
    url:HOME + 'add',
    type:'POST',
    cache:false,
    data: {
      'name' : name,
      'status' : status,
      'start_date' : startDate,
      'end_date' : endDate
    },
    success:function(rs) {
      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status == 'success') {
          edit(ds.id);
        }
        else {
          swal({
            title:'Error!',
            text:ds.message,
            type:'error'
          });
        }
      }
      else {
        swal({
          title:'Error!',
          text:rs,
          type:'error',
          html:true
        })
      }
    }
  })
}


function update() {
  $('.rq').removeClass('has-error');
  let id = $('#id').val();
  let name = $('#name').val();
  let status = $('#status').val();
  let startDate = $('#start_date').val();
  let endDate = $('#end_date').val();

  if(name.length == 0) {
    $('#name').addClass('has-error');
    return false;
  }

  if( ! isDate(startDate)) {
    $('#start_date').addClass('has-error');
    return false;
  }

  if( ! isDate(endDate)) {
    $('#end_date').addClass('has-error');
    return false;
  }

  $.ajax({
    url:HOME + 'update',
    type:'POST',
    cache:false,
    data: {
      'id' : id,
      'name' : name,
      'status' : status,
      'start_date' : startDate,
      'end_date' : endDate
    },
    success:function(rs) {
      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status == 'success') {
          swal({
            title:'Success',
            type:'success',
            timer:1000
          });
        }
        else {
          swal({
            title:'Error!',
            text:ds.message,
            type:'error'
          });
        }
      }
      else {
        swal({
          title:'Error!',
          text:rs,
          type:'error',
          html:true
        })
      }
    }
  })
}


function viewDetail(id) {
  window.location.href = HOME + 'view_detail/'+id;
}


$("#model-code").autocomplete({
	source: BASE_URL + 'auto_complete/get_style_code',
	autoFocus: true,
  close:function(){
    var rs = $(this).val();
    var arr = rs.split(' | ');
    $(this).val(arr[0]);
  }
});


$('#model-code').keyup(function(event) {
	if(event.keyCode == 13){
		var code = $(this).val();
		if(code.length > 0){
			setTimeout(function(){
				addByModel();
			}, 300);
		}
	}
});



$('#item-code').autocomplete({
	source:BASE_URL + 'auto_complete/get_product_code',
	minLength: 2,
	autoFocus:true,
  close:function() {
    var rs = $(this).val();
    var arr = rs.split(' | ');
    $(this).val(arr[0]);
  }
});

$('#item-code').keyup(function(e){
	if(e.keyCode == 13){
		var code = $(this).val();
		if(code.length > 4){
			setTimeout(function(){
				addItem();
			}, 200);
		}
	}
});


function addItem() {
  let code = $('#item-code').val();
  let id = $('#id').val();

  if(code.length) {
    load_in();
    $.ajax({
      url:HOME + 'add_item',
      type:'POST',
      cache:false,
      data: {
        'id' : id,
        'product_code' : code
      },
      success:function(rs) {
        load_out();

        if( isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status == 'success') {
            let source = $('#item-template').html();
            let output = $('#result-table');

            render_prepend(source, ds.row, output);
            reIndex();
            $('#item-code').val('');
            $('#item-code').focus();
          }
          else {
            swal({
              title:'Error!',
              text:ds.message,
              type:'error'
            });
          }
        }
        else {
          swal({
            title:'Error!',
            text:rs,
            type:'error',
            html:true
          })
        }
      }
    })
  }
}



function addByModel() {
  let styleCode = $('#model-code').val();
  let id = $('#id').val();

  if(styleCode.length) {
    load_in();
    $.ajax({
      url:HOME + 'add_style',
      type:'POST',
      cache:false,
      data:{
        'id' : id,
        'style_code' : styleCode
      },
      success:function(rs) {
        load_out();
        if(isJson(rs)) {
          let ds = JSON.parse(rs);
          if(ds.status == 'success') {
            swal({
              title:'Success',
              type:'success',
              timer:1000
            });

            let source = $('#style-template').html();
            let output = $('#result-table');

            render_prepend(source, ds.rows, output);

            reIndex();
            $('#model-code').val('');
            $('#model-code').focus();
          }
          else {
            swal({
              title:'Error!',
              text:ds.message,
              type:'error'
            });
          }
        }
        else {
          swal({
            title:'Error!',
            text:rs,
            type:'error',
            html:true
          })
        }
      }
    })
  }
}



$('#chk-all').change(function() {
  if($(this).is(":checked")) {
    $('.chk').prop('checked', true);
  }
  else {
    $('.chk').prop('checked', false);
  }
})


function removeChecked() {
  if($('.chk:checked').length) {
    swal({
      title:"Are you sure ?",
      text:"Do you really want to delete checked items ?",
      type:'warning',
      showCancelButton:true,
      confirmButtonColor:'#d15b47',
      confirmButtonText:'Yes',
      cancelButtonText:'No',
      closeOnConfirm:true
    }, function() {
      let ids = [];

      $('.chk:checked').each(function() {
        let id = $(this).val();
        ids.push(id);
      });

      if(ids.length > 0) {
        load_in();

        $.ajax({
          url:HOME + 'remove_items',
          type:'POST',
          cache:false,
          data: {
            'ids' : ids
          },
          success:function(rs) {
            load_out();

            if(rs === 'success') {
              swal({
                title:'Success',
                type:'success',
                timer:1000
              });

              ids.forEach((id) => {
                $('#row-'+id).remove();
              })

              reIndex();
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
      else {
        swal({
          title:'Error!',
          text:"Please select items",
          type:"error"
        })
      }
    })
  }
}


function getDelete(id, code) {
  swal({
    title:"Are you sure ?",
    text:"Do you really want to delete "+code+" ?",
    type:'warning',
    showCancelButton:true,
    confirmButtonColor:'#d15b47',
    confirmButtonText:'Yes',
    cancelButtonText:'No',
    closeOnConfirm:true
  },
  function() {
    setTimeout(() => {
      load_in();

      $.ajax({
        url: HOME + 'delete_policy',
        type:'POST',
        cache:false,
        data: {
          'id' : id
        },
        success:function(rs) {
          load_out();

          if(rs == 'success') {
            swal({
              title:'Success',
              type:'success',
              timer:1000
            });

            $('#row-'+id).remove();
            reIndex();
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
    }, 200);
  });
}

var HOME = BASE_URL + 'orders/reserv_stock/';

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
  $('#searchForm').submit();
}


function clearFilter() {
  $.get(HOME + 'clear_filter', () => {
    goBack();
  })
}


$('#fromDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#toDate').datepicker('option', 'minDate', sd);
  }
});


$('#toDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#fromDate').datepicker('option', 'maxDate', sd);
  }
});


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


function save() {
  let id = $('#id').val();

  $.ajax({
    url:HOME + 'save',
    type:'POST',
    cache:false,
    data:{
      'id' : id
    },
    success:function(rs) {
      if(rs.trim() === 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        setTimeout(() => {
          viewDetail(id);
        },1200)
      }
      else {
        beep();
        showError(rs);
      }
    },
    error:function(rs) {
      beep();
      showError(rs);
    }
  })
}


function approve() {
  let id = $('#id').val();

  $.ajax({
    url:HOME + 'approve',
    type:'POST',
    cache:false,
    data:{
      'id' : id
    },
    success:function(rs) {
      if(rs.trim() === 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        setTimeout(() => {
          viewDetail(id);
        },1200)
      }
      else {
        beep();
        showError(rs);
      }
    },
    error:function(rs) {
      beep();
      showError(rs);
    }
  })
}


function rejected() {
  let id = $('#id').val();

  $.ajax({
    url:HOME + 'rejected',
    type:'POST',
    cache:false,
    data:{
      'id' : id
    },
    success:function(rs) {
      if(rs.trim() === 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        setTimeout(() => {
          viewDetail(id);
        },1200)
      }
      else {
        beep();
        showError(rs);
      }
    },
    error:function(rs) {
      beep();
      showError(rs);
    }
  })
}


function add() {
  clearErrorByClass('rq');

  let h = {
    'warehouse_code' : $('#warehouse').val(),
    'name' : $('#name').val().trim(),
    'active' : $('#active').val(),
    'start_date' : $('#start_date').val(),
    'end_date' : $('#end_date').val()
  }

  if(h.warehouse_code == "") {
    $('#warehouse').hasError();
    return false;
  }

  if(h.name.length == 0) {
    $('#name').hasError();
    return false;
  }

  if( ! isDate(h.start_date)) {
    $('#start_date').hasError();
    return false;
  }

  if( ! isDate(h.end_date)) {
    $('#end_date').hasError();
    return false;
  }

  $.ajax({
    url:HOME + 'add',
    type:'POST',
    cache:false,
    data: {
      'data' : JSON.stringify(h)
    },
    success:function(rs) {
      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status == 'success') {
          edit(ds.id);
        }
        else {
          beep();
          showError(ds.message);
        }
      }
      else {
        beep();
        showError(rs);
      }
    },
    error:function(rs) {
      beep();
      showError(rs);
    }
  })
}


function update() {
  clearErrorByClass('rq');

  let h = {
    'id' : $('#id').val(),
    'warehouse_code' : $('#warehouse').val(),
    'name' : $('#name').val().trim(),
    'active' : $('#active').val(),
    'start_date' : $('#start_date').val(),
    'end_date' : $('#end_date').val()
  }

  if(h.warehouse_code == "") {
    $('#warehouse').hasError();
    return false;
  }

  if(h.name.length == 0) {
    $('#name').hasError();
    return false;
  }

  if( ! isDate(h.start_date)) {
    $('#start_date').hasError();
    return false;
  }

  if( ! isDate(h.end_date)) {
    $('#end_date').hasError();
    return false;
  }

  $.ajax({
    url:HOME + 'update',
    type:'POST',
    cache:false,
    data: {
      'data' : JSON.stringify(h)
    },
    success:function(rs) {
      if(rs.trim() === 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        setTimeout(() => {
          refresh();
        }, 1200)
      }
      else {
        beep();
        showError(rs);
      }
    },
    error:function(rs) {
      beep();
      showError(rs);
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
	source:BASE_URL + 'auto_complete/get_product_code_and_name',
	minLength: 2,
	autoFocus:true,
  close:function() {
    let arr = $(this).val().split(' | ');

    if(arr.length == 2) {
      $('#item-code').val(arr[0]);
      $('#item-name').val(arr[1]);

      setTimeout(() => {
        $('#item-qty').val(1).focus().select();
      }, 100);
    }
    else {
      $('#item-code').val('');
      $('#item-name').val('');
    }
  }
});


$('#item-code').keyup(function(e){
	if(e.keyCode == 13){
		var code = $(this).val();
		if(code.length > 4){
			setTimeout(function(){
				$('#item-qty').focus().select();
			}, 200);
		}
	}
});


$('#item-qty').keyup(function(e) {
  if(e.keyCode == 13) {
    addItem();
  }
})


function addItem() {
  clearErrorByClass('item-control');

  let h = {
    'id' : $('#id').val(),
    'code' : $('#code').val(),
    'product_code' : $('#item-code').val().trim(),
    'product_name' : $('#item-name').val().trim(),
    'qty' : parseDefault(parseFloat($('#item-qty').val()), 0)
  }

  if(h.product_code.length == 0) {
    $('#item-code').hasError();
    return false;
  }

  if(h.product_name.length == 0) {
    $('#item-name').hasError();
    return false;
  }

  if(h.qty == 0) {
    $('#item-qty').hasError();
    return false;
  }

  load_in();

  $.ajax({
    url:HOME + 'add_item',
    type:'POST',
    cache:false,
    data:{
      'data' : JSON.stringify(h)
    },
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          $('#row-'+ds.id).remove();

          let source = $('#item-template').html();
          let output = $('#result-table');

          render_prepend(source, ds.data, output);

          reIndex('no');

          $('#item-qty').val(1);
          $('#item-name').val('');
          $('#item-code').val('').focus();
        }
        else {
          beep();
          showError(ds.message);
        }
      }
      else {
        beep();
        showError(rs);
      }
    },
    error:function(rs) {
      beep();
      showError(rs);
    }
  });
}


function getProductGrid(){
	var pdCode 	= $("#model-code").val();
	if( pdCode.length > 0  ){
		load_in();
		$.ajax({
			url: HOME + 'get_product_grid/'+pdCode,
			type:"GET",
			cache:"false",
			data:{
				"style_code" : pdCode
			},
			success: function(rs){
				load_out();

        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status === 'success') {
            $('#modal').css("width", ds.width + "px");
            $('#modalTitle').html(pdCode);
            $('#modalBody').html(ds.data);
            $('#itemGrid').modal('show');
          }
          else {
            beep();
            showError(ds.message);
          }
        }
        else {
          beep();
          showError(rs);
        }
			},
      error:function(rs) {
        beep();
        showError(rs);
      }
		});
	}
}


function addItems() {
  let h = {
    'id' : $('#id').val(),
    'code' : $('#code').val(),
    'items' : []
  }

  $('.item-grid').each(function() {
    let el = $(this);
    let qty = parseDefault(parseFloat(el.val()), 0);

    if(qty > 0) {
      let row = {
        'product_code' : el.data('code'),
        'product_name' : el.data('name'),
        'qty' : qty
      }

      h.items.push(row);
    }
  })

  if(h.items.length == 0) {
    return false;;
  }

  $('#itemGrid').modal('hide');

  load_in();

  $.ajax({
    url:HOME + 'add_items',
    type:'POST',
    cache:false,
    data:{
      'data' : JSON.stringify(h)
    },
    success:function(rs) {
      load_out();

      if(rs.trim() === 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        setTimeout(() => {
          refresh();
        }, 1200);
      }
      else {
        beep();
        showError(rs);
      }
    },
    error:function(rs) {
      beep();
      showError(rs);
    }
  })
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
            'id' : $('#id').val(),
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
    text:"Do you really want to cancle "+code+" ?",
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
        url: HOME + 'cancel',
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

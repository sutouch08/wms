function goBack(){
  window.location.href = `${HOME}`;
}

function getEdit(code){
  window.location.href = `${HOME}edit/${code}`;
}

function toggleOption(el) {
  let name = el.data('name');
  let option = el.is(':checked') ? 1 : 0;
  $("input[name='" + name + "']").val(option);
  console.log(name + ' : ' + $("input[name='" + name + "']").val());
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
      url: `${HOME}delete/${code}`,
      type:'GET',
      cache:false,
      success:function(rs){
        if(rs === 'success'){
          swal({
            title:'Deleted',
            text:'ลบคลัง '+code+' เรียบร้อยแล้ว',
            type:'success',
            timer:1000
          });
          $('#row-'+code).remove();
          reIndex();
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

function syncData(){
  load_in();
  $.get(`${HOME}syncData`, function(){
    load_out();
    swal({
      title:'Completed',
      type:'success',
      timer:1000
    });
    setTimeout(function(){
      goBack();
    }, 1500);
  });
}


function syncAllData() {
  load_in();

  $.get(`${HOME}syncAllData`, function() {
    load_out();
    swal({
      title:'Completed',
      type:'success',
      timer:1000
    });

    setTimeout(function() {
      goBack();
    }, 1500);
  });
}

function exportFilter(){
  let code = $('#code').val();
  let role = $('#role').val();
  let is_consignment = $('#is_consignment').val();
  let sell = $('#sell').val();
  let prepare = $('#prepare').val();
  let lend = $('#lend').val();
  let active = $('#active').val();
  let auz = $('#auz').val();
  let is_pos = $('#is_pos').val();

  $('#export-code').val(code);  
  $('#export-role').val(role);
  $('#export-is-consignment').val(is_consignment);
  $('#export-sell').val(sell);
  $('#export-prepare').val(prepare);
  $('#export-lend').val(lend);
  $('#export-active').val(active);
  $('#export-auz').val(auz);
  $('#export-is-pos').val(is_pos);


  var token = $('#token').val();
  get_download(token);
  $('#exportForm').submit();
}


$('#default-zone-code').autocomplete({
  source: `${HOME}get_zone_code_and_name/${$('#code').val()}`,
  autoFocus: true,
  close: function() {
    let arr = $(this).val().trim().split(' | ');
    
    if(arr.length === 2) {
      $('#default-zone-code').val(arr[0]);
      $('#default-zone-name').val(arr[1]);
    } 
    else {
      $('#default-zone-code').val('');
      $('#default-zone-name').val('');
    }
  }
});

function update() {
  $('#role').clearError();
  $('#default-zone-code').clearError();

  let h = {
    'code' : $('#code').val(),
    'role' : $('#role').val(),
    'default_zone' : $('#default-zone-code').val().trim(),
    'is_consignment' : $('#is-consignment').val(),
    'sell' : $('#sell').val(),
    'prepare' : $('#prepare').val(),
    'lend' : $('#lend').val(),    
    'auz' : $('#auz').val(),
    'is_pos' : $('#is-pos').val(),
    'auto_shipping' : $('#auto-shipping').val()
  };

  if(h.role === '') {
    $('#role').hasError();
    return false;
  }

  if(h.auto_shipping == 1 && h.default_zone === '') {
    $('#default-zone-code').hasError();
    swal({
      title:'Required',
      text: 'Default zone is required when auto shipping is enabled.',
      type: 'error'
    });

    return false;
  }

  load_in();
  
  $.ajax({
    url: `${HOME}update`,
    type:'POST',
    cache:false,
    data: {
      'data' : JSON.stringify(h)
    },
    success:function(rs) {
      load_out();

      if(rs.trim() === 'success') {
        swal({
          title: 'Success',
          type: 'success',
          timer: 1000
        });
      }
      else {
        showError(rs);
      }
    },
    error:function(rs) {
      showError(rs);
    }
  });
}
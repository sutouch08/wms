var HOME = BASE_URL + 'inventory/invoice/';

function goBack(){
  window.location.href = HOME;
}


function viewDetail(code) {
  let viewPort = window.innerWidth;
  let width = 1250;
  let height = 800;
  let center = (viewPort - width) / 2;
  let top = 100;
  let target = HOME + 'view_detail/'+code+'?nomenu';

  window.open(target, "_blank", `width=${width}, height=${height}, left=${center}, top=${top}, scrollbars=yes`);
}


function doExport(){
  var code = $('#order_code').val();
  load_in();
  $.ajax({
    url:BASE_URL + 'inventory/delivery_order/manual_export/'+code,
    type:'POST',
    cache:false,
    success:function(rs){
      load_out();
      var rs = $.trim(rs);
      if(rs == 'success'){
        swal({
          title:'Success',
          text:'Export success',
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


function do_export(code){
  $.ajax({
    url:BASE_URL + 'inventory/delivery_order/manual_export/'+code,
    type:'POST',
    cache:false,
    success:function(rs){
      var rs = $.trim(rs);
      if(rs == 'success'){
        swal({
          title:'Success',
          text:'Export success',
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

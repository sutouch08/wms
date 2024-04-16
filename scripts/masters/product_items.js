function editItem(code){
  $('#old-lbl-'+code).addClass('hide');
  $('#old-'+code).removeClass('hide');
  $('#bc-lbl-'+code).addClass('hide');
  $('#bc-'+code).removeClass('hide');
  $('#cost-lbl-'+code).addClass('hide');
  $('#cost-'+code).removeClass('hide');
  $('#price-lbl-'+code).addClass('hide');
  $('#price-'+code).removeClass('hide');
  $('#btn-edit-'+code).addClass('hide');
  $('#btn-update-'+code).removeClass('hide');

}



function updateItem(id)
{
  var code = $('#code-'+id).val();
  var oldCode = $('#old-'+id).val();
  var barcode = $('#bc-'+id).val();
  var cost = $('#cost-'+id).val();
  var price = $('#price-'+id).val();

  if( $('.has-error').length ){
    swal({
      title:'Error!',
      text:'พบข้อผิดพลาด กรุณาแก้ไข',
      type:'error'
    });

    return false;
  }


  $.ajax({
    url: BASE_URL + 'masters/products/update_item',
    type:'POST',
    cache:false,
    data:{
      'id' : id,
      'code' : code,
      'old_code' : oldCode,
      'barcode' : barcode,
      'cost' : cost,
      'price' : price
    },
    success:function(rs){
      var rs = $.trim(rs);
      if(rs === 'success'){
        $('#old-lbl-'+id).text(oldCode);
        $('#old-'+id).addClass('hide');
        $('#old-lbl-'+id).removeClass('hide');
        $('#bc-lbl-'+id).text(barcode);
        $('#bc-'+id).addClass('hide');
        $('#bc-lbl-'+id).removeClass('hide');
        $('#cost-lbl-'+id).text(cost);
        $('#cost-'+id).addClass('hide');
        $('#cost-lbl-'+id).removeClass('hide');
        $('#price-lbl-'+id).text(price);
        $('#price-'+id).addClass('hide');
        $('#price-lbl-'+id).removeClass('hide');
        $('#btn-update-'+id).addClass('hide');
        $('#btn-edit-'+id).removeClass('hide');
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



$('.barcode').focusout(function(){
  let bc = $(this).val();
  if(bc.length > 0){
    let id = $(this).data('id');
    checkBarcode(bc, id);
  }
});



function checkBarcode(barcode, id)
{
  var el = $('#bc-'+id);
  $.ajax({
    url: BASE_URL + 'masters/product_barcode/valid_barcode/',
    type:'POST',
    cache:false,
    data:{
      'barcode' : barcode,
      'id' : id
    },
    success:function(rs){
      if(rs === 'exists'){
      el.addClass('has-error');
      el.attr('data-original-title', 'บาร์โค้ดซ้ำ');
      el.tooltip();
      }else{
        el.removeClass('has-error');
        el.attr('data-original-title', '');
      }
    }
  })
}



function setImages()
{
	var style	= $("#style").val();
	load_in();
	$.ajax({
		url: BASE_URL + 'masters/products/get_image_items',
		type:"POST",
    cache:"false",
    data:{
      'style_code' : style
    },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'noimage' ){
				swal('ไม่พบรูปภาพ หรือ รายการสินค้า');
			}else{
				$("#mappingBody").html(rs);
				$("#imageMappingTable").modal('show');
			}
		}
	});
}


function doMapping() {
  let items = [];

  $('.chk-items:checked').each(function() {
    let item = {
      'product_code' : $(this).data('item'),
      'image_id' : $(this).data('imageid')
    }

    items.push(item);
  })

  if(items.length > 0) {
    $('#imageMappingTable').modal('hide');

    load_in();

    $.ajax({
      url:BASE_URL + 'masters/products/mapping_image/',
      type:'POST',
      cache:false,
      data:{        
        'items' : JSON.stringify(items)
      },
      success:function(rs) {
        load_out();

        if(rs === 'success') {
          swal({
            title:'Success',
            type:'success',
            timer:1000
          });

          setTimeout(() => {
            window.location.reload();
          }, 1200);
        }
        else {
          swal({
            title:'Error!',
            text:rs,
            type:'error'
          }, function() {
            $('#imageMappingTable').modal('show');
          })
        }
      }
    })
  }
}


function checkOldCode(style_code, old_style){
  if(old_style != ""){
    confirmGenOldCode(style_code, old_style);
  }else{
    swal({
      title:"ไม่พบรหัสรุ่นเก่า !!",
      text:"กรุณาระบุรหัสรุ่นเก่าในแถบข้อมูลแล้วบันทึกก่อนใช้งานปุ่มนี้",
      type:"warning"
    });
  }
}


function confirmGenOldCode(style_code){
  swal({
    title:'สร้างรหัสเก่า',
    text:'รหัส(เก่า)เดิม จะถูกแทนที่ด้วยรหัส(เก่า)ที่ถูกสร้างใหม่ ต้องการดำเนินการต่อหรือไม่ ?',
    type:'warning',
    showCancelButton: true,
		confirmButtonColor: '#FA5858',
		confirmButtonText: 'ใช่, ฉันต้องการ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
  },function(){
    $.ajax({
      url: BASE_URL + 'masters/products/generate_old_code_item',
      type:'POST',
      cache:false,
      data:{
        'style_code' : style_code
      },
      success:function(rs){
        if(rs === 'success'){
          swal({
            title:'Success',
            text:'สร้างรหัส(เก่า)เรียบร้อยแล้ว',
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
    })
  })
}



function setBarcodeForm(){
  if($('.cost').length){
    $('#barcodeOption').modal('show');
  }

}


function startGenerate(){
  $('#barcodeOption').modal('hide');
  var style = $('#style').val();
  var barcodeType = $("input[name='barcodeType']:checked").val();
  load_in();
  $.ajax({
    url: BASE_URL + 'masters/products/generate_barcode',
    type:'POST',
    cache:false,
    data:{
      'style' : style,
      'barcodeType' : barcodeType
    },
    success:function(rs){
      load_out();
      if(rs === 'success'){
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        setTimeout(function(){
          window.location.reload();
        }, 1200);

      }else{
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        });
      }
    }
  })
}


//--- toggle can sell

$('.can-sell').click(function(){
  var el = $(this);
  var code = el.data("code");
  var url = BASE_URL + 'masters/products/toggle_can_sell/'+code;
  $.get(url, function(rs){
    if(rs == 1){
      el.html('<i class="fa fa-check green"></i>');

    }else if(rs == 0){
      el.html('<i class="fa fa-times red"></i>');

    }else{
      swal({
        title:'Error!',
        text: 'เปลี่ยนสถานะไม่สำเร็จ',
        type:'error'
      });
    }
  });
});


//--- toggle active
$('.act').click(function(){
  var el = $(this);
  var code = el.data("code");
  var url = BASE_URL + 'masters/products/toggle_active/'+code;
  $.get(url, function(rs){
    if(rs == 1){
      el.html('<i class="fa fa-check green"></i>');

    }else if(rs == 0){
      el.html('<i class="fa fa-times red"></i>');

    }else{
      swal({
        title:'Error!',
        text: 'เปลี่ยนสถานะไม่สำเร็จ',
        type:'error'
      });
    }
  });
});



//--- toggle active
$('.api').click(function(){
  var el = $(this);
  var code = el.data("code");
  var url = BASE_URL + 'masters/products/toggle_api/'+code;
  $.get(url, function(rs){
    if(rs == 1){
      el.html('<i class="fa fa-check green"></i>');

    }else if(rs == 0){
      el.html('<i class="fa fa-times red"></i>');

    }else{
      swal({
        title:'Error!',
        text: 'เปลี่ยนสถานะไม่สำเร็จ',
        type:'error'
      });
    }
  });
});



function deleteItem(id, code){
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
      url: BASE_URL + 'masters/products/delete_item',
      type:'POST',
      cache:false,
      data:{
        'code' : code
      },
      success:function(rs){
        if(rs === 'success'){
          swal({
            title:'Deleted',
            text:'ลบรายการสินค้าเรียบร้อยแล้ว',
            type:'success',
            timer:1000
          });

          $('#row-'+item).remove();
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

function downloadBarcode(code)
{
	var token	= new Date().getTime();
	get_download(token);
	window.location.href = BASE_URL + 'masters/products/export_barcode/'+code+'/'+token;
}


function sendToWeb(code)
{
  load_in();
  $.ajax({
    url:BASE_URL + 'masters/products/export_products_to_web',
    type:'POST',
    cache:false,
    data:{
      'style_code' : code
    },
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

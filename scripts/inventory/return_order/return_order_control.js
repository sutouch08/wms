window.addEventListener('load', () => {
  invoice_init();
  zone_init();
});


$('#order-code').keyup(function(e) {
  if(e.keyCode == 13) {
    $('#product-code').focus();
  }
});


function invoice_init() {
	let customer_code = $('#customer-code').val();

	$('#invoice').autocomplete({
		source:HOME + 'get_sap_invoice_code/' + customer_code,
		autoFocus:true,
		open:function(event) {
			let ul = $(this).autocomplete('widget');
			ul.css('width', 'auto');
		},
		select:function(event, ui) {
			let code = ui.item.code;
			let customerCode = ui.item.customer_code;
			let customerName = ui.item.customer_name;

			if(customerCode.length && customerName.length) {
				$('#customer-code').val(customerCode);
				$('#customer-name').val(customerName);
			}
		},
		close:function(){
			var arr = $(this).val().split(' | ');

			if(arr.length > 2){
				$(this).val(arr[0]);
			}
			else {
				$(this).val('');
			}
		}
	});
}


function zone_init(clear) {
  let warehouse_code = $('#warehouse').val();

  if(clear == 'Y') {
    $('#zone-code').val('');
    $('#zone-name').val('');
  }

  $('#zone-code').autocomplete({
    source:BASE_URL + 'auto_complete/get_zone_code_and_name/'+ warehouse_code,
    autoFocus:true,
    close:function() {
      let arr = $(this).val().split(' | ');

      if(arr.length == 2) {
        $('#zone-code').val(arr[0]);
        $('#zone-name').val(arr[1]);
        $('#zone_code').val(arr[0]);
      }
      else {
        $('#zone-code').val('');
        $('#zone-name').val('');
        $('#zone_code').val('');
      }
    }
  })
}


function setInvoice() {
  let invoice = $('#invoice').val().trim();

  if(invoice.length > 3) {

    if($('.input-qty').length > 0) {
      swal({
        title:'คำเตือน',
        text:'รายการทั้งหมดจะถูกลบ <br/>ต้องการดำเนินการหรือไม่ ?',
        type:'warning',
        html:true,
        showCancelButton:true,
        confirmButtonText:'Yes',
        cancelButtonText:'No',
        closeOnConfirm:true
      },function() {
        $('#invoice').attr('disabled', 'disabled');
        $('#btn-confirm-inv').addClass('hide');
        $('#btn-clear-inv').removeClass('hide');
        $('#detail-table').html('');
        $('#order-code').focus();
      })
    }
    else {
      $('#invoice').attr('disabled', 'disabled');
      $('#btn-confirm-inv').addClass('hide');
      $('#btn-clear-inv').removeClass('hide');
      $('#detail-table').html('');
      $('#order-code').focus();
    }
  }
}


function getInvoice() {
  let invoice_code = $('#invoice').val();
  let order_code = $('#order-code').val().trim();
  let product_code = $('#product-code').val().trim();

  if(invoice_code.length > 3) {
    load_in();

    $.ajax({
      url:HOME + 'load_invoice',
      type:'POST',
      cache:false,
      data:{
        'invoice_code' : invoice_code,
        'order_code' : order_code,
        'product_code' : product_code
      },
      success:function(rs) {
        load_out();

        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status == 'success') {
            if(ds.data != null) {
              let source = $('#invoice-template').html();
              let output = $('#invoice-table');

              render(source, ds.data, output);

              $('#invoice-grid').modal('show');
            }
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
        showError(rs);
      }
    })
  }
}


function changeInvoice() {
  swal({
    title:'Clear data',
    text:'รายการทั้งหมดจะถูกลบ ต้องการดำเนินการต่อหรือไม่ ?',
    type:'warning',
    showCancelButton:true,
    confirmButtonText:'Yes',
    cancelButtonText:'No',
    closeOnConfirm:true
  }, function() {
    setTimeout(() => {
      clearInvoice();
      // $('#detail-table').html('');
      // recalTotal();
      //
      // $('#btn-clear-inv').addClass('hide');
      // $('#btn-confirm-inv').removeClass('hide');
      // $('#invoice').removeAttr('disabled');
      // $('#invoice').val('').focus();
      // $('.item-control').removeAttr('disabled');

      swal({
        title:'Success',
        type:'success',
        timer:1000
      });
    }, 200)
  })
}


function checkAll(el) {
	if (el.is(":checked")) {
		$('.chk').prop("checked", true);
	} else {
		$('.chk').prop("checked", false);
	}
}


function receiveAll() {
  $('.inv-qty').each(function() {
    let uid = $(this).data('uid');
    let sold = parseDefault(parseFloat($(this).data('sold')), 0);

    if(sold > 0) {
      $(this).val(sold);
    }
  })
}


function clearAll() {
  $('.inv-qty').val('');
}


function addToOrder() {
  clearErrorByClass('inv-qty');
  let error = 0;
  let rows = [];

  $('.inv-qty').each(function() {
    let uid = $(this).data('uid');
    let sold = parseDefault(parseFloat($(this).data('sold')), 0);
    let qty = parseDefault(parseFloat($(this).val()), 0);

    if(qty != 0) {
      if(qty < 0 || qty > sold) {
        $(this).hasError();
        error++;
      }
      else {
        let price = parseDefault(parseFloat($(this).data('price')), 0);
        let disc = parseDefault(parseFloat($(this).data('discount')), 0);
        let discount = disc > 0 ? (disc * 0.01) : 0;

        if($('#qty-'+uid).length) {
          let prevQty = parseDefault(parseFloat($('#qty-'+uid).val()), 0);
          let newQty = qty + prevQty;
          qty = newQty > sold ? sold : newQty;
        }

        let amount = (qty * price) - (qty * (price * discount));

        rows.push({
          'uid' : $(this).data('uid'),
          'product_code' : $(this).data('pdcode'),
          'product_name' : $(this).data('pdname'),
          'invoice' : $(this).data('invoice'),
          'DocEntry' : $(this).data('docentry'),
          'LineNum' : $(this).data('linenum'),
          'order_code' : $(this).data('order'),
          'sold_qty' : $(this).data('sold'),
          'price' : $(this).data('price'),
          'discount' : $(this).data('discount'),
          'qty' : qty,
          'amount' : addCommas(amount.toFixed(2))
        });
      }
    }
  })

  if(error > 0) {
    return false;
  }

  $('#invoice-grid').modal('hide');

  load_in();

  if(rows.length) {
    let updateTemplate = $('#row-update-template').html();
    let rowTemplate = $('#row-template').html();

    rows.forEach(function(row) {
      if($('#qty-' + row.uid).length) {
        let output = $('#row-' + row.uid);
        render(updateTemplate, row, output);
      }
      else {
        let output =  $('#detail-table');

        render_append(rowTemplate, row, output);
      }
    });
  }

  load_out();

  reIndex();
  recalTotal();
}


function deleteChecked() {
  if($('.chk:checked').length) {
    $('.chk:checked').each(function() {
      let uid = $(this).data('uid');
      $('#row-'+uid).remove();
    });

    recalTotal();
    reIndex();
  }
}


function recalRow(el) {
  el.clearError();
  let uid = el.data('uid');
  let limit = parseDefault(parseFloat(el.data('sold')), 0);
  let qty = parseDefault(parseFloat(el.val()), 0);
  let price = parseDefault(parseFloat(el.data('price')), 0);
  let disc = parseDefault(parseFloat(el.data('discount')), 0);
  let discount = disc > 0 ? (disc * 0.01) : 0;
  let amount = (qty * price) - (qty * (price * discount));

	$('#amount-' + uid).text(addCommas(amount.toFixed(2)));

  if(qty < 0 || qty > limit) {
    el.hasError();
  }

	recalTotal();
}


function recalTotal(){
	var totalAmount = 0;
	var totalQty = 0;

	$('.amount-label').each(function(){
		let amount = removeCommas($(this).text());
		amount = parseDefault(parseFloat(amount), 0);
		totalAmount += amount;
	});

	$('.input-qty').each(function(){
		let qty = $(this).val();
		qty = parseDefault(parseFloat(qty), 0);
		totalQty += qty;
	});

	totalQty = totalQty.toFixed(2);
	totalAmount = totalAmount.toFixed(2);

	$('#total-qty').text(addCommas(totalQty));
	$('#total-amount').text(addCommas(totalAmount));
}


function doReceive() {
  let qty = parseDefault(parseFloat($('#qty').val()), 0);
  let bc = $('#barcode').val().trim();

  if(qty > 0 && bc.length > 0) {
    let barcode = md5(bc);
    let valid = 0;

    if($('.'+barcode).length) {

      $('#barcode').attr('disabled', 'disabled');

      $('.'+barcode).each(function() {
				if(valid == 0 && qty > 0) {
          let el = $(this);
					let uid = el.data('uid');
					let limit = parseDefault(parseFloat(el.data('limit')), 0);
					let inputQty = parseDefault(parseFloat(el.val()), 0);
					let diff = limit - inputQty;

					if(diff > 0) {
						let receiveQty = qty >= diff ? diff : qty;
						let newQty = inputQty + receiveQty;

            el.val(newQty);
						qty -= receiveQty;
					}

					if(qty == 0) {
						valid = 1;
					}
				}

        recalRow($(this));
			});

      if(qty > 0) {
				beep();
				swal({
					title: "ข้อผิดพลาด !",
					text: "สินค้าเกิน "+qty+" Pcs.",
					type: "error"
				},
				function(){
					setTimeout( function() {
						$("#barcode").removeAttr('disabled').val('').focus();
					}, 100);
				});
			}

			$('#qty').val(1);
			$('#barcode').removeAttr('disabled').val('').focus();
    }
    else {
			$('#barcode').val('');
			$('#barcode').removeAttr('disabled');
			beep();
			swal({
				title: "ข้อผิดพลาด !",
				text: "บาร์โค้ดไม่ถูกต้องหรือสินค้าไม่ตรงกับใบสั่งซื้อ",
				type: "error"
			},
			function(){
				setTimeout( function() {
					$('#barcode').removeAttr('disabled').val('').focus();
				}, 100 );
			});
		}
  }
}

$("#barcode").keyup(function(e) {
  if( e.keyCode == 13 ) {
		doReceive();
	}
});

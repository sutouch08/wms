$('#barcode').keyup(function(e){
  if(e.keyCode == 13){
    let barcode = $.trim($(this).val());
    let qty = $('#qty').val();
    doReceive();
  }
});

$('#invoice-box').keyup(function(e){
  if(e.keyCode === 13){
    add_invoice();
  }
})
;
$('#item_code').autocomplete({
	source:BASE_URL + 'auto_complete/get_item_code',
	autoFocus:true,
	close:function() {
		let arr = $(this).val().split(' | ');
		if(arr.length == 2) {
			$(this).val(arr[0]);
		}
		else {
			$(this).val('');
		}
	}
});

$('#i-gp').keyup(function(e) {
	if(e.keyCode == 13) {
		$('#i-qty').focus();
	}
});

$('#i-qty').keyup(function(e) {
	if(e.keyCode == 13) {
		add_item();
	}
});

$('#item_code').keyup(function(e) {
	if(e.keyCode === 13) {
		setTimeout(function(){
			get_item_by_code();
		}, 300);
	}
});

function get_item_by_code() {
	let item_code = $('#item_code').val().trim();
	if(item_code.length > 0) {
		$.ajax({
			url:`${HOME}get_item_by_code`,
			type:'POST',
			cache:false,
			data:{
				'item_code' : item_code
			},
			success:function(rs){
				if(isJson(rs)){
					let pd = JSON.parse(rs);
					let code = pd.code;
					let gp = parseDefaultFloat($('#gp').val(), 0);
					let price = parseFloat(pd.price).toFixed(2);								

					if(code.length)
					{
						$('#i-barcode').val(pd.barcode);
						$('#item_name').val(pd.name);
						$('#i-price').val(price);
						$('#i-gp').val(gp);
						$('#i-qty').val(1);

						$('#i-gp').focus();
					}
				}
				else
				{
					$('#i-barcode').val("");
					$('#item_name').val("");
					$('#i-price').val("");
					$('#i-gp').val(0);
					$('#i-qty').val(1);
					swal('ไม่พบสินค้า');
				}
			}//-- success
		}); //--- ajax
	}
}

function add_item() {
	let barcode = $('#i-barcode').val().trim();
  let qty = parseDefaultFloat($('#i-qty').val(), 1);

	if(qty > 0 && barcode.length > 0) {
    //---- ถ้ามีรายการนี้อยู่ในตารางแล้ว
    if($(`#barcode_${barcode}`).length)
    {
      let no = $(`#barcode_${barcode}`).val();
      let c_qty = parseDefault(parseInt($(`#qty_${no}`).val()), 0);
      let new_qty = c_qty + qty;
      $(`#qty_${no}`).val(new_qty);
      recalRow(no);
      $('#item_code').val('');
			$('#item_name').val('');
			$('#i-barcode').val('');
			$('#i-price').val('');
			$('#i-gp').val(0);
      $('#i-qty').val(1);

      $('#item_code').focus();
    }
    else
    {
      //---- ถ้าไม่มีรายการอยู่
      //---- เช็คสินค้า แล้วเพิ่มเข้ารายการ
			let code = $('#item_code').val();
			let name = $('#item_name').val();
			let gp = parseDefaultFloat($('#i-gp').val(), 0);			
      let price = parseDefaultFloat($('#i-price').val(), 0).toFixed(2);
			let discount = (gp * 0.01).toFixed(2);
			let amount = price * qty;
			let disAmount = (price * discount) * qty;

			if(code.length)
			{
				let invoice = $('#invoice_code').val();
				let no = $('#no').val();
				no++;
				$('#no').val(no);
				let data = {
					'no' : no,
					'barcode' : barcode,
					'code' : code,
					'name' : name,
					'qty' : qty,
					'price' : price,
					'invoice' : invoice,
					'discount' : gp,
					'amount' : addCommas((amount - disAmount ).toFixed(2))
				};

				let source = $('#row-template').html();
				let output = $('#detail-table');
				render_append(source, data, output);
				reIndex();
				recalTotal();

				$('#item_code').val('');
				$('#item_name').val('');
				$('#i-barcode').val('');
				$('#i-price').val('');
				$('#i-gp').val(0);
	      $('#i-qty').val(1);

	      $('#item_code').focus();
			}
    }
  }
}

//---- ยิงบาร์โค้ดเพื่อรับสินค้า
//---- 1. เช็คก่อนว่ามีรายการอยู่ในตารางหน้านี้หรือไม่ ถ้ามีเพิ่มจำนวน แล้วคำนวนยอดใหม่
//---- 2. ถ้าไม่มีรายการอยู่ เช็คสินค้าก่อนว่ามีในระบบหรือไม่
//---- 3. ถ้ามีในระบบ เพิ่มรายการเข้าตาราง
function doReceive() {
  let barcode = $('#barcode').val();
  let qty = parseDefaultInt($('#qty').val(), 1);

  if(qty > 0 && barcode.length > 0)
  {
    $('#barcode').attr('disabled', 'disabled');

    //---- ถ้ามีรายการนี้อยู่ในตารางแล้ว
    if($(`#barcode_${barcode}`).length)
    {
      let no = $(`#barcode_${barcode}`).val();      
      let c_qty = parseDefaultInt($(`#qty_${no}`).val(), 0);  
      let new_qty = c_qty + qty;
      $(`#qty_${no}`).val(new_qty);
      recalRow(no);
      $('#barcode').val('');
      $('#qty').val(1);
      $('#barcode').removeAttr('disabled');
      $('#barcode').focus();
    }
    else
    {
      //---- ถ้าไม่มีรายการอยู่
      //---- เช็คสินค้า แล้วเพิ่มเข้ารายการ
      load_in();
      $.ajax({
        url:`${HOME}get_item`,
        type:'POST',
        cache:false,
        data:{
          'barcode' : barcode
        },
        success:function(rs){
          load_out();
          if(isJson(rs)){
            let pd = JSON.parse(rs);
            let code = pd.code;
            let gp = parseDefaultFloat($('#gp').val(), 0);
            let price = roundNumber(parseDefaultFloat(pd.price, 0), 2);
            let discount = roundNumber(gp * 0.01, 2);
            let amount = price * qty;
            let disAmount = (price * discount) * qty;

            if(code.length)
            {
              let invoice = $('#invoice_code').val();
              let no = $('#no').val();
              no++;
              $('#no').val(no);
              let data = {
                'no' : no,
                'barcode' : barcode,
                'code' : pd.code,
                'name' : pd.name,
                'qty' : qty,
                'price' : price,
                'invoice' : invoice,
                'discount' : gp,
                'amount' : addCommas((amount - disAmount ).toFixed(2))
              };

              let source = $('#row-template').html();
              let output = $('#detail-table');
              render_append(source, data, output);
              reIndex();
              recalTotal();

              $('#barcode').val('');
              $('#qty').val(1);
              $('#barcode').removeAttr('disabled');
              $('#barcode').focus();
            }
          }
          else
          {
            swal('ไม่พบสินค้า');
            $('#barcode').removeAttr('disabled');
          }
        }//-- success
      }); //--- ajax
    }
  }
}

function add_invoice() {
  let h = {
    'code' : $('#code').val(),
    'invoice' : $('#invoice-box').val(),
    'customer_code' : $('#customer-code').val()
  };
  
  if(h.invoice.length == 0) {
    return false;
  }

  if(h.customer_code.length == 0) {
    return false;
  }

  load_in();

  $.ajax({
    url:`${HOME}add_invoice`,
    type:'POST',
    cache:false,
    data: h,
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let data = JSON.parse(rs);
        $('#invoice_list').html(data.invoice);
        $('#bill_amount').val(data.amount);
        $('#invoice-box').val('');
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

function removeInvoice(return_code, invoice_code)
{
  load_in();
  $.ajax({
    url:`${HOME}remove_invoice`,
    type:'GET',
    cache:false,
    data:{
      'return_code' : return_code,
      'invoice_code' : invoice_code
    },
    success:function(rs){
      load_out();
      if(isJson(rs)){
        let ds = JSON.parse(rs);
        $('#invoice_list').html(ds.invoice);
        $('#bill_amount').val(ds.amount);
      }
      else
      {
        showError(rs);
      }
    },
    error:function(rs) {
      showError(rs);
    }
  });
}

$('.input-qty').focusin(function() {
  $(this).select();
});

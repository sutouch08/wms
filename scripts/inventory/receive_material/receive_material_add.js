window.addEventListener('load', () => {
	poInit();
	vendorInit();
});

var click = 0;

$("#date-add").datepicker({ dateFormat: 'dd-mm-yy'});
$("#posting-date").datepicker({ dateFormat: 'dd-mm-yy'});

function poInit() {
	let vendor_code = $('#vendor-code').val();

	$('#po-code').autocomplete({
		source:HOME + 'get_po_code/' + vendor_code,
		autoFocus:true,
		position: {
			my: 'right top',
			at: 'right bottom'
		},
		close:function(rs) {
			let arr = $(this).val().split(' | ');

			if(arr.length == 3) {
				$(this).val(arr[0]);

				if(vendor_code.length == 0) {
					$('#vendor-code').val(arr[1]);
					$('#vendor-name').val(arr[2]);
				}
			}
			else {
				$(this).val('');
			}
		}
	});
}


function vendorInit() {
	$('#vendor-code').autocomplete({
		source:HOME + 'get_vendor',
		autoFocus:true,
		close:function(rs) {
			let arr = $(this).val().split(' | ');

			if(arr.length === 2) {
				$('#vendor-code').val(arr[0]);
				$('#vendor-name').val(arr[1]);

				setTimeout(() => {
					poInit();
					$('#po-code').focus();
				}, 100);
			}
			else {
				$('#vendor-code').val('');
				$('#vendor-name').val('');

				setTimeout(() => {
					poInit();
				}, 100);
			}
		}
	})
}


function add() {
	if(click == 0) {
		click = 1;
		clearErrorByClass('r');
		let h = {
			'date_add' : $('#date-add').val(),
			'posting_date' : $('#posting-date').val(),
			'vendor_code' : $('#vendor-code').val().trim(),
			'vendor_name' : $('#vendor-name').val().trim(),
			'po_code' : $('#po-code').val().trim(),
			'warehouse_code' : $('#warehouse').val(),
			'remark' : $('#remark').val().trim()
		};

		if( ! isDate(h.date_add)) {
			$('#date-add').hasError();
			click = 0;
			return false;
		}

		if( ! isDate(h.posting_date)) {
			$('#posting-date').hasError();
			click = 0;
			return false;
		}

		if(h.vendor_code.length == 0 || h.vendor_name.length == 0) {
			$('#vendor-code').hasError();
			$('#vendor-name').hasError();
			click = 0;
			return false;
		}

		if(h.po_code.length < 7) {
			$('#po-code').hasError();
			click = 0;
			return false;
		}

		if(h.warehouse_code.length == 0) {
			$('#warehouse').hasError();
			click = 0;
			return false;
		}

		load_in();

		$.ajax({
			url:HOME + 'add',
			type:'POST',
			cache:false,
			data:{
				'data' : JSON.stringify(h)
			},
			success:function(rs) {

			},
			error:function(rs) {
				showError(rs);
			}
		})
	}
}

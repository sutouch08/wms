<?php $this->load->view('include/header_mobile'); ?>
<?php $this->load->view('inventory/receive_po/mobile/style'); ?>
<?php $this->load->view('inventory/receive_po/mobile/process_style'); ?>
<?php $this->load->view('inventory/receive_po/mobile/header_mobile'); ?>
<?php $this->load->view('inventory/receive_po/mobile/detail_mobile'); ?>
<?php $this->load->view('inventory/receive_po/mobile/process_menu'); ?>

<script src="<?php echo base_url(); ?>scripts/beep.js"></script>

<script>
	var HOME = BASE_URL + 'inventory/receive_po/';

	window.addEventListener('load', () => {
		focus_init();
		bclick_init();
		$('#barcode-item').focus();
	})


	function focus_init() {
		$('.focus').focusout(function() {
			autoFocus = 1
			setTimeout(() => {
				if(autoFocus == 1) {
					setFocus();
				}
			}, 1000)
		})

		$('.focus').focusin(function() {
			autoFocus = 0;
		});
	}


	function setFocus() {
		$('#barcode-item').focus();
	}


	function bclick_init() {
	  $('.b-click').click(function(){
	    let barcode = $(this).text().trim();
	    $('#barcode-item').val(barcode);
	    $('#barcode-item').focus();
	  });
	}


	function leave() {
		let unsave = 0;

		$('.buffer').each(function() {
			let el = $(this);
			let qty = parseDefault(parseFloat(el.val()), 0);

			if(qty != 0) {
				unsave += qty;
			}
		});

		if(unsave > 0) {
			beep();
			swal({
				title: 'Warning',
				text:'รายการที่ยังไม่บันทึก '+unsave+' รายการจะหายไป<br/>ต้องการออกจากหน้านี้หรือไม่ ?',
				type: 'warning',
				html:true,
				showCancelButton: true,
				cancelButtonText: 'No',
				confirmButtonText: 'Yes',
				closeOnConfirm: false
			}, function(){
				window.location.href = HOME;
			});
		}
		else {
			window.location.href = HOME;
		}
	}


	function doRefresh() {
		let unsave = 0;

		$('.buffer').each(function() {
			let el = $(this);
			let qty = parseDefault(parseFloat(el.val()), 0);

			if(qty != 0) {
				unsave += qty;
			}
		});

		if(unsave > 0) {
			beep();
			swal({
				title: 'Warning',
				text:'รายการที่ยังไม่บันทึก '+unsave+' รายการจะหายไป<br/>ต้องการโหลดหน้านี้ใหม่หรือไม่ ?',
				type: 'warning',
				html:true,
				showCancelButton: true,
				cancelButtonText: 'No',
				confirmButtonText: 'Yes',
				closeOnConfirm: false
			}, function(){
				refresh();
			});
		}
		else {
			refresh();
		}
	}


	function viewDetail(code){
		window.location.href = HOME + 'view_detail/'+ code;
	}


	function doReceive() {
		let barcode = $('#barcode-item').val().trim();

		if(barcode.length) {
			let qty = parseDefault(parseFloat($('#qty').val()), 1);
			let valid = 0;
			let totalQty = parseDefault(parseFloat(removeCommas($('#all-qty').val())), 0);

			if($('.'+barcode).length) {

				$('#barcode-item').attr('disabled', 'disabled');

				$('.'+barcode).each(function() {
					if(valid == 0 && qty > 0) {
						let id = $(this).data('id');
						let el = $('#receive-qty-'+id);
						let received = parseDefault(parseFloat(removeCommas(el.val())), 1);
						let limit = parseDefault(parseFloat(el.data('limit')), 0);
						let diff = limit - received;
						let balance = parseDefault(parseFloat(removeCommas($('#balance-'+id).val())), 0);

						if(diff > 0) {
							let buffer = parseDefault(parseFloat($('#buffer-'+id).val()), 0);
							let receiveQty = qty >= diff ? diff : qty;
							let newQty = received + receiveQty;
							balance = limit - newQty;
							qty -= receiveQty;
							buffer += receiveQty;
							totalQty += receiveQty;

							$('#receive-qty-'+id).val(newQty);
							$('#buffer-'+id).val(buffer);
							$('#balance-'+id).val(addCommas(balance));
							$('#all-qty').val(addCommas(totalQty));
						}

						if(qty == 0) {
							valid = 1;
						}

						$('.receive-item').removeClass('heighlight');
						$('#receive-item-'+id).addClass('heighlight');
						$('#receive-item-'+id).prependTo($('#incomplete-box'));

						if(balance == 0) {
							$('#receive-item-'+id).removeClass('unvalid').addClass('valid').prependTo($('#complete-box'));
						}
					}
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
							$("#barcode-item").focus();
						}, 1000 );
					});
				}

				$('#qty').val(1);
				$('#barcode-item').removeAttr('disabled').val('').focus();

				if($('.unvalid').length == 0) {
					$('#close-bar').removeClass('hide');
				}
				else {
					$('#close-bar').addClass('hide');
				}
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
						$("#barcode-item").focus();
					}, 1000 );
				});
			}
		}
	}


	function decreaseReceived(id) {
		let buffer = parseDefault(parseFloat($('#buffer-'+id).val()), 0); //---- จำนวนรับแล้วที่ยังไม่บันทึก (buffer)
		let received = parseDefault(parseFloat(removeCommas($('#receive-qty-'+id).val())), 0);
		let balance = parseDefault(parseFloat(removeCommas($('#balance-'+id).val())), 0);
		let allQty = parseDefault(parseFloat(removeCommas($('#all-qty').val())), 0);

		if(received > 0) {
			received--;
			buffer--;
			allQty--;
			balance++;

			$('#receive-qty-'+id).val(addCommas(received));
			$('#buffer-'+id).val(buffer);
			$('#balance-'+id).val(addCommas(balance));
			$('#all-qty').val(addCommas(allQty));

			$('.receive-item').removeClass('heighlight');
			$('#receive-item-'+id).removeClass('valid').addClass('unvalid').addClass('heighlight');
			$('#receive-item-'+id).prependTo($('#incomplete-box'));
		}
	}


	function saveReceived() {
		let h = {
			'code' : $('#code').val(),
			'rows' : []
		};

		$('.buffer').each(function() {
			let el = $(this);
			let qty = parseDefault(parseFloat(el.val()), 0);

			if(qty != 0) {
				h.rows.push({
					'id' : el.data('id'),
					'product_code' : el.data('code'),
					'qty' : qty
				});
			}
		});

		if(h.rows.length) {
			load_in();

			$.ajax({
				url:HOME + 'save_receive_rows',
				type:'POST',
				cache:false,
				data:{
					'data' : JSON.stringify(h)
				},
				success:function(rs) {
					load_out();

					if(rs.trim() === 'success') {
						swal({
							title:'Saved',
							type:'success',
							timer:1000
						});

						setTimeout(() => {
							refresh();
						}, 1200);
					}
					else {
						showError(rs);
					}
				},
				error:function(rs) {
					showError(rs);
				}
			})
		}
	}


	function finishReceive() {
		load_in();

		let h = {
			'code' : $('#code').val(),
			'rows' : []
		};

		$('.buffer').each(function() {
			let el = $(this);
			let qty = parseDefault(parseFloat(el.val()), 0);

			if(qty != 0) {
				h.rows.push({
					'id' : el.data('id'),
					'product_code' : el.data('code'),
					'qty' : qty
				});
			}
		});

		$.ajax({
			url:HOME + 'save_and_close',
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
						swal({
							title:'Success',
							type:'success',
							timer:1000
						});

						setTimeout(() => {
							viewDetail(h.code);
						}, 1200);
					}
					else if(ds.status === 'warning') {
						swal({
							title:'Warning',
							text: ds.message,
							type:'warning',
							html:true
						}, () => {
							viewDetail(h.code);
						});
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
				showError(rs);
			}
		});
	}


	function forceClose() {
		swal({
			title:'Force Close',
			text:'สินค้าไม่ครบตามยอดส่ง ต้องการบังคับปิดเอกสารนี้หรือไม่ ?',
			type:'warning',
			html:true,
			showCancelButton:true,
			cancelButtonText:'No',
			confirmButtonText:'บังคับปิด',
			confirmButtonColor:'#d15b47',
			closeOnConfirm:true
		},function() {
			setTimeout(() => {
				finishReceive();
			}, 100);
		})
	}


	function openHeader() {
		$('#header-pad').addClass('move-in');
	}


	function closeHeader() {
		$('#header-pad').removeClass('move-in');
	}


	function openComplete() {
		$('#complete-pad').addClass('move-in');
	}


	function closeComplete() {
		$('#complete-pad').removeClass('move-in');
	}


	$('#barcode-item').keyup(function(e) {
		if(e.keyCode === 13) {
			doReceive();
		}
	});


	$('#btn-increse').click(function() {
		let qty = parseDefault(parseInt($('#qty').val()), 1);

		if(qty > 0) {
			qty++;
		}
		else {
			qty = 1;
		}

		$('#qty').val(qty);
	})


	$('#btn-decrese').click(function() {
		let qty = parseDefault(parseInt($('#qty').val()), 1);

		if(qty > 1) {
			qty--;
		}
		else {
			qty = 1;
		}

		$('#qty').val(qty);
	})
</script>

<?php $this->load->view('include/footer'); ?>

window.addEventListener('load', () => {
	poInit();
	vendorInit();
	zoneInit();
});

var click = 0;

$("#date-add").datepicker({ dateFormat: 'dd-mm-yy'});
$("#posting-date").datepicker({ dateFormat: 'dd-mm-yy'});


function poInit() {
	let vendor_code = $('#vendor-code').val();
	let item = "";

	$('#po-code').autocomplete({
		source:HOME + 'get_po_code/' + vendor_code,
		autoFocus:true,
		// position: {
		// 	my: 'right top',
		// 	at: 'right bottom'
		// },
		select:function(event, ui) {
			item = ui.item;
		},
		close:function() {
			setTimeout(() => {
				if(item != undefined && item != null && item != '') {
					let arr = item.label != undefined ? item.label.split(' | ') : [];
					let currency = item.currency;
					let rate = item.rate;
					if(arr.length == 3) {
						$(this).val(arr[0]);

						if(vendor_code.length == 0) {
							$('#vendor-code').val(arr[1]);
							$('#vendor-name').val(arr[2]);
						}

						$('#DocCur').val(currency);
						$('#DocRate').val(rate);

						confirmChangePo();
					}
					else {
						$(this).val('');
					}
				}
				else {
					$(this).val('');
				}
			},100)
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


function changeZone() {
	$('#zone-code').val('');
	$('#zone-name').val('');

	zoneInit();
}


function zoneInit() {
	let whsCode = $('#warehouse').val();

	$('#zone-code').autocomplete({
		source:HOME + 'get_zone/' + whsCode,
		autoFocus:true,
		close:function() {
			let label = $(this).val().split(' | ');

			if(label.length == 2) {
				$(this).val(label[0]);
				$('#zone-name').val(label[1]);
			}
			else {
				$(this).val('');
				$('#zone-name').val('');
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
			'currency' : $('#DocCur').val(),
			'rate' : $('#DocRate').val(),
			'invoice_code' : $('#invoice-code').val().trim(),
			'warehouse_code' : $('#warehouse').val(),
			'zone_code' : $('#zone-code').val().trim(),
			'remark' : $('#remark').val().trim()
		};

		if( ! isDate(h.date_add)) {
			$('#date-add').hasError();
			click = 0;
			return false;
		}

		if(h.posting_date != "" && ! isDate(h.posting_date)) {
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

		if(h.warehouse_code == "") {
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
				click = 0;
				load_out();

				if(isJson(rs)) {
					let ds = JSON.parse(rs);

					if(ds.status === 'success') {
						edit(ds.code);
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
				click = 0;
			}
		})
	}
}


function save(saveType) {
	if(click == 0) {
		click = 1;
		err = 0;
		clearErrorByClass('r');

		let h = {
			'code' : $('#code').val(),
			'save_type' : saveType,
			'date_add' : $('#date-add').val(),
			'posting_date' : $('#posting-date').val(),
			'vendor_code' : $('#vendor-code').val().trim(),
			'vendor_name' : $('#vendor-name').val().trim(),
			'po_code' : $('#po-code').val().trim(),
			'currency' : $('#DocCur').val(),
			'rate' : $('#DocRate').val(),
			'invoice_code' : $('#invoice-code').val().trim(),
			'warehouse_code' : $('#warehouse').val(),
			'zone_code' : $('#zone-code').val().trim(),
			'remark' : $('#remark').val().trim(),
			'items' : []
		};

		if( ! isDate(h.date_add)) {
			click = 0;
			$('#date-add').hasError();
			return false;
		}

		if(h.posting_date != "" && ! isDate(h.posting_date)) {
			click = 0;
			$('#posting-date').hasError();
			return false;
		}

		if(h.vendor_code.length == 0 || h.vendor_name.length == 0) {
			click = 0;
			$('#vendor-code').hasError();
			$('#vendor-name').hasError();
			showError('กรุณาระบุผู้ซื้อ');
			return false;
		}

		if(saveType == 'C' || saveType == 'O') {
			if(h.po_code.length == 0) {
				click = 0;
				$('#po-code').hasError();
				showError('กรุณาระบุ PO No.');
				return false;
			}

			if(h.invoice_code.length == 0) {
				click = 0;
				$('#invoice-code').hasError();
				showError('กรุณาระบุใบส่งสินค้า');
				return false;
			}

			if(h.warehouse_code == '') {
				click = 0;
				$('#warehouse').hasError();
				showError('กรุณาระบุคลัง');
				return false;
			}

			if(saveType == 'C' && h.zone_code.length == 0) {
				click = 0;
				$('#zone-code').hasError();
				showError('กรุณาระบุโซน');
				return false;
			}

			if($('.receive-qty').length == 0) {
				click = 0;
				showError('ไม่พบรายการรับเข้า');
				return false;
			}
		}

    let errMsg = "";
    let line = 0;
    let sc = true;

		$('.receive-qty').each(function() {
			if(sc === true) {
				let el = $(this);
				let uid = el.data('uid');
				let receiveQty = parseDefaultFloat(el.val(), 0);
				let limit = parseDefaultFloat(el.data('limit'), 0);
				let hasBatch = el.data('batch') == 'Y' ? 1 : 0;

				if(receiveQty <= 0) {
          el.hasError();
          sc = false;
          errMsg = "จำนวนไม่ถูกต้อง";
          return false;
        }

				if(receiveQty > limit) {
					el.hasError();
					sc = false;
					errMsg = "จำนวนมากกว่ายอดค้างร้บ";
					return false;
				}

				if(sc === true && hasBatch == 1) {
          if($('.child-of-'+uid).length == 0) {
            sc = false;
            $('#item-code-'+uid).hasError();
            errMsg = "กรุณาระบุ Batch";
            return false;
          }
        }

				if(sc === true) {
					let row = {
						'uid' : uid,
						'baseCode' : el.data('basecode'),
						'baseEntry' : el.data('baseentry'),
						'baseLine' : el.data('baseline'),
						'product_code' : el.data('code'),
						'product_name' : el.data('name'),
						'qty' : receiveQty,
						'Price' : el.data('price'),
						'PriceBefDi' : el.data('bfprice'),
						'PriceAfVAT' : el.data('afprice'),
						'backlogs' : el.data('backlogs'),
						'currency' : h.currency,
						'rate' : h.rate,
						'vatGroup' : el.data('vatcode'),
						'vatRate' : el.data('vatrate'),
						'UomCode' : el.data('uom'),
						'UomCode2' : el.data('uom2'),
						'UomEntry' : el.data('uomentry'),
						'UomEntry2' : el.data('uomentry2'),
						'unitMsr' : el.data('unitmsr'),
						'unitMsr2' : el.data('unitmsr2'),
						'NumPerMsr' : el.data('numpermsr'),
						'NumPerMsr2' : el.data('numpermsr2'),
						'hasBatch' : el.data('batch'),
						'batchRows' : []
					};

					if(hasBatch == 1) {
						let sumBatchQty = 0;
            let batches = [];

						$('.child-of-'+uid).each(function() {
              if(sc === false) { return false; }

              let uuid = $(this).data('uid');
              let ro = $('#batch-qty-'+uuid);
              let batchNum = $('#batch-'+uuid).val().trim();
              let batchAttr1 = $('#batch-attr1-'+uuid).val().trim();
              let batchAttr2 = $('#batch-attr2-'+uuid).val().trim();

              if(batchNum == "" || batchNum == null || batchNum == undefined) {
                sc = false;
                $('#batch-'+uuid).hasError();
                errMsg = "กรุณาระบุ Batch No.";
                return false;
              }

              if(sc === true) {
                if(batches.includes(batchNum)) {
                  sc = false;
                  $('#batch-'+uuid).hasError();
                  errMsg = "Batch No. ซ้ำ";
                  return false;
                }
                else {
                  batches.push(batchNum);
                }
              }

              let bQty = parseDefaultFloat(ro.val(), 0);
              sumBatchQty += bQty;

							row.batchRows.push({
								'batchNo' : batchNum,
								'batchAttr1' : batchAttr1,
								'batchAttr2' : batchAttr2,
								'batchQty' : bQty,
								'uid' : uuid
							});              
            });

						if(sc === true && roundNumber(receiveQty, 4) != roundNumber(sumBatchQty, 4)) {
              el.hasError();
              sc = false;
              errMsg = "จำนวนไม่ถูกต้อง กรุณาแก้ไข" ;
              return false;
            }
					}

					h.items.push(row);
          line++;
				}
			} //-- end sc
		}); //-- end foreach 

		if(sc === false) {
      click = 0;
      swal({
        title:'Error!',
        text:errMsg,
        type:'error'
      });

      return false;
    }

		if(sc === true) {
			load_in();

			$.ajax({
				url:HOME + 'save',
				type:'POST',
				cache:false,
				data:{
					'data' : JSON.stringify(h)
				},
				success:function(rs) {
					load_out();
					click = 0;

					if(isJson(rs)) {
						let ds = JSON.parse(rs);

						if(ds.status === 'success') {
							if(ds.ex == 1) {
								swal({
									title:'Oops !',
									text:'บันทึกเอกสารสำเร็จ แต่ส่งข้อมูลไป SAP ไม่สำเร็จ <br/>'+ds.message,
									type:'info',
									html:true
								}, function() {
									viewDetail(h.code);
								})
							}
							else {
								swal({
									title:'Success',
									type:'success',
									timer:1000
								});

								setTimeout(() => {
									viewDetail(h.code);
								}, 1200);
							}
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
			});
		}		
	}
}


function rollback(code) {
	swal({
		title:'ย้อนสถานะ',
		text:'ต้องการย้อนสถานะ '+code+' หรือไม่ ?',
		type:'warning',
		html:true,
		showCancelButton:true,
		confirmButtonColor:'#DD6B55',
		confirmButtonText:'Yes',
		cancelButtonText:'No',
		closeOnConfirm:true
	}, function() {
		load_in();

		setTimeout(() => {
			$.ajax({
				url:HOME + 'rollback',
				type:'POST',
				cache:false,
				data: {
					'code' : code
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
							edit(code);
						}, 1200)
					}
					else {
						showError(rs);
					}
				},
				error:function(rs) {
					showError(rs);
				}
			})
		}, 100);
	})
}

var autoFocus = 1;

window.addEventListener('load', () => {
  focus_init();
  
  $('#barcode-item').focus();
});


window.addEventListener('keydown', (event) => {
  if (event.key === 'F1') {
    event.preventDefault();
    confirmSaveBeforeAddBox();
  }

  if (event.code === 'Space') {
    event.preventDefault();
    saveQc(0);
  }

  if (event.key === 'F2') {
    event.preventDefault();

    if ($(".incomplete").length == 0) {
      closeOrder();
    }
  }

  if (event.key === 'F3') {
    event.preventDefault();
    if ($('#state').val() == 7) {
      confirmOrder($('#order_code').val());
    }
  }

  if(event.key === 'Escape') {
    event.preventDefault();
    goBack();
  }
});

function focus_init() {
  $('.focus').focusout(function () {
    autoFocus = 1
    setTimeout(() => {
      if (autoFocus == 1) {
        barcodeFocus();
      }
    }, 1000)
  })

  $('.focus').focusin(function () {
    autoFocus = 0;
  });
}

function barcodeFocus() {
  $('#barcode-item').focus();
}


$("#chk-force-close").change(function () {
  if ($("#chk-force-close").prop('checked') == true) {
    $("#btn-force-close").removeClass('not-show');
  }
  else {
    $("#btn-force-close").addClass('not-show');
  }
});


function printBox(id) {
  var code = $("#order_code").val();
  var center = ($(document).width() - 800) / 2;
  var target = HOME + 'print_box/' + code + '/' + id;
  window.open(target, "_blank", "width=800, height=900. left=" + center + ", scrollbars=yes");
}


function printAllBox(code) {
  var center = ($(document).width() - 800) / 2;
  var target = HOME + 'print_all_box/' + code;
  window.open(target, "_blank", "width=800, height=900. left=" + center + ", scrollbars=yes");
}

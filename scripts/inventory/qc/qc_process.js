var autoFocus = 1;

window.addEventListener('load', () => {
  let box_id = $('#id_box').val();
  layoutInit();
  updateBoxList(box_id);
  focus_init();  
  $('#barcode-item').focus();
});

function layoutInit() {
  let videoOnPack = $('#video-on-pack').val() == 1 ? true : false;
  let weightOnPack = $('#weight-on-pack').val() == 1 ? true : false;

  if(videoOnPack) {
    const videoDevices = localStorage.getItem('packCameraId');
    if(videoDevices) {
      $('#video-box').removeClass('hide');
    }    
  }

  if(weightOnPack) {
    const weighingDevice = localStorage.getItem('WrxActiveDevice');
    if(weighingDevice) {      
      $('#weight-box').removeClass('hide');
    }
  }
}


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

function printAddressEng() {

}


function printBox(id) {
  const code = $("#order_code").val();
  const width = 800;
  const height = 900;
  const left = (window.innerWidth - width) / 2;
  const target = `${HOME}print_box/${code}/${id}`;
  window.open(target, "_blank", `width=${width}, height=${height}, left=${left}, scrollbars=yes`);
} 

function printBoxEng(id) {
  const code = $("#order_code").val();
  const width = 800;
  const height = 900;
  const left = (window.innerWidth - width) / 2;
  const target = `${HOME}print_box/${code}/${id}/eng`;
  window.open(target, "_blank", `width=${width}, height=${height}, left=${left}, scrollbars=yes`);
}


function printAllBox(code) {
  const width = 800;
  const height = 900;
  const left = (window.innerWidth - width) / 2;
  const target = `${HOME}print_all_box/${code}`;
  window.open(target, "_blank", `width=${width}, height=${height}, left=${left}, scrollbars=yes`);
}


function printAllBoxEng(code) {
  const width = 800;
  const height = 900;
  const left = (window.innerWidth - width) / 2;
  const target = `${HOME}print_all_box/${code}/eng`;
  window.open(target, "_blank", `width=${width}, height=${height}, left=${left}, scrollbars=yes`);
}

function viewTR() {
  let code = $('#tr-list').val();

  if(code.length) {
    let width = 1250;
    let height = 750;
    let left = (window.innerWidth - width) / 2;
    let target = BASE_URL + 'productions/production_transfer/view_detail/'+code+'?nomenu';
    let prop = `width=${width}, height=${height}, left=${left}, scrollbars=yes`;
    window.open(target, '_blank', prop);
  }
}


function viewGI() {
  let code = $('#gi-list').val();

  if(code.length) {
    let width = 1250;
    let height = 750;
    let left = (window.innerWidth - width) / 2;
    let target = BASE_URL + 'productions/production_issue/view_detail/'+code+'?nomenu';
    let prop = `width=${width}, height=${height}, left=${left}, scrollbars=yes`;
    window.open(target, '_blank', prop);
  }
}


function viewGR() {
  let code = $('#gr-list').val();

  if(code.length) {
    let width = 1250;
    let height = 750;
    let left = (window.innerWidth - width) / 2;
    let target = BASE_URL + 'productions/production_receipt/view_detail/'+code+'?nomenu';
    let prop = `width=${width}, height=${height}, left=${left}, scrollbars=yes`;
    window.open(target, '_blank', prop);
  }
}


function viewProductionOrder(code) {
  if(code.length) {
    let width = 1250;
    let height = 750;
    let left = (window.innerWidth - width) / 2;
    let target = BASE_URL + 'productions/production_order/sap_production_order/'+code+'?nomenu';
    let prop = `width=${width}, height=${height}, left=${left}, scrollbars=yes`;
    window.open(target, '_blank', prop);
  }
}


function viewApiLogs(code) {
  let url = BASE_URL + "rest/V1/sap_api_logs";
  let mapForm = document.createElement("form");
  mapForm.target = "Map";
  mapForm.method = "POST";
  mapForm.action = url;

  let mapInput = document.createElement("input");
  mapInput.type = "text";
  mapInput.name = "code";
  mapInput.value = code;
  mapForm.appendChild(mapInput);

  document.body.appendChild(mapForm);
  let width = 1350;
  let height = 750;
  let left = (window.innerWidth - width) / 2;
  let prop = `width=${width}, height=${height}, left=${left}, scrollbars=yes`;

  map = window.open(url, "Map", prop);
  mapForm.submit();
  document.body.removeChild(mapForm);
}

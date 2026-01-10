var HOME = BASE_URL + 'inventory/delivery_order/';
//--- กลับหน้าหลัก
function goBack(){
  window.location.href = HOME;
}



//--- ไปหน้ารายละเอียดออเดอร์
function goDetail(code) {
  let viewPort = window.innerWidth;
  let width = 1250;
  let height = 800;
  let center = (viewPort - width) / 2;
  let top = 100;
  let target = HOME + 'view_detail/'+code;

  window.open(target, "_blank", `width=${width}, height=${height}, left=${center}, top=${top}, scrollbars=yes`);
}

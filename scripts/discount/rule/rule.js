var HOME = BASE_URL + 'discount/discount_rule/';
function goBack(){
  window.location.href = HOME;
}


function goAdd(){
  window.location.href = HOME + 'add_new/';
}


function goEdit(id){
  window.location.href = HOME + 'edit_rule/'+id;
}


function viewDetail(id){
  window.location.href = HOME + 'view_rule_detail/'+id;
}

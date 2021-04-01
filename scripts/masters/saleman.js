var HOME = BASE_URL + 'masters/saleman/';

function getSearch(){
  $('#searchForm').submit();
}


$('.search-box').keyup(function(e){
  if(e.keyCode == 13){
    getSearch();
  }
});


function clearFilter(){
  $.get(HOME + 'clear_filter', function(){
    goBack();
  });
}

function goBack(){
  window.location.href = HOME;
}


function syncData(){
  load_in();
  $.ajax({
    url:HOME + 'syncData',
    type:'POST',
    cache:false,
    success:function(rs){
      load_out();
      if(rs == 'success'){
        swal({
          title:'Completed',
          type:'success',
          timer:1000
        });

        setTimeout(function(){
          goBack();
        }, 1500);
      }else{
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        });
      }
    }
  });
}

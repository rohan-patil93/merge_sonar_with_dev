
// function updaterolemodal(role_id){
//   $.ajax({
//       type:"POST",
//       url:"views/addpermisionroles.php",
//       data:"action=updatemodal&role_id="+role_id,//only input
//       success: function(response){
//           $(".ajaxmodal").html(response);
//       }
//   });
//   $(".updaterolemodal").modal("show");
// }
function addrolemodal(role_id=''){
  $(".updaterolemodal").modal("show");
  var module_id = $("#module_id").val();
  $.ajax({
    type:"POST",
    url:"views/addpermisionroles.php",
       data:"action=updatemodal&role_id="+role_id+"&module_id="+module_id,//only input
       success: function(response){
        $(".ajaxmodal").html(response);
      }
    });
}


$('body').on('click', '#checkpermisionalls', function() {
  if(this.checked){ 
    $('.pagecheckboxsss').each(function(){
      this.checked = true;
    });
  }else{
    $('.pagecheckboxsss').each(function(){
      this.checked = false;
    });
  }

});
$('body').on('click', '.select_all', function() {
  if(this.checked){
    $('.checkboxsss').each(function(){
      this.checked = true;
    });
  }else{
   $('.checkboxsss').each(function(){
    this.checked = false;
  });
 }
 var formserialize = $("#addroleform").serialize();
 //console.log(formserialize);
  var module_id = $("#module_id").val();
 $.ajax({
  type:"POST",
  url:"views/addpermisionroles.php",
      data:formserialize+"&action=pagelist&module_id="+module_id,//only input
      success: function(response){
        $(".pageswooow").html(response);
      }
    });
});



$('body').on('click', '.checkboxsss', function() {
	 var formserialize = $("#addroleform").serialize();
    var module_id = $("#module_id").val(); 
 //console.log(formserialize);
  $.ajax({
    type:"POST",
    url:"views/addpermisionroles.php",
      data:formserialize+"&action=pagelist&module_id="+module_id,//only input
      success: function(response){
        $(".pageswooow").html(response);
      }
    });
});
function addRole(){
	 var formserialize = $("#addroleform").serialize();

 //console.log(addroleform);
  $.ajax({
    type:"POST",
    url:"ajax.php",
            data:formserialize+"&page=insertrole",//only input
            success: function(response){
              console.log(response);  
              $(".updaterolemodal").modal("hide");
			  tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success  pt-2 pb-2','animated flipInX','animated flipOutX','Record Inserted Successfully','');
                location.reload();
             }
           });
}
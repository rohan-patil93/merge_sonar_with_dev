window.addEventListener("load", function() {
	$(document).on("click", ".listUserType", function() {
		listUserType();
	});

	$(document).on("click", ".resendrequest", function() {
		var user_id = $(this).data("user_id");
		resendrequest(user_id);
	});
	

});	
function resendrequest(val){
	$.ajax({
        url: "ajax.php",
        data:'page=resenduserdetails&id='+val,
        type: "POST",
        success:function(data){
        console.log(data);
         tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Invitation Send To User Successfully','');
        },
        error:function (){}
	});

}
function dynamicinsertpopup(option){
	//alert("insert");
	jQuery.ajax({
        url: "views/new_from.php",
        data:'option='+option,
        type: "POST",
        success:function(data){
            $('#popup2').html(data);
            $('#kt_modal_KTDatatable_remote1').modal("show");
        },
        error:function (){}
    });
}
	function statuspopup(status,id){
  //alert("data");
 var table_name = document.getElementById('table_name').value;
  jQuery.ajax({
        url: "views/custom_insert_status_popup.php",
        data:'table='+table_name+'&status='+status+'&rid='+id,
        type: "POST",
        success:function(data){
            $('#popup1').html(data);
            $('#kt_modal_KTDatatable_remote').modal("show");
        },
        error:function (){}
    });
	}
function displaystausdrop(id,table,status_id){
	//alert("id");
	$.ajax({
        url: "ajax.php",
        data:'page=current_status_drop&id='+id+'&current_status='+status_id+'&table='+table,
        type: "POST",
        success:function(data){
        	console.log(data);
            $("#dropdownlist_"+id).html(data);
        },
        error:function (){}
	});
	
}
function listUserType(){
	var reg_invoit = $('input[name="reg_invoit"]:checked').val();
    $(".hideall").addClass('d-none');
    $("."+reg_invoit).removeClass('d-none');
    var available_record = 0;
    $("."+reg_invoit).each(function(index){
        available_record++;
    });
    if(available_record == 0){
      $("#available_record_error").removeClass('d-none');
    }else{
       $("#available_record_error").addClass('d-none');
    }
}

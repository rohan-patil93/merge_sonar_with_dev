window.addEventListener("load", function() {


    $(document).on("change", ".listUserType", function () {
        listUserType();
    });


    $(document).on("click", ".resendrequest", function () {
        var userid = $(this).data("userid");
        resendrequest(userid);
    });
    $(document).on("click", ".reSentPassword", function () {
        var userid = $(this).data("userid");
        reSentPassword(userid);
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
        if(reg_invoit=='1'){ 
            $(".register").removeClass('d-none');
			$(".invited").addClass('d-none');			
        }else if(reg_invoit=='2'){
            $(".invited").removeClass('d-none');
			$(".register").addClass('d-none');
        }else{
			$(".invited").removeClass('d-none');
			$(".register").addClass('d-none');
		}
}
function reSentPassword(resetpass_user_id){
  swal({
    title: "",
    text: 'Are you sure you want to reset password?',
    type: "warning",
    showCancelButton: true,
    confirmButtonClass: "btn-danger",
    confirmButtonText: "Yes",
    cancelButtonText: "No",
    closeOnConfirm: true,
    closeOnCancel: true
  },
  function(isConfirm) {
    if (isConfirm) {
        $.ajax({
          url: "ajax.php",
          data:'page=userpasswordreset&resetpass_user_id='+resetpass_user_id,
          type: "POST",
          success:function(data){
            console.log(data);
            var res = JSON.parse(data);
            if(res.result == "Y"){
              tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','User Password Successfully Reset','');
            }else{
              tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX','User Not Found Or Active','');
            }
          },
          error:function (){}
      });
    }
  });

  
}

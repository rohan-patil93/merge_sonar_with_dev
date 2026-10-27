//console.log('outside');
//window.addEventListener("load", function() {
//console.log('inside');
  $(document).on("click", ".submitDynamiPopUpForm", function() {
    submitDynamiPopUpForm();
  });
//});  

  function getStockBalanceAvailability(transaction_table, transaction_id){
      let retres = 0;
      $.ajax({
          url: 'ajax.php',
          type: "POST",
          async: false,
          data: 'page=getstockbalanceavailability&transaction_table='+transaction_table+'&transaction_id='+transaction_id,
          success: function(response) {
            console.log(response);
            var res = JSON.parse(response);
            if(res.errorthrow.errorcount != 0){
              var errormsg = "Item ";
              $.each(res.errorthrow.details, function(index, itemdet) {
                errormsg += itemdet.item_name+" has only "+itemdet.closing_qty+" qty available — you applied "+itemdet.sum_item_quantity+" qty. , ";
              });
              errormsg += "Please check stock before applying.";
              swal("", errormsg, "error");
              retres = 1;
            }else{
              retres = 0;
            }
          }
      })
      return retres;
    }
  	
    function submitDynamiPopUpForm(){
      var updated_statuse_transaction_table = $("#updatedstatusetransactiontable").val();
      var updated_statuse_transaction_id = $("#updatedstatusetransactionid").val();
      let returnresult = 0;
      if(updated_statuse_transaction_table == 'delivery_orders' || updated_statuse_transaction_table == 'sales_invoices' || updated_statuse_transaction_table == 'dekittings' || updated_statuse_transaction_table == 'kittings'){
        returnresult = getStockBalanceAvailability(updated_statuse_transaction_table,updated_statuse_transaction_id);
      }
      if(returnresult == 0){
        //$("#statuspopupbtn").prop('disabled', true);
        $("#statuspopupbtn").text("Please Wait........");
        $("#statuspopupbtn").addClass("disabled");
    		var formdata = $("#dynamic_status_forms").serialize();
    		console.log(formdata);
        if($("#hide_mobileview_id").val() =="Y"){ 
          var url ="../../ajax.php";
        }else{
           var url ="ajax.php";
        }
    		$.ajax({
    			url: url,
    			type: "POST",
    			data: 'page=submitstatusdynamicworkflow&'+formdata,
    			success: function(response) {
            console.log(response);
    				var res = JSON.parse(response);
            console.log(res);
            if(res.transaction_update.status=='sucess'){
                swal({
                  title: "",
                  text: 'Submission Successful',
                  type: "success",
                  showCancelButton: false,
                  confirmButtonClass: "btn-danger",
                  confirmButtonText: "Yes",
                  cancelButtonText: "No",
                  closeOnConfirm: true,
                  closeOnCancel: true
                },
                function(isConfirm) {
                  if (isConfirm) {
                     $('#kt_modal_KTDatatable_remote').modal('hide');
                      $(".pcoded-main-container").addClass('event_disabled');
                      window.location.href=window.location.href;
                  } 
                });
                $('#kt_modal_KTDatatable_remote').modal('hide');
            }else{
              $("#statuspopupbtn").text("Submit");
              $("#statuspopupbtn").removeClass("disabled");
            }
    			}
    		});
      }else{
        console.log('not working');
      }
  	}
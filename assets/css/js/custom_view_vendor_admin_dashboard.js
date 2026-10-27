 window.addEventListener("load", function () {
        $(document).on("click", ".createVendorRegisterToVendor", function () {
            var vendor_id = $(this).data("vendor-id");
            createVendorRegisterToVendor(vendor_id);
        });
        $(document).on("click", ".syncVendors", function () {
            syncVendors();
        });
    });
function changeMonth(valu){
        console.log(valu);
        if(valu == "all"){
            $(".showallemp").show('slow');
        }else{
            $(".showallemp").hide('slow');
            $(".showonlyemp_"+valu).show('slow');
        }
    }
    function assignTaskEmployee(emp_id){
        jQuery.ajax({
            url: "views/custom_onboarding_employee_task_assigns.php",
            data:'emp_id='+emp_id,
            type: "POST",
            success:function(data){
                $('#popupm').html(data);
                $("#kt_modal_KTDatatable_remotem").modal("show");
            },
            error:function (){}
        });
    }
    function createVendorRegisterToVendor(vendor_id){
    swal({
        title: "",
        text: 'Are you sure you want to create vendor?',
        type: "info",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Yes",
        cancelButtonText: "No",
        closeOnConfirm: true,
        closeOnCancel: true
    },
    function(isConfirm) {
        if (isConfirm) {
            var transaction_table = 'vendor_registers';
            var vtype = 'VR';
            jQuery.ajax({
                url: "ajax.php",
                data:'page=createvendorregistertovendor&transaction_table='+transaction_table+"&vendor_id="+vendor_id+"&vtype="+vtype,
                type: "POST",
                async:false,
                success:function(data){
                     tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Vendor Successfully Created','');
                     location.reload();
                },
                error:function (){}
            });
        }
    });
}
function syncVendors(){
    var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;"></div>';
    $('#popupm').html(loader);
    $("#kt_modal_KTDatatable_remotem").modal("show");
    jQuery.ajax({
        url: "ajax.php",
        data:'page=insertvendorandvendorregisterssynchapi',
        type: "POST",
        success:function(data){
            $("#kt_modal_KTDatatable_remotem").modal("hide");
            tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Data has been synced successfully.','');

        },
        error:function (){}
    });
}
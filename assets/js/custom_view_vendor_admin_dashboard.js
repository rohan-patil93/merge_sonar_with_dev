window.addEventListener("load", function () {
    $(document).on("click", ".createVendorRegisterToVendor", function () {
        var vendor_id = $(this).data("vendor-id");
        createVendorRegisterToVendor(vendor_id);
    });
    $(document).on("click", ".syncVendors", function () {
        syncVendors();
    });
    $(document).on("click", ".dynamicVendorAdminDashboard", function () {
        var current_page = $(this).data("current_page");
        dynamicVendorAdminDashboard(current_page);
    });
    $(document).on("keyup", "#entervendornamefind", function () {
        dynamicVendorAdminDashboard();
    });
});

function dynamicVendorAdminDashboard(current_page="1"){
    var htmloader = '<div class="text-center"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;"></div>';
    $("#dyanmictableshow").html(htmloader);
    var entervendornamefind = $("#entervendornamefind").val();
   // var filter_text = $("#dynamicfilterhideshow").val();
    var filter_dynamic_select = {};
    jQuery.ajax({
        url: "views/custom_view_vendor_admin_dashboard_ajax.php",
        data: "filterajax=Y&current_page="+current_page+"&entervendornamefind="+entervendornamefind,
        type: "POST",
        success:function(data){
            $("#dyanmictableshow").html(data);
        },
        error:function (){}
    });
}

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
            //$(".createVendorRegisterToVendor").text("Please Wait......");
            //$(".createVendorRegisterToVendor").addClass("disabled");    
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
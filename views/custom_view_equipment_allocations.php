<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$_SESSION['apply_js']['multiselest'] = "1";
$url = API."?mode=getequipmentallocations&pagetype=HOME&admin_type=".$_SESSION['admin_type']."&employee_id=".$_SESSION['emp_id'];
$response = callAPI($url);
$result = json_decode($response,1);
?>
<script type="text/javascript">
<?php if($_REQUEST['TAB1'] !=""){ ?>
        getBuildingDynamicPage('<?php echo $_REQUEST['TAB1']; ?>');
<?php } ?>
function showPopup(carddiv, menutype=""){
    $(".hidebuildingmenu").hide();
    $(".buildingmenu_"+menutype).show();
    $(".custom-popup-open").hide();
    $("."+carddiv).show('slow');
}
function closedPopup(){
    $(".custom-popup-open").hide('slow');
}

function ShowBuildingFloors(building_floor_id,eaid=""){
    jQuery.ajax({
        url: "views/custom_view_equipment_allocation_popup.php",
        data: "building_floor_id="+building_floor_id+"&eaid="+eaid,
        type: "POST",
        success:function(data){
            $('#dynamic_popup11').html(data);
            $('#kt_modal_dynamic_popup2').modal("show");
            $('.tooltip').remove();
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });
}
function getBuildingDetails(building_id=""){
     $("#kt_modal_dynamic_popup2").modal({
        backdrop: 'static',
        keyboard: false
    });
    jQuery.ajax({
        url: "views/custom_insert_buildings.php",
        data: "building_id="+building_id,
        type: "POST",
        success:function(data){
            $('#dynamic_popup11').html(data);
            $('#kt_modal_dynamic_popup2').modal("show");
              $(".js-example-basic-single").select2();
              $(".js-example-basic-multiple").select2();

        }
    });
}
function getBuildingFoolSpaceDetails(building_floor_id=""){
    var htmloader = '<div class="text-center"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
    $('#dynamic_popup11').html(htmloader);
    jQuery.ajax({
        url: "views/custom_insert_building_floors.php",
        data: "building_floor_id="+building_floor_id+'&ajaxpage=Y',
        type: "POST",
        success:function(data){
            $('#dynamic_popup11').html(data);
            $('#kt_modal_dynamic_popup2').modal("show");
            $(".js-example-basic-single").select2();
             $('.tooltip').remove();
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });
}
function getBuildingDynamicPage(pagetype=""){
    console.log(pagetype);
    if(pagetype !=""){
        updateUrlParameter('TAB1',pagetype);
    }
    var formdata;
    if($("#form_"+pagetype).serialize()){
        formdata = $("#form_"+pagetype).serialize();
    }
    var htmloader = '<div class="text-center"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
    $('#htmlajaxpage').html(htmloader);
    if(pagetype !=""){
        $(".removeactivetab").removeClass('active');
        $(".tab_"+pagetype).addClass('active');
    }
    jQuery.ajax({
        url: "views/custom_view_equipment_allocation_ajax.php",
        data: formdata+"&pagetype="+pagetype+"&getajax=Y&ajaxpagecall=Y",
        type: "POST",
        success:function(data){
            $('#htmlajaxpage').html(data);
            $(".js-example-basic-single").select2();
            $('.tooltip').remove();
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });
}

function insertEquipmentAllocation(building_floor_id,building_floor_space_id="",eaid="",eq_id="",eq_cat_id="",eq_type_id="",emp_id="",transaction_type){
    $("#kt_modal_dynamic_popup2").modal({
        backdrop: 'static',
        keyboard: false
    });
    jQuery.ajax({
        url: "views/custom_insert_equipment_allocations.php",
        data: "building_floor_id="+building_floor_id+"&building_floor_space_id="+building_floor_space_id+"&eaid="+eaid+"&eq_id="+eq_id+"&eq_cat_id="+eq_cat_id+"&eq_type_id="+eq_type_id+"&emp_id="+emp_id+"&transaction_type="+transaction_type+"&ajaxpage=Y",
        type: "POST",
        success:function(data){
            $('#dynamic_popup11').html(data);
            $('#kt_modal_dynamic_popup2').modal("show");
            $(".js-example-basic-single").select2();
            $('.tooltip').remove();
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });
}
function dynamicInsertPagePopUp(option,Rid=""){
     jQuery.ajax({
        url: "views/new_from.php",
        data:'option='+option+'&Rid='+Rid,
        type: "POST",
        success:function(data){
            $('#dynamic_popup11').html(data);
            $('#kt_modal_dynamic_popup2').modal("show");
        },
        error:function (){}
    });
    //   url: "views/custom_inbox_page_redirection.php",
    //   data:'option='+option+"&rid="+id+"&ajaxpage=Y",
    //   type: "POST",
}
function insertEquipemnt(eq_id=""){
     jQuery.ajax({
        url: "views/custom_insert_equipments.php",
        data:'eq_id='+eq_id,
        type: "POST",
        success:function(data){
            $('#dynamic_popup11').html(data);
            $('#kt_modal_dynamic_popup2').modal("show");
            $(".js-example-basic-single").select2();
            var elem = Array.prototype.slice.call(document.querySelectorAll('.js-equipemnt-allocation'));
            elem.forEach(function(html) {
                var switchery = new Switchery(html, { color: '#4099ff', jackColor: '#fff',size: 'medium' });
            });
        },
        error:function (){}
    });
}
function setLocationEquipments(eq_id=""){
    $("#kt_modal_dynamic_popup").modal({
        backdrop: 'static',
        keyboard: false
    });
    jQuery.ajax({
        url: "views/custom_view_equipment_allocation_location_popup.php",
        data:'eq_id='+eq_id,
        type: "POST",
        success:function(data){
            $('#dynamic_popup1').html(data);
            $('#kt_modal_dynamic_popup').modal("show");
            $(".js-example-basic-single").select2();
        },
        error:function (){}
    });
}
function setLocationAssets(eq_id=""){
    $("#kt_modal_dynamic_popup").modal({
        backdrop: 'static',
        keyboard: false
    });
    jQuery.ajax({
        url: "views/custom_view_asset_allocation_location_popup.php",
        data:'eq_id='+eq_id,
        type: "POST",
        success:function(data){
            $('#dynamic_popup1').html(data);
            $('#kt_modal_dynamic_popup').modal("show");
            $(".js-example-basic-single").select2();
        },
        error:function (){}
    });
}
function setLocationEmployee(emp_id=""){
    $("#kt_modal_dynamic_popup").modal({
        backdrop: 'static',
        keyboard: false
    });
    jQuery.ajax({
        url: "views/custom_view_equipment_allocation_location_popup.php",
        data:'emp_id='+emp_id,
        type: "POST",
        success:function(data){
            $('#dynamic_popup1').html(data);
            $('#kt_modal_dynamic_popup').modal("show");
            $(".js-example-basic-single").select2();
        },
        error:function (){}
    });
}
function showEquipmentLogs(eq_id){
     jQuery.ajax({
        url: "views/custom_view_equipment_logs_popup.php",
        data:'eq_id='+eq_id,
        type: "POST",
        success:function(data){
            $('#dynamic_popup1').html(data);
            $('#kt_modal_dynamic_popup').modal("show");
        },
        error:function (){}
    });
}
function insertEmployeePopUp(emp_id=""){
    $('#kt_modal_dynamic_popup').modal("show");
    var htmloader = '<div class="text-center"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
    $('#dynamic_popup1').html(htmloader);

    jQuery.ajax({
        url: "views/custom_insert_building_employee_popup.php",
        data:'emp_id='+emp_id,
        type: "POST",
        success:function(data){
            $('#dynamic_popup1').html(data);

            $(".js-example-basic-single").select2();
            $(".js-example-basic-single").select2({
                dropdownParent: $("#kt_modal_dynamic_popup")
            });
            $('.tooltip').remove();
            $('[data-bs-toggle="tooltip"]').tooltip();

            var elemdefault = document.querySelector('.js-single-switch');
            var switchery = new Switchery(elemdefault, { color: '#4099ff', jackColor: '#fff',size: 'small' });
        },
        error:function (){}
    });
}
function getEquipemntsWithSeat(pagetype,building_id){
    if(document.getElementById("waiticon_"+building_id)){
        $(".allshowbuldicon").show();
        $(".allshowbuldwaitmsg").hide();
        $("#waiticon_"+building_id).hide();
        $("#waitmsg_"+building_id).show();
    }
    $(".emptycss_building_selectio").css("border-bottom","none");
    $("#building_selection_"+building_id).css("border-bottom","5px solid #b0dc00");
    var htmloader = '<div class="text-center"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
    $('.booking-seat').html(htmloader);
    updateUrlParameter('TAB2',pagetype);
    jQuery.ajax({
        url: "views/custom_view_equipment_allocation_ajax.php",
        data: "pagetype="+pagetype+"&getajax=Y&ajaxpagecall=Y&building_id="+building_id,
        type: "POST",
        success:function(data){
            $('.booking-seat').html(data);
            $(".js-example-basic-single").select2();
             $('.tooltip').remove();
            $('[data-bs-toggle="tooltip"]').tooltip();
             $('html, body').animate({
                scrollTop: $("#ar_seat_id").offset().top
            }, 100); // Adjust the duration (1000ms = 1s)

            if(document.getElementById("waiticon_"+building_id)){
                $("#waiticon_"+building_id).show();
                $("#waitmsg_"+building_id).hide();
            }
        }
    });
}
function toAllocateEmployee(ea_id){
    $(".custom-popup-open").hide();
    $(".to_allocate_employee_"+ea_id).show();
    var htmloader = '<div class="text-center"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
    $(".to_allocate_employee_"+ea_id).html(htmloader);
    jQuery.ajax({
        url: "views/custom_view_equipment_allocation_ajax.php",
        data: "pagetype=ALLOCATEEMP&getajax=Y&ajaxpagecall=Y&equipment_allocation_id="+ea_id,
        type: "POST",
        success:function(data){
            $(".to_allocate_employee_"+ea_id).html(data);
           $('.tooltip').remove();
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });
}
function closeToAllocateEmployee(){
    $('.tooltip').remove();

    $(".custom-popup-open").hide('slow');
    $(".empty_popup_template").empty();
}
function allocateEmployeeSeat(equipment_allocation_id,emp_id=""){
    var msg = succesmsg = "";
    if(emp_id !=""){
        msg = '<?php echo $_SESSION['error_message']['9122']; ?> Allocate The Seat';
        succesmsg = '<?php echo $_SESSION['error_message']['9146']; ?>';
    }else{
        msg = '<?php echo $_SESSION['error_message']['9122']; ?> Un-Allocate The Seat';
        succesmsg = '<?php echo $_SESSION['error_message']['9147']; ?>';
    }
    swal({
        title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
        text: msg,
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
            jQuery.ajax({
                url: "ajax.php",
                data: "page=allocateemployeeseat&equipment_allocation_id="+equipment_allocation_id+"&emp_id="+emp_id,
                type: "POST",
                success:function(data){
                    closeToAllocateEmployee();
                    console.log(data);
                    var result = JSON.parse(data);
                    console.log('building_id - '+result.building_id);
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',succesmsg,'');
                     $('.tooltip').remove();
                    $('[data-bs-toggle="tooltip"]').tooltip();
                    if(result.building_id !=""){
                        getEquipemntsWithSeat('AR_SEAT',result.building_id);
                    }else{
                        toAllocateEmployee(equipment_allocation_id);
                    }
                }
            });
        }
    });
}

function toCubicleSpaceAllocateEmployee(bfs_id,event){
    closeToAllocateEmployee();
    $(".to_allocate_employee_"+bfs_id).show();

    console.log("event");
    console.log(event);
    const popup = document.getElementsByClassName('to_allocate_employee_'+bfs_id)[0];
    const baseContainer = document.getElementById('tophieghtcheck_1');

    const buttonRect = event.target.getBoundingClientRect();

    const baseRect = baseContainer.getBoundingClientRect();

    console.log("Start ------------ baseRect");
    console.log(baseRect);
    console.log("end ------------ baseRect");
    const buttonTop = buttonRect.top - baseRect.top;
    const buttonLeft = buttonRect.left - baseRect.left;

    const buttonWidth = buttonRect.width;
    const buttonHeight = buttonRect.height;
    console.log("buttonTop:"+buttonTop+"---"+"buttonLeft:"+buttonLeft+"---"+"buttonWidth:"+buttonWidth+"---"+"buttonHeight:"+buttonHeight+"---");

    const popupWidth = popup.offsetWidth;
    const popupHeight = popup.offsetHeight;

    console.log("popupWidth:"+popupWidth+"---"+"popupHeight:"+popupHeight);
    console.log("windowinnerWidth:"+window.innerWidth+"---"+"windowinnerHeight:"+window.innerHeight);
    console.log("windowscrollY:"+window.scrollY+"---"+"windowscrollX:"+window.scrollX);

    let popupTop, popupLeft;
    if (buttonTop + buttonHeight + popupHeight + 0 < window.innerHeight) {
        popupTop = buttonHeight + 0; // Below the button
         console.log(" step 1:"+popupTop);
    } else {
        popupTop = buttonHeight - popupHeight - 200; // Above the button
        console.log(" step 2:"+popupTop);
    }

    if (buttonLeft + popupWidth + 0 < (window.innerWidth-250)) {
        popupLeft = buttonWidth + 0; // Right of the button
         console.log("case 1:"+popupLeft);
    } else {
        popupLeft = buttonWidth - popupWidth - 0; // Left of the button
         console.log("case 2:"+popupLeft);
    }
    // Adjust popup position to prevent overflow

    popup.style.top = `${popupTop}px`;
    popup.style.left = `${popupLeft}px`;
    popup.style.display = 'block';

    var htmloader = '<div class="text-center"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
    $(".to_allocate_employee_"+bfs_id).html(htmloader);
    jQuery.ajax({
        url: "views/custom_view_equipment_allocation_ajax.php",
        data: "pagetype=CSA_ALLOCATION_EMP&getajax=Y&ajaxpagecall=Y&building_floor_space_id="+bfs_id,
        type: "POST",
        success:function(data){
            $(".to_allocate_employee_"+bfs_id).html(data);
            $('.tooltip').remove();
            $('[data-bs-toggle="tooltip"]').tooltip();


        }
    });
}

function allocateEmployeecubicleSpace(building_floor_space_id,building_floor_space_allocation_id="",emp_id="",reserve_pool_id="",switchanothersapace= ""){
    var msg = succesmsg = "";
    var space_allocation_popup = "N";
    console.log("after --- "+space_allocation_popup);
    var space_allocation_popup_building_floor_id = "";
    if(document.getElementById("space_allocation_popup")){
        space_allocation_popup = "Y";
        var space_allocation_popup_building_floor_id = $("#space_allocation_popup_building_floor_id").val();
    }
    if(switchanothersapace =="0"){
        msg = '<?php echo $_SESSION['error_message']['9169']; ?>';
        succesmsg = '<?php echo $_SESSION['error_message']['9170']; ?>';
    }else if(emp_id !="" || reserve_pool_id !=""){
        msg = '<?php echo $_SESSION['error_message']['9150']; ?>';
        succesmsg = '<?php echo $_SESSION['error_message']['9152']; ?>';
    }else{
        msg = '<?php echo $_SESSION['error_message']['9151']; ?>';
        succesmsg = '<?php echo $_SESSION['error_message']['9153']; ?>';
    }
    swal({
        title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
        text: msg,
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
            closeToAllocateEmployee();
            jQuery.ajax({
                url: "ajax.php",
                data: "page=insertbuildingfloorspaceallocations&building_floor_space_id="+building_floor_space_id+"&emp_id="+emp_id+"&building_floor_space_allocation_id="+building_floor_space_allocation_id+"&reserve_pool_id="+reserve_pool_id,
                type: "POST",
                success:function(data){

                    console.log(data);
                    var result = JSON.parse(data);
                    console.log('building_id - '+result.building_id);
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',succesmsg,'');
                    $('.tooltip').remove();
                    $('[data-bs-toggle="tooltip"]').tooltip();
                    if(space_allocation_popup == "Y"){
                        ShowBuildingFloorSpaceAllocations(space_allocation_popup_building_floor_id);
                    }else if(result.building_id !=null){
                        getEquipemntsWithSeat('CSA_ALLOCATION',result.building_id);
                        console.log('CSA_ALLOCATION --- ');
                    }else{
                        console.log('Not  === CSA_ALLOCATION --- ');
                        getBuildingDynamicPage('B')
                    }
                }
            });
        }
    });
}
function closeBookingSeat(){
    $(".emptycss_building_selectio").css("border-bottom","none");
     $('.booking-seat').html('');
}
function selectSpaceallocatedemployee(emp_id){
    if(emp_id !=""){
        $(".spaceallocated_allemp").hide();
        $("#spaceallocated_emp"+emp_id).show();
    }else{
       $(".spaceallocated_allemp").show();
    }
}
function ShowBuildingFloorSpaceAllocations(building_floor_id,building_floor_space_allocation_id=""){
    var htmloader = '<div class="text-center"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
    $('#dynamic_popup11').html(htmloader);
    jQuery.ajax({
        url: "views/custom_view_building_floor_space_allocations.php",
        data: "building_floor_id="+building_floor_id+"&building_floor_space_allocation_id="+building_floor_space_allocation_id+"&ajaxpage=Y",
        type: "POST",
        success:function(data){
            $('#dynamic_popup11').html(data);
            $('#kt_modal_dynamic_popup2').modal("show");
            $(".js-example-basic-single").select2();
             $('.tooltip').remove();
            $('[data-bs-toggle="tooltip"]').tooltip();
            if(building_floor_space_allocation_id !=""){
                $('#kt_modal_dynamic_popup2').animate({
                    scrollTop: $(".active_employee_space").offset().top
                }, 100);
            }
        }
    });
}

function getBuildingFoolSpaceDetailsNew(building_floor_id="",order_by=""){
    var htmloader = '<div class="text-center"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
    $('#dynamic_popup11').html(htmloader);
    jQuery.ajax({
        url: "views/custom_insert_building_floors_new.php",
        data: "building_floor_id="+building_floor_id+"&ORDERBY="+order_by+"&ajaxpage=Y",
        type: "POST",
        success:function(data){
            $('#dynamic_popup11').html(data);
            $('#kt_modal_dynamic_popup2').modal("show");
            $(".js-example-basic-single").select2();
             $('.tooltip').remove();
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });
}
function getSearchEmployeeLocations(valu){
    if(valu.length >= 3){
        var htmloader = '<div class="text-center"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $('#search_result_employee').html(htmloader);
        $("#search_result_employee").show('slow');
        jQuery.ajax({
            url: "ajax.php",
            data: "page=getemployeelistwithbuildingdetailfilter&value="+valu,
            type: "POST",
            success:function(data){
                $("#search_result_employee").html(data);
            }
        });
    }else{
        $("#search_result_employee").hide();
    }
}
function AddAssets(asset_id=""){
        jQuery.ajax({
            url: "views/custom_insert_asset_popup.php",
            data: "asset_id="+asset_id,
            type: "POST",
            success:function(data){
                $('#dynamic_popup11').html(data);
                $('#kt_modal_dynamic_popup2').modal("show");
                $( ".datepicker" ).datepicker({
                    showOn: "both",
                    buttonText: "<i class='ti-calendar text-dark f-20 f-w-800 m-r-10'></i>",
                    dateFormat: date_format,
                    yearRange: "-60:+10",
                    setDate: new Date(),
                    changeMonth: true,
                    changeYear: true,
                    beforeShow: function (input, inst) {
                            var rect = input.getBoundingClientRect();
                            setTimeout(function () {
                                inst.dpDiv.css({ top: rect.top + 10, left: rect.left + 0 });
                            }, 0);
                        }
                    });
            },
            error:function (){}
        });
    }

function ShowAssetsLocations(building_floor_id,asset_allocations_id=""){
    var htmloader = '<div class="text-center"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
    $('#dynamic_popup11').html(htmloader);
    jQuery.ajax({
        url: "views/custom_view_asset_allocations.php",
        data: "building_floor_id="+building_floor_id+"&asset_allocations_id="+asset_allocations_id+"&ajaxpage=Y",
        type: "POST",
        success:function(data){
            $('#dynamic_popup11').html(data);
            $('#kt_modal_dynamic_popup2').modal("show");
            $(".js-example-basic-single").select2();
             $('.tooltip').remove();
            $('[data-bs-toggle="tooltip"]').tooltip();
            if(building_floor_space_allocation_id !=""){
                $('#kt_modal_dynamic_popup2').animate({
                    scrollTop: $(".active_employee_space").offset().top
                }, 100);
            }
        }
    });
}
    function insertAssetsAllocation(building_id="",building_floor_id,asset_id="",asset_allocation_id=""){
    $("#kt_modal_dynamic_popup2").modal({
        backdrop: 'static',
        keyboard: false
    });
    jQuery.ajax({
        url: "views/custom_insert_equipment_allocations_new.php",
        data: "building_id="+building_id+"&building_floor_id="+building_floor_id+"&asset_id="+asset_id+"&asset_allocation_id="+asset_allocation_id,
        type: "POST",
        success:function(data){
            $('#dynamic_popup11').html(data);
            $('#kt_modal_dynamic_popup2').modal("show");

            $(".js-example-basic-single").select2();
            $('.tooltip').remove();
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });
}

function insertAssetVerifications(){
    jQuery.ajax({
        url: "views/custom_insert_asset_verifications_popup.php",
        data: "building_id=vfd",
        type: "POST",
        success:function(data){
            $('#dynamic_popup11').html(data);
            $('#kt_modal_dynamic_popup2').modal("show");
            $(".js-example-basic-single").select2();
            $('.tooltip').remove();
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });
}

function insertAssetallocationlist(building_floor_id,building_floor_space_id,asset_id,equipment_allocation_id=""){
    jQuery.ajax({
        url: "ajax.php",
        data: 'page=insertequipmentallocations&building_floor_id='+building_floor_id+'&asset_id='+asset_id+'&equipment_allocation_id='+equipment_allocation_id+'&building_floor_space_id='+building_floor_space_id,
        type: "POST",
        success:function(response){
            var result = JSON.parse(response);
            if(result.error != "0"){
                 tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX',result.error_message,'');
             }else{
                if(equipment_allocation_id == ''){
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3000']; ?>','');
                    $(".assetallocationwithbtnchnage_"+asset_id).html('<button type="button" class="btn p-0 border-0 bg-transparent ti-check text-success f-w-700" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Click To Un-Allocate Asset" onclick="insertAssetallocationlist(\''+building_floor_id+'\',\''+building_floor_space_id+'\',\''+asset_id+'\')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> </button>');
                }else{
                    $(".assetallocationwithbtnchnage_"+asset_id).html('<button type="button" class="btn p-0 border-0 bg-transparent ti-trash text-danger f-w-700" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Click To Allocate Asset" onclick="insertAssetallocationlist(\''+building_floor_id+'\',\''+building_floor_space_id+'\',\''+asset_id+'\',\''+result.equipment_allocation_id+'\')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> </button>');
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3002']; ?>','');
                }
            }
        }
    });
}

function approvedSpaceAllocations(building_floor_space_allocation_id){
    swal({
        title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
        text: "Are you sure you want to allocate this cubicle space",
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
            jQuery.ajax({
                url: "ajax.php",
                data: "page=approvedapaceallocationsstatus&building_floor_space_allocation_id="+building_floor_space_allocation_id,
                type: "POST",
                success:function(data){
                    console.log(data);
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['9059']; ?>','');
                    getBuildingDynamicPage('HOME');

                }
            });
        }
    });
}

</script>
<div class="card">
    <div class="card-body">
        <ul class="nav md-tabs nav-bold nav-tabs-line nav-line-tabs-3x border-transparent fs-5 fw-bolder flex-nowrap responsive-tab My_Informarion_Tab infisuite_tab">
            <li class="nav-item">
                <a class="nav-link <?php echo ($_REQUEST['TAB1'] =="HOME" || $_REQUEST['TAB1'] =="")?"active":""; ?> removeactivetab tab_HOME" data-bs-toggle="tab" href="#homes" role="tab" aria-selected="true" onclick="getBuildingDynamicPage('HOME')">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($_REQUEST['TAB1'] =="B")?"active":""; ?> removeactivetab tab_B" data-bs-toggle="tab" href="#itemcategory" role="tab" aria-selected="true" onclick="getBuildingDynamicPage('B')">Buildings</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($_REQUEST['TAB1']=="BSEM")?"active":""; ?> removeactivetab tab_BSEM" data-bs-toggle="tab" href="#costcenter" role="tab" aria-selected="true" onclick="getBuildingDynamicPage('BSEM')">Employees</a>
            </li>
            <?php if($_SESSION['working_role_short_code'] != SPACE_LOCATION_ADMIN_ROLE){ ?>
            <li class="nav-item">
                <a class="nav-link <?php echo ($_REQUEST['TAB1']=="REPORT")?"active":""; ?> removeactivetab tab_REPORT" data-bs-toggle="tab" href="#costcenter" role="tab" aria-selected="true" onclick="getBuildingDynamicPage('REPORT')">Report</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($_REQUEST['TAB1']=="DETAILREPORT")?"active":""; ?> removeactivetab tab_DETAILREPORT" data-bs-toggle="tab" href="#costcenter" role="tab" aria-selected="true" onclick="getBuildingDynamicPage('DETAILREPORT')">Detail Report</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($_REQUEST['TAB1']=="ASSETS")?"active":""; ?> removeactivetab tab_ASSETS" data-bs-toggle="tab" href="#costcenter" role="tab" aria-selected="true" onclick="getBuildingDynamicPage('ASSETS')">Assets</a>
            </li>
        <?php } ?>


        </ul>
    </div>
</div>


<div id="htmlajaxpage">
<?php
    if(file_exists("views/custom_view_equipment_allocation_ajax.php")){
        include_once "views/custom_view_equipment_allocation_ajax.php";
    }else{
        include_once "custom_view_equipment_allocation_ajax.php";
    }
?>
</div>


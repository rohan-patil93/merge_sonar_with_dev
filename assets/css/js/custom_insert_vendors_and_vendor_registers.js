
window.addEventListener("load", function() {
    if ($("#hide_get_tab").val() == "task_tab") {
        getAjaxDynamicPage('custom_insert_transaction_activity_schedules', 'task_tab');
    }

    $(document).on("click", ".editVendorProfile", function () {
        var functiontype = $(this).data("functiontype");
        editVendorProfile(functiontype);
    });
    
    $(document).on("change", ".selectedcategory", function () {
        var selected_id = $(this).val();
       var element_id = $(this).data("element_id");
       var table_name = $(this).data("table_name");
       var field_name = $(this).data("field_name");
       var targetelement = $(this).data("targetelement");
        selectedcategory(selected_id, element_id, table_name, field_name, targetelement);
    });
    

    $(document).on("click", ".createVendorRegisterToVendor", function () {
        createVendorRegisterToVendor();
    });
    

    $(document).on("click", ".getAjaxDynamicPage", function () {
        var option = $(this).data("trans-table");
        var divid = $(this).data("trans-tab");
        getAjaxDynamicPage(option,divid);
    });

    $(document).on("click", ".updateUrlParameter_tab", function () {
        var param = $(this).data("param");
        var paramvalue = $(this).data("paramvalue");
        updateUrlParameter(param,paramvalue);
    });


     $(document).on("change", ".onchangeWorkManufracturerFacilityAnswer", function () {
        var thisval = $(this).val();
        onchangeWorkManufracturerFacilityAnswer(thisval);
    });
    
     $(document).on("change", ".onchangeHoldOriginalPassportAnswer", function () {
        var thisval = $(this).val();
        onchangeHoldOriginalPassportAnswer(thisval);
    });
    
      $(document).on("change", ".onchangeSupplyChainResponsibilityAnswer", function () {
        var thisval = $(this).val();
        var seqval = $(this).data("seqval");
        onchangeSupplyChainResponsibilityAnswer(thisval,seqval);
    });
    

    $(document).on("click", ".checkInstructions", function () {
        checkInstructions();
    });

    $(document).on("click", ".getVendorSummary", function () {
        getVendorSummary();
    });


     $(document).on("click", ".SubmitForVendorCreation", function () {
        SubmitForVendorCreation();
    });

     $(document).on("click", ".UpdateSendVerificationProfileStatus", function () {
        UpdateSendVerificationProfileStatus();
    });


      $(document).on("click", ".vendorProfileUpdateStatus", function () {
        vendorProfileUpdateStatus();
    });
       $(document).on("click", ".cancelComment", function () {
        cancelComment();
    });
     

    $(document).on("click", ".showComment", function () {
            
        var showCommenttype = $(this).attr("data-showcommenttype");
       showComment(showCommenttype);
    });

       
    $(document).on("click", ".verificationVendorDetails", function () {
        verificationVendorDetails();
    });          
    $(document).on("click", ".MSMEConfirmationBTN", function () {
        var transaction_table = $("#hid_transaction_table").val();
        var vendor_id = $("#hid_vendor_id").val();
        jQuery.ajax({
            url: "ajax.php",
            data: 'page=msmeconfirmationupdate&transaction_table='+transaction_table+'&transaction_id='+vendor_id+'&msme_confirmation='+$('#msme_confirmation').val()+"&msme_confirmation_msme_number="+$('#msme_confirmation_msme_number').val()+"&msme_confirmation_remarks="+$('#msme_confirmation_remarks').val(),
            type: "POST",
            success: function(data) {
                tostarnotify('top', 'right', 'fa fa-thumbs-up', 'form-label label label-success', 'animated flipInX', 'animated flipOutX', 'Successfully Updated', '');
                location.reload();
            }
        })
        
    }); 
});

    function showUpdateChecklist(id) {
        $(".custom-popup-open").hide();
        $(".custom-popup-slide-updatechecklist-" + id).show('slow');
    }

    function closeSmallPopUp() {
        $(".custom-popup-open").hide('slow');
    }
    function editVendorProfile(edittype = "") {


        var htmloader = '<div class="text-center"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;"></div>';
        $('#ajaxvendorprofile').html(htmloader);
        var transaction_table = $("#hid_transaction_table").val();
        var vendor_id = $("#hid_vendor_id").val();
        var vtype = $("#hid_vtype").val();
        var savetext = "'EDIT'";
        jQuery.ajax({
            url: "views/custom_insert_vendors_and_vendor_registers_ajax.php",
            data: 'transaction_table=' + transaction_table + "&vendor_id=" + vendor_id + "&ajaxcall=Y&edittype=" + edittype + "&vtype=" + vtype,
            type: "POST",
            success: function(data) {
                $("#ajaxvendorprofile").html(data);
                $("#chnagebtndiv").html();
                $(".js-example-basic-single").select2();
                if (edittype == "") {
                    $("#chnagebtndiv").html('<button class="btn btn-primary editVendorProfile" data-functiontype = "' + savetext + '" ><i class="ti-pencil text-white"></i> Edit</button>');
                } else {
                    $("#chnagebtndiv").html('<button class="btn btn-danger editVendorProfile" data-functiontype = ""><i class="ti-close text-white"></i> Cancel</button>');
                }
                $(".datepicker").datepicker({
                    showOn: "both",
                    buttonText: "<i class='ti-calendar'></i>",
                    dateFormat: date_format,
                    changeMonth: true,
                    changeYear: true
                 }).next("button.ui-datepicker-trigger").html("<i class='ti-calendar text-dark f-12 f-w-800 m-r-10'></i>");
   
                var elem = Array.prototype.slice.call(document.querySelectorAll('.js-switch-vendor'));
                elem.forEach(function(html) {
                    var switchery = new Switchery(html, {
                        color: '#4099ff',
                        jackColor: '#fff'
                    });
                });

            },
            error: function() {}
        });

        //$("#ajaxvendorprofile").html();
    }

    function addedurows() {
        var date_format = "yy-mm-dd";
        var count = $(".itemRow").length;
        count = count + 1;
        jQuery(document).ready(function() {
            KTBootstrapDatepicker.init();
        });
        var htmlRows = '';
        htmlRows += '<tr class="">';

        htmlRows += '<td>' + count + '</td>';
        htmlRows += '<td><input type="text" name="ag_type[]"  class="form-control rowcount itemRow " autocomplete="off">  </td>';
        htmlRows += '<td><input type="text" name="ag_number[]"  class="form-control " autocomplete="off"> </td>';
        htmlRows += '<td><input type="text" name="ag_sin_date[]"  class="form-control datepicker"  autocomplete="off">  </td>';
        htmlRows += '<td><input type="text" name="ag_ex_date[]"  class="form-control datepicker"  autocomplete="off">  </td>';
        //htmlRows += '<td><input type="text" name="score[]"  class="form-control " autocomplete="off">  </td>';   
        htmlRows += '<td><div class="image-input col-lg-3" id="kt_image_5" style="display: inherit; margin-top: -2px;"><label class="btn-hover-text-primary" data-action="change" data-toggle="tooltip" title="" data-original-title="Upload" ><i class="ti-cloud-up f-20 text-primary f-30 f-w-800" data-toggle="tooltip" title="Upload"></i><input type="file" name="agreement_file[]" /><input type="hidden" name="hidden_agreement_file[]" value=""/></label></div></td>';
        // htmlRows +='<td><div class="image-input image-input-empty image-input-outline" id="kt_image_5" ><label class="btn-hover-text-primary mt-2" data-action="change" data-toggle="tooltip" title="" data-original-title="Change avatar" > <i class="ti-cloud-up f-20 text-primary f-30 f-w-800" data-toggle="tooltip" title="Upload"></i></div></td>'
        htmlRows += '</tr>';
        $('#edutable').append(htmlRows);
        $(".js-example-basic-single").select2();
        $(".datepicker").datepicker({
            showOn: "both",
            buttonText: "<i class='ti-calendar'></i>",
            dateFormat: date_format,
            changeMonth: true,
            changeYear: true
        }).next("button.ui-datepicker-trigger").html("<i class='ti-calendar text-dark f-12 f-w-800 m-r-10'></i>");
   
    }

    function selectedcategory(selected_id, element_id, table_name, field_name, targetelement) {
        var selectedvalues = selected_id;
        //alert(selected_id);

        if (table_name == 'states' || table_name == 'cities') {
            $.ajax({
                url: "ajax.php",
                data: 'page=getselectedlist&table_name=' + table_name + '&id=' + selectedvalues + '&field=' + field_name,
                type: "POST",
                success: function(response) {
                    var response = JSON.parse(response);
                    console.log(response.length);
                    console.log(response);
                    var select = document.getElementById(element_id);
                    if (select) {
                        select.innerHTML = "";
                    }
                    //document.getElementById(element_id).innerHTML = "";
                    for (var i = 0; i < response.length; i++) {
                        var el = document.createElement("option");
                        if (table_name == 'states') {
                            el.textContent = response[i].state_name;
                            el.value = response[i].id;

                        }
                        if (table_name == 'cities') {
                            el.textContent = response[i].city_name;
                            el.value = response[i].id;

                        }
                        select.add(el);
                    }
                }
            });
        }

    }

    function createVendorRegisterToVendor() {
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
                    var transaction_table = $("#hid_transaction_table").val();
                    var vendor_id = $("#hid_vendor_id").val();
                    var vtype = $("#hid_vtype").val();

                    jQuery.ajax({
                        url: "ajax.php",
                        data: 'page=createvendorregistertovendor&transaction_table=' + transaction_table + "&vendor_id=" + vendor_id + "&vtype=" + vtype,
                        type: "POST",
                        async: false,
                        success: function(data) {
                            tostarnotify('top', 'right', 'fa fa-thumbs-up', 'form-label label label-success', 'animated flipInX', 'animated flipOutX', 'Vendor Successfully Created', '');
                            location.reload();
                        },
                        error: function() {}
                    });
                }
            });
    }

    function getAjaxDynamicPage(option, divid, pagetype = "", rid = "") {
        console.log(rid);
        updateUrlParameter('T1', divid);

        var transaction_table = $("#hid_transaction_table").val();
        var transaction_id = $("#hid_vendor_id").val();
        var vtype = $("#hid_vtype").val();
        var currentUrl = window.location.href;
        var urlParts = currentUrl.split('?');
        var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;"></div>';
        $("#" + divid).html(loader);
        $.ajax({
            url: "views/" + option + ".php",
            data: 'option=' + option + '&ajaxpage=Y&transaction_table=' + transaction_table + '&transaction_id=' + transaction_id + '&' + urlParts['1'],
            type: "POST",
            async: true,
            success: function(data) {
                $('#' + divid).html(data);
                $('[data-bs-toggle="tooltip"]').tooltip();
                if (rid != "") {
                    getCheckList(rid);
                }
            },
            error: function() {}
        });
    }

    // function onchangeVRQAnswer(valuess) {
    //     if (valuess == 'Y') {
    //         $(".vrq_extra").removeClass('d-none');
    //     } else {
    //         $(".vrq_extra").addClass('d-none');
    //     }
    // }

    function onchangeWorkManufracturerFacilityAnswer(valuess) {
        if (valuess == 'Y') {
            $(".work_manufacturer_facility").removeClass('d-none');
            $(".supplychainresponsibility_5").addClass('d-none');

        } else {
            $(".work_manufacturer_facility").addClass('d-none');
            $(".supplychainresponsibility_5").removeClass('d-none');
        }
    }

    function onchangeHoldOriginalPassportAnswer(valuess) {
        if (valuess == 'Y') {
            $(".hold_original_passport").removeClass('d-none');
        } else {
            $(".hold_original_passport").addClass('d-none');
        }
    }

    function onchangeAssociatedAnswer(valuess) {
        if (valuess == 'Y') {
            $(".associated_team").removeClass('d-none');
        } else {
            $(".associated_team").addClass('d-none');
        }
    }

    function onchangeSupplyChainResponsibilityAnswer(valuess, seq) {
        if (seq == '3') {
            if (valuess == 'Y') {
                $(".supplychainresponsibility_7").addClass('d-none');
            } else {
                $(".supplychainresponsibility_7").removeClass('d-none');
            }
        }
    }

    function checkInstructions() {
         var transaction_table = $("#hid_transaction_table").val();
        var transaction_id = $("#hid_vendor_id").val();

        $("#instructions_checkbox_div").addClass('event_disabled');
        $.ajax({
            url: "ajax.php",
            data: 'page=checkvendorinstructions&transaction_table=' + transaction_table + '&transaction_id=' + transaction_id,
            type: "POST",
            async: true,
            success: function(data) {
                //console.log(data);
                location.reload();
            },
            error: function() {}
        });
    }

    function getVendorSummary() {
        var transaction_table = $("#hid_transaction_table").val();
        var transaction_id = $("#hid_vendor_id").val();
        var vtype = $("#hid_vtype").val();
       jQuery.ajax({
            url: "views/custom_view_vendors_popup.php",
            data: "transaction_table=" + transaction_table + "&transaction_id=" + transaction_id + "&vtype=" + vtype,
            type: "POST",
            success: function(data) {
                $('#dynamic_popup11').html(data);
                $('#kt_modal_dynamic_popup2').modal("show");
                //$(".js-example-basic-single").select2();
            }
        });
    }

    function SubmitForVendorCreation() {
        var transaction_table = $("#hid_transaction_table").val();
        var vendor_id = $("#hid_vendor_id").val();
        var vtype = $("#hid_vtype").val();
        jQuery.ajax({
            url: "views/custom_insert_vendors_and_vendor_registers_ajax.php",
            data: 'transaction_table=' + transaction_table + "&vendor_id=" + vendor_id + "&ajaxcall=Y&edittype=SendVerificationProfile&vtype=" + vtype,
            type: "POST",
            success: function(data) {
                $('#dynamic_popup11').html(data);
                $('#kt_modal_dynamic_popup2').modal("show");
            }
        });
    }

    function UpdateSendVerificationProfileStatus() {
        swal({
                title: "",
                text: 'Do You Want to Submit The Details For Approval?',
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Submit",
                cancelButtonText: "Cancel",
                closeOnConfirm: true,
                closeOnCancel: true
            },
            function(isConfirm) {
                if (isConfirm) {
                    $("#popsubmitforreview_btn").text("Please Wait........");
                    $("#popsubmitforreview_btn").addClass("disabled");
                    var transaction_table = $("#hid_transaction_table").val();
                    var transaction_id = $("#hid_vendor_id").val();
                    var vtype = $("#hid_vtype").val();
                    $.ajax({
                        url: "ajax.php",
                        data: 'page=updatesendverificationprofilestatus&transaction_table=' + transaction_table + '&transaction_id=' + transaction_id,
                        type: "POST",
                        async: true,
                        success: function(data) {
                            //console.log(data);
                            window.location.href = window.location.href;
                        },
                        error: function() {}
                    });
                }
            });

    }

    function vendorProfileUpdateStatus() {
        var status = $("#selected_check_box").val();
         var transaction_table = $("#hid_transaction_table").val();
        var transaction_id = $("#hid_vendor_id").val();
        var vtype = $("#hid_vtype").val();
        var profile_review_comments = $("#profile_review_comments").val();
        $("#profile_review_comments_error").hide();
        if (profile_review_comments == "") {
            $("#profile_review_comments_error").show();
        } else {
            $("#profile_review_comment_div").show('slow');
            //$("#instructions_checkbox_div").addClass('event_disabled');
            if (status == "Y") {
                var extramsg = " Recommend The Vendor";
            } else {
                var extramsg = " Object The Vendor";
            }
            swal({
                    title: "",
                    text: 'Are You Sure ' + extramsg,
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
                            data: 'page=updatevendorprofileupdatestatus&transaction_table=' + transaction_table + '&transaction_id=' + transaction_id + '&status=' + status + '&profile_review_comments=' + profile_review_comments,
                            type: "POST",
                            async: true,
                            success: function(data) {
                                console.log(data);
                                location.reload();
                            },
                            error: function() {}
                        });
                    } else {
                        //location.reload();
                    }
                });
        }

    }

    function cancelComment() {
        $('.coomment_check_box').prop('checked', false);
        $("#selected_check_box").val('');
        $("#profile_review_comment_div").addClass('d-none');
        $("#dispabled_div").css('pointer-events', 'all')
    }

    function showComment(valu) {
        $("#dispabled_div").css('pointer-events', 'none');
        $("#profile_review_comment_div").removeClass('d-none');
        $("#selected_check_box").val(valu);

    }

    function verificationVendorDetails(verification_type = "") {

        var transaction_table = $("#hid_transaction_table").val();
        var vendor_id = $("#hid_vendor_id").val();
        var vtype = $("#hid_vtype").val();

        jQuery.ajax({
            url: "views/custom_insert_vendor_verifications_popup.php",
            data: 'transaction_table=' + transaction_table + "&vendor_id=" + vendor_id + "&vtype=" + vtype + '&verification_type=' + verification_type,
            type: "POST",
            success: function(data) {
                $('#dynamic_popup1').html(data);
                $('#kt_modal_dynamic_popup').modal("show");
            }
        });
    }

    function selectItemsWithParty(item_id, type, party_item_id = "") {
        var transaction_table = $("#hid_transaction_table").val();
        var vendor_id = $("#hid_vendor_id").val();
        if (type == "REMOVE") {
            swal({
                    title: "",
                    text: "Do you want to proceed with Un-allocating the Item?",
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
                            type: "POST",
                            data: 'page=insertvendorcustomeritems&transaction_table=' + transaction_table + '&transaction_id=' + transaction_id + '&party_item_id=' + party_item_id + '&type=' + type,
                            success: function(response) {
                                console.log(response);
                                tostarnotify('top', 'right', 'fa fa-thumbs-up', 'form-label label label-success', 'animated flipInX', 'animated flipOutX', "Item Un-Allocated Successfully", '');
                                getVendorCustomerItems();
                            }
                        });
                    }
                });

        } else {
            jQuery.ajax({
                url: "views/custom_insert_vendor_customer_item_popup.php",
                data: 'transaction_table=' + transaction_table + "&transaction_id=" + transaction_id + "&item_id=" + item_id + '&type=' + type,
                type: "POST",
                success: function(data) {
                    $('#dynamic_popup1').html(data);
                    $('#kt_modal_dynamic_popup').modal("show");
                }
            });
        }

    }

    function getVendorCustomerItems() {
        var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;"></div>';
        $("#vendor_items").html(loader);
        var transaction_table = $("#hid_transaction_table").val();
        var transaction_id = $("#hid_vendor_id").val();
        jQuery.ajax({
            url: "views/custom_list_vendor_customer_items.php",
            data: 'transaction_table=' + transaction_table + "&transaction_id=" + transaction_id,
            type: "POST",
            success: function(data) {
                $('#vendor_items').html(data);
                $(".js-example-basic-single").select2();
                $('[data-bs-toggle="tooltip"]').tooltip();
            }
        });
    }
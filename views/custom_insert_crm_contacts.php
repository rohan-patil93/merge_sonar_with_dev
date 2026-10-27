<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$rid = $_POST['rid'];
$transaction_table = "crm_contacts";
$crm_history_id = $_POST['crm_history_id'];
$apiusrl = API."?mode=getcrmleads&transaction_table=".$transaction_table."&rid=".$rid;
$page_list = callAPI($apiusrl);
$res = json_decode($page_list,1);
$transaction = $res['transaction'];
$crm_general_codes = $res['crm_general_codes'];
$application_users = $res['application_users'];
$application_users_role  =$res['application_users_role'];
$countries =$res['countries'];
$states =$res['states'];
$cities =$res['cities'];
$crm_companies =$res['crm_companies'];
$crm_campaigns = $res['crm_campaigns'];
$crm_lead_types =$res['crm_lead_types'];
$crmleaddetailstypes = array();
foreach($crm_lead_types as $crmlt){
    if($crmlt['type'] == 2){
        $crmleaddetailstypes[$crmlt['id']] =$crmlt;
    }
}
$crm_lead_details = $res['crm_lead_details'];
foreach($crm_lead_details as $crmhist){
    if($crm_history_id == $crmhist['id']){
        $task_crm_history = $crmhist;
    }
}
?>
<script type="text/javascript">
    function pageCallHistories(transaction_table,rid){
        var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $("#crmleadhistories").html(loader);
        jQuery.ajax({
            url: "views/custom_insert_crm_lead_histories.php",
            data: "rrid="+rid+"&transaction_table="+transaction_table,
            type: "POST",
            success:function(data){
                $("#crmleadhistories").html(data);

            }
        });
    }
    function submitLeadForm(){
        var validations = ajaxFormSubmitValidation();
        if(validations == 0){
            $(".sbt_loading_en_dis").text("Please Wait........");
            $(".sbt_loading_en_dis").attr("disabled", true);
            var crm_lead_id = $("#crm_lead_id").val();
            var formData = new FormData();
            formData.append("page", "insertcrmleads");
            var dataArray = $("#submit_lead_form").serializeArray(),
            dataObj = {
};
            $(dataArray).each(function(i, field){
              formData.append(field.name, field.value);
            });
            var display_image = document.getElementById('display_image').files[0];
            formData.append("file",display_image);
            jQuery.ajax({
                url: "ajax.php",
                data: formData,
                type: "POST",
                contentType: false,
                processData: false,
                //async:false,
                success:function(data){
                    console.log(data);
                    var jsonres = JSON.parse(data);
                    console.log(jsonres.res.status);
                    if(jsonres.res.status == "sucess"){
                        tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3000']; ?>','');
                            closeOpenBox();
                            $('#htmlsetpage').html('');
                            var urlParams = new URLSearchParams(window.location.search);
                            if(urlParams.has('TRANST')) {
                                param_value = urlParams.get('TRANST');
                                param_value = (param_value !=null || param_value !="")?param_value:"<?php echo $transaction_table; ?>";
                                getCRMListDashboard(param_value)
                            }else{
                                location.reload();
                            }
                    }else{
                        tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX',jsonres.res.message,'');
                        $(".sbt_loading_en_dis").text("Submit");
                        $(".sbt_loading_en_dis").attr("disabled", false);
                    }
                }
            });
        }
    }
    function submitLeadFormDetail(formid){
        var formvalue =  $("#"+formid).serialize();

        var dataArray = $("#"+formid).serializeArray();
        var assetid = [];
        $(dataArray).each(function(i, field){
            console.log(field.name+' -- inside work --- '+field.value);
            if(field.value == ""){
                assetid.push(field.name);
            }
        });
        if (assetid.length != 0){
            swal({
                title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                text: "Please Enter Required Fields",
                type: "error",
                showCancelButton: false,
                confirmButtonClass: "btn-primary",
                confirmButtonText: "Ok",
                closeOnConfirm: true
            });
            return false;
        }


        var transaction_table = '<?php echo $transaction_table; ?>';
        var transaction_id = '<?php echo $rid; ?>';
        var crm_history_id = '<?php echo $crm_history_id; ?>';
        jQuery.ajax({
            url: "ajax.php",
            data: "page=insertcrmleaddetails&crm_lead_id="+transaction_id+"&transaction_id="+transaction_id+"&transaction_table="+transaction_table+"&"+formvalue,
            type: "POST",
            success:function(data){
                console.log(data);
                var jsonres = JSON.parse(data);
                console.log(jsonres.res.status);
                if(jsonres.res.status == "sucess"){
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3000']; ?>','');
                    if(crm_history_id != ""){
                        if(transaction_table =="crm_contacts"){
                            var pagecalltype ="CONTACT";
                        }elseif(transaction_table =="crm_leads"){
                            var pagecalltype ="LEAD";
                        }
                        $('#'+formid)[0].reset();
                        leadPageCall(pagecalltype,transaction_id);
                    }else{
                        pageCallHistories(transaction_table, transaction_id);
                        $('#'+formid)[0].reset();
                    }
                    $('.resetmulyiselect').val(null).trigger('change');

                }else{
                    tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX',jsonres.res.message,'');
                }
            }
        });
    }
    function closeOpenBox(){
        var my_val = $('.pcoded').attr('vertical-placement');
        if (my_val == 'right') {
            var options = {
                direction: 'left'
            };
        } else {
            var options = {
                direction: 'right'
            };
        }
        $('.leadsidebar-form').toggle('slide', options, 500);
        $('.leadsidebar-form').css('display', 'block');
    }

    function selectedcategory(selected_id,element_id,table_name,field_name) {
      var selectedvalues = selected_id;
     if(table_name=='states' || table_name=='cities'){
       $.ajax({
        url: "ajax.php",
        data:'page=getselectedlist&table_name='+table_name+'&id='+selectedvalues+'&field='+field_name,
        type: "POST",
        success: function(response) {
          console.log(response);
          var response = JSON.parse(response);
          console.log(response.length);
          var select = document.getElementById(element_id);
          document.getElementById(element_id).innerHTML = null;
          for (var i = 0; i < response.length; i++)
          {
           var el = document.createElement("option");
           if(table_name=='states'){
            el.textContent = response[i].state_name;
            el.value = response[i].id;
          }
          if(table_name=='cities'){
            el.textContent = response[i].city_name;
            el.value = response[i].id;
          }
          select.add(el);

          console.log(el);
        }
      }
    });
     }

   }

</script>
<div class="user-box">
    <div class="chat-search-box border-bottom">
        <a class="back_leadsidebar-form">
            <i class="ti-close icon-x"></i>
        </a>
        <div class="right-icon-control">
            <h4 class="text-dark f-20 f-w-600 text-left">Add Contact</h4>
        </div>
    </div>
    <div class="main-friend-list card-body form-design custom-scroll" style="height: 600px">
        <div class="row">
            <div class="col-lg-5">
                <form id="submit_lead_form">
                <input type="hidden" name="rid" id="rid" value="<?php echo $rid; ?>">
                 <input type="hidden" name="transaction_table" id="transaction_table" value="<?php echo $transaction_table; ?>">
                <div class="bg-white p-3 b-radius-10">
                    <h5 class="f-18 f-w-600 bg-gray p-2">Additional Information</h5>
                    <div class="row mt-3">
                        <div class="col-lg-6 form-group w-full">
                            <span class="form-label">Contact Source <span class="requried">*</span></span>
                            <span class="d-block error-message text-danger"></span>
                            <select aria-label="Contact Source" class="form-control js-example-basic-single requried_field" name="contact_source">
                                <?php foreach($crm_general_codes['SOURCETYPE'] as $lsval){ ?>
                                    <option value="<?php echo $lsval['value']; ?>" <?php echo $transaction['contact_source'] == $lsval['value']?"selected":"";?>><?php echo $lsval['label']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-6 form-group w-full">
                            <span class="form-label">Originator <span class="requried">*</span></span>
                             <span class="d-block error-message text-danger"></span>
                            <select aria-label="Originator" class="form-control js-example-basic-single requried_field" name="originator">
                                <option value="">Select Any One</option>
                               <?php foreach($application_users as $uval){ ?>
                                    <option value="<?php echo $uval['id']; ?>" <?php echo ($transaction['originator'] == $uval['id'] || ($transaction['originator'] == "" && $_SESSION['userid']== $uval['id']))?"selected":"";?>><?php echo $uval['display_name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-6  form-group">
                            <span class="form-label">Company </span>
                            <select aria-label="Crm Company" class="form-control js-example-basic-single" name="crm_company_id">
                                <option value="">Select Any One</option>
                               <?php foreach($crm_companies as $uval){ ?>
                                    <option value="<?php echo $uval['id']; ?>" <?php echo ($transaction['crm_company_id'] == $uval['id'])?"selected":"";?>><?php echo $uval['company_name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-6  form-group">
                            <span class="form-label">Phone No. <span class="requried">*</span></span>
                            <span class="d-block error-message text-danger"></span>
                            <input aria-label="Phone" class="form-control requried_field" type="tel" name="phone" placeholder="Phone" value="<?php echo $transaction['phone']; ?>">
                        </div>
                        <div class="col-lg-4 form-group">
                            <span class="form-label">First Name <span class="requried">*</span></span>
                             <span class="d-block error-message text-danger"></span>
                            <input aria-label="First Name" class="form-control requried_field" type="text" name="first_name" placeholder="First Name" value="<?php echo $transaction['first_name']; ?>">
                        </div>
                        <div class="col-lg-4 form-group">
                            <span class="form-label">Middle Name</span>
                            <input aria-label="Middle Name" class="form-control" type="text" name="middle_name" placeholder="Middle Name" value="<?php echo $transaction['middle_name']; ?>">
                        </div>
                        <div class="col-lg-4 form-group">
                            <span class="form-label">last Name <span class="requried">*</span></span>
                            <span class="d-block error-message text-danger"></span>
                            <input aria-label="Last Name" class="form-control requried_field" type="text" name="last_name" placeholder="Last Name" value="<?php echo $transaction['last_name']; ?>">
                        </div>
                        <div class="col-lg-6 form-group">
                            <span class="form-label">Email </span>
                            <input aria-label="Email" class="form-control" type="email" name="email_id" placeholder="Email" value="<?php echo $transaction['email_id']; ?>">
                        </div>

                        <div class="col-lg-6  form-group">
                            <span class="form-label">Office No.</span>
                            <input aria-label="Phone" class="form-control" type="tel" name="office_phone" placeholder="Phone" value="<?php echo $transaction['office_phone']; ?>">
                        </div>
                        <div class="col-lg-6 form-group">
                            <span class="form-label">Fax</span>
                            <input aria-label="Fax" class="form-control" type="text" name="fax" placeholder="Fax" value="<?php echo $transaction['fax']; ?>">
                        </div>
                        <div class="col-lg-6  form-group">
                            <span class="form-label">Website</span>
                            <input aria-label="Website" class="form-control" type="text" name="website" placeholder="Website" value="<?php echo $transaction['website']; ?>">
                        </div>
                        <div class="col-lg-6  form-group">
                            <span class="form-label">Campaign</span>
                            <select aria-label="Crm Campaign" class="form-control js-example-basic-single" name="crm_campaign_id">
                                <option value="">Select Any One</option>
                               <?php foreach($crm_campaigns as $uval){ ?>
                                    <option value="<?php echo $uval['id']; ?>" <?php echo ($transaction['crm_campaign_id'] == $uval['id'])?"selected":"";?>><?php echo $uval['campaign_name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-6">
                            <span class="form-label">Image</span>
                            <div class="col-lg-9 col-xl-6 ">
                                <div class="profile-pic">
                                    <label class="-label" for="display_image">
                                        <span> <i class="fa fa-camera"></i> &nbsp; Change Image</span>
                                    </label>
                                    <input type="file" name="image" id="display_image" accept=".png, .jpg, .jpeg" onchange="loadFile(event,this.id)"/>
                                    <img src="<?php echo "uploads/all/".$transaction['image'] ; ?>" id="outputdisplay_image" width="200" alt="Attachment Preview" />
                                    <input type="hidden" name="hid_image" value="<?php echo $transaction['image'] ;  ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <h5 class="f-18 f-w-600 bg-gray p-2">Address Information</h5>
                    <div class="row mt-3">
                        <div class="col-lg-12 form-group">
                            <span class="form-label">Mailing Address</span>
                            <div class="grow-wrap">
                                <textarea aria-label="Address"  placeholder="Address" class="border form-control" rows="3" name="address" oninput="this.parentNode.dataset.replicatedValue = this.value"><?php echo $transaction['address']; ?></textarea>
                            </div>
                        </div>
                        <div class="col-lg-6 form-group w-full">
                            <span class="form-label">Country</span>
                            <select aria-label="Country" class="form-control js-example-basic-single" name="country_id" onchange="selectedcategory(this.value,'state_id','states','country_id')">
                                <option value="">Select Any One</option>
                                <?php foreach($countries as $cval){ ?>
                                    <option value="<?php echo $cval['id']; ?>" <?php echo $transaction['country_id'] == $cval['id']?"selected":"";?>><?php echo $cval['country_name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-6 form-group w-full">
                            <span class="form-label">State</span>
                            <select aria-label="State" class="form-control js-example-basic-single" name="state_id" id="state_id" onchange="selectedcategory(this.value,'city_id','cities','state_id')">
                                <option value="">Select Any One</option>
                                <?php foreach($states as $cval){ ?>
                                    <option value="<?php echo $cval['id']; ?>" <?php echo $transaction['state_id'] == $cval['id']?"selected":"";?>><?php echo $cval['state_name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-6 form-group w-full">
                            <span class="form-label">City</span>
                            <select aria-label="City" class="form-control js-example-basic-single" name="city_id" id="city_id">
                                <option value="">Select Any One</option>
                                <?php foreach($cities as $cval){ ?>
                                    <option value="<?php echo $cval['id']; ?>" <?php echo $transaction['city_id'] == $cval['id']?"selected":"";?>><?php echo $cval['city_name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-6 form-group">
                            <span class="form-label">Post Code</span>
                            <input aria-label="Postal Code" class="form-control" type="text" name="post_code" placeholder="Postal Code" value="<?php echo $transaction['post_code']; ?>">
                        </div>
                    </div>
                    <h5 class="f-18 f-w-600 bg-gray p-2">Description Information</h5>
                    <div class="row mt-3">
                        <div class="col-lg-12 form-group">
                            <span class="form-label">Description</span>
                            <div class="grow-wrap">
                                <textarea aria-label="Description" name="remark" placeholder="Description" class="border form-control" rows="3" oninput="this.parentNode.dataset.replicatedValue = this.value"><?php echo $transaction['remark']; ?></textarea>
                            </div>
                        </div>
                    </div>
                    <h5 class="f-18 f-w-600 bg-gray p-2">Feasibility</h5>
                    <div class="row mt-3">
                        <div class="col-lg-6 form-group">
                            <span class="form-label d-block">Feasibility</span>
                            <input aria-label="Feasibility Check" type="checkbox" class="js-success-sm feasiblity_check" name="feasibility_check" value="Y" <?php echo $transaction['feasibility_check'] =="Y"?"checked":""; ?> onchange="feasibiltyCheckBox()">
                        </div>
                    </div>
                    <div class="mt-2">
                        <button type="button" class="btn btn-primary waves-effect waves-light sbt_loading_en_dis" onclick="submitLeadForm()">Submit</button>
                        <button type="reset" class="btn btn-default waves-effect " data-bs-dismiss="modal">Reset</button>
                    </div>
                </div>
                </form>
            </div>
            <div class="col-lg-7">
                <?php if($rid ==""){ ?>
                 <div class="card-px text-center ">
                    <p class="text-muted f-16 fw-bold m-b-30"><i class="ti ti-alert text-warning"></i> No Histories</p>
                    <div class="text-center px-4">
                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                    </div>
                </div>
             <?php }else{
                include_once "custom_insert_crm_lead_details.php";
               ?>

        <?php } ?>




            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
      function feasibiltyCheckBox(){
        $('.removeallactivetab').removeClass('active');
       if ($(".feasiblity_check").is(":checked")) {
            $("#feasibilityWrapper").show();
            // Scroll the page to the tab
            $('.main-friend-list').animate({
                scrollTop: $("#feasibilityWrapper").offset().top - 100 // Scroll to the tab, with a little offset for visual clarity
            }, 500);
            $('.activefeasbility').addClass('active');
        }else{
            $('.activehomecomments').addClass('active');
            $("#feasibilityWrapper").hide();
        }
    }
    function reinitSelect2() {
        if (!$.fn.select2) {
            console.warn("Select2 library not loaded");
            return;
        }
        document.querySelectorAll('.js-example-basic-single').forEach(function(el) {
            const $el = $(el);
            if ($el.hasClass("select2-hidden-accessible")) {
                $el.select2('destroy');
            }
            $el.select2({
                allowClear: true
            });
        });
    }
        function previous(page, previous_id, lead_id) {
        $.ajax({
            url: `views/${page}.php`, // Replace with your backend URL
            type: 'GET',
            data: {
                'previous_id': previous_id,
                'lead_id': lead_id
            },
            success: function(response) {
                $('#feasibilitySlides').html(response);
                $( ".datepicker" ).datepicker({
                    showOn: "both",
                    buttonText: "<i class='ti-calendar'></i>",
                    dateFormat: date_format,
                    changeMonth: true,
                    changeYear: true
                }).next("button.ui-datepicker-trigger").html("<i class='ti-calendar text-dark f-12 f-w-800 m-r-10'></i>");
                $('[data-bs-toggle="tooltip"]').tooltip();
                reinitSelect2(); // Reinitialize select2 after loading new content
            },
            error: function(xhr, status, error) {
                alert('Error: ' + error);
            }
        });
    }

       $('.back_leadsidebar-form').on('click', function() {
        var contactleadid = $("#rid").val();
        if(contactleadid == ""){
             swal({
                title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                text: '<?php echo $_SESSION['error_message']['9182']; ?>',
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
                    var my_val = $('.pcoded').attr('vertical-placement');
                    if (my_val == 'right') {
                        var options = {
                            direction: 'left'
                        };
                    } else {
                        var options = {
                            direction: 'right'
                        };
                    }
                    $('.leadsidebar-form').toggle('slide', options, 500);
                    $('.leadsidebar-form').css('display', 'block');
                }
            });
        }else{
            var my_val = $('.pcoded').attr('vertical-placement');
            if (my_val == 'right') {
                var options = {
                    direction: 'left'
                };
            } else {
                var options = {
                    direction: 'right'
                };
            }
            $('.leadsidebar-form').toggle('slide', options, 500);
            $('.leadsidebar-form').css('display', 'block');
        }

    });
</script>

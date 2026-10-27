<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$rid = $_POST['rid'];
$transaction_table = "crm_campaigns";
$apiusrl = API."?mode=getcrmleads&transaction_table=".$transaction_table."&rid=".$rid;
$page_list = callAPI($apiusrl);
$res = json_decode($page_list,1);
$transaction = $res['transaction'];
$crm_general_codes = $res['crm_general_codes'];
$application_users = $res['application_users'];
$company_crm_contacts = $res['company_crm_contacts'];
$countries =$res['countries'];
$states =$res['states'];
$cities =$res['cities'];
$crm_lead_types =$res['crm_lead_types'];
$crmleaddetailstypes = array();
foreach($crm_lead_types as $crmlt){
        $crmleaddetailstypes[$crmlt['id']] =$crmlt;
}
$company_types = array();
foreach($res['company_types'] as $ctype) {
    $company_types[$ctype['id']] = $ctype;
}
$company_with_contacts = $res['company_with_contacts'];
?>
<script type="text/javascript">
    function pageCallHistories(rid){
        var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $("#crmleadhistories").html(loader);
        jQuery.ajax({
            url: "views/custom_insert_crm_lead_histories.php",
            data: "rrid="+rid,
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
            var dataArray = $("#submit_lead_form").serializeArray();
            dataObj = {
};
            $(dataArray).each(function(i, field){
              formData.append(field.name, field.value);
            });
            var display_image = document.getElementById('display_image').files[0];
            console.log(display_image);
            formData.append("file",display_image);
            jQuery.ajax({
                url: "ajax.php",
                data: formData,
                type: "POST",
                contentType: false,
                processData: false,
               // async:false,
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
        var crm_lead_id = $("#crm_lead_id").val();
        var formvalue =  $("#"+formid).serialize();
        jQuery.ajax({
            url: "ajax.php",
            data: "page=insertcrmleaddetails&crm_lead_id="+crm_lead_id+"&"+formvalue,
            type: "POST",
            success:function(data){
                console.log(data);
                var jsonres = JSON.parse(data);
                console.log(jsonres.res.status);
                if(jsonres.res.status == "sucess"){
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3000']; ?>','');
                    pageCallHistories(crm_lead_id);
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
            <h4 class="text-dark f-20 f-w-600 text-left">Add Campaign</h4>
        </div>
    </div>
    <div class="main-friend-list card-body form-design custom-scroll" style="height: 600px">
        <div class="row">
            <div class="col-lg-5">
                <form id="submit_lead_form">
                <input type="hidden" name="rid" id="rid" value="<?php echo $rid; ?>">
                 <input type="hidden" name="transaction_table" id="transaction_table" value="<?php echo $transaction_table; ?>">
                <div class="bg-white p-3 b-radius-10">
                    <h5 class="f-18 f-w-600 bg-gray p-2">Campaign Information</h5>
                    <div class="row mt-3">
                        <div class="col-lg-6 form-group w-full">
                            <span class="form-label">Campaign Name<span class="requried">*</span></span>
                            <span class="d-block error-message text-danger"></span>
                            <input aria-label="campaign_name" class="form-control requried_field" type="text" name="campaign_name" placeholder="campaign_name" value="<?php echo $transaction['campaign_name']; ?>">
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
                        <div class="col-lg-6 form-group w-full">
                            <span class="form-label">Campaign Type <span class="requried text-danger">*</span></span>
                            <span class="d-block error-message text-danger"></span>
                            <select aria-label="Crm Campaign Type" class="form-control js-example-basic-single requried_field" name="crm_campaign_type">
                                <?php foreach($crm_general_codes['CAMPAIGNTYPE'] as $lsval){ ?>
                                    <option value="<?php echo $lsval['value']; ?>" <?php echo $transaction['crm_campaign_type'] == $lsval['value']?"selected":"";?>><?php echo $lsval['label']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-6 form-group w-full">
                            <span class="form-label">Campaign Objective <span class="requried">*</span></span>
                            <span class="d-block error-message text-danger"></span>
                            <select aria-label="Campaign Objective" class="form-control js-example-basic-single requried_field" name="campaign_objective">
                                <?php foreach($crm_general_codes['CAMPAIGNOBJECTIVES'] as $lsval){ ?>
                                    <option value="<?php echo $lsval['value']; ?>" <?php echo $transaction['campaign_objective'] == $lsval['value']?"selected":"";?>><?php echo $lsval['label']; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-lg-6 form-group">
                            <span class="form-label">Start Sate <span class="requried">*</span></span>
                            <span class="d-block error-message text-danger"></span>
                            <input aria-label="Start Date" class="form-control startdatepicker requried_field" type="text" name="campaign_start_date" placeholder="Start Date" value="<?php echo $transaction['campaign_start_date']; ?>">
                        </div>
                        <div class="col-lg-6 form-group">
                            <span class="form-label">End Date <span class="requried">*</span></span>
                            <span class="d-block error-message text-danger"></span>
                            <input aria-label="End Date" class="form-control requried_field enddatepicker" type="text" name="campaign_end_date" placeholder="End Date" value="<?php echo $transaction['campaign_end_date']; ?>">
                        </div>
                        <div class="col-lg-6 form-group">
                            <span class="form-label">Total Budget </span>
                            <input aria-label="Total Budget" class="form-control" type="number" name="total_budget" placeholder="Total Budget" value="<?php echo $transaction['total_budget']; ?>">
                        </div>
                        <div class="col-lg-6  form-group">
                            <span class="form-label">Redirect URL</span>
                            <input aria-label="Website URL" class="form-control " type="text" name="redirect_url" placeholder="Website URL" value="<?php echo $transaction['redirect_url']; ?>">
                        </div>

                         <div class="col-lg-6  form-group">
                            <span class="form-label">Contact Email Id</span>

                            <input aria-label="Contact Email" class="form-control" type="email" name="contact_email_id" placeholder="Contact Email" value="<?php echo $transaction['contact_email_id']; ?>">
                        </div>
                        <div class="col-lg-6  form-group">
                            <span class="form-label">Contact No.</span>
                            <input aria-label="Contact No." class="form-control" type="phone" name="contact_no" placeholder="Contact No." value="<?php echo $transaction['contact_no']; ?>">
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
                        <div class="col-lg-12 form-group">
                            <span class="form-label">Description</span>
                            <div class="grow-wrap">
                                <textarea aria-label="Remark" name="remark" placeholder="Remark" class="border form-control" rows="3" oninput="this.parentNode.dataset.replicatedValue = this.value"><?php echo $transaction['remark']; ?></textarea>
                            </div>
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
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
       $('.back_leadsidebar-form').on('click', function() {
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
    });
</script>
<script type="text/javascript" src="assets/pages/accordion/accordion.js"></script>

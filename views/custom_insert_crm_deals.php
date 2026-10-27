<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$rid = $_POST['rid'];
$transaction_table = "crm_deals";
$apiusrl = API."?mode=getcrmleads&transaction_table=".$transaction_table."&rid=".$rid;
$page_list = callAPI($apiusrl);
$res = json_decode($page_list,1);
$transaction = $res['transaction'];
$crm_general_codes = $res['crm_general_codes'];
$application_users = $res['application_users'];
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
$crm_companies = $res['crm_companies'];
$deal_with_contacts_list = $res['deal_with_contacts'];
foreach($deal_with_contacts_list as $dlcontct){
    if($dlcontct['id'] == $transaction['crm_contact_id']){
        $deal_with_contacts[] = $dlcontct;
    }
}


?>
<script type="text/javascript">
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
            console.log(display_image);
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
    function getCRMContactLst(crm_company_id){
        if(crm_company_id !=""){
            jQuery.ajax({
                url: "ajax.php",
                data: "&crm_company_id="+crm_company_id+"&page=getcrmcontactslist",
                type: "POST",
                success:function(data){
                    $('#crm_contact_id').html(data);
                }
            });
        }else{
            $('#crm_contact_id').html("<option value=''>Select Any One</option>");
        }
    }
</script>
<div class="user-box">
    <div class="chat-search-box border-bottom">
        <a class="back_leadsidebar-form">
            <i class="ti-close icon-x"></i>
        </a>
        <div class="right-icon-control">
            <h4 class="text-dark f-20 f-w-600 text-left">Add Deal</h4>
        </div>
    </div>
    <div class="main-friend-list card-body form-design custom-scroll" style="height: 600px">
        <div class="row">
            <div class="col-lg-5">
                <form id="submit_lead_form">
                <input type="hidden" name="rid" id="rid" value="<?php echo $rid; ?>">
                 <input type="hidden" name="transaction_table" id="transaction_table" value="<?php echo $transaction_table; ?>">
                <div class="bg-white p-3 b-radius-10">
                    <h5 class="f-18 f-w-600 bg-gray p-2">Deal Information</h5>
                    <div class="row mt-3">
                        <div class="col-lg-12 form-group w-full">
                            <span class="form-label">Deal Name<span class="requried">*</span></span>
                            <span class="d-block error-message text-danger"></span>
                            <input aria-label="deal_name" class="form-control requried_field" type="text" name="deal_name" placeholder="deal_name" value="<?php echo $transaction['deal_name']; ?>">
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
                            <span class="form-label">Source Type <span class="requried text-danger">*</span></span>
                            <span class="d-block error-message text-danger"></span>
                            <select aria-label="Source Type" class="form-control js-example-basic-single requried_field" name="source_type">
                                <option value="">Select Any One</option>
                                <?php foreach($crm_general_codes['SOURCETYPE'] as $lsval){ ?>
                                    <option value="<?php echo $lsval['value']; ?>" <?php echo $transaction['source_type'] == $lsval['value']?"selected":"";?>><?php echo $lsval['label']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-6 form-group w-full">
                            <span class="form-label">Company <span class="requried text-danger">*</span></span>
                            <span class="d-block error-message text-danger"></span>
                            <select aria-label="Crm Company" class="form-control js-example-basic-single requried_field" name="crm_company_id" onchange="getCRMContactLst(this.value)">
                                <option value="">Select Any One</option>
                                <?php foreach($crm_companies as $comp){ ?>
                                    <option value="<?php echo $comp['id']; ?>" <?php echo $transaction['crm_company_id'] == $comp['id']?"selected":"";?>><?php echo $comp['company_name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-6 form-group w-full">
                            <span class="form-label">Primary Contact <span class="requried text-danger">*</span></span>
                            <span class="d-block error-message text-danger"></span>
                            <select aria-label="Crm Contact" class="form-control js-example-basic-single requried_field" name="crm_contact_id" id="crm_contact_id">
                                <option value="">Select Any One</option>
                                <?php foreach($deal_with_contacts_list as $cont){ ?>
                                    <option value="<?php echo $cont['id']; ?>" <?php echo $transaction['crm_contact_id'] == $cont['id']?"selected":"";?>><?php echo $cont['display_name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-6 form-group">
                            <span class="form-label">Deal Total Budget</span>
                            <input aria-label="Total Budget" class="form-control" type="number" name="total_budget" placeholder="Total Budget" value="<?php echo $transaction['total_budget']; ?>">
                        </div>
                        <div class="col-lg-6 form-group">
                            <span class="form-label">Due Date <span class="requried">*</span></span>
                            <span class="d-block error-message text-danger"></span>
                            <input aria-label="Due Date" class="form-control datepicker requried_field" type="text" name="due_date" placeholder="Due Date" value="<?php echo fromDate($transaction['due_date']); ?>">
                        </div>
                        <div class="col-lg-6 form-group">
                            <span class="form-label">Expected Close Date <span class="requried">*</span></span>
                            <span class="d-block error-message text-danger"></span>
                            <input aria-label="Expected Close Date" class="form-control requried_field datepicker" type="text" name="expected_close_date" placeholder="Expected Close Date" value="<?php echo fromDate($transaction['expected_close_date']); ?>">
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
                <div class="card">
                    <div class="card-header">
                        <h5>Contacts</h5>
                        <div class="card-header-right"></div>
                    </div>
                    <div class="card-body">
                        <?php if(count((array)$deal_with_contacts) !=0){ ?>
                            <table class="table table-hover m-b-0" style="font-size:13px;" id="categoryfilter">
                                <thead>
                                    <tr><th scope="col">Name</th>
                                        <th scope="col">Number</th>
                                        <th scope="col">Originator</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($deal_with_contacts as $crmleads){ ?>
                                    <tr>
                                        <td>
                                            <button type="button" !" class="leadsidebar text-primary" onclick="leadPageCall('CONTACT','<?php echo $crmleads['id']; ?>')"><?php echo $crmleads['display_name']; ?></button>
                                        </td>
                                        <td><?php echo $crmleads['phone']; ?></td>
                                        <td><?php echo $_SESSION['users'][$crmleads['originator']]; ?></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        <?php }else{ ?>
                            <div class="card-px text-center ">
                                <p class="text-muted f-16 fw-bold m-b-30"><i class="ti ti-alert text-warning"></i> No Contacts</p>
                                <div class="text-center px-4">
                                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="">
                    <?php include_once "custom_list_item_search.php"; ?>
                </div>

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

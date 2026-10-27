<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$rid = $_POST['rid'];
$transaction_table = "projects";
$apiusrl = API."?mode=getcrmleads&transaction_table=".$transaction_table."&rid=".$rid;
$page_list = callAPI($apiusrl);
$res = json_decode($page_list,1);
$transaction = $res['transaction'];
$application_users = $res['application_users'];
$customers = $res['customers'];
$project_teams = $res['project_teams'];
$project_teams_userid = array();
foreach($project_teams as $prteam){
    $project_teams_userid[$prteam['user_id']] = $prteam['user_id'];
}
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
            var dataArray = $("#submit_lead_form").serializeArray(),
            dataObj = {};
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

</script>
<div class="user-box">
    <div class="chat-search-box border-bottom">
        <a class="back_leadsidebar-form">
            <i class="ti-close icon-x"></i>
        </a>
        <div class="right-icon-control">
            <h4 class="text-dark f-20 f-w-600 text-left">Add Project</h4>
        </div>
    </div>
    <div class="main-friend-list card-body form-design custom-scroll" style="height: 600px">
        <div class="row">
            <div class="col-lg-5">
                <form id="submit_lead_form">
                <input type="hidden" name="rid" id="rid" value="<?php echo $rid; ?>">
                 <input type="hidden" name="transaction_table" id="transaction_table" value="<?php echo $transaction_table; ?>">
                <div class="bg-white p-3 b-radius-10">
                    <h5 class="f-18 f-w-600 bg-gray p-2">Project Information</h5>
                    <div class="row mt-3">
                        <div class="col-lg-6 form-group w-full">
                            <span class="form-label">Project Name<span class="requried">*</span></span>
                            <span class="d-block error-message text-danger"></span>
                            <input aria-label="Project Name" class="form-control requried_field" type="text" name="project_name" placeholder="Project Name" value="<?php echo $transaction['project_name']; ?>">
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
                            <span class="form-label">Customer <span class="requried text-danger">*</span></span>
                            <span class="d-block error-message text-danger"></span>
                            <select aria-label="Customer" class="form-control js-example-basic-single requried_field" name="customer_id">
                                <option value="">Select Any One</option>
                                <?php foreach($customers as $cust){ ?>
                                    <option value="<?php echo $cust['id']; ?>" <?php echo $cust['id'] == $transaction['customer_id']?"selected":""; ?>><?php echo $cust['company']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-6 form-group">
                            <span class="form-label">Project Total Budget </span>
                            <input aria-label="Total Budget" class="form-control" type="number" name="project_cost" placeholder="Total Budget" value="<?php echo $transaction['project_cost']; ?>">
                        </div>
                        <div class="col-lg-6 form-group">
                            <span class="form-label">Start Sate <span class="requried">*</span></span>
                            <span class="d-block error-message text-danger"></span>
                            <input aria-label="Start Date" class="form-control startdatepicker requried_field" type="text" name="planned_start" placeholder="Start Date" value="<?php echo fromDate($transaction['planned_start']); ?>">
                        </div>
                        <div class="col-lg-6 form-group">
                            <span class="form-label">End Date <span class="requried">*</span></span>
                            <span class="d-block error-message text-danger"></span>
                            <input aria-label="End Date" class="form-control requried_field enddatepicker" type="text" name="planned_finish" placeholder="End Date" value="<?php echo fromDate($transaction['planned_finish']); ?>">
                        </div>

                         <div class="col-lg-12 form-group w-full">
                            <span class="form-label">Team Member <span class="requried">*</span></span>
                             <span class="d-block error-message text-danger"></span>
                            <select aria-label="Project Team User []" class="form-control js-example-basic-single requried_field" name="project_team_user_id[]" multiple="">
                                <option value="">Select Any One</option>
                               <?php foreach($application_users as $uval){ ?>
                                    <option value="<?php echo $uval['id']; ?>" <?php echo in_array($uval['id'], (array)$project_teams_userid)?"selected":""; ?>><?php echo $uval['display_name']; ?></option>
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
                                    <img src="<?php echo "uploads/all/".$transaction['project_logo'] ; ?>" id="outputdisplay_image" width="200" alt="Logo" />
                                    <input type="hidden" name="hid_image" value="<?php echo $transaction['image'] ;  ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 form-group">
                            <span class="form-label">Description</span>
                            <div class="grow-wrap">
                                <textarea aria-label="Description" name="description" placeholder="Description" class="border form-control" rows="3" oninput="this.parentNode.dataset.replicatedValue = this.value"><?php echo $transaction['description']; ?></textarea>
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

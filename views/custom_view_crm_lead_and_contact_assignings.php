<?php
 $_SESSION['apply_js']['multiselest'] = 1;
$url = CRMAPI."?mode=getleadandcontactassigned&admin_type=".$_SESSION['admin_type'];
$apires = callAPI($url);
$result = json_decode($apires, 1);
$application_users = $result['application_users'];
$crm_projects = $result['crm_projects'];
$dashboard_cnt = $result['dashboard_cnt'];
?>
<script type="text/javascript">
function chnageAssigneType(){
    var assign_type = $("input[id='assign_type']:checked").val();
    if(assign_type == '1'){
        $("#auto_assign_div").hide();
        $("#manual_assign_div").show('slow');
    }else{
        $("#manual_assign_div").hide();
        $("#auto_assign_div").show('slow');
    }
}
function submitForm(){
    var error_check = "N";
    $("#error_project_id").text("");
    $("#error_user_id").text("");
    $("#error_number_of_lead_contact").text("");
    var assign_type = $("input[id='assign_type_2']:checked").val();
    var assign_lead_contact = $("input[id='assign_lead_contact']:checked").val();
    console.log("assign_lead_contact - "+assign_lead_contact);
    var number_of_lead_contact = $("#number_of_lead_contact").val()
    var crm_lead_total_cnt  = $("#crm_lead_total_cnt").val()
    var crm_contacts_total_cnt  = $("#crm_contacts_total_cnt").val()
    if(assign_type == '1'){
        var user_id = $("#user_id").val();
        if(user_id ==""){
            error_check = "Y";
             console.log("empty user id");
            $("#error_user_id").text("Please Enter Required Fields");
        }
    }else{
        var project_id = $(".project_ids").val();
        if(project_id ==""){
            error_check = "Y";
            console.log("empty project id");
            $("#error_project_id").text("Please Enter Required Fields");
        }
    }
    if(number_of_lead_contact =="" || number_of_lead_contact =="0"){
        error_check = "Y";
         console.log("empty number");
        $("#error_number_of_lead_contact").text("Please Enter Required Fields");
    }
    if(assign_lead_contact == "crm_leads"){
        if(parseInt(crm_lead_total_cnt) < parseInt(number_of_lead_contact)){
            error_check = "Y";
            console.log("Leads");
            $("#error_number_of_lead_contact").text("Available Leads Is "+crm_lead_total_cnt);
        }
    }else if(assign_lead_contact == "crm_contacts"){
        if(parseInt(crm_contacts_total_cnt) < parseInt(number_of_lead_contact)){
            error_check = "Y";
             console.log("contacts");
            $("#error_number_of_lead_contact").text("Available Contact Is "+crm_contacts_total_cnt);
        }else{
            console.log("contacts -"+crm_contacts_total_cnt+" --- "+number_of_lead_contact);
        }
    }
    if(error_check == "N"){
        console.log('insert');
        var assignecontactform = $("#assignecontactform").serialize();
        jQuery.ajax({
            url: "ajax.php",
            data: "page=leadandcontactassigning&"+assignecontactform,
            type: "POST",
            async:false,
            success:function(data){
                console.log(data);
                var jsonres = JSON.parse(data);
                if(jsonres.res.status == "sucess"){
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3000']; ?>','');
                        location.reload();
                }else{
                    tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX',jsonres.res.message,'');
                }
            }
        });
    }else{
        tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX','Oopps','');
    }
}
</script>
<ul class="dynamic_card_menu">
    <li>
        <a href="#!" >
            <div class="card ticket-card">
                <div class="card-body text-center p-2">

                    <p class="m-b-30 bg-c-blue lbl-card custom-limit-text" style="--linehide: 1;">
                    Total Leads </p>

                    <div class="text-center">
                        <h2 class="m-b-0 d-inline-block text-c-red"><?php echo $dashboard_cnt['crm_lead_total_cnt'] !=""?$dashboard_cnt['crm_lead_total_cnt']:0; ?></h2>
                    </div>

                </div>
            </div>
        </a>
    </li>
    <li>
        <a href="#!" >
            <div class="card ticket-card">
                <div class="card-body text-center p-2">

                    <p class="m-b-30 bg-c-blue lbl-card custom-limit-text" style="--linehide: 1;">
                    Allocatd Leads</p>

                    <div class="text-center">
                        <h2 class="m-b-0 d-inline-block text-c-red"><?php echo $dashboard_cnt['crm_lead_assigne_cnt'] !=""?$dashboard_cnt['crm_lead_assigne_cnt']:0; ?></h2>
                    </div>

                </div>
            </div>
        </a>
    </li>
    <li>
        <a href="#!" >
            <div class="card ticket-card">
                <div class="card-body text-center p-2">

                    <p class="m-b-30 bg-c-blue lbl-card custom-limit-text" style="--linehide: 1;">
                    Un-Allocated Leads</p>

                    <div class="text-center">
                        <h2 class="m-b-0 d-inline-block text-c-red"><?php echo $dashboard_cnt['crm_lead_un_assigne_cnt'] !=""?$dashboard_cnt['crm_lead_un_assigne_cnt']:0; ?></h2>
                    </div>

                </div>
            </div>
        </a>
    </li>
        <li>
        <a href="#!" >
            <div class="card ticket-card">
                <div class="card-body text-center p-2">

                    <p class="m-b-30 bg-c-yellow  lbl-card custom-limit-text" style="--linehide: 1;">
                    Total Contacts </p>

                    <div class="text-center">
                        <h2 class="m-b-0 d-inline-block text-c-red"><?php echo $dashboard_cnt['crm_contacts_total_cnt'] !=""?$dashboard_cnt['crm_contacts_total_cnt']:0; ?></h2>
                    </div>

                </div>
            </div>
        </a>
    </li>
    <li>
        <a href="#!" >
            <div class="card ticket-card">
                <div class="card-body text-center p-2">

                    <p class="m-b-30 bg-c-yellow  lbl-card custom-limit-text" style="--linehide: 1;">
                    Allocatd Contacts</p>

                    <div class="text-center">
                        <h2 class="m-b-0 d-inline-block text-c-red"><?php echo $dashboard_cnt['crm_contacts_assigne_cnt'] !=""?$dashboard_cnt['crm_contacts_assigne_cnt']:0; ?></h2>
                    </div>

                </div>
            </div>
        </a>
    </li>
    <li>
        <a href="#!" >
            <div class="card ticket-card">
                <div class="card-body text-center p-2">

                    <p class="m-b-30 bg-c-yellow  lbl-card custom-limit-text" style="--linehide: 1;">
                    Un-Allocated Contacts</p>
                    <div class="text-center">
                        <h2 class="m-b-0 d-inline-block text-c-red"><?php echo $dashboard_cnt['crm_contacts_un_assigne_cnt'] !=""?$dashboard_cnt['crm_contacts_un_assigne_cnt']:0; ?></h2>
                    </div>

                </div>
            </div>
        </a>
    </li>
</ul>

<div class="row">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header table-card-header">
                <h5>User Analysis</h5>
                <div class="card-header-right table-card-header f-12">
                     <span class="form-label label label-xs label-info " data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Total"><i class="ti-info-alt"></i></span> Total
                     <span class="form-label label label-xs label-danger " data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Yet To start"><i class="ti-info-alt"></i></span> Yet To start
                     <span class="form-label label label-xs label-success " data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="In-Progress"><i class="ti-info-alt"></i></span> In-Progress
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <?php  if(count((array)$application_users) !=0){ ?>
                    <table class="table table-hover">
                        <tr>
                            <th scope="col">User</th>
                            <th scope="col">Leads</th>
                            <th scope="col">Contacts</th>
                        </tr>
                       <?php $im=1; foreach($application_users as $auser){
                        $leadcnt = $auser['leads'];
                        $contactscnt = $auser['contacts'];
                        ?>
                        <tr>
                            <td class="p-2 w-b-w-s">
                                <div class="align-middle">
                                    <?php if($auser['user_photo']) { ?>
                                        <img src="uploads/all/<?php echo $auser['user_photo']; ?>"  class="img-radius img-50 align-top m-r-15" alt="User Profile">
                                    <?php } else { ?>
                                        <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($auser['display_name']); ?></span>
                                    <?php } ?>
                                    <div class="d-inline-block width73">
                                        <button type="button" !" class="leadsidebar text-primary" onclick="leadPageCall('LEAD','<?php echo $auser['id']; ?>')"><h6><?php echo $auser['display_name']; ?></h6></button>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php
                                    $lead_total_cnt = $leadcnt['lead_total_cnt']!=""?$leadcnt['lead_total_cnt']:0 ;
                                    $lead_used_cnt = $leadcnt['lead_used_cnt'] !=""?$leadcnt['lead_used_cnt']:0;
                                    $unused = $lead_total_cnt-$lead_used_cnt;
                                ?>
                                <span class="form-label label label-xs label-info " data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Total"><?php echo $lead_total_cnt;  ?></span>
                                <span class="form-label label label-xs label-danger " data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title=" Yet To start"> <?php echo $unused ?></span>
                                <span class="form-label label label-xs label-success " data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="In-Progress"><?php echo $lead_used_cnt; ?></span>
                            </td>
                            <td>
                                <?php
                                    $lead_total_cnt = $contactscnt['lead_total_cnt']!=""?$contactscnt['lead_total_cnt']:0 ;
                                    $lead_used_cnt = $contactscnt['lead_used_cnt'] !=""?$contactscnt['lead_used_cnt']:0;
                                    $unused = $lead_total_cnt-$lead_used_cnt;
                                ?>
                               <span class="form-label label label-xs label-info " data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Total"><?php echo $lead_total_cnt;  ?></span>
                                <span class="form-label label label-xs label-danger " data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Yet To start"><?php echo $unused ?></span>
                                <span class="form-label label label-xs label-success " data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="In-Progress"><?php echo $lead_used_cnt ?></span>
                            </td>
                        </tr>
                <?php $im++;
} ?>
                    </table>
                    <?php }else{ ?>
                    <div class="card-px text-center ">
                        <p class="text-muted f-16 fw-bold m-b-30"><i class="ti ti-alert text-warning"></i> No Users</p>
                        <div class="text-center px-4">
                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                        </div>
                    </div>
                <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header table-card-header">
                <h5> Lead And Contact Assigning</h5>
                <div class="card-header-right table-card-header ">
                    <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                        <i class="icofont icofont-reply"></i>
                        Back
                    </a>
                </div>
            </div>
            <div class="card-body mr-r-50 m-l-50" style="">
                <form id="assignecontactform">
                    <div class="row">
                        <div class=" col-lg-12">
                            <span class="form-label">Assign Type <span class="requried  text-danger">*</span></span>
                            <div class="form-radio">
                                <div class="radio radio-inline">
                                    <label class="form-label">
                                        <input aria-label="Assign Type" type="radio" name="assign_type" id="assign_type_3" value="1"  onchange="chnageAssigneType()" checked="checked">
                                        <i class="helper"></i>Manual Assign
                                    </label>
                                </div>
                                <div class="radio radio-inline">
                                    <label class="form-label">
                                        <input aria-label="Assign Type" type="radio" name="assign_type" id="assign_type_4"  value="2" onchange="chnageAssigneType()">
                                        <i class="helper"></i>Auto Assign
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" >
                        <div class=" col-lg-6">
                            <span class="form-label">Assign Lead/Contacts <span class="requried  text-danger">*</span></span>
                            <div class="form-radio">
                                <div class="radio radio-inline">
                                    <label class="form-label">
                                        <input aria-label="Assign Lead Contact" type="radio" name="assign_lead_contact" id="assign_lead_contact_2" checked="checked" value="crm_leads">
                                        <i class="helper"></i>Leads
                                    </label>
                                    <input type="hidden" id="crm_lead_total_cnt" value="<?php echo $dashboard_cnt['crm_lead_un_assigne_cnt'] !=""?$dashboard_cnt['crm_lead_un_assigne_cnt']:0; ?>">
                                    <span class="form-label label label-xs label-primary text-white"><?php echo $dashboard_cnt['crm_lead_un_assigne_cnt'] !=""?$dashboard_cnt['crm_lead_un_assigne_cnt']:0; ?></span>
                                </div>
                                <div class="radio radio-inline">
                                    <label class="form-label">
                                        <input aria-label="Assign Lead Contact" type="radio" name="assign_lead_contact"  id="assign_lead_contact_3" value="crm_contacts">
                                        <i class="helper"></i>Contacts
                                    </label>
                                    <input type="hidden" id="crm_contacts_total_cnt" value="<?php echo $dashboard_cnt['crm_contacts_un_assigne_cnt'] !=""?$dashboard_cnt['crm_contacts_un_assigne_cnt']:0; ?>">
                                    <span class="form-label label label-xs label-warning  text-white"><?php echo $dashboard_cnt['crm_contacts_un_assigne_cnt'] !=""?$dashboard_cnt['crm_contacts_un_assigne_cnt']:0; ?></span>
                                </div>
                            </div>
                        </div>
                        <div class=" col-lg-6 mt-2">
                            <span class="form-label">Order By <span class="requried  text-danger">*</span></span>
                            <div class="form-radio">
                                <div class="radio radio-inline">
                                    <label class="form-label">
                                        <input aria-label="Order By" type="radio" name="order_by" id="order_by"  value="ASC" checked="checked">
                                        <i class="helper"></i>Oldest
                                    </label>
                                </div>
                                <div class="radio radio-inline">
                                    <label class="form-label">
                                        <input aria-label="Order By" type="radio" name="order_by" id="order_by_2" value="DESC" >
                                        <i class="helper"></i>Newest
                                    </label>
                                </div>

                            </div>
                        </div>
                        <div class="col-lg-6 form-group w-full mt-2" id="manual_assign_div">
                            <span class="form-label  d-block">User <span class="requried  text-danger">*</span></span>
                            <span id="error_user_id" class="text-danger"></span>
                            <select aria-label="Application User" class="form-control js-example-basic-single" name="application_user_id" id="application_user_id">
                                <option value="">Select Any One</option>
                               <?php foreach($application_users as $uval){ ?>
                                    <option value="<?php echo $uval['id']; ?>" <?php echo ($transaction['application_user_id'] == $uval['id'] || ($transaction['originator'] == "" && $_SESSION['userid']== $uval['id']))?"selected":"";?>><?php echo $uval['display_name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-6 form-group w-full mt-2" id="auto_assign_div" style="display: none">
                            <span class="form-label d-block">Project <span class="requried  text-danger">*</span></span>
                            <span id="error_project_id" class="text-danger"></span>
                            <select aria-label="Project" class="form-control js-example-basic-single project_ids" name="project_id" id="project_ids">
                                <option value="">Select Any One</option>
                               <?php foreach($crm_projects as $uval){ ?>
                                    <option value="<?php echo $uval['id']; ?>"><?php echo $uval['project_name']." - (".$uval['team_user_cnt'].")"; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-6 form-group mt-2">
                            <span class="form-label d-block">Sharing Count <span class="requried  text-danger">*</span></span>
                            <span id="error_number_of_lead_contact" class="text-danger"></span>
                            <input aria-label="Count" class="form-control" type="text" name="number_of_lead_contact" id="number_of_lead_contact" placeholder="Count" value="">
                        </div>

                         <div class="mt-2">
                            <button type="button" class="btn btn-primary waves-effect waves-light" onclick="submitForm()">Assign</button>

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


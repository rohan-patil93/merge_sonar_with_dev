<?php
if($_POST['ajaxcall'] =="Y"){
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    $rid = $_POST['rid'];
    $url = API."?mode=getapplicationusers&rid=".$rid;
    $response = callAPI($url);
    $result = json_decode($response,1);
    $audetail  = $result['application_users'];
    $application_user_module_roles = $result['application_user_module_roles'];
    $all_application_users = $result['all_application_users'];
    $cost_centers = $result['cost_centers'];
    $locations = $result['locations'];
    $warehouses = $result['warehouses'];
    $countries = $result['countries'];
    $states = $result['states'];
    $currencies = $result['currencies'];
    $application_roles = $result['application_roles'];
    $rid = $audetail['id'];
    $moduledetail = array();
    $role_idd =array(SUPERADMIN_ROLE,ADMIN_ROLE,CANDIDATE_ROLE,VENDOR_ROLE);
    foreach($application_user_module_roles as $rolesre11){
        $role_idd[] = $rolesre11['role_short_code'];
    }
    foreach($application_roles as $rolesre){
        if(!in_array($rolesre['role_short_code'], $role_idd)){
            $roles[$rolesre['application_module']][] = $rolesre;
            $moduledetail[$rolesre['application_module']] = $rolesre['display_name'];
        }
    }
}
$user_cost_centers  = $result['user_cost_centers'];
$user_departments  = $result['user_departments'];
$user_divisions  = $result['user_divisions'];
$user_locations  = $result['user_locations'];
$user_warehouse  = $result['user_warehouse'];
$bank_account_users  = $result['bank_account_users'];
$user_assets  = $result['user_assets'];

if($_POST['transactiontype'] != ''){ ?>
<div class="card">
    <ul class="nav md-tabs infisuite_tab mt-2 ml-2">
        <li class="nav-item">
            <a class="nav-link <?php echo $_POST['transactiontype'] == 'application_users'?"active":""; ?>" data-bs-toggle="tab" href="#application_users_tab" role="tab">Basic Info</a>
            <div class="slide"></div>
        </li>
        <?php if($_SESSION['admin_type'] ==1){ ?>
        <li class="nav-item">
            <a class="nav-link <?php echo $_POST['transactiontype'] == 'application_user_module_roles'?"active":""; ?>" data-bs-toggle="tab" href="#application_user_module_roles_tab" role="tab">Module Role</a>
            <div class="slide"></div>
        </li>
        <?php } ?>
    </ul>
    <div class="tab-content card-block">
        <div class="tab-pane <?php echo $_POST['transactiontype'] == 'application_users'?"active":""; ?>" id="application_users_tab" role="tabpanel">
            <form method="post" id="application_users_form">
            <div class="row" >
                <div class="form-group col-lg-4">
                    <span class="form-label">First Name</span>
                    <input aria-label="First Name" class="form-control" type="text" id="first_name" name="first_name" value="<?php echo $audetail['first_name']; ?>" >
                </div>
                <div class="form-group col-lg-4" >
                    <span class="form-label">Middle Name</span>
                        <input aria-label="Middle Name" class="form-control" type="text" id="middle_name" name="middle_name" value="<?php echo $audetail['middle_name']; ?>" >
                </div>
                <div class="form-group col-lg-4">
                    <span class="form-label">Last Name</span>
                    <input aria-label="Last Name" class="form-control" type="text" id="last_name" name="last_name" value="<?php echo $audetail['last_name']; ?>" >
                </div>
                <div class="form-group col-lg-4">
                    <span class="form-label">Display Name</span>
                    <input aria-label="Display Name" class="form-control" name="display_name" type="text" value="<?php echo $audetail['display_name']; ?>" >
                </div>
                <div class="form-group col-lg-4">
                    <span class="form-label">User Email</span>
                    <input aria-label="User Email" class="form-control" type="text" id="user_email" name="user_email" value="<?php echo $audetail['user_email']; ?>" readonly>
                </div>
                <div class="form-group col-lg-4">
                    <span class="form-label">User Mobile</span>
                    <input aria-label="User Mobile" class="form-control" type="number"  name="user_mobile" value="<?php echo $audetail['user_mobile']; ?>" >
                </div>
                <div class="form-group col-lg-4">
                    <span class="form-label">Cost Center</span>
                    <select aria-label="Cost Center" name="cost_center" class="form-select js-example-basic-single">
                        <option value="">Select Any One</option>
                        <?php foreach($cost_centers as $ccres) { ?>
                            <option <?php echo $audetail['cost_center']==$ccres['id'] ? 'selected':'' ?> value="<?php echo $ccres['id'];?>"><?php echo $ccres['cost_center_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-lg-4">
                    <span class="form-label">Location</span>
                    <select aria-label="Location" name="location_id" class="form-select js-example-basic-single">
                        <option value="">Select Any One</option>
                        <?php foreach($locations as $loc) { ?>
                            <option <?php echo $audetail['location_id']==$loc['id'] ? 'selected':'' ?> value="<?php echo $loc['id'];?>"><?php echo $loc['location_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-lg-4">
                    <span class="form-label">Warehouse</span>
                    <select aria-label="Default Warehouse" name="default_warehouse_id" class="form-select js-example-basic-single">
                        <option value="">Select Any One</option>
                        <?php foreach($warehouses as $warh) { ?>
                            <option <?php echo $audetail['default_warehouse_id']==$warh['id'] ? 'selected':'' ?> value="<?php echo $warh['id'];?>"><?php echo $warh['warehouse_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-lg-4">
                    <span class="form-label">Country</span>
                    <select aria-label="Country" name="country_id" class="form-control form-select js-example-basic-single" onchange="selectCounty(this.value)" value="<?php echo $config['display_logo'] ;  ?>">
                        <option value="">Select Any One</option>
                        <?php
                        $timezone = "";
                        foreach((array)$countries as $countr) {
                            if($audetail['country_id'] ==$countr['id']){
                                $timezone = $countr['time_zones'];
                            }
                        ?>
                            <option value='<?php echo $countr['id']."||".$countr['time_zones']; ?>' <?php echo $audetail['country_id'] == $countr['id']?"selected":""; ?> ><?php echo $countr['country_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-lg-4">
                    <span class="form-label">State</span>
                    <select aria-label="Default State" name="default_state_id" class="form-control form-select js-example-basic-single" id="getstate" >
                        <option value="">Select Any One</option>
                        <?php foreach((array)$states as $state) { ?>
                            <option value='<?php echo $state['id']; ?>' <?php echo $audetail['default_state_id'] == $state['id']?"selected":""; ?> ><?php echo $state['state_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-lg-4">
                    <span class="form-label">TimeZone</span>
                    <select aria-label="Timezone" name="timezone" id="time_zone" class="form-control form-select js-example-basic-single">
                        <option value="">Select Any One</option>
                        <?php $timezones = json_decode($timezone, 1);
                        foreach((array)$timezones as $tz) { ?>
                            <option value='<?php echo $tz['zoneName']; ?>' <?php echo  $tz['zoneName'] ==$audetail['timezone']?"selected":""; ?>><?php echo $tz['zoneName']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-lg-4">
                    <span class="form-label">Currency</span>
                    <select aria-label="Default Currency" name="default_currency_id" class="form-select js-example-basic-single">
                        <option value="">Select Any One</option>
                        <?php foreach($currencies as $curr) { ?>
                            <option <?php echo $audetail['default_currency_id']==$curr['id'] ? 'selected':'' ?> value="<?php echo $curr['id'];?>"><?php echo $curr['currency_list']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-lg-4">
                    <span class="form-label">Profile Image <span class="text-danger f-10 d-block">* After Uploading The Picture Please Refresh The Page if Picture Is not Showing </span></span>
                    <div class="col-lg-9 col-xl-6">
                        <div class="profile-pic">
                            <label class="-label" for="profile_user_photo">
                                <span> <i class="fa fa-camera"></i> &nbsp; Change Image</span>
                            </label>
                            <input type="file" name="user_photo" id="profile_user_photo" accept=".png, .jpg, .jpeg" onchange="loadFile(event,this.id)"/>
                            <?php $user_photo_src = !empty($audetail['user_photo']) ? "uploads/all/".$audetail['user_photo'] : 'uploads/all/no-image.jpg'; ?>
                            <img src="<?php echo $user_photo_src; ?>" id="outputprofile_user_photo" width="200" alt="User Profile" />
                            <input type="hidden" name="old_user_photo" value="<?php echo $audetail['user_photo'] ;  ?>">
                        </div>
                    </div>
                </div>
                <div class="form-group col-lg-4">
                    <span class="form-label">Status</span>
                    <select aria-label="Is Active" name="is_active" class="form-select js-example-basic-single">
                        <option  <?php if($audetail['is_active']==0){ echo "selected";
} ?> value="0">Active</option>
                        <option <?php if($audetail['is_active']==1){ echo "selected";
} ?>  value="1">Inactive</option>
                    </select>
                </div>
            </div>
            <?php if($_SESSION['admin_type'] ==1){ ?>
            <div class="row">
                <div class="form-group col-lg-4">
                    <span class="form-label">HR Connect</span>
                    <select aria-label="Hr Connect" name="hr_connect" class="form-select js-example-basic-single">
                        <option value="">Select Any One</option>
                        <?php foreach($all_application_users as $apuser) { ?>
                            <option <?php echo $audetail['hr_connect']==$apuser['id'] ? 'selected':'' ?> value="<?php echo $apuser['id'];?>"><?php echo $apuser['display_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-lg-4">
                    <span class="form-label">Finance Connect</span>
                    <select aria-label="Finance Connect" name="finance_connect" class="form-select js-example-basic-single">
                        <option value="">Select Any One</option>
                        <?php foreach($all_application_users as $apuser) { ?>
                            <option <?php echo $audetail['finance_connect']==$apuser['id'] ? 'selected':'' ?> value="<?php echo $apuser['id'];?>"><?php echo $apuser['display_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-lg-4">
                    <span class="form-label">Admin Connect</span>
                    <select aria-label="Admin Connect" name="admin_connect" class="form-select js-example-basic-single">
                        <option value="">Select Any One</option>
                        <?php foreach($all_application_users as $apuser) { ?>
                            <option <?php echo $audetail['admin_connect']==$apuser['id'] ? 'selected':'' ?> value="<?php echo $apuser['id'];?>"><?php echo $apuser['display_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-lg-4">
                    <span class="form-label">Project Connect</span>
                    <select aria-label="Project Connect" name="project_connect" class="form-select js-example-basic-single">
                        <option value="">Select Any One</option>
                        <?php foreach($all_application_users as $apuser) { ?>
                            <option <?php echo $audetail['project_connect']==$apuser['id'] ? 'selected':'' ?> value="<?php echo $apuser['id'];?>"><?php echo $apuser['display_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-lg-4">
                    <span class="form-label">location Connect</span>
                    <select aria-label="Location Connect" name="location_connect" class="form-select js-example-basic-single">
                        <option value="">Select Any One</option>
                        <?php foreach($all_application_users as $apuser) { ?>
                            <option <?php echo $audetail['location_connect']==$apuser['id'] ? 'selected':'' ?> value="<?php echo $apuser['id'];?>"><?php echo $apuser['display_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-lg-4">
                    <span class="form-label">ESOP Connect</span>
                    <select aria-label="Esop Connect" name="esop_connect" class="form-select js-example-basic-single">
                        <option value="">Select Any One</option>
                        <?php foreach($all_application_users as $apuser) { ?>
                            <option <?php echo $audetail['esop_connect']==$apuser['id'] ? 'selected':'' ?> value="<?php echo $apuser['id'];?>"><?php echo $apuser['display_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-lg-4">
                    <span class="form-label">Legal Connect</span>
                    <select aria-label="Legal Connect" name="legal_connect" class="form-select js-example-basic-single">
                        <option value="">Select Any One</option>
                        <?php foreach($all_application_users as $apuser) { ?>
                            <option <?php echo $audetail['legal_connect']==$apuser['id'] ? 'selected':'' ?> value="<?php echo $apuser['id'];?>"><?php echo $apuser['display_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-lg-4">
                    <span class="form-label">Reporting Manager</span>
                    <select aria-label="Reports To" name="reports_to" class="form-select js-example-basic-single">
                        <option value="">Select Any One</option>
                        <?php foreach($all_application_users as $apuser) { ?>
                            <option <?php echo $audetail['reports_to']==$apuser['id'] ? 'selected':'' ?> value="<?php echo $apuser['id'];?>"><?php echo $apuser['display_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-lg-4">
                    <span class="form-label">Approval Manager</span>
                    <select aria-label="Approval Manager" name="approval_manager" class="form-select js-example-basic-single">
                        <option value="">Select Any One</option>
                        <?php foreach($all_application_users as $apuser) { ?>
                            <option <?php echo $audetail['approval_manager']==$apuser['id'] ? 'selected':'' ?> value="<?php echo $apuser['id'];?>"><?php echo $apuser['display_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        <?php } ?>
            <button  class="btn btn-primary m-b-20 m-t-20" type="button" onclick="dynamicSubmitForm('application_users_form')"><i class="ti-check"></i>
            Update</button>
            <button  class="btn btn-danger m-l-20 m-b-20 m-t-20"  type="button" onclick="editUserDetails()"><i class="ti-close"></i>
            Cancel</button>
            </form>
        </div>
        <div class="tab-pane <?php echo $_POST['transactiontype'] == 'application_user_module_roles'?"active":""; ?>" id="application_user_module_roles_tab" role="tabpanel">
            <div class="row">
                <div class="col-lg-6">
                    <div class="card-header">
                        <h5 class="text-primary">Module Roles List</h5>
                        <div class="card-header-right" >
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if(count((array)$application_user_module_roles)>0){  ?>
                            <table class="table table-hover m-b-0">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Module</th>
                                        <th scope="col">Role</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $k=1;
                                    foreach ($application_user_module_roles as $aumr) { ?>
                                        <tr>
                                            <td style="width: 10%;"><?php echo $k; ?></td>
                                            <td style="width: 40%;"><?php echo $aumr['ms_display_name']; ?></td>
                                            <td style="width: 40%;"><?php echo $aumr['ar_display_name']; ?></td>
                                            <td style="width: 10%;"><i class="ti-trash text-danger" onclick="removeRoleModule('<?php echo $aumr['id']; ?>')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove Role" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i></td>
                                        </tr>
                                    <?php $k++;
}  ?>
                                </tbody>
                            </table>
                        <?php }else{ ?>
                            <div class="card-px text-center ">
                                <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Module Roles</p>
                                <div class="text-center px-4">
                                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card-header">
                        <h5 class="text-primary">Insert Module Roles</h5>
                        <div class="card-header-right" >
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group w-full">
                            <span class="form-label">Role</span>
                           <select aria-label="Role Short Code"  class ="form-select js-example-basic-single" id="role_short_code" required="" >
                               <option value="">Select Any One</option>>
                              <?php foreach($roles as $key=>$modulename){ ?>
                                <optgroup label="<?php echo $moduledetail[$key]; ?> ">
                                <?php foreach($modulename as $rolesr){  ?>
                                    <option value="<?php echo $rolesr['role_short_code']; ?>"><?php echo $rolesr['role_display_name']; ?></option>
                               <?php  } ?>
                                </optgroup>
                              <?php  }  ?>
                            </select>
                            <span class="text-danger" id="module_role_id_error" style="display: none;">Please Select Any One Role</span>
                        </div>
                        <div class="form-group">
                            <button  class="btn btn-primary m-b-20 m-t-20" type="button" onclick="submitRoleModuleForm()"><i class="ti-check"></i>Submit</button>
                            <button  class="btn btn-danger m-l-20 m-b-20 m-t-20"  type="button" onclick="editUserDetails()"><i class="ti-close"></i> Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php }else{ // view only part?>

<div class="row">
    <div class="col-lg-12 col-xl-6">
        <div class="table-responsive">
            <div class="card-header"><h5 class="text-primary">Basic Info</h5></div>
            <div class="card-body">
                <table class="table m-0">
                    <tbody>
                        <tr>
                            <th scope="row" class="p-2">Full Name</th>
                            <td class="p-2"><?php echo $audetail['first_name'].$audetail['middle_name']." ".$audetail['last_name']; ?></td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">Display Name</th>
                            <td class="p-2"><?php echo $audetail['display_name']; ?></td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">Contact</th>
                            <td class="p-2"><?php echo $audetail['user_mobile']; ?></td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">Email</th>
                            <td class="p-2"><?php echo emptytextdashShow($audetail['user_email']); ?></td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">Modules <?php if($_SESSION['admin_type'] ==1){ ?><button type="button" class="btn p-0 border-0 bg-transparent ti-pencil cursor-pointer" onclick="editUserDetails('application_user_module_roles')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button><?php
} ?></th>
                            <td class="p-2">
                                <?php foreach($application_user_module_roles as $umodule){ ?>
                                    <span class="d-block mt-2">
                                    <span class="form-label label label-md label-success "><?php echo $umodule['ms_display_name']; ?></span></span>
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">Roles <?php if($_SESSION['admin_type'] ==1){ ?><button type="button" class="btn p-0 border-0 bg-transparent ti-pencil cursor-pointer" onclick="editUserDetails('application_user_module_roles')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button> <?php
} ?></th>
                            <td class="p-2">
                                <?php foreach($application_user_module_roles as $urole){ ?>
                                    <span class="d-block mt-2">
                                    <span class="form-label label label-md label-info"><?php echo $urole['ar_display_name']; ?></span></span>
                                <?php } ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-12 col-xl-6">
        <div class="table-responsive">
            <div class="card-header">
                <div class="card-header-right" >
                    <button class="btn btn-primary" onclick="editUserDetails('application_users')"><i class="ti-pencil text-white"></i> Edit</button>
                </div>
            </div>
            <div class="card-body mt-3">
                <table class="table m-0">
                    <tbody>
                        <tr>
                            <th scope="row" class="p-2">location</th>
                            <td class="p-2"><?php echo emptytextdashShow($audetail['location_name']); ?></td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">Default Warehouse</th>
                            <td class="p-2"><?php echo emptytextdashShow($audetail['warehouse_name']); ?></td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">Country</th>
                            <td class="p-2"><?php echo emptytextdashShow($audetail['country_name']); ?></td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">State</th>
                            <td class="p-2"><?php echo emptytextdashShow($audetail['state_name']); ?></td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">Timezone</th>
                            <td class="p-2"><?php echo emptytextdashShow($audetail['timezone']); ?></td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">Currency</th>
                            <td class="p-2"><?php echo emptytextdashShow($audetail['currency_name']); ?></td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">Status</th>
                            <td class="p-2"><?php echo $audetail['is_active']=='0' ? "Active" : "Inactive"; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="table-responsive">
            <div class="card-header">
                <h5 class="text-primary">Cost Centers</h5>
                <div class="card-header-right" >

                </div>
            </div>
            <div class="card-body">
                <?php if(count((array)$user_cost_centers)>0){  ?>
                    <table class="table table-hover m-b-0">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Cost Center Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $k=1;
                            foreach ($user_cost_centers as $inst) { ?>
                                <tr>
                                    <td style="width: 10%;"><?php echo $k; ?></td>
                                    <td style="width: 90%;"><?php echo $inst['cost_center_name']; ?></td>
                                </tr>
                            <?php $k++;
}  ?>
                        </tbody>
                    </table>
                <?php }else{ ?>
                    <div class="card-px text-center ">
                        <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Cost Centers</p>
                        <div class="text-center px-4">
                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="table-responsive">
            <div class="card-header">
                <h5 class="text-primary">Departments</h5>
                <div class="card-header-right" >

                </div>
            </div>
            <div class="card-body">
                <?php if(count((array)$user_departments)>0){  ?>
                    <table class="table table-hover m-b-0">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Department Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $k=1;
                            foreach ($user_departments as $inst) { ?>
                                <tr>
                                    <td style="width: 10%;"><?php echo $k; ?></td>
                                    <td style="width: 90%;"><?php echo $inst['department_name']; ?></td>
                                </tr>
                            <?php $k++;
}  ?>
                        </tbody>
                    </table>
                <?php }else{ ?>
                    <div class="card-px text-center ">
                        <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Departments</p>
                        <div class="text-center px-4">
                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="table-responsive">
            <div class="card-header">
                <h5 class="text-primary">Divisions</h5>
                <div class="card-header-right" >

                </div>
            </div>
            <div class="card-body">
                <?php if(count((array)$user_divisions)>0){  ?>
                    <table class="table table-hover m-b-0">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Cost Center Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $k=1;
                            foreach ($user_divisions as $inst) { ?>
                                <tr>
                                    <td style="width: 10%;"><?php echo $k; ?></td>
                                    <td style="width: 90%;"><?php echo $inst['division_name']; ?></td>
                                </tr>
                            <?php $k++;
}  ?>
                        </tbody>
                    </table>
                <?php }else{ ?>
                    <div class="card-px text-center ">
                        <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Divisions</p>
                        <div class="text-center px-4">
                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="table-responsive">
            <div class="card-header">
                <h5 class="text-primary">Locations</h5>
                <div class="card-header-right" >

                </div>
            </div>
            <div class="card-body">
                <?php if(count((array)$user_locations)>0){  ?>
                    <table class="table table-hover m-b-0">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Warehouse Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $k=1;
                            foreach ($user_locations as $inst) { ?>
                                <tr>
                                    <td style="width: 10%;"><?php echo $k; ?></td>
                                    <td style="width: 90%;"><?php echo $inst['location_name']; ?></td>
                                </tr>
                            <?php $k++;
}  ?>
                        </tbody>
                    </table>
                <?php }else{ ?>
                    <div class="card-px text-center ">
                        <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Locations</p>
                        <div class="text-center px-4">
                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="table-responsive">
            <div class="card-header">
                <h5 class="text-primary">Warehouse</h5>
                <div class="card-header-right" >

                </div>
            </div>
            <div class="card-body">
                <?php if(count((array)$user_warehouse)>0){  ?>
                    <table class="table table-hover m-b-0">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Account Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $k=1;
                            foreach ($user_warehouse as $inst) { ?>
                                <tr>
                                    <td style="width: 10%;"><?php echo $k; ?></td>
                                    <td style="width: 90%;"><?php echo $inst['warehouse_name']; ?></td>
                                </tr>
                            <?php $k++;
}  ?>
                        </tbody>
                    </table>
                <?php }else{ ?>
                    <div class="card-px text-center ">
                        <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Warehouse</p>
                        <div class="text-center px-4">
                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="table-responsive">
            <div class="card-header">
                <h5 class="text-primary">Bank Accounts</h5>
                <div class="card-header-right" >

                </div>
            </div>
            <div class="card-body">
                <?php if(count((array)$bank_account_users)>0){  ?>
                    <table class="table table-hover m-b-0">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Bank Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $k=1;
                            foreach ($bank_account_users as $inst){ ?>
                                <tr>
                                    <td style="width: 10%;"><?php echo $k; ?></td>
                                    <td style="width: 90%;"><?php echo $inst['bank_account_name']; ?></td>
                                </tr>
                            <?php $k++;
}  ?>
                        </tbody>
                    </table>
                <?php }else{ ?>
                    <div class="card-px text-center ">
                        <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Bank Account</p>
                        <div class="text-center px-4">
                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
        <div class="col-lg-6">
        <div class="table-responsive">
            <div class="card-header">
                <h5 class="text-primary">Assets</h5>
                <div class="card-header-right" >

                </div>
            </div>
            <div class="card-body">
                <?php if(count((array)$user_assets)>0){  ?>
                    <table class="table table-hover m-b-0">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Assets Name</th>
                                <th scope="col">Start Date</th>
                                <th scope="col">End Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $k=1;
                            foreach ($user_assets as $inst) { ?>
                                <tr>
                                    <td style="width: 10%;"><?php echo $k; ?></td>
                                    <td style="width: 50%;"><?php echo $inst['asset_description']; ?></td>
                                    <td style="width: 20%;"><?php echo fromDate($inst['start_date']); ?></td>
                                    <td style="width: 20%;"><?php echo fromDate($inst['end_date']); ?></td>
                                </tr>
                            <?php $k++;
}  ?>
                        </tbody>
                    </table>
                <?php }else{ ?>
                    <div class="card-px text-center ">
                        <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Assets</p>
                        <div class="text-center px-4">
                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<?php } ?>

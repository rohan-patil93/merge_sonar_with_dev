<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$empdata =array();
$ress =$_POST;
if($_POST['table'] == "pay_areas"){ $posttable= "pay_area_employees";
}
if($_POST['table'] == "payroll_admins"){ $posttable = "payroll_area_admins";
}

if($_POST['department']!=""){ $vdata1["where"]["department_id"] = $_POST['department'];
}
if($_POST['designation']!=""){ $vdata1["where"]["designation_id"] = $_POST['designation'];
}
if($_POST['location']!=""){ $vdata1["where"]["location_id"] = $_POST['location'];
}
if($_POST['vendor']!=""){ $vdata1["where"]["vendor_id"] = $_POST['vendor'];
}
if($_POST['cost_center_pp']!=""){ $vdata1["where"]["cost_center_id"] = $_POST['cost_center_pp'];
}
if($_POST['emp_name_code']!=""){ $vdata1["where"]["emp_name_code"] = $_POST['emp_name_code'];
}
$vdata1["where"]["company_id"] = $_SESSION['company_id'];
$vdata1["where"]["is_active"] = 0;
$url = API."?mode=getassignemployeelistpopup&method=post";
$response = file_post_contents($url, $vdata1);
$alldata = json_decode($response,1);
$empdata = $alldata['emp_list'];
$empdep =    $alldata['department'];
$empdes =    $alldata['designation'];
$emploc =    $alldata['locations'];
$empven =    $alldata['vendors'];
$empcos =    $alldata['cost_centers'];
$vdata1["where"]= array('is_active'=>0);
$vdata = urlencode(json_encode($vdata1));
$vurl = API."?mode=gettabledetailswithconditions&table=".$posttable."&data=".$vdata;
$configesponse = callAPI($vurl);
$empesgindata = json_decode($configesponse,1);
$esassignpayarea = array();
$emp_signss = array();
$emp_sign = array();

foreach($empesgindata as $empesginda){
    if($empesginda['pay_area_id'] == $_POST['pay_area_id'] ){
        $emp_signss[] = $empesginda['emp_id'];
    }else{
        $esassignpayarea[] = $empesginda['emp_id'];
    }
    $emp_sign[] = $empesginda['emp_id'];
}
?>
<script type="text/javascript">
    function assignemployeelist(id){
        var table_name = document.getElementById('table_name').value;
        var department = document.getElementById('department').value;
        var designation = document.getElementById('designation').value;
        var location = document.getElementById('location').value;
        var vendor = document.getElementById('vendor').value;
        var cost_center_pp = document.getElementById('cost_center_pp').value;
        var check_list = document.getElementById('check_list').value;
        var effective_date= document.getElementById('effective_date').value;
        var expire_date= document.getElementById('expire_date').value;
        var emp_name_code= document.getElementById('emp_name_code').value;
        console.log('data');
         jQuery.ajax({
            url: "views/custom_insert_pay_area_assign_employee_popup.php",
            data:'table='+table_name+'&workorder_id='+id+'&department='+department+'&designation='+designation+'&location='+location+'&vendor='+vendor+'&cost_center_pp='+cost_center_pp+'&check_list='+check_list+'&effective_date='+effective_date+'&expire_date='+expire_date+'&emp_name_code='+emp_name_code,
            type: "POST",
            success:function(data){

                $('#dynamic_popupxxl').html(data);
                $('#kt_modal_dynamic_popupxxl').modal({ show: true});
            },
            error:function (){}
        });
    }
    function toinsertandpdateesignemployeeonlyone(id,uname){
        var error = 0;
        var message = '';
        var table_name = document.getElementById('table_name_post').value;
        var effective_date= document.getElementById('effective_date').value;
        var expire_date= document.getElementById('expire_date').value;
        var pay_area_id = document.getElementById('pay_area_id').value;
        $.ajax({
            url: "ajax.php",
            data:'page=insertpayareaasignemployee&table_name='+table_name+'&emp_id='+id+'&effective_date='+effective_date+'&expire_date='+expire_date+'&pay_area_id='+pay_area_id,
            type: "POST",
            success:function(data){
                console.log(data);
                if($("#checkbox_"+id).prop('checked') == true){
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',uname+' Allocated to Pay Arear','');
                } else {
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-danger','animated flipInX','animated flipOutX',uname+' Removed Form Pay Arear','');
                }
            },
            error:function (){}
        });
    }

</script>
<div class="modal-header">
    <h5 class="modal-title">Assign Employees</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

</div>

    <div class="modal-body  j-pro">

        <div class="row kt-margin-b-20 mb-2 j-content" >
            <div class="col-lg-3 form-group kt-margin-b-10-tablet-and-mobile">
                <span><?php echo "Cost Center"; ?>:</span>
                <select aria-label="Cost Center Pp" class="form-control kt-select2" id="cost_center_pp" name="">
                    <option value=""> All  </option>
                    <?php foreach($empcos as $cos){ $emp_cos[$cos['id']]= $cos['cost_center_name']; ?>
                    <option   <?php if($ress['cost_center_pp'] == $cos['id'] ){ ?> selected <?php
} ?> value="<?php echo $cos['id']; ?>"><?php echo $cos['cost_center_name']; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-lg-3  form-group kt-margin-b-10-tablet-and-mobile">
            <span><?php echo "Vendors"; ?>:</span>
            <select aria-label="Vendor" class="form-control kt-select2" id="vendor" name="">
                <option value=""> All  </option>
                <?php foreach($empven as $ven){ $emp_ven[$ven['id']]= $ven['display_name']; ?>
                <option   <?php if($ress['vendor'] == $ven['id'] ){ ?> selected <?php
} ?> value="<?php echo $ven['id']; ?>"><?php echo $ven['display_name']; ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="col-lg-3 form-group kt-margin-b-10-tablet-and-mobile">
        <span><?php echo "Department"; ?>:</span>
        <select aria-label="Vendor" class="form-control kt-select2" id="department" name="vendor_id">
            <option value=""> All  </option>
            <?php foreach($empdep as $dep){ $emp_dep[$dep['id']]= $dep['department_name'];  ?>
            <option  <?php if($ress['department'] == $dep['id'] ){ ?> selected <?php
} ?> value="<?php echo $dep['id']; ?>"><?php echo $dep['department_name']; ?></option>
        <?php } ?>
    </select>
</div>
<div class="col-lg-3 form-group kt-margin-b-10-tablet-and-mobile">
    <span><?php echo "Designation"; ?>:</span>
    <select aria-label="Category" class="form-control kt-select2" id="designation" name="category_id">
        <option value=""> All  </option>
        <?php foreach($empdes as $des){  $emp_des[$des['id']]= $des['designation_name'];  ?>
        <option  <?php if($ress['designation'] == $des['id'] ){ ?> selected <?php
} ?> value="<?php echo $des['id']; ?>"><?php echo $des['designation_name']; ?></option>
    <?php } ?>
</select>
</div>
<div class="col-lg-3  form-group kt-margin-b-10-tablet-and-mobile mt-2">
    <span><?php echo "Location"; ?>:</span>
    <select aria-label="Location" class="form-control kt-select2" id="location" name="">
        <option value=""> All  </option>
        <?php foreach($emploc as $loc){ $emp_loc[$loc['id']]= $loc['location_name']; ?>
        <option   <?php if($ress['location'] == $loc['id'] ){ ?> selected <?php
} ?> value="<?php echo $loc['id']; ?>"><?php echo $loc['location_name']; ?></option>
    <?php } ?>
</select>
</div>

<div class="col-lg-3 form-group kt-margin-b-10-tablet-and-mobile mt-2">
    <span><?php echo "Assign Emloyees"; ?>:</span>
    <select aria-label="Check List" class="form-control kt-select2" id="check_list" name="">
        <option value=""> All  </option>
        <?php $empass= array('1'=>'Assign Employees','2'=>"Unassign Employees");
        foreach($empass as $empass_id=>$ass){ ?>
            <option   <?php if($ress['check_list'] == $empass_id){ ?> selected <?php
} ?> value="<?php echo $empass_id; ?>"><?php echo $ass; ?></option>
        <?php } ?>
    </select>
</div>
<div class="col-lg-3 form-group kt-margin-b-10-tablet-and-mobile mt-2">
    <span>Enetr Employee Name/Code</span>
    <input aria-label="Emp Name Code" type="text" class="form-control" name="emp_name_code"  value="<?php echo $_POST['emp_name_code']; ?>" id="emp_name_code">
</div>
<div class="col-lg-3 kt-margin-b-10-tablet-and-mobile">
    <span>&nbsp;</span>
    <input type="hidden" name="submit" value="search" />
    <div>
        <button class="btn btn-primary btn-brand--icon" type="button" id="kt_search" onclick="assignemployeelist(<?php echo $_POST['workorder_id'] ?>)">
            <span>

                <span>Search</span>
            </span>
        </button>
    </div>
</div>
</div>
<div class="separator separator-solid mb-3"></div>
<div class="dt-responsive table-responsive">
    <table class="table table-hover" id="kt_datatable1">
        <thead>
            <tr>
                <th scope="col">&nbsp;</th>
                <th scope="col">#</th>
                <th scope="col">Name </th>
                <th scope="col">Code </th>
                <th scope="col">Cost Center</th>
                <th scope="col">Location</th>
                <th scope="col">Department</th>
                <th scope="col">Designation</th>
                <th scope="col">Vendor</th>

            </tr>
        </thead>
        <tbody>
            <?php $i=1; foreach ($empdata as $row){ ?>
                <?php
                $bgtr = ''; $disable ='';
                if(in_array($row['id'],$esassignpayarea)){  $disable = "disabled"; $bgtr = 'bg-gray';
}
                if($_POST['check_list']==1 ){
                    $condition = $_POST['check_list']==1 ? "in_array(":"!in_array(";
                    if(in_array($row['id'],(array)$emp_sign)){ ?>
                        <tr class=" <?php echo $bgtr; ?>">
                            <td>
                                <div class="checkbox-zoom zoom-primary mt-1">
                                            <label class="checkbox checkbox-outline checkbox-primary">
                                                <input aria-label="Checkbox" type="checkbox" onclick="toinsertandpdateesignemployeeonlyone(<?php echo $row['id']; ?>,'<?php echo $row['display_name']; ?>')" class="itemRow" name="checkbox1" <?php if(in_array($row['id'],(array)$emp_sign)){ echo "checked";
}?> id="checkbox_<?php echo $row['id']; ?>_c1" type="checkbox" value="<?php echo $row['id']; ?>" <?php echo $disable; ?> onkeydown="" />    <span class="cr">
                                                  <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                                </span>
                                                <span></span>
                                            </label>
                                        </div>

                                </td>
                                <td><?php echo $i; ?></td>
                                <td>
                                    <div class="align-middle m-b-35">
                                        <?php $rrt =0; if($row['image']!="" && $rrt ==1) {?>

                                        <?php } else { ?>
                                            <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center" ><?php echo initials($row['display_name']); ?></span>
                                        <?php } ?>
                                        <div class="d-inline-block width73">
                                            <a href="#!" class="text-danger text-capitalize"><h6>   <?php echo $row['display_name']; ?></h6></a>
                                            <p class="text-muted m-b-0"><?php echo $row['email_id']; ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo $row['emp_code']; ?></td>
                                <td><?php echo $emp_cos[$row['cost_center_id']]; ?></td>
                                <td><?php echo $emp_loc[$row['location_id']]; ?></td>
                                <td><?php echo $emp_dep[$row['department_id']]; ?></td>
                                <td><?php echo $emp_des[$row['designation_id']]; ?></td>
                                <td><?php echo $emp_ven[$row['vendor_id']]; ?></td>
                            </tr>
                            <?php $i++;
}
}elseif( $_POST['check_list']==2){
                                if(!in_array($row['id'],(array)$emp_sign)){ ?>
                                    <tr class=" <?php echo $bgtr; ?>">
                                        <td><div class="checkbox-zoom zoom-primary mt-1">
                                            <label class="checkbox checkbox-outline checkbox-primary">
                                                <input aria-label="Checkbox" type="checkbox" onclick="toinsertandpdateesignemployeeonlyone(<?php echo $row['id']; ?>,'<?php echo $row['display_name']; ?>')" class="itemRow" name="checkbox1" <?php if(in_array($row['id'],(array)$emp_sign)){ echo "checked";
}?> id="checkbox_<?php echo $row['id']; ?>_c2" type="checkbox" value="<?php echo $row['id']; ?>" <?php echo $disable; ?> onkeydown="" />    <span class="cr">
                                                  <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                                </span>
                                                <span></span>
                                            </label>
                                        </div>
                                            </td>
                                            <td><?php echo $i; ?></td>
                                            <td>
                                                <div class="align-middle m-b-35">
                                                    <?php if($row['image']!=""&& $rrr==1) {?>

                                                    <?php } else { ?>
                                                        <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center" ><?php echo initials($row['display_name']); ?></span>
                                                    <?php } ?>
                                                    <div class="d-inline-block width73">
                                                        <a href="#!" class="text-danger text-capitalize"><h6>    <?php echo $row['display_name']; ?></h6></a>
                                                        <p class="text-muted m-b-0"><?php echo $row['email_id']; ?></p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?php echo $row['emp_code']; ?></td>
                                            <td><?php echo $row['email_id']; ?></td>
                                            <td><?php echo $emp_cos[$row['cost_center_id']]; ?></td>
                                            <td><?php echo $emp_loc[$row['location_id']]; ?></td>
                                            <td><?php echo $emp_dep[$row['department_id']]; ?></td>
                                            <td><?php echo $emp_des[$row['designation_id']]; ?></td>
                                            <td><?php echo $emp_ven[$row['vendor_id']]; ?></td>

                                        </tr>
                                        <?php $i++;
}
} else{ ?>
                                            <tr class=" <?php echo $bgtr; ?>">
                                                <td>
                                                <div class="checkbox-zoom zoom-primary mt-1">
                                            <label class="checkbox checkbox-outline checkbox-primary">
                                                <input aria-label="Checkbox" type="checkbox" onclick="toinsertandpdateesignemployeeonlyone(<?php echo $row['id']; ?>,'<?php echo $row['display_name']; ?>')" class="itemRow" name="checkbox1" <?php if(in_array($row['id'],(array)$emp_sign)){ echo "checked";
}?> id="checkbox_<?php echo $row['id']; ?>_all" type="checkbox" value="<?php echo $row['id']; ?>" <?php echo $disable; ?> onkeydown="" />    <span class="cr">
                                                  <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                                </span>
                                                <span></span>
                                            </label>
                                        </div>
                                                    </td>
                                                    <td><?php echo $i; ?></td>
                                                    <td>
                                                        <div class="align-middle m-b-35">
                                                            <?php if($row['image']!="" && $rrr==1) {?>

                                                            <?php } else { ?>
                                                                <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center" ><?php echo initials($row['display_name']); ?></span>
                                                            <?php } ?>
                                                            <div class="d-inline-block width73">
                                                                <a href="#!" class="text-danger text-capitalize"><h6>    <?php echo $row['display_name']; ?></h6></a>
                                                                <p class="text-muted m-b-0"><?php echo $row['email_id']; ?></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><?php echo $row['emp_code']; ?></td>

                                                    <td><?php echo $emp_cos[$row['cost_center_id']]; ?></td>
                                                    <td><?php echo $emp_loc[$row['location_id']]; ?></td>
                                                    <td><?php echo $emp_dep[$row['department_id']]; ?></td>
                                                    <td><?php echo $emp_des[$row['designation_id']]; ?></td>
                                                    <td><?php echo $emp_ven[$row['vendor_id']]; ?></td>

                                                </tr>
                                                <?php $i++; ?>
                                                <?php } ?>
                                                <?php } ?>

                                            </tbody>
                                        </table>
                                    </div>
                                    <input type="hidden" name="status" id='status_value' value="<?php echo $_POST['status']; ?>">
                                    <input type="hidden" name="table_name_post" id='table_name_post' value="<?php echo  $posttable; ?>">
                                    <input type="hidden" name="table" id='table' value="<?php echo $posttable; ?>">
                                    <input type="hidden" name="rid" id='rid' value="<?php echo $_POST['rid']; ?>">

                                    <input type="hidden" name="pay_area_id" id='pay_area_id' value="<?php echo $_POST['pay_area_id']; ?>">

                                    <input type="hidden" name="effective_date" id='effective_date' value="<?php echo $_POST['effective_date']; ?>">
                                    <input type="hidden" name="expire_date" id='expire_date' value="<?php echo $_POST['expire_date']; ?>">

                                </div>

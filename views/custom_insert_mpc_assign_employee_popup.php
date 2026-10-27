<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$empdata =array();
$ress =$_POST;
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
$vdata1 = array();
$vdata1["where"]["company_id"] = $_SESSION['company_id'];
$vdata1["where"]["is_active"] = 0;
$url = API."?mode=getassignemployeelistpopup&method=post";
$response = file_post_contents($url, $vdata1);
$empdata = json_decode($response,1);
$empdep = $empdata['department'];
$empdes = $empdata['designation'];
$emploc = $empdata['locations'];
$empven = $empdata['vendors'];
$empcos = $empdata['cost_centers'];
$vrcdetailurl = API."?mode=getvendorratecardidordetailsid&old_ref_no=".$_POST['old_ref_number']."&vrc_id=".$_POST['vrc_id']."&item_id=".$_POST['item_id'];
$vrcdetailrespons = callAPI($vrcdetailurl);
$vrcdetailrespons = json_decode($vrcdetailrespons,1);
$vrcdetailwh["where"] = array('transaction_id'=> $_POST['vrc_id'],'item_id'=>$_POST['item_id'],'company_id'=>$_SESSION['company_id'],'is_active'=>'0');
$vrcdetailwh["field"] = "id";
$vrcdetailencode = urlencode(json_encode($vrcdetailwh));
$vrcdetailurl = API."?mode=gettabledetailswithconditions&table=vrc_items&data=".$vrcdetailencode;
$vrcdetailres = callAPI($vrcdetailurl);
$vrcdetailres = json_decode($vrcdetailres,1);
$vrcdetail_id = $vrcdetailres[0]['id'];

$vdata1["where"]= array('work_order_id'=>$_POST['workorder_id'],'company_id'=>$_SESSION['company_id']);
$vdata = urlencode(json_encode($vdata1));
$vurl = API."?mode=gettabledetailswithconditions&table=emp_work_order&data=".$vdata;
$configesponse = callAPI($vurl);
$empesgindata = json_decode($configesponse,1);
foreach($empesgindata as $empesginda){
    $emp_sign[] = $empesginda['emp_id'];
    $vrc_item_id[$empesginda['emp_id']] = $empesginda['vrc_item_id'];
}
?>
<script type="text/javascript">
    function vrcandpoemployeeassign(emp_id,rid,item,type){
            jQuery.ajax({
                url: "views/custom_esign_purchaseorder_empassign_popup.php",
                data:'emp_id='+emp_id+'&rid='+rid+'&type='+type+'&item='+item,
                type: "POST",
                success:function(data){
                    $('#popupm').html(data);
                    $('#kt_modal_KTDatatable_remotem').modal("show");
                },
                error:function (){}
            });
        }
    function submitrom(){
        var len=0;
        var favorite = [];
        $.each($("input[name='checkbox1']:checked"), function(){
            favorite.push($(this).val());
            len=len+1;
        });
        $.ajax({
            url: "ajax.php",
            data:'page=esign_employee&emp_ids='+favorite+'&rid='+len,
            type: "POST",
            success:function(data){
                console.log(data);
                $("#dropdownlist").html(data);
            },
            error:function (){}
        });
    }
    function assignemployeelist(id){
        var table_name = document.getElementById('table_name').value;
        var department = document.getElementById('department').value;
        var designation = document.getElementById('designation').value;
        var location = document.getElementById('location').value;
        var vendor = document.getElementById('vendor').value;
        var cost_center_pp = document.getElementById('cost_center_pp').value;
        var check_list = document.getElementById('check_list').value;
        var workorder_detail_id = document.getElementById('workorder_detail_id').value;
        var vrc_id = document.getElementById('vrc_id').value;
        var item_id = document.getElementById('item_id').value;
        var vrcdetail_id = document.getElementById('vrcdetail_id').value;
        var item_price = document.getElementById('item_price').value;
        var effective_date= document.getElementById('effective_date').value;
        var expire_date= document.getElementById('expire_date').value;
        var emp_name_code= document.getElementById('emp_name_code').value;
        var old_ref_number= document.getElementById('old_ref_number').value;
        var min_exp= document.getElementById('min_exp').value;
        var max_exp= document.getElementById('max_exp').value;
        jQuery.ajax({
            url: "views/custom_insert_mpc_assign_employee_popup.php",
            data:'table='+table_name+'&workorder_id='+id+'&department='+department+'&designation='+designation+'&location='+location+'&vendor='+vendor+'&cost_center_pp='+cost_center_pp+'&check_list='+check_list+'&workorder_detail_id='+workorder_detail_id+'&vrc_id='+vrc_id+'&item_id='+item_id+'&vrcdetail_id='+vrcdetail_id+'&item_price='+item_price+'&effective_date='+effective_date+'&expire_date='+expire_date+'&emp_name_code='+emp_name_code+'&old_ref_number='+old_ref_number+'&min_exp='+min_exp+'&max_exp='+max_exp,
            type: "POST",
            success:function(data){
                $('#dynamic_popupxxl').html(data);
                $('#kt_modal_dynamic_popupxxl').modal({ show: true});
            },
            error:function (){}
        });
    }
    function toinsertandpdateesignemployeeonlyone(id,uname,newcreate = ""){
        var error = 0;
        var message = '';
        var table_name = document.getElementById('table_name').value;
        var workorder_id= document.getElementById('workorder_id').value;
        var workorder_detail_id= document.getElementById('workorder_detail_id').value;
        var item_price= document.getElementById('item_price').value;
        var effective_date= document.getElementById('effective_date').value;
        var expire_date= document.getElementById('expire_date').value;
        var select_job_title = $("#select_job_title"+id).val();
        var jobtitledetails = select_job_title.split("||");
        var vrc_id= jobtitledetails[0];
        var vrcdetail_id= jobtitledetails[1];
        console.log(jobtitledetails+" - "+vrcdetail_id);
        if($("#checkbox_"+id).prop('checked') == true){
            var inserttype = 'Y';
        }else{
            var inserttype = 'N';
        }
        if(workorder_id == ""){
            error = 1;
            message += " Purchase Orders Not Found";
        }if(workorder_detail_id == ""){
            error = 1;
            if(message !=""){
                message += " AND ";
            }
            message += " Purchase Order Jobtitle Not Found";
        }if(jobtitledetails == ""){
            error = 1;
            if(message !=""){
                message += " AND ";
            }
            message += " Vendor Ratecard AND Vendor Ratecard Jobtitle Not Found";
        }

        if(error == '1'){
            $("#checkbox_"+id).prop('checked', false);
            message = message.slice(0, -1);
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please contact your Admin for '+message
            });
        }else{
            $.ajax({
                url: "ajax.php",
                data:'page=insertpmcasignemployee&table_name='+table_name+'&workorder_id='+workorder_id+'&workorder_detail_id='+workorder_detail_id+'&vrc_id='+vrc_id+'&vrcdetail_id='+vrcdetail_id+'&emp_id='+id+'&item_price='+item_price+'&effective_date='+effective_date+'&expire_date='+expire_date+"&inserttype="+inserttype+"&newcreate="+newcreate,
                type: "POST",
                success:function(data){
                    console.log(data);
                    var returnres = JSON.parse(data)
                    console.log(returnres.other_work_order);
                    if(returnres.other_work_order== 'Y'){
                        Swal.fire({
                          title: 'Already Other Purchase Order Active This Employeee',
                          icon: 'info',
                          showDenyButton: true,
                          showCancelButton: false,
                          confirmButtonText: 'New Create',
                          denyButtonText: 'old continue',
                        }).then((result) => {
                          if (result.isConfirmed) {
                            toinsertandpdateesignemployeeonlyone(id,uname,"Y")
                          }else if (result.isDenied) {
                              $("#checkbox_"+id).prop('checked', false);
                          }
                        })


                    }else{
                        if($("#checkbox_"+id).prop('checked') == true){
                            tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success pt-4 pb-4','animated flipInX','animated flipOutX',uname+' Allocated From Purchase Order','');
                        } else {
                            $("#checkbox_"+id).attr('checked', false);
                            tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger pt-4 pb-4','animated flipInX','animated flipOutX',uname+' Removed From Purchase Order','');
                        }
                    }
                },
                error:function (){}
            });

        }
    }

    function assignemployeelistonly(id){
        var table_name = document.getElementById('table_name').value;
        jQuery.ajax({
            url: "views/custom_insert_mpc_assign_employee_popup.php",
            data:'table='+table_name+'&rid='+id+'&check_list=1',
            type: "POST",
            success:function(data){
                $('#dynamic_popupxxl').html(data);
                $('#kt_modal_dynamic_popupxxl').modal({ show: true});
            },
            error:function (){}
        });
    }

</script>
<style>
table.custom_asign_emp_mpc tr th, table.custom_asign_emp_mpc tr td {
    white-space: normal;
}
</style>
<div class="modal-header">
    <h5 class="modal-title">Assign Employees</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body  j-pro">

        <div class="row kt-margin-b-20 mb-2 j-content" >
            <div class="col-lg-3 form-group kt-margin-b-10-tablet-and-mobile">
                <span><?php echo "Cost Center"; ?>:</span>
                <select aria-label="Cost Center Pp" class=" form-select js-example-basic-single" id="cost_center_pp" name="">
                    <option value=""> All  </option>
                    <?php foreach($empcos as $cos){ $emp_cos[$cos['id']]= $cos['cost_center_name']; ?>
                    <option   <?php if($ress['cost_center_pp'] == $cos['id'] ){ ?> selected <?php
} ?> value="<?php echo $cos['id']; ?>"><?php echo $cos['cost_center_name']; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-lg-3  form-group kt-margin-b-10-tablet-and-mobile">
            <span><?php echo "Vendors"; ?>:</span>
            <select aria-label="Vendor" class=" form-select js-example-basic-single" id="vendor" name="">
                <option value=""> All  </option>
                <?php foreach($empven as $ven){ $emp_ven[$ven['id']]= $ven['display_name']; ?>
                <option   <?php if($ress['vendor'] == $ven['id'] ){ ?> selected <?php
} ?> value="<?php echo $ven['id']; ?>"><?php echo $ven['display_name']; ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="col-lg-3 form-group kt-margin-b-10-tablet-and-mobile">
        <span><?php echo "Department"; ?>:</span>
        <select aria-label="Vendor" class=" form-select js-example-basic-single" id="department" name="vendor_id">
            <option value=""> All  </option>
            <?php foreach($empdep as $dep){ $emp_dep[$dep['id']]= $dep['department_name'];  ?>
            <option  <?php if($ress['department'] == $dep['id'] ){ ?> selected <?php
} ?> value="<?php echo $dep['id']; ?>"><?php echo $dep['department_name']; ?></option>
        <?php } ?>
    </select>
</div>
<div class="col-lg-3 form-group kt-margin-b-10-tablet-and-mobile">
    <span><?php echo "Designation"; ?>:</span>
    <select aria-label="Category" class=" form-select js-example-basic-single" id="designation" name="category_id">
        <option value=""> All  </option>
        <?php foreach($empdes as $des){  $emp_des[$des['id']]= $des['designation_name'];  ?>
        <option  <?php if($ress['designation'] == $des['id'] ){ ?> selected <?php
} ?> value="<?php echo $des['id']; ?>"><?php echo $des['designation_name']; ?></option>
    <?php } ?>
</select>
</div>
<div class="col-lg-3  form-group kt-margin-b-10-tablet-and-mobile mt-2">
    <span><?php echo "Location"; ?>:</span>
    <select aria-label="Location" class=" form-select js-example-basic-single" id="location" name="">
        <option value=""> All  </option>
        <?php foreach($emploc as $loc){ $emp_loc[$loc['id']]= $loc['location_name']; ?>
        <option   <?php if($ress['location'] == $loc['id'] ){ ?> selected <?php
} ?> value="<?php echo $loc['id']; ?>"><?php echo $loc['location_name']; ?></option>
    <?php } ?>
</select>
</div>

<div class="col-lg-3 form-group kt-margin-b-10-tablet-and-mobile mt-2">
    <span><?php echo "Assign Emloyees"; ?>:</span>
    <select aria-label="Check List" class=" form-select js-example-basic-single" id="check_list" name="">
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
    <table class="table table-hover custom_asign_emp_mpc" id="kt_datatable1" >
        <thead >
            <tr>
                <th scope="col" style="width: 2%;">&nbsp;</th>
                <th scope="col" style="width: 2%;">#</th>
                <th scope="col" style="width: 15%;">Name </th>

                <th scope="col" style="width: 15%;">Cost Center</th>
                <th scope="col" style="width: 10%;">Location</th>
                <th scope="col" style="width: 10%;">Department</th>
                <th scope="col" style="width: 10%;">Designation</th>
                <th scope="col" style="width: 10%;">Vendor</th>
                <th scope="col" style="width: 25%;">Job Title</th>

            </tr>
        </thead>
        <tbody >
        <?php  $i=1; foreach ($empdata['emp_list'] as $row){ ?>
                ?>
                <tr >
                            <td class="p-2">
                                <label class="kt-checkbox">
                                    <input aria-label="Input field" onclick="vrcandpoemployeeassign(<?php echo $row['id'].",".$_POST['workorder_id'].",".$_POST['workorder_detail_id'].",'mpc'"; ?>)" class="itemRow" name="checkbox1" <?php if(in_array($row['id'],(array)$emp_sign)){ echo "checked";
}?> id="checkbox_<?php echo $row['id']; ?>" type="checkbox" value="<?php echo $row['id']; ?>" onkeydown="">  <span> </span> </label>
                                </td>
                                <td class="p-2"><?php echo $i; ?></td>
                                <td class="p-2">
                                    <div class="align-middle">
                                        <?php if($row['image']!="") {?>
                                            <img src="uploads/all/<?php echo $row['image']; ?>"   class="img-radius img-50 align-top m-r-15" alt="Attachment Preview">
                                        <?php } else { ?>
                                            <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center" ><?php echo initials($row['display_name']); ?></span>
                                        <?php } ?>
                                        <div class="d-inline-block width73">
                                            <a href="#!" class="text-danger text-capitalize"><h6>   <?php echo $row['display_name']; ?></h6></a>
                                            <p class="text-muted m-b-0"><?php echo $row['email_id']; ?></p>
                                        </div>
                                    </div>
                                </td>


        <td class="p-2"><p><?php echo $emp_cos[$row['cost_center_id']]; ?></p></td>
        <td class="p-2"><p><?php echo $emp_loc[$row['location_id']]; ?></p></td>
        <td class="p-2"><p><?php echo $emp_dep[$row['department_id']]; ?></p></td>
        <td class="p-2"><p><?php echo $emp_des[$row['designation_id']]; ?></p></td>
        <td class="p-2"><p><?php echo $emp_ven[$row['vendor_id']]; ?></p></td>
                    <td class="p-2">

                                <select aria-label="Select option" class=" form-select js-example-basic-single" id="select_job_title<?php echo $row['id']; ?>" name="" style="width:200px;">
                <?php if(count((array)$vrcdetailrespons) > 0){ ?><option value=""> Select Job title  </option> <?php
} ?>
        <?php foreach($vrcdetailrespons as $vrcdetailrespone){
            $selected = "";
if($vrc_item_id[$row['id']]==$vrcdetailrespone['vrc_item_id'] || ($vrcdetailrespone['min_exp']==$_POST['min_exp'] && $vrcdetailrespone['max_exp']==$_POST['max_exp'])){
    $selected = "selected";
}
            ?>
            <option value="<?php echo $vrcdetailrespone['vrc_id']."||".$vrcdetailrespone['vrc_item_id']; ?>"  <?php echo $selected;  ?> ><?php echo $vrcdetailrespone['job_title']."(".$vrcdetailrespone['min_exp']." To ".$vrcdetailrespone['max_exp'].")"; ?></option>
        <?php } ?>
    </select>
        </td>
    </tr>
    <?php $i++;
} ?>


                                            </tbody>
                                        </table>
                                    </div>
                                    <input type="hidden" name="status" id='status_value' value="<?php echo $_POST['status']; ?>">
                                    <input type="hidden" name="table_name" id='table_name' value="<?php echo $_POST['table']; ?>">
                                    <input type="hidden" name="table" id='table' value="<?php echo $_POST['table']; ?>">
                                    <input type="hidden" name="rid" id='rid' value="<?php echo $_POST['rid']; ?>">
                                    <input type="hidden" name="extra" id='extra' value="<?php echo $_POST['extra']; ?>">

                                    <input type="hidden" name="workorder_id" id='workorder_id' value="<?php echo $_POST['workorder_id']; ?>">
                                    <input type="hidden" name="workorder_detail_id" id='workorder_detail_id' value="<?php echo $_POST['workorder_detail_id']; ?>">
                                    <input type="hidden" name="vrc_id" id='vrc_id' value="<?php echo $_POST['vrc_id']; ?>">
                                    <input type="hidden" name="item_id" id='item_id' value="<?php echo $_POST['item_id']; ?>">
                                    <input type="hidden" name="vrcdetail_id" id='vrcdetail_id' value="<?php echo $vrcdetail_id; ?>">
                                    <input type="hidden" name="item_price" id='item_price' value="<?php echo $_POST['item_price']; ?>">

                                    <input type="hidden" name="effective_date" id='effective_date' value="<?php echo $_POST['effective_date']; ?>">
                                    <input type="hidden" name="expire_date" id='expire_date' value="<?php echo $_POST['expire_date']; ?>">
                                    <input type="hidden" name="old_ref_number" id='old_ref_number' value="<?php echo $_POST['old_ref_number']; ?>">
                                    <input type="hidden" name="min_exp" id='min_exp' value="<?php echo $_POST['min_exp']; ?>">
                                    <input type="hidden" name="max_exp" id='max_exp' value="<?php echo $_POST['max_exp']; ?>">

                                </div>

    </div>

<script type="text/javascript" src="assets/bower_components/select2/js/select2.full.min.js"></script>

                    <script type="text/javascript" src="assets/bower_components/bootstrap-multiselect/js/bootstrap-multiselect.js">
                    </script>
                    <script type="text/javascript" src="assets/bower_components/multiselect/js/jquery.multi-select.js"></script>
                    <script type="text/javascript" src="assets/js/jquery.quicksearch.js"></script>
                    <!-- Custom js -->
                    <script type="text/javascript" src="assets/pages/advance-elements/select2-custom.js"></script><!-- Switch component js -->

                    <script type="text/javascript" src="assets/bower_components/switchery/js/switchery.min.js"></script>

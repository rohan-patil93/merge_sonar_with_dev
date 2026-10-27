<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$empdata =array();
$ress =$_POST;
$vdata1 = array();
if($_POST['department']!=""){
    $vdata1["where"]["department_id"] = $_POST['department'];
}
if($_POST['designation']!=""){
    $vdata1["where"]["designation_id"] = $_POST['designation'];
}
if($_POST['location']!=""){
    $vdata1["where"]["location_id"] = $_POST['location'];
}
if($_POST['vendor']!=""){
    $vdata1["where"]["vendor_id"] = $_POST['vendor'];
}
if($_POST['cost_center_pp']!=""){
    $vdata1["where"]["cost_center_id"] = $_POST['cost_center_pp'];
}
if($_POST['emp_name_code']!=""){
    $vdata1["where"]["emp_name_code"] = $_POST['emp_name_code'];
}

$vdata1["where"]["company_id"] = $_SESSION['company_id'];
$vdata1["where"]["is_active"] = 0;
$url = API."?mode=getmilestoneAssignEmployeeListPopup&transaction_id=".$_POST['transaction_id']."&item_id=".$_POST['item_id']."&method=post";
$response = file_post_contents($url, $vdata1);
$empdata = json_decode($response,1);
$empdep = $empdata['department'];
$empdes = $empdata['designation'];
$emploc = $empdata['locations'];
$empven = $empdata['vendors'];
$empcos = $empdata['cost_centers'];
$emp_ids = $empdata['emp_ids'];
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
        var transaction_id = document.getElementById('transaction_id').value;
        var item_id = document.getElementById('item_id').value;
        var department = document.getElementById('department').value;
        var designation = document.getElementById('designation').value;
        var location = document.getElementById('location').value;
        var vendor = document.getElementById('vendor').value;
        var cost_center_pp = document.getElementById('cost_center_pp').value;
        var check_list = document.getElementById('check_list').value;
        var emp_name_code = document.getElementById('emp_name_code').value;
        jQuery.ajax({
            url: "views/custom_insert_milestone_assign_employee_popup.php",
            data:'table='+table_name+'&item_id='+item_id+'&department='+department+'&designation='+designation+'&location='+location+'&vendor='+vendor+'&cost_center_pp='+cost_center_pp+'&check_list='+check_list+'&transaction_id='+transaction_id+'&emp_name_code='+emp_name_code,
            type: "POST",
            success:function(data){
                $('#dynamic_popupxxl').html(data);
                $('#kt_modal_dynamic_popupxxl').modal({ show: true});
            },
            error:function (){}
        });
    }
    function msemployeeassign(emp_id, name){
        var transaction_id= document.getElementById('transaction_id').value;
        var item_id= document.getElementById('item_id').value;

        console.log('transaction_id'+transaction_id+"item_id"+item_id+"emp_id"+emp_id);
        $.ajax({
                url: "ajax.php",
                data:'page=addupdateempassignmilestone&transaction_id='+transaction_id+'&item_id='+item_id+'&emp_id='+emp_id,
                type: "POST",
                success:function(data){
                    var returnres = JSON.parse(data);
                    if(returnres.check ==0){
                        tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success pt-4 pb-4','animated flipInX','animated flipOutX',name+' Added On Mile Stone','');
                    }else{
                        tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger pt-4 pb-4','animated flipInX','animated flipOutX',name+' Removed From  Mile Stone','');
                    }

                },
                error:function (){}
            });


    }

    function assignemployeelistonly(id){
        var table_name = document.getElementById('table_name').value;
        jQuery.ajax({
            url: "views/custom_insert_milestone_assign_employee_popup.php",
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
    <h5 class="modal-title">Assign Employees s</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
    <div class="modal-body  j-pro">
        <div class="row kt-margin-b-20 mb-2 j-content" >
        <div class="col-lg-2  form-group kt-margin-b-10-tablet-and-mobile">
            <span><?php echo "Vendors"; ?>:</span>
            <select aria-label="Vendor" class=" form-select js-example-basic-single" id="vendor" name="">
                <option value=""> All  </option>
                <?php foreach($empven as $ven){ $emp_ven[$ven['id']]= $ven['display_name']; ?>
                <option   <?php if($ress['vendor'] == $ven['id'] ){ ?> selected <?php
} ?> value="<?php echo $ven['id']; ?>"><?php echo $ven['display_name']; ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="col-lg-2 form-group kt-margin-b-10-tablet-and-mobile">
                <span><?php echo "Cost Center"; ?>:</span>
                <select aria-label="Cost Center Pp" class=" form-select js-example-basic-single" id="cost_center_pp" name="">
                    <option value=""> All  </option>
                    <?php foreach($empcos as $cos){ $emp_cos[$cos['id']]= $cos['cost_center_name']; ?>
                    <option   <?php if($ress['cost_center_pp'] == $cos['id'] ){ ?> selected <?php
} ?> value="<?php echo $cos['id']; ?>"><?php echo $cos['cost_center_name']; ?></option>
                <?php } ?>
            </select>
        </div>

        <input type="hidden" name="department" id="department_2" value="">
        <div class="col-lg-2 form-group kt-margin-b-10-tablet-and-mobile">
            <span><?php echo "Designation"; ?>:</span>
            <select aria-label="Category" class=" form-select js-example-basic-single" id="designation" name="category_id">
                <option value=""> All  </option>
                <?php foreach($empdes as $des){  $emp_des[$des['id']]= $des['designation_name'];  ?>
                <option  <?php if($ress['designation'] == $des['id'] ){ ?> selected <?php
} ?> value="<?php echo $des['id']; ?>"><?php echo $des['designation_name']; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-lg-2  form-group kt-margin-b-10-tablet-and-mobile ">
            <span><?php echo "Location"; ?>:</span>
            <select aria-label="Location" class=" form-select js-example-basic-single" id="location" name="">
                <option value=""> All  </option>
                <?php foreach($emploc as $loc){ $emp_loc[$loc['id']]= $loc['location_name']; ?>
                <option   <?php if($ress['location'] == $loc['id'] ){ ?> selected <?php
} ?> value="<?php echo $loc['id']; ?>"><?php echo $loc['location_name']; ?></option>
                <?php } ?>
            </select>
        </div>
        <input type="hidden" name="check_list" id="check_list_2" value="">
        <div class="col-lg-2 form-group kt-margin-b-10-tablet-and-mobile">
            <span>Enetr Employee Name/Code</span>
            <input aria-label="Emp Name Code" type="text" class="form-control" name="emp_name_code"  value="<?php echo $_POST['emp_name_code']; ?>" id="emp_name_code">
        </div>

        <div class="col-lg-2 kt-margin-b-10-tablet-and-mobile">
            <span>&nbsp;</span>
            <input type="hidden" name="submit" value="search" />
            <div>
                <button class="btn btn-primary btn-brand--icon" type="button" id="kt_search" onclick="assignemployeelist()">
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
                <th scope="col" style="width: 10%;">Vendor</th>
                <th scope="col" style="width: 15%;">Cost Center</th>
                <th scope="col" style="width: 10%;">Designation</th>
                <th scope="col" style="width: 10%;">Location</th>


            </tr>
        </thead>
        <tbody >
            <?php  $i=1; foreach ($empdata['emp_list'] as $row){ ?>

                <tr >
                    <td class="p-2">
                        <label class="kt-checkbox">
                            <input aria-label="Input field" onclick="msemployeeassign(<?php echo $row['id']; ?>,'<?php echo $row['display_name']; ?>')" class="itemRow" name="checkbox1" <?php if(in_array($row['id'],(array)$emp_ids)){ echo "checked";
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
                                <a href="#!" class="text-danger text-capitalize"><h6>   <?php echo $row['display_name']." - ".$row['emp_code']; ?></h6></a>
                                <p class="text-muted m-b-0"><?php echo $row['email_id']; ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="p-2"><p><?php echo $emp_ven[$row['vendor_id']]; ?></p></td>

                    <td class="p-2"><p><?php echo $emp_cos[$row['cost_center_id']]; ?></p></td>
                    <td class="p-2"><p><?php echo $emp_des[$row['designation_id']]; ?></p></td>
                    <td class="p-2"><p><?php echo $emp_loc[$row['location_id']]; ?></p></td>


                </tr>
            <?php $i++;
} ?>


            </tbody>
        </table>
    </div>
    <input type="hidden" name="transaction_id" id='transaction_id' value="<?php echo $_POST['transaction_id']; ?>">
    <input type="hidden" name="table_name" id='table_name' value="<?php echo $_POST['table']; ?>">
    <input type="hidden" name="item_id" id='item_id' value="<?php echo $_POST['item_id']; ?>">
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

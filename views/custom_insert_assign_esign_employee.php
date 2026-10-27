<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

    $empdata =array(); $ress =$_POST;
    if($_POST['department']!=""){$vdata1["where"]["department_id"] = $_POST['department'];
}
    if($_POST['designation']!=""){$vdata1["where"]["designation_id"] = $_POST['designation'];
}
    if($_POST['location']!=""){$vdata1["where"]["location_id"] = $_POST['location'];
}
    if($_POST['vendor']!=""){$vdata1["where"]["vendor_id"] = $_POST['vendor'];
}
    if($_POST['cost_center_pp']!=""){$vdata1["where"]["cost_center_id"] = $_POST['cost_center_pp'];
}
    $vdata = urlencode(json_encode($vdata1));
    $vurl = API."?mode=gettabledetailswithconditions&table=employees&data=".$vdata;
    $configesponse = callAPI($vurl);
    $empdata = json_decode($configesponse,1);

$empurl = API."?mode=gettabledetails&table=departments";
$empres = callAPI($empurl);
$empdep = json_decode($empres,1);

$empurl = API."?mode=gettabledetails&table=designations";
$empres = callAPI($empurl);
$empdes = json_decode($empres,1);

$empurl = API."?mode=gettabledetails&table=locations";
$empres = callAPI($empurl);
$emploc = json_decode($empres,1);

$empurl = API."?mode=gettabledetails&table=vendors";
$empres = callAPI($empurl);
$empven = json_decode($empres,1);

$empurl = API."?mode=gettabledetails&table=cost_centers";
$empres = callAPI($empurl);
$empcos = json_decode($empres,1);

    $vdata1["where"]= array('is_active'=>0,'esgin_budget_approval_id'=>$_POST['rid'],'eba_vendors_allocation_id'=>$_POST['eba']);
    $vdata = urlencode(json_encode($vdata1));
    $vurl = API."?mode=gettabledetailswithconditions&table=esgin_employees&data=".$vdata;
    $configesponse = callAPI($vurl);
    $empesgindata = json_decode($configesponse,1);
    foreach($empesgindata as $empesginda){
        $emp_sign[] = $empesginda['emp_id'];
    }

?>
<div class="modal-header">
    <h5 class="modal-title">Assign Employees</h5>
    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
        <i aria-hidden="true" class="ki ki-close"></i>X
    </button>
</div>

        <div class="modal-body">

    <div class="row kt-margin-b-20 mb-2" >
        <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile">
            <span><?php echo "Cost Center"; ?>:</span>
            <select aria-label="Cost Center Pp" class="form-select js-example-basic-single" id="cost_center_pp" name="">
                <option value=""> All  </option>
                <?php foreach($empcos as $cos){ $emp_cos[$cos['id']]= $cos['cost_center_name']; ?>
                <option   <?php if($ress['cost_center_pp'] == $cos['id'] ){ ?> selected <?php
} ?> value="<?php echo $cos['id']; ?>"><?php echo $cos['cost_center_name']; ?></option>
            <?php } ?>
            </select>
        </div>
        <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile">
            <span><?php echo "Vendors"; ?>:</span>
            <select aria-label="Vendor" class="form-select js-example-basic-single" id="vendor" name="">
                <option value=""> All  </option>
                <?php foreach($empven as $ven){ $emp_ven[$ven['id']]= $ven['display_name']; ?>
                <option   <?php if($ress['vendor'] == $ven['id'] ){ ?> selected <?php
} ?> value="<?php echo $ven['id']; ?>"><?php echo $ven['display_name']; ?></option>
            <?php } ?>
            </select>
        </div>
        <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile">
            <span><?php echo "Department"; ?>:</span>
            <select aria-label="Vendor" class="form-select js-example-basic-single" id="department" name="vendor_id">
                <option value=""> All  </option>
                <?php foreach($empdep as $dep){ $emp_dep[$dep['id']]= $dep['department_name'];  ?>
                <option  <?php if($ress['department'] == $dep['id'] ){ ?> selected <?php
} ?> value="<?php echo $dep['id']; ?>"><?php echo $dep['department_name']; ?></option>
            <?php } ?>
            </select>
        </div>
        <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile">
            <span><?php echo "Designation"; ?>:</span>
            <select aria-label="Category" class="form-select js-example-basic-single" id="designation" name="category_id">
                <option value=""> All  </option>
                <?php foreach($empdes as $des){  $emp_des[$des['id']]= $des['designation_name'];  ?>
                <option  <?php if($ress['designation'] == $des['id'] ){ ?> selected <?php
} ?> value="<?php echo $des['id']; ?>"><?php echo $des['designation_name']; ?></option>
            <?php } ?>
            </select>
        </div>
        <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile mt-2">
            <span><?php echo "Location"; ?>:</span>
            <select aria-label="Location" class="form-select js-example-basic-single" id="location" name="">
                <option value=""> All  </option>
                <?php foreach($emploc as $loc){ $emp_loc[$loc['id']]= $loc['location_name']; ?>
                <option   <?php if($ress['location'] == $loc['id'] ){ ?> selected <?php
} ?> value="<?php echo $loc['id']; ?>"><?php echo $loc['location_name']; ?></option>
            <?php } ?>
            </select>
        </div>


        <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile mt-2">
            <span><?php echo "Assign Emloyees"; ?>:</span>
            <select aria-label="Check List" class="form-select js-example-basic-single" id="check_list" name="">
                <option value=""> All  </option>
                <?php $empass= array('1'=>'Assign Employees','2'=>"Unassign Employees");
                foreach($empass as $empass_id=>$ass){ ?>
                <option   <?php if($ress['check_list'] == $empass_id){ ?> selected <?php
} ?> value="<?php echo $empass_id; ?>"><?php echo $ass; ?></option>
            <?php } ?>
            </select>
        </div>
        <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile">
            <span>&nbsp;</span>
            <input type="hidden" name="submit" value="search" />
            <div>
            <button class="btn btn-primary btn-brand--icon" type="button" id="kt_search" onclick="assignemployeelist(<?php echo $_POST['rid'] ?>)">
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
                    <th scope="col">Email</th>
                    <th scope="col">Cost Center</th>
                    <th scope="col">Location</th>
                    <th scope="col">Department</th>
                    <th scope="col">Designation</th>
                    <th scope="col">Vendor</th>

                </tr>
            </thead>
            <tbody>
                    <?php $i=1; foreach ($empdata as $row){
                        $show_row = false;
                        if($_POST['check_list']==1 ){
                            $show_row = in_array($row['id'],(array)$emp_sign);
                        } elseif( $_POST['check_list']==2){
                            $show_row = !in_array($row['id'],(array)$emp_sign);
                        } else {
                            $show_row = true;
                        }
                        if($show_row){ ?>
                            <tr>
                                <td>
                                    <label class="kt-checkbox">
                                    <input aria-label="Input field" onclick="toinsertandpdateesignemployeeonlyone(<?php echo $row['id']; ?>)" class="itemRow" name="checkbox1" <?php if(in_array($row['id'],(array)$emp_sign)){ echo "checked";
}?> id="checkbox_<?php echo $row['id']; ?>" type="checkbox" value="<?php echo $row['id']; ?>" onkeydown="">  <span> </span> </label>
                                </td>
                                <td><?php echo $i; ?></td>
                                <td><?php echo $row['display_name']; ?></td>
                                <td><?php echo $row['email_id']; ?></td>
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
            <input type="hidden" name="table_name" id='table_name' value="<?php echo $_POST['table']; ?>">
            <input type="hidden" name="table" id='table' value="<?php echo $_POST['table']; ?>">
            <input type="hidden" name="rid" id='rid' value="<?php echo $_POST['rid']; ?>">
            <input type="hidden" name="eba" id='eba' value="<?php echo $_POST['eba']; ?>">
            <input type="hidden" name="extra" id='extra' value="<?php echo $_POST['extra']; ?>">
        </div>


</div>
<script type="text/javascript">
    function toinsertandpdateesignemployeeonlyone(id){
        var table_name = document.getElementById('table_name').value;
        var len= document.getElementById('rid').value;
        var cid =document.getElementById('checkbox_'+id);
        var mtid =document.getElementById('eba');
        if(id == "" || len == "" || mtid =="" ){
            console.log("work");
            alert("check");
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please contact your Admin for '
            });
        }else{
        jQuery.ajax({
             url: "ajax.php",
             data:'page=toinsertandpdateesignemployeeonlyone&table_name='+table_name+'&emp_id='+id+'&rid='+len+'&eba='+$('#eba').val(),
             type: "POST",
             success:function(data){
            console.log(data);
            if($("#checkbox_"+id).prop('checked') == true){
                        tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success pt-4 pb-4','animated flipInX','animated flipOutX',' Employee Allocated to esign','');
                    } else {
                        tostarnotify('top','right','fa fa-thumbs-up','form-label label label-danger pt-4 pb-4','animated flipInX','animated flipOutX','Employee Removed Form esign','');
                    }
            $("#dropdownlist").html(data);
         },
             error:function (){}
          });
       }
    }

    function submitrom(){
        alert("Hai");
        var len=0;
        var favorite = [];
        $.each($("input[name='checkbox1']:checked"), function(){
             favorite.push($(this).val());
            len=len+1;
        });

        alert(len);
        alert(favorite);
        $.ajax({
             url: "ajax.php",
             data:'page=esign_employee&emp_ids='+favorite+'&rid='+$('#rid').val()+'&eba='+$('#eba').val(),
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

        jQuery.ajax({
            url: "views/custom_insert_assign_employee.php",
            data:'table='+table_name+'&rid='+id+'&department='+department+'&designation='+designation+'&location='+location+'&vendor='+vendor+'&cost_center_pp='+cost_center_pp+'&check_list='+check_list,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal({ show: true});
            },
            error:function (){}
        });
    }


    function assignemployeelistonly(id){
        var table_name = document.getElementById('table_name').value;
        jQuery.ajax({
            url: "views/custom_insert_assign_employee.php",
            data:'table='+table_name+'&rid='+id+'&check_list=1&eba=',
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal({ show: true});
            },
            error:function (){}
        });
    }

</script>
<script type="text/javascript">
  $(".js-example-basic-single").select2();
  $(".js-example-basic-multiple").select2();
  $(".js-example-disabled-results").select2();
</script>

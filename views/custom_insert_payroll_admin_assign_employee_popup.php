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

$empurl = API."?mode=gettabledetails&table=pay_areas";
$empres = callAPI($empurl);
$payareadata = json_decode($empres,1);

$vdata1["where"]= array('is_active'=>0,'id'=>$_POST['payroll_admin_id']);

$vdata = urlencode(json_encode($vdata1));
$vurl = API."?mode=gettabledetailswithconditions&table=payroll_admins&data=".$vdata;
$empres = callAPI($vurl);
$payroll_admins = json_decode($empres,1);
$payroll_admin = $payroll_admins['0'];

$vdata1["where"]= array('is_active'=>0);
$vdata = urlencode(json_encode($vdata1));
$vurl = API."?mode=gettabledetailswithconditions&table=".$posttable."&data=".$vdata;
$configesponse = callAPI($vurl);
$empesgindata = json_decode($configesponse,1);
$esassignpayarea = array();
$emp_signss = array();
$emp_sign = array();
foreach($empesgindata as $empesginda){
    if($empesginda['user_id'] == $payroll_admin['user_id'] ){
        $emp_signss[] = $empesginda['pay_area_id'];
    }else{
        $esassignpayarea[] = $empesginda['pay_area_id'];
    }
    $emp_sign[] = $empesginda['pay_area_id'];
}

?>
<script type="text/javascript">

    function toinsertandpdateesignemployeeonlyone(pay_area_id,uname){
        var error = 0;
        var message = '';
        var table_name = document.getElementById('table_name_post').value;
        var effective_date= document.getElementById('effective_date').value;
        var expire_date= document.getElementById('expire_date').value;
        var id = document.getElementById('user_id').value;
        var payroll_admin_id = document.getElementById('payroll_admin_id').value;
        $.ajax({
            url: "ajax.php",
            data:'page=insertpayrolladminasignemployee&table_name='+table_name+'&user_id='+id+'&effective_date='+effective_date+'&expire_date='+expire_date+'&pay_area_id='+pay_area_id,
            type: "POST",
            success:function(data){
                console.log(data);
                if($("#checkbox_"+payroll_admin_id).prop('checked') == true){
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
    <h5 class="modal-title">Assign Pay Areas</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

</div>

</div>
<div class="separator separator-solid mb-3"></div>
<div class="m-l-50 m-r-50">
    <table class="table table-hover" id="kt_datatable1">
        <thead>
            <tr>
                <th scope="col">&nbsp;</th>
                <th scope="col">#</th>
                <th scope="col">Pay Area</th>
                <th scope="col">Salary Run Day </th>
                <th scope="col">Salary Pay Day</th>
            </tr>
        </thead>
        <tbody>
            <?php $i=1;
            foreach ($payareadata as $row){  ?>
                <?php  $bgtr = ''; $disable ='';
                if(in_array($row['id'],$esassignpayarea)){  $disable = "disabled"; $bgtr = 'bg-gray';
}
                 if($_POST['check_list']==1 ){

                    $condition = $_POST['check_list']==1 ? "in_array(":"!in_array(";
                    if(in_array($row['id'],(array)$emp_sign)){ ?>
                        <tr class=" <?php echo $bgtr; ?>">
                            <td>
                                <div class="checkbox-zoom zoom-primary mt-1">
                                            <label class="checkbox checkbox-outline checkbox-primary">
                                                <input aria-label="Checkbox" type="checkbox" onclick="toinsertandpdateesignemployeeonlyone(<?php echo $row['id']; ?>,'<?php echo $row['id']; ?>')"  class="itemRow" name="checkbox1" <?php if(in_array($row['id'],(array)$emp_sign)){ echo "checked";
}?> id="checkbox_<?php echo $row['id']; ?>_c1" type="checkbox" value="<?php echo $row['id']; ?>" onkeydown="" />    <span class="cr" <?php echo $disable; ?>>
                                                  <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                                </span>
                                                <span></span>
                                            </label>
                                        </div>

                                </td>
                                <td><?php echo $i; ?></td>

                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo $row['salary_run_day']; ?></td>
                                <td><?php echo $row['salary_pay_day']; ?></td>

                            </tr>
                            <?php $i++;
}
}elseif( $_POST['check_list']==2){
                                if(!in_array($row['id'],(array)$emp_sign)){ ?>
                                    <tr class=" <?php echo $bgtr; ?>">
                                        <td>
                                            <div class="checkbox-zoom zoom-primary mt-1">
                                            <label class="checkbox checkbox-outline checkbox-primary">
                                                <input aria-label="Checkbox" type="checkbox" onclick="toinsertandpdateesignemployeeonlyone(<?php echo $row['id']; ?>,'<?php echo $row['id']; ?>','<?php echo $row['display_name']; ?>')"  class="itemRow" name="checkbox1" <?php if(in_array($row['id'],(array)$emp_sign)){ echo "checked";
}?> id="checkbox_<?php echo $row['id']; ?>_c2" type="checkbox" value="<?php echo $row['id']; ?>" <?php echo $disable; ?> onkeydown="" />    <span class="cr">
                                                  <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                                </span>
                                                <span></span>
                                            </label>
                                        </div>

                                            </td>
                                            <td><?php echo $i; ?></td>

                                            <td><?php echo $row['name']; ?></td>
                                            <td><?php echo $row['salary_run_day']; ?></td>
                                            <td><?php echo $emp_cos[$row['salary_run_day']]; ?></td>


                                        </tr>
                                        <?php $i++;
}
} else{ ?>
                                            <tr class=" <?php echo $bgtr; ?>">
                                                <td>
                                                    <div class="checkbox-zoom zoom-primary mt-1">
                                            <label class="checkbox checkbox-outline checkbox-primary">
                                                <input aria-label="Checkbox" type="checkbox" onclick="toinsertandpdateesignemployeeonlyone(<?php echo $row['id']; ?>,'<?php echo $row['id']; ?>','<?php echo $row['display_name']; ?>')"  class="itemRow" name="checkbox1" <?php if(in_array($row['id'],(array)$emp_sign)){ echo "checked";
}?> id="checkbox_<?php echo $row['id']; ?>_all" type="checkbox" value="<?php echo $row['id']; ?>"  <?php echo $disable; ?> onkeydown="" />    <span class="cr">
                                                  <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                                </span>
                                                <span></span>
                                            </label>
                                        </div>
                                                    </td>
                                                    <td><?php echo $i; ?></td>

                                                    <td><?php echo $row['name']; ?></td>

                                                    <td><?php echo $row['salary_run_day']; ?></td>
                                                    <td><?php echo $row['salary_pay_day']; ?></td>
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
                                    <input type="hidden" name="user_id" id='user_id' value="<?php echo $payroll_admin['user_id']; ?>">
                                    <input type="hidden" name="emp_id" id='emp_id' value="<?php echo $payroll_admin['emp_id']; ?>">
                                    <input type="hidden" name="payroll_admin_id" id='payroll_admin_id' value="<?php echo $payroll_admin['id']; ?>">

                                    <input type="hidden" name="effective_date" id='effective_date' value="<?php echo $_POST['effective_date']; ?>">
                                    <input type="hidden" name="expire_date" id='expire_date' value="<?php echo $_POST['expire_date']; ?>">

                                </div>

<div class="modal-footer" >
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
</div>

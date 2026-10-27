<?php $_SESSION['apply_js']['multiselest'] = 1;
if(isset($_POST['start_goal'])){
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
    $_POST['start_goal'] = "Y";
    $url = API."?mode=insertgoals&method=post";
    $response1 = file_post_contents($url, $_POST, 'update');
    $response2 = json_decode($response1,1);
    ?>
     <script type="text/javascript">
            document.onreadystatechange = function(){
                if(document.readyState == "complete") {
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');
                }
            }
        </script>
<?php
}
if(isset($_POST['insert_goal_btn'])){
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
    $url = API."?mode=insertgoals&method=post";
    $response1 = file_post_contents($url, $_POST, 'update');
    $response2 = json_decode($response1,1);
    $response =$response2['insert'];
    if($response['status'] == 'sucess'){
        if($response['LASTID'] == 0){
            $msgcode = 3001; //update
            $_SESSION['insert_msg'] = $msgcode;
        }else{
            $msgcode = 3000; //insert
            $_SESSION['insert_msg'] = $msgcode;
            header("location:index.php?option=COM_LIST_GOALS"); exit;
        }
        ?>
        <script type="text/javascript">
            document.onreadystatechange = function(){
                if(document.readyState == "complete") {
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][$msgcode]; ?>','');
                }
            }
        </script>
        <?php
    }else{
        if(isset($_SESSION['error'][$response['error_no']])){
            $dis_err = explode(",", $response['other']);
            foreach ($dis_err as $va) {
                $dis_er [] = $dis_labels[$va];
            }
            $error_message = $response['error_no'] ." : ".$_SESSION['error'][$response['error_no']]." : ".implode(", ", (array)$dis_er);
        }else{
            $error_message = $response['message'];
        }
    }
}
$drid = encodeId($_GET['Rid']);
$erid = explode(":",$drid);
$rid = $erid['1'];
$apiurl = API."?mode=getgoals&rid=".$rid;
$apicallss = callAPI($apiurl);
$result = json_decode($apicallss,1);
$modellist = $result['modellist'];
$upmllist = $result['uomllist'];
$employee_goals = $result['employee_goals'];
$employees = $result['employees'];

?>
<div class="card">
    <div class="card-header">
        <form method="post">
            <h5>Employee Goals</h5>
            <div class="card-header-right table-card-header">
                    <input type="hidden" name="rid" value="<?php echo $rid; ?>" />
                    <?php if($employee_goals['share'] =='1'){ ?>
                    <button type="submit" name="start_goal" class="btn btn-primary mb-2 desktop-view"> Share Goal</button>
                    <?php } ?>
                <button type="button" class="btn waves-effect waves-light mb-2 desktop-view"><i class="icofont icofont-reply"></i>Back</button>
            </div>
        </form>
    </div>
    <div class="card-body m-r-50 m-l-50" <?php if($employee_goals['share'] =='0'){ ?>style="pointer-events: none; opacity: 0.7;"<?php
} ?>>
        <form method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="form-group col-lg-4">
                    <span class="form-label">Goal Name<span class="text-danger">*</span></span>
                    <input aria-label="Goal Name" type="text" name="goal_name" class="form-control" value="<?php echo $employee_goals['goal_name']; ?>" required>
                </div>
                <div class="form-group col-lg-4">
                    <span class="form-label">Descriptions<span class="text-danger">*</span></span>
                    <textarea aria-label="Descriptions" type="text" name="descriptions" class="form-control"><?php echo $employee_goals['descriptions']; ?></textarea>
                </div>
                <div class="form-group col-lg-2">
                    <span class="form-label">Goal Start Date</span>
                    <input aria-label="Input field" type="text" class="form-control startdatepicker_1 rangedatepicker"  value="<?php echo  fromDate($employee_goals['goal_start_date']); ?>" data-id="1"  name="goal_start_date" onblur="assessmentchange()" />
                </div>
                <div class="form-group col-lg-2">
                    <span class="form-label">Goal End Date </span>
                    <input aria-label="Goal End Date" type="text" class="form-control enddatepicker_1" name="goal_end_date" value="<?php echo fromDate($employee_goals['goal_end_date']); ?>"   />
                </div>
                <div class="form-group col-lg-8">
                    <?php
                    if($employee_goals['employee_ids'] !=""){
                       $emailid = json_decode($employee_goals['employee_ids'],1);
                    }else{
                        $emailid = array($_SESSION['emp_id']);
                    } ?>
                    <span class="form-label">Employees<span class="text-danger">*</span></span>
                    <select aria-label="Employee Ids[]" class="form-control js-example-basic-multiple" name="employee_ids[]" multiple>
                        <option value="0">Select Any One</option>
                        <?php foreach ($employees as  $empid=>$empname){  ?>
                            <option <?php if(in_array($empid, (array)$emailid)){ ?> selected <?php
} ?> value="<?php echo $empid; ?>"
 <?php if(in_array(PMS_EMPLOYEE_ROLE,(array)$_SESSION['role_ids']) && $empid != $_SESSION['emp_id']){  ?> disabled <?php
} ?>><?php echo $empname; ?></option>
                        <?php }  ?>
                    </select>

                </div>
            </div>
            <input type="hidden" name="rid" value="<?php echo $rid; ?>" />
            <button type="submit" class="btn btn-primary waves-effect waves-light mt-5" name="insert_goal_btn"><i class="fa fa-check m-r-10"></i> <?php echo $rid ==""?"Submit":"Update"; ?></button>

        </form>
    </div>
</div>

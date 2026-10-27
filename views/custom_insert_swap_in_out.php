<?php
if(isset($_POST['submit_value']) && isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK){
    if(pathinfo($_FILES['csv_file']['name'], PATHINFO_EXTENSION) == 'csv'){
        $ext=explode(".",$_FILES["csv_file"]['name']);
        $file_name = $ext[0];
        $separator = ',';
        $file_insert_name = 'attendance'.'_'.time().uniqid().'.'.$ext[1];
        $file_path='uploads/edi/'.$file_insert_name;
        move_uploaded_file($_FILES["data"]["tmp_name"],$file_path);
        $csvFile = $_FILES['csv_file']['tmp_name'];
        $postarray['company_id'] = $_SESSION['company_id'];
        $postarray['start_date'] = $_POST['start_date'];
        $postarray['end_date'] = $_POST['end_date'];
           $postarray['user_id'] = $_SESSION['userid'];
           $postarray['comment'] = $_POST['comment'];
           $postarray['import_type'] = $_POST['import_type'];
           $postarray['month_year'] = $_POST['month_year'];
           $postarray['csvFile'] = $csvFile;
           $postarray['file_name'] = $file_insert_name;
        $url = API."?mode=Insertimportswapinout&method=post";
        $response = file_post_contents($url, $postarray, 'insert');
        $response = json_decode($response,1);
        $getid = $response['respons']["LASTID"];
        $Rrid = deocdeId("id:".$getid);
        $_SESSION['insert_msg'] = "9092";
           header("location:index.php?option=COM_INSERT_SWAP_IN_OUT&TYPE=".$_POST['import_type']."&yearmonth=".$_POST['month_year']."&Rid=".$Rrid); exit;
    }else{
        $error_messages =  "Please upload CSV file format";
    }
}else{
    if($_SESSION['insert_msg'] !=""){
        $msgcode = $_SESSION['insert_msg'];
        $_SESSION['insert_msg'] = "";
        ?>
        <script type="text/javascript">
            document.onreadystatechange = function(){
                if(document.readyState == "complete") {
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][$msgcode]; ?>','');
                }
            }
        </script>
    <?php
    }
}

if(isset($_POST['submit_import'])){
    $_POST['company_id'] = $_SESSION['company_id'];
       $_POST['user_id'] = $_SESSION['userid'];
    $url = API."?mode=insertwithoutofftempattendancedetails&method=post";
    $response = file_post_contents($url, $_POST, 'insert');
    $response = json_decode($response,1);
     $_SESSION['insert_msg'] = "9091";

    header("location:index.php?option=COM_LIST_WORK_ATTENDANCES&SUBDATE=".$_POST['month_year']);
}
if($_GET['yearmonth'] !=""){
    $startmonthdate = date("Y-m-01", strtotime($_GET['yearmonth'].'-01'));
    $endmonthdate = date("Y-m-t", strtotime($startmonthdate));
}
$getrid =  encodeId($_GET['Rid']);
$decodrid = urldecode($getrid);
$spitrid = explode(':', $decodrid);
$rid = $spitrid['1'];
$inouturl = API."?mode=getimportswapinout&rid=".$rid."&import_type=".$_GET['TYPE'];
$inoutcall = callAPI($inouturl);
$responss = json_decode($inoutcall,1);
$import_swap_in_out_details = $responss['import_swap_in_out_details'];
$employees = $responss['employees'];
$leave_categories = $responss['leave_categories'];
$emp_leave_balances = $responss['emp_leave_balances'];
$employee_terminations = $responss['employee_terminations'];

?>
<script type="text/javascript">

    function TerminationEmployee(emp_id,display_name,emp_code,email_id,joining_date){
        jQuery.ajax({
            url: "views/custom_insert_employee_terminations_popup.php",
            data:"emp_id="+emp_id+"&display_name="+display_name+"&emp_code="+emp_code+"&email_id="+email_id+"&joining_date="+joining_date,
            type: "POST",
            success:function(data){
                $('#dynamic_popup1').html(data);
                $('#kt_modal_dynamic_popup').modal("show");
                $(".js-example-basic-single").select2();
                $(".setdatepicker").datepicker({
                showOn: "both",
                  buttonText: "<i class='ti-calendar text-dark f-20 f-w-800 m-r-10'></i>",
                  dateFormat: DateFormate,
                  changeMonth: false,
                  changeYear: false,
                  minDate: new Date(<?php echo date("Y", strtotime($startmonthdate)); ?>, <?php echo date("m", strtotime($startmonthdate))-1; ?>, 1), // January 1, 2022
                    maxDate: new Date(<?php echo date("Y", strtotime($startmonthdate)); ?>, <?php echo date("m", strtotime($startmonthdate))-1; ?>, <?php echo date("t", strtotime($startmonthdate)); ?>), // December 31, 2022
                  beforeShow: function (input, inst) {
                        var rect = input.getBoundingClientRect();
                        setTimeout(function () {
                            inst.dpDiv.css({ top: rect.top + 40, left: rect.left + 0 });
                        }, 0);
                    }
                });
            },
            error:function (){}
        });
    }
</script>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <?php if($error_messages !="" ){?>
             <div class="alert alert-danger alert-dismissible">
                <strong><?php echo $error_messages; ?> </strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>
        <div class="card card-custom card-sticky " id="kt_page_sticky_card">
            <div class="card-header table-card-header">
                <h5><?php echo "Import";  ?></h5>
                <div class="card-header-right table-card-header ">
                    <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                        <i class="icofont icofont-reply"></i>
                        Back
                    </a>
                </div>
            </div>
        <?php if($rid !=""){
            $noerror = 0;
            ?>
            <div class="card-body ">
                <div class="row mb-2">
                    <div class="col-lg-5 text-start">
                        <a href="index.php?option=COM_INSERT_SWAP_IN_OUT&TYPE=<?php echo $_GET['TYPE']; ?>&yearmonth=<?php echo $_GET['yearmonth']; ?>" type="submit" class="btn btn-primary waves-effect waves-light  submitattendancess"   data-container="body" data-toggle="tooltip" data-placement="bottom" title=""><i class="ti-cloud-up"></i> Re-Import</a>
                    </div>
                    <div class="text-right col-lg-3">
                        <div class="d-inline-block m-l-10">
                            <button type="button" class="btn waves-effect waves-light btn-danger p-2"></button>
                            <span>Applied Leaves are More Than Available Leaves</span>
                        </div>
                        <div class="d-inline-block m-l-10">
                            <button type="button" class="btn waves-effect waves-light btn-danger p-2"></button>
                            <span>Off Days Are More Than Available Sundays In the Month</span>
                        </div>
                        <div class="d-inline-block m-l-10">
                            <button type="button" class="btn waves-effect waves-light btn-warning p-2"></button>
                            <span>Off Days Are More Than Available Sundays In the Month</span>
                        </div>
                    </div>
                    <div class="col-lg-4">
                         <div class="input-group input-group-button mb-0">
                            <input type="text" id="dynamicfilterhideshow" data-id="categoryfilter" data-type="tr" class="form-control form-control-sm width1" placeholder="Search" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Search" aria-label="Search">
                            <span class="input-group-text addon-style">
                                <span class=""><span class="ti-search text-white mt-2"></span></span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover m-b-0" style="font-size:13px;" id="categoryfilter_2">
                        <thead>
                            <tr>
                                <th scope="col" rowspan="2">#</th>
                                <th scope="col" rowspan="2" class="align-middle">Employee</th>
                                <th scope="col" class="text-right align-middle" rowspan="2">Off Days</th>
                                <th scope="col" class="text-right align-middle" rowspan="2">Half Day Present</th>
                                <th scope="col" class="text-right align-middle" rowspan="2">Half Day Absent</th>
                                <th scope="col" class="text-center" colspan="<?php echo count((array)$leave_categories); ?>">Available Leaves/Leaves</th>
                                <th scope="col" class="text-right align-middle" rowspan="2">Absents</th>
                                <th scope="col" class="text-right align-middle" rowspan="2">Pressents</th>
                                <th scope="col" rowspan="2" class="align-middle">Error</th>
                                <th scope="col" class="text-right align-middle" rowspan="2">Total Day</th>
                            </tr>
                            <tr>
                                <?php foreach($leave_categories as $lckey=>$lcategory){ ?>
                                    <th scope="col" class="text-right" >
                                        <span class="form-label label label-primary" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="<?php echo $lcategory['name']; ?>"><?php echo $lcategory['key_short_code']; ?></span></th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $mk=1; foreach($import_swap_in_out_details as $empid => $isiod){
                                $empdata = $employees[$empid];
                                $error = $errorl = "";
                                if($isiod['offday_cnt'] >$isiod['sunday_cnt']){
                                    $noerror = 1;
                                    $error .='<span class="form-label label label-danger" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Maximum Off day Is '.$isiod['sunday_cnt'].'">O</span>';
                                }

                                ///}
                                ?>
                            <tr class="<?php echo $class; ?>">
                                <td class="p-2"><?php echo $mk; ?></td>
                                <td class="text-right p-2">
                                    <div class="align-middle float-left ml-2">
                                        <?php if($empdata['image']!=""){?>
                                            <img src="uploads/all/<?php echo $empdata['image']; ?>"   class="img-radius img-30 align-top m-r-15" alt="Attachment Preview">
                                          <?php }else{ ?>
                                            <span  alt="user" class="img-radius align-top m-r-10 custom_txt_workentry f-12 text-center" ><?php echo initials($empdata['display_name']); ?></span>
                                          <?php } ?>
                                          <div class="d-inline-block">
                                            <p class="f-10 mb-0 d-block text-start"><?php echo $empdata['emp_code']." - ".$empdata['display_name']; ?></p>
                                            <p class="f-10  mb-0 d-block text-start"><?php echo $empdata['email_id']; ?></p>
                                          </div>
                                    </div>
                                    <?php if($employee_terminations[$empdata['id']] ==""){ ?>
                                        <button class="btn btn-primary float-end btn-sm" onclick="TerminationEmployee('<?php echo $empdata['id']; ?>','<?php echo $empdata['display_name']; ?>','<?php echo $empdata['emp_code']; ?>','<?php echo $empdata['email_id']; ?>','<?php echo $empdata['joining_date']; ?>')">Termination</button>
                                    <?php }else{  ?>
                                        <span class="form-label label label-md label-danger float-end">Termination Date - <?php echo fromDate($employee_terminations[$empdata['id']]); ?></span>
                                    <?php } ?>
                                </td>
                                <td class="text-right p-2"><?php echo $isiod['offday_cnt']; ?></td>
                                <td class="text-right p-2"><?php echo $isiod['total_half_day_cnt']; ?></td>
                                <td class="text-right p-2"><?php echo $isiod['total_half_day_absent_cnt']; ?></td>
                                <?php
                                $leavecnt = 0; $applicatleave = 0;
                                if(count((array)$leave_categories)>0){
                                    foreach((array)$leave_categories as $lckey=>$lcategory){
                                        $key_short_code = $lcategory['key_short_code'];
                                        $leavecnt = $isiod['leave'][$key_short_code];
                                        if($lcategory['key_short_code'] == "C" && $isiod['total_half_day_cnt'] !='0'){
                                            $key_short_code = 'C';
                                            $leavecnt = $isiod['total_half_day_cnt']*0.5;
                                        }

                                        $leavecnt = $leavecnt!=""?$leavecnt:0;
                                        $applicatleave = $emp_leave_balances[$empid][$key_short_code]['applicable_leaves'];
                                        $applicatleave = $applicatleave!=""?$applicatleave:0;
                                        if($applicatleave <$leavecnt){
                                            $noerror = 1;
                                            $errorl ='<span class="form-label label label-danger" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="'.$lcategory['name'].'">'.$lcategory['key_short_code'].'</span>';
                                        }

                                        ?>
                                        <td class="text-right p-2"><?php echo $applicatleave;  ?>/<?php echo $leavecnt ?></td>
                                    <?php } ?>
                                <?php }else{

                                ?>
                                    <td class="text-right p-2"><?php echo $applicatleave;  ?>/<?php echo $leavecnt ?></td>
                                <?php } ?>
                                <td class="text-right p-2"><?php echo $isiod['absent_cnt']; ?></td>
                                <td class="text-right p-2"><?php echo $isiod['present_cnt']; ?></td>
                                <td class="p-2"><?php echo $error." ".$errorl; ?></td>
                                <td class="text-right p-2"><?php echo $isiod['total_day_cnt']; ?></td>
                            </tr>
                        <?php $mk++;
} ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <form method="post">
                <div class="row">
                    <div class="col-lg-6">
                        <input type="hidden" class="form-control" name="import_type"  value="<?php echo $_GET['TYPE']; ?>">
                        <input type="hidden" class="form-control" name="month_year"  value="<?php echo $_GET['yearmonth']; ?>">
                        <input type="hidden" class="form-control" name="rid"  value="<?php echo $rid; ?>">
                        <button class="btn btn-primary btn-shadow-hover font-weight-bold mr-2 m-b-10 <?php echo $noerror == '1'?"disabled":""; ?>" name="submit_import" value="exit"><i class="la la-check"></i> Submit</button>
                        <?php if($noerror == '1'){ ?>
                            <p class="text-danger"><?php echo $_SESSION['error_message']['9090']; ?></p>
                        <?php } ?>
                    </div>
                </div>
                </form>
            </div>

        <?php }else{ ?>
            <form method="post" enctype="multipart/form-data">
                <div class="card-body card-infisuite">
                    <div class="row">
                        <div class="form-group col-lg-4">
                            <span class="form-label">Import</span>
                            <input aria-label="Csv File" type="file" class="form-control" name="csv_file" id="csv_file" required="">
                        </div>
                        <div class="form-group col-lg-2">
                            <span class="form-label">Start Date</span>
                            <input aria-label="Start Date" type="text" class="form-control datepicker" name="start_date" value="<?php echo fromDate($startmonthdate); ?>" required="">
                        </div>
                        <div class="form-group col-lg-2">
                            <span class="form-label">End Date</span>
                            <input aria-label="End Date" type="text" class="form-control datepicker" name="end_date" required="" value="<?php echo fromDate($endmonthdate); ?>">
                        </div>
                        <div class="form-group col-lg-4">
                            <span class="form-label">Comment</span>
                            <textarea aria-label="Comment" type="text" class="form-control" name="comment"></textarea>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-6">
                            <input type="hidden" name="submit" value="Add">
                            <input type="hidden" class="form-control" name="import_type"  value="<?php echo $_GET['TYPE']; ?>">
                            <input type="hidden" class="form-control" name="month_year"  value="<?php echo $_GET['yearmonth']; ?>">
                            <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2 m-b-10" name="submit_value" value="exit"><i class="la la-check"></i>Import</button>
                        </div>
                    </div>
                </div>
            </form>
            <?php } ?>
        </div>
    </div>
</div>

<?php
if(isset($_POST['submit'])){
    $data = array();
    if($_POST['submit']=='addpeople'){
        $_POST['company_id'] = $_SESSION['company_id'];
        $_POST['userid'] = $_SESSION['userid'];
        $File_Uploads_Path = "uploads/all/";
        if($_FILES['newfilename']['error'] != "4"){
            $temp = explode(".", $_FILES['newfilename']['name']);
            $Filename=  "file_".time()."_".uniqid();
            $newfilename = $Filename.'.' .end($temp);
            if(move_uploaded_file($_FILES['newfilename']["tmp_name"], $File_Uploads_Path.$newfilename)){
                $_POST['file_name'] = $newfilename;
            }
        }else{
            $_POST['file_name'] = $_POST['oldfilename'];
        }
        $url = API."?mode=insertperformancesetup&method=post";
        $response1 = file_post_contents($url, $_POST, 'update');
        $response2 = json_decode($response1,1);
        $response =$response2['insert'];
        if($response['status'] == 'fail'){
            if(isset($_SESSION['error'][$response['error_no']])){
                $dis_err = explode(",", $response['other']);
                foreach ($dis_err as $va) {
                    $dis_er [] = $dis_labels[$va];
                }
                $error_message = $response['error_no'] ." : ".$_SESSION['error'][$response['error_no']]." : ".implode(", ", (array)$dis_er);
            }else{
                $error_message = $response['message'];
            }
        }else{
            if($_POST['rid']!=""){
                $getid = $_POST['rid'];
            }else{
                $getid = $response["LASTID"];
            }
            $Rrid = deocdeId("id:".$getid);
            header("location:index.php?option=COM_VIEW_PERFORMANCE_APPRAISAL_SETTINGS&Rid=".$Rrid."&AY=".$_POST['assessment_year']); exit;
        }
    }
}
if($_GET['Rid']!=""){
    $drid = encodeId($_GET['Rid']);
    $erid = explode(":",$drid);
    $rid = $erid['1'];
    $vurl = API."?mode=singlerowtabledata&table=performance_setup&rid=".$rid;
    $configesponse = callAPI($vurl);
    $role = json_decode($configesponse,1);
    $goal = json_decode($role['goals'],1);
    $filterwh["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id'],'transaction_id'=>$rid,'application_tablename'=>'performance_setup');
    $filterwh['field'] = "id";
    $filterw1 = urlencode(json_encode($filterwh));
    $filterurl = API."?mode=gettabledetailswithconditions&table=filter_tables&data=".$filterw1;
    $filterres = callAPI($filterurl);
    $filterressult = json_decode($filterres,1);
    $filtertbl_rowid = $filterressult[0]['id'];
}
?>
<?php if(count((array)$_POST) !='0'){ ?>
<script type="text/javascript">
    document.onreadystatechange = function(){
        if(document.readyState == "complete") {
            tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');
        }
    }
</script>
<?php } ?>

<script type="text/javascript">

    function showpopupappraisal(){
        var filter_id = document.getElementById('filtertbl_rowid').value;
        var process_type = '3';
        var application_table = 'performance_setup';
        jQuery.ajax({
            url: "views/custom_insert_filters.php",
            data:'filter_id='+filter_id+'&process_type='+process_type+"&tablename="+application_table,
            type: "GET",
            success:function(data){
                $('#dynamic_popup1').html(data);
                $("#kt_modal_dynamic_popup").modal("show");
                $('[data-bs-toggle="tooltip"]').tooltip();
            },
            error:function (){}
        });
    }
    function getnamelist(emplist){
        $('#kt_modal_dynamic_popup').modal('hide');
        $.ajax({
            url: "ajax.php",
            data:'page=filternamelist&filterdata='+emplist,
            type: "POST",
            success:function(response){
                console.log("response ="+response);
                document.getElementById('filternames').innerHTML =  response;
            },
            error:function (){}
        });
    }
    function assessmentchange(){
        //     url: "ajax.php",
        //     data:'page=performance_date&pdate='+val+'&month='+assessment_month,
        //     type: "POST",
    }
    function perrfeedbackchange(){
        var val =  document.getElementById('kt_datepicker_32').value;
        var assessment_month = document.getElementById('peer_feedback_month').value;
        $.ajax({
            url: "ajax.php",
            data:'page=peerfeedback_date&pdate='+val+'&month='+assessment_month,
            type: "POST",
            success:function(response){
                console.log("response ="+response);
                document.getElementById('peerfeedback_date').innerHTML = response;
            },
            error:function (){}
        });
    }
</script>
        <?php
        if($error_message !=''){ ?>
            <div class="alert alert-danger background-danger alert-dismissible">
                <strong><?php echo $error_message; ?></strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>
        <div class="card card-custom">
            <div class="card-header table-card-header">
                <h5 class="card-label">&nbsp;<?php echo "Performance";  ?> </h5>
                <div class="card-header-right table-card-header ">
                    <?php if($_GET['option'] != "COM_VIEW_PERFORMANCE_APPRAISAL_SETTINGS"){ ?>
                        <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                            <i class="icofont icofont-reply"></i>  Back
                        </a>
                    <?php } ?>

                </div>
            </div>
            <div class="card-body">
                <form class="kt-form" id="kt_form" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">
                    <input type="hidden" name="filtertbl_rowid" id="filtertbl_rowid" value="<?php echo $filtertbl_rowid;?>" >
                    <div class="card-body">
                        <div class="row ">
                            <div class="form-group col-lg-4">
                                <span class="form-label">Name<span class="text-danger">*</span></span>
                                <input aria-label="Name" type="text" name="name" class="form-control" value="<?php echo $role['name']; ?>">
                            </div>
                            <div class="form-group col-lg-4 w-full">
                                <span class="form-label">Aassessment Year:</span></br>
                                <select aria-label="Assessment Year" class="form-control form-select js-example-basic-single" name="assessment_year"  required="">
                                    <option value="">Select Any One</option>
                                    <?php for ($i=2020; $i<2040;$i++) {
                                        $k= $i+1; $view_assemnt_year =$i."-".$k; ?>
                                        <option <?php if($role['assessment_year'] == $view_assemnt_year){ echo "selected";
} ?> value="<?php echo $view_assemnt_year; ?>"  ><?php echo $i." - ".$k ?></option>
                                    <?php  } ?>
                                </select>
                            </div>
                            <div class="form-group col-lg-4">
                            </div>
                            <div class="form-group col-lg-4">
                                <span class="form-label">Assessment Start Date</span>
                                <?php $date_pic = fromDate($role['assessment_start_date']); ?>
                                <input aria-label="Input field" type="text" class="form-control startdatepicker_1 rangedatepicker"  value="<?php echo  $date_pic; ?>" data-id="1"  name="assessment_begin" onblur="assessmentchange()" id="kt_datepicker_31" />
                            </div>
                            <div class="form-group col-lg-4">
                                <span class="form-label">Assessments End Date </span>
                                <?php
                                $date_pic2 = fromDate($role['assessment_end_date']);
                                ?>
                                <input aria-label="Assessment Due" type="text" class="form-control enddatepicker_1" name="assessment_due" value="<?php echo  $date_pic2; ?>" name="assessment_due"  />
                            </div>
                            <div class="form-group col-lg-4">
                            </div>
                            <div class="form-group col-lg-4">
                                <span class="form-label">Self Assessment Start Date</span>
                                <?php $date_pic = fromDate($role['employee_start']); ?>
                                <input aria-label="Input field" type="text" class="form-control startdatepicker_2 rangedatepicker"  value="<?php echo  $date_pic; ?>" data-id="2" name="employee_begin" id="kt_datepicker_4_1" />
                            </div>
                            <div class="form-group col-lg-4">
                                <span class="form-label">Self Assessment End Date</span>

                                <?php $date_pic2 = fromDate($role['employee_end']); ?>
                                <input aria-label="Input field" type="text" class="form-control enddatepicker_2"  value="<?php echo  $date_pic2; ?>"  name="employee_end" id="kt_datepicker_4_2"  />
                            </div>
                            <div class="form-group col-lg-4">
                            </div>
                            <div class="form-group col-lg-4">
                                <span class="form-label">Manager's Assessement Start Date </span>

                                <?php $date_pic = fromDate($role['manager_start']); ?>
                                <input aria-label="Input field" type="text" class="form-control startdatepicker_3 rangedatepicker"  value="<?php echo  $date_pic; ?>" data-id="3" name="manager_begin" id="kt_datepicker_4_3" />
                            </div>
                            <div class="form-group col-lg-4">
                                <span class="form-label">Manager's Assessment End Date </span>

                                <?php $date_pic2 = fromDate($role['manager_end']); ?>
                                <input aria-label="Manager End" type="text" class="form-control enddatepicker_3" name="manager_end" value="<?php echo  $date_pic2; ?>"  id="kt_datepicker_4_4"   />
                            </div>
                            <div class="form-group col-lg-4">
                            </div>
                            <div class="form-group col-lg-4">
                                <span class="form-label">Reviewing Manager's Assessment Start Date</span>
                                <?php $date_pic = fromDate($role['reporting_manager_start']); ?>
                                <input aria-label="Input field" type="text" class="form-control startdatepicker_4 rangedatepicker"  value="<?php echo  $date_pic; ?>" data-id="4" name="managerto_begin"  id="kt_datepicker_4_5" />
                            </div>
                            <div class="form-group col-lg-4">
                                <span class="form-label"> Reviewing Manager's Assessment End Date</span>
                                <?php $date_pic2 = fromDate($role['reporting_manager_end']); ?>
                                <input aria-label="Input field" type="text" class="form-control enddatepicker_4" value="<?php echo  $date_pic2; ?>"  name="managerto_end" id="kt_datepicker_4_6" />
                            </div>
                            <div class="form-group col-lg-4">
                            </div>
                            <div class="form-group col-lg-4">
                                <span class="form-label">URL</span>
                                <textarea aria-label="Url" name="url" class="form-control"><?php echo $role['url']; ?></textarea>
                            </div>
                            <div class="form-group col-lg-4">
                                <span class="form-label">File Upload</span>
                                <div></div>
                                <div class="custom-file">
                                    <input aria-label="Newfilename" type="file" class="custom-file-input" id="customFile" name="newfilename" value="<?php echo $role['file_name']; ?>">

                                    <input type="hidden"  name="oldfilename" value="<?php echo $role['file_name']; ?>">
                                    <a  class="mt-2 text-primary" href="uploads/all/<?php echo $role['file_name']; ?>" download><?php echo $role['file_name']; ?></a>
                                </div>
                            </div>
                            <div class="form-group col-lg-8 w-full" >
                                <span class="form-label">How often do assessments happen?</span>
                                <select aria-label="Assessment Month" class="form-select js-example-basic-single" onchange="assessmentchange()" name="assessment_month" id="assessment_month">
                                    <option <?php if($role['assessment_month']==3){ echo "selected";
}  ?> value="3" >Every 3 Months</option>
                                    <option <?php if($role['assessment_month']==4){ echo "selected";
}  ?> value="4" >Every 4 Months</option>
                                    <option <?php if($role['assessment_month']==6){ echo "selected";
}  ?> value="6" >Every 6 Months</option>
                                    <option <?php if($role['assessment_month']==12){ echo "selected";
}  ?> value="12" >Every Year</option>
                                </select>
                            </div>
                            <div class="form-group col-lg-12">
                                        <span class="font-weight-bolder">Asign to <span class="text-danger">*</span></span>
                                        <div id="tsk_fortable"></div>
                                        <div class="col-xl-12">
                                            <i class="flaticon2-group text-success"></i>
                                            <button type="button" onclick="showpopupappraisal();">Edit </button>
                                        </div>
                                        <div id="filternames"> </div>

                                    </div>
                                    </div>
                                </div>
                                <input type="hidden" name="submit" value="addpeople" />
                                <input type="hidden" name="rid" value="<?php echo $rid; ?>" />
                                <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"   name="submit_value" value="exit"><i class="la la-check"></i>Save </button>
                            </form>
                        </div>
                    </div>


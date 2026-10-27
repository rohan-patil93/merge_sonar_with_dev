<?php
if(isset($_POST['submit'])){
    $data = array();
    if($_POST['submit']=='addpeople'){
        $data['company_id'] = $_SESSION['company_id'];
        $data['name'] = $_POST['name'];
        $data['assessment_start_date'] = date('Y-m-d',strtotime($_POST['assessment_begin']));
        $data['assessment_end_date'] = date('Y-m-d',strtotime($_POST['assessment_due']));
        $data['employee_start'] = date('Y-m-d',strtotime($_POST['employee_begin']));
        $data['employee_end'] = date('Y-m-d',strtotime($_POST['employee_end']));
        $data['manager_start'] = date('Y-m-d',strtotime($_POST['manager_begin']));
        $data['manager_end'] = date('Y-m-d',strtotime($_POST['manager_end']));
        $data['reporting_manager_start'] = date('Y-m-d',strtotime($_POST['managerto_begin']));
        $data['reporting_manager_end'] = date('Y-m-d',strtotime($_POST['managerto_end']));
        $data['assessment_month'] = $_POST['assessment_month'];
        $data['assessment_assigned_to'] = $_POST['assessment_assigned_to'];
        $data['assigned_to_employees'] = urldecode($_POST['assigned_to_employees']);
        $data['url'] = $_POST['url'];
        $image = imageupload('all','file_name',$_POST['hid_image']);
        $data['file_name'] = $image;
        $data['created_date'] = nowDate();
        $data['created_by'] = $_SESSION['userid'];
        $data['is_active'] = 0;
        $data['current_status'] =0;
        if($_POST['rid']!=""){
            $url = API."?mode=updaterowtabledata&rid=".$_POST['rid']."&table=performance_setup&method=post";
            $response = file_post_contents($url, $data, 'update');

        }else{
            $url = API."?mode=adddata&table=performance_setup&method=post";
            $response = file_post_contents($url, $data, 'insert');
        }
        if($response){
            $res = json_decode($response,1);
             $pid = deocdeId("id:".$res["LASTID"]);
            header("location:index.php?option=COM_LIST_PERFORMANCE_SETUP");
        }
    }
    if($_POST['submit']=='savepeerfeedback'){
        $data['peer_feedback_date'] = date('Y-m-d',strtotime($_POST['peer_feedback_date']));
        $data['peer_feedback_month'] = $_POST['peer_feedback_month'];
        echo "<pre>"; print_r($_POST); exit;

        $url = API."?mode=updaterowtabledata&table=performance_setup&rid=".$_POST['rid']."&method=post";
        $response = file_post_contents($url, $data, 'update');
    }
    if($_POST['submit']=='savegoal'){
        $dat['kra'] = $_POST['kra']=="on" ? 1:0;
        $dat['kpi'] = $_POST['kpi']=="on" ? 1:0;
        $jsn = json_encode($dat);
        $data['goals'] = $jsn;
        $url = API."?mode=updaterowtabledata&table=performance_setup&rid=".$_POST['rid']."&method=post";
        $response = file_post_contents($url, $data, 'update');
    }
    if($_POST['submit']=='saveemployeeassessment'){
        $url = API."?mode=insertandupdateperformancesetupquestions&method=post";
        $response = file_post_contents($url, $_POST, 'update');
    }
    if($_POST['submit']=='savemanagementassessment'){
        $url = API."?mode=insertandupdateperformancesetupquestions&method=post";
        $response = file_post_contents($url, $_POST, 'update');
    }
    if($_POST['submit']=='savemanagertoassessment'){
        $url = API."?mode=insertandupdateperformancesetupquestions&method=post";
        $response = file_post_contents($url, $_POST, 'update');
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
    $assigned_to_employees = $role['assigned_to_employees'];
    if($assigned_to_employees=='All Employees'){
        $assigned_to_employees_names = "All Employees";
    } else{
        $filterdata = json_decode($assigned_to_employees,1);
        $filternamelist = $filterdata['matches_names'];
        foreach($filternamelist as $ids){
            $shared_idslist[]= $ids['display_name'];
        }
        $assigned_to_employees_names = implode(',', (array)$shared_idslist);
    }
    $vdata1["where"] = array('assessment_id'=> $rid,'is_active'=>'0');
    $vdata = urlencode(json_encode($vdata1));
    $vurl = API."?mode=gettabledetailswithconditions&table=performance_setup_questions&data=".$vdata;
    $configesponse = callAPI($vurl);
    $returns = json_decode($configesponse,1);
    foreach($returns as $return){
        $data[$return['assign_type']][] = $return;
    }
}
?>
<script type="text/javascript">

    function showpopup(){
        var filter_id = document.getElementById('filtertbl_rowid').value;
        jQuery.ajax({
            url: "views/custom_filter_bulktransfer.php?filter_id="+filter_id,
            data:'filter_id='+filter_id,
            type: "GET",
            success:function(data){
                $('#dynamic_popup1').html(data);
                $('#kt_modal_dynamic_popup').modal({ show: true});
            },
            error:function (){}
        }).done(function(){
        });
    }
    function getnamelist(emplist){
        document.getElementById('assigned_to_employees').value = emplist;
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
        var val =  document.getElementById('kt_datepicker_31').value;
        var assessment_month = document.getElementById('assessment_month').value;
        $.ajax({
            url: "ajax.php",
            data:'page=performance_date&pdate='+val+'&month='+assessment_month,
            type: "POST",
            success:function(response){
                console.log("response ="+response);
                document.getElementById('datedisplay').innerHTML = response;
            },
            error:function (){}
        });
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
<div class="d-flex flex-column-fluid">

    <div class="container-fluid">
        <div class="card card-custom card-sticky" id="kt_page_sticky_card">
            <div class="card-header">
                <div class="card-title">
                    <h3 class="card-label">&nbsp;<?php echo "Performance";  ?> </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="card-toolbar">
                        <a href="javascript:history.go(-1)" class="btn btn-primary font-weight-bolder mr-2">
                            Genrate Future Performance
                        </a>
                        <a href="javascript:history.go(-1)" class="btn btn-light-primary font-weight-bolder mr-2">
                            <i class="ki ki-long-arrow-back icon-xs"></i>
                            Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card card-custom">
            <div class="card-body">
                <div class="row col-lg-12">
                    <div class="d-flex mt-2 h-55px">
                        <ul class="nav nav-tabs nav-bold nav-tabs-line nav-line-tabs-3x border-transparent fs-5 fw-bolder flex-nowrap">
                            <li class="nav-item">
                                <a class="nav-link active" id="profile-tab-1" data-toggle="tab" href="#people-1" aria-controls="HOME" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                    <span class="nav-icon">
                                        <i class="flaticon2-layers-1"></i>
                                    </span>
                                    <span class="nav-text">People</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link " id="profile-tab-1_2" data-toggle="tab" href="#employee-1" aria-controls="profile" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                    <span class="nav-icon">
                                        <i class="flaticon2-layers-1"></i>
                                    </span>
                                    <span class="nav-text">Assessment Questions For Employee</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="profile-tab-1_3" data-toggle="tab" href="#manger-1" aria-controls="dATA" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                    <span class="nav-icon">
                                        <i class="flaticon2-layers-1"></i>
                                    </span>
                                    <span class="nav-text">Assessment Questions For Manager</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="profile-tab-1_4" data-toggle="tab" href="#manger-2" aria-controls="manager" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                    <span class="nav-icon">
                                        <i class="flaticon2-layers-1"></i>
                                    </span>
                                    <span class="nav-text">Assessment Questions For Manager To</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="profile-tab-1_5" data-toggle="tab" href="#peer-1" aria-controls="profile" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                    <span class="nav-icon">
                                        <i class="flaticon2-layers-1"></i>
                                    </span>
                                    <span class="nav-text">Peer Feedback </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="profile-tab-1_6" data-toggle="tab" href="#goal-1" aria-controls="dATA" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                    <span class="nav-icon">
                                        <i class="flaticon2-layers-1"></i>
                                    </span>
                                    <span class="nav-text">Goals</span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </div>
                <div class="">
                    <div class="tab-content mb-5 " id="myTabContent1">
                        <div class="tab-pane fade show active" id="people-1" role="tabpanel" aria-labelledby="home-tab-1">
                            <div class="card card-custom">
                                <form class="kt-form" id="kt_form" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">
                                <div class="card-body">

                                    <div class="row ">
                                        <div class="form-group col-lg-4">
                                            <span class="form-labe">Name<span class="text-danger">*</span></span>
                                            <input aria-label="Name" type="text" name="name" class="form-control" value="<?php echo $role['name']; ?>">
                                        </div>
                                        <div class="form-group col-lg-8">
                                        </div>
                                        <div class="form-group col-lg-4">
                                            <span class="form-labe">Start Assessment </span>
                                            <div class="input-group date">
                                                <?php $date_pic = fromDate($role['assessment_start_date']); ?>
                                                <input aria-label="Input field" type="text" class="form-control"  value="<?php echo  $date_pic; ?>"  name="assessment_begin" onblur="assessmentchange()" id="kt_datepicker_31" />
                                                <div class="input-group-append">
                                                    <span class="input-group-text">
                                                        <i class="la la-calendar"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-4">
                                            <span class="form-labe">End Assessments </span>
                                            <div class="input-group date">
                                                <?php

                                                $date_pic2 = fromDate(date('Y-m-d',strtotime($date_pic."+1 month"))); ?>
                                                <input aria-label="Assessment Due" type="text" class="form-control" name="assessment_due" value="<?php echo  $date_pic2; ?>"  name="assessment_due" readonly  />
                                                <div class="input-group-append">
                                                    <span class="input-group-text">
                                                        <i class="la la-calendar"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-4">
                                        </div>

                                        <div class="form-group col-lg-4">
                                            <span class="form-labe">Self Assessment Start</span>
                                            <div class="input-group date">
                                                <?php $date_pic = fromDate($role['employee_start']); ?>
                                                <input aria-label="Input field" type="text" class="form-control"  value="<?php echo  $date_pic; ?>"  name="employee_begin" id="kt_datepicker_4_1" />
                                                <div class="input-group-append">
                                                    <span class="input-group-text">
                                                        <i class="la la-calendar"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-4">
                                            <span class="form-labe">Self Assessment End</span>
                                            <div class="input-group date">
                                                <?php $date_pic = fromDate($role['employee_end']); ?>
                                                <input aria-label="Input field" type="text" class="form-control"  value="<?php echo  $date_pic2; ?>"  name="employee_end" id="kt_datepicker_4_2"  />
                                                <div class="input-group-append">
                                                    <span class="input-group-text">
                                                        <i class="la la-calendar"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group col-lg-4">
                                        </div>

                                        <div class="form-group col-lg-4">
                                            <span class="form-labe">Manager's Assessement Start </span>
                                            <div class="input-group date">
                                                <?php $date_pic = fromDate($role['manager_start']); ?>
                                                <input aria-label="Input field" type="text" class="form-control"  value="<?php echo  $date_pic; ?>"  name="manager_begin" id="kt_datepicker_4_3" />
                                                <div class="input-group-append">
                                                    <span class="input-group-text">
                                                        <i class="la la-calendar"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-4">
                                            <span class="form-label">Manager's Assessment End </span>
                                            <div class="input-group date">
                                                <?php $date_pic = fromDate($role['manager_end']); ?>
                                                <input aria-label="Manager End" type="text" class="form-control" name="manager_end" value="<?php echo  $date_pic2; ?>"  id="kt_datepicker_4_4"   />
                                                <div class="input-group-append">
                                                    <span class="input-group-text">
                                                        <i class="la la-calendar"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group col-lg-4">
                                        </div>

                                        <div class="form-group col-lg-4">
                                            <span class="form-label">Reviewing Manager's Assessment Start</span>
                                            <div class="input-group date">
                                                <?php $date_pic = fromDate($role['reporting_manager_start']); ?>
                                                <input aria-label="Input field" type="text" class="form-control"  value="<?php echo  $date_pic; ?>"  name="managerto_begin"  id="kt_datepicker_4_5" />
                                                <div class="input-group-append">
                                                    <span class="input-group-text">
                                                        <i class="la la-calendar"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-4">
                                            <span class="form-label"> Reviewing Manager's Assessment End</span>
                                            <div class="input-group date">
                                                <?php $date_pic = fromDate($role['reporting_manager_end']); ?>
                                                <input aria-label="Input field" type="text" class="form-control" value="<?php echo  $date_pic2; ?>"  name="managerto_end"  id="kt_datepicker_4_6" />
                                                <div class="input-group-append">
                                                    <span class="input-group-text">
                                                        <i class="la la-calendar"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group col-lg-4">
                                        </div>
                                        <div class="form-group col-lg-4">
                                            <span class="form-label">URL</span>
                                            <textarea aria-label="Url" name="url" class="form-control" > <?php echo $role['url']; ?></textarea>
                                        </div>
                                        <div class="form-group col-lg-4">
                                            <span class="form-label">File Upload</span>
                                            <div>
                                                <input aria-label="File Name" type="file" class="custom-file-input" id="customFile" name="file_name" value="">
                                                <input type="hidden" name="hid_image" value="<?php echo $role['file_name']; ?>">

                                            </div>
                                        </div>
                                        <div class="form-group col-lg-12" >
                                            <span>How often do assessments happen?</span>
                                            <select aria-label="Assessment Month" class="form-control  col-lg-3" onchange="assessmentchange()" name="assessment_month" id="assessment_month">
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
                                        <div class="form-group col-lg-12" >
                                            <span>Future Assessment Dates</span>
                                            <div id="datedisplay">
                                                <?php if($role['assessment_start_date']!=""){
                                                     $sdate = date('Y-m-d', strtotime($role['assessment_start_date']));
                                                    for($i=0;$i<4;$i++){
                                                        echo "<br>";
                                                        $sdate = date("Y-m-d", strtotime($sdate."+".$role['assessment_month']." month"));
                                                           echo  "-> ".date("M d, Y", strtotime($sdate)) ." - ".date("M D, Y", strtotime($sdate."+1 month"));
                                                    }
                                                } ?>
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-12">
                                            <span class="font-weight-bolder">Asign to <span class="text-danger">*</span></span>
                                            <div id="tsk_fortable"></div>
                                            <div class="col-xl-12">
                                                <i class="flaticon2-group text-success"></i>
                                                <button type="button" onclick="showpopup();">Edit </button>
                                            </div>
                                            <div id="filternames"> <?php echo $assigned_to_employees_names; ?> </div>

                                            <input type="hidden" name="assigned_to_employees" id="assigned_to_employees" value="<?php echo urlencode($assigned_to_employees); ?>">
                                            <input type="hidden" name="assessment_assigned_to" id="filtertbl_rowid" value="<?php echo $role['assessment_assigned_to'];?>" >

                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="submit" value="addpeople" />
                                <input type="hidden" name="rid" value="<?php echo $rid; ?>" />
                                <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"   name="submit_value" value="exit"><i class="la la-check"></i>Save </button>
                                </form>
                            </div>
                        </div>
                        <div class="tab-pane fade show " id="employee-1" role="tabpanel" aria-labelledby="profile-tab-1">
                            <?php include_once 'custom_insert_performance_emp.php'; ?>
                        </div>
                        <div class="tab-pane fade" id="manger-1" role="tabpanel" aria-labelledby="contact-tab-1">
                            <?php include_once 'custom_insert_performance_manager.php'; ?>
                        </div>
                        <div class="tab-pane fade" id="manger-2" role="tabpanel" aria-labelledby="contact-tab-1">
                            <?php include_once 'custom_insert_performance_manager_to.php'; ?>
                        </div>
                        <div class="tab-pane fade" id="peer-1" role="tabpanel" aria-labelledby="home-tab-1">
                            <div class="card card-custom">
                                <div class="card-body">
                                    <form class="kt-form" id="kt_form_2" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">
                                    <div class="row ">
                                        <h6>Next Peer Feedback Round</h6>
                                        <div class="form-group col-lg-12" >
                                            <span>When should we prompt managers to request peer feedback?</span>
                                            <div class="input-group date  col-lg-3">
                                                <?php $date_pic3 = fromDate($role['peer_feedback_date']); ?>
                                                <input aria-label="Input field" type="text" class="form-control"  value="<?php echo  $date_pic3; ?>"  name="peer_feedback_date" onblur="perrfeedbackchange()" id="kt_datepicker_32" />

                                                <div class="input-group-append">
                                                    <span class="input-group-text">
                                                        <i class="la la-calendar"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-12" >
                                            <span>How often do you want managers to get peer feedback on their direct reports?</span>
                                            <select aria-label="Peer Feedback Month" class="form-control  col-lg-3" onclick="perrfeedbackchange()" onkeyup="perrfeedbackchange()" name="peer_feedback_month" id="peer_feedback_month">
                                                <option <?php if($role['peer_feedback_month']==3){ echo "selected";
}  ?> value="3" >Every 3 Months</option>
                                                <option <?php if($role['peer_feedback_month']==4){ echo "selected";
}  ?> value="4" >Every 4 Months</option>
                                                <option <?php if($role['peer_feedback_month']==6){ echo "selected";
}  ?> value="6" >Every 6 Months</option>
                                                <option <?php if($role['peer_feedback_month']==12){ echo "selected";
}  ?> value="12" >Every Year</option>
                                            </select>
                                        </div>

                                        <div class="form-group col-lg-12" >
                                            <div id="peerfeedback_date">
                                                <?php if($role['peer_feedback_date']!=""){
                                                    $sdate = date('Y-m-d', strtotime($_POST['peer_feedback_date']));
                                                    for($i=0;$i<4;$i++){
                                                        echo "<br>";
                                                        $sdate = date("Y-m-d", strtotime($sdate."+".$_POST['peer_feedback_month']." month"));
                                                           echo  "-> ".date("M d, Y", strtotime($sdate));
                                                    }
                                                } ?>
                                            </div>
                                        </div>
                                        <?php include_once 'custom_insert_performance_peer_feedback.php'; ?>
                                    </div>
                                    <input type="hidden" name="submit" value="savepeerfeedback" />
                                    <input type="hidden" name="rid" value="<?php echo $rid; ?>" />
                                    <input type="hidden" name="assign_type" value="3" />
                                    <input type="hidden" name="display_questions" value="0" />
                                <button id="kt_login_signin_submit_continue" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"   name="submit_value" value="exit"><i class="la la-check"></i>Save </button>
                                  </form>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="goal-1" role="tabpanel" aria-labelledby="profile-tab-1">
                            <div class="card card-custom">
                                <div class="card-body">
                                    <form class="kt-form" id="kt_form_3" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">
                                    <div class="row ">
                                        <div class="form-group">
                                            <div class="checkbox-list">
                                                <label class="checkbox">
                                                    <input aria-label="Checkbox" type="checkbox"  <?php if($goal['kra']==1){ echo "checked";
} ?>  name="kra"/>
                                                    <span class="float-left"></span>
                                                    Key Result Areas
                                                </label>
                                                <label class="checkbox">
                                                    <input aria-label="Checkbox" type="checkbox" <?php if($goal['kpi']==1){ echo "checked";
} ?>  name="kpi"/>
                                                    <span class="float-left"></span>
                                                    Key Performance Indicator
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="submit" value="savegoal" />
                                    <input type="hidden" name="rid" value="<?php echo $rid; ?>" />
                                <button id="kt_login_signin_submit_3" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"   name="submit_value" value="exit"><i class="la la-check"></i>Save </button>
                                  </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$apiurl = API."?mode=getpmsadmindashboard&rid=".$performance_appraisal_setup_id;
$apicallss = callAPI($apiurl);
$result = json_decode($apicallss,1);
$employee_goals = $result['employee_goals'];
$yet_to_start = $inprogress = $completed =0;
foreach($employee_goals as $eg){
    if($eg['percentage_of_completion'] == '0' || $eg['percentage_of_completion'] == ''){
        $yet_to_start++;
    }elseif($eg['percentage_of_completion'] == '100'){
        $completed++;
    }else{
        $inprogress++;
    }
}
$employees = $result['employees'];
$total_cnt = $result['total_cnt'];
$employee_performance_appraisals_ratting = $result['employee_performance_appraisals_ratting'];
$performance_appraisal_setups = $result['performance_appraisal_setups'];
$employee_performance_appraisals = $result['employee_performance_appraisals'];
$goals = $result['goals'];
$all_employee_goals = $result['all_employee_goals'];
$feedbacks = $result['feedbacks'];
$employee_feedbacks = $result['employee_feedbacks'];
$arslabs  = $result['arslabs'];
?>
<script type="text/javascript">
    function getEmployeeGoals(emp_goal_id,emp_id){
        jQuery.ajax({
            url: "views/custom_insert_employee_goals_popup.php",
            data:'emp_goal_id='+emp_goal_id+'&emp_id='+emp_id,
            type: "POST",
            success:function(data){
                $('#dynamic_popup1').html(data);
                $("#kt_modal_dynamic_popup").modal("show");
                $('[data-bs-toggle="tooltip"]').tooltip();
            },
            error:function (){}
        });
    }
    function openPopUp(classtext,rowid){
        $(".custom-popup-open").hide();
        $(".open-popup-"+classtext+rowid).show('slow');
    }
    function closePopUp(){
        $(".custom-popup-open").hide();
    }
    function getEmployeeFeedbacks(emp_fd_id, emp_id){
        jQuery.ajax({
            url: "views/custom_insert_employee_feedbacks_popup.php",
            data:'emp_fd_id='+emp_fd_id+'&emp_id='+emp_id,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $("#kt_modal_KTDatatable_remote").modal("show");
                $(".js-example-basic-single").select2();
            },
            error:function (){}
        });
    }
</script>
<div class="row">
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card prod-p-card card-red">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="m-b-20 text-white">Total Employess 2</h6>
                        <h3 class="m-b-0 f-w-700 text-white"><?php echo $total_cnt['total_employee']; ?></h3>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill-alt text-c-red f-18"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card prod-p-card card-blue">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col col-lg-5">
                        <h6 class="m-b-20 text-white f-14 text-center">Total Appraisals</h6>
                        <div class="row align-items-center text-center">
                            <div class="col">
                                <h3 class="m-b-0 text-white"><?php echo $total_cnt['total_appraisals']; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col col-lg-2 text-center">
                        <i class="fas fa-exchange-alt text-c-green f-18"></i>
                    </div>
                    <div class="col col-lg-5">
                        <h6 class="m-b-20 text-white f-14 text-center">Total Publish</h6>
                        <div class="row align-items-center text-center">
                            <div class="col">
                                <h3 class="m-b-0 text-white"><?php echo $total_cnt['total_appraisal_shared']; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card prod-p-card card-green">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col col-lg-5">
                        <h6 class="m-b-20 text-white f-14 text-center">Total Goals</h6>
                        <div class="row align-items-center text-center">
                            <div class="col">
                                <h3 class="m-b-0 text-white"><?php echo $total_cnt['total_goals']; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col col-lg-2 text-center">
                        <i class="fas fa-exchange-alt text-c-green f-18"></i>
                    </div>
                    <div class="col col-lg-5">
                        <h6 class="m-b-20 text-white f-14 text-center">Total Publish</h6>
                        <div class="row align-items-center text-center">
                            <div class="col">
                                <h3 class="m-b-0 text-white"><?php echo $total_cnt['total_shared_goals']?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
     <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card prod-p-card card-yellow">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col col-lg-5">
                        <h6 class="m-b-5 text-white f-14 text-cente">Request FeedBacks</h6>
                        <div class="row align-items-center text-center">
                            <div class="col">
                                <h3 class="m-b-0 text-white"><?php echo $total_cnt['total_goals']; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col col-lg-2 text-center">
                        <i class="fas fa-exchange-alt text-c-green f-18"></i>
                    </div>
                    <div class="col col-lg-5">
                        <h6 class="m-b-5 text-white f-14 text-center">Received FeedBacks</h6>
                        <div class="row align-items-center text-center">
                            <div class="col">
                                <h3 class="m-b-0 text-white"><?php echo $total_cnt['total_feedbacks']; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-5 col-md-5 col-sm-12 col-12">
        <div class="card">
            <div class="card-header">
                <h5>Employee Count By Rattings</h5>
            </div>
            <input type="hidden" id="employee_performance_appraisals_ratting" value='<?php echo json_encode((array)$employee_performance_appraisals_ratting); ?>'>
            <div class="card-body" style="height: 500px; width: 500px">
                <canvas id="barChart" width="200" height="200"></canvas>
            </div>
        </div>
    </div>
     <div class="col-lg-7 col-md-7 col-sm-12 col-12">
        <div class="card">
            <div class="card-header">
                <h5>My Goals</h5>
                <div class="card-header-right table-card-header">
                    <span class="text-c-yellow f-w-700">Count Of Goals : <?php echo count((array)$employee_goals); ?></span>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                        <div class="social-card">
                            <div class="card-body text-center">
                                <div class="text-info"><i class="fa fa-question-circle"></i></div>
                                <h3 class="text-facebook f-w-700 mb-0"><?php echo $yet_to_start; ?></h3>
                                <p>Yet To Start</p>
                            </div>
                        </div>
                        <div class="social-card">
                            <div class="card-body text-center">
                                <div class="text-primary"><i class="fa fa-hourglass-half"></i></div>
                                <h3 class="text-facebook f-w-700 mb-0"><?php echo $inprogress; ?></h3>
                                <p>In Progress</p>
                            </div>
                        </div>
                        <div class="social-card">
                            <div class="card-body text-center">
                                <div class="text-danger"><i class="fa fa-thumbs-up"></i></div>
                                <h3 class="text-facebook f-w-700 mb-0"><?php echo $completed; ?></h3>
                                <p>Completed</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-12">
                        <div class="table-responsive custom-scroll" style="height: 450px;">
                            <table class="table table-hover m-b-0" style="font-size:13px;">
                                <thead>
                                    <tr>
                                        <th scope="col" class="w-b-w-s" style="width: 30%;">Employee</th>
                                        <th scope="col" class="w-b-w-s" style="width: 30%;">Question</th>
                                        <th scope="col" style="width: 10%;">End Date</th>
                                        <th scope="col" style="width: 10%;">End Date</th>
                                        <th scope="col" style="width: 20%;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($employee_goals as $eg){
                                        $empdetail = $employees[$eg['employee_id']];
                                        ?>
                                    <tr onclick="getEmployeeGoals('<?php echo $eg['id']; ?>','<?php echo $eg['employee_id']?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                                        <td class="p-2 w-b-w-s">
                                            <div class="align-middle">
                                            <?php if($empdetail['image'] !=""){?>
                                                <img src="uploads/all/<?php echo $empdetail['image']; ?>"  class="img-radius img-30 align-top m-r-15" alt="Attachment Preview" onerror="this.src='uploads/all/no-image.jpg';">
                                            <?php } else { ?>
                                                <span  alt="user" class="img-radius align-middle m-r-0 custom_txt_round p-1 f-12 text-center img-40"><?php echo initials($empdetail['display_name']); ?></span>
                                            <?php } ?>
                                            <div class="d-inline-block width73">
                                                <span class="f-12"><?php echo $empdetail['display_name']; ?></span>
                                            </div>
                                        </div>

                                        </td>
                                        <td class="p-2 w-b-w-s"><?php echo $eg['goal_name']; ?></td>
                                        <td class="p-2"><?php echo fromDate($eg['goal_start_date']); ?></td>
                                        <td class="p-2"><?php echo fromDate($eg['goal_end_date']); ?></td>
                                        <td class="p-2">
                                            <div class="row align-items-center">
                                                <div class="col">
                                                    <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                                                        <span class="fw-bold fs-6 text-gray-400">Progress</span>
                                                        <span class="fw-bolder fs-6 ml-2"><?php echo $eg['percentage_of_completion']; ?>%</span>
                                                    </div>
                                                    <div class="progress progress-xs">
                                                        <div class="progress-bar bg-c-green" style="width:<?php echo $eg['percentage_of_completion']; ?>%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-lg-7 col-md-7 col-sm-12 col-12">
                <h5>Appraisals</h5>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                 <span class="form-label text-start">Aassessment Year</span>
                <select aria-label="Assessment Year" class="form-select js-example-basic-single" name="assessment_year" >
                    <option value="">Select Any One</option>
                    <?php for ($i=2020; $i<2090;$i++) {
                        $k= $i+1; $view_assemnt_year =$i."-".$k; ?>
                        <option <?php if(date('Y') == $view_assemnt_year){ echo "selected";
} ?> value="<?php echo $view_assemnt_year; ?>"  ><?php echo $i." - ".$k ?></option>
                    <?php  } ?>
                </select>
            </div>
            <div class="col-lg-1 col-md-1 col-sm-12 col-12">
               <a href="#!" onclick="viewAllRecord('quotations',2)" class=" b-b-primary text-primary float-right" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">View all</a>
           </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive custom-scroll mt-5 " style=" height: 350px;">
            <table class="table table-hover m-b-0" style="font-size:13px;">
                <thead>
                    <tr>
                        <th scope="col" class="w-b-w-s">Appraisal Name</th>
                        <th scope="col">Assessment Year</th>
                        <th scope="col">Assessment Start Date</th>
                        <th scope="col">Assessment End Date</th>
                        <th scope="col">Self Assessment Start Date</th>
                        <th scope="col">Self Assessment Start Date</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($performance_appraisal_setups as $pas){ ?>
                    <tr>
                        <td class="p-2 text-primary"><a href="index.php?option=COM_INSERT_PERFORMANCE_APPRAISAL_SETUPS&Rid=<?php echo deocdeId('id:'.$pas['id']); ?>" target="_blank"><?php echo $pas['performance_appraisal_name']; ?></a></td>
                        <td class="p-2"><?php echo $pas['assessment_year']; ?></td>
                        <td class="p-2"><?php echo fromDate($pas['assessment_start_date']); ?></td>
                        <td class="p-2"><?php echo fromDate($pas['assessment_end_date']); ?></td>
                        <td class="p-2 custom-popup">
                            <?php echo fromDate($pas['self_assessment_start_date']); ?>

                        <div class=" custom-popup-open open-popup-appraisal<?php echo $pas['id']; ?> text-start" style="display: none; width: 500px; ">
                            <div class="card">
                                <div class="card-header">
                                  <h5>Appraisals Employee</h5>
                                  <div class="card-header-right">
                                    <button type="button" class="btn p-0 border-0 bg-transparent ti-close" onclick="closePopUp()" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Closed" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
                                  </div>
                                </div>
                                <div class="card-body  custom-scroll" style="height: 250px;">
                                    <table class="table table-hover m-b-0" style="font-size:13px;">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="w-b-w-s">Employee</th>
                                                <th scope="col" class="text-right">Self Score</th>
                                                <th scope="col" class="text-right">Ro Score</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach($employee_performance_appraisals[$pas['id']] as $epadeatail){
                                            $empdetail = $employees[$epadeatail['employee_id']];
                                            ?>
                                            <tr>
                                                <td class="p-2 w-b-w-s">
                                                    <a href="index.php?option=COM_INSERT_EMPLOYEE_PERFORMANCE_APPRAISALS&Rid=<?php echo deocdeId('id:'.$epadeatail['id']); ?>" target="_blank">
                                                    <div class="align-middle">
                                                    <?php if($empdetail['image'] !=""){?>
                                                        <img src="uploads/all/<?php echo $empdetail['image']; ?>"  class="img-radius img-30 align-top m-r-15" alt="Attachment Preview" onerror="this.src='uploads/all/no-image.jpg';">
                                                    <?php } else { ?>
                                                        <span alt="user" class="img-radius img-30 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($empdetail['display_name']); ?></span>
                                                    <?php } ?>
                                                        <div class="d-inline-block width73">
                                                            <span class="f-12"><?php echo $empdetail['display_name']; ?></span>
                                                            <p class="mb-0"><?php echo $empdetail['emp_code']; ?></p>
                                                        </div>
                                                    </div>
                                                    </a>
                                                </td>
                                                <td>  <?php
                                        foreach($arslabs as $slab){
                                            if($slab['start_range'] <= $epadeatail['self_total'] && $slab['end_range'] >= $epadeatail['self_total']){  ?>
                                               <span class="label label-lg label-inline f-w-700" style="background-color:<?php echo $slab['background_color'];?> "><?php echo $slab['slabs_name'];?></span>

                                            <?php }
                                        }
                                        ?></td>
                                                <td><?php
                                        if($epadeatail['current_status'] == ST_APPROVED){
                                            foreach($arslabs as $slab){
                                                if($slab['start_range'] <= $epadeatail['ro_total'] && $slab['end_range'] >= $epadeatail['ro_total']){  ?>
                                                   <span class="label label-lg label-inline f-w-700" style="background-color:<?php echo $slab['background_color'];?> "><?php echo $slab['slabs_name'];?></span>

                                                <?php }
                                            }
                                        }else{ echo " - ";
}  ?></td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        </td>
                        <td class="p-2"><?php echo fromDate($pas['self_assessment_end_date']); ?></td>
                        <td class="p-2">
                            <span class="form-label label label-sm label-primary" onclick="openPopUp('appraisal','<?php echo $pas['id']; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"> <i class="ti-user f-16"></i>  &nbsp; &nbsp; <span class="f-16"><?php echo count((array)$employee_performance_appraisals[$pas['id']]); ?></span>
                            </span>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5>Goals</h5>
                <div class="card-header-right table-card-header m-r-10">
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive custom-scroll " style=" height: 350px;">
                    <table class="table table-hover m-b-0" style="font-size:13px;">
                        <thead>
                            <tr>
                                <th scope="col" class="w-b-w-s" style="width: 50%;">Goal Name</th>
                                <th scope="col" style="width: 20%;">Goal Start Date</th>
                                <th scope="col" style="width: 20%;">Goal End Date</th>
                                <th scope="col" style="width: 10%;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($goals as $goalres){ ?>
                            <tr>
                                <td class="p-2 text-primary w-b-w-s custom-popup"><a href="index.php?option=COM_INSERT_GOALS&Rid=<?php echo deocdeId('id:'.$goalres['id']); ?>" target="_blank"><?php echo $goalres['goal_name']; ?></a>

                                    <div class=" custom-popup-open open-popup-goals<?php echo $pas['id']; ?> text-start" style="display: none; width: 500px; ">
                                    <div class="card">
                                        <div class="card-header">
                                          <h5>Appraisals Employee</h5>
                                          <div class="card-header-right">
                                            <button type="button" class="btn p-0 border-0 bg-transparent ti-close" onclick="closePopUp()" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Closed" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
                                          </div>
                                        </div>
                                        <div class="card-body  custom-scroll" style="height: 250px;">
                                            <table class="table table-hover m-b-0" style="font-size:13px;">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">Employee</th>
                                                        <th scope="col">Progress</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php foreach($all_employee_goals[$pas['id']] as $empgoaldetails){
                                                    $empdetail = $employees[$empgoaldetails['employee_id']];
                                                    ?>
                                                    <tr>
                                                        <td class="p-2 w-b-w-s">
                                                            <div class="align-middle">
                                                            <?php if($empdetail['image'] !=""){?>
                                                                <img src="uploads/all/<?php echo $empdetail['image']; ?>"  class="img-radius img-30 align-top m-r-15" alt="Attachment Preview" onerror="this.src='uploads/all/no-image.jpg';">
                                                            <?php } else { ?>
                                                                <span alt="user" class="img-radius img-30 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($empdetail['display_name']); ?></span>
                                                            <?php } ?>
                                                                <div class="d-inline-block width73">
                                                                    <span class="f-12"><?php echo $empdetail['display_name']; ?></span>
                                                                    <p class="mb-0"><?php echo $empdetail['emp_code']; ?></p>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="row align-items-center">
                                                                <div class="col">
                                                                    <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                                                                        <span class="fw-bold fs-6 text-gray-400">Progress</span>
                                                                        <span class="fw-bolder fs-6 ml-2"><?php echo $empgoaldetails['percentage_of_completion']; ?>%</span>
                                                                    </div>
                                                                    <div class="progress progress-xs">
                                                                        <div class="progress-bar bg-c-green" style="width:<?php echo $empgoaldetails['percentage_of_completion']; ?>%"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                </td>
                                <td class="p-2" ><?php echo fromDate($goalres['goal_start_date']); ?></td>
                                <td class="p-2" ><?php echo fromDate($goalres['goal_end_date']); ?></td>
                                <td class="p-2">
                                    <span class="form-label label label-sm label-primary" onclick="openPopUp('goals','<?php echo $goalres['id']; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"> <i class="ti-user f-16"></i>  &nbsp; &nbsp; <span class="f-16"><?php echo count((array)$all_employee_goals[$goalres['id']]); ?></span>
                                    </span>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5>FeedBacks</h5>
                <div class="card-header-right table-card-header m-r-10">
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive custom-scroll " style=" height: 350px;">
                    <table class="table table-hover m-b-0" style="font-size:13px;">
                        <thead>
                            <tr>
                                <th scope="col" class="w-b-w-s" style="width: 80%;">Question</th>
                                <th scope="col" style="width: 20%;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($feedbacks as $feedbackres){ ?>
                            <tr>
                                <td class="p-2 text-primary w-b-w-s custom-popup"><a href="index.php?option=COM_INSERT_FEEDBACKS&Rid=<?php echo deocdeId('id:'.$feedbackres['id']); ?>" target="_blank"><?php echo $feedbackres['question']; ?></a>

                                <div class=" custom-popup-open open-popup-feedbacks<?php echo $pas['id']; ?> text-start" style="display: none; width: 500px; left: 40px;">
                                    <div class="card">
                                        <div class="card-header">
                                          <h5>Employee FeedBack</h5>
                                          <div class="card-header-right">
                                            <button type="button" class="btn p-0 border-0 bg-transparent ti-close" onclick="closePopUp()" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Closed" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
                                          </div>
                                        </div>
                                        <div class="card-body  custom-scroll" style="height: 250px;">
                                            <table class="table table-hover m-b-0" style="font-size:13px;">
                                                <thead>
                                                    <tr>
                                                        <th scope="col" class="w-b-w-s" style="width: 80%;">Employee</th>
                                                        <th scope="col" style="width: 20%;">Response</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php foreach($employee_feedbacks[$feedbackres['id']] as $feeddetail){
                                                    $empdetail = $employees[$feeddetail['employee_id']];
                                                    ?>
                                                    <tr>
                                                        <td class="p-2 w-b-w-s">
                                                            <div class="align-middle">
                                                            <?php if($empdetail['image'] !=""){?>
                                                                <img src="uploads/all/<?php echo $empdetail['image']; ?>"  class="img-radius img-30 align-top m-r-15" alt="Attachment Preview" onerror="this.src='uploads/all/no-image.jpg';">
                                                            <?php } else { ?>
                                                                <span alt="user" class="img-radius img-30 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($empdetail['display_name']); ?></span>
                                                            <?php } ?>
                                                                <div class="d-inline-block width73">
                                                                    <span class="f-12"><?php echo $empdetail['display_name']; ?></span>
                                                                    <p class="mb-0"><?php echo $empdetail['emp_code']; ?></p>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="p-2  text-center">
                                                            <?php if($feeddetail['response_status']==''){ echo "Pendding";
}else{  ?>
                                                                <i class="ti-eye f-16" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="View FeedBack" onclick="getEmployeeFeedbacks('<?php echo $feeddetail['id']; ?>','<?php echo $feeddetail['employee_id']?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                </td>
                                <td class="p-2">
                                    <span class="form-label label label-sm label-primary" onclick="openPopUp('feedbacks','<?php echo $feedbackres['id']; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"> <i class="ti-user f-16"></i>  &nbsp; &nbsp; <span class="f-16"><?php echo count((array)$employee_feedbacks[$feedbackres['id']]); ?></span>
                                    </span>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>


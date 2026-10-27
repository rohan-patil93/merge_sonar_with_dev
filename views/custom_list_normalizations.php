<?php
$_SESSION['apply_js']['multiselest'] = 1;
$_POST['company_id'] = $_SESSION['company_id'];
$_POST['user_id'] = $_SESSION['userid'];
$url = API."?mode=getemployeeperformanceappraisalsnormalisations&method=post";
$response1 = file_post_contents($url, $_POST, 'insert');
$result = json_decode($response1,1);
if($_POST['submit_btn'] == 'submitnormalizations'){ ?>
<script type="text/javascript">
    document.onreadystatechange = function(){
        if(document.readyState == "complete") {
            tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');
        }
    }
</script>
 <?php }
$performance_appraisal_setups = $result['performance_appraisal_setups'];
$employee_performance_appraisals = $result['employee_performance_appraisals'];
$employees = $result['employees'];
$arslabs = $result['arslabs'];
$application_users = $result['application_users'];
$locations = $result['locations'];
$departments = $result['departments'];
$job_title = $result['job_title'];
$appraisal_bell_curves = $result['appraisal_bell_curves'];
$slabdetail = array();
foreach($arslabs as $ars){
    $slabdetail[$ars['id']] = $ars;
}
$slabs = json_decode($appraisal_bell_curves['slabs'],1);
$empranking = array();
foreach($employee_performance_appraisals as $epa){
    $empranking[$epa['ro_appraisal_rating_slab_id']][] = $epa['employee_id'];
}
?>
<script type="text/javascript">
    function allCheckNormalize(){
        if($("#allchecknormalize").is(':checked')){
            $('.checknormalize').prop('checked', true);
            $(".submitnormalizations").show();
            $(".checknormalize_enter").css("pointer-events","visible");
            $(".checknormalize_enter").css("opacity","1");
        }else{
            $('.checknormalize').prop('checked', false);
            $(".submitnormalizations").hide();
            $(".checknormalize_enter").css("pointer-events","none");
            $(".checknormalize_enter").css("opacity","0.4");
        }
    }
    function getAppraisalId(appraisal_id){
        console.log();
        var appraisal_bell_curve_id = $('#appraisal_id option:selected').data('id')
        if(appraisal_bell_curve_id !="" && appraisal_id !=""){
            jQuery.ajax({
                url: "ajax.php",
                data:'page=getappraisalbellcurves&appraisal_id='+appraisal_id+'&rid='+appraisal_bell_curve_id,
                type: "POST",
                success:function(data){
                    console.log(data);
                    $("#appraisal_bell_curves").html(data);
                    $("#showallfilter").show();
                },
                error:function (){}
            });
        }else{
            $("#showallfilter").hide();
            $("#appraisal_bell_curves").html(' ');


        }
        console.log(appraisal_id);
    }
    function singleCheckNormalize(eid){
        console.log(eid);
        if($("#singlecheck_"+eid).is(':checked')){
            $("#singlevisiblecheck_"+eid).css("pointer-events","visible");
            $("#singlevisiblecheck_"+eid).css("opacity","1");
        }else{
            $("#singlevisiblecheck_"+eid).css("pointer-events","none");
            $("#singlevisiblecheck_"+eid).css("opacity","0.4");
        }
    }
    function chnageRanking(){
        var cnt1 = cnt2= cnt3 = cnt4= cnt5 = cnt6 = 0;
        $('.allrankingcnt').each(function(){
            if($(this).val() =="1"){
                cnt1++;
            }
            if($(this).val() =="2"){
                cnt2++;
            }
            if($(this).val() =="3"){
                cnt3++;
            }
            if($(this).val() =="4"){
                cnt4++;
            }
            if($(this).val() =="5"){
                cnt5++;
            }
            if($(this).val() =="6"){
                cnt6++;
            }
            $("#slabs_1").text(cnt1);
            $("#slabs_2").text(cnt2);
            $("#slabs_3").text(cnt3);
            $("#slabs_4").text(cnt4);
            $("#slabs_5").text(cnt5);
            $("#slabs_6").text(cnt6);

        });
    }

</script>
<div class="card custom_card">
    <div class="card-header">
        <h5>Normalizations Appraisals</h5>
        <div class="card-header-right table-card-header">
            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                <i class="icofont icofont-reply"></i>
                Back
            </a>
        </div>
    </div>
    <div class="card-body ">
        <form method="post">
            <div class="row">
                <div class="col-lg-4 w-full">
                    <span class="form-label">Appraisal</span>
                    <select aria-label="Appraisal" class="form-control form-select js-example-basic-single" name="appraisal_id" id="appraisal_id" onchange="getAppraisalId(this.value)">
                        <option value="">Select Any One</option>
                        <?php foreach($performance_appraisal_setups as $pas){ ?>
                            <option value="<?php echo $pas['id']; ?>" <?php echo $_POST['appraisal_id'] == $pas['id']?"selected":"";?> data-id="<?php echo $pas['appraisal_bell_curve_id']; ?>"><?php echo $pas['performance_appraisal_name']." - ".$pas['assessment_year']; ?></option>
                        <?php  } ?>
                    </select>
                </div>
                <div class="col-lg-8 text-right p-t-35" id="appraisal_bell_curves">
                    <?php
                        foreach($slabs as $slab){
                            $slabdetails =$slabdetail[$slab['appraisal_rating_slab_id']];
                            echo '<span class="label label-md label-inline f-w-700" style="background-color :'.$slabdetails['background_color'].'">'.$slabdetails['slabs_name']." - ".$slab['percentage'].'%</span>';
                        }
                    ?>
                </div>
            </div>
            <div class="row mt-2" id="showallfilter" <?php echo $_POST['appraisal_id'] ==""?"style='display:none'":"";?>>
                <div class="col-lg-2 w-full">
                    <span class="form-label">Aassessment Year</span>
                    <select aria-label="Assessment Year" class="form-control form-select js-example-basic-single" name="assessment_year" >
                        <option value="">Select Any One</option>
                        <?php for ($i=2020; $i<2090;$i++) {
                            $k= $i+1; $view_assemnt_year =$i."-".$k; ?>
                            <option <?php echo $_POST['assessment_year'] == $view_assemnt_year?"selected":"";?> value="<?php echo $view_assemnt_year; ?>"  ><?php echo $i." - ".$k ?></option>
                        <?php  } ?>
                    </select>
                </div>
                <div class="col-lg-2 w-full">
                    <span class="form-label">Reporting Manager</span>
                    <select aria-label="Reports To" class="form-control form-select js-example-basic-single" name="reports_to" >
                        <option value="">Select Any One</option>
                        <?php foreach($application_users as $au){ ?>
                            <option value="<?php echo $au['id']; ?>"  <?php echo $_POST['reports_to'] == $au['id']?"selected":"";?>><?php echo $au['display_name']; ?></option>
                        <?php  } ?>
                    </select>
                </div>
                <div class="col-lg-2 w-full">
                    <span class="form-label">Location</span>
                    <select aria-label="Location" class="form-control form-select js-example-basic-single" name="location_id" >
                        <option value="">Select Any One</option>
                        <?php foreach($locations as $loc){ ?>
                            <option value="<?php echo $loc['id']; ?>"  <?php echo $_POST['location_id'] == $loc['id']?"selected":"";?>><?php echo $loc['location_name']; ?></option>
                        <?php  } ?>
                    </select>
                </div>
                <div class="col-lg-2 w-full">
                    <span class="form-label">Department</span>
                    <select aria-label="Department" class="form-control form-select js-example-basic-single" name="department_id" >
                        <option value="">Select Any One</option>
                        <?php foreach($departments as $dep){ ?>
                            <option value="<?php echo $dep['id']; ?>"  <?php echo $_POST['department_id'] == $dep['id']?"selected":"";?>><?php echo $dep['department_name']; ?></option>
                        <?php  } ?>
                    </select>
                </div>
                <div class="col-lg-2 w-full">
                    <span class="form-label">Designation</span>
                    <select aria-label="Job Title" class="form-control form-select js-example-basic-single" name="job_title_id" >
                        <option value="">Select Any One</option>
                        <?php foreach($job_title as $jt){ ?>
                            <option value="<?php echo $jt['id']; ?>"  <?php echo $_POST['job_title_id'] == $jt['id']?"selected":"";?>><?php echo $jt['job_title']; ?></option>
                        <?php  } ?>
                    </select>
                </div>
                <div class="col-lg-2">
                    <span class="form-label">Date of joining</span>
                    <input aria-label="Joining Date" type="text" class="form-control datepicker"  name="joining_date" value="<?php echo $_POST['joining_date'];?>">
                </div>
                <div class="col-lg-4 mt-2 w-full">
                    <span class="form-label">Employee</span>
                    <select aria-label="Emp []" class="form-control form-select js-example-basic-multiple" name="emp_id[]" multiple>
                        <?php foreach($employees as $em){ ?>
                            <option value="<?php echo $em['id']; ?>"  <?php echo (in_array($em['id'], (array)$_POST['emp_id']))?"selected":""; ?>><?php echo $em['display_name']; ?></option>
                        <?php  } ?>
                    </select>
                </div>
            </div>
            <div class="mt-3 row">
                <div class="col-lg-2">
                <button type="submit" class="btn btn-primary waves-effect waves-light " name="submit_btn" value="submitfilter"><i class="fa fa-check m-r-10"></i> Filter</button>
                </div>
                <div class="col-lg-10 text-right ">
                    <?php
                        foreach($arslabs as $slab){
                            echo '<span class="label label-md label-inline f-w-700" style="background-color :'.$slab['background_color'].'" >'.$slab['slabs_name'].' - <span id="slabs_'.$slab['id'].'">'.count((array)$empranking[$slab['id']]).'</span></span>';
                        }
                    ?>

                </div>
            </div>
        <div class="table-responsive mt-5">
            <?php if(count((array)$employee_performance_appraisals) !=0){ ?>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col">
                            <div class="checkbox-fade fade-in-primary">
                                <label class="form-label">
                                    <input aria-label="Allchecknormalize" type="checkbox" class="podalist" id="allchecknormalize" onclick="allCheckNormalize()" value="" onkeydown="">
                                    <span class="cr">
                                      <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                    </span>
                                </label>
                            </div>
                        </th>
                        <th scope="col">Employee</th>
                        <th scope="col">Self Grade</th>
                        <th scope="col">RO Grade</th>
                        <th scope="col">Final Grade</th>
                        <th scope="col">RO Comment</th>
                        <th scope="col">HOD Comment</th>
                    </tr>
                </thead>
                <tbody>
                <?php     $mk=1;
                        foreach($employee_performance_appraisals as $epa){
                        $empdetail = $employees[$epa['employee_id']];
                    ?>
                    <tr <?php echo $epa['normalization_approved_status'] =='0'?'style="pointer-events: none; opacity: 0.4;" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Normalization Approved"':'';?>>
                        <td class="p-2" >
                            <div class="checkbox-fade fade-in-primary">
                                <label class="form-label">
                                    <input aria-label="Checkbox" type="checkbox" id="singlecheck_<?php echo $epa['id']; ?>" class="podalist <?php echo $epa['normalization_approved_status'] =='0'?"disabled":"checknormalize"; ?>" name="employee_performance_appraisal_id[]" value="<?php echo $epa['id']; ?>"  onclick="singleCheckNormalize('<?php echo $epa['id']; ?>')" onkeydown="">
                                    <span class="cr">
                                      <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                    </span>
                                </label>
                            </div>
                        </td>
                        <td class="p-2 ">
                            <div class="align-middle">
                            <?php if($empdetail['image'] !=""){?>
                                <img src="uploads/all/<?php echo $empdetail['image']; ?>"  class="img-radius img-30 align-top m-r-15" alt="Attachment Preview">
                            <?php } else { ?>
                                <span  alt="user" class="img-radius img-30 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($empdetail['display_name']); ?></span>
                            <?php } ?>
                                <div class="d-inline-block width73">
                                    <span class="f-12"><?php echo $empdetail['display_name']; ?></span>
                                    <p class="mb-0"><?php echo $empdetail['emp_code']; ?> (<?php echo fromDate($epa['start_date'])." To ".fromDate($epa['end_date']); ?>)</p>
                                </div>
                            </div>
                        </td>
                        <td class="p-2">
                            <?php
                            foreach($arslabs as $slab){
                                if($slab['start_range'] <= $epa['self_total'] && $slab['end_range'] >= $epa['self_total']){ ?>
                                    <span class="label label-md  label-inline" style="background-color:<?php echo $slab['background_color'];?> "><b><?php echo $slab['slabs_name'];?></b></span>
                                <?php }
                            }
                            ?>
                            </td>
                        <td class="p-2" >
                            <?php
                            foreach($arslabs as $slab){
                                if($slab['start_range'] <= $epa['ro_total'] && $slab['end_range'] >= $epa['ro_total']){ ?>
                                    <span class="label label-md  label-inline" style="background-color:<?php echo $slab['background_color'];?> "><b><?php echo $slab['slabs_name'];?></b></span>
                                <?php }
                            }
                            ?>
                        </td>
                        <td class="p-2 <?php echo $epa['normalization_approved_status'] =='0'?"":"checknormalize_enter"; ?>" <?php echo $epa['normalization_approved_status'] =='0'?"":'style="pointer-events: none; opacity: 0.4;"'; ?> id="singlevisiblecheck_<?php echo $epa['id']; ?>">
                            <div class="w-full">
                                <select aria-label="Select option" class="form-control form-select js-example-basic-single allrankingcnt" name="normalization_ranking[<?php echo $epa['id']; ?>]" onchange="chnageRanking()">
                                    <?php foreach($arslabs as $slab){
                                        $selected = "";
                                        if($epa['normalization_ranking'] == $slab['id']){
                                            $selected = "selected";
                                        }
                                        if($epa['normalization_ranking'] == "" && $slab['start_range'] <= $epa['ro_total'] && $slab['end_range'] >= $epa['ro_total']){
                                            $selected = "selected";
                                        }
                                        ?>
                                        <option <?php echo $selected;  ?> value="<?php echo $slab['id'];?>"  ><?php echo $slab['slabs_name'];?></option>
                                    <?php  } ?>
                                </select>
                            </div>
                        </td>
                        <td class="p-2 w-b-w-s"><?php echo $epa['ro_comment']; ?></td>
                        <td class="p-2 w-b-w-s"><?php echo $epa['hod_comment']; ?></td>
                    </tr>
                <?php $mk++;
} ?>

                </tbody>
            </table>
            <div class="mt-5">
                <button type="submit" class="btn btn-info waves-effect waves-light submitnormalizations m-r-20"  name="submit_btn" value="savenormalizations"><i class="fa fa-folder-open m-r-10"></i> Save</button>
                <button type="submit" class="btn btn-primary waves-effect waves-light submitnormalizations"  name="submit_btn" value="submitnormalizations"><i class="fa fa-check m-r-10"></i> Submit</button>
            </div>
        <?php }else{ ?>
            <div class="card-px text-center ">
                <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Normalization / Please Apply Filter</p>
                <div class="text-center px-4">
                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                </div>
            </div>
        <?php } ?>
        </div>
    </form>
</div>

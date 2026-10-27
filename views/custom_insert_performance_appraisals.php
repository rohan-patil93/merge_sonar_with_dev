
<?php
if(isset($_POST['performanace_appraiser_sbt'])){
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
    $url = API."?mode=inserperformanaceappraiser&method=post";
    $response0 = file_post_contents($url, $_POST, 'insert');
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
$drid = encodeId($_GET['Rid']);
$erid = explode(":",$drid);
$epa_header_id  = $erid['1'];
$epahwh["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id'],'id'=>$epa_header_id);
$epahwh['field'] = "id,appraisal_id,employee_id,assessment_year,current_status";
$epahencode = urlencode(json_encode($epahwh));
$epahurl = API."?mode=gettabledetailswithconditions&table=employee_performance_appraisal_headers&data=".$epahencode;
$epasresult = callAPI($epahurl);
$epasdecod = json_decode($epasresult,1);

$pepah_res = $epasdecod[0];
$appraisal_id  = $pepah_res['appraisal_id'];
$assessment_year  = $pepah_res['assessment_year'];
$employee_id = $pepah_res['employee_id'];
$url = API."?mode=getperformanceappraisal&emp_id=".$employee_id."&assessment_year=".$assessment_year."&appraisal_id=".$appraisal_id."&epa_header_id=".$epa_header_id;
$response = callAPI($url);
$kipandkradetails = json_decode($response,1);

$employees_detail  = $kipandkradetails['employee'];
$report_to_detail  = $kipandkradetails['report_to'];
$arslabs  = $kipandkradetails['arslabs'];
$paheaders  = $kipandkradetails['performance_appraisal_headers'];
$performance_setup  = $kipandkradetails['performance_setup'];

$buddy_details  = $kipandkradetails['buddy_details'];
$employees  = $kipandkradetails['employees'];
$pasharray = array();
$pashres = $kipandkradetails['performance_appraisal_setting_histories'];
foreach($pashres as $pashres){
    $pasharray[$pashres['appraisal_setting_id']] = $pashres['appraisal_setting_id'];
}

$emp_questions = $kipandkradetails['emp_questions'];
$mgr_questions = $kipandkradetails['mgr_questions'];
$reviewmgr_questions = $kipandkradetails['reviewmgr_questions'];
$typeofquestion= array('1'=>'MCQ','2'=>'FT');

$perfresdetails = $kipandkradetails['performance_responses_details'];
foreach($perfresdetails as $perfresdetails){
$emprohodres[$perfresdetails['question_id']] = $perfresdetails;
}
$total_kpi_target = 0;
foreach($kipandkradetails['kra_list'] as $kralist){
    $kraname[$kralist['key_reslut_id']] = $kralist['area_name'];
    $kraweightage[$kralist['key_reslut_id']] = $kralist['kra_weightage']; //
    $kra_list[$kralist['kra_type']][$kralist['key_reslut_id']][] = $kralist;
    $total_kpi_target += $kralist['traget'];
}
foreach($kipandkradetails['employee_performance_appraisals'] as $epa){
    $epappraisal[$epa['header_id']][] = $epa;
}
$stausresult = ViewStatusData();

if($performance_setup['publish']=='Y'){
    if($buddy_details['hr_connect'] == $_SESSION['userid']){
        if($pepah_res['current_status'] != ST_APPROVED2){
            $updatebtndisable = 'style="pointer-events: none; opacity: 0.6;"';
        }
        $loginuser_type = "HOD"; //EMP,RO,HOD
    }elseif($buddy_details['report_to'] == $_SESSION['userid']){
        $loginuser_type = "RO"; //EMP,RO,HOD
        if($pepah_res['current_status'] != ST_APPROVED1){
            $updatebtndisable = 'style="pointer-events: none; opacity: 0.6;"';
        }
    }elseif($employees['user_id'] == $_SESSION['userid'] || $_SESSION['admin_type'] == '1'){
        $loginuser_type = "EMP"; //EMP,RO,HOD
        if($pepah_res['current_status'] != ST_DRAFT && $pepah_res['current_status'] != ST_ACTIVE && $pepah_res['current_status'] != ST_SEND_BACK && $pepah_res['current_status'] != ST_CALLBACK){
            $updatebtndisable = 'style="pointer-events: none; opacity: 0.6;"';
        }
    }
}else{
     $updatebtndisable = 'style="pointer-events: none; opacity: 0.6;"';
}

?>
<script type="text/javascript">
    function addMoreRow(header_id){
        var hedcnt = $("#cntrows_"+header_id).val();
        var hedcntadd  = hedcnt;
        hedcntadd++
        $("#cntrows_"+header_id).val(hedcntadd);
        var appraisal_id = $("#appraisal_id").val();
        var apraisar_employee_id = $("#apraisar_employee_id").val();
        var apraisar_assessment_year_id = $("#apraisar_assessment_year_id").val();
        var loginuser_type = $("#loginuser_type").val();

        jQuery.ajax({
            url: "views/custom_insert_performance_appraisal_ajax_table.php",
            data:'header_id='+header_id+'&appraisal_id='+appraisal_id+'&employee_id='+apraisar_employee_id+'&assessment_year='+apraisar_assessment_year_id+"&loginuser_type="+loginuser_type+'&hedcnt='+hedcnt,
            type: "POST",
            success:function(htmlRows){
                console.log(htmlRows);
                $('#dinamictable_'+header_id).append(htmlRows);
            },
            error:function (){}
        });
    }
    function appraiserDeleteRow(row_id){
        swal({
    title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
    text: 'Do you want to Remove the Row?',
    type: "warning",
    showCancelButton: true,
    confirmButtonClass: "btn-danger",
    confirmButtonText: "Yes",
    cancelButtonText: "No",
    closeOnConfirm: true,
    closeOnCancel: true
},
function(isConfirm) {
    if (isConfirm) {
        $("#deleterow_"+row_id).remove();
    }
});

    }

    //kra kpi
    function getAbsoluteRatting(id,kra_id,type,u_type){
        var weightage = $("#weightage_"+id).val();
        var self_target = $("#self_target_"+id).val();
        var self_achieved = $("#self_achieved_"+id).val();
        var kra_scor_slabs = $("#kra_scor_slabs").val();
        var scor_slabs = JSON.parse(kra_scor_slabs);
        var total = parseFloat(self_achieved/self_target*100);
        var score = '';
        scor_slabs.forEach(function(slabs) {
            if(parseFloat(slabs.start_range) <= total && parseFloat(slabs.end_range) >= total){
                    score = slabs.score;
                }
            });
            $("#self_scored_"+id).val(score);  //total score
            var kraweightage = $("#kraweightage_"+kra_id).val();
            console.log("kraweightage--"+kraweightage);
            var kraabsoluterating = 0;
            $(".absoluterating_"+kra_id).each(function( index ) {
                var absulutescore = $(this).val()!=""?$(this).val():0;
                kraabsoluterating += parseFloat(absulutescore);
                console.log(+kraweightage+" --- "+$(this).val());
            });
            $("#kraabsoluterating_"+kra_id).val(kraweightage*kraabsoluterating/100); //kra weightage * employeee total score / 100
            gettotal(id,kra_id,type,u_type); // total show
        }
        function getAbsoluteRattingManager(id,kra_id,type,u_type){
            var weightage = $("#weightage_"+id).val();
            var self_target = $("#self_target_"+id).val();
            var self_achieved = $("#RO_achieved_"+id).val();
            var kra_scor_slabs = $("#kra_scor_slabs").val();
            var scor_slabs = JSON.parse(kra_scor_slabs);
            var total = parseFloat(self_achieved/self_target*100);
            var score = '';
            scor_slabs.forEach(function(slabs) {
                if(parseFloat(slabs.start_range) <= total && parseFloat(slabs.end_range) >= total){
                score = slabs.score;
            }
        });
        $("#RO_scored_"+id).val(score);  //total score
        var kraweightage = $("#kraweightage_"+kra_id).val();
        var kraabsoluterating = 0;
        $(".RO_absoluterating_"+kra_id).each(function( index ) {
            var absulutescore = $(this).val()!=""?$(this).val():0;
            kraabsoluterating += parseFloat(absulutescore);
            console.log(+kraabsoluterating+" --- "+$(this).val());
        });
        $("#managerkraabsoluterating_"+kra_id).val(kraweightage*kraabsoluterating/100); //kra weightage * employeee total score / 100
        gettotalManager(id,kra_id,type,u_type); // total show
    }
    function gettotalManager(id,kra_id,type,u_type){
        var kraabsoluterating1total = 0;
        $(".managerkraabsoluterating_"+type).each(function(index) {
            var kraabsoluterating1 = $(this).val()!=""?$(this).val():0;
            kraabsoluterating1total += parseFloat(kraabsoluterating1);
        });
        $("#managerkratotal_"+type).val(kraabsoluterating1total.toFixed(2));
        /*achived_emp employeed*/
        var achived_emp = 0;
        $(".achived_RO_"+type).each(function(index) {
            var getachived_emp = $(this).val()!=""?$(this).val():0;
            achived_emp += parseFloat(getachived_emp);
        });
        $("#RO_achive_total_"+type).val(achived_emp.toFixed(2));
        /*achived_emp employeed*/
        var sum_absoluteratin = 0;
        $(".RO_sumabsoluterating_"+type).each(function(index) {
            var absoluteratin = $(this).val()!=""?$(this).val():0;
            sum_absoluteratin += parseFloat(absoluteratin);
        });
        $("#RO_absoluteratin_total_"+type).val(sum_absoluteratin.toFixed(2));
        totalPercentage();
    }

    function gettotal(id,kra_id,type,u_type){
        var kraabsoluterating1total = 0;
        $(".kraabsoluterating_"+type).each(function(index) {
            var kraabsoluterating1 = $(this).val()!=""?$(this).val():0;
            kraabsoluterating1total += parseFloat(kraabsoluterating1);
        });
        $("#kratotal_"+type).val(kraabsoluterating1total.toFixed(2));
        console.log(kraabsoluterating1total);

        /*achived_emp employeed*/
        var achived_emp = 0;
        $(".achived_emp_"+type).each(function(index) {
            var getachived_emp = $(this).val()!=""?$(this).val():0;
            achived_emp += parseFloat(getachived_emp);
        });
        $("#emp_achive_total_"+type).val(achived_emp.toFixed(2));

        /*achived_emp employeed*/
        var sum_absoluteratin = 0;
        $(".sumabsoluterating_"+type).each(function(index) {
            var absoluteratin = $(this).val()!=""?$(this).val():0;
            sum_absoluteratin += parseFloat(absoluteratin);
        });
        $("#emp_absoluteratin_total_"+type).val(sum_absoluteratin.toFixed(2));
        totalPercentage();
    }

    function totalPercentage(){


        /* employee total */
        var totaltarget= $("#total_kpi_target").val()!=""?$("#total_kpi_target").val():0;
        var totalachive_emp1= $("#emp_achive_total_1").val()!=""?$("#emp_achive_total_1").val():0;
        var totalachive_emp2= $("#emp_achive_total_2").val()!=""?$("#emp_achive_total_2").val():0;
        var addtotalemp = (parseFloat(totalachive_emp1)+parseFloat(totalachive_emp2));
        var total  = addtotalemp*parseFloat(100/totaltarget);

        $("#finalkra_total_employee").val(Math.round(total)+"%");
        $("#text_finalkra_total_employee").val(Math.round(total));
        var totalachive_mang1= $("#RO_achive_total_1").val()!=""?$("#RO_achive_total_1").val():0;
        var totalachive_mang2= $("#RO_achive_total_2").val()!=""?$("#RO_achive_total_2").val():0;
        var addtotalmag = (parseFloat(totalachive_mang1)+parseFloat(totalachive_mang2));
        var total2  = addtotalmag*(100/totaltarget);
        $("#finalkra_total_manager").val(Math.round(total2)+"%");
        $("#text_finalkra_total_manager").val(Math.round(total2));
        /* manager total */
    }

    function startWorkflow(rid,transaction_table,enrid){
  Swal.fire({
  title: '<?php echo $_SESSION['error_message']['1011'];?>',
  showDenyButton: true,
  showCancelButton: false,
  confirmButtonText: 'Yes',
  denyButtonText: `No`,
}).then((result) => {
  if (result.isConfirmed) {
     jQuery.ajax({
          url: "ajax.php",
          data:'page=startworkflow&transaction_table='+transaction_table+'&rid='+rid,
          type: "POST",
          success:function(data){
              console.log(data);
            var data1 = JSON.parse(data);
           console.log(data1.error);
            if(data1.error == "404"){
                Swal.fire({
                  icon: 'error',
                  title: 'Oops...',
                  text: "<?php echo $_SESSION['error_message'][1012];?>"
                })
            }else{
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001'];?>','');
                location.reload();
          }
          }
      });
  }
})

}

  function statuspopupnew(id,table_name,status,transaction_id,workflow_id,type){
      jQuery.ajax({
            url: "views/custom_insert_status_popup_new.php",
            data:'table='+table_name+'&status='+status+'&rid='+id+'&transaction_id='+transaction_id+'&workflow_id='+workflow_id+"&type="+type,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal("show");
            },
            error:function (){}
        });
   }

    function submitDynamicWorkflowPopUp(table_name,transaction_id,current_status,next_status){
    jQuery.ajax({
        url: "views/custom_insert_dynamic_status_popup.php",
        data:'table_name='+table_name+'&current_status='+current_status+'&transaction_id='+transaction_id+'&next_status='+next_status,
        type: "POST",
        success:function(data){
            $('#popup1').html(data);
            $('#kt_modal_KTDatatable_remote').modal("show");
        },
        error:function (){}
    });
}

function submitDynamicWorkflow(table_name,transaction_id,current_status,next_status,old_status_name,new_status_name){
    swal({
            title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
            text: "Please note the status of the document will change to "+new_status_name,
            type: "info",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            closeOnConfirm: false
        },
        function(){
            console.log("dataa aa");
            jQuery.ajax({
                url: "ajax.php",
                data:'page=submitstatusdynamicworkflow&table_name='+table_name+'&current_status='+current_status+'&transaction_id='+transaction_id+'&next_status='+next_status,
                type: "POST",
                success:function(response){
                    console.log(response);
                    var res = JSON.parse(response);
                    window.location.reload();
                    if(res.transaction_update.status=='sucess'){
                         window.location.reload();
                        swal({
                            title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                            text: "<?php echo $_SESSION['error_message']['9059']; ?>",
                            type: "success",
                            showCancelButton: false,
                            closeOnConfirm: false,
                            showLoaderOnConfirm: true
                        }, function () {
                            window.location.reload();
                        });
                    }else{
                        swal({
                            title: "Oops...",
                            text: res.transaction_update.message,
                            type: "error",
                            showCancelButton: true,
                            showConfirmButton: false,
                            cancelButtonText: "Ok",
                            confirmButtonClass: "btn-danger",
                            closeOnCancel: false
                        });
                    }
                },
                error:function (){}
            });
        });
}
</script>
<?php
          if($buddy_details['hr_connect']=="0" || $buddy_details['report_to']=="0"){ ?>
             <div class="card-body">
                 <div class="alert alert-danger background-info alert-dismissible">
                    <strong><?php echo "HR connect is not configured.  <strong>Workflow need to be started!</strong>"; ?></strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                    </button>
                </div>
            </div>
         <?php } ?>

<div class="card card-custom card-sticky" id="kt_page_sticky_card">
<?php $table = 'employee_performance_appraisal_headers';
 if($pepah_res['id'] !=""){
    $workflowsteps = getWorkflowStatus('employee_performance_appraisal_headers',$pepah_res['id'],$pepah_res['current_status']);
    echo $workflowsteps['current_status_ribbon'];
} ?>

    <div class="card-header">
        <h5>Performance Appraisal</h5>
        <div class="card-header-right table-card-header m-r-50">

<?php
if($pepah_res['current_status']!=ST_DRAFT){
    echo $workflowsteps['workflow_btn'];
}
            ?>
            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                <i class="icofont icofont-reply"></i>
                Back
            </a>
        </div>
    </div>
    <div class="card_body"  >
        <div class="">
            <?php echo $workflowsteps['breadcrumb']; ?>
        </div>
        <form method="post">
            <input type="hidden" name="current_status" value="<?php echo $pepah_res['current_status']==ST_DRAFT?ST_ACTIVE:$pepah_res['current_status']; ?>">
            <div class="row">
                <div class="col-lg-4"></div>
                <div class="col-lg-2">
                    <div class="form-group"  >
                        <input type="hidden" class="form-control" name="hr_connect"  value="<?php echo $buddy_details['hr_connect']; ?>">
                        <input type="hidden" class="form-control" name="report_to"   value="<?php echo $buddy_details['report_to']; ?>">
                        <span class="orm-label">Appraisal Name</span>
                        <input type="hidden" class="form-control" name="appraisal_id"  id="appraisal_id" value="<?php echo $appraisal_id ?>">
                        <input type="hidden" class="form-control" name="epa_header_id"  id="epa_header_id" value="<?php echo $epa_header_id ?>">
                        <input aria-label="Input field" type="text"  class="form-control"  value="<?php echo $performance_setup['name']; ?>" readonly>

                        <input type="hidden" class="form-control" value="<?php echo $employee_id; ?>" id="apraisar_employee_id" name="employee_id" readonly="">
                        <input type="hidden" class="form-control" value="<?php echo $loginuser_type; ?>" id="loginuser_type" readonly="">
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="form-group"  >
                        <span class="orm-label">Aassessment Year</span>
                        <input aria-label="Assessment Year" type="text" class="form-control"  name="assessment_year" id="apraisar_assessment_year_id" value="<?php echo $assessment_year ?>" readonly>

                    </div>
                </div>
                <div class="col-lg-4"></div>
            </div>
            <input type="hidden" id="kra_scor_slabs" value='<?php echo json_encode($arslabs); ?>'>
            <input type="hidden" id="total_kpi_target" value='<?php echo $total_kpi_target; ?>'>
            <div class="card-body">

                <div class="accordion-block color-accordion-block">
                    <div class="color-accordion" id="color-accordion">

                    <a class="accordion-msg b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2">Professional Excellence</a>
                    <div class="accordion-desc">
                        <?php include_once "custom_insert_performance_appraisal_basic_Informations.php"; ?>
                    </div>
                    <?php if(in_array(KRA_KPI_HEADERID,(array)$pasharray)){ ?>
                    <?php if(count((array)$kra_list['1']) !=0){?>
                        <a class="accordion-msg b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2">Professional Excellence</a>
                        <div class="accordion-desc">
                            <?php include_once "custom_insert_performance_appraisal_kra_and_kpi_first.php"; ?>
                        </div>
                    <?php } ?>
                    <?php if(count((array)$kra_list['1']) !=0){?>
                        <a class="accordion-msg b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2">Personal Excellence</a>
                        <div class="accordion-desc">
                            <?php include_once "custom_insert_performance_appraisal_kra_and_kpi_second.php"; ?>
                        </div>
                    <?php } ?>
                    <?php } ?>
                    <?php foreach($paheaders as $paheaders){
                            $header_display_setting = json_decode($paheaders['header_display_setting'],1);
                            $sub_header_name = json_decode($paheaders['sub_header_name'],1);
                            $paheadersrow = $paheaders['id'];
                            if(isset($epappraisal[$paheaders['id']])){
                                $paheaders['sub_header_default_row'] = count((array)$epappraisal[$paheaders['id']]);
                            }
                            if($header_display_setting[$loginuser_type]== "Y"){ ?>
                                <a class="accordion-msg b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2"><?php echo $paheaders['header_name']; ?>
                                <p class="f-w-400"><?php echo $paheaders['header_comment']; ?></p>
                            </a>
                            <div class="accordion-desc">
                                <?php if($paheaders['dynamic_page']=='0'){   // custom page call
                                    include_once $paheaders['page_name'].".php";
                                }else{  //dyanamic table generated
                                    ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="dinamictable_<?php echo $paheaders['id']; ?>">
                                            <thead>
                                                <tr>
                                                    <?php foreach($sub_header_name as $sub_headerres){ ?>
                                                        <th scope="col"><?php echo $sub_headerres['field_name']; ?></th>
                                                    <?php } //}
                                                    if($paheaders['sub_header_addmore'] == '0'){ ?>
                                                        <th scope="col">Action</th>
                                                    <?php }  ?>
                                                </tr>
                                            </thead>
                                            <tbody data-repeater-list="">
                                                <?php $hedcnt[$paheadersrow] = 1; for($hed = 0;$hed < $paheaders['sub_header_default_row'];$hed++){ ?>
                                                    <tr id="deleterow_<?php echo $paheaders['id'].$hedcnt[$paheadersrow]; ?>">
                                                        <input type="hidden" class="form-control" name="row_id[<?php echo $paheaders['id']; ?>][]" value="<?php echo $epappraisal[$paheaders['id']][$hed]['id']; ?>">

                                                        <?php $fieldcnt = 1; foreach($sub_header_name as $sub_headerres){
                                                            $fieldname = "filed[".$paheaders['id']."][".$fieldcnt."][]";
                                                            $fieldvalue = $epappraisal[$paheaders['id']][$hed]['filed_'.$fieldcnt];
                                                            ?>
                                                            <td>
                                                                <div class="">
                                                                    <?php if($sub_headerres['type']=="S" && $sub_headerres[$loginuser_type] == "Y"){ ?>
                                                                        <select aria-label="Select option" class="form-control form-select js-example-basic-single" name="<?php echo $fieldname; ?>">
                                                                            <option value="Yes" <?php echo $fieldvalue=="Yes"?"selected":"";?>>Yes</option>
                                                                            <option value="No" <?php echo $fieldvalue=="No"?"selected":"";?>>No</option>
                                                                            <option value="Dont_know" <?php echo $fieldvalue=="Dont_know"?"selected":"";?>>i don't know</option>
                                                                        </select>
                                                                    <?php }else{ ?>
                                                                        <input aria-label="Input field" type="text" class="form-control" name="<?php echo $fieldname; ?>" value="<?php echo $fieldvalue; ?>" <?php echo $sub_headerres[$loginuser_type] == "N"?"readonly":""; ?>>
                                                                    <?php } ?>

                                                                </div>
                                                            </td>
                                                            <?php  $fieldcnt++;
} ?>
                                                            <?php if($paheaders['sub_header_addmore'] == '0'){ //delete button visible 0 or 1 is not ?>
                                                                <td>
                                                                    <button type="button" data-repeater-delete="" class="btn btn-sm font-weight-bolder btn-light-danger btn-primary" onclick="appraiserDeleteRow(<?php echo $paheaders['id'].$hedcnt[$paheadersrow]; ?>)">
                                                                        <i class="fa fa-trash"></i>Delete
                                                                    </button>
                                                                </td>
                                                            <?php } ?>
                                                        </tr>
                                                        <?php $hedcnt[$paheadersrow]++;
} ?>
                                                    </tbody>
                                                </table>
                                                <?php if($paheaders['sub_header_addmore'] == '0'){ //delete button visible 0 or 1 is not ?>
                                                    <input type="hidden" id="cntrows_<?php echo $paheaders['id']; ?>" value="<?php echo $hedcnt[$paheadersrow];?>">
                                                    <button type="button" class="btn btn-sm font-weight-bolder btn-light-primary btn-primary" onclick="addMoreRow('<?php echo $paheaders['id']; ?>')">
                                                        <i class="fa fa-plus"></i>Add
                                                    </button>
                                                <?php } ?>
                                            </div>
                                        <?php  } ?>
                                    </div>
                                <?php } ?>
                                <?php } ?>

                        <a class="accordion-msg b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2">Employee Use Only</a>
                        <div class="accordion-desc">
                            <div class="table-responsive" <?php echo $loginuser_type!='EMP'?'style="pointer-events: none; opacity: 0.6;"':''; ?>>
                                <table class="table table-bordered" id="dinamictable_<?php echo $paheaders['id']; ?>">
                                    <thead>
                                        <tr>
                                            <th scope="col" style="width: 60%;">Question</th>
                                            <th scope="col" style="width: 15%;">Response</th>
                                            <th scope="col" style="width: 25%;">Note</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $value_id = 0;
                                        foreach($emp_questions as $emp_que) {
                                            $answer = $emprohodres[$emp_que['id']];

                                            $value_id++;
                                            $optionlist = json_decode($emp_que['question_option'],1);
                                            ?>
                                            <tr>
                                                <td >
                                                    <input type="hidden" name="response[0][<?php echo $emp_que['id']; ?>]" value="<?php echo $answer['id']; ?>">
                                                    <label class="col-12 font-weight-bold" for=""><?php echo $emp_que['assessment_question'];?></label>
                                                </td>
                                                <td >
                                                    <div>
                                                        <?php if($emp_que['options_display_type']== $typeofquestion[1] ) {
                                                            $response_Responsewered_option = json_decode($employee_responses[$emp_que['id']]['response_option'],1)[1];
                                                            ?>
                                                            <select aria-label="Select option" name="response_option[0][<?php echo $emp_que['id']; ?>]" class="form-control form-select js-example-basic-single " >
                                                                <?php foreach($optionlist as $opkey=>$opts) { $optiondata = $opts; ?>
                                                                <option value="<?php echo $optiondata; ?>" <?php if($opts==$answer['response_option']){ echo "selected";
} ?> ><?php echo $opts;?></option>
                                                            <?php } ?>
                                                        </select>

                                                    <?php } elseif($emp_que['options_display_type']== $typeofquestion[2] ) {
                                                        $response_Responsewered_option = json_decode($employee_responses[$emp_que['id']]['response_option'],1)[0];
                                                        ?>
                                                        <textarea aria-label="Text area" name="response_option[0][<?php echo $emp_que['id']; ?>]" class="form-control col-md-12"> <?php echo $answer['response_option']; ?></textarea>
                                                    <?php } ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if($emp_que['display_remark'] == "Y"){ ?>
                                                <textarea aria-label="Text area" name="response_text[0][<?php echo $emp_que['id']; ?>]" class="form-control col-md-12"><?php echo $answer['response_remark']; ?></textarea>
                                                <?php } ?>
                                            </td>
                                        </tr>

                                    <?php } ?>
                                </tbody></table>
                            </div>
                        </div>
                        <a class="accordion-msg b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2">RO Use Only</a>
                        <div class="accordion-desc">
                            <div class="table-responsive"  <?php echo $loginuser_type!='RO'?'style="pointer-events: none; opacity: 0.6;"':''; ?>>
                                <table class="table table-bordered" id="dinamictable_<?php echo $paheaders['id']; ?>">
                                    <tbody>
                                        <thead>
                                        <tr>
                                            <th scope="col" style="width: 60%;">Question</th>
                                            <th scope="col" style="width: 15%;">Response</th>
                                            <th scope="col" style="width: 25%;">Note</th>
                                        </tr>
                                    </thead>

                                        <?php
                                        $value_id = 0;
                                        foreach($mgr_questions as $mgr_que) {
                                            $answer = $emprohodres[$mgr_que['id']];

                                            $value_id++;
                                            $optionlist = json_decode($mgr_que['question_option'],1);

                                            ?>
                                            <tr>
                                                <td >
                                                    <input type="hidden" name="response[1][<?php echo $mgr_que['id']; ?>]" value="<?php echo $answer['id']; ?>">
                                                    <label class="col-12 font-weight-bold" for=""><?php echo $mgr_que['assessment_question'];?></label>
                                                    <?php if($mgr_que['options_display_type']== $typeofquestion[1] ) {
                                                        $response_Responsewered_option = json_decode($manager_responses[$mgrs_que['id']]['response_option'],1)[1];
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <select aria-label="Select option" class="form-control form-select js-example-basic-single" name="response_option[1][<?php echo $mgr_que['id']; ?>]" >
                                                            <?php foreach($optionlist as $opkey=>$opts) { $optiondata = $opts; ?>
                                                            <option value="<?php echo $optiondata; ?>" <?php if($opts==$answer['response_option']){ echo "selected";
} ?> ><?php echo $opts;?></option>
                                                        <?php } ?>
                                                    </select>
                                                <?php } elseif($mgr_que['options_display_type']== $typeofquestion[2] ) {
                                                    $response_Responsewered_option = json_decode($manager_responses[$mgrs_que['id']]['response_option'],1)[0];
                                                    ?>
                                                    <textarea aria-label="Text area" name="response_option[1][<?php echo $mgr_que['id']; ?>]" class="form-control col-md-12"> <?php echo $answer['response_option']; ?>  </textarea>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <?php if($emp_que['display_remark'] == "Y"){ ?>
                                                <textarea aria-label="Text area" name="response_text[1][<?php echo $mgr_que['id']; ?>]" class="form-control col-md-12"><?php echo $answer['response_remark']; ?></textarea>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody></table></div>

                            </div>

                            <a class="accordion-msg b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2">HOD Use Only</a>
                            <div class="accordion-desc">
                                <div class="table-responsive"  <?php echo $loginuser_type!='HOD'?'style="pointer-events: none; opacity: 0.6;"':''; ?>>
                                    <table class="table table-bordered" id="dinamictable_<?php echo $paheaders['id']; ?>">
                                        <thead>
                                        <tr>
                                            <th scope="col" style="width: 60%;">Question</th>
                                            <th scope="col" style="width: 15%;">Response</th>
                                            <th scope="col" style="width: 25%;">Note</th>

                                        </tr>
                                    </thead>
                                        <tbody>
                                            <?php

                                            $value_id = 0;
                                            foreach($reviewmgr_questions as $emp_que) {
                                                $answer = $emprohodres[$emp_que['id']];
                                                $value_id++;
                                                $optionlist = json_decode($emp_que['question_option'],1);
                                                ?>
                                                <tr>
                                                    <td>
                                                        <input type="hidden" name="response[2][<?php echo $emp_que['id']; ?>]" value="<?php echo $answer['id']; ?>">
                                                        <label class="col-12 font-weight-bold" for=""><?php echo $emp_que['assessment_question'];?></label>

                                                    </td>
                                                    <td>
                                                        <?php if($emp_que['options_display_type']== $typeofquestion[1] ) {
                                                            $response_Responsewered_option = json_decode($reviewer_responses[$emp_que['id']]['response_option'],1)[1];
                                                            ?>
                                                            <select aria-label="Select option" name="response_option[2][<?php echo $emp_que['id']; ?>]" class="form-control form-select js-example-basic-single" id="exampleSelect1">
                                                                <?php foreach($optionlist as $opkey=>$opts) { $optiondata = $opts; ?>
                                                                <option value="<?php echo $optiondata; ?>" <?php if($opts==$answer['response_option']){ echo "selected";
} ?> ><?php echo $opts;?></option>
                                                            <?php } ?>
                                                        </select>
                                                    <?php } elseif($emp_que['options_display_type']== $typeofquestion[2] ) {
                                                        $response_Responsewered_option = json_decode($reviewer_responses[$emp_que['id']]['response_option'],1)[0];
                                                        ?>
                                                        <textarea aria-label="Text area"  name="response_option[2][<?php echo $emp_que['id']; ?>]" class="form-control col-md-12"> <?php echo $answer['response_option']; ?></textarea>
                                                    <?php } ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if($emp_que['text_box_show'] == "Y"){ ?>
                                                <textarea aria-label="Text area"  name="response_text[2][<?php echo $emp_que['id']; ?>]" class="form-control col-md-12"><?php echo $answer['response_remark']; ?></textarea>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>

                                </tbody></table>
                            </div>
                        </div>

                            </div>
                        </div>
                        <button type="submit" name="performanace_appraiser_sbt" class="btn btn-primary" <?php echo $disablebtn; ?>>Submit</button>
                    </div>
                </form>
                            <div class="row">
    <?php
echo $todisplaythree = todisplaythree(array('table'=>$table,'originator'=>$employees['user_id'],'encode'=>'no','id'=>$pepah_res['id'],'dis_query'=>'yes','dis_timeline'=>'yes','dis_comments'=>'yes'));
?>

</div>
            </div>
        </div>


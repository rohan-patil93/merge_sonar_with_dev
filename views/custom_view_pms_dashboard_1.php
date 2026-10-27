<?php 
$_SESSION['apply_js']['multiselest']= 1;
$url1 = API."?mode=pmsdashboard1&appraisal_id=".$appraisal_id."&assessment_year=".$assessment_year; 
$resultapi = callAPI($url1);
$result = json_decode($resultapi,1);
//echo "<pre>"; print_r($result['performance_setup_id']); echo "</pre>";
$employees = $result['employees'];
$performance_setup = $result['performance_setup'];
$appraisalemployees = $result['employee_performance_appraisal_headers'];
$arslabs = $result['arslabs'];
// foreach ($appraisalemployees as $appraisalemploye){
//     if($appraisalemploye['current_status'] == ST_APPROVAL_PENDING){
//         $appraisalemp['hrpendning']++;
//     }elseif($appraisalemploye['current_status'] == ST_APPROVED){
//         $appraisalemp['approved']++; 
//     }else{
//         $appraisalemp['emp_pending']++; 
//     }
// }
?>
<script type="text/javascript">
    function sendAppraisalId(appraisal_id){
        var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;"></div>';
        $("#ajaxhtml").html(loader);
        jQuery.ajax({
            url: "views/custom_view_pms_dashboard_1_ajax.php",
            data:'appraisal_id='+appraisal_id,
            type: "POST",
            success:function(data){
                console.log(data);
                $("#ajaxhtml").html(data);
            },
            error:function (){}
        });
    }
</script>
<div class="row mb-4">
    <div class="col-lg-8"></div>
    <div class="col-lg-4">
        <label class="form-label">Appraisal</label>
        <select class="form-control js-example-basic-single" onchange="sendAppraisalId(this.value)">
            <option value="0">Select Any One</option>
            <?php foreach($performance_setup as $performancesetup){ ?>
                <option value="<?php echo $performancesetup['id']; ?>" <?php echo $result['performance_setup_id'] == $performancesetup['id']?"selected":""; ?>>
                    <?php echo $performancesetup['name']." - (".$performancesetup['assessment_year'].")"; ?>
                </option>
            <?php } ?>
        </select>
    </div>
</div>
<div id="ajaxhtml" >
        <?php include("views/custom_view_pms_dashboard_1_ajax.php"); ?>
</div>


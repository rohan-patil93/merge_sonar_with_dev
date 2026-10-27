<?php
$_SESSION['apply_js']['multiselest']= 1;
$_SESSION['apply_js']['pms_dashboard2']=1;
$appraisal_id = 1;
$employee_id = 1;
$url1 = API."?mode=pmsdashboard2&appraisal_id=".$appraisal_id."&employee_id=".$employee_id; 
$resultapi = callAPI($url1);
$result = json_decode($resultapi,1);
//echo "<pre>"; print_r($result);echo "</pre>";
$employee_goals = $result['employee_goals'];
$performance_setup = $result['performance_setup'];
?>
<script type="text/javascript">
    function sendAppraisalId(appraisal_id){
        var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;"></div>';
        $("#ajaxhtml").html(loader);
        jQuery.ajax({
            url: "views/custom_view_pms_dashboard_2_ajax.php",
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
<div class="row mb-2">
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
        <?php include("views/custom_view_pms_dashboard_2_ajax.php"); ?>
</div>

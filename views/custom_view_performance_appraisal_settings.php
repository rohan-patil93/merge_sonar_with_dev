
<?php

$_SESSION['apply_js']['multiselest'] = 1;




$apid ="";
if(isset($_GET['Rid']) && $_GET['Rid'] !=""){
$apidd = encodeId($_GET['Rid']);
$apiddd = explode(":",$apidd);
$apid  = $apiddd['1'];
}
$url = API."?mode=getviewperformanceappraisalsettig&assessment_year=".$assessment_year."&appraisal_id=".$apid;
$psresult = callAPI($url);
$psdecod = json_decode($psresult,1);

$performance_setup_res = $psdecod['performance_setup'];
$_GET['AY'] = $performance_setup_res['assessment_year'];
$assessment_year = $_GET['AY'];
$modulesset = $psdecod['check_performance_appraisal_settings'];
foreach ($psdecod['performance_appraisal_settings'] as $valuee) {
    $performance_appraisal_se[$valuee['id']] = $valuee;
}
?>
<script type="text/javascript">


    //         url: "views/custom_insert_filters.php",
    //         data:'filter_id='+filter_id+"&tablename="+application_table,
    //         type: "GET",
    //         success:function(data){
    //         error:function (){}
    //         url: "ajax.php",
    //         data:'page=getkpalist&selected_kpa='+selected_kpa,
    //         type: "POST",
    //         success:function(data){
    //         error:function (){}

    //         url: "views/custom_kpi_and_kri_popup.php",
    //         data:'page=dis_att_type&id='+kri_id+'&kpi_id='+kpi_id+'&kpi_tasks_ids='+kpi_tasks_ids+'&row='+row,
    //         type: "POST",
    //         success:function(data){
    //         error:function (){}


function publichappraisal(id){
    $("#loader-hide").show();
    swal({
        title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
        text: '<?php echo $_SESSION['error_message']['9034']; ?>',
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
            $("#loader-show").show();
              var assessment_year     = '<?php echo $assessment_year; ?>';
            jQuery.ajax({
                url: "ajax.php",
                 data:'page=publichappraisal&id='+id+'&assessment_year='+assessment_year,
                type: "POST",
                success:function(data){
                    console.log(data)
                      $("#loader-show").hide();
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3000']; ?>','');
                    location.reload();
                }
            });
        }else{
            $("#loader-show").hide();
        }
    });
// Swal.fire({
//   showDenyButton: true,
//   showCancelButton: false,
//   confirmButtonText: 'Yes',
//   denyButtonText: `No`,
//             url: "ajax.php",
//              data:'page=publichappraisal&id='+id+_2'&assessment_year='+assessment_year,
//             type: "POST",
//             success:function(data){


}

</script>
<div class="d-flex flex-column-fluid ">
    <div class="container-fluid">
        <div class="row">  <!-- j-content -->
            <div class="col-lg-12">
                <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                    <div class="card-header">
                        <h5>Performance Appraisal Setting</h5>
                        <div class="card-header-right">
                            <?php if($apid !="" && $performance_setup_res['publish']=='N'){ ?>
                                <button type="button"   class="btn btn-primary" onclick="publichappraisal('<?php echo $apid; ?>')">Publish</button>
                            <?php } ?>
                            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                                <i class="icofont icofont-reply"></i>
                                Back
                            </a>
                        </div>
                    </div>
                    <div class="text-center" style="display: none;  margin-left: auto;  margin-right: auto;  width: 40%; " id="loader-show">
                            <img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview">
                     </div>
                    <div class="card-body" id="loader-hide">
                        <div class="">
                            <div class="col-lg-12 col-xl-12">
                                <ul class="nav md-tabs infisuite_tab">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#appraisal_setting" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> Appraisal Setting</a>
                                        <div class="slide"></div>
                                    </li>

                                <?php if($_GET['Rid'] !=""){ ?>
                                    <li class="nav-item">
                                        <a class="nav-link " data-bs-toggle="tab" href="#modul_setting" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Modules </a>
                                        <div class="slide"></div>
                                    </li>
                                    <?php foreach($performance_appraisal_se as $kyid=>$performance_appraiseting){?>
                                    <li class="nav-item" id ="menu_show_<?php echo $kyid; ?>" <?php if(!in_array($kyid,(array)$modulesset)){ echo "style='display:none'";
} ?>>
                                        <a class="nav-link " data-bs-toggle="tab" href="#dynamicmenu_<?php echo $kyid; ?>" role="tab"><?php echo $performance_appraiseting['performance_area']; ?> </a>
                                        <div class="slide"></div>
                                    </li>
                                    <?php  }  ?>


                                    <li class="nav-item">
                                        <a class="nav-link " data-bs-toggle="tab" href="#ques_for_emp" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Quest  EMP</a>
                                        <div class="slide"></div>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link " data-bs-toggle="tab" href="#ques_for_ro" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Quest RO</a>
                                        <div class="slide"></div>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link " data-bs-toggle="tab" href="#ques_for_hod" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Quest HOD</a>
                                        <div class="slide"></div>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link " data-bs-toggle="tab" href="#performanceappraisalheaders" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Appraisal Headers</a>
                                        <div class="slide"></div>
                                    </li>
                                    <?php
} ?>
                                </ul>
                                <!-- Tab panes -->
                                <div class="tab-content card-block" <?php echo $performance_setup_res['publish']=='Y'?'style="pointer-events: none; opacity: 0.6;"':''; ?>>

                                    <div class="tab-pane active" id="appraisal_setting" role="tabpanel">
                                        <?php include_once 'views/custom_insert_performance.php'; ?>
                                    </div>
                                    <div class="tab-pane " id="modul_setting" role="tabpanel">
                                        <?php include_once 'views/custom_insert_performance_appraisal_settings.php'; ?>
                                    </div>
                                    <?php foreach($performance_appraisal_se as $kyid=>$performance_appraiseting){ //echo $performance_appraiseting['url'];?>
                                    <div class="tab-pane " id="dynamicmenu_<?php echo $kyid; ?>" role="tabpanel">
                                        <?php include_once 'views/'.$performance_appraiseting['url'].'.php'; ?>
                                    </div>
                                <?php } ?>

                                    <div class="tab-pane " id="ques_for_emp" role="tabpanel">
                                        <?php include_once 'views/custom_insert_performance_emp.php'; ?>
                                    </div>
                                    <div class="tab-pane " id="ques_for_ro" role="tabpanel">
                                        <?php include_once 'views/custom_insert_performance_manager.php'; ?>
                                    </div>
                                    <div class="tab-pane " id="ques_for_hod" role="tabpanel">
                                        <?php include_once 'views/custom_insert_performance_manager_to.php'; ?>
                                    </div>
                                    <div class="tab-pane " id="performanceappraisalheaders" role="tabpanel">
                                        <?php include_once 'views/custom_insert_performance_appraisal_headers.php'; ?>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

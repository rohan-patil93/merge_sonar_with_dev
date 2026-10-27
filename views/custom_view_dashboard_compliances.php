<?php
    $listcompliancesurl = API."?mode=getcompliancesdashbord&type=dashboard";
    $listcompliancesres = callAPI($listcompliancesurl);
    $listcompliances = json_decode($listcompliancesres, 1);
    $compliancelist = $listcompliances['compliances'];
    $compliancetaskhistory = $listcompliances['compliance_task_histories'];

?>
<script>
function getDetailsEmployeeTask(id,date){
    jQuery.ajax({
        url: "views/custom_list_dashboard_compliances_popup.php",
        data:'id='+id+'&date='+date,
        type: "POST",
        success:function(data){
            $('#dynamic_popup1').html(data);
            $('#kt_modal_dynamic_popup').modal("show");
        },
        error:function (){}
    });
}
</script>
<div class="pcoded-inner-content">
    <div class="page-wrapper">
        <div class="page-body border-bottom">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-header-text">Compliance Dashboard</h5>
                    <div class="card-header-right">
                            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                                <i class="icofont icofont-reply"></i>
                                Back
                            </a>
                        </div>
                </div>
    <!-- Page-body start -->
                <div class="page-body">
                    <div class="row mt-4">
                        <div class="col-lg-5">
                        </div>
                        <div class="col-lg-7 ">

                            <div class="d-inline-block m-l-10">
                                <button class="btn waves-effect waves-light btn-success p-2"></button>
                                <span>Completed</span>
                            </div>
                            <div class="d-inline-block m-l-10">
                                <button class="btn waves-effect waves-light btn-info p-2"></button>
                                <span>Completed with Delay</span>
                            </div>
                            <div class="d-inline-block m-l-10">
                                <button class="btn waves-effect waves-light btn-warning p-2"></button>
                                <span>Review Pending</span>
                            </div>
                            <div class="d-inline-block m-l-10">
                                <button class="btn waves-effect waves-light btn-danger p-2"></button>
                                <span>Incompleted</span>
                            </div>
                        </div>
                    </div>
                    <div class="card mt-3">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card-block accordion-block color-accordion-block">
                                    <div class="color-accordion" id="color-accordion">
                                        <a class="accordion-msg b-none waves-effect waves-light bg-gray mb-2 f-w-600 f-16">Daily Compliance</a>
                                        <div class="accordion-desc">
                                        <?php foreach($compliancelist['DY'] as $complianlist){ ?>
                                            <div class="row">
                                                <div class="col-lg-3 border p-3">
                                                    <h3 class="f-16 m-0"><?php echo $complianlist['task_name']; ?></h3>
                                                </div>
                                                <div class="col-lg-9 border p-3">
                                                    <ul class="compliance-dashboard pb-2">
                                                        <?php foreach($compliancetaskhistory[$complianlist['id']]  as $compliancetaskhistry){
                                                            $taskbgcolor = "btn-danger";
                                                            if(strtotime($compliancetaskhistry['task_date']) < strtotime($compliancetaskhistry['completion_date']) && $compliancetaskhistry['completion_date'] != ""){
                                                                $taskbgcolor = "btn-info";
                                                            }elseif(strtotime($compliancetaskhistry['task_date']) >= strtotime($compliancetaskhistry['completion_date']) && $compliancetaskhistry['completion_date'] != ""){
                                                                $taskbgcolor = "btn-success";
                                                            }
                                                        ?>
                                                        <li class="text-center">
                                                            <button type="button" class="btn p-0 border-0 bg-transparent text-reset cursor-pointer w-100" onclick="getDetailsEmployeeTask('<?php echo $complianlist['id']; ?>','<?php echo $compliancetaskhistry['task_date']; ?>')">
                                                                <h5 class="f-w-600 f-14 text-dark"><?php echo $compliancetaskhistry['task_date'];?></h5>
                                                                <span class="<?php echo $taskbgcolor; ?> w-100 p-2 d-block mt-2"></span>
                                                            </button>
                                                        </li>
                                                    <?php } ?>

                                                    </ul>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        </div>
                                        <a class="accordion-msg bg-dark-primary b-none waves-effect waves-light bg-gray mb-2 f-w-600 f-16">Weekly Compliance</a>
                                        <div class="accordion-desc">
                                            <?php foreach($compliancelist['WK'] as $complianlist){ ?>
                                            <div class="row">
                                                <div class="col-lg-3 border p-3">
                                                    <h3 class="f-16 m-0"><?php echo $complianlist['task_name']; ?></h3>
                                                </div>
                                                <div class="col-lg-9 border p-3">
                                                    <ul class="compliance-dashboard pb-2">
                                                        <?php foreach($compliancetaskhistory[$complianlist['id']]  as $compliancetaskhistry){
                                                            $taskbgcolor = "btn-danger";
                                                            if(strtotime($compliancetaskhistry['task_date']) < strtotime($compliancetaskhistry['completion_date']) && $compliancetaskhistry['completion_date'] != ""){
                                                                $taskbgcolor = "btn-info";
                                                            }elseif(strtotime($compliancetaskhistry['task_date']) >= strtotime($compliancetaskhistry['completion_date']) && $compliancetaskhistry['completion_date'] != ""){
                                                                $taskbgcolor = "btn-success";
                                                            }
                                                        ?>
                                                        <li class="text-center">
                                                            <button type="button" class="btn p-0 border-0 bg-transparent text-reset cursor-pointer w-100" onclick="getDetailsEmployeeTask('<?php echo $complianlist['id']; ?>','<?php echo $compliancetaskhistry['task_date']; ?>')">
                                                                <h5 class="f-w-600 f-14 text-dark"><?php echo $compliancetaskhistry['task_date'];?></h5>
                                                                <span class="<?php echo $taskbgcolor; ?> w-100 p-2 d-block mt-2"></span>
                                                            </button>
                                                        </li>
                                                    <?php } ?>

                                                    </ul>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        </div>
                                        <a class="accordion-msg bg-darkest-primary b-none waves-effect waves-light bg-gray mb-2 f-w-600 f-16">Monthly</a>
                                        <div class="accordion-desc">
                                            <?php foreach($compliancelist['MN'] as $complianlist){ ?>
                                            <div class="row">
                                                <div class="col-lg-3 border p-3">
                                                    <h3 class="f-16 m-0"><?php echo $complianlist['task_name']; ?></h3>
                                                </div>
                                                <div class="col-lg-9 border p-3">
                                                    <ul class="compliance-dashboard pb-2">
                                                        <?php foreach($compliancetaskhistory[$complianlist['id']]  as $compliancetaskhistry){
                                                            $taskbgcolor = "btn-danger";
                                                            if(strtotime($compliancetaskhistry['task_date']) < strtotime($compliancetaskhistry['completion_date']) && $compliancetaskhistry['completion_date'] != ""){
                                                                $taskbgcolor = "btn-info";
                                                            }elseif(strtotime($compliancetaskhistry['task_date']) >= strtotime($compliancetaskhistry['completion_date']) && $compliancetaskhistry['completion_date'] != ""){
                                                                $taskbgcolor = "btn-success";
                                                            }
                                                        ?>
                                                        <li class="text-center">
                                                            <button type="button" class="btn p-0 border-0 bg-transparent text-reset cursor-pointer w-100" onclick="getDetailsEmployeeTask('<?php echo $complianlist['id']; ?>','<?php echo $compliancetaskhistry['task_date']; ?>')">
                                                                <h5 class="f-w-600 f-14 text-dark"><?php echo $compliancetaskhistry['task_date'];?></h5>
                                                                <span class="<?php echo $taskbgcolor; ?> w-100 p-2 d-block mt-2"></span>
                                                            </button>
                                                        </li>
                                                    <?php } ?>

                                                    </ul>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        </div>
                                        <a class="accordion-msg bg-darkest-primary b-none waves-effect waves-light bg-gray mb-2 f-w-600 f-16">Yearly Compliance</a>
                                        <div class="accordion-desc">
                                            <?php foreach($compliancelist['YR'] as $complianlist){ ?>
                                            <div class="row">
                                                <div class="col-lg-3 border p-3">
                                                    <h3 class="f-16 m-0"><?php echo $complianlist['task_name']; ?></h3>
                                                </div>
                                                <div class="col-lg-9 border p-3">
                                                    <ul class="compliance-dashboard pb-2">
                                                        <?php foreach($compliancetaskhistory[$complianlist['id']]  as $compliancetaskhistry){
                                                            $taskbgcolor = "btn-danger";
                                                            if(strtotime($compliancetaskhistry['task_date']) < strtotime($compliancetaskhistry['completion_date']) && $compliancetaskhistry['completion_date'] != ""){
                                                                $taskbgcolor = "btn-info";
                                                            }elseif(strtotime($compliancetaskhistry['task_date']) >= strtotime($compliancetaskhistry['completion_date']) && $compliancetaskhistry['completion_date'] != ""){
                                                                $taskbgcolor = "btn-success";
                                                            }
                                                            ?>
                                                        <li class="text-center">
                                                            <button type="button" class="btn p-0 border-0 bg-transparent text-reset cursor-pointer w-100" onclick="getDetailsEmployeeTask('<?php echo $complianlist['id']; ?>','<?php echo $compliancetaskhistry['task_date']; ?>')">
                                                                <h5 class="f-w-600 f-14 text-dark"><?php echo $compliancetaskhistry['task_date'];?></h5>
                                                                <span class="<?php echo $taskbgcolor; ?> w-100 p-2 d-block mt-2"></span>
                                                            </button>
                                                        </li>
                                                    <?php } ?>

                                                    </ul>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Page-body start -->
            </div>
        </div>
    </div>
</div>

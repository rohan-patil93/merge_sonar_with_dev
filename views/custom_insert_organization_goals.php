<?php

if(isset($_POST['submit_value'])){
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
    $url = API."?mode=insertgoaldata&method=post";
    $response = file_post_contents($url, $_POST, 'insert');
    $response1 = json_decode($response,1);
}

$url = API."?mode=getorganizationgoalsdetails";
$response = callAPI($url);
$kipandkradetails = json_decode($response,1);

$modellist  = $kipandkradetails['modellist'];
$upmllist = $kipandkradetails['uomllist'];
$kralist = $kipandkradetails['kralist'];

$kpilistres = $kipandkradetails['kpilist'];

$table_name = "kpi_tasks"; // pop resun used dynamic popup
?>
<script type="text/javascript">

    function showpopup(){
        var filter_id = document.getElementById('filtertbl_rowid').value;
        var application_table = document.getElementById('application_table').value;
        jQuery.ajax({
            url: "views/custom_insert_filters.php",
            data:'filter_id='+filter_id+"&tablename="+application_table,
            type: "GET",
            success:function(data){
                $('#dynamic_popup1').html(data);
                $('#kt_modal_dynamic_popup').modal("show");
            },
            error:function (){}
        });
    }
    function selectKPA(){
        var selected_kpa = $("#selected_kpa").val();
        $.ajax({
            url: "ajax.php",
            data:'page=getkpalist&selected_kpa='+selected_kpa,
            type: "POST",
            success:function(data){
                console.log(data);
                document.getElementById('newhpirow').innerHTML = data;
                $(".js-example-basic-single").select2();
                $(".js-example-disabled-results").select2();
            },
            error:function (){}
        });

    }
    function taskpopup(kpi_id,kri_id,row){
        var kpi_tasks_ids = $("#kpi_tasks_ids_"+kpi_id+row).val();
        jQuery.ajax({
            url: "views/custom_kpi_and_kri_popup.php",
            data:'page=dis_att_type&id='+kri_id+'&kpi_id='+kpi_id+'&kpi_tasks_ids='+kpi_tasks_ids+'&row='+row,
            type: "POST",
            success:function(data){
                $('#dynamic_popup11').html(data);
                $("#kt_modal_dynamic_popup2").modal("show");
            },
            error:function (){}
        });

    }
</script>
<div class="d-flex flex-column-fluid ">
    <div class="container-fluid">
        <div class="row j-content">  <!-- j-content -->
            <div class="col-lg-12">
                <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                    <div class="card-header">
                        <h5>Organization Goals</h5>
                        <div class="card-header-right">
                            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                                <i class="icofont icofont-reply"></i>
                                Back
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="card">

                            <form method="post" id="formsubmitid">
                                <div class="row">
                                    <div class="form-group col-lg-4">
                                        <span class="form-label">Goal Name <span class="text-danger">*</span></span>
                                        <input aria-label="Goal Name" type="text"  class="form-control" name="goal_name" />
                                    </div>
                                    <div class="form-group col-lg-4">
                                        <span class="form-label">Start Date <span class="text-danger">*</span></span>
                                        <input aria-label="Start Date" type="text"  class="form-control startdatepicker"  name="start_date" />
                                    </div>
                                    <div class="form-group col-lg-4">
                                        <span class="form-label">End Date <span class="text-danger">*</span></span>
                                        <input aria-label="End Date" type="text"  class="form-control enddatepicker" name="end_date" />
                                    </div>

                                </div>
                                <input type="hidden" name="filtertbl_rowid" id="filtertbl_rowid" value="<?php echo $filter_id; ?>">
                                <input type="hidden" name="rid"  value="<?php echo $row_id ; ?>">
                                <input type="hidden" name="application_table" id="application_table"  value="organization_goals">

                                <div class="card-footer">
                                    <div class="col-lg-4">
                                        <input type="hidden" id="table_name" name="table_name" value=<?php echo $table_name; ?> />
                                        <input type="hidden" id="user_id" name="user_id" value=<?php echo $_SESSION['userid']; ?> />
                                        <input type="hidden" id="now_date" name="now_date" value="<?php echo date("Y-m-d H:i:s"); ?>" />
                                        <input type="hidden" name="rid" id="rid" value="<?php echo $rid;?>">

                                        <?php  $disable = "disabled";     if($total == 100) { $disable = "";
} ?>
                                                <input type="hidden" name="submit_value"  value="">

                                                <button type="submit" onclick="submitForm()" class="btn btn-primary mr-2"   ><i class="la la-check"></i>Submit</button>


                                            </div>
                                        </div>
                                    </form>
                                    <div id="newhpirow" style="pointer-events:none; opacity:0.6; "></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

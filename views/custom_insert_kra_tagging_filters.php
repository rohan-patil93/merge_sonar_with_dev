<?php
if(isset($_POST['kra_mapping']) && $_POST['kra_mapping']="insertgoal"){
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
    $_POST['appraisal_id'] =  $performance_appraisal_setup_id;
    $url = API."?mode=insertgoaldata&method=post";
    $response = file_post_contents($url, $_POST, 'insert');
    $response1 = json_decode($response,1);
    ?>
    <script type="text/javascript">
        document.onreadystatechange = function(){
            if(document.readyState == "complete") {
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3000']; ?>','');
            }
        }
    </script>
    <?php
}
$url = API."?mode=getkratagging&assessment_year=".$assessment_year."&appraisal_id=".$performance_appraisal_setup_id;
$response = callAPI($url);
$kipandkradetails = json_decode($response,1);
$departmentlist  = $kipandkradetails['department_list'];
$designationlist = $kipandkradetails['designation_list'];
$locationlist = $kipandkradetails['location_list'];
$kralist  = $kipandkradetails['kralist'];
$table_name = "kpi_tasks";
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
    //         url: "ajax.php",
    //         data:'page=getkpalist&selected_kpa='+selected_kpa,
    //         type: "POST",

    //         url: "views/custom_kpi_and_kri_popup.php",
    //         data:'page=dis_att_type&id='+kri_id+'&kpi_id='+kpi_id+'&kpi_tasks_ids='+kpi_tasks_ids+'&row='+row,
    //         type: "POST",

    function selectKPA(){
        var organization_goalsidd = $("#organization_goalsidd").val();
        var appraisal_id = '<?php echo $performance_appraisal_setup_id; ?>';
        var kralist = [];
        $('.kraselected:checked').each(function(){
            kralist.push($(this).val());
        });
        var selected_kpa = kralist;
        $.ajax({
            url: "ajax.php",
            data:'page=getkpalist&selected_kpa='+selected_kpa+"&organization_goalsidd="+organization_goalsidd+"&appraisal_id="+appraisal_id,
            type: "POST",
            success:function(data){
                console.log(data);
                document.getElementById('newhpirowtagging').innerHTML = data;
                $(".js-example-basic-single").select2();
                $(".js-example-disabled-results").select2();
            },
            error:function (){}
        });

    }
    function checkOrgnizationGoal(vall){
        $("#outputtest").html("");
        var appraisal_id = '<?php echo $performance_appraisal_setup_id; ?>';
        var tagging_location_id = [];
        $(".tagging_location_id :selected").each(function(){
            tagging_location_id.push($(this).val());
        });
        var tagging_department_id = [];
        $(".tagging_department_id :selected").each(function(){
            tagging_department_id.push($(this).val());
        });
        var tagging_designation_id = [];
        $(".tagging_designation_id :selected").each(function(){
            tagging_designation_id.push($(this).val());
        });

        $.ajax({
            url: "ajax.php",
            data:'page=getorganizationgoalcheck&location_id='+tagging_location_id+'&department_id='+tagging_department_id+'&designation_id='+tagging_designation_id+"&appraisal_id="+appraisal_id,
            type: "POST",
            success:function(data){
                console.log(data);
                $("#getandshowlocationdesdep").html(data);
        },
        error:function (){}
    });

        }

        function checkalllocation(){
            if($('.select_all_goalsset').is(':checked')){
                $('.ceckbox_goalsset').each(function(){
                    this.checked = true;
                });
    }else{
        $('.ceckbox_goalsset').each(function(){
            this.checked = false;
        });
    }
}
function setkraandkpifun(){
    var appraisal_id = '<?php echo $performance_appraisal_setup_id; ?>';
    var assessment_year     = '<?php echo $assessment_year; ?>';
    $("#getandshowlocationdesdep").hide('slow');
    $("#div_kraandkpi").show('slow');
    var fomdet = $("#filterformsubmit").serialize();
    $.ajax({
            url: "views/custom_insert_kra_taggings.php",
            data:fomdet+"&appraisal_id="+appraisal_id+"&assessment_year="+assessment_year+"&inserttype=NEW",
            type: "POST",
            success:function(data){
                console.log(data);
                $("#div_kraandkpi").html(data);
        }
    });
}
function editKrAandKip(loc_id,dept_id,desg_id){
    var appraisal_id = '<?php echo $performance_appraisal_setup_id; ?>';
    var assessment_year     = '<?php echo $assessment_year; ?>';
    $("#getandshowlocationdesdep").hide('slow');
    $("#div_kraandkpi").show('slow');
    $.ajax({
            url: "views/custom_insert_kra_taggings.php",
            data: "&appraisal_id="+appraisal_id+"&assessment_year="+assessment_year+"&loc_id="+loc_id+"&dept_id="+dept_id+"&desg_id="+desg_id+"&inserttype=OLD",
            type: "POST",
            success:function(data){
                console.log(data);
                $("#div_kraandkpi").html(data);
        }
    });
}
function setLocationDesignationDep(){
    $("#getandshowlocationdesdep").show('slow');
    $("#div_kraandkpi").hide('slow');
    $("#div_kraandkpi").html("");
}
//         url: "ajax.php",
//         data:'page=updatepormanace&applicable='+value+'&id='+id+'&appraisal_id='+pasappraisal_id,
//         type: "POST",

</script>
<div class="row">  <!-- j-content -->
    <div class="col-lg-12">
        <div class="card card-custom card-sticky" id="kt_page_sticky_card">
            <div class="card-header">
                <h5>KRA Tagging</h5>
                <div class="card-header-right">
                    <?php if($_GET['option'] != "COM_VIEW_PERFORMANCE_APPRAISAL_SETTINGS"){ ?>
                        <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                            <i class="icofont icofont-reply"></i>  Back
                        </a>
                    <?php } ?>
                </div>
            </div>
            <div class="card-body">
                    <div class="row">
                        <div class="col-sm-3 col-xl-3 form-group w-full"  >
                            <span class="form-label d-block">Location</span>
                            <select aria-label="Location []"  class="form-select  js-example-basic-multiple tagging_location_id" name="location_id[]"  multiple>
                                <?php foreach($locationlist  as $locationres){ ?>
                                    <option value='<?php echo $locationres['id']; ?>'><?php echo $locationres['location_name'];?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-sm-3 col-xl-3 form-group w-full"  >
                            <span class="form-label d-block">Department</span>
                            <select aria-label="Department []"  class="form-select  js-example-basic-multiple tagging_department_id" name="department_id[]" multiple>
                                <?php foreach($departmentlist  as $departmentres){ ?>
                                    <option value='<?php echo $departmentres['id']; ?>'><?php echo $departmentres['department_name'];?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-sm-3 col-xl-3 form-group w-full"  >
                            <span class="form-label d-block">Designation</span>
                            <select aria-label="Designation []"  class="form-select  js-example-basic-multiple tagging_designation_id" name="designation_id[]"   multiple>
                                <?php foreach($designationlist  as $designationres){ ?>
                                    <option value='<?php echo $designationres['id']; ?>'><?php echo $designationres['designation_name'];?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <button type="submit" class="btn btn-primary waves-effect waves-light mt-4" onclick="checkOrgnizationGoal()">Search</button>
                        </div>
                        <input type="hidden" class="form-control"  name="appraisal_id" value="<?php echo $performance_appraisal_setup_id ?>" readonly>

                    </div>
                    <form method="post" id="filterformsubmit">
                        <div class="row" id="getandshowlocationdesdep">

                        </div>
                    </form>
                    <!-- custom_insert_kra_taggings -->
                    <div class="row" id="div_kraandkpi">
                    </div>


            </div>
        </div>
    </div>
</div>

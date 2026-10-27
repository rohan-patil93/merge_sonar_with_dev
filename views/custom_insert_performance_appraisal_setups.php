<?php
$_SESSION['apply_js']['multiselest'] = 1;
if(isset($_POST['submitperformance_appraisal_setups'])){
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
    $File_Uploads_Path = "uploads/all/";
    if($_FILES['newfilename']['error'] != "4"){
        $temp = explode(".", $_FILES['newfilename']['name']);
        $Filename=  "file_".time()."_".uniqid();
        $newfilename = $Filename.'.' .end($temp);
        if(move_uploaded_file($_FILES['newfilename']["tmp_name"], $File_Uploads_Path.$newfilename)){
            $_POST['file_name'] = $newfilename;
        }
    }else{
        $_POST['file_name'] = $_POST['oldfilename'];
    }
    $url = API."?mode=insertperformanceappraisalsetups&method=post";
    $response1 = file_post_contents($url, $_POST, 'update');
    $response2 = json_decode($response1,1);
    $response =$response2['insert'];
    if($response['status'] == 'sucess'){
        if($response['LASTID'] == 0){
            $msgcode = 3001; //update
        }else{
            $msgcode = 3000; //insert
            $_SESSION['insert_msg'] = $msgcode;
            $getid = $response["LASTID"];
            $Rrid = deocdeId("id:".$getid);
            header("location:index.php?option=COM_INSERT_PERFORMANCE_APPRAISAL_SETUPS&Rid=".$Rrid); exit;
        }
        ?>
        <script type="text/javascript">
            document.onreadystatechange = function(){
                if(document.readyState == "complete") {
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][$msgcode]; ?>','');
                }
            }
        </script>
        <?php
    }else{
        if(isset($_SESSION['error'][$response['error_no']])){
            $dis_err = explode(",", $response['other']);
            foreach ($dis_err as $va) {
                $dis_er [] = $dis_labels[$va];
            }
            $error_message = $response['error_no'] ." : ".$_SESSION['error'][$response['error_no']]." : ".implode(", ", (array)$dis_er);
        }else{
            $error_message = $response['message'];
        }
    }
}else{
    if($_SESSION['insert_msg'] !=""){
        $msgcode = $_SESSION['insert_msg'];
        $_SESSION['insert_msg'] = "";
        ?>
        <script type="text/javascript">
            document.onreadystatechange = function(){
                if(document.readyState == "complete") {
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][$msgcode]; ?>','');
                }
            }
        </script>
    <?php
    }
}

if(isset($_POST['submitperformance_appraisal_questions'])){
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
    $url = API."?mode=insertperformanceappraisalquestions&method=post";
    $response1 = file_post_contents($url, $_POST, 'update');
    $response2 = json_decode($response1,1);
    $response =$response2['insert'];
    $msgcode = '3001';
        ?>
    <script type="text/javascript">
        document.onreadystatechange = function(){
            if(document.readyState == "complete") {
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][$msgcode]; ?>','');
            }
        }
    </script>
    <?php
}

$drid = encodeId($_GET['Rid']);
$erid = explode(":",$drid);
$performance_appraisal_setup_id = $erid['1'];
$apiurl = API."?mode=getperformanceappraisalsetups&rid=".$performance_appraisal_setup_id;
$apicallss = callAPI($apiurl);
$result = json_decode($apicallss,1);
$pas = $result['performance_appraisal_setups'];
$appraisal_bell_curves = $result['appraisal_bell_curves'];
$filter_tables = $result['filter_tables'];
$filtertbl_rowid = $filter_tables['id'];

$performance_appraisal_questions = $result['performance_appraisal_questions'];

?>
<script type="text/javascript">
    function showpopupappraisal(){
        var filter_id = document.getElementById('filtertbl_rowid').value;
        var process_type = '3';
        var application_table = 'performance_appraisal_setups';
        jQuery.ajax({
            url: "views/custom_insert_filters.php",
            data:'filter_id='+filter_id+'&process_type='+process_type+"&tablename="+application_table,
            type: "GET",
            success:function(data){
                $('#dynamic_popup1').html(data);
                $("#kt_modal_dynamic_popup").modal("show");
                $('[data-bs-toggle="tooltip"]').tooltip();
            },
            error:function (){}
        });
    }
    function getapraisalsetuptype(type){
        $(".removeselected").removeClass("bg-success");
        $(".removeselected").removeClass("text-white");
        $("#appraisal_setup_type_"+type).addClass("bg-success");
        $("#appraisal_setup_type_"+type).addClass("text-white");
        $("#appraisal_setup_type").val(type);
        if(type=='KRA'){
            $("#kratab").show();
        }else{
           $("#kratab").hide();
        }
    }
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
            var assessment_year  = '<?php echo $assessment_year; ?>';
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
            var assessment_year  = '<?php echo $assessment_year; ?>';
            jQuery.ajax({
                url: "ajax.php",
                data:'page=publichappraisal&id='+id+_2'&assessment_year='+assessment_year,
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
}
function getPreviewAppraisal(appraisal_id){
    var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
    $("#preview_appraisal").html(loader);
    jQuery.ajax({
        url: "views/custom_view_performance_appraisal_setups.php",
        data:'appraisal_id='+appraisal_id,
        type: "POST",
        success:function(data){
            $('#preview_appraisal').html(data);
        },
        error:function (){}
    });
}
</script>
<?php if($error_message !=''){ ?>
        <div class="alert alert-danger background-danger alert-dismissible">
            <strong><?php echo $error_message; ?></strong>
             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
<?php } ?>
<div class="row">
    <div class="col-lg-2">
        <div class="card">
            <div class="card-header">
                <h5>Menu</h5>
                <div class="card-header-right">
                    <?php if($performance_appraisal_setup_id !="" && $pas['publish']=='N'){ ?>
                        <button type="button"   class="btn btn-primary" onclick="publichappraisal('<?php echo $performance_appraisal_setup_id; ?>')">Publish</button>
                    <?php } ?>
                </div>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs md-tabs d-block">
                    <li class="nav-item">
                        <a class="nav-link text-left <?php echo ($_POST['parent_tab'] == "performance_appraisal_setups" || $_POST['parent_tab'] =="")?"active":""; ?>" data-bs-toggle="tab" href="#performance_appraisal_setups" role="tab" aria-selected="false">Performance Appraisal</a>
                        <div class="slide"></div>
                    </li>
                    <?php if($performance_appraisal_setup_id !=""){ ?>
                    <li class="nav-item mt-2" id="kratab" style="<?php echo ($pas['appraisal_setup_type']=="KRA")?"":"display:none";?>">
                        <a class="nav-link <?php echo $_POST['parent_tab'] == "kra"?"active":""; ?>" data-bs-toggle="tab" href="#kra" role="tab" aria-selected="false">KRA</a>
                        <div class="slide"></div>
                    </li>
                    <li class="nav-item mt-2">
                        <a class="nav-link <?php echo $_POST['parent_tab'] == "otherquestion"?"active":""; ?>" data-bs-toggle="tab" href="#otherquestion" role="tab" aria-selected="false">Appraisal Questions</a>
                        <div class="slide"></div>
                    </li>
                    <li class="nav-item mt-2">
                        <a class="nav-link <?php echo $_POST['parent_tab'] == "otheractivity"?"active":""; ?>" data-bs-toggle="tab" href="#otheractivity" role="tab" aria-selected="false">Other Activity</a>
                        <div class="slide"></div>
                    </li>
                    <li class="nav-item mt-2">
                        <button type="button" class="nav-link" data-bs-toggle="tab" preview_appraisal" role="tab" aria-selected="false" onclick="getPreviewAppraisal('<?php echo $performance_appraisal_setup_id; ?>')">Preview Appraisal</button>
                        <div class="slide"></div>
                    </li>
                <?php } ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="text-center" style="display: none;  margin-left: auto;  margin-right: auto;  width: 40%; " id="loader-show_2">
        <img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview">
    </div>
    <div class="col-lg-10" id="loader-hide">
        <div class="tab-content">
            <div class="tab-pane <?php echo ($_POST['parent_tab'] == "performance_appraisal_setups" || $_POST['parent_tab'] =="")?"active":""; ?>" id="performance_appraisal_setups" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5>Performance Appraisal Setups</h5>
                        <div class="card-header-right"></div>
                    </div>
                    <div class="card-body"  <?php echo $pas['publish']=='Y'?'style="pointer-events: none; opacity: 0.7;"':''; ?>>

                        <form method="post" enctype="multipart/form-data">
                            <div class="row">
                                <div class="form-group col-lg-3">
                                    <span class="form-label">Name<span class="text-danger">*</span></span>
                                    <input aria-label="Performance Appraisal Name" type="text" name="performance_appraisal_name" class="form-control" value="<?php echo $pas['performance_appraisal_name']; ?>" required>
                                </div>
                                <div class="form-group col-lg-3 w-full">
                                    <span class="form-label">Aassessment Year:</span>
                                    <select aria-label="Assessment Year" class="form-control form-select js-example-basic-single" name="assessment_year" >
                                        <option value="">Select Any One</option>
                                        <?php for ($i=2020; $i<2090;$i++) {
                                            $k= $i+1; $view_assemnt_year =$i."-".$k; ?>
                                            <option <?php if($pas['assessment_year'] == $view_assemnt_year){ echo "selected";
} ?> value="<?php echo $view_assemnt_year; ?>"  ><?php echo $i." - ".$k ?></option>
                                        <?php  } ?>
                                    </select>
                                </div>
                                <div class="form-group col-lg-3">
                                    <span class="form-label">Assessment Start Date</span>
                                    <input aria-label="Input field" type="text" class="form-control startdatepicker_1 rangedatepicker"  value="<?php echo  fromDate($pas['assessment_start_date']); ?>" data-id="1"  name="assessment_start_date" onblur="assessmentchange()" />
                                </div>
                                <div class="form-group col-lg-3">
                                    <span class="form-label">Assessments End Date </span>
                                    <input aria-label="Assessment End Date" type="text" class="form-control enddatepicker_1" name="assessment_end_date" value="<?php echo  fromDate($pas['assessment_end_date']); ?>"   />
                                </div>
                                <div class="form-group col-lg-3">
                                    <span class="form-label">Self Assessment Start Date</span>
                                    <input aria-label="Input field" type="text" class="form-control startdatepicker_2 rangedatepicker"  value="<?php echo  fromDate($pas['self_assessment_start_date']); ?>" data-id="2" name="self_assessment_start_date"  />
                                </div>
                                <div class="form-group col-lg-3">
                                    <span class="form-label">Self Assessment End Date</span>
                                    <input aria-label="Input field" type="text" class="form-control enddatepicker_2"  value="<?php echo  fromDate($pas['self_assessment_end_date']); ?>"  name="self_assessment_end_date" />
                                </div>
                                <div class="form-group col-lg-3">
                                    <span class="form-label">Manager Assessement Start Date </span>
                                    <input aria-label="Input field" type="text" class="form-control startdatepicker_3 rangedatepicker"  value="<?php echo fromDate($pas['manager_assessment_start_date']); ?>" data-id="3" name="manager_assessment_start_date" />
                                </div>
                                <div class="form-group col-lg-3">
                                    <span class="form-label">Manager Assessment End Date </span>
                                    <input aria-label="Manager Assessment End Date" type="text" class="form-control enddatepicker_3" name="manager_assessment_end_date" value="<?php echo  fromDate($pas['manager_assessment_end_date']);  ?>"   />
                                </div>
                                <div class="form-group col-lg-3">
                                    <span class="form-label">Reviewing Assessment Start Date</span>
                                    <input aria-label="Input field" type="text" class="form-control startdatepicker_4 rangedatepicker"  value="<?php echo fromDate($pas['reviewing_start_date']); ?>" data-id="4" name="reviewing_start_date" />
                                </div>
                                <div class="form-group col-lg-3">
                                    <span class="form-label"> Reviewing Assessment End Date</span>
                                    <input aria-label="Input field" type="text" class="form-control enddatepicker_4" value="<?php echo  fromDate($pas['reviewing_end_date']); ?>"  name="reviewing_end_date" />
                                </div>
                                <div class="form-group col-lg-3">
                                    <span class="form-label">URL</span>
                                    <textarea aria-label="Url" name="url" class="form-control"><?php echo $pas['url']; ?></textarea>
                                </div>
                                <div class="form-group col-lg-3">
                                    <span class="form-label">File Upload</span>
                                    <div></div>
                                    <div class="custom-file">
                                        <input aria-label="Newfilename" type="file" class="custom-file-input" id="customFile" name="newfilename" value="<?php echo $pas['file_name']; ?>">

                                        <input type="hidden"  name="oldfilename" value="<?php echo $pas['file_name']; ?>">
                                        <a  class="mt-2 text-primary" href="uploads/all/<?php echo $pas['file_name']; ?>" download><?php echo $pas['file_name']; ?></a>
                                    </div>
                                </div>
                                <div class="form-group col-lg-3 w-full" >
                                    <span class="form-label">How often do assessments happen?</span>
                                    <select aria-label="Action Frequency" class="form-select js-example-basic-single" name="action_frequency">
                                         <option value="">Select Any one</option>
                                        <option <?php if($pas['action_frequency']==3){ echo "selected";
}  ?> value="3" >Every 3 Months</option>
                                        <option <?php if($pas['action_frequency']==4){ echo "selected";
}  ?> value="4" >Every 4 Months</option>
                                        <option <?php if($pas['action_frequency']==6){ echo "selected";
}  ?> value="6" >Every 6 Months</option>
                                        <option <?php if($pas['action_frequency']==12){ echo "selected";
}  ?> value="12" >Every Year</option>
                                    </select>
                                </div>
                                <div class="col-lg-6">
                                    <span class="form-label">Appraisal Type<span class="text-danger">*</span></span>
                                    <div class="row">
                                        <input type="hidden" name="appraisal_setup_type" id="appraisal_setup_type" value="<?php echo $pas['appraisal_setup_type'] !=""?$pas['appraisal_setup_type']:"C"; ?>">

                                        <div class="col-lg-6" onclick="getapraisalsetuptype('C')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                                            <div class="card text-center <?php echo ($pas['appraisal_setup_type']=="C" || $pas['appraisal_setup_type'] =="")?"bg-success text-white":"";?> removeselected" id="appraisal_setup_type_C">
                                                <div class="card-body">
                                                    <span class="d-block f-22"><i class="ti-comment"></i></span>
                                                    <span>Classic Appraisal</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6" onclick="getapraisalsetuptype('KRA')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                                            <div class="card text-center <?php echo $pas['appraisal_setup_type']=="KRA"?"bg-success text-white":"";?> removeselected" id="appraisal_setup_type_KRA">
                                                <div class="card-body">
                                                    <span class="d-block f-22"><i class="ti-layout-accordion-list"></i></span>
                                                    <span>KRA/KPI Appraisal</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-lg-3 w-full">
                                    <span class="form-label">Bell Curves</span>
                                    <select aria-label="Appraisal Bell Curve" class="form-control form-select js-example-basic-single" name="appraisal_bell_curve_id" >
                                        <option value="">Select Any One</option>
                                        <?php foreach($appraisal_bell_curves as $abc){ ?>
                                            <option value="<?php echo $abc['id']; ?>" <?php if($abc['id'] == $pas['appraisal_bell_curve_id']){ echo "selected";
} ?>><?php echo $abc['bell_curve_name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group col-lg-3">
                                    <span class="font-weight-bolder">Asign to <span class="text-danger">*</span></span>
                                    <div id="tsk_fortable"></div>
                                    <div class="col-xl-12">
                                        <i class="flaticon2-group text-success"></i>
                                        <button type="button" onclick="showpopupappraisal();">Edit </button>
                                    </div>
                                    <div id="filternames"> </div>

                                </div>
                            </div>
                            <input type="hidden" name="filtertbl_rowid" id="filtertbl_rowid" value="<?php echo $filtertbl_rowid;?>" >
                            <input type="hidden" name="submit" value="addpeople" />
                            <input type="hidden" name="rid" value="<?php echo $performance_appraisal_setup_id; ?>" />
                            <button type="submit" class="btn btn-primary waves-effect waves-light mt-5" name="submitperformance_appraisal_setups"><i class="fa fa-check m-r-10"></i> <?php echo $performance_appraisal_setup_id ==""?"Submit":"Update"; ?></button>

                        </form>
                    </div>
                </div>
            </div>
             <?php if($performance_appraisal_setup_id !=""){ ?>
            <div class="tab-pane <?php echo $_POST['parent_tab'] == "kra"?"active":""; ?>" id="kra" role="tabpanel" >

                <div class="card">
                    <div class="card-header">
                        <h5>KRA</h5>
                        <div class="card-header-right"></div>
                    </div>
                    <div class="card-body">
                        <ul class="nav md-tabs infisuite_tab ml-2 mb-2">
                            <li class="nav-item <?php echo ($_POST['child_tab'] == "kra" || $_POST['child_tab'] =="")?"active":""; ?>">
                                <a class="nav-link active" data-bs-toggle="tab" href="#setkra" role="tab" aria-selected="false" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">KRA</a>
                            </li>
                            <li class="nav-item <?php echo $_POST['child_tab'] == "setkratagging"?"active":""; ?>">
                                <a class="nav-link" data-bs-toggle="tab" href="#setkratagging" role="tab" aria-selected="false" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">KRA Tagging</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#setgoal" role="tab" aria-selected="false" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Allocate KRA Target</a>
                            </li>
                        </ul>
                        <div class="tab-content"  <?php echo $pas['publish']=='Y'?'style="pointer-events: none; opacity: 0.7;"':''; ?>>
                            <div class="tab-pane <?php echo ($_POST['child_tab'] == "kra" || $_POST['child_tab'] =="")?"active":""; ?>" id="setkra" role="tabpanel">
                                <?php include_once 'views/custom_insert_kpi_and_kra.php'; ?>
                            </div>
                            <div class="tab-pane <?php echo $_POST['child_tab'] == "setkratagging"?"active":""; ?>" id="setkratagging" role="tabpanel">
                                <?php include_once 'views/custom_insert_kra_tagging_filters.php'; ?>
                            </div>
                            <div class="tab-pane" id="setgoal" role="tabpanel">
                                 <?php include_once 'views/custom_view_kra_taggings.php'; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane <?php echo $_POST['parent_tab'] == "otherquestion"?"active":""; ?>" id="otherquestion" role="tabpanel"  <?php echo $pas['publish']=='Y'?'style="pointer-events: none; opacity: 0.7;"':''; ?>>
                <?php include_once 'views/custom_insert_performance_appraisal_questions.php'; ?>
            </div>
            <div class="tab-pane" id="otheractivity" role="tabpanel"  <?php echo $pas['publish']=='Y'?'style="pointer-events: none; opacity: 0.7;"':''; ?>>
                <?php include_once 'views/custom_insert_performance_appraisal_headers.php'; ?>
            </div>
            <div class="tab-pane" id="preview_appraisal" role="tabpanel">

            </div>
        <?php } ?>
        </div>

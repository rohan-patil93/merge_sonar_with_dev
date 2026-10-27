<?php

$display_id = $_GET['NI']!=""?$_GET['NI']:'2';
if (isset($_POST['submit1']) && $_POST['submit1'] == 'Add') {
        $_POST['created_by'] = $_SESSION['userid'];
        $_POST['created_date'] = nowDate();
        $_POST['vendor_id'] =  $_SESSION['vendor_id'];
        $_POST['current_status'] =  0;
        foreach ($_FILES['file']['name'] as  $image_key => $value_image) {
            $_POST['file_name'][$image_key] = imageuploadwitharray('all','file',$_POST['hid_image'][$image_key],"",$image_key);
        }
        $url = API."?mode=addjobpostingapplicatantdata&method=post";
        $response = file_post_contents($url, $_POST, 'insert');
        if ($response) {
            header("location:index.php?option=".$option."&Rid=".$_GET['Rid']);
        }
    }

if(isset($_POST['post_job'])){
        $alreadypost_job = 1;
        $url = API."?mode=insertjobpost&method=post";
        $_POST['company_id'] = $_SESSION['company_id'];
        $_POST['userid'] = $_SESSION['userid'];
        		$screen_question = array();
    foreach($_POST['screening_question_seq'] as $quekey=>$qua){
        $questionlist = array();
        if($_POST['screening_question'][$quekey] !=""){
            $questionlist['question'] = $_POST['screening_question'][$quekey];
            $questionlist['mandatory_question'] = $_POST['screening_mandatory_question'][$quekey] =="Y"?"Y":"N";
            $questionlist['question_answer'] = $_POST['screening_question_answer'][$quekey];
        }
        $screen_question[] =$questionlist; 
    }
    $_POST['question'] = json_encode($screen_question);
    $_POST['candidate_mandatory_field'] = json_encode($_POST['candidate_mandatory_field']);
        $_POST['job_tag'] = json_encode($_POST['job_tag']);
        $_POST['summa'] = base64_encode($_POST['summa']);
        $_POST['min_salary'] = convertamountnumber($_POST['min_salary']);
        $_POST['max_salary'] = convertamountnumber($_POST['max_salary']);
        $_POST['annual_salary'] = convertamountnumber($_POST['annual_salary']);
        $_POST['candidate_show_status'] = isset($_POST['candidate_show_status'])?"0":"1";
        $_POST['aa_eeo'] = isset($_POST['aa_eeo'])?"0":"1";
        $responses = file_post_contents($url, $_POST);
        $response = json_decode($responses,1);
        if($response['status'] == 'fail'){
        if(isset($_SESSION['error'][$response['error_no']])){
            $dis_err = explode(",", $response['other']);
            foreach ($dis_err as $va) {
                $dis_er [] = $dis_labels[$va];
            }
            $error_message = $response['error_no'] ." : ".$_SESSION['error'][$response['error_no']]." : ".implode(", ", (array)$dis_er);
        }else{
            $error_message = $response['message'];
        }
    }else{
        if($_POST['submit_value'] == 'exit'){
        }else{
      ?>
      <script type="text/javascript">
      document.onreadystatechange = function () {
        if (document.readyState == "complete") {
          tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][3000]; ?>','');
        }
      }
      </script>
      <?php
    }
    }
 }

if($_GET['Rid']!=""){
    $rid = urldecode($_GET['Rid']);
    $rid =  encodeId($rid);
    $ex_rid = explode(':', $rid);
    $jobpost = $ex_rid['1'];
}else{
    $jobpost = $_GET['job'];
}

if($_SESSION['user_type']==3){
    $reswrdata["where"] = array('company_id'=> $_SESSION['company_id'],'job_posting_id'=> $jobpost,'is_active'=>0,'vendor_id'=>$_SESSION['vendor_id']);
}else{
    $reswrdata["where"] = array('company_id'=> $_SESSION['company_id'],'job_posting_id'=> $jobpost,'is_active'=>0);
}

$roleshortcodes  = json_encode($_SESSION['role_ids']);
$resurll = API."?mode=getjobpostingpagedetails&job_posting_id=".$jobpost."&vendor_id=".$_SESSION['vendor_id']."&user_type=".$_SESSION['user_type']."&roleshortcode=".$roleshortcodes;
$API = callAPI($resurll);
$resultdetails = json_decode($API,1);
$resdata = $resultdetails['job_posting_candidates'];
$loigeneratedcnt = 0;
foreach($resdata as $resdatajpc){
    if($resdatajpc['current_status'] == ST_LOIGENERATED){
        $loigeneratedcnt++;
    }
}
$processdata = $resultdetails['job_postings'];
$spdata  = $resultdetails['job_selection_process_steps'];

//ST_LOIGENERATED

 $candidatelist = array();
foreach($resultdetails['candidates'] as $candidatelistre){
    $candidatelist[$candidatelistre['id']] =$candidatelistre;
}

$stausresult = ViewStatusData();
 $count_check = count((array)$spdata)-1;

$colo_array = array('info','warning','success','secondary','dark-50','primary');
$m=0;
foreach($spdata as $sp){
    $process[$sp['id']] =$sp['select_name'];
    $process_color[$sp['id']] =$colo_array[$m];
    $m++;
}

?>
<script type="text/javascript">
    function updatejobstatus(id,job_status){
        $.ajax({
            url: "ajax.php",
            data:'page=dynmicupdatetable&id='+id+'&job_status='+job_status+'&table=job_postings',
            type: "POST",
            success:function(data){
                console.log(data);
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][3001]; ?>','');
            },
            error:function (){}
        });
    }
    function updatepostcandidate(type_id,can_id,type){
    jQuery.ajax({
        url: "views/custom_insert_job_posting_candidat_popup.php",
        data:'type='+type+'&type_id='+type_id+'&job_can_id='+can_id,
        type: "POST",
        success:function(data){
            console.log(data);
            $('#popup1').html(data);
            $("#kt_modal_KTDatatable_remote").modal("show");
        },
        error:function (){}
    });
}
function displaystausdrop(id,table,status_id){
        $.ajax({
            url: "ajax.php",
            data:'page=current_status_drop&id='+id+'&current_status='+status_id+'&table='+table,
            type: "POST",
            success:function(data){
                console.log(data);
                $("#dropdownlist").html(data);
            },
            error:function (){}
        });

    }
    function statuspopup(status,id){
      var table_name = 'job_postings';
      jQuery.ajax({
          url: "views/custom_insert_status_popup.php",
          data:'table='+table_name+'&status='+status+'&rid='+id,
          type: "POST",
          success:function(data){
              $('#popup1').html(data);
              $('#kt_modal_KTDatatable_remote').modal("show");
          },
          error:function (){}
      });
  }
  function changepageview(val){
        alert(val);
        window.location.href = 'index.php?option=COM_INSERT_JOB_POSTING_CANDIDATES&job='+val;
    }
    function addrows(){
        var count = $(".itemRow").length;
        count++;
        var htmlRows = '';
        htmlRows += '<tr>';
        htmlRows += '<td><input aria-label="Name[]" type="text" class="form-control" name="name[]" value=""></td> ';
        htmlRows += '<td><input aria-label="Email[]" type="text" class="form-control" name="email[]" value=""> </td>';
        htmlRows += '<td><input aria-label="Phone[]" type="text" class="form-control" name="phone[]" value=""></td>';
        htmlRows += '<?php if($_SESSION["user_type"] ==3){ ?><td>    <div class="custom-file"><input type="file" class="custom-file-input" id="customFile" name="file[]"/> <label class="custom-file-label" for="customFile">Choose file</label></div>  <input type="hidden" name="hid_image[]" value="<?php  echo $res['file']; ?>"></td>  <?php
} ?>';
        htmlRows += '<td><select aria-label="Process[]" class="form-control" name="process[]" ><option value="0">Select Any One</option>    <?php foreach ($spdata as  $sp) { ?><option  value="<?php echo $sp['id']; ?>"><?php echo $sp['select_name']; ?></option><?php
} ?></select></td>';
        htmlRows += '<?php if($_SESSION["user_type"] !=3){ ?><td>&nbsp; </td><td>&nbsp;</textarea> </td>';
        htmlRows += '<td>&nbsp;</textarea> </td><td> <button class="btn btn-primary dropdown-toggle" style="color: <?php echo "#".$stausresult[0]['color']; ?>;    background:  <?php echo "#".strval($stausresult[0]['background_color']); ?>; border-color: <?php echo "#".strval($stausresult[0]['background_color']); ?>;" type="button"  ><?php echo $stausresult[0]['name']; ?> </button></td>';
        htmlRows += '<td><a herf="#" class="btn btn-secondary mr-2" disabled data-container="body" data-toggle="popover" data-placement="top" data-content="Create Candidate Details"  onkeypress="if(event.key===\'Enter\'||event.key===\' \')this.click()" role="button"> Genrate </a></td> <?php } ?>';
        htmlRows += '</tr>';
        $('#invoiceItem').append(htmlRows);
    }
    function genrateregistation(name,email,phone){
        swal.fire({
            //title: "Do you want to Genrate auto candidaite registation",
            text: "Do you want to Genrate auto candidaite registation ",
            type: "success",
            buttonsStyling: false,
            confirmButtonText: "<i class='la la-hand-o-up'></i>Yes, close!",
            confirmButtonClass: "btn btn-primary",
            showCancelButton: true,
            cancelButtonText: "<i class='la la-hand-o-right'></i> No, continue",
            cancelButtonClass: "btn btn-danger"
        }).then(function(result) {
            if (result.value) {
                jQuery.ajax({
                    url: "ajax.php",
                    data:'page=candidateautoregistation&display_name='+name+'&user_mobile='+phone+'&user_email='+email,
                    type: "POST",
                    success:function(data){
                        console.log(data);
                        data =  JSON.parse(data);
                        console.log(data.status);
                        var mms = "Candidate registation is success";
                        if(data.status == 'fail'){
                            mms = "Email is already exists"
                        }
                        swal.fire({
                        //title: "You want to go goal page",
                        text: mms,
                        type: "success",
                        buttonsStyling: false,
                        confirmButtonText: "<i class='la la-hand-o-up'></i>Yes, close!",
                        confirmButtonClass: "btn btn-primary",
                        showCancelButton: false,
                        cancelButtonText: "<i class='la la-hand-o-right'></i> No, continue",
                        cancelButtonClass: "btn btn-danger"
                    }).then(function(result) {
                        if (result.value) {
                            window.location.href = 'index.php?option=COM_INSERT_ORGANIZATION_GOALS&role='+role_id+'&assemnt_year='+assemnt_year;
                        }
                    });

                },
                error:function (){}
            });
            }
        });
    }
    function publishdatapopup(job_post_id){
        jQuery.ajax({
            url: "views/custom_insert_job_hiring_source_popup.php",
            data:'job_post_id='+job_post_id,
            type: "POST",
            success:function(data){
                console.log(data);
                $('#dynamic_popup11').html(data);
                $("#kt_modal_dynamic_popup2").modal("show");
            },
            error:function (){}
        });
    }
</script>
<style>
    .symbollogo{
        background-color: #f64e60 !important;
        color: #fff !important;
        width: 62px !important;
        height: 62px !important;
    }
</style>
<div class="d-flex flex-column-fluid ">
    <div class="container-fluid">
        <!--begin::Navbar-->
        <div class="card mb-2">
            <div class="card-body p-t-40 p-l-40 p-r-40 p-b-40">
                <!--begin::Details-->
                <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
                    <div class="flex-shrink-0 mr-7 mt-lg-0 mt-3  mr-10">
                        <div class="symbol symbol-danger symbol-70  symbollogo p-15 text-center" >
                            <span class="font-size-h4 f-18"><?php echo initials($processdata['job_title_name']); ?></span>
                        </div>
                    </div>
                    <!--begin: Info-->
                    <div class="flex-grow-1 m-l-10">
                        <!--begin::Title-->
                        <div class="row">
                            <div class="col-lg-7">
                                <div class="justify-content-between flex-wrap mt-1">
                                    <div class="d-flex mr-3">
                                        <a href="#" class="text-dark-75 text-hover-primary font-size-h5 font-weight-bold mr-3 f-16 f-w-600"><?php echo $processdata['job_title_name'];?></a>
                                    </div>
                                    <div class="d-flex flex-wrap justify-content-between mt-2" style="">
                                        <div class="d-flex flex-column flex-grow-1 ">
                                            <div class="d-flex flex-wrap mb-2">
                                                <a href="#" class="text-dark-50 text-hover-primary  mr-lg-8 mr-10 mb-lg-0 mb-2 f-14 text-uppercase">
                                                    <i class="ti-briefcase"></i> &nbsp;<?php echo $processdata['job_number'];?>
                                                </a>
                                                <a href="#" class="text-dark-50 text-hover-primary font-weight-bold mr-lg-8 mr-10 mb-lg-0  f-14mb-2 text-uppercase">
                                                    <i class="ti-shine"></i> &nbsp;<?php echo $processdata['department_name'];?>
                                                </a>
                                                <a href="#" class="text-dark-50 text-hover-primary font-weight-bold f-14 text-uppercase">
                                                    <i class="ti-location-pin"></i>&nbsp;<?php echo $processdata['location_name'];?>
                                                </a>
                                            </div>
                                            <div class="mt-1" style="max-height: 80px;min-height: 84px;">

                                            </div>

                                        </div>
                                        <?php
                                        $pp = 0;
                                            if($loigeneratedcnt != 0 && $processdata['no_of_resources'] !=0){
                                                $pp = ($loigeneratedcnt/$processdata['no_of_resources'])*100;
                                            }
                                         ?>
                                    </div>
                                </div>
                            </div>
                            <!--end::Title-->
                            <div class="col-lg-5">
                                <div class="my-lg-0 my-3">
                                    <div class="align-items-center text-end">
                                        <?php if($jobpost!="" && $_SESSION['role_ids'][0]==HIRINGMANAGER_ROLE && $processdata['recruitment_manager'] !="" && $processdata['recruitment_manager'] !="-1") { $nmm= ViewStatusValue($processdata['current_status'],'job_postings');  ?>

                                        <div class="btn-group dropdown-split-primary">
                                            <button type="button" class="btn btn-primary text-capitalize"><?php echo $nmm['name']; ?> </button>
                                            <button onclick="displaystausdrop(<?php echo $jobpost; ?>,<?php echo "'job_postings'"; ?>,<?php echo $processdata['current_status']; ?>)" class="btn btn-primary desktop-view dropdown-toggle dropdown-toggle-split waves-effect waves-light text-capitalize" style="color: <?php echo "#". $nmm['color']; ?>; background:  <?php echo "#".strval($nmm['background_color'] ); ?>; border-color: <?php echo "#".strval($nmm['background_color']); ?>;" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                            >

                                            </button>
                                            <div class="dropdown-menu dropdown-menu-status" id="dropdownlist">
                                            </div>
                                            </div>

                                        <?php } ?>


    <?php if($jobpost!="" && $_SESSION['role_ids'][0]==RECRUITINGMANAGER_ROLE && $processdata['current_status'] >= ST_TOBEPUBLISH) {  ?>
            <button onclick="publishdatapopup(<?php echo $jobpost; ?>)" class="btn btn-primary desktop-view text-capitalize"  type="button"  >Publish</button>
        <?php } ?> &nbsp;
        <?php $job_status = $processdata['job_status'];
        $job_status_name = $job_status==1 ? "CLOSE" : "OPEN";
        if($_SESSION['user_type'] !=3){
            ?>

<div class="form-radio m-l-10 d-inline-block align-middle">
    <div class="radio radio-inline" >
      <label class="form-label">
        <input aria-label="Io" type="radio" name="radio" <?php echo $job_status =="0"?"checked":"";  ?> onchange="updatejobstatus(<?php echo $processdata['id']; ?>,0)">
        <i class="helper"></i>Open
      </label>
    </div>
    <div class="radio radio-inline" >
      <label class="form-label">
        <input aria-label="Radio option" type="radio" <?php echo $job_status =="1"?"checked":"";  ?> name="radio" onchange="updatejobstatus(<?php echo $processdata['id']; ?>,1)">
        <i class="helper"></i>Close
      </label>
    </div>
    <div class="radio radio-inline m-0" >
      <label class="form-label">
        <input aria-label="Radio option" type="radio" <?php echo $job_status =="2"?"checked":"";  ?> name="radio" onchange="updatejobstatus(<?php echo $processdata['id']; ?>,2)">
        <i class="helper"></i>Hold
      </label>
    </div>
</div>
        <?php }else{ ?>
            <button class="btn btn-primary desktop-view " type="button"> <?php echo $job_status_name; ?>  </button> &nbsp;
        <?php } ?>


                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-lg-3"></div>
                                        <div class="col-lg-9">
                                    <div class="d-flex align-items-center w-200px w-sm-300px flex-column mt-3" style="min-width: 200px;">
                                            <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                                            <span class="fw-bold fs-6 text-gray-400">Hiring Status</span>
                                            <span class="fw-bolder fs-6 ml-2"><?php echo $pp; ?>%</span>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="progress  progress-xs">
                                                <div class="progress-bar progress-bar-pink " role="progressbar" style="width: <?php echo $pp; ?>%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                </div>
                                <div class="row text-right mt-2">
                                        <div class="symbol-group symbol-hover">
                                            <div class="symbol-group symbol-hove ml-2r">
                                                <?php $k=1; $cs =0; foreach ($resdata as $res){
                                                    if($res['current_status'] == '199'){ $cs++;
}
                                                    if($k<4){ ?>
                                                        <div class="symbol symbol-35 symbol-circle"  data-bs-toggle="tooltip" data-placement="bottom" title="<?php echo $res['candidate_name']; ?>">
                                                            <span class="symbol-label font-weight-bold"> <?php echo initials($res['candidate_name']); ?></span>
                                                        </div>
                                                    <?php } $k++;
} ?>
                                                    <?php if(count((array)$resdata)>3){ ?>
                                                        <div class="symbol symbol-35 symbol-circle"  data-bs-toggle="tooltip" data-placement="bottom" title="<?php echo $res['candidate_name']; ?>">
                                                            <span class="symbol-label font-weight-bold"> <?php echo count((array)$resdata)-3; ?>+</span>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>

                                </div>

                                </div>
                            </div>
                            <!--end::Content-->

                        </div>
                        <!--end::Info-->
                    </div>
                    <!--end::Details-->


            <div class="card  mb-1 mb-xl-10">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-10">
                            <div class="d-flex align-items-center " style="margin-left: 77px;">
                                <div class="m-r-20 text-center">
                                    <div class="font-weight-bold mb-2 mr-10 d-inline-block">Requested Date</div>
                                    <span class="form-label label label-lg label-primary f-14"><?php echo date('d M, Y',strtotime($processdata['requested_date']));?></span>
                                </div>
                                <div class="mr-5 text-center">
                                    <div class="font-weight-bold mb-2 mr-5 d-inline-block">Required Date</div>
                                    <span class="form-label label label-lg label-success f-14"><?php echo date('d M, Y',strtotime($processdata['required_date']));?></span>
                                </div>
                            </div>
                        </div>
                            <div class="col-lg-2">
                                <div class="text-end">
                                    <div class="font-weight-bold mb-2 mr-5 d-inline-block">No of Resources </div>
                                    <span class="form-label label label-lg label-primary f-14"><?php echo $processdata['no_of_resources'];?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Navbar-->


        <div class="card">
            <div class="card-body">
                <!--begin::Navs-->
                <div class="row">
                    <div class="col-lg-8">
                    <ul class="nav md-tabs infisuite_tab float-left">
                        <!--begin::Nav item-->
                        <li class="nav-item">
                            <a class="nav-link me-6 <?php if($display_id== 2){ echo "active";
} ?>"  href="index.php?option=COM_LIST_JOB_POSTING_CANDIDATE&Rid=<?php echo $_GET['Rid']; ?>&NI=<?php echo "2"; ?>">
                                <?php echo "Job Details"; ?></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link me-6 <?php if($display_id== 1){ echo "active";
} ?>"  href="index.php?option=COM_LIST_JOB_POSTING_CANDIDATE&Rid=<?php echo $_GET['Rid']; ?>&NI=1">
                                    <?php echo "Candidates"; ?></a>
                                </li>
                                <li class="nav-item">
                                <a class="nav-link me-6 <?php if($display_id== 3){ echo "active";
} ?>"  href="index.php?option=COM_LIST_JOB_POSTING_CANDIDATE&Rid=<?php echo $_GET['Rid']; ?>&NI=3">
                                    <?php echo "Search"; ?></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link me-6 <?php if($display_id== 4){ echo "active";
} ?>"  href="index.php?option=COM_LIST_JOB_POSTING_CANDIDATE&Rid=<?php echo $_GET['Rid']; ?>&NI=4">
                                    <?php echo "Job feedback questions"; ?></a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-lg-4">
                            <?php if($_SESSION['user_type'] !=3){ ?>
                                <a href="index.php?option=COM_INSERT_JOB_POSTINGS" class="btn btn-success desktop-view text-capitalize float-right"><i class="icofont icofont-plus icon-white"></i>   Job Posting</a>
</div>
                            <?php } ?>
                        </div>

                        <!--begin::Navs-->
                    </div>
                </div>

                <div class="card">

                    <?php
                    if($display_id==1){
                        include_once "custom_list_job_candidate_details.php";
                    }elseif($display_id==3){
                        include_once "custom_list_candidates.php";
                    }elseif($display_id==4){
                        include_once "custom_list_job_posting_candidate_feedback_questions.php";
                    }else{
                        $_GET['option'] = 'COM_INSERT_JOB_POSTINGS';
                        include_once "includes/controller.php";
                        $dispage = str_replace("views/", '', $disptemp);
                        include_once $dispage;
                    } ?>
                </div>
            </div>
        </div>
        </div>
            </div>
        <div id="kt_modal_KTDatatable_remote" class="modal fade" role="dialog" aria-hidden="true">
            <div class="modal-dialog " >
                <div class="modal-content"  id="popup1">

                </div>
            </div>
        </div>

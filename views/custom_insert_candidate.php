<?php
$display_id = $_GET['NI']!="" ? $_GET['NI'] : '0';
$rid1 =  encodeId($_GET['Rid']);
$rid = urldecode($rid1);
$cand = explode(':', $rid);
$candidate_id = $cand['1'];
$table_name = "candidates";
if($_POST['submit'] == 'query'){
    $res = Insertqueries($_POST);
     $_POST['submit'] = '';
     ?>
     <script type="text/javascript">
         document.onreadystatechange = function () {
             if (document.readyState == "complete") {
                 var mesg = "Query Submited Successfully";
                 tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',mesg,'');
             }
         }
     </script>
     <?php
 }elseif($_POST['submit'] == 'reply'){
     $res = updatequeries($_POST);
     ?>
     <script type="text/javascript">
         document.onreadystatechange = function () {
             if (document.readyState == "complete") {
                 var mesg = "Query Submited Successfully";
                 tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',mesg,'');
             }
         }
     </script>
     <?php
 }elseif(isset($_POST['submit'])){
     $_POST['company_id']=$_SESSION['company_id'];
     $_POST['user_id']=$_SESSION['userid'];
     $_POST['salary_template']='Y';
     $url = API."?mode=genratecandidatesalarytemplate&method=post";
     $response = file_post_contents($url, $_POST, 'update');
     $response3 = json_decode($response,1);
     $option_c = $_GET['option'];
     $RID_c = $_GET['Rid'];
     if ($response) {
         header("location:index.php?option=COM_LIST_CANDIDATE_OFFER&Rid=".$RID_c);
     }
     ?>
     <script type="text/javascript">
         document.onreadystatechange = function () {
             if (document.readyState == "complete") {
                 var mesg = "<?php echo $_SESSION['error_message']['9143']; ?>";
                 tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',mesg,'');
             }
         }
     </script>
     <?php

 }
 if (isset($_FILES['profile_pic_file']) && $_FILES['profile_pic_file']['error'] != "4") {
         $_POST['candidate_id'] = $candidate_id;
         $path = $_SESSION['DB_NAME']."/".$_SESSION['company_id']."/candidates/".$candidate_id;
         $compay_file = imageuploadwitharray($path,'profile_pic_file','','',$key);
         $_POST['image'] = $compay_file;
         $_POST['company_id']=$_SESSION['company_id'];
         $_POST['user_id']=$_SESSION['userid'];
         $url = API."?mode=insertcandidatedetails&method=post";
         $response = file_post_contents($url, $_POST, 'insert');
         $response1 = json_decode($response,1);
     }
 if (isset($_FILES['resume_upload_file']) && $_FILES['resume_upload_file']['error'] != "4") {
         $_POST['candidate_id'] = $candidate_id;
         $path = "all";
         $compay_file = imageuploadwitharray($path,'resume_upload_file','','',$key);
         $_POST['file'] = $compay_file;
         $_POST['company_id']=$_SESSION['company_id'];
         $_POST['user_id']=$_SESSION['userid'];
         $url = API."?mode=insertcandidatedetails&method=post";
         $response = file_post_contents($url, $_POST, 'insert');
         $response1 = json_decode($response,1);
         ?>
         <script type="text/javascript">
             document.onreadystatechange = function () {
                 if (document.readyState == "complete") {
                     var mesg = "<?php echo $_SESSION['error_message']['9144']; ?>";
                     tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',mesg,'');
                 }
             }
         </script>
         <?php
     }
     if(isset($_POST['submit_create_employee'])){
     $_POST['category_id'] = $cat_id;
     $url = API."?mode=genratecandidatetoemployee&&method=post&candidate_id=".$candidate_id;
     $response2 = file_post_contents($url, $_POST, 'insert');
     $response3 = json_decode($response2,1);
     $respons = $response3['new_user_create'];
     if($respons['status'] == 'sucess'){
         ?>
         <script type="text/javascript">
             document.onreadystatechange = function () {
                 if (document.readyState == "complete") {
                     var mesg = "<?php echo $_SESSION['error_message']['9145']; ?>";
                     tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',mesg,'');
                 }
             }
         </script>
         <?php
     }else{
         if(isset($_SESSION['error'][$respons['error_no']])){
             $dis_err = explode(",", $respons['other']);
             foreach ((array)$dis_err as $va) {
                 $dis_er [] = $dis_labels[$va];
             }
             $error_message = $respons['error_no'] ." : ".$_SESSION['error'][$respons['error_no']]." : ".implode(", ", (array)$dis_er);
         }else{
             $error_message = $respons['message'];
         }
     }
 }
 if(isset($_POST['submitlog'])){
     $filedetails=array();
     if($_FILES['files']['error'][0] != '4'){
          foreach((array)$_FILES['files']['name'] as $key=>$files){
          $filedetailsd['originalname'] = basename($_FILES['files']['name'][$key]);
          $filedetailsd['filesize'] =basename($_FILES['files']['size'][$key]);
          $name = time().basename($_FILES['files']['name'][$key]);
          if(move_uploaded_file($_FILES["files"]["tmp_name"][$key], "uploads/attachments/".$name)) {
              $filedetailsd['filename'] = $name;
          }
          $filedetails[] = $filedetailsd;
      }
    }
    $_POST['fila']=$filedetails;
    $_POST['company_id']=$_SESSION['company_id'];
    $_POST['user_id']=$_SESSION['userid'];
    $url = API."?mode=insertcandidatelogs&method=post";
    $responses = file_post_contents($url, $_POST);
    $log_data = json_decode($responses,1);
    ?>
    <script type="text/javascript">
        document.onreadystatechange = function () {
            if (document.readyState == "complete") {
                var mesg = "Query Submited Successfully";
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',mesg,'');
            }
        }
    </script>
<?php } ?>
<?php
if (isset($_POST['insertcandidateoffer']) && $_POST['pay_template'] !=0 && $_POST['pay_template'] !="") {
        $_POST['company_id']=$_SESSION['company_id'];
        $_POST['user_id']=$_SESSION['userid'];
        $url = API."?mode=genratecandidatesalarytemplate&method=post";
        $response = file_post_contents($url, $_POST, 'update');
        $response3 = json_decode($response,1);
        $option_c = $_GET['option'];
        $RID_c = $_GET['Rid'];
        ?>
         <script type="text/javascript">
             document.onreadystatechange = function () {
                 if (document.readyState == "complete") {
                     var mesg = "<?php echo $_SESSION['error_message']['9143']; ?>";
                     tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',mesg,'');
                 }
             }
         </script>
         <?php
    }

$url = API."?mode=getcandidatemaster&table=application_query&candidate_id=".$candidate_id;
$response = callAPI($url);
$result = json_decode($response,1);
$application_query = $result['application_query'];
$data = $result['candidates'];
$job_posting_candidates = $result['job_posting_candidates'];
$task_cnt = $result['task_cnt'];
$i=1; $k=1;
foreach ((array)$data as  $value) {
    if($value!="" ){
        $k++;
    }
    $i++;
}
if($i !=0 && $i!=""){
        $pp = round(($k/$i)*100);
    }else{
        $pp =$k*100;
    }
    $dispath = "uploads/".$_SESSION['DB_NAME']."/".$_SESSION['company_id']."/".$table_name."/".$candidate_id."/".$data['image'];
    ?>
    <script type="text/javascript">
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
       jQuery.ajax({
           url: "views/custom_insert_status_popup.php",
           data:'table=candidates'+'&status='+status+'&rid='+id,
           type: "POST",
           success:function(data){
               $('#popup123').html(data);
               $('#kt_modal_KTDatatable_remote123').modal({ show: true});
           },
           error:function (){}
       });
   }
   function updateProfilePic(){
       $("#profile_pic_file").submit();
   }
   function updateresume(){
       $("#resume_upload_file").submit();
   }
   function verificationCandidateDetails(verification_type =""){
        var candidate_id = '<?php echo $candidate_id; ?>';
        jQuery.ajax({
            url: "views/custom_insert_candidate_verifications_popup.php",
            data:'candidate_id='+candidate_id+'&verification_type='+verification_type,
            type: "POST",
            success:function(data){
                $('#dynamic_popup1').html(data);
                $('#kt_modal_dynamic_popup').modal("show");
            }
        });
    }
   //         url: "ajax.php",
   //         data:'page=cnadidateverified&candidate_id='+candidate_id+"&type="+type,
   //         type: "POST",
   function showCV(filename){
       var data = "";
       data +='<div class="card-body" style="height:500px"><embed  src="'+filename+'" style="width: 100%;height: 150%;border: none;"></embed></div>';
       $('#dynamicpageshow').html(data);
   }
//ajax page open
function loadDynamicPage(option,id="",tabid="",subtabid=""){
    console.log(option);
    if(option == 'COM_INSERT_TRANSACTION_ACTIVITY_SCHEDULES'){
        var id = '';
    }
    var transaction_id = '<?php echo $candidate_id; ?>';
    var transaction_table  = '<?php echo $table_name; ?>';
    var extra_paramiter  = '<?php echo json_encode($_GET); ?>';
    var parent_option  = '<?php echo $_GET['option']; ?>';
    $(".removeativtab").removeClass("active");
    $("#active_"+option).addClass("active");
    var htmloader = '<div class="text-center"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
    $('#dynamicpageshow').html(htmloader);
    jQuery.ajax({
        url: "views/custom_inbox_page_redirection.php",
        data:'option='+option+"&rid="+id+"&Rid="+id+"&ajaxpage=Y&transaction_table="+table_name+"&transaction_id="+transaction_id,
        type: "POST",
        success:function(htmlRows){
                $('#dynamicpageshow').html(htmlRows);
                insertadittrails(parent_option,option,extra_paramiter);
                if(option != 'COM_LIST_CANDIDATE_INFO'){
                    $(".js-example-basic-single").select2();
                }
                $(".datepicker" ).datepicker({
                    showOn: "both",
                    buttonText: "<i class='ti-calendar text-dark f-20 f-w-800 m-r-10'></i>",
                    dateFormat: DateFormate,
                    changeMonth: true,
                    changeYear: true
                });
                $('[data-bs-toggle="tooltip"]').tooltip();
            },
            error:function (){}
        });
}
//end ajax page open
<?php if(isset($_GET['ETYPE']) && $_GET['ETYPE']!=""){ ?>
    document.onreadystatechange = function () {
        if (document.readyState == "complete") {
            $('html, body').animate({
                scrollTop: $("#<?php echo $_GET['ETYPE']; ?>").offset().top
            }, 20);
            $("#<?php echo $_GET['ETYPE']; ?>").css("background-color","cornsilk");
            setTimeout(function() {$("#<?php echo $_GET['ETYPE']; ?>").css("background-color","");}, 5000);

        }
    }
<?php } ?>
</script>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <?php if($error_message !=''){ ?>
            <div class="alert alert-danger background-danger alert-dismissible">
                <strong><?php echo $error_message; ?></strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>
        <div class="card mb-1 mb-xl-10">
            <?php if($data['id'] !="" && $_GET['Rid']!=""){ ?>
                <div class="card-body pt-9 pb-0">
                    <div class="banner">
                        <img src="assets/images/banner.webp" class="w-100 b-radius-20" alt="">
                    </div>
                    <div class="profile-img" style="margin:-40px 0 0 0;">
                        <div class="row align-items-bottom">
                            <div class="col-lg-1"></div>
                            <div class="col-lg-5">
                                <div class="image-input image-input-empty image-input-outline" id="kt_image_5" >
                                    <form method="post" id="profile_pic_file" enctype="multipart/form-data" class="position-relative">
                                        <label class="btn-hover-text-primary" data-action="remove" data-bs-toggle="tooltip" title="Edit Profile Pic" style="left: 90px; bottom: 0px;position: absolute;">
                                            <span><i class="ti-pencil-alt"></i></span>
                                            <input aria-label="Profile Pic File" type="file" name="profile_pic_file" accept=".png, .jpg, .jpeg"  onchange="updateProfilePic()" />
                                        </label>

                                        <?php if($data['image']!=""){ ?>
                                            <img src="<?php echo $dispath; ?>" class="img-radius img-90" alt="">
                                        <?php  }else{ ?>
                                            <div class="symbol symbol-50 symbol-lg-120 symbol-primary ">
                                                <span class="font-size-h3 symbol-label font-weight-boldest img-90 p-20 f-40" style=" border-radius: 50%; line-height: 1.1;"><?php echo $data['display_name']!=""?initials($data['display_name']):"NA"; ?></span>
                                            </div>

                                        <?php } ?>
                                        <span id="verified_btn">
                                            <?php
                                            $clr_ver = "warning"; $icn_ver = "alert";
                                            $nam_ver = "Verifiaction Pending";
                                            $onc_ver = 'N';
                                            if($data['candidate_verified'] == '0'){
                                                $clr_ver = "success";
                                                $icn_ver = "check";
                                                $nam_ver = "Verified";
                                            }
                                            if($_SESSION['user_type']!=4 && $_SESSION['userid']==$data['rm_id']){
                                                $onc_ver = 'Y';
                                            }
                                            ?>
                                            <button type="button" class="btn waves-effect waves-light btn-<?php echo $clr_ver; ?> pt-1 pb-1 m-l-20" <?php if($onc_ver == 'Y'){ ?> onclick="verificationCandidateDetails()"<?php
}else{ ?> style="cursor: auto;"; <?php
} ?> ><i class="ti-<?php echo $icn_ver; ?> m-r-5"></i> <?php echo $nam_ver; ?>
                                        </button>
                                    </span>
                                </form>
                            </div>
                            <div class="mt-3 d-block mb-3">
                                <div class="d-flex mr-3 mb-2">
                                    <a href="#" class="text-dark-75 f-18 text-hover-primary font-size-h5 font-weight-bold mr-10 f-w-600"><?php echo $data['display_name'];?></a>
                                    <a href="#">
                                        <i class="flaticon2-correct text-success font-size-h5 mr-2"></i>
                                    </a>
                                </div>
                                <?php if($data['email_id']!=""){ ?>
                                    <a href="#" class="text-dark-50 text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2 m-l-5">
                                        <i class="ti-email f-14 text-primary"></i>&nbsp;<?php echo $data['email_id'];?></a>
                                    <?php }
if($data['phone']!=""){  ?>
                                        <a href="#" class="text-dark-50 text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2 m-l-5">
                                            <i class="fa fa-phone f-14 text-primary"></i>&nbsp;<?php echo DynamicPhoneNumberFormt($data['phone']);?></a>
                                        <?php } ?>
                                        <?php if($data['address']!=""){ ?>
                                            <a class="font-weight-bold text-dark-50 d-block mb-2"><i class="ti-location-pin text-primary f-18"></i>&nbsp;<?php echo $data['address']." ".$data['post_code'];?> </a>
                                        <?php } ?>
                                        <div class="font-weight-bold text-dark-50 mt-1 f-14">
                                            <?php if($data['total_experiance']!=""){  ?>
                                                <b class="f-w-600">Total Experiance : </b><?php echo $data['total_experiance']." Years";?>&nbsp;
                                            <?php }
if($data['notice_period']!=""){  ?>
                                                <b class="f-w-600">Notice Period : </b><?php echo $data['notice_period']." Days";?>&nbsp;
                                            <?php }
if($data['excepted_doj']!=""){  ?>
                                                <b class="f-w-600">Available By : </b><?php echo fromDate($data['excepted_doj']);?>&nbsp;
                                            <?php }
if($data['expected_ctc']!="" && $data['expected_ctc'] !="0"){  ?>
                                                <b class="f-w-600">Expected CTC : </b>
                                                <?php echo customeummarmonyformate($data['expected_ctc']);?>&nbsp;
                                            <?php } ?>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-lg-6 text-right mt-3" id="custom-popup">
                                    <a href="#!" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" class="btn waves-effect waves-light btn-primary mt-5 b-radius-10 " data-action="remove" data-bs-toggle="tooltip" title="Query" onclick="NewMessageAdded('custom-popup-slide-query-contact_details')">
                                        <i class="ti-comment-alt" ></i>
                                    </a>
                                    <div class="custom-popup-open custom-popup-slide-query-contact_details text-start" style="display: none;left: 0px;top: 90px;">
                                        <form method="post" enctype="multipart/form-data" >
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>Query</h5>
                                                    <div class="card-header-right">
                                                        <button type="button" class="btn p-0 border-0 bg-transparent ti-close" onclick="closedPopupcompliance22()" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Closed" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <span class="form-label">Field Group</span>
                                                        <select aria-label="Application Query" class="form-select js-example-basic-single" name="application_query_id">
                                                            <option value="0">Select Any One</option>
                                                            <?php foreach((array)$application_query as $applicationquery){ ?>
                                                                <option   value="<?php echo $applicationquery['id']; ?>"><?php echo $applicationquery['field_group'];  ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <span class="form-label">Query</span>
                                                        <textarea aria-label="Description" type="text" class="form-control" name="description"></textarea>
                                                    </div>
                                                </div>
                                                <div class="card-footer pt-0">
                                                    <input type="hidden" name="rid" value="<?php echo $candidate_id; ?>">
                                                    <input type="hidden" name="display_name" value="<?php echo $_SESSION['display_name']; ?>">
                                                    <input type="hidden" name="parent_id" value="0">
                                                    <button class="input-group-text addon-style cursor-pointer" name="submitlog" ><i class="ti-share"></i>  &nbsp; Submit</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <?php if($data['file']!=""){ ?>
                                        <a href="uploads/all/<?php echo $data['file']; ?>" class="btn waves-effect waves-light btn-primary mt-5 b-radius-10 " download>
                                            <i class="ti-download m-r-10"></i> &nbsp Download CV
                                        </a>
                                    <?php } ?>
                                    <button class="btn waves-effect waves-light btn-primary mt-5 b-radius-10 ">
                                        <div class="image-input image-input-empty image-input-outline" id="kt_image_5_2" >
                                            <form method="post" id="resume_upload_file" enctype="multipart/form-data" class="position-relative">
                                                <label class="btn-hover-text-primary m-0 text-white" data-action="remove" data-bs-toggle="tooltip" title="Upload CV" style="left: -9px;top: 0px;">
                                                    <span> <i class="ti-upload"></i></span>
                                                    <input aria-label="Resume Upload File" type="file" name="resume_upload_file" accept=".pdf"  onchange="updateresume()" />
                                                </label>
                                            </form>
                                        </div>
                                    </button>
                                    <?php if($_SESSION['user_type']!=4){ ?>
                                        <a href="index.php?option=COM_VIEW_CANDIDATE_DETAILS&Rid=<?php echo $data['id']; ?>" target="_blank" class="btn waves-effect waves-light btn-primary mt-5 b-radius-10 ">

                                            <i class="ti-download m-r-10"></i>  Download Profile
                                        </a>
                                    <?php } ?>
                                    <div class="row mt-4">
                                        <div class="col-2 text-center">
                                            <div class="s-caption  text-primary"><i class="ti-list f-20"></i></div>
                                            <div class="s-cont d-inline-block">
                                                <h5 class="f-w-700 m-b-0"><?php echo $task_cnt['total_task_cnt'] !=""?$task_cnt['total_task_cnt']:0; ?></h5>
                                                <p class="m-b-0 f-12">Total Task</p>
                                            </div>
                                        </div>
                                        <div class="col-2 b-l-default text-center">
                                            <div class="s-caption  text-danger"><i class="ti-time f-20"></i></div>
                                            <div class="s-cont d-inline-block">
                                                <h5 class="f-w-700 m-b-0"><?php echo $task_cnt['yet_to_start_cnt'] !=""?$task_cnt['yet_to_start_cnt']:0; ?></h5>
                                                <p class="m-b-0 f-12">Yet To Start</p>
                                            </div>
                                        </div>
                                        <div class="col-2 b-l-default text-center">
                                            <div class="s-caption  text-info"><i class="ti-timer f-20"></i></div>
                                            <div class="s-cont d-inline-block">
                                                <h5 class="f-w-700 m-b-0"><?php echo $task_cnt['inprogress_cnt'] !=""?$task_cnt['inprogress_cnt']:0; ?></h5>
                                                <p class="m-b-0  f-12">In Progress</p>
                                            </div>
                                        </div>
                                        <div class="col-2 b-l-default text-center">
                                            <div class="s-caption  text-info"><i class="ti-write f-20"></i></div>
                                            <div class="s-cont d-inline-block">
                                                <h5 class="f-w-700 m-b-0"><?php echo $task_cnt['review_cnt'] !=""?$task_cnt['review_cnt']:0; ?></h5>
                                                <p class="m-b-0 f-12">Review Pending</p>
                                            </div>
                                        </div>
                                        <div class="col-2 b-l-default text-center">
                                            <div class="s-caption  text-success"><i class="ti-check-box f-20"></i></div>
                                            <div class="s-cont d-inline-block">
                                                <h5 class="f-w-700 m-b-0"><?php echo $task_cnt['approve_cnt'] !=""?$task_cnt['approve_cnt']:0; ?></h5>
                                                <p class="m-b-0 f-12">Approved</p>
                                            </div>
                                        </div>
                                        <div class="col-2 b-l-default text-center">
                                            <div class="s-caption  text-danger"><i class="ti-pulse f-20"></i></div>
                                            <div class="s-cont d-inline-block">
                                                <h5 class="f-w-700 m-b-0 text-danger"><?php echo 0; ?></h5>
                                                <p class="m-b-0 f-12 text-danger">Delayed Task</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row m-t-10  m-b-10">
                                        <div class="font-weight-bold text-dark-50 mt-1 f-14">
                                            <b class="f-w-600">Self tasks : </b><?php echo $task_cnt['self_task_cnt']; ?>&nbsp;&nbsp;&nbsp;
                                            <b class="f-w-600">Review Task : </b><?php echo $task_cnt['riview_cnt']; ?>
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
                        <div class="d-flex overflow-auto h-55px">
                            <ul class="nav md-tabs nav-bold nav-tabs-line nav-line-tabs-3x border-transparent fs-5 fw-bolder flex-nowrap responsive-tab infisuite_tab">

                                <?php $option_name="COM_LIST_CANDIDATE_INFO"; ?>
                                <li class="nav-item">
                                    <button type="button" class="nav-link text-active-primary  removeativtab me-6 active" id="active_COM_LIST_CANDIDATE_INFO" onclick="loadDynamicPage('COM_LIST_CANDIDATE_INFO','<?php echo $_GET['Rid'];?>','<?php echo $_GET['Rid'];?>','<?php echo $list['id']; ?>')">Candidate</button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link text-active-primary  removeativtab me-6 " id="active_COM_INSERT_CANDIDATES_SCREENING_QUESTIONS" onclick="loadDynamicPage('COM_INSERT_CANDIDATES_SCREENING_QUESTIONS','<?php echo $_GET['Rid'];?>','<?php echo $_GET['Rid'];?>','<?php echo $list['id']; ?>')">Screening Questions</button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link text-active-primary  removeativtab me-6 " id="active_COM_VIEW_CANDIDATE_OVERVIEWS" onclick="loadDynamicPage('COM_VIEW_CANDIDATE_OVERVIEWS','<?php echo $_GET['Rid'];?>','<?php echo $_GET['Rid'];?>','<?php echo $list['id']; ?>')">Overview</button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link text-active-primary  removeativtab me-6 " id="active_COM_INSERT_TRANSACTION_ACTIVITY_SCHEDULES" onclick="loadDynamicPage('COM_INSERT_TRANSACTION_ACTIVITY_SCHEDULES','<?php echo $_GET['Rid'];?>','<?php echo $_GET['Rid'];?>','<?php echo $list['id']; ?>')">Task</button>
                                </li>
                                <?php if($_SESSION['user_type'] != 4 && count((array)$job_posting_candidates) !=0){  ?>
                                    <li class="nav-item">
                                        <button type="button" class="nav-link text-active-primary  removeativtab me-6 " id="active_COM_INSERT_CANDIDATE_OFFER" onclick="loadDynamicPage('COM_INSERT_CANDIDATE_OFFER','<?php echo $_GET['Rid'];?>','<?php echo $_GET['Rid'];?>','<?php echo $list['id']; ?>')">Generate LoI</button>
                                    </li>
                                    <?php if($data['current_status']>=ST_LOIGENERATED){ ?>
                                        <li class="nav-item">
                                            <button type="button" class="nav-link text-active-primary  removeativtab me-6 " id="active_COM_LIST_CANDIDATE_OFFER" onclick="loadDynamicPage('COM_LIST_CANDIDATE_OFFER','<?php echo $_GET['Rid'];?>','<?php echo $_GET['Rid'];?>','<?php echo $list['id']; ?>')">View LoI</button>
                                        </li>
                                        <li class="nav-item">
                                            <button type="button" class="nav-link text-active-primary  removeativtab me-6 " id="active_COM_LIST_JOIN_REQUIRED_DOCUMENTS" onclick="loadDynamicPage('COM_LIST_JOIN_REQUIRED_DOCUMENTS','<?php echo $_GET['Rid'];?>','<?php echo $_GET['Rid'];?>','<?php echo $list['id']; ?>')">Generate Employee </button>
                                        </li>
                                    <?php } ?>
                                    <?php } ?>
                                    <?php if($_SESSION['user_type'] == 4 && $data['current_status']>=ST_LOIGENERATED){  ?>
                                        <li class="nav-item">
                                            <button type="button" class="nav-link text-active-primary  removeativtab me-6 " id="active_COM_LIST_CANDIDATE_OFFER_2" onclick="loadDynamicPage('COM_LIST_CANDIDATE_OFFER','<?php echo $_GET['Rid'];?>','<?php echo $_GET['Rid'];?>','<?php echo $list['id']; ?>')">View LoI</button>
                                        </li>
                                    <?php } ?>

                                    <?php if($data['file']!=""){ ?>
                                        <li class="nav-item">
                                            <button type="button" class="nav-link removeativtab" data-bs-toggle="tab" role="tab" onclick="showCV('uploads/all/<?php echo $data['file']; ?>')">CV</button>
                                        </li>
                                    <?php } ?>
                                    <li class="nav-item">
                                        <button type="button" class="nav-link text-active-primary  removeativtab me-6 " id="active_COM_LIST_CANDIDATE_QUERY" onclick="loadDynamicPage('COM_LIST_CANDIDATE_QUERY','<?php echo $_GET['Rid'];?>','<?php echo $_GET['Rid'];?>','<?php echo $list['id']; ?>')">Query</button>
                                    </li>

                                </ul>
                            </div>
                            <!--begin::Navs-->
                        </div>
                    </div>

                    <div class="card mb-5 " id="dynamicpageshow">
                        <?php $_GET['option'] = $option_name;
                        if($_GET['option']!=""){
                            include_once "includes/controller.php";
                            $dispage = str_replace("views/", '', $disptemp);
                            include_once $dispage;
                        }else{
                        }
                        ?>
                    </div>
                <?php  }else{  ?>
                    <div class="card-body pt-9 pb-0 text-center">
                        <h2 class="text-info"><i class='fa fa-warning'></i> Candidate Not Found! Please Contact Admin</h2>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div id="kt_modal_KTDatatable_remote123" class="modal fade" role="dialog" aria-hidden="true">
            <div class="modal-dialog " >
                <div class="modal-content"  id="popup123">
                </div>
            </div>
        </div>

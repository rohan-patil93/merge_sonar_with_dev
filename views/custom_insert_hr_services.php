<?php
$_SESSION['apply_js']['multiselest'] = '1';
$emp_id =$_SESSION['emp_id'];
if(isset($_POST['submit'])){
    $listt = json_decode(urldecode($_POST['filedata']),1);
    $data['company_id'] = $_SESSION['company_id'];
    $data['status_date'] = nowDate();
    $data['emp_id'] = $_POST['emp_id'];
    $data['name'] = $_POST['name'];
    $data['document_id'] = $_POST['document_id'];
    $data['other_document'] = $_POST['other'];
    $data['required_by_date'] = dateconvertdbformat($_POST['required_by_date']);
    $data['response_date'] = dateconvertdbformat($_POST['response_date']);
    $data['comments'] = $_POST['comments'];
    $data['emp_upload'] = imageupload('all','file',$_POST['hidden_file']);
    $data['hr_upload'] = imageupload('all','file2',$_POST['hr_hidden_file']);
    $data['rid'] = $_POST['hid'];
    $data['hr_user_id'] = $_POST['hr_user_id'];
    $url  = API."?mode=inserthrservices&method=post";
    if($_POST['submit'] == 'Add'){
        $data['is_active'] = '0';
        $data['current_status'] = '0';
        $data['created_date'] = nowDate();
        $data['request_date'] = nowDate();
        $data['created_by'] = $_SESSION['userid'];
        $response = file_post_contents($url, $data, 'insert');
        $messge= $_SESSION['error_message']['3000'];
    }
    if($_POST['submit'] == 'update'){
        $data['updated_date'] = nowDate();
        $data['updated_by'] = $_SESSION['userid'];
        $response = file_post_contents($url, $data, 'insert');
        $messge= $_SESSION['error_message']['3001'];
    }
    $response = json_decode($response,1);
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
        ?>
        <script type="text/javascript">
            document.onreadystatechange = function () {
                if (document.readyState == "complete"){
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $messge;?>','');
                }
            }
        </script>
        <?php
    }

}

$rid =  encodeId($_GET['Rid']);
$rid = explode(":",$rid);
$rid = $rid['1'];
$vurl = API."?mode=getemployeehrservice&emp_id=".$emp_id."&rid=".$rid;
$servicecall = callAPI($vurl);
$servicesres = json_decode($servicecall,1);
$services = $servicesres['all_services'];
$documents = $servicesres['company_documents'];
$role = $servicesres['services'];
$buddy_details  = $servicesres['buddy_details'];

$documentname = array();
foreach ($documents as $key => $dd) {
    $values[$dd['id']] = $dd['documnet_path'];
    $documentname[$dd['id']] = $dd['document_name'];
}
$val_data = urlencode(json_encode($values));

$leavelist = array();
foreach($services as $loopdetails){
    if($loopdetails['current_status'] == ST_APPROVED){
        $leavelist[ST_APPROVED][] = $loopdetails;
    }elseif($loopdetails['current_status'] == ST_ACTIVE){
        $leavelist[ST_ACTIVE][] = $loopdetails;
    }elseif($loopdetails['current_status'] == ST_SEND_BACK || $loopdetails['current_status'] == ST_REJECTED){
        $leavelist['rejandsendback'][] = $loopdetails;
    }else{
        $leavelist['inpro'][] = $loopdetails;
    }
}
if(isset($_GET['Rid']) && $_GET['Rid']!=''){
    $emp_id = $role['emp_id'];
    $buddy_details['hr_connect'] =   $role['hr_user_id']!=""?$role['hr_user_id']:$buddy_details['hr_connect'];
    if($role['current_status'] == '10' || $role['current_status'] == '199'){
        $raeonlyfield = "style='pointer-events: none;opacity: 0.7;' readonly";
    }
}

?>
<script type="text/javascript">
    function displayother(val){
        if(val!=-1){
            $("#other").css("display", "none");
        }else{

            $('#other').show();
        }
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

 function startWorkflow(rid,transaction_table,enrid){
     Swal.fire({
         title: '<?php echo $_SESSION['error_message']['1011'];?>',
         showDenyButton: true,
         showCancelButton: false,
         confirmButtonText: 'Yes',
         denyButtonText: `No`,
     }).then((result) => {
         if (result.isConfirmed){
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
                         window.location.href = "index.php?option=COM_INSERT_HR_SERVICES";
                     }
                 }
             });
         }
     })

 }
</script>
<?php $workflowsteps = getWorkflowStatus('emp_hr_services',$role['id'],$role['current_status']); ?>
<div class="flex-column-fluid">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-custom  " id="kt_page_sticky_card">
                    <?php if($role['id'] != ""){ echo $workflowsteps['current_status_ribbon'];
} ?>
                    <div class="card-header ">
                        <div class="card-title">
                            <h5 class="card-label">&nbsp;HR Service Requests  </h5>
                        </div>
                        <div class="card-header-right table-card-header m-r-50 ">


                            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                                <i class="icofont icofont-reply"></i>  Back </a>
                                <?php echo $workflowsteps['workflow_btn']; ?>
                            </div>
                        </div>
                        <div class="card-body">
                             <?php echo $workflowsteps['breadcrumb'];?>
                            <?php
                            if($error_message !=''){ ?>
                                <div class="alert alert-danger background-danger alert-dismissible">
                                    <strong><?php echo $error_message; ?></strong>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php } ?>

                            <div class="col-lg-12">
                                <div class="row">
                                    <?php echo $dis_status=todisplayWorkflowstatuslist(array('table'=>'emp_hr_services','rid'=>$role['id'],'current_status'=>$role['current_status'])); ?>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <form class="kt-form" id="kt_form" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">
                                            <input type="hidden" name="filedata" value="<?php echo $val_data; ?>">
                                            <input type="hidden" name="emp_id" value="<?php echo $emp_id; ?>">


                                            <div class="form-group">
                                                <span class="form-label">Document List</span>
                                                <div class="" <?php echo $raeonlyfield; ?>>
                                                    <select aria-label="Document" class="form-control form-select js-example-basic-single" id="kt_select2_41"  name="document_id" onchange="displayother(this.value)" required>
                                                        <option  value=""> Select Any One</option>
                                                        <?php foreach ($documents as $document) { ?>
                                                            <option <?php if($role['document_id'] ==  $document['id'] ){ echo "selected";
} ?>  value="<?php echo $document['id']; ?>"><?php echo $document['document_name']; ?></option>
                                                        <?php } ?>
                                                        <option <?php if($role['document_id'] ==  -1 ){ echo "selected";
} ?> value="-1"> Others - Please enter the details below</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group " id="other"  <?php if($role['document_id'] !=  '-1' ){ ?> style="display: none;" <?php
} ?>>
                                                <span class="form-label">&nbsp;&nbsp;&nbsp;</span>
                                                <div class="">
                                                    <input aria-label="Please specify the document name" type="text" class="form-control" placeholder="Please specify the document name" name="other" value="<?php echo $role['other_document']; ?>" <?php echo $raeonlyfield; ?>/>
                                                </div>
                                            </div>
                                            <div class="form-group ">
                                                <span class="form-label">Required for (Please give the reason)</span>
                                                <div class="">
                                                    <input aria-label="Name" type="text" class="form-control" name="name"  value="<?php echo $role['name']; ?>" <?php echo $raeonlyfield; ?> required/>
                                                </div>
                                            </div>
                                            <div class="form-group ">
                                                <span class="form-label">Required Date</span>
                                                <div class="">
                                                    <input aria-label="Required By Date" type="text" class="form-control datepicker" name="required_by_date"  value="<?php echo fromDate($role['required_by_date']); ?>" <?php echo $raeonlyfield; ?> required/>
                                                </div>
                                            </div>
                                            <div class="form-group ">
                                                <span class="form-label">Upload Templates Specific Format</span>
                                                <div class="">
                                                    <div class="custom-file mb-2">
                                                        <input aria-label="File" type="file" class="custom-file-input" id="customFile_emp" name="file" <?php echo $raeonlyfield; ?>/>
                                                        <input type="hidden" name="hidden_file" value="<?php echo $role['emp_upload'];?>" />

                                                    </div>
                                                    <?php if($role['emp_upload'] !=""){ ?>
                                                        <a href="uploads/all/<?php echo $role['emp_upload'];?>" download><i class="fa fa-download"></i> <?php echo $role['emp_upload'];?></a>
                                                    <?php } ?>
                                                </div>
                                            </div>

                                            <div class="form-group ">
                                                <span class="form-label">Comments</span>
                                                <div class="">
                                                    <textarea aria-label="Comments" type="text" class="form-control" name="comments" <?php echo $raeonlyfield; ?>><?php echo $role['comments'];?></textarea>
                                                </div>
                                            </div>

                                            <?php
                                            if($rid!="" && $_SESSION['userid']==$role['hr_user_id']){?>
                                                <input type="hidden" name="response_date" value="<?php echo nowDate(); ?>">
                                                <div class="form-group ">
                                                    <span class="form-label">HR Upload</span>
                                                    <div class="">
                                                        <div class="custom-file mb-2">
                                                            <input aria-label="File2" type="file" class="custom-file-input" id="customFile_hr" name="file2" />
                                                            <input type="hidden" name="hr_hidden_file" value="<?php echo $role['hr_upload'];?>" />
                                                        </div>
                                                        <?php if($role['hr_upload'] !=""){ ?>
                                                            <a href="uploads/all/<?php echo $role['hr_upload'];?>" download><i class="fa fa-download"></i> <?php echo $role['hr_upload'];?></a>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <?php if($rid!="" && $role['current_status']==ST_APPROVED){?>
                                                <div class="form-group">
                                                    <span class="form-label">&nbsp;&nbsp;</span>
                                                    <div class="">
                                                        <a href="" class="" target="_blank">
                                                            <img alt=""  class="max-h-65px" src="assets/media/svg/files/pdf.svg" />
                                                        </a>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <div class="card-footer">
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <input type="hidden" name="hr_user_id" id="hr_user_id" value="<?php echo $buddy_details['hr_connect']; ?>">
                                                        <?php  $disablebtn = ""; if($buddy_details['hr_connect']=="0"){  $disablebtn ="disabled"; ?>
                                                        <span class="text-danger f-12"><?php echo $_SESSION['error_message']['9001']; ?></span></br>
                                                    <?php } ?>
                                                    <?php
                                                    if($rid!=""){
                                                        if($role['current_status']==ST_REJECTED || $role['current_status']==ST_SEND_BACK || $role['current_status']==ST_APPROVED){
                                                            $disablebtn ="disabled";
                                                        }
                                                        ?>
                                                        <input type="hidden" name="hid" value=<?php echo $rid; ?> />
                                                        <input type="hidden" name="submit" value="update" />
                                                        <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"   name="submit_value" value="exit" <?php echo $disablebtn;?> <?php echo $role['current_status']==ST_APPROVED?"disabled":""; ?>><i class="la la-check" ></i>Update</button>
                                                    <?php }else{ ?>
                                                        <input type="hidden" name="submit" value="Add" />
                                                        <button id="kt_login_signin_submit_continue" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"   name="submit_value" value="exit" <?php echo $disablebtn;?>><i class="la la-check"></i>Save & exit</button>

                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-lg-8">
                                    <div class="card-block accordion-block color-accordion-block">
                                        <div class="color-accordion" id="color-accordion">
                                            <a class="accordion-msg b-none waves-effect waves-light">
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        Yet To Submit Service Requests
                                                    </div>
                                                    <div class="col-lg-6 text-right">
                                                        <span class="form-label label label-md  label-primary w-100">
                                                            <?php echo count((array)$leavelist[ST_ACTIVE]); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </a>
                                            <div class="accordion-desc p-2">
                                                <div class="table-responsive">
                                                    <?php if(count((array)$leavelist[ST_ACTIVE])!='0'){  ?>
                                                        <table class="table table-hover m-b-0">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col" class="p-2" style="width: 10%;">#</th>
                                                                    <th scope="col" class="p-2" style="width: 25%;">Doc Name</th>
                                                                    <th scope="col" class="p-2" style="width: 25%;">Note</th>
                                                                    <th scope="col" class="p-2" style="width: 30%;">Comments</th>
                                                                    <th scope="col" class="p-2 text-center" style="width: 10%;">Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody classs="custom-scroll" style="height: 250px; display: table-caption;">
                                                                <?php $mk=1; foreach($leavelist[ST_ACTIVE] as $leavel1){ ?>
                                                                    <tr style="display:flex;">
                                                                        <td class="p-2" style="width: 10%;"><?php echo $mk; ?></td>
                                                                        <td class="p-2" style="width: 25%;"><?php echo $documentname[$leavel1['document_id']]; ?></td>
                                                                        <td class=" p-2" style="width: 25%;"><p><?php echo $leavel1['name']; ?></p></td>
                                                                        <td class=" p-2" style="width: 30%;"><p><?php echo $leavel1['comments']; ?></p></td>
                                                                        <td class=" p-2 text-center" style="width: 10%;"><a href="index.php?option=COM_INSERT_HR_SERVICES&Rid=<?php  echo deocdeId("id:".$leavel1['id']); ?>"><i class="fa fa-edit"></i></a></td>
                                                                    </tr>
                                                                    <?php $mk++;
} ?>
                                                                </tbody>
                                                            </table>
                                                        <?php    }else{ ?>
                                                            <div class="card-px text-center py-20 m-t-30">
                                                                <p class="text-warning f-16 fw-bold m-b-30"><i class='fa fa-warning'></i> No Service Requests </p>
                                                                <div class="text-center px-4">
                                                                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                                                                </div>
                                                            </div>

                                                        <?php     } ?>
                                                    </div>
                                                </div>
                                                <a class="accordion-msg b-none waves-effect waves-light">
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            In-Process Service Requests
                                                        </div>
                                                        <div class="col-lg-6 text-right">
                                                            <span class="form-label label label-md  label-primary w-100">
                                                                <?php echo count((array)$leavelist['inpro']); ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </a>
                                                <div class="accordion-desc p-2">
                                                    <div class="table-responsive">
                                                        <?php if(count((array)$leavelist['inpro'])!='0'){  ?>
                                                            <table class="table table-hover m-b-0">
                                                                <thead>
                                                                    <tr>
                                                                        <th scope="col" class="p-2" style="width: 10%;">#</th>
                                                                        <th scope="col" class="p-2" style="width: 30%;">Doc Name</th>
                                                                        <th scope="col" class="p-2" style="width: 30%;">Note</th>
                                                                        <th scope="col" class="p-2" style="width: 30%;">Comments</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody classs="custom-scroll" style="height: 250px; display: table-caption;">
                                                                    <?php $mk=1; foreach($leavelist['inpro'] as $leavel1){ ?>
                                                                        <tr style="display:flex;">
                                                                            <td class="p-2" style="width: 10%;"><?php echo $mk; ?></td>
                                                                            <td class="p-2" style="width: 30%;"><?php echo $documentname[$leavel1['document_id']]; ?></td>
                                                                            <td class=" p-2" style="width: 30%;"><p><?php echo $leavel1['name']; ?></p></td>
                                                                            <td class=" p-2" style="width: 30%;"><p><?php echo $leavel1['comments']; ?></p></td>
                                                                        </tr>
                                                                        <?php $mk++;
} ?>
                                                                    </tbody>
                                                                </table>
                                                            <?php    }else{ ?>
                                                                <div class="card-px text-center py-20 m-t-30">
                                                                    <p class="text-warning f-16 fw-bold m-b-30"><i class='fa fa-warning'></i> No Service Requests </p>
                                                                    <div class="text-center px-4">
                                                                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 250px!important;" />
                                                                    </div>
                                                                </div>

                                                            <?php     } ?>
                                                        </div>
                                                    </div>
                                                    <a class="accordion-msg b-none waves-effect waves-light">
                                                        <div class="row">
                                                            <div class="col-lg-6">
                                                                Approved Service Requests
                                                            </div>
                                                            <div class="col-lg-6 text-right">
                                                                <span class="form-label label label-md  label-primary w-100">
                                                                    <?php echo count((array)$leavelist[ST_APPROVED]); ?>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </a>
                                                    <div class="accordion-desc p-2">
                                                        <div class="table-responsive">
                                                            <?php if(count((array)$leavelist[ST_APPROVED])!='0'){  ?>
                                                                <table class="table table-hover m-b-0">
                                                                    <thead>
                                                                        <tr>
                                                                            <th scope="col" class="p-2" style="width: 10%;">#</th>
                                                                            <th scope="col" class="p-2" style="width: 25%;">Doc Name</th>
                                                                            <th scope="col" class="p-2" style="width: 25%;">Note</th>
                                                                            <th scope="col" class="p-2" style="width: 30%;">Comments</th>
                                                                            <th scope="col" class="p-2 text-center" style="width: 10%;">Action</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody classs="custom-scroll" style="height: 250px; display: table-caption;">
                                                                        <?php $mk=1; foreach($leavelist[ST_APPROVED] as $leavel1){ ?>
                                                                            <tr style="display:flex;">
                                                                                <td class="p-2" style="width: 10%;"><?php echo $mk; ?></td>
                                                                                <td class="p-2" style="width: 25%;"><?php echo $documentname[$leavel1['document_id']]; ?></td>
                                                                                <td class=" p-2" style="width: 25%;"><p><?php echo $leavel1['name']; ?></p></td>
                                                                                <td class=" p-2" style="width: 30%;"><p><?php echo $leavel1['comments']; ?></p></td>
                                                                                <td class=" p-2 text-center" style="width: 10%;"><a href="uploads/all/<?php echo $leavel1['hr_upload'];?>" download><i class="fa fa-download"></i></a></td>
                                                                            </tr>
                                                                            <?php $mk++;
} ?>
                                                                        </tbody>
                                                                    </table>
                                                                <?php    }else{ ?>
                                                                    <div class="card-px text-center py-20 m-t-30">
                                                                        <p class="text-warning f-16 fw-bold m-b-30"><i class='fa fa-warning'></i> No Service Requests </p>
                                                                        <div class="text-center px-4">
                                                                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                                                                        </div>
                                                                    </div>

                                                                <?php     } ?>
                                                            </div>
                                                        </div>
                                                        <a class="accordion-msg b-none waves-effect waves-light">
                                                        <div class="row">
                                                            <div class="col-lg-6">
                                                                Rejected OR Send Back Service Requests
                                                            </div>
                                                            <div class="col-lg-6 text-right">
                                                                <span class="form-label label label-md  label-primary w-100">
                                                                    <?php echo count((array)$leavelist['rejandsendback']); ?>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </a>
                                                    <div class="accordion-desc p-2">
                                                        <div class="table-responsive">
                                                            <?php if(count((array)$leavelist['rejandsendback'])!='0'){  ?>
                                                                <table class="table table-hover m-b-0">
                                                                    <thead>
                                                                        <tr>
                                                                            <th scope="col" class="p-2" style="width: 10%;">#</th>
                                                                            <th scope="col" class="p-2" style="width: 25%;">Doc Name</th>
                                                                            <th scope="col" class="p-2" style="width: 25%;">Note</th>
                                                                            <th scope="col" class="p-2" style="width: 30%;">Comments</th>
                                                                            <th scope="col" class="p-2 text-center" style="width: 10%;">Action</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody classs="custom-scroll" style="height: 250px; display: table-caption;">
                                                                        <?php $mk=1; foreach($leavelist['rejandsendback'] as $leavel1){ ?>
                                                                            <tr style="display:flex;">
                                                                                <td class="p-2" style="width: 10%;"><?php echo $mk; ?></td>
                                                                                <td class="p-2" style="width: 25%;"><?php echo $documentname[$leavel1['document_id']]; ?></td>
                                                                                <td class=" p-2" style="width: 25%;"><p><?php echo $leavel1['name']; ?></p></td>
                                                                                <td class=" p-2" style="width: 30%;"><p><?php echo $leavel1['comments']; ?></p></td>
                                                                                <td class=" p-2 text-center" style="width: 10%;"><a href="uploads/all/<?php echo $leavel1['hr_upload'];?>" download><i class="fa fa-download"></i></a></td>
                                                                            </tr>
                                                                            <?php $mk++;
} ?>
                                                                        </tbody>
                                                                    </table>
                                                                <?php    }else{ ?>
                                                                    <div class="card-px text-center py-20 m-t-30">
                                                                        <p class="text-warning f-16 fw-bold m-b-30"><i class='fa fa-warning'></i> No Service Requests </p>
                                                                        <div class="text-center px-4">
                                                                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                                                                        </div>
                                                                    </div>

                                                                <?php     } ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <?php

                                            echo $todisplaythree = todisplaythree(array('table'=>'employee_leaves','originator'=>$_SESSION['userid'],'encode'=>'no','id'=>$rid,'dis_query'=>'yes','dis_timeline'=>'yes','dis_comments'=>'yes'));
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
<?php if(isset($_POST['ajaxpage']) && $_POST['ajaxpage'] == 'Y'){ ?>
            <script type="text/javascript" src="assets/pages/accordion/accordion.js"></script>
            <?php } ?>

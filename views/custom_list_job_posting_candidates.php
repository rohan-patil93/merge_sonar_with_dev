<?php
if (isset($_POST['submit1']) && $_POST['submit1'] == 'Add') {
        $_POST['created_by'] = $_SESSION['userid'];
        $_POST['created_date'] = nowDate();
        $url = API."?mode=addjobpostingapplicatantdata&method=post";
        $response = file_post_contents($url, $_POST, 'insert');
        if ($response) {
            header("location:index.php?option=".$option."&job=".$_POST['job_posting']);
        }
    }

if($_GET['Rid']!=""){
    $rid =  encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $ex_rid = explode(':', $rid);
    $jobpost = $ex_rid['1'];
}else{
    $jobpost = $_GET['job'];
}

$roleshortcodes  = json_encode($_SESSION['role_ids']);
$apiurl = API."?mode=getjobpostinglist&roleshortcode=".$roleshortcodes."&user_type=".$_SESSION['user_type']."&vendor_id=".$_SESSION['vendor_id'];
$callapi = callAPI($apiurl);
$result11 = json_decode($callapi,1);
$result = $result11['job_postings'];
$jobcodedata = $result11['job_titles'];
$job_posting_distributes = $result11['job_posting_distribute'];
foreach ($jobcodedata as $codedata) {
    $code[$codedata['id']] =  $codedata['job_title'];
}

if($_SESSION['user_type']==3){
    $mulcheck= array();
    foreach($job_posting_distributes as $job_posting_distribute){
        $mulcheck[] = $job_posting_distribute['job_posting_id'];
    }
}
foreach($result as $res){
    if($_SESSION['user_type']==3){
        if($res['job_posting_distribute_type']==1){
            $data1[$res['id']] = $res;
        }
        if ($res['job_posting_distribute_type']==2 && in_array($res['id'],$mulcheck)) {
                $data1[$res['id']] = $res;
            }
    }else{
        $data1[$res['id']] = $res;
    }
}
$result = $data1;
$stausresult = ViewStatusData();
$aciveid = $_GET['JOBACT'];

$statuslist  = getstatusprogresslist('job_postings');
foreach($statuslist as $statuslist){
    $statusnamelist[$statuslist['status']] = $statuslist['name'];
}

?>
<script type="text/javascript">
    function removerows(){
        $(".itemRow:checked").each(function() {
            console.log(this);
            $(this).closest('tr').remove();
        });
        $('#checkAll').prop('checked', false);
    }
    function changepageview(val){
       window.location.href = 'index.php?option=COM_INSERT_JOB_POSTING_CANDIDATES&job='+val;
   }
   function displaystausdrop(id,table,status_id){
        $.ajax({
            url: "ajax.php",
            data:'page=current_status_drop&id='+id+'&current_status='+status_id+'&table='+table,
            type: "POST",
            success:function(data){
                console.log(data);
                $("#dropdownlist_"+id).html(data);
            },
            error:function (){}
        });

    }
    function statuspopup(status,id){
      var table_name = 'job_posting_candidates';
      jQuery.ajax({
          url: "views/custom_insert_status_popup.php",
          data:'table='+table_name+'&status='+status+'&rid='+id,
          type: "POST",
          success:function(data){
              $('#popup1').html(data);
              $('#kt_modal_KTDatatable_remote').modal({ show: true});
          },
          error:function (){}
      });
  }
  function addrows(){
        var count = $(".itemRow").length;
        count++;
        var htmlRows = '';
        htmlRows += '<tr>';
        htmlRows += '<td><label class="kt-checkbox"> <input aria-label="Checkbox"  class="itemRow" type="checkbox">  <span> </span> </label> </td>';
        htmlRows += '<td><input aria-label="Name[]" type="text" class="form-control" name="name[]" value=""></td> ';
        htmlRows += '<td><input aria-label="Email[]" type="text" class="form-control" name="email[]" value=""> </td>';
        htmlRows += '<td><input aria-label="Phone[]" type="text" class="form-control" name="phone[]" value=""></td>';
        htmlRows += '<td><select aria-label="Process[]" class="form-control" name="process[]" ><option value="0">Select Any One</option>    <?php foreach ($spdata as  $sp) { ?><option  value="<?php echo $sp[id]; ?>"><?php echo $sp[select_name]; ?></option><?php
} ?></select></td>';
        htmlRows += '<td><textarea aria-label="Remarks[]" class="form-control" name="remarks[]" ></textarea> </td>';
        htmlRows += '<td><select aria-label="Status[]" class="form-control" name="status[]"><option value="0">Select Any One</option><option value="0">Active</option></select></td>';
        htmlRows += '<td><a herf="#" class="btn btn-primary mr-2" disabled data-container="body" data-toggle="popover" data-placement="top" data-content="Create Candidate Details"  onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"> Infrom </a></td onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">';
        htmlRows += '</tr>';
        $('#invoiceItem').append(htmlRows);
    }
    function genrateregistation(name,email,phone){

                jQuery.ajax({
                    url: "ajax.php",
                    data:'page=candidateautoregistation&display_name='+name+'&user_mobile='+phone+'&user_email='+email+'&job_title_id'+$('#job_title_id').val(),
                    type: "POST",
                    success:function(data){
                        console.log(data);
                        data =  JSON.parse(data);
                        console.log(data.status);
                        var mms = <?php echo "'".$_SESSION['error_message']['9005']."'" ?>;
                        if(data.status == 'fail'){
                            if(data.type != 'mobile'){
                                mms = <?php echo "'".$_SESSION['error_message']['9004']."'" ?>;
                            }else{
                                mms = <?php echo "'".$_SESSION['error_message']['9006']."'" ?>;
                            }
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
</script>
<style>
.custom-share{
    top: 2px;
    position: absolute;
    left: 10px;
    z-index: 9;
}
</style>
<div class="d-flex flex-column-fluid card">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <form class="kt-form" id="kt_form" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">
                    <div class="" id="kt_page_sticky_card">
                        <div class="card-header mb-3">
                            <h5>Job Posting Applicants</h5>
                            <div class="card-header-right">
                                <?php if($_SESSION['user_type'] !=3){ ?>
                                    <a href="index.php?option=COM_INSERT_JOB_POSTINGS" class="btn btn-success "> <i class="icofont icofont-plus icon-white"></i>New Job Posting</a>
                                <?php } ?>
                                <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                                    <i class="icofont icofont-reply"></i>
                                    Back
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row" >
                                <div class="col-lg-12">
                                    <ul class="nav md-tabs infisuite_tab">
                                        <li class="nav-item">
                                            <a class="nav-link f-w-600 <?php if($aciveid=="-1"){ echo "active";
} ?>" data-bs-toggle="tab" href="#total" role="tab" aria-selected="true">Total Jobs</a>
                                            <div class="slide"></div>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link f-w-600 <?php if($aciveid=='0' || $aciveid==''){ echo "active";
} ?>"  data-bs-toggle="tab" href="#open" role="tab" aria-selected="false"> Open Jobs </a>
                                            <div class="slide"></div>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link f-w-600 <?php if($aciveid==1){ echo "active";
} ?>" data-bs-toggle="tab" href="#close" role="tab" aria-selected="false"> Close Jobs </a>
                                            <div class="slide"></div>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link f-w-600 <?php if($aciveid==2){ echo "active";
} ?>" data-bs-toggle="tab" href="#hold" role="tab" aria-selected="false"> Hold Jobs </a>
                                            <div class="slide"></div>
                                        </li>
                                    </ul>
                                </div>
                                <?php $checkjobs = array('0'=>'open','1'=>'close','2'=>'hold'); ?>
                                <div class="tab-content tabs card-body">
                                    <div class="tab-pane <?php if($aciveid=="-1"){ echo "active";
} ?> " id="total" role="tabpanel">
                                        <div class="col-lg-12 row">
                                            <?php if(count((array)$result) !=0){ $m=1;
                                                foreach ($result as  $res) {
                                                $nridw = "id:".$res['id'];
                                                $uimg = $_SESSION['user_image'][$res['requested_by']]!=""?$_SESSION['user_image'][$res['requested_by']]:"no-image.jpg";
                                                ?>
                                                <div class="col-xl-3 col-md-6">
                                                    <div class="card sale-card ">
                                                        <?php
                                                        $statucolorname = $stausresult[$res['current_status']];
                                                        ?>
                                                        <div class="ribbon ribbon-top-right" >
                                                            <span class="color-primary" style="color: <?php echo $statucolorname['color']; ?>;    background:  <?php echo $statucolorname['background_color']; ?>; border-color: <?php echo $statucolorname['background_color']; ?>;"><?php echo $statusnamelist[$res['current_status']]; ?></span>
                                                        </div>

                                                        <div class="">
                                                            <a href="index.php?option=COM_INSERT_JOB_POSTING_DISTRIBUTE&POST=<?php echo $res['id'] ?>" target="_blank" class=" btn-hover-light-primary custom-share" ><i class="fa fa-share-alt"></i></a>
                                                        </div>
                                                        <div class="card-header mt-2">
                                                            <h5><?php echo $code[$res['job_code']]; ?></h5>
                                                        </div>
                                                        <div class="card-block text-center">
                                                            <div data-label="40%" class="radial-bar radial-bar-40 radial-bar-lg radial-bar-danger">
                                                                <img src="uploads/all/<?php echo $uimg; ?>" alt="User">
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-6">
                                                                    <?php if($res['job_status']==1){ ?>
                                                                        <div class="s-caption bg-danger" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Closed"></div>
                                                                    <?php }elseif($res['job_status']==2){ ?>
                                                                        <div class="s-caption bg-warning" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Hold"></div>
                                                                    <?php }else{ ?>
                                                                        <div class="s-caption bg-info" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Open"></div>
                                                                    <?php } ?>
                                                                    <div class="s-cont d-inline-block">
                                                                        <h5 class="f-w-700 m-b-0 f-14"><?php echo initials($code[$res['job_code']]); ?></h5>
                                                                        <p class="m-b-0 f-12"><?php echo $res['job_number']; ?></p>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6 b-l-default">
                                                                    <a href="index.php?option=COM_LIST_JOB_POSTING_CANDIDATE&Rid=<?php echo  deocdeId($nridw); ?>" class="btn btn-info waves-effect waves-light  f-12">view</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php  $m++;
}
                                            }else{ ?>
                                    <div class="card-px text-center py-20 m-t-30">
                                        <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Jobs Posting </p>
                                        <div class="text-center px-4">
                                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                        </div>
                                    </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <?php foreach($checkjobs  as $ckey=>$checkjob){ ?>
                                    <div class="tab-pane <?php if($aciveid==$ckey){ echo "active";
} ?>  " id="<?php echo $checkjob; ?>" role="tabpanel">
                                        <div class="col-lg-12 row">
                                            <?php if(count((array)$result) !=0){ $m=1;
                                                foreach ($result as  $res) {

                                                $statucolorname = $stausresult[$res['current_status']];

                                                if($res['job_status']==$ckey){
                                                $nridw = "id:".$res['id'];
                                                $uimg = $_SESSION['user_image'][$res['requested_by']]!=""?$_SESSION['user_image'][$res['requested_by']]:"no-image.jpg";
                                                ?>
                                                <div class="col-xl-3 col-md-6">
                                                    <div class="card sale-card ">
                                                        <?php
                                                        $statucolorname = $stausresult[$res['current_status']];
                                                        ?>
                                                        <div class="ribbon ribbon-top-right" >
                                                            <span class="color-primary" style="color: <?php echo $statucolorname['color']; ?>;    background:  <?php echo $statucolorname['background_color']; ?>; border-color: <?php echo $statucolorname['background_color']; ?>;"><?php echo $statusnamelist[$res['current_status']]; ?></span>
                                                        </div>
                                                        <div class="">
                                                            <a href="index.php?option=COM_INSERT_JOB_POSTING_DISTRIBUTE&POST=<?php echo $res['id'] ?>" target="_blank" class=" btn-hover-light-primary custom-share" ><i class="fa fa-share-alt"></i></a>
                                                        </div>
                                                        <div class="card-header mt-2">
                                                            <h5><?php echo $code[$res['job_code']]; ?></h5>
                                                        </div>
                                                        <div class="card-block text-center">
                                                            <div data-label="40%" class="radial-bar radial-bar-40 radial-bar-lg radial-bar-danger">
                                                                <img src="uploads/all/<?php echo $uimg; ?>" alt="User">
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-6">
                                                                    <?php if($res['job_status']==1){ ?>
                                                                        <div class="s-caption bg-danger" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Closed"></div>
                                                                    <?php }elseif($res['job_status']==2){ ?>
                                                                        <div class="s-caption bg-warning" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Hold"></div>
                                                                    <?php }else{ ?>
                                                                        <div class="s-caption bg-info" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Open"></div>
                                                                    <?php } ?>
                                                                    <div class="s-cont d-inline-block">
                                                                        <h5 class="f-w-700 m-b-0 f-14"><?php echo initials($code[$res['job_code']]); ?></h5>
                                                                        <p class="m-b-0 f-12"><?php echo $res['job_number']; ?></p>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6 b-l-default">
                                                                    <a href="index.php?option=COM_LIST_JOB_POSTING_CANDIDATE&Rid=<?php echo  deocdeId($nridw); ?>" class="btn btn-info waves-effect waves-light  f-12">view</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php  $m++;
}
}
                                            }else{ ?>
                                    <div class="card-px text-center py-20 m-t-30">
                                        <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Jobs Posting </p>
                                        <div class="text-center px-4">
                                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                        </div>
                                    </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <?php }?>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

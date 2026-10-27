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
$reswrdata["where"] = array('company_id'=> $_SESSION['company_id'],'job_posting_id'=> $jobpost,'is_active'=>0);
$rescondata = urlencode(json_encode($reswrdata));
$resurll = API."?mode=gettabledetailswithconditions&table=job_posting_candidates&data=".$rescondata;
$resdata = callAPI($resurll);
$resdata = json_decode($resdata,1);

$jobcode["where"] = array('company_id'=> $_SESSION['company_id'],'is_active'=>0);
$jobcodejson = urlencode(json_encode($jobcode));
$jobcodeurl = API."?mode=gettabledetailswithconditions&table=job_titles&data=".$jobcodejson;
$jobcodedata = callAPI($jobcodeurl);
$jobcodedata = json_decode($jobcodedata,1);

foreach ($jobcodedata as $codedata) {
    $code[$codedata['id']] =  $codedata['job_title'];
}
$data11["where"] = array('company_id'=> $_SESSION['company_id'],'is_active'=>0,'recruitment_manager'=>$_SESSION['userid']);
$datw = urlencode(json_encode($data11));
$curl1 = API."?mode=gettabledetailswithconditions&table=job_postings&data=".$datw;
$cudata1 = callAPI($curl1);
$result = json_decode($cudata1,1);

if($jobpost  !=''){
    foreach ($result as $checkvalue) {
        if($checkvalue['id'] == $jobpost){
            $processdata = $checkvalue;
            break;
        }
    }
    $datass["where"] = array('company_id'=> $_SESSION['company_id'],'is_active'=>0,'process_id'=>$processdata['job_selection_process']);
    $datasw = urlencode(json_encode($datass));
    $curlss = API."?mode=gettabledetailswithconditions&table=job_selection_process_steps&data=".$datasw;
    $spdata = callAPI($curlss);
    $spdata = json_decode($spdata,1);
}

$stausresult = ViewStatusData();
$count_check = count((array)$spdata)-1;
$vdata11["where"] = array('company_id '=> '1');
$vdata11["field"] = "id,display_name,user_photo";
$userlisturldata = urlencode(json_encode($vdata11));
$userlisturl = API."?mode=gettabledetailswithconditions&table=application_users&data=".$userlisturldata;
$userlist = callAPI($userlisturl);
$userlist = json_decode($userlist,1);

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
                $("#kt_modal_KTDatatable_remote").modal("show");
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
        htmlRows += '<td><button type="button" herf="#" class="btn btn-primary mr-2" disabled data-container="body" data-toggle="popover" data-placement="top" data-content="Create Candidate Details"> Infrom </button></td>';
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
<div class="d-flex flex-column-fluid j-wrapper">
    <div class="container-fluid j-pro">
        <div class="row j-content">
            <div class="col-lg-12">
                <form class="kt-form" id="kt_form" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">
                    <div class="card card-custom" id="kt_page_sticky_card">
                        <div class="card-header">
                            <h5>Job Posting Applicants</h5>
                            <div class="card-header-right">

                                <button type="button" class="btn btn-primary waves-effect waves-ligh"> <i class="ti-arrow-left text-white"></i> Back</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                            <?php $m=1; foreach ($result as  $res) {
                                $nrid = "id:".$res['id'];
                                $userlistsss = filterArrayValue($userlist,"id",$res['requested_by']);
                                $userlistsss = $userlistsss[0];
                                $uimg = $userlistsss['user_photo']!=""?$userlistsss['user_photo']:"no-image.jpg";
                                ?>
                                <div class="col-xl-3 col-md-6">

                                    <div class="card sale-card ">

                                        <div class="card-header">
                                            <h5><?php echo $code[$res['job_code']]; ?></h5>
                                        </div>

                                        <div class="card-block text-center">
                                            <div data-label="40%" class="radial-bar radial-bar-40 radial-bar-lg radial-bar-danger">
                                                <img src="uploads/all/<?php echo $uimg; ?>" alt="User">
                                            </div>
                                            <div class="row">
                                                <div class="col-6">
                                                    <?php if($res['job_status']==1){ ?>
                                                        <div class="s-caption bg-danger"></div>
                                                    <?php }else{ ?>
                                                        <div class="s-caption bg-info"></div>
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
                                <?php $m++; if($m=='8'){ $m=1; ?>
                                <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

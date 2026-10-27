<?php

include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

$url = API."?mode=getdynamicmoduledashboard&transaction_table=".$_POST['transaction_table']."&viewallpopup=Y&type=".$_POST['type']."&admin_type=".$_SESSION['admin_type'];
$apires = callAPI($url);
$result = json_decode($apires,1);
$category_data = $result['category_data'];
$categorydatalist = array();
foreach($category_data as $category_data){
    $categorydatalist[$category_data['category_id']][] = $category_data;
}

$statuse = ViewStatusData();
$list = $result['status_table_list'];

$createtransactioncnt = $createtransactionapprovedcnt = 0;
foreach($result['transaction_approval_logs'] as $transactionlog){
    if($transactionlog['response_type'] =='0' && $transactionlog['send_back_flag'] == '0' && $transactionlog['call_back'] == '1'){
        $createtransactioncnt++;
        $pendinglog[$transactionlog['transaction_id']] = $transactionlog;
    }elseif($transactionlog['response_type'] == ST_SEND_BACK){
        $sendbacklogcheck[$transactionlog['transaction_id']] = $transactionlog;
    }elseif($transactionlog['response_type'] == ST_REJECTED){
        $rejectlogcheck[$transactionlog['transaction_id']] = $transactionlog;
    }else{
        $createtransactionapprovedcnt++;
        $aprovedlog[$transactionlog['transaction_id']] = $transactionlog;
    }
}
foreach($sendbacklogcheck as $transid => $transactionlog){
    if(!isset($pendinglog[$transid]) && !isset($aprovedlog[$transid])){
        $sendbacklog[$transid] = $transactionlog;
    }
}
foreach($rejectlogcheck as $transid => $transactionlog){
    if(!isset($pendinglog[$transid]) && !isset($aprovedlog[$transid])){
        $rejectlog[$transid] = $transactionlog;
    }
}

if($_POST['type'] == 'APPROVEPENDING'){
    $list = $pendinglog;
}elseif($_POST['type'] == 'SENDBACK'){
    $list = $sendbacklog;
}elseif($_POST['type'] == 'REJECTED'){
    $list = $rejectlog;
}elseif($_POST['type'] == 'APPROVELOG'){
    $list = $aprovedlog;
}

$allpermision = $_SESSION['role_permission'][$_POST['transaction_table']];
if($allpermision['permission_update'] == '1'){$edit_permissionbtn = "disabled";
}
?>
<script type="text/javascript">
    function displaystausdrop(id,table,status_id,tab){
        $.ajax({
            url: "ajax.php",
            data:'page=current_status_drop_table&id='+id+'&current_status='+status_id+'&table='+table,
            type: "POST",
            success:function(data){
                console.log(data);
                $("#dropdownlist_"+tab+"_"+id).html(data);
            },
            error:function (){}
        });
    }
    function statuspopup(status,id,table_name){
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
<input type="hidden" name="job_posting_id" id="job_posting_id" value="<?php echo $jobpost; ?>">
<div class="modal-header">
    <h5 class="modal-title">
        <?php echo ucwords(str_replace("_"," ",($_POST['transaction_table']))); ?> List
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">

        <div class="row" style="position: sticky;top: 0px;">
            <div class="col-lg-8">&nbsp;</div>
            <div class="col-lg-4 " >
                <div class="input-group input-group-button mb-0" >
                    <input aria-label="Search" type="text" id="dynamicfilterhideshow" data-id='categoryfilter_popup' class="form-control form-control-sm width1"  placeholder="Search" data-bs-toggle="tooltip" data-placement="bottom" title="Search">
                    <span class="input-group-text addon-style">
                        <span class=""><i class="ti-search"></i></span>
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body custom-scroll" style=" height: 450px;">


            <div class="table-responsive">
                <table class="table table-hover m-b-0" style="font-size:13px;" id="categoryfilter_popup_2">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Transaction No.</th>
                            <th scope="col">Originator</th>
                            <th scope="col">Date</th>
                            <th scope="col">Party Name</th>
                            <th scope="col" class="text-right">Amount</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $mk=1; if(count((array)$list) !=0){ foreach($list as $list){
                            $rrrid =  $list['transaction_id'] !=""?$list['transaction_id']:$list['id'];
                            ?>
                            <tr>
                                <td><?php echo $mk;  ?></td>
                                <td><a href="index.php?option=<?php echo $_POST['optionurl']; ?>&Rid=<?php echo deocdeId('id:'.$rrrid); ?>"  target="_blank" <?php echo $edit_permissionbtn; ?>><?php echo $list['transaction_number']; ?></a></td>
                                <td class="w-b-w-s"><?php echo $_SESSION['users'][$list['originator']]; ?></td>
                                <td><?php echo fromDate($list['transaction_date']); ?></td>
                                <td class="w-b-w-s"><?php echo $list['party_name']; ?></td>
                                <td class="text-right"><?php echo customeummarmonyformateifine($list['total_amount']); ?></td>
                                <td>
                                    <span class="form-label label label-md" style="color:<?php echo $statuse[$list['current_status']]['color']; ?>; background-color:<?php echo $statuse[$list['current_status']]['background_color']; ?>;"><?php echo $statuse[$list['current_status']]['name']; ?></span>
                                </td>
                            </tr>
                        <?php $mk++;
}
}else{ ?>
                        <tr>
                            <td colspan="8">
                                <div class="card-px text-center ">
                                    <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No  <?php echo ucwords(str_replace("_"," ",($_POST['transaction_table']))); ?></p>
                                    <div class="text-center px-4">
                                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>


        <div class="modal-footer">
            <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
        </div>

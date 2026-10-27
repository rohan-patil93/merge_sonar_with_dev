<?php

    $wh1["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id'],'process_type'=>'C');
    $wh1["field"] = "id,task_name,action_frequency,critical_compliance";
    $data1 = urlencode(json_encode($wh1));
    $url1 = API."?mode=gettabledetailswithconditions&table=compliances&data=".$data1;
    $complianceres = callAPI($url1);
    $compliancelist = json_decode($complianceres,1);

    $wh2["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id'],'genc_code'=>'RECURRENCE');
    $wh2["field"] = "id,genc_detail_name,genc_detail_code";
    $data2 = urlencode(json_encode($wh2));
    $url2 = API."?mode=gettabledetailswithconditions&table=general_code_details&data=".$data2;
    $actionfrequencyres = callAPI($url2);
    $actionfrequencydeat = json_decode($actionfrequencyres,1);
    foreach($actionfrequencydeat as $frequency){
        $frequencyary[$frequency['genc_detail_code']] = $frequency['genc_detail_name'];
    }

?>
<script>
function selectallcheck(){
        if($('#selectall_checks').is(':checked')){
            $('.checkboxassets').each(function(){
                this.checked = true;
            });
        }else{
            $('.checkboxassets').each(function(){
                this.checked = false;
            });
        }
    }


function setcompliance(){
    $('#modal_allocation_complince').modal("show");
}


    function taskAllocationSubmit(){
        var start_date =  $("#popup_start_date").val();
        var end_date =  $("#popup_end_date").val();
        jQuery.ajax({
            url: "ajax.php",
            data: "page=setcomplianceallocation&start_date="+start_date+"&end_date="+end_date,
            type: "POST",
            success:function(data){
                console.log(data);
                $('#modal_allocation_complince').modal("hide");
            }
        });
    }


    </script>
<div class="pcoded-inner-content">
  <div class="main-body">
    <div class="page-wrapper">
     <!-- Page-body start -->
     <div class="page-body">
    <div class="card">
        <div class="card-header ">
            <h5>Allocation Compliance</h5>
            <div class="card-header-right">
                <button type="button" onclick="setcompliance()" class="btn btn-success ">
                Task Allocation
                </button>
                <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                  <i class="icofont icofont-reply"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
        <div class="dt-responsive table-responsive">
    <table id="basic-btn" class="table table-striped table-bordered nowrap">
        <thead>
            <tr>

                <th scope="col">Compliance Name</th>
                <th scope="col">Action Frequency</th>
                <th scope="col">Critical Compliance</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($compliancelist as $compliancelist){ ?>
                    <tr class="<?php echo $varianc_class; ?>">

                    <td><?php echo $compliancelist['task_name']; ?></td>
                    <td><?php echo $frequencyary[$compliancelist['action_frequency']]; ?></td>
                    <td><?php echo $compliancelist['critical_compliance']=="N"?'No':'Yes'; ?></td>
                    <td>
                        <a href="index.php?option=COM_INSERT_COMPLIANCES&TBL=compliances&Rid=<?php echo deocdeId("id:".$compliancelist['id']); ?>" target="_blank" class="btn btn-primary waves-effect waves-light" ><i class="fa fa-edit"></i> Edit</a>
                    </td>
                </tr>
            <?php } ?>

            </tbody>
        </table>
    </div>
        </div>
    </div>
 </div>
</div>
 </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modal_allocation_complince" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title modallabel" id="exampleModalLabel">Task Allocation Period</h5>
            </div>
            <div class="card-body">
                <div class="form-group row">
                   <label for="example-date-input-from" class="col-4 col-form-label">Start Date</label>
                   <div class="col-5">
                      <input aria-label="Popup Start Date" class="form-control datepicker" type="text" id="popup_start_date" value="<?php echo fromDate(date('d-m-Y'))?>"  id="example-date-input-1">
                   </div>
                   <label for="example-date-input-to" class="col-4 col-form-label mt-2">End Date</label>
                   <div class="col-5 mt-2">
                      <input aria-label="Popup End Date" class="form-control datepicker" type="text" id="popup_end_date" value="<?php echo fromDate(date('d-m-Y',strtotime('+1 year')))?>" id="example-date-input-2">
                   </div>
                </div>
            </div>
        <div class="modal-footer">
        <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal" >Cancel</button>
        <button type="button" class="btn btn-primary " onclick="taskAllocationSubmit()" >Submit</button>
    </div>
    </div>


    </div>
  </div>
</div>

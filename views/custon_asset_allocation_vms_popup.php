<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
        $wh1["where"] = array('is_active'=>0,'company_id'=>$_SESSION['company_id'],'allocation_status'=>'N');
        $wh1["field"] = "id,asset_description";
        $data1 = urlencode(json_encode($wh1));
        $url1 = API."?mode=gettabledetailswithconditions&table=assets&data=".$data1;
        $assetlists = callAPI($url1);
        $assetlist = json_decode($assetlists,1);


    $url = API."?mode=getassetallocationvms&employee=".$_POST['emp_id'];
    $data_details = callAPI($url);
    $asedetails = json_decode($data_details,1);
    $aaetlist = $asedetails['list_assets'];
    $asset_allocation_status= array('0'=>'allocate','1'=>'Accepted','2'=>'Rejected','3'=>'Return');

?>
<script type="text/javascript">
    function submitAsset(){
        $("#selectasset").text("");
    if($("#asset_id_popup").val() == ""){

        $("#selectasset").text("Please Select Asset");
        return false;
    }
     var dataArray = $("#assetsallocationform").serialize()
       jQuery.ajax({
            url: "ajax.php",
            data: "page=assetallocationaws&"+dataArray,
            type: "POST",
            success:function(data){
                console.log(data);
                $('#kt_modal_dynamic_popup2').modal("hide");
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Asset Allocation Successfully','');
            },
            error:function (){}
        });
    }

</script>

<div class="modal-header">
    <h4 class="modal-title">Asset Allocation</h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body j-pro">
    <div class="j-content">

<div class="dt-responsive table-responsive">
    <table id="basic-btn" class="table table-striped table-bordered nowrap">
        <thead>
            <tr>
                <th scope="col">
                </th>
                <th scope="col">Asset Name</th>
                <th scope="col">Start Date</th>
                <th scope="col">End Date</th>
                <th scope="col">Remarks</th>
                <th scope="col">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $i=1; if(count((array)$aaetlist) !="0"){ foreach($aaetlist as $list_asset){
                ?>
                    <tr class="<?php echo $varianc_class; ?>">
                        <td class="text-center">

                            <?php echo $i;?>
                        </td>
                        <td><?php echo $list_asset['asset_description']; ?></td>
                        <td><?php echo $list_asset['start_date']; ?></td>
                        <td><?php echo $list_asset['end_date']; ?></td>
                        <td><?php echo $list_asset['remarks']; ?></td>
                        <td><?php echo $list_asset['allocation_type'] != ""?'<span class="form-label label label-warning">'.$asset_allocation_status[$list_asset['allocation_type']].'</span>':""; ?>
                    </td>

                </tr>
            <?php  $i++;
}
}else{ ?>
                <tr><td class="text-center" colspan="5">
                    <h4 class="text-center"><i class='fa fa-warning'></i> No Asset Allocated</h4>
                    </td>
                </tr>
        <?php    } ?>

            </tbody>
        </table>
    </div>

    <form id="assetsallocationform" method="post" >
    <input type="hidden" name="employee_id"  value="<?php echo $_POST['emp_id']; ?>">
    <div class="card p-2 mt-2">
        <div class="row">
            <div class="form-group col-lg-4 select_emp " >
                <span>Asset List<span class="requried">*</span></span>
                <select aria-label="Asset" class="form-select js-example-basic-single " name="asset_id" id="asset_id_popup">
                        <option value="">Select Any One</option>
                <?php foreach ($assetlist as $assetlistd){ ?>
                    <option  value="<?php echo $assetlistd['id']; ?>" ><?php echo ucwords($assetlistd['asset_description']); ?></option>
                <?php } ?>
                </select>
                <span id="selectasset" class="text-danger"></span>
            </div>
            <div class="form-group col-lg-4">
                <span>Start date</span>
                <input aria-label="Start Date" type="text" class="form-control datepicker" id="start_date"  name="start_date" value="<?php echo fromDate(); ?>">
            </div>
            <div class="form-group col-lg-4">
                <span>End date</span>
                <input aria-label="End Date" type="text" class="form-control datepicker" id="end_date" name="end_date" >
            </div>
            <div class="form-group col-lg-12">
                <span>Remarks</span>
                <textarea aria-label="Remarks" type="text" class="form-control" id="remarks" rows="4" cols="50" name="remarks" ></textarea>
            </div>
        </div>
    </div>

    </form>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary waves-effect waves-light"  onclick="submitAsset()">Submit</button>
</div>

<script src="assets/pages/data-table/extensions/responsive/js/responsive-custom.js"></script>
<script type="text/javascript">
    $(".js-example-basic-single").select2();
    $(".js-example-disabled-results").select2();
    $( ".datepicker" ).datepicker({
        showOn: "both",
        buttonText: "<i class='ti-calendar'></i>",
        dateFormat: date_format,
        changeMonth: true,
        changeYear: true
    });
</script>

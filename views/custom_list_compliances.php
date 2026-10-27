<?php

    $wh1["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id'],'process_type'=>'C');
    $wh1["field"] = "id,task_name,action_frequency,critical_compliance";
    $data1 = urlencode(json_encode($wh1));
    $url1 = API."?mode=gettabledetailswithconditions&table=process_task_settings&data=".$data1;
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
    var assetid = [];
    $('.checkboxassets').each(function(){
        if($(this).is(':checked')){
            assetid.push($(this).val());
        }
    });
    if (assetid.length === 0) {
        alert("please select Minimum one Compliance");
        return false;
    }
    console.log(assetid);
    jQuery.ajax({
        url: "ajax.php",
        data: "page=setcompliance&id="+assetid,
        type: "POST",
        success:function(data){
            console.log(data);
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
            <h5>Compliance Selection</h5>
            <div class="card-header-right">
                <button type="button" onclick="setcompliance()" class="btn btn-success ">
                Set Compliance
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
                <th scope="col">
                    <div class="checkbox-fade fade-in-primary">
                        <label class="checkbox checkbox-success">
                            <input aria-label="Ectall Checks" type="checkbox" id="selectall_checks" class="itemRow" onclick="selectallcheck()" onkeydown="">
                            <span class="cr">
                                <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                            </span>
                        </label> All
                    </div>
                </th>
                <th scope="col">Compliance Name</th>
                <th scope="col">Action Frequency</th>
                <th scope="col">Critical Compliance</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($compliancelist as $compliancelist){ ?>
                    <tr class="<?php echo $varianc_class; ?>">
                        <td class="text-center">
                            <div class="checkbox-fade fade-in-primary" >
                                <label class="checkbox checkbox-success">
                                    <input aria-label="Checkbox" type="checkbox" class="itemRow checkboxassets" name="checkassets[<?php echo $compliancelist['id']; ?>]" value="<?php echo $compliancelist['id']; ?>" onchange="selectcheck()">
                                    <span class="cr">
                                        <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                    </span>
                                </label>
                            </div>
                        </td>
                        <td><?php echo $compliancelist['task_name']; ?></td>
                        <td><?php echo $frequencyary[$compliancelist['action_frequency']]; ?></td>
                        <td><?php echo $compliancelist['critical_compliance']=="N"?'No':'Yes'; ?></td>

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

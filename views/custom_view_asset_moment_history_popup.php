<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$url = API."?mode=getassetallocation&admin_type=".$_SESSION['admin_type']."&resultgettype=DIRECTASSETALLOCATE&assetid=".$_POST['assetid']."&momenthistory=Y";
$data_details = callAPI($url);
$details = json_decode($data_details,1);

$list_assets = $details['list_assets'];
$momenthistory1 = $details['momenthistory'];
$ass_condition = array('1'=>'Used','2'=>'Good','3'=>'Poor','4'=>'Damaged','New');
$allocation_type = array('1'=>'Create Assets','2'=>'Update Assets','3'=>'Allocation','4'=>'Asset accept','5'=>'Asset Reject','6'=>'Return Assets','7'=>'Transefer');
foreach($details['asset_status'] as $asset_status){
    $ass_status[$asset_status['id']] = $asset_status['name'];
}
foreach($details['locations'] as $locations_list){
    $locations_lists[$locations_list['id']] = $locations_list['location_name'];
}
$assets_list = array();
foreach($list_assets as $assets_listt){
     $assets_list[$assets_listt['id']] = $assets_listt['asset_description'];
}
$momenthistoryart = array();
foreach($momenthistory1 as $momenthistory){
    $momenthistoryart[$momenthistory['asset_code']][] = $momenthistory;
}
?>
<script type="text/javascript">
    function selectAllocationType(){
            var selected = $("#allocation_type:checked").val();
            $(".select_caost_center").hide();
            $(".select_dept").hide();
            $(".select_emp").hide();
            if(selected=="CC"){
                $(".select_caost_center").show();
            }else if(selected=="D"){
                $(".select_dept").show();
            }else{
                $(".select_emp").show();
            }

    }
function submitdetails(){
      var formData = new FormData();
    formData.append("page", "insertassetallocation");
    var dataArray = $("#assetsallocationform").serializeArray(),
    dataObj = {};
    $(dataArray).each(function(i, field){
      formData.append(field.name, field.value);
    });
        jQuery.ajax({
            url: "ajax.php",
            data: formData,
            type: "POST",
            contentType: false,
            processData: false,
            success:function(data){
                console.log(data);
                var res = JSON.parse(data);
                if(res.errer=="404"){
                    alert("oops Employee not found");
                }else{
                }
            }
        });
}

</script>

<div class="modal-header">
    <h4 class="modal-title">Asset Allocation</h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body j-pro">
    <div class="j-content">
    <div class="card p-2 mt-2">

        <div class="row">


            <?php foreach($momenthistoryart as $momenthistorykey=>$momenthistory){ ?>
            <div class="col-xl-4 col-md-12">




   <div class="card-block accordion-block">
        <div id="accordion" role="tablist" aria-multiselectable="true">
            <div class="accordion-panel">
                <div class="accordion-heading btn-round" role="tab" id="headingOne">
                    <h3 class="card-title accordion-title">
                        <a class="accordion-msg waves-effect waves-dark f-18 bg-primary" data-bs-toggle="collapse"
                        data-parent="#accordion" href="#collapseOne<?php echo $momenthistorykey; ?>"
                        aria-expanded="true" aria-controls="collapseOne">
                        <?php echo $assets_list[$momenthistorykey];?>
                    </a>
                </h3>
            </div>
            <div id="collapseOne<?php echo $momenthistorykey; ?>" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                <div class="accordion-content accordion-desc">
                   <?php foreach($momenthistory as $momenthistore){
                                $jsondecode = json_decode($momenthistore['asset_detail'],1);
                            ?>
                            <div class="latest-update-box">
                                <div class="row p-t-20 p-b-30">
                                    <div class="col-auto text-end update-meta p-r-0">
                                       <i class="fa fa-user f-w-600 bg-c-green update-icon"></i>
                                    </div>

                                    <div class="col p-l-5">
                                        <a href="#!"><h6><?php echo ucwords($allocation_type[$momenthistore['asset_trans_type']]); ?></h6></a>
                                        <p class="text-muted m-b-0"><?php echo $momenthistore['trans_date']; ?></p>
                                        <div class="label-main">
                                          <span class="form-label label label-primary">Location : <?php echo $locations_lists[$jsondecode['asset_location']]; ?></span>
                                        </div>
                                        <div class="label-main">
                                          <span class="form-label label label-success">Status : <?php echo $ass_status[$jsondecode['status']]; ?></span>
                                        </div>
                                        <div class="label-main">
                                          <span class="form-label label label-info">Candition : <?php echo $ass_condition[$jsondecode['conditions']]; ?></span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        <?php } ?>



                </div>
            </div>
        </div>
    </div>
</div>


























            </div>

        <?php } ?>

        </div>
    </div>

    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary waves-effect waves-light"  onclick="submitdetails()">Submit</button>
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
<?php  ?>

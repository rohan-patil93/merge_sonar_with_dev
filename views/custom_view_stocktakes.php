<?php

if($_POST['submited']=="count_complete"){
    $_POST['company_id']=$_SESSION['company_id'];
    $_POST['user_id']=$_SESSION['userid'];
    $_POST['type']="item";
    $url = API."?mode=insertstocktakesitems&method=post";
    $response = file_post_contents($url, $_POST, 'insert');
    $res = json_decode($response,1);
}
$filter_location = json_encode($_POST['filter_location']);
$filter_veriances = isset($_POST['filter_veriances'])?'0':'1';
$rid =  encodeId($_GET['Rid']);
$rid = urldecode($rid);
$rid = explode(":", $rid);
$rid= $rid[1];
$url = API."?mode=getstocktakes&Rid=".$rid."&stoctype=list&filter_location=".$filter_location."&filter_veriances=".$filter_veriances;
$data_details = callAPI($url);
$details = json_decode($data_details,1);
$stocktakes = $details['stocktakes'];
$location_nameres = $details['locations'];
$itemlists= $details['item_list'];
$stockitems = array();
foreach($details['stockitems'] as $stockim){
    $stockitems[$stockim['location_id']][$stockim['item_id']] =$stockim;
}
$locationitemavailability = $details['locationitemavailability'];
foreach($location_nameres as $location){
    $location_name[$location['id']] =$location;
}
?>
<script type="text/javascript">
    function getUomDetails(item_id,uom_id,uom_name){
        var sid = $("#stocktake_id").val();
        jQuery.ajax({
            url: "views/custom_insert_stocktake_popup.php",
            data:'item_id='+item_id+'&uom_id='+uom_id+"&uom_name="+uom_name+"&sid="+sid,
            type: "POST",
            success:function(data){
                $('#modal_uom_page').html(data);
                $('#modal_uom_deatils').modal("show");
            },
            error:function (){}
        });
    }

function uompopup(item_id,uom_id,uom_name,location_id,id){

    var sid = $("#stocktake_id").val();
    var count_qty = $("#count_qty_"+id).val();
    jQuery.ajax({
        url: "views/custom_insert_stocktake_popup.php",
        data:'item_id='+item_id+'&uom_id='+uom_id+"&uom_name="+uom_name+"&sid="+sid+"&rowid="+id+"&count_qty="+count_qty+"&location_id="+location_id,
        type: "POST",
        success:function(data){
        if($(".popup-uom-"+id).css("display") == "none"){
                $(".table-popup").hide();
                $(".table-popup").empty();
                $(".popup-uom-"+id).html(data);

        }else{
              $(".popup-uom-"+id).html("");

        }
         jQuery(".popup-uom-"+id).toggle();
        }
    });
}
    function submitcount(valu=""){
        $('#status').val(valu);
        if(valu == '2'){
            Swal.fire({
                title: '<?php echo $_SESSION['error_message']['1007']; ?>',
                showDenyButton: true,
                showCancelButton: false,
                confirmButtonText: 'Yes',
                denyButtonText: 'No',
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#status').val(valu);
                    $("#formitemssbt").submit();
                }
            })
        }else if(valu == '3'){
            Swal.fire({
                title: '<?php echo $_SESSION['error_message']['1008']; ?>',
                showDenyButton: true,
                showCancelButton: false,
                confirmButtonText: 'Yes',
                denyButtonText: 'NO',
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#status').val(valu);
                    $("#formitemssbt").submit();
                }
            })
        }else{

            $("#formitemssbt").submit();
        }
    }
    function popupRecommendation(){
        var sid = $("#stocktake_id").val();
        jQuery.ajax({
            url: "views/custom_view_stocktake_recommendations_popup.php",
            data: "sid="+sid,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal("show");
            },
            error:function (){}
        });
    }
    function startstocktakes(userid){
        var sid = $("#stocktake_id").val();
        Swal.fire({
            title: '<?php echo $_SESSION['error_message']['1006']; ?>',
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: 'Yes',
            denyButtonText: 'No',
        }).then((result) => {
            if(result.isConfirmed){
                $.ajax({
                    url: "ajax.php",
                    data:'page=startstocktakes&sid='+sid+'&userid='+userid,
                    type: "POST",
                    success:function(data){
                   location.reload();
               }
           });
            }
        })
    }
    function deletedrecord(){
        var sid = $("#stocktake_id").val();
        Swal.fire({
            title: '<?php echo $_SESSION['error_message']['1005']; ?>',
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: 'Yes',
            denyButtonText: 'No',
        }).then((result) => {
            if(result.isConfirmed){
                $.ajax({
                    url: "ajax.php",
                    data:'page=softdeletedrecord&id='+sid+'&table=stocktakes',
                    type: "POST",
                    success:function(data){
                        console.log(data);
                        window.location.href = "index.php?option=COM_LIST_STOCKTAKES";
                    }
                });
            }
        })
    }
    function selectallcheck(){
        if($('#selectall_checks').is(':checked')){
            $('.checkbox2').each(function(){
                this.checked = true;
            });
        }else{
            $('.checkbox2').each(function(){
                this.checked = false;
            });
        }
    }
    function stocktakeFilter(){
        var filter_location  = $("#filter_location").val();
         var filter_veriances = $('input[name="filter_veriances"]:checked').val();
        var sid = $("#stocktake_id").val();
        jQuery.ajax({
            url: "views/custom_view_stocktakes_table.php",
            data: "sid="+sid+"&filter_location="+filter_location+"&filter_veriances="+filter_veriances,
            type: "POST",
            success:function(data){
                $('#stocktakestable').html(data);
            },
            error:function (){}
        });
    }
</script>
<style type="text/css">
    .table-popup{
        display: none;
        position: absolute;


        left: 41%;
        width: 50%;
        background: #ffffff;
        border: 1px solid #ccc;
        z-index: 99;
    }
    .table-popup{
        display: none;
        position: absolute;


        left: 41%;
        width: 50%;
        background: #ffffff;
        border: 1px solid #ccc;
    }
    .table-popup ul{
        display: flex;
        flex-wrap: wrap;
        padding-left: 0;
        margin-bottom: 0;
        list-style: none;

    }
    .table-popup ul li{
        -webkit-box-flex: 1;
        -ms-flex: 1;
        flex: 1;
        text-align: center;
        position: relative;
    }
</style>
<div id="modal_uom_deatils" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" >
        <div class="modal-content"  id="modal_uom_page">

        </div>
    </div>
</div>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid" >
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                    <div class="card-header mb-3">
                        <h5> View Stocktakes</h5>
                        <div class="card-header-right">

                            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                                <i class="icofont icofont-reply"></i>
                                Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group row ml-20 mr-20">
                            <div class="col-lg-1"></div>
                            <div class="form-group col-lg-2 text-center">
                                <span class="f-w-700">Name</span></br>
                                <span><?php echo $stocktakes['name']; ?></span>
                            </div>
                            <div class="form-group col-lg-2 text-center">
                                <span  class="f-w-700">User</span></br>
                                <span><?php echo $_SESSION['users'][$stocktakes['user_id']]; ?></span>
                            </div>
                            <div class="form-group col-lg-2 text-center">
                                <span  class="f-w-700">Planned Date</span></br>
                                <span><?php echo $stocktakes['Planned_date']; ?></span>
                            </div>
                            <div class="form-group col-lg-2 text-center">
                                <span  class="f-w-700">Status</span></br>
                                <span><?php echo $_SESSION['status'][$stocktakes['current_status']]['name']; ?></span>
                            </div>
                            <div class="form-group col-lg-2 text-center">
                                <span  class="f-w-700">Completed Date</span></br>
                                <span><?php echo $stocktakes['completed_date']!=""?$stocktakes['completed_date']:"-"; ?> </span>
                            </div>
                            <div class="col-lg-1"></div>
                        </div>
                        <div class="form-group row ml-20 mr-20">
                            <?php if($stocktakes['status'] == '0'){ $cenetrspac = "3"; ?>
                            <div class="form-group col-lg-2 text-right">
                                <a href="index.php?option=COM_INSERT_STOCKTAKES&Rid=<?php echo $_GET['Rid']; ?>" class="btn btn-primary waves-effect waves-light  col-lg-10"> <i class="icofont icofont-reply"></i> Back</a>
                            </div>
                            <div class="form-group col-lg-2 text-left">
                                <button class="btn btn-danger waves-effect waves-light  col-lg-10" onclick="deletedrecord()"><i class="fa fa-trash-o"></i> Delete</button>
                            </div>
                        <?php }else{
                            $cenetrspac = "7";

                        } ?>
                            <div class="form-group col-lg-<?php echo  $cenetrspac; ?> text-center">
                            </div>
                            <div class="form-group col-lg-5 text-right">
                                <?php if($stocktakes['status'] == '1'){ ?>
                                    <button class="btn waves-effect waves-light btn-primary btn-square"  onclick="submitcount('1')"><i class="ti-control-play"></i> Save And Continue</button>
                                    <button class="btn waves-effect waves-light btn-success btn-square" onclick="submitcount('2')"><i class="ti-control-play"></i> Submit Stocktake</button>

                                <?php } ?>
                                <?php if($stocktakes['status'] == '0'){ ?>
                                    <button class="btn waves-effect waves-light btn-primary btn-square" onclick="startstocktakes('<?php echo $stocktakes['user_id']; ?>')"><i class="ti-control-play"></i> Start Stocktake</button>
                                <?php } ?>
                                <?php if($stocktakes['status'] == '2'){ ?>
                                    <button class="btn waves-effect waves-light btn-primary btn-square" onclick="submitcount('3')"><i class="ti-control-play"></i> Complete Stocktake</button>
                                <?php  } ?>
                            </div>
                        </div>
                        <form method="post">
                            <div class="form-group row m-l-20">
                                <div class="form-group col-lg-4">
                                    <span class="f-w-700">Location</span>
                                    <select aria-label="Filter Location[]"  class="form-select js-example-basic-multiple" name="filter_location[]" id="filter_location" onchange="stocktakeFilter()" multiple>
                                <?php foreach ($location_nameres as $location) { ?>
                                    <option <?php if(in_array($location['id'], (array)$_POST['filter_location'])){ ?> selected <?php
} ?> value="<?php echo $location['id']; ?>"><?php echo ucwords($location['location_name']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <?php if($stocktakes['status'] == '2' || $stocktakes['status'] == '3'){ ?>
                            <div class="form-group col-lg-6">

                                <div class="form-radio mt-4">
                                    <div class="radio radio-inline" onclick="stocktakeFilter()" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                                        <label class="form-label">
                                            <input aria-label="Filter Veriances" type="radio" name="filter_veriances" checked="checked" value="all">
                                            <i class="helper"></i>Show All
                                        </label>
                                    </div>
                                    <div class="radio radio-inline" onclick="stocktakeFilter()" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                                        <label class="form-label">
                                            <input aria-label="Filter Veriances" type="radio" name="filter_veriances" value="cls_add">
                                            <i class="helper"></i>Positive Variance
                                        </label>
                                    </div>
                                    <div class="radio radio-inline" onclick="stocktakeFilter()" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                                        <label class="form-label">
                                            <input aria-label="Filter Veriances" type="radio" name="filter_veriances" value="cls_sub">
                                            <i class="helper"></i>Negative Variance
                                        </label>
                                    </div>
                                    <div class="radio radio-inline" onclick="stocktakeFilter()" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                                        <label class="form-label">
                                            <input aria-label="Filter Veriances" type="radio" name="filter_veriances" value="cls_zero">
                                            <i class="helper"></i>No Variance
                                        </label>
                                    </div>
                                </div>
                            </div>
                        <?php }else{ echo '<input aria-label="Filter Veriances" type="radio" name="filter_veriances" checked="checked" value="all" style="display:none" >';
} ?>

                    </div>
                </form>

                    <div  >
                        <form id="formitemssbt" method="post">
                        <input type="hidden" name="stocktake_id" id="stocktake_id" value="<?php echo $rid; ?>">
                        <input type="hidden" name="status" id="status" value="">
                        <input type="hidden" name="transaction_number" value="<?php echo $stocktakes['transaction_number']; ?>">
                        <div class="row" id="stocktakestable">
                        <?php include_once "custom_view_stocktakes_table.php"; ?>
                        </div>
                        </form>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

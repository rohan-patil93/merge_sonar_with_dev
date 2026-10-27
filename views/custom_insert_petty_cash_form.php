<?php
if($_GET['Rid'] !=""){
    $rid =  encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $rid = explode(':', $rid);
    $rid = $rid['1'];
    $_POST['type'] = "add";
}else{
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
}

$url1 = API."?mode=getpettycash&admin_type=".$_SESSION['admin_type']."&type=".$_POST['type']."&rid=".$rid;
$resultapi = callAPI($url1);
$result = json_decode($resultapi,1);
$petty_cash_items = $result['petty_cash_items'];
$petty_cash = $result['petty_cash'];
$pc_items = $result['pc_items'];
$petty_cash_balances = $result['petty_cash_balances'];
$available_balance = $petty_cash_balances['closing_balance'];
$pcitemid = array();
foreach($pc_items as $pcitem){
    $pcitemid[$pcitem['item_id']] = $pcitem['item_id'];
}
$petty_cash_box_id = $petty_cash['ref_id'];
if($petty_cash['current_status']!=""){
    $workflowsteps = getWorkflowStatus('petty_cash',$petty_cash['id'],$petty_cash['current_status']);
}
?>
<script type="text/javascript">
    function addItemCart(id,name,descr){
        $("#pettycashitemaddcartbtn_"+id).hide('slow');
        $("#checkpettycashitemaddcartbtn_"+id).show('slow');
        $("#empty_cart").hide();
         $("#pettycashitem_"+id).addClass('bg-info');
        var html = "";
        html +='<div class="row mt-2" id="pettycashcartitem_'+id+'">';
        html +='<div class="col-lg-7"><h4 class="f-14 mb-0 f-w-600 m-l-10">'+name+'</h4><span class="f-12 m-l-10">'+descr+'</span> </div>';
        html +=' <div class="col-lg-4"><input aria-label="Item Amt[" type="number" class="form-control cartamttextbox text-right" name="item_amt['+id+']" value="0" onblur="sumCartTotal()"> <input type="hidden" class="form-control" name="item_id['+id+']" value="'+id+'"><input type="hidden" class="form-control" name="item_name['+id+']" value="'+name+'"><input type="hidden" class="form-control" name="item_description['+id+']" value="'+descr+'"></div>';
        html +=' <div class="col-lg-1"><button type="button" class="btn p-0 border-0 bg-transparent ti-trash text-danger cursor-pointer" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Remove Cart" onclick="remvePettyCashItem('+id+')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button> </div>';
        html +='</div>';
        $("#billcartrows").append(html);
        tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['9081']; ?>','');
    }
    function sumCartTotal(){
        var totalcart = 0;
        $('.cartamttextbox').each(function(){
            if($(this).val() !=""){
                totalcart += parseFloat($(this).val());
            }
        });
        $("#sumcarttotal").val(totalcart);
        if(totalcart =='0' || totalcart ==""){
            $("#submitpettycashinvoice").attr("disabled", true);
        }else{
            $("#submitpettycashinvoice").attr("disabled", false);
        }
    }
    function remvePettyCashItem(id){

        $("#pettycashitemaddcartbtn_"+id).show('slow');
        $("#checkpettycashitemaddcartbtn_"+id).hide('slow');
        $("#pettycashitem_"+id).removeClass('bg-info');
        $("#pettycashcartitem_"+id).remove();
        sumCartTotal();
        tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['9082']; ?>','');
    }
    function submitPettyCashinvoice(){
        var petty_cash_box_id = $("#petty_cash_box_id").val();
        var pettycashform = $("#Petty_Cash_invoice_form").serialize();
        jQuery.ajax({
            url: "ajax.php",
            data:'page=insertpettycash&petty_cash_box_id='+petty_cash_box_id+'&'+pettycashform,
            type: "POST",
            success:function(data){

                var pettycash_rid = $("#pettycash_rid").val();
                if(pettycash_rid !=""){
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');
                    location.reload();
                }else{
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3000']; ?>','');
                    getPettyCashBoxTab('list');
                }
            },
            error:function (){}
        });
    }
    function submitDynamicWorkflowPopUp(table_name,transaction_id,current_status,next_status){
    jQuery.ajax({
        url: "views/custom_insert_dynamic_status_popup.php",
        data:'table_name='+table_name+'&current_status='+current_status+'&transaction_id='+transaction_id+'&next_status='+next_status,
        type: "POST",
        success:function(data){
            $('#popup1').html(data);
            $('#kt_modal_KTDatatable_remote').modal("show");
        },
        error:function (){}
    });
}
function submitDynamicWorkflow(table_name,transaction_id,current_status,next_status,old_status_name,new_status_name){
    swal({
        title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
        text: "Please note the status of the document will change to "+new_status_name,
        type: "info",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Yes",
        cancelButtonText: "No",
        closeOnConfirm: false
    },
    function(){
        console.log("dataa aa");
        jQuery.ajax({
            url: "ajax.php",
            data:'page=submitstatusdynamicworkflow&table_name='+table_name+'&current_status='+current_status+'&transaction_id='+transaction_id+'&next_status='+next_status,
            type: "POST",
            success:function(response){
                console.log(response);
                var res = JSON.parse(response);
                window.location.reload();
                if(res.transaction_update.status=='sucess'){
                     window.location.reload();
                    swal({
                        title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                        text: "<?php echo $_SESSION['error_message']['9059']; ?>",
                        type: "success",
                        showCancelButton: false,
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true
                    }, function () {
                        window.location.reload();
                    });
                }else{
                    swal({
                        title: "Oops...",
                        text: res.transaction_update.message,
                        type: "error",
                        showCancelButton: true,
                        showConfirmButton: false,
                        cancelButtonText: "Ok",
                        confirmButtonClass: "btn-danger",
                        closeOnCancel: false
                    });
                }
            },
            error:function (){}
        });
    });
}

</script>
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <?php
                if($petty_cash['current_status']!=""){
                  echo $workflowsteps['current_status_ribbon'];
                }
            ?>
            <div class="card-header">
                <h5>Items</h5>
                <div class="card-header-right table-card-header  m-r-50">
                     <div class="input-group input-group-button mb-0">
                        <input type="text" id="dynamicfilterhideshow" data-id="categoryfilter" data-type="div" class="form-control form-control-sm width1" placeholder="Search" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Search" aria-label="Search">
                        <span class="input-group-text addon-style">
                            <span class=""><span class="ti-search text-white mt-2"></span></span>
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                    <div class="row">
                        <?php echo $workflowsteps['breadcrumb']; ?>
                    </div>
                    <div class="row <?php echo ($petty_cash['current_status'] ==ST_ACTIVE || $petty_cash['current_status'] =="")?"":"event_disabled"; ?>">
                    <?php
                if(count((array)$petty_cash_items) !=0){
                    $m=1; foreach($petty_cash_items as $pci){  ?>
                    <div class="col-lg-3 m-b-15" id="categoryfilter_2">
                        <div class="pro-dsc-sec border">
                            <div class="pro-img"></div>
                            <div class="row">
                                <div class="col-lg-9">
                                    <h4 class="mt-3 f-12 text-primary f-w-600 text-uppercase m-l-10"><?php echo $pci['item_name']; ?></h4>
                                    <h3 class="f-16 f-w-600 m-l-10 m-b-15"><?php echo $pci['description']; ?></h3>
                                </div>
                                <div class="col-lg-3 pt-2">
                                    <i class="ti-share text-primary cursor-pointer" id="pettycashitemaddcartbtn_<?php echo $pci['id']; ?>" data-bs-toggle="tooltip" data-bs-original-title="Add Bill"  onclick="addItemCart(<?php echo $pci['id']; ?>,'<?php echo $pci['item_name']; ?>','<?php echo $pci['description']; ?>')" <?php echo isset($pcitemid[$pci['id']])?"style='display: none'":""; ?> tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                                    <i class="ti-check" id="checkpettycashitemaddcartbtn_<?php echo $pci['id']; ?>" <?php echo isset($pcitemid[$pci['id']])?"":"style='display: none'"; ?>></i>
                                </div>
                            </div>

                        </div>
                    </div>
                    <?php  $m++;
}
}else{ ?>
                    <div class="card-px text-center py-20 m-t-30">
                        <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Items</p>
                        <div class="text-center px-4">
                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                        </div>
                    </div>
                <?php } ?>
            </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card" style="position: sticky; top: 0;">
                <div class="card-header">
                    <h5>Bill</h5>
                    <div class="card-header-right table-card-header">
                        <?php
                        if($available_balance >= $petty_cash['amount']){
                            echo $workflowsteps['workflow_btn'];
                        }else{
                            echo "<span class='text-danger'>Balance Not Available</span>";
                        }
                         ?>

                    </div>
                </div>
                <div class="card-body <?php echo ($petty_cash['current_status'] ==ST_ACTIVE || $petty_cash['current_status'] =="")?"":"event_disabled"; ?>">
                    <form method="post" id="Petty_Cash_invoice_form">
                        <div id="billcartrows" >
                            <?php $totaldisbtn = 0; if(count((array)$pc_items) != 0){
                                foreach($pc_items as $pcitem){

                                    $totaldisbtn += $pcitem['total_amount']!=""?$pcitem['total_amount']:0;
                                ?>
                                <div class="row mt-2" id="pettycashcartitem_<?php echo $pcitem['item_id']; ?>">
                                    <div class="col-lg-7">
                                        <h4 class="f-14 mb-0 f-w-600 m-l-10"><?php echo $pcitem['item_name']; ?></h4><span class="f-12 m-l-10"><?php echo $pcitem['description']; ?></span>
                                    </div>
                                    <div class="col-lg-4">
                                        <input aria-label="Enter number" type="number" class="form-control cartamttextbox text-right" name="item_amt[<?php echo $pcitem['item_id']; ?>]" value="<?php echo $pcitem['total_amount']; ?>" onblur="sumCartTotal()">
                                        <input type="hidden" class="form-control" name="item_id[<?php echo $pcitem['item_id']; ?>]" value="<?php echo $pcitem['item_id']; ?>">
                                        <input type="hidden" class="form-control" name="item_name[<?php echo $pcitem['item_id']; ?>]" value="<?php echo $pcitem['item_name']; ?>">
                                        <input type="hidden" class="form-control" name="item_description[<?php echo $pcitem['item_id']; ?>]" value="<?php echo $pcitem['description']; ?>">
                                         <input type="hidden" class="form-control" name="pc_item_id[<?php echo $pcitem['item_id']; ?>]" value="<?php echo $pcitem['id']; ?>">
                                    </div>
                                    <div class="col-lg-1">
                                        <i class="ti-trash text-danger cursor-pointer" data-bs-toggle="tooltip"  data-placement="bottom" data-bs-original-title="Remove Cart" onclick="remvePettyCashItem(<?php echo $pcitem['item_id']; ?>)" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                                    </div>
                                </div>

                            <?php } ?>
                            <?php } else { ?>
                            <div class="card-px text-center py-20 m-t-30" id="empty_cart">
                                <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> Please Select Items</p>
                                <div class="text-center px-4">
                                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                        <div class="row align-items-center mt-3" >
                            <div class="col-lg-7">
                                <h4 class="f-14 mb-0 f-w-600 m-l-10">Total Amount</h4>
                            </div>
                            <div class="col-lg-5">
                                 <input aria-label="Total Amount" type="number" class="form-control text-right" id="sumcarttotal" name="total_amount" value="<?php echo $petty_cash['amount'];  ?>" readonly >
                            </div>
                            <span class="text-danger text-right">Available Balance : <?php echo customeummarmonyformate($available_balance); ?></span>
                        </div>
                        <div class="row align-items-center mt-3" >
                            <div class="col-lg-7">
                                <h4 class="f-14 mb-0 f-w-600 m-l-10">Transaction Date</h4>
                            </div>
                            <div class="col-lg-5">
                                 <input aria-label="Transaction Date" type="text" class="form-control datepicker" name="transaction_date" value="<?php echo fromDate($petty_cash['transaction_date']);  ?>">
                            </div>
                        </div>
                        <div class="row align-items-center mt-3" >
                            <div class="col-lg-7">
                                <h4 class="f-14 mb-0 f-w-600 m-l-10">Description</h4>
                            </div>
                            <div class="col-lg-5">
                                 <textarea aria-label="Notes" type="text" class="form-control datepicker" name="notes" ><?php echo $petty_cash['notes'];  ?></textarea>
                            </div>
                        </div>
                        <div class=" card-body row">
                            <div class="form-group col-lg-6 text-left">
                                <input type="hidden" name="rid" id="pettycash_rid" value="<?php echo $rid; ?>">
                                <button type="button" class="btn btn-primary" id="submitpettycashinvoice" onclick="submitPettyCashinvoice()" <?php echo $totaldisbtn =='0'?"disabled":""; ?>><?php echo $rid !=""?"Update":"Submit"; ?></button>
                            </div>
                            <div class="form-group col-lg-6"> </div>
                        </div>
                    </form>
            </div>
        </div>
    </div>
</div>

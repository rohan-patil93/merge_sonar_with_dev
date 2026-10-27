<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$_SESSION['apply_js']['multiselest'] = '1';
$document_type = $_POST['transaction_table'] !=""?$_POST['transaction_table']:"";
$transaction_id = $_POST['transaction_id'] !=""?$_POST['transaction_id']:"";
$api = API."?mode=getmultireferencepagedetails&transaction_id=".$transaction_id."&transaction_table=".$document_type;
$apicall = callAPI($api);
$result = json_decode($apicall,1);
$dynamic_reference_tables_document_type = $result['dynamic_reference_tables_document_type'];
$dynamic_reference_tables_reference_document_type = $result['dynamic_reference_tables_reference_document_type'];

?>
<script>
    <?php if($document_type !=""){ ?>
            selectDocumentType();
            console.log('done');
    <?php } ?>
    function selectDocumentType(){
        $("#reference_document_type").html('<option>Please Wait.....</option');
        $("#muilti_party_id").html('<option>Please Wait.....</option');
        var document_type = $("#document_type").val();

        var multi_ref_transaction_id = $("#multi_ref_transaction_id").val();
        var multi_ref_transaction_table = $("#multi_ref_transaction_table").val();

        jQuery.ajax({
            url: "ajax.php",
            data:'page=getmultireferencepagedetails&gettype=document_type&value='+document_type+'&multi_ref_transaction_id='+multi_ref_transaction_id+'&multi_ref_transaction_table='+multi_ref_transaction_table,
            type: "POST",
            success:function(response){
                var obj = JSON.parse(response);
                console.log(response);
                $("#reference_document_type").html(obj.option);
                $("#muilti_party_id").html(obj.vendor_or_customer_list);
                $("#party_table").val(obj.party_table);
                if(multi_ref_transaction_id !="" && multi_ref_transaction_id !="0" && multi_ref_transaction_table !=""){
                    getTransactionlist();
                }

            },
            error:function (){}
        });
    }
    function getTransactionlist(){

        var party_id = $("#muilti_party_id").val();
        var reference_document_type = $("#reference_document_type").val();

        var document_type = $("#document_type").val();

        var multi_ref_transaction_id = $("#multi_ref_transaction_id").val();
        var multi_ref_transaction_table = $("#multi_ref_transaction_table").val();

        $("#party_id_error").text('');
        $("#reference_document_type_error").text('');

        if(reference_document_type == "requisitions" || reference_document_type == "enquiries" || reference_document_type == "sales_orders"){
            party_id ="";
        }
        if(party_id =="" && reference_document_type != "requisitions" && reference_document_type != "enquiries" && reference_document_type != "sales_orders"){
            $("#party_id_error").text('Please Select Party.');
        }else if(reference_document_type ==""){
            $("#reference_document_type_error").text('Please Select Reference Document Type.');

        }else{
            var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
            $("#transaction_list").html(loader);
            jQuery.ajax({
                url: "ajax.php",
                data:'page=getmultireferencepagedetails&gettype=transaction_list&reference_document_type='+reference_document_type+'&party_id='+party_id+'&multi_ref_transaction_id='+multi_ref_transaction_id+'&multi_ref_transaction_table='+multi_ref_transaction_table+'&document_type='+document_type,
                type: "POST",
                success:function(response){
                    var obj = JSON.parse(response);
                   $("#transaction_list").html(obj.tables);
                   if(multi_ref_transaction_id !="" && multi_ref_transaction_id !="0" && multi_ref_transaction_table !=""){
                        selectTransactions();
                    }
                },
                error:function (){}
            });
        }
    }
    function selectTransactions(){
        var reference_document_type = $("#reference_document_type").val();
        var multi_ref_transaction_id = $("#multi_ref_transaction_id").val();
        var multi_ref_transaction_table = $("#multi_ref_transaction_table").val();
        var document_type = $("#document_type").val();

        var transaction_id = [];
        $('.transaction_lists').each(function(){
            if($(this).is(':checked')){
                transaction_id.push($(this).val());
            }
        });
        console.log(transaction_id);
        $("#transaction_item_list").html('');
        if (transaction_id.length != 0) {
            var loader = '<div class="text-center" id="loader-show_2"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
            $("#transaction_item_list").html(loader);
            jQuery.ajax({
                url: "ajax.php",
                data:'page=getmultireferencepagedetails&gettype=transaction_item_list&reference_document_type='+reference_document_type+'&transaction_id='+transaction_id+'&multi_ref_transaction_id='+multi_ref_transaction_id+'&multi_ref_transaction_table='+multi_ref_transaction_table+'&document_type='+document_type,
                type: "POST",
                success:function(response){
                    var obj = JSON.parse(response);
                   $("#transaction_item_list").html(obj.tables);
                   selectTransactionsItems();
                },
                error:function (){}
            });
        }else{
            var emptyload = '<div class="card-px text-center "><p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> Please Select Transaction</p><div class="text-center px-4"><img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;"></div></div>';
            $("#transaction_item_list").html(emptyload);
        }
    }

    function selectTransactionsItems(){
        var reference_document_type = $("#reference_document_type").val();
        var transaction_item_id = [];
        $("#submit_multi_reference_documnets").addClass("disabled");
        $('.check_transaction_item_list').each(function(){
            if($(this).is(':checked')){
                $("#submit_multi_reference_documnets").removeClass("disabled");
            }else{
            }
        });

    }

    //      minimumFractionDigits: 2,


    function submitMultiReferenceDocumnets(){
        $("#submit_multi_reference_documnets").text("Please Wait........");
        $("#submit_multi_reference_documnets").addClass("disabled");
        jQuery.ajax({
            url: "ajax.php",
            data: $('#multireferencedocumnets_form').serialize()+'&page=submitmultireferencedocumnets',
            type: "POST",
            success:function(response){
                var obj = JSON.parse(response);
                if(obj.transaction_result.status == "sucess"){
                    if(obj.transaction_result.LASTID == '0'){
                        tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');
                    }else{
                        tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3000']; ?>','');
                        updateUrlParameter('Rid',obj.rid);
                    }
                    $('#kt_modal_dynamic_popup').modal("hide");
                    $('#dynamic_popup1').html("");
                       location.reload();
                }else{
                    tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX',obj.transaction_result.message,'');
                    $("#submit_multi_reference_documnets").text("Submit");
                    $("#submit_multi_reference_documnets").removeClass("disabled");
                }
            },
            error:function (){}
        });
    }

    function selectAllTransactionsItems(){
        $('.check_transaction_item_list').prop('checked', false);
        $("#submit_multi_reference_documnets").addClass("disabled");
        if($(".check_all_check_box").is(':checked')){
            $('.check_transaction_item_list').prop('checked', true);
            $("#submit_multi_reference_documnets").removeClass("disabled");
        }
    }
</script>

<div class="modal-header">
    <h4 class="modal-title">Multi Reference Documnets</h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <form id="multireferencedocumnets_form">
        <input type='hidden' id='multi_ref_transaction_id' name='multi_ref_transaction_id'  value='<?php echo $transaction_id; ?>'>
        <input type='hidden' id='multi_ref_transaction_table' name='multi_ref_transaction_table'  value='<?php echo $document_type; ?>'>
        <div class="row">
            <div class="form-group col-md-3 w-full <?php echo $document_type !=""?"event_disabled":""; ?>">
                <label for="final_item_name" class="form-label d-block">Document Type <span class="text-danger">*</span></label>
                <select aria-label="Document Type" class="js-example-basic-single" id="document_type" name="document_type" onchange="selectDocumentType()" required>
                    <option value="">Select Any one</option>
                    <?php foreach ($dynamic_reference_tables_document_type as $drttype){ ?>
                        <option value="<?= $drttype['reference_table']; ?>" <?php echo $document_type == $drttype['reference_table']?"selected":""; ?>>
                            <?= ucwords(str_replace('_',' ',$drttype['reference_table'])); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group col-md-3 w-full">
                <label for="final_item_name" class="form-label d-block">Reference Document Type <span class="text-danger">*</span></label>
                <select aria-label="Reference Document Type" class="js-example-basic-single" id="reference_document_type" name="reference_document_type" onchange="getTransactionlist()" required>
                    <option value="">Select Any one</option>
                    <?php foreach ($dynamic_reference_tables_reference_document_type as $drttype){ ?>
                        <option value="<?= $drttype['transaction_table']; ?>">
                            <?= ucwords(str_replace('_',' ',$drttype['transaction_table'])); ?>
                        </option>
                    <?php } ?>
                </select>
                <span class="d-block text-danger" id="reference_document_type_error"></span>
            </div>
            <div class=" form-group col-md-3 w-full" >
                <span class="form-label d-block">Party <span class="text-danger">*</span></span>


                <input type='hidden' id='party_table' name='party_table'  value=''>
                <select aria-label="Party" class="js-example-basic-single" id="muilti_party_id" name="party_id" onchange="getTransactionlist()"  required>
                    <option value="">Select Any one</option>
                </select>
                <span class="d-block text-danger" id="party_id_error"></span>
            </div>
        </div>
        <div class="row">
            <div class=" form-group col-md-4">
                <div class="card">
                    <div class="card-header">
                          <h5>Transactions</h5>
                          <div class="card-header-right table-card-header">
                          </div>
                    </div>
                    <div class="card-body custom-scroll" id="transaction_list" style="min-height: 500px; max-height: 500px;">
                        <div class="card-px text-center "><p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i>Please Select Reference Document Type</p><div class="text-center px-4"><img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;"></div></div>
                    </div>
                </div>
            </div>
            <div class=" form-group col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>Transaction Item List</h5>
                        <div class="card-header-right table-card-header">

                        </div>
                    </div>
                    <div class="card-body custom-scroll" id="transaction_item_list" style="min-height: 500px; max-height: 500px;">
                        <div class="card-px text-center "><p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> Please Select Transaction</p><div class="text-center px-4"><img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;"></div></div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect" data-bs-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary waves-effect waves-light disabled" id="submit_multi_reference_documnets"  onclick="submitMultiReferenceDocumnets()">Submit</button>
</div>


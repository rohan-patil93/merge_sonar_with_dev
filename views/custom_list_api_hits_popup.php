<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$api_hit_id = $_POST['api_hit_id'];
$url = API."?mode=getapihits&api_hit_id=".$api_hit_id;
$response = callAPI($url);
$result = json_decode($response,1);
$api_masters = $result['api_masters'];
$api_code = $api_masters['api_code'];
$api_masters_response_details = json_decode($api_masters['api_response_details'],1);
$api_hits = $result['api_hits'];
$api_hits_requist_parameters_details = json_decode(urldecode($api_hits['api_request_parameters']),1);
$api_hits_response_details123 = json_decode(urldecode($api_hits['api_response_details']),1);
$api_hits_response_details = $api_hits_response_details123[$api_code];
?>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery(".data").hide();
        jQuery(".main").click(function() {

          });
    });
    function opensubtable(id){
        var showhide = $("#showhide"+id).val();
        $(".showclassval").val(0);
        $(".showclassdiv").hide();
        if(showhide=='0'){
            $(".showclass"+id).show('slow');
            $("#showhide"+id).val('1');
        }else{
            $(".showclass"+id).hide('slow');
            $("#showhide"+id).val('0');
        }
    }
</script>
<div class="modal-header  ">
    <h5 class="modal-title">API Hits Response</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
<?php if($api_hits['response_status'] =="200"){ ?>
        <?php if($api_masters['api_type'] == "R"){ ?>
            <?php if($api_masters['api_code'] == "SENDPAYMENTS" || $api_masters['api_code'] == "RECEIVERECEIPTS"){ ?>

            <div class="custom-scroll" style="min-height: 470px;  max-height: 470px;">
                <table class="table table-separate table-head-custom table-checkable data-table custom_asign_emp_mpc" id="htmlajaxapihits">
                    <tr>
                        <th scope="col"></th>
                        <th scope="col">Transaction No.</th>
                        <th scope="col">Transaction Date</th>
                        <th scope="col" class="text-right">Total</th>
                    </tr>
                <?php foreach($api_hits_response_details as $trans_id=>$tableslist){
                    $arychunks = array_chunk($tableslist['Ledger_Entries'], 2);
                    $recepttotalamt = 0;
                    foreach($arychunks as $partydetailandtransdetail){
                        $recepttotalamt += $partydetailandtransdetail[0]['AMOUNT'];
                    }
                    ?>
                    <tr class="main">
                        <td class="cursor-pointer">
                            <i class="ti-angle-double-down" onclick="opensubtable('<?php echo $trans_id; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>
                            <input type="hidden" class="showclassval" id="showhide<?php echo $trans_id; ?>" value="0">
                        </td>
                        <td><?php echo $tableslist['VOUCHERNUMBER']; ?></td>
                        <td><?php echo $tableslist['InvDate']; ?></td>
                        <td class="text-right"><?php echo $recepttotalamt; ?></td>
                    </tr>
                    <tr class="data showclass<?php echo $trans_id; ?> showclassdiv">
                        <td colspan="20">
                            <div class="row">
                                <div class="col-lg-12">
                                    <table class="table table-td-6">
                                        <thead>
                                            <tr><th scope="col">Transaction No.</th><th scope="col">Party_Name</th><th scope="col" class="text-right">Total</th></tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                             foreach($arychunks as $tableslist1){?>
                                            <tr>
                                                <td class="w-b-w-s"><?php echo $tableslist1[0]['Bill_Details']['Bill_NO']; ?></td>
                                                <td class="w-b-w-s"><?php echo $tableslist1[0]['Party_Name']; ?></td>
                                                <td class="text-right"><?php echo customeummarmonyformate($tableslist1[0]['AMOUNT']); ?></td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                </table>
            </div>

            <?php }elseif($api_masters['api_code'] == "RECEIVEPURCHASEORDERS" || $api_masters['api_code'] == "RECEIVEPURCHASEINVOICES" || $api_masters['api_code'] == "RECEIVEGOODSRECEIPTNOTES" || $api_masters['api_code'] == "RECEIVESALESORDERS" || $api_masters['api_code'] == "RECEIVESALESINVOICES" || $api_masters['api_code'] == "RECEIVEDELIVERYORDERS" || $api_masters['api_code'] == "RECEIVEDEBITNOTES" || $api_masters['api_code'] == "RECEIVECREDITNOTES"){ ?>

                <div class="custom-scroll" style="min-height: 470px;  max-height: 470px;">
                <table class="table table-separate table-head-custom table-checkable data-table custom_asign_emp_mpc" id="htmlajaxapihits_2">
                    <tr>
                        <th scope="col"></th>
                        <th scope="col">Transaction No.</th>
                        <th scope="col">Transaction Date</th>
                        <th scope="col">Party Name</th>
                        <th scope="col" class="text-right">Amount</th>
                        <th scope="col" class="text-right">Tax</th>
                        <th scope="col" class="text-right">Total</th>
                    </tr>
                <?php foreach($api_hits_response_details as $trans_id=>$tableslist){
                    if($tableslist['Voucher_Amount']!=""){
                        $invtotalamt = str_replace(",","",$tableslist['Voucher_Amount']);
                    }
                    $totaltax = 0;
                    foreach($tableslist['Ledger_Entries'] as $ledgerentr){
                        if($ledgerentr['Ledger_Name'] == "CGST" || $ledgerentr['Ledger_Name'] == "SGST" || $ledgerentr['Ledger_Name'] == "IGST"){
                            $totaltax += $ledgerentr['AMOUNT'];
                        }
                    }
                    ?>
                    <tr class="main">
                        <td class="cursor-pointer">
                            <i class="ti-angle-double-down" onclick="opensubtable('<?php echo $trans_id; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>
                            <input type="hidden" class="showclassval" id="showhide<?php echo $trans_id; ?>" value="0">
                        </td>
                        <td><?php echo $tableslist['VOUCHERNUMBER']; ?></td>
                        <td><?php echo $tableslist['InvDate']; ?></td>
                        <td><?php echo $tableslist['PARTYNAME']; ?></td>
                        <td class="text-right"><?php echo $invtotalamt-$totaltax; ?></td>
                        <td class="text-right"><?php echo $totaltax; ?></td>
                        <td class="text-right"><?php echo $invtotalamt; ?></td>
                    </tr>
                    <tr class="data showclass<?php echo $trans_id; ?> showclassdiv">
                        <td colspan="20">
                            <div class="row">
                                <div class="col-lg-9">
                                    <table class="table table-td-6">
                                        <thead>
                                            <tr><th scope="col">Item</th><th scope="col">SKU</th><th scope="col" class="text-right">Quantity</th><th scope="col" class="text-right">Amount</th><th scope="col" class="text-right">Total</th></tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($tableslist['Item_Entries'] as $tableslist1){?>
                                            <tr>
                                                <td class="w-b-w-s"><?php echo $tableslist1['Stock_Item']; ?></td>
                                                <td ><?php echo $tableslist1['part_no']; ?></td>
                                                <td class="text-right"><?php echo customeummarmonyformate($tableslist1['QUANTITY']); ?></td>
                                                <td class="text-right"><?php echo customeummarmonyformate($tableslist1['RATE']); ?></td>
                                                <td class="text-right"><?php echo customeummarmonyformate($tableslist1['AMOUNT']); ?></td>

                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-6 col-6">
                                    <div class="card" style="background: #f5f6f7;">
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                                        <div class="card-header">
                                            <h5>Order Summary</h5>
                                        </div>
                                    </div>
                                    </div>
                                        <div class="card-body">
                                            <table class="table table-sm mb-0">
                                                <tr><th scope="col">Sub Total </th><td style="text-align: right;"><?php echo $invtotalamt-$totaltax; ?></td></tr>
                                                <tr><th scope="col">Tax</th><td style="text-align: right;"><?php echo $totaltax; ?></td></tr>
                                                <tr class="bg-primary"><th scope="col">Total</th><td style="text-align: right;"><span class="f-18"><?php echo $invtotalamt; ?></span></td></tr>
                                            </table>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                </table>

            </div>

            <?php }else{ ?>

            <div class="custom-scroll" style="min-height: 470px;  max-height: 470px;">
                <table class="table table-hover m-b-0" style="font-size:13px;" id="htmlajaxapihits_3">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <?php foreach($api_masters_response_details as $keyfield=>$mappingfield){ ?>
                            <th scope="col">
                                <?php echo $keyfield; ?>
                            </th>
                            <?php } ?>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $im=1;
                        foreach($api_hits_response_details as $apijitrdetail){ ?>
                        <tr>
                            <td class="p-2"><?php echo $im; ?></td>
                            <?php
                                foreach($api_masters_response_details as $keyfield=>$mappingfield){ ?>
                                <td class="p-2">
                                    <?php echo $apijitrdetail[$keyfield]; ?>
                                </td>
                            <?php } ?>
                            <td class="p-2">-</td>
                        </tr>
                    <?php $im++;
} ?>
                    </tbody>
                </table>
            </div>

        <?php } ?>

    <?php }else{ ?>
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5>API Requist</h5>
                        <div class="card-header-right table-card-header ">
                        </div>
                    </div>
                    <div class="card-block custom-scroll" style="min-height: 470px;  max-height: 470px;">
                        <?php echo "<pre>"; print_r($api_hits_requist_parameters_details);  echo "</pre>"; ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5>API Response</h5>
                        <div class="card-header-right table-card-header ">
                        </div>
                    </div>
                    <div class="card-block custom-scroll" style="min-height: 470px;  max-height: 470px;">
                        <?php echo "<pre>"; print_r($api_hits_response_details123);  echo "</pre>"; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php  } ?>

    <?php }else{ ?>
        <div class="card-px text-center ">
            <p class="text-danger f-16 fw-bold m-b-30"><i class="ti ti-alert text-danger"></i> Oops Wrong Response</p>
            <div class="text-center px-4">
                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
            </div>
        </div>
    <?php } ?>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal" >Close</button>
    <?php if($api_masters['api_type'] == "R"){ ?>
     <button type="button" class="btn btn-primary waves-effect waves-light" onclick="checkAPIDataValidateORNot('<?php echo $api_hit_id; ?>','C')"> Check Validations </button>
    <button type="button" class="btn btn-primary waves-effect waves-light" id="insertemployeedetails_btn" onclick="checkAPIDataValidateORNot('<?php echo $api_hit_id; ?>','S')"> Sync With Database </button>
        <?php } ?>
</div>
<script type="text/javascript">
    function checkAPIDataValidateORNot(api_hit_id,checkstatus){
        var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $("#htmlajaxapihits").html(loader);
        $.ajax({
            url: "ajax.php",
            data:'page=checkapidatavalidateornot&api_hit_id='+api_hit_id+'&checkstatus='+checkstatus,
            type: "POST",
            success:function(data){
                console.log(data);
                var response = JSON.parse(data);
                if(response.result_cnt != 0){
                    $("#htmlajaxapihits").html(response.html_res);
                    jQuery(".data").hide();
                }
            }
        });
    }
</script>

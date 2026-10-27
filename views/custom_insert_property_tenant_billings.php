<?php
if(isset($_POST['monthyear'])){
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    $monthyear = explode('-',$_POST['monthyear']);
    $year = $monthyear[0];
    $month = $monthyear[1];
    $admindashboardurl = API."?mode=getrentaladmindashboard&vend_limit=3&admin_type=".$_SESSION['admin_type']."&admin_access_employee=".$_SESSION['admin_access_employee']."&year=$year&month=$month";
    $admindashboardres = callAPI($admindashboardurl);
    $admindashboardd = json_decode($admindashboardres,1);

    foreach($admindashboardd['all_rental_invoices_list'] as $pending_list){
        $rental_invoices_transnumber[$pending_list['id']] = $pending_list['transaction_number'];
    }
    $tenant_billings  =$admindashboardd['tenant_billings'];

}

    $property_tenant_billings = $tenant_billings['property_tenant_billings'];
    $invoicetobegenerated = $invoicetobeemailed = $invoicesharedwithtenant = $utility_invoicetobegenerated = $utility_invoicetobeemailed = $utility_invoicesharedwithtenant= array();
    foreach($property_tenant_billings as $ptb){
        if($ptb['shared'] == '0'){
            $invoicesharedwithtenant[] = $ptb;
        }elseif($ptb['generated_invoice'] == '0'){
            $invoicetobeemailed[] = $ptb;
        }else{
            $invoicetobegenerated[] = $ptb;
        }
    }
    foreach($property_tenant_billings as $ptb){
        if($ptb['utility_shared'] == '0'){
            $utility_invoicesharedwithtenant[] = $ptb;
        }elseif($ptb['utility_generated_invoice'] == '0'){
            $utility_invoicetobeemailed[] = $ptb;
        }else{
            $utility_invoicetobegenerated[] = $ptb;
        }
    }

    $property_owners = $tenant_billings['property_owners'];
    $property_owner_details = $tenant_billings['property_owner_details'];
    ?>
    <script type="text/javascript">
        function calculateRent(id){
            var totalamt = 0;
            console.log(id);
            $('.rent_'+id).each(function(){
                if($(this).val() !=''){
                    totalamt = parseFloat(totalamt)+parseFloat($(this).val());
                }
            });
            console.log(totalamt);
            $("#totalamt_"+id).text(totalamt);
        }
        function opentTenantUtilityPopUp(rowid){
            $(".custom-popup-open").hide();
            $(".open_popup_tenant_billing_"+rowid).show('slow');
        }
        function opentTenantUtilityonlyPopUp(rowid){
            $(".custom-popup-open").hide();
            $(".open_popup_tenant_billing_utility_"+rowid).show('slow');
        }


        function submitTenantBill(property_tenant_billing_id,type,extrform="",billtype=""){
            console.log("#form_id_"+extrform+property_tenant_billing_id);
            var emtycheck = 0;
            $('.checkamountnotepmty_'+extrform+property_tenant_billing_id).each(function(){
                if($(this).val() =='' || $(this).val() =='0'){
                    console.log("------"+$(this).val());
                    emtycheck = 1;
                }
            });
            if(emtycheck == 0 || type == 'S'){
                var formdata = $("#form_id_"+extrform+property_tenant_billing_id).serialize();
                jQuery.ajax({
                    url: "ajax.php",
                    data:'page=updatepropertytenantbillings&type='+type+'&property_tenant_billing_id='+property_tenant_billing_id+'&bill_type='+billtype+'&'+formdata,
                    type: "POST",
                    success:function(res){
                        console.log(res);
                        var data = JSON.parse(res);
                        var respon = data.result;
                        if(respon.status =="fail"){
                            swal("<?php echo ucwords($_SESSION['config']['name']); ?>",respon.message, "error");
                        }else{
                            if(type =="S"){
                                swal("<?php echo ucwords($_SESSION['config']['name']); ?>",'<?php echo $_SESSION['error_message']['3001']; ?>', "success");
                            }else{
                                console.log("rental_invoice_id === "+data.rental_invoice_id);
                                console.log("rental_invoice_id === "+data.decode_rental_invoice_id);
                                if(data.rental_invoice_id !=""){
                                    swal({
                                        title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                                        text: '<?php echo $_SESSION['error_message']['9080']; ?>',
                                        type: "warning",
                                        showCancelButton: true,
                                        confirmButtonClass: "btn-danger",
                                        confirmButtonText: "Yes",
                                        cancelButtonText: "No",
                                        closeOnConfirm: true,
                                        closeOnCancel: true
                                    },
                                    function(isConfirm) {
                                        if(isConfirm){
                                            window.location.href = "index?option=COM_INSERT_RENTAL_INVOICES&Rid="+data.decode_rental_invoice_id;
                                        }else{
                                            location.reload();
                                        }
                                    });
                                }else{
                                    swal("<?php echo ucwords($_SESSION['config']['name']); ?>",'<?php echo $_SESSION['error_message']['3001']; ?>', "success");
                                }
                            }

                        }
                    },
                    error:function (){}
                });
            }else{
                swal("<?php echo ucwords($_SESSION['config']['name']); ?>", "Utility Amount Is Required", "error");
            }
        }
        function closePopUp(rowid){
            $(".custom-popup-open").hide();
        }
        function renatalInvoiceSend(property_tenant_billings_id,bill_type=""){
            jQuery.ajax({
                url: "ajax.php",
                data:'page=renatalinvoicesend&property_tenant_billings_id='+property_tenant_billings_id+'&bill_type='+bill_type,
                type: "POST",
                success:function(res){
                    console.log(res);
                    var data = JSON.parse(res);
                    var respon = data.res;
                    if(respon.status =="fail"){
                        swal("<?php echo ucwords($_SESSION['config']['name']); ?>",respon.message, "error");
                    }else{
                        swal("<?php echo ucwords($_SESSION['config']['name']); ?>",'<?php echo $_SESSION['error_message']['9078']; ?>', "success");
                    }
                    location.reload();
                },
                error:function (){}
            });
        }
    </script>
<div class="card">
    <div class="card-header  row">
        <div class="col-lg-10">
            <h5>Property Rent Invoices</h5>
        </div>
    </div>
    <div class="card-body">
        <ul class="nav md-tabs infisuite_tab mt-2 ml-2 mb-3">
            <li class="nav-item">
                <a class="nav-link f-w-600 f-12 active" data-bs-toggle="tab" href="#invoicetobegenerated" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">  Invoice to Be Generated <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$invoicetobegenerated);?></span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#invoicetobeemailed" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> Invoice to be Emailed <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$invoicetobeemailed);?></span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#invoicesharedwithtenant" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> Invoice Shared With Tenant <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$invoicesharedwithtenant);?></span></a>
            </li>
        </ul>
        <div class="tab-content tabs " >
            <div class="tab-pane card-body active p-0 custom-scroll" id="invoicetobegenerated" role="tabpanel" style=" height: 400px;">
                <?php if(count((array)$invoicetobegenerated) !=0){ ?>
                    <table class="table table-hover m-b-0" style="font-size:13px;" id="categoryfilter">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Tenant Name</th>
                                <th scope="col">Property Name</th>
                                <th scope="col" class="text-right">Monthly Rent</th>
                                <th scope="col" class="text-right">Utility Amount</th>
                                <th scope="col" class="text-right">Total Amount</th>
                                <th scope="col">Rent Due Date</th>
                                <th scope="col" class="text-center">Rent</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php $m=1; foreach($invoicetobegenerated as $ptb){
                                $bill_detail = json_decode($ptb['bill_detail'],1);
                                $utility_details = json_decode($ptb['utility_details'],1);
                                $totalamt = $utilitytotalamt =  0;
                                ?>
                                <tr>
                                    <td  class="p-2"><?php echo $m; ?></td>
                                    <td class="p-2"><?php echo $ptb['tenant_name']; ?></td>
                                    <td class="p-2"><?php echo $property_owner_details[$ptb['property_owner_detail_id']]['property_name']; ?></td>
                                    <td class="p-2 text-right"><?php echo customeummarmonyformate($ptb['rent_amount']); ?></td>
                                    <td class="p-2 text-right"><?php echo customeummarmonyformate($ptb['utility_amount']); ?></td>
                                    <td class="p-2 text-right"><?php echo customeummarmonyformate($ptb['total_amount']); ?></td>
                                    <td class="p-2" id="custom-popup"><?php echo fromDate($ptb['month_start_date']); ?>
                                    <div class="custom-popup-open open_popup_tenant_billing_<?php echo $ptb['id']; ?> text-start" style="display: none; width: 650px; right: 0px;">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5>Property Tenant Billing</h5>
                                                <div class="card-header-right">
                                                    <i class="ti-close" onclick="closePopUp('<?php echo $ptb['id']; ?>')" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Closed" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>
                                                </div>
                                            </div>
                                            <form method="post" id="form_id_<?php echo $ptb['id']; ?>">
                                                <div class="card-body">
                                                    <table class="table table-hover m-b-0" style="font-size:13px;" id="categoryfilter_2">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col" style="width: 5%;">#</th>
                                                                <th scope="col" style="width: 37%;">Utility</th>
                                                                <th scope="col" class="text-right" style="width: 18%;">Amount</th>
                                                                <th scope="col" style="width: 30%;">Remark</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php $i=1;foreach($bill_detail as $bd){
                                                                $redonlytext = $extrlebl = '';
                                                                if($bd['transaction_table']=='property_owner_detail_areas' || $bd['utility_bill_auto_and_fix']=='F'){
                                                                    $redonlytext = 'readonly';
                                                                    $extrlebl = "<span>Rent (".$bd['monthly_rent'].")+ Tax (".$bd['rent_tax'].")</span>";
                                                                }
                                                                ?>
                                                                <tr>
                                                                    <td  class="p-1"><?php echo $i; ?></td>
                                                                    <td class="p-1"><span class="d-block"><?php echo $bd['name']; ?></span><?php echo $extrlebl; ?></td>
                                                                    <td class="p-1"><input aria-label="Input field" class="form-control text-right checkamountnotepmty_<?php echo $ptb['id']; ?>" type="number" name="rent[]" value="<?php echo $bd['rent']; ?>" <?php echo $redonlytext; ?> ></td>
                                                                    <td class="p-1">
                                                                        <textarea aria-label="Remarks[]" class="form-control" name="remarks[]"><?php echo $bd['remarks']; ?></textarea>
                                                                    </td>
                                                                </tr>
                                                                <?php $i++;
} ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="card-footer pt-0 m-b-35">
                                                        <button type="button" class="input-group-text addon-style cursor-pointer float-left btn btn-primary" onclick="submitTenantBill('<?php echo $ptb['id']; ?>','S')"> Save</button>
                                                        <button type="button" class="input-group-text addon-style cursor-pointer float-left btn btn-primary m-l-5" onclick="submitTenantBill('<?php echo $ptb['id']; ?>','SG')"> Generate Invoice</button>
                                                        <button type="button" class="input-group-text addon-style cursor-pointer float-left btn btn-primary m-l-5" onclick="submitTenantBill('<?php echo $ptb['id']; ?>','SGS')"> Generate Invoice & Send Tenant</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>

                                        </td>
                                        <td class="p-2 text-center"><i class="fa fa-cog  f-16" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Utility Bill" onclick="opentTenantUtilityPopUp('<?php echo $ptb['id']; ?>')"  role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>
                                        </td>
                                    </tr>
                                    <?php  $m++;
} ?>
                                </tbody>
                            </table>
                        <?php }else{?>
                            <div class="card-px text-center py-20 m-t-30">
                                <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Invoice</p>
                                <div class="text-center px-4">
                                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="tab-pane card-body p-0 custom-scroll" id="invoicetobeemailed" role="tabpanel" style=" height: 400px;">

                        <?php if(count((array)$invoicetobeemailed) !=0){ ?>
                            <table class="table table-hover m-b-0" style="font-size:13px;" id="categoryfilter_3">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Tenant Name</th>
                                        <th scope="col">Property Name</th>
                                        <th scope="col" class="text-right">Monthly Rent</th>
                                        <th scope="col" class="text-right">Utility Amount</th>
                                        <th scope="col" class="text-right">Total Amount</th>
                                        <th scope="col">Rent Due Date</th>
                                        <th scope="col">Invoice Date</th>
                                        <th scope="col">Invoice Number</th>
                                        <th scope="col">Invoice Generated Date</th>
                                        <th scope="col"  class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $m=1; foreach($invoicetobeemailed as $ptb){
                                        $bill_detail = json_decode($ptb['bill_detail'],1);
                                        $totalamt = $utilitytotalamt =  0;
                                        foreach($bill_detail as $bd){
                                            if($bd['transaction_table']=="property_owner_detail_areas"){
                                                $totalamt += $bd['rent'];
                                            }else{
                                                $utilitytotalamt += $bd['rent'];
                                            }
                                        }
                                        ?>
                                        <tr>
                                            <td  class="p-2"><?php echo $m; ?></td>
                                            <td class="p-2"><?php echo $ptb['tenant_name']; ?></td>
                                            <td class="p-2"><?php echo $property_owner_details[$ptb['property_owner_detail_id']]['property_name']; ?></td>
                                            <td class="p-2 text-right"><?php echo customeummarmonyformate($totalamt); ?></td>
                                            <td class="p-2 text-right"><?php echo customeummarmonyformate($utilitytotalamt); ?></td>
                                            <td class="p-2 text-right"><?php echo customeummarmonyformate($totalamt + $utilitytotalamt); ?></td>
                                            <td class="p-2" ><?php echo fromDate($ptb['month_start_date']); ?></td>
                                            <td id="custom-popup_2" class="p-2"><?php echo fromDate($ptb['generated_invoice_datetime']); ?></td>
                                            <td  class="p-2"><?php echo $rental_invoices_transnumber[$ptb['rental_invoice_id']]; ?></td>
                                            <td id="custom-popup_3" class="p-2"><?php echo fromDate($ptb['generated_invoice_datetime']); ?></td>
                                            <td class="p-2 text-center">
                                                <a href="index.php?option=COM_VIEW_RENTAL_INVOICES&rid=<?php echo $ptb['rental_invoice_id']; ?>&table=rental_invoices&title=Rental Invoices" target="_blank">
                                                    <button type="button" class="input-group-text addon-style cursor-pointer float-left btn btn-primary"> View </button>
                                                </a>

                                                <button type="button" class="input-group-text addon-style cursor-pointer float-left btn btn-primary m-l-5" onclick="renatalInvoiceSend('<?php echo $ptb['id']; ?>')"> Send</button>
                                            </td>
                                        </tr>
                                        <?php  $m++;
} ?>
                                    </tbody>
                                </table>
                            <?php }else{ ?>
                                <div class="card-px text-center py-20 m-t-30">
                                    <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Invoice</p>
                                    <div class="text-center px-4">
                                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="tab-pane card-body p-0 custom-scroll" id="invoicesharedwithtenant" role="tabpanel" style=" height: 400px;">
                            <?php if(count((array)$invoicesharedwithtenant) !=0){ ?>
                                <table class="table table-hover m-b-0" style="font-size:13px;" id="categoryfilter_4">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Tenant Name</th>
                                            <th scope="col">Property Name</th>
                                            <th scope="col" class="text-right">Monthly Rent</th>
                                            <th scope="col" class="text-right">Utility Amount</th>
                                            <th scope="col" class="text-right">Total Amount</th>
                                            <th scope="col">Rent Due Date</th>
                                            <th scope="col">Invoice Date</th>
                                            <th scope="col">Invoice Number</th>
                                            <th scope="col">Invoice Shared Date</th>
                                            <th scope="col"  class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $m=1; foreach($invoicesharedwithtenant as $ptb){
                                            $bill_detail = json_decode($ptb['bill_detail'],1);
                                            $totalamt = $utilitytotalamt =  0;
                                            foreach($bill_detail as $bd){
                                                if($bd['transaction_table']=="property_owner_detail_areas"){                            $totalamt += $bd['rent'];
                                            }else{
                                                $utilitytotalamt += $bd['rent'];
                                            }
                                        }
                                        ?>
                                        <tr>
                                            <td  class="p-2"><?php echo $m; ?></td>
                                            <td class="p-2"><?php echo $ptb['tenant_name']; ?></td>
                                            <td class="p-2"><?php echo $property_owner_details[$ptb['property_owner_detail_id']]['property_name']; ?></td>
                                            <td class="p-2 text-right"><?php echo customeummarmonyformate($totalamt); ?></td>
                                            <td class="p-2 text-right"><?php echo customeummarmonyformate($utilitytotalamt); ?></td>
                                            <td class="p-2 text-right"><?php echo customeummarmonyformate($totalamt + $utilitytotalamt); ?></td>
                                            <td class="p-2" ><?php echo fromDate($ptb['month_start_date']); ?></td>
                                            <td class="p-2"><?php echo fromDate($ptb['generated_invoice_datetime']); ?></td>
                                            <td  class="p-2"><?php echo $rental_invoices_transnumber[$ptb['rental_invoice_id']]; ?></td>
                                            <td id="custom-popup_4" class="p-2"><?php echo fromDate($ptb['shared_datetime']); ?></td>
                                            <td class="p-2 text-center">
                                                <a href="index.php?option=COM_VIEW_RENTAL_INVOICES&rid=<?php echo $ptb['rental_invoice_id']; ?>&table=rental_invoices&title=Rental Invoices" target="_blank">
                                                    <button type="button" class="input-group-text addon-style cursor-pointer float-left btn btn-primary"> View </button>
                                                </a>

                                                <button type="button" class="input-group-text addon-style cursor-pointer float-left btn btn-primary m-l-5" onclick="renatalInvoiceSend('<?php echo $ptb['id']; ?>')">Re-Send</button>
                                            </td>

                                        </tr>
                                        <?php  $m++;
} ?>
                                    </tbody>
                                </table>
                            <?php }else{ ?>
                                <div class="card-px text-center py-20 m-t-30">
                                    <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Invoice</p>
                                    <div class="text-center px-4">
                                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

<div class="card">
    <div class="card-header  row">
        <div class="col-lg-10">
            <h5>Property Utility Invoices</h5>
        </div>
    </div>
    <div class="card-body">
        <ul class="nav md-tabs infisuite_tab mt-2 ml-2 mb-3">
            <li class="nav-item">
                <a class="nav-link f-w-600 f-12 active" data-bs-toggle="tab" href="#utility_invoicetobegenerated" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">  Invoice to Be Generated <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$utility_invoicetobegenerated);?></span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#utility_invoicetobeemailed" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> Invoice to be Emailed <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$utility_invoicetobeemailed);?></span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#utility_invoicesharedwithtenant" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> Invoice Shared With Tenant <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$utility_invoicesharedwithtenant);?></span></a>
            </li>
        </ul>
        <div class="tab-content tabs " >
            <div class="tab-pane card-body active p-0 custom-scroll" id="utility_invoicetobegenerated" role="tabpanel" style=" height: 400px;">
                <?php if(count((array)$utility_invoicetobegenerated) !=0){ ?>
                    <table class="table table-hover m-b-0" style="font-size:13px;" id="categoryfilter_5">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Tenant Name</th>
                                <th scope="col">Property Name</th>
                                <th scope="col" class="text-right">Utility Amount</th>
                                <th scope="col">Rent Due Date</th>
                                <th scope="col" class="text-center">Utility</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $m=1; foreach($utility_invoicetobegenerated as $ptb){
                                $utility_details = json_decode($ptb['utility_details'],1);
                                $totalamt = $utilitytotalamt =  0;
                                ?>
                                <tr>
                                    <td  class="p-2"><?php echo $m; ?></td>
                                    <td class="p-2"><?php echo $ptb['tenant_name']; ?></td>
                                    <td class="p-2"><?php echo $property_owner_details[$ptb['property_owner_detail_id']]['property_name']; ?></td>
                                    <td class="p-2 text-right"><?php echo customeummarmonyformate($ptb['utility_amount']); ?></td>

                                    <td class="p-2" id="custom-popup_5"><?php echo fromDate($ptb['month_start_date']); ?>
                                        <div class="custom-popup-open open_popup_tenant_billing_utility_<?php echo $ptb['id']; ?> text-start" style="display: none; width: 650px; right: 0px;">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>Utility Tenant Billing</h5>
                                                    <div class="card-header-right">
                                                        <i class="ti-close" onclick="closePopUp('<?php echo $ptb['id']; ?>')" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Closed" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>
                                                    </div>
                                                </div>
                                                <form method="post" id="form_id_utility_<?php echo $ptb['id']; ?>">
                                                    <div class="card-body">
                                                        <table class="table table-hover m-b-0" style="font-size:13px;" id="categoryfilter_6">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col" style="width: 5%;">#</th>
                                                                    <th scope="col" style="width: 37%;">Utility</th>
                                                                    <th scope="col" class="text-right" style="width: 18%;">Amount</th>
                                                                    <th scope="col" style="width: 30%;">Remark</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php $i=1;foreach($utility_details as $bd){
                                                                    $redonlytext = $extrlebl = '';
                                                                    if($bd['transaction_table']=='property_owner_detail_areas' || $bd['utility_bill_auto_and_fix']=='F'){
                                                                        $redonlytext = 'readonly';
                                                                        $extrlebl = "<span>Rent (".$bd['monthly_rent'].")+ Tax (".$bd['rent_tax'].")</span>";
                                                                    }
                                                                    ?>
                                                                    <tr>
                                                                        <td  class="p-1"><?php echo $i; ?></td>
                                                                        <td class="p-1"><span class="d-block"><?php echo $bd['name']; ?></span><?php echo $extrlebl; ?></td>
                                                                        <td class="p-1"><input aria-label="Input field" class="form-control text-right checkamountnotepmty_utility_<?php echo $ptb['id']; ?>" type="number" name="rent[]" value="<?php echo $bd['rent']; ?>" <?php echo $redonlytext; ?> ></td>
                                                                        <td class="p-1">
                                                                            <textarea aria-label="Remarks[]" class="form-control" name="remarks[]"><?php echo $bd['remarks']; ?></textarea>
                                                                        </td>
                                                                    </tr>
                                                                    <?php $i++;
} ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="card-footer pt-0 m-b-35">
                                                            <button type="button" class="input-group-text addon-style cursor-pointer float-left btn btn-primary" onclick="submitTenantBill('<?php echo $ptb['id']; ?>','S','utility_','U')"> Save</button>
                                                            <button type="button" class="input-group-text addon-style cursor-pointer float-left btn btn-primary m-l-5" onclick="submitTenantBill('<?php echo $ptb['id']; ?>','SG','utility_','U')"> Generate Invoice</button>
                                                            <button type="button" class="input-group-text addon-style cursor-pointer float-left btn btn-primary m-l-5" onclick="submitTenantBill('<?php echo $ptb['id']; ?>','SGS','utility_','U')"> Generate Invoice & Send Tenant</button>
                                                        </div>
                                                    </form>
                                                </div>
                                                </div>
                                        </td>
                                        <td class="p-2 text-center"><i class="fa fa-cog  f-16" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Utility Bill" onclick="opentTenantUtilityonlyPopUp('<?php echo $ptb['id']; ?>')"  role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>
                                        </td>
                                    </tr>
                                    <?php  $m++;
} ?>
                                </tbody>
                            </table>
                        <?php }else{?>
                            <div class="card-px text-center py-20 m-t-30">
                                <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Invoice</p>
                                <div class="text-center px-4">
                                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="tab-pane card-body p-0 custom-scroll" id="utility_invoicetobeemailed" role="tabpanel" style=" height: 400px;">

                        <?php if(count((array)$utility_invoicetobeemailed) !=0){ ?>
                            <table class="table table-hover m-b-0" style="font-size:13px;" id="categoryfilter_7">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Tenant Name</th>
                                        <th scope="col">Property Name</th>
                                        <th scope="col" class="text-right">Utility Amount</th>
                                        <th scope="col">Rent Due Date</th>
                                        <th scope="col">Invoice Date</th>
                                        <th scope="col">Invoice Number</th>
                                        <th scope="col">Invoice Generated Date</th>
                                        <th scope="col"  class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $m=1; foreach($utility_invoicetobeemailed as $ptb){
                                        $utility_details = json_decode($ptb['utility_details'],1);
                                        ?>
                                        <tr>
                                            <td  class="p-2"><?php echo $m; ?></td>
                                            <td class="p-2"><?php echo $ptb['tenant_name']; ?></td>
                                            <td class="p-2"><?php echo $property_owner_details[$ptb['property_owner_detail_id']]['property_name']; ?></td>
                                            <td class="p-2 text-right"><?php echo customeummarmonyformate($ptb['utility_amount']); ?></td>
                                            <td class="p-2" ><?php echo fromDate($ptb['month_start_date']); ?></td>
                                            <td id="custom-popup_6" class="p-2"><?php echo fromDate($ptb['utility_generated_invoice_datetime']); ?></td>
                                            <td  class="p-2"><?php echo $rental_invoices_transnumber[$ptb['utility_rental_invoice_id']]; ?></td>
                                            <td id="custom-popup_7" class="p-2"><?php echo fromDate($ptb['utility_generated_invoice_datetime']); ?></td>
                                            <td class="p-2 text-center">
                                                <a href="index.php?option=COM_VIEW_RENTAL_INVOICES&rid=<?php echo $ptb['utility_rental_invoice_id']; ?>&table=rental_invoices&title=Rental Invoices" target="_blank">
                                                    <button type="button" class="input-group-text addon-style cursor-pointer float-left btn btn-primary"> View </button>
                                                </a>

                                                <button type="button" class="input-group-text addon-style cursor-pointer float-left btn btn-primary m-l-5" onclick="renatalInvoiceSend('<?php echo $ptb['id']; ?>','U')"> Send</button>
                                            </td>
                                        </tr>
                                        <?php  $m++;
} ?>
                                    </tbody>
                                </table>
                            <?php }else{ ?>
                                <div class="card-px text-center py-20 m-t-30">
                                    <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Invoice</p>
                                    <div class="text-center px-4">
                                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="tab-pane card-body p-0 custom-scroll" id="utility_invoicesharedwithtenant" role="tabpanel" style=" height: 400px;">
                            <?php if(count((array)$utility_invoicesharedwithtenant) !=0){ ?>
                                <table class="table table-hover m-b-0" style="font-size:13px;" id="categoryfilter_8">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Tenant Name</th>
                                            <th scope="col">Property Name</th>
                                            <th scope="col" class="text-right">Utility Amount</th>
                                            <th scope="col">Rent Due Date</th>
                                            <th scope="col">Invoice Date</th>
                                            <th scope="col">Invoice Number</th>
                                            <th scope="col">Invoice Shared Date</th>
                                            <th scope="col"  class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $m=1; foreach($utility_invoicesharedwithtenant as $ptb){
                                            $utility_details = json_decode($ptb['utility_details'],1);

                                        ?>
                                        <tr>
                                            <td  class="p-2"><?php echo $m; ?></td>
                                            <td class="p-2"><?php echo $ptb['tenant_name']; ?></td>
                                            <td class="p-2"><?php echo $property_owner_details[$ptb['property_owner_detail_id']]['property_name']; ?></td>
                                            <td class="p-2 text-right"><?php echo customeummarmonyformate($ptb['utility_amount']); ?></td>
                                            <td class="p-2" ><?php echo fromDate($ptb['month_start_date']); ?></td>
                                            <td class="p-2"><?php echo fromDate($ptb['utility_generated_invoice_datetime']); ?></td>
                                            <td  class="p-2"><?php echo $rental_invoices_transnumber[$ptb['utility_rental_invoice_id']]; ?></td>
                                            <td id="custom-popup_8" class="p-2"><?php echo fromDate($ptb['utility_shared_datetime']); ?></td>
                                            <td class="p-2 text-center">
                                                <a href="index.php?option=COM_VIEW_RENTAL_INVOICES&rid=<?php echo $ptb['utility_rental_invoice_id']; ?>&table=rental_invoices&title=Rental Invoices" target="_blank">
                                                    <button type="button" class="input-group-text addon-style cursor-pointer float-left btn btn-primary"> View </button>
                                                </a>

                                                <button type="button" class="input-group-text addon-style cursor-pointer float-left btn btn-primary m-l-5" onclick="renatalInvoiceSend('<?php echo $ptb['id']; ?>','U')">Re-Send</button>
                                            </td>

                                        </tr>
                                        <?php  $m++;
} ?>
                                    </tbody>
                                </table>
                            <?php }else{ ?>
                                <div class="card-px text-center py-20 m-t-30">
                                    <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Invoice</p>
                                    <div class="text-center px-4">
                                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

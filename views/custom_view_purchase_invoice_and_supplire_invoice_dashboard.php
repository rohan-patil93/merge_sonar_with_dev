<?php
$_SESSION['apply_js']['PURCHASE_INVOICE_AND_SUPPLIRE_INVOICE_DASHBOARD'] = 1;
$url = API."?mode=getpurchaseinvoiceandsupplireinvoicedashboard&admin_type=".$_SESSION['admin_type'];
$response = callAPI($url);
$result = json_decode($response,1);
$invoice_detail = $result['invoice_detail'];
$spend_by_vendors = $result['spend_by_vendors'];
$spend_by_customers = $result['spend_by_customers'];
$spend_by_vendor_category = $result['spend_by_vendor_category'];
$spend_by_vendor_category_graph = array();
foreach($spend_by_vendor_category as $sbvc){
    $spend_by_vendor_category_graph[] = array($sbvc['category_description'], round((float)$sbvc['total_amount'], 2));
}
$spend_by_customer_category = $result['spend_by_customer_category'];
$spend_by_customer_category_graph = array();
foreach($spend_by_customer_category as $sbvc){
    $spend_by_customer_category_graph[] = array($sbvc['category_description'], round((float)$sbvc['total_amount'], 2));
}
$purchase_invoices_detail =$supplier_bills_detail =$requisitions_detail =$quotations_detail = array();
if($invoice_detail['purchase_invoices'] !=""){
    $purchase_invoices_detail = explode('||', $invoice_detail['purchase_invoices']);
}
if($invoice_detail['requisitions'] !=""){
    $requisitions_detail = explode('||', $invoice_detail['requisitions']);
}
if($invoice_detail['quotations'] !=""){
    $quotations_detail = explode('||', $invoice_detail['quotations']);
}
if($invoice_detail['sales_invoices'] !=""){
    $sales_invoices_detail = explode('||', $invoice_detail['sales_invoices']);
}
if($invoice_detail['enquiries'] !=""){
    $enquiries_detail = explode('||', $invoice_detail['enquiries']);
}
if($invoice_detail['estimates'] !=""){
    $estimates_detail = explode('||', $invoice_detail['estimates']);
}
?>

<script type="text/javascript">

function getEnquiryVendororCustomer(valu){
    if(valu.length >= 3){
        var searchvendorcustomertype = "vendors";
        var htmloader = '<div class="text-center"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $('#search_result_employee').html(htmloader);
        $("#search_result_employee").show('slow');
        jQuery.ajax({
            url: "ajax.php",
            data: "page=getenquiryvendororcustomer&value="+valu+"&searchvendorcustomertype="+searchvendorcustomertype,
            type: "POST",
            success:function(data){
                $("#search_result_employee").html(data);
            }
        });
    }else{
        $("#search_result_employee_1").hide();
    }
}
function getEnquiryVendororCustomer1(valu){
    if(valu.length >= 3){
        var searchvendorcustomertype = "customers";
        var htmloader = '<div class="text-center"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $('#search_result_employee_1').html(htmloader);
        $("#search_result_employee_1").show('slow');
        jQuery.ajax({
            url: "ajax.php",
            data: "page=getenquiryvendororcustomer&value="+valu+"&searchvendorcustomertype="+searchvendorcustomertype,
            type: "POST",
            success:function(data){
                $("#search_result_employee_1").html(data);
            }
        });
    }else{
        $("#search_result_employee_1").hide();
    }
}
function getEnquiryVendororCustomerPartyDetailspopup(party_id,part_table=""){
    $.ajax({
        url: "views/custom_view_party_detail_popup.php",
        data:'part_table='+part_table+'&party_id='+party_id,
        type: "POST",
        success:function(data){
            $('#dynamic_popup1').html(data);
            $('#kt_modal_dynamic_popup').modal("show");
        }
    });
}

</script>

<div class="row">
    <div class="col-lg-6">
        <div class="row">
                <div class="col-lg-6">
                <div class="card comp-card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto p-r-0 p-l-5">
                                <i class="fa fa-user-plus bg-c-red"></i>
                            </div>
                            <div class="col p-l-5">
                                <h3 class="f-w-700 text-c-black f-18 mb-1">Requisitions</h3>
                                <p class="m-b-0 f-16"><?php echo $requisitions_detail[1] !=""?customeNumberFormate($requisitions_detail[1]):0; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card comp-card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto p-r-0 p-l-5">
                                <i class="fa fa-user-plus bg-c-red"></i>
                            </div>
                            <div class="col p-l-5">
                                <h3 class="f-w-700 text-c-red f-18 mb-1">Quotations</h3>
                                <p class="m-b-0 f-16"><?php echo $quotations_detail[1] !=""?customeNumberFormate($quotations_detail[1]):0; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card comp-card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto p-r-0 p-l-5">
                                <i class="fa fa-user-plus bg-c-blue"></i>
                            </div>
                            <div class="col p-l-5">
                                <h3 class="f-w-700 text-c-blue f-18 mb-1">Invoice Amount</h3>
                                <p class="m-b-0 f-16"><?php echo $purchase_invoices_detail[1] !=""?customeNumberFormate($purchase_invoices_detail[1]):0; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card comp-card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto p-r-0 p-l-5">
                                <i class="fa fa-user-plus bg-c-yellow"></i>
                            </div>
                            <div class="col p-l-5">
                                <h3 class="f-w-700 text-c-yellow f-18 mb-1">Total Payments</h3>
                                <p class="m-b-0 f-16"><?php echo $purchase_invoices_detail[2] !=""?customeNumberFormate($purchase_invoices_detail[1]-$purchase_invoices_detail[2]):0; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
         <div class="row">
            <div class="col-lg-12">
                <div class="card mb-1 text-center bg-c-blue">
                    <div class="card-body">
                        <h3 class="f-18 mb-0 text-white">Spend by Category</h3>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <input type="hidden" name="chart_data_gender" id="chart_data_bill_vendor" value='<?php echo json_encode((array)$spend_by_vendor_category_graph); ?>' >
                        <?php if(0 =='1'){ ?>
                          <div class="card-px text-center py-20 m-t-30">
                                <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Data</p>
                                <div class="text-center px-4">
                                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                </div>
                            </div>
                        <?php }else{ ?>
                          <div id="chart_by_category" style="width: 100%; height: 400px;"></div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card mb-1 text-center bg-c-green">
                    <div class="card-body">
                        <h3 class="f-18 mb-0 text-white">Spend By Vendors  Top 5</h3>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th scope="col">Vendor</th>
                                    <th scope="col" style="text-align: right;">Purchase Invoices</th>
                                    <th scope="col" style="text-align: right;">Item Quantity</th>
                                    <th scope="col" style="text-align: right;">Total Amount</th>
                                </tr>
                                <?php foreach($spend_by_vendors as $sbv){ ?>
                                <tr>
                                    <td class="p-2"><?php echo $sbv['vendor_company'];?></td>
                                    <td style="text-align: right;" class="p-2"><?php echo $sbv['total_invoice_cnt'];?></td>
                                    <td style="text-align: right;" class="p-2"><?php echo $sbv['item_quantity'];?></td>
                                    <td style="text-align: right;" class="p-2"><?php echo customeNumberFormate($sbv['total_amount']);?></td>
                                </tr>
                                <?php } ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card mb-1 text-center bg-c-blue">
                    <div class="card-body">
                        <h3 class="f-18 mb-0 text-white">Vendor Search</h3>
                    </div>
                </div>
                <div class="card-body" >
                    <div class="col-lg-12">
                        <div class="input-group input-group-button mb-0">
                            <input type="text" class="form-control form-control-sm width1" placeholder="Enter Minimum 3 Characters" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Search" aria-label="Search" onkeyup="getEnquiryVendororCustomer(this.value)">
                            <div class="input-group-text addon-style">
                                <span class=""><i class="ti-search text-white mt-2"></i></span>
                            </div>
                        </div>
                    </div>
                    <div id="search_result_employee" class="mt-2 custom-scroll" style=" min-height: 350px; max-height: 350px;">
                        <div class="card-px text-center py-20 m-t-30">
                            <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Vendor</p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="row">
            <div class="col-lg-6">
                <div class="card comp-card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto p-r-0 p-l-5">
                                <i class="fa fa-user-plus bg-c-red"></i>
                            </div>
                            <div class="col p-l-5">
                                <h3 class="f-w-700 text-c-black f-18 mb-1">Enquiry</h3>
                                <p class="m-b-0 f-16"><?php echo $enquiries_detail[1] !=""?customeNumberFormate($enquiries_detail[1]):0; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card comp-card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto p-r-0 p-l-5">
                                <i class="fa fa-user-plus bg-c-red"></i>
                            </div>
                            <div class="col p-l-5">
                                <h3 class="f-w-700 text-c-red f-18 mb-1">Estimates</h3>
                                <p class="m-b-0 f-16"><?php echo $estimates_detail[1] !=""?customeNumberFormate($estimates_detail[1]):0; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card comp-card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto p-r-0 p-l-5">
                                <i class="fa fa-user-plus bg-c-blue"></i>
                            </div>
                            <div class="col p-l-5">
                                <h3 class="f-w-700 text-c-blue f-18 mb-1">Invoice Amount</h3>
                                <p class="m-b-0 f-16"><?php echo $sales_invoices_detail[1] !=""?customeNumberFormate($sales_invoices_detail[1]):0; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card comp-card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto p-r-0 p-l-5">
                                <i class="fa fa-user-plus bg-c-yellow"></i>
                            </div>
                            <div class="col p-l-5">
                                <h3 class="f-w-700 text-c-yellow f-18 mb-1">Total Payments</h3>
                                <p class="m-b-0 f-16"><?php echo $sales_invoices_detail[2] !=""?customeNumberFormate($sales_invoices_detail[1]-$sales_invoices_detail[2]):0; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card mb-1 text-center bg-c-blue">
                    <div class="card-body">
                        <h3 class="f-18 mb-0 text-white">Sale By Category</h3>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <input type="hidden" name="chart_data_gender" id="chart_data_bill_customer" value='<?php echo json_encode((array)$spend_by_customer_category_graph); ?>' >
                        <?php if(0 =='1'){ ?>
                          <div class="card-px text-center py-20 m-t-30">
                                <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Data</p>
                                <div class="text-center px-4">
                                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                </div>
                            </div>
                        <?php }else{ ?>
                          <div id="chart_by_customer" style="width: 100%; height: 400px;"></div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card mb-1 text-center bg-c-green">
                    <div class="card-body">
                        <h3 class="f-18 mb-0 text-white">Sales by Customer Top 5</h3>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                             <table class="table table-bordered">
                                <tr>
                                    <th scope="col">Customer</th>
                                    <th scope="col" style="text-align: right;">Sale Invoices</th>
                                    <th scope="col" style="text-align: right;">Item Quantity</th>
                                    <th scope="col" style="text-align: right;">Total Amount</th>
                                </tr>
                                <?php foreach($spend_by_customers as $sbv){ ?>
                                <tr>
                                    <td class="p-2"><?php echo $sbv['customer_company'];?></td>
                                    <td style="text-align: right;" class="p-2"><?php echo $sbv['total_invoice_cnt'];?></td>
                                    <td style="text-align: right;" class="p-2"><?php echo $sbv['item_quantity'];?></td>
                                    <td style="text-align: right;" class="p-2"><?php echo customeNumberFormate($sbv['total_amount']);?></td>
                                </tr>
                                <?php } ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card mb-1 text-center bg-c-blue">
                    <div class="card-body">
                        <h3 class="f-18 mb-0 text-white">Customer Search</h3>
                    </div>
                </div>
                <div class="card-body" >
                      <div class="col-lg-12">
                        <div class="input-group input-group-button mb-0">
                            <input type="text" class="form-control form-control-sm width1" placeholder="Enter Minimum 3 Characters" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Search" aria-label="Search" onkeyup="getEnquiryVendororCustomer1(this.value)">
                            <div class="input-group-text addon-style">
                                <span class=""><i class="ti-search text-white mt-2"></i></span>
                            </div>
                        </div>
                    </div>
                    <div id="search_result_employee_1" class="mt-2 custom-scroll" style=" min-height: 350px; max-height: 350px;" >
                        <div class="card-px text-center py-20 m-t-30">
                            <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Customer</p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

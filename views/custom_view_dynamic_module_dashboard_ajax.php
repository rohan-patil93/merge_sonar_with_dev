<?php 
if($_POST['ajaxcall'] != 'Y'){
    include_once('includes/session.php');
    include_once('dbconfig.php');
    include_once('comm.php');
}else{
    include_once('../includes/session.php');
    include_once('../dbconfig.php');
    include_once('../comm.php');
}
$optiondetails = getOptionPageTableName($_POST['option']);
$url = API."?mode=getdynamicmoduledashboard&transaction_table=".$optiondetails['application_table_name']."&admin_type=".$_SESSION['admin_type']."&admin_access_employee=".$_SESSION['admin_access_employee'];
$apires = callAPI($url);
$result = json_decode($apires,1);
//echo "<pre>"; print_r($result['transaction_approval_logs_sql']); echo "</pre>";
$category_data = $result['category_data'];
$categorydatalist = array();
foreach($category_data as $category_data){
    $categorydatalist[$category_data['category_id']][] = $category_data;
}
$item_categories = $result['item_categories'];
$holidaydata = $result['holidays'];
$holidays = nearestDateFirstShow($holidaydata,'holiday_date');
$location_data = $result['location_data'];
$locations = $result['locations'];
$locationlist = array();
foreach($location_data as $locationdata){
    if($locationdata['location_id'] !="0" AND $locationdata['location_id'] !=""){
        $locationlist[$locationdata['location_id']][] = $locationdata;
    }
}
$cost_center_datalist = $result['cost_center_name'];
$cost_center_data = $result['cost_center_data'];
$costcenterlist = array();
foreach($cost_center_data as $costcenterdata){
    if($costcenterdata['cost_center_id'] !="0" AND $costcenterdata['cost_center_id'] !=""){
        $costcenterlist[$costcenterdata['cost_center_id']][] = $costcenterdata;
    }
}
$statuse = ViewStatusData();
$allrecords = $result['status_table_list'];
$draftres = array();
$activeres = array();
$inprocessres = array();
$approvedres = array();
$alltransaction = array();
$transactionlogcnt = 0;
$createtransactioncnt = 0;
foreach($allrecords as $allrecord){
    $alltransaction[$allrecord['id']] = $allrecord;
    if($allrecord['current_status'] == ST_DRAFT){
        if($allrecord['originator']  == $_SESSION['userid']){
            $draftres[] = $allrecord;
            $transactionlogcnt++;
        }
    }elseif($allrecord['current_status'] == ST_ACTIVE){
        if($allrecord['originator']  == $_SESSION['userid']){
            $activeres[] = $allrecord;
            $transactionlogcnt++;
            $totaldocsamt += $allrecord['total_amount'];
        }
    }elseif($allrecord['current_status'] == ST_APPROVED){
        if($allrecord['originator']  == $_SESSION['userid']){
            $approvedres[] = $allrecord;
            $approvedresamt += $allrecord['total_amount'];
            $totaldocsamt += $allrecord['total_amount'];
            $transactionlogcnt++;
        }
    }else{
        if($allrecord['originator']  == $_SESSION['userid']){
            $inprocessres[] = $allrecord;
            $totaldocsamt += $allrecord['total_amount'];
            $transactionlogcnt++;
        }
    }
}
//echo "<pre>"; print_r($result['transaction_approval_logs_sql']); echo "</pre>";
foreach($result['transaction_approval_logs'] as $transactionlog){
    //print_r($transactionlog);
    //if($transactionlog['response_type'] =='0' AND $transactionlog['send_back_flag'] == '0'){
    if($transactionlog['response_type'] =='0' AND $transactionlog['send_back_flag'] == '0' AND $transactionlog['call_back'] == '1'){
        $createtransactioncnt++;
        $pendinglog[$transactionlog['transaction_id']] = $transactionlog;   
    }elseif($transactionlog['response_type'] == ST_SEND_BACK){
        $sendbacklogcheck[$transactionlog['transaction_id']] = $transactionlog;
    }elseif($transactionlog['response_type'] == ST_REJECTED){
        $rejectlogcheck[$transactionlog['transaction_id']] = $transactionlog;
    }else{
        $createtransactionapprovedcnt++;
        $aprovedlog[$transactionlog['transaction_id']] = $transactionlog;   
    }
}
foreach($sendbacklogcheck as $transid => $transactionlog){
    if(!isset($pendinglog[$transid]) AND !isset($aprovedlog[$transid])){
        $sendbacklog[$transid] = $transactionlog;
    }
}
foreach($rejectlogcheck as $transid => $transactionlog){
    if(!isset($pendinglog[$transid]) AND !isset($aprovedlog[$transid])){
        $rejectlog[$transid] = $transactionlog;
    }
}

$status_progress = $result['status_progress'];
$allpermision = $_SESSION['role_permission'][$optiondetails['application_table_name']];
//echo "<pre>"; print_r($sendbacklog); echo "</pre>";
$permission_listbtn = $insert_permissionbtn = $permission_importbtn = $edit_permissionbtn = $approval_permissionbtn = "";
if($allpermision['permission_view'] == '1'){$permission_listbtn = "disabled"; }
if($allpermision['permission_insert'] == '1'){$insert_permissionbtn = "disabled"; }
if($allpermision['permission_import'] == '1'){$permission_importbtn = "disabled"; }
if($allpermision['permission_update'] == '1'){$edit_permissionbtn = "disabled"; }
//if($allpermision['permission_approval'] == '1'){$approval_permissionbtn = "disabled"; }
//echo "<pre>";print_r($result['generatesql']);echo "</pre>";
$generatedocs = $result['generate'];
$table_name = $optiondetails['application_table_name'];
//echo "table name - ".$table_name;
//print_r($optiondetails['transaction_approval_logs/']);
 // echo "<pre>";
 //  print_r($result['transaction_approval_logs_sql']);
 //  echo "</pre>";
?>
<script type="text/javascript">
    function viewAllRecord(transaction_table,type){
    //console.log(transaction_table);
    var optionurl = '<?php echo $optiondetails['insert_page_name']; ?>';
    jQuery.ajax({
        url: "views/custom_view_dynamic_module_dashboard_popup.php",
        data:'transaction_table='+transaction_table+'&type='+type+'&optionurl='+optionurl,
        type: "POST",
        success:function(data){
            $('#dynamic_popup1').html(data);
            $("#kt_modal_dynamic_popup").modal("show");
        },
        error:function (){}
    });
}
function changestatus(val,tranid,allowitemid,table_name){
            var x = val;
            //var table_name = '<?php echo $table_name; ?>'
            jQuery.ajax({
                url: "views/custom_quotation_vender_pop.php",
                data:'table='+table_name+'&field='+x+'&rid='+tranid+'&tranid='+tranid+'&allowitemid='+allowitemid,
                type: "POST",
                success:function(data){
                    $('#popupm').html(data);
                    $('#kt_modal_KTDatatable_remotem').modal("show");
                    $(".js-example-basic-multiple").select2(); 
                },
                error:function (){}
            });
        }
        function openApprovedPage(table,clone){
            var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;"></div>';
            $("#tabsopen").html(loader);
            jQuery.ajax({
                url: "views/custom_list_approved_table.php",
                data:'table='+table+'&clone='+clone+'&ajaxpage=Y',
                type: "POST",
                success:function(data){
                    $('#tabsopen').html(data);
                    $(".js-example-basic-single").select2(); 
                    $( ".datepicker" ).datepicker({
                        showOn: "both",
                        buttonText: "<i class='ti-calendar text-dark f-20 f-w-800 m-r-10'></i>",
                        dateFormat: date_format,
                        yearRange: "-60:+10",
                        setDate: new Date(),
                        changeMonth: true,
                        changeYear: true
                    });
                },
                error:function (){}
            });
        } 
        function updateApplicationFavourites(application_table_name,transaction_id,type){
    if(type == 'F'){
        var msg = '<?php echo $_SESSION['error_message']['9133']; ?>';
    }else{
        var msg = '<?php echo $_SESSION['error_message']['9134']; ?>';
    }
    jQuery.ajax({
        url: "ajax.php",
        data:'page=updateapplicationfavourites&application_table_name='+application_table_name+'&transaction_id='+transaction_id+'&type='+type,
        type: "POST",
        success:function(data){
            console.log(data);
            
            swal({
                title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                text: msg,
                type: "success",
                showCancelButton: false,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Ok",
                cancelButtonText: "No",
                closeOnConfirm: true,
                closeOnCancel: true
            },
            function(isConfirm) {
                if (isConfirm) {
                    location.reload();
                }
            });
        },
        error:function (){}
    });
}
function getSchedulesOrders(){
    var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;"></div>';
    $("#schedules_tab").html(loader);
    jQuery.ajax({
        url: "views/custom_view_schedules_orders.php",
        data:'table=1',
        type: "POST",
        success:function(data){
            $('#schedules_tab').html(data);
        },
        error:function (){}
    });
}

function getEnquiryVendororCustomer(valu){
    if(valu.length >= 3){
        var searchvendorcustomertype = $('.searchvendorcustomertype:checked').val();
        var htmloader = '<div class="text-center"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;"></div>';
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
        $("#search_result_employee").hide();
    }
}
function getEnquiryVendororCustomerPartyDetailspopup(party_id,part_table=''){
        //var part_table = $('.searchvendorcustomertype:checked').val();
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
    <input type="hidden" id="transaction_option_name" value='<?php echo $_POST['option']; ?>'>

    <div class="col-lg-9" style="padding: 0px !important;">
        <div class="row" style="padding: 0px !important; margin: 0px!important;">
            <div class="col-lg-3">
                <div class="card sale-card mb-2">
                    <div class="card-header pb-mob">
                        <h5><?php echo $_POST['menu_label']; ?></h5>
                        <div class="card-header-right custom-right-header">
                            <a href="index.php?option=<?php echo $optiondetails['insert_page_name']; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Add <?php echo $_POST['menu_label']; ?>" <?php echo $insert_permissionbtn; ?>> <i class="icofont icofont-plus icon-dark"></i> </a>
                            <a href="index.php?option=<?php echo $optiondetails['list_page_name']; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="<?php echo $_POST['menu_label']; ?> List "<?php echo $permission_listbtn; ?>> <i class="ti ti-timer icon-dark"></i></a>
                            <a href="#!" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="<?php echo $_POST['menu_label']; ?> Details "<?php echo $permission_listbtn; ?> onclick="getTransactionInvoiceList()"> <i class="ti-eye icon-dark"></i></a>
                        </div>
                    </div>
                    <div class="card-block text-center">
                         
<?php 
$approvedresamt = $approvedresamt !=""?$approvedresamt:0;
$totaldocsamt = $totaldocsamt !=""?$totaldocsamt:0;
$totaldoc = $totaldocsamt+$approvedresamt; 
if($totaldoc != 0 AND $totaldoc !=""){

    $approvedocs = 100-round((($totaldocsamt-$approvedresamt)/$totaldoc)*100);
}else{
    $approvedocs = 0;
}
 ?>
                        <div class="m-b-10" style="text-align: -webkit-center;">
                            <div class="circle_gradiant" style="--p1: <?php echo customNanAndEmptyValidation((($approvedocs)*3.6)); ?>deg; --p2_color: #ff5370;--p1_color: #2ed8b6;">
                                <?php 
                        $disname = $_SESSION['user']['display_name']!=""?$_SESSION['user']['display_name']:$_SESSION['display_name'];
                        if($_SESSION['user']['user_photo']!=""){ ?>
                          <img src="<?php echo "uploads/all/".$_SESSION['user']['user_photo']; ?>" class="percentag-img-radius img-90" alt="image" style="border-radius: 50%;border: 8px solid #ffffff;">
                       <?php }elseif($_SESSION['employee']['gender'] !=""){ ?>
                                <img src="uploads/all/<?php echo $_SESSION['employee']['gender'].".png"; ?>" class="percentag-img-radius img-90" alt="image" style="border-radius: 50%;border: 8px solid #ffffff;">
                        <?php }else{ ?>
                          <span class="text-dark f-16 f-w-600 d-block" style="    background-color: white !important;"><?php echo initials($disname); ?></span>
                      <?php } ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="s-caption bg-c-red m-0"></div>
                                <div class="s-cont d-inline-block">
                                    <h5 class="f-w-700 m-b-0  f-12"><?php 
                                    //echo number_format($totaldocsamt)."</br>";
                                    echo convert_Rupee_Number($totaldocsamt !=''?$totaldocsamt:"0"); ?></h5>
                                    <p class="m-b-0 f-10">Total Value</p>
                                </div>
                            </div>
                            <div class="col-6 b-l-default">
                                <div class="s-caption bg-c-green m-0"></div>
                                <div class="s-cont d-inline-block">
                                    <h5 class="f-w-700 m-b-0 f-12"><?php 
                                    //echo number_format($approvedresamt)."</br>";
                                    echo convert_Rupee_Number($approvedresamt !=''?$approvedresamt:"0"); ?></h5>
                                    <p class="m-b-0 f-10">Approved </p>
                                </div>
                            </div>
                        </div>
                        </div>
                </div>
                <div class="card sale-card mb-2">
                    <div class="card-header">
                        <h5>Announcements</h5>
                    </div>
                    <div class="card-block text-center">
                        <h2 class="text-facebook m-b-20"><i class="ti-announcement "></i></h2>
                        <div class="row mt-2">
                            <?php 
                            $anouncement =  API."?mode=togetlatestannouncement";
                            $anouncement_list = callAPI($anouncement);  
                            $anouncement_list = json_decode($anouncement_list,1);
                                                //print_r($anouncement_list);           
                            ?>
                            <p><?php echo substr($anouncement_list['announcement_message'],0,250);?></p>
                            <p class="m-b-0 m-t-15"><a  href="index.php?option=COM_VIEW_ANNOUNCEMENTS" class="btn btn-info waves-effect waves-light btn_mobile"><?php echo $anouncement_list['button_label']; ?></a></p>
                        </div>
                    </div>
                </div>
                <div class="card sale-card mb-2">
                    <div class="card-header">
                        <h5>Holidays </h5>
                        <div class="card-header-right table-card-header ">
                            <span class="text-dark f-w-600"><?php echo date('Y');?></span>
                        </div>
                        
                    </div>
                    <div class="card-block custom-scroll" style=" height: 300px;">
                        <?php 
                        if(!empty($holidays)) {   
                            foreach ($holidays as  $holiday) {

                                $Notification_Message = $holiday['holiday_name'];
                                $Notification_DateTime = date('d M',strtotime($holiday['holiday_date']));
                                ?>
                                <div class="align-middle m-b-10" style="border-bottom:1px dashed #cccccc;">
                                    <!-- <span  alt="user image" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($Notification_Message); ?></span> -->
                                    <div class="">
                                        <p class="text-muted m-b-5 f-12"><?php echo $Notification_Message; ?>  <span class="float-right"><?php echo $Notification_DateTime; ?></span></p>
                                    </div>
                                </div>
                            <?php } }else{ 
                                echo "<h4><i class='fa fa-warning'></i> No Holidays Available</h4>";
                            } ?>
                        </div>
                    </div>
                    <div class="card mb-2" >
                        <div class="card-header table-card-header ">
                            <h5>Search vendor/customer</h5>
                            <div class="card-header-right"></div>
                        </div>
                        <div class="card-body" >
                            <div class="col-lg-12">
                                <div class="form-radio">
                                    <div class="radio radio-inline">
                                        <label class="form-label">
                                        <input type="radio" name="searchvendorcustomertype" value="vendors" class="searchvendorcustomertype" checked="checked">
                                        <i class="helper"></i>Vendor
                                        </label>
                                    </div>
                                    <div class="radio radio-inline">
                                        <label class="form-label">
                                        <input type="radio" name="searchvendorcustomertype" value="customers" class="searchvendorcustomertype">
                                        <i class="helper"></i>Customer
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="input-group input-group-button mb-0">
                                    <input type="text" class="form-control form-control-sm width1" placeholder="Enter Minimum 3 Characters" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Search" aria-label="Search" onkeyup="getEnquiryVendororCustomer(this.value)"> 
                                    <div class="input-group-text addon-style">
                                        <label class=""><i class="ti-search text-white mt-2"></i></label>
                                    </div>
                                </div>
                            </div>
                            <div id="search_result_employee" class="mt-2 custom-scroll" style=" min-height: 160px; max-height: 160px; display: none">
                                
                            </div>
                        </div>
                    </div>




                </div>
                <div class="col-lg-9">
                    <ul class="nav md-tabs infisuite_tab mb-2" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link f-w-600 f-12 active" data-bs-toggle="tab" href="#selfdocuments" role="tab" aria-selected="true"><?php echo $_POST['menu_label']; ?></a>
                        </li>
                        <?php  foreach($generatedocs as $trnstabkey=>$gdocs){ ?>
                            <li class="nav-item">
                                <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#tabsopen" role="tab" aria-selected="true" onclick="openApprovedPage('<?php echo $trnstabkey; ?>','<?php echo $table_name; ?>')" ><?php echo "Approved ".ucwords(str_replace('_',' ',$trnstabkey)); ?>
                                <!-- <label class="form-label label label-xs label-success m-l-10"><?php echo count((array)$gdocs);?></label>-->
</a>                            </li>
                        <?php } ?>
                        <!-- <li class="nav-item float-right">
                            <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#schedules_tab" role="tab" aria-selected="true" onclick="getSchedulesOrders()">Schedules</a>
                        </li> -->
                        <?php  /** if($table_name == 'work_orders'){ ?>
                        <li class="nav-item">
                            <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#itemssummary" role="tab" aria-selected="true">Items Summary</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#partysummary" role="tab" aria-selected="true">Party Summary</a>
                        </li>
                        <?php } **/ ?>
                    </ul>
                    <div class="tab-content tabs">
                        <?php /** if($table_name == 'work_orders'){ ?>
                        <div class="tab-pane card-body p-0" id="partysummary" role="tabpanel">  
                            <?php 
                            $_GET['company_id'] = 1;
                            $_GET['data_name'] = "infistaqin_infisuite_2";
                            $_GET['user_id'] = 459;
                            include("../apis/custom_vendor_customer_finance_summary.php"); ?>
                        </div>
                        <div class="tab-pane card-body p-0" id="itemssummary" role="tabpanel">  
                            <?php 
                            $_GET['company_id'] = 1;
                            $_GET['data_name'] = "infistaqin_infisuite_2";
                            $_GET['user_id'] = 459;
                            include("../apis/custom_items_summary.php"); ?>
                        </div>
                        <?php } **/ ?>
                        <div class="tab-pane card-body p-0" id="schedules_tab" role="tabpanel"> 
                        </div>
                        <div class="tab-pane card-body active p-0" id="selfdocuments" role="tabpanel">  
                    <div class="card mb-2">
                        <ul class="nav md-tabs infisuite_tab mt-2 ml-2" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link f-w-600 f-12 active" data-bs-toggle="tab" href="#itemcategory" role="tab" aria-selected="true">
                                    <?php 
                                    $itemcatname = "Item Categories";
                                    if($_SESSION['work_module_setup_id'] == PETTY_CASH_MODULE){ 
                                        $itemcatname =  "Petty Cash"; 
                                    //}elseif($_SESSION['work_module_setup_id'] == ACCOUNT_MODULE || $table_name == "debit_notes" || $table_name == "credit_notes"){
                                    //}elseif($_SESSION['work_module_setup_id'] == ACCOUNT_MODULE){ 
                                    }elseif($table_name == "debit_notes" || $table_name == "credit_notes" || $table_name == "journal_vouchers" || $table_name =="bank_vouchers" || $table_name =="cash_vouchers"){
                                        $itemcatname =  "Transaction Type"; 
                                        $item_categories = array(array('id'=>'purchase_invoices','category_description'=>'Purchase Invoices'),array('id'=>'supplier_bills','category_description'=>'Supplier Bills'),array('id'=>'supplier_bill_cancellations','category_description'=>'Supplier Bill Cancellations'),array('id'=>'payments','category_description'=>'Payments'),array('id'=>'receipts','category_description'=>'Receipts'),array('id'=>'sales_invoices','category_description'=>'Sales Invoices'),array('id'=>'sales_returns','category_description'=>'Sales Returns'),array('id'=>'sales_invoice_cancellations','category_description'=>'Sales Invoice Cancellations'),array('id'=>'goods_receipt_notes','category_description'=>'Goods Receipt Notes'),array('id'=>'grn_return','category_description'=>'GRN Return'),array('id'=>'delivery_orders','category_description'=>'Delivery Orders'),array('id'=>'debit_notes','category_description'=>'Debit Notes'),array('id'=>'credit_notes','category_description'=>'Credit Notes'),array('id'=>'stock_transfers','category_description'=>'Stock Transfers'),array('id'=>'stock_transfer_receipts','category_description'=>'Stock Transfer Receipts'),array('id'=>'stock_transfer_returns','category_description'=>'Stock Transfer Returns'),array('id'=>'stock_adjustments','category_description'=>'Stock Adjustments'),array('id'=>'manual','category_description'=>'Manual'));
                                    }
                                      ?> 
                                    <?php echo $itemcatname; ?><label class="form-label label label-xs label-success m-l-10"><?php echo array_sum(array_map("count", $categorydatalist)); ?></label></a>

                            </li>
                            <?php if($table_name != "debit_notes" AND $table_name != "credit_notes" AND $table_name != "journal_vouchers" AND $table_name !="bank_vouchers" AND $table_name !="cash_vouchers"){ ?>
                            <li class="nav-item">
                                <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#itemlocation" role="tab" aria-selected="true">Locations<label class="form-label label label-xs label-success m-l-10"><?php echo array_sum(array_map("count", $locationlist)); ?></label></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#costcenter" role="tab" aria-selected="true">Cost Centers<label class="form-label label label-xs label-success m-l-10"><?php echo array_sum(array_map("count", $costcenterlist)); ?></label></a>
                            </li>
                            <?php } ?>
                            <?php /** foreach($generatedocs as $trnstabkey=>$gdocs){ ?>
                            <li class="nav-item">
                                <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#tabs_<?php echo $trnstabkey; ?>" role="tab" aria-selected="true"><?php echo "Approved ".ucwords($trnstabkey); ?><label class="form-label label label-xs label-success m-l-10"><?php echo count((array)$gdocs);?></label></a>
                            </li>
                            <?php }  **/ ?>
                        </ul>
                        <div class="tab-content tabs custom-scroll" style="max-height: 1000px;">
                            <div class="tab-pane card-body active p-0" id="itemcategory" role="tabpanel">
                                <div class="card-header">
                                    <h5><?php echo $itemcatname; ?></h5>
                                    <div class="card-header-right table-card-header">
                      <div class="input-group input-group-button mb-0" >
                                    <input type="text" id="dynamicfilterhideshow" data-id='categoryfilter' data-type="tr" class="form-control form-control-sm width1"  placeholder="Search" data-bs-toggle="tooltip" data-placement="bottom" title="Search"> 
                                    <span class="input-group-text addon-style">
                                        <label class=""><span class="ti-search text-white mt-2"></span></label>
                                    </span>
                                </div>
                  </div>
                                </div>
                                <div class="card-block accordion-block color-accordion-block itemcategory_sec">
                                    <div class="color-accordion" id="color-accordion">
                                        <?php 
                                        // $recordaray = $norecordaray = array();
                                        // foreach($item_categories as $item_categorielist){ 
                                        //  $list = $categorydatalist[$item_categorielist['id']];
                                        //  if(count((array)$list)){
                                        //      $recordaray[] = $item_categorielist;
                                        //  }else{
                                        //      $norecordaray[] = $item_categorielist;
                                        //  }
                                        // }
                                        // $margeitemcat = array_merge((array)$recordaray,(array)$norecordaray);
                                        if(count((array)$item_categories) !=0){     
                                        foreach($item_categories as $item_categorie){ 
                                            $list = $categorydatalist[$item_categorie['id']]; ?>
                                            <a class="accordion-msg b-none waves-effect waves-light f-w-600 bg-gray"><span class="m-r-20"> 
                                                    <?php if($item_categorie['favourite_id'] ==""){ ?>
                                                    <i class="ti ti-star" onclick="updateApplicationFavourites('<?php echo $result['item_table_name']; ?>','<?php echo $item_categorie['id']; ?>','F')" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Add To Favourite"></i>
                                                    <?php }else{ ?>
                                                    <i class="fa fa-star text-danger" onclick="updateApplicationFavourites('<?php echo $result['item_table_name']; ?>','<?php echo $item_categorie['id']; ?>','U')" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Remove From Favourite"></i> 
                                                    <?php } ?>
                                                </span>
                                                <?php echo $item_categorie['category_description']; ?> <label class="form-label label label-md label-warning float-right"><?php echo count((array)$list); ?> </label></a>
                                            <div class="accordion-desc custom-scroll p-0" style="max-height: 400px;">
                                                <?php if(count((array)$list) !=0){ ?>
                                            <div class="row" style="position: sticky;top: 0px;">
                                                <div class="col-lg-8">&nbsp;</div>
                                                <div class="col-lg-4 " >
                                                    
                                                </div>
                                            </div>
                                                    <table class="table table-hover m-b-0" style="font-size:13px;" id="categoryfilter">
                                                        <thead>
                                                            <th>Transaction No.</th>
                                                            <th>Originator</th>
                                                            <th>Date</th>
                                                            <th>Party Name</th>
                                                            <th class="text-right">Amount</th>
                                                            <th>Satus</th>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach($list as $list){ ?>
                                                                <tr>
                                                                    <td><a class="d-block" href="index.php?option=<?php echo $optiondetails['insert_page_name']; ?>&Rid=<?php echo deocdeId('id:'.$list['id']); ?>" <?php echo $edit_permissionbtn; ?>><?php echo $list['transaction_number']; ?></a>
<?php 
if($list['current_status'] == ST_APPROVED AND ($table_name =="supplier_bills" OR $table_name =="sales_invoices")){
    $badgeclass = "";
    if($list['pending_amount'] == $list['total_amount']){ 
        $badgeclass ="label-danger";
        $badgetext = customeummarmonyformateifine($list['pending_amount']);
    }elseif($list['pending_amount'] == 0){ 
        $badgeclass ="label-success";
        $badgetext = "PAID";
    }elseif($list['pending_amount'] != $list['total_amount']){ 
        $badgeclass ="label-warning";
        $badgetext = customeummarmonyformateifine($list['pending_amount']);
    }
     ?>
    <label class="form-label label label-md <?php echo $badgeclass; ?> mt-2"><?php echo $badgetext; ?></label>
<?php } ?>



                                                                    </td>
                                                                    <td class="w-b-w-s"><?php echo $_SESSION['users'][$list['originator']]; ?></td>
                                                                    <td><?php echo fromDate($list['transaction_date']); ?></td>
                                                                    <td class="w-b-w-s"><?php echo $list['party_name']; ?></td>
                                                                    <td class="text-right"><?php echo customeummarmonyformateifine($list['total_amount']); ?></td>
                                                                    <td >
                                                                        <label class="form-label label label-md" style="color:<?php echo $statuse[$list['current_status']]['color']; ?>; background-color:<?php echo $statuse[$list['current_status']]['background_color']; ?>;"><?php echo $status_progress[$list['current_status']]['name']; ?></label>
                                                                    </td>
                                                                </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                <?php }else{ ?>
                                                    <div class="card-px text-center ">
                                                        <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No <?php echo $_POST['menu_label']; ?></p>   
                                                        <div class="text-center px-4">
                                                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                                        </div>   
                                                    </div>
                                                <?php   } ?>
                                            </div>

                                        <?php } ?>
                                        <?php  }else{ ?>
                                            <div class="card-px text-center ">
                                                        <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No <?php echo $itemcatname; ?></p>   
                                                        <div class="text-center px-4">
                                                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                                        </div>   
                                                    </div>
                                            <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane card-body p-0" id="itemlocation" role="tabpanel">
                                <div class="card-header">
                                    <h5>Locations</h5>
                                    <div class="card-header-right table-card-header">
                                        <div class="input-group input-group-button mb-0" >
                                            <input type="text" id="dynamicfilterhideshow" data-id='locationfilter' data-type="tr" class="form-control form-control-sm width1"  placeholder="Search" data-bs-toggle="tooltip" data-placement="bottom" title="Search"> 
                                            <span class="input-group-text addon-style">
                                                <label class=""><span class="ti-search text-white mt-2"></span></label>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-block accordion-block color-accordion-block itemcategory_sec">
                                    <div class="color-accordion" id="color-accordion">
                                        <?php 
                                        // $recordaray = $norecordaray = array();
                                        // foreach($locations as $item_categorielist){ 
                                        //  $list = $locationlist[$item_categorielist['id']];
                                        //  if(count((array)$list)){
                                        //      $recordaray[] = $item_categorielist;
                                        //  }else{
                                        //      $norecordaray[] = $item_categorielist;
                                        //  }
                                        // }
                                        // $margeitemloc = array_merge((array)$recordaray,(array)$norecordaray);
                                        foreach($locations as $item_categorie){ 
                                            $list = $locationlist[$item_categorie['id']]; ?>
                                            <a class="accordion-msg b-none waves-effect waves-light f-w-600 bg-gray"><span class="m-r-20"> 
                                                    <?php if($item_categorie['favourite_id'] ==""){ ?>
                                                    <i class="ti ti-star" onclick="updateApplicationFavourites('locations','<?php echo $item_categorie['id']; ?>','F')" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Add To Favourite"></i>
                                                    <?php }else{ ?>
                                                    <i class="fa fa-star text-danger" onclick="updateApplicationFavourites('locations','<?php echo $item_categorie['id']; ?>','U')" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Remove From Favourite"></i> 
                                                    <?php } ?>
                                                </span>
                                                <?php echo $item_categorie['location_name']; ?> <label class="form-label label label-md label-warning float-right"><?php echo count((array)$list); ?> </label></a>
                                            <div class="accordion-desc custom-scroll p-0" style=" height: 400px;">
                                                <?php if(count((array)$list) !=0){ ?>
                                            <div class="row" style="position: sticky;top: 0px;">
                                                <div class="col-lg-8">&nbsp;</div>
                                                <div class="col-lg-4 ">
                                                
                                                </div>
                                            </div>
                                                    <table class="table table-hover m-b-0" style="font-size:13px;" id="locationfilter">
                                                        <thead>
                                                            <th>Transaction No.</th>
                                                            <th>Originator</th>
                                                            <th>Date</th>
                                                            <th>Party Name</th>
                                                            <th class="text-right">Amount</th>
                                                            <th>Satus</th>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach($list as $list){?>
                                                                <tr>
                                                                    <td><a class="d-block" href="index.php?option=<?php echo $optiondetails['insert_page_name']; ?>&Rid=<?php echo deocdeId('id:'.$list['id']); ?>" <?php echo $edit_permissionbtn; ?>><?php echo $list['transaction_number']; ?></a>
<?php 
if($list['current_status'] == ST_APPROVED AND ($table_name =="supplier_bills" OR $table_name =="sales_invoices")){
    $badgeclass = "";
    if($list['pending_amount'] == $list['total_amount']){ 
        $badgeclass ="label-danger";
        $badgetext = customeummarmonyformateifine($list['pending_amount']);
    }elseif($list['pending_amount'] == 0){ 
        $badgeclass ="label-success";
        $badgetext = "PAID";
    }elseif($list['pending_amount'] != $list['total_amount']){ 
        $badgeclass ="label-warning";
        $badgetext = customeummarmonyformateifine($list['pending_amount']);
    }
     ?>
    <label class="form-label label label-md <?php echo $badgeclass; ?> mt-2"><?php echo $badgetext; ?></label>
<?php } ?>
                                                                    </td>
                                                                    <td class="w-b-w-s"><?php echo $_SESSION['users'][$list['originator']]; ?></td>
                                                                    <td><?php echo fromDate($list['transaction_date']); ?></td>
                                                                    <td class="w-b-w-s"><?php echo $list['party_name']; ?></td>
                                                                    <td class="text-right"><?php echo customeummarmonyformateifine($list['total_amount']); ?></td>
                                                                    <td >
                                                                        <label class="form-label label label-md" style="color:<?php echo $statuse[$list['current_status']]['color']; ?>; background-color:<?php echo $statuse[$list['current_status']]['background_color']; ?>;"><?php echo $status_progress[$list['current_status']]['name']; ?></label>
                                                                    </td>
                                                                </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                <?php }else{ ?>
                                                    <div class="card-px text-center ">
                                                        <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No <?php echo $_POST['menu_label']; ?></p>   
                                                        <div class="text-center px-4">
                                                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                                        </div>   
                                                    </div>
                                                <?php   } ?>
                                            </div>

                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane card-body p-0" id="costcenter" role="tabpanel">
                                <div class="card-header">
                                    <h5>Cost Centers</h5>
                                    <div class="card-header-right table-card-header">
                                        <div class="input-group input-group-button mb-0" >
                                            <input type="text" id="dynamicfilterhideshow" data-id='costcenterfilter' data-type="tr" class="form-control form-control-sm width1"  placeholder="Search" data-bs-toggle="tooltip" data-placement="bottom" title="Search"> 
                                            <span class="input-group-text addon-style">
                                                <label class=""><span class="ti-search text-white mt-2"></span></label>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-block accordion-block color-accordion-block itemcategory_sec">
                                    <div class="color-accordion" id="color-accordion">
                                        
                                        <?php 
                                        // $recordaray = $norecordaray = array();
                                        // foreach($cost_center_datalist as $item_categorielist){ 
                                        //  $list = $costcenterlist[$item_categorielist['id']];
                                        //  if(count((array)$list)){
                                        //      $recordaray[] = $item_categorielist;
                                        //  }else{
                                        //      $norecordaray[] = $item_categorielist;
                                        //  }
                                        // }
                                        // $margeitemcostcenter = array_merge((array)$recordaray,(array)$norecordaray);
                                        foreach($cost_center_datalist as $item_categorie){ 
                                            $list = $costcenterlist[$item_categorie['id']]; ?>
                                            <a class="accordion-msg b-none waves-effect waves-light f-w-600 bg-gray" ><span class="m-r-20"> 
                                                    <?php if($item_categorie['favourite_id'] ==""){ ?>
                                                    <i class="ti ti-star" onclick="updateApplicationFavourites('cost_centers','<?php echo $item_categorie['id']; ?>','F')" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Add To Favourite"></i>
                                                    <?php }else{ ?>
                                                    <i class="fa fa-star text-danger" onclick="updateApplicationFavourites('cost_centers','<?php echo $item_categorie['id']; ?>','U')" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Remove From Favourite"></i> 
                                                    <?php } ?>
                                                </span>
                                                <?php echo $item_categorie['cost_center_name']; ?> <label class="form-label label label-md label-warning float-right"><?php echo count((array)$list); ?> </label></a>
                                            <div class="accordion-desc custom-scroll p-0" style=" height: 400px;">
                                                <?php if(count((array)$list) !=0){ ?>
                                            <div class="row" style="position: sticky;top: 0px;">
                                                <div class="col-lg-8">&nbsp;</div>
                                                <div class="col-lg-4 ">
                                                </div>
                                            </div>
                                                    <table class="table table-hover m-b-0" style="font-size:13px;" id="costcenterfilter">
                                                        <thead>
                                                            <th>Transaction No.</th>
                                                            <th>Originator</th>
                                                            <th>Date</th>
                                                            <th>Party Name</th>
                                                            <th class="text-right">Amount</th>
                                                            <th>Satus</th>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach($list as $list){?>
                                                                <tr>
                                                                    <td><a class="d-block" href="index.php?option=<?php echo $optiondetails['insert_page_name']; ?>&Rid=<?php echo deocdeId('id:'.$list['id']); ?>" <?php echo $edit_permissionbtn; ?>><?php echo $list['transaction_number']; ?></a>
                                                                    <?php 
if($list['current_status'] == ST_APPROVED AND ($table_name =="supplier_bills" OR $table_name =="sales_invoices")){
    $badgeclass = "";
    if($list['pending_amount'] == $list['total_amount']){ 
        $badgeclass ="label-danger";
        $badgetext = customeummarmonyformateifine($list['pending_amount']);
    }elseif($list['pending_amount'] == 0){ 
        $badgeclass ="label-success";
        $badgetext = "PAID";
    }elseif($list['pending_amount'] != $list['total_amount']){ 
        $badgeclass ="label-warning";
        $badgetext = customeummarmonyformateifine($list['pending_amount']);
    }
     ?>
    <label class="form-label label label-md <?php echo $badgeclass; ?> mt-2"><?php echo $badgetext; ?></label>
<?php } ?></td>
                                                                    <td class="w-b-w-s"><?php echo $_SESSION['users'][$list['originator']]; ?></td>
                                                                    <td><?php echo fromDate($list['transaction_date']); ?></td>
                                                                    <td class="w-b-w-s"><?php echo $list['party_name']; ?></td>
                                                                    <td class="text-right"><?php echo customeummarmonyformateifine($list['total_amount']); ?></td>
                                                                    <td>
                                                                        <label class="form-label label label-md" style="color:<?php echo $statuse[$list['current_status']]['color']; ?>; background-color:<?php echo $statuse[$list['current_status']]['background_color']; ?>;"><?php echo $status_progress[$list['current_status']]['name']; ?></label>
                                                                    </td>
                                                                </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                <?php }else{ ?>
                                                    <div class="card-px text-center ">
                                                        <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No <?php echo $_POST['menu_label']; ?></p>   
                                                        <div class="text-center px-4">
                                                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                                        </div>   
                                                    </div>
                                                <?php   } ?>
                                            </div>

                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            

                    <?php /** foreach($generatedocs as $trnstabkey=>$gdocs){ 
                            $viewpageotion = getPagenametotable($table_name,'insert_page_name'); ?> 
                                    <div class="tab-pane card-body p-0" id="tabs_<?php echo $trnstabkey; ?>" role="tabpanel">
                                <div class="card-header">
                                    <h5><?php echo $trnstabkey; ?></h5>
                                    <div class="card-header-right table-card-header">
                                        <div class="input-group input-group-button mb-0" >
                                            <input type="text" id="dynamicfilterhideshow" data-id='costcenterfilter' data-type="tr" class="form-control form-control-sm width1"  placeholder="Search" data-bs-toggle="tooltip" data-placement="bottom" title="Search"> 
                                            <span class="input-group-text addon-style">
                                                <label class=""><span class="ti-search text-white mt-2"></span></label>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <?php if(count((array)$gdocs) !=0){ ?>
                <table class="table table-hover m-b-0" style="font-size:13px;" id="costcenterfilter">
                    <thead>
                        <th>Transaction No.</th>
                        <th>Originator</th>
                        <th>Date</th>
                        <th>Party Name</th>
                        <th class="text-right">Amount</th>
                        <th>Generate</th>
                    </thead>
                    <tbody>
                        <?php foreach($gdocs as $list){?>
                            <tr>
                                <td><a href="index.php?option=<?php echo $viewpageotion; ?>&Rid=<?php echo deocdeId('id:'.$list['id']); ?>" <?php echo $edit_permissionbtn; ?>><?php echo $list['transaction_number']; ?></a></td>
                                <td class="w-b-w-s"><?php echo $_SESSION['users'][$list['originator']]; ?></td>
                                <td><?php echo fromDate($list['transaction_date']); ?></td>
                                <td class="w-b-w-s"><?php echo $list['party_name']; ?></td>
                                <td class="text-right"><?php echo customeummarmonyformateifine($list['total_amount']); ?></td>
                                <td>
                                <?php if($trnstabkey == "requisitions"){ 
                                        if(!isset($purchase_orders[$row['id']])){ ?>
                                    <button class="btn btn-primary btn-brand--icon" id="kt_search" onclick="changestatus('<?php echo $table_name; ?>','<?php echo $list['id']; ?>','<?php echo $list['itemtb_id']; ?>','<?php echo $trnstabkey; ?>')"><span><?php echo $_POST['menu_label']; ?></span></button>
                                <?php   }   } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php }else{ ?>
                <div class="card-px text-center ">
                    <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No <?php echo $trnstabkey; ?></p>   
                    <div class="text-center px-4">
                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                    </div>   
                </div>
            <?php   } ?>
        
        </div>
<?php } **/  ?> 







                        </div>
                    </div>


</div>
<div class="tab-pane card-body p-0" id="tabsopen" role="tabpanel">

</div>
</div>        

<div class="card mt-3">
    <div class="card-header">
        <h5> Recently Updated</h5>
    </div>
    <div class="table-responsive p-2">
        <?php if(count((array)$result['latest_table_list']) != 0){ ?>
        <table class="table table-hover m-b-0" style="font-size:13px;" id="costcenterfilter">
                                                        <thead>
                                                            <th>Transaction No.</th>
                                                            <th>Originator</th>
                                                            <th>Date</th>
                                                            <th>Party Name</th>
                                                            <th class="text-right">Amount</th>
                                                            <th>Satus</th>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach($result['latest_table_list'] as $list){?>
                                                                <tr>
                                                                    <td><a class="d-block" href="index.php?option=<?php echo $optiondetails['insert_page_name']; ?>&Rid=<?php echo deocdeId('id:'.$list['id']); ?>" <?php echo $edit_permissionbtn; ?>><?php echo $list['transaction_number']; ?></a>
                                                                                                                <?php 
if($list['current_status'] == ST_APPROVED AND ($table_name =="supplier_bills" OR $table_name =="sales_invoices")){
    $badgeclass = "";
    if($list['pending_amount'] == $list['total_amount']){ 
        $badgeclass ="label-danger";
        $badgetext = customeummarmonyformateifine($list['pending_amount']);
    }elseif($list['pending_amount'] == 0){ 
        $badgeclass ="label-success";
        $badgetext = "PAID";
    }elseif($list['pending_amount'] != $list['total_amount']){ 
        $badgeclass ="label-warning";
        $badgetext = customeummarmonyformateifine($list['pending_amount']);
    }
     ?>
    <label class="form-label label label-md <?php echo $badgeclass; ?> mt-2"><?php echo $badgetext; ?></label>
<?php } ?></td>
                                                                    <td class="w-b-w-s"><?php echo $_SESSION['users'][$list['originator']]; ?></td>
                                                                    <td><?php echo fromDate($list['transaction_date']); ?></td>
                                                                    <td class="w-b-w-s"><?php echo $list['party_name']; ?></td>
                                                                    <td class="text-right"><?php echo customeummarmonyformateifine($list['total_amount']); ?></td>
                                                                    <td>
                                                                        <label class="form-label label label-md" style="color:<?php echo $statuse[$list['current_status']]['color']; ?>; background-color:<?php echo $statuse[$list['current_status']]['background_color']; ?>;"><?php echo $status_progress[$list['current_status']]['name']; ?></label>
                                                                    </td>
                                                                </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                    <?php  }else{ ?>
                        <div class="card-px text-center ">
                                    <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Update</p>   
                                    <div class="text-center px-4">
                                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                    </div>   
                                </div>
                        <?php } ?>
    </div>
</div>  
</div>

</div>

                
        </div>
        <div class="col-lg-3">
            <ul class="nav md-tabs infisuite_tab mt-2 mb-2" role="tablist">
                <li class="nav-item">
                    <a class="nav-link f-w-600 f-12 active" data-bs-toggle="tab" href="#selfcreated" role="tab" aria-selected="true">Self<label class="form-label label label-xs label-success m-l-10"><?php echo $transactionlogcnt; ?></label></a>
                </li> 

                <li class="nav-item">
                    <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#selfaction" role="tab" aria-selected="true">Action<label class="form-label label label-xs label-danger m-l-10"><?php echo $createtransactioncnt; ?></label>
                    <label class="form-label label label-xs label-success m-l-10"><?php echo $createtransactionapprovedcnt !=""?$createtransactionapprovedcnt:"0" ; ?></label>
                    <label class="form-label label label-xs label-warning m-l-10"><?php echo count((array)$sendbacklog) !=""?count((array)$sendbacklog):"0" ; ?></label></a>
                </li>
            </ul>   
            <div class="tab-content tabs ">
                <div class="tab-pane card-body active p-0" id="selfcreated" role="tabpanel">
                    <div class="card mb-2">
                        <div class="card-header">
                            <h5>Draft 
                                <label class="form-label label label-xs label-success m-l-10" style="color:<?php echo $statuse[ST_DRAFT]['color']; ?>; background-color:<?php echo $statuse[ST_DRAFT]['background_color']; ?>;"><?php echo count((array)$draftres); ?></label>
                            </h5>
                            <a href="#!"  onclick="viewAllRecord('<?php echo $optiondetails['application_table_name']; ?>',<?php echo ST_DRAFT; ?>)" class=" b-b-primary text-primary float-right">View all</a>
                        </div>
                        <div class="card-body">
                            <?php //echo "<pre>"; print_r($draftres); echo "</pre>";
                             $mm = 1; if(count((array)$draftres) !=0){ foreach($draftres as $activeres1){ if($mm == 4){ break; } ?>
                            <div class="row mt-3 pb-3" style="border-bottom:1px dashed #cccccc;">
                                <div class="col-auto">
                                    <!-- <div class="p-1 b-radius-25 text-center left-icon-width" style="color:<?php echo $statuse[$activeres1['current_status']]['color']; ?>; background-color:<?php echo $statuse[$activeres1['current_status']]['background_color']; ?>;"> -->
                                        <!-- <i class=" ti-briefcase f-w-600"></i> -->
                
                <div class="align-middle">
                  <?php if($_SESSION['user_image'][$activeres1['originator']]!=""){?>
                    <img src="uploads/all/<?php echo $_SESSION['user_image'][$activeres1['originator']]; ?>"   class="img-radius img-40 align-top" onerror="this.src='uploads/all/no-image.jpg';">
                  <?php }else{ ?>
                    <span  alt="user image" class="img-radius align-top custom_txt_workentry f-12 text-center" ><?php echo initials($_SESSION['users'][$activeres1['originator']]); ?></span>
                  <?php } ?>
                </div>  
                                <!--    </div> -->
                                </div>
                                <div class="col p-0 m-l-10">
                                    <a href="index.php?option=<?php echo $optiondetails['insert_page_name']; ?>&Rid=<?php echo deocdeId('id:'.$activeres1['id']); ?>" <?php echo $edit_permissionbtn; ?>>
                                        <h6 class="f-w-600 mb-1 f-14"><?php echo $_SESSION['users'][$activeres1['originator']]; ?>                                          <?php 
if($activeres1['current_status'] == ST_APPROVED AND ($table_name =="supplier_bills" OR $table_name =="sales_invoices")){
    $badgeclass = "";
    if($activeres1['pending_amount'] == $activeres1['total_amount']){ 
        $badgeclass ="label-danger";
        $badgetext = customeummarmonyformateifine($activeres1['pending_amount']);
    }elseif($activeres1['pending_amount'] == 0){ 
        $badgeclass ="label-success";
        $badgetext = "PAID";
    }elseif($activeres1['pending_amount'] != $activeres1['total_amount']){ 
        $badgeclass ="label-warning";
        $badgetext = customeummarmonyformateifine($activeres1['pending_amount']);
    }
     ?>
    <label class="form-label label label-md <?php echo $badgeclass; ?> mt-2"><?php echo $badgetext; ?></label>
<?php } ?></h6>
                                        <span class="text-muted f-12"><?php echo $activeres1['transaction_number']; ?> - (<?php echo customeummarmonyformateifine($activeres1['total_amount']); ?>) - <?php echo fromDate($activeres1['transaction_date']); ?></span>
                                    </a>
                                </div>
                            </div>
                        <?php $mm++; } }else{ ?>
                        <div class="card-px text-center ">
                                    <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Draft Records</p>   
                                    <div class="text-center px-4">
                                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                    </div>   
                                </div>
                        <?php } ?>  
                        </div>
                    </div>
                    <div class="card mb-2">
                        <div class="card-header">
                            <h5>Active 
                                <label class="form-label label label-xs label-success m-l-10" style="color:<?php echo $statuse[ST_ACTIVE]['color']; ?>; background-color:<?php echo $statuse[ST_ACTIVE]['background_color']; ?>;"><?php echo count((array)$activeres); ?></label>
                            </h5>
                            <a href="#!"  onclick="viewAllRecord('<?php echo $optiondetails['application_table_name']; ?>','<?php echo ST_ACTIVE; ?>')" class=" b-b-primary text-primary float-right">View all</a>
                        </div>
                        <div class="card-body">
                            <?php $mm = 1; if(count((array)$activeres) !=0){ foreach($activeres as $activeres1){ if($mm == 4){ break; } ?>
                            <div class="row mt-3 pb-3" style="border-bottom:1px dashed #cccccc;">
                                <div class="col-auto">
                                    <!-- <div class="p-2 b-radius-5 text-center left-icon-width" style="color:<?php echo $statuse[$activeres1['current_status']]['color']; ?>; background-color:<?php echo $statuse[$activeres1['current_status']]['background_color']; ?>;">
                                        <i class=" ti-briefcase f-w-600"></i>
                                    </div> -->
                                    <div class="align-middle">
                  <?php if($_SESSION['user_image'][$activeres1['originator']]!=""){?>
                    <img src="uploads/all/<?php echo $_SESSION['user_image'][$activeres1['originator']]; ?>"   class="img-radius img-40 align-top" onerror="this.src='uploads/all/no-image.jpg';">
                  <?php }else{ ?>
                    <span  alt="user image" class="img-radius align-top custom_txt_workentry f-12 text-center" ><?php echo initials($_SESSION['users'][$activeres1['originator']]); ?></span>
                  <?php } ?>
                </div>  
                                </div>
                                <div class="col p-0">
                                    <a href="index.php?option=<?php echo $optiondetails['insert_page_name']; ?>&Rid=<?php echo deocdeId('id:'.$activeres1['id']); ?>" <?php echo $edit_permissionbtn; ?>>
                                        <h6 class="f-w-600 mb-1 f-14"><?php echo $activeres1['party_name']; ?>                                          <?php 
if($activeres1['current_status'] == ST_APPROVED AND ($table_name =="supplier_bills" OR $table_name =="sales_invoices")){
    $badgeclass = "";
    if($activeres1['pending_amount'] == $activeres1['total_amount']){ 
        $badgeclass ="label-danger";
        $badgetext = customeummarmonyformateifine($activeres1['pending_amount']);
    }elseif($activeres1['pending_amount'] == 0){ 
        $badgeclass ="label-success";
        $badgetext = "PAID";
    }elseif($activeres1['pending_amount'] != $activeres1['total_amount']){ 
        $badgeclass ="label-warning";
        $badgetext = customeummarmonyformateifine($activeres1['pending_amount']);
    }
     ?>
    <label class="form-label label label-md <?php echo $badgeclass; ?> mt-2"><?php echo $badgetext; ?></label>
<?php } ?></h6>
                                        <span class="text-muted f-12"><?php echo $activeres1['transaction_number']; ?> - (<?php echo customeummarmonyformateifine($activeres1['total_amount']); ?>) - <?php echo fromDate($activeres1['transaction_date']); ?></span>
                                    </a>
                                </div>
                            </div>
                        <?php $mm++; } }else{ ?>
                        <div class="card-px text-center ">
                                    <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Active Records</p>   
                                    <div class="text-center px-4">
                                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                    </div>   
                                </div>
                        <?php } ?>  
                        </div>
                    </div>
                    <div class="card mb-2">
                        <div class="card-header">
                            <h5>In Process 
                                <label class="form-label label label-xs label-success m-l-10" style="color:<?php echo $statuse[ST_APPROVED1]['color']; ?>; background-color:<?php echo $statuse[ST_APPROVED1]['background_color']; ?>;"><?php echo count((array)$inprocessres); ?></label>
                            </h5>
                            <a href="#!"  onclick="viewAllRecord('<?php echo $optiondetails['application_table_name']; ?>','inprocess')" class=" b-b-primary text-primary float-right">View all</a>
                        </div>
                        <div class="card-body">
                            <?php $mm = 1; if(count((array)$inprocessres) !=0){ foreach($inprocessres as $activeres1){ if($mm == 4){ break; } ?>
                            <div class="row mt-3 pb-3" style="border-bottom:1px dashed #cccccc;">
                                <div class="col-auto">
                                    <!-- <div class="p-2 b-radius-5 text-center left-icon-width"style="color:<?php echo $statuse[$activeres1['current_status']]['color']; ?>; background-color:<?php echo $statuse[$activeres1['current_status']]['background_color']; ?>;">
                                        <i class=" ti-briefcase f-w-600"></i>
                                    </div> -->
                                    <div class="align-middle">
                  <?php if($_SESSION['user_image'][$activeres1['originator']]!=""){?>
                    <img src="uploads/all/<?php echo $_SESSION['user_image'][$activeres1['originator']]; ?>"   class="img-radius img-40 align-top" onerror="this.src='uploads/all/no-image.jpg';">
                  <?php }else{ ?>
                    <span  alt="user image" class="img-radius align-top custom_txt_workentry f-12 text-center" ><?php echo initials($_SESSION['users'][$activeres1['originator']]); ?></span>
                  <?php } ?>
                </div>  
                                </div>
                                <div class="col p-0">
                                    <a href="index.php?option=<?php echo $optiondetails['insert_page_name']; ?>&Rid=<?php echo deocdeId('id:'.$activeres1['id']); ?>" <?php echo $edit_permissionbtn; ?>>
                                        <h6 class="f-w-600 mb-1 f-14"><?php echo $_SESSION['users'][$activeres1['originator']]; ?>                                          <?php 
if($activeres1['current_status'] == ST_APPROVED AND ($table_name =="supplier_bills" OR $table_name =="sales_invoices")){
    $badgeclass = "";
    if($activeres1['pending_amount'] == $activeres1['total_amount']){ 
        $badgeclass ="label-danger";
        $badgetext = customeummarmonyformateifine($activeres1['pending_amount']);
    }elseif($activeres1['pending_amount'] == 0){ 
        $badgeclass ="label-success";
        $badgetext = "PAID";
    }elseif($activeres1['pending_amount'] != $activeres1['total_amount']){ 
        $badgeclass ="label-warning";
        $badgetext = customeummarmonyformateifine($activeres1['pending_amount']);
    }
     ?>
    <label class="form-label label label-md <?php echo $badgeclass; ?> mt-2"><?php echo $badgetext; ?></label>
<?php } ?></h6>
                                        <span class="text-muted f-12"><?php echo $activeres1['transaction_number']; ?> - (<?php echo customeummarmonyformateifine($activeres1['total_amount']); ?>) - <?php echo fromDate($activeres1['transaction_date']); ?></span>

                                        <label class="form-label label label-md" style="color:<?php echo $statuse[$activeres1['current_status']]['color']; ?>; background-color:<?php echo $statuse[$activeres1['current_status']]['background_color']; ?>;"><?php echo $status_progress[$activeres1['current_status']]['name']; ?></label>
                                    </a>
                                </div>
                            </div>
                        <?php $mm++; } }else{ ?>
                        <div class="card-px text-center ">
                                    <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No In-Process Records</p>   
                                    <div class="text-center px-4">
                                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                    </div>   
                                </div>
                        <?php } ?>
                        </div>
                    </div>
                    <div class="card mb-2">
                        <div class="card-header">
                            <h5>Approved 
                                <label class="form-label label label-xs label-success m-l-10" style="color:<?php echo $statuse[ST_APPROVED]['color']; ?>; background-color:<?php echo $statuse[ST_APPROVED]['background_color']; ?>;"><?php echo count((array)$approvedres); ?></label>
                            </h5>
                            <a href="#!"  onclick="viewAllRecord('<?php echo $optiondetails['application_table_name']; ?>','<?php echo ST_APPROVED; ?>')" class=" b-b-primary text-primary float-right">View all</a>
                        </div>
                        <div class="card-body">
                            <?php $mm = 1; if(count((array)$approvedres) !=0){ foreach($approvedres as $activeres1){ if($mm == 4){ break; } ?>
                            <div class="row mt-3 pb-3" style="border-bottom:1px dashed #cccccc;">
                                <div class="col-auto">
                                    <!-- <div class="p-2 b-radius-5 text-center left-icon-width" style="color:<?php echo $statuse[$activeres1['current_status']]['color']; ?>; background-color:<?php echo $statuse[$activeres1['current_status']]['background_color']; ?>;">
                                        <i class=" ti-briefcase f-w-600"></i>
                                    </div> -->
                                    <div class="align-middle">
                  <?php if($_SESSION['user_image'][$activeres1['originator']]!=""){?>
                    <img src="uploads/all/<?php echo $_SESSION['user_image'][$activeres1['originator']]; ?>"   class="img-radius img-40 align-top" onerror="this.src='uploads/all/no-image.jpg';">
                  <?php }else{ ?>
                    <span  alt="user image" class="img-radius align-top custom_txt_workentry f-12 text-center" ><?php echo initials($_SESSION['users'][$activeres1['originator']]); ?></span>
                  <?php } ?>
                </div>  
                                </div>
                                <div class="col p-0">
                                    <a href="index.php?option=<?php echo $optiondetails['insert_page_name']; ?>&Rid=<?php echo deocdeId('id:'.$activeres1['id']); ?>" <?php echo $edit_permissionbtn; ?>>
                                        <h6 class="f-w-600 mb-1 f-14"><?php echo $_SESSION['users'][$activeres1['originator']]; ?>
                                            <?php 
if($activeres1['current_status'] == ST_APPROVED AND ($table_name =="supplier_bills" OR $table_name =="sales_invoices")){
    $badgeclass = "";
    if($activeres1['pending_amount'] == $activeres1['total_amount']){ 
        $badgeclass ="label-danger";
        $badgetext = customeummarmonyformateifine($activeres1['pending_amount']);
    }elseif($activeres1['pending_amount'] == 0){ 
        $badgeclass ="label-success";
        $badgetext = "PAID";
    }elseif($activeres1['pending_amount'] != $activeres1['total_amount']){ 
        $badgeclass ="label-warning";
        $badgetext = customeummarmonyformateifine($activeres1['pending_amount']);
    }
     ?>
    <label class="form-label label label-md <?php echo $badgeclass; ?> mt-2"><?php echo $badgetext; ?></label>
<?php } ?>
                                        </h6>
                                        <span class="text-muted f-12"><?php echo $activeres1['transaction_number']; ?> - (<?php echo customeummarmonyformateifine($activeres1['total_amount']); ?>) - <?php echo fromDate($activeres1['transaction_date']); ?></span>
                                    </a>
                                </div>
                            </div>
                        <?php $mm++; } }else{ ?>
                        <div class="card-px text-center ">
                                    <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Approved Records</p>   
                                    <div class="text-center px-4">
                                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                    </div>   
                                </div>
                        <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="tab-pane card-body p-0" id="selfaction" role="tabpanel">
                    <div class="card mb-2">
                        <div class="card-header">
                            <h5>Approval Pending 
                                <label class="form-label label label-xs label-danger m-l-10" ><?php echo count((array)$pendinglog); ?></label>
                            </h5>
                            <a href="#!"  onclick="viewAllRecord('<?php echo $optiondetails['application_table_name']; ?>','APPROVEPENDING')" class=" b-b-primary text-primary float-right">View all</a>
                        </div>
                        <div class="card-body">
                            <?php $mm = 1; if(count((array)$pendinglog) !=0){ foreach($pendinglog as $logdetails){ 
                                //$activeres1 = $alltransaction[$logdetails['transaction_id']];
                                if($mm == 4){ break; } ?>
                            <div class="row mt-3 pb-3" style="border-bottom:1px dashed #cccccc;">
                                <div class="col-auto">
                                    <!-- <div class="p-2 b-radius-5 text-center left-icon-width" style="color:<?php echo $statuse[$logdetails['current_status']]['color']; ?>; background-color:<?php echo $statuse[$logdetails['current_status']]['background_color']; ?>;">
                                        <i class=" ti-briefcase f-w-600"></i>
                                    </div> -->
                                    <div class="align-middle">
                  <?php if($_SESSION['user_image'][$logdetails['originator']]!=""){?>
                    <img src="uploads/all/<?php echo $_SESSION['user_image'][$logdetails['originator']]; ?>"   class="img-radius img-40 align-top" onerror="this.src='uploads/all/no-image.jpg';">
                  <?php }else{ ?>
                    <span  alt="user image" class="img-radius align-top custom_txt_workentry f-12 text-center" ><?php echo initials($_SESSION['users'][$logdetails['originator']]); ?></span>
                  <?php } ?>
                </div>  
                                </div>
                                <div class="col p-0">
                                    <a href="index.php?option=<?php echo $optiondetails['insert_page_name']; ?>&Rid=<?php echo deocdeId('id:'.$logdetails['transaction_id']); ?>" <?php echo $approval_permissionbtn; ?>>
                                        <h6 class="f-w-600 mb-1 f-14"><?php echo $_SESSION['users'][$logdetails['originator']]; ?></h6>
                                        <span class="text-muted f-12"><?php echo $logdetails['transaction_number']; ?> - (<?php echo customeummarmonyformateifine($logdetails['total_amount']); ?>) - <?php echo fromDate($logdetails['transaction_date']); ?></span>
                                    </a>
                                </div>
                            </div>
                        <?php $mm++; } }else{ ?>
                        <div class="card-px text-center ">
                                    <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Approval Pending</p>   
                                    <div class="text-center px-4">
                                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                    </div>   
                                </div>
                        <?php } ?>  
                        </div>
                    </div>
                    
                    <div class="card mb-2">
                        <div class="card-header">
                            <h5>Approved 
                                <label class="form-label label label-xs label-success m-l-10" style="color:<?php echo $statuse[ST_APPROVED]['color']; ?>; background-color:<?php echo $statuse[ST_APPROVED]['background_color']; ?>;"><?php echo count((array)$aprovedlog); ?></label>
                            </h5>
                            <a href="#!"  onclick="viewAllRecord('<?php echo $optiondetails['application_table_name']; ?>','APPROVELOG')" class=" b-b-primary text-primary float-right">View all</a>
                        </div>
                        <div class="card-body">
                            <?php $mm = 1; if(count((array)$aprovedlog) !=0){ foreach($aprovedlog as $logdetails){ 
                                //$activeres1 = $alltransaction[$logdetails['transaction_id']];
                                if($mm == 4){ break; } ?>
                            <div class="row mt-3 pb-3" style="border-bottom:1px dashed #cccccc;">
                                <div class="col-auto">
                                    <!-- <div class="p-2 b-radius-5 text-center left-icon-width" style="color:<?php echo $statuse[$logdetails['current_status']]['color']; ?>; background-color:<?php echo $statuse[$logdetails['current_status']]['background_color']; ?>;">
                                        <i class=" ti-briefcase f-w-600"></i>
                                    </div> -->
                                    <div class="align-middle">
                  <?php if($_SESSION['user_image'][$logdetails['originator']]!=""){?>
                    <img src="uploads/all/<?php echo $_SESSION['user_image'][$logdetails['originator']]; ?>"   class="img-radius img-40 align-top" onerror="this.src='uploads/all/no-image.jpg';">
                  <?php }else{ ?>
                    <span  alt="user image" class="img-radius align-top custom_txt_workentry f-12 text-center" ><?php echo initials($_SESSION['users'][$logdetails['originator']]); ?></span>
                  <?php } ?>
                </div>  
                                </div>
                                <div class="col p-0">
                                    <a href="index.php?option=<?php echo $optiondetails['insert_page_name']; ?>&Rid=<?php echo deocdeId('id:'.$logdetails['transaction_id']); ?>" <?php echo $approval_permissionbtn; ?>>
                                        <h6 class="f-w-600 mb-1 f-14"><?php echo $_SESSION['users'][$logdetails['originator']]; ?></h6>
                                        <span class="text-muted f-12"><?php echo $logdetails['transaction_number']; ?> - (<?php echo customeummarmonyformateifine($logdetails['total_amount']); ?>) - <?php echo fromDate($logdetails['transaction_date']); ?></span>
                                    </a>
                                </div>
                            </div>
                        <?php $mm++; } }else{ ?>
                        <div class="card-px text-center ">
                                    <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Approved</p>   
                                    <div class="text-center px-4">
                                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                    </div>   
                                </div>
                        <?php } ?>  
                        </div>
                    </div>
                    <div class="card mb-2">
                        <div class="card-header">
                            <h5>Send Back 
                                <label class="form-label label label-xs label-warning m-l-10" ><?php echo count((array)$sendbacklog); ?></label>
                            </h5>
                            <a href="#!"  onclick="viewAllRecord('<?php echo $optiondetails['application_table_name']; ?>','SENDBACK')" class=" b-b-primary text-primary float-right">View all</a>
                        </div>
                        <div class="card-body">
                            <?php $mm = 1; if(count((array)$sendbacklog) !=0){ foreach($sendbacklog as $logdetails){ 
                                //$activeres1 = $alltransaction[$logdetails['transaction_id']];
                                if($mm == 4){ break; } ?>
                            <div class="row mt-3 pb-3" style="border-bottom:1px dashed #cccccc;">
                                <div class="col-auto">
                                    <!-- <div class="p-2 b-radius-5 text-center left-icon-width" style="color:<?php echo $statuse[$logdetails['current_status']]['color']; ?>; background-color:<?php echo $statuse[$logdetails['current_status']]['background_color']; ?>;">
                                        <i class=" ti-briefcase f-w-600"></i>
                                    </div> -->
                                    <div class="align-middle">
                  <?php if($_SESSION['user_image'][$logdetails['originator']]!=""){?>
                    <img src="uploads/all/<?php echo $_SESSION['user_image'][$logdetails['originator']]; ?>"   class="img-radius img-40 align-top" onerror="this.src='uploads/all/no-image.jpg';">
                  <?php }else{ ?>
                    <span  alt="user image" class="img-radius align-top custom_txt_workentry f-12 text-center" ><?php echo initials($_SESSION['users'][$logdetails['originator']]); ?></span>
                  <?php } ?>
                </div>  
                                </div>
                                <div class="col p-0">
                                    <a href="index.php?option=<?php echo $optiondetails['insert_page_name']; ?>&Rid=<?php echo deocdeId('id:'.$logdetails['transaction_id']); ?>" <?php echo $approval_permissionbtn; ?>>
                                        <h6 class="f-w-600 mb-1 f-14"><?php echo $_SESSION['users'][$logdetails['originator']]; ?></h6>
                                        <span class="text-muted f-12"><?php echo $logdetails['transaction_number']; ?> - (<?php echo customeummarmonyformateifine($logdetails['total_amount']); ?>) - <?php echo fromDate($logdetails['transaction_date']); ?></span>
                                    </a>
                                </div>
                            </div>
                        <?php $mm++; } }else{ ?>
                        <div class="card-px text-center ">
                                    <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Send back</p>   
                                    <div class="text-center px-4">
                                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                    </div>   
                                </div>
                        <?php } ?>  
                        </div>
                    </div>
                    <div class="card mb-2">
                        <div class="card-header">
                            <h5>Rejected
                                <label class="form-label label label-xs label-warning m-l-10" ><?php echo count((array)$rejectlog); ?></label>
                            </h5>
                            <a href="#!"  onclick="viewAllRecord('<?php echo $optiondetails['application_table_name']; ?>','REJECTED')" class=" b-b-primary text-primary float-right">View all</a>
                        </div>
                        <div class="card-body">
                            <?php $mm = 1; if(count((array)$rejectlog) !=0){ foreach($rejectlog as $logdetails){ 
                                //$activeres1 = $alltransaction[$logdetails['transaction_id']];
                                if($mm == 4){ break; } ?>
                            <div class="row mt-3 pb-3" style="border-bottom:1px dashed #cccccc;">
                                <div class="col-auto">
                                    <!-- <div class="p-2 b-radius-5 text-center left-icon-width" style="color:<?php echo $statuse[$logdetails['current_status']]['color']; ?>; background-color:<?php echo $statuse[$logdetails['current_status']]['background_color']; ?>;">
                                        <i class=" ti-briefcase f-w-600"></i>
                                    </div> -->
                                    <div class="align-middle">
                  <?php if($_SESSION['user_image'][$logdetails['originator']]!=""){?>
                    <img src="uploads/all/<?php echo $_SESSION['user_image'][$logdetails['originator']]; ?>"   class="img-radius img-40 align-top" onerror="this.src='uploads/all/no-image.jpg';">
                  <?php }else{ ?>
                    <span  alt="user image" class="img-radius align-top custom_txt_workentry f-12 text-center" ><?php echo initials($_SESSION['users'][$logdetails['originator']]); ?></span>
                  <?php } ?>
                </div>  
                                </div>
                                <div class="col p-0">
                                    <a href="index.php?option=<?php echo $optiondetails['insert_page_name']; ?>&Rid=<?php echo deocdeId('id:'.$logdetails['transaction_id']); ?>" <?php echo $approval_permissionbtn; ?>>
                                        <h6 class="f-w-600 mb-1 f-14"><?php echo $_SESSION['users'][$logdetails['originator']]; ?></h6>
                                        <span class="text-muted f-12"><?php echo $logdetails['transaction_number']; ?> - (<?php echo customeummarmonyformateifine($logdetails['total_amount']); ?>) - <?php echo fromDate($logdetails['transaction_date']); ?></span>
                                    </a>
                                </div>
                            </div>
                        <?php $mm++; } }else{ ?>
                        <div class="card-px text-center ">
                                    <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Rejected</p>   
                                    <div class="text-center px-4">
                                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                    </div>   
                                </div>
                        <?php } ?>  
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php /** if($_POST['ajaxcall'] == 'Y'){ ?>
<script type="text/javascript" src="assets/pages/accordion/accordion.js"></script>
<?php }  **/?>
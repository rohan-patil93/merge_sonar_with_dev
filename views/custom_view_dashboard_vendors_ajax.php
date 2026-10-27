<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$optiondetails = getOptionPageTableName($_POST['option']);
$url = API."?mode=getdynamicmoduledashboard&transaction_table=".$optiondetails['application_table_name']."&admin_type=".$_SESSION['admin_type']."&admin_access_employee=".$_SESSION['admin_access_employee']."&vendor_id=".$_SESSION['vendor_id']."&fieldaccess=party_type";
$apires = callAPI($url);
$result = json_decode($apires,1);
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
    $locationlist[$locationdata['location_id']][] = $locationdata;
}

$cost_center_datalist = $result['cost_center_name'];
$cost_center_data = $result['cost_center_data'];
$costcenterlist = array();
foreach($cost_center_data as $costcenterdata){
    $costcenterlist[$costcenterdata['cost_center_id']][] = $costcenterdata;
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
        if($allrecord['party_id']  == $_SESSION['vendor_id']){
            $draftres[] = $allrecord;
            $transactionlogcnt++;
        }
    }elseif($allrecord['current_status'] == ST_ACTIVE){
        if($allrecord['party_id']  == $_SESSION['vendor_id']){
            $activeres[] = $allrecord;
            $transactionlogcnt++;
            $totaldocsamt += $allrecord['total_amount'];
        }
    }elseif($allrecord['current_status'] == ST_APPROVED){
        if($allrecord['party_id']  == $_SESSION['vendor_id']){
            $approvedres[] = $allrecord;
            $approvedresamt += $allrecord['total_amount'];
            $totaldocsamt += $allrecord['total_amount'];
            $transactionlogcnt++;
        }
    }else{
        if($allrecord['party_id']  == $_SESSION['vendor_id']){
            $inprocessres[] = $allrecord;
            $totaldocsamt += $allrecord['total_amount'];
            $transactionlogcnt++;
        }
    }
}
foreach($result['transaction_approval_logs'] as $transactionlog){
    if($transactionlog['response_type'] =='0' && $transactionlog['send_back_flag'] == '0' && $transactionlog['call_back'] == '1'){
        $createtransactioncnt++;
        $pendinglog[$transactionlog['transaction_id']] = $transactionlog;
    }else{
        $createtransactionapprovedcnt++;
        $aprovedlog[$transactionlog['transaction_id']] = $transactionlog;
    }
}
$status_progress = $result['status_progress'];
$allpermision = $_SESSION['role_permission'][$optiondetails['application_table_name']];
if($allpermision['permission_view'] == '1'){$permission_listbtn = "disabled";
}
if($allpermision['permission_insert'] == '1'){$insert_permissionbtn = "disabled";
}
if($allpermision['permission_import'] == '1'){$permission_importbtn = "disabled";
}
if($allpermision['permission_update'] == '1'){$edit_permissionbtn = "disabled";
}
if($allpermision['permission_approval'] == '1'){$approval_permissionbtn = "disabled";
}

$generatedocs = $result['generate'];

$table_name = $optiondetails['application_table_name'];

?>
<script type="text/javascript">
    function viewAllRecord(transaction_table,type){
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
            var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
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
</script>
<div class="row">
    <input type="hidden" id="transaction_option_name" value='<?php echo $_POST['option']; ?>'>

    <div class="col-lg-9" style="padding: 0px !important;">
        <div class="col-lg-12 row" style="padding: 0px !important; margin: 0px!important;">
            <div class="col-lg-3">
                <div class="card sale-card mb-2">
                    <div class="card-header">
                        <h5><?php echo $_POST['menu_label']; ?></h5>
                        <div class="card-header-right">
                            <a href="#!" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="<?php echo $_POST['menu_label']; ?> Details "<?php echo $permission_listbtn; ?> onclick="getTransactionInvoiceList()"> <i class="ti-eye icon-dark"></i></a>
                        </div>
                    </div>
                    <div class="card-block text-center">

<?php
$approvedresamt = $approvedresamt !=""?$approvedresamt:0;
$totaldocsamt = $totaldocsamt !=""?$totaldocsamt:0;
$totaldoc = $totaldocsamt+$approvedresamt;
if($totaldoc != 0 && $totaldoc !=""){
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
                          <img src="<?php echo "uploads/all/".$_SESSION['user']['user_photo']; ?>" class="percentag-img-radius img-90" alt="Details" style="border-radius: 50%;border: 8px solid #ffffff;">
                       <?php }elseif($_SESSION['employee']['gender'] !=""){ ?>
                                <img src="uploads/all/<?php echo $_SESSION['employee']['gender'].".png"; ?>" class="percentag-img-radius img-90" alt="Details" style="border-radius: 50%;border: 8px solid #ffffff;">
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
                                    echo convert_Rupee_Number($totaldocsamt !=''?$totaldocsamt:"0"); ?></h5>
                                    <p class="m-b-0 f-10">Total Value</p>
                                </div>
                            </div>
                            <div class="col-6 b-l-default">
                                <div class="s-caption bg-c-green m-0"></div>
                                <div class="s-cont d-inline-block">
                                    <h5 class="f-w-700 m-b-0 f-12"><?php
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
                        <div class="text-facebook m-b-20"><i class="ti-announcement "></i></div>
                        <div class="row mt-2">
                            <?php
                            $anouncement =  API."?mode=togetlatestannouncement";
                            $anouncement_list = callAPI($anouncement);
                            $anouncement_list = json_decode($anouncement_list,1);
                            ?>
                            <p><?php echo substr($anouncement_list['announcement_message'],0,250);?></p>
                            <p class="m-b-0 m-t-15"><a  href="index.php?option=COM_VIEW_ANNOUNCEMENTS" class="btn btn-info waves-effect waves-light"><?php echo $anouncement_list['button_label']; ?></a></p>
                        </div>
                    </div>
                </div>
                </div>
                <div class="col-lg-9">
                    <div class="tab-content tabs">
                        <div class="tab-pane card-body active p-0" id="selfdocuments" role="tabpanel">
                    <div class="card mb-2">
                        <ul class="nav md-tabs infisuite_tab mt-2 ml-2">
                            <li class="nav-item">
                                <a class="nav-link f-w-600 f-12 active" data-bs-toggle="tab" href="#itemcategory" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                    <?php
                                    $itemcatname = "Item Categories";
                                    if($_SESSION['work_module_setup_id'] == PETTY_CASH_MODULE){
                                        $itemcatname =  "Petty Cash";
                                    }elseif($_SESSION['work_module_setup_id'] == ACCOUNT_MODULE){
                                        $itemcatname =  "Account Group";
                                    }  ?>
                                    <?php echo $itemcatname; ?><span class="form-label label label-xs label-success m-l-10"><?php echo array_sum(array_map("count", $categorydatalist)); ?></span></a>

                            </li>
                            <li class="nav-item">
                                <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#itemlocation" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Locations<span class="form-label label label-xs label-success m-l-10"><?php echo array_sum(array_map("count", $locationlist)); ?></span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#costcenter" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Cost Centers<span class="form-label label label-xs label-success m-l-10"><?php echo array_sum(array_map("count", $costcenterlist)); ?></span></a>
                            </li>
                        </ul>
                        <div class="tab-content tabs custom-scroll" style="max-height: 1000px;">
                            <div class="tab-pane card-body active p-0" id="itemcategory" role="tabpanel">
                                <div class="card-header">
                                    <h5><?php echo $itemcatname; ?></h5>
                                    <div class="card-header-right table-card-header">
                                         <div class="input-group input-group-button mb-0" >
                                            <input aria-label="Search" type="text" id="dynamicfilterhideshow" data-id='categoryfilter' data-type="tr" class="form-control form-control-sm width1"  placeholder="Search" data-bs-toggle="tooltip" data-placement="bottom" title="Search">
                                            <span class="input-group-text addon-style">
                                                <span class=""><span class="ti-search text-white mt-2"></span></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-block accordion-block color-accordion-block">
                                    <div class="color-accordion" id="color-accordion">
                                        <?php
                                        $recordaray = $norecordaray = array();
                                        foreach($item_categories as $item_categorielist){
                                            $list = $categorydatalist[$item_categorielist['id']];
                                            if(count((array)$list)){
                                                $recordaray[] = $item_categorielist;
                                            }else{
                                                $norecordaray[] = $item_categorielist;
                                            }
                                        }
                                        $margeitemcat = array_merge((array)$recordaray,(array)$norecordaray);
                                        foreach($margeitemcat as $item_categorie){
                                            $list = $categorydatalist[$item_categorie['id']]; ?>
                                            <a class="accordion-msg b-none waves-effect waves-light f-w-600"><?php echo $item_categorie['category_description']; ?> <span class="form-label label label-md label-warning float-right"><?php echo count((array)$list); ?> </span></a>
                                            <div class="accordion-desc custom-scroll p-0" style="max-height: 400px;">
                                                <?php if(count((array)$list) !=0){ ?>
                                            <div class="row" style="position: sticky;top: 0px;">
                                                <div class="col-lg-8">&nbsp;</div>
                                                <div class="col-lg-4 " >

                                                </div>
                                            </div>
                                                    <table class="table table-hover m-b-0" style="font-size:13px;" id="categoryfilter_2">
                                                        <thead>
                                                            <th scope="col">Transaction No.</th>
                                                            <th scope="col">Originator</th>
                                                            <th scope="col">Date</th>
                                                            <th scope="col">Party Name</th>
                                                            <th scope="col" class="text-right">Amount</th>
                                                            <th scope="col">Satus</th>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach($list as $list){ ?>
                                                                <tr>
                                                                    <td><a href="index.php?option=<?php echo $optiondetails['insert_page_name']; ?>&Rid=<?php echo deocdeId('id:'.$list['id']); ?>" <?php echo $edit_permissionbtn; ?>><?php echo $list['transaction_number']; ?></a></td>
                                                                    <td class="w-b-w-s"><?php echo $_SESSION['users'][$list['originator']]; ?></td>
                                                                    <td><?php echo fromDate($list['transaction_date']); ?></td>
                                                                    <td class="w-b-w-s"><?php echo $list['party_name']; ?></td>
                                                                    <td class="text-right"><?php echo customeummarmonyformateifine($list['total_amount']); ?></td>
                                                                    <td >
                                                                        <span class="form-label label label-md" style="color:<?php echo $statuse[$list['current_status']]['color']; ?>; background-color:<?php echo $statuse[$list['current_status']]['background_color']; ?>;"><?php echo $status_progress[$list['current_status']]['name']; ?></span>
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
                                                <?php    } ?>
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
                                            <input aria-label="Search" type="text" id="dynamicfilterhideshow_2" data-id='locationfilter' data-type="tr" class="form-control form-control-sm width1"  placeholder="Search" data-bs-toggle="tooltip" data-placement="bottom" title="Search">
                                            <span class="input-group-text addon-style">
                                                <span class=""><span class="ti-search text-white mt-2"></span></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-block accordion-block color-accordion-block">
                                    <div class="color-accordion" id="color-accordion-location">
                                        <?php
                                        $recordaray = $norecordaray = array();
                                        foreach($locations as $item_categorielist){
                                            $list = $locationlist[$item_categorielist['id']];
                                            if(count((array)$list)){
                                                $recordaray[] = $item_categorielist;
                                            }else{
                                                $norecordaray[] = $item_categorielist;
                                            }
                                        }
                                        $margeitemloc = array_merge((array)$recordaray,(array)$norecordaray);
                                        foreach($margeitemloc as $item_categorie){
                                            $list = $locationlist[$item_categorie['id']]; ?>
                                            <a class="accordion-msg b-none waves-effect waves-light f-w-600"><?php echo $item_categorie['location_name']; ?> <span class="form-label label label-md label-warning float-right"><?php echo count((array)$list); ?> </span></a>
                                            <div class="accordion-desc custom-scroll p-0" style=" height: 400px;">
                                                <?php if(count((array)$list) !=0){ ?>
                                            <div class="row" style="position: sticky;top: 0px;">
                                                <div class="col-lg-8">&nbsp;</div>
                                                <div class="col-lg-4 ">

                                                </div>
                                            </div>
                                                    <table class="table table-hover m-b-0" style="font-size:13px;" id="locationfilter_2">
                                                        <thead>
                                                            <th scope="col">Transaction No.</th>
                                                            <th scope="col">Originator</th>
                                                            <th scope="col">Date</th>
                                                            <th scope="col">Party Name</th>
                                                            <th scope="col" class="text-right">Amount</th>
                                                            <th scope="col">Satus</th>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach($list as $list){?>
                                                                <tr>
                                                                    <td><a href="index.php?option=<?php echo $optiondetails['insert_page_name']; ?>&Rid=<?php echo deocdeId('id:'.$list['id']); ?>" <?php echo $edit_permissionbtn; ?>><?php echo $list['transaction_number']; ?></a></td>
                                                                    <td class="w-b-w-s"><?php echo $_SESSION['users'][$list['originator']]; ?></td>
                                                                    <td><?php echo fromDate($list['transaction_date']); ?></td>
                                                                    <td class="w-b-w-s"><?php echo $list['party_name']; ?></td>
                                                                    <td class="text-right"><?php echo customeummarmonyformateifine($list['total_amount']); ?></td>
                                                                    <td >
                                                                        <span class="form-label label label-md" style="color:<?php echo $statuse[$list['current_status']]['color']; ?>; background-color:<?php echo $statuse[$list['current_status']]['background_color']; ?>;"><?php echo $status_progress[$list['current_status']]['name']; ?></span>
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
                                                <?php    } ?>
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
                                            <input aria-label="Search" type="text" id="dynamicfilterhideshow_3" data-id='costcenterfilter' data-type="tr" class="form-control form-control-sm width1"  placeholder="Search" data-bs-toggle="tooltip" data-placement="bottom" title="Search">
                                            <span class="input-group-text addon-style">
                                                <span class=""><span class="ti-search text-white mt-2"></span></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-block accordion-block color-accordion-block">
                                    <div class="color-accordion" id="color-accordion-costcenter">

                                        <?php
                                        $recordaray = $norecordaray = array();
                                        foreach($cost_center_datalist as $item_categorielist){
                                            $list = $costcenterlist[$item_categorielist['id']];
                                            if(count((array)$list)){
                                                $recordaray[] = $item_categorielist;
                                            }else{
                                                $norecordaray[] = $item_categorielist;
                                            }
                                        }
                                        $margeitemcostcenter = array_merge((array)$recordaray,(array)$norecordaray);
                                        foreach($margeitemcostcenter as $item_categorie){
                                            $list = $costcenterlist[$item_categorie['id']]; ?>
                                            <a class="accordion-msg b-none waves-effect waves-light f-w-600" ><?php echo $item_categorie['cost_center_name']; ?> <span class="form-label label label-md label-warning float-right"><?php echo count((array)$list); ?> </span></a>
                                            <div class="accordion-desc custom-scroll p-0" style=" height: 400px;">
                                                <?php if(count((array)$list) !=0){ ?>
                                            <div class="row" style="position: sticky;top: 0px;">
                                                <div class="col-lg-8">&nbsp;</div>
                                                <div class="col-lg-4 ">
                                                </div>
                                            </div>
                                                    <table class="table table-hover m-b-0" style="font-size:13px;" id="costcenterfilter_2">
                                                        <thead>
                                                            <th scope="col">Transaction No.</th>
                                                            <th scope="col">Originator</th>
                                                            <th scope="col">Date</th>
                                                            <th scope="col">Party Name</th>
                                                            <th scope="col" class="text-right">Amount</th>
                                                            <th scope="col">Satus</th>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach($list as $list){?>
                                                                <tr>
                                                                    <td><a href="index.php?option=<?php echo $optiondetails['insert_page_name']; ?>&Rid=<?php echo deocdeId('id:'.$list['id']); ?>" <?php echo $edit_permissionbtn; ?>><?php echo $list['transaction_number']; ?></a></td>
                                                                    <td class="w-b-w-s"><?php echo $_SESSION['users'][$list['originator']]; ?></td>
                                                                    <td><?php echo fromDate($list['transaction_date']); ?></td>
                                                                    <td class="w-b-w-s"><?php echo $list['party_name']; ?></td>
                                                                    <td class="text-right"><?php echo customeummarmonyformateifine($list['total_amount']); ?></td>
                                                                    <td>
                                                                        <span class="form-label label label-md" style="color:<?php echo $statuse[$list['current_status']]['color']; ?>; background-color:<?php echo $statuse[$list['current_status']]['background_color']; ?>;"><?php echo $status_progress[$list['current_status']]['name']; ?></span>
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
                                                <?php    } ?>
                                            </div>

                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
</div>
<div class="tab-pane card-body p-0" id="tabsopen" role="tabpanel">

</div>
</div>
<?php  ?>

</div>

</div>


        </div>
<div class="col-lg-3">
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
                        <div class="">
                            <p class="text-muted m-b-5 f-12"><?php echo $Notification_Message; ?>  <span class="float-right"><?php echo $Notification_DateTime; ?></span></p>
                        </div>
                    </div>
                <?php } ?>
                <?php } else {
                    echo "<h4><i class='fa fa-warning'></i> No Holidays Available</h4>";
                } ?>
            </div>
        </div>
</div>
    </div>
    <script type="text/javascript" src="assets/pages/accordion/accordion.js"></script>

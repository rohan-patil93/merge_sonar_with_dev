<?php $_SESSION['apply_js']['multiselest'] = 1;
$_SESSION['apply_js']['CRM_DASHBOARDS'] = 1;
$from_date = fromDate($_REQUEST['from_date'] !=""?$_REQUEST['from_date']:date("Y-01-01"));
$to_date =fromDate($_REQUEST['to_date'] !=""?$_REQUEST['to_date']:date("Y-12-t"));
$menu = API."?mode=getcrmleads&dashboard=Y&admin_type=".$_SESSION['admin_type'];
$page_list = callAPI($menu);
$result = json_decode($page_list,1);
$dashboard_cnt = $result['dashboard_cnt'];
$crm_leads = $result['crm_leads'];
$crm_contacts = $result['crm_contacts'];
$crm_deals = $result['crm_deals'];
$crm_pendding_tasks = $result['crm_pendding_tasks'];
$crm_pendding_task_details = $result['crm_pendding_task_details'];
$crm_companies_id_name = $result['crm_companies_id_name'];
$project_task_status = $result['project_task_status'];
$crm_general_codes = $result['crm_general_codes'];
$feasibility_requirements = $result['feasibility_requirements'];

$crm_source_types = array();
foreach($crm_general_codes as $key=>$crmsource1){
    foreach($crmsource1 as $crmsource){
        $crm_source_types[$key][$crmsource['value']] = $crmsource['label'];
    }
}
$crm_activity_typesres =$result['crm_activity_types'];
$crm_activity_types = array();
foreach($crm_activity_typesres as $crmlt){
        $crm_activity_types[$crmlt['id']] =$crmlt;
}

//Start graph crm leads
$crm_leads_graph = $result['crm_leads_graph'];
foreach($crm_leads_graph as $crmstat){
    $crm_leads_chart[] = array($project_task_status['crm_leads'][$crmstat['crm_status']]['name'],(int)$crmstat['cnt']);
}
//End graph crm leads

//Start graph crm Contacts
$crm_contacts_graph = $result['crm_contacts_graph'];
foreach($crm_contacts_graph as $crmstat){
    $crm_contacts_chart[] = array($project_task_status['crm_leads'][$crmstat['crm_status']]['name'],(int)$crmstat['cnt']);
}
//End graph crm Contacts
//Start graph crm Deals
$crm_deals_graph= $result['crm_deals_graph'];
foreach($crm_deals_graph as $crmstat){
    $crm_deals_chart[] = array($project_task_status['crm_leads'][$crmstat['crm_status']]['name'],(int)$crmstat['cnt']);
}
//End graph crm Deals

//LEAD CHART
//LEAD CHART

// Get current date
$feasibility_requirements_expiry_days = $result['feasibility_requirements_expiry_days'];
$currentDate = new DateTime();

// Arrays to hold categorized data
$expired = array();
$days15 = array();
$days30 = array();
$days45 = array();

foreach ($feasibility_requirements_expiry_days as $item) {
    $expiryDate = new DateTime($item['evaluation_end_date']);
    $diffDays = $expiryDate->diff($currentDate)->days;  // Difference in days

    // Determine category based on difference in days
    if ($expiryDate < $currentDate ) {
        // Expired
        $expired[] = $item;
    } elseif ($diffDays <= 15) {
        // Within 15 days
        $days15[] = $item;
    } elseif ($diffDays <= 30) {
        // Within 30 days
        $days30[] = $item;
    } elseif ($diffDays <= 45) {
        // Within 45 days
        $days45[] = $item;
    }
}
?>
<script type="text/javascript">

    <?php if($_GET['TRANST'] !=""){ ?>
        console.log('insidesdsef ----- <?php echo $_GET['TRANST']; ?>');
        window.addEventListener("load", function() {
            getCRMListDashboard('<?php echo $_GET['TRANST']; ?>');
        });
    <?php } ?>

    function viewAllListPage(crm_tab_type){
        if(crm_tab_type =="home"){
            updateUrlParameter('TRANST','home');
            $(".datepickerselection").hide();
        }else{
            $(".datepickerselection").show();
            $("#home-tab").removeClass("active");
            $(".active_all_class").removeClass("active");

            $(".active_"+crm_tab_type).addClass("active");
            $(".dynamictabviews").addClass("active");
            getCRMListDashboard(crm_tab_type);
        }
    }
    function leadPageCall(crm_tab_type,rid="",crm_history_id=""){
        console.log("dsfdsvdfsv");
        var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $("#htmlsetpage").html(loader);
        var url = "";
        if(crm_tab_type == "LEAD"){
            url = "custom_insert_crm_leads.php";
        }else if(crm_tab_type == "CONTACT"){
            url = "custom_insert_crm_contacts.php";
        }else if(crm_tab_type == "COMPANY"){
            url = "custom_insert_crm_companies.php";
        }else if(crm_tab_type == "CAPAIGN"){
            url = "custom_insert_crm_campaigns.php";
        }else if(crm_tab_type == "DEALS"){
            url = "custom_insert_crm_deals.php";
        }else if(crm_tab_type == "PROJECT"){
            url = "custom_insert_crm_projects.php";
        }
        jQuery.ajax({
            url: "views/"+url,
            data: "rid="+rid+"&crm_history_id="+crm_history_id,
            type: "POST",
            success:function(data){
                $('#htmlsetpage').html(data);
                $(".js-example-basic-single").select2();
                $(".js-example-basic-multiple").select2();
                $(".datepicker" ).datepicker({
                    showOn: "both",
                    buttonText: "<i class='ti-calendar text-dark f-20 f-w-800 m-r-10'></i>",
                    dateFormat: DateFormate,
                    changeMonth: true,
                    changeYear: true,
                    minDate: 0
                }).next("button.ui-datepicker-trigger").html("<i class='ti-calendar text-dark f-12 f-w-800 m-r-10'></i>");
                if($(".js-success-sm").length  > 0){
                    var elem = Array.prototype.slice.call(document.querySelectorAll('.js-success-sm'));
                    elem.forEach(function(html) {
                        var switchery = new Switchery(html, { color: '#2ed8b6', jackColor: '#fff',size: 'small' });
                    });
                }
            if(crm_tab_type == "CAPAIGN" || crm_tab_type == "PROJECT"){
                $(".startdatepicker").datepicker({
                    dateFormat: date_format,
                    changeMonth: true,
                    buttonText: "<i class='ti-calendar text-dark f-20 f-w-800 m-r-10'></i>",
                    changeYear: true,
                    showOn: "both",
                    onSelect: function (date) {
                        var date2 = $('.startdatepicker').datepicker('getDate');
                        $('.enddatepicker').datepicker('setDate', date2);
                        $('.enddatepicker').datepicker('option', 'minDate', date2);
                    }
                }).next("button.ui-datepicker-trigger").html("<i class='ti-calendar text-dark f-12 f-w-800 m-r-10'></i>");
                $('.enddatepicker').datepicker({
                    dateFormat: date_format,
                    changeMonth: true,
                    buttonText: "<i class='ti-calendar text-dark f-20 f-w-800 m-r-10'></i>",
                    changeYear: true,
                    showOn: "both",
                    onClose: function () {
                        var dt1 = $('.startdatepicker').datepicker('getDate');
                        console.log(dt1);
                        var dt2 = $('.enddatepicker').datepicker('getDate');
                        if (dt2 <= dt1) {
                            var minDate = $('.enddatepicker').datepicker('option', 'minDate');
                            $('.enddatepicker').datepicker('setDate', minDate);
                        }
                    }
                }).next("button.ui-datepicker-trigger").html("<i class='ti-calendar text-dark f-12 f-w-800 m-r-10'></i>");
            }

            }
        });
    }
    function crmupdatestatus(status,crm_lead_id){
        console.log(status+" -- "+crm_lead_id);
        var transaction_table = "crm_leads";
        jQuery.ajax({
            url: "ajax.php",
            data: "page=dynamicupdatestatus&current_status="+status+"&rid="+crm_lead_id+"&transaction_table="+transaction_table,
            type: "POST",
            success:function(data){
                console.log(data);
                var jsonres = JSON.parse(data);
                console.log(jsonres.res.status);
                if(jsonres.res.status == "sucess"){
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3000']; ?>','');
                }else{
                    tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX',jsonres.res.message,'');
                }
            }
        });

    }
    function dynamicSelectDateRange(type,name,start="",end=""){
        $("#btn_date").text(name);
        $("#pick_date").val(name);
        $("#shortcode_date").val(type);
        $(".empty_active").removeClass("active");
        $("#"+type).addClass("active");
        if(name == "Custom Date"){
            $("#custome_date").show('slow');
        }else{
            $("#custome_date").hide();
            $("#from_date").val(start);
            $("#to_date").val(end);
        }
        getCRMListDashboard();
    }

    function getCRMListDashboard(transaction_table=""){

        if(transaction_table ==""){
            var urlParams = new URLSearchParams(window.location.search);
            if(urlParams.has('TRANST')){
                param_value = urlParams.get('TRANST');
                transaction_table = (param_value !=null || param_value !="")?param_value:"";
            }else{
                location.reload();
            }
        }
        $(".datepickerselection").show();
        updateUrlParameter('TRANST',transaction_table);
        var from_date = $("#from_date").val();
        var to_date = $("#to_date").val();
        var loader = '<div class="text-center" id="loader-show_2"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $(".dynamictabviews").html(loader);
        var url = "";
        if(transaction_table == "crm_leads"){
            url = "custom_list_crm_leads.php";
        }else if(transaction_table == "crm_contacts"){
            url = "custom_list_crm_contacts.php";
        }else if(transaction_table == "crm_companies"){
            url = "custom_list_crm_companies.php";
        }else if(transaction_table == "crm_campaigns"){
            url = "custom_list_crm_campaigns.php";
        }else if(transaction_table == "crm_projects"){
            url = "custom_list_crm_projects.php";
        }else if(transaction_table == "crm_deals"){
            url = "custom_list_crm_deals.php";
        }else if(transaction_table == "crm_feasibilities"){
            url = "custom_list_crm_feasibilities.php";
        }
        console.log(url);
        jQuery.ajax({
            url: "views/"+url,
            data: "getlistpage=Y&transaction_table="+transaction_table+"&from_date="+from_date+"&to_date="+to_date,
            type: "POST",
            success:function(data){
                $('.dynamictabviews').html(data);
                $('.active_all_class').removeClass('active');
                if(transaction_table == 'home' || transaction_table == ''){
                    $('.active_crm_home').addClass('active');
                    $('.activehomecomments').addClass('active');
                }else{
                    $('.active_'+transaction_table).addClass('active');
                    $('.dynamictabviews').addClass('active');
                }
            }
        });
    }

    function createSalesOrdersWithFeasibilty(feasibility_id, crm_contact_id){
        swal({
            title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
            text: 'Are you sure you want to create a Sales Order?',
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function(isConfirm) {
            if (isConfirm) {
                 jQuery.ajax({
                    url: "ajax.php",
                    data: "page=createsalesorderswithfeasibilty&feasibility_id="+feasibility_id+"&crm_contact_id="+crm_contact_id,
                    type: "POST",
                    success:function(data){
                        console.log(data);
                        var jsonres = JSON.parse(data);
                        console.log(jsonres);
                        console.log(jsonres.main.status);
                        if(jsonres.main.status == "sucess"){
                            swal({
                                title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                                text: "Sales Order <?php echo $_SESSION['error_message']['9059']; ?>",
                                type: "success",
                                showCancelButton: false,
                                closeOnConfirm: true,
                                showLoaderOnConfirm: true
                            }, function () {
                                window.location.replace("index.php?option=COM_LIST_SALES_ORDERS");
                            });
                        }else{
                            tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX',jsonres.main.message,'');
                        }
                    }
                });
            }
        });

    }

    function showFeasibilityReport(feasibility_id){
        console.log("feasibility_id: "+feasibility_id);

        var loader = '<div class="text-center" id="loader-show_3"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $("#dynamic_popupxxl").html(loader);

        jQuery.ajax({
            url: "views/custom_view_feasibility_report.php",
            data:{'feasibility_id': feasibility_id},
            type: "GET",
            success:function(data){

                $('#dynamic_popupxxl').html(data);
                $('#kt_modal_dynamic_popupxxl').modal("show");
            },
            error:function (){}
        });
    }
</script>

<div class="card product-progress-card mb-3">
    <div class="card-block">
        <div class="row ">
            <div class="col-lg-5">
                <div class="d-flex">
                <div class="flex-shrink-0 m-r-30 m-l-25 mt-lg-0 mt-3">
                    <?php if($_SESSION['user']['user_photo']!=""){ ?>
                    <div class="symbol symbol-50 symbol-lg-120">
                      <img src="../uploads/all/<?php echo $_SESSION['user']['user_photo']; ?>" class="img-120 " alt="Details"/>
                    </div>

                  <?php }else{ ?>
                    <div class="symbol symbol-50 symbol-lg-120">
                      <img src="../uploads/all/<?php echo $_SESSION['employee']['gender'].".png"; ?>" class="img-120 " alt="Details"/>
                    </div>
                  <?php } ?>

                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between flex-wrap mt-1">
                        <div class="d-flex mr-3">
                            <span class="text-dark-75 text-hover-primary font-size-h5 font-weight-bold mr-3 f-16 f-w-600"><?php echo $_SESSION['user']['display_name']; ?></span>
                        </div>
                    </div>
                    <div class="">
                        <div class="d-flex flex-wrap justify-content-between mt-1 ">
                            <div class="d-flex flex-column flex-grow-1 pr-8  ">
                                <div class="d-flex flex-wrap mb-2 mt-2">
                                    <a href="#" class="text-dark-50 text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2 m-l-5">
                                    <i class="ti-email f-14 text-primary"></i>&nbsp;  <?php echo $_SESSION['user']['user_email']; ?></a>
                                </div>
                            </div>
                            <input type="hidden" id="location_id" value="">
                            <input type="hidden" id="start_date" value="2023-08-11">
                        </div>
                    </div>
                </div>
              </div>
            </div>
            <div class="col-lg-7" >
            <div class="col-lg-12" >
                <div class="row">
                    <div class="col-xl-4 col-md-6">
                        <div class="card social-card">
                            <div class="card-body text-center p-0">
                                <div class="text-facebook m-b-5"><i class="ti-user f-24 text-mute"></i></div>
                                <h3 class="text-facebook f-w-700"><?php echo $dashboard_cnt['today_crm_lead_cnt']!=""?$dashboard_cnt['today_crm_lead_cnt']:0; ?></h3>
                                <p>Today's Leads</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6 ">
                        <div class="card social-card">
                            <div class="card-body text-center p-0">
                                <div class="text-facebook m-b-5"><i class="ti-user f-24 text-mute"></i></div>
                                <h3 class="text-facebook f-w-700"><?php echo $dashboard_cnt['today_crm_contacts_cnt']!=""?$dashboard_cnt['today_crm_contacts_cnt']:0; ?></h3>
                                <p>Today's Contacts</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6">
                        <div class="card social-card">
                            <div class="card-body text-center p-0">
                                <div class="text-facebook m-b-5"><i class="ti-user f-24 text-mute"></i></div>
                                <h3 class="text-facebook f-w-700"><?php echo $dashboard_cnt['today_crm_deals_cnt']!=""?$dashboard_cnt['today_crm_deals_cnt']:0; ?></h3>
                                <p>Today's Deals</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="row mt-4">
                    <div class="col-2 text-center">
                        <div class="s-caption  text-primary"><i class="ti-list f-20"></i></div>
                        <div class="s-cont d-inline-block">
                            <h5 class="f-w-700 m-b-0"><?php echo $dashboard_cnt['crm_lead_cnt']!=""?$dashboard_cnt['crm_lead_cnt']:0; ?></h5>
                            <p class="m-b-0 f-12">Total leads</p>
                        </div>
                    </div>
                    <div class="col-2 b-l-default text-center">
                        <div class="s-caption  text-danger"><i class="ti-time f-20"></i></div>
                        <div class="s-cont d-inline-block">
                            <h5 class="f-w-700 m-b-0"><?php echo $dashboard_cnt['crm_contacts_cnt']!=""?$dashboard_cnt['crm_contacts_cnt']:0; ?></h5>
                            <p class="m-b-0 f-12">Total Contacts</p>
                        </div>
                    </div>
                    <div class="col-2 b-l-default text-center">
                        <div class="s-caption  text-info"><i class="ti-timer f-20"></i></div>
                        <div class="s-cont d-inline-block">
                            <h5 class="f-w-700 m-b-0"><?php echo $dashboard_cnt['crm_companies_cnt']!=""?$dashboard_cnt['crm_companies_cnt']:0; ?></h5>
                            <p class="m-b-0  f-12">Total Companies</p>
                        </div>
                    </div>
                    <div class="col-2 b-l-default text-center">
                        <div class="s-caption  text-info"><i class="ti-write f-20"></i></div>
                        <div class="s-cont d-inline-block">
                            <h5 class="f-w-700 m-b-0"><?php echo $dashboard_cnt['crm_campaigns_cnt']!=""?$dashboard_cnt['crm_campaigns_cnt']:0; ?></h5>
                            <p class="m-b-0 f-12">Total Campaigns</p>
                        </div>
                    </div>
                    <div class="col-2 b-l-default text-center">
                        <div class="s-caption  text-success"><i class="ti-check-box f-20"></i></div>
                        <div class="s-cont d-inline-block">
                            <h5 class="f-w-700 m-b-0"><?php echo $dashboard_cnt['crm_deals_cnt']!=""?$dashboard_cnt['crm_deals_cnt']:0; ?></h5>
                            <p class="m-b-0 f-12">Total deals</p>
                        </div>
                    </div>
                     <div class="col-2 b-l-default text-center">
                        <div class="s-caption  text-success"><i class="ti-check-box f-20"></i></div>
                        <div class="s-cont d-inline-block">
                            <h5 class="f-w-700 m-b-0"><?php echo $dashboard_cnt['projects_cnt']!=""?$dashboard_cnt['projects_cnt']:0; ?></h5>
                            <p class="m-b-0 f-12">Total Projects</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-6">
        <ul class="nav md-tabs infisuite_tab mb-3">
            <li class="nav-item">
                <button type="button" class="nav-link f-w-600 f-12 active_crm_home active active_all_class" data-bs-toggle="tab" home-tab" role="tab" aria-selected="true" onclick="viewAllListPage('home')">Home</button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link f-w-600 f-12 active_crm_leads active_all_class" data-bs-toggle="tab" lead-tab" role="tab" aria-selected="true" onclick="getCRMListDashboard('crm_leads')">Leads</button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link f-w-600 f-12 active_crm_contacts active_all_class" data-bs-toggle="tab" lead-tab" role="tab" aria-selected="true" onclick="getCRMListDashboard('crm_contacts')">Contacts</button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link f-w-600 f-12 active_crm_companies active_all_class" data-bs-toggle="tab" lead-tab" role="tab" aria-selected="true" onclick="getCRMListDashboard('crm_companies')">Companies</button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link f-w-600 f-12 active_crm_deals active_all_class" data-bs-toggle="tab" lead-tab" role="tab" aria-selected="true" onclick="getCRMListDashboard('crm_deals')">Deals</button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link f-w-600 f-12 active_crm_campaigns active_all_class" data-bs-toggle="tab" lead-tab" role="tab" aria-selected="true" onclick="getCRMListDashboard('crm_campaigns')"> Campaign</button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link f-w-600 f-12 active_crm_projects active_all_class" data-bs-toggle="tab" lead-tab" role="tab" aria-selected="true" onclick="getCRMListDashboard('crm_projects')">Projects</button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link f-w-600 f-12 active_crm_feasibilities active_all_class" data-bs-toggle="tab" lead-tab" role="tab" aria-selected="true" onclick="getCRMListDashboard('crm_feasibilities')">Feasibility</button>
            </li>

        </ul>
    </div>
   <div class="col-lg-3">
        <div class="row mb-2" style="display: none" id="custome_date">
            <div  class="col-lg-5">
                <span  class="form-label">Start Date</span>
                <input aria-label="From Date" type="text" class="form-control filterdynamic datepicker" name="from_date" id="from_date" value="<?php echo $from_date; ?>">
            </div>
            <div class="col-lg-5">
                <span  class="form-label">End Date</span>
                <input aria-label="To Date" type="text" class="form-control filterdynamic datepicker" name="to_date" id="to_date" value="<?php echo $to_date; ?>">
            </div>
            <div class="col-lg-2">
                <button class="btn btn-primary mt-4 desktop-view" onclick="getCRMListDashboard()"><span class="ti-search mt-2"></span></button>
            </div>
        </div>
        <div class="row mb-2 datepickerselection" style="display: none">
            <input type="hidden" name="pick_date" id="pick_date" value="TY">
            <input type="hidden" name="shortcode_date" id="shortcode_date" value="TY">
            <div class="btn-group dropdown-split-primary" style="width: 100%;">
                <button type="button" class="btn btn-primary" style="width: 80%;height: 40px;"><i class="ti-calendar"></i>
                    <span id="btn_date">This Year</span>
                </button>
                <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split waves-effect waves-light" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 20%;height: 40px;">
                    <span class="visually-hidden">Toggle primary</span>
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="TD"?"active":"";?> empty_active" id="TD" href="#!" onclick="dynamicSelectDateRange('TD','Today','<?php echo fromDate(date("Y-m-d")); ?>','<?php echo fromDate(date("Y-m-d")); ?>')">Today</a>
                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="TW"?"active":"";?> empty_active" id="TW" href="#!" onclick="dynamicSelectDateRange('TW','This Week','<?php echo fromDate(date("Y-m-d", strtotime('monday this week')));?>','<?php echo fromDate(date("Y-m-d", strtotime('monday this sunday ')));?>')">This Week</a>
                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="TM"?"active":"";?> empty_active" id="TM" href="#!" onclick="dynamicSelectDateRange('TM','This Month','<?php echo fromDate(date("Y-m-01")); ?>','<?php echo fromDate(date("Y-m-t")); ?>')">This Month</a>
                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="TY"?"active":"";?> empty_active" id="TY" href="#!" onclick="dynamicSelectDateRange('TY','This Year','<?php echo fromDate(date("Y-01-01")); ?>','<?php echo fromDate(date("Y-12-31")); ?>')">This Year</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="YD"?"active":"";?> empty_active" id="YD" href="#!" onclick="dynamicSelectDateRange('YD','Yesterday','<?php echo fromDate(date('Y-m-d',strtotime("-1 days"))); ?>','<?php echo fromDate(date('Y-m-d',strtotime("-1 days"))); ?>')">Yesterday</a>
                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="PW"?"active":"";?> empty_active" id="PW" href="#!" onclick="dynamicSelectDateRange('PW','Previous Week','<?php echo fromDate(date('Y-m-d', strtotime("previous week monday"))); ?>','<?php echo fromDate(date('Y-m-d', strtotime("previous week sunday"))); ?>')">Previous Week</a>
                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="PM"?"active":"";?> empty_active" id="PM" href="#!" onclick="dynamicSelectDateRange('PM','Previous Month','<?php echo fromDate(date("Y-m-01",strtotime('last month'))); ?>','<?php echo fromDate(date("Y-m-t",strtotime('last month'))); ?>')">Previous Month</a>
                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="PY"?"active":"";?> empty_active" id="PY" href="#!" onclick="dynamicSelectDateRange('PY','Previous Year','<?php echo fromDate(date("Y-01-01",strtotime('last year'))); ?>','<?php echo fromDate(date("Y-12-31",strtotime('last year'))); ?>')">Previous Year</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="CD"?"active":""; ?> empty_active" id="CD" href="#!" onclick="dynamicSelectDateRange('CD','Custom Date')">Custom Date</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 text-right">
        <?php if($_SESSION['admin_type'] == 1){ ?>
        <a href="index.php?option=COM_VIEW_CRM_LEAD_AND_CONTACT_ASSIGNINGS" class="btn btn-success mb-2 desktop-view"> <i class="icofont icofont-plus icon-white "></i> Lead/Contact Assignings </a>
    <?php } ?>
    </div>
</div>
        <div class="tab-content tabs">
            <div class="tab-pane card-body p-0 active active_crm_home active_all_class" id="home-tab" role="tabpanel">
                <?php include_once "views/custom_view_crm_home_dashboards.php"; ?>
            </div>
            <div class="tab-pane card-body p-0 dynamictabviews active_all_class" id="lead-tab" role="tabpanel" >

            </div>

        </div>



<div id="sidebar111" class="users p-chat-user leadsidebar-form text-left" style="display:none">
    <div class="had-container">
        <div class="p-fixed users-main " id="htmlsetpage" >

        </div>
    </div>
</div>

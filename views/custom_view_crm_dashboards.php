<?php $_SESSION['apply_js']['multiselest'] = 1;
//$_SESSION['apply_js']['ratting'] = 1;
$_SESSION['apply_js']['CRM_DASHBOARDS'] = 1;
$from_date = fromDate($_REQUEST['from_date'] !=""?$_REQUEST['from_date']:date("Y-01-01"));
$to_date =fromDate($_REQUEST['to_date'] !=""?$_REQUEST['to_date']:date("Y-12-t"));
$menu = API."?mode=getcrmleads&dashboard=Y&admin_type=".$_SESSION['admin_type'];
$page_list = callAPI($menu);
$result = json_decode($page_list,1);
//echo "<pre>"; print_r($result);echo "</pre>";
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
    //if($crmlt['type'] == 2){
        $crm_activity_types[$crmlt['id']] =$crmlt;
    //}
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


//echo "<pre>"; print_r($feasibility_requirements);echo "</pre>";
// $crm_leads = $result['crm_leads'];

// $crm_companies = $result['crm_companies'];
// $crm_general_codes = $result['crm_general_codes'];
// $project_task_status = $result['project_task_status'];
// $crm_campaigns = $result['crm_campaigns'];
// $crm_deals = $result['crm_deals'];
// $crm_projects = $result['crm_projects'];
// $crm_source_types = array();
// foreach($crm_general_codes as $key=>$crmsource1){
//     foreach($crmsource1 as $crmsource){
// 	   $crm_source_types[$key][$crmsource['value']] = $crmsource['label'];
//     }
// }
// $crm_companies_id_name = $result['crm_companies_id_name'];
// $leadcode = array();
// foreach($crm_leadslist as $crm_leads){
// 	$leadcode[$crm_leads['id']] = $crm_leads['lead_code'];
// }
// $crm_lead_types =$res['crm_lead_types'];
// $crmleaddetailstypes = array();
// foreach($crm_lead_types as $crmlt){
//     if($crmlt['type'] == 2){
//         $crmleaddetailstypes[$crmlt['id']] =$crmlt;
//     }
// }
//LEAD CHART
// $crm_leads_graph = $result['crm_leads_graph'];
// foreach($crm_leads_graph as $crmstat){
//     $crm_leads_chart[] = array($project_task_status['crm_leads'][$crmstat['crm_status']]['name'],(int)$crmstat['cnt']);
// }
// $crm_contacts_graph = $result['crm_contacts_graph'];
// foreach($crm_contacts_graph as $crmstat){
//     $crm_contacts_chart[] = array($project_task_status['crm_leads'][$crmstat['crm_status']]['name'],(int)$crmstat['cnt']);
// }
// $crm_companies_graph  = $result['crm_companies_graph'];
// foreach($crm_companies_graph as $crmstat){
//     $crm_companies_chart[] = array($project_task_status['crm_leads'][$crmstat['crm_status']]['name'],(int)$crmstat['cnt']);
// }
// $crm_campaigns_graph= $result['crm_campaigns_graph'];
// foreach($crm_campaigns_graph as $crmstat){
//     $crm_campaigns_chart[] = array($project_task_status['crm_leads'][$crmstat['crm_status']]['name'],(int)$crmstat['cnt']);
// }
// $crm_deals_graph= $result['crm_deals_graph'];
// foreach($crm_deals_graph as $crmstat){
//     $crm_deals_chart[] = array($project_task_status['crm_leads'][$crmstat['crm_status']]['name'],(int)$crmstat['cnt']);
// }
//LEAD CHART

//print_r($crm_contacts_chart);
// $crm_histories =$result['crm_histories'];
// foreach($crm_histories as $crmhistry){
// 	$lastactivity[$crmhistry['transaction_table']][$crmhistry['transaction_id']]=$crmhistry;
// }
//print_r($lastactivity);


// print_r($res); // For debugging, you can remove this later
// Get current date
$feasibility_requirements_expiry_days = $result['feasibility_requirements_expiry_days'];
$currentDate = new DateTime();

// Arrays to hold categorized data
$expired = array();
$days15 = array();
$days30 = array();
$days45 = array();

// Loop through the API response and categorize based on expiry date
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
       //updateUrlParameter('TAB1',crm_tab_type);
    	var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;"></div>';
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
        if(name == <?php echo json_encode(__('Custom Date'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>){
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
        var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;"></div>';
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
            title: <?php echo json_encode(ucwords($_SESSION['config']['name']), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>,
            text: <?php echo json_encode(__('Are you sure you want to create a Sales Order?'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>,
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: <?php echo json_encode(__('Yes'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>,
            cancelButtonText: <?php echo json_encode(__('No'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>,
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
                                title: <?php echo json_encode(ucwords($_SESSION['config']['name']), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>,
                                text: <?php echo json_encode(__('Sales Order').' '.($_SESSION['error_message']['9059'] ?? ''), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>,
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

        var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;"></div>';
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
<!-- <ul class="nav md-tabs infisuite_tab mb-3" role="tablist">
	<li class="nav-item">
		<a class="nav-link f-w-600 f-12 active" data-bs-toggle="tab" href="#itemcategory" role="tab" aria-selected="true"><?= __('Leads'); ?><label class="form-label label label-xs label-success m-l-10"><?php echo count((array)$crm_leadslist); ?></label></a>
	</li>
	<li class="nav-item">
		<a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#itemlocation" role="tab" aria-selected="true"><?= __('Deals'); ?><label class="form-label label label-xs label-success m-l-10">0</label></a>
	</li>
	<li class="nav-item">
		<a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#costcenter" role="tab" aria-selected="true"><?= __('Contact'); ?><label class="form-label label label-xs label-success m-l-10">0</label></a>
	</li>
	<li class="nav-item">
		<a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#costcenter" role="tab" aria-selected="true"><?= __('Companies'); ?><label class="form-label label label-xs label-success m-l-10">0</label></a>
	</li>
</ul>
 -->

<?php
// function xor_encrypt($text, $key) {
//     $encrypted = '';
//     for ($i = 0; $i < strlen($text); $i++) {
//         $encrypted .= $text[$i] ^ $key[$i % strlen($key)];
//     }
//     return $encrypted;
// }

// // Custom Base64 Encoding (symbol-free)
// function safe_base64_encode($string) {
//     $data = base64_encode($string);
//     $data = str_replace(['+', '/', '='], ['-', '_', ''], $data);
//     return $data;
// }

// // Custom Base64 Decoding
// function safe_base64_decode($string) {
//     $data = str_replace(['-', '_'], ['+', '/'], $string);
//     $mod4 = strlen($data) % 4;
//     if ($mod4) {
//         $data .= substr('====', $mod4);
//     }
//     return base64_decode($data);
// }

// // Encrypt Function
// function encrypt($text, $key) {
//     $xor = xor_encrypt($text, $key);
//     return safe_base64_encode($xor);
// }

// // Decrypt Function
// function decrypt($encoded, $key) {
//     $xor = safe_base64_decode($encoded);
//     return xor_encrypt($xor, $key);  // XOR is reversible
// }

// // Example Usage
// $text = "infistaqin_infisuite-super_admin";
// $key = "mysecretkey";

// $encrypted = stringXOREncrypt($text);
// $decrypted = stringXORDecrypt($encrypted);

// echo "Original: $text\n";
// echo "Encrypted: $encrypted\n";
// echo "Decrypted: $decrypted\n";
?>





<div class="card product-progress-card mb-3">
    <div class="card-block">
        <div class="row ">
            <div class="col-lg-5">
                <div class="d-flex">
                <div class="flex-shrink-0 m-r-30 m-l-25 mt-lg-0 mt-3">
                    <?php if($_SESSION['user']['user_photo']!=""){ ?>
                    <div class="symbol symbol-50 symbol-lg-120">
                      <img src="../uploads/all/<?php echo $_SESSION['user']['user_photo']; ?>" class="img-120 " alt="" data-onerror-src="uploads/all/no-image.jpg"/>
                    </div>

                  <?php }else{ ?>
                    <div class="symbol symbol-50 symbol-lg-120">
                      <img src="../uploads/all/<?php echo $_SESSION['employee']['gender'].".png"; ?>" class="img-120 " alt="" data-onerror-src="uploads/all/no-image.jpg"/>
                    </div>
                  <?php } ?>
                    <!-- <div class="d-flex align-items-center w-100px w-sm-120px flex-column mt-3 ">
                        <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                          <span class="f-12 f-w-600"><?= __('Leads'); ?></span>
                          <span class="f-12 f-w-600"><?php echo $totaltaskper; ?>%</span>
                        </div>
                        <div class="col-md-12">
                          <div class="progress progress-xs">
                            <div class="progress-bar progress-bar-success" role="progressbar" style="width:<?php echo $totaltaskper; ?>%;" aria-valuenow=" " aria-valuemin="0" aria-valuemax="100"></div>
                          </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center w-100px w-sm-120px flex-column mt-3 ">
                        <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                          <span class="f-12 f-w-600"><?= __('Contacts'); ?></span>
                          <span class="f-12 f-w-600"><?php echo $totaltaskper; ?>%</span>
                        </div>
                        <div class="col-md-12">
                          <div class="progress progress-xs">
                            <div class="progress-bar progress-bar-success" role="progressbar" style="width:<?php echo $totaltaskper; ?>%;" aria-valuenow=" " aria-valuemin="0" aria-valuemax="100"></div>
                          </div>
                        </div>
                    </div> -->
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
                                <h2 class="text-facebook m-b-5"><i class="ti-user f-24 text-mute"></i></h2>
                                <h3 class="text-facebook f-w-700"><?php echo $dashboard_cnt['today_crm_lead_cnt']!=""?$dashboard_cnt['today_crm_lead_cnt']:0; ?></h3>
                                <p><?= __('Today\'s Leads'); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6 ">
                        <div class="card social-card">
                            <div class="card-body text-center p-0">
                                <h2 class="text-facebook m-b-5"><i class="ti-user f-24 text-mute"></i></h2>
                                <h3 class="text-facebook f-w-700"><?php echo $dashboard_cnt['today_crm_contacts_cnt']!=""?$dashboard_cnt['today_crm_contacts_cnt']:0; ?></h3>
                                <p><?= __('Today\'s Contacts'); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6">
                        <div class="card social-card">
                            <div class="card-body text-center p-0">
                                <h2 class="text-facebook m-b-5"><i class="ti-user f-24 text-mute"></i></h2>
                                <h3 class="text-facebook f-w-700"><?php echo $dashboard_cnt['today_crm_deals_cnt']!=""?$dashboard_cnt['today_crm_deals_cnt']:0; ?></h3>
                                <p><?= __('Today\'s Deals'); ?></p>
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
                            <p class="m-b-0 f-12"><?= __('Total leads'); ?></p>
                        </div>
                    </div>
                    <div class="col-2 b-l-default text-center">
                        <div class="s-caption  text-danger"><i class="ti-time f-20"></i></div>
                        <div class="s-cont d-inline-block">
                            <h5 class="f-w-700 m-b-0"><?php echo $dashboard_cnt['crm_contacts_cnt']!=""?$dashboard_cnt['crm_contacts_cnt']:0; ?></h5>
                            <p class="m-b-0 f-12"><?= __('Total Contacts'); ?></p>
                        </div>
                    </div>
                    <div class="col-2 b-l-default text-center">
                        <div class="s-caption  text-info"><i class="ti-timer f-20"></i></div>
                        <div class="s-cont d-inline-block">
                            <h5 class="f-w-700 m-b-0"><?php echo $dashboard_cnt['crm_companies_cnt']!=""?$dashboard_cnt['crm_companies_cnt']:0; ?></h5>
                            <p class="m-b-0  f-12"><?= __('Total Companies'); ?></p>
                        </div>
                    </div>
                    <div class="col-2 b-l-default text-center">
                        <div class="s-caption  text-info"><i class="ti-write f-20"></i></div>
                        <div class="s-cont d-inline-block">
                            <h5 class="f-w-700 m-b-0"><?php echo $dashboard_cnt['crm_campaigns_cnt']!=""?$dashboard_cnt['crm_campaigns_cnt']:0; ?></h5>
                            <p class="m-b-0 f-12"><?= __('Total Campaigns'); ?></p>
                        </div>
                    </div>
                    <div class="col-2 b-l-default text-center">
                        <div class="s-caption  text-success"><i class="ti-check-box f-20"></i></div>
                        <div class="s-cont d-inline-block">
                            <h5 class="f-w-700 m-b-0"><?php echo $dashboard_cnt['crm_deals_cnt']!=""?$dashboard_cnt['crm_deals_cnt']:0; ?></h5>
                            <p class="m-b-0 f-12"><?= __('Total deals'); ?></p>
                        </div>
                    </div>
                     <div class="col-2 b-l-default text-center">
                        <div class="s-caption  text-success"><i class="ti-check-box f-20"></i></div>
                        <div class="s-cont d-inline-block">
                            <h5 class="f-w-700 m-b-0"><?php echo $dashboard_cnt['projects_cnt']!=""?$dashboard_cnt['projects_cnt']:0; ?></h5>
                            <p class="m-b-0 f-12"><?= __('Total Projects'); ?></p>
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
                <a class="nav-link f-w-600 f-12 active_crm_home active active_all_class" data-bs-toggle="tab" href="#home-tab" role="tab" aria-selected="true" onclick="viewAllListPage('home')"><?= __('Home'); ?></a>
            </li>
			<li class="nav-item">
				<a class="nav-link f-w-600 f-12 active_crm_leads active_all_class" data-bs-toggle="tab" href="#lead-tab" role="tab" aria-selected="true" onclick="getCRMListDashboard('crm_leads')"><?= __('Leads'); ?><!-- <label class="form-label label label-xs label-success m-l-10"><?php //echo $dashboard_cnt['crm_lead_cnt']!=""?$dashboard_cnt['crm_lead_cnt']:0; ?></label> --></a>
			</li>
			<li class="nav-item">
				<a class="nav-link f-w-600 f-12 active_crm_contacts active_all_class" data-bs-toggle="tab" href="#lead-tab" role="tab" aria-selected="true" onclick="getCRMListDashboard('crm_contacts')"><?= __('Contacts'); ?><!-- <label class="form-label label label-xs label-success m-l-10"><?php //echo $dashboard_cnt['crm_contacts_cnt']!=""?$dashboard_cnt['crm_contacts_cnt']:0; ?></label> --></a>
			</li>
			<li class="nav-item">
				<a class="nav-link f-w-600 f-12 active_crm_companies active_all_class" data-bs-toggle="tab" href="#lead-tab" role="tab" aria-selected="true" onclick="getCRMListDashboard('crm_companies')"><?= __('Companies'); ?><!-- <label class="form-label label label-xs label-success m-l-10"><?php //echo $dashboard_cnt['crm_companies_cnt']!=""?$dashboard_cnt['crm_companies_cnt']:0; ?></label> --></a>
			</li>
			<li class="nav-item">
				<a class="nav-link f-w-600 f-12 active_crm_deals active_all_class" data-bs-toggle="tab" href="#lead-tab" role="tab" aria-selected="true" onclick="getCRMListDashboard('crm_deals')"><?= __('Deals'); ?><!-- <label class="form-label label label-xs label-success m-l-10"><?php //echo $dashboard_cnt['crm_deals_cnt']!=""?$dashboard_cnt['crm_deals_cnt']:0; ?></label> --></a>
			</li>
            <li class="nav-item">
                <a class="nav-link f-w-600 f-12 active_crm_campaigns active_all_class" data-bs-toggle="tab" href="#lead-tab" role="tab" aria-selected="true" onclick="getCRMListDashboard('crm_campaigns')"> <?= __('Campaign'); ?><!-- <label class="form-label label label-xs label-success m-l-10"><?php //echo $dashboard_cnt['crm_campaigns_cnt']!=""?$dashboard_cnt['crm_campaigns_cnt']:0; ?></label> --></a>
            </li>
            <li class="nav-item">
                <a class="nav-link f-w-600 f-12 active_crm_projects active_all_class" data-bs-toggle="tab" href="#lead-tab" role="tab" aria-selected="true" onclick="getCRMListDashboard('crm_projects')"><?= __('Projects'); ?><!-- <label class="form-label label label-xs label-success m-l-10"><?php //echo $dashboard_cnt['projects_cnt']!=""?$dashboard_cnt['projects_cnt']:0; ?></label> --></a>
            </li>
            <li class="nav-item">
                <a class="nav-link f-w-600 f-12 active_crm_feasibilities active_all_class" data-bs-toggle="tab" href="#lead-tab" role="tab" aria-selected="true" onclick="getCRMListDashboard('crm_feasibilities')"><?= __('Feasibility'); ?></a>
            </li>

		</ul>
    </div>
   <div class="col-lg-3">
        <div class="row mb-2" style="display: none" id="custome_date">
            <div  class="col-lg-5">
                <span  class="form-label d-inline-block fw-bold"><?= __('Start Date'); ?></span>
                <input aria-label="<?= htmlspecialchars(__('From date'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control filterdynamic datepicker" name="from_date" id="from_date" value="<?php echo $from_date; ?>">
            </div>
            <div class="col-lg-5">
                <span  class="form-label d-inline-block fw-bold"><?= __('End Date'); ?></span>
                <input aria-label="<?= htmlspecialchars(__('To date'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control filterdynamic datepicker" name="to_date" id="to_date" value="<?php echo $to_date; ?>">
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
                    <span id="btn_date"><?= __('This Year'); ?></span>
                </button>
                <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split waves-effect waves-light" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 20%;height: 40px;">
                    <span class="visually-hidden"><?= __('Toggle primary'); ?></span>
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="TD"?"active":"";?> empty_active" id="TD" href="#!" onclick="dynamicSelectDateRange('TD',<?php echo htmlspecialchars(json_encode(__('Today'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8'); ?>,'<?php echo fromDate(date("Y-m-d")); ?>','<?php echo fromDate(date("Y-m-d")); ?>')"><?= __('Today'); ?></a>
                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="TW"?"active":"";?> empty_active" id="TW" href="#!" onclick="dynamicSelectDateRange('TW',<?php echo htmlspecialchars(json_encode(__('This Week'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8'); ?>,'<?php echo fromDate(date("Y-m-d", strtotime('monday this week')));?>','<?php echo fromDate(date("Y-m-d", strtotime('monday this sunday ')));?>')"><?= __('This Week'); ?></a>
                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="TM"?"active":"";?> empty_active" id="TM" href="#!" onclick="dynamicSelectDateRange('TM',<?php echo htmlspecialchars(json_encode(__('This Month'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8'); ?>,'<?php echo fromDate(date("Y-m-01")); ?>','<?php echo fromDate(date("Y-m-t")); ?>')"><?= __('This Month'); ?></a>
                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="TY"?"active":"";?> empty_active" id="TY" href="#!" onclick="dynamicSelectDateRange('TY',<?php echo htmlspecialchars(json_encode(__('This Year'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8'); ?>,'<?php echo fromDate(date("Y-01-01")); ?>','<?php echo fromDate(date("Y-12-31")); ?>')"><?= __('This Year'); ?></a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="YD"?"active":"";?> empty_active" id="YD" href="#!" onclick="dynamicSelectDateRange('YD',<?php echo htmlspecialchars(json_encode(__('Yesterday'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8'); ?>,'<?php echo fromDate(date('Y-m-d',strtotime("-1 days"))); ?>','<?php echo fromDate(date('Y-m-d',strtotime("-1 days"))); ?>')"><?= __('Yesterday'); ?></a>
                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="PW"?"active":"";?> empty_active" id="PW" href="#!" onclick="dynamicSelectDateRange('PW',<?php echo htmlspecialchars(json_encode(__('Previous Week'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8'); ?>,'<?php echo fromDate(date('Y-m-d', strtotime("previous week monday"))); ?>','<?php echo fromDate(date('Y-m-d', strtotime("previous week sunday"))); ?>')"><?= __('Previous Week'); ?></a>
                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="PM"?"active":"";?> empty_active" id="PM" href="#!" onclick="dynamicSelectDateRange('PM',<?php echo htmlspecialchars(json_encode(__('Previous Month'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8'); ?>,'<?php echo fromDate(date("Y-m-01",strtotime('last month'))); ?>','<?php echo fromDate(date("Y-m-t",strtotime('last month'))); ?>')"><?= __('Previous Month'); ?></a>
                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="PY"?"active":"";?> empty_active" id="PY" href="#!" onclick="dynamicSelectDateRange('PY',<?php echo htmlspecialchars(json_encode(__('Previous Year'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8'); ?>,'<?php echo fromDate(date("Y-01-01",strtotime('last year'))); ?>','<?php echo fromDate(date("Y-12-31",strtotime('last year'))); ?>')"><?= __('Previous Year'); ?></a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="CD"?"active":""; ?> empty_active" id="CD" href="#!" onclick="dynamicSelectDateRange('CD',<?php echo htmlspecialchars(json_encode(__('Custom Date'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8'); ?>)"><?= __('Custom Date'); ?></a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 text-right">
        <?php if($_SESSION['admin_type'] == 1){ ?>
        <a href="index.php?option=COM_VIEW_CRM_LEAD_AND_CONTACT_ASSIGNINGS" class="btn btn-success mb-2 desktop-view"> <i class="icofont icofont-plus icon-white "></i> <?= __('Lead/Contact Assignings'); ?></a>
    <?php } ?>
    </div>
</div>
		<div class="tab-content tabs">
            <div class="tab-pane card-body p-0 active active_crm_home active_all_class" id="home-tab" role="tabpanel">
                <?php include "views/custom_view_crm_home_dashboards.php"; ?>
            </div>
			<div class="tab-pane card-body p-0 dynamictabviews active_all_class" id="lead-tab" role="tabpanel" >
                 <?php //include("views/custom_list_crm_leads.php"); ?>

        	</div>
	<!--
			<div class="tab-pane card-body p-0" id="contact-tab" role="tabpanel">

			</div>
			<div class="tab-pane card-body p-0" id="company-tab" role="tabpanel">

			</div>
			<div class="tab-pane card-body p-0" id="deal-tab" role="tabpanel">

			</div>
            <div class="tab-pane card-body p-0" id="campaigns-tab" role="tabpanel">

            </div>
            <div class="tab-pane card-body p-0" id="project-tab" role="tabpanel">

            </div> --></div>



<?php /** ?>
    <div class="col-lg-3 text-right">
		<button type="button" class="btn btn-primary waves-effect waves-light m-r-10" ><i class="ti-list"></i> <?= __('List'); ?></button>
		<button type="button" class="btn btn-primary waves-effect waves-light" ><i class="ti-layout-column4-alt"></i> <?= __('Kanban board'); ?></button>

		<div class="card">
			<div class="card-header">
				<h5><?= __('Task'); ?></h5>
			</div>
			<div class="card-body">
				<table class="table table-hover m-b-0" style="font-size:13px;" >
					<thead>
						<tr><th><?= __('Lead Code'); ?></th>
							<th><?= __('Activity'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($today_crm_lead_details as $crmld){
							$meg = "";
		                    if($crmld['lead_type'] == '2' || $crmld['lead_type'] == '3'){
		                        $meg .= "<span class='d-block'>".$crmld['date']." ".$crmld['time']."</span>".$crmld['comment'];
		                    }elseif($crmld['lead_type'] == '4'){
		                        $meg = "<span class='d-block'>".$crmld['phone']." ".$crmld['time']."</span>".$crmld['sms_text'];
		                    }elseif($crmld['lead_type'] == '5'){
		                        $meg .= "<span class='d-block'>".$crmld['recipient_email_id']."</span>".$crmld['sms_text'];
		                    }elseif($crmld['lead_type'] == '7'){ //meeting
		                        $member = json_decode($crmld['member'],1);
		                            //($member);
		                            $membermsg = "";
		                            foreach($member as $members){
		                                $membermsg .= "<span class='m-r-5'>".$_SESSION['users'][$members].",</span>";
		                            }
		                        $meg .= "<span class='d-block'>".$crmld['date']." ".$crmld['time']."</span>".$membermsg." <span class='d-block'>".$crmld['comment']."</span>";
		                    }elseif($crmld['lead_type'] == '8'){ //visit
		                         $meg .= "<span class='d-block'>".$crmld['date']." ".$crmld['time']."</span>";
		                         $meg .= "<span class='d-block'>".$crmld['address']." ".$crmld['post_code']."</span>";
		                         $meg .=$crmld['comment'];
		                    }else{
		                         $meg .= $crmld['comment'] !=""?$crmld['comment']:"-";
		                    }
							?>
							<tr>
								<td><?php echo $leadcode[$crmld['crm_lead_id']]; ?></td>
								<td class="w-b-w-s"><?php echo $meg; ?></td>
							</tr>
					<?php } ?>
					</tbody>
				</table>

			</div>
		</div>
	</div>
    <?php **/ ?>

<!---------Add Lead Form------------->
<div id="sidebar111" class="users p-chat-user leadsidebar-form text-left" style="display:none">
    <div class="had-container">
        <div class="p-fixed users-main " id="htmlsetpage" >

        </div>
    </div>
</div>
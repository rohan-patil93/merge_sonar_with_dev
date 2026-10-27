<?php
$application_menus = $_SESSION['application_menu_TNL'];
$listpages = array();
foreach($application_menus as $applicationmenu2){
    foreach ($applicationmenu2['child'] as $applicationmenu){
        if($applicationmenu['dashboard'] == 0){
            $optiondetails = getOptionPageTableName($applicationmenu['Page_Name']);
            if($optiondetails['application_table_name'] !=""){
                $listpages[] =$optiondetails['application_table_name'];
            }
            $applicationmenu['application_table_name'] = $optiondetails['application_table_name'];
            $application_menu[] = $applicationmenu;
        }
    }
}

$pageencode = base64_encode(json_encode($listpages));
$url = API."?mode=getdynamicmoduledashboard&transaction_table=".$pageencode."&tablerows=Y&admin_type=".$_SESSION['admin_type']."&admin_access_employee=".$_SESSION['admin_access_employee'];
$apires = callAPI($url);
$result = json_decode($apires,1);
$transactioncnt = $result['transaction_table'];
$pendding_action = $result['pendding_action'];
?>
<script type="text/javascript" src="../assets/js/custom_items_summary.js"></script>
<script type="text/javascript">

//             url: "views/custom_list_view_all_dash_board.php",
//             data:'TYPE='+type,
//             type: "POST",

//         url: "views/custom_view_recruitingmanager_dashboard_popup.php",
//         data:'job_posting_id='+job_posting_id+'&field_name='+field_name+'&value='+value,
//         type: "POST",
//     swal({
//             title: "Submission Successful",
//             text: "ok",
//             type: "success",
//             showCancelButton: false,
//             closeOnConfirm: true,
//             showLoaderOnConfirm: true
//     swal({
//             title: "Submission Successful",
//             text: "Your will not be able to recover this imaginary file!",
//             type: "success",
//             showCancelButton: true,
//             confirmButtonClass: "btn-danger",
//             confirmButtonText: "Ok",
//             closeOnConfirm: false

function selectMenuOption(menu_label,option,seq){
    $(".spanicon").html('');
    $("#dynamic_label").text(menu_label+" Invices");
    $("#spanicon_"+seq).html('<i class="fa fa-check-circle text-warning f-16"></i>');
    var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
    $("#htmlview").html(loader);
    jQuery.ajax({
        url: "views/custom_view_dynamic_module_dashboard_ajax.php",
        data:'menu_label='+menu_label+'&option='+option+'&ajaxcall=Y',
        type: "POST",
        success:function(data){
            $("#htmlview").html(data);
        },
        error:function (){}
    });
}
function getTransactionInvoiceList(){
    var option =$("#transaction_option_name").val();
    var loader = '<div class="text-center" id="loader-show_2"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
    $("#htmlview").html(loader);
    jQuery.ajax({
        url: "views/custom_list_dynamic_transaction_invoices.php",
        data:'option='+option,
        type: "POST",
        success:function(data){
            $("#htmlview").html(data);
        },
        error:function (){}
    });
}

</script>

<ul class="dynamic_card_menu">
    <?php
    $cntmenucard = count((array)$application_menu);
     $dynamicoptionary  = ""; $mmk=1; $mmj=1; $cntmenu = 0;
     foreach($application_menu as $applicationmenu){

         if($applicationmenu['dashboard'] == 0){ $cntmenu++;
        $iconimg = "";

        if($mmk == 1){

            $dynamicoptionary = $applicationmenu;
            $iconimg = '<i class="fa fa-check-circle text-warning f-16"></i>';

        }   ?>

        <li>
        <a href="#!" onclick="selectMenuOption('<?php echo $applicationmenu['Label']; ?>','<?php echo $applicationmenu['Page_Name']; ?>','<?php echo $mmk; ?>')" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
            <div class="card ticket-card">
                <div class="card-body text-center p-2">

                    <p class="m-b-30 bg-c-blue lbl-card custom-limit-text" style="--linehide: 1;">
                    <?php echo $applicationmenu['Label']; ?> &nbsp;&nbsp; <span class="spanicon" id="spanicon_<?php echo $mmk; ?>"><?php echo $iconimg; ?></span></p>
                    <?php if($pendding_action[$applicationmenu['application_table_name']] !=0){?>
                    <div class="show-num bg-danger"><i class="fa fa-envelope"></i> <?php echo $pendding_action[$applicationmenu['application_table_name']]; ?></div>
                    <?php } ?>
                    <div class="text-center">
                        <h2 class="m-b-0 d-inline-block text-c-red"><?php echo customNanAndEmptyValidation($transactioncnt[$applicationmenu['application_table_name']]); ?></h2>
                    </div>

                </div>
            </div>
        </a>
    </li>
<?php  $mmk++; $mmj++; if($mmj == 8){ ?></ul><ul class="dynamic_card_menu"><?php $mmj=0;
}
}
}  ?>

</ul>

<button type="button" class="artificial_intelligence_btn bg-primary border-0"
    onclick="handleArtificialIntelligenceClick()"
    data-bs-toggle="tooltip"
    data-bs-placement="bottom"
    title="Dashboard Summary With AI">
    <i class="ti-info spin-infinite"></i>
</button>

<?php if($cntmenu == 0){ ?>
    <div class="card-px text-center ">
        <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Menu Setup</p>
        <div class="text-center px-4">
            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
        </div>
    </div>
<?php }else{ ?>
<div id="htmlview">
    <?php
    $_POST['option'] = $dynamicoptionary['Page_Name'];
    $_POST['menu_label'] = $dynamicoptionary['Label'];
    include_once "views/custom_view_dynamic_module_dashboard_ajax.php"; ?>
</div>
<?php } ?>

<?php
$menuarray = array("quotations"=>array("title"=>"Quotations","option"=>"COM_LIST_QUOTATIONS"),"purchase_orders"=>array("title"=>"Purchase Orders","option"=>"COM_LIST_PURCHASE_ORDERS"),"purchase_invoices"=>array("title"=>"Purchase Invoices","option"=>"COM_LIST_PURCHASE_INVOICES"));
foreach ($menuarray as $trantable=>$option){
    $optiondetails = getOptionPageTableName($option['option']);
    if($optiondetails['application_table_name'] !=""){
        $listpages[] =$optiondetails['application_table_name'];
    }
}
$pageencode = base64_encode(json_encode($listpages));
$vendordashboardurl = API."?mode=getvendordashboard&transaction_table=".$pageencode."&admin_type=".$_SESSION['admin_type'].".&admin_access_employee=".$_SESSION['admin_access_employee']."&vend_limit=3&vendor_id=".$_SESSION['vendor_id'];
 $vendordashboardres = callAPI($vendordashboardurl);
 $vendordashboardd = json_decode($vendordashboardres,1);
 $transactioncnt = $vendordashboardd['transaction_table'];
 $vendors = $vendordashboardd['vendors'];

 ?>
 <script type="text/javascript">
     function selectMenuOption(menu_label,option,seq){
        $(".spanicon").html('');
        $("#dynamic_label").text(menu_label+" Invices");
        $("#spanicon_"+seq).html('<i class="fa fa-check-circle text-warning f-16"></i>');
        var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $("#htmlview").html(loader);
        jQuery.ajax({
            url: "views/custom_view_dashboard_vendors_ajax.php",
            data:'menu_label='+menu_label+'&option='+option,
            type: "POST",
            success:function(data){
                $("#htmlview").html(data);
                console.log(data);
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
        data:'option='+option+"&fieldaccess=party_type",
        type: "POST",
        success:function(data){
            $("#htmlview").html(data);
            console.log(data);
        },
        error:function (){}
    });
}
 </script>
<ul class="dynamic_card_menu">
    <?php $mmk=1; foreach($menuarray as $tabl=>$menuarray1){
        $iconimg ="";
        if($mmk == 1){
            $dynamicoptionary  = $menuarray1;
            $iconimg = '<i class="fa fa-check-circle text-warning f-16"></i>';
        }
        ?>
    <li>
        <a href="#!" onclick="selectMenuOption('<?php echo $menuarray1['title']; ?>','<?php echo $menuarray1['option']; ?>','<?php echo $mmk; ?>')" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
            <div class="card ticket-card">
                <div class="card-body text-center p-2">
                    <p class="m-b-30 bg-c-blue lbl-card p-2 text-white">
                    <?php echo $menuarray1['title']; ?> &nbsp;&nbsp; <span class="spanicon" id="spanicon_<?php echo $mmk; ?>"><?php echo $iconimg; ?></span></p>
                    <div class="text-center">
                        <h2 class="m-b-0 d-inline-block text-c-red"><?php echo customNanAndEmptyValidation($transactioncnt[$tabl]['cnt']); ?></h2>
                    </div>

                </div>
            </div>
        </a>
    </li>
<?php $mmk++;
} ?>
</ul>
<div id="htmlview">
    <?php
    $_POST['option'] = $dynamicoptionary['option'];
    $_POST['menu_label'] = $dynamicoptionary['title'];
    include_once "views/custom_view_dashboard_vendors_ajax.php"; ?>
</div>

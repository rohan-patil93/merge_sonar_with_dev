 <?php
 $_SESSION['apply_js']['amchart'] =1;
 $admindashboardurl = API."?mode=getadmindashboard&vend_limit=3&admin_type=".$_SESSION['admin_type']."&admin_access_employee=".$_SESSION['admin_access_employee'];
 $admindashboardres = callAPI($admindashboardurl);
 $admindashboardd = json_decode($admindashboardres,1);
 $vendors = $admindashboardd['vendors'];
 $approvedinvoices = $unapprovedinvoices = array();
 foreach($admindashboardd['purchase_invoice_list'] as $pending_list){
    if($pending_list['current_status']==ST_APPROVED){
        $approvedinvoices[] = $pending_list;
    }else{
        $unapprovedinvoices[] = $pending_list;
    }
}
$transaction_approval_logs = $admindashboardd['transaction_approval_logs'];
foreach($transaction_approval_logs as $tal){
  if($tal['response_type'] == 0){
      $transactionapprovallogs['pending'][] = $tal;
  }else{
      $transactionapprovallogs['approved'][] = $tal;
  }
}
$vendorinvoicecnt = $admindashboardd['vendor_invoice_cnt'];
$all_vendors = $admindashboardd['all_vendors'];

//customer
 $customers = $admindashboardd['customers'];
 $approvedinvoices = $unapprovedinvoices = array();
 foreach($admindashboardd['sales_invoices_list'] as $pending_list){
    if($pending_list['current_status']==ST_APPROVED){
        $approvedinvoices[] = $pending_list;
    }else{
        $unapprovedinvoices[] = $pending_list;
    }
}
$transaction_approval_logs_customer = $admindashboardd['transaction_approval_logs_customer'];
foreach($transaction_approval_logs_customer as $tal){
  if($tal['response_type'] == 0){
      $transactionapprovallogscustomer['pending'][] = $tal;
  }else{
      $transactionapprovallogscustomer['approved'][] = $tal;
  }
}
$customerinvoicecnt = $admindashboardd['customer_invoice_cnt'];
$all_customers = $admindashboardd['all_customers'];
//customer

?>
<script>

  function billChart(){
    var reimbursementsyear = $("#billchartyear").val();
    jQuery.ajax({
        url: "ajax.php",
        data:'page=getbillsstackdashboardchart&reimbursementsyear='+reimbursementsyear+'&transaction_table=supplier_bills',
        type: "POST",
        success:function(resp){
               var res =  JSON.parse(resp);
    var chart = AmCharts.makeChart("deal-analytic-bills-chart", {
        "type": "serial",
        "theme": "light",
        "dataDateFormat": "YYYY-MM-DD",
        "precision": 2,
        "valueAxes": [{
            "id": "v1",
            "position": "left",
            "autoGridCount": false,
            "labelFunction": function(value) {
                return "$" + Math.round(value) + "M";
            }
        }, {
            "id": "v2",
            "gridAlpha": 0,
            "autoGridCount": false
        }],
        "graphs": [{
            "id": "g1",
            "valueAxis": "v2",
            "bullet": "round",
            "bulletBorderAlpha": 1,
            "bulletColor": "#FFFFFF",
            "bulletSize": 8,
            "hideBulletsCount": 50,
            "lineThickness": 3,
            "lineColor": "#2ed8b6",
            "title": "Bills",
            "useLineColorForBulletBorder": true,
            "valueField": "market1",
            "balloonText": "[[title]]<br /><b style='font-size: 130%'>[[value]]</b>"
        }, {
            "id": "g2",
            "valueAxis": "v2",
            "bullet": "round",
            "bulletBorderAlpha": 1,
            "bulletColor": "#FFFFFF",
            "bulletSize": 8,
            "hideBulletsCount": 50,
            "lineThickness": 3,
            "lineColor": "#e95753",
            "title": "Paid",
            "useLineColorForBulletBorder": true,
            "valueField": "market2",
            "balloonText": "[[title]]<br /><b style='font-size: 130%'>[[value]]</b>"
        }],
        "chartCursor": {
            "pan": true,
            "valueLineEnabled": true,
            "valueLineBalloonEnabled": true,
            "cursorAlpha": 0,
            "valueLineAlpha": 0.2
        },
        "categoryField": "date",
        "categoryAxis": {
            "parseDates": true,
            "dashLength": 1,
            "minorGridEnabled": true
        },
        "legend": {
            "useGraphSettings": true,
            "position": "top"
        },
        "balloon": {
            "borderThickness": 1,
            "shadowAlpha": 0
        },
        "dataProvider": res
    });
        },
        error:function (){}
    });
}

function salesChart(){
     var reimbursementsyear = $("#saleschartyear").val();
    console.log(reimbursementsyear);
    jQuery.ajax({
        url: "ajax.php",
        data:'page=getbillsstackdashboardchart&reimbursementsyear='+reimbursementsyear+'&transaction_table=sales_invoices',
        type: "POST",
        success:function(resp){
             floatchart()
    $(window).on('resize', function() {
        floatchart();
    });console.log(resp);
               var res =  JSON.parse(resp);

    var chart = AmCharts.makeChart("deal-analytic-saless-chart", {
        "type": "serial",
        "theme": "light",
        "dataDateFormat": "YYYY-MM-DD",
        "precision": 2,
        "valueAxes": [{
            "id": "v1",
            "position": "left",
            "autoGridCount": false,
            "labelFunction": function(value) {
                return "$" + Math.round(value) + "M";
            }
        }, {
            "id": "v2",
            "gridAlpha": 0,
            "autoGridCount": false
        }],
        "graphs": [{
            "id": "g1",
            "valueAxis": "v2",
            "bullet": "round",
            "bulletBorderAlpha": 1,
            "bulletColor": "#FFFFFF",
            "bulletSize": 8,
            "hideBulletsCount": 50,
            "lineThickness": 3,
            "lineColor": "#2ed8b6",
            "title": "Bills",
            "useLineColorForBulletBorder": true,
            "valueField": "market1",
            "balloonText": "[[title]]<br /><b style='font-size: 130%'>[[value]]</b>"
        }, {
            "id": "g2",
            "valueAxis": "v2",
            "bullet": "round",
            "bulletBorderAlpha": 1,
            "bulletColor": "#FFFFFF",
            "bulletSize": 8,
            "hideBulletsCount": 50,
            "lineThickness": 3,
            "lineColor": "#e95753",
            "title": "Paid",
            "useLineColorForBulletBorder": true,
            "valueField": "market2",
            "balloonText": "[[title]]<br /><b style='font-size: 130%'>[[value]]</b>"
        }],
        "chartCursor": {
            "pan": true,
            "valueLineEnabled": true,
            "valueLineBalloonEnabled": true,
            "cursorAlpha": 0,
            "valueLineAlpha": 0.2
        },
        "categoryField": "date",
        "categoryAxis": {
            "parseDates": true,
            "dashLength": 1,
            "minorGridEnabled": true
        },
        "legend": {
            "useGraphSettings": true,
            "position": "top"
        },
        "balloon": {
            "borderThickness": 1,
            "shadowAlpha": 0
        },
        "dataProvider": res
    });
        },
        error:function (){}
    });
}

</script>
<ul class="nav md-tabs infisuite_tab mb-2">
    <li class="nav-item">
        <a class="nav-link f-w-600 f-12 active" data-bs-toggle="tab" href="#vendarbills" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Vendar Bills</a>
    </li>
    <li class="nav-item">
        <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#customerbills" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Customer Bills </a>
    </li>
</ul>
<div class="tab-content tabs" >
    <div class="tab-pane card-body active p-0" id="vendarbills" role="tabpanel">
        <?php include_once "custom_view_billstack_admin_dashboard_vendors.php"; ?>
    </div>
    <div class="tab-pane card-body p-0" id="customerbills" role="tabpanel">
        <?php include_once "custom_view_billstack_admin_dashboard_customers.php"; ?>
    </div>
</div>

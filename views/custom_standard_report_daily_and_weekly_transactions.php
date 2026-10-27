<?php
$trnsaction_cnt_array = array('requisitions','quotations','purchase_orders','goods_receipt_notes','grn_returns','purchase_invoices','supplier_bills','returnable_gate_passes','non_returnable_gate_passes','enquiries','estimates','sales_orders','delivery_orders','sales_returns','sales_invoices');
$transtable = base64_encode(json_encode($trnsaction_cnt_array));
$shortcode = $_REQUEST['shortcode_date'] !=""?$_REQUEST['shortcode_date']:"TM";
$from_date = fromDate($_REQUEST['from_date'] !=""?$_REQUEST['from_date']:date("Y-m-01"));
$to_date =fromDate($_REQUEST['to_date'] !=""?$_REQUEST['to_date']:date("Y-m-t"));
$url = API."?mode=getdailyandweeklytransactions&transaction_tables=".$transtable."&from_date=".$from_date."&to_date=".$to_date;
$che = callAPI($url);
$return = json_decode($che, 1);
$transaction = $return['transaction'];
$_SESSION['download_data'] = array();
?>

<div class="card">
    <div class="card-header">
        <h5>Daily Transactions Count Reports</h5>
        <div class="card-header-right">

            <div class="dropdown-primary dropdown open">
          <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
          <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut" style="">

            <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=Transaction Count Report" target="_blank" >CSV</a>
            <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=Transaction Count Report" target="_blank">Excel</a>
          </div>
        </div>
        </div>
    </div>
    <div class="card-body">
        <form id="reportFilterForm" action="" method="post">
            <div class="form-group row">
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-4">
                            <span  class="form-label">Date Range</span>
                            <input type="hidden" name="pick_date" id="pick_date" value="This Year">
                            <input type="hidden" name="shortcode_date" id="shortcode_date" value="TY">
                            <div class="btn-group dropdown-split-primary" style="width: 100%;">
                                <button type="button" class="btn btn-primary" style="width: 80%;height: 40px;"><i class="ti-calendar"></i>
                                    <span id="btn_date">This Month</span>
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
                        <div class="col-lg-6" style="display: none" id="custome_date">
                            <div class="row" >
                                <div  class="col-lg-6">
                                    <span  class="form-label">Start Date</span>
                                    <input aria-label="From Date" type="text" class="form-control filterdynamic datepicker" name="from_date" id="from_date" value="<?php echo $from_date; ?>">
                                </div>
                                <div class="col-lg-6">
                                    <span  class="form-label">End Date</span>
                                    <input aria-label="To Date" type="text" class="form-control filterdynamic datepicker" name="to_date" id="to_date" value="<?php echo $to_date; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <span class="form-label d-block">&nbsp</span>
                            <button class="btn btn-primary form-control" onclick="filterSubmitForm()" type="submit" >
                                <i class="ti-search m-r-2"></i>View
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="card-title">
            <div class="row">
                <div class="col-lg-3">
                <div class="col bg-light-primary ">
                    <span class="svg-icon svg-icon-3x svg-icon-primary d-block my-2">
                        <img class="img-fluid img-120" src="uploads/all/<?php echo $_SESSION['config']['display_logo']; ?>" alt="Theme-Logo" />
                    </span>
                    <span class="text-dark-75 font-weight-bolder font-size-h3"><?php echo $company_name; ?></span>
                </div>
            </div>
            <div class="col-lg-6">
                <h4 class="card-label text-center f-w-700">Daily Transactions Count </br><small class="f-10">Name : <?php echo $_SESSION['display_name']; ?> &nbsp;  Date & Time : <?php echo date("d-m-Y H:i:s"); ?></small></h4>

            </div>
        </div>


        <div class="table-responsive mt-4" id="report_results">
            <table class="table table-hover">
                <thead>
                    <tr class="f-14" style="background-color: #eeeeee;">
                        <th id="date_week_header">Date</th>
                        <?php foreach($trnsaction_cnt_array as $transtb){ ?>
                        <th scope="col"><?php echo ucwords(str_replace('_',' ',$transtb)); ?></th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody id="report_data">
                    <?php


                        $startDate = new DateTime($from_date);
                        $endDate = new DateTime($to_date);
                        while($startDate <= $endDate ){
                            $csvary = array();
                            $trandateamp = $startDate->format('d-m-Y');
                            $yearmontdate = $startDate->format('Ymd');
                            $csvary['Date'] =$trandateamp;
                        ?>
                        <tr class="date-row">
                            <td class="p-2"><?php echo fromDate($trandateamp); ?></td>
                            <?php foreach($trnsaction_cnt_array as $transtb){
                                $trantable = $transaction[$transtb][$yearmontdate];
                                $csvary[$transtb] = $trantable['cnt']!=""?$trantable['cnt']:0;

                                ?>
                                <td class="p-2"><?php echo $trantable['cnt']!=""?"<span class='text-primary f-w-700'>".$trantable['cnt']."</span>":0; ?></td>
                            <?php }  ?>
                        </tr>
                        <?php  $startDate->modify('+1 day');  $cssdata[] = $csvary;
}
                        $_SESSION['download_data'] = $cssdata; ?>
                </tbody>
            </table>
        </div>

        <!-- No Data Message -->
        <div class="card-px text-center py-20 my-10 d-none" id="no_data_message">
            <p class="text-muted f-16 fw-bold mb-10"><i class="ti-alert text-warning"></i> No Data Available</p>
            <div class="text-center px-4">
                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
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
    }
</script>

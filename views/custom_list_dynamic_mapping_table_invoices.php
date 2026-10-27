<?php
$page_details= toGetTableNameBaseApplicationPagesList('list_page_name', $_GET['option']);
$menu_details= toGetMenuDetailsBaseMenuPageName($_GET['option']);
$title =  $menu_details['menu_label']!= '' ?  $menu_details['menu_label'] :  viewTitle($_GET['option']);
$insert_page_name = $page_details['insert_page_name'];
$transaction_table =$page_details['application_table_name'];
$insert = $page_details['insert_page_name'];
$link = "index.php?option=".$insert;

$from_date = fromDate($_REQUEST['from_date'] !=""?$_REQUEST['from_date']:date("Y-m-01"));
$to_date =fromDate($_REQUEST['to_date'] !=""?$_REQUEST['to_date']:date("Y-m-t"));
$apiurl = API."?mode=getdynamicmappingtableinvoices&transaction_table=".$transaction_table."&from_date=".$from_date."&to_date=".$to_date;
$apires = callAPI($apiurl);
$result = json_decode($apires, 1);
$rental_invoices = $result['transactions'];
$credit_notes_sum = $result['credit_notes_sum'];
$debit_notes_sum = $result['debit_notes_sum'];
$salesmen = $result['salesmen'];
$stausresult = ViewStatusData();
$_SESSION['download_data'] = array();
?>
<script type="text/javascript">
    function goToAnotherPageAndPrint(inv_id,transaction_table){
       $.ajax({
            url: "views/custom_qo_print.php",
            method: "GET",
            async: false,
            data:'rid='+inv_id+'&print=Y&table='+transaction_table,
            success: function (data) {
                var popup = window.open("", "_blank", "width=600,height=400");
                popup.document.write(data);
                popup.document.close();
                popup.print();
            },
            error: function () {
                alert("Error fetching data");
            }
        });
    }
    function DownloadPDF(inv_id,transaction_table){
        $.ajax({
            url: "views/custom_qo_print.php",
            method: "GET",
            data:'rid='+inv_id+'&print=Y&table='+transaction_table,
            success: function (htmlContent){
                $.ajax({
                    url: 'convert_html_to_pdf.php',
                    type: 'POST',
                    async: false,
                    data: { html_content: htmlContent,title:transaction_table },
                    success: function(pdfdetail) {
                      var result = JSON.parse(pdfdetail);
                        var link = document.createElement('a');
                          link.href = result.pdf_url;
                          link.download =result.pdf_name;
                          link.style.display = 'none';
                          document.body.appendChild(link);
                          link.click();
                          document.body.removeChild(link);
                          $.ajax({
                        url: 'convert_html_to_pdf.php',
                        type: 'POST',
                        data: 'removepdf='+result.pdf_name,
                        success: function(res) {
                          console.log(res);
                        }
                    });
                    }
                });
            },
            error: function () {
                alert("Error fetching data");
            }
        });
    }

     function renatalInvoiceSend(transaction_id,transaction_table){
      jQuery.ajax({
        url: "ajax.php",
        data:'page=emailinvoicesend&transaction_id='+transaction_id+'&transaction_table='+transaction_table+'&template_reference_id=33',
        type: "POST",
        success:function(res){
          var data = JSON.parse(res);
            swal("<?php echo ucwords($_SESSION['config']['name']); ?>",'<?php echo $_SESSION['error_message']['9168']; ?>', "success");
        },
        error:function (){}
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
    $('#dateselection_form').submit();
  }

</script>
<div class="card">
    <div class="card-header">
        <h5>  <?php echo $title;  ?></h5>
        <div class="card-header-right table-card-header">
           <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
              <i class="icofont icofont-reply"></i>
              Back
            </a>
        </div>
      </div>
      <div class="card-body m-t-20">

          <div class="row mb-3">
            <div class="col-lg-1"></div>
            <div class="col-lg-3">
              <div class="input-group input-group-button mb-0">
                  <input type="text" id="dynamicfilterhideshow" data-id="categoryfilter" data-type="div" class="form-control form-control-sm width1" placeholder="Search" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Search" aria-label="Search">
                  <span class="input-group-text addon-style">
                      <span class=""><span class="ti-search text-white mt-2"></span></span>
                  </span>
              </div>
            </div>
            <div class="col-lg-3">
              <form method="post" id="dateselection_form">
              <div class="row">
                 <div class="col-lg-12">
                  <input type="hidden" name="pick_date" id="pick_date" value="This Month">
                  <input type="hidden" name="shortcode_date" id="shortcode_date" value="TM">
                  <div class="btn-group dropdown-split-primary" style="width: 100%;">
                      <button type="button" class="btn btn-primary" style="width: 80%;height: 40px;"><i class="ti-calendar"></i>
                        <span id="btn_date"><?php echo $_POST['pick_date']!=""?$_POST['pick_date']:"This Month"; ?></span>
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
                <div class="col-lg-12" style="display: none" id="custome_date">
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
              </div>
            </form>
            </div>

            <div class="dropdown-primary dropdown open col-lg-1">
              <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
              <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut" style="">

                <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=<?php echo $title;  ?>" target="_blank" >CSV</a>
                <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=<?php echo $title;  ?>" target="_blank">Excel</a>
              </div>
            </div>

            <div class="col-lg-3 text-right">
              <a href="index.php?option=COM_STANDARD_REPORT_DOWNLOAD_PDF_PURCHASE_INVOICES&TB=<?php echo $transaction_table; ?>" class="btn btn-primary mb-2 desktop-view" > <i class="ti-pie-chart"></i> Reports</a>
             <a href="<?php echo $link; ?>"  class="btn btn-success mb-2 desktop-view" >
              <i class="icofont icofont-plus icon-white " ></i>
              <?php echo $title;  ?>
            </a>
          </div>
          </div>


          <div class="row">
            <?php if(count((array)$rental_invoices) !=0){
              $mk =1;
              foreach($rental_invoices as $ri){
                $cssdata[] = array('Sl No'=> $mk,'Transaction Number'=> $ri['transaction_number'], 'Transaction Date'=> fromDate($ri['transaction_date']), 'Party Name'=> $ri['party_name'],
                  'Salesman Name'=> $ri['salesmen_id']!=""?$salesmen[$ri['salesmen_id']]['salesman_name']:"-",
                  'amount'=> $ri['amount'], 'Tax'=>$ri['tax_amount'],'Discount'=>$ri['discount_amount'],'Total Amount'=>$ri['total_amount'],'Debit Note'=>$debit_notes_sum[$ri['id']],'Credit Note'=>$credit_notes_sum[$ri['id']],'Paid Amount'=>$ri['total_amount']-$ri['pending_amount'],'Pending Amount'=>$ri['pending_amount']);
              ?>
              <div class="col-lg-3" id="categoryfilter_2">
                  <div class="card">
                      <div class="card-header table-card-header">
                          <h5 class="">Invoices

                            <span class="form-label label label-primary" style="color: <?php echo $stausresult[$ri['current_status']]['color']; ?>;    background:  <?php echo $stausresult[$ri['current_status']]['background_color']; ?>; border-color: <?php echo $stausresult[$ri['current_status']]['background_color']; ?>;"><?php echo $stausresult[$ri['current_status']]['name']; ?></span>
                          </h5>
                          <div class="card-header-right">
                            <i class="ti-printer" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Print" onclick="goToAnotherPageAndPrint('<?php echo $ri['id']; ?>','<?php echo $transaction_table; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>

                             <i class="ti-cloud-down" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Download" onclick="DownloadPDF('<?php echo $ri['id']; ?>','<?php echo $transaction_table; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>


                           <?php if($ri['party_email'] !=""){  ?>
                              <i class="ti-email" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Send Email" onclick="renatalInvoiceSend('<?php echo $ri['id']; ?>','<?php echo $transaction_table; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>
                            <?php } ?>
                          </div>
                      </div>
                      <div class="table-responsive  card-body">
                          <table class="table table-hover m-b-0" style="font-size:13px;">
                            <tbody>
                                  <tr>
                                    <th scope="col" class="p-2">Number</th>
                                    <td class="text-right p-2 "> <a href="index?option=<?php echo $insert; ?>&Rid=<?php echo deocdeId("id:".$ri['id']);?>" class="text-primary"><i class="fa fa-edit"></i>  <?php echo $ri['transaction_number']; ?></a></td>
                                  </tr>
                                  <tr>
                                    <th scope="col" class="p-2">Transaction Date</th>
                                    <td class="text-right p-2"><?php echo fromDate($ri['transaction_date']); ?></td>
                                  </tr>
                                  <tr>
                                    <th scope="col" class="p-2">Part Name</th>
                                    <td class="text-right p-2 w-b-w-s "><span class="text-limit p-r-5"><?php echo $ri['party_name']; ?><span></td>
                                  </tr>
                                  <?php if(array_key_exists('salesmen_id', $ri)){ ?>
                                  <tr>
                                    <th scope="col" class="p-2">Salesman</th>
                                    <td class="text-right p-2 w-b-w-s "><span class="text-limit p-r-5"><?php echo $ri['salesmen_id']!=""?$salesmen[$ri['salesmen_id']]['salesman_name']:"-"; ?><span></td>
                                  </tr>
                                  <?php } ?>
                                  <tr>
                                    <th scope="col" class="p-2">Amount</th>
                                    <td class="text-right p-2"><?php echo customeummarmonyformate($ri['amount']); ?></td>
                                  </tr>
                                  <tr>
                                    <th scope="col" class="p-2">Tax</th>
                                    <td class="text-right p-2"><?php echo customeummarmonyformate($ri['tax_amount']); ?></td>
                                  </tr>
                                  <tr>
                                    <th scope="col" class="p-2">Discount</th>
                                    <td class="text-right p-2"><?php echo customeummarmonyformate($ri['discount_amount']); ?></td>
                                  </tr>
                                  <tr>
                                    <th scope="col" class="p-2">Total</th>
                                    <td class="text-right p-2"><?php echo customeummarmonyformate($ri['total_amount']); ?></td>
                                </tr>
                                 <tr>
                                    <th scope="col" class="p-2">Debit Notes</th>
                                    <td class="text-right p-2"><?php echo customeummarmonyformate($debit_notes_sum[$ri['id']]); ?></td>
                                </tr>
                                 <tr>
                                    <th scope="col" class="p-2">Credit Notes</th>
                                    <td class="text-right p-2 text-danger"><?php echo customeummarmonyformate($credit_notes_sum[$ri['id']]); ?></td>
                                </tr>
                                <tr>
                                    <th scope="col" class="p-2">Payments</th>
                                    <td class="text-right p-2"><?php echo customeummarmonyformate($ri['total_amount']-$ri['pending_amount']); ?></td>
                                </tr>
                                <tr>
                                    <th scope="col" class="p-2">Pending Amount</th>
                                    <td class="text-right p-2 text-danger"><?php echo customeummarmonyformate($ri['pending_amount']); ?></td>
                                </tr>

                              </tbody>
                          </table>
                      </div>
                      <div class="card-footer pt-0">
                          <a href="index.php?option=COM_INSERT_DEBIT_NOTES&ref_type=<?php echo $transaction_table; ?>&ref_id=<?php echo $ri['id']; ?>" class="input-group-text  cursor-pointer float-left btn btn-success btn-sm" > Debit Note</a>
                          <a href="index.php?option=COM_INSERT_CREDIT_NOTES&ref_type=<?php echo $transaction_table; ?>&ref_id=<?php echo $ri['id']; ?>"  class="input-group-text cursor-pointer float-right btn btn-danger m-l-5 btn-sm" >Credit Note</a>
                    </div>
                  </div>
              </div>
            <?php   $mk++;
}  $_SESSION['download_data'] = $cssdata;
}else{ ?>
              <div class="card-px text-center ">
                  <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No  <?php echo $title;  ?></p>
                  <div class="text-center px-4">
                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                  </div>
                </div>
            <?php } ?>
          </div>
      </div>
</div>

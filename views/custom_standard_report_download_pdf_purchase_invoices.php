<?php
$_SESSION['apply_js']['multiselest'] = 1;
  $transaction_table = $_GET['TB'];

  $from_date = fromDate($_REQUEST['from_date'] !=""?$_REQUEST['from_date']:date("Y-m-01"));
  $to_date =fromDate($_REQUEST['to_date'] !=""?$_REQUEST['to_date']:date("Y-m-t"));
$party_id = ($_POST['vendor_ids']!="")?json_encode($_POST['vendor_ids']):"";
  $apiurl = API."?mode=getdynamicdownloadpdfinvoices&transaction_table=".$transaction_table."&from_date=".$from_date."&to_date=".$to_date."&party_id=".$party_id;
  $apires = callAPI($apiurl);
  $result = json_decode($apires, 1);
  $transactions = $result['transactions'];
  $party = $result['party'];

?>
<script>
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
      <h5 class="">PDF Download <?php echo ucwords(str_replace('_',' ',$transaction_table)); ?></h5>
      <div class="card-header-right">
        <div class="dropdown-primary dropdown open">
          <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
          <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
            <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=<?php echo ucwords(str_replace('_',' ',$transaction_table)); ?>" target="_blank">CSV</a>
            <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=<?php echo ucwords(str_replace('_',' ',$transaction_table)); ?>" target="_blank">Excel</a>
          </div>
        </div>
      </div>
    </div>
    <div class="card-body">
      <form method="post" action="">
        <div class="row">
          <div class="col-lg-3">
            <span class="form-label">Select Vendors</span>
            <select aria-label="Vendor Ids[]" name="vendor_ids[]" class="form-control js-example-basic-multiple" multiple>
              <?php
              foreach ($party as $vendor) {
                $selected = in_array($vendor['id'], (array)$_POST['vendor_ids'] ?? []) ? 'selected' : '';
                echo '<option value="' . $vendor['id'] . '" ' . $selected . '>'.$vendor['company'].' ('.$vendor['display_name'].')</option>';
              }
              ?>
            </select>
          </div>

           <div class="col-lg-4 p-t-30">
                  <input type="hidden" name="pick_date" id="pick_date" value="<?php echo $_POST['pick_date'] !=""?$_POST['pick_date']:"This Month"; ?>">
                  <input type="hidden" name="shortcode_date" id="shortcode_date" value="<?php echo $_POST['shortcode_date'] !=""?$_POST['shortcode_date']:"TM"; ?>">
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
          <div class="col-lg-2 p-t-10">
            <span class="form-label"> &nbsp; </span>
            <button type="submit" name="applyform" value="apply" class="mt-3 btn btn-info waves-effect waves-light input-group-text mt-0">
              Apply
            </button>
            <input type="hidden" name="submit" value="applyform">
          </div>
           <div class="col-lg-1 p-t-10">
           </div>
          <div class="col-lg-2 mt-1 text-right">
            <span class="form-label"> &nbsp; </span>
            <button type="button" class="mt-3 btn btn-primary" id="download_transaction" onclick="downloadTransaction()" disabled>Download PDF</button>
            <span class="text-info d-block" id="download_transaction_text"> </span>
          </div>
        </div>
      </form>
    </div>

  <?php include_once "custom_standard_top_print.php"; ?>

<div class="card">
    <div class="card-body">
      <span class="form-label label label-warning mt-5  "><span id="astcnt" class="f-18">0</span> - Transactons</span>
      <div class="table-responsive mt-3">
        <table class="table table-hover">
          <tr style="background-color: #eeeeee;">
            <th scope="col">
              <div class="checkbox-fade fade-in-primary">
                <label class="checkbox checkbox-success">
                  <input aria-label="Ectall Checks" type="checkbox" id="selectall_checks" class="itemRow" onclick="selectallcheck()" onkeydown="">
                  <span class="cr">
                    <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                  </span>
                </label> All
              </div>
            </th>
            <th scope="col">Transaction Number</th>
            <th scope="col">Transaction Date</th>
            <th scope="col">Party Name</th>
            <th scope="col">Amount</th>
            <th scope="col" class="text-right">Tax</th>
            <th scope="col" class="text-right">Total</th>
          </tr>

          <?php
          $sl = 1;
          if (!empty($transactions)) {
            foreach ($transactions as $data) {

              $cssdata[] = array(
                'Sl No' => $sl,
                'Transaction Number' => $data['transaction_number'],
                'Transaction Date' => $data['transaction_date'],
                'Vendor Name' => $data['party_name'],
                'Amount' => $data['amount'],
                'Tax' => $data['tax_amount'],
                'total_amount' => $data['total_amount'],
              );
              ?>
              <tr>
                <td>
                  <div class="checkbox-fade fade-in-primary" >
                    <label class="checkbox checkbox-success">
                      <input aria-label="Checkbox" type="checkbox" class="itemRow checkboxassets" name="checkassets[<?php echo $data['id']; ?>]" value="<?php echo $data['id']; ?>" onchange="selectcheck()">
                      <span class="cr">
                        <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                      </span>
                    </label>
                  </div>
                </td>
                <td><?php echo $data['transaction_number']; ?></td>
                <td><?php echo fromDate($data['transaction_date']); ?></td>
                <td><?php echo $data['party_name']; ?></td>
                <td class="text-right"><?php echo number_format($data['amount'], 2); ?></td>
                <td class="text-right"><?php echo number_format($data['tax_amount'], 2); ?></td>
                <td class="text-right"><?php echo number_format($data['total_amount'], 2); ?></td>
              </tr>
              <?php
              $sl++;
            }
            $_SESSION['download_data'] = $cssdata;
          } else { ?>
            <tr>
              <td colspan="10">
                <div class="card-px text-center py-20 m-t-30">
                  <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Data Available</p>
                  <div class="text-center px-4">
                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                  </div>
                </div>
              </td>
            </tr>
          <?php } ?>
        </table>
      </div>
    </div>
  </div>

<script>
    function selectallcheck(){
      if($('#selectall_checks').is(':checked')){
        $('.checkboxassets').each(function(){
          this.checked = true;
        });
      }else{
        $('.checkboxassets').each(function(){
          this.checked = false;
        });
      }
      selectcheck();
    }
    function selectcheck(){
      var cnt = 0;
      $('.checkboxassets').each(function(){
        if($(this).is(':checked')){
          cnt++;
        }
      });
      $("#astcnt").text(cnt);

      if(cnt !=0){
        $("#download_transaction").prop("disabled", false);
      }else{
        $("#download_transaction").prop("disabled", true);
      }
    }

function downloadTransaction(){
  console.log('process start');
  let total = $('.checkboxassets:checked').length;
  $("#download_transaction").text("Downloading Start - 0%");
  $("#download_transaction").prop("disabled", true);
  (async function(){
      let completed = 0;
      const checkboxes = $('.checkboxassets:checked').toArray();
      for(const cb of checkboxes){
          await DownloadPDF(cb.value, '<?php echo $transaction_table; ?>');
          completed++;
          let percentage = Math.round((completed / total) * 100);
          $("#download_transaction").text("Downloading Start - "+percentage + "%");
      }
      $("#download_transaction").text("Download PDF");
     // Downloading Completed
      $("#download_transaction_text").text(total+"- Downloading Completed");
      $("#download_transaction").prop("disabled", false);
  })();
}
function DownloadPDF(inv_id, transaction_table){
    return new Promise((resolve, reject) => {
        $.ajax({
            url: "views/custom_qo_print.php",
            method: "GET",
            data: { rid: inv_id, print: 'Y', table: transaction_table },
            success: function(htmlContent){
                $.ajax({
                    url: 'convert_html_to_pdf.php',
                    type: 'POST',
                    data: { html_content: htmlContent, title: transaction_table },
                    success: function(pdfdetail){
                        var result = JSON.parse(pdfdetail);
                        var link = document.createElement('a');
                        link.href = result.pdf_url;
                        link.download = result.pdf_name;
                        link.style.display = 'none';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);

                        $.post('convert_html_to_pdf.php', { removepdf: result.pdf_name }, function(res){
                            console.log(res);
                            resolve(); // mark as finished
                        });
                    },
                    error: reject
                });
            },
            error: reject
        });
    });
}

</script>

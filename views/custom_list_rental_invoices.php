<?php
$apiurl = API."?mode=getrentalinvoices";
$apires = callAPI($apiurl);
$result = json_decode($apires, 1);
$rental_invoices = $result['rental_invoices'];
$credit_notes_sum = $result['credit_notes_sum'];
$debit_notes_sum = $result['debit_notes_sum'];
$property_tenant_billings_shared_datetime = $result['property_tenant_billings_shared_datetime'];
$stausresult = ViewStatusData();
?>
<script type="text/javascript">
  function OpenInsertRentalInvoicePopUp(billtype=""){
    jQuery.ajax({
        url: "views/custom_insert_rental_invoices_popup.php",
        data: "insert=Y&bill_type="+billtype,
        type: "POST",
        success:function(data){
            $('#dynamic_popup11').html(data);
            $('#kt_modal_dynamic_popup2').modal("show");
            $(".js-example-basic-single").select2();
        }
    });
}
  function OpenInsertDebitNotePopUp(rental_inv_id){
    jQuery.ajax({
        url: "views/custom_insert_debit_note_popup.php",
        data: "rental_inv_id="+rental_inv_id,
        type: "POST",
        success:function(data){
            // $('#dynamic_popup11').html(data);
            // $('#kt_modal_dynamic_popup2').modal("show");
            $('#popupm').html(data);
            $('#kt_modal_KTDatatable_remotem').modal("show");
            $(".js-example-basic-single").select2();
        }
    });
}
function OpenInsertCreditNotePopUp(rental_inv_id){

    jQuery.ajax({
        url: "views/custom_insert_credit_note_popup.php",
        data: "rental_inv_id="+rental_inv_id,
        type: "POST",
        success:function(data){
            $('#popupm').html(data);
            $('#kt_modal_KTDatatable_remotem').modal("show");
            $(".js-example-basic-single").select2();
        }
    });
}
  function renatalInvoiceSend(property_tenant_billings_id,bill_type){
      jQuery.ajax({
        url: "ajax.php",
        data:'page=renatalinvoicesend&property_tenant_billings_id='+property_tenant_billings_id+'&bill_type='+bill_type,
        type: "POST",
        success:function(res){
          var data = JSON.parse(res);
          var respon = data.res;
          if(respon.status =="fail"){
            swal("<?php echo ucwords($_SESSION['config']['name']); ?>",respon.message, "error");
          }else{
            swal("<?php echo ucwords($_SESSION['config']['name']); ?>",'<?php echo $_SESSION['error_message']['9078']; ?>', "success");
          }
         // location.reload();
        },
        error:function (){}
      });
    }

    function goToAnotherPageAndPrint(inv_id,transaction_table){
       $.ajax({
            url: "views/custom_view_rental_invoices.php",
            method: "GET",
            data:'rid='+inv_id+'&print=Y&transaction_table='+transaction_table,
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
            url: "views/custom_view_rental_invoices.php",
            method: "GET",
            data:'rid='+inv_id+'&print=Y&transaction_table='+transaction_table,
            success: function (htmlContent){
                $.ajax({
                    url: 'convert_html_to_pdf.php',
                    type: 'POST',
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



</script>
<div class="card">
    <div class="card-header row">
        <h5 class="col-lg-6"> Rental Invoices</h5>
        <div class="card-header-right table-card-header col-lg-6">
          <div class="row">
            <div class="col-lg-5">
              <div class="input-group input-group-button mb-0">
                  <input type="text" id="dynamicfilterhideshow" data-id="categoryfilter" data-type="div" class="form-control form-control-sm width1" placeholder="Search" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Search" aria-label="Search">
                  <span class="input-group-text addon-style">
                      <span class=" d-inline-block fw-bold" style="margin-bottom:5px"><span class="ti-search text-white mt-2"></span></span>
                  </span>
              </div>
            </div>
            <a href="#!" onclick="OpenInsertRentalInvoicePopUp()" class="btn btn-success mb-2 desktop-view col-lg-4">
              <i class="icofont icofont-plus icon-white "></i> Rental Invoice</a>
            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light col-lg-2">
              <i class="icofont icofont-reply"></i>
              Back
            </a>
          </div>
        </div>
      </div>
      <div class="card-body m-t-20">

          <div class="row">
            <?php if(count((array)$rental_invoices) !=0){ foreach($rental_invoices as $ri){ ?>
              <div class="col-lg-3" id="categoryfilter">
                  <div class="card">
                      <?php if($ri['cancel_invoice'] =="Y"){ ?>
                          <div id="dynamiccanceldiv"><span>Cancel Invoice</span></div>
                      <?php } ?>
                      <div class="card-header table-card-header">
                          <h5 class=""> Rental Invoices

                            <span class="form-label label label-primary d-inline-block fw-bold" style="color: <?php echo $stausresult[$ri['current_status']]['color']; ?>;    background:  <?php echo $stausresult[$ri['current_status']]['background_color']; ?>; border-color: <?php echo $stausresult[$ri['current_status']]['background_color']; ?>;"><?php echo $stausresult[$ri['current_status']]['name']; ?></span>
                          </h5>
                          <div class="card-header-right">
                            <i role="button" tabindex="0" aria-label="Print" onkeydown="if(event.target===this&&(event.key==='Enter'||event.key===' ')){event.preventDefault();this.click();}" class="ti-printer" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Print" onclick="goToAnotherPageAndPrint('<?php echo $ri['id']; ?>','rental_invoices')"></i>

                             <i role="button" tabindex="0" aria-label="Download" onkeydown="if(event.target===this&&(event.key==='Enter'||event.key===' ')){event.preventDefault();this.click();}" class="ti-cloud-down" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Download" onclick="DownloadPDF('<?php echo $ri['id']; ?>','rental_invoices')"></i>


                            <?php /**if($property_tenant_billings_shared_datetime[$ri['id']]['pdf_name'] !=""){  ?>
                               <a href="/uploads/rental_invoices/<?php echo $property_tenant_billings_shared_datetime[$ri['id']]['pdf_name']; ?>" download><i class="ti-cloud-down" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Download"></i>
                            <?php } **/ ?>
                            <?php if($ri['party_email'] !="" && $property_tenant_billings_shared_datetime[$ri['id']]['id'] !=""){  ?>
                              <i role="button" tabindex="0" aria-label="Send Email" onkeydown="if(event.target===this&&(event.key==='Enter'||event.key===' ')){event.preventDefault();this.click();}" class="ti-email" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Send Email" onclick="renatalInvoiceSend('<?php echo $property_tenant_billings_shared_datetime[$ri['id']]['id']; ?>','<?php echo $ri['ref_bill_type'];?>')"></i>
                            <?php } ?>
                          </div>
                      </div>
                      <div class="card-body">
                          <table class="table table-hover m-b-0" style="font-size:13px;">
                            <tbody>
                                  <tr>
                                    <th class="p-2">Number</th>
                                    <td class="text-right p-2 "> <a href="index?option=COM_INSERT_RENTAL_INVOICES&Rid=<?php echo deocdeId("id:".$ri['id']);?>" class="text-primary"><i class="fa fa-edit"></i>  <?php echo $ri['transaction_number']; ?></a></td>
                                  </tr>
                                  <tr>
                                    <th class="p-2">Transaction Date</th>
                                    <td class="text-right p-2"><?php echo fromDate($ri['transaction_date']); ?></td>
                                  </tr>
                                  <tr>
                                    <th class="p-2">Part Name</th>
                                    <td class="text-right p-2 w-b-w-s"><?php echo $ri['party_name']; ?></td>
                                  </tr>
                                  <tr>
                                    <th class="p-2">Amount</th>
                                    <td class="text-right p-2"><?php echo customeummarmonyformate($ri['amount']); ?></td>
                                  </tr>
                                  <tr>
                                    <th class="p-2">Tax</th>
                                    <td class="text-right p-2"><?php echo customeummarmonyformate($ri['tax_amount']); ?></td>
                                  </tr>
                                  <tr>
                                    <th class="p-2">Discount</th>
                                    <td class="text-right p-2"><?php echo customeummarmonyformate($ri['discount_amount']); ?></td>
                                  </tr>
                                  <tr>
                                    <th class="p-2">Total</th>
                                    <td class="text-right p-2"><?php echo customeummarmonyformate($ri['total_amount']); ?></td>
                                </tr>
                                 <tr>
                                    <th class="p-2">Debit Notes</th>
                                    <td class="text-right p-2"><?php echo customeummarmonyformate($debit_notes_sum[$ri['id']]); ?></td>
                                </tr>
                                 <tr>
                                    <th class="p-2">Credit Notes</th>
                                    <td class="text-right p-2 text-danger"><?php echo customeummarmonyformate($credit_notes_sum[$ri['id']]); ?></td>
                                </tr>
                                <tr>
                                    <th class="p-2">Payments</th>
                                    <td class="text-right p-2"><?php echo customeummarmonyformate($ri['total_amount']-$ri['pending_amount']); ?></td>
                                </tr>
                                <tr>
                                    <th class="p-2">Pending Amount</th>
                                    <td class="text-right p-2 text-danger"><?php echo customeummarmonyformate($ri['pending_amount']); ?></td>
                                </tr>
                                <tr>
                                    <th class="p-2">Email Sent</th>
                                    <td class="text-right p-2"><?php echo $property_tenant_billings_shared_datetime[$ri['id']]['shared_datetime'] !=""?fromDate($property_tenant_billings_shared_datetime[$ri['id']]['shared_datetime']):" - ";?></td>
                                </tr>
                              </tbody>
                          </table>
                      </div>
                      <div class="card-footer pt-0">


                          <a href="index.php?option=COM_INSERT_DEBIT_NOTES&table=rental_invoices&Rid=<?php echo $ri['id']; ?>&TD=<?php echo fromDate(); ?>" class="input-group-text  cursor-pointer float-left btn btn-success btn-sm" > Debit Note</a>
                          <a href="index.php?option=COM_INSERT_CREDIT_NOTES&table=rental_invoices&Rid=<?php echo $ri['id']; ?>&TD=<?php echo fromDate(); ?>"  class="input-group-text cursor-pointer float-right btn btn-danger m-l-5 btn-sm" >Credit Note</a>
                    </div>
                      <!-- <div class="card-footer pt-0">
                          <a href="#!" onclick="OpenInsertDebitNotePopUp('<?php echo $ri['id']; ?>')" class="input-group-text  cursor-pointer float-left btn btn-success" > Debit Note</a>
                          <a href="#!"  onclick="OpenInsertCreditNotePopUp('<?php echo $ri['id']; ?>')" class="input-group-text cursor-pointer float-right btn btn-danger m-l-5" >Credit Note</a>
                    </div> -->
                   <!--  <div class="card-footer">
                      <div class="row">
                      <div class="col-lg-7"></div>
                      <div class="col-lg-1">
                              <a target="_blank" href="index.php?option=COM_QO_PRINT&amp;rid=2&amp;table=rental_invoices&amp;title=Rental+Invoices" class="btn btn-clean"><i class="ti-printer"></i>
                              </a>
                            </div>
                            <div class="col-lg-1">
                              <a target="_blank" href="index.php?option=COM_QO_PRINT&amp;rid=2&amp;table=rental_invoices&amp;title=Rental+Invoices" class="btn btn-clean">
                                <i class="fa fa-download"></i>
                              </a>
                            </div>
                            <div class="col-lg-1">
                                <a target="_blank" href="index.php?option=COM_QO_PRINT&amp;rid=2&amp;table=rental_invoices&amp;title=Rental+Invoices" class="btn btn-clean">
                                <i class="ti-mobile" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Phone Conversation Request"></i></a>
                              </div>
                              <div class="col-lg-1">
                                <a target="_blank" href="index.php?option=COM_QO_PRINT&amp;rid=2&amp;table=rental_invoices&amp;title=Rental+Invoices" class="btn btn-clean">
                              <i class="ti-email" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Send Email"></i>
                            </a>
                            </div>
                      </div>
                    </div> -->
                  </div>
              </div>
            <?php }
            }else{ ?>
              <div class="card-px text-center ">
                  <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Rental Invices</p>
                  <div class="text-center px-4">
                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                  </div>
                </div>
            <?php } ?>
          </div>
      </div>
</div>
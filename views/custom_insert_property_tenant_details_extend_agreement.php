<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$transaction_id = $_POST['transaction_id'];
$url1 = API."?mode=getpropertytenantdetails&rid=".$transaction_id;
$data_details1 = callAPI($url1);
$result =  json_decode($data_details1,1);
$property_owners = $result['property_owners'];
$property_tenant_details = $result['property_tenant_details'];
$property_owner_details = $result['property_owner_details'];
$podareas = $result['property_owner_detail_areas'];
$rentbilltoberaise = $result['rent_bill_to_be_raise_type'];
$billtoberaisedtype = $result['bill_to_be_raise_type'];
$rental_bill_types = $result['rental_bill_types'];
$property_tenant_billings = $result['property_tenant_billings'];
$property_tenant_details_extends = $result['property_tenant_details_extends'];
?>
<script type="text/javascript">
    function submitExtendAgreement(){
        $("#extend_agreement_month_error").text('');
        var extend_agreement_month =$("#extend_agreement_month").val();
        if(extend_agreement_month != ""){
            var formvalue =  $("#employee_popup_form").serialize();
            jQuery.ajax({
                url: "ajax.php",
                data: "page=submitextendagreement&"+formvalue,
                type: "POST",
                success:function(response){
                    console.log(response);

                     tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');
                     $('#popupm').html("");
                     $('#kt_modal_KTDatatable_remotem').modal("hide");

                }
            });
        }else{
          $("#extend_agreement_month_error").text('Please Enter Required Fields');
        }
    }
</script>
<div class="modal-header  ">
  <h5 class="modal-title"> Extend Agreement</h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="closeInsertEmployees()"></button>
</div>
<div class="modal-body p-l-50 p-r-50 p-t-50">
    <div class="row mb-4">
      <div class="col-3 text-center">
          <div class="s-cont d-inline-block">
              <p class="m-b-0 f-12">Commencement Date</p>
              <h5 class="f-w-700 m-b-0"><?php echo fromDate($property_tenant_details['rent_commencement_date']); ?></h5>
          </div>
      </div>
      <div class="col-3 b-l-default text-center">
          <div class="s-cont d-inline-block">
              <p class="m-b-0 f-12">Expiry Date</p>
              <h5 class="f-w-700 m-b-0"><?php echo fromDate($property_tenant_details['lease_period_end_date']); ?></h5>
          </div>
      </div>
      <div class="col-3 b-l-default text-center">
          <div class="s-cont d-inline-block">
              <p class="m-b-0  f-12">Rent Escalation After</p>
              <h5 class="f-w-700 m-b-0"><?php echo $property_tenant_details['rent_escalation_after_month']; ?> - (<?php echo $property_tenant_details['rent_escalation_percentage']; ?>%)</h5>
          </div>
      </div>
       <div class="col-3 b-l-default text-center">
          <div class="s-cont d-inline-block">
              <p class="m-b-0 f-12">Tenant Name</p>
              <h5 class="f-w-700 m-b-0"><?php echo $property_tenant_details['company_name']; ?></h5>
          </div>
      </div>
  </div>
    <form id="employee_popup_form">
        <input type="hidden" name="rid" value='<?php echo $transaction_id; ?>'>
        <div class="row">
          <div class="col-lg-8">
            <?php
              $aryseq = array();
              $billingdetails = $property_tenant_billings[0]['bill_detail'];
              $billingdetail = json_decode($billingdetails,1);
              foreach($billingdetail as $bd1){
                $aryseq[] = $bd1['name'];
              }
              $utilitydetails = $property_tenant_billings[0]['utility_details'];
              $utilitydetail = json_decode($utilitydetails,1);
              foreach($utilitydetail as $bd1){
                $aryseq[] = $bd1['name'];
              }
              $datearray = array();
              foreach($property_tenant_billings as $ptb){
                $aryval = array();
                $billingdetail = json_decode($ptb['bill_detail'],1);
                foreach($billingdetail as $bd1){
                  $aryval[] = $bd1;
                }
                $utilitydetails = json_decode($ptb['utility_details'],1);
                foreach($utilitydetails as $bd1){
                  $aryval[] = $bd1;
                }
                $datearray[$ptb['month_start_date']] = $aryval;
              }
            ?>
            <div clas="dt-responsive table-responsive" style="overflow: auto;">
              <table class="table table-hover m-b-0" style="font-size:13px;" id="categoryfilter">
                <thead>
                  <tr>
                    <th scope="col">Bill</th>
                    <?php foreach($property_tenant_billings as $ptb){ ?>
                    <th scope="col" class="text-right"><?php echo fromDate($ptb['month_start_date']); ?></th>
                    <?php } ?>

                  </tr>
                </thead>
                <tbody>
                  <?php
                  $totalary = array();
                  foreach($aryseq as $keyid =>$bd1){ ?>
                    <tr>
                      <th scope="col" class="p-2">
                        <?php echo $bd1; ?>
                      </th>
                      <?php foreach($datearray as $datekey=>$ptb){
                          $billingdetails1 = $ptb[$keyid];
                          $totalary[$datekey] += $billingdetails1['rent'];
                      ?>
                        <td class="text-right p-2"><?php echo customeummarmonyformateifine($billingdetails1['rent']); ?></td>
                        <?php }  ?>
                    </tr>
                  <?php } ?>
                    <tr>
                      <th scope="col" class="p-2">
                        Total
                      </th>
                      <?php foreach($datearray as $datekey=>$ptb){ ?>
                        <td class="p-2 text-right"><?php echo customeummarmonyformateifine($totalary[$datekey]!=""?$totalary[$datekey]:0); ?></td>
                      <?php } ?>
                    </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="col-xl-4 form-group" >
                <span class="form-label">Extend Month Year <span class="text-danger">*</span></span>
                <span class="d-block error-message text-danger" id="extend_agreement_month_error"></span>
                <input aria-label="Extend Agreement Month" class="form-control" type="month" name="extend_agreement_month" id="extend_agreement_month" min="<?php echo date("Y-m", strtotime('+1 months', strtotime($property_tenant_details['lease_period_end_date']))); ?>" value="<?php echo date("Y-m", strtotime('+1 months', strtotime($property_tenant_details['lease_period_end_date']))); ?>"/>
                <div>
                    <table class="table table-hover m-b-0" style="font-size:13px;" id="categoryfilter_2">
                      <thead>
                        <tr>
                          <th scope="col">Extend Date</th>
                          <th scope="col">Date Time</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        if(count((array)$property_tenant_details_extends) !="0"){
                          foreach($property_tenant_details_extends as $ptde){ ?>
                          <tr>
                            <td><?php echo $ptde['extend_agreement_month_year']; ?></td>
                            <td><?php echo $ptde['extend_agreement_datetime']; ?></td>
                          </tr>
                        <?php } ?>
                        <?php } else { echo "<tr><td colspan='2' class='text-center'>No Data</td></tr>";
} ?>
                      </tbody>
                    </table>

                </div>
          </div>
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary waves-effect waves-light" id="insertemployeedetails_btn" onclick="submitExtendAgreement()">Submit</button>
</div>

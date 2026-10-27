<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

$table_name = 'customers';
$vdata1['order'] = "id ASC";
$vdata1["where"] = array('company_id' => $_SESSION['company_id'], 'is_active' => '0');
$vdata = urlencode(json_encode($vdata1));
$vurl = API . "?mode=gettabledetailswithconditions&table=" . $table_name . "&data=" . $vdata;
$customers_response = callAPI($vurl);
$customers_response = json_decode($customers_response, 1);
$customers_array = array();
foreach ($customers_response as $row) {
    $customers_array[$row['id']] = $row['first_name'].$row['last_name'];
}
?>
<script type="text/javascript">
      function submitDynamiPopUpFormEMPT(){
          var formdata = $("#dynamic_status_forms").serialize();
          console.log(formdata);
          $.ajax({
              url: "ajax.php",
              type: "POST",
              data: 'page=submitemploymenttype&'+formdata,
              success: function(response) {
          console.log(response);
                  var res = JSON.parse(response);
          console.log(res);
          if(res.status=='Fail'){

            tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX','The transition date is already assigned in an existing record. Please update the effective dates first','');
          }else{
               tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Employment type added successfully','');
             $('#kt_modal_KTDatatable_remotesq').modal('hide');

          }
              }
          });
      }
  </script>
<div class="second-modal" id="secondModal" style="border: 1px #cccc solid; border-radius: 20px;" >
  <div class="modal-header">
      <h5 class="modal-title">
          Update Employment Type
      </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="" method="post" id="dynamic_status_forms">
          <div class="modal-body" >
            <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
                <div class="form-group row">
                    <div class="col-lg-6">
                        <span class="m-b-5">Effective From </span>
                        <input aria-label="Effective From Date" type="date" class="form-control" name="effective_from_date" id="effective_from_date">
                    </div>
                    <div class="col-lg-6">
                        <span class="m-b-5">Effective To </span>
                        <input aria-label="Effective To Date" type="date"  class="form-control" name="effective_to_date" id="effective_to_date">
                    </div>
                    <div class="col-lg-6 m-t-20">
                        <span class="m-b-5">Customer</span>

                        <select aria-label="Customer" id="customer_id" name="customer_id" class="form-select js-example-basic-single">
                            <option value="">Select Any One</option>
                            <?php foreach($customers_array as $key=>$val) { ?>
                                <option value="<?php echo $key;?>"><?php echo $val; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-lg-6 m-t-20">
                        <span class="m-b-5">Billing Amount</span>
                        <input aria-label="Billing Amount" type="number" min="0" value="0" class="form-control" name="billing_amount" id="billing_amount">
                    </div>
                    <div class="col-lg-6 m-t-20">
                        <span class="m-b-5">Remarks </span>
                        <textarea aria-label="Remarks"  class="form-control" name="remarks" id="remarks"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="emp_id" value="<?php echo $_GET['emp_id']; ?>">
                <input type="hidden" name="employment_type" value="<?php echo $_GET['type']; ?>">
                <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary mr-2" onclick="submitDynamiPopUpFormEMPT()" id="statuspopupbtn">Submit</button>
            </div>
          </div>
      </form>
  </div>
</div>

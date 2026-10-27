<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$rid = $_POST['rid'];
$url1 = API."?mode=getpropertytenantdetails&rid=".$rid."&openingbalance=Y";
$data_details1 = callAPI($url1);
$result =  json_decode($data_details1,1);

$property_owners = $result['property_owners'];
$property_tenant_details = $result['property_tenant_details'];
$podareas = $result['property_owner_detail_areas'];

$rental_invoices = $result['rental_invoices'];
$rental_invoice_items = $result['rental_invoice_items'];
$rentalinvoiceitems = array();
foreach($rental_invoice_items as $rii){
    $rentalinvoiceitems[$rii['transaction_id']][] = $rii;
}
$property_owner_detail_areas = array();
foreach($podareas as $podarea){
    $property_owner_detail_areas[$podarea['id']] = $podarea;
}
$pownerareids =json_decode($property_tenant_details['property_owner_area_ids'],1);
?>
<script type="text/javascript">
function submitTenantOpeningbalance(){
    $("#popupshowerror").hide();
     var error = 0;
    $('.checkempty').each(function(index, element) {
         if($(element).val() == ''){
             error = 1;
         }
    });
    if(error == 0){
         swal({
              title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
              text: "<?php echo $_SESSION['error_message']['9034']; ?>",
              type: "info",
              showCancelButton: true,
              confirmButtonClass: "btn-danger",
              confirmButtonText: "Submit",
              cancelButtonText: "Don't Submit",
              closeOnConfirm: true,
              closeOnCancel: true
            },
            function(isConfirm) {
              if(isConfirm) {
                var formdata = $("#tenantopeningbalanceform").serialize();
                $.ajax({
                    url: "ajax.php",
                    type: "POST",
                    data: 'page=inserttenantopeningbalance&'+formdata,
                    success: function(response) {
                        console.log(response);
                        tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3000']; ?>','');
                        $('#kt_modal_dynamic_popup2').modal('hide');
                    }
                });
            }
        });
    }else{
        $("#popupshowerror").show();
    }
}
function deletRow(id){
    $("#"+id).remove();
}
var maxrows = 1000;
function addMoreOpeningBalance(){
    var startdate = $('#rent_commencement_date').val();
    var start_dates = getDateMonthYear(startdate,'<?php echo $_SESSION['system_date_formate'] ?>');
    var start_date = new Date(start_dates['year'], start_dates['month']-1, start_dates['day']);

    var system_start_date = $('#system_start_date').val();
    var systestrmdate = getDateMonthYear(system_start_date,'<?php echo $_SESSION['system_date_formate'] ?>');
    var sys_start_date = new Date(systestrmdate['year'], systestrmdate['month']-1, systestrmdate['day']);

    var system_start_date_check =$('#system_start_date').val();
    var startDateObjectss = "";
    if(system_start_date_check !=""){
        var maxDate = start_date > sys_start_date ? start_date : sys_start_date;
        startDateObjectss = new Date(maxDate);
    }else{
        startDateObjectss = startDateObject;
    }

    maxrows = maxrows+1;
    var html ="";
    var rowid = 'removerow_'+maxrows;
    html +='<tr id="'+rowid+'">';
    <?php foreach($pownerareids as $prid){ ?>
        html +='<th scope="col" class="p-1"><input type="hidden" name="property_area_name['+maxrows+'][]"  class="form-control" value="<?php echo $property_owner_detail_areas[$prid]['property_area_name'];  ?>"><input type="hidden" name="property_area_id['+maxrows+'][]"  class="form-control" value="<?php echo $prid;  ?>"><input aria-label="Rent[" type="number" name="rent['+maxrows+'][]" class="form-control text-right sumrent_'+maxrows+'" onblur="calculateRent('+maxrows+')"></th>';
    <?php } ?>
    html +='<th scope="col" class="p-1"><input aria-label="Transaction Date[" type="text" name="transaction_date['+maxrows+']" class="form-control datepicker" value=""></th>';
    html +='<th scope="col" class="p-1"><input aria-label="Total[" type="number" name="total['+maxrows+']" id="total_rent_'+maxrows+'" class="form-control text-right" value="" readonly=""></th>';
    html +='<th scope="col" class="p-1"><textarea aria-label="Comment[" type="text" name="comment['+maxrows+']" class="form-control" id="comment"></textarea></th>';
    html +='<th scope="col" class="p-1 text-center"><button type="button" class="btn p-0 border-0 bg-transparent ti-trash text-danger mr-5" onclick="deletRow(\''+rowid+'\')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Removed" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button></th></tr>';
    $("#tenantopenbalancetable").append(html);
    $( ".datepicker" ).datepicker({
        showOn: "both",
        buttonText: "<i class='ti-calendar text-dark f-20 f-w-800 m-r-10'></i>",
        dateFormat: date_format,
        maxDate: startDateObjectss,
        setDate: new Date(),
        changeMonth: true,
        changeYear: true,
    });

}
function calculateRent(rowid){
    var total_rent = 0;
    $('.sumrent_'+rowid).each(function(index, element) {
         if($(element).val() != ''){
             total_rent = parseFloat(total_rent)+ parseFloat($(element).val());
         }
    });
    $("#total_rent_"+rowid).val(total_rent);
}

</script>
<div class="modal-header">
    <h4 class="modal-title">Tenant Opening Balances</h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body m-l-50 m-r-50">
    <div class="row">
        <?php $mk = 1; foreach($rental_invoices as $renti){ ?>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5><?php echo $renti['transaction_number']; ?></h5>
                </div>
                <div class="card-body">
                    <table class="table m-0">
                        <tbody>
                            <tr>
                                <th scope="col" class="p-1">Transaction Date</th>
                                <td class="p-1 text-right"><?php echo fromDate($renti['transaction_date']); ?></td>
                            </tr>
                            <?php foreach($rentalinvoiceitems[$renti['id']] as $rntitem){ ?>
                                <tr>
                                    <th scope="col" class="p-1"><?php echo $rntitem['item_name']; ?></th>
                                    <td class="p-1 text-right"><?php echo customeummarmonyformateifine($rntitem['total_amount']); ?></td>
                                </tr>
                            <?php }  ?>
                            <tr>
                                <th scope="col" class="p-1">Total</th>
                                <th scope="col" class="p-1 text-right"><?php echo customeummarmonyformateifine($renti['total_amount']); ?></th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php $mk++;
} ?>
    </div>
    <form id="tenantopeningbalanceform" method="post">
        <table class="table table-hover m-b-0" style="font-size:13px;" id="tenantopenbalancetable">
            <thead>
                <tr>
                    <th scope="col" class="text-center p-1" colspan="<?php echo count((array)$pownerareids); ?>">Propert Rent+Utility</th>
                    <th scope="col" class="align-middle p-1 text-center" rowspan="2">Transaction Date <span class="text-danger">*</span></th>
                    <th scope="col" class="align-middle p-1 text-center" rowspan="2">Total <span class="text-danger">*</span></th>
                    <th scope="col" class="align-middle p-1 text-center" rowspan="2">Comment</th>
                    <th scope="col" class="align-middle p-1 text-center" rowspan="2">Remove</th>
                </tr>
                <tr>
                    <?php foreach($pownerareids as $prid){ ?>
                    <th scope="col" class="p-1 text-center"><?php echo $property_owner_detail_areas[$prid]['property_area_name'];  ?> <span class="text-danger">*</span></th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <tr id="removerow_1">
                    <?php foreach($pownerareids as $prid){ ?>
                    <th scope="col" class="p-1">
                        <input type="hidden" name="property_area_name[1][]"  class="form-control" value="<?php echo $property_owner_detail_areas[$prid]['property_area_name'];  ?>">
                        <input type="hidden" name="property_area_id[1][]"  class="form-control" value="<?php echo $prid;  ?>">
                        <input aria-label="Rent[1][]" type="number" name="rent[1][]"  class="form-control text-right sumrent_1 checkempty" onblur="calculateRent(1)">
                    </th>
                    <?php } ?>
                    <th scope="col" class="p-1">
                        <input aria-label="Transaction Date[1]" type="text" name="transaction_date[1]" class="form-control datepicker checkempty" value="">
                    </th>
                    <th scope="col" class="p-1">
                        <input aria-label="Total[1]" type="number" name="total[1]" class="form-control text-right checkempty" id="total_rent_1" value="" readonly="">
                    </th>
                    <th scope="col" class="p-1">
                        <textarea aria-label="Comment[1]" type="text" name="comment[1]" class="form-control " id="comment_2"></textarea>
                    </th>
                    <th scope="col" class="p-1 text-center">
                        <button type="button" class="btn p-0 border-0 bg-transparent ti-trash text-danger mr-5" onclick="deletRow('removerow_1')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Removed" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
                    </th>
                </tr>
            </tbody>
        </table>
        <input type="hidden" name="rid" id="rid" value="<?php echo $rid; ?>">
        <button class="btn btn-success m-t-20" id="addRows" type="button" onclick="addMoreOpeningBalance()"><i class="fa fa-plus m-r-5"></i>Add More</button>

    </form>

</div>
<div class="modal-footer" >
    <span class="text-danger f-w-700" id="popupshowerror" style="display: none">Please fill out all required fields</span>
    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="submitTenantOpeningbalance()"> Save</button>
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
</div>

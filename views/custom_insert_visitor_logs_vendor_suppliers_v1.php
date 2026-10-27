<?php
   $entryTypes = array('IM' => 'Inward Material','OM' => 'Outward Material','RT' => 'Returnable','NR' => 'Non Returnable','EV' => 'Empty Vehicle','MP' => 'Material Pickup','MD' => 'Material Delivery');
   $vehicleTypes = array('TRK'=>'Truck','TMP'=>'Tempo','CNT' => 'Container','VAN' => 'Van','CAR' => 'Car','TRL' => 'Trailer','PUV' => 'Pickup Van','TNK' => 'Tanker','DMP' => 'Dumper','TWT' => 'Tractor with Trolley');
   $prooftype = array('AAD' => 'Aadhaar','DL'=>'License', 'PAN'=>'PAN','VID'=>'Voter ID');
   $referenceTypes = array('NOREF' => 'No Reference','purchase_orders' => 'Purchase Order','returnable_gate_passes' => 'RGP','non_returnable_gate_passes' => 'NRGP', 'purchase_invoices' => 'Purchase Invoice');
?>
<script>
function getReferenceTransactions(){
    var vendor_supplier_document_type = $("#vendor_supplier_document_type").val();
    if(vendor_supplier_document_type!="NOREF"){
        jQuery.ajax({
            url: "ajax.php",
            data: "page=getreferencetransactions&vendor_supplier_document_type="+vendor_supplier_document_type,
            type: "POST",
            success:function(data){
                $("#vendor_supplier_transaction_id").html(data);
            }
        });
    }else{
        $("#vendor_supplier_transaction_id").html('<option value="">No Documents</option>');
    }
}
 function goToAnotherPageAndPrint(){
        var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $("#printviewdocuments").html(loader);
        $("#printviewdocuments").removeClass('d-none');
        var transaction_table = $("#vendor_supplier_document_type").val();
        var inv_id = $("#vendor_supplier_transaction_id").val();

       $.ajax({
            url: "views/custom_insert_visitor_logs_vendor_suppliers_ajax.php",
            method: "POST",
            data:'transaction_id='+inv_id+'&transaction_select=Y&transaction_table='+transaction_table,
            success: function (data) {
                var result = JSON.parse(data);
               $("#printviewdocuments").html(result.htmlview);
               $('#vendor_supplier_vendor_id').select2();
                $('#vendor_supplier_vendor_id').val(result.vendor_id).trigger('change');
            },
            error: function () {
                alert("Error fetching data");
            }
        });

       //      url: "views/custom_qo_print.php",
       //      method: "GET",

    }

</script>
<div class="col-lg-6">
    <div class="row">
        <div class="form-group col-lg-4 col-xs-12">
            <span>Gate Pass Number </span>
            <input aria-label="Vendor Supplier Transaction No" class="form-control" type="text" name="vendor_supplier_transaction_no" id="vendor_supplier_transaction_no" value="00001" readonly>
        </div>
        <div class="form-group col-lg-4 col-xs-12 w-full">
            <span>Entry Type</span>
            <select aria-label="Vendor Supplier Entry Type" class="form-select js-example-basic-single"  name="vendor_supplier_entry_type">
                <?php foreach($entryTypes as $key => $value){ ?>
                    <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="form-group col-lg-4 col-xs-12 w-full">
            <span>Vehicle Type</span>
            <select aria-label="Vendor Supplier Vehicle Type" class="form-select js-example-basic-single"  name="vendor_supplier_vehicle_type">
                 <?php foreach ($vehicleTypes as $key => $value) { ?>
                    <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="form-group col-lg-4 col-xs-12">
            <span>Vehicle Number</span>
            <input aria-label="Vendor Supplier Vehicle Number" class="form-control" type="text" name="vendor_supplier_vehicle_number" id="vendor_supplier_vehicle_number" value="<?php echo htmlspecialchars($return['delivery_phone'] ?? ''); ?>">
        </div>
        <div class="form-group col-lg-4 col-xs-12">
            <span>Driver Name</span>
            <input aria-label="Vendor Supplier Driver Name" class="form-control" type="text" name="vendor_supplier_driver_name" id="vendor_supplier_driver_name" value="" >
        </div>
        <div class="form-group col-lg-4 col-xs-12">
            <span>Mobile Number</span>
            <input aria-label="Vendor Supplier Driver Mobile" class="form-control" type="text" name="vendor_supplier_driver_mobile" id="vendor_supplier_driver_mobile" value="" >
        </div>
        <div class="form-group col-lg-12 col-xs-12">
            <span>Purpose of Visit</span>
            <textarea aria-label="Vendor Supplier Purpose" class="form-control" rows="5" cols="5" id="vendor_supplier_purpose" name="vendor_supplier_purpose"><?php echo htmlspecialchars($return['address'] ?? ''); ?></textarea>
        </div>

        <div class="form-group col-lg-12 col-xs-12">
            <div class="row">
               <div class="form-group col-lg-4 col-xs-12 w-full">
                <span>Proof Type (any one)</span>
                <select aria-label="Vendor Supplier Proof Type[]" class="form-select js-example-basic-single"  name="vendor_supplier_proof_type[]">
                     <?php foreach ($prooftype as $key => $value) { ?>
                        <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group col-lg-4 col-xs-12">
                <span>Document No.</span>
                <input aria-label="Vendor Supplier Proof Document No[]" class="form-control" type="tert" name="vendor_supplier_proof_document_no[]"  >
            </div>
            <div class="form-group col-lg-4 col-xs-12">
                <span>Proof Document</span>
                <input aria-label="Vendor Supplier Proof Document File[]" class="form-control" type="file" name="vendor_supplier_proof_document_file[]" >
            </div>
        </div>
    </div>
</div>
</div>
<div class="col-lg-6">
   <div class="form-group col-lg-12 col-xs-12">
        <div class="row">
            <div class="form-group col-lg-4 col-xs-12 w-full">
                <span>Reference Type (any one)</span>
                <select aria-label="Vendor Supplier Document Type" class="form-select js-example-basic-single"  name="vendor_supplier_document_type" id="vendor_supplier_document_type" onchange="getReferenceTransactions()">
                     <?php foreach ($referenceTypes as $key => $value) { ?>
                        <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group col-lg-5 col-xs-12 w-full">
                <span>Document No.</span>
                <select aria-label="Vendor Supplier Transaction" class="form-select js-example-basic-single" id="vendor_supplier_transaction_id" name="vendor_supplier_transaction_id" onchange="goToAnotherPageAndPrint()">
                    <option value="">Select Any One</option>
                </select>
            </div>
            <div class="form-group col-lg-3 col-xs-12">
                <span>Document</span>
                <input aria-label="Vendor Supplier Document File" class="form-control" type="file" name="vendor_supplier_document_file">
            </div>
        </div>
    </div>
<div class="form-group col-lg-12 col-xs-12 w-full">
    <span>Vendor/Supplier<b class="text-danger">*</b></span>
    <select aria-label="Vendor Supplier Vendor" class="form-select js-example-basic-single" id="vendor_supplier_vendor_id"  name="vendor_supplier_vendor_id">
        <option value=""> Select Any one </option>
        <?php
            foreach ($vendors as $e) {
                $sel = ($return['delivery_company_person'] == $e['id']) ? 'selected' : '';
                echo '<option value="' . htmlspecialchars($e['id']) . '" ' . $sel . '>' . htmlspecialchars($e['company'].' - '.$e['display_name']) . '</option>';
            }
        ?>
    </select>
</div>
<div class="form-group col-lg-12 col-xs-12 w-full">
    <span>Department<b class="text-danger">*</b></span>
    <select aria-label="Vendor Supplier Department" class="form-select js-example-basic-single"  name="vendor_supplier_department_id">
        <option value=""> Select Any one </option>
        <?php foreach ($departments as $e) {
                $sel = ($return['delivery_company_person'] == $e['id']) ? 'selected' : '';
                echo '<option value="' . htmlspecialchars($e['id']) . '" ' . $sel . '>' . htmlspecialchars($e['department_name']) . '</option>';
            }
        ?>
    </select>
</div>
<div class="custom-scroll" style=" height: 300px;" id="printviewdocuments">
    <?php include_once 'custom_insert_visitor_logs_vendor_suppliers_ajax.php'; ?>
</div>
</div>

// Helper function to initialize select2, select2 disabled, and tooltips
function initializeSelectorsAndTooltips() {
    $(".js-example-basic-single").select2();
    $(".js-example-disabled-results").select2();
    $('[data-bs-toggle="tooltip"]').tooltip();
}

// Global variables for BOM item management
var seq = 10222;
var oldWorkOrderQty = 1;

// Select product name and auto-populate UOM and Price
function selectProductName(seqNum){
    // console.log('test');
    var selectedOption = $('#item_id_' + seqNum + ' option:selected');
    var uomId = selectedOption.attr('data-uom');
    var price = selectedOption.attr('data-price') || 0;
    
    // Set UOM field
    if(uomId){
        $('#uom_' + seqNum).val(uomId).trigger('change');
    }
    
    // Set Price field
    $('#item_price_' + seqNum).val(price);
    calculateItemTotal(seqNum);
}

// Calculate total amount for a specific item row
function calculateItemTotal(seqNum){
    var qty = parseFloat($('#request_qty_' + seqNum).val()) || 0;
    var price = parseFloat($('#item_price_' + seqNum).val()) || 0;
    var total = qty * price;
    $('#item_total_amount_' + seqNum).val(total.toFixed(2));
}

// Add more BOM child rows
function addMoreBomChild(){
    seq = seq + 1;
    // Get items data from existing select options for template
    var itemsOptions = $('#item_id_1').html();
    var uomOptions = $('#uom_1').html();
    
    var htmlRows = `<tr id="remove_row_${seq}">
        <th class="p-1 w-full">
            <input type="hidden" class="manufacturing_bom_items_seq_no[]" value="${seq}">
            <input type="hidden" name="subcontract_work_order_item_id[]" value="">
            <select class="js-example-basic-single item-selection" id="item_id_${seq}" name="item_id[]" onchange="selectProductName('${seq}')" required>
                ${itemsOptions}
            </select>
        </th>
        <th class="p-1 w-full">
            <select class="js-example-basic-single" id="uom_${seq}" name="uom[]" required>
                ${uomOptions}
            </select>
        </th>
        <th class="p-1">
            <input type="number" class="form-control text-right" id="request_qty_${seq}" name="request_qty[]" min="1" value="1" onchange="calculateItemTotal('${seq}')" oninput="calculateItemTotal('${seq}')" required>
        </th>
        <th class="p-1">
            <input type="hidden" class="form-control text-right" id="bom_qty_${seq}" name="bom_qty[]" value="" readonly>
        </th>
        <th class="p-1">
            <input type="number" class="form-control text-right" id="item_price_${seq}" name="item_price[]" min="0" step="0.01" value="0" onchange="calculateItemTotal('${seq}')" oninput="calculateItemTotal('${seq}')" required>
        </th>
        <th class="p-1">
            <input type="number" class="form-control text-right" id="item_total_amount_${seq}" name="item_total_amount[]" min="0" step="0.01" value="0" readonly>
        </th>
        <th><textarea id="description_${seq}" name="description[]" class="form-control " autocomplete="off"></textarea></th>
        <th class="p-1">
            <i class="ti-trash cursor-pointer" onclick="removerows('remove_row_${seq}')"></i>
        </th>
    </tr>`;
    $('#invoiceItem').append(htmlRows);
    initializeSelectorsAndTooltips();
}

// Setup work order quantity change listener
function setupWorkOrderQtyListener(){
    oldWorkOrderQty = parseFloat($('#work_order_item_qty').val()) || 1;
    
    $('#work_order_item_qty').off('change.bomqty').on('change.bomqty', function(){
        var newWorkOrderQty = parseFloat($(this).val()) || 1;
        if(newWorkOrderQty > 0 && oldWorkOrderQty > 0){
            var ratio = newWorkOrderQty / oldWorkOrderQty;
            
            // Update all request_qty values in the table
            $('input[name="request_qty[]"]').each(function(){
                var currentQty = parseFloat($(this).val()) || 0;
                var newQty = currentQty * ratio;
                $(this).val(newQty.toFixed(2)).trigger('change');
            });
            
            oldWorkOrderQty = newWorkOrderQty;
        }
    });
}

window.addEventListener("load", function() { 
    // Event listeners
    $(document).off("change", ".OnChangeSelectpaty").on("change", ".OnChangeSelectpaty", function () {
        var vendor_id = $(this).val();
        OnChangeSelectpaty(vendor_id);
    });
    $(document).off("change", ".selectWorkoOderItemID").on("change", ".selectWorkoOderItemID", function () {
        var product_id = $(this).val();
        selectWorkoOderItemID(product_id);
    });
    $(document).off("change", ".selectmanufacturingBOM").on("change", ".selectmanufacturingBOM", function () {
        selectmanufacturingBOM();
    });
    
    // Initialize selectors on page load
    initializeSelectorsAndTooltips();
    
    // Auto-populate price when product is selected
    $('#product_id').on('change', function(){
        var selectedOption = $(this).find('option:selected');
        var price = selectedOption.attr('data-price') || 0;
        $('#work_order_item_price').val(price);
        calculateWorkOrderTotal();
    });

    // Calculate total amount when quantity or price changes
    $('#work_order_item_qty, #work_order_item_price').on('input change', function(){
        calculateWorkOrderTotal();
    });

    // Initial calculation on page load
    calculateWorkOrderTotal();
    
    // Setup work order quantity listener for BOM items
    setupWorkOrderQtyListener();
    
    // Initialize BOM item calculations on initial load
    initializeBomItemCalculations();
    
    // Edit mode - populate dependent dropdowns
    var rid = $('input[name="rid"]').val();
    if(rid != "" && rid != "0"){
        var party_id = $('select[name="party_id"]').val();
        var product_id = $('select[name="product_id"]').val();
        var manufacturing_bom_id = $('select[name="manufacturing_bom_id"]').val();
        
        if(party_id != ""){
            OnChangeSelectpaty(party_id);
        }
        if(product_id != ""){
            selectWorkoOderItemID(product_id);
            // After BOM loading, select the saved BOM
            setTimeout(function(){
                if(manufacturing_bom_id != ""){
                    $("#manufacturing_bom_id").val(manufacturing_bom_id).trigger('change');
                }
            }, 300);
        }
    }
});

// Calculate work order total amount (main page)
function calculateWorkOrderTotal(){
    var qty = parseFloat($('#work_order_item_qty').val()) || 0;
    var price = parseFloat($('#work_order_item_price').val()) || 0;
    var total = qty * price;
    $('#work_order_total_amount').val(total.toFixed(2));
}
function OnChangeSelectpaty(vendor_id){
    try {
        $.ajax({
            url: "ajax.php",
            data:'page=getdynamicinviceparty&transaction_table=vendors&party_id='+vendor_id,
            type: "POST",
            success:function(data){
                var resul = JSON.parse(data);
                var request_warehouse_id = "<option value=''>Select Any one</option>";
                var vendor_warehouse = resul.vendor_warehouse;
                $.each(vendor_warehouse, function(i, row) {
                    var selectcheck = "";
                    if(row.default_warehouse == "0"){
                        var selectcheck = "selected";
                    }
                    request_warehouse_id += "<option value='"+row.id+"' "+selectcheck+">"+row.warehouse_name+"</option>"
                })
                $("#party_warehouse_id").html(request_warehouse_id);
                initializeSelectorsAndTooltips();
            },
            error: function(xhr, status, error) {
                console.error("Error loading vendor warehouses:", error);
            }
        });
    }catch(error){
        console.error("Error loading vendor warehouses:", error);
    }
}


function selectWorkoOderItemID(work_order_item_id){
    $.ajax({
        url: "ajax.php",
        data:'page=getmanufacturingworkorders&work_order_item_id='+work_order_item_id+'&gettype=SELECTWORKOODERITEMID',
        type: "POST",
        success:function(data){
            var res = JSON.parse(data);
            var boms = res.SELECTWORKOODERITEMID;
            var bomoptions = '<option value="">Select Any one</option>';
            var seq_version = 1;
            $.each(boms, function(index, bom) {
              bomoptions += `<option value="${bom.id}">
               V${seq_version} - ${bom.transaction_date} - ${bom.uom_name} (${bom.bom_quantity})
              </option>`;
              seq_version = seq_version+1;
            });
            $("#manufacturing_bom_id").html(bomoptions);
            initializeSelectorsAndTooltips();
        },
        error: function(xhr, status, error) {
            console.error("Error loading BOMs:", error);
        }
    });
}

function selectmanufacturingBOM(){
    var manufacturing_bom_id = $("#manufacturing_bom_id").val();
    var work_order_item_qty = $("#work_order_item_qty").val();
     $.ajax({
        url: "views/custom_insert_subcontract_work_order_ajax.php",
        data:'manufacturing_bom_id='+manufacturing_bom_id+'&work_order_item_qty='+work_order_item_qty+'&gettype=SELECTMANUFACTURINGBOM',
        type: "POST",
        success:function(data){
            $("#Manufacturing_Work_Orders_ajax").html(data);
            initializeSelectorsAndTooltips();
            // Initialize calculations for all loaded rows
            initializeBomItemCalculations();
        },
        error: function(xhr, status, error) {
            console.error("Error loading BOM items:", error);
        }
    });

}

// Initialize BOM item calculations after AJAX load
function initializeBomItemCalculations(){
    setTimeout(function(){
        $('input[name="request_qty[]"]').each(function(index){
            var seqNum = $(this).closest('tr').find('input.manufacturing_bom_items_seq_no').val();
            if(seqNum && typeof calculateItemTotal === 'function'){
                calculateItemTotal(seqNum);
            }
        });
        // Re-setup the work order qty listener
        setupWorkOrderQtyListener();
    }, 100);
}

function removerows(rowId){
    $("#" + rowId).remove();
}
// visitor_out_view.js
(function($) {
    const $list = $('#lugList');

    function updateRowUI($row) {
        const isReturnable = $row.data('returnable') === 1 || $row.data('returnable') === '1';
        if (!isReturnable) return;
        const $chk = $row.find('.mark-returned');
        const $wrap = $row.find('.lug-return-wrap');
        $wrap.toggle($chk.is(':checked'));
    }

    function summary() {
        const total = $list.find('.lug-item').length;
        const ret = $list.find('.lug-item[data-returnable="1"]').length;
        const sel = $list.find('.mark-returned:checked').length;
        $('#lugSummary').text(`Total: ${total} • Returnable: ${ret} • Selected to return: ${sel}`);
    }

    if ($list.length) {
        $list.on('change', '.mark-returned', function() {
            const $row = $(this).closest('.lug-item');
            updateRowUI($row);
            summary();
        });

        // Initialize all rows (hide date/remarks until checked)
        $list.find('.lug-item').each(function() {
            updateRowUI($(this));
        });
        summary();
    }

    // Mark all returnables as returned (today)
    $('#markAllReturnable').on('click', function() {
        const today = new Date().toISOString().slice(0, 10);
        $list.find('.lug-item[data-returnable="1"]').each(function() {
            const $row = $(this);
            $row.find('.mark-returned').prop('checked', true);
            $row.find('.returned-at').val(today);
            updateRowUI($row);
        });
        summary();
    });

    // Clear selections
    $('#clearReturnSelections').on('click', function() {
        $list.find('.mark-returned').prop('checked', false);
        $list.find('.lug-return-wrap').hide();
        summary();
    });

    // Sync Pass UI toggles
    function syncPassUI() {
        const isChecked = $('#passReturnedChk').is(':checked');
        if (isChecked) {
            $('#passReturnDetailsWrap').slideDown();
        } else {
            $('#passReturnDetailsWrap').slideUp();
        }
    }

    // =================== MATERIAL OUT LOGGING ===================
    const ITEM_SETS = {
        visitor: [
            { key: 'laptop', label: 'Laptop', icon: 'fas fa-laptop' },
            { key: 'mobile', label: 'Mobile', icon: 'fas fa-mobile-alt' },
            { key: 'tablet', label: 'Tablet', icon: 'fas fa-tablet-alt' },
            { key: 'camera', label: 'Camera', icon: 'fas fa-camera' },
            { key: 'headphones', label: 'Headphones', icon: 'fas fa-headphones' },
            { key: 'pendrive', label: 'Pen Drive', icon: 'fab fa-usb' },
            { key: 'harddisk', label: 'Hard Disk', icon: 'fas fa-hdd' },
            { key: 'documents', label: 'Documents', icon: 'fas fa-file-alt' },
            { key: 'samplebox', label: 'Sample Box', icon: 'fas fa-archive' },
            { key: 'backpack', label: 'Backpack', icon: 'fas fa-briefcase' }
        ],
        delivery: [
            { key: 'courier', label: 'Courier Box', icon: 'fas fa-cube' },
            { key: 'food', label: 'Food Parcel', icon: 'fas fa-utensils' },
            { key: 'supplies', label: 'Office Supplies', icon: 'fas fa-pencil-alt' },
            { key: 'spares', label: 'Spare Parts', icon: 'fas fa-cogs' },
            { key: 'returnship', label: 'Return Shipment', icon: 'fas fa-undo' },
            { key: 'crate', label: 'Crate', icon: 'fas fa-archive' },
            { key: 'envelope', label: 'Envelope', icon: 'far fa-envelope' },
            { key: 'drum', label: 'Drum', icon: 'fas fa-database' },
            { key: 'trolley', label: 'Trolley', icon: 'fas fa-shopping-cart' },
            { key: 'pallet', label: 'Pallet', icon: 'fas fa-tasks' }
        ],
        service: [
            { key: 'toolkit', label: 'Tool Kit', icon: 'fas fa-wrench' },
            { key: 'laptop', label: 'Laptop', icon: 'fas fa-laptop' },
            { key: 'ladder', label: 'Ladder', icon: 'fas fa-level-up-alt' },
            { key: 'safety', label: 'Safety Gear', icon: 'fas fa-shield-alt' },
            { key: 'cleaning', label: 'Cleaning Kit', icon: 'fas fa-tint' },
            { key: 'camera', label: 'Inspection Cam', icon: 'fas fa-camera' },
            { key: 'spares', label: 'Spare Parts', icon: 'fas fa-cogs' },
            { key: 'ac', label: 'Air Conditioner', icon: 'far fa-snowflake' },
            { key: 'lights', label: 'Lights', icon: 'far fa-lightbulb' },
            { key: 'fan', label: 'Fan', icon: 'fas fa-sync-alt' },
            { key: 'pc', label: 'PC/Desktop', icon: 'fas fa-desktop' },
            { key: 'printer', label: 'Printer', icon: 'fas fa-print' },
            { key: 'router', label: 'Router', icon: 'fas fa-wifi' },
            { key: 'projector', label: 'Projector', icon: 'fas fa-video' }
        ],
        employee: [
            { key: 'companylaptop', label: 'Company Laptop', icon: 'fas fa-laptop' },
            { key: 'monitor', label: 'Monitor', icon: 'fas fa-desktop' },
            { key: 'keyboard', label: 'Keyboard', icon: 'far fa-keyboard' },
            { key: 'mouse', label: 'Mouse', icon: 'fas fa-mouse-pointer' },
            { key: 'headset', label: 'Headset', icon: 'fas fa-headphones' },
            { key: 'idcard', label: 'Access Card', icon: 'far fa-id-card' },
            { key: 'backpack', label: 'Backpack', icon: 'fas fa-shopping-bag' },
            { key: 'docs', label: 'Documents', icon: 'fas fa-file-alt' },
            { key: 'demo', label: 'Demo Kit', icon: 'fas fa-suitcase' },
            { key: 'samples', label: 'Samples', icon: 'fas fa-flask' }
        ],
        others: [
            { key: 'chair', label: 'Chair', icon: 'fas fa-wheelchair' },
            { key: 'art', label: 'Artwork', icon: 'far fa-image' },
            { key: 'music', label: 'Musical', icon: 'fas fa-music' },
            { key: 'sports', label: 'Sports Gear', icon: 'fas fa-trophy' },
            { key: 'food', label: 'Food', icon: 'fas fa-utensils' },
            { key: 'plants', label: 'Plants', icon: 'fas fa-leaf' },
            { key: 'electronics', label: 'Electronics', icon: 'fas fa-plug' },
            { key: 'box', label: 'Box', icon: 'fas fa-cube' },
            { key: 'bag', label: 'Bag', icon: 'fas fa-shopping-bag' },
            { key: 'misc', label: 'Misc', icon: 'fas fa-ellipsis-h' }
        ]
    };

    const $scroller = $('#outLuggageScroller');
    const $outList = $('#outLuggageList');
    const $section = $('#outLuggageSection');
    
    const modalEl = document.getElementById('luggageModalOut');
    let outModal = null;
    if (modalEl) {
        outModal = new bootstrap.Modal(modalEl);
    }

    let OUT_LUG_IDX = 0;
    let itemCounts = {};

    function getVisitType() {
        return $('input[name="visit_type"]').val() || 'visitor';
    }

    function pillHtml(it) {
        const act = itemCounts[it.key] > 0 ? 'active' : '';
        return `
            <div class="lug-pill ${act}" data-item-key="${it.key}" data-item-label="${it.label}">
                <div class="lug-img"><i class="${it.icon}"></i></div>
                <div class="lug-label">${it.label}</div>
            </div>`;
    }

    function renderPills() {
        const vt = getVisitType();
        const items = ITEM_SETS[vt] || ITEM_SETS.visitor;
        $scroller.html(items.map(pillHtml).join(''));
    }

    function updateCount() {
        const n = $outList.find('.lug-item').length;
        $('#outLugCountBadge').text(`${n} item${n===1?'':'s'}`);
    }

    const $yes = $('#out_lug_yes');
    const $no = $('#out_lug_no');

    function toggleOutLuggage() {
        if ($yes.is(':checked')) {
            $section.show().removeClass('d-none').addClass('d-view-block');
            renderPills();
        } else {
            if (outModal) outModal.hide();
            $section.hide().removeClass('d-view-block').addClass('d-none');
            $outList.empty();
            itemCounts = {};
            renderPills();
            updateCount();
        }
    }
    
    $yes.on('change', toggleOutLuggage);
    $no.on('change', toggleOutLuggage);

    $scroller.on('click', '.lug-pill', function() {
        if (!$yes.is(':checked')) return;
        const key = this.dataset.itemKey;
        const label = this.dataset.itemLabel;

        $('#lm_out_item_key').val(key);
        $('#lm_out_item_label').val(label);
        $('#lm_out_brand_name').val('');
        $('#lm_out_qty').val(1);
        $('#lm_out_serial_or_remarks').val('');
        $('#lm_out_returnable_yes').prop('checked', true);
        $('#lm_out_returnable_no').prop('checked', false);
        $('#lm_out_return_date').val(new Date().toISOString().slice(0, 10));
        $('#lm_out_return_date_wrap').show().removeClass('d-none').addClass('d-view-block');

        if (outModal) outModal.show();
    });

    $('input[name="lm_out_returnable"]').on('change', function() {
        if ($('#lm_out_returnable_yes').is(':checked')) {
            $('#lm_out_return_date_wrap').show().removeClass('d-none').addClass('d-view-block');
        } else {
            $('#lm_out_return_date_wrap').hide().removeClass('d-view-block').addClass('d-none');
        }
    });

    $('#lm_out_save_btn').on('click', function() {
        const item_key = $('#lm_out_item_key').val();
        const item_label = $('#lm_out_item_label').val() || 'Item';
        const brand = $('#lm_out_brand_name').val();
        const qty = parseInt($('#lm_out_qty').val() || '1', 10);
        const ser = $('#lm_out_serial_or_remarks').val();
        const returnable = $('input[name="lm_out_returnable"]:checked').val() === 'yes' ? 1 : 0;
        const retDate = returnable ? ($('#lm_out_return_date').val() || '') : '';

        if (!item_key) {
            toast('Select an item.');
            return;
        }
        if (!qty || qty < 1) {
            toast('Quantity must be at least 1.');
            return;
        }

        const rowKey = 'out_' + (OUT_LUG_IDX++);
        const rowId = 'out_lug_row_' + rowKey;

        const html = `
            <div class="lug-item" id="${rowId}" data-item-key="${item_key}">
                <div class="lug-meta">
                    <strong>${item_label}</strong>
                    <div>Brand: ${brand ? brand : '-'}</div>
                    <div>Qty: ${qty}</div>
                    <div>Serial/Remarks: ${ser ? ser : '-'}</div>
                    <div>Returnable: ${returnable ? 'yes' +(retDate ? ' — ' +  retDate : '') : 'no'}</div>
                </div>
                <div class="lug-actions">
                    <button type="button" class="btn btn-link text-danger p-0 js-remove-out-lug" data-row-id="${rowId}" data-item-key="${item_key}">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
                <div class="d-none">
                    <input type="hidden" name="out_luggage[${rowKey}][item_key]" value="${item_key}">
                    <input type="hidden" name="out_luggage[${rowKey}][item_name]" value="${item_label}">
                    <input type="hidden" name="out_luggage[${rowKey}][brand_name]" value="${brand}">
                    <input type="hidden" name="out_luggage[${rowKey}][qty]" value="${qty}">
                    <input type="hidden" name="out_luggage[${rowKey}][serial_or_remarks]" value="${ser}">
                    <input type="hidden" name="out_luggage[${rowKey}][returnable]" value="${returnable}">
                    ${returnable ? `<input type="hidden" name="out_luggage[${rowKey}][return_date]" value="${retDate}">` : ``}
                    <input type="hidden" name="out_luggage[${rowKey}][vistor_type]" value="OUT">
                </div>
            </div>
        `;
        $outList.append(html);

        itemCounts[item_key] = (itemCounts[item_key] || 0) + 1;
        $scroller.find(`.lug-pill[data-item-key="${item_key}"]`).addClass('active');

        updateCount();
        if (outModal) outModal.hide();
    });

    $(document).on('click', '.js-remove-out-lug', function () {
        const rowId = $(this).data('row-id');
        const itemKey = $(this).data('item-key');

        $('#' + rowId).remove();

        if (itemCounts[itemKey]) {
            itemCounts[itemKey]--;
            if (itemCounts[itemKey] <= 0) {
                delete itemCounts[itemKey];
                $('#outLuggageScroller').find(`.lug-pill[data-item-key="${itemKey}"]`).removeClass('active');
            }
        }
        updateCount();
    });

    // =================== GUIDED WIZARD NAVIGATION & VALIDATION ===================
    var currentStep = 1;
    var maxStep = 1;
    $('.checkout-slide').each(function() {
        var step = parseInt($(this).data('step'), 10);
        if (step > maxStep) maxStep = step;
    });

    function showStep(step) {
        $('.checkout-slide').removeClass('active').hide();
        $(`.checkout-slide[data-step="${step}"]`).addClass('active').fadeIn();
        
        // Update progress indicators
        $('.checkout-step-progress .step-progress-step').removeClass('active completed');
        $('.checkout-step-progress .step-progress-step').each(function() {
            var s = parseInt($(this).data('step'), 10);
            if (s < step) {
                $(this).addClass('completed');
            } else if (s === step) {
                $(this).addClass('active');
            }
        });
        
        // Fill indicator track width
        var pct = ((step - 1) / (maxStep - 1)) * 100;
        $('#checkoutStepProgressBarFill').css('width', pct + '%');
        
        // Prev/Next buttons
        if (step === 1) {
            $('.js-checkout-prev').hide();
        } else {
            $('.js-checkout-prev').show();
        }
        
        if (step === maxStep) {
            $('.js-checkout-next').hide();
        } else {
            $('.js-checkout-next').show();
        }

        // Summary details update on last step
        if (step === maxStep) {
            updateCheckoutSummary();
        }
        
        currentStep = step;
    }

    function validateCheckoutStep(step) {
        if (step === 1) {
            var gate = $('#out_gate_no').val();
            if (!gate || gate === '0') {
                toast('Please select an Exit Gate');
                return false;
            }
            var outTime = $('#out_datetime').val();
            if (!outTime) {
                toast('Please enter Out Date & Time');
                return false;
            }
            return true;
        }
        if (step === 2 && $('#checkout-slide-2').length > 0) {
            // Badge return step validation
            return true;
        }
        if (step === 3 || (step === 2 && $('#checkout-slide-2').length === 0)) {
            // Materials Log validation
            const takingOutVal = ($('input[name="taking_out"]:checked').val() || "").toLowerCase();
            if (takingOutVal === "yes") {
                const outCount = $("#outLuggageList .lug-item").length;
                if (outCount === 0) {
                    toast("Please add at least one item to take OUT");
                    return false;
                }
            }
            return true;
        }
        return true;
    }

    function updateCheckoutSummary() {
        // 1) Exit Gate
        var gateName = $('.checkout-gate-select-grid .gate-select-card.active .gate-name').text().trim() || 'Not selected';
        $('#summary-exit-gate').html('<strong>' + gateName + '</strong>');

        // 2) Exit Time
        var exitTime = $('#out_datetime').val();
        if (exitTime) {
            var dt = new Date(exitTime);
            $('#summary-exit-time').text(dt.toLocaleString('en-IN', { dateStyle: 'medium', timeStyle: 'short' }));
        } else {
            $('#summary-exit-time').text('—');
        }

        // 3) Exit Mode
        var mode = $('#exit_mode').val() || 'person';
        var modeText = mode === 'vehicle' ? 'Exiting with Vehicle' : 'Exiting as Person';
        var modeIcon = mode === 'vehicle' ? '<i class="fa fa-truck text-primary me-1"></i> ' : '<i class="fa fa-walking text-primary me-1"></i> ';
        $('#summary-exit-mode').html(modeIcon + modeText);

        // 4) Badge Return (if applicable)
        if ($('#checkout-slide-2').length > 0) {
            var isReturned = $('#passReturnedChk').is(':checked');
            var returnRemarks = $('input[name="pass_return[remarks]"]').val().trim();
            var valHtml = isReturned 
                ? '<span class="text-success fw-bold"><i class="fa fa-check-circle me-1"></i> Returned</span>'
                : '<span class="text-danger fw-bold"><i class="fa fa-exclamation-triangle me-1"></i> Missing / Not Returned</span>';
            if (isReturned && returnRemarks) {
                valHtml += ' <span class="text-muted font-italic">(' + returnRemarks + ')</span>';
            }
            $('#summary-badge-returned').html(valHtml);
        }

        // 5) Materials Logged
        var materialsText = '';
        if ($('#vendorSupplierLugList').length > 0) {
            var items = [];
            $('#vendorSupplierLugList tbody tr').each(function() {
                var itemName = $(this).find('td strong').text().trim();
                var leftQty = $(this).find('input[name*="[left_qty]"]').val();
                var leftRemarks = $(this).find('input[name*="[left_remarks]"]').val().trim();
                var itemDesc = itemName + ' (Left Qty: ' + leftQty + ')';
                if (leftRemarks) itemDesc += ' [' + leftRemarks + ']';
                items.push(itemDesc);
            });
            materialsText = items.join(', ') || 'No materials left in vehicle.';
        } else {
            var takingOut = $('input[name="taking_out"]:checked').val() === 'yes';
            if (takingOut) {
                var loggedItems = [];
                $('#outLuggageList .lug-item').each(function() {
                    var metaText = $(this).find('.lug-meta strong').text().trim();
                    loggedItems.push(metaText);
                });
                materialsText = 'Outgoing: ' + loggedItems.join(', ') || 'No items logged.';
            } else {
                materialsText = 'No items logged to take out.';
            }
        }
        $('#summary-materials-logged').html('<i class="fa fa-boxes text-secondary me-1"></i> ' + materialsText);
    }

    // Bind prev/next buttons
    $('.js-checkout-next').off('click').on('click', function() {
        if (validateCheckoutStep(currentStep)) {
            showStep(currentStep + 1);
        }
    });

    $('.js-checkout-prev').off('click').on('click', function() {
        showStep(currentStep - 1);
    });

    // =================== VISUAL CARDS INTERACTIVE BINDINGS ===================

    // 1) Exit Gate Card Clicks
    $(document).off('click', '.checkout-gate-select-grid .gate-select-card').on('click', '.checkout-gate-select-grid .gate-select-card', function() {
        var gateId = $(this).data('gate-id');
        $('.checkout-gate-select-grid .gate-select-card').removeClass('active');
        $(this).addClass('active');
        $('#out_gate_no').val(gateId).trigger('change');
    });

    // 2) Exit Mode Card Clicks
    $(document).off('click', '.exit-mode-card').on('click', '.exit-mode-card', function() {
        var mode = $(this).data('mode');
        $('.exit-mode-card').removeClass('active');
        $(this).addClass('active');
        $('#exit_mode').val(mode);
    });

    // 3) Badge Return Card Clicks
    $(document).off('click', '.badge-return-card').on('click', '.badge-return-card', function() {
        var returned = $(this).data('returned') == '1';
        $('.badge-return-card').removeClass('active');
        $(this).addClass('active');
        $('#passReturnedChk').prop('checked', returned).trigger('change');
        syncPassUI();
    });

    // 4) Taking Out Items Yes/No Cards Clicks
    $(document).off('click', '.taking-out-card').on('click', '.taking-out-card', function() {
        var val = $(this).data('val');
        $('.taking-out-card').removeClass('active');
        $(this).addClass('active');
        if (val === 'yes') {
            $('#out_lug_yes').prop('checked', true).trigger('change');
        } else {
            $('#out_lug_no').prop('checked', true).trigger('change');
        }
        toggleOutLuggage();
    });

    // Initialize View
    syncPassUI();
    toggleOutLuggage();
    showStep(1);

})(window.jQuery);

function toast(msg) {
    if (typeof tostarnotify === 'function') {
        tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger',
            'animated flipInX', 'animated flipOutX', msg, '');
    } else {
        alert(msg);
    }
}

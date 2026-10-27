// visitor_out_view.js

(function() {
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

})();

$(function () {

    const $chk = $('.js-pass-return-toggle');
    const $wrap = $('.js-pass-return-wrap');

    if (!$chk.length || !$wrap.length) return;

    function syncPassUI() {
        $wrap.toggle($chk.is(':checked'));
    }

    syncPassUI();
    $(document).on('change', '.js-pass-return-toggle', syncPassUI);

});

    (function() {
        // =================== MATERIAL SETS BY VISIT TYPE (with icons) ===================
        const ITEM_SETS = {
            visitor: [{
                    key: 'laptop',
                    label: 'Laptop',
                    icon: 'fa-laptop'
                },
                {
                    key: 'mobile',
                    label: 'Mobile',
                    icon: 'fa-mobile-screen'
                },
                {
                    key: 'tablet',
                    label: 'Tablet',
                    icon: 'fa-tablet-screen-button'
                },
                {
                    key: 'camera',
                    label: 'Camera',
                    icon: 'fa-camera'
                },
                {
                    key: 'headphones',
                    label: 'Headphones',
                    icon: 'fa-headphones'
                },
                {
                    key: 'pendrive',
                    label: 'Pen Drive',
                    icon: 'fa-usb'
                },
                {
                    key: 'harddisk',
                    label: 'Hard Disk',
                    icon: 'fa-hard-drive'
                },
                {
                    key: 'documents',
                    label: 'Documents',
                    icon: 'fa-file-lines'
                },
                {
                    key: 'samplebox',
                    label: 'Sample Box',
                    icon: 'fa-box-open'
                },
                {
                    key: 'backpack',
                    label: 'Backpack',
                    icon: 'fa-bag-shopping'
                }
            ],
            delivery: [{
                    key: 'courier',
                    label: 'Courier Box',
                    icon: 'fa-box'
                },
                {
                    key: 'food',
                    label: 'Food Parcel',
                    icon: 'fa-bowl-food'
                },
                {
                    key: 'supplies',
                    label: 'Office Supplies',
                    icon: 'fa-pencil'
                },
                {
                    key: 'spares',
                    label: 'Spare Parts',
                    icon: 'fa-gears'
                },
                {
                    key: 'returnship',
                    label: 'Return Shipment',
                    icon: 'fa-arrow-rotate-left'
                },
                {
                    key: 'crate',
                    label: 'Crate',
                    icon: 'fa-box-open'
                },
                {
                    key: 'envelope',
                    label: 'Envelope',
                    icon: 'fa-envelope'
                },
                {
                    key: 'drum',
                    label: 'Drum',
                    icon: 'fa-drum-steelpan'
                },
                {
                    key: 'trolley',
                    label: 'Trolley',
                    icon: 'fa-cart-flatbed'
                },
                {
                    key: 'pallet',
                    label: 'Pallet',
                    icon: 'fa-pallet'
                }
            ],
            service: [{
                    key: 'toolkit',
                    label: 'Tool Kit',
                    icon: 'fa-screwdriver-wrench'
                },
                {
                    key: 'laptop',
                    label: 'Laptop',
                    icon: 'fa-laptop'
                },
                {
                    key: 'ladder',
                    label: 'Ladder',
                    icon: 'fa-person-arrow-up-from-line'
                },
                {
                    key: 'safety',
                    label: 'Safety Gear',
                    icon: 'fa-helmet-safety'
                },
                {
                    key: 'cleaning',
                    label: 'Cleaning Kit',
                    icon: 'fa-soap'
                },
                {
                    key: 'camera',
                    label: 'Inspection Cam',
                    icon: 'fa-camera'
                },
                {
                    key: 'spares',
                    label: 'Spare Parts',
                    icon: 'fa-gears'
                },
                {
                    key: 'ac',
                    label: 'Air Conditioner',
                    icon: 'fa-snowflake'
                },
                {
                    key: 'lights',
                    label: 'Lights',
                    icon: 'fa-lightbulb'
                },
                {
                    key: 'fan',
                    label: 'Fan',
                    icon: 'fa-fan'
                },
                {
                    key: 'pc',
                    label: 'PC/Desktop',
                    icon: 'fa-desktop'
                },
                {
                    key: 'printer',
                    label: 'Printer',
                    icon: 'fa-print'
                },
                {
                    key: 'router',
                    label: 'Router',
                    icon: 'fa-wifi'
                },
                {
                    key: 'projector',
                    label: 'Projector',
                    icon: 'fa-video'
                }
            ],
            employee: [{
                    key: 'companylaptop',
                    label: 'Company Laptop',
                    icon: 'fa-laptop'
                },
                {
                    key: 'monitor',
                    label: 'Monitor',
                    icon: 'fa-desktop'
                },
                {
                    key: 'keyboard',
                    label: 'Keyboard',
                    icon: 'fa-keyboard'
                },
                {
                    key: 'mouse',
                    label: 'Mouse',
                    icon: 'fa-computer-mouse'
                },
                {
                    key: 'headset',
                    label: 'Headset',
                    icon: 'fa-headphones'
                },
                {
                    key: 'idcard',
                    label: 'Access Card',
                    icon: 'fa-id-card'
                },
                {
                    key: 'backpack',
                    label: 'Backpack',
                    icon: 'fa-bag-shopping'
                },
                {
                    key: 'docs',
                    label: 'Documents',
                    icon: 'fa-file-lines'
                },
                {
                    key: 'demo',
                    label: 'Demo Kit',
                    icon: 'fa-suitcase-rolling'
                },
                {
                    key: 'samples',
                    label: 'Samples',
                    icon: 'fa-flask'
                }
            ],
            others: [{
                    key: 'chair',
                    label: 'Chair',
                    icon: 'fa-chair'
                },
                {
                    key: 'art',
                    label: 'Artwork',
                    icon: 'fa-image'
                },
                {
                    key: 'music',
                    label: 'Musical',
                    icon: 'fa-guitar'
                },
                {
                    key: 'sports',
                    label: 'Sports Gear',
                    icon: 'fa-medal'
                },
                {
                    key: 'food',
                    label: 'Food',
                    icon: 'fa-burger'
                },
                {
                    key: 'plants',
                    label: 'Plants',
                    icon: 'fa-seedling'
                },
                {
                    key: 'electronics',
                    label: 'Electronics',
                    icon: 'fa-bolt'
                },
                {
                    key: 'box',
                    label: 'Box',
                    icon: 'fa-box'
                },
                {
                    key: 'bag',
                    label: 'Bag',
                    icon: 'fa-briefcase'
                },
                {
                    key: 'misc',
                    label: 'Misc',
                    icon: 'fa-ellipsis'
                }
            ]
        };

        // =================== STATE ===================
        const $scroller = $('#outLuggageScroller');
        const $list = $('#outLuggageList');
        const $section = $('#outLuggageSection');
        const outModal = new bootstrap.Modal(document.getElementById('luggageModalOut'));

        let OUT_LUG_IDX = 0;
        let itemCounts = {};

        function getVisitType() {
            const el = document.querySelector('input[name="visit_type"]:checked');
            return el ? el.value : 'visitor';
        }

        function pillHtml(it) {
            const act = itemCounts[it.key] > 0 ? 'active' : '';
            return `
      <div class="lug-pill ${act}" data-item-key="${it.key}" data-item-label="${it.label}">
        <div class="lug-img"><i class="fa-solid ${it.icon}"></i></div>
        <div class="lug-label">${it.label}</div>
      </div>`;
        }

        function renderPills() {
            const vt = getVisitType();
            const items = ITEM_SETS[vt] || ITEM_SETS.visitor;
            $scroller.html(items.map(pillHtml).join(''));
        }

        function updateCount() {
            const n = $list.find('.lug-item').length;
            $('#outLugCountBadge').text(`${n} item${n===1?'':'s'}`);
        }

        // NEW: toggle visibility based on radios
        const $yes = $('#out_lug_yes');
        const $no = $('#out_lug_no');

        function toggleOutLuggage() {
            if ($yes.is(':checked')) {
                $section.show();
                renderPills(); // ensure pills for current type are visible
            } else {
                // hide & reset when user says "No"
                outModal.hide();
                $section.hide();
                $list.empty();
                itemCounts = {};
                renderPills(); // clears active state
                updateCount();
            }
        }
        $yes.on('change', toggleOutLuggage);
        $no.on('change', toggleOutLuggage);
        toggleOutLuggage(); // initialize

        // click on pill → open modal ONLY if "Yes"
        $scroller.on('click', '.lug-pill', function() {
            if (!$yes.is(':checked')) return; // guard
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
            $('#lm_out_return_date_wrap').show();

            outModal.show();
        });

        // type change → if "Yes", re-render; if "No", we keep hidden anyway
        document.querySelectorAll('input[name="visit_type"]').forEach(r => {
            r.addEventListener('change', () => {
                itemCounts = {};
                $list.empty();
                renderPills();
                updateCount();
            });
        });

        // returnable toggle (unchanged)
        $('input[name="lm_out_returnable"]').on('change', function() {
            if ($('#lm_out_returnable_yes').is(':checked')) $('#lm_out_return_date_wrap').show();
            else $('#lm_out_return_date_wrap').hide();
        });

        // save from modal (unchanged except using outModal.hide())
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
          <strong>${(item_label)}</strong>
          <div>Brand: ${brand ? (brand) : '-'}</div>
          <div>Qty: ${qty}</div>
          <div>Serial/Remarks: ${ser ? (ser) : '-'}</div>
          <div>Returnable: ${returnable ? 'yes' +(retDate ? ' — ' +  (retDate) : '') : 'no'}</div>
        </div>
        <div class="lug-actions">
          <button type="button"
                    class="btn btn-link text-danger p-0 js-remove-out-lug"
                    data-row-id="${rowId}"
                    data-item-key="${item_key}">
                <i class="icofont icofont-trash"></i>
            </button>

        </div>
        <div class="d-none">
          <input type="hidden" name="out_luggage[${rowKey}][item_key]" value="${(item_key)}">
          <input type="hidden" name="out_luggage[${rowKey}][item_name]" value="${(item_label)}">
          <input type="hidden" name="out_luggage[${rowKey}][brand_name]" value="${(brand)}">
          <input type="hidden" name="out_luggage[${rowKey}][qty]" value="${qty}">
          <input type="hidden" name="out_luggage[${rowKey}][serial_or_remarks]" value="${(ser)}">
          <input type="hidden" name="out_luggage[${rowKey}][returnable]" value="${returnable}">
          ${returnable ? `<input type="hidden" name="out_luggage[${rowKey}][return_date]" value="${(retDate)}">` : ``}
          <input type="hidden" name="out_luggage[${rowKey}][vistor_type]" value="OUT">
        </div>
      </div>
    `;
            $list.append(html);

            // keep pill active (blue)
            itemCounts[item_key] = (itemCounts[item_key] || 0) + 1;
            $scroller.find(`.lug-pill[data-item-key="${item_key}"]`).addClass('active');

            updateCount();
            outModal.hide();
        });

        // expose remover (unchanged)
        $(document).on('click', '.js-remove-out-lug', function () {
            const rowId = $(this).data('row-id');
            const itemKey = $(this).data('item-key');

            $('#' + rowId).remove();

            if (window.itemCounts && window.itemCounts[itemKey]) {
                window.itemCounts[itemKey]--;
                if (window.itemCounts[itemKey] <= 0) {
                    delete window.itemCounts[itemKey];
                    $('#outLuggageScroller')
                        .find(`.lug-pill[data-item-key="${itemKey}"]`)
                        .removeClass('active');
                }
            }

            updateCount?.();
        });


        // initial count
        updateCount();
    })();

    // ====== REQUIRED: your existing toast(msg) ======
    function toast(msg) {
        tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger',
            'animated flipInX', 'animated flipOutX', msg, '');
    }
    // Smooth focus helper (optional)
    function focusEl($el) {
        if (!$el || !$el.length) return;
        try {
            $el[0].scrollIntoView({
                behavior: "smooth",
                block: "center"
            });
        } catch (e) {}
        setTimeout(() => $el.trigger?.("focus"), 30);
    }

    function validateVisitorFom() {
        const $passBox = $("#passBox"); // exists when pass applies
        const passRequired = $passBox.length > 0; // only validate if block is present
        if (passRequired) {
            const alreadyReturned = String($passBox.data("already-returned")) === "1";
            const isChecked = $("#passReturnedChk").is(":checked");
            if (!alreadyReturned && !isChecked) {
                toast('Please collect the Visitor Pass / ID Card and tick "Pass Returned?"');
                focusEl($("#passReturnedChk"));
                return false; // hard block
            }
        }

        const takingOutVal = ($('input[name="taking_out"]:checked').val() || "").toLowerCase();
        if (takingOutVal === "yes") {
            const outCount = $("#outLuggageList .lug-item").length;
            if (outCount === 0) {

                toast("Please add at least one item in 'Items being taken OUT now'");
                focusEl($("#outLuggageSection"));
                return false; // hard block
            }
        }
        return true; // all good
    }


    $(document).on('submit', '[data-name="visitorOutForm"]', function (e) {
        if (!validateVisitorFom()) {
            e.preventDefault();
            return false;
        }
    });

// custom_insert_visitor_logs.js
// visitors
(function() {
    console.log('3');
    // Initial show if editing with saved tags
    if ($('#service_tag_enable').is(':checked')) {
        document.getElementById('serviceTagControls').classList.remove('d-none');
        document.getElementById('serviceTagControls').classList.add('d-view-block');
    }

    // Order-preserving IDs pulled from hidden inputs you already write
    function readSavedIdsInOrder() {
        var ids = [];
        $('#serviceTagHiddenInputs input[name$="[visitor_id]"]').each(function() {
            var v = $(this).val();
            if (v) ids.push(v);
        });
        return ids;
    }
    // ===== Keep your simplified generator =====
    function fetchNextVisitorIds(count, afterCode) {
        return new Promise(function(resolve) {
            resolve(localGenerateIds(count, afterCode));
        });
    }

    function localGenerateIds(count, afterCode) {
        var m = afterCode.match(/^(.*?)(\d+)$/);
        if (!m) {
            var out = [];
            for (var i = 1; i <= count; i++) {
                out.push(afterCode + '-' + i);
            }
            return out;
        }
        var prefix = m[1],
            numStr = m[2];
        var base = parseInt(numStr, 10);
        var pad = numStr.length;
        var out = [];
        for (var j = 1; j <= count; j++) {
            var next = (base + j).toString().padStart(pad, '0');
            out.push(prefix + next);
        }
        return out;
    }

    // ===== Helpers for saved state =====
    function readSavedTagsMap() {
        // returns map by visitor_id -> name from hidden inputs
        var map = {};
        $('#serviceTagHiddenInputs input[name$="[visitor_id]"]').each(function() {
            var vid = $(this).val();
            var idx = (this.name.match(/\[(\d+)\]/) || [, '0'])[1];
            var name = $('#serviceTagHiddenInputs input[name="service_tags[' + idx + '][name]"]').val() || '';
            map[vid] = name;
        });
        return map;
    }

    function writeHiddenInputs(rows) { // rows = [{name, visitor_id}]
        var $wrap = $('#serviceTagHiddenInputs');
        $wrap.empty();
        rows.forEach(function(r, i) {
            // Create safely (avoid manual escaping)
            $('<input>', {
                type: 'hidden',
                name: 'service_tags[' + i + '][name]',
                value: r.name
            }).appendTo($wrap);
            $('<input>', {
                type: 'hidden',
                name: 'service_tags[' + i + '][visitor_id]',
                value: r.visitor_id
            }).appendTo($wrap);
        });
        // update small summary badge
        if (rows.length) {
            const el = document.getElementById('serviceTagSummary');
            el.textContent = 'Saved: ' + rows.length;
            el.classList.remove('d-none');
            el.classList.add('d-inline-show');
        } else {
            const el = document.getElementById('serviceTagSummary');
            el.classList.remove('d-inline-show');
            el.classList.add('d-none');
        }

    }

    function resetTaggingUI() {
        $('#service_tag_count').val('');
        writeHiddenInputs([]); // clears hidden inputs + summary
    }

    // ===== Modal wiring =====
    var stmModalEl = document.getElementById('serviceTagModal');
    var stmModal = new bootstrap.Modal(stmModalEl);

    function renderModalRows(ids) {
        var saved = readSavedTagsMap();
        var $tb = $('#serviceTagTableModal tbody');
        $tb.empty();
        ids.forEach(function(code, idx) {
            var preName = saved[code] || '';
            $tb.append(
                '<tr>' +
                '<td><input type="text" class="form-control" name="stm_name" value="' + (preName || '') + '" placeholder="Enter name"></td>' +
                '<td><input type="text" class="form-control" name="stm_vid" value="' + code + '" readonly></td>' +
                '</tr>'
            );
        });
    }

    // Toggle show/hide controls (unchanged)
    $('#service_tag_enable').on('change', function() {
        if (this.checked) {
            // $('#serviceTagControls').show();
            document.getElementById('serviceTagControls').classList.remove('d-none');
            document.getElementById('serviceTagControls').classList.add('d-view-block');
        } else {
            document.getElementById('serviceTagControls').classList.remove('d-view-block');
            document.getElementById('serviceTagControls').classList.add('d-none');
            resetTaggingUI();
        }
    });

    // Details → open modal with rows
    $('#service_tag_details_btn').off('click').on('click', function() {
        var base = $('#visiting_company_id_service').val() || '';
        var savedIds = readSavedIdsInOrder(); // <-- use saved rows if present
        var n = parseInt($('#service_tag_count').val(), 10);

        if (savedIds.length) {
            // Keep UI in sync with what’s saved
            if (n !== savedIds.length) $('#service_tag_count').val(savedIds.length);

            $('#stm_base_id').text(base || savedIds[0] || '—');
            $('#stm_count').text(savedIds.length);

            // Your existing renderer expects an array of IDs
            renderModalRows(savedIds);

            stmModal.show(); // uses your existing modal instance
            return; // don't generate new IDs
        }

        // No saved rows yet → behave exactly as before
        if (!n || n < 1) {
            toast('Enter a valid count (e.g., 3)');
            return;
        }
        if (!base) {
            toast('Enter the Visiting Id Card No first');
            return;
        }

        fetchNextVisitorIds(n, base).then(function(ids) {
            if (!ids || !ids.length) {
                toast('No IDs available');
                return;
            }
            $('#stm_base_id').text(base);
            $('#stm_count').text(ids.length);
            renderModalRows(ids); // still array of IDs; names will come from your map (empty first time)
            stmModal.show();
        });
    });


    // Modal Save → write hidden inputs and close
    $('#service_tag_save_btn').on('click', function() {
        var rows = [];
        $('#serviceTagTableModal tbody tr').each(function() {
            var name = $(this).find('input[name="stm_name"]').val().trim();
            var vid = $(this).find('input[name="stm_vid"]').val().trim();
            rows.push({
                name: name,
                visitor_id: vid
            });
        });
        writeHiddenInputs(rows);
        stmModal.hide();
    });

})();

//employee
(function() {

    // Initial state for edit mode
    if ($('#emp_temp_enable').is(':checked')) {
        showEmpTempUI(true);
        // show/hide host for xbranch on load
        (function toggleOnLoad() {
            const reason = $('input[name="emp_temp_reason"]:checked').val();
            document.getElementById('empTempHostWrap').classList.toggle('d-none', reason !== 'xbranch');

        })();
    }
    const el = document.getElementById('visitorConfig');
    // Reuse NEXT_BADGE if available from your page bootstrap
    const NEXT_BADGE = el?.dataset.nextBadge
    ? JSON.parse(el.dataset.nextBadge)
    : {};
    function showEmpTempUI(on) {
        document.getElementById('empTempReasonWrap').classList.toggle('d-none', !on);
        document.getElementById('empTempFields').classList.toggle('d-none', !on);
        document.getElementById('empTempVisitorIdWrap').classList.toggle('d-none', !on);

        // Disable normal employee code when temp is on
        // $('#employee_code').prop('disabled', !!on);
        $('#employee_code').prop('disabled', !!on).trigger('change.select2');
        $('#employee_name').prop('disabled', !!on);
        $('#employee_department').prop('disabled', !!on);

        // Show existing Visitor Card if employee + temp ON
        const type = $('input[name="visit_type"]:checked').val();
        if (on && type === 'employee') {
            document.getElementById('visitorCardWrap').classList.remove('d-none');
            document.getElementById('visitorCardWrap').classList.add('d-view-block');
            // If empty, prefill from NEXT_BADGE for speed
            const cur = $('#visiting_company_id_employee_temp').val();
            if (!cur && NEXT_BADGE && NEXT_BADGE.status === 'success') {
                const code = NEXT_BADGE.full_code || '';
                $('#visiting_company_id_employee_temp').val(code);
                $('#visit_card_code').text(code || '—');
            }
        } else {
            document.getElementById('visitorCardWrap').classList.remove('d-view-block');
            document.getElementById('visitorCardWrap').classList.add('d-none');
            // If not in employee temp mode, card visibility falls back to your existing logic elsewhere
            // Do not force-hide here to avoid flicker; main showTypeDiv handles it.
        }
    }

    // Toggle on/off
    $('#emp_temp_enable').on('change', function() {
        $show_id = this.checked;
        showEmpTempUI($show_id);
        // Reason-specific host field
        toggleEmpTempHost();
    });

    function toggleEmpTempHost() {
        const reason = $('input[name="emp_temp_reason"]:checked').val();
        document.getElementById('empTempHostWrap').classList.toggle('d-none', reason !== 'xbranch');

    }

    $('input[name="emp_temp_reason"]').on('change', toggleEmpTempHost);



    // Keep your existing sync… just append the employee temp input selector:
    $('#visiting_company_id_visitor, #visiting_company_id_delivery, #visiting_company_id_service, #visiting_company_id_employee_temp, #visiting_company_id_doctor')
        .on('input', function() {
            $('#visit_card_code').text($(this).val() || '—');
        });

    // Ensure main showTypeDiv considers employee temp for card visibility.
    // If you have the function, tweak its card line to:
    // const isEmpTemp = $('#emp_temp_enable').is(':checked');
    // if (type && (type !== 'employee' || isEmpTemp)) $('#visitorCardWrap').show(); else $('#visitorCardWrap').hide();

})();

document.addEventListener('DOMContentLoaded', function () {
    const luggageList = document.getElementById('luggageList');
    // console.log("A");
    if (!luggageList) return;
    // console.log("B");

    // ================================
    // Prefill luggage logic (edit mode)
    // ================================
    // console.log("B1", luggageList.dataset.hasPrefill);

    if (luggageList.dataset.hasPrefill == '1') {
    // console.log("C");

        // keep your variable exactly same
        window._prefill_lug_idx = 0;
        document.getElementById('lug_yes').checked = true;
        const lugYes = document.getElementById('lug_yes');
        const luggageSection = document.getElementById('luggageSection');
        if (lugYes) lugYes.checked = true;
        if (luggageSection) {
            luggageSection.classList.remove('d-none');
            luggageSection.classList.add('d-view-block');
        }
    }

});




    $(function() {
        // cache DOM
        var $wrap = $('#personPreviewWrap');
        var $img = $('#personPreviewInline');
        var $placeholder = $wrap.find('.photo-placeholder-inline');

        // 1) Delegated change handler (bulletproof)
        $(document).on('change', '#image_path', function() {
            var file = this.files && this.files[0];
            if (!file) {
                // cleared or canceled: hide image, show placeholder
                if ($img.length) $img.hide().attr('src', '');
                if ($placeholder.length) $placeholder.show();
                return;
            }

            // only images
            if (!file.type || file.type.indexOf('image/') !== 0) {
                // not an image – reset UI
                if ($img.length) $img.hide().attr('src', '');
                if ($placeholder.length) $placeholder.text('Not an image').show();
                return;
            }

            var reader = new FileReader();
            reader.onload = function(e) {
                if ($img.length) {
                    $img.attr('src', e.target.result).show();
                }
                if ($placeholder.length) $placeholder.hide();
                // ensure the preview area is visible (if you toggle per type)
                $wrap.show();
            };
            reader.readAsDataURL(file);
        });

        // 2) Optional: if the page loads with an existing value (edit mode),
        // we already set the <img src="..."> in PHP. Make sure visibility is correct:
        if ($img.length && $img.attr('src')) {
            $img.show();
            $placeholder.hide();
        }
    });




    // Toggle "Others" input only
    document.addEventListener('DOMContentLoaded', function() {
        const wrap = document.getElementById('visit_type_other_wrap');
        const radios = document.querySelectorAll('input[name="visit_type"]');

        function toggleOthers() {
            const sel = document.querySelector('input[name="visit_type"]:checked');
            wrap.classList.toggle('d-none', !(sel && sel.value === 'others'));
            wrap.classList.toggle('d-view-block', (sel && sel.value === 'others'));

        }
        radios.forEach(r => r.addEventListener('change', toggleOthers));
        toggleOthers(); // initial
    });



    (function() {
        // radios for "Carrying any items?"
        const yes = document.getElementById('lug_yes');
        const no = document.getElementById('lug_no');
        const section = document.getElementById('luggageSection');

        function toggleLuggage() {
            section.classList.toggle('d-none', !yes.checked);
            section.classList.toggle('d-view-block', yes.checked);

        }
        [yes, no].forEach(r => r.addEventListener('change', toggleLuggage));
        toggleLuggage();

        // Bootstrap modal
        const modalEl = document.getElementById('luggageModal');
        const modal = new bootstrap.Modal(modalEl);

        // Icon sets per visit_type
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
                // ✅ New electronics for service
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

        // Render the scroller for current visit_type
        const scroller = document.getElementById('luggageScroller');
        let itemCounts = {}; // active counts per item_key
        function seedItemCountsFromPrefill() {
            itemCounts = {};
            document.querySelectorAll('#luggageList .lug-item').forEach(row => {
                const k = row.dataset.itemKey;
                if (!k) return;
                itemCounts[k] = (itemCounts[k] || 0) + 1;
            });
        }

        function getVisitType() {
            const sel = document.querySelector('input[name="visit_type"]:checked');
            return sel ? sel.value : 'visitor';
            // default to 'visitor' if none selected
        }

        function renderItemsForVisitType() {
            const vt = getVisitType();
            const items = ITEM_SETS[vt] || ITEM_SETS['visitor'];
            scroller.innerHTML = items.map(it => `
      <div class="lug-pill ${itemCounts[it.key] > 0 ? 'active' : ''}"
           data-item-key="${it.key}" data-item-label="${it.label}">
        <div class="lug-img"><i class="fa-solid ${it.icon}"></i></div>
        <div class="lug-label">${it.label}</div>
      </div>
    `).join('');

            // Bind click to open modal
            scroller.querySelectorAll('.lug-pill').forEach(pill => {
                pill.addEventListener('click', function() {
                    if (!yes.checked) return; // only when luggage=yes
                    document.getElementById('lm_item_key').value = this.dataset.itemKey;
                    document.getElementById('lm_item_label').value = this.dataset.itemLabel;
                    document.getElementById('lm_brand_name').value = '';
                    document.getElementById('lm_qty').value = 1;
                    document.getElementById('lm_serial_or_remarks').value = '';

                    // Returnable default: Yes; show date
                    document.getElementById('lm_returnable_yes').checked = true;
                    document.getElementById('lm_returnable_no').checked = false;
                    // document.getElementById('lm_return_date_wrap').d-view-block.display = '';
                    document.getElementById('lm_return_date_wrap').classList.remove('d-none');
                    document.getElementById('lm_return_date_wrap').classList.add('d-view-block');

                    // date input already has PHP default

                    modal.show();
                });
            });
        }

        // Initial render and on visit_type change
        seedItemCountsFromPrefill();
        renderItemsForVisitType();
        document.querySelectorAll('input[name="visit_type"]').forEach(r => {
            r.addEventListener('change', function() {
                // when type changes, keep counts only for matching keys if you want,
                // or reset; here we reset for clarity
                itemCounts = {};
                document.getElementById('luggageList').innerHTML = '';
                renderItemsForVisitType();
            });
        });

        // Toggle Return Date on radio change
        function toggleReturnDate() {
            const wrap = document.getElementById('lm_return_date_wrap');
            const yesR = document.getElementById('lm_returnable_yes').checked;
            // wrap.d-view-block.display = yesR ? '' : 'none';
            wrap.classList.toggle('d-none', !yesR);
            wrap.classList.toggle('d-view-block', yesR);
        }
        document.getElementById('lm_returnable_yes').addEventListener('change', toggleReturnDate);
        document.getElementById('lm_returnable_no').addEventListener('change', toggleReturnDate);

        // Add row
        let lugIndex = 0;
        const list = document.getElementById('luggageList');
        document.getElementById('lm_save_btn').addEventListener('click', function() {
            const item_key = document.getElementById('lm_item_key').value;
            const item_label = document.getElementById('lm_item_label').value;
            const brand = document.getElementById('lm_brand_name').value;
            const qty = document.getElementById('lm_qty').value || '1';
            const ser = document.getElementById('lm_serial_or_remarks').value || '';
            const returnable = document.querySelector('input[name="lm_returnable"]:checked').value; // yes/no
            // const return_date_visible = document.getElementById('lm_return_date_wrap').style.display !== 'none';
            const return_date_visible = !document.getElementById('lm_return_date_wrap').classList.contains('d-none');

            const return_date = return_date_visible ? (document.getElementById('lm_return_date').value || '') : '';

            const id = 'lug_row_' + lugIndex;
            const row = document.createElement('div');
            row.className = 'lug-item';
            row.id = id;
            row.dataset.itemKey = item_key;

            row.innerHTML = `
      <div class="lug-meta">
        <strong>${item_label}</strong>
        <div>Brand: ${brand || '-'}</div>
        <div>Qty: ${qty}</div>
        <div>Serial/Remarks: ${ser || '-'}</div>
        <div>Returnable: ${returnable}${returnable==='yes' ? (return_date ? ' — ' + return_date : '') : ''}</div>
      </div>
      <div class="lug-actions">
        <button type="button" class="js-remove-luggage" data-name="removeLuggageRow" data-row-id="${id}" data-item-key="${item_key}">
            <i class="icofont icofont-trash"></i></button>
      </div>
      <div class="d-none">
        <input type="hidden" name="luggage[${lugIndex}][item_key]" value="${item_key}">
        <input type="hidden" name="luggage[${lugIndex}][item_name]" value="${item_label}">
        <input type="hidden" name="luggage[${lugIndex}][brand_name]" value="${brand}">
        <input type="hidden" name="luggage[${lugIndex}][qty]" value="${qty}">
        <input type="hidden" name="luggage[${lugIndex}][serial_or_remarks]" value="${ser}">
        <input type="hidden" name="luggage[${lugIndex}][returnable]" value="${returnable}">
        ${returnable==='yes' ? `<input type="hidden" name="luggage[${lugIndex}][return_date]" value="${return_date}">` : ``}
      </div>
    `;
            list.appendChild(row);

            // active state & counter
            itemCounts[item_key] = (itemCounts[item_key] || 0) + 1;
            const pill = scroller.querySelector(`.lug-pill[data-item-key="${item_key}"]`);
            if (pill) pill.classList.add('active');

            lugIndex++;
            modal.hide();
        });
        $(document).on('click', '.js-remove-luggage', function () {
            removeLuggageRow($(this).data('row-id'),$(this).data('item-key'));
        });

        // Remove row (global)
        window.removeLuggageRow = function(rowId, itemKey) {
            const row = document.getElementById(rowId);
            if (row) row.remove();
            if (itemCounts[itemKey]) {
                itemCounts[itemKey]--;
                if (itemCounts[itemKey] <= 0) {
                    delete itemCounts[itemKey];
                    const pill = scroller.querySelector(`.lug-pill[data-item-key="${itemKey}"]`);
                    if (pill) pill.classList.remove('active');
                }
            }
        };
    })();



    document.addEventListener('DOMContentLoaded', function() {
        if (window.jQuery && jQuery().select2) {
            $('.js-example-basic-single').select2();
        }
        $('#delivery_company_person').on('change', function() {
            if ($(this).val() === '__new__') {
                $('#new_company_box').removeClass('d-none').addClass('d-view-block');
            } else {
                $('#new_company_box').removeClass('d-view-block').addClass('d-none');
            }

        });

        $('#service_company').on('change', function() {
            if ($(this).val() === '__new__') {
                $('#new_service_company_box').removeClass('d-none').addClass('d-view-block');
            } else {
                $('#new_service_company_box').removeClass('d-view-block').addClass('d-none');
            }

        });
        const el = document.getElementById('visitorConfig');
		const EDIT_MODE = el?.dataset.editMode === '1';
		const NEXT_BADGE = el?.dataset.nextBadge
		    ? JSON.parse(el.dataset.nextBadge)
		    : {};

        const visitorDiv = document.getElementById('visitorDiv');
        const deliveryDiv = document.getElementById('deliveryDiv');
        const vendor_supplierDiv = document.getElementById('vendor_supplierDiv');
        
        const serviceDiv = document.getElementById('serviceDiv');
        const employeeDiv = document.getElementById('employeeDiv');
        const doctorDiv = document.getElementById('doctorDiv');

        // Visit type switcher WITH confirm alert (clear fields if user agrees)
        let prevVisitType = document.querySelector('input[name="visit_type"]:checked')?.value || '';

        function clearTypeDivs() {
            [visitorDiv, deliveryDiv, serviceDiv, employeeDiv, doctorDiv, vendor_supplierDiv].forEach(div => {
                if (!div) return;
                div.querySelectorAll('input[type="text"], textarea').forEach(el => el.value = '');
                div.querySelectorAll('select').forEach(sel => {
                    if (sel.classList.contains('js-example-basic-single')) {
                        $(sel).val('0').trigger('change');
                    } else {
                        sel.value = '0';
                    }
                });
                div.querySelectorAll('input[type="radio"]').forEach(r => r.checked = false);
            });
            // Clear luggage (if present)
            const lugList = document.getElementById('luggageList');
            if (lugList) lugList.innerHTML = '';
            document.querySelectorAll('.lug-pill.active').forEach(p => p.classList.remove('active'));
        }

        function showTypeDiv(type) {
            visitorDiv.classList.toggle('d-none', !(type === 'visitor' || type === 'others'));
            visitorDiv.classList.toggle('d-view-block',  (type === 'visitor' || type === 'others'));

            deliveryDiv.classList.toggle('d-none', type !== 'delivery');
            deliveryDiv.classList.toggle('d-view-block', type === 'delivery');


            vendor_supplierDiv.classList.toggle('d-none', type !== 'vendor_supplier');
            vendor_supplierDiv.classList.toggle('d-view-block', type === 'vendor_supplier');

            serviceDiv.classList.toggle('d-none', type !== 'service');
            serviceDiv.classList.toggle('d-view-block', type === 'service');

            employeeDiv.classList.toggle('d-none', type !== 'employee');
            employeeDiv.classList.toggle('d-view-block', type === 'employee');

            doctorDiv.classList.toggle('d-none', type !== 'doctor');
            doctorDiv.classList.toggle('d-view-block', type === 'doctor');

            // show/hide the card
            if (type && type !== 'employee') {
                $('#visitorCardWrap').removeClass('d-none').addClass('d-view-block');
            } else {
                $('#visitorCardWrap').removeClass('d-view-block').addClass('d-none');
            }


            // IMPORTANT:
            // • If NEW record (EDIT_MODE == false) → use NEXT_BADGE when available
            // • If EDIT record (EDIT_MODE == true) → NEVER touch inputs; just mirror what’s already there
            if (!EDIT_MODE) {
                if (NEXT_BADGE && NEXT_BADGE.status === 'success') {
                    setCodeToInputAndCard(NEXT_BADGE.full_code || '', type);
                } else {
                    syncCardFromInputs();
                }
            } else {
                syncCardFromInputs();
            }

        }

        function syncCardFromInputs() {
            const t = currentType();
            let val = '';
            if (t === 'delivery') val = $('#visiting_company_id_delivery').val();
            else if (t === 'service') val = $('#visiting_company_id_service').val();
            else if (t === 'doctor') val = $('#visiting_company_id_doctor').val();
            else val = $('#visiting_company_id_visitor').val(); // visitor/others/default
            $('#visit_card_code').text(val || '—');
        }

        function currentType() {
            const sel = document.querySelector('input[name="visit_type"]:checked');
            return sel ? sel.value : '';
        }

        document.querySelectorAll('input[name="visit_type"]').forEach(r => {
            r.addEventListener('change', function(e) {
                const newType = this.value;
                prevVisitType = newType;
                showTypeDiv(newType);

            });
        });


        const initialType = document.querySelector('input[name="visit_type"]:checked')?.value || '';
        prevVisitType = initialType;
        showTypeDiv(initialType);

        // expose the PHP-decoded JSON you already have:
        function showCardForType(type) {
            // card only for non-employee
            
            if (type && type !== 'employee') {
                $('#visitorCardWrap').removeClass('d-none').addClass('d-view-block');
            } else {
                $('#visitorCardWrap').removeClass('d-view-block').addClass('d-none');
            }
        }

        function setCodeToInputAndCard(code, type) {
            var t = type;
            if (t === 'delivery') {
                $('#visiting_company_id_delivery').val(code || '');
            } else if (t === 'service') {
                $('#visiting_company_id_service').val(code || '');
            } else if (t === 'doctor') {
                $('#visiting_company_id_doctor').val(code || '');
            } else if (t === 'visitor' || t === 'others' || !t) {
                $('#visiting_company_id_visitor').val(code || '');
            }
            $('#visit_card_code').text(code || '—');
        }
    });


    document.addEventListener('DOMContentLoaded', function() {
        const companyWrap = document.getElementById('company_name_wrap');
        const addressWrap = document.getElementById('address_wrap');
        const othersWrap = document.getElementById('purpose_other_wrap');

        const companyInput = document.getElementById('company_name');
        const addressInput = document.getElementById('address');
        const othersInput = document.getElementById('purpose_other');

        function toggleVisitorPurposeFields() {
            const sel = document.querySelector('input[name="purpose"]:checked');
            const val = sel ? sel.value : '';

            // reset hidden fields to avoid submitting stale values
            function hideAndClear(elWrap, inputEl) {
                if (!elWrap) return;
                elWrap.classList.remove('d-view-block');
                elWrap.classList.add('d-none');

                if (!inputEl) return;
                inputEl.value = '';
                if (inputEl.tagName === 'TEXTAREA') inputEl.value = '';
                else inputEl.value = '';
            }

            // show only the one required block
            if (val === 'official') {
                companyWrap.classList.remove('d-none');
                companyWrap.classList.add('d-view-block');

                addressWrap.classList.remove('d-view-block');
                addressWrap.classList.add('d-none');

                othersWrap.classList.remove('d-view-block');
                othersWrap.classList.add('d-none');

                addressInput.value = '';
                othersInput.value = '';
            }else if (val === 'personal') {
                addressWrap.classList.remove('d-none');
                addressWrap.classList.add('d-view-block');

                companyWrap.classList.remove('d-view-block');
                companyWrap.classList.add('d-none');

                othersWrap.classList.remove('d-view-block');
                othersWrap.classList.add('d-none');

                companyInput.value = '';
                othersInput.value = '';
            }else if (val === 'others') {
                othersWrap.classList.remove('d-none');
                othersWrap.classList.add('d-view-block');

                companyWrap.classList.remove('d-view-block');
                companyWrap.classList.add('d-none');

                addressWrap.classList.remove('d-view-block');
                addressWrap.classList.add('d-none');

                companyInput.value = '';
                addressInput.value = '';
            }else {
                // nothing selected yet
                hideAndClear(companyWrap, companyInput);
                hideAndClear(addressWrap, addressInput);
                hideAndClear(othersWrap, othersInput);
            }
        }

        document.querySelectorAll('input[name="purpose"]').forEach(r => {
            r.addEventListener('change', toggleVisitorPurposeFields);
        });

        // initial (handles edit/new states)
        toggleVisitorPurposeFields();
    });






    // helper: safe text
    function escapeHtml(s) {
        return (s || '').toString()
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    // helper: initials from full name (first + last word)
    function getInitials(fullName) {
        const n = (fullName || '').trim().split(/\s+/);
        if (!n.length) return 'NA';
        const first = n[0]?.[0] || '';
        const last = (n.length > 1 ? n[n.length - 1][0] : (n[0][1] || '')) || '';
        return (first + last).toUpperCase();
    }

    // helper: build avatar HTML (photo if present else initials)
    function buildAvatar(name, imagePath, checkedOut, idCardNo = '') {
        const statusCls = checkedOut ? 'out' : 'in';
        const hasImg = imagePath && imagePath.trim() !== '';
        const safeId = escapeHtml(idCardNo || '');

        if (hasImg) {
            const src = 'uploads/visitors/' + imagePath.trim();
            return `
                <span class="avatar-hover">
                    <span class="avatar-circle ${statusCls}">
                        <img src="${escapeHtml(src)}" alt="">
                    </span>
                    <span class="avatar-pop">
                        <img src="${escapeHtml(src)}" alt="">
                        <div class="idcard-caption">${safeId}</div>
                    </span>
                </span>
            `;
        }
        const initials = getInitials(name);
        return `<span class="avatar-circle ${statusCls}">${escapeHtml(initials)}</span>`;
    }
    // Call this when phone changes (visitor/others only)
    function lookupVisitorByPhone() {
        var visitType = $('input[name="visit_type"]:checked').val();
        if (visitType !== 'visitor' && visitType !== 'others') return;

        var phone = $('#phone').val().trim();
        if (!phone) return;
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            data: `page=getVisitorsByPhoneAndEmpCode&phone=${encodeURIComponent(phone)}`,
            success: function(data) {
                if (!data) return;
                var response;
                try {
                    response = JSON.parse(data);
                } catch (e) {
                    return;
                }
                var records = (response && response.visitor_logs_res) ? response.visitor_logs_res : [];
                // 1st record → fill fields
                var first = records[0] || {};
                // name
                $('#name').val(first.name || '');
                // id type + number
                if (first.idcard_type) {
                    $('#idcard_type').val(first.idcard_type);
                    if ($.fn.select2 && $('#idcard_type').hasClass('js-example-basic-single')) {
                        $('#idcard_type').trigger('change');
                    }
                }
                $('#idcard_number').val(first.idcard_number || '');
                // whom to meet
                if (first.whom_to_meet) {
                    $('#whom_to_meet').val(first.whom_to_meet);
                    if ($('#whom_to_meet').hasClass('js-example-basic-single')) {
                        $('#whom_to_meet').trigger('change');
                    }
                }
                var rowsHtml = '';
                for (var i = 0; i < records.length; i++) {
                    var r = records[i] || {};
                    var nm = r.name || '';
                    var ph = r.phone || '';
                    var ci = r.in_datetime || '';
                    var co = r.out_datetime || '';

                    // checked out? Use out_datetime; treat '0000-00-00 00:00:00' as not checked out
                    var checkedOut = !!(co && co !== '0000-00-00 00:00:00');

                    const avatar = buildAvatar(nm, r.image_path || '', checkedOut, r.employee_code || r.visitor_idcard_no || '');
                    var nameCls = 'visitor-name ' + (checkedOut ? 'out' : 'in');

                    rowsHtml += `
                            <tr>
                            <td>
                                <div class="visitor-cell">
                                ${avatar}
                                <div>
                                    <div class="${nameCls}">${escapeHtml(nm || '-')}</div>
                                </div>
                                </div>
                            </td>
                            <td>${escapeHtml(ph || '')}</td>
                            <td>${escapeHtml(ci)}</td>
                            <td>${escapeHtml(co)}</td>
                            </tr>
                        `;
                }
                // inject or show empty state
                if (rowsHtml.trim()) {
                    $('#visitorHistoryTable tbody').html(rowsHtml);
                } else {
                    $('#visitorHistoryTable tbody').html(`
                            <tr class="no-records-row">
                            <td colspan="4">
                                <div class="text-center">
                                <p class="text-muted f-16 fw-bold m-b-30">
                                    <i class="fa fa-warning"></i> No Records
                                </p>
                                <div class="text-center px-4">
                                    <img class="mw-100 mh-300" alt="" src="assets/images/work.png" >
                                </div>
                                </div>
                            </td>
                            </tr>
                        `);
                }
                $('#visitorHistoryWrap')
                    .removeClass('d-none')
                    .addClass('d-view-block');

            }
        });
    }

    // Trigger on blur or Enter; you can also add input debounce if needed
    $(function() {
        $('#phone').on('blur', lookupVisitorByPhone);
        $('input[name="visit_type"]').on('change', function() {
            // re-check when type flips to visitor/others
            lookupVisitorByPhone();
        });
    });



    // trigger on blur (and Enter press) of Employee ID
    $(function() {
        // $('#employee_code')
        //     .on('blur', lookupEmployeeByCode)
        //     .on('keypress', function(e) {
        //         if (e.which === 13) { // Enter
        //             e.preventDefault();
        //             lookupEmployeeByCode();
        //         }
        //     });
        $('#employee_code').on('change', lookupEmployeeByCode);

        if (($('#employee_code').val() || '') !== '0') {
            lookupEmployeeByCode();
        }
    });

    function lookupEmployeeByCode() {
        var code = $('#employee_code').val().trim();
        const location_id = ($('#location_id').val() || '').trim();

        if (!code) return;

        // your style: POST to ajax.php with page param + the code
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            data: `page=getVisitorsByPhoneAndEmpCode&employee_code=${encodeURIComponent(code)}&location_id=${location_id}`,
            success: function(data) {
                if (!data) return;

                var res;
                try {
                    res = JSON.parse(data);
                } catch (e) {
                    return;
                }
                if (res.emp_res) {
                    var emp = res.emp_res || {};
                    $('#employee_name').val(emp.display_name || '');
                    $('#employee_department').val(emp.department_name || '');
                } else {
                    // clear and toast
                    $('#employee_name').val('');
                    $('#employee_department').val('');
                    if (typeof tostarnotify === 'function') {
                        tostarnotify(
                            'top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger',
                            'animated flipInX', 'animated flipOutX',
                            'Employee not found', ''
                        );
                    }
                }
            }
        });
    }



    $(function() {
        // Live reflect manual edits to any of the three inputs
        $('#visiting_company_id_visitor, #visiting_company_id_delivery, #visiting_company_id_service, #visiting_company_id_doctor')
            .on('input', function() {
                $('#visit_card_code').text($(this).val() || '—');
            });
    });


    function toast(msg) {
        tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger',
            'animated flipInX', 'animated flipOutX', msg, '');
    }

    $(function() {
        $("#visitorFom").on("submit", function(e) {
            console.log("Validating form...");
            if (!validateVisitorFom()) {
                e.preventDefault();
            }
        });
    });



    function focusId(id) {
        const el = document.getElementById(id);
        if (el) el.focus();
    }

    function getType() {
        return $("input[name='visit_type']:checked").val() || "";
    }

    function badgeForType(type) {
        if (type === "delivery") return ($("#visiting_company_id_delivery").val() || "").trim();
        if (type === "service") return ($("#visiting_company_id_service").val() || "").trim();
        if (type === "doctor") return ($("#visiting_company_id_doctor").val() || "").trim();
        return ($("#visiting_company_id_visitor").val() || "").trim(); // visitor/others
    }

    function validateVisitorFom() {
        let ok = true;

        // ===== Visit Type must be chosen (BUGFIX) =====
        const hasType = $("input[name='visit_type']:checked").length > 0;
        if (!hasType) {
            toast("Please select Type");
            focusId("vt_visitor");
            return false; // EARLY RETURN so we don't skip other guards
        }
        const type = getType();

        // ===== Common =====
        const gateVal = $("#in_gate_no").val();
        if (!gateVal || gateVal === "0") {
            toast("Please select Gate");
            $("#in_gate_no").focus();
            ok = false;
        }

        if (type !== "employee" && !badgeForType(type)) {
            toast("Please enter Badge / Visiting Company ID");
            if (type === "delivery") focusId("visiting_company_id_delivery");
            else if (type === "service") focusId("visiting_company_id_service");
            else if (type === "doctor") focusId("visiting_company_id_doctor");
            else focusId("visiting_company_id_visitor");
            ok = false;
        }

        // ===== Visitor / Others =====
        if (type === "visitor" || type === "others") {
            const phone = ($("#phone").val() || "").trim();
            const meet = $("#whom_to_meet").val();

            if (!/^[0-9]{7,15}$/.test(phone)) {
                toast("Enter valid Phone number (7–15 digits)");
                focusId("phone");
                ok = false;
            }
            if (!meet || meet === "0") {
                toast("Please select Whom to meet");
                focusId("whom_to_meet");
                ok = false;
            }

            const purpose = $("input[name='purpose']:checked").val() || "";
            if (purpose === "official" && !($("#company_name").val() || "").trim()) {
                toast("Enter Company Name");
                focusId("company_name");
                ok = false;
            }
            if (purpose === "personal" && !($("#address").val() || "").trim()) {
                toast("Enter Address");
                focusId("address");
                ok = false;
            }
            if (purpose === "others" && !($("#purpose_other").val() || "").trim()) {
                toast("Specify Purpose");
                focusId("purpose_other");
                ok = false;
            }

            if (type === "others" && !($("#visit_type_other").val() || "").trim()) {
                toast("Specify Visit Type (Others)");
                focusId("visit_type_other");
                ok = false;
            }
        }

        // ===== Delivery =====
        if (type === "delivery") {
            if (!($("#delivery_company_person").val() || "").trim()) {
                toast("Select Delivery Company / Person");
                focusId("delivery_company_person");
                ok = false;
            }
            if ($("#delivery_company_person").val() == '__new__' && $("#new_delivery_company").val() == '') {
                toast("Enter Delivery Company / Person");
                focusId("new_delivery_company");
                ok = false;

            }
            const deliverTo = $("#deliver_to").val();
            if (!deliverTo || deliverTo === "0") {
                toast("Select Whom to deliver");
                focusId("deliver_to");
                ok = false;
            }
        }

        // ===== Service =====
        if (type === "service") {
            if (!($("#service_company").val() || "").trim()) {
                toast("Select Service Company");
                focusId("service_company");
                ok = false;
            }
            if ($("#service_company").val() == '__new__' && $("#new_service_company").val() == '') {
                toast("Enter Service Company");
                focusId("service_company");
                ok = false;

            }
            const auth = $("#authorized_employee").val();
            if (!auth || auth === "0") {
                toast("Select Authorized Employee");
                focusId("authorized_employee");
                ok = false;
            }
            if ($('input[name="visit_type"]:checked').val() === 'service' && $('#service_tag_enable').is(':checked')) {
                var cnt = parseInt($('#service_tag_count').val(), 10) || 0;
                if (cnt <= 0) {
                    toast('Enter the Tagged Members count.');
                }
                // var saved = $('#serviceTagHiddenInputs input[name$="[visitor_id]"]').length;
                // if (cnt > 0 && saved !== cnt) {
                //     toast('Click Details and Save the tagged visitors.');
                //     ok = false;
                // }
            }
        }

        // ===== Employee =====
        if (type === "employee") {
            if ($('#emp_temp_enable').is(':checked')) {
                const phone = ($('#emp_temp_phone').val() || '').trim();
                const name = ($('#emp_temp_name').val() || '').trim();
                const reason = $('input[name="emp_temp_reason"]:checked').val() || '';

                if (!/^[0-9]{7,15}$/.test(phone)) {
                    toast("Enter valid Phone number (7–15 digits)");
                    focusId("emp_temp_phone");
                    ok = false;
                }
                if (!name) {
                    toast("Enter Name");
                    focusId("emp_temp_name");
                    ok = false;
                }
                if (!reason) {
                    toast("Please click Visitor Pass Reason");
                    focusId("empTempFields");
                    ok = false;
                }
                if (reason === 'xbranch') {
                    const meet = $("#emp_temp_whom_to_meet").val();
                    if (!meet || meet === "0") {
                        toast("Please select Whom to meet");
                        focusId("emp_temp_whom_to_meet");
                        ok = false;
                    }
                }
                const badge = ($("#visiting_company_id_employee_temp").val() || "").trim();
                if (!badge) {
                    toast("Generate Visitor ID");
                    ok = false;
                }
            } else {
                // if (!($("#employee_code").val() || "").trim()) {
                //     toast("Enter Employee ID");
                //     focusId("employee_code");
                //     ok = false;
                // }

                if (!($("#employee_code").val() || "").trim() || $("#employee_code").val() == '0') {
                    toast("Select Employee ID");
                    $('#employee_code').focus();
                    ok = false;
                }
            }
        }

        // ===== Doctor =====
        if (type === "doctor") {
            const phone = ($("#doctor_phone").val() || "").trim();
            const name = $("#doctor_name").val();

            if (!/^[0-9]{7,15}$/.test(phone)) {
                toast("Enter valid Phone number (7–15 digits)");
                focusId("doctor_phone");
                ok = false;
            }
            if (!name || name === "") {
                toast("Please Enter");
                focusId("doctor_name");
                ok = false;
            }
        }


        // ===== Luggage =====
        if ($("#lug_yes").is(":checked")) {
            if ($("#luggageList .lug-item").length === 0) {
                toast("Please add at least one luggage item");
                ok = false;
            }
        }

        console.log("Validation result:", ok);
        return ok;
    }
document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('btnWorkPermit');
    if (!btn) return;

    btn.addEventListener('click', function () {
        const url = btn.dataset.url;
        if (!url) return;

        // Open securely in new tab
        window.open(url, '_blank', 'noopener,noreferrer');
    });
});
document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('btnMaterialGatePass');
    if (!btn) return;

    btn.addEventListener('click', function () {
        const url = btn.dataset.url;
        if (!url) return;

        window.open(url, '_blank', 'noopener,noreferrer');
    });
});

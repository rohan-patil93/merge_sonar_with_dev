// custom_insert_visitor_logs.js
// visitors

// ─── jQuery Guard: ensure $ is available before running any code ───
(function waitForJQuery(callback) {
    if (window.jQuery) {
        callback(window.jQuery);
    } else {
        setTimeout(function () { waitForJQuery(callback); }, 30);
    }
})(function ($) {

    (function () {
        console.log('3');
        // Initial show if editing with saved tags
        if ($('#service_tag_enable').is(':checked')) {
            $('#serviceTagControls').removeClass('d-none').addClass('d-view-block');
        }

        // Order-preserving IDs pulled from hidden inputs you already write
        function readSavedIdsInOrder() {
            var ids = [];
            $('#serviceTagHiddenInputs input[name$="[visitor_id]"]').each(function () {
                var v = $(this).val();
                if (v) ids.push(v);
            });
            return ids;
        }
        // ===== Keep your simplified generator =====
        function fetchNextVisitorIds(count, afterCode) {
            return new Promise(function (resolve) {
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
            $('#serviceTagHiddenInputs input[name$="[visitor_id]"]').each(function () {
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
            rows.forEach(function (r, i) {
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
            const el = document.getElementById('serviceTagSummary');
            if (el) {
                if (rows.length) {
                    el.textContent = 'Saved: ' + rows.length;
                    el.classList.remove('d-none');
                    el.classList.add('d-inline-show');
                } else {
                    el.classList.remove('d-inline-show');
                    el.classList.add('d-none');
                }
            }
        }

        function resetTaggingUI() {
            $('#service_tag_count').val('');
            writeHiddenInputs([]); // clears hidden inputs + summary
        }

        const stmModal = {
            show: function () {
                const modalEl = document.getElementById('serviceTagModal');
                const bsModal = new bootstrap.Modal(modalEl);
                bsModal.show();
            },
            hide: function () {
                const modalEl = document.getElementById('serviceTagModal');
                const bsModal = bootstrap.Modal.getInstance(modalEl);
                if (bsModal) {
                    bsModal.hide();
                }
            }
        };

        // var stmModal = {
        //     show: function () { $('#serviceTagModal').modal('show'); },
        //     hide: function () { $('#serviceTagModal').modal('hide'); }
        // };

        function renderModalRows(ids) {
            var saved = readSavedTagsMap();
            var $tb = $('#serviceTagTableModal tbody');
            $tb.empty();
            ids.forEach(function (code, idx) {
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
        $('#service_tag_enable').on('change', function () {
            if (this.checked) {
                $('#serviceTagControls').removeClass('d-none').addClass('d-view-block');
            } else {
                $('#serviceTagControls').removeClass('d-view-block').addClass('d-none');
                resetTaggingUI();
            }
        });

        // Details → open modal with rows
        $('#service_tag_details_btn').off('click').on('click', function () {
            var base = $('#visiting_company_id_service').val() || '';
            var savedIds = readSavedIdsInOrder();
            var n = parseInt($('#service_tag_count').val(), 10);

            if (savedIds.length) {
                if (n !== savedIds.length) $('#service_tag_count').val(savedIds.length);
                $('#stm_base_id').text(base || savedIds[0] || '—');
                $('#stm_count').text(savedIds.length);
                renderModalRows(savedIds);
                stmModal.show();
                return;
            }

            if (!n || n < 1) {
                toast('Enter a valid count (e.g., 3)');
                return;
            }
            if (!base) {
                toast('Enter the Visiting Id Card No first');
                return;
            }

            fetchNextVisitorIds(n, base).then(function (ids) {
                if (!ids || !ids.length) {
                    toast('No IDs available');
                    return;
                }
                $('#stm_base_id').text(base);
                $('#stm_count').text(ids.length);
                renderModalRows(ids);
                stmModal.show();
            });
        });

        // Modal Save
        $('#service_tag_save_btn').on('click', function () {
            var rows = [];
            $('#serviceTagTableModal tbody tr').each(function () {
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

    // employee temp settings
    (function () {
        if ($('#emp_temp_enable').is(':checked')) {
            showEmpTempUI(true);
            (function toggleOnLoad() {
                const hostWrap = document.getElementById('empTempHostWrap');
                if (hostWrap) {
                    const reason = $('input[name="emp_temp_reason"]:checked').val();
                    hostWrap.classList.toggle('d-none', reason !== 'xbranch');
                }
            })();
        }
        const el = document.getElementById('visitorConfig');
        const NEXT_BADGE = el?.dataset.nextBadge ? JSON.parse(el.dataset.nextBadge) : {};

        function showEmpTempUI(on) {
            const reasonWrap = document.getElementById('empTempReasonWrap');
            if (reasonWrap) reasonWrap.classList.toggle('d-none', !on);
            const fieldsWrap = document.getElementById('empTempFields');
            if (fieldsWrap) fieldsWrap.classList.toggle('d-none', !on);
            const visitorIdWrap = document.getElementById('empTempVisitorIdWrap');
            if (visitorIdWrap) visitorIdWrap.classList.toggle('d-none', !on);

            $('#employee_code').prop('disabled', !!on);
            $('#employee_name').prop('disabled', !!on);
            $('#employee_department').prop('disabled', !!on);

            const type = $('input[name="visit_type"]:checked').val();
            if (on && type === 'employee') {
                updateEmployeeTempId();
            }
        }

        function updateEmployeeTempId() {
            const isTemp = $('#emp_temp_enable').is(':checked');
            if (!isTemp) return;

            const empName = ($('#emp_temp_name').val() || '').trim().replace(/[^a-zA-Z\s]/g, '').toUpperCase();
            let namePart = 'EMP';
            if (empName) {
                const parts = empName.split(/\s+/).filter(Boolean);
                namePart = (parts.length >= 2 ? (parts[0][0] + parts[1][0]) : parts[0]?.substring(0, 3)) || 'EMP';
            }

            const empPhone = ($('#emp_temp_phone').val() || '').trim().replace(/[^0-9]/g, '');
            const phonePart = empPhone.length >= 4 ? empPhone.substring(empPhone.length - 4) : (empPhone || '0000');

            const now = new Date();
            const day = String(now.getDate()).padStart(2, '0');
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const year = String(now.getFullYear()).substring(2);
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');

            const generatedId = `TEMP-${namePart}-${phonePart}-${day}${month}${year}-${hours}${minutes}`;

            const curVal = $('#visiting_company_id_employee_temp').val();
            if (!curVal || curVal.startsWith('TEMP-') || curVal.startsWith('VISIT')) {
                $('#visiting_company_id_employee_temp').val(generatedId).trigger('input');
            }
        }
        window.updateEmployeeTempId = updateEmployeeTempId;

        $('#emp_temp_enable').on('change', function () {
            showEmpTempUI(this.checked);
            toggleEmpTempHost();
            if (this.checked) {
                updateEmployeeTempId();
            }
        });

        $('#emp_temp_name, #emp_temp_phone').on('input change', function () {
            updateEmployeeTempId();
        });

        function toggleEmpTempHost() {
            const hostWrap = document.getElementById('empTempHostWrap');
            if (hostWrap) {
                const reason = $('input[name="emp_temp_reason"]:checked').val();
                hostWrap.classList.toggle('d-none', reason !== 'xbranch');
            }
        }

        $('input[name="emp_temp_reason"]').on('change', toggleEmpTempHost);

        $('#visiting_company_id_visitor, #visiting_company_id_delivery, #visiting_company_id_service, #visiting_company_id_employee_temp, #visiting_company_id_doctor')
            .on('input', function () {
                var val = $(this).val() || '';
                $('#visit_card_code').text(val || '—');
                if (typeof window.updateLiveBadgePreview === 'function') {
                    window.updateLiveBadgePreview();
                }
            });
    })();

    $(function () {
        const luggageList = document.getElementById('luggageList');
        if (!luggageList) return;

        if (luggageList.dataset.hasPrefill == '1') {
            window._prefill_lug_idx = 0;
            document.getElementById('lug_yes').checked = true;
            const luggageSection = document.getElementById('luggageSection');
            if (luggageSection) {
                luggageSection.classList.remove('d-none');
                luggageSection.classList.add('d-view-block');
            }
        }
    });



    $(function () {
        const wrap = document.getElementById('visit_type_other_wrap');
        if (!wrap) return;
        const radios = document.querySelectorAll('input[name="visit_type"]');

        function toggleOthers() {
            const sel = document.querySelector('input[name="visit_type"]:checked');
            wrap.classList.toggle('d-none', !(sel && sel.value === 'others'));
            wrap.classList.toggle('d-view-block', (sel && sel.value === 'others'));
        }
        radios.forEach(r => r.addEventListener('change', toggleOthers));
        toggleOthers();
    });

    (function () {
        const yes = document.getElementById('lug_yes');
        const no = document.getElementById('lug_no');
        const section = document.getElementById('luggageSection');

        function toggleLuggage() {
            if (section && yes) {
                section.classList.toggle('d-none', !yes.checked);
                section.classList.toggle('d-view-block', yes.checked);
            }
        }
        if (yes && no) {
            [yes, no].forEach(r => r.addEventListener('change', toggleLuggage));
            toggleLuggage();
        }

        const modal = {
            show: function () {
                const modalEl = document.getElementById('luggageModal');
                const bsModal = new bootstrap.Modal(modalEl);
                bsModal.show();
            },
            hide: function () {
                const modalEl = document.getElementById('luggageModal');
                const bsModal = bootstrap.Modal.getInstance(modalEl);
                if (bsModal) {
                    bsModal.hide();
                }
            }
        };

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
                { key: 'food', label: 'Food Parcel', icon: 'fas fa-shopping-basket' },
                { key: 'supplies', label: 'Office Supplies', icon: 'fas fa-pencil-alt' },
                { key: 'spares', label: 'Spare Parts', icon: 'fas fa-cogs' },
                { key: 'returnship', label: 'Return Shipment', icon: 'fas fa-undo' },
                { key: 'crate', label: 'Crate', icon: 'fas fa-archive' },
                { key: 'envelope', label: 'Envelope', icon: 'fas fa-envelope' },
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
                { key: 'ac', label: 'Air Conditioner', icon: 'fas fa-thermometer-half' },
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
                { key: 'idcard', label: 'Access Card', icon: 'fas fa-id-card' },
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
                { key: 'food', label: 'Food', icon: 'fas fa-shopping-basket' },
                { key: 'plants', label: 'Plants', icon: 'fas fa-leaf' },
                { key: 'electronics', label: 'Electronics', icon: 'fas fa-plug' },
                { key: 'box', label: 'Box', icon: 'fas fa-cube' },
                { key: 'bag', label: 'Bag', icon: 'fas fa-shopping-bag' },
                { key: 'misc', label: 'Misc', icon: 'fas fa-ellipsis-h' }
            ],
            vendor_supplier: [
                { key: 'laptop', label: 'Laptop', icon: 'fas fa-laptop' },
                { key: 'samplebox', label: 'Sample Box', icon: 'fas fa-archive' },
                { key: 'spares', label: 'Spare Parts', icon: 'fas fa-cogs' },
                { key: 'toolkit', label: 'Tool Kit', icon: 'fas fa-wrench' },
                { key: 'backpack', label: 'Backpack', icon: 'fas fa-briefcase' },
                { key: 'cargobox', label: 'Cargo Box', icon: 'fas fa-cube' },
                { key: 'drums', label: 'Drums', icon: 'fas fa-database' },
                { key: 'chemicals', label: 'Chemicals', icon: 'fas fa-flask' },
                { key: 'rawmaterial', label: 'Raw Material', icon: 'fas fa-industry' },
                { key: 'misc', label: 'Misc', icon: 'fas fa-ellipsis-h' }
            ]
        };

        const scroller = document.getElementById('luggageScroller');
        let itemCounts = {};

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
        }

        function renderItemsForVisitType() {
            if (!scroller) return;
            const vt = getVisitType();
            const items = ITEM_SETS[vt] || ITEM_SETS['visitor'];
            scroller.innerHTML = items.map(it => `
            <div class="lug-pill ${itemCounts[it.key] > 0 ? 'active' : ''}" data-item-key="${it.key}" data-item-label="${it.label}">
                <div class="lug-img"><i class="${it.icon}"></i></div>
                <div class="lug-label">${it.label}</div>
            </div>
        `).join('');

            scroller.querySelectorAll('.lug-pill').forEach(pill => {
                pill.addEventListener('click', function () {
                    if (yes && !yes.checked) return;
                    document.getElementById('lm_item_key').value = this.dataset.itemKey;
                    document.getElementById('lm_item_label').value = this.dataset.itemLabel;
                    document.getElementById('lm_brand_name').value = '';
                    const otherInput = document.getElementById('lm_brand_name_other');
                    const btnAddBrand = document.getElementById('btn_add_brand_other');
                    if (otherInput) {
                        otherInput.value = '';
                        otherInput.classList.add('d-none');
                    }
                    if (btnAddBrand) {
                        btnAddBrand.classList.add('d-none');
                    }
                    document.getElementById('lm_qty').value = 1;
                    document.getElementById('lm_serial_or_remarks').value = '';

                    document.getElementById('lm_returnable_yes').checked = true;
                    document.getElementById('lm_returnable_no').checked = false;
                    document.getElementById('lm_return_date_wrap').classList.remove('d-none');
                    document.getElementById('lm_return_date_wrap').classList.add('d-view-block');

                    modal.show();
                });
            });
        }

        const brandSel = document.getElementById('lm_brand_name');
        if (brandSel) {
            brandSel.addEventListener('change', function () {
                const otherInput = document.getElementById('lm_brand_name_other');
                const btnAddBrand = document.getElementById('btn_add_brand_other');
                if (otherInput) {
                    if (this.value === 'Other') {
                        otherInput.classList.remove('d-none');
                        if (btnAddBrand) btnAddBrand.classList.remove('d-none');
                        otherInput.focus();
                    } else {
                        otherInput.classList.add('d-none');
                        if (btnAddBrand) btnAddBrand.classList.add('d-none');
                        otherInput.value = '';
                    }
                }
            });
        }

        seedItemCountsFromPrefill();
        renderItemsForVisitType();
        document.querySelectorAll('input[name="visit_type"]').forEach(r => {
            r.addEventListener('change', function () {
                itemCounts = {};
                document.getElementById('luggageList').innerHTML = '';
                renderItemsForVisitType();
            });
        });

        function toggleReturnDate() {
            const wrap = document.getElementById('lm_return_date_wrap');
            const yesEl = document.getElementById('lm_returnable_yes');
            if (wrap && yesEl) {
                const yesR = yesEl.checked;
                wrap.classList.toggle('d-none', !yesR);
                wrap.classList.toggle('d-view-block', yesR);
            }
        }
        const yesEl = document.getElementById('lm_returnable_yes');
        const noEl = document.getElementById('lm_returnable_no');
        if (yesEl && noEl) {
            yesEl.addEventListener('change', toggleReturnDate);
            noEl.addEventListener('change', toggleReturnDate);
        }

        let lugIndex = 0;
        const list = document.getElementById('luggageList');
        document.getElementById('lm_save_btn').addEventListener('click', function () {
            const item_key = document.getElementById('lm_item_key').value;
            const item_label = document.getElementById('lm_item_label').value;
            let brand = document.getElementById('lm_brand_name').value;
            if (brand === 'Other') {
                brand = (document.getElementById('lm_brand_name_other').value || '').trim() || 'Other';
            }
            const qty = document.getElementById('lm_qty').value || '1';
            const ser = document.getElementById('lm_serial_or_remarks').value || '';
            const returnable = document.querySelector('input[name="lm_returnable"]:checked').value;
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
                <div>Returnable: ${returnable}${returnable === 'yes' ? (return_date ? ' — ' + return_date : '') : ''}</div>
            </div>
            <div class="lug-actions">
                <button type="button" class="js-remove-luggage" data-row-id="${id}" data-item-key="${item_key}">
                    <i class="fa fa-trash"></i></button>
            </div>
            <div class="d-none">
                <input type="hidden" name="luggage[${lugIndex}][item_key]" value="${item_key}">
                <input type="hidden" name="luggage[${lugIndex}][item_name]" value="${item_label}">
                <input type="hidden" name="luggage[${lugIndex}][brand_name]" value="${brand}">
                <input type="hidden" name="luggage[${lugIndex}][qty]" value="${qty}">
                <input type="hidden" name="luggage[${lugIndex}][serial_or_remarks]" value="${ser}">
                <input type="hidden" name="luggage[${lugIndex}][returnable]" value="${returnable}">
                ${returnable === 'yes' ? `<input type="hidden" name="luggage[${lugIndex}][return_date]" value="${return_date}">` : ``}
            </div>
        `;
            list.appendChild(row);

            itemCounts[item_key] = (itemCounts[item_key] || 0) + 1;
            const pill = scroller.querySelector(`.lug-pill[data-item-key="${item_key}"]`);
            if (pill) pill.classList.add('active');

            lugIndex++;
            modal.hide();
        });

        $(document).on('click', '.js-remove-luggage', function () {
            removeLuggageRow($(this).data('row-id'), $(this).data('item-key'));
        });

        window.removeLuggageRow = function (rowId, itemKey) {
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

    $(function () {
        // function initSelect2() {
        //     if (window.jQuery && jQuery.fn.select2) {
        //         $('.js-example-basic-single').select2();
        //     } else {
        //         setTimeout(initSelect2, 50);
        //     }
        // }
        // initSelect2();
        $('#delivery_company_person').on('change', function () {
            if ($(this).val() === '__new__') {
                $('#new_company_box').removeClass('d-none').addClass('d-view-block');
            } else {
                $('#new_company_box').removeClass('d-view-block').addClass('d-none');
            }
        });

        $('#service_company').on('change', function () {
            if ($(this).val() === '__new__') {
                $('#new_service_company_box').removeClass('d-none').addClass('d-view-block');
            } else {
                $('#new_service_company_box').removeClass('d-view-block').addClass('d-none');
            }
        });

        const el = document.getElementById('visitorConfig');
        const EDIT_MODE = el?.dataset.editMode === '1';
        const NEXT_BADGE = el?.dataset.nextBadge ? JSON.parse(el.dataset.nextBadge) : {};

        const visitorDiv = document.getElementById('visitorDiv');
        const deliveryDiv = document.getElementById('deliveryDiv');
        const serviceDiv = document.getElementById('serviceDiv');
        const employeeDiv = document.getElementById('employeeDiv');
        const doctorDiv = document.getElementById('doctorDiv');
        const vendor_supplierDiv = document.getElementById('vendor_supplierDiv');

        let prevVisitType = document.querySelector('input[name="visit_type"]:checked')?.value || '';

        function showTypeDiv(type) {
            let headingText = 'Visitor Details';
            if (type === 'delivery') headingText = 'Delivery Details';
            else if (type === 'service') headingText = 'Service Details';
            else if (type === 'employee') headingText = 'Employee Details';
            else if (type === 'doctor') headingText = 'Doctor Details';
            else if (type === 'vendor_supplier') headingText = 'Vendor/Supplier Details';
            $('#formSectionTitle').text(headingText);

            if (visitorDiv) {
                visitorDiv.classList.toggle('d-none', !(type === 'visitor' || type === 'others'));
                visitorDiv.classList.toggle('d-view-block', (type === 'visitor' || type === 'others'));
            }
            if (deliveryDiv) {
                deliveryDiv.classList.toggle('d-none', type !== 'delivery');
                deliveryDiv.classList.toggle('d-view-block', type === 'delivery');
            }
            if (serviceDiv) {
                serviceDiv.classList.toggle('d-none', type !== 'service');
                serviceDiv.classList.toggle('d-view-block', type === 'service');
            }
            if (employeeDiv) {
                employeeDiv.classList.toggle('d-none', type !== 'employee');
                employeeDiv.classList.toggle('d-view-block', type === 'employee');
            }
            if (doctorDiv) {
                doctorDiv.classList.toggle('d-none', type !== 'doctor');
                doctorDiv.classList.toggle('d-view-block', type === 'doctor');
            }
            if (vendor_supplierDiv) {
                vendor_supplierDiv.classList.toggle('d-none', type !== 'vendor_supplier');
                vendor_supplierDiv.classList.toggle('d-view-block', type === 'vendor_supplier');
            }

            if (type) {
                $('#visitorCardWrap').removeClass('d-none').addClass('d-view-block');
            } else {
                $('#visitorCardWrap').removeClass('d-view-block').addClass('d-none');
            }

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
            else if (t === 'vendor_supplier') val = $('#vendor_supplier_transaction_no').val();
            else val = $('#visiting_company_id_visitor').val();
            $('#visit_card_code').text(val || '—');
        }

        function currentType() {
            const sel = document.querySelector('input[name="visit_type"]:checked');
            return sel ? sel.value : '';
        }

        document.querySelectorAll('input[name="visit_type"]').forEach(r => {
            r.addEventListener('change', function (e) {
                const newType = this.value;
                prevVisitType = newType;
                showTypeDiv(newType);
            });
        });

        const initialType = document.querySelector('input[name="visit_type"]:checked')?.value || '';
        prevVisitType = initialType;
        showTypeDiv(initialType);

        function setCodeToInputAndCard(code, type) {
            var t = type;
            if (t === 'delivery') {
                $('#visiting_company_id_delivery').val(code || '');
            } else if (t === 'service') {
                $('#visiting_company_id_service').val(code || '');
            } else if (t === 'doctor') {
                $('#visiting_company_id_doctor').val(code || '');
            } else if (t === 'vendor_supplier') {
                $('#vendor_supplier_transaction_no').val(code || '');
            } else if (t === 'visitor' || t === 'others' || !t) {
                $('#visiting_company_id_visitor').val(code || '');
            }
            $('#visit_card_code').text(code || '—');
        }
    });

    $(function () {
        const companyWrap = document.getElementById('company_name_wrap');
        const addressWrap = document.getElementById('address_wrap');
        const othersWrap = document.getElementById('purpose_other_wrap');

        const companyInput = document.getElementById('company_name');
        const addressInput = document.getElementById('address');
        const othersInput = document.getElementById('purpose_other');

        function toggleVisitorPurposeFields() {
            const sel = document.querySelector('input[name="purpose"]:checked');
            const val = sel ? sel.value : '';

            function hideAndClear(elWrap, inputEl) {
                if (!elWrap) return;
                elWrap.classList.remove('d-view-block');
                elWrap.classList.add('d-none');
                if (!inputEl) return;
                inputEl.value = '';
            }

            if (val === 'official') {
                if (companyWrap) { companyWrap.classList.remove('d-none'); companyWrap.classList.add('d-view-block'); }
                if (addressWrap) { addressWrap.classList.remove('d-view-block'); addressWrap.classList.add('d-none'); }
                if (othersWrap) { othersWrap.classList.remove('d-view-block'); othersWrap.classList.add('d-none'); }
                if (addressInput) addressInput.value = '';
                if (othersInput) othersInput.value = '';
            } else if (val === 'personal') {
                if (addressWrap) { addressWrap.classList.remove('d-none'); addressWrap.classList.add('d-view-block'); }
                if (companyWrap) { companyWrap.classList.remove('d-view-block'); companyWrap.classList.add('d-none'); }
                if (othersWrap) { othersWrap.classList.remove('d-view-block'); othersWrap.classList.add('d-none'); }
                if (companyInput) companyInput.value = '';
                if (othersInput) othersInput.value = '';
            } else if (val === 'others') {
                if (othersWrap) { othersWrap.classList.remove('d-none'); othersWrap.classList.add('d-view-block'); }
                if (companyWrap) { companyWrap.classList.remove('d-view-block'); companyWrap.classList.add('d-none'); }
                if (addressWrap) { addressWrap.classList.remove('d-view-block'); addressWrap.classList.add('d-none'); }
                if (companyInput) companyInput.value = '';
                if (addressInput) addressInput.value = '';
            } else {
                hideAndClear(companyWrap, companyInput);
                hideAndClear(addressWrap, addressInput);
                hideAndClear(othersWrap, othersInput);
            }
        }

        document.querySelectorAll('input[name="purpose"]').forEach(r => {
            r.addEventListener('change', toggleVisitorPurposeFields);
        });
        toggleVisitorPurposeFields();
    });

    function escapeHtml(s) {
        return (s || '').toString()
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function getInitials(fullName) {
        const n = (fullName || '').trim().split(/\s+/);
        if (!n.length) return 'NA';
        const first = n[0]?.[0] || '';
        const last = (n.length > 1 ? n[n.length - 1][0] : (n[0][1] || '')) || '';
        return (first + last).toUpperCase();
    }

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

    function lookupVisitorByPhone() {
        var visitType = $('input[name="visit_type"]:checked').val();
        if (visitType !== 'visitor' && visitType !== 'others') return;

        var phone = $('#phone').val().trim();
        if (!phone) return;
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            data: `page=getVisitorsByPhoneAndEmpCode&phone=${encodeURIComponent(phone)}`,
            success: function (data) {
                if (!data) return;
                var response;
                try {
                    response = JSON.parse(data);
                } catch (e) {
                    return;
                }
                var records = (response && response.visitor_logs_res) ? response.visitor_logs_res : [];
                var first = records[0] || {};
                $('#name').val(first.name || '').trigger('change');
                if (first.idcard_type) {
                    $('#idcard_type').val(first.idcard_type);
                    if ($.fn.select2 && $('#idcard_type').hasClass('js-example-basic-single')) {
                        $('#idcard_type').trigger('change');
                    }
                }
                $('#idcard_number').val(first.idcard_number || '').trigger('change');
                if (first.whom_to_meet) {
                    $('#whom_to_meet').val(first.whom_to_meet);
                    if ($('#whom_to_meet').hasClass('js-example-basic-single')) {
                        $('#whom_to_meet').trigger('change');
                    }
                }
                if (typeof window.updateLiveBadgePreview === 'function') {
                    window.updateLiveBadgePreview();
                }
                var rowsHtml = '';
                for (var i = 0; i < records.length; i++) {
                    var r = records[i] || {};
                    var nm = r.name || '';
                    var ph = r.phone || '';
                    var ci = r.in_datetime || '';
                    var co = r.out_datetime || '';

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
                $('#visitorHistoryWrap').removeClass('d-none').addClass('d-view-block');
            }
        });
    }

    $(function () {
        $('#phone').on('blur', lookupVisitorByPhone);
        $('input[name="visit_type"]').on('change', function () {
            lookupVisitorByPhone();
        });
    });

    $(function () {
        $('#employee_code')
            .on('blur', lookupEmployeeByCode)
            .on('keypress', function (e) {
                if (e.which === 13) {
                    e.preventDefault();
                    lookupEmployeeByCode();
                }
            });
    });

    function lookupEmployeeByCode() {
        var code = $('#employee_code').val().trim();
        const location_id = ($('#location_id').val() || '').trim();

        if (!code) return;

        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            data: `page=getVisitorsByPhoneAndEmpCode&employee_code=${encodeURIComponent(code)}&location_id=${location_id}`,
            success: function (data) {
                if (!data) return;

                var res;
                try {
                    res = JSON.parse(data);
                } catch (e) {
                    return;
                }
                if (res.emp_res) {
                    var emp = res.emp_res || {};
                    $('#employee_name').val(emp.display_name || '').trigger('change');
                    $('#employee_department').val(emp.department_name || '').trigger('change');
                    if (typeof window.updateLiveBadgePreview === 'function') {
                        window.updateLiveBadgePreview();
                    }
                } else {
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

    function toast(msg) {
        console.log("VMS validation toast requested:", msg);
        if (typeof tostarnotify === 'function') {
            try {
                tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger',
                    'animated flipInX', 'animated flipOutX', msg, '');
            } catch (e) {
                console.error("tostarnotify failed:", e);
                if (typeof swal === 'function') {
                    swal("Warning", msg, "warning");
                } else {
                    alert(msg);
                }
            }
        } else if (typeof swal === 'function') {
            swal("Warning", msg, "warning");
        } else {
            alert(msg);
        }
    }

    $(function () {
        window.approvalPollInterval = null;
        window.currentPendingVisitorLogId = null;

        $("#visitorFom").on("submit", function (e) {
            e.preventDefault();

            if (!validateVisitorFom()) {
                return false;
            }

            const type = getType();
            let meet = '0';
            if (type === 'visitor' || type === 'others') {
                meet = $('#whom_to_meet').val();
            } else if (type === 'delivery') {
                meet = $('#deliver_to').val();
            } else if (type === 'service') {
                meet = $('#authorized_employee').val();
            } else if (type === 'employee' && $('#emp_temp_enable').is(':checked')) {
                meet = $('#emp_temp_whom_to_meet').val();
            } else if (type === 'vendor_supplier') {
                meet = $('#whom_to_meet_vendor').val();
            }

            const hasHost = (meet && meet !== '0');
            const $submitBtn = $('#submitBtn');

            // Disable submit button and show processing state
            $submitBtn.prop('disabled', true).addClass('j-processing');
            const originalBtnText = $submitBtn.html();
            $submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Processing...');

            // Prepare Form Data (including webcam image/files)
            var form = $('#visitorFom')[0];
            var formData = new FormData(form);
            formData.append('page', 'insertVisitorBundle');
            if (hasHost) {
                // 1. Show the Host Approval Modal
                var modal = new bootstrap.Modal(document.getElementById('hostApprovalModal'));
                modal.show();

                // Reset modal layout
                $('#hostApprovalModal .approval-spinner').removeClass('d-none');
                $('#hostApprovalModal .approval-status-icon').addClass('d-none');
                $('#statusSuccessIcon').hide();
                $('#statusErrorIcon').hide();
                $('#approvalStatusTitle').text('Initiating Authorization...');
                $('#approvalStatusDesc').text('Sending authorization request to the host. Please wait.');
                $('#btnManualConfirm').prop('disabled', false).removeClass('d-none');
                $('#btnResendEmail').prop('disabled', true);
                $('#btnGeneratePass').addClass('d-none');
                $('#hostInfoCard').addClass('d-none');

                // 2. Submit form via AJAX to save pending log
                $.ajax({
                    url: 'ajax.php',
                    //data: 'page=insertVisitorBundle',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function (response) {
                        // Restore submit button
                        $submitBtn.prop('disabled', false).removeClass('j-processing').html(originalBtnText);

                        if (response && response.status === 'success') {
                            const logId = response.id || response.LASTID || response.rid;
                            window.currentPendingVisitorLogId = logId;

                            $('#approvalStatusTitle').text('Waiting for Host Response');
                            $('#approvalStatusDesc').text('Authorization email sent. Waiting for host to respond...');
                            $('#btnManualConfirm').prop('disabled', false);
                            $('#btnResendEmail').prop('disabled', false);

                            // Start polling (also populates host info on first call)
                            startPollingStatus(logId);
                        } else {
                            toast(response.message || "Failed to save check-in details");
                            var modalEl = document.getElementById('hostApprovalModal');
                            var modal = bootstrap.Modal.getInstance(modalEl);
                            if (modal) {
                                modal.hide();
                            }
                        }
                    },
                    error: function (xhr, status, error) {
                        $submitBtn.prop('disabled', false).removeClass('j-processing').html(originalBtnText);
                        toast("Connection error. Please try again.");
                        var modalEl = document.getElementById('hostApprovalModal');
                        var modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) {
                            modal.hide();
                        }
                    }
                });

            } else {
                // No Host Selected: Submit and Finalize Immediately
                $.ajax({
                    url: 'ajax.php',
                    //data: 'page=insertVisitorBundle',
                    // url: 'api.php?mode=insertVisitorBundle&method=post',
                    type: 'POST',
                    //data: formData,
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function (response) {
                        if (response && response.status === 'success') {
                            const logId = response.id || response.LASTID || response.rid;
                            // Redirect directly to print page since it is already finalized
                            window.location.href = 'index.php?option=COM_INSERT_VISITOR_LOGS&print_mode=1&print_rid=' + logId;
                        } else {
                            $submitBtn.prop('disabled', false).removeClass('j-processing').html(originalBtnText);
                            toast(response.message || "Failed to complete check-in");
                        }
                    },
                    error: function (xhr, status, error) {
                        $submitBtn.prop('disabled', false).removeClass('j-processing').html(originalBtnText);
                        toast("Connection error. Please try again.");
                    }
                });
            }
            return false;
        });

        var _hostInfoPopulated = false;

        function startPollingStatus(logId) {
            if (window.approvalPollInterval) {
                clearInterval(window.approvalPollInterval);
            }
            _hostInfoPopulated = false;

            window.approvalPollInterval = setInterval(function () {
                $.ajax({
                    url: 'ajax.php',
                    data: 'page=checkVisitorApproval&rid=' + logId,
                    // url: 'api.php?mode=checkVisitorApproval&rid=' + logId,
                    type: 'POST',
                    dataType: 'json',
                    success: function (res) {
                        if (res && res.status === 'success') {
                            // Populate host info card on first successful poll
                            if (!_hostInfoPopulated && res.host) {
                                _hostInfoPopulated = true;
                                var host = res.host;
                                if (host.name || host.email || host.phone) {
                                    $('#hostDisplayName').text(host.name || '—');
                                    $('#hostDisplayEmail span').text(host.email || '—');
                                    $('#hostDisplayPhone span').text(host.phone || '—');
                                    $('#hostInfoCard').removeClass('d-none');
                                }
                            }

                            const status = parseInt(res.current_Status, 10);
                            if (status === 12) {
                                clearInterval(window.approvalPollInterval);
                                showApprovalSuccess();
                            } else if (status === 11) {
                                clearInterval(window.approvalPollInterval);
                                showApprovalRejected();
                            }
                        }
                    }
                });
            }, 2000);
        }

        // Resend Email button handler
        $(document).on('click', '#btnResendEmail', function () {
            const logId = window.currentPendingVisitorLogId;
            if (!logId) return;

            const $btn = $(this);
            $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Sending...');

            $.ajax({
                url: 'ajax.php',
                data: 'page=resendApprovalEmail&rid=' + logId,
                // url: 'api.php?mode=resendApprovalEmail&rid=' + logId,
                type: 'POST',
                dataType: 'json',
                success: function (res) {
                    $btn.prop('disabled', false).html('<i class="fa fa-paper-plane me-1"></i> Resend Email');
                    if (res && res.status === 'success') {
                        if (typeof tostarnotify === 'function') {
                            tostarnotify('top', 'right', 'fa fa-check', 'form-label label label-success',
                                'animated flipInX', 'animated flipOutX',
                                'Reminder email sent successfully!', '');
                        } else {
                            alert('Reminder email sent!');
                        }
                    } else {
                        toast(res.message || 'Failed to resend email');
                    }
                },
                error: function () {
                    $btn.prop('disabled', false).html('<i class="fa fa-paper-plane me-1"></i> Resend Email');
                    toast('Connection error. Could not resend email.');
                }
            });
        });

        function showApprovalSuccess() {
            $('#hostApprovalModal .approval-spinner').addClass('d-none');
            $('#hostApprovalModal .approval-status-icon').removeClass('d-none');
            $('#statusSuccessIcon').show();
            $('#statusErrorIcon').hide();
            $('#approvalStatusTitle').text('Host Approved!');
            $('#approvalStatusDesc').text('The host has authorized this visit. Click below to generate the ID pass.');
            $('#btnManualConfirm').addClass('d-none');
            $('#btnGeneratePass').removeClass('d-none');
        }

        function showApprovalRejected() {
            $('#hostApprovalModal .approval-spinner').addClass('d-none');
            $('#hostApprovalModal .approval-status-icon').removeClass('d-none');
            $('#statusSuccessIcon').hide();
            $('#statusErrorIcon').show();
            $('#approvalStatusTitle').text('Entry Rejected');
            $('#approvalStatusDesc').text('The host has rejected this entry request.');
            $('#btnManualConfirm').addClass('d-none');
            $('#btnGeneratePass').addClass('d-none');
        }

        $(document).on('click', '#btnManualConfirm', function () {
            const logId = window.currentPendingVisitorLogId;
            if (!logId) return;

            const $btn = $(this);
            $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Confirming...');

            $.ajax({
                url: 'ajax.php',
                data: 'page=manualConfirmVisitor&rid=' + logId,
                // url: 'api.php?mode=manualConfirmVisitor&rid=' + logId,
                type: 'POST',
                dataType: 'json',
                success: function (res) {
                    $btn.prop('disabled', false).html('<i class="fa fa-phone-alt me-1"></i> Manual Confirm');
                    if (res && res.status === 'success') {
                        clearInterval(window.approvalPollInterval);
                        showApprovalSuccess();
                    } else {
                        toast("Failed to manually confirm entry");
                    }
                },
                error: function () {
                    $btn.prop('disabled', false).html('<i class="fa fa-phone-alt me-1"></i> Manual Confirm');
                    toast("Connection error");
                }
            });
        });

        $(document).on('click', '#btnGeneratePass', function () {
            const logId = window.currentPendingVisitorLogId;
            if (!logId) return;

            const $btn = $(this);
            $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Generating...');

            const gateId = $('#in_gate_no').val();
            const companyId = $('#company_id').val() || 1;
            const userId = $('#user_id').val() || 1;
            const locationId = $('#location_id').val() || 1;

            $.ajax({
                url: 'ajax.php',
                data: 'page=finalizeVisitorCheckin&rid=' + logId + '&gate_id=' + gateId + '&company_id=' + companyId + '&user_id=' + userId + '&location_id=' + locationId,
                type: 'POST',
                dataType: 'json',
                success: function (res) {
                    if (res && res.status === 'success') {
                        var modalEl = document.getElementById('hostApprovalModal');
                        var modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) {
                            modal.hide();
                        }
                        window.location.href = 'index.php?option=COM_INSERT_VISITOR_LOGS&print_mode=1&print_rid=' + logId;
                    } else {
                        $btn.prop('disabled', false).html('<i class="fa fa-id-card me-1"></i> Generate ID Pass');
                        toast(res.message || "Failed to finalize check-in");
                    }
                },
                error: function () {
                    $btn.prop('disabled', false).html('<i class="fa fa-id-card me-1"></i> Generate ID Pass');
                    toast("Connection error");
                }
            });
        });

        $(document).on('click', '#btnCancelApproval', function () {
            if (window.approvalPollInterval) {
                clearInterval(window.approvalPollInterval);
            }
            var modalEl = document.getElementById('hostApprovalModal');
            var modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) {
                modal.hide();
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
        if (type === "vendor_supplier") return ($("#vendor_supplier_transaction_no").val() || "").trim();
        return ($("#visiting_company_id_visitor").val() || "").trim();
    }

    function validateStep1() {
        const hasType = $("input[name='visit_type']:checked").length > 0;
        const typeVal = $("input[name='visit_type']:checked").val();
        const gateVal = $("#in_gate_no").val();
        console.log("validateStep1 debug info:", { hasType, typeVal, gateVal });

        if (!hasType) {
            toast("Please select Visit Type");
            focusId("vt_visitor");
            return false;
        }

        if (!gateVal || gateVal === "0") {
            toast("Please select Gate");
            $("#in_gate_no").focus();
            return false;
        }
        return true;
    }

    function validateStep2() {
        const type = getType();

        if (type === "visitor" || type === "others") {
            const phone = ($("#phone").val() || "").trim();
            const meet = $("#whom_to_meet").val();

            if (!/^[0-9]{7,15}$/.test(phone)) {
                toast("Enter valid Phone number (7–15 digits)");
                focusId("phone");
                return false;
            }
            if (!meet || meet === "0") {
                toast("Please select Whom to meet");
                focusId("whom_to_meet");
                return false;
            }

            const purpose = $("input[name='purpose']:checked").val() || "";
            if (purpose === "others" && !($("#purpose_other").val() || "").trim()) {
                toast("Specify Purpose");
                focusId("purpose_other");
                return false;
            }

            if (type === "others" && !($("#visit_type_other").val() || "").trim()) {
                toast("Specify Visit Type (Others)");
                focusId("visit_type_other");
                return false;
            }
        }

        if (type === "delivery") {
            if (!($("#delivery_company_person").val() || "").trim()) {
                toast("Select Delivery Company / Person");
                focusId("delivery_company_person");
                return false;
            }
            if ($("#delivery_company_person").val() == '__new__' && $("#new_delivery_company").val() == '') {
                toast("Enter Delivery Company / Person");
                focusId("new_delivery_company");
                return false;
            }
            const deliverTo = $("#deliver_to").val();
            if (!deliverTo || deliverTo === "0") {
                toast("Select Whom to deliver");
                focusId("deliver_to");
                return false;
            }
        }

        if (type === "service") {
            if (!($("#service_company").val() || "").trim()) {
                toast("Select Service Company");
                focusId("service_company");
                return false;
            }
            if ($("#service_company").val() == '__new__' && $("#new_service_company").val() == '') {
                toast("Enter Service Company");
                focusId("new_service_company");
                return false;
            }
            const auth = $("#authorized_employee").val();
            if (!auth || auth === "0") {
                toast("Select Authorized Employee");
                focusId("authorized_employee");
                return false;
            }
        }

        if (type === "vendor_supplier") {
            const vendorId = $("#vendor_supplier_vendor_id").val();
            if (!vendorId || vendorId === "0") {
                toast("Please select Vendor/Supplier");
                focusId("vendor_supplier_vendor_id");
                return false;
            }
            const deptId = $("#vendor_supplier_department_id").val();
            if (!deptId || deptId === "0") {
                toast("Please select Department");
                focusId("vendor_supplier_department_id");
                return false;
            }
            const meetVendor = $("#whom_to_meet_vendor").val();
            if (!meetVendor || meetVendor === "0") {
                toast("Please select Whom to meet");
                focusId("whom_to_meet_vendor");
                return false;
            }
        }

        if (type === "employee") {
            if ($('#emp_temp_enable').is(':checked')) {
                const phone = ($('#emp_temp_phone').val() || '').trim();
                const name = ($('#emp_temp_name').val() || '').trim();
                const reason = $('input[name="emp_temp_reason"]:checked').val() || '';

                if (!/^[0-9]{7,15}$/.test(phone)) {
                    toast("Enter valid Phone number (7–15 digits)");
                    focusId("emp_temp_phone");
                    return false;
                }
                if (!name) {
                    toast("Enter Name");
                    focusId("emp_temp_name");
                    return false;
                }
                if (!reason) {
                    toast("Please select Visitor Pass Reason");
                    return false;
                }
                if (reason === 'xbranch') {
                    const meet = $("#emp_temp_whom_to_meet").val();
                    if (!meet || meet === "0") {
                        toast("Please select Whom to meet");
                        focusId("emp_temp_whom_to_meet");
                        return false;
                    }
                }
            } else {
                if (!($("#employee_code").val() || "").trim()) {
                    toast("Enter Employee ID");
                    focusId("employee_code");
                    return false;
                }
            }
        }

        if (type === "doctor") {
            const phone = ($("#doctor_phone").val() || "").trim();
            const name = $("#doctor_name").val();

            if (!/^[0-9]{7,15}$/.test(phone)) {
                toast("Enter valid Phone number (7–15 digits)");
                focusId("doctor_phone");
                return false;
            }
            if (!name || name === "") {
                toast("Please enter doctor name");
                focusId("doctor_name");
                return false;
            }
        }

        if ($("#lug_yes").is(":checked")) {
            if ($("#luggageList .lug-item").length === 0) {
                toast("Please add at least one luggage item");
                return false;
            }
        }

        return true;
    }

    function validateStep3() {
        return true;
    }

    function validateVisitorFom() {
        return validateStep1() && validateStep2() && validateStep3();
    }

    /* Multi-Step Wizard Controller */
    $(function () {
        let currentStep = 1;

        function updateNextButtonText(step) {
            if (step === 1) {
                const type = getType();
                let nextText = "Next <i class='fa fa-arrow-right'></i>";
                if (type === 'visitor') nextText = "Fill Visitor Profile <i class='fa fa-arrow-right'></i>";
                else if (type === 'delivery') nextText = "Fill Delivery Details <i class='fa fa-arrow-right'></i>";
                else if (type === 'service') nextText = "Fill Service Details <i class='fa fa-arrow-right'></i>";
                else if (type === 'employee') nextText = "Fill Employee Details <i class='fa fa-arrow-right'></i>";
                else if (type === 'doctor') nextText = "Fill Doctor Details <i class='fa fa-arrow-right'></i>";
                else if (type === 'others') nextText = "Fill Details <i class='fa fa-arrow-right'></i>";
                $('#nextBtn').html(nextText);
            } else if (step === 2) {
                const type = getType();
                const supportsGuided = true;
                if (supportsGuided && isGuidedMode) {
                    const total = getGuidedSlideCount(type);
                    if (currentGuidedSlide < total) {
                        $('#nextBtn').html("Next <i class='fa fa-arrow-right'></i>");
                    } else {
                        $('#nextBtn').html("Done &ndash; Review <i class='fa fa-check'></i>");
                    }
                } else {
                    $('#nextBtn').html("Continue to Verification <i class='fa fa-arrow-right'></i>");
                }
            }
        }

        function showStep(step) {
            $('.vms-step-pane').addClass('d-none').removeClass('active');
            $(`#step-pane-${step}`).removeClass('d-none').addClass('active');

            $('.step-progress-step').each(function () {
                const s = parseInt($(this).data('step'), 10);
                const $icon = $(this).find('.step-icon');
                if (s < step) {
                    $(this).addClass('completed').removeClass('active');
                    $icon.html('<i class="fa fa-check"></i>');
                } else if (s === step) {
                    $(this).addClass('active').removeClass('completed');
                    $icon.html(s);
                } else {
                    $(this).removeClass('active completed');
                    $icon.html(s);
                }
            });

            // Update progress track bar fill percentage
            const fillWidth = (step - 1) * 50;
            $('#stepProgressBarFill').css('width', fillWidth + '%');

            // Toggle buttons based on current step
            if (step === 1) {
                $('#prevBtn').addClass('d-none');
                $('#nextBtn').removeClass('d-none');
                $('#submitBtn').addClass('d-none');
            } else if (step === 2) {
                $('#prevBtn').removeClass('d-none');
                $('#nextBtn').removeClass('d-none');
                $('#submitBtn').addClass('d-none');
            } else if (step === 3) {
                $('#prevBtn').removeClass('d-none');
                $('#nextBtn').addClass('d-none');
                $('#submitBtn').removeClass('d-none');
            }

            // Update Next button text dynamically
            updateNextButtonText(step);

            // Scroll page up to the progress tracker element
            const progressEl = $('.vms-step-progress');
            if (progressEl.length && progressEl.is(':visible')) {
                $('html, body').animate({
                    scrollTop: progressEl.offset().top - 20
                }, 300);
            }
        }

        // ==========================================================================
        // PROGRESSIVE GUIDED WIZARD CONTROLLER (STEP 2 PROGRESSIVE FLOW)
        // ==========================================================================
        let isGuidedMode = true;
        let currentGuidedSlide = 1;
        let isOptionalExpanded = false; // true when user opted to fill additional details

        function getGuidedSlideCount(type) {
            if (type === 'visitor' || type === 'others') return isOptionalExpanded ? 7 : 5;
            if (type === 'delivery') return isOptionalExpanded ? 9 : 6;
            if (type === 'service') return isOptionalExpanded ? 9 : 5;
            if (type === 'doctor') return isOptionalExpanded ? 6 : 3;
            if (type === 'employee') {
                const tempPass = $('#emp_temp_enable').is(':checked');
                if (!tempPass) return 2;
                const reason = $('input[name="emp_temp_reason"]:checked').val() || 'forgot';
                const isCrossBranch = (reason === 'xbranch');
                return isCrossBranch ? 6 : 5;
            }
            if (type === 'vendor_supplier') {
                const refType = $('#vendor_supplier_document_type').val() || 'NOREF';
                return (refType === 'NOREF') ? 7 : 8;
            }
            return 0;
        }

        function initGuidedWizard() {
            const type = getType();
            const supportsGuided = true;
            window.renderGuidedSlide = renderGuidedSlide;
            window.advanceGuidedSlide = advanceGuidedSlide;

            if (supportsGuided) {
                $('#btnToggleGuided').parent().removeClass('d-none');
                if (isGuidedMode) {
                    $('#standardFormLayout').addClass('d-none');
                    $('#guidedWizardContainer').removeClass('d-none');
                    renderGuidedSlide();
                } else {
                    restorePrintViewDocuments();
                    $('#guidedWizardContainer').addClass('d-none');
                    $('#standardFormLayout').removeClass('d-none');
                }
            } else {
                $('#btnToggleGuided').parent().addClass('d-none');
                $('#guidedWizardContainer').addClass('d-none');
                $('#standardFormLayout').removeClass('d-none');
            }
        }

        function toggleGuidedMode() {
            isGuidedMode = !isGuidedMode;
            $('#btnToggleGuided').toggleClass('active-mode', isGuidedMode);
            initGuidedWizard();
            if (currentStep === 2) {
                updateNextButtonText(2);
            }
        }

        // Bind toggle mode button click
        $(document).off('click', '#btnToggleGuided').on('click', '#btnToggleGuided', function (e) {
            e.preventDefault();
            toggleGuidedMode();
        });

        // HTML escaping helper
        function esc(s) {
            return (s || '').toString()
                .replace(/&/g, '&amp;').replace(/</g, '&lt;')
                .replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
        }

        function buildVisitorSlideHtml(slide) {
            let html = '';
            if (slide === 1) {
                const currentVal = $('input[name="purpose"]:checked').val() || '';
                const specifyVal = $('#purpose_other').val() || '';
                let cardsHtml = '';
                $('input[name="purpose"]').each(function () {
                    const val = $(this).val();
                    const labelId = $(this).attr('id');
                    let text = $('label[for="' + labelId + '"]').text().trim();
                    if (!text) {
                        text = $(this).next('label').text().trim() || val;
                    }
                    const activeCls = (val === currentVal) ? 'active' : '';
                    let icon = 'fa fa-info-circle';
                    if (val === 'official') icon = 'fa fa-briefcase';
                    else if (val === 'personal') icon = 'fa fa-user';
                    else if (val === 'others') icon = 'fa fa-ellipsis-h';
                    
                    cardsHtml += `
                        <div class="guided-option-card ${activeCls}" data-val="${esc(val)}">
                            <div class="card-icon"><i class="${icon}"></i></div>
                            <div class="card-label">${esc(text)}</div>
                        </div>`;
                });
                html = `
                <div class="guided-slide active">
                    <div class="guided-slide-title">Purpose of Visit</div>
                    <div class="guided-slide-desc">Please select the reason for your visit today</div>
                    <div class="guided-cards-grid">
                        ${cardsHtml}
                    </div>
                    <div class="guided-other-purpose-wrap ${currentVal === 'others' ? '' : 'd-none'}" id="guidedOtherPurposeWrap">
                        <div class="input-group">
                            <input type="text" class="form-control guided-large-input" id="guided_purpose_other" 
                                value="${esc(specifyVal)}" placeholder="Explain your purpose...">
                            <button type="button" class="btn btn-primary btn-add-inline-other px-4" data-type="visit_purpose" data-input-id="guided_purpose_other" data-select-id="purpose"><i class="fa fa-plus"></i> Add</button>
                        </div>
                    </div>
                </div>`;
            } else if (slide === 2) {
                const meetVal = $('#whom_to_meet').val() || '0';
                const meetText = meetVal !== '0' ? $('#whom_to_meet option:selected').text().trim() : '';
                html = `
                <div class="guided-slide active">
                    <div class="guided-slide-title">Who would you like to meet?</div>
                    <div class="guided-slide-desc">Type employee name to search and select</div>
                    <div class="guided-large-input-wrap">
                        <input type="text" class="form-control guided-large-input" id="guided_meet_search" 
                            value="${esc(meetText)}" placeholder="Type name to search...">
                        <div class="employee-suggestion-list d-none" id="guidedMeetSuggestions"></div>
                    </div>
                </div>`;
            } else if (slide === 3) {
                const phoneVal = $('#phone').val() || '';
                html = `
                <div class="guided-slide active">
                    <div class="guided-slide-title">Mobile Number</div>
                    <div class="guided-slide-desc">Enter your 10-digit mobile number</div>
                    <div class="guided-large-input-wrap">
                        <input type="text" class="form-control guided-large-input" id="guided_phone" 
                            value="${esc(phoneVal)}" placeholder="e.g. 9876543210" maxlength="15">
                        <div class="guided-input-hint"><i class="fa fa-keyboard"></i> Press <kbd>Enter</kbd> or click <strong>Next</strong> below to continue</div>
                    </div>
                </div>`;
            } else if (slide === 4) {
                const nameVal = $('#name').val() || '';
                html = `
                <div class="guided-slide active">
                    <div class="guided-slide-title">What is your name?</div>
                    <div class="guided-slide-desc">Enter your full name</div>
                    <div class="guided-large-input-wrap">
                        <input type="text" class="form-control guided-large-input" id="guided_name" 
                            value="${esc(nameVal)}" placeholder="Your Full Name">
                        <div class="guided-input-hint"><i class="fa fa-keyboard"></i> Press <kbd>Enter</kbd> or click <strong>Next</strong> below</div>
                    </div>
                </div>`;
            } else if (slide === 5) {
                html = `
                <div class="guided-slide active">
                    <div class="guided-slide-title">Want to add more details?</div>
                    <div class="guided-slide-desc">You can add optional info like company name &amp; address, or skip directly to check-in</div>
                    <div class="guided-cards-grid" style="max-width:380px;margin:24px auto;">
                        <div class="guided-option-card guided-yes-card" id="guidedOptYes">
                            <div class="card-icon" style="color:#10b981;"><i class="fa fa-plus-circle"></i></div>
                            <div class="card-label">Yes, add details</div>
                        </div>
                        <div class="guided-option-card guided-no-card" id="guidedOptNo">
                            <div class="card-icon" style="color:#64748b;"><i class="fa fa-arrow-right"></i></div>
                            <div class="card-label">No, skip to check-in</div>
                        </div>
                    </div>
                </div>`;
            } else if (slide === 6) {
                const companyVal = $('#company_name').val() || '';
                html = `
                <div class="guided-slide active">
                    <div class="guided-slide-title">Company / Organisation</div>
                    <div class="guided-slide-desc">Optional &mdash; which company or organisation are you from?</div>
                    <div class="guided-large-input-wrap">
                        <input type="text" class="form-control guided-large-input" id="guided_company_name"
                            value="${esc(companyVal)}" placeholder="e.g. Acme Corp">
                        <div class="guided-input-hint"><i class="fa fa-keyboard"></i> Press <kbd>Enter</kbd> or click <strong>Next</strong> (optional)</div>
                    </div>
                </div>`;
            } else if (slide === 7) {
                const addressVal = $('#address').val() || '';
                html = `
                <div class="guided-slide active">
                    <div class="guided-slide-title">Address</div>
                    <div class="guided-slide-desc">Optional &mdash; enter your home or office address</div>
                    <div class="guided-large-input-wrap">
                        <input type="text" class="form-control guided-large-input" id="guided_address"
                            value="${esc(addressVal)}" placeholder="e.g. 12, MG Road, Mumbai">
                        <div class="guided-input-hint"><i class="fa fa-keyboard"></i> Press <kbd>Enter</kbd> or click <strong>Done – Review</strong></div>
                    </div>
                </div>`;
            }
            return html;
        }

        function buildDeliverySlideHtml(slide) {
            let html = '';
            if (slide === 1) {
                const currentVal = $('#delivery_type').val() || '';
                let cardsHtml = '';
                $('#delivery_type option').each(function () {
                    const val = $(this).val();
                    const text = $(this).text().trim();
                    if (val === '') return;
                    const activeCls = (val === currentVal) ? 'active' : '';
                    let icon = 'fa fa-box';
                    if (val === 'Courier') icon = 'fa fa-box';
                    else if (val === 'Food Delivery') icon = 'fa fa-hamburger';
                    else if (val === 'Office Supplies') icon = 'fa fa-paperclip';
                    else if (val === 'Heavy Cargo') icon = 'fa fa-truck-loading';
                    else if (val === 'Miscellaneous') icon = 'fa fa-cubes';
                    
                    cardsHtml += `
                        <div class="guided-option-card ${activeCls}" data-val="${esc(val)}">
                            <div class="card-icon"><i class="${icon}"></i></div>
                            <div class="card-label">${esc(text)}</div>
                        </div>`;
                });
                html = `
                <div class="guided-slide active">
                    <div class="guided-slide-title">What type of delivery is this?</div>
                    <div class="guided-slide-desc">Select delivery classification</div>
                    <div class="guided-cards-grid">
                        ${cardsHtml}
                    </div>
                </div>`;
            } else if (slide === 2) {
                const meetVal = $('#deliver_to').val() || '0';
                const meetText = meetVal !== '0' ? $('#deliver_to option:selected').text().trim() : '';
                html = `
                <div class="guided-slide active">
                    <div class="guided-slide-title">Who is this delivery for?</div>
                    <div class="guided-slide-desc">Type employee name to search</div>
                    <div class="guided-large-input-wrap">
                        <input type="text" class="form-control guided-large-input" id="guided_deliver_search" 
                            value="${esc(meetText)}" placeholder="Type name to search...">
                        <div class="employee-suggestion-list d-none" id="guidedDeliverSuggestions"></div>
                    </div>
                </div>`;
            } else if (slide === 3) {
                const vehicleVal = $('#vehicle_number_delivery').val() || '';
                html = `
                <div class="guided-slide active">
                    <div class="guided-slide-title">Delivery Vehicle Number</div>
                    <div class="guided-slide-desc">Optional - enter delivery vehicle registration plate</div>
                    <div class="guided-large-input-wrap">
                        <input type="text" class="form-control guided-large-input" id="guided_vehicle_number" 
                            value="${esc(vehicleVal)}" placeholder="e.g. MH 12 AB 1234">
                        <div class="guided-input-hint"><i class="fa fa-keyboard"></i> Press <kbd>Enter</kbd> or click <strong>Next</strong> to continue (optional)</div>
                    </div>
                </div>`;
            } else if (slide === 4) {
                const phoneVal = $('#delivery_phone').val() || '';
                html = `
                <div class="guided-slide active">
                    <div class="guided-slide-title">Contact Phone Number</div>
                    <div class="guided-slide-desc">Optional - enter rider contact number</div>
                    <div class="guided-large-input-wrap">
                        <input type="text" class="form-control guided-large-input" id="guided_delivery_phone" 
                            value="${esc(phoneVal)}" placeholder="e.g. 9876543210" maxlength="15">
                        <div class="guided-input-hint"><i class="fa fa-keyboard"></i> Press <kbd>Enter</kbd> or click <strong>Next</strong> to continue (optional)</div>
                    </div>
                </div>`;
            } else if (slide === 5) {
                const companyVal = $('#delivery_company_person').val() || '';
                const newCompanyVal = $('#new_delivery_company').val() || '';
                let cardsHtml = '';
                $('#delivery_company_person option').each(function () {
                    const val = $(this).val();
                    const text = $(this).text().trim();
                    if (val === '') return;
                    const activeCls = (val === companyVal) ? 'active' : '';
                    let icon = 'fa fa-building';
                    const txtLower = text.toLowerCase();
                    const valLower = val.toLowerCase();
                    if (txtLower.includes('amazon') || valLower === 'amz') icon = 'fab fa-amazon';
                    else if (txtLower.includes('dhl') || valLower === 'dhl') icon = 'fa fa-truck';
                    else if (txtLower.includes('fedex') || valLower === 'fdx') icon = 'fa fa-shipping-fast';
                    else if (txtLower.includes('blue dart') || txtLower.includes('bluedart') || valLower === 'bld') icon = 'fa fa-plane';
                    else if (txtLower.includes('swiggy') || txtLower.includes('zomato') || txtLower.includes('food') || txtLower.includes('rider') || txtLower.includes('blinkit') || txtLower.includes('zepto') || valLower === 'swz') icon = 'fa fa-motorcycle';
                    else if (val === '__new__') icon = 'fa fa-edit';
                    
                    cardsHtml += `
                        <div class="guided-option-card ${activeCls}" data-val="${esc(val)}">
                            <div class="card-icon"><i class="${icon}"></i></div>
                            <div class="card-label">${esc(text)}</div>
                        </div>`;
                });
                html = `
                <div class="guided-slide active">
                    <div class="guided-slide-title">Which company is this delivery from?</div>
                    <div class="guided-slide-desc">Select company or add a custom one</div>
                    <div class="guided-cards-grid">
                        ${cardsHtml}
                    </div>
                    <div class="guided-other-purpose-wrap ${companyVal === '__new__' ? '' : 'd-none'}" id="guidedNewCompanyWrap">
                        <div class="input-group">
                            <input type="text" class="form-control guided-large-input" id="guided_new_company" 
                                value="${esc(newCompanyVal)}" placeholder="Enter company name...">
                            <button type="button" class="btn btn-primary btn-add-inline-other px-4" data-type="delivery_company" data-input-id="guided_new_company" data-select-id="delivery_company_person"><i class="fa fa-plus"></i> Add</button>
                        </div>
                    </div>
                </div>`;
            } else if (slide === 6) {
                html = `
                <div class="guided-slide active">
                    <div class="guided-slide-title">Want to add more details?</div>
                    <div class="guided-slide-desc">You can add AWB number, package count &amp; security info, or skip to check-in</div>
                    <div class="guided-cards-grid" style="max-width:380px;margin:24px auto;">
                        <div class="guided-option-card guided-yes-card" id="guidedOptYes">
                            <div class="card-icon" style="color:#10b981;"><i class="fa fa-plus-circle"></i></div>
                            <div class="card-label">Yes, add details</div>
                        </div>
                        <div class="guided-option-card guided-no-card" id="guidedOptNo">
                            <div class="card-icon" style="color:#64748b;"><i class="fa fa-arrow-right"></i></div>
                            <div class="card-label">No, skip to check-in</div>
                        </div>
                    </div>
                </div>`;
            } else if (slide === 7) {
                const awbVal = $('#delivery_awb_no').val() || '';
                html = `
                <div class="guided-slide active">
                    <div class="guided-slide-title">AWB / Reference No.</div>
                    <div class="guided-slide-desc">Optional &mdash; enter airway bill or reference number</div>
                    <div class="guided-large-input-wrap">
                        <input type="text" class="form-control guided-large-input" id="guided_awb_no"
                            value="${esc(awbVal)}" placeholder="e.g. 1234567890">
                        <div class="guided-input-hint"><i class="fa fa-keyboard"></i> Press <kbd>Enter</kbd> or click <strong>Next</strong> (optional)</div>
                    </div>
                </div>`;
            } else if (slide === 8) {
                const pkgVal = $('#package_count').val() || '';
                html = `
                <div class="guided-slide active">
                    <div class="guided-slide-title">Package Count</div>
                    <div class="guided-slide-desc">Optional &mdash; how many packages / items are in this delivery?</div>
                    <div class="guided-large-input-wrap">
                        <input type="number" min="1" class="form-control guided-large-input" id="guided_package_count"
                            value="${esc(pkgVal)}" placeholder="e.g. 3">
                        <div class="guided-input-hint"><i class="fa fa-keyboard"></i> Press <kbd>Enter</kbd> or click <strong>Next</strong> (optional)</div>
                    </div>
                </div>`;
            } else if (slide === 9) {
                const secCleared = $('#security_cleared').is(':checked');
                html = `
                <div class="guided-slide active">
                    <div class="guided-slide-title">Security Check</div>
                    <div class="guided-slide-desc">Has this delivery been security-cleared?</div>
                    <div class="guided-cards-grid" style="max-width:340px;margin:24px auto;">
                        <div class="guided-option-card ${secCleared ? 'active' : ''}" id="guidedSecYes" data-val="yes">
                            <div class="card-icon" style="color:#10b981;"><i class="fa fa-shield-alt"></i></div>
                            <div class="card-label">Yes, cleared</div>
                        </div>
                        <div class="guided-option-card ${!secCleared ? 'active' : ''}" id="guidedSecNo" data-val="no">
                            <div class="card-icon" style="color:#ef4444;"><i class="fa fa-times-circle"></i></div>
                            <div class="card-label">Not yet</div>
                        </div>
                    </div>
                </div>`;
            }
            return html;
        }

        function restorePrintViewDocuments() {
            const table = $('#printviewdocuments');
            if (table.length) {
                if (table.parent().attr('id') === 'guided_printviewdocuments_container') {
                    $('#vendor_supplierDiv .additional-fields-section').append(table);
                }
            }
        }

        function buildServiceSlideHtml(slide) {
            let html = '';
            if (slide === 1) {
                const currentVal = $('#service_company').val() || '';
                const newCompanyVal = $('#new_service_company').val() || '';
                let cardsHtml = '';
                $('#service_company option').each(function () {
                    const val = $(this).val();
                    const text = $(this).text().trim();
                    if (val === '') return;
                    const activeCls = (val === currentVal) ? 'active' : '';
                    let icon = 'fa fa-tools';
                    if (val === '__new__') icon = 'fa fa-edit';
                    cardsHtml += `
                        <div class="guided-option-card ${activeCls}" data-val="${esc(val)}">
                            <div class="card-icon"><i class="${icon}"></i></div>
                            <div class="card-label">${esc(text)}</div>
                        </div>`;
                });
                html = `
                <div class="guided-slide active">
                    <div class="guided-slide-title">Which service company are you from?</div>
                    <div class="guided-slide-desc">Select company or specify if not in the list</div>
                    <div class="guided-cards-grid">
                        ${cardsHtml}
                    </div>
                    <div class="guided-other-purpose-wrap ${currentVal === '__new__' ? '' : 'd-none'}" id="guidedServiceCompanyWrap">
                        <div class="input-group">
                            <input type="text" class="form-control guided-large-input" id="guided_new_service_company" 
                                value="${esc(newCompanyVal)}" placeholder="Enter company name...">
                            <button type="button" class="btn btn-primary btn-add-inline-other px-4" data-type="service_company" data-input-id="guided_new_service_company" data-select-id="service_company"><i class="fa fa-plus"></i> Add</button>
                        </div>
                    </div>
                </div>`;
            } else if (slide === 2) {
                const meetVal = $('#authorized_employee').val() || '0';
                const meetText = meetVal !== '0' ? $('#authorized_employee option:selected').text().trim() : '';
                html = `
                <div class="guided-slide active">
                    <div class="guided-slide-title">Who authorized your service?</div>
                    <div class="guided-slide-desc">Type employee name to search and select</div>
                    <div class="guided-large-input-wrap">
                        <input type="text" class="form-control guided-large-input" id="guided_auth_search" 
                            value="${esc(meetText)}" placeholder="Type name to search...">
                        <div class="employee-suggestion-list d-none" id="guidedAuthSuggestions"></div>
                    </div>
                </div>`;
            } else if (slide === 3) {
                const phoneVal = $('#service_phone').val() || '';
                html = `
                <div class="guided-slide active">
                    <div class="guided-slide-title">Contact Phone Number</div>
                    <div class="guided-slide-desc">Enter your 10-digit mobile number</div>
                    <div class="guided-large-input-wrap">
                        <input type="text" class="form-control guided-large-input" id="guided_service_phone" 
                            value="${esc(phoneVal)}" placeholder="e.g. 9876543210" maxlength="15">
                        <div class="guided-input-hint"><i class="fa fa-keyboard"></i> Press <kbd>Enter</kbd> or click <strong>Next</strong></div>
                    </div>
                </div>`;
            } else if (slide === 4) {
                const nameVal = $('#service_person').val() || '';
                html = `
                <div class="guided-slide active">
                    <div class="guided-slide-title">What is your name?</div>
                    <div class="guided-slide-desc">Enter your full name</div>
                    <div class="guided-large-input-wrap">
                        <input type="text" class="form-control guided-large-input" id="guided_service_person" 
                            value="${esc(nameVal)}" placeholder="Your Full Name">
                        <div class="guided-input-hint"><i class="fa fa-keyboard"></i> Press <kbd>Enter</kbd> or click <strong>Next</strong></div>
                    </div>
                </div>`;
            } else if (slide === 5) {
                html = `
                <div class="guided-slide active">
                    <div class="guided-slide-title">Want to add more details?</div>
                    <div class="guided-slide-desc">You can add classification, work permit, safety briefing, and ID card info</div>
                    <div class="guided-cards-grid" style="max-width:380px;margin:24px auto;">
                        <div class="guided-option-card guided-yes-card" id="guidedOptYes">
                            <div class="card-icon" style="color:#10b981;"><i class="fa fa-plus-circle"></i></div>
                            <div class="card-label">Yes, add details</div>
                        </div>
                        <div class="guided-option-card guided-no-card" id="guidedOptNo">
                            <div class="card-icon" style="color:#64748b;"><i class="fa fa-arrow-right"></i></div>
                            <div class="card-label">No, skip to check-in</div>
                        </div>
                    </div>
                </div>`;
            } else if (slide === 6) {
                const currentVal = $('#service_classification').val() || '';
                let cardsHtml = '';
                $('#service_classification option').each(function () {
                    const val = $(this).val();
                    const text = $(this).text().trim();
                    if (val === '') return;
                    const activeCls = (val === currentVal) ? 'active' : '';
                    let icon = 'fa fa-tools';
                    if (val === 'IT Support') icon = 'fa fa-laptop';
                    else if (val === 'HVAC Maintenance') icon = 'fa fa-wind';
                    else if (val === 'Electrical') icon = 'fa fa-bolt';
                    else if (val === 'Plumbing') icon = 'fa fa-faucet';
                    else if (val === 'Cleaning') icon = 'fa fa-broom';
                    else if (val === 'General Maintenance') icon = 'fa fa-hammer';
                    
                    cardsHtml += `
                        <div class="guided-option-card ${activeCls}" data-val="${esc(val)}">
                            <div class="card-icon"><i class="${icon}"></i></div>
                            <div class="card-label">${esc(text)}</div>
                        </div>`;
                });
                html = `
                <div class="guided-slide active">
                    <div class="guided-slide-title">Service Classification</div>
                    <div class="guided-slide-desc">Select service category</div>
                    <div class="guided-cards-grid">
                        ${cardsHtml}
                    </div>
                </div>`;
            } else if (slide === 7) {
                const permitVal = $('#work_permit_no').val() || '';
                html = `
                <div class="guided-slide active">
                    <div class="guided-slide-title">Work Permit Number</div>
                    <div class="guided-slide-desc">Optional &mdash; enter permit reference number</div>
                    <div class="guided-large-input-wrap">
                        <input type="text" class="form-control guided-large-input" id="guided_work_permit_no"
                            value="${esc(permitVal)}" placeholder="Permit No.">
                        <div class="guided-input-hint"><i class="fa fa-keyboard"></i> Press <kbd>Enter</kbd> or click <strong>Next</strong></div>
                    </div>
                </div>`;
            } else if (slide === 8) {
                const briefingCleared = $('#safety_briefing').is(':checked');
                html = `
                <div class="guided-slide active">
                    <div class="guided-slide-title">Safety Briefing</div>
                    <div class="guided-slide-desc">Has the safety briefing been completed?</div>
                    <div class="guided-cards-grid" style="max-width:340px;margin:24px auto;">
                        <div class="guided-option-card ${briefingCleared ? 'active' : ''}" id="guidedSafetyYes" data-val="yes">
                            <div class="card-icon" style="color:#10b981;"><i class="fa fa-hard-hat"></i></div>
                            <div class="card-label">Yes, completed</div>
                        </div>
                        <div class="guided-option-card ${!briefingCleared ? 'active' : ''}" id="guidedSafetyNo" data-val="no">
                            <div class="card-icon" style="color:#ef4444;"><i class="fa fa-times-circle"></i></div>
                            <div class="card-label">Not yet</div>
                        </div>
                    </div>
                </div>`;
            } else if (slide === 9) {
                const cardType = $('#idcard_type_service').val() || '0';
                const cardNo = $('#idcard_number_service').val() || '';
                let cardTypeOptions = '<option value="0">Select ID Card</option>';
                $('#idcard_type_service option').each(function () {
                    const val = $(this).val();
                    const text = $(this).text().trim();
                    if (val === '0' || val === '') return;
                    const sel = (val === cardType) ? 'selected' : '';
                    cardTypeOptions += `<option value="${esc(val)}" ${sel}>${esc(text)}</option>`;
                });
                html = `
                <div class="guided-slide active">
                    <div class="guided-slide-title">Identity Card Verification</div>
                    <div class="guided-slide-desc">Select ID card type and enter its number</div>
                    <div class="guided-large-input-wrap">
                        <select class="form-select guided-large-input mb-3" id="guided_idcard_type_service" style="height:auto;padding:12px;">
                            ${cardTypeOptions}
                        </select>
                        <input type="text" class="form-control guided-large-input" id="guided_idcard_number_service" 
                            value="${esc(cardNo)}" placeholder="ID Card Number">
                        <div class="guided-input-hint"><i class="fa fa-keyboard"></i> Press <kbd>Enter</kbd> or click <strong>Done – Review</strong></div>
                    </div>
                </div>`;
            }
            return html;
        }

        function buildEmployeeSlideHtml(slide) {
            let html = '';
            const isTemp = $('#emp_temp_enable').is(':checked');

            if (slide === 1) {
                html = `
                <div class="guided-slide active">
                    <div class="guided-slide-title">Select Check-in Type</div>
                    <div class="guided-slide-desc">Standard employee check-in or issue temporary pass</div>
                    <div class="guided-cards-grid" style="max-width:440px;margin:24px auto;">
                        <div class="guided-option-card ${!isTemp ? 'active' : ''}" id="guidedEmpTypeStd">
                            <div class="card-icon" style="color:#3b82f6;"><i class="fa fa-user-check"></i></div>
                            <div class="card-label">Standard Check-in</div>
                        </div>
                        <div class="guided-option-card ${isTemp ? 'active' : ''}" id="guidedEmpTypeTemp">
                            <div class="card-icon" style="color:#8b5cf6;"><i class="fa fa-key"></i></div>
                            <div class="card-label">Issue Temp Pass</div>
                        </div>
                    </div>
                </div>`;
            } else {
                if (!isTemp) {
                    if (slide === 2) {
                        const empCode = $('#employee_code').val() || '';
                        html = `
                        <div class="guided-slide active">
                            <div class="guided-slide-title">Enter Employee ID</div>
                            <div class="guided-slide-desc">Type your Employee Code and press Enter to search</div>
                            <div class="guided-large-input-wrap">
                                <input type="text" class="form-control guided-large-input" id="guided_employee_code" 
                                     value="${esc(empCode)}" placeholder="e.g. EMP123">
                                <div class="guided-input-hint"><i class="fa fa-keyboard"></i> Press <kbd>Enter</kbd> or click <strong>Next</strong> to search</div>
                            </div>
                        </div>`;
                    }
                } else {
                    const reasonVal = $('input[name="emp_temp_reason"]:checked').val() || 'forgot';
                    const isCross = (reasonVal === 'xbranch');

                    if (slide === 2) {
                        html = `
                        <div class="guided-slide active">
                            <div class="guided-slide-title">Reason for Temporary Pass</div>
                            <div class="guided-slide-desc">Select why a temporary pass is needed</div>
                            <div class="guided-cards-grid">
                                <div class="guided-option-card ${reasonVal === 'forgot' ? 'active' : ''}" data-val="forgot">
                                    <div class="card-icon"><i class="fa fa-id-card-alt"></i></div>
                                    <div class="card-label">Forgot ID Card</div>
                                </div>
                                <div class="guided-option-card ${reasonVal === 'new' ? 'active' : ''}" data-val="new">
                                    <div class="card-icon"><i class="fa fa-user-plus"></i></div>
                                    <div class="card-label">New Onboarding</div>
                                </div>
                                <div class="guided-option-card ${reasonVal === 'xbranch' ? 'active' : ''}" data-val="xbranch">
                                    <div class="card-icon"><i class="fa fa-code-branch"></i></div>
                                    <div class="card-label">Cross Branch Visit</div>
                                </div>
                            </div>
                        </div>`;
                    } else if (slide === 3) {
                        const phoneVal = $('#emp_temp_phone').val() || '';
                        html = `
                        <div class="guided-slide active">
                            <div class="guided-slide-title">Mobile Number</div>
                            <div class="guided-slide-desc">Enter your contact number</div>
                            <div class="guided-large-input-wrap">
                                <input type="text" class="form-control guided-large-input" id="guided_emp_temp_phone" 
                                    value="${esc(phoneVal)}" placeholder="e.g. 9876543210" maxlength="15">
                                <div class="guided-input-hint"><i class="fa fa-keyboard"></i> Press <kbd>Enter</kbd> to continue</div>
                            </div>
                        </div>`;
                    } else if (slide === 4) {
                        const nameVal = $('#emp_temp_name').val() || '';
                        html = `
                        <div class="guided-slide active">
                            <div class="guided-slide-title">Full Name</div>
                            <div class="guided-slide-desc">Enter employee's name</div>
                            <div class="guided-large-input-wrap">
                                <input type="text" class="form-control guided-large-input" id="guided_emp_temp_name" 
                                    value="${esc(nameVal)}" placeholder="Full Name">
                                <div class="guided-input-hint"><i class="fa fa-keyboard"></i> Press <kbd>Enter</kbd> to continue</div>
                            </div>
                        </div>`;
                    } else if (isCross && slide === 5) {
                        const meetVal = $('#emp_temp_whom_to_meet').val() || '0';
                        const meetText = meetVal !== '0' ? $('#emp_temp_whom_to_meet option:selected').text().trim() : '';
                        html = `
                        <div class="guided-slide active">
                            <div class="guided-slide-title">Whom would you like to meet?</div>
                            <div class="guided-slide-desc">Type employee name to search and select</div>
                            <div class="guided-large-input-wrap">
                                <input type="text" class="form-control guided-large-input" id="guided_emp_temp_meet_search" 
                                    value="${esc(meetText)}" placeholder="Type name to search...">
                                <div class="employee-suggestion-list d-none" id="guidedEmpTempMeetSuggestions"></div>
                            </div>
                        </div>`;
                    } else if ((isCross && slide === 6) || (!isCross && slide === 5)) {
                        if (typeof window.updateEmployeeTempId === 'function') {
                            window.updateEmployeeTempId();
                        }
                        const cardId = $('#visiting_company_id_employee_temp').val() || '';
                        html = `
                        <div class="guided-slide active">
                            <div class="guided-slide-title">Temporary Card ID</div>
                            <div class="guided-slide-desc">Assign a physical temporary card number</div>
                            <div class="guided-large-input-wrap">
                                <input type="text" class="form-control guided-large-input" id="guided_visiting_company_id_employee_temp" 
                                    value="${esc(cardId)}" placeholder="Temp Badge ID">
                                <div class="guided-input-hint"><i class="fa fa-keyboard"></i> Press <kbd>Enter</kbd> or click <strong>Done &ndash; Review</strong></div>
                            </div>
                        </div>`;
                    }
                }
            }
            return html;
        }

        function buildDoctorSlideHtml(slide) {
            let html = '';
            if (slide === 1) {
                const phoneVal = $('#doctor_phone').val() || '';
                html = `
                <div class="guided-slide active">
                    <div class="guided-slide-title">Doctor's Mobile Number</div>
                    <div class="guided-slide-desc">Enter 10-digit mobile number</div>
                    <div class="guided-large-input-wrap">
                        <input type="text" class="form-control guided-large-input" id="guided_doctor_phone" 
                            value="${esc(phoneVal)}" placeholder="e.g. 9876543210" maxlength="15">
                        <div class="guided-input-hint"><i class="fa fa-keyboard"></i> Press <kbd>Enter</kbd> to continue</div>
                    </div>
                </div>`;
            } else if (slide === 2) {
                const nameVal = $('#doctor_name').val() || '';
                html = `
                <div class="guided-slide active">
                    <div class="guided-slide-title">Doctor's Name</div>
                    <div class="guided-slide-desc">Enter full name with prefix Dr.</div>
                    <div class="guided-large-input-wrap">
                        <input type="text" class="form-control guided-large-input" id="guided_doctor_name" 
                            value="${esc(nameVal)}" placeholder="Dr. Name">
                        <div class="guided-input-hint"><i class="fa fa-keyboard"></i> Press <kbd>Enter</kbd> to continue</div>
                    </div>
                </div>`;
            } else if (slide === 3) {
                html = `
                <div class="guided-slide active">
                    <div class="guided-slide-title">Want to add more details?</div>
                    <div class="guided-slide-desc">You can add specialization, emergency status, and ID verification</div>
                    <div class="guided-cards-grid" style="max-width:380px;margin:24px auto;">
                        <div class="guided-option-card guided-yes-card" id="guidedOptYes">
                            <div class="card-icon" style="color:#10b981;"><i class="fa fa-plus-circle"></i></div>
                            <div class="card-label">Yes, add details</div>
                        </div>
                        <div class="guided-option-card guided-no-card" id="guidedOptNo">
                            <div class="card-icon" style="color:#64748b;"><i class="fa fa-arrow-right"></i></div>
                            <div class="card-label">No, skip to check-in</div>
                        </div>
                    </div>
                </div>`;
            } else if (slide === 4) {
                const specVal = $('#doctor_specialization').val() || '';
                html = `
                <div class="guided-slide active">
                    <div class="guided-slide-title">Specialization / Department</div>
                    <div class="guided-slide-desc">Optional &mdash; enter doctor's specialty or department</div>
                    <div class="guided-large-input-wrap">
                        <input type="text" class="form-control guided-large-input" id="guided_doctor_specialization"
                            value="${esc(specVal)}" placeholder="e.g. Cardiology, Pediatrics">
                        <div class="guided-input-hint"><i class="fa fa-keyboard"></i> Press <kbd>Enter</kbd> to continue</div>
                    </div>
                </div>`;
            } else if (slide === 5) {
                const isEmergency = $('#is_emergency').is(':checked');
                html = `
                <div class="guided-slide active">
                    <div class="guided-slide-title">Emergency Visit?</div>
                    <div class="guided-slide-desc">Is this doctor check-in for an emergency?</div>
                    <div class="guided-cards-grid" style="max-width:340px;margin:24px auto;">
                        <div class="guided-option-card ${isEmergency ? 'active' : ''}" id="guidedEmergencyYes" data-val="yes">
                            <div class="card-icon" style="color:#ef4444;"><i class="fa fa-exclamation-triangle"></i></div>
                            <div class="card-label">Yes, Emergency</div>
                        </div>
                        <div class="guided-option-card ${!isEmergency ? 'active' : ''}" id="guidedEmergencyNo" data-val="no">
                            <div class="card-icon" style="color:#3b82f6;"><i class="fa fa-check-circle"></i></div>
                            <div class="card-label">Standard Visit</div>
                        </div>
                    </div>
                </div>`;
            } else if (slide === 6) {
                const cardType = $('#doctor_idcard_type').val() || '0';
                const cardNo = $('#doctor_idcard_number').val() || '';
                let cardTypeOptions = '<option value="0">Select ID Card</option>';
                $('#doctor_idcard_type option').each(function () {
                    const val = $(this).val();
                    const text = $(this).text().trim();
                    if (val === '0' || val === '') return;
                    const sel = (val === cardType) ? 'selected' : '';
                    cardTypeOptions += `<option value="${esc(val)}" ${sel}>${esc(text)}</option>`;
                });
                html = `
                <div class="guided-slide active">
                    <div class="guided-slide-title">Identity Card Verification</div>
                    <div class="guided-slide-desc">Select ID card type and enter its number</div>
                    <div class="guided-large-input-wrap">
                        <select class="form-select guided-large-input mb-3" id="guided_doctor_idcard_type" style="height:auto;padding:12px;">
                            ${cardTypeOptions}
                        </select>
                        <input type="text" class="form-control guided-large-input" id="guided_doctor_idcard_number" 
                            value="${esc(cardNo)}" placeholder="ID Card Number">
                        <div class="guided-input-hint"><i class="fa fa-keyboard"></i> Press <kbd>Enter</kbd> or click <strong>Done – Review</strong></div>
                    </div>
                </div>`;
            }
            return html;
        }

        function buildVendorSupplierSlideHtml(slide) {
            let html = '';
            const refType = $('#vendor_supplier_document_type').val() || 'NOREF';
            const isNoref = (refType === 'NOREF');

            let activeSlide = slide;
            if (isNoref && slide >= 5) {
                activeSlide = slide + 1;
            }

            if (activeSlide === 1) {
                const deptVal = $('#vendor_supplier_department_id').val() || '';
                let cardsHtml = '';
                $('#vendor_supplier_department_id option').each(function () {
                    const val = $(this).val();
                    const text = $(this).text().trim();
                    if (val === '' || val === '0' || text.toLowerCase().indexOf('select') !== -1) return;
                    const activeCls = (val == deptVal) ? 'active' : '';
                    cardsHtml += `
                        <div class="guided-option-card ${activeCls}" data-val="${esc(val)}">
                            <div class="card-icon"><i class="fa fa-building"></i></div>
                            <div class="card-label">${esc(text)}</div>
                        </div>`;
                });
                html = `
                <div class="guided-slide active">
                    <div class="guided-slide-title">Select Department</div>
                    <div class="guided-slide-desc">Please choose the department to visit</div>
                    <div class="guided-cards-grid">
                        ${cardsHtml}
                    </div>
                </div>`;
            } else if (activeSlide === 2) {
                const meetVal = $('#whom_to_meet_vendor').val() || '0';
                const meetText = meetVal !== '0' ? $('#whom_to_meet_vendor option:selected').text().trim() : '';
                html = `
                <div class="guided-slide active">
                    <div class="guided-slide-title">Whom would you like to meet?</div>
                    <div class="guided-slide-desc">Type employee name to search and select</div>
                    <div class="guided-large-input-wrap">
                        <input type="text" class="form-control guided-large-input" id="guided_vendor_meet_search" 
                            value="${esc(meetText)}" placeholder="Type name to search...">
                        <div class="employee-suggestion-list d-none" id="guidedVendorMeetSuggestions"></div>
                    </div>
                </div>`;
            } else if (activeSlide === 3) {
                const entryVal = $('#vendor_supplier_entry_type').val() || '';
                let cardsHtml = '';
                $('#vendor_supplier_entry_type option').each(function () {
                    const val = $(this).val();
                    const text = $(this).text().trim();
                    if (val === '') return;
                    const activeCls = (val === entryVal) ? 'active' : '';
                    cardsHtml += `
                        <div class="guided-option-card ${activeCls}" data-val="${esc(val)}">
                            <div class="card-icon"><i class="fa fa-sign-in-alt"></i></div>
                            <div class="card-label">${esc(text)}</div>
                        </div>`;
                });
                html = `
                <div class="guided-slide active">
                    <div class="guided-slide-title">Entry Type</div>
                    <div class="guided-slide-desc">Select the type of entry for materials/vehicle</div>
                    <div class="guided-cards-grid">
                        ${cardsHtml}
                    </div>
                </div>`;
            } else if (activeSlide === 4) {
                const docType = $('#vendor_supplier_document_type').val() || 'NOREF';
                let cardsHtml = '';
                $('#vendor_supplier_document_type option').each(function () {
                    const val = $(this).val();
                    const text = $(this).text().trim();
                    if (val === '') return;
                    const activeCls = (val === docType) ? 'active' : '';
                    cardsHtml += `
                        <div class="guided-option-card ${activeCls}" data-val="${esc(val)}">
                            <div class="card-icon"><i class="fa fa-file-invoice"></i></div>
                            <div class="card-label">${esc(text)}</div>
                        </div>`;
                });
                html = `
                <div class="guided-slide active">
                    <div class="guided-slide-title">Reference Document Type</div>
                    <div class="guided-slide-desc">Choose if you have a PO, RGP, NRGP, or PI document</div>
                    <div class="guided-cards-grid">
                        ${cardsHtml}
                    </div>
                </div>`;
            } else if (activeSlide === 5) {
                const txnId = $('#vendor_supplier_transaction_id').val() || '';
                let cardsHtml = '';
                $('#vendor_supplier_transaction_id option').each(function () {
                    const val = $(this).val();
                    const text = $(this).text().trim();
                    if (val === '') return;
                    const activeCls = (val === txnId) ? 'active' : '';
                    cardsHtml += `
                        <div class="guided-option-card ${activeCls}" data-val="${esc(val)}">
                            <div class="card-icon"><i class="fa fa-hashtag"></i></div>
                            <div class="card-label">${esc(text)}</div>
                        </div>`;
                });
                if (!cardsHtml) {
                    cardsHtml = '<div class="text-muted p-3">No reference documents found for this type.</div>';
                }
                html = `
                <div class="guided-slide active">
                    <div class="guided-slide-title">Select Document Number</div>
                    <div class="guided-slide-desc">Choose reference document for auto-populating items</div>
                    <div class="guided-cards-grid">
                        ${cardsHtml}
                    </div>
                </div>`;
            } else if (activeSlide === 6) {
                const vendorVal = $('#vendor_supplier_vendor_id').val() || '';
                const vendorText = (vendorVal && vendorVal !== '0') ? $('#vendor_supplier_vendor_id option:selected').text().trim() : '';

                let vendorSelectHtml = '';
                if (isNoref) {
                    let vendorOptions = '<option value="">Select Vendor/Supplier</option>';
                    $('#vendor_supplier_vendor_id option').each(function () {
                        const val = $(this).val();
                        const text = $(this).text().trim();
                        if (val === '' || text.toLowerCase().indexOf('select') !== -1) return;
                        const sel = (val == vendorVal) ? 'selected' : '';
                        vendorOptions += `<option value="${esc(val)}" ${sel}>${esc(text)}</option>`;
                    });
                    vendorSelectHtml = `
                    <div class="mb-3 text-start">
                        <label class="form-label font-weight-bold">Vendor / Supplier <b class="text-danger">*</b></label>
                        <select class="form-select guided-large-input" id="guided_vendor_supplier_vendor_id" style="height:auto;padding:12px;">
                            ${vendorOptions}
                        </select>
                    </div>`;
                } else {
                    vendorSelectHtml = `
                    <div class="alert alert-info py-2 px-3 mb-3 text-start d-flex align-items-center gap-2" style="border-radius:10px;">
                        <i class="fa fa-check-circle text-success" style="font-size:18px;"></i>
                        <div>
                            <div style="font-size:11px;color:#0369a1;text-transform:uppercase;font-weight:700;">Resolved Vendor</div>
                            <div class="font-weight-bold text-dark" style="font-size:14px;">${esc(vendorText || 'Resolved automatically')}</div>
                        </div>
                    </div>`;
                }

                html = `
                <div class="guided-slide active" style="max-width:100%; width:100%;">
                    <div class="guided-slide-title">Vendor &amp; Material Items</div>
                    <div class="guided-slide-desc">${isNoref ? 'Select Vendor and specify the material items' : 'Review dynamic material items populated from document'}</div>
                    <div style="max-width:680px; margin: 16px auto 0;" class="text-center">
                        ${vendorSelectHtml}
                        <div class="card p-3 border shadow-sm text-start" style="border-radius:12px; background:#fff;">
                            <div id="guided_printviewdocuments_container">
                            </div>
                        </div>
                    </div>
                </div>`;
            } else if (activeSlide === 7) {
                const driverName = $('#vendor_supplier_driver_name').val() || '';
                const driverMobile = $('#vendor_supplier_driver_mobile').val() || '';
                html = `
                <div class="guided-slide active">
                    <div class="guided-slide-title">Driver Information</div>
                    <div class="guided-slide-desc">Please enter driver's name and contact number</div>
                    <div class="guided-large-input-wrap">
                        <input type="text" class="form-control guided-large-input mb-3" id="guided_vendor_supplier_driver_name" 
                            value="${esc(driverName)}" placeholder="Driver Name">
                        <input type="text" class="form-control guided-large-input" id="guided_vendor_supplier_driver_mobile" 
                            value="${esc(driverMobile)}" placeholder="Mobile Number" maxlength="15">
                        <div class="guided-input-hint"><i class="fa fa-keyboard"></i> Press <kbd>Enter</kbd> or click <strong>Next</strong></div>
                    </div>
                </div>`;
            } else if (activeSlide === 8) {
                const vehicleType = $('#vendor_supplier_vehicle_type').val() || '';
                const vehicleNo = $('#vendor_supplier_vehicle_number').val() || '';

                let cardsHtml = '';
                $('#vendor_supplier_vehicle_type option').each(function () {
                    const val = $(this).val();
                    const text = $(this).text().trim();
                    if (val === '') return;
                    const activeCls = (val === vehicleType) ? 'active' : '';
                    cardsHtml += `
                        <div class="guided-option-card ${activeCls}" data-val="${esc(val)}" style="padding:10px 14px;">
                            <div class="card-icon" style="font-size:22px;margin-bottom:6px;"><i class="fa fa-truck"></i></div>
                            <div class="card-label" style="font-size:12px;">${esc(text)}</div>
                        </div>`;
                });
                html = `
                <div class="guided-slide active" style="max-width:100%; width:100%;">
                    <div class="guided-slide-title">Vehicle Details</div>
                    <div class="guided-slide-desc">Select vehicle type and enter registration number</div>
                    <div style="max-width: 580px; margin: 16px auto 0;">
                        <div class="guided-cards-grid mb-3" style="grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));gap:10px;">
                            ${cardsHtml}
                        </div>
                        <input type="text" class="form-control guided-large-input" id="guided_vendor_supplier_vehicle_number" 
                            value="${esc(vehicleNo)}" placeholder="Vehicle Number (e.g. MH 12 AB 1234)">
                        <div class="guided-input-hint"><i class="fa fa-keyboard"></i> Press <kbd>Enter</kbd> or click <strong>Done &ndash; Review</strong></div>
                    </div>
                </div>`;
            }
            return html;
        }

        function renderGuidedSlide() {
            const type = getType();

            // Safe restore BEFORE emptying container to prevent DOM destruction!
            restorePrintViewDocuments();

            const container = $('#guidedWizardContainer');
            container.empty();

            const totalSlides = getGuidedSlideCount(type);
            if (totalSlides === 0) return;

            // 1. Draw Progress Dots
            let dotsHtml = '<div class="guided-progress-dots">';
            for (let i = 1; i <= totalSlides; i++) {
                let cls = '';
                if (i === currentGuidedSlide) cls = 'active';
                else if (i < currentGuidedSlide) cls = 'completed';
                dotsHtml += `<div class="guided-dot ${cls}" data-slide="${i}"></div>`;
            }
            dotsHtml += '</div>';
            container.append(dotsHtml);

            // Bind click to progress dots
            container.find('.guided-dot').on('click', function () {
                const target = parseInt($(this).data('slide'), 10);
                if (target < currentGuidedSlide || validateGuidedSlide(currentGuidedSlide)) {
                    currentGuidedSlide = target;
                    renderGuidedSlide();
                    updateNextButtonText(2);
                }
            });

            // 2. Draw Slide Content
            let slideHtml = '';
            if (type === 'visitor' || type === 'others') {
                slideHtml = buildVisitorSlideHtml(currentGuidedSlide);
            } else if (type === 'delivery') {
                slideHtml = buildDeliverySlideHtml(currentGuidedSlide);
            } else if (type === 'service') {
                slideHtml = buildServiceSlideHtml(currentGuidedSlide);
            } else if (type === 'employee') {
                slideHtml = buildEmployeeSlideHtml(currentGuidedSlide);
            } else if (type === 'doctor') {
                slideHtml = buildDoctorSlideHtml(currentGuidedSlide);
            } else if (type === 'vendor_supplier') {
                slideHtml = buildVendorSupplierSlideHtml(currentGuidedSlide);
            }

            container.append(slideHtml);

            // Determine if the logical slide is Vendor & Material Items (Slide 6)
            let activeSlide = currentGuidedSlide;
            if (type === 'vendor_supplier') {
                const refType = $('#vendor_supplier_document_type').val() || 'NOREF';
                if (refType === 'NOREF' && currentGuidedSlide >= 5) {
                    activeSlide = currentGuidedSlide + 1;
                }
            }

            if (type === 'vendor_supplier' && activeSlide === 6) {
                setTimeout(function () {
                    $('#printviewdocuments').appendTo('#guided_printviewdocuments_container').removeClass('d-none');
                }, 50);
            }

            // Add standard switch helper at footer
            container.append(`
            <div class="guided-slide-footer">
                <span class="switch-standard-link" id="lnkSwitchStandard">
                    <i class="fa fa-th-list me-1"></i> Switch to Standard View
                </span>
            </div>`);

            $('#lnkSwitchStandard').on('click', function (e) {
                e.preventDefault();
                toggleGuidedMode();
            });

            // 3. Bind Event Handlers for Slide Controls
            bindSlideEvents(type, currentGuidedSlide);
        }

        function bindSlideEvents(type, slide) {
            if (type === 'visitor' || type === 'others') {
                if (slide === 1) {
                    const cards = $('#guidedWizardContainer .guided-option-card');
                    const purposeOtherWrap = $('#guidedOtherPurposeWrap');

                    cards.on('click', function () {
                        const val = $(this).data('val');
                        cards.removeClass('active');
                        $(this).addClass('active');

                        $(`input[name="purpose"][value="${val}"]`).prop('checked', true).trigger('change');

                        if (val === 'others') {
                            purposeOtherWrap.removeClass('d-none');
                            $('#guided_purpose_other').focus();
                        } else {
                            purposeOtherWrap.addClass('d-none');
                            setTimeout(function () {
                                advanceGuidedSlide();
                            }, 220);
                        }
                    });

                    $('#guided_purpose_other').on('input change keyup', function () {
                        $('#purpose_other').val($(this).val()).trigger('input');
                    });
                } else if (slide === 2) {
                    const searchInput = $('#guided_meet_search');
                    const suggestions = $('#guidedMeetSuggestions');

                    searchInput.on('input focus', function () {
                        const query = $(this).val().trim().toLowerCase();
                        suggestions.empty().addClass('d-none');

                        let matches = [];
                        $('#whom_to_meet option').each(function () {
                            const id = $(this).val();
                            const name = $(this).text().trim();
                            if (id !== '0' && name.toLowerCase().indexOf(query) !== -1) {
                                matches.push({ id: id, name: name });
                            }
                        });

                        if (matches.length > 0) {
                            suggestions.removeClass('d-none');
                            matches.slice(0, 5).forEach(m => {
                                suggestions.append(`<div class="employee-suggestion-item" data-id="${m.id}">${esc(m.name)}</div>`);
                            });

                            suggestions.find('.employee-suggestion-item').on('click', function () {
                                const id = $(this).data('id');
                                const name = $(this).text();
                                searchInput.val(name);
                                suggestions.addClass('d-none').empty();

                                $('#whom_to_meet').val(id).trigger('change');

                                setTimeout(function () {
                                    advanceGuidedSlide();
                                }, 220);
                            });
                        }
                    });

                    $(document).on('click.guidedMeetOutside', function (e) {
                        if (!$(e.target).closest('#guided_meet_search, #guidedMeetSuggestions').length) {
                            suggestions.addClass('d-none');
                            $(document).off('click.guidedMeetOutside');
                        }
                    });

                    searchInput.focus();
                } else if (slide === 3) {
                    const input = $('#guided_phone');
                    input.on('input change keyup', function () {
                        $('#phone').val($(this).val()).trigger('input');
                    });
                    // Quick-search: look up existing visitor on blur/Enter
                    input.on('blur keydown', function (e) {
                        if (e.type === 'blur' || e.key === 'Enter') {
                            if (e.type === 'keydown') e.preventDefault();
                            const phone = input.val().trim();
                            if (!/^[0-9]{7,15}$/.test(phone)) {
                                if (e.type === 'keydown') advanceGuidedSlide();
                                return;
                            }
                            $('#phone').val(phone);
                            // Ajax quick search
                            $.ajax({
                                url: 'ajax.php',
                                type: 'POST',
                                data: `page=getVisitorsByPhoneAndEmpCode&phone=${encodeURIComponent(phone)}`,
                                success: function (data) {
                                    var res; try { res = JSON.parse(data); } catch (ex) { }
                                    var records = (res && res.visitor_logs_res) ? res.visitor_logs_res : [];
                                    var first = records[0] || {};
                                    if (first.name) {
                                        // Pre-fill standard fields
                                        $('#name').val(first.name || '').trigger('change');
                                        if (first.idcard_type) $('#idcard_type').val(first.idcard_type).trigger('change');
                                        $('#idcard_number').val(first.idcard_number || '');
                                        if (first.company_name) $('#company_name').val(first.company_name);
                                        if (first.address) $('#address').val(first.address);
                                        if (typeof window.updateLiveBadgePreview === 'function') window.updateLiveBadgePreview();
                                        // Show quick-confirm banner
                                        var banner = $('<div class="guided-found-visitor-banner" id="guidedFoundBanner"></div>').html(`
                                            <div class="found-visitor-avatar"><i class="fa fa-user-check"></i></div>
                                            <div class="found-visitor-info">
                                                <div class="found-visitor-name">${esc(first.name)}</div>
                                                <div class="found-visitor-sub">Returning visitor &mdash; details auto-filled</div>
                                            </div>
                                            <div class="found-visitor-actions">
                                                <button type="button" class="btn-confirm-visitor" id="btnConfirmVisitor"><i class="fa fa-check me-1"></i> Yes, that's me &mdash; Skip to Check-in</button>
                                                <button type="button" class="btn-not-me" id="btnNotMe">Not me</button>
                                            </div>
                                        `);
                                        $('#guidedWizardContainer .guided-slide').after(banner);

                                        $('#btnConfirmVisitor').on('click', function () {
                                            currentStep = 3;
                                            showStep(currentStep);
                                        });
                                        $('#btnNotMe').on('click', function () {
                                            $('#name').val('').trigger('change');
                                            $('#company_name').val('');
                                            $('#address').val('');
                                            $('#guidedFoundBanner').remove();
                                            advanceGuidedSlide();
                                        });
                                    } else {
                                        advanceGuidedSlide();
                                    }
                                },
                                error: function () { advanceGuidedSlide(); }
                            });
                        }
                    });

                    // Listen for automatic changes to Name standard field
                    $('#name').on('change.guidedNameSync', function () {
                        if (currentGuidedSlide === 3 || currentGuidedSlide === 4) {
                            $('#guided_name').val($(this).val());
                        }
                    });

                    input.focus();
                } else if (slide === 4) {
                    const input = $('#guided_name');
                    input.on('input change keyup', function () {
                        $('#name').val($(this).val()).trigger('input');
                    });
                    input.on('keydown', function (e) {
                        if (e.key === 'Enter') { e.preventDefault(); advanceGuidedSlide(); }
                    });
                    input.focus();
                } else if (slide === 5) {
                    // Prompt: add more details?
                    $('#guidedOptYes').on('click', function () {
                        isOptionalExpanded = true;
                        currentGuidedSlide++;
                        renderGuidedSlide();
                        updateNextButtonText(2);
                    });
                    $('#guidedOptNo').on('click', function () {
                        const v2 = validateStep2();
                        if (v2) { currentStep = 3; showStep(currentStep); }
                    });
                } else if (slide === 6) {
                    const input = $('#guided_company_name');
                    input.on('input change keyup', function () {
                        $('#company_name').val($(this).val());
                    });
                    input.on('keydown', function (e) {
                        if (e.key === 'Enter') { e.preventDefault(); advanceGuidedSlide(); }
                    });
                    input.focus();
                } else if (slide === 7) {
                    const input = $('#guided_address');
                    input.on('input change keyup', function () {
                        $('#address').val($(this).val());
                    });
                    input.on('keydown', function (e) {
                        if (e.key === 'Enter') { e.preventDefault(); advanceGuidedSlide(); }
                    });
                    input.focus();
                }
            } else if (type === 'delivery') {
                if (slide === 1) {
                    const cards = $('#guidedWizardContainer .guided-option-card');
                    cards.on('click', function () {
                        const val = $(this).data('val');
                        cards.removeClass('active');
                        $(this).addClass('active');

                        $('#delivery_type').val(val).trigger('change');

                        setTimeout(function () {
                            advanceGuidedSlide();
                        }, 220);
                    });
                } else if (slide === 2) {
                    const searchInput = $('#guided_deliver_search');
                    const suggestions = $('#guidedDeliverSuggestions');

                    searchInput.on('input focus', function () {
                        const query = $(this).val().trim().toLowerCase();
                        suggestions.empty().addClass('d-none');

                        let matches = [];
                        $('#deliver_to option').each(function () {
                            const id = $(this).val();
                            const name = $(this).text().trim();
                            if (id !== '0' && name.toLowerCase().indexOf(query) !== -1) {
                                matches.push({ id: id, name: name });
                            }
                        });

                        if (matches.length > 0) {
                            suggestions.removeClass('d-none');
                            matches.slice(0, 5).forEach(m => {
                                suggestions.append(`<div class="employee-suggestion-item" data-id="${m.id}">${esc(m.name)}</div>`);
                            });

                            suggestions.find('.employee-suggestion-item').on('click', function () {
                                const id = $(this).data('id');
                                const name = $(this).text();
                                searchInput.val(name);
                                suggestions.addClass('d-none').empty();

                                $('#deliver_to').val(id).trigger('change');

                                setTimeout(function () {
                                    advanceGuidedSlide();
                                }, 220);
                            });
                        }
                    });

                    $(document).on('click.guidedDeliverOutside', function (e) {
                        if (!$(e.target).closest('#guided_deliver_search, #guidedDeliverSuggestions').length) {
                            suggestions.addClass('d-none');
                            $(document).off('click.guidedDeliverOutside');
                        }
                    });

                    searchInput.focus();
                } else if (slide === 3) {
                    const input = $('#guided_vehicle_number');
                    input.on('input change keyup', function () {
                        $('#vehicle_number_delivery').val($(this).val()).trigger('input');
                    });
                    // Enter key to advance
                    input.on('keydown', function (e) {
                        if (e.key === 'Enter') { e.preventDefault(); advanceGuidedSlide(); }
                    });
                    input.focus();
                } else if (slide === 4) {
                    const input = $('#guided_delivery_phone');
                    input.on('input change keyup', function () {
                        $('#delivery_phone').val($(this).val()).trigger('input');
                    });
                    // Enter key to advance
                    input.on('keydown', function (e) {
                        if (e.key === 'Enter') { e.preventDefault(); advanceGuidedSlide(); }
                    });
                    input.focus();
                } else if (slide === 5) {
                    const cards = $('#guidedWizardContainer .guided-option-card');
                    const newCompanyWrap = $('#guidedNewCompanyWrap');

                    cards.on('click', function () {
                        const val = $(this).data('val');
                        cards.removeClass('active');
                        $(this).addClass('active');

                        $('#delivery_company_person').val(val).trigger('change');

                        if (val === '__new__') {
                            newCompanyWrap.removeClass('d-none');
                            $('#guided_new_company').focus();
                        } else {
                            newCompanyWrap.addClass('d-none');
                            setTimeout(function () {
                                advanceGuidedSlide();
                            }, 220);
                        }
                    });

                    $('#guided_new_company').on('input change keyup', function () {
                        $('#new_delivery_company').val($(this).val()).trigger('input');
                    });
                } else if (slide === 6) {
                    // Prompt: add more details?
                    $('#guidedOptYes').on('click', function () {
                        isOptionalExpanded = true;
                        currentGuidedSlide++;
                        renderGuidedSlide();
                        updateNextButtonText(2);
                    });
                    $('#guidedOptNo').on('click', function () {
                        const v2 = validateStep2();
                        if (v2) { currentStep = 3; showStep(currentStep); }
                    });
                } else if (slide === 7) {
                    const input = $('#guided_awb_no');
                    input.on('input change keyup', function () {
                        $('#delivery_awb_no').val($(this).val());
                    });
                    input.on('keydown', function (e) {
                        if (e.key === 'Enter') { e.preventDefault(); advanceGuidedSlide(); }
                    });
                    input.focus();
                } else if (slide === 8) {
                    const input = $('#guided_package_count');
                    input.on('input change keyup', function () {
                        $('#package_count').val($(this).val());
                    });
                    input.on('keydown', function (e) {
                        if (e.key === 'Enter') { e.preventDefault(); advanceGuidedSlide(); }
                    });
                    input.focus();
                } else if (slide === 9) {
                    const secCards = $('#guidedSecYes, #guidedSecNo');
                    secCards.on('click', function () {
                        secCards.removeClass('active');
                        $(this).addClass('active');
                        const isYes = $(this).attr('id') === 'guidedSecYes';
                        $('#security_cleared').prop('checked', isYes);
                        setTimeout(function () { advanceGuidedSlide(); }, 220);
                    });
                }
            } else if (type === 'service') {
                if (slide === 1) {
                    const cards = $('#guidedWizardContainer .guided-option-card');
                    const compSelect = $('#service_company');
                    const newCompWrap = $('#guidedServiceCompanyWrap');

                    cards.on('click', function () {
                        const val = $(this).data('val');
                        cards.removeClass('active');
                        $(this).addClass('active');

                        compSelect.val(val).trigger('change');

                        if (val === '__new__') {
                            newCompWrap.removeClass('d-none');
                            $('#guided_new_service_company').focus();
                        } else {
                            newCompWrap.addClass('d-none');
                            setTimeout(function () {
                                advanceGuidedSlide();
                            }, 220);
                        }
                    });

                    $('#guided_new_service_company').on('input change keyup', function () {
                        $('#new_service_company').val($(this).val()).trigger('input');
                    });
                } else if (slide === 2) {
                    const searchInput = $('#guided_auth_search');
                    const suggestions = $('#guidedAuthSuggestions');

                    searchInput.on('input focus', function () {
                        const query = $(this).val().trim().toLowerCase();
                        suggestions.empty().addClass('d-none');

                        let matches = [];
                        $('#authorized_employee option').each(function () {
                            const id = $(this).val();
                            const name = $(this).text().trim();
                            if (id !== '0' && name.toLowerCase().indexOf(query) !== -1) {
                                matches.push({ id: id, name: name });
                            }
                        });

                        if (matches.length > 0) {
                            suggestions.removeClass('d-none');
                            matches.slice(0, 5).forEach(m => {
                                suggestions.append(`<div class="employee-suggestion-item" data-id="${m.id}">${esc(m.name)}</div>`);
                            });

                            suggestions.find('.employee-suggestion-item').on('click', function () {
                                const id = $(this).data('id');
                                const name = $(this).text();
                                searchInput.val(name);
                                suggestions.addClass('d-none').empty();

                                $('#authorized_employee').val(id).trigger('change');

                                setTimeout(function () {
                                    advanceGuidedSlide();
                                }, 220);
                            });
                        }
                    });

                    $(document).on('click.guidedAuthOutside', function (e) {
                        if (!$(e.target).closest('#guided_auth_search, #guidedAuthSuggestions').length) {
                            suggestions.addClass('d-none');
                            $(document).off('click.guidedAuthOutside');
                        }
                    });

                    searchInput.focus();
                } else if (slide === 3) {
                    const input = $('#guided_service_phone');
                    input.on('input change keyup', function () {
                        $('#service_phone').val($(this).val()).trigger('input');
                    });
                    input.on('keydown', function (e) {
                        if (e.key === 'Enter') { e.preventDefault(); advanceGuidedSlide(); }
                    });
                    input.focus();
                } else if (slide === 4) {
                    const input = $('#guided_service_person');
                    input.on('input change keyup', function () {
                        $('#service_person').val($(this).val()).trigger('input');
                    });
                    input.on('keydown', function (e) {
                        if (e.key === 'Enter') { e.preventDefault(); advanceGuidedSlide(); }
                    });
                    input.focus();
                } else if (slide === 5) {
                    $('#guidedOptYes').on('click', function () {
                        isOptionalExpanded = true;
                        currentGuidedSlide++;
                        renderGuidedSlide();
                        updateNextButtonText(2);
                    });
                    $('#guidedOptNo').on('click', function () {
                        const v2 = validateStep2();
                        if (v2) { currentStep = 3; showStep(currentStep); }
                    });
                } else if (slide === 6) {
                    const cards = $('#guidedWizardContainer .guided-option-card');
                    cards.on('click', function () {
                        const val = $(this).data('val');
                        cards.removeClass('active');
                        $(this).addClass('active');

                        $('#service_classification').val(val).trigger('change');

                        setTimeout(function () {
                            advanceGuidedSlide();
                        }, 220);
                    });
                } else if (slide === 7) {
                    const input = $('#guided_work_permit_no');
                    input.on('input change keyup', function () {
                        $('#work_permit_no').val($(this).val());
                    });
                    input.on('keydown', function (e) {
                        if (e.key === 'Enter') { e.preventDefault(); advanceGuidedSlide(); }
                    });
                    input.focus();
                } else if (slide === 8) {
                    const cards = $('#guidedSafetyYes, #guidedSafetyNo');
                    cards.on('click', function () {
                        cards.removeClass('active');
                        $(this).addClass('active');
                        const isYes = $(this).attr('id') === 'guidedSafetyYes';
                        $('#safety_briefing').prop('checked', isYes);
                        setTimeout(function () { advanceGuidedSlide(); }, 220);
                    });
                } else if (slide === 9) {
                    const dropdown = $('#guided_idcard_type_service');
                    const input = $('#guided_idcard_number_service');

                    dropdown.on('change', function () {
                        $('#idcard_type_service').val($(this).val()).trigger('change');
                    });
                    input.on('input change keyup', function () {
                        $('#idcard_number_service').val($(this).val());
                    });
                    input.on('keydown', function (e) {
                        if (e.key === 'Enter') { e.preventDefault(); advanceGuidedSlide(); }
                    });
                    input.focus();
                }
            } else if (type === 'employee') {
                const isTemp = $('#emp_temp_enable').is(':checked');
                if (slide === 1) {
                    $('#guidedEmpTypeStd').on('click', function () {
                        $('#emp_temp_enable').prop('checked', false).trigger('change');
                        setTimeout(function () { advanceGuidedSlide(); }, 220);
                    });
                    $('#guidedEmpTypeTemp').on('click', function () {
                        $('#emp_temp_enable').prop('checked', true).trigger('change');
                        setTimeout(function () { advanceGuidedSlide(); }, 220);
                    });
                } else {
                    if (!isTemp) {
                        if (slide === 2) {
                            const input = $('#guided_employee_code');
                            input.on('input change keyup', function () {
                                $('#employee_code').val($(this).val());
                            });
                            input.on('keydown blur', function (e) {
                                if (e.key === 'Enter' || e.type === 'blur') {
                                    if (e.type === 'keydown') e.preventDefault();
                                    const code = $(this).val().trim();
                                    if (code) {
                                        $('#employee_code').val(code).trigger('blur');
                                    }
                                    if (e.key === 'Enter') {
                                        setTimeout(function () { advanceGuidedSlide(); }, 500);
                                    }
                                }
                            });
                            input.focus();
                        }
                    } else {
                        const reasonVal = $('input[name="emp_temp_reason"]:checked').val() || 'forgot';
                        const isCross = (reasonVal === 'xbranch');

                        if (slide === 2) {
                            const cards = $('#guidedWizardContainer .guided-option-card');
                            cards.on('click', function () {
                                const val = $(this).data('val');
                                cards.removeClass('active');
                                $(this).addClass('active');

                                $(`input[name="emp_temp_reason"][value="${val}"]`).prop('checked', true).trigger('change');

                                setTimeout(function () {
                                    advanceGuidedSlide();
                                }, 220);
                            });
                        } else if (slide === 3) {
                            const input = $('#guided_emp_temp_phone');
                            input.on('input change keyup', function () {
                                $('#emp_temp_phone').val($(this).val()).trigger('input');
                            });
                            input.on('keydown', function (e) {
                                if (e.key === 'Enter') { e.preventDefault(); advanceGuidedSlide(); }
                            });
                            input.focus();
                        } else if (slide === 4) {
                            const input = $('#guided_emp_temp_name');
                            input.on('input change keyup', function () {
                                $('#emp_temp_name').val($(this).val()).trigger('input');
                            });
                            input.on('keydown', function (e) {
                                if (e.key === 'Enter') { e.preventDefault(); advanceGuidedSlide(); }
                            });
                            input.focus();
                        } else if (isCross && slide === 5) {
                            const searchInput = $('#guided_emp_temp_meet_search');
                            const suggestions = $('#guidedEmpTempMeetSuggestions');

                            searchInput.on('input focus', function () {
                                const query = $(this).val().trim().toLowerCase();
                                suggestions.empty().addClass('d-none');

                                let matches = [];
                                $('#emp_temp_whom_to_meet option').each(function () {
                                    const id = $(this).val();
                                    const name = $(this).text().trim();
                                    if (id !== '0' && name.toLowerCase().indexOf(query) !== -1) {
                                        matches.push({ id: id, name: name });
                                    }
                                });

                                if (matches.length > 0) {
                                    suggestions.removeClass('d-none');
                                    matches.slice(0, 5).forEach(m => {
                                        suggestions.append(`<div class="employee-suggestion-item" data-id="${m.id}">${esc(m.name)}</div>`);
                                    });

                                    suggestions.find('.employee-suggestion-item').on('click', function () {
                                        const id = $(this).data('id');
                                        const name = $(this).text();
                                        searchInput.val(name);
                                        suggestions.addClass('d-none').empty();

                                        $('#emp_temp_whom_to_meet').val(id).trigger('change');

                                        setTimeout(function () {
                                            advanceGuidedSlide();
                                        }, 220);
                                    });
                                }
                            });

                            $(document).on('click.guidedEmpTempMeetOutside', function (e) {
                                if (!$(e.target).closest('#guided_emp_temp_meet_search, #guidedEmpTempMeetSuggestions').length) {
                                    suggestions.addClass('d-none');
                                    $(document).off('click.guidedEmpTempMeetOutside');
                                }
                            });

                            searchInput.focus();
                        } else if ((isCross && slide === 6) || (!isCross && slide === 5)) {
                            const input = $('#guided_visiting_company_id_employee_temp');
                            input.on('input change keyup', function () {
                                $('#visiting_company_id_employee_temp').val($(this).val()).trigger('input');
                            });
                            input.on('keydown', function (e) {
                                if (e.key === 'Enter') { e.preventDefault(); advanceGuidedSlide(); }
                            });
                            input.focus();
                        }
                    }
                }
            } else if (type === 'doctor') {
                if (slide === 1) {
                    const input = $('#guided_doctor_phone');
                    input.on('input change keyup', function () {
                        $('#doctor_phone').val($(this).val()).trigger('input');
                    });
                    input.on('keydown', function (e) {
                        if (e.key === 'Enter') { e.preventDefault(); advanceGuidedSlide(); }
                    });
                    input.focus();
                } else if (slide === 2) {
                    const input = $('#guided_doctor_name');
                    input.on('input change keyup', function () {
                        $('#doctor_name').val($(this).val()).trigger('input');
                    });
                    input.on('keydown', function (e) {
                        if (e.key === 'Enter') { e.preventDefault(); advanceGuidedSlide(); }
                    });
                    input.focus();
                } else if (slide === 3) {
                    $('#guidedOptYes').on('click', function () {
                        isOptionalExpanded = true;
                        currentGuidedSlide++;
                        renderGuidedSlide();
                        updateNextButtonText(2);
                    });
                    $('#guidedOptNo').on('click', function () {
                        const v2 = validateStep2();
                        if (v2) { currentStep = 3; showStep(currentStep); }
                    });
                } else if (slide === 4) {
                    const input = $('#guided_doctor_specialization');
                    input.on('input change keyup', function () {
                        $('#doctor_specialization').val($(this).val());
                    });
                    input.on('keydown', function (e) {
                        if (e.key === 'Enter') { e.preventDefault(); advanceGuidedSlide(); }
                    });
                    input.focus();
                } else if (slide === 5) {
                    const cards = $('#guidedEmergencyYes, #guidedEmergencyNo');
                    cards.on('click', function () {
                        cards.removeClass('active');
                        $(this).addClass('active');
                        const isEmerg = $(this).data('val') === 'yes';
                        $('#is_emergency').prop('checked', isEmerg).trigger('change');
                        setTimeout(function () { advanceGuidedSlide(); }, 220);
                    });
                } else if (slide === 6) {
                    const dropdown = $('#guided_doctor_idcard_type');
                    const input = $('#guided_doctor_idcard_number');

                    dropdown.on('change', function () {
                        $('#doctor_idcard_type').val($(this).val()).trigger('change');
                    });
                    input.on('input change keyup', function () {
                        $('#doctor_idcard_number').val($(this).val());
                    });
                    input.on('keydown', function (e) {
                        if (e.key === 'Enter') { e.preventDefault(); advanceGuidedSlide(); }
                    });
                    input.focus();
                }
            } else if (type === 'vendor_supplier') {
                const refType = $('#vendor_supplier_document_type').val() || 'NOREF';
                const isNoref = (refType === 'NOREF');

                let activeSlide = slide;
                if (isNoref && slide >= 5) {
                    activeSlide = slide + 1;
                }

                if (activeSlide === 1) {
                    const cards = $('#guidedWizardContainer .guided-option-card');
                    cards.on('click', function () {
                        const val = $(this).data('val');
                        cards.removeClass('active');
                        $(this).addClass('active');

                        $('#vendor_supplier_department_id').val(val).trigger('change');

                        setTimeout(function () {
                            advanceGuidedSlide();
                        }, 220);
                    });
                } else if (activeSlide === 2) {
                    const searchInput = $('#guided_vendor_meet_search');
                    const suggestions = $('#guidedVendorMeetSuggestions');

                    searchInput.on('input focus', function () {
                        const query = $(this).val().trim().toLowerCase();
                        suggestions.empty().addClass('d-none');

                        let matches = [];
                        $('#whom_to_meet_vendor option').each(function () {
                            const id = $(this).val();
                            const name = $(this).text().trim();
                            if (id !== '0' && name.toLowerCase().indexOf(query) !== -1) {
                                matches.push({ id: id, name: name });
                            }
                        });

                        if (matches.length > 0) {
                            suggestions.removeClass('d-none');
                            matches.slice(0, 5).forEach(m => {
                                suggestions.append(`<div class="employee-suggestion-item" data-id="${m.id}">${esc(m.name)}</div>`);
                            });

                            suggestions.find('.employee-suggestion-item').on('click', function () {
                                const id = $(this).data('id');
                                const name = $(this).text();
                                searchInput.val(name);
                                suggestions.addClass('d-none').empty();

                                $('#whom_to_meet_vendor').val(id).trigger('change');

                                setTimeout(function () {
                                    advanceGuidedSlide();
                                }, 220);
                            });
                        }
                    });

                    $(document).on('click.guidedVendorMeetOutside', function (e) {
                        if (!$(e.target).closest('#guided_vendor_meet_search, #guidedVendorMeetSuggestions').length) {
                            suggestions.addClass('d-none');
                            $(document).off('click.guidedVendorMeetOutside');
                        }
                    });

                    searchInput.focus();
                } else if (activeSlide === 3) {
                    const cards = $('#guidedWizardContainer .guided-option-card');
                    cards.on('click', function () {
                        const val = $(this).data('val');
                        cards.removeClass('active');
                        $(this).addClass('active');

                        const oldVal = $('#vendor_supplier_entry_type').val();
                        if (oldVal !== val) {
                            $('#vendor_supplier_entry_type').val(val).trigger('change');
                        }

                        setTimeout(function () {
                            advanceGuidedSlide();
                        }, 220);
                    });
                } else if (activeSlide === 4) {
                    const cards = $('#guidedWizardContainer .guided-option-card');
                    cards.on('click', function () {
                        const val = $(this).data('val');
                        cards.removeClass('active');
                        $(this).addClass('active');

                        const oldVal = $('#vendor_supplier_document_type').val();
                        if (oldVal !== val) {
                            const $card = $(this);
                            $card.html('<div class="card-icon"><i class="fa fa-spinner fa-spin"></i></div><div class="card-label">Loading...</div>');
                            $('#vendor_supplier_document_type').val(val).trigger('change');

                            if (window.lastRefTxnPromise && typeof window.lastRefTxnPromise.always === 'function') {
                                window.lastRefTxnPromise.always(function () {
                                    setTimeout(function () {
                                        advanceGuidedSlide();
                                    }, 100);
                                });
                            } else if (window.lastRefTxnPromise && typeof window.lastRefTxnPromise.then === 'function') {
                                window.lastRefTxnPromise.then(function () {
                                    setTimeout(function () {
                                        advanceGuidedSlide();
                                    }, 100);
                                });
                            } else {
                                setTimeout(function () {
                                    advanceGuidedSlide();
                                }, 250);
                            }
                        } else {
                            setTimeout(function () {
                                advanceGuidedSlide();
                            }, 250);
                        }
                    });
                } else if (activeSlide === 5) {
                    const cards = $('#guidedWizardContainer .guided-option-card');
                    cards.on('click', function () {
                        const val = $(this).data('val');
                        cards.removeClass('active');
                        $(this).addClass('active');

                        const oldVal = $('#vendor_supplier_transaction_id').val();
                        if (oldVal !== val) {
                            $('#vendor_supplier_transaction_id').val(val).trigger('change');
                        }

                        setTimeout(function () {
                            advanceGuidedSlide();
                        }, 250);
                    });
                } else if (activeSlide === 6) {
                    if (isNoref) {
                        const dropdown = $('#guided_vendor_supplier_vendor_id');
                        dropdown.on('change', function () {
                            $('#vendor_supplier_vendor_id').val($(this).val()).trigger('change');
                        });
                        if (window.jQuery && jQuery.fn.select2) {
                            dropdown.select2({
                                dropdownParent: $('#guidedWizardContainer')
                            });
                            dropdown.on('select2:select', function (e) {
                                $('#vendor_supplier_vendor_id').val(e.params.data.id).trigger('change');
                            });
                        }
                    }
                    // Reactive sync for resolved vendor name inside Guided Mode slide
                    $('#vendor_supplier_vendor_id').off('change.guidedVendorSync').on('change.guidedVendorSync', function () {
                        const vendorText = $('#vendor_supplier_vendor_id option:selected').text().trim();
                        $('#guidedResolvedVendorName').text(vendorText || 'Resolved automatically');
                    });
                } else if (activeSlide === 7) {
                    const nameInput = $('#guided_vendor_supplier_driver_name');
                    const mobileInput = $('#guided_vendor_supplier_driver_mobile');

                    nameInput.on('input change keyup', function () {
                        $('#vendor_supplier_driver_name').val($(this).val());
                    });
                    mobileInput.on('input change keyup', function () {
                        $('#vendor_supplier_driver_mobile').val($(this).val()).trigger('input');
                    });
                    mobileInput.on('keydown', function (e) {
                        if (e.key === 'Enter') { e.preventDefault(); advanceGuidedSlide(); }
                    });
                    nameInput.focus();
                } else if (activeSlide === 8) {
                    const cards = $('#guidedWizardContainer .guided-option-card');
                    cards.on('click', function () {
                        const val = $(this).data('val');
                        cards.removeClass('active');
                        $(this).addClass('active');

                        $('#vendor_supplier_vehicle_type').val(val).trigger('change');
                    });

                    const numInput = $('#guided_vendor_supplier_vehicle_number');
                    numInput.on('input change keyup', function () {
                        $('#vendor_supplier_vehicle_number').val($(this).val()).trigger('input');
                    });
                    numInput.on('keydown', function (e) {
                        if (e.key === 'Enter') { e.preventDefault(); advanceGuidedSlide(); }
                    });
                    numInput.focus();
                }
            }
        }

        function validateGuidedSlide(slide) {
            const type = getType();
            if (type === 'visitor' || type === 'others') {
                if (slide === 1) {
                    const purpose = $('input[name="purpose"]:checked').val() || '';
                    if (!purpose) {
                        toast("Please select Purpose of Visit");
                        return false;
                    }
                    if (purpose === 'others' && !($('#purpose_other').val() || '').trim()) {
                        toast("Please specify Purpose of Visit");
                        $('#guided_purpose_other').focus();
                        return false;
                    }
                } else if (slide === 2) {
                    const meet = $('#whom_to_meet').val() || '0';
                    if (meet === '0') {
                        toast("Please select Whom to Meet");
                        $('#guided_meet_search').focus();
                        return false;
                    }
                } else if (slide === 3) {
                    const phone = ($('#phone').val() || '').trim();
                    if (!/^[0-9]{7,15}$/.test(phone)) {
                        toast("Please enter a valid Phone number (7–15 digits)");
                        $('#guided_phone').focus();
                        return false;
                    }
                } else if (slide === 4) {
                    const name = ($('#name').val() || '').trim();
                    if (!name) {
                        toast("Please enter your Name");
                        $('#guided_name').focus();
                        return false;
                    }
                }
            } else if (type === 'delivery') {
                if (slide === 1) {
                    const delType = $('#delivery_type').val() || '';
                    if (!delType) {
                        toast("Please select Delivery Type");
                        return false;
                    }
                } else if (slide === 2) {
                    const deliverTo = $('#deliver_to').val() || '0';
                    if (deliverTo === '0') {
                        toast("Please select Whom to Deliver");
                        $('#guided_deliver_search').focus();
                        return false;
                    }
                } else if (slide === 5) {
                    const company = $('#delivery_company_person').val() || '';
                    if (!company) {
                        toast("Please select Delivery Company");
                        return false;
                    }
                    if (company === '__new__' && !($('#new_delivery_company').val() || '').trim()) {
                        toast("Please enter Delivery Company name");
                        $('#guided_new_company').focus();
                        return false;
                    }
                }
            } else if (type === 'service') {
                if (slide === 1) {
                    const comp = $('#service_company').val();
                    if (!comp) {
                        toast("Please select Service Company");
                        return false;
                    }
                    if (comp === '__new__' && !($('#new_service_company').val() || '').trim()) {
                        toast("Please enter Service Company name");
                        $('#guided_new_service_company').focus();
                        return false;
                    }
                } else if (slide === 2) {
                    const auth = $('#authorized_employee').val() || '0';
                    if (auth === '0') {
                        toast("Please select Authorized Employee");
                        $('#guided_auth_search').focus();
                        return false;
                    }
                } else if (slide === 3) {
                    const phone = ($('#service_phone').val() || '').trim();
                    if (!/^[0-9]{7,15}$/.test(phone)) {
                        toast("Please enter a valid Phone number (7–15 digits)");
                        $('#guided_service_phone').focus();
                        return false;
                    }
                } else if (slide === 4) {
                    const name = ($('#service_person').val() || '').trim();
                    if (!name) {
                        toast("Please enter Service Person Name");
                        $('#guided_service_person').focus();
                        return false;
                    }
                }
            } else if (type === 'employee') {
                const isTemp = $('#emp_temp_enable').is(':checked');
                if (!isTemp) {
                    if (slide === 2) {
                        const code = ($('#employee_code').val() || '').trim();
                        if (!code) {
                            toast("Please enter Employee ID");
                            $('#guided_employee_code').focus();
                            return false;
                        }
                    }
                } else {
                    if (slide === 2) {
                        const reason = $('input[name="emp_temp_reason"]:checked').val();
                        if (!reason) {
                            toast("Please select Temporary Pass Reason");
                            return false;
                        }
                    } else if (slide === 3) {
                        const phone = ($('#emp_temp_phone').val() || '').trim();
                        if (!/^[0-9]{7,15}$/.test(phone)) {
                            toast("Please enter a valid Phone number (7–15 digits)");
                            $('#guided_emp_temp_phone').focus();
                            return false;
                        }
                    } else if (slide === 4) {
                        const name = ($('#emp_temp_name').val() || '').trim();
                        if (!name) {
                            toast("Please enter Employee Name");
                            $('#guided_emp_temp_name').focus();
                            return false;
                        }
                    } else if (slide === 5) {
                        const reason = $('input[name="emp_temp_reason"]:checked').val();
                        if (reason === 'xbranch') {
                            const meet = $('#emp_temp_whom_to_meet').val() || '0';
                            if (meet === '0') {
                                toast("Please select Whom to Meet");
                                $('#guided_emp_temp_meet_search').focus();
                                return false;
                            }
                        }
                    }
                }
            } else if (type === 'doctor') {
                if (slide === 1) {
                    const phone = ($('#doctor_phone').val() || '').trim();
                    if (!/^[0-9]{7,15}$/.test(phone)) {
                        toast("Please enter a valid Phone number (7–15 digits)");
                        $('#guided_doctor_phone').focus();
                        return false;
                    }
                } else if (slide === 2) {
                    const name = ($('#doctor_name').val() || '').trim();
                    if (!name) {
                        toast("Please enter Doctor Name");
                        $('#guided_doctor_name').focus();
                        return false;
                    }
                }
            } else if (type === 'vendor_supplier') {
                const refType = $('#vendor_supplier_document_type').val() || 'NOREF';
                const isNoref = (refType === 'NOREF');

                let activeSlide = slide;
                if (isNoref && slide >= 5) {
                    activeSlide = slide + 1;
                }

                if (activeSlide === 1) {
                    const dept = $('#vendor_supplier_department_id').val() || '0';
                    if (dept === '0' || !dept) {
                        toast("Please select Department");
                        return false;
                    }
                } else if (activeSlide === 2) {
                    const meet = $('#whom_to_meet_vendor').val() || '0';
                    if (meet === '0') {
                        toast("Please select Whom to Meet");
                        $('#guided_vendor_meet_search').focus();
                        return false;
                    }
                } else if (activeSlide === 3) {
                    const entry = $('#vendor_supplier_entry_type').val();
                    if (!entry) {
                        toast("Please select Entry Type");
                        return false;
                    }
                } else if (activeSlide === 4) {
                    const doc = $('#vendor_supplier_document_type').val();
                    if (!doc) {
                        toast("Please select Reference Document Type");
                        return false;
                    }
                } else if (activeSlide === 5) {
                    const txn = $('#vendor_supplier_transaction_id').val();
                    if (!txn) {
                        toast("Please select Document Number");
                        return false;
                    }
                } else if (activeSlide === 6) {
                    const vendor = $('#vendor_supplier_vendor_id').val();
                    if (!vendor || vendor === '0') {
                        toast("Please select/resolve Vendor/Supplier");
                        if (isNoref) $('#guided_vendor_supplier_vendor_id').focus();
                        return false;
                    }
                    if (isNoref) {
                        let hasItems = false;
                        $('#noref-items-body tr').each(function () {
                            const itemId = $(this).find('select.js-noref-item').val();
                            const qty = parseFloat($(this).find('input[name^="received_qty"]').val()) || 0;
                            if (itemId && qty > 0) {
                                hasItems = true;
                            }
                        });
                        if (!hasItems) {
                            toast("Please add at least one material item with quantity");
                            return false;
                        }
                    }
                }
            }
            return true;
        }

        function advanceGuidedSlide() {
            const type = getType();
            const totalSlides = getGuidedSlideCount(type);

            if (validateGuidedSlide(currentGuidedSlide)) {
                if (currentGuidedSlide < totalSlides) {
                    currentGuidedSlide++;
                    renderGuidedSlide();
                    updateNextButtonText(2);
                } else {
                    const v2 = validateStep2();
                    if (v2) {
                        currentStep = 3;
                        showStep(currentStep);
                    }
                }
            }
        }

        function prevGuidedSlide() {
            if (currentGuidedSlide > 1) {
                currentGuidedSlide--;
                renderGuidedSlide();
                updateNextButtonText(2);
            } else {
                currentStep = 1;
                showStep(currentStep);
            }
        }

        // ==========================================================================
        // STEP ACTIONS OVERRIDES
        // ==========================================================================
        $('#nextBtn').on('click', function (e) {
            console.log("nextBtn click handler triggered. currentStep:", currentStep);
            if (currentStep === 1) {
                const v1 = validateStep1();
                console.log("validateStep1 result:", v1);
                if (v1) {
                    currentStep = 2;
                    showStep(currentStep);
                }
            } else if (currentStep === 2) {
                const type = getType();
                const supportsGuided = true;
                if (supportsGuided && isGuidedMode) {
                    advanceGuidedSlide();
                } else {
                    const v2 = validateStep2();
                    console.log("validateStep2 result:", v2);
                    if (v2) {
                        currentStep = 3;
                        showStep(currentStep);
                    }
                }
            }
        });

        $('#prevBtn').on('click', function () {
            if (currentStep === 2) {
                const type = getType();
                const supportsGuided = true;
                if (supportsGuided && isGuidedMode) {
                    prevGuidedSlide();
                } else {
                    currentStep = 1;
                    showStep(currentStep);
                }
            } else if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
            }
        });

        // Reset slide count and re-evaluate mode on type change
        $('input[name="visit_type"]').on('change', function () {
            currentGuidedSlide = 1;
            isOptionalExpanded = false;
            updateNextButtonText(currentStep);
        });

        // Override showStep helper to check guided wizard
        const originalShowStep = showStep;
        showStep = function (step) {
            originalShowStep(step);
            if (step === 2) {
                initGuidedWizard();
            }
        };

        // Initialize state
        showStep(currentStep);
    });

    $(function () {
        const btn = document.getElementById('btnWorkPermit');
        if (btn) {
            btn.addEventListener('click', function () {
                const url = btn.dataset.url;
                if (url) window.open(url, '_blank', 'noopener,noreferrer');
            });
        }
    });

    $(function () {
        const btn = document.getElementById('btnMaterialGatePass');
        if (btn) {
            btn.addEventListener('click', function () {
                const url = btn.dataset.url;
                if (url) window.open(url, '_blank', 'noopener,noreferrer');
            });
        }
    });

    // Live Badge Preview Sync Logic
    $(function () {
        function currentType() {
            const sel = document.querySelector('input[name="visit_type"]:checked');
            return sel ? sel.value : 'visitor';
        }

        function updateLiveBadgePreview() {
            const type = currentType();

            // Update Badge Title text dynamically
            const $title = $('#badgePreviewTitle');
            let titleText = 'VISITOR PASS';
            if (type === 'delivery') titleText = 'DELIVERY PASS';
            else if (type === 'service') titleText = 'SERVICE PASS';
            else if (type === 'employee') titleText = 'EMPLOYEE PASS';
            else if (type === 'doctor') titleText = 'DOCTOR PASS';
            else if (type === 'vendor_supplier') titleText = 'VENDOR/SUPPLIER PASS';
            else if (type === 'others') {
                const specify = ($('#visit_type_other').val() || '').trim();
                titleText = specify ? specify.toUpperCase() + ' PASS' : 'VISITOR PASS';
            }
            $title.text(titleText);

            // 1. Update Badge Pill Text and Background
            const $pill = $('#badgePreviewPill');
            let typeText = type.toUpperCase();
            if (type === 'others') {
                const specify = ($('#visit_type_other').val() || '').trim();
                if (specify) typeText = specify.toUpperCase();
            }
            $pill.text(typeText);

            // Apply color based on visitor type
            let bg = '#3b82f6'; // visitor/others default blue
            if (type === 'delivery') bg = '#f59e0b'; // amber
            else if (type === 'service') bg = '#10b981'; // emerald
            else if (type === 'employee') bg = '#8b5cf6'; // purple
            else if (type === 'doctor') bg = '#ef4444'; // red
            $pill.css('background-color', bg);

            // 2. Update Visitor Name
            let name = 'Visitor Name';
            if (type === 'visitor' || type === 'others') {
                name = $('#name').val() || '';
            } else if (type === 'delivery') {
                name = $('#delivery_company_person').val() === '__new__' ?
                    ($('#new_delivery_company').val() || '') :
                    ($('#delivery_company_person option:selected').text() || '');
                if (name.trim() === 'Select Company') name = '';
            } else if (type === 'service') {
                name = $('#service_person').val() || '';
            } else if (type === 'employee') {
                name = $('#emp_temp_enable').is(':checked') ?
                    ($('#emp_temp_name').val() || '') :
                    ($('#employee_name').val() || '');
            } else if (type === 'doctor') {
                name = $('#doctor_name').val() || '';
            } else if (type === 'vendor_supplier') {
                name = $('#vendor_supplier_driver_name').val() || '';
            }
            name = name.trim();
            $('#badgePreviewName').text(name || 'Visitor Name');

            // 3. Update Badge ID
            let code = '';
            if (type === 'delivery') code = $('#visiting_company_id_delivery').val();
            else if (type === 'service') code = $('#visiting_company_id_service').val();
            else if (type === 'doctor') code = $('#visiting_company_id_doctor').val();
            else if (type === 'employee' && $('#emp_temp_enable').is(':checked')) code = $('#visiting_company_id_employee_temp').val();
            else if (type === 'employee') code = $('#employee_code').val();
            else if (type === 'vendor_supplier') code = $('#vendor_supplier_transaction_no').val();
            else code = $('#visiting_company_id_visitor').val();

            // Resolve values for the preview grid
            const gateVal = $('#in_gate_no').val();
            const gateText = (gateVal && gateVal !== '0') ? $('#in_gate_no option:selected').text().trim() : '—';

            let host = '—';
            if (type === 'visitor' || type === 'others') {
                const selectVal = $('#whom_to_meet').val();
                host = (selectVal && selectVal !== '0') ? $('#whom_to_meet option:selected').text().trim() : '—';
            } else if (type === 'delivery') {
                const selectVal = $('#deliver_to').val();
                host = (selectVal && selectVal !== '0') ? $('#deliver_to option:selected').text().trim() : '—';
            } else if (type === 'service') {
                const selectVal = $('#authorized_employee').val();
                host = (selectVal && selectVal !== '0') ? $('#authorized_employee option:selected').text().trim() : '—';
            } else if (type === 'employee' && $('#emp_temp_enable').is(':checked')) {
                const selectVal = $('#emp_temp_whom_to_meet').val();
                host = (selectVal && selectVal !== '0') ? $('#emp_temp_whom_to_meet option:selected').text().trim() : '—';
            } else if (type === 'employee') {
                host = 'Self Check-in';
            } else if (type === 'doctor') {
                host = $('#is_emergency').is(':checked') ? 'Emergency Duty' : 'On Call / Consultation';
            } else if (type === 'vendor_supplier') {
                const selectVal = $('#whom_to_meet_vendor').val();
                host = (selectVal && selectVal !== '0') ? $('#whom_to_meet_vendor option:selected').text().trim() : '—';
            }

            let phone = '—';
            if (type === 'visitor' || type === 'others') {
                phone = $('#phone').val() || '—';
            } else if (type === 'delivery') {
                phone = $('#delivery_phone').val() || '—';
            } else if (type === 'service') {
                phone = $('#service_phone').val() || '—';
            } else if (type === 'employee') {
                phone = $('#emp_temp_enable').is(':checked') ? ($('#emp_temp_phone').val() || '—') : '—';
            } else if (type === 'doctor') {
                phone = $('#doctor_phone').val() || '—';
            } else if (type === 'vendor_supplier') {
                phone = $('#vendor_supplier_driver_mobile').val() || '—';
            }

            let purpose = '—';
            if (type === 'visitor' || type === 'others') {
                let purpVal = $('input[name="purpose"]:checked').val() || '';
                if (purpVal === 'official') purpose = 'Official';
                else if (purpVal === 'personal') purpose = 'Personal';
                else if (purpVal === 'others') purpose = $('#purpose_other').val() || 'Others';
            } else if (type === 'delivery') {
                let delType = $('#delivery_type').val() || '';
                purpose = delType ? 'Delivery (' + delType + ')' : 'Delivery';
            } else if (type === 'service') {
                let svcClass = $('#service_classification').val() || '';
                purpose = svcClass ? 'Service (' + svcClass + ')' : 'Service';
            } else if (type === 'employee') {
                purpose = 'Employee Temp Pass';
            } else if (type === 'doctor') {
                let spec = $('#doctor_specialization').val() || '';
                purpose = spec ? 'Doctor (' + spec + ')' : 'Doctor';
            } else if (type === 'vendor_supplier') {
                let vsPurp = $('#vendor_supplier_purpose').val() || '';
                if (vsPurp) {
                    purpose = vsPurp;
                } else {
                    let entryType = $('#vendor_supplier_entry_type').val() || '';
                    if (entryType) {
                        const types = { IM: 'Inward Material', OM: 'Outward Material', RT: 'Returnable', NR: 'Non Returnable', EV: 'Empty Vehicle', MP: 'Material Pickup', MD: 'Material Delivery' };
                        purpose = types[entryType] || entryType;
                    }
                }
            }

            let vehicle = '—';
            if (type === 'visitor' || type === 'others') {
                vehicle = $('#vehicle_number').val() || '—';
            } else if (type === 'delivery') {
                vehicle = $('#vehicle_number_delivery').val() || '—';
            } else if (type === 'vendor_supplier') {
                vehicle = $('#vendor_supplier_vehicle_number').val() || '—';
                let vType = $('#vendor_supplier_vehicle_type').val();
                if (vehicle !== '—' && vehicle !== '' && vType) {
                    const types = { TRK: 'Truck', TMP: 'Tempo', CNT: 'Container', VAN: 'Van', CAR: 'Car', TRL: 'Trailer', PUV: 'Pickup Van', TNK: 'Tanker', DMP: 'Dumper', TWT: 'Tractor' };
                    vehicle += ' (' + (types[vType] || vType) + ')';
                }
            }

            // Helper to escape HTML in JS preview
            function esc(s) {
                return (s || '').toString()
                    .replace(/&/g, '&amp;').replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
            }

            function createGridItem(label, value) {
                return `<div class="badge-info-item">
                    <span>${esc(label)}</span>
                    <strong>${esc(value || '—')}</strong>
                </div>`;
            }

            // 3. Dynamic Grid Construction
            let gridHtml = '';
            if (type === 'visitor' || type === 'others') {
                gridHtml += createGridItem('Gate', gateText);
                gridHtml += createGridItem('Host / Meet', host);
                gridHtml += createGridItem('Contact Phone', phone);
                gridHtml += createGridItem('Purpose', purpose);
            } else if (type === 'delivery') {
                gridHtml += createGridItem('Gate', gateText);
                gridHtml += createGridItem('Contact Phone', phone);
                gridHtml += createGridItem('Whom To Deliver', host);
                gridHtml += createGridItem('Vehicle No', vehicle);
                gridHtml += createGridItem('Delivery Type', $('#delivery_type').val() || '—');
            } else if (type === 'service') {
                let serviceCompany = $('#service_company').val() === '__new__' ? ($('#new_service_company').val() || '') : ($('#service_company option:selected').text() || '');
                if (serviceCompany.trim() === 'Select Service Company') serviceCompany = '—';

                gridHtml += createGridItem('Gate', gateText);
                gridHtml += createGridItem('Service Company', serviceCompany);
                gridHtml += createGridItem('Contact Phone', phone);
                gridHtml += createGridItem('Authorized By', host);
            } else if (type === 'employee') {
                if ($('#emp_temp_enable').is(':checked')) {
                    gridHtml += createGridItem('Gate', gateText);
                    gridHtml += createGridItem('Contact Phone', phone);
                    gridHtml += createGridItem('Host / Meet', host);
                } else {
                    let empCode = $('#employee_code').val() || '—';
                    let empDept = $('#employee_department').val() || '—';
                    gridHtml += createGridItem('Gate', gateText);
                    gridHtml += createGridItem('Employee ID', empCode);
                    gridHtml += createGridItem('Department', empDept);
                }
            } else if (type === 'doctor') {
                gridHtml += createGridItem('Gate', gateText);
                gridHtml += createGridItem('Contact Phone', phone);
            } else if (type === 'vendor_supplier') {
                let vendorComp = $('#vendor_supplier_vendor_id option:selected').text().trim();
                if (vendorComp.toLowerCase().indexOf('select') !== -1) vendorComp = '—';
                let deptName = $('#vendor_supplier_department_id option:selected').text().trim();
                if (deptName.toLowerCase().indexOf('select') !== -1) deptName = '';

                gridHtml += createGridItem('Gate', gateText);
                gridHtml += createGridItem('Vendor Company', vendorComp);
                gridHtml += createGridItem('Department', deptName || '—');
                gridHtml += createGridItem('Host / Meet', host);
            }

            $('#badgePreviewGrid').html(gridHtml);

            // Legacy card text update
            $('#visit_card_code').text(code || '—');
        }

        function generateBarcodeSVG(text) {
            text = (text || '').trim().toUpperCase();
            text = text.replace(/[^0-9A-Z\-\.\ \$\/\+\%]/g, '');
            if (!text) return '';

            const fullText = '*' + text + '*';

            const CODE39 = {
                '0': '000110100', '1': '100100001', '2': '001100001', '3': '101100000',
                '4': '000110001', '5': '100110000', '6': '001110000', '7': '000100101',
                '8': '100100100', '9': '001100100', 'A': '100001001', 'B': '001001001',
                'C': '101001000', 'D': '000011001', 'E': '100011000', 'F': '001011000',
                'G': '000001101', 'H': '100001100', 'I': '001001100', 'J': '000011100',
                'K': '100000011', 'L': '001000011', 'M': '101000010', 'N': '000010011',
                'O': '100010010', 'P': '001010010', 'Q': '000000111', 'R': '100000110',
                'S': '001000110', 'T': '000010110', 'U': '110000001', 'V': '011000001',
                'W': '111000000', 'X': '010010001', 'Y': '110010000', 'Z': '011010000',
                '-': '010000101', '.': '110000100', ' ': '011000100', '*': '010010100',
                '$': '010101000', '/': '010100010', '+': '010001010', '%': '000101010'
            };

            const narrowWidth = 2;
            const wideWidth = 5;
            const gapWidth = 2;

            let x = 0;
            let svgPath = '';

            for (let i = 0; i < fullText.length; i++) {
                const char = fullText[i];
                const pattern = CODE39[char];
                if (!pattern) continue;

                for (let j = 0; j < 9; j++) {
                    const isBar = (j % 2 === 0);
                    const isWide = (pattern[j] === '1');
                    const w = isWide ? wideWidth : narrowWidth;

                    if (isBar) {
                        svgPath += `M ${x} 0 L ${x} 50 L ${x + w} 50 L ${x + w} 0 Z `;
                    }
                    x += w;
                }
                x += gapWidth;
            }

            const viewWidth = x - gapWidth;

            return `<svg width="100%" height="100%" viewBox="0 0 ${viewWidth} 50" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <path d="${svgPath.trim()}" fill="#000000" />
        </svg>`;
        }

        function updateBadgeBarcode(code) {
            const container = document.getElementById('badgePreviewBarcodeContainer');
            if (container) {
                container.innerHTML = generateBarcodeSVG(code);
            }
        }

        // List of inputs to sync on keyup/change
        const previewSelectors = [
            'input[name="visit_type"]',
            '#in_gate_no',
            '#phone', '#name', '#whom_to_meet', '#visiting_company_id_visitor', '#company_name', '#visit_type_other',
            '#delivery_company_person', '#new_delivery_company', '#deliver_to', '#visiting_company_id_delivery',
            '#service_company', '#new_service_company', '#service_person', '#authorized_employee', '#visiting_company_id_service',
            '#employee_code', '#employee_name', '#employee_department', '#emp_temp_enable', '#emp_temp_name', '#emp_temp_whom_to_meet', '#visiting_company_id_employee_temp',
            '#doctor_name', '#doctor_phone', '#visiting_company_id_doctor', '#doctor_specialization', '#is_emergency',
            '#vendor_supplier_transaction_no', '#vendor_supplier_driver_name', '#vendor_supplier_driver_mobile', '#vendor_supplier_vendor_id', '#vendor_supplier_department_id', '#whom_to_meet_vendor',
            'input[name="purpose"]', '#purpose_other', '#delivery_type', '#service_classification', '#vendor_supplier_purpose', '#vendor_supplier_entry_type', '#expected_duration',
            '#vehicle_number', '#vehicle_number_delivery', '#vendor_supplier_vehicle_number', '#vendor_supplier_vehicle_type',
            '#delivery_phone', '#service_phone', '#emp_temp_phone'
        ];

        $(document).on('keyup change blur input', previewSelectors.join(', '), function () {
            updateLiveBadgePreview();
        });

        // Handle select2 change events properly
        $(document).on('change', '.js-example-basic-single', function () {
            updateLiveBadgePreview();
        });

        // Run initial update on load
        setTimeout(updateLiveBadgePreview, 100);

        // Expose to window for manual syncing
        window.updateLiveBadgePreview = updateLiveBadgePreview;
        window.generateBarcodeSVG = generateBarcodeSVG;
        window.updateBadgeBarcode = updateBadgeBarcode;
    });

    // Check-out (OUT) management integration
    (function () {
        const cfgEl = document.getElementById('visitor-out-config');
        const CFG = cfgEl ? JSON.parse(cfgEl.textContent) : {};

        const serviceCompanyRes = CFG.serviceCompanyRes || {};
        const deviveryCompanyRes = CFG.deliveryCompanyRes || {};

        function toastErr(msg) {
            if (typeof tostarnotify === 'function') {
                tostarnotify('top', 'right', 'fa fa-thumbs-up', 'form-label label label-danger',
                    'animated flipInX', 'animated flipOutX', msg, '');
            } else {
                alert(msg);
            }
        }

        function escapeHtml(s) {
            return (s || '').toString()
                .replace(/&/g, '&amp;').replace(/</g, '&lt;')
                .replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
        }

        function getInitials(fullName) {
            const n = (fullName || '').trim().split(/\s+/);
            if (!n.length) return 'NA';
            const first = n[0]?.[0] || '';
            const last = (n.length > 1 ? n[n.length - 1][0] : (n[0][1] || '')) || '';
            return (first + last).toUpperCase();
        }

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

        function mapDisplayFields(r) {
            const vt = (r.visit_type || 'visitor').toLowerCase();
            switch (vt) {
                case 'delivery':
                    return {
                        visitType: 'Delivery',
                        displayName: deviveryCompanyRes[r.delivery_company_person] || '',
                        displayPhone: r.delivery_phone || r.phone || ''
                    };
                case 'vendor_supplier':
                    return {
                        visitType: 'Vendor/Supplier',
                        displayName: r.vendor_supplier_driver_name || r.name || '',
                        displayPhone: r.vendor_supplier_driver_mobile || r.phone || ''
                    };
                case 'service':
                    return {
                        visitType: 'Service',
                        displayName: serviceCompanyRes[r.service_company] || '',
                        displayPhone: r.service_phone || ''
                    };
                case 'employee':
                    return {
                        visitType: 'Employee',
                        displayName: r.employee_name || '',
                        displayPhone: r.phone || ''
                    };
                case 'doctor':
                    return {
                        visitType: 'Doctor',
                        displayName: r.doctor_name || '',
                        displayPhone: r.doctor_phone || ''
                    };
                case 'visitor':
                case 'others':
                default:
                    return {
                        visitType: titleCase(vt || 'Visitor'),
                        displayName: r.name || '',
                        displayPhone: r.phone || ''
                    };
            }
        }

        function titleCase(s) {
            return (s || '').toString().replace(/\b[a-z]/g, (m) => m.toUpperCase());
        }

        function renderVisitorRows(records) {
            const $tbody = $('#checkoutHistoryTable tbody');
            const $wrap = $('#checkoutHistoryWrap');
            if (!records || !records.length) {
                $wrap.removeClass('d-none');
                $tbody.html(`
                <tr class="no-records-row">
                <td colspan="7">
                    <div class="text-center">
                    <p class="text-muted f-16 fw-bold m-b-30">
                        <i class="fa fa-warning"></i> No Records
                    </p>
                    <div class="text-center px-4">
                        <img class="visitor-empty-img" alt="" src="assets/images/work.png" style="max-height: 200px; margin: 20px auto;">
                    </div>
                    </div>
                </td>
                </tr>
            `);
                return;
            }

            let html = '';
            for (let i = 0; i < records.length; i++) {
                const r = records[i] || {};
                const {
                    visitType,
                    displayName,
                    displayPhone
                } = mapDisplayFields(r);
                const id = r.visitor_idcard_no || r.employee_code || '';
                const ci = r.in_datetime || '';
                const co = r.out_datetime || '';
                const checkedOut = !!(co && co !== '0000-00-00 00:00:00');
                const avatar = buildAvatar(displayName, r.image_path || '', checkedOut, r.employee_code || r.visitor_idcard_no || '');

                const nameCls = 'visitor-name ' + (checkedOut ? 'out' : 'in');
                const idSafe = parseInt(r.id, 10) || 0;
                const vtSafe = (r.visit_type || r.visitor_type || 'visitor').toLowerCase();

                const actionBtn = checkedOut ? `
                <span class="badge bg-success py-2 px-3">Checked Out</span>
                ` : `
                <button type="button"
                        class="btn btn-sm btn-primary js-checkout-view-visitor"
                        data-id="${idSafe}"
                        data-visit-type="${escapeHtml(vtSafe)}">
                    <i class="fa fa-sign-out-alt"></i> Check Out
                </button>
                `;

                html += `
                <tr>
                <td>
                    <div class="visitor-cell">
                    ${avatar}
                    <div>
                        <div class="${nameCls}">${escapeHtml(displayName || '-')}</div>                        
                    </div>
                    </div>
                </td>
                <td>${escapeHtml(id)}</td>
                <td>${escapeHtml(displayPhone || '')}</td>
                <td>${escapeHtml(visitType)}</td>
                <td>${escapeHtml(ci)}</td>
                <td>${escapeHtml(co)}</td>
                <td style="text-align:center;">${actionBtn}</td>
                </tr>
            `;
            }
            $wrap.removeClass('d-none');
            $tbody.html(html);
        }
        window.renderVisitorRows = renderVisitorRows;

        $(document).on('click', '.js-checkout-view-visitor', function () {
            const id = parseInt($(this).data('id'), 10) || 0;
            const visitType = ($(this).data('visit-type') || '').toLowerCase();
            viewVisitor(id, visitType);
        });

        function viewVisitor(id, visitType) {
            // Adjust layout for split-screen
            $('#checkout-form-col').removeClass('d-none');
            $('#checkout-list-col').removeClass('col-lg-12').addClass('col-lg-5 col-md-5');

            data = {
                type: visitType,
                id: id,
                key: 'AJAX_FETCH_VISITOR_OUT'
            };

            const loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 150px; margin: 30px auto;"></div>';
            $('#checkoutResult').html(loader);

            $.ajax({
                url: 'views/visitor_out_view.php',
                type: 'GET',
                data: data,
                success: function (response) {
                    $('#checkoutResult').html(response)
                        .find('.js-example-basic-single')
                        .each(function () {
                            $(this).select2();
                        });
                    $('html, body').animate({
                        scrollTop: $("#checkoutResult").offset().top - 100
                    }, 500);
                },
                error: function (error) {
                    console.error('Failed to load view:', error);
                    toastErr('Failed to load the page. Please try again.');
                }
            });
        }

        $(function () {
            function triggerCheckoutSearch() {
                // Reset layout to full-width list
                $('#checkout-form-col').addClass('d-none');
                $('#checkout-list-col').removeClass('col-lg-3 col-md-4').addClass('col-lg-12');
                $('#checkoutResult').empty();

                const text = ($('#checkoutSearchText').val() || '').trim();
                const date = ($('#checkoutSearchDate').val() || '').trim();
                const location_id = ($('#location_id_checkout').val() || '').trim();
                const visit_type = ($('#checkoutSearchType').val() || '').trim();

                if (!text && !date) {
                    toastErr('Enter search text or a date');
                    return;
                }

                $.ajax({
                    url: 'ajax.php',
                    type: 'POST',
                    data: {
                        page: 'getVisitorsSearch',
                        text: text,
                        date: date,
                        location_id: location_id,
                        visit_type: visit_type
                    },
                    success: function (resp) {
                        let json = null;
                        try {
                            json = JSON.parse(resp);
                        } catch (e) {
                            /* ignore */
                        }
                        const rows = (json && json.visitor_logs_res) ? json.visitor_logs_res : [];
                        renderVisitorRows(rows);
                    },
                    error: function () {
                        toastErr('Failed to fetch history. Try again.');
                    }
                });
            }

            $('#checkoutSearchBtn').on('click', triggerCheckoutSearch);

            $('#checkoutSearchText').on('keydown', function (e) {
                if (e.which === 13 || e.keyCode === 13) {
                    e.preventDefault();
                    triggerCheckoutSearch();
                }
            });

            // Live filter on type or date changes
            $('#checkoutSearchType, #checkoutSearchDate').on('change', triggerCheckoutSearch);

            // Live search on text typing (debounce or length-based)
            $('#checkoutSearchText').on('input', function () {
                const val = $(this).val().trim();
                if (val.length === 0 || val.length >= 3) {
                    triggerCheckoutSearch();
                }
            });

            // Auto trigger search when tab is clicked
            $(document).on('shown.bs.tab', 'button[data-bs-target="#checkout-pane"]', function () {
                triggerCheckoutSearch();
            });

            // Run search on page load
            triggerCheckoutSearch();
        });
    })();

    $(function () {
        // Helper to replace broken search result images with initials placeholder
        window.handleSearchThumbError = function (img) {
            if (img && img.parentNode) {
                var name = $(img).closest('.search-result-item').find('strong').text() || 'U';
                var initials = getInitials(name);
                var placeholder = document.createElement('div');
                placeholder.className = 'item-thumb-placeholder';
                // Choose a nice gradient based on the name hash
                let hash = 0;
                for (let i = 0; i < name.length; i++) {
                    hash = name.charCodeAt(i) + ((hash << 5) - hash);
                }
                const index = Math.abs(hash) % 4;
                const gradients = [
                    'linear-gradient(135deg, #3b82f6, #06b6d4)', // Blue-Cyan
                    'linear-gradient(135deg, #6366f1, #a855f7)', // Indigo-Purple
                    'linear-gradient(135deg, #10b981, #3b82f6)', // Emerald-Blue
                    'linear-gradient(135deg, #f59e0b, #ec4899)'  // Amber-Pink
                ];
                placeholder.style.background = gradients[index];
                placeholder.style.color = '#ffffff';
                placeholder.style.fontWeight = '700';
                placeholder.style.fontSize = '14px';
                placeholder.style.letterSpacing = '0.5px';
                placeholder.style.textShadow = '0 1px 2px rgba(0,0,0,0.1)';
                placeholder.innerHTML = initials;
                img.parentNode.replaceChild(placeholder, img);
            }
        };

        function getModernAvatarPlaceholder(name) {
            const initials = getInitials(name);
            let hash = 0;
            for (let i = 0; i < name.length; i++) {
                hash = name.charCodeAt(i) + ((hash << 5) - hash);
            }
            const index = Math.abs(hash) % 4;
            const gradients = [
                'linear-gradient(135deg, #3b82f6, #06b6d4)', // Blue-Cyan
                'linear-gradient(135deg, #6366f1, #a855f7)', // Indigo-Purple
                'linear-gradient(135deg, #10b981, #3b82f6)', // Emerald-Blue
                'linear-gradient(135deg, #f59e0b, #ec4899)'  // Amber-Pink
            ];
            return '<div class="item-thumb-placeholder" style="background: ' + gradients[index] + ' !important; color: #ffffff !important; font-weight: 700 !important; font-size: 14px !important; letter-spacing: 0.5px !important; text-shadow: 0 1px 2px rgba(0,0,0,0.1) !important;">' + initials + '</div>';
        }

        // 1) autocomplete search for historical visitor profile
        var searchTimer = null;
        $('#visitor_profile_search').on('keyup', function () {
            var query = $(this).val().trim();
            var visitType = $('input[name="visit_type"]:checked').val() || 'visitor';
            var container = $('#search_results_container');

            clearTimeout(searchTimer);
            if (query.length < 3) {
                container.addClass('d-none').html('');
                return;
            }

            searchTimer = setTimeout(function () {
                $.ajax({
                    url: 'ajax.php',
                    type: 'POST',
                    data: {
                        page: 'getHistoricalVisitorProfileSearch',
                        query: query,
                        visit_type: visitType,
                        location_id: $('#location_id').val()
                    },
                    dataType: 'json',
                    success: function (records) {
                        if (!records || records.length === 0) {
                            container.html('<div class="search-result-item text-muted">No historical records found</div>').removeClass('d-none');
                            return;
                        }

                        // Save records globally for safe index-based lookup to avoid quote escaping issues
                        window._lastSearchRecords = records;

                        var html = '';
                        records.forEach(function (r, idx) {
                            var title = '';
                            var metaHtml = '';
                            var imgSrc = r.image_path ? 'uploads/visitors/' + r.image_path : '';

                            if (visitType === 'visitor' || visitType === 'others') {
                                title = r.name || 'Unnamed';
                                if (r.phone) metaHtml += '<span class="meta-badge"><i class="fa fa-phone me-1"></i>' + escapeHtml(r.phone) + '</span>';
                                if (r.company_name) metaHtml += '<span class="meta-badge"><i class="fa fa-building me-1"></i>' + escapeHtml(r.company_name) + '</span>';
                            } else if (visitType === 'delivery') {
                                title = r.delivery_company_person || 'Unnamed Delivery';
                                if (r.delivery_phone) metaHtml += '<span class="meta-badge"><i class="fa fa-phone me-1"></i>' + escapeHtml(r.delivery_phone) + '</span>';
                            } else if (visitType === 'service') {
                                title = r.service_person || 'Unnamed Service';
                                if (r.service_phone) metaHtml += '<span class="meta-badge"><i class="fa fa-phone me-1"></i>' + escapeHtml(r.service_phone) + '</span>';
                                if (r.service_company) metaHtml += '<span class="meta-badge"><i class="fa fa-building me-1"></i>' + escapeHtml(r.service_company) + '</span>';
                            } else if (visitType === 'employee') {
                                title = (r.emp_temp_enable == 1) ? r.emp_temp_name : r.employee_name;
                                if (r.employee_code) metaHtml += '<span class="meta-badge"><i class="fa fa-id-badge me-1"></i>Emp ID: ' + escapeHtml(r.employee_code) + '</span>';
                            } else if (visitType === 'doctor') {
                                title = r.doctor_name || 'Unnamed Doctor';
                                if (r.doctor_phone) metaHtml += '<span class="meta-badge"><i class="fa fa-phone me-1"></i>' + escapeHtml(r.doctor_phone) + '</span>';
                            } else if (visitType === 'vendor_supplier') {
                                title = r.vendor_supplier_driver_name || 'Driver: Unnamed';
                                if (r.vendor_supplier_driver_mobile) metaHtml += '<span class="meta-badge"><i class="fa fa-phone me-1"></i>' + escapeHtml(r.vendor_supplier_driver_mobile) + '</span>';
                                if (r.vendor_supplier_vehicle_number) metaHtml += '<span class="meta-badge"><i class="fa fa-truck me-1"></i>' + escapeHtml(r.vendor_supplier_vehicle_number) + '</span>';
                            }

                            // Build thumbnail
                            var thumbHtml = imgSrc
                                ? '<img class="item-thumb" src="' + imgSrc + '" onerror="handleSearchThumbError(this)" alt="">'
                                : getModernAvatarPlaceholder(title);

                            html += '<div class="search-result-item" data-record-index="' + idx + '">'
                                + thumbHtml
                                + '<div class="item-info">'
                                + '<strong>' + escapeHtml(title) + '</strong>'
                                + '<div class="item-meta">' + metaHtml + '</div>'
                                + '</div>'
                                + '</div>';
                        });
                        container.html(html).removeClass('d-none');
                    },
                    error: function () {
                        container.addClass('d-none').html('');
                    }
                });
            }, 300);
        });

        // Close search dropdown on click outside
        $(document).on('click', function (e) {
            if (!$(e.target).closest('#visitor_profile_search, #search_results_container').length) {
                $('#search_results_container').addClass('d-none');
            }
        });

        // Click on search result to autofill fields
        $(document).on('click', '#search_results_container .search-result-item', function () {
            var idx = $(this).data('record-index');
            var r = window._lastSearchRecords ? window._lastSearchRecords[idx] : null;
            if (!r) return;

            var visitType = $('input[name="visit_type"]:checked').val() || 'visitor';

            if (visitType === 'visitor' || visitType === 'others') {
                $('#phone').val(r.phone || '');
                $('#name').val(r.name || '');
                if (r.idcard_type) $('#idcard_type').val(r.idcard_type).trigger('change');
                $('#idcard_number').val(r.idcard_number || '');
                if (r.whom_to_meet) $('#whom_to_meet').val(r.whom_to_meet).trigger('change');
                if (r.purpose) {
                    $('input[name="purpose"][value="' + r.purpose + '"]').prop('checked', true).trigger('change');
                }
                $('#company_name').val(r.company_name || '');
                $('#address').val(r.address || '');
                $('#purpose_other').val(r.purpose_other || '');
                if (r.gender) {
                    $('input[name="gender"][value="' + r.gender + '"]').prop('checked', true);
                }
                if (r.expected_duration) $('#expected_duration').val(r.expected_duration).trigger('change');
                if (r.vehicle_type) $('#vehicle_type').val(r.vehicle_type).trigger('change');
                $('#vehicle_number').val(r.vehicle_number || '');
            }
            else if (visitType === 'delivery') {
                if (r.delivery_company_person) $('#delivery_company_person').val(r.delivery_company_person).trigger('change');
                $('#delivery_phone').val(r.delivery_phone || '');
                if (r.deliver_to) $('#deliver_to').val(r.deliver_to).trigger('change');
                $('#delivery_awb_no').val(r.delivery_awb_no || '');
                if (r.delivery_type) $('#delivery_type').val(r.delivery_type).trigger('change');
                $('#vehicle_number_delivery').val(r.vehicle_number || '');
                $('#package_count').val(r.package_count || 1);
                $('#security_cleared').prop('checked', r.security_cleared == 1);
            }
            else if (visitType === 'service') {
                if (r.service_company) $('#service_company').val(r.service_company).trigger('change');
                $('#service_phone').val(r.service_phone || '');
                $('#service_person').val(r.service_person || '');
                if (r.idcard_type_service) $('#idcard_type_service').val(r.idcard_type_service).trigger('change');
                $('#idcard_number_service').val(r.idcard_number_service || '');
                if (r.authorized_employee) $('#authorized_employee').val(r.authorized_employee).trigger('change');
                if (r.service_classification) $('#service_classification').val(r.service_classification).trigger('change');
                $('#work_permit_no').val(r.work_permit_no || '');
                $('#safety_briefing').prop('checked', r.safety_briefing == 1);
            }
            else if (visitType === 'employee') {
                $('#emp_temp_enable').prop('checked', r.emp_temp_enable == 1).trigger('change');
                if (r.emp_temp_enable == 1) {
                    $('#emp_temp_phone').val(r.emp_temp_phone || '');
                    $('#emp_temp_name').val(r.emp_temp_name || '');
                    if (r.emp_temp_reason) {
                        $('input[name="emp_temp_reason"][value="' + r.emp_temp_reason + '"]').prop('checked', true).trigger('change');
                    }
                    if (r.emp_temp_whom_to_meet) $('#emp_temp_whom_to_meet').val(r.emp_temp_whom_to_meet).trigger('change');
                } else {
                    $('#employee_code').val(r.employee_code || '').trigger('blur');
                }
            }
            else if (visitType === 'doctor') {
                $('#doctor_phone').val(r.doctor_phone || '');
                $('#doctor_name').val(r.doctor_name || '');
                if (r.doctor_idcard_type) $('#doctor_idcard_type').val(r.doctor_idcard_type).trigger('change');
                $('#doctor_idcard_number').val(r.doctor_idcard_number || '');
                $('#doctor_specialization').val(r.doctor_specialization || '');
                $('#is_emergency').prop('checked', r.is_emergency == 1);
            }
            else if (visitType === 'vendor_supplier') {
                if (r.vendor_supplier_entry_type) $('#vendor_supplier_entry_type').val(r.vendor_supplier_entry_type).trigger('change');
                if (r.vendor_supplier_vehicle_type) $('#vendor_supplier_vehicle_type').val(r.vendor_supplier_vehicle_type).trigger('change');
                $('#vendor_supplier_vehicle_number').val(r.vendor_supplier_vehicle_number || '');
                $('#vendor_supplier_driver_name').val(r.vendor_supplier_driver_name || '');
                $('#vendor_supplier_driver_mobile').val(r.vendor_supplier_driver_mobile || '');
                $('#vendor_supplier_purpose').val(r.vendor_supplier_purpose || '');
                if (r.vendor_supplier_proof_type) {
                    var ptypes = r.vendor_supplier_proof_type.split(',');
                    var pnos = (r.vendor_supplier_proof_document_no || '').split(',');
                    $('select[name="vendor_supplier_proof_type[]"]').val(ptypes[0] || '').trigger('change');
                    $('input[name="vendor_supplier_proof_document_no[]"]').val(pnos[0] || '');
                }
                if (r.vendor_supplier_document_type) {
                    $('#vendor_supplier_document_type').val(r.vendor_supplier_document_type);
                    window.getReferenceTransactions(r.vendor_supplier_transaction_id);
                }
                if (r.vendor_supplier_vendor_id) $('#vendor_supplier_vendor_id').val(r.vendor_supplier_vendor_id).trigger('change');
                if (r.vendor_supplier_department_id) $('#vendor_supplier_department_id').val(r.vendor_supplier_department_id).trigger('change');
                if (r.whom_to_meet) $('#whom_to_meet_vendor').val(r.whom_to_meet).trigger('change');
                if (r.vendor_supplier_vehicle_status) $('#vendor_supplier_vehicle_status').val(r.vendor_supplier_vehicle_status).trigger('change');
                if (r.vendor_supplier_loading_start) {
                    $('#vendor_supplier_loading_start').val(r.vendor_supplier_loading_start.replace(' ', 'T'));
                }
                if (r.vendor_supplier_loading_end) {
                    $('#vendor_supplier_loading_end').val(r.vendor_supplier_loading_end.replace(' ', 'T'));
                }
            }

            // If the selected historical record has an image, autofill photo preview
            if (r.image_path) {
                var imgUrl = 'uploads/visitors/' + r.image_path;
                // Set existing_image hidden field so it's submitted if user doesn't change photo
                $('input[name="existing_image"]').val(r.image_path);
                // Show photo preview
                var personImg = document.getElementById('personPreviewInline');
                if (personImg) {
                    personImg.src = imgUrl;
                    personImg.classList.remove('d-none');
                }
                $('.photo-actions-container').removeClass('d-flex').addClass('d-none');
                $('.photo-preview-container').removeClass('d-none').addClass('d-flex');
                // Update badge preview photo
                var badgePhoto = document.getElementById('badgePreviewPhoto');
                if (badgePhoto) {
                    badgePhoto.src = imgUrl;
                    badgePhoto.classList.remove('d-none');
                    badgePhoto.style.display = 'block';
                }
                var badgePlaceholder = document.querySelector('.badge-photo-placeholder');
                if (badgePlaceholder) {
                    badgePlaceholder.classList.add('d-none');
                    badgePlaceholder.style.display = 'none';
                }
            }

            $('#search_results_container').addClass('d-none');
            if (typeof window.updateLiveBadgePreview === 'function') {
                window.updateLiveBadgePreview();
            }
        });
    });

    // ──────────────────────────────────────────────────────────────────────
    // No-Reference item data (populated from PHP JSON output)
    // ──────────────────────────────────────────────────────────────────────
    var _norefConfig = (function () {
        try {
            var el = document.getElementById('noref-items-config');
            return el ? JSON.parse(el.textContent || el.innerHTML) : { items: [], uoms: [] };
        } catch (e) { return { items: [], uoms: [] }; }
    })();

    function buildItemSelectOptions(selectedId) {
        var opts = '<option value="">-- Select Item --</option>';
        (_norefConfig.items || []).forEach(function (it) {
            var label = (it.sku ? it.sku + ' - ' : '') + it.item_name;
            var sel = (selectedId && selectedId == it.id) ? ' selected' : '';
            opts += '<option value="' + it.id + '" data-name="' + it.item_name.replace(/"/g, '&quot;') + '" data-uom="' + (it.purchase_uom || '') + '"' + sel + '>' + label + '</option>';
        });
        return opts;
    }

    function buildUomSelectOptions(selectedId) {
        var opts = '<option value="">-- UOM --</option>';
        (_norefConfig.uoms || []).forEach(function (u) {
            var label = u.uom_name + (u.uom_short_code ? ' (' + u.uom_short_code + ')' : '');
            var sel = (selectedId && selectedId == u.id) ? ' selected' : '';
            opts += '<option value="' + u.id + '"' + sel + '>' + label + '</option>';
        });
        return opts;
    }

    var _norefRowIdx = 0;

    function addNorefItemRow() {
        _norefRowIdx++;
        var idx = _norefRowIdx;
        var row = '<tr id="noref-row-' + idx + '">' +
            '<td class="p-1">' +
            '<select class="form-select js-noref-item" name="item_id[' + idx + ']" id="noref_item_' + idx + '">' +
            buildItemSelectOptions('') +
            '</select>' +
            '<input type="hidden" name="item_name[' + idx + ']" id="noref_item_name_' + idx + '">' +
            '</td>' +
            '<td class="p-1"><input type="text" class="form-control" name="description[' + idx + ']" placeholder="Description"></td>' +
            '<td class="p-1"><input type="number" step="0.01" min="0" class="form-control text-right" name="received_qty[' + idx + ']" value="1" style="width:80px;"></td>' +
            '<td class="p-1">' +
            '<select class="form-select" name="purchase_uom[' + idx + ']">' +
            buildUomSelectOptions('') +
            '</select>' +
            '</td>' +
            '<td class="p-1 text-center">' +
            '<button type="button" class="btn btn-sm btn-danger js-noref-del-row" data-idx="' + idx + '"><i class="fa fa-times"></i></button>' +
            '</td>' +
            '</tr>';
        $('#noref-items-body').append(row);
        // Sync item name on item select change
        $('#noref_item_' + idx).on('change', function () {
            var opt = $(this).find('option:selected');
            $('#noref_item_name_' + idx).val(opt.data('name') || opt.text());
            var uom = opt.data('uom') || opt.attr('data-uom');
            if (uom) {
                $(this).closest('tr').find('select[name="purchase_uom[' + idx + ']"]').val(uom).trigger('change');
            }
        });
        if (window.jQuery && jQuery.fn.select2) {
            var opts = {};
            if ($('#guided_printviewdocuments_container').length) {
                opts.dropdownParent = $('#guidedWizardContainer');
            }
            $('#noref_item_' + idx).select2(opts);
        }
    }

    // 2) Global helpers for reference transaction loading
    window.toggleVsDocNoWrap = function () {
        var docType = $('#vendor_supplier_document_type').val();
        var isNoRef = (!docType || docType === 'NOREF');
        var $wrap = $('#vs_document_no_wrap');
        if (isNoRef) {
            $wrap.hide();
        } else {
            $wrap.show();
        }
    };
    window.toggleVsDocNowrap = window.toggleVsDocNoWrap;

    window.getReferenceTransactions = function (selectedValueToSet) {
        var docType = $('#vendor_supplier_document_type').val();
        var targetVal = selectedValueToSet || $('#vendor_supplier_transaction_id').val();
        if (!docType || docType === 'NOREF') {
            $('#vendor_supplier_transaction_id').html('<option value="">Select Any One</option>').trigger('change');

            // Show manual entry table for NOREF
            var tableHtml =
                '<div class="table-responsive mt-2">' +
                '<div class="d-flex align-items-center justify-content-between mb-2">' +
                '<strong><i class="fa fa-list text-primary me-1"></i> Material Items (Manual Entry)</strong>' +
                '<button type="button" class="btn btn-sm btn-success" id="noref-add-row-btn">' +
                '<i class="fa fa-plus"></i> Add Item' +
                '</button>' +
                '</div>' +
                '<table class="table table-hover" id="invoiceItem">' +
                '<thead><tr>' +
                '<th width="43%" class="p-1">Item/Service</th>' +
                '<th class="p-1" width="25%">Description</th>' +
                '<th class="p-1 text-right" width="15%">Received Qty <span class="text-danger">*</span></th>' +
                '<th class="p-1" width="12%">UOM <span class="text-danger">*</span></th>' +
                '<th class="p-1" width="5%"></th>' +
                '</tr></thead>' +
                '<tbody id="noref-items-body"></tbody>' +
                '</table>' +
                '</div>';
            $('#printviewdocuments').html(tableHtml);

            // Bind add row button
            $(document).off('click', '#noref-add-row-btn').on('click', '#noref-add-row-btn', function () {
                addNorefItemRow();
            });
            // Bind delete row button
            $(document).off('click', '.js-noref-del-row').on('click', '.js-noref-del-row', function () {
                var idx = $(this).data('idx');
                $('#noref-row-' + idx).remove();
            });

            // Add one empty row to start
            addNorefItemRow();
            window.lastRefTxnPromise = Promise.resolve();
            return window.lastRefTxnPromise;
        }

        $('#printviewdocuments').html('');
        window.lastRefTxnPromise = $.ajax({
            url: 'ajax.php',
            type: 'POST',
            data: {
                page: 'getreferencetransactions',
                vendor_supplier_document_type: docType
            },
            success: function (html) {
                $('#vendor_supplier_transaction_id').html(html);
                if (targetVal) {
                    $('#vendor_supplier_transaction_id').val(targetVal);
                }
                $('#vendor_supplier_transaction_id').trigger('change');
                $('#printviewdocuments').html('');
            }
        });
        return window.lastRefTxnPromise;
    };

    window.goToAnotherPageAndPrint = function () {
        var txnId = $('#vendor_supplier_transaction_id').val();
        var docType = $('#vendor_supplier_document_type').val();
        if (!txnId) {
            $('#printviewdocuments').html('');
            return;
        }
        $.ajax({
            url: 'views/custom_insert_visitor_logs_vendor_suppliers_ajax.php',
            type: 'POST',
            data: {
                transaction_id: txnId,
                transaction_table: docType
            },
            dataType: 'json',
            success: function (res) {
                if (res) {
                    if (res.vendor_id > 0) {
                        $('#vendor_supplier_vendor_id').val(res.vendor_id).trigger('change');
                    }
                    $('#printviewdocuments').html(res.htmlview);
                    // Initialize Select2 on any loaded dropdowns
                    if (window.jQuery && jQuery.fn.select2) {
                        $('#printviewdocuments .js-example-basic-single').select2();
                    }
                }
            }
        });
    };

    $(function () {
        if ($('#vendor_supplier_document_type').length > 0) {
            window.getReferenceTransactions();
            window.toggleVsDocNoWrap();
        }
    });

    // JavaScript Runtime Error Banner handler
    window.addEventListener('error', function (e) {
        var debugDiv = document.getElementById('js-debug-errors');
        var msgDiv = document.getElementById('js-error-msg');
        if (debugDiv && msgDiv) {
            debugDiv.style.display = 'block';
            msgDiv.innerHTML += '<div class="mt-2"><strong>Error:</strong> ' + e.message + ' <br><strong>File:</strong> ' + e.filename + ' <br><strong>Line:</strong> ' + e.lineno + ':' + e.colno + '</div>';
        }
    });

    // Print Preview Overlay DOMContentLoaded Handler
    document.addEventListener('DOMContentLoaded', function () {
        // Show the print preview overlay
        var overlay = document.getElementById('printPreviewOverlay');
        if (overlay) {
            overlay.style.display = 'flex';
            // Animate in
            setTimeout(function () { overlay.classList.add('active'); }, 50);
        }

        // Print button handler
        var printBtn = document.getElementById('btnPrintBadge');
        if (printBtn) {
            printBtn.addEventListener('click', function () {
                window.print();
            });
        }
    });

    // Auto-select UOM when item changes in Reference PO/PI transaction items table
    $(document).on('change', '.js-ref-item', function () {
        var opt = $(this).find('option:selected');
        var uom = opt.data('uom') || opt.attr('data-uom');
        if (uom) {
            $(this).closest('tr').find('select[name^="purchase_uom"]').val(uom).trigger('change');
        }
    });

    // WebCam Capture Script Integration & Visit Type card clicks
    (function () {
        function initWebcam() {
            var snapBtn = document.getElementById('btnSnapPhoto');
            if (!snapBtn) return;

            const webcamModal = {
                show: function () {
                    const modalEl = document.getElementById('webcamModal');
                    const bsModal = new bootstrap.Modal(modalEl);
                    bsModal.show();
                },
                hide: function () {
                    const modalEl = document.getElementById('webcamModal');
                    const bsModal = bootstrap.Modal.getInstance(modalEl);
                    if (bsModal) {
                        bsModal.hide();
                    }
                }
            };
            var video = document.getElementById('webcamVideo');
            var canvas = document.getElementById('webcamCanvas');
            var captureBtn = document.getElementById('btnCaptureFrame');
            var webcamImageHidden = document.getElementById('webcam_image');

            var stream = null;

            // Webcam snap frame trigger
            snapBtn.addEventListener('click', function () {
                webcamModal.show();
                navigator.mediaDevices.getUserMedia({ video: { facingMode: "user", width: 640, height: 480 } })
                    .then(function (s) {
                        stream = s;
                        video.srcObject = stream;
                        video.play();
                    })
                    .catch(function (err) {
                        console.error("Camera access error:", err);
                        alert("Could not access camera. Please check permissions.");
                        webcamModal.hide();
                    });
            });

            // Stop camera when modal hidden
            $('#webcamModal').on('hidden.bs.modal', function () {
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                    stream = null;
                }
                video.srcObject = null;
            });

            // Snap Frame logic
            captureBtn.addEventListener('click', function () {
                if (!stream) return;
                var context = canvas.getContext('2d');
                context.translate(canvas.width, 0);
                context.scale(-1, 1);
                context.drawImage(video, 0, 0, canvas.width, canvas.height);
                context.setTransform(1, 0, 0, 1, 0, 0);

                var dataURL = canvas.toDataURL('image/jpeg', 0.9);
                webcamImageHidden.value = dataURL;
                $('#image_path').val(''); // clear file selection

                updatePhotoPreview(dataURL);
                webcamModal.hide();
            });

            // Click "Upload Photo" triggers the hidden file input
            $(document).on('click', '#btnUploadTrigger', function () {
                $('#image_path').trigger('click');
            });

            // File Selection preview handling
            $('#image_path').on('change', function () {
                if (this.files && this.files[0]) {
                    if (webcamImageHidden) webcamImageHidden.value = '';
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        updatePhotoPreview(e.target.result);
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });

            // Remove photo option
            $(document).on('click', '#btnRemovePhoto', function () {
                $('#image_path').val('');
                if (webcamImageHidden) webcamImageHidden.value = '';
                $('input[name="existing_image"]').val('');

                // Hide previews
                var personImg = document.getElementById('personPreviewInline');
                if (personImg) {
                    personImg.src = '';
                    personImg.classList.add('d-none');
                }
                $('.photo-preview-container').removeClass('d-flex').addClass('d-none');
                $('.photo-actions-container').removeClass('d-none').addClass('d-flex');

                // Reset right-side live badge preview photo
                var badgePhoto = document.getElementById('badgePreviewPhoto');
                if (badgePhoto) {
                    badgePhoto.src = '';
                    badgePhoto.classList.add('d-none');
                    badgePhoto.style.display = 'none';
                }
                var badgePlaceholder = document.querySelector('.badge-photo-placeholder');
                if (badgePlaceholder) {
                    badgePlaceholder.classList.remove('d-none');
                    badgePlaceholder.style.display = 'flex';
                }
            });

            // Change photo option
            $(document).on('click', '#btnChangePhoto', function () {
                $('#btnRemovePhoto').trigger('click');
            });

            function updatePhotoPreview(dataURL) {
                // Update inline preview image src
                var personImg = document.getElementById('personPreviewInline');
                if (personImg) {
                    personImg.src = dataURL;
                    personImg.classList.remove('d-none');
                }

                // Show preview container, hide action selection cards
                $('.photo-actions-container').removeClass('d-flex').addClass('d-none');
                $('.photo-preview-container').removeClass('d-none').addClass('d-flex');

                // Update live badge photo preview on the right
                var badgePhoto = document.getElementById('badgePreviewPhoto');
                if (badgePhoto) {
                    badgePhoto.src = dataURL;
                    badgePhoto.classList.remove('d-none');
                    badgePhoto.style.display = 'block';
                }
                var badgePlaceholder = document.querySelector('.badge-photo-placeholder');
                if (badgePlaceholder) {
                    badgePlaceholder.classList.add('d-none');
                    badgePlaceholder.style.display = 'none';
                }
            }

            // Custom CSS styling adjustment to visit type card click
            document.querySelectorAll('.visit-type-card').forEach(function (card) {
                card.addEventListener('click', function () {
                    if (this.classList.contains('disabled')) return;
                    document.querySelectorAll('.visit-type-card').forEach(c => c.classList.remove('active'));
                    this.classList.add('active');
                    var radio = this.querySelector('input[type="radio"]');
                    if (radio && !radio.checked) {
                        radio.checked = true;
                        // trigger change event on radio manually to inform the external JS controller
                        var event = new Event('change', { bubbles: true });
                        radio.dispatchEvent(event);
                    }
                });
            });

            // Modern Gate Selector click handling & onload activation
            $(document).on('click', '.gate-select-card', function () {
                var gateId = $(this).data('gate-id');
                $('.gate-select-card').removeClass('active');
                $(this).addClass('active');
                $('#in_gate_no').val(gateId).trigger('change');
            });
            var initialGateVal = $('#in_gate_no').val();
            if (initialGateVal && initialGateVal !== '0') {
                $('.gate-select-card').removeClass('active');
                $(`.gate-select-card[data-gate-id="${initialGateVal}"]`).addClass('active');
            }
        }

        if (window.jQuery) {
            initWebcam();
        } else {
            window.addEventListener('load', function () {
                var interval = setInterval(function () {
                    if (window.jQuery) {
                        clearInterval(interval);
                        initWebcam();
                    }
                }, 50);
            });
        }
    })();

    // Toggle Additional Details Section
    $(document).on('click', '.toggle-additional-btn', function (e) {
        e.preventDefault();
        var $btn = $(this);
        var $section = $btn.closest('.toggle-btn-wrapper').next('.additional-fields-section');
        if (!$section.length) {
            $section = $btn.closest('.row').find('.additional-fields-section');
        }
        if ($section.hasClass('d-none')) {
            $section.removeClass('d-none').addClass('d-view-block');
            var newHtml = $btn.html()
                .replace('fa-plus-circle', 'fa-minus-circle')
                .replace(/Add\s+Optional/i, 'Hide Optional');
            $btn.html(newHtml);
        } else {
            $section.removeClass('d-view-block').addClass('d-none');
            var newHtml = $btn.html()
                .replace('fa-minus-circle', 'fa-plus-circle')
                .replace(/Hide\s+Optional/i, 'Add Optional');
            $btn.html(newHtml);
        }
    });

    // Inline Option Master additions
    $(document).on('click', '.add-option-trigger', function (e) {
        e.preventDefault();
        var optionType = $(this).data('type');
        var selectId = $(this).data('select-id');
        var label = $(this).data('label') || 'Option';

        $('#modal_option_type').val(optionType);
        $('#modal_select_id').val(selectId);
        $('#modal_option_label_title').text(label);
        $('#modal_option_value').val('').attr('placeholder', 'Enter new ' + label.toLowerCase());
        
        var modal = new bootstrap.Modal(document.getElementById('addOptionModal'));
        modal.show();
    });

    $(document).off('click', '#btn_save_modal_option').on('click', '#btn_save_modal_option', function (e) {
        e.preventDefault();
        var optionType = $('#modal_option_type').val();
        var selectId = $('#modal_select_id').val();
        var optionValue = $('#modal_option_value').val().trim();

        if (!optionValue) {
            alert('Please enter a value');
            return;
        }

        var saveBtn = $(this);
        saveBtn.prop('disabled', true).text('Saving...');

        $.ajax({
            url: 'ajax.php?page=add_custom_visitor_option',
            type: 'POST',
            data: {
                option_type: optionType,
                option_value: optionValue,
                page:'add_custom_visitor_option'
            },
            dataType: 'json',
            success: function (res) {
                saveBtn.prop('disabled', false).text('Save Option');
                if (res.status === 'success') {
                    var val = res.option_value;
                    var lbl = res.option_label;

                    if (selectId === 'purpose') {
                        // Radio button container
                        var container = $('#purpose-radios-container');
                        var safeVal = val.replace(/[^a-zA-Z0-9]/g, '_');
                        var radioHtml = 
                            '<div class="form-check form-check-inline mt-2">' +
                            '<input class="form-check-input" type="radio" name="purpose" id="purpose_' + safeVal + '" value="' + val + '" checked>' +
                            '<label class="form-check-label" for="purpose_' + safeVal + '">' + lbl + '</label>' +
                            '</div>';
                        // Insert radioHtml before the button inside the container
                        container.find('.add-option-trigger').before(radioHtml);
                        
                        // Trigger change on purpose radios to handle dynamic page changes
                        $('input[name="purpose"]:checked').trigger('change');
                    } else {
                        // Update target dropdown(s)
                        var targets = [];
                        if (optionType === 'identity_card') {
                            targets = ['#idcard_type', '#idcard_type_service', '#doctor_idcard_type'];
                        } else {
                            targets = ['#' + selectId];
                        }

                        targets.forEach(function (tid) {
                            var $sel = $(tid);
                            if ($sel.length) {
                                // check if option already exists
                                if ($sel.find('option[value="' + val + '"]').length === 0) {
                                    var newOpt = new Option(lbl, val, true, true);
                                    $sel.append(newOpt);
                                }
                                $sel.val(val);
                                if ($sel.hasClass('js-example-basic-single') && $.fn.select2) {
                                    $sel.trigger('change');
                                }
                            }
                        });
                    }

                    // Hide modal
                    var modalEl = document.getElementById('addOptionModal');
                    var bsModal = bootstrap.Modal.getInstance(modalEl);
                    if (bsModal) {
                        bsModal.hide();
                    }
                } else {
                    alert(res.message || 'Failed to save option');
                }
            },
            error: function () {
                saveBtn.prop('disabled', false).text('Save Option');
                alert('An error occurred. Please try again.');
            }
        });
    });

    // Inline Other fields add buttons
    $(document).on('click', '.btn-add-inline-other', function (e) {
        e.preventDefault();
        var $btn = $(this);
        var optionType = $btn.data('type');
        var inputId = $btn.data('input-id');
        var selectId = $btn.data('select-id');
        var optionValue = $('#' + inputId).val().trim();

        if (!optionValue) {
            alert('Please enter a value');
            return;
        }

        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Adding...');

        $.ajax({
            url: 'ajax.php?page=add_custom_visitor_option',
            type: 'POST',
            data: {
                option_type: optionType,
                option_value: optionValue,
                page:'add_custom_visitor_option'
            },
            dataType: 'json',
            success: function (res) {
                $btn.prop('disabled', false).html('<i class="fa fa-plus"></i> Add');
                if (res.status === 'success') {
                    var val = res.option_value;
                    var lbl = res.option_label;

                    if (selectId === 'purpose') {
                        // Radio button container
                        var container = $('#purpose-radios-container');
                        var safeVal = val.replace(/[^a-zA-Z0-9]/g, '_');
                        
                        // Check if radio already exists
                        var radioExists = $('input[name="purpose"][value="' + val + '"]');
                        if (radioExists.length === 0) {
                            var radioHtml = 
                                '<div class="form-check form-check-inline mt-2">' +
                                '<input class="form-check-input" type="radio" name="purpose" id="purpose_' + safeVal + '" value="' + val + '">' +
                                '<label class="form-check-label" for="purpose_' + safeVal + '">' + lbl + '</label>' +
                                '</div>';
                            container.find('.add-option-trigger').before(radioHtml);
                        }
                        
                        // Check it
                        $('input[name="purpose"][value="' + val + '"]').prop('checked', true).trigger('change');
                        
                        // Clear and hide both standard and guided wraps
                        $('#purpose_other').val('');
                        $('#guided_purpose_other').val('');
                        $('#purpose_other_wrap').addClass('d-none');
                        $('#guidedOtherPurposeWrap').addClass('d-none');
                        
                        // If it was clicked from guided mode, re-render and auto-advance
                        if (inputId.startsWith('guided')) {
                            if (typeof window.renderGuidedSlide === 'function') {
                                window.renderGuidedSlide();
                                if (typeof window.advanceGuidedSlide === 'function') {
                                    setTimeout(function () {
                                        window.advanceGuidedSlide();
                                    }, 600);
                                }
                            }
                        }
                    } else if (selectId === 'lm_brand_name') {
                        var $sel = $('#' + selectId);
                        if ($sel.length) {
                            if ($sel.find('option[value="' + val + '"]').length === 0) {
                                // insert before the 'Other' option
                                var newOpt = $('<option>', { value: val, text: lbl });
                                $sel.find('option[value="Other"]').before(newOpt);
                            }
                            $sel.val(val).trigger('change');
                        }
                        // Clear and hide standard and guided inputs
                        $('#lm_brand_name_other').val('').addClass('d-none');
                        $('#guided_brand_name_other').val('').addClass('d-none');
                        $btn.addClass('d-none');
                    } else {
                        var $sel = $('#' + selectId);
                        if ($sel.length) {
                            if ($sel.find('option[value="' + val + '"]').length === 0) {
                                // insert before the '+ Add New Company' or '__new__' option
                                var newOpt = $('<option>', { value: val, text: lbl });
                                var newTriggerOpt = $sel.find('option[value="__new__"]');
                                if (newTriggerOpt.length) {
                                    newTriggerOpt.before(newOpt);
                                } else {
                                    $sel.append(newOpt);
                                }
                            }
                            $sel.val(val).trigger('change');
                        }
                        
                        // Clear and hide standard and guided inputs
                        $('#new_delivery_company').val('');
                        $('#guided_new_company').val('');
                        $('#new_company_box').addClass('d-none');
                        $('#guidedNewCompanyWrap').addClass('d-none');

                        $('#new_service_company').val('');
                        $('#guided_new_service_company').val('');
                        $('#new_service_company_box').addClass('d-none');
                        $('#guidedServiceCompanyWrap').addClass('d-none');
                        
                        // If it was clicked from guided mode, re-render and auto-advance
                        if (inputId.startsWith('guided')) {
                            if (typeof window.renderGuidedSlide === 'function') {
                                window.renderGuidedSlide();
                                if (typeof window.advanceGuidedSlide === 'function') {
                                    setTimeout(function () {
                                        window.advanceGuidedSlide();
                                    }, 600);
                                }
                            }
                        }
                    }
                } else {
                    alert(res.message || 'Failed to save option');
                }
            },
            error: function () {
                $btn.prop('disabled', false).html('<i class="fa fa-plus"></i> Add');
                alert('An error occurred. Please try again.');
            }
        });
    });

    // Auto-checkout trigger from dashboard redirect
    $(function() {
        const urlParams = new URLSearchParams(window.location.search);
        
        // 1. Switch to checkout tab if active_tab is checkout (regardless of from_dashboard)
        if (urlParams.get('active_tab') === 'checkout') {
            const tabEl = document.querySelector('#checkout-tab');
            if (tabEl) {
                tabEl.click();
            }
        }

        // 2. Run search and auto-checkout only if from_dashboard=1
        if (urlParams.get('active_tab') === 'checkout' && urlParams.get('from_dashboard') === '1') {
            const searchTerm = urlParams.get('search_term');
            const logId = parseInt(urlParams.get('visitor_log_id'), 10) || 0;
            if (searchTerm) {
                $('#checkoutSearchText').val(searchTerm);
                
                // Trigger the AJAX search
                const location_id = ($('#location_id_checkout').val() || '').trim();
                const visit_type = ($('#checkoutSearchType').val() || '').trim();
                const date = ($('#checkoutSearchDate').val() || '').trim();
                
                $.ajax({
                    url: 'ajax.php',
                    type: 'POST',
                    data: {
                        page: 'getVisitorsSearch',
                        text: searchTerm,
                        date: date,
                        location_id: location_id,
                        visit_type: visit_type
                    },
                    success: function (resp) {
                        let json = null;
                        try {
                            json = JSON.parse(resp);
                        } catch (e) { /* ignore */ }
                        const rows = (json && json.visitor_logs_res) ? json.visitor_logs_res : [];
                        if (typeof window.renderVisitorRows === 'function') {
                            window.renderVisitorRows(rows);
                        }
                        
                        // 3. If a specific log ID is passed, auto-trigger its Check Out action!
                        if (logId > 0 && rows.length > 0) {
                            const btn = $(`.js-checkout-view-visitor[data-id="${logId}"]`);
                            if (btn.length) {
                                btn.click();
                            }
                        }
                    }
                });
            }
        }
    });

}); // end waitForJQuery

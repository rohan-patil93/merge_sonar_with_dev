// custom_view_visitor_out.js
const cfgEl = document.getElementById('visitor-out-config');
const CFG = cfgEl ? JSON.parse(cfgEl.textContent) : {};

const serviceCompanyRes  = CFG.serviceCompanyRes  || {};
const deviveryCompanyRes = CFG.deliveryCompanyRes || {};

// helpers
function toastErr(msg) {
    if (typeof tostarnotify === 'function') {
        tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger',
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
                displayName: deviveryCompanyRes[r.delivery_company_person] ||  '',
                displayPhone: r.delivery_phone || r.phone || ''
            };
        case 'service':
            return {
                visitType: 'Service',
                displayName: serviceCompanyRes[r.service_company]  || '',
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
    const $tbody = $('#visitorHistoryTable tbody');
    if (!records || !records.length) {
        $('#visitorHistoryWrap').removeClass('visitor-history-wrap');
        $('#visitorHistoryWrap').addClass('visitor-history-wrap-is-visible');
        $tbody.html(`
            <tr class="no-records-row">
            <td colspan="7">
                <div class="text-center">
                <p class="text-muted f-16 fw-bold m-b-30">
                    <i class="fa fa-warning"></i> No Records
                </p>
                <div class="text-center px-4">
                    <img class="visitor-empty-img" alt="" src="assets/images/work.png" >
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
        // build action button (calls viewVisitor with id, visit_type)
        const idSafe = parseInt(r.id, 10) || 0;
        const vtSafe = (r.visit_type || r.visitor_type || 'visitor').toLowerCase();
        const actionBtn = `
            <button type="button"
                    class="btn btn-sm btn-primary js-view-visitor"
                    data-id="${idSafe}"
                    data-visit-type="${escapeHtml(vtSafe)}">
                <i class="fa fa-eye"></i> View
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
    $('#visitorHistoryWrap').removeClass('visitor-history-wrap');
    $('#visitorHistoryWrap').addClass('visitor-history-wrap-is-visible');
    $('#visitorHistoryTable tbody').html(html);
}
$(document).on('click', '.js-view-visitor', function () {
    const id = parseInt($(this).data('id'), 10) || 0;
    const visitType = ($(this).data('visit-type') || '').toLowerCase();
    viewVisitor(id, visitType);
});

function viewVisitor(id, visitType) {
    const $gate = $('#out_gate_no');
    data = {
        type: visitType,
        id: id,
        key: 'AJAX_FETCH_VISITOR_OUT'
    };

    const loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;"></div>';
    $('#visitorResult').html(loader);

    $.ajax({
        url: 'views/visitor_out_view.php',
        type: 'GET',
        data: data, // { type, gate_id, id }
        success: function(response) {
            $('#visitorResult').html(response)
                .find('.js-example-basic-single')
                .each(function () {
                    $(this).select2();
            });

        },
        error: function(error) {
            console.error('Failed to load view:', error);
            invalid('Failed to load the page. Please try again.');
        }
    });
}
// Search click handler
(function() {
    $('#historySearchBtn').on('click', function() { 
        const text = ($('#historySearchText').val() || '').trim();
        const date = ($('#historySearchDate').val() || '').trim(); // YYYY-MM-DD
        const location_id = ($('#location_id').val() || '').trim();

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
                location_id: location_id
            },
            success: function(resp) {
                let json = null;
                try {
                    json = JSON.parse(resp);
                } catch (e) {
                    /* ignore */
                }
                const rows = (json && json.visitor_logs_res) ? json.visitor_logs_res : [];
                renderVisitorRows(rows);
            },
            error: function() {
                toastErr('Failed to fetch history. Try again.');
            }
        });
    });
})();

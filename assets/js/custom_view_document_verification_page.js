$(document).ready(function () {
    $(document).off("click", ".verifyPAN").on("click", ".verifyPAN", function () {
        NexVeri.verify('PAN');
    });
    $(document).off("click", ".verifyGST").on("click", ".verifyGST", function () {
        NexVeri.verify('GST');
    });
    $(document).off("click", ".verifyBANK").on("click", ".verifyBANK", function () {
        NexVeri.verify('BANK');
    });
    $(document).off("click", ".verifyMSME").on("click", ".verifyMSME", function () {
        NexVeri.verify('MSME');
    });
    $(document).off("click", ".verifyTAN").on("click", ".verifyTAN", function () {
        NexVeri.verify('TAN');
    });
});
const NexVeri = {
    getEndpoint: function () {
        //const path = window.location.pathname;
        //if (path.includes('/views/')) return '../apis/call_api.php';
        return '../apis/call_api_without_log.php';
    },
    verify: function (type) {
        // if (!$(`#consent_${type.toLowerCase()}`).is(':checked')) {
        //     this.fb(type, 'Please provide consent to proceed', 'error');
        //     return;
        // }

        const payload = this.prepare(type);
        if (!payload) return;

        this.setLoading(type, true);
        this.clearFB(type);

        $.ajax({
            url: this.getEndpoint(),
            type: 'POST',
            data: payload,
            dataType: 'json',
            success: (res) => this.handle(type, res),
            error: () => this.fb(type, 'communication failed', 'error'),
            complete: () => this.setLoading(type, false)
        });
    },

    prepare: function (type) {
        let d = { 
            consent: 'Y', 
            consent_text: 'I hereby authorize and confirm that I have obtained valid consent to verify this information.', 
            mode: 'sync', 
            transaction_table: 'standalone', 
            transaction_id: '0' 
        };

        switch (type) {
            case 'PAN':
                const p = $('#nx_pan').val().toUpperCase().trim();
                if (!/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/.test(p)) return this.fb(type, 'Invalid PAN Format', 'error');
                d.api_code = 'ZOOPONEPANLITE'; d.customer_pan_number = p; d.pan_holder_name = 'Enterprise Verification';
                break;
            case 'GST':
                const g = $('#nx_gst').val().toUpperCase().trim();
                if (!/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/.test(g)) return this.fb(type, 'Invalid GSTIN Format', 'error');
                d.api_code = 'GSTVERIFICATIONLITE'; d.business_gstin_number = g;
                break;
            case 'BANK':
                const a = $('#nx_bank_acc').val().trim(), i = $('#nx_bank_ifsc').val().toUpperCase().trim();
                if (!a || !i) return this.fb(type, 'Bank Details Missing', 'error');
                d.api_code = 'BANKACVERIFICATIONLITE'; d.account_number = a; d.ifsc = i;
                break;
            case 'MSME':
                const m = $('#nx_msme').val().toUpperCase().trim();
                if (!m) return this.fb(type, 'Udyam Number Required', 'error');
                d.api_code = 'UDYOGAADHAAR'; d.udyog_aadhaar = m;
                break;
            case 'TAN':
                const t = $('#nx_tan').val().toUpperCase().trim();
                if (!/^[A-Z]{4}[0-9]{5}[A-Z]{1}$/.test(t)) return this.fb(type, 'Invalid TAN Format', 'error');
                d.api_code = 'ZOOPONETANLITE'; d.customer_tan_number = t;
                break;
        }
        return d;
    },

    handle: function (type, res) {
        if (res.success && res.result) {
            this.showResultModal(type, res.result);
        } else {
            const msg = res.metadata?.reason_message || res.response_message || 'Verification Failed';
            this.fb(type, msg, 'error');
        }
    },

    showResultModal: function (type, data) {
        const grid = $('#modal-grid');
        const title = $('#modal-title');
        const iconBox = $('#modal-icon-box');

        title.text(`${type} Verification Report`);
        
        let icon = 'fa-id-card';
        let color = '#4f46e5';
        if (type === 'GST') { icon = 'fa-file-invoice'; color = '#0ea5e9'; }
        if (type === 'BANK') { icon = 'fa-university'; color = '#f59e0b'; }
        if (type === 'MSME') { icon = 'fa-industry'; color = '#ec4899'; }
        if (type === 'TAN') { icon = 'fa-file-invoice-dollar'; color = '#6366f1'; }

        iconBox.html(`<i class="fas ${icon}"></i>`).css('color', color).css('background', `${color}15`);

        grid.empty();
        this.renderRecursive(grid, data);

        const bootstrapModal = new bootstrap.Modal(document.getElementById('nxResultModal'));
        bootstrapModal.show();
    },

    renderRecursive: function (grid, data) {
        Object.keys(data).forEach(k => {
            const v = data[k];
            if (v && typeof v === 'object' && !Array.isArray(v)) {
                this.renderRecursive(grid, v);
            } else if (v !== null && v !== undefined && v !== '') {
                const label = k.replace(/_/g, ' ').toUpperCase();
                grid.append(`
                    <div class="nx-data-item">
                        <div class="nx-data-label">${label}</div>
                        <div class="nx-data-val">${Array.isArray(v) ? v.join(', ') : v}</div>
                    </div>
                `);
            }
        });
    },

    setLoading: function (type, is) {
        const typeLower = type.toLowerCase();
        const card = $(`#card-${typeLower}`);
        
        if (is) {
            card.addClass('loading');
        } else {
            card.removeClass('loading');
        }
    },

    fb: function (type, msg, s) { 
        $(`#${type.toLowerCase()}-feedback`).text(msg).css('color', s === 'error' ? '#ef4444' : '#4f46e5'); 
        return null; 
    },
    clearFB: function (type) { $(`#${type.toLowerCase()}-feedback`).text(''); },
    
    resetFields: function () {
        $('.nx-input').val('');
        $('.nx-checkbox-label input').prop('checked', false);
        $('.small.fw-bold').text('');
    }
};

$(document).on('hidden.bs.modal', '#nxResultModal', function () {
    NexVeri.resetFields();
});

$(document).on('input', '.text-uppercase', function () { 
    this.value = this.value.toUpperCase(); 
});

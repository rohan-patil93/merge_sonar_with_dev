document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('.csr-closing-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            closingRequest(this.dataset.id);
        });
    });
});

function closingRequest(rid) {
    if (!rid) {
        console.error('Invalid rid');
        return;
    }

    const formData = {
        id: rid,
        fid: 'id',
        tracking_status: '1'
    };

    $.ajax({
        url: 'ajax.php',
        type: 'POST',
        data: {
            page: 'updatemasters',
            table_name: 'csr_proposal_data',
            sendData: JSON.stringify(formData)
        },
        success: function (response) {
            const data = JSON.parse(JSON.parse(response));

            if (data['status'] === 'sucess') {
                window.location.href = "index.php?option=COM_LIST_CSR_CLOSING_REQUEST";
            } else {
                alert('Failed to save item Type: ' + data.message);
            }
        },
        error: function () {
            alert('An error occurred. Please try again.');
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {

    // -------------------- SYNC DATA --------------------
    var syncBtn = document.getElementById('syncBtn');

    if (syncBtn) {
        syncBtn.addEventListener('click', function () {
            syncData();
        });
    }

});
const cfg = document.getElementById('csr_config')?.dataset || {};
const syncSuccessMsg = cfg.syncSuccessMsg || '';
function syncData() {
    $.ajax({
        url: 'cronjob/add_all_csr_data.php',
        type: 'GET',
        data: '',
        success: function (response) {
            console.log('response', response);

            tostarnotify(
                'top',
                'right',
                'fa fa-thumbs-up',
                'form-label label label-success',
                'animated flipInX',
                'animated flipOutX',
                syncSuccessMsg,
                ''
            );

            window.location.href = window.location.href;
        },
        error: function () {
            alert('Failed to load data');
        }
    });
}

// Survey Setup - JavaScript Functions

document.addEventListener('DOMContentLoaded', function() {
    // Add question link handler
    const addQuestionLinks = document.querySelectorAll('.survey-add-question-link');
    addQuestionLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            if (typeof addQuestion === 'function') {
                const type = this.getAttribute('data-type');
                const title = this.getAttribute('data-title');
                addQuestion(type, title);
            }
        });
    });

    // Show popup link handler
    const showPopupLinks = document.querySelectorAll('.survey-show-popup');
    showPopupLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            if (typeof showpopup === 'function') {
                showpopup();
            }
        });
    });

    // Delete question handler
    const deleteQuestionButtons = document.querySelectorAll('.survey-delete-question');
    deleteQuestionButtons.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            if (typeof deleteQuestion === 'function') {
                const qid = this.getAttribute('data-qid');
                deleteQuestion(qid);
            }
        });
    });

    // Delete option handler
    const deleteOptionButtons = document.querySelectorAll('.survey-delete-option');
    deleteOptionButtons.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            if (typeof deleteOption === 'function') {
                const rowid = this.getAttribute('data-rowid');
                const qid = this.getAttribute('data-qid');
                deleteOption(rowid, qid);
            }
        });
    });

    // Add option button handler
    const addOptionButtons = document.querySelectorAll('.survey-add-option-btn');
    addOptionButtons.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            if (typeof addNewRow === 'function') {
                const qid = this.getAttribute('data-qid');
                addNewRow(qid);
            }
        });
    });
});

// Stub functions - implement based on your requirements
function showpopup() {
    var filter_id = document.getElementById('filtertbl_rowid').value;
    jQuery.ajax({
        url: "views/custom_filter_bulktransfer.php",
        data: 'filter_id=' + filter_id,
        type: "GET",
        success: function(data) {
            jQuery('#popup2').html(data);
            jQuery('#kt_modal_KTDatatable_setup').modal('show');
        },
        error: function() {
            console.error('Failed to load popup');
        }
    });
}

// Placeholder functions - add your actual implementations
function addQuestion(type, title) {
    console.log('Add question: type=' + type + ', title=' + title);
}

function deleteQuestion(qid) {
    console.log('Delete question: ' + qid);
}

function deleteOption(rowid, qid) {
    console.log('Delete option: rowid=' + rowid + ', qid=' + qid);
}

function addNewRow(qid) {
    console.log('Add new option row: qid=' + qid);
}

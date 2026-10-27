(function() {
    'use strict';

    var anscount = 0;
    var myVar = 0;
    var obj = {};

    function getPhpData() {
        var dataContainer = document.querySelector('.survey-response-php-data');
        if (!dataContainer) return {};
        return {
            totalQuestions: dataContainer.dataset.totalQuestions,
            surveyId: dataContainer.dataset.surveyId,
            empId: dataContainer.dataset.empId,
            category: dataContainer.dataset.category
        };
    }

    function startTimer(duration, display) {
        duration = duration * 60;
        document.getElementById("save").style.display = "block";
        document.getElementById("start").style.display = "none";
    }

    function callnext(qno, currentqno, qtype) {
        var x;
        var totalquestions = document.getElementById('total_q').value;
        for (x = 1; x <= totalquestions; x++) {
            if (x == qno) {
                document.getElementById("disdiv" + x).style.display = "block";
                document.getElementById("start").style.display = "none";
                document.getElementById("save").style.display = "block";
            } else {
                document.getElementById("disdiv" + x).style.display = "none";
            }
        }
        var checkedval = [];
        if (qtype == 1 || qtype == 3) {
            var inputs = document.querySelectorAll('.response_option' + currentqno);
            for (var i = 0; i < inputs.length; i++) {
                if (inputs[i].checked) {
                    $('#summarytext' + currentqno).addClass("active");
                    checkedval.push(inputs[i].value);
                }
            }
            if (checkedval.length > 0) {
                obj[currentqno] = checkedval;
            } else {
                document.getElementById('summary' + currentqno).style.backgroundColor = '#FFFFFF';
                $('#summarytext' + currentqno).css("color", "#000000");
                delete obj[currentqno];
                console.log(obj);
            }
        } else {
            var textval = document.getElementById('response_option' + currentqno).value;
            if (textval.length > 0) {
                $('#summarytext' + currentqno).addClass("active");
                checkedval.push(textval);
                obj[currentqno] = checkedval;
            } else {
                delete obj[currentqno];
                document.getElementById('summary' + currentqno).style.backgroundColor = '#FFFFFF';
                $('#summarytext' + currentqno).css("color", "#000000");
            }
        }
        console.log(obj);
        document.getElementById('anscount').innerHTML = Object.keys(obj).length;
        document.getElementById('unanscount').innerHTML = totalquestions - Object.keys(obj).length;
        document.getElementById('answered').value = Object.keys(obj).length;
    }

    function callclear(qno, qtype) {
        if (qtype == 1 || qtype == 3) {
            var inputs = document.querySelectorAll('.response_option' + qno);
            for (var i = 0; i < inputs.length; i++) {
                inputs[i].checked = false;
            }
        } else {
            document.getElementById('response_option' + qno).value = "";
        }
        document.getElementById('summary' + qno).style.backgroundColor = '#FFFFFF';
        $('#summarytext' + qno).css("color", "#000000");
        delete obj[qno];
        document.getElementById('anscount').innerHTML = Object.keys(obj).length;
        document.getElementById('unanscount').innerHTML = totalquestions - Object.keys(obj).length;
    }

    function submitform() {
        console.log("form submission after");
        $("#myForm").submit();
    }

    function submitsurvey() {
        Swal.fire({
            title: "",
            text: "Time is Expired And Please Submit",
            buttonsStyling: false,
            confirmButtonText: "<i class='la la-thumbs-up'></i>OK",
            showCancelButton: false,
            cancelButtonText: "<i class='la la-thumbs-down'></i> No I will do it later",
            customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-default"
            }
        }).then(function(result) {
            document.getElementById('expiry_submit').style.display = 'block';
            document.getElementById('save').style.display = 'none';
            document.getElementById('previous').style.display = 'none';
            console.log("form submission before");
        });
    }

    function bindEvents() {
        $(document).on('click', '.survey-next-btn', function(e) {
            e.preventDefault();
            var qno = $(this).data('qno');
            var currentqno = $(this).data('currentqno');
            var qtype = $(this).data('qtype');
            callnext(qno, currentqno, qtype);
        });

        $(document).on('click', '.survey-previous-btn', function(e) {
            e.preventDefault();
            var qno = $(this).data('qno');
            var currentqno = $(this).data('currentqno');
            var qtype = $(this).data('qtype');
            callnext(qno, currentqno, qtype);
        });

        $(document).on('click', '.survey-clear-btn', function(e) {
            e.preventDefault();
            var qno = $(this).data('qno');
            var qtype = $(this).data('qtype');
            callclear(qno, qtype);
        });

        $(document).on('click', '.survey-summary-link', function(e) {
            e.preventDefault();
            var qno = $(this).data('qno');
            var currentqno = $(this).data('currentqno');
            var qtype = $(this).data('qtype');
            callnext(qno, currentqno, qtype);
        });
    }

    function init() {
        bindEvents();
    }

    $(document).ready(function() {
        init();
    });

})();

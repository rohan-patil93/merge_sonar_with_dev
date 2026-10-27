var date_format = DateFormate;

$(function () {
    if ($('#backdate_limit').length) {
        var backdate_limit = $('#backdate_limit').val();
        backdate_limit = backdate_limit != "" ? "-" + backdate_limit : 0;
    } else {
        var backdate_limit = -600;
    }

    // --- Normal Datepicker ---
    $(".datepicker").datepicker({
        showOn: "both",
        buttonText: "Calendar", // placeholder text
        dateFormat: date_format,
        minDate: backdate_limit,
        setDate: new Date(),
        changeMonth: true,
        changeYear: true
    }).next("button.ui-datepicker-trigger")
      .html("<i class='ti-calendar text-dark f-12 f-w-800 m-r-10'></i>");

    // --- Datepicker with range -10 to +10 ---
    $(".datepicker1").datepicker({
        showOn: "both",
        buttonText: "Calendar",
        dateFormat: date_format,
        minDate: -10,
        maxDate: "+10D",
        changeMonth: true,
        changeYear: true
    }).next("button.ui-datepicker-trigger")
      .html("<i class='ti-calendar text-dark f-12 f-w-800 m-r-10'></i>");

    // --- Previous only ---
    $(".datepickerprevious").datepicker({
        showOn: "both",
        buttonText: "Calendar",
        dateFormat: date_format,
        changeMonth: true,
        changeYear: true,
        maxDate: 'today'
    }).next("button.ui-datepicker-trigger")
      .html("<i class='ti-calendar text-dark f-12 f-w-800 m-r-10'></i>");

    // --- Not Previous ---
    $(".datepickernotprevious").datepicker({
        showOn: "both",
        buttonText: "Calendar",
        dateFormat: date_format,
        changeMonth: true,
        changeYear: true,
        minDate: 'today'
    }).next("button.ui-datepicker-trigger")
      .html("<i class='ti-calendar text-dark f-12 f-w-800 m-r-10'></i>");

    /* --- Leave date range picker --- */
    $(".startdatepicker").datepicker({
        dateFormat: date_format,
        changeMonth: true,
        changeYear: true,
        showOn: "both",
        buttonText: "Calendar",
        onSelect: function (date) {
            var date2 = $('.startdatepicker').datepicker('getDate');
            $('.enddatepicker').datepicker('setDate', date2);
            $('.enddatepicker').datepicker('option', 'minDate', date2);
            tocheckdays();
        }
    }).next("button.ui-datepicker-trigger")
      .html("<i class='ti-calendar text-dark f-12 f-w-800 m-r-10'></i>");

    $('.enddatepicker').datepicker({
        dateFormat: date_format,
        changeMonth: true,
        changeYear: true,
        showOn: "both",
        buttonText: "Calendar",
        onClose: function () {
            var dt1 = $('.startdatepicker').datepicker('getDate');
            var dt2 = $('.enddatepicker').datepicker('getDate');
            if (dt2 <= dt1) {
                var minDate = $('.enddatepicker').datepicker('option', 'minDate');
                $('.enddatepicker').datepicker('setDate', minDate);
            }
        }
    }).next("button.ui-datepicker-trigger")
      .html("<i class='ti-calendar text-dark f-12 f-w-800 m-r-10'></i>");

    


    /* --- Dynamic range datepicker --- */
/**    $(".rangedatepicker").each(function () {
        var seq = $(this).attr('data-id');

        $(".startdatepicker_" + seq).datepicker({
            dateFormat: date_format,
            changeMonth: true,
            changeYear: true,
            showOn: "both",
            buttonText: "Calendar",
            onSelect: function (date) {
                var date2 = $('.startdatepicker_' + seq).datepicker('getDate');
                $('.enddatepicker_' + seq).datepicker('setDate', date2);
                $('.enddatepicker_' + seq).datepicker('option', 'minDate', date2);
            }
        }).next("button.ui-datepicker-trigger")
          .html("<i class='ti-calendar text-dark f-12 f-w-800 m-r-10'></i>");

        $('.enddatepicker_' + seq).datepicker({
            dateFormat: date_format,
            changeMonth: true,
            changeYear: true,
            showOn: "both",
            buttonText: "Calendar",
            onClose: function () {
                var dt1 = $('.startdatepicker_' + seq).datepicker('getDate');
                var dt2 = $('.enddatepicker_' + seq).datepicker('getDate');
                if (dt2 <= dt1) {
                    var minDate = $('.enddatepicker_' + seq).datepicker('option', 'minDate');
                    $('.enddatepicker_' + seq).datepicker('setDate', minDate);
                }
            }
        }).next("button.ui-datepicker-trigger")
          .html("<i class='ti-calendar text-dark f-12 f-w-800 m-r-10'></i>");
    });
**/
       function setCalendarIcon(el) {
                el.nextAll("button.ui-datepicker-trigger").first()
                  .html("<i class='ti-calendar text-dark f-12 f-w-800 m-r-10'></i>");
            }

            $(".rangedatepicker").each(function () {

                var seq = $(this).attr('data-id');

                // 🔹 START DATE
                var $start = $(".startdatepicker_" + seq);
                var $end = $(".enddatepicker_" + seq);

                $start.datepicker({
                    dateFormat: date_format,
                    changeMonth: true,
                    changeYear: true,
                    showOn: "both",
                    buttonText: "Calendar",

                    onSelect: function () {
                        var date2 = $start.datepicker('getDate');

                        $end.datepicker('setDate', date2);
                        $end.datepicker('option', 'minDate', date2);

                        // ✅ Re-apply icon after refresh
                        setTimeout(function () {
                            setCalendarIcon($end);
                        }, 0);
                    }
                });

                // apply icon to start
                setCalendarIcon($start);


                // 🔹 END DATE
                $end.datepicker({
                    dateFormat: date_format,
                    changeMonth: true,
                    changeYear: true,
                    showOn: "both",
                    buttonText: "Calendar",

                    onClose: function () {
                        var dt1 = $start.datepicker('getDate');
                        var dt2 = $end.datepicker('getDate');

                        if (dt2 <= dt1) {
                            var minDate = $end.datepicker('option', 'minDate');
                            $end.datepicker('setDate', minDate);

                            // ✅ Re-apply icon again if reset happens
                            setTimeout(function () {
                                setCalendarIcon($end);
                            }, 0);
                        }
                    }
                });

                // apply icon to end
                setCalendarIcon($end);

            });


});

(function() {
    'use strict';

    function selectAnswer(clickedId, anu_id, mes_type, like_type) {
        document.getElementById(clickedId).classList.add('selected');
        tosubmitvaluesannoumnetpolis(anu_id, mes_type, like_type);
    }

    function announcementpopup(announcement_id, viewtype) {
        jQuery.ajax({
            url: "views/custom_insert_announcement_popup_new.php",
            data: 'announcement_id=' + announcement_id + '&view=' + viewtype,
            type: "POST",
            success: function(data) {
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal("show");
            },
            error: function() {}
        });
    }

    function saveannouncesummary(announcement_id, fieldname) {
        var totalcomments = parseInt(document.getElementById('totalcomments' + announcement_id).value);
        var totallikes = parseInt(document.getElementById('totallikes' + announcement_id).value);
        var totaldislikes = parseInt(document.getElementById('totaldislikes' + announcement_id).value);
        var userid = document.getElementById('userid').value;
        var content = '';
        var rowid = '';
        var postdata;

        if (fieldname == 'comments') {
            content = document.getElementById('input' + announcement_id).value;
            if (content != '') {
                var updatedata = '&msg_type=1&like_type=0&userid=' + userid + '&announcement_id=' + announcement_id + '&comments=' + content;
                console.log("id =" + rowid);
                rowid = rowid == "" ? null : rowid;
                $.ajax({
                    url: "ajax.php",
                    data: 'page=summaryannouncements&table_name=summary_announcements&id=' + rowid + updatedata,
                    type: "POST",
                    success: function(response) {
                        console.log("response =" + response);
                        document.getElementById('spantotalcomments' + announcement_id).innerHTML = parseInt(document.getElementById('spantotalcomments' + announcement_id).innerHTML) + 1;
                        document.getElementById('input' + announcement_id).value = "";
                    }
                });
            }
        } else {
            var liketype = 0;
            postdata = '&userid=' + userid + '&announcement_id=' + announcement_id + '&msg_type=2';
            $.ajax({
                url: "ajax.php",
                data: 'page=getannouncements&table_name=summary_announcements' + postdata,
                type: "POST",
                success: function(response) {
                    var result = JSON.parse(response);
                    console.log("rsssesponse =" + result);
                    rowid = result.rowid;
                    liketype = result.like_type;
                    if (typeof rowid === 'undefined') {
                        rowid = "";
                    }
                    if (typeof liketype === 'undefined') {
                        liketype = "";
                    }
                    console.log("rowid =" + rowid);
                    console.log("liketype =" + liketype);
                    var updatedata = '';
                    if (fieldname == 'like') {
                        updatedata = '&msg_type=2&like_type=1&userid=' + userid + '&announcement_id=' + announcement_id + '&comments=' + content;
                        console.log("Increase like and decrease dislike" + rowid + liketype);

                        if (rowid == null) {
                            document.getElementById('spantotallikes' + announcement_id).innerHTML = parseInt(document.getElementById('spantotallikes' + announcement_id).innerHTML) + 1;
                        } else if (liketype == '2' && rowid != null) {
                            console.log("Increase like and decrease dislike");
                            document.getElementById('spantotallikes' + announcement_id).innerHTML = parseInt(document.getElementById('spantotallikes' + announcement_id).innerHTML) + 1;
                            document.getElementById('spantotaldislikes' + announcement_id).innerHTML = parseInt(document.getElementById('spantotaldislikes' + announcement_id).innerHTML) - 1;
                            var element = document.getElementById('iconspantotaldislikes' + announcement_id);
                            element.classList.remove("text-primary");
                        }
                        var element = document.getElementById('iconspantotallikes' + announcement_id);
                        element.classList.add("text-primary");
                    }
                    if (fieldname == 'dislike') {
                        updatedata = '&msg_type=2&like_type=2&userid=' + userid + '&announcement_id=' + announcement_id + '&comments=' + content;
                        if (rowid == null) {
                            document.getElementById('spantotaldislikes' + announcement_id).innerHTML = parseInt(document.getElementById('spantotaldislikes' + announcement_id).innerHTML) + 1;
                        } else if (liketype == '1' && rowid != null) {
                            console.log("Increase dislike and decrease like");
                            document.getElementById('spantotaldislikes' + announcement_id).innerHTML = parseInt(document.getElementById('spantotaldislikes' + announcement_id).innerHTML) + 1;
                            document.getElementById('spantotallikes' + announcement_id).innerHTML = parseInt(document.getElementById('spantotallikes' + announcement_id).innerHTML) - 1;
                            var element = document.getElementById('iconspantotallikes' + announcement_id);
                            element.classList.remove("text-primary");
                        }
                        var element = document.getElementById('iconspantotaldislikes' + announcement_id);
                        element.classList.add("text-primary");
                    }
                    console.log(updatedata);
                    $.ajax({
                        url: "ajax.php",
                        data: 'page=summaryannouncements&table_name=summary_announcements&id=' + rowid + updatedata,
                        type: "POST",
                        success: function(response) {
                            console.log("response =" + response);
                        }
                    });
                }
            });
        }
    }

    function dynamicinsertpopup(option) {
        jQuery.ajax({
            url: "views/new_from.php",
            data: 'option=' + option,
            type: "POST",
            success: function(data) {
                $('#dynamic_popup1').html(data);
                $('#kt_modal_dynamic_popup').modal("show");
            },
            error: function() {}
        });
    }

    function tosubmitvaluesannoumnetpolis(anu_id, mes_type, like_type) {
        console.log("anu_id:" + anu_id + "/mes_type:" + mes_type + "/like_type:" + like_type);
        $.ajax({
            url: "ajax.php",
            data: 'page=toupdateanoumentpolsdata&announcement_id=' + anu_id + '&msg_type=' + mes_type + '&like_type=' + like_type,
            type: "POST",
            success: function(response) {
                console.log("response =" + response);
                tostarnotify('top', 'right', 'fa fa-thumbs-up', 'form-label label label-success', 'animated flipInX', 'animated flipOutX', 'Thank you! Your survey response has been recorded successfully', '');
            }
        });
    }

    function tochangerankingdata(bar_id, anu_id, mes_type, like_type) {
        const slider = document.getElementById(bar_id);
        if (!slider) {
            console.error('Slider not found:', bar_id);
            return;
        }
        const v = Number(slider.value);
        const percent = Math.round(((v - slider.min) / (slider.max - slider.min)) * 100);
        slider.style.background = `linear-gradient(90deg, #22c55e ${percent}%, #e5e7eb ${percent}%)`;
        console.log('Selected value:', v);
        $('#' + bar_id + '_spn').html(v);
        tosubmitvaluesannoumnetpolis(anu_id, mes_type, like_type);
    }

    function bindEvents() {
        $(document).on('click', '.announcements-new-btn', function(e) {
            e.preventDefault();
            dynamicinsertpopup('COM_INSERT_COMPANY_ANNOUNCEMNTS');
        });

        $(document).on('click', '.announcements-popup-btn', function(e) {
            e.preventDefault();
            var announcementId = $(this).data('announcement-id');
            var viewType = $(this).data('view-type');
            announcementpopup(announcementId, viewType);
        });

        $(document).on('click', '.announcements-save-summary-btn', function(e) {
            e.preventDefault();
            var announcementId = $(this).data('announcement-id');
            var fieldName = $(this).data('field-name');
            saveannouncesummary(announcementId, fieldName);
        });

        $(document).on('click', '.announcements-select-answer', function(e) {
            e.preventDefault();
            var clickedId = $(this).attr('id');
            var anuId = $(this).data('anu-id');
            var mesType = $(this).data('mes-type');
            var likeType = $(this).data('like-type');
            selectAnswer(clickedId, anuId, mesType, likeType);
        });

        $(document).on('click', '.announcements-polis-btn', function(e) {
            e.preventDefault();
            var anuId = $(this).data('anu-id');
            var mesType = $(this).data('mes-type');
            var likeType = $(this).data('like-type');
            tosubmitvaluesannoumnetpolis(anuId, mesType, likeType);
        });

        $(document).on('change', '.announcements-ranking-slider', function(e) {
            var barId = $(this).attr('id');
            var anuId = $(this).data('anu-id');
            var mesType = $(this).data('mes-type');
            var likeType = $(this).val();
            tochangerankingdata(barId, anuId, mesType, likeType);
        });
    }

    window.selectAnswer = selectAnswer;
    window.announcementpopup = announcementpopup;
    window.saveannouncesummary = saveannouncesummary;
    window.dynamicinsertpopup = dynamicinsertpopup;
    window.tosubmitvaluesannoumnetpolis = tosubmitvaluesannoumnetpolis;
    window.tochangerankingdata = tochangerankingdata;

    $(document).ready(function() {
        bindEvents();
    });

})();

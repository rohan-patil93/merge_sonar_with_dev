"use strict";
$(document).ready(function() {
 // New Lead 
    $('.leadsidebar').on('click', function() {
        var my_val = $('.pcoded').attr('vertical-placement');
        if (my_val == 'right') {
            var options = {
                direction: 'left'
            };
        } else {
            var options = {
                direction: 'right'
            };
        }
        $('.leadsidebar-form').toggle('slide', options, 500);
    });
        // New Lead close
    // $('.back_leadsidebar-form').on('click', function() {
    //     var my_val = $('.pcoded').attr('vertical-placement');
    //     if (my_val == 'right') {
    //         var options = {
    //             direction: 'left'
    //         };
    //     } else {
    //         var options = {
    //             direction: 'right'
    //         };
    //     }
    //     $('.leadsidebar-form').toggle('slide', options, 500);
    //     $('.leadsidebar-form').css('display', 'block');
    // });
    // $('.back_friendlist').on('click', function() {
    //     var my_val = $('.pcoded').attr('vertical-placement');
    //     if (my_val == 'right') {
    //         var options = {
    //             direction: 'left'
    //         };
    //     } else {
    //         var options = {
    //             direction: 'right'
    //         };
    //     }
    //     $('.showChat').toggle('slide', options, 500);
    //     $('.showChat').css('display', 'block');
    // });
});
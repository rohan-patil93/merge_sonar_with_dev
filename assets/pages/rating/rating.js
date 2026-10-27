"use strict";
$(document).ready(function() {
    function ratingEnable() {
        $('#example-1to10').barrating('show', {
            theme: 'bars-1to10',
        });

        $('#example-movie').barrating('show', {
            theme: 'bars-movie'
        });

        $('#example-movie').barrating('set', 'Mediocre');

        $('#example-square').barrating('show', {
            theme: 'bars-square',
            showValues: true,
            showSelectedRating: false
        });

        $('#example-pill').barrating('show', {
            theme: 'bars-pill',
            initialRating: 'D',
            showValues: true,
            showSelectedRating: false,
            allowEmpty: true,
            emptyValue: '-- no rating selected --',
            onSelect: function(value, text) {
                //alert('Selected rating: ' + value);
            }
        });

        // $('#example-reversed').barrating('show', {
        //     theme: 'bars-reversed',
        //     showSelectedRating: true,
        //     reverse: true
        // });
        



var currentRating = $('#example-fontawesome-o').data('current-rating');

        $('.stars-example-fontawesome-o .current-rating')
            .find('span')
            .html(currentRating);

        $('.stars-example-fontawesome-o .clear-rating').on('click', function(event) {
            event.preventDefault();

            $('#example-fontawesome-o')
                .barrating('clear');
        });

        $('#example-fontawesome-o').barrating({
            theme: 'fontawesome-stars-o',
            showSelectedRating: false,
            initialRating: currentRating,
            onSelect: function(value, text) {
                if (!value) {
                    $('#example-fontawesome-o')
                        .barrating('clear');
                } else {
                    $('.stars-example-fontawesome-o .current-rating')
                        .addClass('hidden');

                    $('.stars-example-fontawesome-o .your-rating')
                        .removeClass('hidden')
                        .find('span')
                        .html(value);
                }
            },
            onClear: function(value, text) {
                $('.stars-example-fontawesome-o')
                    .find('.current-rating')
                    .removeClass('hidden')
                    .end()
                    .find('.your-rating')
                    .addClass('hidden');
            }
        });

        
        $('#example-reversed').barrating('show', {
            theme: 'bars-reversed',
            showSelectedRating: true,
            reverse: true
        });


        $('#example-horizontal').barrating('show', {
            theme: 'bars-horizontal',
            reverse: true,
            hoverState: false
        });

        $('#example-fontawesome').barrating({
            theme: 'fontawesome-stars',
            showSelectedRating: false
        });

        $('.rating-star').barrating({
            theme: 'css-stars',
            showSelectedRating: false
        });

        $('#example-bootstrap').barrating({
            theme: 'bootstrap-stars',
            showSelectedRating: false
        });


//alert($('.rating_star').length);
     $('.rating_star').each(function(index) {  
        var id = $(this).data("id");
        var currentRating = $('.example-fontawesome-o'+id).data('current-rating');
        $("#star_value_input").val(currentRating);
        // console.log(currentRating+' - '+id);
        rattingchanges(currentRating,id);
       
    });
      
}


    function ratingDisable() {
        $('select').barrating('destroy');
    }

    $('.rating-enable').on('click',function(event) {
        event.preventDefault();
        ratingEnable();
        $(this).addClass('deactivated');
        $('.rating-disable').removeClass('deactivated');
    });

    $('.rating-disable').on('click',function(event) {
        event.preventDefault();

        ratingDisable();

        $(this).addClass('deactivated');
        $('.rating-enable').removeClass('deactivated');
    });

    ratingEnable();
});



 function rattingchanges(vals,id){
        console.log(vals+' - '+id);
         var id = id;
         $('.example-fontawesome-o'+id).barrating({
            theme: 'fontawesome-stars-o',
            showSelectedRating: false,
            initialRating: vals,
            onSelect: function(value, text){
                $("#star_value_input"+id).val(value);
            },
          });
    }



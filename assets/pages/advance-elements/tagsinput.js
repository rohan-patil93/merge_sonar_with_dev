"use strict";
$(document).ready(function() {
	// Color tags
	var cities = new Bloodhound({
		datumTokenizer: Bloodhound.tokenizers.obj.whitespace('text'),
		queryTokenizer: Bloodhound.tokenizers.whitespace,
		prefetch: 'assets/pages/advance-elements/cities.json'
	});
	cities.initialize();

	var elt = $('.color-tags > input');
	elt.tagsinput({
		tagClass: function(item) {
			switch (item.continent) {
				case 'Europe'   : return 'label label-primary';
				case 'America'  : return 'label label-danger';
				case 'Australia': return 'label label-success';
				case 'Africa'   : return 'label label-default';
				case 'Asia'     : return 'label label-warning';
			}
		},
		itemValue: 'value',
		itemText: 'text',

		typeaheadjs: [
		{
			hint: true,
			highlight: true,
			minLength: 2
		},
		{
			name: 'cities',
			displayKey: 'text',
			source: cities.ttAdapter()
		}
		]
	});
	elt.tagsinput('add', { "value": 1 , "text": "Amsterdam"   , "continent": "Europe"    });
	elt.tagsinput('add', { "value": 4 , "text": "Washington"  , "continent": "America"   });
	elt.tagsinput('add', { "value": 7 , "text": "Sydney"      , "continent": "Australia" });
	elt.tagsinput('add', { "value": 10, "text": "Beijing"     , "continent": "Asia"      });
	elt.tagsinput('add', { "value": 13, "text": "Cairo"       , "continent": "Africa"    });

// Maximum tags option
$('.tags_max').tagsinput({
        maxTags: 3
    });

// Maximum charcters option
$('.tags_max_char').tagsinput({
        maxChars: 8
    });

// Multiple tags option
$(".tags_add_multiple").tagsinput('items');
// Tags plugins ends

// Max-length js start

// Default max-length
    $('input[maxlength]').maxlength();

    // Thresold value
    $('input.thresold-i').maxlength({
        threshold: 20
    });

    //Color class
    $('input.color-class').maxlength({
        alwaysShow: true,
        threshold: 10,
        warningClass: "label label-success",
        limitReachedClass: "label label-danger"
    });

    //Position class
    $('input.position-class').maxlength({
        alwaysShow: true,
        placement: 'top-left'
    });

    // Textareas max-length
    $('textarea.max-textarea').maxlength({
        alwaysShow: true
    });
// Max-length js ends
 });

/*  button box    */
function deleterowinputtype(id){
	$(".deleterow"+id).remove();
	$("#divid"+id).remove();
}
function deletebutton(id){
	$(".deletebutton"+id).remove();
	$("#buttondiv"+id).remove();
}
//var maxid = 1; 
var maxid= $("#max").val();
$('body').on('click', '.addnewbutton', function() {
	maxid++;
	$.ajax({
		type: 'POST',
		url: 'views/custom_attributes_fileds_ajax.php',
		data: "id="+maxid+"&page=elementordesignbutton",
		success:function(data){	
			var result = JSON.parse(data);
			$(".new_buttons").append(result['setting']);
			$(".buttonlist").append(result['textfileds']);
			$(".js-example-basic-single").select2();
			$(".js-example-disabled-results").select2();
			
		}
	});
});
$('body').on('change', '.button_size', function() {
	var id = $(this).attr("data-id");
	var size = $(".button_sizes"+id).val();
	$("#buttonid"+id).attr('class', '');

	$("#buttonid"+id).addClass("btn btn-primary textcapital "+size+" mr-2");
	
});
$('body').on('change', '.buttonwidth', function() {
	var id = $(this).attr("data-id");
 	//var size = $(".button_sizes"+id).val();
 	var width = $(".buttonwidth"+id).val();
 	$("#buttondiv"+id).attr('class', '');
 	$("#buttondiv"+id).addClass("col-lg-"+width);

 });

$('body').on('keyup', '.buttonname', function() {
	var id = $(this).attr("data-id");
	var value = $(this).val();
	var type = $('.function_type'+id).val();
	var button = "";
	if(type == "saveandcontinue"){
		button = '<i class="la la-check-circle"></i>';
	}else if(type == "update"){
		button = '<i class="flaticon2-pen mr-5"></i>';
	}else if(type == "print"){	
		button = '<i class="icon-2x flaticon-technology"></i>';
	}else{
		button = '<i class="la la-check"></i>';
	}

	$("#buttonid"+id).html(button+"  "+value);
});
$('body').on('keyup', '.buttonid', function() {
	var id = $(this).attr("data-id");
	var value = $(this).val();

	$("#buttonid"+id).attr("id", value);
});		

$('body').on('change', '.function_type', function() {
	var id = $(this).attr("data-id");
	var value = $(this).val();

	if(value == "saveandcontinue"){
		$(".buttonname"+id).val('Save &amp; continue');
		$("#buttonid"+id).attr("value", "saveandcontineu");
		$(".button_class"+id).attr("value", "saveandcontineu");
		$(".title"+id).html('<i class="flaticon2-cancel-music" onclick="deletebutton('+id+')"></i>Save continue');
		$("#buttonid"+id).html('<i class="la la-check-circle" ></i>Save &amp; continue');
	}else if(value == "update"){
		$(".buttonname"+id).val('Update');
		$("#buttonid"+id).attr("value", "update");
		$(".button_class"+id).attr("value", "update");
		$(".title"+id).html('<i class="flaticon2-cancel-music" onclick="deletebutton('+id+')"></i>Update');
		$("#buttonid"+id).html('<i class="flaticon2-pen mr-5" ></i>Update');
	}else if(value == "print"){
		$(".buttonname"+id).val('Print');
		$("#buttonid"+id).attr("value", "print");
		$(".button_class"+id).attr("value", "print");
		$(".title"+id).html('<i class="flaticon2-cancel-music" onclick="deletebutton('+id+')"></i>Print');
		$("#buttonid"+id).html('<i class="icon-2x flaticon-technology" ></i>Print');
	}else{
		$(".buttonname"+id).val('save');
		$(".buttonid"+id).attr("value", "save");
		$("#button_class"+id).attr("value", "save");
		$(".title"+id).html('<i class="flaticon2-cancel-music" onclick="deletebutton('+id+')"></i>Save');
		$("#buttonid"+id).html('<i class="la la-check" ></i>Save');
	}
});
$('body').on('click', '.btn-outline-secondary', function() {
	var value = $(this).val();
	var id = $(this).attr("data-id");
	var width = $(".buttonwidth"+id).val();
	$("#buttondiv"+id).attr('class', '');
	$("#buttondiv"+id).addClass("col-lg-"+width+"  "+value);
	$("#buttonposition"+id).val(value);
});
/* button  text box    */
/*$('body').on('change', '.fileuplodingdisplay', function() {
	var label = $(".fileuplodinglablel").val();

	if($(this).is(":checked")){
		alert("check");
		$.ajax({
			type: 'POST',
			url: 'views/custom_attributes_fileds_ajax.php',
			data: "page=fileuploadingstatusdisplay",
			success:function(data){	
				$(".fileuploadUig").append(data);	
				$(".fileuplodinglablelui").html(label);
			}
		});
	}else{
		alert("un-check");
		$(".fileuploadui").remove();
		$(".fileuploaddeleteui").remove();
	}
});
*/
$('body').on('change', '.fileuplodingdeleteddisplay', function() {
	if($(this).is(":checked")){
		$(".fileuploaddeleteui").show();
	}else{
		$(".fileuploaddeleteui").hide();
	}
});
$('body').on('change', '.fileuplodingwidth', function() {
	var value = $(this).val();
	$("#fileuploaduiid").attr('class', '');
	$("#fileuploaduiid").addClass("col-lg-"+value+" fileuploadui");
});
$('body').on('change', '.fileuplodingdleetewidth', function() {
	var value = $(this).val();
	$("#fileuploaddeleteuiid").attr('class', '');
	$("#fileuploaddeleteuiid").addClass("col-lg-"+value+" fileuploaddeleteui");
});
$('body').on('keyup', '.fileuplodinglablel', function() {
	var lablevalue = $(this).val();
	$(".fileuplodinglablelui").html(lablevalue);
});
/*  text box    */
var increment = $("#incrimenttxetbox").val()
$('body').on('click', '.addnewfillds', function() {
	var count = ($(".card_count").length+1)*10;
	maxid++;
	$.ajax({
		type: 'POST',
		url: 'views/custom_attributes_fileds_ajax.php',
		data: "id="+maxid+"&count="+count+"&page=elementordesign&increment="+increment,
		success:function(data){	
			console.log(data);
			var result = JSON.parse(data);
			$(".new_fields").append(result['setting']);
			$(".body_fields").append(result['textfileds']);
			$(".js-example-basic-single").select2();
			$(".js-example-disabled-results").select2();
			//alert('.js-switch_append'+increment-1);
			var numb = increment-1;
			//alert('.js-switch_append'+numb);
		
			if($('.js-switch_append'+numb).length  > 0){
				var elem = Array.prototype.slice.call(document.querySelectorAll('.js-switch_append'+numb));
				elem.forEach(function(html) {
					var switchery = new Switchery(html, { color: '#4099ff', jackColor: '#fff',size: 'medium' });
				});
			}
		}
	});
	increment++;
});
$('body').on('change', '.elementor_text_box_type', function() {
	var id = $(this).attr("data-id");
	var elementory_id = $(this).attr("data-elementory_id");
	var type= $(this).val();
	var key = $(this).attr("data-iddd");
	$.ajax({
		type: 'POST',
		url: 'views/custom_attributes_fileds_ajax.php',
		data: "type="+type+"&page=elementortype&id="+id,
		success:function(data){	
			$(".inputtypediv"+id).html(data);
			$('.autonumber').autoNumeric('init');
			
			if(type=='switch'){
				console.log('js-switch-s'+id);
				var elem = Array.prototype.slice.call(document.querySelectorAll('.js-switch-s'+id));
				elem.forEach(function(html) {
					var switchery = new Switchery(html, { color: '#4099ff', jackColor: '#fff',size: 'medium' });
				});
			}
			if(type=='rating'){
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
			}
		}
	});
//popup
var res = key.split("#");
var table = 	res[0];
var field = 	res[1];
if(type == 'select' || type == 'radio' || type == 'switch' || type == 'checkbox' || type == 'multi_select' || type =='disable_select' || type == 'auto_select' || type == 'rating'){
	jQuery.ajax({
		url: "views/custom_attributes_popup.php",
		data:'page=dis_att_type&table='+table+'&field='+field+'&type='+type,
		type: "POST",
		success:function(data){
			$('#popup1').html(data);
			$('#kt_modal_KTDatatable_remote').modal("show");
			
		},
		error:function (){}
	});
	var res = values.split("#");
	$('#table').val(res[0]);
	$('#field').val(res[1]); 
}
//poup coding

});
$('body').on('keyup', '.elementory_lable', function() {
	var elementory_id = $(this).attr("data-labletext");
	var id = $(this).attr("data-id");
	var lablevalue = $(this).val();
	$("."+elementory_id).html(lablevalue);
	$(".textboxclass"+id).html('<i class="flaticon2-cancel-music" onclick="deleterowinputtype('+id+')"></i>'+lablevalue);
});
$('body').on('keyup', '.valuesdefoult', function() {
	var id = $(this).attr("data-id");
	var values = $(this).val();
	$(".inputbox"+id).val(values);
});
$('body').on('keyup', '.elementory_placeholder', function() {
	var elementory_id = $(this).attr("data-elementory_id");
	var lablevalue = $(this).val();
	$("."+elementory_id).attr("placeholder", lablevalue);
});
$('body').on('change', '.elementory_required', function() {
	var elementory_id = $(this).attr("data-elementory_id");
	var label = $(this).attr("data-requerd");
	if($(this).is(":checked")){
		//$("."+elementory_id).prop('required',$(this).is(":checked"));
		$("."+label).show();
	}else{
		$("."+label).hide();
		//$("."+elementory_id).prop('required',$(this).is(":checked"));
	}
});
$('body').on('change', '.elementory_width', function() {
	var elementory_id = $(this).attr("data-elementory_id");
	var width = $(this).val();
	var id = $(this).attr("data-id");
	$("#divid"+id).attr('class', '');
	$("#divid"+id).addClass("divbox"+id+"  col-lg-"+width+" mt-5");
});
$('body').on('keyup', '.elementory_id', function() {
	var elementory_id = $(this).attr("data-elementory_id");
	var idvalue = $(this).val();
	$("."+elementory_id).attr("id", idvalue);
});
$('body').on('change', '.elementory_labelandinputtype', function() {
	var inputtype = $(this).attr("data-elementory_id");
	var label = $(this).attr("data-label");
	var div = $(this).attr("data-div");	
	var id = $(this).attr("data-id");	
	if($(this).is(":checked")){
		$("."+div).addClass("row");
		$(".inputtypediv"+id).addClass("col-lg-9 col-xl-9");
		$("."+label).addClass("col-lg-3 col-xl-3");
	}else{
		$("."+div).removeClass("row");
		$(".inputtypediv"+id).removeClass("col-lg-9 col-xl-9");
		$("."+label).removeClass("col-lg-3 col-xl-3");
	}	
});
$('body').on('keyup', '.elementor_fieldname', function() {
	var id = $(this).attr("data-id");
	var value = $(this).val();
 	//$(".textboxclass"+id).html('<i class="flaticon2-cancel-music"></i>'+value);
 });

$('body').on('keyup', '.formname', function() {
	var value = $(this).val();
	$(".formnamehedimg").text(value);
});





/*  text box    */
/**function Getinserttype(values,type){
	var res = values.split("#");
	var table = 	res[0];
	var field = 	res[1];
		//alert(values +"--"+ type);
		if(type == 'select' || type == 'radio' || type == 'switch' || type == 'checkbox' || type == 'multi_select' || type =='disable_select'){
			jQuery.ajax({
				url: "views/custom_attributes_popup.php",
				data:'page=dis_att_type&table='+table+'&field='+field,
				type: "POST",
				success:function(data){
					$('#popup1').html(data);
					$('#kt_modal_KTDatatable_remote').modal({ show: true});
				},
				error:function (){}
			});
			var res = values.split("#");
			$('#table').val(res[0]);
			$('#field').val(res[1]);
			
		}
	} **/
	$('body').on('keypress', '.space', function(e) {
		if(e.which === 32) 
			return false;
	});
	function toSubmit(){	
		var errer = 0;
		var counts = ($(".elementor_fieldname").length);
		if(counts ==0){
			alert("minimum one field requerd")
			errer++;
		}
		var tablename = $(".tablname").val();
		if(tablename == ""){
			errer++;
			alert("Enter table name");
		}
		var formname = $(".formname").val();
		if(formname == ""){
			errer++;
			alert("Enter Form name");
		}
		$(".elementor_fieldname").each(function(){
			if($(this).val() ==""){
				alert("Enter Field name");
				errer++;

			}
		}); 
		if(errer!=0){
			return false;
		}
	}

	$('body').on('change', '.display_status', function() {
		var id = $(this).attr("data-id");
		if($(this).is(":checked")){
			$("#divid"+id).show();
		}else{
			$("#divid"+id).hide();
		}

	});
	$('body').on('change', '.tablname_new', function() {
		var value = $(this).val();
		var fileds = "id";
		var where = "application_table_name='"+value+"'";
		if(value=="-1" || value==""){
			$(".tablname").show();
			$(".tablname").attr("value", "");
		}else{
			$(".tablname").hide();
			$(".tablname").attr("value", value);
			window.location.href = 'index.php?option=COM_INSERT_DISPLAY_ATTRIBUTES&table='+value;


		}
	});

var num = 150; //number of pixels before modifying styles

$(window).bind('scroll', function () {
    //console.log($(window).scrollTop());
    if ($(window).scrollTop() > num) {
        $('.nnnnnsss').addClass('fixed');
    } else {
        $('.nnnnnsss').removeClass('fixed');
    }
});
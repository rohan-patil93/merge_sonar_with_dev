/**
// Trigger action when the contexmenu is about to be shown
$(document).bind("contextmenu", function (event) {
    // Avoid the real one
    event.preventDefault();
    // Show contextmenu
    $(".custom-menu").finish().toggle(100). 
    // In the right position (the mouse)
    css({
        top: event.pageY + "px",
        left: event.pageX + "px"
    });
});
// If the document is clicked somewhere
$(document).bind("mousedown", function (e) { 
    // If the clicked element is not the menu
    if (!$(e.target).parents(".custom-menu").length > 0) {
        // Hide it
        $(".custom-menu").hide(100);
    }
});
// If the menu element is clicked
$(".custom-menu li").click(function(){
    // This is the triggered action name
    switch($(this).attr("data-action")) {
        // A case for each action. Your actions here
        case "first": alert("first"); break;
        case "second": alert("second"); break;
        case "third": alert("third"); break;
    }
    // Hide it AFTER the action was triggered
    $(".custom-menu").hide(100);
  });



function addTextAtCaret(textAreaId, text) {
      var textArea = document.getElementById(textAreaId);
      var cursorPosition = textArea.selectionStart;
      addTextAtCursorPosition(textArea, cursorPosition, text);
      updateCursorPosition(cursorPosition, text, textArea);
  }
  function addTextAtCursorPosition(textArea, cursorPosition, text) {
      var front = (textArea.value).substring(0, cursorPosition);
      var back = (textArea.value).substring(cursorPosition, textArea.value.length);
      textArea.value = front + text + back;
  }
  function updateCursorPosition(cursorPosition, text, textArea) {
      cursorPosition = cursorPosition + text.length;
      textArea.selectionStart = cursorPosition;
      textArea.selectionEnd = cursorPosition;
      textArea.focus();    
  }

**/

/**
// Trigger action when the contexmenu is about to be shown
$(document).bind("contextmenu", function (event) {
    // Avoid the real one
    event.preventDefault();
    // Show contextmenu
    $(".custom-menu").finish().toggle(100). 
    // In the right position (the mouse)
    css({
        top: event.pageY + "px",
        left: event.pageX + "px"
    });
});
// If the document is clicked somewhere
$(document).bind("mousedown", function (e) { 
    // If the clicked element is not the menu
    if (!$(e.target).parents(".custom-menu").length > 0) {
        // Hide it
        $(".custom-menu").hide(100);
    }
});
// If the menu element is clicked
$(".custom-menu li").click(function(){
    // This is the triggered action name
    switch($(this).attr("data-action")) {
        // A case for each action. Your actions here
        case "first": alert("first"); break;
        case "second": alert("second"); break;
        case "third": alert("third"); break;
    }
    // Hide it AFTER the action was triggered
    $(".custom-menu").hide(100);
  });



function addTextAtCaret(textAreaId, text) {
      var textArea = document.getElementById(textAreaId);
      var cursorPosition = textArea.selectionStart;
      addTextAtCursorPosition(textArea, cursorPosition, text);
      updateCursorPosition(cursorPosition, text, textArea);
  }
  function addTextAtCursorPosition(textArea, cursorPosition, text) {
      var front = (textArea.value).substring(0, cursorPosition);
      var back = (textArea.value).substring(cursorPosition, textArea.value.length);
      textArea.value = front + text + back;
  }
  function updateCursorPosition(cursorPosition, text, textArea) {
      cursorPosition = cursorPosition + text.length;
      textArea.selectionStart = cursorPosition;
      textArea.selectionEnd = cursorPosition;
      textArea.focus();    
  }

**/

window.addEventListener("load", function() {
   
$(document).on("click", ".submitDynamicWorkflowPopUp", function() {
    var table_name = $(this).data("table_name");
    var transaction_id = $(this).data("transaction_id");
    var current_status = $(this).data("current_status");
    var next_status = $(this).data("next_status");
    submitDynamicWorkflowPopUp(table_name,transaction_id,current_status,next_status);
  });
  $(document).on("click", ".submitDynamicWorkflow", function() {
    var table_name = $(this).data("table_name");
    var transaction_id = $(this).data("transaction_id");
    var current_status = $(this).data("current_status");
    var next_status = $(this).data("next_status");
    var old_status_name = $(this).data("old_status_name");
    var new_status_name = $(this).data("new_status_name");
    submitDynamicWorkflow(table_name,transaction_id,current_status,next_status,old_status_name,new_status_name);
  });




});


function scrollVisitors(scrollAmount,divid) {
    document.getElementById(divid).scrollBy({
        left: scrollAmount,
        behavior: "smooth"
    });
}

$(document).on("change", "#display_logo", function (event) {
    var image = document.getElementById("outputdisplay_logo");
    image.src = URL.createObjectURL(event.target.files[0]);
});




$('body').on('keyup', '#filter1111', function() {
  console.log("work");
  var filter = $(this).val(),
  count = 0;
  $('#results div').each(function() {
       if ($(this).text().search(new RegExp(filter, "i")) < 0) {
          $(this).hide();
        } else {
          $(this).show();
          count++;
        }
    });
});
$('body').on('keyup', '#dynamicfilterhideshow', function() {
  var filterdivid = $(this).attr("data-id"); 
  var filterdivtype = $(this).attr("data-type") !=""?$(this).attr("data-type"):'tr';
  var filter = $(this).val(),
  count = 0;
  console.log(filterdivid);
  if(filterdivtype == "tr"){
    $('#'+filterdivid+' tr').each(function() {
       if ($(this).text().search(new RegExp(filter, "i")) < 0) {
          $(this).hide();
        } else {
          $(this).show();
          count++;
        }
    });
  }else{
    $(filterdivtype+' #'+filterdivid).each(function() {
       if ($(this).text().search(new RegExp(filter, "i")) < 0) {
          $(this).hide();
        } else {
          $(this).show();
          count++;
        }
    });
  }
});
var loadFile = function (event,id) {
  var image = document.getElementById("output"+id);
  image.src = URL.createObjectURL(event.target.files[0]);
};
$('body').on('keyup', '.timesheet_time', function(e){
  var element = this;
  var key = e.keyCode || e.which;
  var rorcol = $(this).attr("data-rowcol");    
  setTimeout(function(){ insertTimingColorkeyup(element,key,rorcol); }, 1000);    
});
$('body').on('blur', '.timesheet_time', function(e){
  var element = this;
  var key = e.keyCode || e.which; 
  var rorcol = $(this).attr("data-rowcol");  
  insertTimingColor(element,key,rorcol)
});
function insertTimingColorkeyup(element,key,rorcol){
  var rorcol = rorcol.split("-");
  var rownum = rorcol[0];
  var colunmnum = rorcol[1];
     // var billablehrs = $('#task_r'+rownum+'_billable_c'+colunmnum).val();
     // var nonbillablehrs = $('#task_r'+rownum+'_nonbillable_c'+colunmnum).val();
     var billablehrs = $("#task_r"+colunmnum+"_total_billable").val();
     var nonbillablehrs = $("#task_r"+colunmnum+"_total_nonbillable").val();
     var totalhrs = Number(billablehrs)+Number(nonbillablehrs);
     console.log("TOTAL KEYUP-"+Number(totalhrs));
     var error_message = $("#error_message").val();
     var minworkinghours = $("#minworkinghours").val();
     var allowovertime = $("#allowovertime").val();
     var maxhrsallow = Number(allowovertime)=='1'?minworkinghours:24;
      //console.log(maxhrsallow);
      var inputValue = element.value;
      if(element.value.trim().length >= 3){
       if(!inputValue.includes('.') || inputValue.split('.').length > 2){ 
        element.value = '00.00';
        Swal.fire({
         icon: 'error',
         title: 'Oops...',
         text: error_message
       })
        setTimeout(function(){ calucationrecheck();  }, 1000); 
      }
    }
    if(Number(totalhrs) > Number(maxhrsallow)){
     element.value = '00.00'; 
     Swal.fire({
      icon: 'error',
      title: 'Oops...',
      text: error_message
    })
     setTimeout(function(){ calucationrecheck();  }, 1000); 
   }
   if(element.value.trim().length == 2 && key !== 8){
     element.value = element.value + '.00';
   }
 }
 function insertTimingColor(element,key,rorcol){
  var rorcol = rorcol.split("-");
  var rownum = rorcol[0];
  var colunmnum = rorcol[1];
     // var billablehrs = $('#task_r'+rownum+'_billable_c'+colunmnum).val();
     // var nonbillablehrs = $('#task_r'+rownum+'_nonbillable_c'+colunmnum).val();
     var billablehrs = $("#task_r"+colunmnum+"_total_billable").val();
     var nonbillablehrs = $("#task_r"+colunmnum+"_total_nonbillable").val();
     var totalhrs = Number(billablehrs)+Number(nonbillablehrs);
     console.log("key insert- "+Number(totalhrs));
     var error_message = $("#error_message").val();
     var minworkinghours = $("#minworkinghours").val();
     var allowovertime = $("#allowovertime").val();
     var maxhrsallow = Number(allowovertime)=='1'?minworkinghours:24;
     var inputValue = element.value;
     if(element.value.trim().length >= 3){
       if(!inputValue.includes('.') || inputValue.split('.').length > 2){ 
        Swal.fire({
         icon: 'error',
         title: 'Oops...',
         text: error_message
       })
        element.value = '00.00';

      }
    }
    if(Number(totalhrs) > Number(maxhrsallow)){
     Swal.fire({
      icon: 'error',
      title: 'Oops...',
      text: error_message
    })
     element.value = '00.00'; 
   }
   if(element.value.trim().length == 1){
     element.value = '0'+element.value + '.00';
   }
   if(element.value.trim().length == 2 || element.value.trim().length == 3 && key !== 8){
     element.value = element.value + '.00';
   }
   if(element.value == ""){
     element.value = '00.00';
   }
   var twovalue = element.value.split('.');
   var fnumb =  (twovalue[0].length == 1)?'0'+twovalue[0]:twovalue[0];
   var lnumb = (twovalue[1].length == 1)?'0'+twovalue[1]:twovalue[1];
   element.value = fnumb+"."+lnumb;
   if(element.value.trim().length != 5){
     element.value = '00.00';
   }
 }
 function proceduressteps(){
  jQuery.ajax({
    url: "ajax.php",
    data:'page=setdynamichelptext&table_name='+table_name,
    type: "POST",
    success:function(data){
     var data11  = JSON.parse(data);
     console.log(data11);
     $.each(data11, function(key,value) {
			   console.log($("[name='"+key+"']").attr('type'));    //It will shows the Id values
         if(value.type == 'checkbox' || value.type == 'switch' || value.type == 'select' || value.type =='auto_select' || value.type == 'multi_select'){
           if($("[name='"+key+"']").attr('type') == 'hidden'){
            $("[name='"+key+"']:nth-child(2)").next("span").attr('data-step',value.sequence);
            $("[name='"+key+"']:nth-child(2)").next("span").attr('data-intro',value.helptext);
          }else{
            $("[name='"+key+"']:first").next("span").attr('data-step',value.sequence);
            $("[name='"+key+"']:first").next("span").attr('data-intro',value.helptext);
          }
        }else if(value.type =='radio' || value.type == 'editor'){
          $("[name='"+key+"']:first").parent().attr('data-step',value.sequence);
          $("[name='"+key+"']:first").parent().attr('data-intro',value.helptext);
        }else{
         if($("[name='"+key+"']").attr('type') == 'hidden'){
          $("[name='"+key+"']:nth-child(2)").attr('data-step',value.sequence);
          $("[name='"+key+"']:nth-child(2)").attr('data-intro',value.helptext);
        }else{
          $("[name='"+key+"']:first").attr('data-step',value.sequence);
          $("[name='"+key+"']:first").attr('data-intro',value.helptext);        
        }
      }  
    }); 
     introJs().start(); 
   }
 });   
}

// $(document).ready(function() {
//   $('.selector > .selection').click(function(e) {
//     $(this).siblings().removeClass('selected');
//     $(this).addClass('selected'); 
//   });
// });

function dynamicErrorShow(respon){
  if(respon.status =="fail"){
    Swal.fire({
      icon: 'error',
      title: 'Oops...',
      text: respon.message
    })
    var retarray = {result:"1", LASTID:respon.message};
    return retarray;
  }else{
    var retarray = {result:"0", LASTID:respon.LASTID};
    return retarray;
  }
}
$('body').on('keyup', '.inputtpehead', function() {
 var rowcol = $(this).attr("data-id");
 $('.head_text_'+rowcol).text($(this).val());
 console.log(rowcol);
})
$('body').on('keyup', '.header_name_text', function() {
 var rowcol = $(this).attr("data-id");
 $('#header_name_'+rowcol).text($(this).val());
})
$('body').on('keyup', '.header_comment_text', function() {
 var rowcol = $(this).attr("data-id");
 $('#header_comment_'+rowcol).text($(this).val());
})
/**/
if(document.getElementsByClassName('image')){
  const rangeInput = document.querySelectorAll(".range-input input"),priceInput = document.querySelectorAll(".price-input input"),range = document.querySelector(".slider .progress");
  let priceGap = 1000;
  priceInput.forEach((input) => {
    input.addEventListener("input", (e) => {
      let minPrice = parseInt(priceInput[0].value),
        maxPrice = parseInt(priceInput[1].value);
      if (maxPrice - minPrice >= priceGap && maxPrice <= rangeInput[1].max) {
        if (e.target.className === "input-min") {
          rangeInput[0].value = minPrice;
          range.style.left = (minPrice / rangeInput[0].max) * 100 + "%";
        } else {
          rangeInput[1].value = maxPrice;
          range.style.right = 100 - (maxPrice / rangeInput[1].max) * 100 + "%";
        }
      }
    });
  });
  rangeInput.forEach((input) => {
    input.addEventListener("input", (e) => {
      let minVal = parseInt(rangeInput[0].value),
        maxVal = parseInt(rangeInput[1].value);
      if (maxVal - minVal < priceGap) {
        if (e.target.className === "range-min") {
          rangeInput[0].value = maxVal - priceGap;
        } else {
          rangeInput[1].value = minVal + priceGap;
        }
      } else {
        priceInput[0].value = minVal;
        priceInput[1].value = maxVal;
        range.style.left = (minVal / rangeInput[0].max) * 100 + "%";
        range.style.right = 100 - (maxVal / rangeInput[1].max) * 100 + "%";
      }
    });
  });
}
/**/

function insertadittrails(parent_option,child_option,extra_paramiter){
    jQuery.ajax({
      url: "ajax.php",
      data:'page=insertadittrails&parent_option='+parent_option+'&child_option='+child_option+"&extra_paramiter="+extra_paramiter,
      type: "POST",
      success:function(respons){
        console.log(respons);
      }
    });
  }
  $(".breadcrumbs_active").hover(
      function() {
          $(this).addClass('active');
      }, function() {
          $( this ).removeClass('active');
      }
  );
//   function passDateAndDays(date, days) {
//     console.log(date);
//   var newDate = new Date(date); // Create a new Date object from the provided date
//   console.log(newDate);
//  var ddc =  newDate.setDate(newDate.getDate() + days); // Add the specified number of days
//  console.log(ddc);
//   return newDate.toISOString().split('T')[0]; // Convert the new date to a string in "YYYY-MM-DD" format
// }
/**
function passDateAndDays(date, days) {
  console.log(convertDateFormat(date))
  var newDate = new Date(date);
  newDate.setDate(newDate.getDate() + days);
  console.log(getDateWithFormat(newDate));
  return getDateWithFormat(newDate);// Convert the new date to a string in "YYYY-MM-DD" format
}
function getDateWithFormat(date) {
  var year = date.getFullYear();
  var month = ('0' + (date.getMonth() + 1)).slice(-2);
  var day = ('0' + date.getDate()).slice(-2);
  return date_format
    .replace('yy', year)
    .replace('mm', month)
    .replace('dd', day);
}


function convertDateFormat(dateString, currentFormat, targetFormat) {
  var date = new Date(dateString);
  
  // Obtain the parts of the date based on the current format
  var year = date.getFullYear();
  var month = date.getMonth() + 1; // Note: Month starts from 0, so we add 1
  var day = date.getDate();
  
  // Create a new date string in the target format
  var newDateString = targetFormat
    .replace('YYYY', year)
    .replace('MM', ('0' + month).slice(-2)) // Zero-padding month
    .replace('DD', ('0' + day).slice(-2)); // Zero-padding day
  
  return newDateString;
}
*/
function passDateAndDays(date, days) {
  var newDate = new Date(date);
  console.log(newDate);
  newDate.setDate(newDate.getDate() + days);
  return getDateWithFormat(newDate);// Convert the new date to a string in "YYYY-MM-DD" format
}
function getDateWithFormat(date) {
  var year = date.getFullYear();
  var month = ('0' + (date.getMonth() + 1)).slice(-2);
  var day = ('0' + date.getDate()).slice(-2);
  return date_format
    .replace('yy', year)
    .replace('mm', month)
    .replace('dd', day);
}


function convertDateFormat(dateString,currentFormat,targetFormat) {
  var date = new Date(dateString);
  
  // Format the date in the current format
  var formattedDate = date.toLocaleDateString(undefined, { 
    year: 'numeric', 
    month: '2-digit', 
    day: '2-digit' 
  });
  
  // Extract the parts of the date in the current format
  var parts = formattedDate.split('/');
  var year = parseInt(parts[2], 10);
  var month = parseInt(parts[0], 10);
  var day = parseInt(parts[1], 10);
  
  // Create a new date object with the extracted parts
  var newDate = new Date(year, month - 1, day);
  
  // Format the new date in the target format
  var formattedNewDate = newDate.toLocaleDateString(undefined, { 
    year: targetFormat.includes('YYYY') ? 'numeric' : undefined, 
    month: targetFormat.includes('MM') ? '2-digit' : undefined, 
    day: targetFormat.includes('DD') ? '2-digit' : undefined 
  });
  
  return formattedNewDate;
}

function formSubmitValidation(formid,submittype=""){
  var error_cnt = 0;
  $("#submit_form_type").val(submittype);
  $(".requried_field").each(function(){
      var id = $(this).attr('id');
      if($(this).val() == "" || $(this).val() == "-1"){
        console.log("emty - "+id);
        $('#'+id).prev('.error-message').text('Please Enter Required Fields');
        //$('#'+id).prev().html('<span>please enter required fields</span>');
        //$("#"+id).css('background-color','#ff7272');
        if(error_cnt == 0){
          $('html, body').animate({
          scrollTop: $("#"+id).offset().top
        }, 500);
        }
        error_cnt = error_cnt+1;
      }else{
        console.log("not emty - "+id);
        //$('#'+id).prev().html('<span></span>');
        $('#'+id).prev('.error-message').text(' ');
      }
  });
  if(error_cnt == 0){
    $("#"+formid).submit();
  }
}
function ajaxFormSubmitValidation(extraword=""){  // used not form submit 
    var error_cnt = 0;
    $(".requried_field"+extraword).each(function(){
        var id = $(this).attr('id');
        var name = $(this).attr("name");
        if($(this).val() == "" || $(this).val() == "-1"){
            console.log("emty - "+name);
            if($("[name='"+name+"']").prev('.error-message').length > 0){
                $("[name='"+name+"']").prev('.error-message').text('Please Enter Required Fields');
            }else{
                $("[name='"+name+"']").parent().prev('.error-message').text('Please Enter Required Fields');
            }
            if(error_cnt == 0){
                $('html, body').animate({
                    scrollTop: $("[name='"+name+"']").offset().top
                }, 500);
            }
            error_cnt = error_cnt+1;
        }else{
            if($("[name='"+name+"']").prev('.error-message').length > 0){
                $("[name='"+name+"']").prev('.error-message').text(' ');
            }else{
                $("[name='"+name+"']").parent().prev('.error-message').text(' ');
            }
        }
    });
    if(error_cnt == 0){
        return 0;
    }else{
        return 1;
    }
}

$('#floor_space').click(function (e){
  var building_floor_space_id = $("#building_floor_space_id").val();
  var equipment_allocation_seq = $("#equipment_allocation_seq").val();
    var $this = $(this); // or use $(e.target) in some cases;
    console.log($this);
    var offset = $this.offset();
    var width = $this.width();
    var height = $this.height();
    var posX = offset.left;
    var posY = offset.top;
    var x = e.pageX-posX;
        x = parseInt(x/width*100,10);
        x = x<0?0:x;
        x = x>100?100:x;
    var y = e.pageY-posY;
        y = parseInt(y/height*100,10);
        y = y<0?0:y;
        y = y>100?100:y;
    $("#position_"+equipment_allocation_seq).css('left',x+'%');
    $("#position_"+equipment_allocation_seq).css('top',y+'%');
    $("#left_"+equipment_allocation_seq).val(x);
    $("#top_"+equipment_allocation_seq).val(y);
});

// $('#set-floor_space').click(function (e){
//   var building_floor_space_id = $("#building_floor_space_id").val();
//   var equipment_allocation_seq = $("#equipment_allocation_seq").val();
//     var $this = $(this); // or use $(e.target) in some cases;
//     console.log($this);
//     var offset = $this.offset();
//     var width = $this.width();
//     var height = $this.height();
//     var posX = offset.left;
//     var posY = offset.top;
//     var x = e.pageX-posX;
//         x = parseInt(x/width*100,10);
//         x = x<0?0:x;
//         x = x>100?100:x;
//     var y = e.pageY-posY;
//         y = parseInt(y/height*100,10);
//         y = y<0?0:y;
//         y = y>100?100:y;
//     $("#position_"+equipment_allocation_seq).css('left',x+'%');
//     $("#position_"+equipment_allocation_seq).css('top',y+'%');
//     $("#left_"+equipment_allocation_seq).val(x);
//     $("#top_"+equipment_allocation_seq).val(y);
// });

// $('#floor_cabin').click(function (e){
//   var building_floor_space_id = $("#building_floor_space_id").val();
//   var equipment_allocation_seq = $("#equipment_allocation_seq").val();
//     var $this = $(this); // or use $(e.target) in some cases;
//     console.log($this);
//     var offset = $this.offset();
//     var width = $this.width();
//     var height = $this.height();
//     var posX = offset.left;
//     var posY = offset.top;
//     //var x = e.pageX-posX;
//     var x = e.pageX;
//         x = parseInt(x/width*100,10);
//         x = x<0?0:x;
//         x = x>100?100:x;
//     //var y = e.pageY-posY;
//     var y = e.pageY;
//         y = parseInt(y/height*100,10);
//         y = y<0?0:y;
//         y = y>100?100:y;
//         console.log(y);
//          console.log("width - "+width+" height - "+height+ " X - "+posX+" Y - "+posY+" oooo - "+e.pageX);
//     $("#position_"+equipment_allocation_seq).css('left',x+'%');
//     $("#position_"+equipment_allocation_seq).css('top',y+'%');
//     $("#left_"+equipment_allocation_seq).val(x);
//     $("#top_"+equipment_allocation_seq).val(y);
// });


$(document).ready(function(){
  if(document.getElementById('image')){
    var defaultCropData = {
        x: 0,   // Default x-coordinate
        y: 0,   // Default y-coordinate
        width: 0,   // Default width
        height: 0   // Default height
      };
      var cropper = new Cropper(document.getElementById('image'), {
        zoomable: true,
        viewMode: 3,
          crop: function(event) {
              var active_floor_space_id = $("#active_floor_space_id").val();
              $("#x_size_"+active_floor_space_id).val(parseInt(event.detail.x));
              $("#y_size_"+active_floor_space_id).val(parseInt(event.detail.y));
              $("#width_"+active_floor_space_id).val(parseInt(event.detail.width));
              $("#height_"+active_floor_space_id).val(parseInt(event.detail.height));
          },
          data: defaultCropData
      });
    } 
});
function updateUrlParameter(param,value){
        var currentUrl = window.location.href;
        var newUrl = getUrlParameter(currentUrl, param, value);
        history.pushState(null, null, newUrl);
}
function getUrlParameter(url, param, value) {
    var urlParts = url.split('?');
    if (urlParts.length >= 2) {
        var prefix = encodeURIComponent(param) + '=';
        var queryParams = urlParts[1].split(/[&;]/g);
        for (var i = 0; i < queryParams.length; i++) {
            if (queryParams[i].startsWith(prefix)) {
                queryParams[i] = prefix + encodeURIComponent(value);
                return urlParts[0] + '?' + queryParams.join('&');
            }
        }
        url += '&' + prefix + encodeURIComponent(value);
    } else {
        url += '?' + encodeURIComponent(param) + '=' + encodeURIComponent(value);
    }
    return url;
}
function dynamicFilePriviews(path){
    jQuery.ajax({
        url: "views/custom_view_dynamic_file_priviews.php",
        data:'path='+path,
        type: "POST",
        success:function(data){
            $('#popup1').html(data);
            $('#kt_modal_KTDatatable_remote').modal("show");
        },
        error:function (){}
    });
}
$('body').on('change', '.fileinputvalidation', function(e){
  var file = this.files[0];
  var acceptedformats;
  var filetype= file.type;
  var imageallfileformat;
  var splitFormat = filetype.split("/");
  console.log(splitFormat);
  if(splitFormat[1] !=""){
    var lowercasefiletype = "."+splitFormat[1].toLowerCase();
    //var lowercasefiletype = "."+splitFormat[1];
    if($(this).attr('accept')){ 
      acceptedformats = $(this).attr('accept');
      var acceptedFormatsArray = acceptedformats.split(',');
      if(acceptedFormatsArray.indexOf('image/*') == '0') {
        imageallfileformat = ['.jpg','.jpeg','.png','.gif','.bmp','.tiff','.svg','.webp'];
        jQuery.each(imageallfileformat, function(index, values) {
            acceptedFormatsArray.push(values);
        });
      }
      console.log(acceptedFormatsArray);
      if($(this).prev('.error-message').length > 0){
        $(this).prev('.fileinputvalidation_error').text(' ');
      }else{
        $(this).parent().prev('.fileinputvalidation_error').text(' ');
      }
      if(acceptedFormatsArray.indexOf(lowercasefiletype) === -1) {
          if($(this).prev('.fileinputvalidation_error').length > 0){
              $(this).prev('.fileinputvalidation_error').text('Please Select '+acceptedformats+ ' Formats');
          }else{
              $(this).parent().prev('.fileinputvalidation_error').text('Please Select '+acceptedformats+ ' Formats');
          }
          console.log('Formats');
          $(this).val('');
          return;
      }else{
          $(this).prev('.fileinputvalidation_error').text('');
          var filesize = $(this).data('size');
          if(filesize !="" && filesize !="0"){
            if (file.size > filesize){
                if($(this).prev('.fileinputvalidation_error').length > 0){
                  $(this).prev('.fileinputvalidation_error').text('File size exceeds the maximum allowed limit ('+filesize+' Bytes).');
                }else{
                  $(this).parent().prev('.fileinputvalidation_error').text('File size exceeds the maximum allowed limit ('+filesize+' Bytes).');
                }
                $(this).val('');
                console.log("file size error");
                return;
            }
          }
      }
    }
  } 
 
});
$('body').on('change', '.onchangeimagepreview', function(e){
   var imagepreview_id = $(this).data('id');
    var reader = new FileReader();
    reader.onload = function(e){
        $('#'+imagepreview_id).attr('src', e.target.result).show();
    };
    reader.readAsDataURL(this.files[0]);
});









// $(document).ready(function(){
//   if(document.getElementById('disable_crop_floor_image')){
//     var defaultCropData = {
//         x: 0,   // Default x-coordinate
//         y: 0,   // Default y-coordinate
//         width: 0,   // Default width
//         height: 0   // Default height
//       };
//       var cropper = new Cropper(document.getElementById('disable_crop_floor_image'), {
//         zoomable: false,
//         viewMode: 3,
//           crop: function(event) {
//               var active_floor_space_id = $("#active_floor_space_id").val();
//               $("#x_size_"+active_floor_space_id).val(parseInt(event.detail.x));
//               $("#y_size_"+active_floor_space_id).val(parseInt(event.detail.y));
//               $("#width_"+active_floor_space_id).val(parseInt(event.detail.width));
//               $("#height_"+active_floor_space_id).val(parseInt(event.detail.height));
//           },
//           data: defaultCropData
//       });
//     } 
// });

// $('#floor_space').click(function (e){
//   var building_floor_space_id = $("#building_floor_space_id").val();
//   var equipment_allocation_seq = $("#equipment_allocation_seq").val();
//     var $this = $(this); // or use $(e.target) in some cases;
//     console.log($this);
//     var offset = $this.offset();
//     var width = $this.width();
//     var height = $this.height();
//     var posX = offset.left;
//     var posY = offset.top;
//     var x = e.pageX-posX;
//         x = parseInt(x/width*100,10);
//         x = x<0?0:x;
//         x = x>100?100:x;
//     var y = e.pageY-posY;
//         y = parseInt(y/height*100,10);
//         y = y<0?0:y;
//         y = y>100?100:y;
//     $("#position_"+equipment_allocation_seq).css('left',x+'%');
//     $("#position_"+equipment_allocation_seq).css('top',y+'%');
//     $("#left_"+equipment_allocation_seq).val(x);
//     $("#top_"+equipment_allocation_seq).val(y);
// });

$(".no-permission").each(function() {
  $(this).css("opacity","0.4");
    if ($(this).attr("href")) {
      $(this).removeAttr("href");
    }
    if ($(this).attr("onclick")) {
      $(this).removeAttr("onclick");
    }
    if ($(this).attr("onchange")) {
      $(this).removeAttr("onchange");
    }
    if ($(this).attr("onblure")) {
      $(this).removeAttr("onblure");
    }
});

function getArtificialIntelligence(page_type){
  var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;"></div>';
  $("#popup1").html(loader);
  $('#kt_modal_KTDatatable_remote').modal("show");
  jQuery.ajax({
    url: "views/custom_view_artificial_intelligences.php",
    data:'page_type='+page_type,
    type: "POST",
    success:function(data){
      $('#popup1').html(data);
      
    },
    error:function (){}
  });
}

$('body').on('input', '.decimalnumberallowe', function() {
//$('.decimalnumberallowe').on('input', function () {
  let value = $(this).val();
  // Allow only digits and one optional dot
  let sanitized = value.replace(/[^0-9.]/g, '');         // Remove non-numeric/non-dot
  sanitized = sanitized.replace(/(\..*?)\..*/g, '$1');   // Allow only one dot
  $(this).val(sanitized);
});




//AI -------------------------------------------------
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => { 
        window.extractedDashboardData = extractFullDashboardContent();
    }, 1200);
});
function extractFullDashboardContent(page_type = null) {
    const data = {
        page_type: page_type || null, 
        full_html: "",
        forms: [],
        tables: []
    }; 
    try { 
        const bodyClone = document.body.cloneNode(true); 
        bodyClone.querySelectorAll('script, style').forEach(el => el.remove()); 
        const cleanHTML = bodyClone.innerHTML
            .replace(/\s+/g, ' ') 
            .replace(/>\s+</g, '><') 
            .trim(); 
        data.full_html = `<body>${cleanHTML}</body>`; 
        const text = el => el?.textContent?.trim().replace(/\s+/g, ' ') || ''; 
        const tables = Array.from(document.querySelectorAll("table"));
        data.tables = tables.map((table, i) => {
            const headers = Array.from(table.querySelectorAll("th")).map(th => text(th));
            const rows = Array.from(table.querySelectorAll("tbody tr")).map(tr =>
                Array.from(tr.querySelectorAll("td")).map(td => text(td))
            );
            return { index: i + 1, headers, rows };
        }); 
        const forms = Array.from(document.querySelectorAll("form"));
        data.forms = forms.map((form, i) => ({
            index: i + 1,
            id: form.id || null,
            action: form.action || "",
            inputs: Array.from(form.querySelectorAll("input, select, textarea")).map(f => ({
                name: f.name || f.id || "",
                type: f.type || f.tagName.toLowerCase(),
                value: f.value || "",
                checked: f.checked || false,
                placeholder: f.placeholder || ""
            }))
        })); 
        return data; 
    } catch (err) {
        return null;
    }
} 
function handleArtificialIntelligenceClick(mode="") {
    var page_type = '';
    const loader = `<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;"></div>`; 
    $("#dynamic_popup1").html(loader); 
    $('#kt_modal_dynamic_popup').modal("show");
    $.ajax({ 
        url: "views/custom_view_artificial_intelligences.php", 
        type: "POST",
        data: {
            page_type: page_type,
        }, 
        success: function(data) {  
            $('#dynamic_popup1').html(data); 
        },
        error: function(xhr, status, error) {
        }
    });
}

/** OLD Omkar Created 
function handleArtificialIntelligenceClick(mode="") {

    var work_module_setup_id = $("#work_module_setup_id").val();
    var mode = mode !=""?mode:work_module_setup_id;
    try { 
        const latestData = extractFullDashboardContent(mode); 
        window.extractedDashboardData = latestData; 
        getArtificialIntelligence(mode, latestData);
    } catch (err) {
        //console.error("⚠️ Failed to refresh dashboard data:", err);
    }
}
function getArtificialIntelligence(page_type, dashboardData) { 
    const loader = `
        <div class="text-center" id="loader-show">
            <img src="assets/images/spiner/spiner3.gif" style="width: 300px;">
        </div>`; 
    $("#popup1").html(loader); 
    $('#kt_modal_KTDatatable_remote').modal("show");
    $.ajax({ 
        url: "views/custom_view_artificial_intelligences.php", 
        type: "POST",
        data: {
            page_type: page_type,
            dashboard_data: JSON.stringify(dashboardData || {})
        }, 
        success: function(data) {  
            $('#popup1').html(data); 
           // console.groupEnd();
        },
        error: function(xhr, status, error) {
        }
    });
}
 **/
//AI -------------------------------------------------


function submitDynamicWorkflowPopUp(table_name,transaction_id,current_status,next_status){
  if (typeof checkMandatoryArray === "function") {
    var validate = checkMandatoryArray();
  }else{
    var validate = 0;
  }
    
    if(validate == 0){
        var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;"></div>';
        $("#popup1").html(loader);
        $('#kt_modal_KTDatatable_remote').modal("show");
        jQuery.ajax({
            url: "views/custom_insert_dynamic_status_popup.php",
            data:'table_name='+table_name+'&current_status='+current_status+'&transaction_id='+transaction_id+'&next_status='+next_status,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
            },
            error:function (){}
        });
    }
}
function submitDynamicWorkflow(table_name,transaction_id,current_status,next_status,old_status_name,new_status_name){
  if (typeof checkMandatoryArray === "function") {
    var validate = checkMandatoryArray();
  }else{
    var validate = 0;
  }
  var session_config_json = JSON.parse($("#session_config_json").val());
    if(validate == 0){
      swal({
        title: session_config_json.name,
        text: "Please note the status of the document will change to "+new_status_name,
        type: "info",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Yes",
        cancelButtonText: "No",
        closeOnConfirm: false
    },
    function(){
        jQuery.ajax({
            url: "ajax.php",
            data:'page=submitstatusdynamicworkflow&table_name='+table_name+'&current_status='+current_status+'&transaction_id='+transaction_id+'&next_status='+next_status,
            type: "POST",
            success:function(response){
                var res = JSON.parse(response);
                window.location.href = window.location.href;
                if(res.transaction_update.status=='sucess'){
                     window.location.href = window.location.href;
                    swal({
                        title: session_config_json.name,
                        text: error_message_json['9059'],
                        type: "success",
                        showCancelButton: false,
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true
                    }, function () {
                        window.location.href = window.location.href;
                    });
                }else{
                    swal({
                        title: "Oops...",
                        text: res.transaction_update.message,
                        type: "error",
                        showCancelButton: true,
                        showConfirmButton: false,
                        cancelButtonText: "Ok",
                        confirmButtonClass: "btn-danger",
                        closeOnCancel: false
                    });
                }
            },
            error:function (){}
        });
      }); 
    }
} 

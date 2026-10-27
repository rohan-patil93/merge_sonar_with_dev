window.addEventListener("load", function() {
 $('[data-bs-toggle="tooltip"]').tooltip();
  $('[data-bs-toggle="popover"]').popover({
      html: true,
      content: function() {
          return $('#primary-popover-content').html();
      }
  });
  $(document).on("click", ".handleArtificialIntelligenceClick", function () {
    handleArtificialIntelligenceClick();
  });
  $(document).on("click", ".toggleTableAI", function () {
        let id = this.id;    
        $("."+id).removeClass('d-none');
  });
  $(document).on("click", ".pauseTimerdash", function () {
    pauseTimerdash();
  });
  $(document).on("click", ".chnageModuleWithRole", function () {
    chnageModuleWithRole();
  });
  $(document).on("click", ".checkinattendence", function () {
    var msg = $(this).data("msg");
    checkinattendence(msg);
  });
  $(document).on("click", ".getApplicationTickets", function () {
    var apttype = $(this).data("apttype");
    getApplicationTickets(apttype);
  });
  $(document).on("click", ".getNotificationList", function () {
    getNotificationList();
  });
  $(document).on("click", ".locationhref", function () {
    var href = $(this).data("href");
    location.href = href;
  });
  $(document).on("click", ".chnageModuleWithRole", function () {
    var module_id = $(this).data("module_id");
    var modulerolename = $(this).data("modulerolename");
    chnageModuleWithRole(module_id, modulerolename);
  });
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

   $(document).on("change", ".loadFile", function () {
      var id = $(this).data("id");
      loadFile(this, id);
  });
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

$(document).on("click", ".templateBackButton", function() {
  window.history.go(-1);
})


  $(document).on("change", "input[type='file']", function () {
    var file = this.files[0];
    if (!file) return;
    var allowedExtensions = [
        "jpg","jpeg","png","gif","bmp","webp","svg","ico",
        "pdf","doc","docx","txt","rtf","odt",
        "xls","xlsx","csv","ods",
        "ppt","pptx","odp",
        "zip","rar","7z","tar","gz",
        "mp3","wav","ogg","aac","m4a","flac",
        "mp4","avi","mov","mkv","wmv","webm","mpeg","mpg","3gp"
    ];
    var extension = file.name.split('.').pop().toLowerCase();
    if ($.inArray(extension, allowedExtensions) === -1) {
        alert("This file type is not allowed.");
        $(this).val(""); // Clear selected file
        return false;
    }
  });

});


function loadFile(input, id) {
    var image = document.getElementById("output" + id);
    image.src = URL.createObjectURL(input.files[0]);
}


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
  console.log('vdfvdfvfg');
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
        $('#'+imagepreview_id).attr('src', e.target.result).removeClass('d-none');
    };
    reader.readAsDataURL(this.files[0]);
});
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
/**
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
            // console.error("❌ AI Popup Load Error");
            // console.error("Status:", status);
            // console.error("Error:", error);
            // console.error("Response Text:", xhr.responseText);
            //console.groupEnd();
        }
    });
}
**/
//AI -------------------------------------------------


// ------------------------------ Start  Header.php js   ---------------------------------------
function clicktransport(){
    //alert("hai");
    jQuery.ajax({
            url: "views/customtransportrequestpopup.php",
            data:'option=1&emp_id='+$('#emp_id').val(),
            type: "POST",
            success:function(data){
                $('#popup2').html(data);
                $('#kt_modal_KTDatatable_remote1').modal("show");
            },
            error:function (){}
        });
    }
    function checkinattendence(msg){
        //alert(dat+'/'+tim);
        //str = 'date='+dat+'&time='+tim;
        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: 'page=employeeclocks',
            success: function(response) {
                console.log(response);
                window.location.reload();
                //alert(response);
                //$('.success').html(response);
                //document.getElementById("myForm").submit();
                /*swal.fire({              
                    title: msg,              
                            //text: "Do you want to close ? ",              
                            type: "success",           
                            buttonsStyling: false,
                            confirmButtonText: "<i class='la la-hand-o-up'></i>Yes, close!",    
                            confirmButtonClass: "btn btn-danger", 
                            showCancelButton: false,     
                            cancelButtonText: "<i class='la la-hand-o-right'></i> No, continue",      
                            cancelButtonClass: "btn btn-default"        
                        }).then(function(result) {
                            if (result.value){
                                $('#kt_modal_KTDatatable_remote').modal('hide');
                                window.location.reload();
                            }
                        }); */
                    }
                });
    }
    function checkemptransportrequest(emp_id,val){
        if(val !=""){
            $.ajax({
                url: "ajax.php",
                data:'page=updatedynamicfied&table=employee_transport_requests&hid='+val+'&fid=is_active&rid=1',
                type: "POST",
                success:function(data){
                    console.log(data);
                },
                error:function (){}
            });
        }else{
            jQuery.ajax({
                url: "views/new_from.php",
                data:'option=COM_INSERT_EMPLOYEE_TRANSPORT_REQUESTS&emp_id='+emp_id+'&location_id='+$('#location_id').val(),
                type: "POST",
                success:function(data){
                    $('#popup2').html(data);
                    $('#kt_modal_KTDatatable_remote1').modal("show");
                },
                error:function (){}
            });
        }
    }
    function checkempfoodoption(val,emp_id){
        $.ajax({
            url: "ajax.php",
            data:'page=insertanupdateempfoodoption&food_option='+val+'&emp_id='+emp_id+'&location_id='+$('#location_id').val()+'&start_date='+$('#start_date').val(),
            type: "POST",
            success:function(data){
                console.log(data);
            },
            error:function (){}
        });

    }
    function dynamicinsertpopup(option){
        jQuery.ajax({
            url: "views/new_from.php",
            data:'option='+option+'&emp_id=1',
            type: "POST",
            success:function(data){
                $('#popup2').html(data);
                $('#kt_modal_KTDatatable_remote1').modal("show");
            },
            error:function (){}
        });
    }

    function divFunction(){
        $('.profile-close').toggle("slide", { direction: "right" }, 1000);
        $('.basic_info_other').hide('slow');
    }

    function div2Function(){
        $('.basic_info_other').toggle("slide", { direction: "left" }, 1000);
        $('.profile-close').hide('slow');
    }
    function closenotificationinfo(id){
     $.ajax({
            url: "ajax.php",
            data:'page=closenotificationinfo&id='+id,
            type: "POST",
            success:function(data){
                console.log(data);
                $("#infoclose"+id).remove();
            },
            error:function (){}
        });
}
 function closeallnotifications(type,user_id){
        $.ajax({
            url: "ajax.php",
            data:'page=closeallnotifications&type='+type+'&user_id='+user_id,
            type: "POST",
            success:function(data){
                console.log(data);
                //$("#infoclose"+id).remove();
            },
            error:function (){}
        });
    }


function getNotificationList(){
    //$(".popnotifictionspeen").show();
    //$(".popnotifiction").hide();
    //$(".iconshownotifiction").html("");
    //$("#iconshownotifiction_"+grp_mid).html('<i class="fa fa-check-circle text-warning f-16"></i>');
    jQuery.ajax({
            url: "views/custom_list_notification_popup.php",
            data:'page=page',
            type: "POST",
            success:function(data){
                console.log(data);
                $('#popnotifiction').html(data);
                //$(".popnotifictionspeen").hide();
                //$(".popnotifiction").show();
            },
            error:function (){}
        });
}

function changeAdminToEmployee(){
    var admin_access_employee = "";
    var msg="";
    if($("#admin_access_employee").is(':checked')){
        admin_access_employee = "Y";
        msg="<?php echo $_SESSION['error_message']['9071']; ?>";
    }else{
        admin_access_employee = "N";
        msg="<?php echo $_SESSION['error_message']['9072']; ?>";
    }
    jQuery.ajax({
            url: "views/custom_change_admin_to_employees.php",
            data:'admin_access_employee='+admin_access_employee,
            type: "POST",
            success:function(data){
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',msg,'');
                window.location.reload();
            },
            error:function (){}
        });
}

function getApplicationTickets(page_type){
    jQuery.ajax({
        url: "views/custom_insert_application_tickets_popup.php",
        data:'page_type='+page_type,
        type: "POST",
        success:function(data){
            if(page_type == 'NT'){
                $('#popupm').html(data);
                $('#kt_modal_KTDatatable_remotem').modal("show");
            }else{  
                $('#dynamic_popup1').html(data);
                $('#kt_modal_dynamic_popup').modal("show");
            }
            
            $(".js-example-basic-single").select2();
        },
        error:function (){}
    });
}

  /**
  let startTimedash, timerIntervaldash, pausedTimedash;

    function startTimerdash() {
        const timerDisplaydash = document.getElementById('timerdash');
        const startButtondash = document.getElementById('startButtondash');
        const pauseButtondash = document.getElementById('pauseButtondash');
        var oldtimedash = $("#oldtimedash").val();
        var currenttimelogdash = $("#currenttimelogdash").val();
        console.log('nowqq'+Date.now());
        if(oldtimedash !=0 && !startTimedash){
            if(currenttimelogdash!="" ){
            var noldtimedash = Date.now() - currenttimelogdash;
            oldtimedash = parseInt(oldtimedash) + parseInt(noldtimedash);
            console.log('currenttimelog'+oldtimedash);
            }
            startTimedash =  Date.now() - oldtimedash;
            console.log('restart'+startTimedash);
            pausedTimedash = Date.now();
            console.log('now pase'+pausedTimedash);
        }
        if (!startTimedash) {
            // Start the timer
            console.log('startTime');
            startTimedash = Date.now();
            $('#btntimedash').hide();
            $('#btnpasdash').show();
            sessionstartandinsertimelog('start',startTimedash,'');
            //  document.getElementById('btntime').innerHTML  = '<button id="pauseButton" class="btn-danger"> <i class="ti-control-record" > </i> </button>';
            timerIntervaldash = setInterval(updateTimerdash, 1000);
        } else if (pausedTimedash) {
          console.log('pay Time:'+Date.now());
          console.log('pausedTime:'+pausedTimedash);
            // Resume the timer after pause
            startTimedash += Date.now() - pausedTimedash;
            pausedTimedash = null;
           // pauseButton.textContent = 'Pause';
           timerIntervaldash = setInterval(updateTimerdash, 1000);
           $('#btntimedash').hide();
           $('#btnpasdash').show();
        }
        function updateTimerdash() {
             //console.log('updateTimer');
            const currentTimedash = Date.now();
            const elapsedMilliseconds = currentTimedash - startTimedash;

            const hours = Math.floor(elapsedMilliseconds / 3600000);
            const minutes = Math.floor((elapsedMilliseconds % 3600000) / 60000);
            const seconds = Math.floor((elapsedMilliseconds % 60000) / 1000);

            const formattedTime = `${padZero(hours)}:${padZero(minutes)}:${padZero(seconds)}`;
            //console.log('updatetime:'+formattedTime);
            timerDisplaydash.textContent = formattedTime;
        }
        function padZero(number) {
            //console.log('padZero');
            return number.toString().padStart(2, '0');
        }
    }
    function pauseTimerdash() {
        console.log('padZero paused');
        if (startTimedash && !pausedTimedash) {
            clearInterval(timerIntervaldash);
            pausedTimedash = Date.now();
            console.log(pausedTimedash);
            //document.getElementById('pauseButton').textContent = 'Resume';
            
            $('#dislogclose').hide();
            sessionstartandinsertimelog('pause',startTimedash,pausedTimedash);
        }
    }
    **/
    //rohan Commented?
    //document.getElementById('startButtondash').addEventListener('click', startTimerdash);
    //document.getElementById('pauseButtondash').addEventListener('click', pauseTimerdash);
   

    function sessionstartandinsertimelog(type,starttime,endtime){
        var task_id = $('#task_id_dash').val();
        jQuery.ajax({
            url: "ajax.php",
            data:'page=toinsertupdateprojecttasktimlog&task_id='+task_id+'&type='+type+'&starttime='+starttime+'&endtime='+endtime,
            type: "POST",
            success:function(data){
                console.log(data);
                //$("#rem_"+val).hide();
            },
        error:function (){}
        });
    }

    function chnageModuleWithRole(module_id ="",role_short_code=""){
        var working_role_short_code = $("#working_role_short_code").val();
        var work_module_setup_id = $("#work_module_setup_id").val();
        var selectmodule = '';
        var selectrole = '';
        
        if(module_id !=""){
            selectmodule = module_id;
        }else{
            selectmodule = work_module_setup_id; 
        }
        if(role_short_code !=""){
            selectrole = role_short_code;
        }else{
            selectrole = working_role_short_code; 
        }
        window.location.href = "index.php?option=COM_CHANGE_MODULE_GROUPS&Rid="+selectmodule+"&working_role_short_code="+selectrole;
        // $href = "index.php?option=COM_CHANGE_MODULE_GROUPS&Rid=".base64_encode($module_list['module_id']);               
    }


// ------------------------------ End Header.php js   ---------------------------------------

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


// custom_insert_dynamic_status_popup.js
// console.log('outside');
window.addEventListener("load", function() {
// console.log('inside');
  $(document).on("click", ".submitDynamiPopUpForm", function() {
    submitDynamiPopUpForm();
  });
});  

  function getStockBalanceAvailability(transaction_table, transaction_id){
    var session_config_json = JSON.parse($("#session_config_json").val());
    var error_message_json = JSON.parse($("#error_message_json").val());
      let retres = 0;
      $.ajax({
          url: 'ajax.php',
          type: "POST",
          async: false,
          data: 'page=getstockbalanceavailability&transaction_table='+transaction_table+'&transaction_id='+transaction_id,
          success: function(response) {
            console.log(response);
            var res = JSON.parse(response);
            if(res.errorthrow.errorcount != 0){
              var errormsg = "Item ";
              $.each(res.errorthrow.details, function(index, itemdet) {
                errormsg += itemdet.item_name+" has only "+itemdet.closing_qty+" qty available — you applied "+itemdet.sum_item_quantity+" qty. , ";
              });
              errormsg += "Please check stock before applying.";
              swal(session_config_json.name, errormsg, "error");
              retres = 1;
            }else{
              retres = 0;
            }
          }
      })
      return retres;
    }
    
    function submitDynamiPopUpForm(){
      var updated_statuse_transaction_table = $("#updatedstatusetransactiontable").val();
      var updated_statuse_transaction_id = $("#updatedstatusetransactionid").val();
      let returnresult = 0;
      if(updated_statuse_transaction_table == 'delivery_orders' || updated_statuse_transaction_table == 'sales_invoices'){
        returnresult = getStockBalanceAvailability(updated_statuse_transaction_table,updated_statuse_transaction_id);
      }
      if(returnresult == 0){
        //$("#statuspopupbtn").prop('disabled', true);
        $("#statuspopupbtn").text("Please Wait........");
        $("#statuspopupbtn").addClass("disabled");
        var formdata = $("#dynamic_status_forms").serialize();
        console.log(formdata);
        if($("#hide_mobileview_id").val() =="Y"){ 
          var url ="../../ajax.php";
        }else{
           var url ="ajax.php";
        }
        $.ajax({
          url: url,
          type: "POST",
          data: 'page=submitstatusdynamicworkflow&'+formdata,
          success: function(response) {
            console.log(response);
            var res = JSON.parse(response);
            console.log(res);
            if(res.transaction_update.status=='sucess'){
                swal({
                  title: "",
                  text: 'Submission Successful',
                  type: "success",
                  showCancelButton: false,
                  confirmButtonClass: "btn-danger",
                  confirmButtonText: "Yes",
                  cancelButtonText: "No",
                  closeOnConfirm: true,
                  closeOnCancel: true
                },
                function(isConfirm) {
                  if (isConfirm) {
                     $('#kt_modal_KTDatatable_remote').modal('hide');
                      $(".pcoded-main-container").addClass('event_disabled');
                      window.location.href=window.location.href;
                  } 
                });
                $('#kt_modal_KTDatatable_remote').modal('hide');
            }else{
              $("#statuspopupbtn").text("Submit");
              $("#statuspopupbtn").removeClass("disabled");
            }
          }
        });
      }
    }
//custom_insert_dynamic_status_popup.js

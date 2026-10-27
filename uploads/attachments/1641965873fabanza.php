<button type='button' class='action btn-sky text-capitalize next btn bodyheight-next-btn' data-info='' id='btnbodyheight'>Next</button>
//button one class added "bodyheight-next-btn" and data-info=''


//submit value function create

function sbtallformvalues(){
  var bodyheight = jQuery("#bodyheight input:text").val();
var selectbustsize = jQuery( "#selectbustsize option:selected") .text();
var underbust = jQuery( "#underbust input:text").val();
var shoulderlength = jQuery( "#shoulderlength input:text").val(); 
var salwararmhole = jQuery( "#salwararmhole input:text").val();
var waistcircumfrance = jQuery( "#waistcircumfrance input:text").val(); 
var salwararmround = jQuery( "#salwararmround input:text").val();
var salwarsleeveround = jQuery( "#salwarsleeveround input:text").val();
var bottomlength = jQuery( "#bottomlength input:text").val();
var sleevelength = jQuery( "#sleevelength input:text").val();
var kameezlength = jQuery( "#kameezlength input:text").val();
var specialmessage  = jQuery( "#specialmessage textarea").val();
jQuery('#id1salwarar').val(bodyheight);
jQuery('#id2salwarar').val(selectbustsize);
jQuery('#id3salwarar').val(underbust);
jQuery('#id4salwarar').val(shoulderlength);
jQuery('#id5salwararmhole').val(salwararmhole);
jQuery('id5sleeveround').val();
jQuery('#id5waistcircumfrance').val(waistcircumfrance);
jQuery('#id5salwararmound').val(salwararmround);
jQuery('#id5salwarsleeveround').val(salwarsleeveround);
jQuery('#id5bottomlength').val(bottomlength);
jQuery('#id5sleevelength').val(sleevelength);
jQuery('#id5kameezlength').val(kameezlength);
jQuery('#id5specialmessage').val(specialmessage);
jQuery("div#specialmessage .more_information").hide(); 
jQuery("#slwarfinalresult").appendTo('.reult_information_salwar');
jQuery("div#slwarfinalresult").show();
jQuery("div#slwarfinalresult .display").show();
jQuery("#slwarfinalresult .sucessresult").hide();
jQuery("div#specialmessage .control").hide();
jQuery("div#specialmessage .btnnext").hide();
jQuery("div#specialmessage .btnprev").hide();
jQuery("div#specialmessage span").hide();

}

//End submit value function create

jQuery(document).on('click', '#salwar-edit1', function(){ 
      jQuery("div#slwarfinalresult").hide();
      jQuery("div#bodyheight").show();   
      jQuery("div#selectmeasurementstype").show();      
      jQuery("div#selectmeasurementstype .more_information").show(); 
     jQuery(".bodyheight-next-btn").attr('data-info', 'finish');     		//data-info id value passs
});

jQuery(document).on('click', '#bodyheight .btnnext', function(){ 
  var chkvalue = jQuery(".bodyheight-next-btn").attr('data-info');
  if(chkvalue=="finish"){     													 //check data-info value finish or other
        jQuery("div#bodyheight").hide(); 
    jQuery("div#sleevelength").hide();
    jQuery("div#kameezlength").hide();
    jQuery("div#salwararmround").hide();
    jQuery("div#salwarsleeveround").hide();
    sbtallformvalues();
  }else{   																 		//normar next
     if( !jQuery("#bodyheight input:text").val() ) {
        jQuery("#bodyheight input").prop('required',true);
        jQuery("#bodyheight").addClass("error");
    }
    jQuery("div#selectbustsize").show(); 
    jQuery("div#underbust").show();
    jQuery("div#selectmeasurementstype").hide();     
    jQuery("div#bodyheight").hide(); 
    jQuery("div#sleevelength").hide();
    jQuery("div#kameezlength").hide();
    jQuery("div#salwararmround").hide();
    jQuery("div#salwarsleeveround").hide();
  }
});


  
$(".fff").click(function() {
	$(".leftsidemenu").toggle( "slow" );
});
function Getinserttypeold(values,type,report_id,key){
	if(type=="text"){ 
		$(".filter"+key).html("<input type='text' class='form-control'>");
	}else if(type=="date_pick"){  
		$(".filter"+key).html("<input type='date' class='form-control'>");
	}else if(type=="select"){
		$(".filter"+key).html('<select class="form-select js-example-basic-single " ><option>Select Any Option</option></select>');  
	}
	if(type == 'select'){
		jQuery.ajax({
			url: "views/custom_report_filter.php",
			data:'page=dis_att_type&report_id='+report_id+'&field='+values,
			type: "POST",
			success:function(data){
				$('#dynamic_popup1').html(data);
				$('#kt_modal_dynamic_popup').modal("show");
			},
			error:function (){}
		});			
	}
}

$('body').on('change', '.table_names_select', function() {
	var page = $(this).val();
	var id = $("#id").val();
	if(page!="0"){ 
		$(".displaye").hide();
		if(id==""){
			window.location.href = 'index.php?option=COM_INSERT_MY_REPORTS&table='+page;
		}else{
			window.location.href = 'index.php?option=COM_INSERT_MY_REPORTS&table='+page+'&Rid='+id;	
		}
	}else{
		$(".displaye").hide();
	}
});
$('body').on('change', '.display', function() {
	var id = $(this).attr("data-id");
	if($(this).is(":checked")){
		$(".filedsrow"+id).show();
	}else{
		$(".filedsrow"+id).hide();
	}
});
$('body').on('keyup', '.txtlablel', function() {
	var id = $(this).attr("data-id");
	var value = $(this).val();
	$("#thlabel"+id).text(value);
});
$('body').on('keyup', '.formname', function() {
	//var id = $(this).attr("data-id");
	var value = $(this).val();
	$(".formnameview").text(value);
});
$('body').on('click', '.positiontable', function() {
	var value = $(this).val();
	var id = $(this).attr("data-id");
	$("#thlabel"+id).attr('class', '');
	$("#thlabel"+id).addClass("pl-0 font-weight-bold text-muted text-uppercase "+value+" filedsrow"+id);
	$("#thvalue"+id).attr('class', '');
	$("#thvalue"+id).addClass("pl-0 pt-7 "+value+" filedsrow"+id);
	$("#heddingpositiontable"+id).val(value);
});

$('body').on('keyup', '.footerdescriptioon', function() {
	var value = $(this).val();
	$(".footerdescriptioonview").text(value);
});



//heade   part

$('body').on('click', '.header_position_hedding', function() {
	var value = $(this).val();
	var width = $(".header_width_hedding").val();
	$("#heading_div_id").attr('class', '');
	$("#heading_div_id").addClass("col-lg-"+width+" "+value);
	$("#heddingposition").val(value);
});
$('body').on('change', '.header_width_hedding', function() {
	var value = $(this).val();
	$("#heading_div_id").attr('class', '');
	$("#heading_div_id").addClass("col-lg-"+value);
});
$('body').on('change', '.header_order_position', function() {
	var value = $(this).val();
	$("#heading_div_id").css("order",value);
});


$('body').on('click', '.header_position_logo', function() {
	var value = $(this).val();
	var width = $(".header_width_logo").val();
	$("#logo_div_id").attr('class', '');
	$("#logo_div_id").addClass("col-lg-"+width+" "+value);
	$("#logoposition").val(value);
});
$('body').on('change', '.header_width_logo', function() {
	var value = $(this).val();
	$("#logo_div_id").attr('class', '');
	$("#logo_div_id").addClass("col-lg-"+value);
});
$('body').on('change', '.header_order_logo', function() {
	var value = $(this).val();
	$("#logo_div_id").css("order",value);
});





$('body').on('click', '.header_position_address', function() {
	var value = $(this).val();
	var width = $(".header_width_address").val();
	$("#address_div_id").attr('class', '');
	$("#address_div_id").addClass("col-lg-"+width+" "+value);
	$("#addressposition").val(value);
});
$('body').on('change', '.header_width_address', function() {
	var value = $(this).val();
	$("#address_div_id").attr('class', '');
	$("#address_div_id").addClass("col-lg-"+value);
});
$('body').on('change', '.header_order_address', function() {
	var value = $(this).val();
	$("#address_div_id").css("order",value);
});


$('body').on('change', '.filterdisplaywitch', function() {
	var id = $(this).attr("data-id");
	if($(this).is(":checked")){
		$(".filter"+id).show();
	}else{
		$(".filter"+id).hide();
	}
	if($(".filterdisplaywitch:checked").length=='0'){
		$(".filterbtn").hide();
	}else{
		$(".filterbtn").show();
	}
});
$('body').on('change', '.addressdislay', function() {
	if($(this).is(":checked")){

		$("#address_div_id").show();
	}else{
		$("#address_div_id").hide();
	}
});


$('body').on('change', '.logodislay', function() {
	if($(this).is(":checked")){
		$("#logo_div_id").show();
	}else{
		$("#logo_div_id").hide();
	}
});
$('body').on('change', '.heddingdislay', function() {
	if($(this).is(":checked")){
		$("#heading_div_id").show();
	}else{
		$("#heading_div_id").hide();
	}
});




//start header drag and drop
document.addEventListener('DOMContentLoaded', (event) => {
	var dragSrcEl = null;
	var dragid="";
	var dragclass = "";
	function handleDragStart(e) {
		dragSrcEl = this;
		e.dataTransfer.effectAllowed = 'move';
		e.dataTransfer.setData('text/html', this.innerHTML);
		$(".box").css("border","1px dotted #1cc4fc");
		$(".box").addClass("min-h-25px");
		console.log($(this).attr("data-id"));
		dragid = $(this).attr("data-id");
		dragclass = $(this).children()[0].dataset['order'];
	}
	function handleDragOver(e) {
		if (e.preventDefault) {
			e.preventDefault();
		}
		e.dataTransfer.dropEffect = 'move';
		return false;
	}
	function handleDragEnter(e) {
		this.classList.add('over');
	}
	function handleDragLeave(e) {
		this.classList.remove('over');
	}
	function handleDrop(e) {
		if (e.stopPropagation) {
      	e.stopPropagation(); // stops the browser from redirecting.
      	$(".box").css("border","");
      	$(".box").removeClass("min-h-25px")
      	dropid=$(this).attr("data-id");
      	if($(this).children()[0]){
      		console.log(console.log($(this).children()[0].dataset['order']));
      		dropclass=$(this).children()[0].dataset['order'];
      		$("."+dropclass).val(dragid);
      		$("."+dropclass).text(dragid);
      	}
      	$("."+dragclass).val(dropid);
      	$("."+dragclass).text(dropid);

      }
      if (dragSrcEl != this) {
      	dragSrcEl.innerHTML = this.innerHTML;
      	this.innerHTML = e.dataTransfer.getData('text/html');
      }
      return false;
  }
  function handleDragEnd(e) {
  	items.forEach(function (item) {
  		item.classList.remove('over');
  	});
  }
  let items = document.querySelectorAll('.container .box');
  items.forEach(function(item) {
  	item.addEventListener('dragstart', handleDragStart, false);
  	item.addEventListener('dragenter', handleDragEnter, false);
  	item.addEventListener('dragover', handleDragOver, false);
  	item.addEventListener('dragleave', handleDragLeave, false);
  	item.addEventListener('drop', handleDrop, false);
  	item.addEventListener('dragend', handleDragEnd, false);
  });
});

//header drag and drop



$('body').on('change', '.inworddisplay', function() {
	if($(this).is(":checked")){
		$(".inworddislayclas").show();
	}else{
		$(".inworddislayclas").hide();

	}
});
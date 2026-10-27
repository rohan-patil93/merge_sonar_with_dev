window.addEventListener("load", function() {
	$(document).on("click", ".showpopup", function() {
		showpopup();
	});


	$(document).on("change", ".showbulkchangefilter", function() {
		var valu = $(this).val();
		showbulkchangefilter(valu);
	});
	$(document).on("click", ".bulkconfirmpopup", function() {
		bulkconfirmpopup();
	});

	$(document).on("click", ".getBasicInfoPopUp", function() {
		var empid = $(this).data('empid');
		getBasicInfoPopUp(empid);
	});

});	

function selectempdata(value) {
	var count =0;
	var bulkemplist =  document.getElementsByName("bulkemplist[]");
	for (var i = 0; i < bulkemplist.length; i++) {
		if(bulkemplist[i].checked){
			count++;
		}
	}
	if(count > 0) {
		document.getElementById('bulkcategory').style.display = "block";
	}else {
		document.getElementById('bulkcategory').style.display = "none";
		document.getElementById('effectdate').style.display = "none";
		document.getElementById('previewbtn').style.display = "none";
		document.getElementById('application_tablename_filter').style.display = "none";
	}
}
function showpopup(){
	var filter_id = document.getElementById('filtertbl_rowid').value;
	jQuery.ajax({
		url: "views/custom_filter_bulktransfer.php",
		data:'filter_id='+filter_id,
		type: "GET",
		success:function(data){
			$('#dynamic_popup1').html(data);
			$('#kt_modal_dynamic_popup').modal("show");
		},
		error:function (){}
	}).done(function(){
	});
}

function selected_bulk_category(categoryid){
	var bulkmove_empids = [];
	var bulkemplist =  document.getElementsByName("bulkemplist[]");
	for (var i = 0; i < bulkemplist.length; i++) {
		if(bulkemplist[i].checked){
			bulkmove_empids.push(bulkemplist[i].value);
		}
	}
	document.getElementById('bulkmove_empids').value = bulkmove_empids.toString();
	document.getElementById('bulkmove_toid').value = categoryid;
	document.getElementById('effectdate').style.display = "block";
	document.getElementById('previewbtn').style.display = "block";
}

function bulkconfirmpopup(){
	var bulkmove_empids = document.getElementById('bulkmove_empids').value;
	var application_tablename = document.getElementById('application_tablename').value;
	var application_table_fieldname = document.getElementById('application_table_fieldname').value;
	var update_employees_fieldname = document.getElementById('update_employees_fieldname').value;
	var insert_history_tablename = document.getElementById('insert_history_tablename').value;


	var bulkchange_categoryid = document.getElementById('bulkchange_categoryid').value;
	var bulkmove_toid = document.getElementById('bulkmove_toid').value;
	var effective_date = document.getElementById('kt_datepicker_4_1').value;
	var pass_str = 'empids='+bulkmove_empids+'&application_tablename='+application_tablename+'&effective_date='+effective_date+'&application_table_fieldname='+application_table_fieldname+'&bulkchange_categoryid='+bulkchange_categoryid+'&update_employees_fieldname='+update_employees_fieldname+'&bulkmove_toid='+bulkmove_toid+'&insert_history_tablename='+insert_history_tablename;
	console.log(pass_str);
	jQuery.ajax({
		url: "views/custom_view_emp_bulktransfer_popup.php",
		data: pass_str,
		type: "GET",
		success:function(data){
			$('#popup2').html(data);
			$('#kt_modal_KTDatatable_remote1').modal({ show: true});
		},
		error:function (){}
	});
}

function showbulkchangefilter(bulkcategory_tablefields){
		//alert(bulkcategory_tablefields);

	var bulkcategory_tablefields = bulkcategory_tablefields.split('|');
	var application_tablename = bulkcategory_tablefields[0];
	var application_table_fieldname = bulkcategory_tablefields[1];
	var bulkchange_categoryid = bulkcategory_tablefields[2];
	var update_employees_fieldname = bulkcategory_tablefields[3];
	var insert_history_tablename = bulkcategory_tablefields[4];
	document.getElementById('bulkchange_categoryid').value = bulkchange_categoryid;
	document.getElementById('application_tablename').value = application_tablename;
	document.getElementById('application_table_fieldname').value = application_table_fieldname;
	document.getElementById('update_employees_fieldname').value = update_employees_fieldname;
	document.getElementById('insert_history_tablename').value = insert_history_tablename;
	$.ajax({
		url: "ajax.php",
		data:'page=getapplicationtablevalues&application_tablename='+application_tablename+'&application_table_fieldname='+application_table_fieldname,
		type: "POST",
		success: function(response) {
			console.log("response ="+response);
			document.getElementById('application_tablename_filter').innerHTML = response;

		}
	});

}

function getnamelist(emplist){
	console.log(emplist);
	document.getElementById('filterby').value = emplist;
	$('#kt_modal_dynamic_popup').modal('hide');
}


function  getBasicInfoPopUp(emp_id) {
	jQuery.ajax({
		url: "views/custom_insert_onboarding_employees.php",
		data:'emp_id='+emp_id,
		type: "POST",
		success:function(data){
		        $('#dynamic_popupxxl').html(data); //popup1
		       // $('#kt_modal_KTDatatable_remote').modal("show"); 
		        $('#kt_modal_dynamic_popupxxl').modal("show");
		        $(".js-example-basic-single").select2();
		        $(".js-example-disabled-results").select2();
		    },
		    error:function (){}
		});
}
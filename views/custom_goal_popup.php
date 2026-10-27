<?php
include('../includes/session.php');
//print_r($_SESSION); exit;	
include('../dbconfig.php');
include('../comm.php');
?>
<script type="text/javascript">
	function addmoreRows() {
		 var count = $(".itemRow123").length;
         count++;
        var htmlRows = '';
        htmlRows += '<tr class="itemRow123">'; 
        htmlRows += '<td><input type="text" name="name[]" class="form-control" value="" id="name_'+count+'"></td> ';
        htmlRows += '<td><textarea class="form-control" name="descrption[]" id="descrption_'+count+'"></textarea></td>';   

        htmlRows += '<td><input type="text" name="short_code[]" class="form-control" value="" id="short_code_'+count+'"></td>';    
        htmlRows += '<td><input type="text" name="frequency[]" class="form-control" value="" id="frequency_'+count+'"></td>';
        htmlRows += '<td><select class="form-control" name="mandatory[]" id="mandatory_'+count+'"><option value="0"> NO</option><option value="1"> Yes</option></select></td>';
        htmlRows += '<td><input type="hidden" value="0" id="delete_value_'+count+'" ><button class="btn btn-warning" id="'+count+'" type="button" onclick="deletestatickRows(this.id)">Delete</button></td>'; 
        
        htmlRows += '</tr>';
        $('#invoiceItem123').append(htmlRows);
	}
	function submitrom(){
		//alert("ravi");
		//var arr = [];
		/*arr["name"]= new Array();
		arr["descrption"]= new Array();
		arr["short_code"]= new Array();
		arr["frequency"]= new Array();
		arr["mandatory"]= new Array();*/
		//arr['kpi_id'] = $('#kpi_id').val();
		var str = 'kpi_id='+$('#kpi_id').val();
		var i=0;
		$("[id^='name_']").each(function() {
        var id = $(this).attr('id');
        id = id.replace("name_",'');
		alert(id);
		/*arr['name'][i] = $('#name_'+id).val();
		arr['descrption'][i] = $('#descrption_'+id).val();
		arr['short_code'][i] = $('#short_code_'+id).val();
		arr['frequency'][i] = $('#frequency_'+id).val();
		arr['mandatory'][i] = $('#mandatory_'+id).val();*/
		i++;
		str += '&name_'+id+'='+$('#name_'+id).val()+'&descrption_'+id+'='+$('#descrption_'+id).val()+'&short_code_'+id+'='+$('#short_code_'+id).val()+'&frequency_'+id+'='+$('#frequency_'+id).val()+'&mandatory_'+id+'='+$('#mandatory_'+id).val();
	  	});
	  	str += '&count='+i;
		console.log(str);
	  /*	console.log(arr.toString());
  		var myJSON1 = JSON.stringify(arr);
  		console.log(myJSON1);*/
	    $.ajax({
	        url: "ajax.php",
	        type: "POST",
	        data: 'page=task_submit&data'+str,
	        success: function(response) {
	        	console.log(response);
	            //$('.success').html(response);
	            swal.fire({              
							title: "Record has been saved successfully ",              
							text: "Do you want to close ? ",              
							type: "success",           
							buttonsStyling: false,
							confirmButtonText: "<i class='la la-hand-o-up'></i>Yes, close!",    
							confirmButtonClass: "btn btn-danger",
							showCancelButton: true,     
							cancelButtonText: "<i class='la la-hand-o-right'></i> No, continue",      
							cancelButtonClass: "btn btn-default"        
						}).then(function(result) {
							if (result.value) {
								$('#kt_modal_KTDatatable_remote').modal('hide');
							}
						}); 

	        }
	    });
	}
</script>

<div class="modal-header">
						<h5 class="modal-title">
							Goals		
							<small>&nbsp;&nbsp; <b>Table: </b>&nbsp; <?php echo $table; ?> <b> &nbsp;&nbsp; Field: </b>&nbsp; <?php echo $field; ?> </small>
						</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body" > 
						<div class="row align-items-center" style="margin-top: 10px;padding: 3px;">
								<div class="col-lg-12">
									<table class="table table-striped- table-bordered table-hover table-checkable  no-footer" style="color: #646c9a;" id="invoiceItem123">	
											<tr>
												<th class="" width="25%">Indicator Name</th>
												<th class="thheader" width="30%">Weightage</th>
												<th class="thheader" width="15%">model</th>
												<th class="thheader" width="15%">Frequency</th>
												<th class="thheader" width="15%">traget</th>
												<th class="thheader" width="5%"></th>
											</tr>
											<?php /*if(count((array)$tr)>0){
												$count = 1;
												foreach ($tr as $td) {*/ //print_r($td); ?> 
												

												<tr class="itemRow123" id="qur_div_<?php echo $count; ?>"  >
													<td>
														<input type="text" name="name[]" class="form-control" value="" id="name_1">
													</td>
													<td>
														<textarea class="form-control" name="descrption[]" id="descrption_1"></textarea>
													</td>
													<td>
														<input type="text" name="short_code[]" class="form-control" value="" id="short_code_1">
													</td>
													<td>
														<input type="text" name="frequency[]" class="form-control" value="" id="frequency_1">
													</td>
													<td>
														<select class="form-control" name="mandatory[]" id="mandatory_1">
															<option value="0"> NO</option>
															<option value="1"> Yes</option>
														</select>
													</td>
													<td>
														<input type="hidden" value="0" id="delete_value_<?php echo $count; ?>" >
														<button class="btn btn-warning" id="<?php echo $count; ?>" type="button" onclick="deletestatickRows(this.id)">Delete</button>
													</td>
												</tr>
											<?php/* $count++;  } }*/ ?>		
									</table>
									<div class="row align-items-center" style="margin-top: 10px;margin-left: 2px;">
								<!-- <button class="btn btn-danger delete" id="removeRows" type="button">- Delete</button>&nbsp;&nbsp; -->
								<button class="btn btn-success" id="addRows" type="button" onclick="addmoreRows()">+ Add More</button>&nbsp;&nbsp;
						
							</div>
								</div>
				<!--end: Search Form -->
			</div>
		</div>
			<div class="modal-footer">
				<?php print_r($_POST); ?>
				<input type="hidden" name="kpi_id" id="kpi_id" value="<?php echo $_POST['id']; ?>">
				<input type="hidden" name="page" value="dis_submit">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" onclick="submitrom()" >Submit</button>
			</div>
<!-- </div> -->
	</form>
</div>
<!-- local datatable modal -->

<?php 
if(isset($_POST['submit1'])){
	//echo "<pre>"; print_r($_POST); exit;
	if($_POST['submit1'] == 'Add'){
	    $_POST['created_by'] = $_SESSION['userid'];
	    $_POST['created_date'] = nowDate();
		$_POST['current_status'] = $_SESSION['current_status'];
		$_POST['is_active'] = '0';
	  //echo "<pre>";print_r($_FILES); print_r($_POST);  exit;
	    $url = API."?mode=addkpidata&method=post";
	    echo $response = file_post_contents($url, $_POST, 'insert');
	     if ($response) {
	       // header("location:index.php?option=".$_GET['option']."&role=".$_GET['role']."&version=".$_GET['version']);
	    }
	}
	if($_POST['submit1'] == 'Update'){
   		$_POST['created_by'] = $_SESSION['userid'];
	    $_POST['created_date'] = nowDate();
    	$_POST['type'] = "update";
    	$url = API."?mode=addkpidata&method=post";
	    echo $response = file_post_contents($url, $_POST, 'insert');
	    if ($response) {
	        //header("location:index.php?option=".$_GET['option']."&role=".$_GET['role']."&version=".$_GET['version']);
	    }
    }

}
$modeleurl =  API."?mode=gettabledetails&fid=genc_code&rid=KPIMODAL&table=".TBL_GENRAL_CODE_DETAILS;
$modellist = callAPI($modeleurl);
$modellist = json_decode($modellist,1);

$result_url =  API."?mode=gettabledetails&table=key_result_areas";
$resultlist = callAPI($result_url);
$resultlists = json_decode($resultlist,1);

$role_url =  API."?mode=gettabledetails&table=job_titles";
$rolelist = callAPI($role_url);
$rolelist = json_decode($rolelist,1);

$uomurl =  API."?mode=gettabledetails&fid=genc_code&rid=KPIUOM&table=".TBL_GENRAL_CODE_DETAILS;
$upmllist = callAPI($uomurl);
$upmllist = json_decode($upmllist,1);
$vers = array();
$rid =2;
if($_GET['role']!="" && $_GET['version']!=""){
	$role_id =$_GET['role'] ;
	$version =$_GET['version'];
	$data1["where"] = array('company_id'=> $_SESSION['company_id'],'role_id'=> $role_id);
	$data = urlencode(json_encode($data1));
	$curl = API."?mode=gettabledetailswithconditions&table=key_performance_indicators&data=".$data; 
	$cudata = callAPI($curl);
	$cudata = json_decode($cudata,1);
	//print_r($data1);
	$vers=array(); $newversion =0;
	foreach ($cudata as  $data) {
		if(!in_array($data['version_no'], (array)$versdd)){
			$versdd[] = $data['version_no'];
			$vers[$data['version_no']]="Version ".$data['version_no'];
			$newversion = $data['version_no'];
		}
	}
	$newversion = $newversion+1;
	$vers[$newversion] = "New Version";
	//echo "<pre>"; print_r($vers);
	$result ="";
	$data11["where"] = array('company_id'=> $_SESSION['company_id'],'role_id'=> $role_id,'version_no'=>$version,'is_active'=>0);
	$datw = urlencode(json_encode($data11));
	$curl1 = API."?mode=gettabledetailswithconditions&table=key_performance_indicators&data=".$datw; 
	$cudata1 = callAPI($curl1);
	$result = json_decode($cudata1,1);
	if(count((array)$result)>0){
		$rid = 1;
		$result = $result;
	}else{
		$rid = 0;
		$result = array(array('weightage'=>0,'id'=>'0'));
	}
	//print_r($result); exit;
}
?>
<script type="text/javascript">
	function goalpopup(){
		swal.fire({              
			title: "<?php echo $_SESSION['error_message']['9009']; ?>",              
			//text: "Do you want to go ? ",              
			type: "success",           
			buttonsStyling: false,
			confirmButtonText: "<i class='la la-hand-o-up'></i>Yes Create",    
			confirmButtonClass: "btn btn-primary",
			showCancelButton: true,     
			cancelButtonText: "<i class='la la-hand-o-right'></i> No Stop",      
			cancelButtonClass: "btn btn-danger"        
		}).then(function(result) {
			if (result.value) {
				//alert("ravi");
				var assemnt_year = $('#assemnt_year').val();
				var role_id = $('#role_id').val();
				var version = $('#version_id').val();
				//alert(assemnt_year);
				jQuery.ajax({
					url: "ajax.php",
					data:'page=goalscheckandadd&assemnt_year='+assemnt_year+'&role_id='+role_id+'&version='+version,
					type: "POST",
					success:function(data){
						console.log(data);
						swal.fire({              
						title: "<?php echo $_SESSION['error_message']['9010']; ?>",              
						//text: "Do you want to close ? ",              
						type: "success",           
						buttonsStyling: false,
						confirmButtonText: "<i class='la la-hand-o-up'></i>Yes",    
						confirmButtonClass: "btn btn-primary",
						showCancelButton: true,     
						cancelButtonText: "<i class='la la-hand-o-right'></i> No",      
						cancelButtonClass: "btn btn-danger"        
						}).then(function(result) {
						if (result.value) {
							//alert("window");
							window.location.href = 'index.php?option=COM_INSERT_ORGANIZATION_GOALS&role='+role_id+'&assemnt_year='+assemnt_year;
						}
						}); 
					
					},
					error:function (){}
				});
			}
		}); 
		/**/
	}
	function taskpopup(id){
		//alert(id);
		jQuery.ajax({
			url: "views/custom_task_popup.php",
			data:'page=dis_att_type&id='+id,
			type: "POST",
			success:function(data){
				$('#popup1').html(data);
				//$('#kt_modal_KTDatatable_remote').modal({ show: true});
				$("#kt_modal_KTDatatable_remote").modal("show");
			},
			error:function (){}
		});

	}
	function changeroleid(val){
		 $.ajax({
        url: "ajax.php",
        data:'page=changekpiroleid&id='+val,
        type: "POST",
        success:function(data){
            console.log(data);
            document.getElementById('version_id').innerHTML = data;
        },
        error:function (){}
        });
	}
	function changepageview(val){
		var role = document.getElementById('role_id').value;
        window.location.href = 'index.php?option=COM_INSERT_KEY_PERFORMANCE_INDICATORS&role='+role+'&version='+val;
	}
	function weightagecheck(){
		var sum=0;
		$("[id^='weightage_']").each(function() {
			var id = $(this).attr('id');
       		 id = id.replace("weightage_",'');
       		var value = $('#weightage_'+id).val();
       		//alert("ID:"+id+"Value:"+value);
       		sum = parseFloat(sum)+ parseFloat(value);
       		//alert("sum:"+sum);
		});
		//alert("sum:"+sum);
		var  rid = $('#rid').val();
		$('#dis_Total').val(parseFloat(sum).toFixed(2)); 
		//alert(rid);
		if(rid==1){
			var sav = "activebutton1";
		}else{
			var sav = "activebutton";
		}
		//alert(sum +"- "+rid);
		if(sum == 100){
			//alert('ravi');
			//document.getElementById("activebutton").disabled = false;
			document.getElementById(sav).disabled = false;
		}else{
			//alert('ravi');
			//document.getElementById("activebutton").disabled = true;		
			document.getElementById(sav).disabled = true;		
		}
		
	}
	 function addrows(){
        var count = $(".itemRow").length;
         count++;
        var htmlRows = '';
        htmlRows += '<tr>'; 
        htmlRows += '<td><div class="checkbox-fade fade-in-primary"><label class="form-label"><input class="form-control itemRow" type="checkbox"> <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span> </label></div></td> ';
        htmlRows += '<td><select class="form-control form-select js-example-basic-single"  name= "result[]"> <option value="0">Select Any One</option><?php foreach ($resultlists as  $resultlist) { ?><option value="<?php echo $resultlist['id']; ?>"><?php echo $resultlist['area_name']; ?></option>	<?php } ?></select><input type="hidden" name="kpi_id[]" value="0"></td>';     
        htmlRows += '<td><input type="text" name="name[]"  class="form-control"   autocomplete="off"> </td>';   

        htmlRows += '<td><select class="form-control form-select js-example-basic-single" name="model[]" ><option value="0">Select Any One</option><?php foreach ($modellist as  $model) { ?><option value="<?php echo $model['genc_detail_code']; ?>"><?php echo $model['genc_detail_name']; ?></option><?php } ?></select></td>';    
        
        htmlRows += '<td><select class="form-control form-select js-example-basic-single" name="uom[]" ><option value="0">Select Any One</option><?php foreach ($upmllist as  $uom) { ?><option value="<?php echo $uom['genc_detail_code']; ?>"><?php echo $uom['genc_detail_name']; ?></option><?php } ?></select></td>';
  
           htmlRows += '<td><input type="text" name="weightage[]" id="weightage_'+count+'"  onblur="weightagecheck(this.id)"  class="form-control amount" value="0" autocomplete="off"> </td>'; 
        
        htmlRows += '</tr>';
        $('#invoiceItem').append(htmlRows);
		$(".js-example-basic-single").select2();
        $(".js-example-disabled-results").select2();
        $( ".datepicker" ).datepicker({
            showOn: "both", 
            buttonText: "<i class='ti-calendar'></i>",
            dateFormat: date_format,
            changeMonth: true,
            changeYear: true
        });
    }
    function removerows(){
          $(".itemRow:checked").each(function() {
            console.log(this);
            $(this).closest('tr').remove();
           // weightagecheck();
        });
        $('#checkAll').prop('checked', false);
    }
</script>
<div class="d-flex flex-column-fluid">
	<div class="container-fluid">
		<?php if($content !='' && $_POST['option']=='' && $call_out_side ==""){ ?>
	<!-- 	<div class="alert alert-custom alert-white shadow-lg fade show mb-5" role="alert">
	        <div class="alert-icon"><i class="flaticon-warning kt-font-brand"></i></div>
	        <div class="alert-text"><?php echo $content; ?></div>
	        <div class="alert-close">
	            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
	                <span aria-hidden="true"><i class="ki ki-close"></i></span>
	            </button>
	        </div>
    	</div> -->
    	 <div class="alert alert-warning alert-dismissible">
      <strong><?php echo $content; ?></strong>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
    <?php } ?>
    	<div class="row">
			<div class="col-lg-12">
				<form class="kt-form" id="kt_form" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">
				<div class="card card-custom" id="kt_page_sticky_card">
					<!-- <div class="card-header">
						<div class="card-title">
							<span class="kt-portlet__head-icon">
										<i class="kt-font-brand"></i>
									</span>
							<h3 class="card-label">&nbsp;<?php echo "Key Perormance Indicators";  ?> </h3>
						</div>
						<div class="card-toolbar">
							<a href="javascript:history.go(-1)" class="btn btn-light-primary font-weight-bolder mr-2">
								<i class="ki ki-long-arrow-back icon-xs"></i>Back
							</a>
						</div>
					</div> -->
<div class="card-header">
    <h5>Key Perormance Indicators</h5>
    <div class="card-header-right">
           <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
              <i class="icofont icofont-reply"></i>
              Back
            </a>
    </div>
</div>


					<div class="card-body">
						<div class="form-group row"> 
      						<label class="col-lg-2 col-form-label text-lg-right">Role:</label>
		      				<div class="col-lg-3">
								<select class="form-control form-select js-example-basic-single" name="role_id" id="role_id"  onchange="changeroleid(this.value)">
									<option value="0">Select Any One</option>
									<?php foreach ($rolelist as  $role) { ?>
										<option <?php if($role_id == $role['id']){ ?> selected <?php } ?> value="<?php echo $role['id']; ?>"  ><?php echo $role	['job_title']; ?></option>
									<?php } ?>
								</select>
							</div>
							<label class="col-lg-2 col-form-label text-lg-right">Version:</label>
							<div class="col-lg-3">
							      <select class="form-control form-select js-example-basic-single" name="version_no" id="version_id" onchange="changepageview(this.value)" >
					                    <option value=""> Select Any one </option>
					                    <?php if($version!=""){ 
					                    	foreach($vers as $key=>$ver){?>
					                    		<option <?php if($key == $version){ ?> selected <?php } ?> value="<?php echo $key; ?>"> <?php echo $ver; ?></option>
					                    <?php } } ?>
							    </select>
							    &nbsp; 

							</div>
						</div>
						<?php if($rid !=2) {?>
						<?php if($rid !=0){ ?>
						<div class="form-group row"> 
      						<label class="col-lg-2 col-form-label text-lg-right">Goals:</label>
		      				<div class="col-lg-2 float-left" >
								<select class="form-control form-select js-example-basic-single" name="assemnt_year" id="assemnt_year" >
									<option value="0">Select Any One</option>
									<?php for ($i=2020; $i<2030;$i++) { 
										$k= $i+1; ?>
										<option  value="<?php echo $i."-".$k ?>"  ><?php echo $i." - ".$k ?></option>
									<?php  } ?>
								</select>

							</div>
							<div class="col-lg-4 float-left" >
								<a href="#"  id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2 float-left" onclick="goalpopup()" >Genrate Organization Goals </a>
							</div>
						</div>
					<?php } ?>
						
						<table class="table table-striped- table-bordered table-hover1 table-checkable dataTable no-footer " id="invoiceItem"> 
							<tr>
								<th  width="2%"></th>
							    <th class="text-center" width="15%">Key Result</th>
							    <th class="text-center" width="20%">Indicator Name</th>
							    <th class="text-center" width="10%">Model </th>
							    <th class="text-center" width="10%">UoM </th>
							   <!--  <th class="thheader" width="15%">Role </th>   -->
							    <th class="text-center" width="15%">Weightage </th>	
							    <?php if($rid !=0){ ?>
							    <th class="text-center" width="5%">Tasks </th>	
							   <!--  <th class="thheader" width="15%">Goal </th>  -->
							    <?php } ?>                   
							    
							</tr>
							<?php $i=1; foreach ($result as $res) { ?>
							<tr>
								 <td>
      								<div class="checkbox-fade fade-in-primary">
  <label class="form-label">
  	<input class="form-control itemRow" type="checkbox">   <span class="cr">
      <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
    </span> </label></div>
   								</td>
								<td>
									<select class="form-control form-select js-example-basic-single" name="result[]" >
										<option value="0">Select Any One</option>
										<?php foreach ($resultlists as  $resultlist) { ?>
											<option  <?php if($res['key_reslut_id'] == $resultlist['id']){ ?> selected <?php } ?>  value="<?php echo $resultlist['id']; ?>"><?php echo $resultlist['area_name']; ?></option>
										<?php } ?>
									</select>
									<input type="hidden" name="kpi_id[]" value="<?php echo $res['id']; ?>">
								</td>
								<td>
									 <input type="text" name="name[]"  class="form-control" value="<?php echo $res['indicator_name']; ?>"  autocomplete="off"> 
								</td>
								<td>
									<select class="form-control form-select js-example-basic-single" name="model[]" >
										<option value="0">Select Any One</option>
										<?php foreach ($modellist as  $model) { ?>
											<option <?php if($res['model'] == $model['genc_detail_code']){ ?> selected <?php } ?>  value="<?php echo $model['genc_detail_code']; ?>"><?php echo $model['genc_detail_name']; ?></option>
										<?php } ?>

									</select>
								</td>
								<td>
									<select class="form-control form-select js-example-basic-single" name="uom[]" >
										<option value="0">Select Any One</option>
										<?php foreach ($upmllist as  $uom) { ?>
											<option <?php if($res['uom'] == $uom['genc_detail_code']){ ?> selected <?php } ?> value="<?php echo $uom['genc_detail_code']; ?>"><?php echo $uom['genc_detail_name']; ?></option>
										<?php } ?>

									</select>

								</td>
								
								<td>
									 <input type="text" name="weightage[]"  id="weightage_<?php echo $i; ?>" onblur="weightagecheck(this.id)"  class="form-control amount" value="<?php echo $res['weightage']; ?>"  autocomplete="off"> 
								</td>
								<?php if($rid !=0){ ?>
								<td class="text-center">

									<a href="#"  id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2" onclick="taskpopup(<?php echo $res['id']; ?>)" >Tasks</a>
								</td>
								<!-- <td>
									<a href="#"  id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2" onclick="goalpopup(<?php echo $res['id']; ?>)" >Goals</a>
								</td -->
								<?php } ?>
								<!-- <td>
									<button></button>
								</td> -->
							</tr>

						<?php $i++; $total = $total+$res['weightage']; } ?>
						</table>
						<div class="row">
					        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
					            <button class="btn btn-danger delete" id="removeRows" onclick="removerows()"  type="button">- Delete</button>
					            <button class="btn btn-success"  onclick="addrows()" id="addRows" type="button">+ Add More</button>
					        </div>
					    </div>
					    <div style="float: left;width: 50%">
					    	&nbsp;
					    </div>
					    <div style="float: left;width: 50%">	
					    <table class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer" >
							<tr>
							    <th class="amount"><span>Total</span></th>
							    <td width="40%"><span><input  type="text" class="form-control amount" readonly value="<?php echo $total; ?>"  id="dis_Total"  >
							    </span></td>
							    <td  width="15%"></td>
							</tr>
						</table>
						</div>
					</div>
				</div>
				<div class="card-footer">
					<div class="col-lg-4">
			             <input type="hidden" id="table_name" name="table_name" value=<?php echo $table_name; ?> />
			             <input type="hidden" id="user_id" name="user_id" value=<?php echo $_SESSION['userid']; ?> />
			             <input type="hidden" id="now_date" name="now_date" value="<?php echo date("Y-m-d H:i:s"); ?>" />
			             <input type="hidden" name="rid" id="rid" value="<?php echo $rid;?>">

			             <?php  $disable = "disabled"; 	if($total == 100) { $disable = ""; } ?>
			            <?php if( $rid !='' ){ ?>
			                 
			                    <input type="hidden" name="submit1" value="Update" />
			                    <button <?php echo $disable; ?>  id="activebutton1" class="btn btn-primary mr-2" ><i class="la la-check"></i>Upadate</button>
			                    <button type="reset" class="btn btn-secondary" ><i class="la la-close"></i>Cancel</button>
			            <?php } else{ ?>
			                    <input type="hidden" name="submit1" value="Add" />
			                    <button class="btn btn-primary mr-2"  id="activebutton" <?php echo $disable; ?>  name="submit_value" value="exit"><i class="la la-check"></i>Save & exit</button>
			                    <button type="reset" class="btn btn-secondary" ><i class="la la-close"></i>Cancel</button>
			            <?php } ?>  
			        </div>
				</div>
			<?php }?>
			</form>
			</div>
		</div>
    </div>
</div>

<div id="kt_modal_KTDatatable_remote" class="modal fade" role="dialog" aria-hidden="true">
			<div class="modal-dialog modal-xl">
				<div class="modal-content" style="min-height: 590px;" id="popup1">
					
				</div>
			</div>

		</div>
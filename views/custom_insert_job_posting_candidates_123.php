<?php
	if(isset($_POST['submit1'])){
		if($_POST['submit1'] == 'Add'){
		    $_POST['created_by'] = $_SESSION['userid'];
		    $_POST['created_date'] = nowDate();
		   //echo "<pre>";print_r($_FILES); print_r($_POST);  exit;
		    $url = API."?mode=addjobpostingapplicatantdata&method=post";
		    $response = file_post_contents($url, $_POST, 'insert');
		  // echo $response; exit;
		     if ($response) {
		        header("location:index.php?option=".$option."&Rid=".deocdeId($nrid));
		    }
		}
	}

	$jobpost = $_GET['job'];
	$reswrdata["where"] = array('company_id'=> $_SESSION['company_id'],'job_posting_id'=> $jobpost,'is_active'=>0);
	$rescondata = urlencode(json_encode($reswrdata));
	$resurll = API."?mode=gettabledetailswithconditions&table=job_posting_candidates&data=".$rescondata; 
	$resdata = callAPI($resurll);
	$resdata = json_decode($resdata,1);


	$jobcode["where"] = array('company_id'=> $_SESSION['company_id'],'is_active'=>0);
	$jobcodejson = urlencode(json_encode($jobcode));
	$jobcodeurl = API."?mode=gettabledetailswithconditions&table=job_titles&data=".$jobcodejson; 
//	echo $jobcodeurl;
	$jobcodedata = callAPI($jobcodeurl);
	$jobcodedata = json_decode($jobcodedata,1);

    foreach ($jobcodedata as $codedata) {
    	$code[$codedata['id']] =  $codedata['job_title'];
    }
	    //print_r($code);
	    $data11["where"] = array('company_id'=> $_SESSION['company_id'],'is_active'=>0);
		$datw = urlencode(json_encode($data11));
		$curl1 = API."?mode=gettabledetailswithconditions&table=job_postings&data=".$datw; 
		$cudata1 = callAPI($curl1);
		$result = json_decode($cudata1,1);

	if($jobpost  !=''){
		foreach ($result as $checkvalue) {
			if($checkvalue['id'] == $jobpost){
				$processdata = $checkvalue;
				break;
			}
		}
		$datass["where"] = array('company_id'=> $_SESSION['company_id'],'is_active'=>0,'process_id'=>$processdata['job_selection_process']);
		$datasw = urlencode(json_encode($datass));
		$curlss = API."?mode=gettabledetailswithconditions&table=job_selection_process_steps&data=".$datasw; 
		$spdata = callAPI($curlss);
		$spdata = json_decode($spdata,1);
	}
//	echo "<pre>"; print_r($result);print_r($datass);
	
?>
<script type="text/javascript">
	function changepageview(val){
		//alert("ravi");
		//var role = document.getElementById('role_id').value;
        alert(val);
        window.location.href = 'index.php?option=COM_INSERT_JOB_POSTING_CANDIDATES&job='+val;
	}
	function addrows(){
	    //KTBootstrapDatepicker.init();
        var count = $(".itemRow").length;
         count++;
        var htmlRows = '';
        htmlRows += '<tr>'; 
        htmlRows += '<td><input type="text" class="form-control" name="name[]" value=""></td> ';
        htmlRows += '<td><input type="text" class="form-control" name="email[]" value=""> </td>';   
        htmlRows += '<td><input type="text" class="form-control" name="phone[]" value=""></td>';    
        htmlRows += '<td><select class="form-control" name="process[]" ><option value="0">Select Any One</option>	<?php foreach ($spdata as  $sp) { ?><option  value="<?php echo $sp[id]; ?>"><?php echo $sp[select_name]; ?></option><?php } ?></select></td>';
         htmlRows += '<td><textarea class="form-control" name="remarks[]" ></textarea> </td>'; 
        htmlRows += '<td><select class="form-control" name="status[]"><option value="0">Select Any One</option></select></td>';
         htmlRows += '<td><a herf="#" class="btn btn-primary mr-2" disabled data-container="body" data-toggle="popover" data-placement="top" data-content="Create Candidate Details" > Genrate </a></td>';        
        htmlRows += '</tr>';
        $('#invoiceItem').append(htmlRows);
    }
    function genrateregistation(name,email,phone){
    	//alert("ravi");
    	//alert(name+'//'+phone+'//'+email);
    	swal.fire({              
			//title: "Do you want to Genrate auto candidaite registation",              
			text: "Do you want to Genrate auto candidaite registation ",              
			type: "success",           
			buttonsStyling: false,
			confirmButtonText: "<i class='la la-hand-o-up'></i>Yes, close!",    
			confirmButtonClass: "btn btn-primary",
			showCancelButton: true,     
			cancelButtonText: "<i class='la la-hand-o-right'></i> No, continue",      
			cancelButtonClass: "btn btn-danger"        
		}).then(function(result) {
			if (result.value) {
				//alert("ravi");
				jQuery.ajax({
					url: "ajax.php",
					data:'page=candidateautoregistation&display_name='+name+'&user_mobile='+phone+'&user_email='+email,
					type: "POST",
					success:function(data){
						console.log(data);	
						data =  JSON.parse(data);
						console.log(data.status);
						var mms = "Candidate registation is success"; 
						if(data.status == 'fail'){
							 mms = "Email is already exists"
						}
						swal.fire({              
						//title: "You want to go goal page",              
						text: mms,              
						type: "success",           
						buttonsStyling: false,
						confirmButtonText: "<i class='la la-hand-o-up'></i>Yes, close!",    
						confirmButtonClass: "btn btn-primary",
						showCancelButton: false,     
						cancelButtonText: "<i class='la la-hand-o-right'></i> No, continue",      
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
    }
</script>
<div class="d-flex flex-column-fluid">
	<div class="container-fluid">
		<?php if($content !='' && $_POST['option']=='' && $call_out_side ==""){ ?>
		<div class="alert alert-custom alert-white shadow-lg fade show mb-5" role="alert">
	        <div class="alert-icon"><i class="flaticon-warning kt-font-brand"></i></div>
	        <div class="alert-text"><?php echo $content; ?></div>
	        <div class="alert-close">
	            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
	                <span aria-hidden="true"><i class="ki ki-close"></i></span>
	            </button>
	        </div>
    	</div>
   		<?php } ?>
    	<div class="row">
			<div class="col-lg-12">
				<form class="kt-form" id="kt_form" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">
				<div class="card card-custom" id="kt_page_sticky_card">
					<div class="card-header">
						<div class="card-title">
							<span class="kt-portlet__head-icon">
										<i class="kt-font-brand"></i>
									</span>
							<h3 class="card-label">&nbsp;<?php echo "Job Posting Applicants";  ?> </h3>
						</div>
						<div class="card-toolbar">
							<a href="javascript:history.go(-1)" class="btn btn-light-primary font-weight-bolder mr-2">
								<i class="ki ki-long-arrow-back icon-xs"></i>Back
							</a>
						</div>
					</div>
					<div class="card-body">
						<div class="form-group row"> 
      						<label class="col-lg-2 col-form-label text-lg-right">Job Posting:</label>
		      				<div class="col-lg-3">
								<select class="form-control" name="job_posting" id="job_posting"  onchange="changepageview(this.value)">
									<option value="0">Select Any One</option>
									<?php foreach ($result as  $res) { ?>
										<option <?php if($jobpost == $res['id']){ ?> selected <?php } ?> value="<?php echo $res['id']; ?>"  ><?php echo $res['job_number']." - ".$code[$res['job_code']]; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<?php if($jobpost !="") {?>
						
						
						<table class="table table-striped- table-bordered table-hover1 table-checkable dataTable no-footer " id="invoiceItem"> 
							<tr>
							    <th class="" width="15%"> Candidate Name </th>
							    <th class="" width="20%"> Email </th>
							    <th class="thheader" width="10%"> Phone </th>
							    <th class="thheader" width="10%"> Selection Process</th>
							   <th class="thheader" width="15%"> Remarks</th>
							    <th class="thheader" width="15%"> Status</th>
							    <th class="thheader" width="5%">Genrate </th>	            
							</tr>
							<?php $i=1; foreach ($resdata as $res) { ?>
							<tr>
								 <td>
								 	<input type="hidden" class="form-control" name="id[]" value="<?php  echo $res['id']; ?>">
      								<input type="text" class="form-control" name="name[]" value="<?php  echo $res['candidate_name']; ?>">
   								</td>
   								<td>
      								<input type="text" class="form-control" name="email[]" value="<?php  echo $res['candidate_email']; ?>">
   								</td>
   								<td>
      								<input type="text" class="form-control" name="phone[]" value="<?php  echo $res['candidate_phone']; ?>">
   								</td>
								<td>
									<select class="form-control" name="process[]" >
										<option value="0">Select Any One</option>
										<?php foreach ($spdata as  $sp) { ?>
											<option  <?php if($res['job_selection_process_step_id'] == $sp['id']){ ?> selected <?php } ?>  value="<?php echo $sp['id']; ?>"><?php echo $sp['select_name']; ?></option>
										<?php } ?>

									</select>
								</td>
								<td>
      								<textarea class="form-control" name="remarks[]" ><?php echo $res['remarks']; ?></textarea>
   								</td>
								<td>
									<select class="form-control" name="status[]" >
										<option value="0">Select Any One</option>
										<?php foreach ($modellist as  $model) { ?>
											<option <?php if($res['model'] == $model['genc_detail_code']){ ?> selected <?php } ?>  value="<?php echo $model['genc_detail_code']; ?>"><?php echo $model['genc_detail_name']; ?></option>
										<?php } ?>

									</select>
								</td>
								<td>
									<a herf="#" class="btn btn-primary mr-2" data-container="body" data-toggle="popover" data-placement="top" data-content="Create Candidate Details" onclick="genrateregistation(<?php echo "'".$res['candidate_name']."'"; ?>,<?php echo "'".$res['candidate_email']."'"; ?>,<?php echo "'".$res['candidate_phone']."'"; ?>)" > Genrate </a>
								</td>
							</tr>

						<?php $i++; } ?>
						</table>
						<div class="row">
					        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
					            <button class="btn btn-danger delete" id="removeRows" onclick="removerows()"  type="button">- Delete</button>
					            <button class="btn btn-success"  onclick="addrows()" id="addRows" type="button">+ Add More</button>
					        </div>
					    </div>
					 
					</div>
				</div>
				<div class="card-footer">
					<div class="col-lg-4">
			             <input type="hidden" id="table_name" name="table_name" value=<?php echo $table_name; ?> />
			             <input type="hidden" id="user_id" name="user_id" value=<?php echo $_SESSION['userid']; ?> />
			             <input type="hidden" id="now_date" name="now_date" value="<?php echo date("Y-m-d H:i:s"); ?>" />
			             <input type="hidden" name="rid" id="rid" value="<?php echo $rid;?>">

			             <?php///$disable = "disabled"; 	if($total == 100) { $disable = ""; } ?>
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
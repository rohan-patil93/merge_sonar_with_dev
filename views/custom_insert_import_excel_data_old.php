<script type="text/javascript">
	function confirmdata(){
		location.reload();
	}
	function btnCancel(){
		window.location.href=window.location.href
	}
</script>
<div class="d-flex flex-column-fluid">
	<div class="container-fluid">
		<?php if($error_messages !="" ){ ?>
		     <div class="alert alert-danger alert-dismissible">
	            <strong><?php echo $error_messages; ?> </strong> 
	            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
	        </div>
		<?php } ?>
		<div class="card card-custom card-sticky " id="kt_page_sticky_card">
			<div class="card-header table-card-header">
				<h5><?php echo "Import";  ?></h5>
				<div class="card-header-right table-card-header ">
					<a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
						<i class="icofont icofont-reply"></i>
						Back
					</a>
				</div>
			</div>
		<?php  
		if(isset($_POST['submit_value']) && isset($_SESSION['import_excel_post']) && $_SESSION['import_excel_post'] == "S"){
			unset($_SESSION['import_excel_post']);
			$ext=explode(".",$_FILES["csv_file"]['name']);
			$file_name = $ext[0];
			$file_insert_name = 'attendance'.'_'.time().uniqid().'.'.$ext[1];
			$file_path='uploads/edi/'.$file_insert_name;
			//move_uploaded_file($_FILES["csv_file"]["tmp_name"],$file_path);
		    $csvFile = $_FILES['csv_file']['tmp_name'];
		   	$postarray['csvFile'] = $csvFile;
		   	$postarray['file_name'] = $file_insert_name;
			$importdata = array_map('str_getcsv', file($csvFile));
			foreach($importdata as $idata){
				$postarray['csvdata'][] =  $idata;				
			}
			$url = API."?mode=insertimportexceldata&method=post";
			$response1 = file_post_contents($url, $postarray, 'insert');
			$response = json_decode($response1,1);
			//echo "<pre>"; print_r($response); echo "<pre>";
		}elseif(isset($_POST['submit_value']) && $_POST['submit_value'] == "add" && isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK && pathinfo($_FILES['csv_file']['name'], PATHINFO_EXTENSION) == 'csv'){
			$_POST['submit_value'] = "UPDATE";
					$ext=explode(".",$_FILES["csv_file"]['name']);
				    $csvFile = $_FILES['csv_file']['tmp_name'];
				    $importdata = array_map('str_getcsv', file($csvFile));
				   // echo "<pre>"; print_r($importdata); echo "<pre>";
			       // $_SESSION['import_excel_post'] = "S";
				?>	
				<div class="alert alert-danger alert-dismissible">
		            <strong>Please Check Header</strong> 
		            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		        </div>
				<div class="card-body">	 	
					<table>
						<tbody>
							<?php $id = 1; foreach($importdata as $idata){ ?>
								<tr>
									<?php foreach($idata as $importda){ ?>
									<th class="p-2"><?php echo $importda; ?></th>
									<?php } ?>
									<?php if($id == 2){ echo "<th>Status</th>";}?>
								</tr>
							<?php $id++; } ?>
						</tbody>
					</table>
				</div>	
				<div class="card-footer">
					<div class="row">
						<div class="col-lg-6">
							<button type="button" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2 m-b-10" name="submit_value" value="upload" onclick="confirmdata()"><i class="la la-check"></i> Confirm</button>
							<button type="button" class="btn btn-danger btn-shadow-hover font-weight-bold mr-2 m-b-10" onclick="btnCancel()"><i class="la la-check"></i> Cancel</button>
						</div>
					</div>
				</div>
		<?php	}else{  unset($_SESSION['import_excel_post']);	?>
			<form method="post" enctype="multipart/form-data">
				<div class="card-body">	   
					<div class="row">	
						<div class="form-group col-lg-4">
							<label class="form-label">System Start Date <span class="text-danger">*</span></label>
							<input type="text" class="form-control" name="system_start_date" id="system_start_date" value="<?php echo $_SESSION['config']['system_start_date']; ?>" required="" readonly="">
						</div>					
						<div class="form-group col-lg-4">
							<label class="form-label">Import <span class="text-danger">*</span></label>
							<input type="file" class="form-control" name="csv_file" id="csv_file" required="">
						</div>
					</div>
				</div>
				<div class="card-footer">
					<div class="row">
						<div class="col-lg-6">
							<button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2 m-b-10" name="submit_value" value="add"><i class="la la-check"></i> Import</button>
						</div>
					</div>
				</div>
			</form>
			<?php } ?>
		</div>
	</div>
</div>	

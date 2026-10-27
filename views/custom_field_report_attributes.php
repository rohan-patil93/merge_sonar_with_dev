<?php
if (isset($_POST['submit'])){
	//echo "<pre>"; ///print_r($_POST); exit;
	$data1= array(); 	$data= array();
	foreach ($_POST['field'] as $postkey => $postvalue) {
		$data1[$postvalue]['lable'] = $_POST['lable'][$postkey];
		$data1[$postvalue]['display_list'] = $_POST['display_list'][$postkey];
		$data1[$postvalue]['display_postion'] = $_POST['display_postion'][$postkey];
		$data1[$postvalue]['filter'] = $_POST['filter'][$postkey];
		$data1[$postvalue]['filter_type'] = $_POST['type'][$postkey];
	}
	//print_r($data1); exit;
	$data_json = json_encode($data1);
	$data['attributes'] = $data_json;
	$data['help_text'] = $_POST['content'];
	if($_POST['id'] !=''){
			$url = API."?mode=updaterowtabledata&method=post&rid=".$_POST['id']."&table=".TBL_REPORT_ATTRIBUTES;
	} 	
	//echo $url; exit;
	$response = file_post_contents($url, $data);
	if ($response) {
		if ($_POST['submit_value'] == 'continue'){
				header("location:index.php?option=COM_LIST_REPORT_ATTRIBUTES&error=3001");
		}else{
				header("location:index.php?option=COM_LIST_REPORT_ATTRIBUTES&error=3001");
		}
		
	}

}
//$_GET['table'] = "accounts";
if((isset($_GET['table']) && $_GET['table'] !='') || $_GET['Rid']){
		//echo $_GET['Rid'];
		$rid= $_GET['Rid'];
		$report_id = $rid;
		$tableurl = API."?mode=singlerowtabledata&table=".TBL_REPORT_ATTRIBUTES."&rid=".$rid;
	  	//echo $tableurl; exit;
		$response = callAPI($tableurl);
		//echo $response; exit;
		$data = json_decode($response,1);
		//echo $data['content_details'];
		$content_details = json_decode($data['content_details'],1);
		//print_r($content_details);
		$tables =  $content_details['tables'];
		$tables_js = json_encode($tables);
		$attribute = json_decode($data['attributes'],1);
		//print_r($attribute); exit;
		$url = API."?mode=morefilednames&table=".json_encode($tables_js);
	//echo $url;
	$showtablesdata = callAPI($url);
	$showtables = json_decode($showtablesdata,1);
	//echo"<pre>"; print_r($showtables); exit;
	
}

$title = viewTitle($_GET['option']);
$data1["field"] = "id,report_name";
$data = urlencode(json_encode($data1));
$resurl = API."?mode=gettabledetailswithconditions&table=".TBL_REPORT_ATTRIBUTES."&data=".$data;
$res_data = callAPI($resurl);
$res_data = json_decode($res_data, 1);
 //print_r($res_data);
?>
<script type="text/javascript">
	function GetSelectedTextValue(key) {
    	//alert(key);
        window.location.href = 'index.php?option=COM_FIELD_REPORT_ATTRIBUTES&Rid='+key;
    }
    function Getinserttype(values,type){
		//var res = values.split("#");
		var report_id = $('#report_id').val();
		if(type == 'select'){
			jQuery.ajax({
				url: "views/custom_report_filter.php",
				data:'page=dis_att_type&report_id='+report_id+'&field='+values,
				type: "POST",
				success:function(data){
					$('#popup1').html(data);
					$('#kt_modal_KTDatatable_remote').modal({ show: true});
				},
				error:function (){}
			});			
		}
	}
</script>


<!---- Frist popup scrpit  --- >

<!----ecnd frist popup  ---->

<div class="d-flex flex-column-fluid">
<div class="container-fluid">
	<div class="">
			<!--begin::Portlet-->


			<div class="card card-custom gutter-b card-stretch">
				<div class="card-body">
					<div class="kt-portlet__head-label">
						<h3 class="kt-portlet__head-title">
							<div>
								<div style="float: left;">
									<label style="margin-top: 10px;"> <?php echo $title; ?><span class="requried">*</span> &nbsp;&nbsp; </label>
								<!-- <div class="col-lg-4" > </div> -->

				 			  </div>
				 			  <div style="float: left;">

							   <select class="form-control" id="kt_select2_41" name= "display_list"  onchange="GetSelectedTextValue(this.value)">
							   	<option> Select Any One</option>
							   			<?php foreach ($res_data as $key => $ress) {  ?>
							   				<option  <?php if($ress['id'] == $report_id){ ?> selected  <?php } ?> value = <?php echo $ress['id']; ?>   > <?php echo ucwords(str_replace('_',' ',$ress['report_name'])); ?> </option>
							   		<?php	} ?>
										
										
								</select>
							  </div>	
						</h3>
					</div>
				</div>
				<!--begin::Form-->
				<form class="kt-form kt-form--label-right card-body" action="" method="post">

					<div class="kt-portlet__body">
						<div class="form-group row">
							<div class="col-lg-3">
								<label>Filed </label>
							</div>
							<div class="col-lg-3">
								<label class="">Lable </label>
							</div>
							<div class="col-lg-1">
								<label class="">   Display</label>
							</div>
							<div class="col-lg-2">
								<label class=""> Postion </label>
							</div>
							<div class="col-lg-1">
								<label class=""> Filter </label>
							</div>
							<div class="col-lg-2">
								<label class=""> Filter Type </label>
							</div>
						</div>
						<input type="hidden" name="report_id" id='report_id'  value="<?php echo $report_id; ?>">
						<?php $i=10;
							foreach ($showtables as $key => $value) { 
								foreach ($value as  $field) {
									$keyf= $key.".".$field;
							 ?>
						<div class="form-group row">
							<div class="col-lg-3">
								<input type="text" class="form-control" name="field[]" value=<?php echo $key.".".$field;?> >
							</div>
							<div class="col-lg-3">
								<?php $lables =$attribute[$keyf]['lable'] !='' ? $attribute[$keyf]['lable'] : ucwords(str_replace('_', ' ', $field)); ?>
								<input type="text" class="form-control"  name="lable[]" value="<?php echo $lables; ?>">
							</div>
							<div class="col-lg-1">
								<?php $att_display_list =$attribute[$keyf]['display_list'] !='' ? $attribute[$keyf]['display_list'] : 0 ; ?>
									<select class="form-control" id="exampleSelect1" name= "display_list[]">
											<option value = "2" >No</option>
											<option value = "1" <?php if($att_display_list == '1') { ?> selected <?php } ?> >Yes</option>
									</select>
							</div>
							<div class="col-lg-2">
								<?php $att_display_postion =$attribute[$keyf]['display_postion'] !='' ? $attribute[$keyf]['display_postion'] : $i ; ?>
								<input type="text" class="form-control" name="display_postion[]" value=<?php echo $att_display_postion;?>>
							</div>
							<div class="col-lg-1">
								<?php $att_filter =$attribute[$keyf]['filter'] !='' ? $attribute[$keyf]['filter'] :0 ; ?>
									<select class="form-control" id="exampleSelect1" name= "filter[]">
											<option value = "2" >No</option>
											<option value = "1" <?php if($att_filter == '1') { ?> selected <?php } ?> >Yes</option>
									</select>
							</div>
							<div class="col-lg-2">
									<?php $filed_array = array('text' => 'Simple Text',  'date_pick' => 'Date', 'date_range' => 'Date Range', 'select'=> 'List');
									 $att_filter_type =$attribute[$keyf]['filter_type'] !='' ? $attribute[$keyf]['filter_type'] : 'text'; ?>
										<select class="form-control" id="<?php echo $key.".".$field;; ?>" onchange="Getinserttype(this.id,this.value)" name= "type[]">

											<?php foreach ($filed_array as $typekey => $typevalue) {?>
												<option value = "<?php echo $typekey; ?>" <?php if($att_filter_type == $typekey) { ?> selected <?php } ?>  ><?php echo $typevalue; ?></option>
											<?php } ?>
										</select>
							</div>
						</div>
						<?php $i=$i+10; } }?>
					</div>

					<div class="form-group  row">
							<label class="col-xl-3 col-lg-3 col-form-label">Content<span class="requried"></span></label>
							<div class="col-lg-9 col-xl-6">
								<div class="input-group">
									<div class="input-group-prepend"><span class="input-group-text"><i class="la la-user" aria-hidden="true"></i></span></div>
									<input class="form-control" type="text"   name="content" value="<?php echo $role['help_text']; ?>">
								</div>
							</div>
					</div>
					<div class="kt-portlet__foot">
						<div class="kt-form__actions">
							<div class="row">
								<div class="col-lg-6">
											<input type="hidden" name="submit" value="Add" />
											<input type="hidden" name="table_name" value="<?php echo $table; ?>" />
									<input type="hidden" name="id" value="<?php echo $rid; ?>" />
											<button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"   name="submit_value" value="exit"><i class="la la-check"></i>Save & exit</button>
											<button id="kt_login_signin_submit" class="btn btn btn-primary btn-shadow-hover font-weight-bold mr-2"   name="submit_value" value="continue"><i class="la la-check-circle"></i>Save & continue</button>
											<button type="reset" class="btn btn-light-primary font-weight-bolder mr-2" ><i class="la la-close"></i>Cancel</button>
									
								</div>
								<div class="col-lg-6 kt-align-right">
									<button type="reset" disabled="disabled" class="btn btn-danger float-right "><i class="la la-trash"></i>Delete</button>
								</div> 
							</div>
						</div>
					</div>
				</form>
				<!--end::Form-->
			</div>

			<!--end::Portlet-->

			<!--begin::Portlet-->
			</div>
		</div>
		<div id="kt_modal_KTDatatable_remote" class="modal fade" role="dialog" aria-hidden="true">
			<div class="modal-dialog modal-xl">
				<div class="modal-content" style="min-height: 590px;" id="popup1">
					
				</div>
			</div>

		</div>
			<!--end::Portlet-->
		</div>
	</div>
</div>
</div>

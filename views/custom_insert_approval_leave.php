<?php 
$_SESSION['apply_js']['calender'] = '1';
	//echo "Ravi".$_GET['Rid']."%%%";
	if($_GET['Rid']!=""){
		$rid =  encodeId($_GET['Rid']);
		//echo $rid ;
		$rid = explode(":",$rid);
		//print_r($rid);
		 $id = $rid['1'];
		// echo "id --".$id;
	}
	$table ="employee_leaves";
	$vdata1["where"] = array('is_active'=> 0);
    $vdata = urlencode(json_encode($vdata1));
    $vurl = API."?mode=gettabledetailswithconditions&table=leave_categories&data=".$vdata; 
    $configesponse = callAPI($vurl);
	$cate = json_decode($configesponse,1);

	$vdata1["where"] = array('is_active'=> 0,'id'=>$id);
    $vdata = urlencode(json_encode($vdata1));
    $vurl = API."?mode=gettabledetailswithconditions&table=employee_leaves&data=".$vdata; 
    $configesponse = callAPI($vurl);
	$empl = json_decode($configesponse,1);
	$empl = $empl['0'];
	//print_r($empl);
	$vdata1["where"] = array('is_active'=> 0,);
    $vdata = urlencode(json_encode($vdata1));
    $vurl = API."?mode=gettabledetailswithconditions&table=employees&data=".$vdata; 
    $configesponse = callAPI($vurl);
	$emplos = json_decode($configesponse,1);
	foreach ($emplos as  $value) {
		$empdata[$value['id']] =$value;
	}
	$emps = $empdata[$empl['employee_id']];
	$otstdate = date('Y-m-d', strtotime('-90 day', strtotime($empl['leave_from'])));
	$ottodate = date('Y-m-d', strtotime('+90 day', strtotime($empl['leave_to'])));
	//echo $otstdate."&".$ottodate;
	$evurl = API."?mode=tosearchempleavesbydate&start=".$otstdate."&end=".$ottodate."&type=REPORTTO"; 
	//echo $evurl; 
	$otherempurl = callAPI($evurl);
	$searchresult = json_decode($otherempurl,1);
	$all_leave_categories =  $searchresult['all_leave_categories'];	
	$otheremp =  $searchresult['employee_leaves'];	$stausresult = ViewStatusData();
	//print_r($otheremp); exit;
?>
<script type="text/javascript">
		function statuspopup(status,id){
      //alert("data");
     var table_name ="employee_leaves";
      jQuery.ajax({
            url: "views/custom_insert_status_popup.php",
            data:'table='+table_name+'&status='+status+'&rid='+id,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal({ show: true});
            },
            error:function (){}
        });
   }

	function displaystausdrop(id,table,status_id){
		//alert("id");
		$.ajax({
	        url: "ajax.php",
	        data:'page=current_status_drop&id='+id+'&current_status='+status_id+'&table='+table,
	        type: "POST",
	        success:function(data){
	        	console.log(data);
	            $("#dropdownlist").html(data);
	        },
	        error:function (){}
    	});
    	
	}
	
	function submitDynamicWorkflowPopUp(table_name,transaction_id,current_status,next_status){
	jQuery.ajax({
		url: "views/custom_insert_dynamic_status_popup.php",
		data:'table_name='+table_name+'&current_status='+current_status+'&transaction_id='+transaction_id+'&next_status='+next_status,
		type: "POST",
		success:function(data){
			$('#popup1').html(data);
			$('#kt_modal_KTDatatable_remote').modal("show");
		},
		error:function (){}
	});
}

function submitDynamicWorkflow(table_name,transaction_id,current_status,next_status,old_status_name,new_status_name){
	swal({
		title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
		text: "Please note the status of the document will change to "+new_status_name,
		type: "info",
		showCancelButton: true,
		confirmButtonClass: "btn-danger",
		confirmButtonText: "Yes",
		cancelButtonText: "No",
		closeOnConfirm: false
	},
	function(){
		console.log("dataa aa");
		jQuery.ajax({
			url: "ajax.php",
			data:'page=submitstatusdynamicworkflow&table_name='+table_name+'&current_status='+current_status+'&transaction_id='+transaction_id+'&next_status='+next_status,
			type: "POST",
			success:function(response){
				console.log(response);
				var res = JSON.parse(response);
                    //console.log(res);
                    window.location.reload();
                    if(res.transaction_update.status=='sucess'){
                    	window.location.reload();
                    	swal({
                    		title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                    		text: "<?php echo $_SESSION['error_message']['9059']; ?>",
                    		type: "success",
                    		showCancelButton: false,
                    		closeOnConfirm: false,
                    		showLoaderOnConfirm: true
                    	}, function () {
                    		window.location.reload();
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
 
</script>

<?php //echo "<pre>"; print_r(getPagenametotable('accounts','edit_page_name'));  //approval_page_name,edit_page_name,insert_page_name?>
<div class="d-flex flex-column-fluid j-content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<div class="card">
					<?php
				if($empl['id']!=""){ 
					$table = 'employee_leaves';	
					$workflowsteps = getWorkflowStatus($table,$empl['id'],$empl['current_status']);
					echo $workflowsteps['current_status_ribbon'];
				}
				?>
		            <div class="card-header table-card-header">
		              <h5><?php echo "Leave Request Approval";  ?></h5>
		              <div class="card-header-right table-card-header ">
							
									<?php  echo $workflowsteps['workflow_btn']; ?>
<a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
					              <i class="icofont icofont-reply"></i>
					              Back
					        </a>
						</div></div>
						<div class="row">
							<?php echo $workflowsteps['breadcrumb']; ?>
						</div>
						<div class="card-body">
							<form class="kt-form" id="kt_form" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">
							
							
							<div class="col-lg-12 mb-4">
								<div class="col-lg-12">
								    <div class="d-flex align-items-center">				
								    		<?php /** if($emps['image'] !=''){ ?>			
								    	<div class="symbol symbol-60 symbol-sm flex-shrink-0">								
								    		<img class="userimageround" src="uploads/all/<?php echo $emps['image']; ?>" alt="photo">				
										</div>	
									<?php }else{ ?>
										<div class="img-radius img-50 align-top m-r-15 text-center userimageround">
									        <span class="symbol-label font-size-h5"><?php echo initials($emps['display_name']); ?></span>
									    </div>		 
									    <?php }  **/?>	
 <?php if($emps['image'] !=''){ ?>	
   <div class=" m-r-15 text-center">
      <img src="uploads/all/<?php echo $emps['image']; ?>"   class="img-radius img-50 align-top m-r-15 mt-4" >
   </div>
   <?php }else{ ?>
   <span  alt="user image" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center" ><?php echo initials($emps['display_name']); ?></span>
   <?php } ?>
   
   

										
										<div class="mt-4">		
											<div class="text-dark-75 font-weight-bolder font-size-lg mb-0 f-16"><?php echo  viewTitle($emps['display_name']); ?></div>				
											<a href="#" class="sub-title text-muted font-weight-bold text-hover-primary f-14">
												<?php echo date('dS M Y',strtotime($empl['leave_from']))." To ".date('dS M Y',strtotime($empl['leave_to']))." (". $all_leave_categories[$empl['leave_type_id']]['name'].") Total Day(s) - ".number_format($empl['leave_days'],2); ?></a>
										</div>					
									</div>
								</div>

							</div>
							<div class="col-lg-12">
								<div class="col-lg-7" style="float: left;">
									    <div class="card card-custom">
									       <input type="hidden" name="calendar_cat" id="calendar_cat" value='11&data={"employee_id":<?php echo $empl['employee_id']; ?>}'>
									      <div class="card-body">
									        <div id="calendar"></div>
									      </div>
									    </div>
									  </div>
								
								<div class="col-lg-5 p-3" style="float: left;">
									<div class="card card-custom card-stretch gutter-b p-3">
										<div class="card-header table-card-header">
											<h5>Who Else Will Be Out</h5>
										</div>
										<?php foreach ($otheremp as $othemp) {
												if($othemp['id'] !=$empl['id']){
										 ?>
										<div class="card mt-2">
											<div class="card-body p-2">
												<div class="d-flex align-items-center">	
												<?php if($empdata[$othemp['employee_id']]['image'] !=''){ ?>			
												<div class="symbol symbol-60 symbol-sm flex-shrink-0">								
													<img class="img-radius img-50" src="uploads/all/<?php echo $empdata[$othemp['employee_id']]['image']; ?>" alt="photo">				
												</div>	
											<?php }else{ ?>
												<div class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center">
													<span class="symbol-label font-size-h5"><?php echo initials($empdata[$othemp['employee_id']]['display_name']); ?></span>
												</div>		
												<?php } ?>			
												<div class="ml-4">		
													<div class="text-dark-75 font-weight-bolder font-size-lg mb-0"><?php echo $empdata[$othemp['employee_id']]['display_name']; ?> <?php $statusVal = $stausresult[$othemp['current_status']];?>
											<label class="form-label badge  m-l-50 " style="background-color: <?php echo $statusVal['background_color'] ?>; color: <?php echo $statusVal['color'] ?>; "><?php echo $statusVal['name']; ?></label></div>				
													<a href="index.php?option=COM_INSERT_APPROVAL_LEAVE&Rid=<?php  echo deocdeId("id:".$othemp['id']); ?>" class="text-muted font-weight-bold text-hover-primary"> <?php echo date('dS M Y',strtotime($othemp['leave_from']))." To ".date('dS M Y',strtotime($othemp['leave_to']))." (". $all_leave_categories[$othemp['leave_type_id']]['name'].")"; ?> </a>
												</div>					
											</div>
											
											</div>
										</div>
										<?php  } 
										} ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</form>
			<div class="row">
	<?php 

echo $todisplaythree = todisplaythree(array('table'=>'employee_leaves','originator'=>$empl['created_by'],'encode'=>'no','id'=>$id,'dis_query'=>'yes','dis_timeline'=>'yes','dis_comments'=>'yes'));  
?>

</div>	
				
				
			</div>
		</div>
	</div>
</div>
<?php 
include('../includes/session.php');
include('../dbconfig.php');
include('../comm.php');
$vdata1["where"] = array('is_active'=> '0', 'assessment_year'=>$_REQUEST['assement_year'], 'document_list_id'=> $_REQUEST['list_id']);
$vdata1["field"] = "id, emp_id";
$vdata = urlencode(json_encode($vdata1));
$vurl = API."?mode=gettabledetailswithconditions&table=emp_assign_release_documents&data=".$vdata; 
$configesponse = callAPI($vurl);
$data = json_decode($configesponse,1);

foreach($data as $eemp){
	$aemp[$eemp['emp_id']] = $eemp['emp_id'];
}
$vdata1["where"] = array('is_active'=> '0');
$vdata1["where_in"] = $_SESSION['document'][$_POST['assement_year']][$_POST['list_id']];
$vdata1["whr_in_key"] = 'id';
$vdata1["field"] = "id, display_name, email_id,image, emp_code";
$vdata = urlencode(json_encode($vdata1));
$vurl = API."?mode=gettabledetailswithconditions&table=employees&data=".$vdata;
$configesponse = callAPI($vurl);
$empdata = json_decode($configesponse,1);
//echo "<pre> -----------------------"; 
	//print_r($_REQUEST);print_r($vdata1); print_r($empdata); echo "</pre>";
//print_r($aemp); echo "</pre>";
$month_type = 'N';
if(isset($_REQUEST['month'])){
	$month_type = $_REQUEST['month'];
}
?>
<script type="text/javascript">
	function assignemp(empid){
    var type = $("#type").val();
    var assement = $("#assement").val();
    var month_type = $("#month_type").val();
    var cc = "";
    if(month_type != 'N'){ 
       cc ="&month="+month_type;
    }
    console.log("type:"+type+",assement:"+assement);
    $.ajax({
      url: "ajax.php",
      data:'page=addsessiondocumenttypeyear&emp_id='+empid+'&list_id='+type+'&assement_year='+assement+'&work=work'+cc,
      type: "POST",
      success:function(data){
        console.log(data);
        //$("#dropdownlist_"+id).html(data);
        $("#row_"+empid).hide();
        
      },
      error:function (){}
    });
  }
  function submitform(){
  	swal({
		title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
		text: '<?php echo $_SESSION['error_message']['9121']; ?>',
		type: "warning",
		showCancelButton: true,
		confirmButtonClass: "btn-danger",
		confirmButtonText: "Yes",
		cancelButtonText: "No",
		closeOnConfirm: true,
		closeOnCancel: true
	},
	function(isConfirm) {
		if (isConfirm) {
			var type = $("#type").val();
		  var assement = $("#assement").val();
		  var month_type = $("#month_type").val();
		      $.ajax({
		        url: "ajax.php",
		        data:'page=assignemptofrom16a&type=&list_id='+type+'&assement_year='+assement+'&month='+month_type,
		        type: "POST",
		        success:function(data){
		          console.log(data);
		          location.reload();

		          //$("#dropdownlist_"+id).html(data);
		        },
		        error:function (){}
		      });
		} else {
			$('#kt_modal_KTDatatable_remote').modal("hide");
		}
	});
  }
</script>
<div class="modal-header">
	<h5 class="modal-title">Assign / Unassign Emloyees</h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
	<?php  //print_r($_SESSION['document'][$_POST['assement_year']][$_POST['list_id']]); ?>
	<table class="table table-hover custom_asign_emp_mpc" id="kt_datatable1" >
		<thead >
			<tr>
				<th width="2%">#</th>
				<th width="15%">Name </th>
				<th width="10%">Email</th>
				<th width="10%"></th>
				<th width="2%"></th>
			</tr>
		</thead>
		<tbody >
			<?php  $i=1; foreach ($empdata as $row){ ?> 
				<tr id="row_<?php echo $row['id']; ?>" >
					<td class="p-2"><?php echo $i; ?></td>
					<td class="p-2">
						<div class="align-middle">
							<?php if($row['image']!="") {?>
								<img src="uploads/all/<?php echo $row['image']; ?>"   class="img-radius img-50 align-top m-r-15" onerror="this.src='uploads/all/no-image.jpg';">
							<?php } else { ?>
								<span  alt="user image" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center" ><?php echo initials($row['display_name']); ?></span>
							<?php } ?>
							<div class="d-inline-block width73">
								<a href="#!" class="text-capitalize"><h6>   <?php echo $row['display_name']; ?></h6></a>
								<p class="text-muted m-b-0"><?php echo $row['emp_code']; ?></p>
							</div>
						</div>
					</td>
					<td class="p-2"><p><?php echo $row['email_id']; ?></p></td>
					<td class="p-2">
						<p>
							<?php if(isset($aemp[$row['id']])){ ?> 
								<span class="f-14 f-w-600 text-danger">Un-Assign</span>
							<?php }else{ ?>
								<span class="f-14 f-w-600  text-success">Assign</span>
							<?php }  ?>
						</p>
					</td>
					<td class="p-2">
						<p>
							<span class="f-14 f-w-600  text-danger cursor-pointer" onclick="assignemp(<?php echo $row['id']; ?>)"> <i class="icofont icofont-ui-delete"></i></span>
						</p>
					</td>
				</tr>
				<?php $i++; } ?>
			</tbody>
		</table>
	</div>

	<div class="modal-footer">
		<input type="hidden" name="assement" id="assement" value="<?php echo $_REQUEST['assement']; ?>">
		<input type="hidden" name="type" id="type" value="<?php echo $_REQUEST['type']; ?>">
		<input type="hidden" name="month_type" id="month_type" value="<?php echo $month_type ?>">

		<button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
		<button type="button" class="btn btn-primary waves-effect waves-light"  onclick="submitform()">Release</button>
	</div>
	<script type="text/javascript">
		$(".js-example-basic-single").select2();
		$(".js-example-basic-multiple").select2();
		$(".js-example-disabled-results").select2();
		$( ".datepicker" ).datepicker({
			showOn: "both", 
			buttonText: "<i class='ti-calendar'></i>",
			dateFormat: date_format,
			changeMonth: true,
			changeYear: true
		});
	</script>

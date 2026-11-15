<?php
include_once('../includes/session.php');
include_once('../dbconfig.php');
include_once('../comm.php');
$url = API."?mode=getapplicationtickets&page_type=".$_POST['page_type'];
$response = callAPI($url);
$result = json_decode($response,1);
$priority = $result['priority'];
$application_tickets = $result['application_tickets'];
$stausresult = ViewStatusData();
$status = array("0"=>"In Progress","1"=>"In Working","40"=>"Cancel","199"=>"Completed");
//echo "<pre>"; print_r($result); echo "</pre>";
//phpinfo();
?>
<script type="text/javascript">
function insertApplicationTickets(){
	var validations = ajaxFormSubmitValidation();
    if(validations == 0){
	  	var formData = new FormData();
	    formData.append("page", "insertapplicationtickets");
	    var dataArray = $("#applicationtickets_form").serializeArray(),
	    dataObj = {};
	    $(dataArray).each(function(i, field){
	      formData.append(field.name, field.value);
	    });
	    var popattachment = document.getElementById('popattickettachment').files[0];
	    formData.append("file",popattachment);
	    jQuery.ajax({
	        url: "ajax.php",
	        data: formData,
	        type: "POST",
	        contentType: false,
	        processData: false,
	        success:function(data){
	        	$('#kt_modal_KTDatatable_remotem').modal("hide");
	            tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',"Record Successfully Insert",'');
	        },
	        error:function (){}
	    });
	}
}
function updateApplicationTickets(rid,status){
	jQuery.ajax({
        url: "ajax.php",
        data: "page=updateapplicationtickets&rid="+rid+"&status="+status,
        type: "POST",
        success:function(data){
        	swal("<?php echo ucwords($_SESSION['config']['name']); ?>", "<?php echo $_SESSION['error_message']['3001']; ?>", "success");
        	getApplicationTickets('PT');
        },
        error:function (){}
    });

}
</script>
<?php if($_POST['page_type'] == "PT"){ ?>
<div class="modal-header  ">
	<h5 class="modal-title"> Tickets Submited To Me</h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ></button>
</div>
<div class="modal-body">
	<?php if(count((array)$application_tickets) != 0){ ?>
	<table class="table table-striped table-bordered nowrap" style="font-size:13px;" id="basic-btn-non">
		<thead>
			<tr>
				<th scope="col"><?= __('User') ?></th>
				<th scope="col"><?= __('Ticket No.') ?></th>
				<th scope="col"><?= __('Issue') ?></th>
				<th scope="col"><?= __('Request') ?></th>
				<th scope="col"><?= __('File') ?></th>
				<!-- <th scope="col">Response</th> -->
				<!-- <th scope="col">Completion</th> -->
				<th scope="col"><?= __('Status') ?></th>
				<!-- <th scope="col"></th> -->
			</tr>
		</thead>
		<tbody>
			<?php foreach($application_tickets as $at){ ?>
			<tr>
				<td class="p-2"><?php echo $_SESSION['users'][$at['originator']]; ?></td>
				<td class="p-2"><?php echo $at['transaction_number']; ?></td>
				<td class="w-b-w-s p-2"><?php echo $at['issue_name']; ?></td>
				<td class="p-2"><?php echo fromDate($at['request_datetime']); ?></td>
				<!-- <td class="p-2"><?php echo $at['response_datetime'] !=""?fromDate($at['response_datetime']):"-"; ?></td> -->
				<!-- <td class="p-2"><?php echo $at['completion_datetime'] !=""?fromDate($at['completion_datetime']):"-"; ?></td> -->
				<td class="p-2 text-center">
					<?php if($at['attachment']!=""){ ?>
						<a onclick="dynamicFilePriviews('uploads/all/<?php echo $at['attachment']; ?>')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Attachments Preview"><i class="ti-eye"></i></a>
					<?php }else{ echo " - "; } ?>
				</td>
				<td class="p-2">
					<span class="label label-md label-primary"><?php echo $status[$at['current_status']]; ?></span>
				</td>
				<!-- <td class="p-2 text-center">
					<div class="dropdown-primary dropdown cursor-pointer">
			            <span class="" id="dropdown-2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="ti-settings"></i></span>
			            <div class="dropdown-menu" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
			            	<?php if($at['current_status'] =='0'){ ?>
			                <a class="dropdown-item waves-light waves-effect"  onclick="updateApplicationTickets('<?php echo $at['id']; ?>','1')">Start To Wrok</a>
			            <?php }else{ ?>

			            <?php } ?>
			                <a class="dropdown-item waves-light waves-effect"  onclick="updateApplicationTickets('<?php echo $at['id']; ?>','<?php echo ST_CANCEL; ?>')">Cancel</a>
			            </div>
			        </div>
				</td> -->
			</tr>
			<?php } ?>
		</tbody>
	</table>
<?php }else{ ?>
	<div class="card-px text-center ">
		<p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Tickets</p>
		<div class="text-center px-4">
			<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
		</div>
	</div>
<?php } ?>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal" ><?= __('Close') ?></button>
</div>
<?php }elseif($_POST['page_type'] == "LT"){ ?>
<div class="modal-header  ">
	<h5 class="modal-title"> My Tickets</h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ></button>
</div>
<div class="modal-body">
	<?php if(count((array)$application_tickets) != 0){ ?>
	<table class="table table-striped table-bordered nowrap" style="font-size:13px;" id="basic-btn-non">
		<thead>
			<tr>
				<th scope="col"><?= __('Ticket No.') ?></th>
				<th scope="col"><?= __('Issue') ?></th>
				<th scope="col"><?= __('Request') ?></th>
				<th scope="col"><?= __('File') ?></th>
				<!-- <th scope="col">Response</th> -->
				<!-- <th scope="col">Completion</th> -->
				<th scope="col"><?= __('Status') ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($application_tickets as $at){ ?>
			<tr>
				<td class="p-2"><?php echo $at['transaction_number']; ?></td>
				<td class="w-b-w-s p-2"><?php echo $at['issue_name']; ?></td>
				<td class="p-2"><?php echo fromDate($at['request_datetime']); ?></td>
				<!-- <td class="p-2"><?php echo $at['completion_datetime'] !=""?fromDate($at['completion_datetime']):"-"; ?></td> -->
				<td class="p-2 text-center">
					<?php if($at['attachment']!=""){ ?>
						<a onclick="dynamicFilePriviews('uploads/all/<?php echo $at['attachment']; ?>')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Attachments Preview"><i class="ti-eye"></i></a>
					<?php }else{ echo " - "; } ?>
				</td>
				<td class="p-2">
					<span class="label label-md label-primary"><?php echo $status[$at['current_status']]; ?></span>

				</td>

			</tr>
			<?php } ?>
		</tbody>
	</table>
	<?php }else{ ?>
	<div class="card-px text-center ">
		<p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Tickets</p>
		<div class="text-center px-4">
			<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
		</div>
	</div>
<?php } ?>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal" ><?= __('Close') ?></button>
</div>
<?php }else{ ?>
<div class="modal-header  ">
	<h5 class="modal-title"> New Ticket</h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" ></button>
</div>
<div class="modal-body p-l-50 p-r-50">
	<form id="applicationtickets_form">
		<div class="row">
			<div class="col-lg-12">
				<span class="form-label col-form-label d-inline-block fw-bold"><?= __('Issue Name') ?> <span class="requried">*</span></span>
				<span class="d-block error-message text-danger"></span>
				<input type="text" class="form-control requried_field" name="issue_name" aria-label="Issue Name">
			</div>
			<div class="col-lg-12">
				<span class="form-label col-form-label d-inline-block fw-bold"><?= __('Issue Description') ?> <span class="requried">*</span></span>
				<span class="d-block error-message text-danger"></span>
				<textarea class="form-control requried_field" name="issue_description" aria-label="Issue Description"></textarea>
			</div>
			<div class="col-lg-6 w-full">
				<span class="form-label col-form-label d-inline-block fw-bold"><?= __('Priority') ?> <span class="requried">*</span></span>
				<span class="d-block error-message text-danger"></span>
				<select class="form-control form-select js-example-basic-single requried_field" name="priority" aria-label="Priority">
					<?php foreach($priority as $priok=>$priov){ ?>
	                    <option value="<?php echo $priok; ?>" ><?php echo htmlspecialchars(__($priov)); ?></option>
	                <?php } ?>
	            </select>
			</div>
			<div class="col-lg-6">
		        <label for="popattickettachment" class="form-label col-form-label d-block"><?= __('Attache File') ?></label>
		        <input type="file" class="form-control" id="popattickettachment" aria-label="Input">
		    </div>
		</div>
	</form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal" ><?= __('Close') ?></button>
    <button type="button" class="btn btn-primary waves-effect waves-light" id="insertemployeedetails_btn" onclick="insertApplicationTickets()"><?= __('Submit') ?></button>
</div>
<?php } ?>
<?php if($_POST['page_type'] == "PT" OR $_POST['page_type'] == "LT"){ ?>

    <script type="text/javascript" src="assets/bower_components/modernizr/js/modernizr.js"></script>
    <script type="text/javascript" src="assets/bower_components/modernizr/js/css-scrollbars.js"></script>
    <script src="assets/pages/data-table/js/jszip.min.js"></script>
     <script src="assets/pages/data-table/extensions/buttons/js/vfs_fonts.js"></script>
    <script src="assets/pages/data-table/extensions/responsive/js/dataTables.responsive.min.js"></script>
    <script src="assets/pages/data-table/extensions/buttons/js/buttons.colVis.min.js"></script>
    <script src="assets/pages/data-table/extensions/responsive/js/responsive-custom.js"></script>
    <script src="assets/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="assets/bower_components/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="assets/pages/data-table/extensions/responsive/js/dataTables.responsive.min.js"></script>
    <script src="assets/bower_components/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="assets/bower_components/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="assets/bower_components/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="assets/bower_components/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="assets/bower_components/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>
    <script src="assets/pages/data-table/extensions/buttons/js/extension-btns-custom.js"></script>
    <?php } ?>
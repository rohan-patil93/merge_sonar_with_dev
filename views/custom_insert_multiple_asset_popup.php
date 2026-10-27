 include_once '../includes/session.php';
 include_once '../dbconfig.php';
 include_once '../comm.php';

 $url = API."?mode=getassetallocation&admin_type=".$_SESSION['admin_type']."&resultgettype=MULTIPLEASSETUPDATE&assetid=".$_POST['assetid']."&momenthistory=Y";
$data_details = callAPI($url);
$details = json_decode($data_details,1);
$assetcategory = $details['asset_categories'];
$locations = $details['locations'];
$departments = $details['departments'];
$costcenters = $details['costcenter'];
$list_assets = $details['list_assets'];
$employees = $details['employees'];
$ass_condition = $details['asset_condition_type'];
foreach($details['asset_categories'] as $asset_category_list){
  $asset_category_lists[$asset_category_list['id']] = $asset_category_list['category_description'];
}
foreach($details['locations'] as $locations_list){
  $locations_lists[$locations_list['id']] = $locations_list['location_name'];
}

foreach($details['asset_status'] as $asset_status){
  $ass_status[$asset_status['id']] = $asset_status['name'];
}
?>
<script type="text/javascript">
  function selectAllocationType(){
    var selected = $("#allocation_type:checked").val();
    $(".select_caost_center").hide();
    $(".select_dept").hide();
    $(".select_emp").hide();
    if(selected=="CC"){
      $(".select_caost_center").show();
    }else if(selected=="D"){
      $(".select_dept").show();
    }else{
      $(".select_emp").show();
    }

  }
  function submitdetails(){
     $('.tabledit-input').removeAttr("disabled")
    var formData = new FormData();
    formData.append("page", "updateassetallocation");
    var dataArray = $("#assetsallocationform").serializeArray(),
    dataObj = {};
    $(dataArray).each(function(i, field){
      formData.append(field.name, field.value);
    });
  jQuery.ajax({
    url: "ajax.php",
    data: formData,
    type: "POST",
    contentType: false,
    processData: false,
    success:function(data){
       console.log(data);
        $('#kt_modal_dynamic_popup2').modal("hide");
        tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',"<?php echo $_SESSION['error_message'][3001]; ?>",'');
        location.reload();

    }
  });
}

</script>
<style>
table#example-1{display: table;
    table-layout: fixed;}

    table#example-1 tr th, table#example-1 tr td{
    display: table-cell;
    width: 170px; height: 35px; padding:5px 5px 3px; line-height : 15px;
    vertical-align: middle;
    }
    table#example-1 tr th{
        padding:10px 5px 10px 5px;
    }

    table#example-1 tr td span.tabledit-span {
    word-break: break-word;
    white-space: break-spaces
    vertical-align: middle;
display: inline-block;
}
table#example-1 tr td button.ui-datepicker-trigger{display:none;}
table#example-1 tr td select.form-control{-webkit-appearance: auto;
    -moz-appearance: inherit;
    appearance: auto;}
</style>
<div class="modal-header">
  <h4 class="modal-title">Asset Updates</h4>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
  <div class="j-content">
    <form id="assetsallocationform" method="post" >
      <div class="row ">
        <div class="table-responsive">
          <table class="table table-striped table-bordered " id="example-1">
            <thead>
              <tr>
               <th scope="col">#</th>
               <th scope="col" style="display: none">Asset Code</th>
               <th scope="col" style="display: none">Asset Name</th>
               <th scope="col">Asset Code</th>
               <th scope="col">Asset Name</th>
               <th scope="col">Category</th>
               <th scope="col">Location</th>
               <th scope="col">Status</th>
               <th scope="col">Brand</th>
               <th scope="col">Model</th>
               <th scope="col">Serial Number</th>
               <th scope="col">Condition</th>

             </tr>
           </thead>
           <tbody>
            <?php $mm=1; foreach($list_assets as $list_asset){ ?>
              <input type="hidden" name="asset_id[]" value="<?php echo $list_asset['id']; ?>">
              <tr>
                <td scope="row"><?php echo $mm; ?></td>
                <td class="tabledit-view-mode  w-b-w-s" style="pointer-events: none; display: none"><span class="tabledit-span"><?php echo $list_asset['asset_code']; ?></span>
                  <input aria-label="Gdfgff[]" class="tabledit-input form-control input-sm" type="text" name="gdfgff[]" value="<?php echo $list_asset['asset_code']; ?>" style="display:none;">
                </td>
                <td class="tabledit-view-mode  w-b-w-s" style="pointer-events: none; display: none"><span class="tabledit-span"><?php echo $list_asset['asset_description']; ?></span>
                  <input aria-label="Sgfsdfsdfsdf[]" class="tabledit-input form-control input-sm" type="text" name="sgfsdfsdfsdf[]" value="" style="display:none;">
                </td>

                <td class="tabledit-view-mode  w-b-w-s" style="pointer-events: none;"><span class="tabledit-span"><?php echo $list_asset['asset_code']; ?></span>
                  <input aria-label="Asset Code[]" class="tabledit-input form-control input-sm" type="text" name="asset_code[]" value="<?php echo $list_asset['asset_code']; ?>" style="display:none;">
                </td>
                <td class="tabledit-view-mode  w-b-w-s"><span class="tabledit-span"><?php echo $list_asset['asset_description']; ?></span>
                  <input aria-label="Asset Description[]" class="tabledit-input form-control input-sm" type="text" name="asset_description[]" value="<?php echo $list_asset['asset_description']; ?>" style="display:none;">
                </td>
                <td class="tabledit-view-mode  w-b-w-s"><span class="tabledit-span"><?php echo $asset_category_lists[$list_asset['category_id']]; ?></span>
                  <select aria-label="Category []" class="tabledit-input form-control input-sm" name="category_id[]"  style="display:none;">
                    <option value="">Select Any One</option>
                    <?php foreach ($assetcategory as $assetcategor) { ?>
                      <option value="<?php echo $assetcategor['id']; ?>" <?php echo $assetcategor['id'] == $list_asset['category_id']?"selected":""; ?> ><?php echo ucwords($assetcategor['category_description']); ?></option>
                    <?php } ?>
                  </select>
                </td>
                <td class="tabledit-view-mode"><span class="tabledit-span"><?php echo $locations_lists[$list_asset['asset_location']]; ?></span>
                  <select aria-label="Asset Location[]" class="tabledit-input form-control input-sm" name="asset_location[]"  style="display:none;">
                   <option value="">Select Any One</option>
                   <?php foreach ($locations as $location) { ?>
                    <option location value="<?php echo $location['id']; ?>" <?php echo $location['id'] == $list_asset['asset_location']?"selected":""; ?>><?php echo ucwords($location['location_name']); ?></option>
                  <?php } ?>
                </select>
              </td>
              <td class="tabledit-view-mode"><span class="tabledit-span"><?php echo $ass_status[$list_asset['asset_status']]; ?></span>
                <select aria-label="Asset Status[]" class="tabledit-input form-control input-sm" name="asset_status[]"  style="display:none;">
                  <option value="">Select Any One</option>
                  <?php foreach ($ass_status as $ass_statuskey=>$ass_statuss){ ?>
                    <option  value="<?php echo $ass_statuskey; ?>" <?php echo $ass_statuskey == $list_asset['status']?"selected":""; ?>><?php echo ucwords($ass_statuss); ?></option>
                  <?php }  ?>
                </select>
              </select>
            </td>
            <td class="tabledit-view-mode"><span class="tabledit-span"><?php echo $list_asset['brand']; ?></span>
              <input aria-label="Brand[]" class="tabledit-input form-control input-sm" type="text" name="brand[]" value="<?php echo $list_asset['brand']; ?>" style="display:none;">
            </td>
            <td class="tabledit-view-mode"><span class="tabledit-span"><?php echo $list_asset['model']; ?></span>
              <input aria-label="Model[]" class="tabledit-input form-control input-sm" type="text" name="model[]" value="<?php echo $list_asset['model']; ?>" style="display:none;">
            </td>
            <td class="tabledit-view-mode"><span class="tabledit-span"><?php echo $list_asset['serial_no']; ?></span>
             <input aria-label="Serial No[]" class="tabledit-input form-control input-sm" type="text" name="serial_no[]" value="<?php echo $list_asset['serial_no']; ?>" style="display:none;">

           </td>
           <td class="tabledit-view-mode"><span class="tabledit-span"><?php echo $ass_condition[$list_asset['conditions']]; ?></span>
            <select aria-label="Conditions[]" class="tabledit-input form-control input-sm" name="conditions[]"  style="display:none;">
             <option value="">Select Any One</option>
             <?php foreach ($ass_condition as $ass_conditiokey=>$ass_conditio) { ?>
              <option  value="<?php echo $ass_conditiokey; ?>" <?php echo $ass_conditiokey == $list_asset['conditions']?"selected":""; ?>><?php echo ucwords($ass_conditio); ?></option>
            <?php } ?>
          </select>
        </select>
      </td>

    </tr>
    <?php $mm++;
} ?>
  </tbody>
</table>
</div>

</div>

</form>
</div>
</div>

<div class="modal-footer">
  <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
  <button type="button" class="btn btn-primary waves-effect waves-light"  onclick="submitdetails()">Update</button>
</div>
    <script type="text/javascript" src="assets/pages/edit-table/jquery.tabledit.js"></script>
    <script type="text/javascript" src="assets/pages/edit-table/editable.js"></script>
<script type="text/javascript">
  $(".js-example-basic-single").select2();
  $(".js-example-disabled-results").select2();
  $( ".datepicker" ).datepicker({
    showOn: "both",
    buttonText: "<i class='ti-calendar'></i>",
    dateFormat: date_format,
    changeMonth: true,
    changeYear: true
  });
</script>


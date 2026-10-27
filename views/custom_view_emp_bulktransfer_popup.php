<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

$vurl = API."?mode=getbulkchangecategories&tablename=".$_GET['application_tablename']."&fieldname=".$_GET['application_table_fieldname']."&empidlist=".$_GET['empids']."&emptable_fieldname=".$_GET['update_employees_fieldname']."&insert_history_tablename=".$_GET['insert_history_tablename'];
$bulk_data = callAPI($vurl);
$bulkdata = json_decode($bulk_data,1);
$allemployees = $bulkdata['allemployees'];
$emp_details = $bulkdata['emp_details'];
$emp_history = $bulkdata['emp_history'];

$update_list = array("transfer_empids"=>array_keys($emp_details),"previous_record_rowid"=>$emp_history,"oldvalue"=>$emp_details);

$transfer_details = urlencode(json_encode($update_list,1));
$application_table_values = $bulkdata['application_table_values'];
$bulkchange_categories = $bulkdata['bulkchange_categories'];
$bulkempids = explode(',', $_GET['empids']);
$fieldname = $bulkchange_categories[$_GET['bulkchange_categoryid']]['update_employees_fieldname'];
$tablename = $bulkchange_categories[$_GET['bulkchange_categoryid']]['application_tablename'];
$insert_tablename = $bulkchange_categories[$_GET['bulkchange_categoryid']]['insert_history_tablename'];
$moveto = $application_table_values[$_GET['bulkmove_toid']];
$category_name = $bulkchange_categories[$_GET['bulkchange_categoryid']]['category_name'];

?>

<script type="text/javascript">
    function confirmbulktransfer(){
        var movetodata =document.getElementById("movetodata").value;
        var category_name =document.getElementById("category_name").value;
        var insert_tablename =document.getElementById("insert_tablename").value;
        var update_fieldname =document.getElementById("update_fieldname").value;
        var update_fieldvalue =document.getElementById("update_fieldvalue").value;
        var effective_from =document.getElementById("effective_from").value;
        var effective_to =document.getElementById("effective_to").value;
        var transfer_data =document.getElementById("transfer_data").value;

        console.log(decodeURI(transfer_data));
        var pass_data = "&insert_tablename="+insert_tablename+"&update_fieldname="+update_fieldname+"&update_fieldvalue="+update_fieldvalue+"&effective_from="+effective_from+"&effective_to="+effective_to+"&transfer_data="+transfer_data;
        console.log(pass_data);
        $.ajax({
            url: "ajax.php",
            data:'page=confirmbulktransfer'+pass_data,
            type: "POST",
            success: function(response) {
                console.log(response);
                 Swal.fire({
                  title:  category_name,
                  text: "Choosen Employee's "+ category_name + " To " + movetodata + " is Completed!",
                  icon: "success",
                  buttonsStyling: false,
                  confirmButtonText: "OK!",
                  customClass: {
                   confirmButton: "btn btn-primary"
                  }
                 });
                $('#kt_modal_KTDatatable_remote1').modal('hide');
                window.location.href = "index.php?option=COM_INSERT_BULK_TRANSFER";

            }
        });
    }

</script>

<div class="modal-header">
    <h5 class="modal-title text-center">
         <?php echo $category_name; ?>
    </h5>
<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body" data-scroll="true" data-height="500" style="height: 500px;">
    <div class="form-group row" >
        <div class="col-lg-12">
            <div class="card-body">

                <!--begin: Datatable -->
                <table class="table table-separate table-head-custom table-checkable" id="kt_datatable1">

                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Name </th>
                                <th scope="col">Transfer From </th>
                                <th scope="col">Transfer To </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $serialno = 0;
                            foreach ($bulkempids as $empkey => $empvalue) {
                                $fromvalue = 0;
                                $serialno++;     ?>
                                <tr>

                                    <td> <?php echo $serialno; ?>  </td>
                                    <td> <div class="d-flex align-items-center mr-7">


<?php if($allemployees[$empvalue]['image']!="") { ?>
                                            <img src="uploads/all/<?php echo $allemployees[$empvalue]['image']; ?>"   class="img-radius img-50 align-top m-r-15" alt="Attachment Preview">
                                        <?php } else { ?>
                                            <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center" ><?php echo initials($allemployees[$empvalue]['display_name']); ?></span>
                                        <?php } ?>

                                         &nbsp;
                                      <?php echo $allemployees[$empvalue]['display_name']; ?> </div></td>
                                    <td> <?php echo $application_table_values[$emp_details[$empvalue]]; ?> </td>
                                    <td> <?php echo $moveto; ?> </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="insert_tablename" value="<?php echo $_GET['application_tablename']; ?>">
    <input type="hidden" id="update_fieldname" value="<?php echo $fieldname; ?>">
    <input type="hidden" id="update_fieldvalue" value="<?php echo $_GET['bulkmove_toid']; ?>">
    <input type="hidden" id="effective_from" value="<?php echo $_GET['effective_date']; ?>">
    <input type="hidden" id="effective_to" value="<?php echo ST_EFFECTIVE_TODATE; ?>">
    <input type="hidden" id="transfer_data" value="<?php echo $transfer_details; ?>">
    <input type="hidden" id="category_name" value="<?php echo $category_name; ?>">
    <input type="hidden" id="movetodata" value="<?php echo $moveto; ?>">

    <div class="modal-footer">
        <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Cancel</button>
        <button id="confirm" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2" onclick="confirmbulktransfer();" name="submitform" value="exit"></i>Confirm</button>
    </div>

</div>

<script src="assets/js/pages/features/miscellaneous/sweetalert2.js"></script>

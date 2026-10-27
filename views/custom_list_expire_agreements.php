<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

$url = API."?mode=getexpireagreementslist";
$details = callAPI($url);
$data = json_decode($details,1);
?>
<?php if(count((array)$data) != 0){ ?>
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Expire Agreements List</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
<form class="form" id="">
      <div class="modal-body">
        <div class="">
<div class="card ">
        <!--begin: Datatable -->
            <table class="table table-separate table-head-custom table-checkable" id="kt_datatable1">

                    <thead>
                        <tr>
                            <th scope="col">Sr.No.</th>
                            <th scope="col">Agreement Name </th>
                            <th scope="col">Vendor Name</th>
                            <th scope="col">Expiry Date  </th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i=1;
                            foreach ($data as $result) {
                                ?>
                            <tr>
                                <td><?php echo $i; ?> </td>
                                <td><?php echo $result['agreement_name']; ?> </td>
                                <td><?php echo $result['display_name']; ?> </td>
                                <td><span style="color:red"><?php echo $result['expiry_date']; ?></span> </td>
                            </tr>
                        <?php  $i++;
} ?>
                        </tbody>
                    </table>
            </div>
        </div>
    </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
      </form>

<?php }else{ echo "0";
} ?>


<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

$feasibility_id = $_GET['feasibility_id'] ?? '';

$api_url = API . "?mode=getFeasibilityDashboard&feasibility_id=" . $feasibility_id;

$response = callAPI($api_url);
$data = json_decode($response, true);

?>

<div class="card">
  <div class="card-header">
    <h5>Pending BOM Returns (Feasibility Closed)</h5>
  </div>
  <div class="card-body custom-scroll" style="min-height: 400px; max-height: 400px;">
    <table class="table ">
      <thead>
        <tr>

          <th scope="col">Feasibility Title</th>
          <th scope="col">Item Name</th>
          <th scope="col">Planned Return By</th>
          <th scope="col">Actual Return By</th>
          <th scope="col">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count((array)$data) > 0) {
          foreach ($data as $row) { ?>
            <tr>


              <td>
                <a href="#!" class="leadsidebar text-primary" onclick="leadPageCall('CONTACT','<?php echo $row['lead_id']; ?>')"><?php echo $row['display_name'] . " - " . $row['feasibility_type']; ?>
                </a>
              </td>

              <td><?php echo htmlspecialchars($row['item_name']); ?></td>
              <td>
                <input aria-label="Input field" type="text" class="form-control" value="<?php echo htmlspecialchars($row['planned_return_by'] != "" ? fromdate($row['planned_return_by']) : ""); ?>" readonly />
              </td>
              <td>
                <input aria-label="Input field" type="text" class="form-control datepicker" id="return_<?php echo $row['bom_id']; ?>" value="<?php echo htmlspecialchars($row['actual_return_by'] != "" ? fromdate($row['actual_return_by']) : ""); ?>" />
              </td>
              <td>
                <button class="btn btn-sm btn-primary" onclick="updateReturnDate(<?php echo $row['bom_id']; ?>)">Update</button>
              </td>
            </tr>
          <?php }
        } else { ?>
          <tr>
            <td colspan="5">
              <div class="text-center ">
                <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Records</p>
                <div class="text-center px-4">
                  <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                </div>
              </div>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</div>


<script>
  function updateReturnDate(bom_id) {
    const actual_return_by = document.getElementById('return_' + bom_id).value;

    if (!actual_return_by) {
      tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX', 'Please select a return date.', '');

      return;
    }
    $.ajax({
      url: 'ajax.php',
      method: 'POST',
      data: {
        page: 'updateReturnDate',
        'bom_id': bom_id,
        'actual_return_by': actual_return_by
      },
      success: function(response) {
        try {
          const res = JSON.parse(response);
          if (res.status === 'sucess') {
            swal("<?php echo ucwords($_SESSION['config']['name']); ?>", "Return date updated successfully.", "success");
            location.reload(); // or update table dynamically
          } else {
            swal("<?php echo ucwords($_SESSION['config']['name']); ?>", "Update failed: " + res.message, "danger");
          }
        } catch (e) {
          console.error("Invalid JSON:", response);
          const appName = "<?php echo ucwords($_SESSION['config']['name']); ?>";
          swal({
            title: appName,
            text: "Something went wrong while updating. " + response,
            type: "error",
            confirmButtonClass: "btn-danger",
            confirmButtonText: "OK"
          });
        }
      },
      error: function(xhr, status, error) {
        console.error("AJAX Error:", error);
        const appName = "<?php echo ucwords($_SESSION['config']['name']); ?>";
        swal({
          title: appName,
          text: "An error occurred while Saving data.\n\nStatus: " + status + "\nError: " + error,
          type: "error",
          confirmButtonClass: "btn-danger",
          confirmButtonText: "OK"
        });
      }
    });

  }

  //         url: "views/custom_view_feasibility_report.php",
  //         data:{'feasibility_id': feasibility_id},
  //         type: "GET",
  //         success:function(data){
  //         error:function (){}
</script>

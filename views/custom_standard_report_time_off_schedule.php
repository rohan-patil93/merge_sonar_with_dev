<?php
$emp_id = $_SESSION['emp_id'];
$menus = $result['menu_data'];
$emp_details = $result['emp_data'];
$_SESSION['download_data'] = array();
$_SESSION['apply_js']['calender']='1';
?>
<div class="page-body">
  <div class="card">
    <div class="card-header">
      <h5 class="f-30">Time Off Schedule</h5>
      <input type="hidden" name="calendar_cat" id="calendar_cat" value="101">
      <div id='calendar'></div>
    </div>
  </div>
  <div class="card">

    <div class="card-body">
      <div class="mt-5 table-card">
        <?php foreach($result['all_leave_type'] as $type){ ?>
        <div class="d-inline-block">
          <div class="p-status  d-inline-block" style="background-color: <?php echo $type['back_ground_color']; ?>"></div>
          <span class="d-inline-block"><?php echo $type['leave_type_name']; ?></span>
        </div>
        <?php } ?>
      </div>
      <div class="table-responsive mt-5">
        <?php if(count((array)$emp_details) !=0){ ?>
        <table class="table table-hover table-sm">
          <tr>
            <th scope="col">Name</th>
            <th scope="col">From</th>
            <th scope="col">To</th>
            <th scope="col">Type</th>
            <th scope="col">Leave Days</th>
            <th scope="col">Status</th>
            <th scope="col">Notes</th>
          </tr>
          <?php foreach($emp_details as $emdata ){ ?>
          <tr>
            <td>
               <div class="d-inline-block align-middle">
                <?php if($emdata['image']!=""){ ?>
                  <img src="uploads/module/hr.webp" alt="user" class="img-radius img-40 align-top m-r-15">
                <?php }else{ ?>
                  <span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  mt-1"><?php echo initials($emdata['display_name']); ?></span>
                <?php } ?>
                <div class="d-inline-block">
                 <?php echo $emdata['display_name']; ?>
                 <p class="text-muted m-b-0 f-12"><?php echo $emdata['emp_code']; ?></p>
               </div>
             </div>
            </td>
            <td><?php echo fromDate($emdata['leave_from']); ?></td>
            <td><?php echo fromDate($emdata['leave_to']); ?></td>
            <td><?php echo $result['leave_type'][$emdata['leave_type_id']]; ?></td>
            <td><?php echo $emdata['leave_days']; ?></td>
            <td>Approved</td>
            <td><?php echo $emdata['special_request']; ?></td>
          </tr>
        <?php } ?>
        </table>
        <?php }else{ ?>
        <div class="card-px text-center py-20 m-t-30">
        <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Data</p>
        <div class="text-center px-4">
          <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
        </div>
      </div>
      <?php } ?>
      </div>
    </div>
  </div>
</div>

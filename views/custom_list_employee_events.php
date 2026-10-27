<div class="d-flex flex-column-fluid">
  <div class="container-fluid">
<div class="card-custom">
    <div class="card-header">
      <div class="card-title">
        <h5 class="card-label">Employee Attendance</h5>
      </div>
      <div class="card-toolbar card-header-right">
        <?php if(count((array)$res_team)>0){ ?>
        <a href="index.php?option=COM_LIST_NEW_EMPLOYEE&NI=<?php echo $_GET['NI']; ?>" class="nav-link d-inline-block" <?php if($_GET['TEAM']!=1){  ?> style="border-bottom: 1px solid #3699FF;" <?php
} ?>> <span class="nav-icon"><i class="flaticon2-user"></i></span>&nbsp;Attendance</a>
        <a href="index.php?option=COM_LIST_NEW_EMPLOYEE&NI=<?php echo $_GET['NI']; ?>&TEAM=1"  class="nav-link d-inline-block"  <?php if($_GET['TEAM']==1){  ?> style="border-bottom: 1px solid #3699FF;" <?php
} ?>> <span class="nav-icon"><i class="flaticon-network"></i></span>&nbsp;Team Attendance</a>
      <?php } ?>
        <?php if($_GET['TEAM']==1){ ?>
          <input type="hidden" name="calendar_cat" id="calendar_cat" value="51">
      <?php }else{ ?>
        <input type="hidden" name="calendar_cat" id="calendar_cat_2" value="1">
       <?php } ?>
      </div>
    </div>
    <div class="card-body">
      <div id="calendar"></div>
    </div>
  </div>
  </div>
  </div>
  <?php if($_POST['ajaxpage'] == 'Y'){?>

<script type="text/javascript" src="assets/bower_components/moment/js/moment.min.js"></script>
<script type="text/javascript" src="assets/bower_components/fullcalendar/js/fullcalendar.min.js"></script>
<script type="text/javascript" src="assets/pages/full-calender/calendar.js"></script>
<?php } ?>

<div class="d-flex flex-column-fluid">
  <div class="container-fluid">
    <div class="card card-custom">


<div class="card-header">
    <h5>Tasks</h5>

    <div class="card-header-right">
             <?php if(count((array)$res_team)>0){ ?>
          <a href="index.php?option=COM_LIST_NEW_EMPLOYEE&NI=<?php echo $_GET['NI']; ?>" class="nav-link ml-3" <?php if($_GET['TEAM']!=1){  ?> style="border-bottom: 1px solid #3699FF;" <?php
} ?>> <span class="nav-icon"><i class="flaticon2-user"></i></span>&nbsp;Task</a>
          <a href="index.php?option=COM_LIST_NEW_EMPLOYEE&NI=<?php echo $_GET['NI']; ?>&TEAM=1"  class="nav-link ml-3"  <?php if($_GET['TEAM']==1){  ?> style="border-bottom: 1px solid #3699FF;" <?php
} ?>> <span class="nav-icon"><i class="flaticon-network"></i></span>&nbsp;Team Task</a>
        <?php } ?>
        <?php if($_GET['TEAM']==1){ ?>
          <input type="hidden" name="calendar_cat" id="calendar_cat_3" value="53-54">
      <?php }else{ ?>
        <input type="hidden" name="calendar_cat" id="calendar_cat_4" value="3-4">
       <?php } ?>

    </div>
</div>

      <div class="card-body">
        <div id="calendar"></div>
      </div>
    </div>
  </div>
</div>


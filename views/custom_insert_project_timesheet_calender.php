<div class="d-flex flex-column-fluid">
  <div class="container-fluid">
    <div class="card">
            <div class="card-header table-card-header">
              <h5><?php echo "Time Sheet";  ?></h5>

          <div class="card-header-right table-card-header">
            <a href="index.php?option=COM_INSERT_PROJECT_TIMESHEETS&CHANGE_DATE=<?php echo date('d-m-Y'); ?>" target="_blank" class="btn btn-primary font-weight-bold">
            <i class="ti-plus text-white icon-md mr-2"></i>Time Sheet</a>
            <input type="hidden" name="calendar_cat" id="calendar_cat" value="15">
          </div>
      </div>
      <div class="card-body">
        <div id="calendar"></div>
      </div>
    </div>
  </div>
</div>


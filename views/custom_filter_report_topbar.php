<?php
$url = API."?mode=toinsertreportviewdata&myreport_id=".$_GET['RID'];
$apires = callAPI($url);
?>
<div class="card-body">
      <form method="post" action="">
        <div class="row">
          <div class="col-lg-2">
            <div class="form-group">
              <div class=" date">
                <input aria-label="<?= htmlspecialchars(__('Fromdate'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control datepicker"  name="fromdate"  value="<?php echo fromDate($fromdate); ?>" />
                <span class="text-danger submiteddateerrer"></span>
              </div>
            </div>
          </div>
          <div class="col-lg-2">
            <div class=" date">
              <input aria-label="<?= htmlspecialchars(__('Todate'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control datepicker"  name="todate"  value="<?php echo fromDate($todate); ?>" />
              <span class="text-danger submiteddateerrer"></span>
            </div>
          </div>
          <div class="col-lg-3">
             <nav class="all-employee">
                <ul>
                  <li class="d-block border"><a href="#"><?php echo __('All Jobs'); ?></a>
                    <ul>
                      <?php foreach($job_postings as $jobtitle){ ?>
                      <li><a href="#"><?php echo htmlspecialchars(__($jobtitle['name'] ?? '')); ?> <i class="ti-angle-right float-end f-12 m-t-5"></i> </a>
                        <ul>
                           <?php foreach($jobtitle['data'] as $jobpost){ ?>
                          <li>
                            <div class="border-checkbox-section">
                              <div class="border-checkbox-group border-checkbox-group-primary">
                                <input  <?php if(in_array($jobpost['id'],(array)$view_post)){ echo "checked";
                                } ?> class="border-checkbox" type="checkbox" name="job_post[]" value="<?php echo $jobpost['id']; ?>" id="<?php echo $jobpost['job_number']; ?>" >
                                <label class="form-label border-checkbox-label" for="<?php echo $jobpost['job_number']; ?>">
                                  <?php echo htmlspecialchars(__($jobpost['job_name'] ?? '')) . " (" . htmlspecialchars($jobpost['job_number'] ?? '') . ")"; ?>
                                </label>
                              </div>
                            </div>
                          </li>
                          <?php } ?>
                        </ul>
                      </li>
                      <?php } ?>
                    </ul>
                  </li>
                </ul>
              </nav>
          </div>
          <div class="col-lg-2">
            <button type="submit" name="applyform" value="apply" class="btn btn-info waves-effect waves-light input-group-text mt-0">
                <?php echo __('Apply'); ?>
              </button>
            <input type="hidden" name="submit" value="applyform">
          </div>
        </div>
      </form>
    </div>

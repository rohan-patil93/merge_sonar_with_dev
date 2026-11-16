<div class="card">
    <div class="card-body">
      <div class="card-title  mb-4">
        <div class="row"><div class="col-lg-3">
          <div class="col bg-light-primary ">
            <span class="svg-icon svg-icon-3x svg-icon-primary d-block my-2">
             <img class="img-fluid img-120" src="uploads/all/<?php echo $_SESSION['config']['display_logo']; ?>" alt="<?= htmlspecialchars(__('Theme-Logo'), ENT_QUOTES, 'UTF-8'); ?>">
           </span>
           <span class="text-dark-75 font-weight-bolder font-size-h3"><?php echo ucwords($_SESSION['config']['name']); ?></span>
          </div>
        </div>
        <div class="col-lg-6">
          <h4 class="card-label text-center f-w-700"><?php echo $title; ?> <br>
            <?php if($from_date!="" && $to_date!=""){ ?>
            <small class="f-10"> <?= __('From'); ?> : <?php echo $from_date; ?> &nbsp;  <?= __('To'); ?> : <?php echo  $to_date; ?></small> <br>
            <?php } ?>
            <small class="f-10"><?= __('Name'); ?> : <?php echo $_SESSION['display_name']; ?> &nbsp;  <?= htmlspecialchars(__('Date & Time'), ENT_QUOTES, 'UTF-8'); ?> : <?php echo date("d-m-Y H:i:s"); ?></small>
          </h4>
        </div>
        <div class="col-lg-3">

        </div>
      </div>
    </div>
  </div>
</div>

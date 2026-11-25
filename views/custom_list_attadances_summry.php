
    <section class="card card-custom gutter-b draggable" aria-label="<?= htmlspecialchars(__('Attendance Summary'), ENT_QUOTES, 'UTF-8'); ?>" style="">
        <div class="card-header">
            <div class="card-title">
                <h3 class="card-label"><?= __('Attendance Summary'); ?></h3>
            </div>
            <div class="card-toolbar">
                <a href="#" class="btn btn-icon btn-sm btn-hover-light-primary draggable-handle">
                    <i class="ki ki-menu"></i>
                </a>
            </div>
        </div>

        <div class="card-body scroll-pull" data-scroll="true" style="height:250px;">
            <div class="card flex-fill">
               <div class="card-body">
                  <div class="statistics">
                     <div class="row">
                        <div class="col-md-6 col-6 text-center">
                           <div class="stats-box mb-4">
                              <p><?= __('Total Employees'); ?></p>
                              <h3>385</h3>
                           </div>
                        </div>
                        <div class="col-md-6 col-6 text-center">
                           <div class="stats-box mb-4">
                              <p><?= __('Present'); ?></p>
                              <h3>19</h3>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="progress mb-4">
                     <progress class="progress-bar bg-purple" value="30" max="100" style="width: 30%;">30%</progress>
                     <progress class="progress-bar bg-warning" value="22" max="100" style="width: 22%;">22%</progress>
                     <progress class="progress-bar bg-success" value="24" max="100" style="width: 24%;">24%</progress>
                     <progress class="progress-bar bg-danger" value="21" max="100" style="width: 26%;">21%</progress>
                     <progress class="progress-bar bg-info" value="10" max="100" style="width: 10%;">10%</progress>
                  </div>
                  <div>
                     <p><i class="far fa-dot-circle text-purple mr-2"></i><?= __('Users have checked in'); ?><span class="float-right">166</span></p>
                     <p><i class="far fa-dot-circle text-warning mr-2"></i><?= __('Users are currently inside office'); ?> <span class="float-right">115</span></p>
                     <p><i class="far fa-dot-circle text-success mr-2"></i><?= __('Users are yet to check in'); ?> <span class="float-right">31</span></p>
                     <p><i class="far fa-dot-circle text-danger mr-2"></i><?= __('Users are on paid leave'); ?> <span class="float-right">47</span></p>
                     <p class=""><i class="far fa-dot-circle text-info mr-2"></i><?= __('Users are on weekend today'); ?> <span class="float-right">5</span></p>
                     <p class=""><i class="far fa-dot-circle text-purple mr-2"></i><?= __('Users are on unpaid leave'); ?> <span class="float-right">5</span></p>
                     <p class=""><i class="far fa-dot-circle text-warning mr-2"></i><?= __('Users are on Holiday today'); ?> <span class="float-right">5</span></p>
                     <p class=""><i class="far fa-dot-circle text-success mr-2"></i><?= __('Users are on onduty today'); ?> <span class="float-right">5</span></p>
                     <p class="mb-0"><i class="far fa-dot-circle text-info mr-2"></i><?= __('Users are on work from home today'); ?> <span class="float-right">5</span></p>
                  </div>
               </div>
            </div>
        </div>
    </section>

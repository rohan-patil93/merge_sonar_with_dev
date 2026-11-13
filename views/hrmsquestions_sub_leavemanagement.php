                      <div class="card border-top-0">
                       <div class="card-header">
                         <form class="form">
                           <div class="card-body">
                            <h3 class="font-size-lg text-dark font-weight-bold mb-6"><?= __('1. Your Leave Cycle?'); ?> </h3>
                            <div class="mb-15">

                              <div class="radio-inline">
                                <label class="radio">
                                  <input aria-label="Ios2" type="radio" name="radios2"/>
                                  <span></span>
                                  <?= __('Calndar Year'); ?>
                                </label>
                                <label class="radio">
                                  <input aria-label="Ios2" type="radio" name="radios2"/>
                                  <span></span>
                                  <?= __('Financial Year'); ?>
                                </label>

                              </div>

                            </div>

                            <h3 class="font-size-lg text-dark font-weight-bold mb-6"><?= __('2. Would you like the leave to be Carried forward?'); ?>  </h3>
                            <div class="mb-15">

                              <div class="radio-inline">
                                <label class="radio">
                                  <input aria-label="Ios2" type="radio" name="radios2"/>
                                  <span></span>
                                  <?= __('Yes - Maximum days carried forward'); ?> <div class="input-group-append">

                                   <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" style="width:50px" placeholder=""/>
                                   <span class="input-group-text"><?= __('days'); ?></span>
                                 </div>
                                 <?= __('Maximum days accumulated'); ?> <div class="input-group-append">

                                   <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" style="width:50px" placeholder=""/>
                                   <span class="input-group-text"><?= __('days'); ?></span>
                                 </div>
                               </label>
                               <label class="radio">
                                <input aria-label="Ios2" type="radio" name="radios2"/>
                                <span></span>
                                <?= __('Financial Year'); ?>
                              </label>

                            </div>

                          </div>  <h3 class="font-size-lg text-dark font-weight-bold mb-6"><?= __('3. What type of Leave would you like to grant?'); ?>  </h3>
                          <div class="mb-15">

                            <label class="checkbox">
                              <input aria-label="<?= htmlspecialchars(__('Checkboxes2'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" name="Checkboxes2"/>
                              <span></span>&nbsp;
                              <?= __('EL – Earned Leave'); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;<div class="input-group-append">

                               <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" style="width:50px" placeholder=""/>
                               <span class="input-group-text"><?= __('days'); ?></span>
                             </div>
                           </label>
                           <label class="checkbox">
                            <input aria-label="<?= htmlspecialchars(__('Checkboxes2'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" name="Checkboxes2"/>
                            <span></span>&nbsp;
                            <?= __('PL - Privilege Leave'); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <div class="input-group-append">

                             <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" style="width:50px" placeholder=""/>
                             <span class="input-group-text"><?= __('days'); ?></span>
                           </div>
                         </label>

                         <label class="checkbox">
                          <input aria-label="<?= htmlspecialchars(__('Checkboxes2'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" name="Checkboxes2"/>
                          <span></span>&nbsp;
                          <?= __('CL – Casual Leave'); ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<div class="input-group-append">

                           <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" style="width:50px" placeholder=""/>
                           <span class="input-group-text"><?= __('days'); ?></span>
                         </div>
                       </label>

                       <label class="checkbox">
                        <input aria-label="<?= htmlspecialchars(__('Checkboxes2'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" name="Checkboxes2"/>
                        <span></span>&nbsp;
                        <?= __('SL – Sick Leave'); ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<div class="input-group-append">

                         <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" style="width:50px" placeholder=""/>
                         <span class="input-group-text"><?= __('days'); ?></span>
                       </div>
                     </label>

                     <label class="checkbox">
                      <input aria-label="<?= htmlspecialchars(__('Checkboxes2'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" name="Checkboxes2"/>
                      <span></span>&nbsp;
                      <?= __('ML – Maternity Leave'); ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<div class="input-group-append">

                       <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" style="width:50px" placeholder=""/>
                       <span class="input-group-text"><?= __('days'); ?></span>
                     </div>
                   </label>

                   <label class="checkbox">
                    <input aria-label="<?= htmlspecialchars(__('Checkboxes2'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" name="Checkboxes2"/>
                    <span></span>&nbsp;
                    <?= __('PL – Paternity Leave'); ?> &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<div class="input-group-append">

                     <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" style="width:50px" placeholder=""/>
                     <span class="input-group-text"><?= __('days'); ?></span>
                   </div>
                 </label>

                 <label class="checkbox">
                  <input aria-label="<?= htmlspecialchars(__('Checkboxes2'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" name="Checkboxes2"/>
                  <span></span>&nbsp;
                  <?= __('P-Leave – Mensuration Leave'); ?>  &nbsp;
                  <div class="input-group-append">

                   <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" style="width:50px" placeholder=""/>
                   <span class="input-group-text"><?= __('days'); ?></span>
                 </div>
               </label>
             </div>

             <!-- End of qustions -->

 </div>
 <div class="card-footer">
  <div class="row">
   <div class="col-lg-3"></div>
   <div class="col-lg-6">
    <button type="reset" class="btn btn-success mr-2"><?= __('Submit'); ?></button>
    <button type="reset" class="btn btn-secondary"><?= __('Cancel'); ?></button>
   </div>
  </div>
 </div>
</form>
</div>
</div>

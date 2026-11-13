                      <div class="card border-top-0">
                       <div class="card-header">
                         <form class="form">
                           <div class="card-body">
                            <h3 class="font-size-lg text-dark font-weight-bold mb-6"><?= __('1. How would you like to monitor Attendance?'); ?> </h3>
                            <div class="mb-15">

                              <div class="checkbox-inline">
                                <label class="checkbox">
                                  <input aria-label="<?= htmlspecialchars(__('Attend Q1'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" name="attend_q1"/>
                                  <span></span>
                                  <?= __('System Check-In'); ?>
                                </label>
                                <label class="checkbox">
                                  <input aria-label="<?= htmlspecialchars(__('Attend Q1'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" name="attend_q1"/>
                                  <span></span>
                                  <?= __('App Check - In'); ?>
                                </label>
                                <label class="checkbox">
                                  <input aria-label="<?= htmlspecialchars(__('Attend Q1'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" name="attend_q1"/>
                                  <span></span>
                                  <?= __('Biometric'); ?>
                                </label>
                                <label class="checkbox">
                                  <input aria-label="<?= htmlspecialchars(__('Attend Q1'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" name="attend_q1"/>
                                  <span></span>
                                  <?= __('Manual Upload'); ?>
                                </label>
                              </div>
                            </div>
                            <h3 class="font-size-lg text-dark font-weight-bold mb-6"><?= __('2. How would you like to manage Attendance?'); ?> </h3>
                            <div class="mb-15">

                              <div class="checkbox-inline">
                                <label class="checkbox">
                                  <input aria-label="<?= htmlspecialchars(__('Attend Q2'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" name="attend_q2"/>
                                  <span></span>
                                  <?= __('Check In'); ?>
                                </label>
                                <label class="checkbox">
                                  <input aria-label="<?= htmlspecialchars(__('Attend Q2'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" name="attend_q2"/>
                                  <span></span>
                                  <?= __('Check Out'); ?>
                                </label>

                              </div>
                            </div>
                            <h3 class="font-size-lg text-dark font-weight-bold mb-6"><?= __('3. How many hours of Working?'); ?> </h3>
                            <div class="mb-15">

                              <div class="col-lg-6">

                               <div class="input-group-append">

                                 <input aria-label="<?= htmlspecialchars(__('Attend Q3'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" name="attend_q3" placeholder=""/>
                                 <span class="input-group-text"><?= __('hours'); ?></span>
                               </div>

                             </div>

                           </div>

                           <h3 class="font-size-lg text-dark font-weight-bold mb-6"><?= __('4.How would you like to calculate the work hours?'); ?> </h3>
                           <div class="mb-15">

                            <div class="radio-inline">
                              <label class="radio">
                                <input aria-label="<?= htmlspecialchars(__('Attend Q4'), ENT_QUOTES, 'UTF-8'); ?>" type="radio" name="attend_q4"/>
                                <span></span>
                                <?= htmlspecialchars(__('Last Check-In & Last Check-Out'), ENT_QUOTES, 'UTF-8'); ?>
                              </label>
                              <label class="radio">
                                <input aria-label="<?= htmlspecialchars(__('Attend Q4'), ENT_QUOTES, 'UTF-8'); ?>" type="radio" name="attend_q4"/>
                                <span></span>
                                <?= __('Multiple Log in Calculation'); ?>
                              </label>

                            </div>

                          </div>

                          <h3 class="font-size-lg text-dark font-weight-bold mb-6"><?= __('5.How would you like to calculate the Absent Status?'); ?></h3>
                          <div class="mb-15">

                            <div class="radio-inline">
                              <label class="radio">
                                <input aria-label="<?= htmlspecialchars(__('Attend Q5'), ENT_QUOTES, 'UTF-8'); ?>" type="radio" name="attend_q5"/>
                                <span></span>
                                <?= __('Late Check In - After'); ?>      <div class="input-group-append">

                                 <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder=""/>
                                 <span class="input-group-text"><?= __('hours'); ?></span>
                               </div>

                             </label>
                             <label class="radio">
                              <input aria-label="Io1" type="radio" name="radio1"/>
                              <span></span>  &nbsp; &nbsp;
                              <?= htmlspecialchars(__('No Log In & No Leave Application'), ENT_QUOTES, 'UTF-8'); ?>
                            </label>

                          </div>

                        </div>

                        <!-- Start Qustions -->

                        <h3 class="font-size-lg text-dark font-weight-bold mb-6"><?= __('6.What is Your Attendance Cycle?'); ?> </h3>
                        <div class="mb-15">

                          <div class="radio-inline">
                            <label class="radio">
                              <input aria-label="Ios3" type="radio" name="radios3"/>
                              <span></span>
                              <?= __('Monthly - DD – DD of Every Month'); ?>
                            </label>
                            <label class="radio">
                              <input aria-label="Ios3" type="radio" name="radios3"/>
                              <span></span>
                              <?= __('Weekly - DD – DD of Every Month'); ?>
                            </label>

                          </div>

                        </div>

                        <h3 class="font-size-lg text-dark font-weight-bold mb-6"><?= __('7.Select working days in a week?'); ?> </h3>

                        <div class="mb-15">

                          <div class="checkbox-inline">
                            <label class="checkbox">
                              <input aria-label="<?= htmlspecialchars(__('Checkboxes2'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" name="Checkboxes2"/>
                              <span></span>
                              <?= __('Monday'); ?>
                            </label>
                            <label class="checkbox">
                              <input aria-label="<?= htmlspecialchars(__('Checkboxes2'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" name="Checkboxes2"/>
                              <span></span>
                              <?= __('Tuesday'); ?>
                            </label>

                            <label class="checkbox">
                              <input aria-label="<?= htmlspecialchars(__('Checkboxes2'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" name="Checkboxes2"/>
                              <span></span>
                              <?= __('Wednesday'); ?>
                            </label>

                            <label class="checkbox">
                              <input aria-label="<?= htmlspecialchars(__('Checkboxes2'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" name="Checkboxes2"/>
                              <span></span>
                              <?= __('Thursday'); ?>
                            </label>

                            <label class="checkbox">
                              <input aria-label="<?= htmlspecialchars(__('Checkboxes2'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" name="Checkboxes2"/>
                              <span></span>
                              <?= __('Friday'); ?>
                            </label>

                            <label class="checkbox">
                              <input aria-label="<?= htmlspecialchars(__('Checkboxes2'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" name="Checkboxes2"/>
                              <span></span>
                              <?= __('Saturday'); ?>
                            </label>

                            <label class="checkbox">
                              <input aria-label="<?= htmlspecialchars(__('Checkboxes2'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" name="Checkboxes2"/>
                              <span></span>
                              <?= __('Sunday'); ?>
                            </label>
                          </div>
                        </div>

                        <h3 class="font-size-lg text-dark font-weight-bold mb-6"><?= __('8.What are Off Days?'); ?> </h3>

                        <div class="mb-15">

                          <div class="checkbox-inline">
                            <label class="checkbox">
                              <input aria-label="<?= htmlspecialchars(__('Checkboxes2'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" name="Checkboxes2"/>
                              <span></span>
                              <?= __('Different Days as Per Shift'); ?>
                            </label>
                          </div>
                        </div>

                        <h3 class="font-size-lg text-dark font-weight-bold mb-6"><?= __('9.Do You operate in Shifts?'); ?> </h3>

                        <div class="mb-15">

                          <div class="radio-inline">
                            <label class="radio">
                              <input aria-label="Ios4" type="radio" name="radios4"/>
                              <span></span>
                              <?= __('Yes'); ?>
                            </label>

                            <label class="radio">
                              <input aria-label="Ios4" type="radio" name="radios4"/>
                              <span></span>
                              <?= __('No'); ?>
                            </label>
                          </div>
                        <div class="radio-inline">
                          <label class="radio">
                            <input aria-label="Io5" type="radio" name="radio5"/>
                            <span></span>
                            <?= __('General Shift'); ?>  &nbsp;<div class="input-group-append">

                             <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" style="width: 100px" placeholder=""/>
                             <span class="input-group-text"><?= __('Starting Hours'); ?></span>
                           </div>
                           -&nbsp; <div class="input-group-append">

                             <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" style="width: 100px" placeholder=""/>
                             <span class="input-group-text"><?= __('Ending Hours'); ?></span>
                           </div>

                         </label>

                         <label class="radio">
                          <input aria-label="Io5" type="radio" name="radio5"/>
                          <span></span>
                          <?= __('Shift A'); ?>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;<div class="input-group-append">

                           <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" style="width: 100px" placeholder=""/>
                           <span class="input-group-text"><?= __('Starting Hours'); ?></span>
                         </div>
                         -&nbsp; <div class="input-group-append">

                           <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" style="width: 100px"  placeholder=""/>
                           <span class="input-group-text"><?= __('Ending Hours'); ?></span>
                         </div>

                       </label>

                       <label class="radio">
                        <input aria-label="Io5" type="radio" name="radio5"/>
                        <span></span>
                        <?= __('Shift B'); ?>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;<div class="input-group-prepend">

                         <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" style="width: 100px" placeholder=""/>
                         <span class="input-group-text"><?= __('Starting Hours'); ?></span>
                       </div>
                       -&nbsp; <div class="input-group-append">

                         <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" style="width: 100px"  placeholder=""/>
                         <span class="input-group-text"><?= __('Ending Hours'); ?></span>
                       </div>

                     </label>

                     <label class="radio">
                      <input aria-label="Io5" type="radio" name="radio5"/>
                      <span></span>
                      <?= __('Shift C'); ?>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;<div class="input-group-prepend">

                       <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" style="width: 100px" placeholder=""/>
                       <span class="input-group-text"><?= __('Starting Hours'); ?></span>
                     </div>
                     -&nbsp; <div class="input-group-append">

                       <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" style="width: 100px"  placeholder=""/>
                       <span class="input-group-text"><?= __('Ending Hours'); ?></span>
                     </div>

                   </label>

</div>
                 </div>

</div>
                 <!-- End of qustions -->
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

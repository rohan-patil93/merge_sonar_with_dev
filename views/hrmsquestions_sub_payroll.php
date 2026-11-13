                      <div class="card border-top-0">
                       <div class="card-header">
                         <form class="form">
                           <div class="card-body">
                            <h3 class="font-size-lg text-dark font-weight-bold mb-6"><?= __('1. What will be the Payroll Cycle?'); ?>  </h3>
                            <div class="mb-15">
                              <div class="radio-inline">
                                <label class="radio">
                                  <input aria-label="Ios2" type="radio" name="radios2"/>
                                  <span></span>
                                  <?= __('Monthly - DD of the month'); ?><div class="input-group-append">

                                   <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" style="width:150px" placeholder=""/>
                                 </div>

                               </div>
                             </div>

                             <h3 class="font-size-lg text-dark font-weight-bold mb-6"><?= __('2. What will be the Pay Preference?'); ?>  </h3>
                             <div class="mb-15">

                              <div class="radio-inline">
                                <label class="radio">
                                  <input aria-label="Ios2" type="radio" name="radios2"/>
                                  <span></span>
                                  <?= __('Calendar Working Days'); ?>  &nbsp;&nbsp;
                                  <label class="radio">
                                    <input aria-label="Ios2" type="radio" name="radios2"/>
                                    <span></span>
                                    <?= __('Actual Working Days'); ?>
                                  </label>

                                </div>

                              </div>  <h3 class="font-size-lg text-dark font-weight-bold mb-6"><?= __('3. What is the PAN number of the Company?'); ?> </h3>
                              <div class="mb-15">
                               <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" style="width:300px" placeholder=""/>
                             </div>

                             <h3 class="font-size-lg text-dark font-weight-bold mb-6"><?= __('4. Pay Frequency'); ?>  </h3>
                             <div class="mb-15">
                              <div class="radio-inline">
                                <label class="radio">
                                  <input aria-label="Ios3" type="radio" name="radios3"/>
                                  <span></span>
                                  <?= __('Weekly'); ?>  &nbsp;&nbsp;
                                  <label class="radio">
                                    <input aria-label="Ios3" type="radio" name="radios3"/>
                                    <span></span>
                                    <?= __('Biweekly'); ?>
                                  </label>
                                  <label class="radio">
                                    <input aria-label="Ios3" type="radio" name="radios3"/>
                                    <span></span>
                                    <?= __('Monthly'); ?>
                                  </label>
                                  <label class="radio">
                                    <input aria-label="Ios3" type="radio" name="radios3"/>
                                    <span></span>
                                    <?= __('Quaterly'); ?>
                                  </label>
                                  <label class="radio">
                                    <input aria-label="Ios3" type="radio" name="radios3"/>
                                    <span></span>
                                    <?= __('Yearly'); ?>
                                  </label>
                                </div>

                                <?= __('Working Days'); ?>
                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" style="width:300px" placeholder=""/>
                                <?= __('Pay day'); ?>

                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" style="width:300px" placeholder=""/>
                                <?= __('First Pay Period'); ?>
                                <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" style="width:300px" placeholder=""/>
                              </div>

                              <h3 class="font-size-lg text-dark font-weight-bold mb-6"><?= __('7.Tax Details ?'); ?> </h3>
                              <div class="mb-15"> <?= __('Organisation PAN'); ?>
                               <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" style="width:300px" placeholder=""/>
                               <?= __('Organisation TAN'); ?>
                               <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" style="width:300px" placeholder=""/>
                             </div>

                             <h3 class="font-size-lg text-dark font-weight-bold mb-6"><?= __('8.TDS Circle ?'); ?> </h3>
                             <div class="mb-15"> <?= __('Tax Payment Frequency'); ?>
                               <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" style="width:300px" placeholder=""/>
                               <?= __('Tax Deductor’s Details'); ?>:
                               <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" style="width:300px" placeholder=""/>

                               <?= __('Deductor’s Name'); ?>:
                               <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" style="width:300px" placeholder=""/>
                               <?= __('Deductor’s Father Name'); ?>:
                               <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" style="width:300px" placeholder=""/>
                             </div>

                             <h3 class="font-size-lg text-dark font-weight-bold mb-6"><?= __('9.What are the Work Location ?'); ?> </h3>
                             <div class="mb-15">


                                            <div class="btn btn-icon btn-clean btn-dropdown btn-lg mr-1" id="kt_quick_payroll_q9_toggle">
                                              <span class="svg-icon svg-icon-xl svg-icon-primary">
                                                <?= __('Fill'); ?>
                                              </span>
                                            </div>


                                            <div id="kt_quick_hrmsq_payroll_q9" class="offcanvas offcanvas-right p-10">
                                              <div class="offcanvas-header d-flex align-items-center justify-content-between pb-7">
                                                <h4 class="font-weight-bold m-0"><?= __('Fill up'); ?></h4>
                                                <a href="#" class="btn btn-xs btn-icon btn-light btn-hover-primary" id="kt_quick_payroll_q9_close">
                                                  <i class="ki ki-close icon-xs text-muted"></i>
                                                </a>
                                              </div>

                                              <?= __('CTC'); ?> <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control"  placeholder=""/> <br>
                                              <?= __('CTC'); ?> <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control"  placeholder=""/><br>
                                              <?= __('CTC'); ?> <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control"  placeholder=""/><br>
                                              <?= __('CTC'); ?> <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control"  placeholder=""/><br>
                                              <?= __('CTC'); ?>  <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control"  placeholder=""/>
                                              <div class="card-footer">
                                                <div class="row">
                                                 <div class="col-lg-3"></div>
                                                 <div class="col-lg-6">
                                                  <button type="reset" class="btn btn-success mr-2"><?= __('Submit'); ?></button>
                                                </div>
                                              </div>
                                            </div>
                                          </div>

                             </div>

                             <h3 class="font-size-lg text-dark font-weight-bold mb-6"><?= __('10.Number of Employees ?'); ?> </h3>
                             <div class="mb-15">
                               <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" style="width:300px" placeholder=""/>
                             </div>

                             <h3 class="font-size-lg text-dark font-weight-bold mb-6"><?= __('11.Minimum Number of Employees required for PF 20 : Are you interested to Opt for it ?'); ?> </h3>

                             <div class="mb-15">

                              <div class="radio-inline">
                                <label class="radio">
                                  <input aria-label="Ios2" type="radio" name="radios2"/>
                                  <span></span>
                                  <?= __('Yes'); ?>  &nbsp;&nbsp;
                                  <label class="radio">
                                    <input aria-label="Ios2" type="radio" name="radios2"/>
                                    <span></span>
                                    <?= __('No'); ?>
                                  </label>

                                </div>

                              </div>

                              <h3 class="font-size-lg text-dark font-weight-bold mb-6"><?= __('12.Minimum Number of Employees required for ESI (Whose Gross Salary is Less than 21K) 10 : Are you interested to Opt for it'); ?> </h3>

                              <div class="mb-15">

                                <div class="radio-inline">
                                  <label class="radio">
                                    <input aria-label="Ios2" type="radio" name="radios2"/>
                                    <span></span>
                                    <?= __('Yes'); ?>  &nbsp;&nbsp;
                                    <label class="radio">
                                      <input aria-label="Ios2" type="radio" name="radios2"/>
                                      <span></span>
                                      <?= __('No'); ?>
                                    </label>

                                  </div>

                                </div>

                                <h3 class="font-size-lg text-dark font-weight-bold mb-6"><?= __('13. Do you want PF contribution to be part of CTC?'); ?> </h3>

                                <div class="mb-15">

                                  <div class="radio-inline">
                                    <label class="radio">
                                      <input aria-label="Ios2" type="radio" name="radios2"/>
                                      <span></span>
                                      <?= __('Yes'); ?>  &nbsp;&nbsp;
                                      <label class="radio">
                                        <input aria-label="Ios2" type="radio" name="radios2"/>
                                        <span></span>
                                        <?= __('No'); ?>
                                      </label>

                                    </div>

                                  </div>

                                  <h3 class="font-size-lg text-dark font-weight-bold mb-6"><?= __('14. Do you want gratuity contribution to be part of CTC?'); ?> </h3>

                                  <div class="mb-15">

                                    <div class="radio-inline">
                                      <label class="radio">
                                        <input aria-label="Ios2" type="radio" name="radios2"/>
                                        <span></span>
                                        <?= __('Yes'); ?>  &nbsp;&nbsp;
                                        <label class="radio">
                                          <input aria-label="Ios2" type="radio" name="radios2"/>
                                          <span></span>
                                          <?= __('No'); ?>
                                        </label>

                                      </div>

                                    </div>

                                    <h3 class="font-size-lg text-dark font-weight-bold mb-6"><?= __('15.Do you want ESI contribution to be part of CTC?'); ?>  </h3>

                                    <div class="mb-15">

                                      <div class="radio-inline">
                                        <label class="radio">
                                          <input aria-label="Ios2" type="radio" name="radios2"/>
                                          <span></span>
                                          <?= __('Yes'); ?>  &nbsp;&nbsp;
                                          <label class="radio">
                                            <input aria-label="Ios2" type="radio" name="radios2"/>
                                            <span></span>
                                            <?= __('No'); ?>
                                          </label>

                                        </div>

                                      </div>

                                      <h3 class="font-size-lg text-dark font-weight-bold mb-6"><?= __('16.Do you want Super annuation contribution to be part of CTC?'); ?>  </h3>

                                      <div class="mb-15">

                                        <div class="radio-inline">
                                          <label class="radio">
                                            <input aria-label="Ios2" type="radio" name="radios2"/>
                                            <span></span>
                                            <?= __('Yes'); ?>  &nbsp;&nbsp;
                                            <label class="radio">
                                              <input aria-label="Ios2" type="radio" name="radios2"/>
                                              <span></span>
                                              <?= __('No'); ?>
                                            </label>

                                          </div>

                                        </div>

                                        <h3 class="font-size-lg text-dark font-weight-bold mb-6"><?= __('17.Do you want ESOP to be part of CTC?'); ?>  </h3>

                                        <div class="mb-15">

                                          <div class="radio-inline">
                                            <label class="radio">
                                              <input aria-label="Ios2" type="radio" name="radios2"/>
                                              <span></span>
                                              <?= __('Yes'); ?>  &nbsp;&nbsp;
                                              <label class="radio">
                                                <input aria-label="Ios2" type="radio" name="radios2"/>
                                                <span></span>
                                                <?= __('No'); ?>
                                              </label>

                                            </div>

                                          </div>

                                          <h3 class="font-size-lg text-dark font-weight-bold mb-6"><?= __('18.Do want to opt for any flexi benefit plan?'); ?>  </h3>

                                          <div class="mb-15">

                                            <div class="radio-inline">
                                              <label class="radio">
                                                <input aria-label="Ios2" type="radio" name="radios2"/>
                                                <span></span>
                                                <?= __('Yes'); ?>  &nbsp;&nbsp;
                                                <label class="radio">
                                                  <input aria-label="Ios2" type="radio" name="radios2"/>
                                                  <span></span>
                                                  <?= __('No'); ?>
                                                </label>

                                              </div>

                                            </div>

                                            <h3 class="font-size-lg text-dark font-weight-bold mb-6"><?= __('19. What component you want to make a dynamic part of your CTC'); ?>  </h3>


                                            <div class="btn btn-icon btn-clean btn-dropdown btn-lg mr-1" id="kt_quick_payroll_q19_toggle">
                                              <span class="svg-icon svg-icon-xl svg-icon-primary">
                                                <?= __('Fill'); ?>
                                              </span>
                                            </div>


                                            <div id="kt_quick_hrmsq_payroll_q19" class="offcanvas offcanvas-right p-10">
                                              <div class="offcanvas-header d-flex align-items-center justify-content-between pb-7">
                                                <h4 class="font-weight-bold m-0"><?= __('Fill up'); ?></h4>
                                                <a href="#" class="btn btn-xs btn-icon btn-light btn-hover-primary" id="kt_quick_payroll_q19_close">
                                                  <i class="ki ki-close icon-xs text-muted"></i>
                                                </a>
                                              </div>

                                              <?= __('CTC'); ?> <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control"  placeholder=""/> <br>
                                              <?= __('CTC'); ?> <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control"  placeholder=""/><br>
                                              <?= __('CTC'); ?> <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control"  placeholder=""/><br>
                                              <?= __('CTC'); ?> <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control"  placeholder=""/><br>
                                              <?= __('CTC'); ?>  <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control"  placeholder=""/>
                                              <div class="card-footer">
                                                <div class="row">
                                                 <div class="col-lg-3"></div>
                                                 <div class="col-lg-6">
                                                  <button type="reset" class="btn btn-success mr-2"><?= __('Submit'); ?></button>
                                                </div>
                                              </div>
                                            </div>
                                          </div>


                                          <!-- End of qustions -->

                                        </div>
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

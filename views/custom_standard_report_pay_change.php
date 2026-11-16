 <div class="page-body">
  <div class="card">
    <div class="card-header">
      <h5 class="f-30"><?= __('Payroll Change'); ?></h5>
      <h6 class="mt-4"><?= __('85 People'); ?></h6>
      <div class="card-header-right">
        <button class="btn bg-gray waves-effect waves-dark btn-primary btn-outline-primary btn-icon mb-0 m-r-10">
          <i class="fa fa-user"></i>
        </button>
        <div class="dropdown-primary dropdown open">
          <button class="btn btn-primary dropdown-toggle waves-effect waves-light" type="button" id="dropdown-2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?= __('More'); ?></button>
          <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut" style="">
            <a class="dropdown-item waves-light waves-effect" href="#"><?= __('PDF'); ?></a>
            <a class="dropdown-item waves-light waves-effect" href="#"><?= __('CSV'); ?></a>
            <a class="dropdown-item waves-light waves-effect" href="#"><?= __('Excel'); ?></a>
          </div>
        </div>
      </div>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-lg-2">
          <div class="form-group">
            <span><?= __('Pay Period End Date'); ?></span>
            <div class="input-group">
              <input aria-label="<?= htmlspecialchars(__('Select date'), ENT_QUOTES, 'UTF-8'); ?>" type="date" class="form-control" placeholder="">
            </div>
          </div>
        </div>
        <div class="col-lg-3">
          <span></span>
          <nav class="all-employee">
            <ul>
              <li class="d-block border dropbtn"><a href="#"><?= __('All Employees'); ?></a>
                <ul>
                  <li><a href="#"><?= __('Department'); ?> <i class="ti-angle-right float-end f-12 m-t-5"></i></a>
                    <ul>
                      <li>
                        <div class="border-checkbox-section">
                          <div class="border-checkbox-group border-checkbox-group-primary">
                            <input class="border-checkbox" type="checkbox" id="success">
                            <label class="form-label border-checkbox-label" for="success"><?= __('Customer Success'); ?></label>
                          </div>
                        </div>
                      </li>
                      <li>
                        <div class="border-checkbox-section">
                          <div class="border-checkbox-group border-checkbox-group-primary">
                            <input class="border-checkbox" type="checkbox" id="finance">
                            <label class="form-label border-checkbox-label" for="finance"><?= __('Finance'); ?></label>
                          </div>
                        </div>
                      </li>
                      <li>
                        <div class="border-checkbox-section">
                          <div class="border-checkbox-group border-checkbox-group-primary">
                            <input class="border-checkbox" type="checkbox" id="resources">
                            <label class="form-label border-checkbox-label" for="resources"><?= __('Human Resources'); ?></label>
                          </div>
                        </div>
                      </li>
                      <li>
                        <div class="border-checkbox-section">
                          <div class="border-checkbox-group border-checkbox-group-primary">
                            <input class="border-checkbox" type="checkbox" id="it">
                            <label class="form-label border-checkbox-label" for="it"><?= __('IT'); ?></label>
                          </div>
                        </div>
                      </li>
                      <li>
                        <div class="border-checkbox-section">
                          <div class="border-checkbox-group border-checkbox-group-primary">
                            <input class="border-checkbox" type="checkbox" id="marketing">
                            <label class="form-label border-checkbox-label" for="marketing"><?= __('Marketing'); ?></label>
                          </div>
                        </div>
                      </li>
                      <li>
                        <div class="border-checkbox-section">
                          <div class="border-checkbox-group border-checkbox-group-primary">
                            <input class="border-checkbox" type="checkbox" id="operations">
                            <label class="form-label border-checkbox-label" for="operations"><?= __('Operations'); ?></label>
                          </div>
                        </div>
                      </li>
                      <li>
                        <div class="border-checkbox-section">
                          <div class="border-checkbox-group border-checkbox-group-primary">
                            <input class="border-checkbox" type="checkbox" id="product">
                            <label class="form-label border-checkbox-label" for="product"><?= __('Product'); ?></label>
                          </div>
                        </div>
                      </li>
                      <li>
                        <div class="border-checkbox-section">
                          <div class="border-checkbox-group border-checkbox-group-primary">
                            <input class="border-checkbox" type="checkbox" id="sales">
                            <label class="form-label border-checkbox-label" for="sales"><?= __('Sales'); ?></label>
                          </div>
                        </div>
                      </li>
                      <li>
                        <div class="border-checkbox-section">
                          <div class="border-checkbox-group border-checkbox-group-primary">
                            <input class="border-checkbox" type="checkbox" id="ux">
                            <label class="form-label border-checkbox-label" for="ux"><?= __('UX'); ?></label>
                          </div>
                        </div>
                      </li>
                    </ul>
                  </li>
                  <li><a href="#"><?= __('Division'); ?> <i class="ti-angle-right float-end f-12 m-t-5"></i></a>
                    <ul>
                      <li>
                        <div class="border-checkbox-section">
                          <div class="border-checkbox-group border-checkbox-group-primary">
                            <input class="border-checkbox" type="checkbox" id="asia">
                            <label class="form-label border-checkbox-label" for="asia"><?= __('Asia-Pacific'); ?></label>
                          </div>
                        </div>
                      </li>
                      <li>
                        <div class="border-checkbox-section">
                          <div class="border-checkbox-group border-checkbox-group-primary">
                            <input class="border-checkbox" type="checkbox" id="europe">
                            <label class="form-label border-checkbox-label" for="europe"><?= __('Europe'); ?></label>
                          </div>
                        </div>
                      </li>
                      <li>
                        <div class="border-checkbox-section">
                          <div class="border-checkbox-group border-checkbox-group-primary">
                            <input class="border-checkbox" type="checkbox" id="america">
                            <label class="form-label border-checkbox-label" for="america"><?= __('North America'); ?></label>
                          </div>
                        </div>
                      </li>
                    </ul>
                  </li>
                  <li><a href="#"><?= __('Employment Status'); ?> <i class="ti-angle-right float-end f-12 m-t-5"></i></a>
                    <ul>
                      <li>
                        <div class="border-checkbox-section">
                          <div class="border-checkbox-group border-checkbox-group-primary">
                            <input aria-label="<?= htmlspecialchars(__('Contractor'), ENT_QUOTES, 'UTF-8'); ?>" class="border-checkbox" type="checkbox" id="contractor">
                            <label class="form-label border-checkbox-label" for="contrctor"><?= __('Contractor'); ?></label>
                          </div>
                        </div>
                      </li>
                      <li>
                        <div class="border-checkbox-section">
                          <div class="border-checkbox-group border-checkbox-group-primary">
                            <input class="border-checkbox" type="checkbox" id="Full-Time">
                            <label class="form-label border-checkbox-label" for="Full-Time"><?= __('Full-Time'); ?></label>
                          </div>
                        </div>
                      </li>
                      <li>
                        <div class="border-checkbox-section">
                          <div class="border-checkbox-group border-checkbox-group-primary">
                            <input class="border-checkbox" type="checkbox" id="furloughed">
                            <label class="form-label border-checkbox-label" for="furloughed"><?= __('Furloughed'); ?></label>
                          </div>
                        </div>
                      </li>
                      <li>
                        <div class="border-checkbox-section">
                          <div class="border-checkbox-group border-checkbox-group-primary">
                            <input class="border-checkbox" type="checkbox" id="intern">
                            <label class="form-label border-checkbox-label" for="intern"><?= __('Intern'); ?></label>
                          </div>
                        </div>
                      </li>
                      <li>
                        <div class="border-checkbox-section">
                          <div class="border-checkbox-group border-checkbox-group-primary">
                            <input class="border-checkbox" type="checkbox" id="part-time">
                            <label class="form-label border-checkbox-label" for="part-time"><?= __('Part-Time'); ?></label>
                          </div>
                        </div>
                      </li>
                      <li>
                        <div class="border-checkbox-section">
                          <div class="border-checkbox-group border-checkbox-group-primary">
                            <input class="border-checkbox" type="checkbox" id="terminated">
                            <label class="form-label border-checkbox-label" for="terminated"><?= __('Terminated'); ?></label>
                          </div>
                        </div>
                      </li>
                    </ul>
                  </li>
                  <li><a href="#"><?= __('Job Title'); ?> <i class="ti-angle-right float-end f-12 m-t-5"></i></a>
                    <ul>
                      <li>
                        <div class="border-checkbox-section">
                          <div class="border-checkbox-group border-checkbox-group-primary">
                            <input class="border-checkbox" type="checkbox" id="terminated_2">
                            <label class="form-label border-checkbox-label" for="terminated_2"><?= __('Terminated'); ?></label>
                          </div>
                        </div>
                      </li>
                      <li><a href="#"><?= __('Submenu 1-2-2'); ?></a></li>
                    </ul>
                  </li>
                  <li><a href="#"><?= __('Location'); ?> <i class="ti-angle-right float-end f-12 m-t-5"></i></a>
                    <ul>
                      <li><a href="#"><?= __('Lindon, Utah'); ?></a></li>
                      <li><a href="#"><?= __('London, UK'); ?></a></li>
                      <li><a href="#"><?= __('Remote Worker'); ?></a></li>
                      <li><a href="#"><?= __('Sydney, Australia'); ?></a></li>
                      <li><a href="#"><?= __('Vancouver, Canada'); ?></a></li>
                    </ul>
                  </li>
                </ul>
              </li>
            </ul>
          </nav>
        </div>
        <div class="col-lg-2">
          <span ></span>
          <a class="text-primary f-w-600 d-inline-block m-t-25" href=""><?= __('Reset'); ?></a>
        </div>
        <div class="col-lg-5 text-end mt-3">
          <span class="m-r-10"><?= __('Uncompleted Change'); ?></span>
          <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="border p-1">
            <option><?= __('Uncompleted Change'); ?></option>
            <option><?= __('Completed Change'); ?></option>
          </select>
          <button class="btn waves-effect waves-light btn-primary btn-icon"><i class="fa fa-refresh"></i></button>
        </div>
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-body">
      <div class="text-center mt-5 mb-5">
        <img src="../files/assets/images/payroll-change.jpg" class="img-100 mb-5" alt="">
        <p><?= __('Before using the Payroll Change Report for the first time, please confirm'); ?><br /><?= __('that employee payroll information in BambooHR matches'); ?><br /><?= __('the information in your payroll system.'); ?></p>
        <button class="btn waves-effect waves-light btn-grd-primary mt-3"><?= __('Start Tracking Changes'); ?></button>
      </div>
    </div>
  </div>
</div>
<!-- Page body end -->
</div>
</div>

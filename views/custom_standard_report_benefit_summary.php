<div class="page-body">
  <div class="card">
    <div class="card-header">
      <h5 class="f-30"><?= __('Benefit Summary'); ?></h5>
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
          <div class="dropdown border w-100">
<button class="dropbtn w-100 text-start"><?= __('All Sources (6)'); ?></button>
<div class="dropdown-content">
<label><input aria-label="<?= htmlspecialchars(__('Item1'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" name="item1" value="item1"> <?= __('Indeed'); ?></label>
<label><input aria-label="<?= htmlspecialchars(__('Item2'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" name="item2" value="item2"> <?= __('Facebook'); ?></label>
<label><input aria-label="<?= htmlspecialchars(__('Item3'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" name="item3" value="item3"> <?= __('Glassdoor'); ?></label>
<label><input aria-label="<?= htmlspecialchars(__('Item1'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" name="item1" value="item1"> <?= __('ZipRecruiter'); ?></label>
<label><input aria-label="<?= htmlspecialchars(__('Item2'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" name="item2" value="item2"> <?= __('Linkedin'); ?></label>
<label><input aria-label="<?= htmlspecialchars(__('Item3'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" name="item3" value="item3"> <?= __('Other'); ?></label>
</div>
</div>
        </div>
        <div class="col-lg-2">
          <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control border">
            <option><?= __('401(K) Plan'); ?></option>
            <option><?= __('HDHP 1'); ?></option>
            <option><?= __('HDHP 2'); ?></option>
            <option><?= __('FSA-Eligible HMO'); ?></option>
            <option><?= __('Dental Basic'); ?></option>
            <option><?= __('Dental Plus'); ?></option>
            <option><?= __('Vision Insaurance'); ?></option>
            <option><?= __('Hospital Indemnity'); ?></option>
            <option><?= __('Accident'); ?></option>
            <option><?= __('Critical Illness'); ?></option>
            <option><?= __('HSA'); ?></option>
            <option><?= __('Medical FSA'); ?></option>
            <option><?= __('Basic Life'); ?></option>
            <option><?= __('Voluntary Life'); ?></option>
          </select>
        </div>
        <div class="col-lg-3">
          <nav class="all-employee">
            <ul>
              <li class="d-block border"><a href="#"><?= __('All Employees'); ?></a>
                <ul>
                  <li><a href="#"><?= __('Department'); ?></a>
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
                  <li><a href="#"><?= __('Division'); ?></a>
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
                  <li><a href="#"><?= __('Employment Status'); ?></a>
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
                  <li><a href="#"><?= __('Job Title'); ?></a>
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
                  <li><a href="#"><?= __('Location'); ?></a>
                    <ul>
                      <li><a href="#"><?= __('Submenu 1-2-1'); ?></a></li>
                      <li><a href="#"><?= __('Submenu 1-2-2'); ?></a></li>
                    </ul>
                  </li>
                </ul>
              </li>
            </ul>
          </nav>

        </div>
        <div class="col-lg-2">
          <a class="text-primary f-w-600 d-inline-block m-t-5" href=""><?= __('Reset'); ?></a>
        </div>
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-body">
      <div class="table-responsive mt-3">
        <table class="table table-hover">
          <tr style="background-color: #eeeeee;">
            <th scope="col"><?= __('Employee'); ?></th>
            <th scope="col"><?= __('Status'); ?></th>
            <th scope="col"><?= __('Effective Date'); ?></th>
            <th scope="col"><?= __('Employee Pays'); ?></th>
            <th scope="col"><?= __('Company Pays'); ?></th>
            <th scope="col"><?= __('Frequency'); ?></th>
          </tr>
          <tr>
            <td><a href=""><?= __('A, Raj'); ?></a></td>
            <td><?= __('Eligible'); ?></td>
            <td>02/25/2023</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
          </tr>
          <tr>
            <td><a href=""><?= __('Abbott, Charlotte'); ?></a></td>
            <td><?= __('Enrolled'); ?></td>
            <td>02/25/2023</td>
            <td>6%</td>
            <td>6%</td>
            <td><?= __('Twice a month'); ?></td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>

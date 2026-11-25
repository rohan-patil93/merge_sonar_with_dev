<div class="card table-card">
                                            <div class="card-header">
                                                <h5><?= __('Tickets'); ?></h5>
                                                <div class="card-header-right">
                                                    <button type="button" class="btn btn-primary waves-effect" data-bs-toggle="modal" data-bs-target="#large-Modal"><i class="ti-plus text-white"></i> <?= __('Large'); ?></button>
                                                    <div class="modal fade" id="large-Modal" tabindex="-1" style="z-index: 1050; display: none;" aria-hidden="true">
                                                        <div class="modal-dialog modal-lg" >
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title"><?= __('Add Ticket'); ?></h4>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="row">
                                                                        <div class="col-lg-6">
                                                                            <span class="form-label col-form-label"><?= __('Ticket Subject'); ?></span>
                                                                            <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control">
                                                                        </div>
                                                                        <div class="col-lg-6">
                                                                            <span class="form-label col-form-label"><?= __('Ticket ID'); ?></span>
                                                                            <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control">
                                                                        </div>
                                                                        <div class="col-lg-6">
                                                                            <span class="form-label col-form-label"><?= __('Assign Staff'); ?></span>
                                                                            <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control fill" style="Border:1px solid #cccccc">
                                                                                <option>- <?= __('Select'); ?> -</option>
                                                                                <option><?= __('Naresh'); ?></option>
                                                                                <option><?= __('Pradeep'); ?></option>
                                                                                <option><?= __('Ravi'); ?></option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-lg-6">
                                                                            <span class="form-label col-form-label"><?= __('Client'); ?></span>
                                                                            <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control fill" style="Border:1px solid #cccccc">
                                                                                <option>- <?= __('Select'); ?> -</option>
                                                                                <option><?= __('Bean'); ?></option>
                                                                                <option><?= __('Bean'); ?></option>
                                                                                <option><?= __('Bean'); ?></option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-lg-6">
                                                                            <span class="form-label col-form-label"><?= __('Priority'); ?></span>
                                                                            <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control fill" style="Border:1px solid #cccccc">
                                                                                <option>- <?= __('Select'); ?> -</option>
                                                                                <option><?= __('High'); ?></option>
                                                                                <option><?= __('Medium'); ?></option>
                                                                                <option><?= __('Low'); ?></option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-lg-6">
                                                                            <span class="form-label col-form-label"><?= __('CC'); ?></span>
                                                                            <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control">
                                                                        </div>
                                                                        <div class="col-lg-6">
                                                                            <span class="form-label col-form-label"><?= __('Assign'); ?></span>
                                                                            <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control">
                                                                        </div>
                                                                        <div class="col-lg-6">
                                                                            <span class="form-label col-form-label d-block"><?= __('Ticket Assignee'); ?></span>
                                                                            <img src="../files/assets/images/avatar-4.jpg" alt="<?= htmlspecialchars(__('user'), ENT_QUOTES, 'UTF-8'); ?>" class="img-radius img-30 align-middle">
                                                                        </div>
                                                                        <div class="col-lg-6">
                                                                            <span class="form-label col-form-label"><?= __('Add Followers'); ?></span>
                                                                            <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control">
                                                                        </div>
                                                                        <div class="col-lg-6">
                                                                            <span class="form-label col-form-label d-block"><?= __('Ticket Followers'); ?></span>
                                                                            <img src="../files/assets/images/avatar-4.jpg" alt="<?= htmlspecialchars(__('user'), ENT_QUOTES, 'UTF-8'); ?>" class="img-radius img-30 align-middle">
                                                                            <img src="../files/assets/images/avatar-4.jpg" alt="<?= htmlspecialchars(__('user'), ENT_QUOTES, 'UTF-8'); ?>" class="img-radius img-30 align-middle">
                                                                            <img src="../files/assets/images/avatar-4.jpg" alt="<?= htmlspecialchars(__('user'), ENT_QUOTES, 'UTF-8'); ?>" class="img-radius img-30 align-middle">
                                                                            <span class="d-inline-block f-w-600 f-24">+2</span>
                                                                        </div>
                                                                        <div class="col-lg-12">
                                                                            <span class="form-label col-form-label d-block"><?= __('Description'); ?></span>
                                                                            <textarea aria-label="<?= htmlspecialchars(__('Text area'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control"></textarea>
                                                                        </div>
                                                                        <div class="col-lg-12">
                                                                            <span class="form-label col-form-label d-block"><?= __('Upload Files'); ?></span>
                                                                            <input aria-label="<?= htmlspecialchars(__('File upload'), ENT_QUOTES, 'UTF-8'); ?>" type="file" class="form-control">
                                                                        </div>
                                                                        <div class="col-lg-12 mt-3">
                                                                            <button type="button" class="btn btn-success waves-effect waves-light float-right"><?= __('Submit'); ?></button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal"><?= __('Close'); ?></button>
                                                                    <button type="button" class="btn btn-primary waves-effect waves-light "><?= __('Save changes'); ?></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row card-body mt-3">
                                                <div class="col-xl-3 col-md-12">
                                                    <div class="card proj-t-card">
                                                        <div class="card-body">
                                                            <div class="align-items-center m-b-30">

                                                                <div class="col p-l-0">
                                                                    <h6 class="m-b-5"><?= __('New Tickets'); ?></h6>

                                                                </div>
                                                            </div>
                                                            <div class="align-items-center text-left">
                                                                <div class="col">
                                                                    <h3 class="m-b-0 f-w-600">112</h3>
                                                                </div>
                                                                <div class="col mt-4 proj-progress-card">
                                                                    <div class="progress">
                                                                        <div class="progress-bar bg-c-red" style="width:10%"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <h6 class="pt-badge bg-c-green">+10%</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-3 col-md-12">
                                                    <div class="card proj-t-card">
                                                        <div class="card-body">
                                                            <div class="align-items-center m-b-30">

                                                                <div class="col p-l-0">
                                                                    <h6 class="m-b-5"><?= __('Solved Tickets'); ?></h6>

                                                                </div>
                                                            </div>
                                                            <div class="align-items-center text-left">
                                                                <div class="col">
                                                                    <h3 class="m-b-0 f-w-600">70</h3>
                                                                </div>
                                                                <div class="col mt-4 proj-progress-card">
                                                                    <div class="progress">
                                                                        <div class="progress-bar bg-c-red" style="width:12.5%"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <h6 class="pt-badge bg-c-green">+12.5%</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-3 col-md-12">
                                                    <div class="card proj-t-card">
                                                        <div class="card-body">
                                                            <div class="align-items-center m-b-30">

                                                                <div class="col p-l-0">
                                                                    <h6 class="m-b-5"><?= __('Open Tickets'); ?></h6>

                                                                </div>
                                                            </div>
                                                            <div class="align-items-center text-left">
                                                                <div class="col">
                                                                    <h3 class="m-b-0 f-w-600">100</h3>
                                                                </div>
                                                                <div class="col mt-4 proj-progress-card">
                                                                    <div class="progress">
                                                                        <div class="progress-bar bg-c-red" style="width:-2.8%"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <h6 class="pt-badge bg-c-red">-2.8%</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-3 col-md-12">
                                                    <div class="card proj-t-card">
                                                        <div class="card-body">
                                                            <div class="align-items-center m-b-30">

                                                                <div class="col p-l-0">
                                                                    <h6 class="m-b-5"><?= __('Pending Tickets'); ?></h6>

                                                                </div>
                                                            </div>
                                                            <div class="align-items-center text-left">
                                                                <div class="col">
                                                                    <h3 class="m-b-0 f-w-600">125</h3>
                                                                </div>
                                                                <div class="col mt-4 proj-progress-card">
                                                                    <div class="progress">
                                                                        <div class="progress-bar bg-c-red" style="width:-75%"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <h6 class="pt-badge bg-c-red">-75%</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row card-body pt-0 mt-0">
                                                <div class="col-lg-2">
                                                    <span class="form-label col-form-label"><?= __('Employee Name'); ?></span>
                                                    <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control">
                                                </div>
                                                <div class="col-lg-2">
                                                    <span class="form-label col-form-label"><?= __('Status'); ?></span>
                                                    <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" style="Border:1px solid #cccccc">
                                                        <option>- <?= __('Select'); ?> -</option>
                                                        <option><?= __('Pending'); ?></option>
                                                        <option><?= __('Approved'); ?></option>
                                                        <option><?= __('Returned'); ?></option>
                                                    </select>
                                                </div>
                                                <div class="col-lg-2">
                                                    <span class="form-label col-form-label"><?= __('Priority'); ?></span>
                                                    <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" style="Border:1px solid #cccccc">
                                                        <option>- <?= __('Select'); ?> -</option>
                                                        <option><?= __('High'); ?></option>
                                                        <option><?= __('Low'); ?></option>
                                                        <option><?= __('Medium'); ?></option>
                                                    </select>
                                                </div>
                                                <div class="col-lg-2">
                                                    <span class="form-label col-form-label"><?= __('From'); ?></span>
                                                    <input aria-label="<?= htmlspecialchars(__('Select date'), ENT_QUOTES, 'UTF-8'); ?>" type="date" class="form-control">
                                                </div>
                                                <div class="col-lg-2">
                                                    <span class="form-label col-form-label"><?= __('To'); ?></span>
                                                    <input aria-label="<?= htmlspecialchars(__('Select date'), ENT_QUOTES, 'UTF-8'); ?>" type="date" class="form-control">
                                                </div>
                                                <div class="col-lg-2">
                                                    <span class="form-label col-form-label"></span>
                                                    <button class="btn waves-effect waves-light hor-grd btn-grd-success d-block w-100 m-t-10"><?= __('Search'); ?></button>
                                                </div>
                                            </div>

                                            <div class="table-responsive dt-responsive p-3">
                                                <table id="dom-jqry" class="table table-striped table-bordered nowrap">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">#</th>
                                                            <th scope="col"><?= __('Ticket ID'); ?></th>
                                                            <th scope="col"><?= __('Ticket Subject'); ?></th>
                                                            <th scope="col"><?= __('Assigned Staff'); ?></th>
                                                            <th scope="col"><?= __('Created Date'); ?></th>
                                                            <th scope="col"><?= __('Last Reply'); ?></th>
                                                            <th scope="col"><?= __('Priority'); ?></th>
                                                            <th scope="col"><?= __('Status'); ?></th>
                                                            <th scope="col"><?= __('Actions'); ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <th scope="col">1</th>
                                                            <td><span class="text-primary"><?= __('#TKT-0001'); ?></span></td>
                                                            <td><?= __('Internet Issue'); ?></td>
                                                            <td>
                                                                <div class="d-inline-block align-middle">
                                                                    <img src="../files/assets/images/avatar-4.jpg" alt="<?= htmlspecialchars(__('user'), ENT_QUOTES, 'UTF-8'); ?>" class="img-radius img-30 align-middle m-r-15">
                                                                    <div class="d-inline-block">
                                                                        <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-select js-example-basic-single col-sm-12">
                                                                            <optgroup label="Developer">
                                                                                <option value="AL"><?= __('Alabama'); ?></option>
                                                                                <option value="WY"><?= __('Wyoming'); ?></option>
                                                                            </optgroup>
                                                                            <optgroup label="Designer">
                                                                                <option value="WY"><?= __('Peter'); ?></option>
                                                                                <option value="WY"><?= __('Hanry Die'); ?></option>
                                                                                <option value="WY"><?= __('John Doe'); ?></option>
                                                                            </optgroup>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td><?= __('5 Jan 2019 07:21 AM'); ?></td>
                                                            <td><?= __('5 Jan 2019 11.12 AM'); ?></td>
                                                            <td>
                                                                 <div class="dropdown-success dropdown open">
                                                                    <button class="btn btn-success dropdown-toggle waves-effect waves-light btn-round" type="button" id="dropdown-priority-1" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><?= __('High'); ?></button>
                                                                    <div class="dropdown-menu" aria-labelledby="dropdown-priority-1" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                                                                        <a class="dropdown-item waves-light waves-effect d-block" href="#"><?= __('High'); ?></a>
                                                                        <a class="dropdown-item waves-light waves-effect d-block" href="#"><?= __('Medium'); ?></a>
                                                                        <a class="dropdown-item waves-light waves-effect d-block" href="#"><?= __('Low'); ?></a>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="dropdown-success dropdown open">
                                                                    <button class="btn btn-success dropdown-toggle waves-effect waves-light btn-round" type="button" id="dropdown-status-1" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><?= __('Opened'); ?></button>
                                                                    <div class="dropdown-menu" aria-labelledby="dropdown-status-1" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                                                                        <a class="dropdown-item waves-light waves-effect d-block" href="#"><?= __('Reopened'); ?></a>
                                                                        <a class="dropdown-item waves-light waves-effect d-block" href="#"><?= __('On Hold'); ?></a>
                                                                        <a class="dropdown-item waves-light waves-effect d-block" href="#"><?= __('Closed'); ?></a>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="dropdown-success dropdown open">
                                                                    <button class="btn btn-success dropdown-toggle waves-effect waves-light btn-round" type="button" id="dropdown-action-1" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></button>
                                                                    <div class="dropdown-menu" aria-labelledby="dropdown-action-1" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                                                                        <a class="dropdown-item waves-light waves-effect d-block" href="#"><?= __('Edit'); ?></a>
                                                                        <a class="dropdown-item waves-light waves-effect d-block" href="#"><?= __('Delete'); ?></a>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="col">2</th>
                                                            <td><span class="text-primary"><?= __('#TKT-0001'); ?></span></td>
                                                            <td><?= __('Internet Issue'); ?></td>
                                                            <td>
                                                                <div class="d-inline-block align-middle">
                                                                    <img src="../files/assets/images/avatar-4.jpg" alt="<?= htmlspecialchars(__('user'), ENT_QUOTES, 'UTF-8'); ?>" class="img-radius img-30 align-middle m-r-15">
                                                                    <div class="d-inline-block">
                                                                        <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-select js-example-basic-single col-sm-12">
                                                                            <optgroup label="Developer">
                                                                                <option value="AL"><?= __('Alabama'); ?></option>
                                                                                <option value="WY"><?= __('Wyoming'); ?></option>
                                                                            </optgroup>
                                                                            <optgroup label="Designer">
                                                                                <option value="WY"><?= __('Peter'); ?></option>
                                                                                <option value="WY"><?= __('Hanry Die'); ?></option>
                                                                                <option value="WY"><?= __('John Doe'); ?></option>
                                                                            </optgroup>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td><?= __('5 Jan 2019 07:21 AM'); ?></td>
                                                            <td><?= __('5 Jan 2019 11.12 AM'); ?></td>
                                                            <td>
                                                                <div class="dropdown-success dropdown open">
                                                                    <button class="btn btn-success dropdown-toggle waves-effect waves-light btn-round" type="button" id="dropdown-priority-2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><?= __('High'); ?></button>
                                                                    <div class="dropdown-menu" aria-labelledby="dropdown-priority-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                                                                        <a class="dropdown-item waves-light waves-effect d-block" href="#"><?= __('High'); ?></a>
                                                                        <a class="dropdown-item waves-light waves-effect d-block" href="#"><?= __('Medium'); ?></a>
                                                                        <a class="dropdown-item waves-light waves-effect d-block" href="#"><?= __('Low'); ?></a>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="dropdown-success dropdown open">
                                                                    <button class="btn btn-success dropdown-toggle waves-effect waves-light btn-round" type="button" id="dropdown-status-2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><?= __('Opened'); ?></button>
                                                                    <div class="dropdown-menu" aria-labelledby="dropdown-status-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                                                                        <a class="dropdown-item waves-light waves-effect d-block" href="#"><?= __('Reopened'); ?></a>
                                                                        <a class="dropdown-item waves-light waves-effect d-block" href="#"><?= __('On Hold'); ?></a>
                                                                        <a class="dropdown-item waves-light waves-effect d-block" href="#"><?= __('Closed'); ?></a>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="dropdown-success dropdown open">
                                                                    <button class="btn btn-success dropdown-toggle waves-effect waves-light btn-round" type="button" id="dropdown-action-2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></button>
                                                                    <div class="dropdown-menu" aria-labelledby="dropdown-action-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                                                                        <a class="dropdown-item waves-light waves-effect d-block" href="#"><?= __('Edit'); ?></a>
                                                                        <a class="dropdown-item waves-light waves-effect d-block" href="#"><?= __('Delete'); ?></a>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>

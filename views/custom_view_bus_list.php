<div class="bus-bg" style="background: url(../assets/images/bus.jpg); height:450px; background-size: 100%; background-repeat: no-repeat; position: relative;">
    <div class="banner-txt">
        <h2 class="f-w-700 f-35"><?= __('Get your bus online,'); ?><br /><?= htmlspecialchars(__('Easy & Safely'), ENT_QUOTES, 'UTF-8'); ?></h2>
    </div>
    <div class="booking-form">
        <h3 class=""><?= __('Choose Your Bus'); ?></h3>
        <div class="row mb-3 mt-4">
            <div class="col-lg-6 m-b-10">
                <div class="input-group">
                    <span class="input-group-text addon-style">
                        <span class=""><i class="ti-location-arrow"></i></span>
                    </span>
                    <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control">
                        <option><?= __('Pickup Point'); ?></option>
                        <option><?= __('Noida'); ?></option>
                        <option><?= __('Delhi'); ?></option>
                        <option><?= __('New Delhi'); ?></option>
                        <option><?= __('Gurugram'); ?></option>
                    </select>
                </div>
            </div>
            <div class="col-lg-6 m-b-10">
                <div class="input-group">
                    <span class="input-group-text addon-style">
                        <span class=""><i class="ti-location-pin"></i></span>
                    </span>
                    <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control">
                        <option><?= __('Dropping Point'); ?></option>
                        <option><?= __('Noida'); ?></option>
                        <option><?= __('Delhi'); ?></option>
                        <option><?= __('New Delhi'); ?></option>
                        <option><?= __('Gurugram'); ?></option>
                    </select>
                </div>
            </div>
            <div class="col-lg-12 m-b-10">
                <div class="input-group">
                    <span class="input-group-text addon-style">
                        <span class=""><i class="ti-time"></i></span>
                    </span>
                    <input aria-label="<?= htmlspecialchars(__('Select date'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" type="date" name="" >
                </div>
            </div>
            <div class="col-lg-6 m-b-10">
                <button class="btn btn-primary w-100"><?= __('Find Bus'); ?></button>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-3">
        <div class="card">
            <div class="card-header border-bottom">
                <h3><?= __('Filter'); ?>
                    <span class="float-right d-inline-block"><?= __('Reset All'); ?></span>
                </h3>
            </div>
            <div class="card-body">
                <div class="border-bottom mb-3 pb-3">
                    <h4><?= __('Vehicle Type'); ?></h4>
                    <div class="border-checkbox-section mt-3">
                        <div class="border-checkbox-group border-checkbox-group-primary">
                            <input class="border-checkbox" type="checkbox" id="checkbox1">
                            <label class="form-label border-checkbox-label" for="checkbox1"><?= __('Classic'); ?></label>
                        </div>
                    </div>
                    <div class="border-checkbox-section mt-3">
                        <div class="border-checkbox-group border-checkbox-group-primary">
                            <input class="border-checkbox" type="checkbox" id="checkbox2">
                            <label class="form-label border-checkbox-label" for="checkbox2"><?= __('Coach'); ?></label>
                        </div>
                    </div>
                    <div class="border-checkbox-section mt-3">
                        <div class="border-checkbox-group border-checkbox-group-primary">
                            <input class="border-checkbox" type="checkbox" id="checkbox3">
                            <label class="form-label border-checkbox-label" for="checkbox3"><?= __('AC'); ?></label>
                        </div>
                    </div>
                </div>
                <div class="border-bottom mb-3 pb-3">
                    <h4><?= __('Routes'); ?></h4>
                    <div class="border-checkbox-section mt-3">
                        <div class="border-checkbox-group border-checkbox-group-primary">
                            <input class="border-checkbox" type="checkbox" id="checkbox4">
                            <label class="form-label border-checkbox-label" for="checkbox4"><?= __('Noida to New Delhi'); ?></label>
                        </div>
                    </div>
                    <div class="border-checkbox-section mt-3">
                        <div class="border-checkbox-group border-checkbox-group-primary">
                            <input class="border-checkbox" type="checkbox" id="checkbox5">
                            <label class="form-label border-checkbox-label" for="checkbox5"><?= __('Noida to NSP'); ?></label>
                        </div>
                    </div>
                    <div class="border-checkbox-section mt-3">
                        <div class="border-checkbox-group border-checkbox-group-primary">
                            <input class="border-checkbox" type="checkbox" id="checkbox6">
                            <label class="form-label border-checkbox-label" for="checkbox6"><?= __('New Delhi to Noida'); ?></label>
                        </div>
                    </div>
                </div>
                <div class="border-bottom mb-3 pb-3">
                    <h4><?= __('Schedules'); ?></h4>
                    <div class="border-checkbox-section mt-3">
                        <div class="border-checkbox-group border-checkbox-group-primary">
                            <input class="border-checkbox" type="checkbox" id="checkbox7">
                            <label class="form-label border-checkbox-label" for="checkbox7"><?= __('06:00am - 03:30pm'); ?></label>
                        </div>
                    </div>
                    <div class="border-checkbox-section mt-3">
                        <div class="border-checkbox-group border-checkbox-group-primary">
                            <input class="border-checkbox" type="checkbox" id="checkbox8">
                            <label class="form-label border-checkbox-label" for="checkbox8"><?= __('06:00am - 03:30pm'); ?></label>
                        </div>
                    </div>
                    <div class="border-checkbox-section mt-3">
                        <div class="border-checkbox-group border-checkbox-group-primary">
                            <input class="border-checkbox" type="checkbox" id="checkbox9">
                            <label class="form-label border-checkbox-label" for="checkbox9"><?= __('06:00am - 03:30pm'); ?></label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-9">
        <div class="card">
            <div class="card-body">
                <div class="row mt-3">
                    <div class="col-lg-3">
                        <h4 class="f-w-700 f-22"><?= __('AC - Kansas - Echo Bass'); ?></h4>
                        <p><?= __('Seat Layout 2x2'); ?></p>
                        <span class="text-danger"><?= __('AC'); ?></span>
                    </div>
                    <div class="col-lg-5">
                        <div class="row">
                            <div class="col-lg-4">
                                <h4 class="f-w-700 f-20">08:00 AM</h4>
                                <p class="f-18 f-w-500"><?= __('Kansas'); ?></p>
                            </div>
                            <div class="col-lg-3 text-center">
                                <i class="ti-arrow-right text-primary f-w-700"></i>
                                <p class="f-18 f-w-500">08:30 min</p>
                            </div>
                            <div class="col-lg-5">
                                <h4 class="f-w-700 f-20">04:30 PM</h4>
                                <p class="f-18 f-w-500"><?= __('Echo Bass'); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 text-right">
                        <h4 class="f-w-700 f-22 text-success">&#8377 100</h4>
                        <p><?= __('Off Days'); ?>: <span class="form-label label label-success"><?= __('Sunday'); ?></span></p>
                        <button class="btn btn-primary"><?= __('Select Bus'); ?></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="row mt-3">
                    <div class="col-lg-3">
                        <h4 class="f-w-700 f-22"><?= __('AC - Kansas - Echo Bass'); ?></h4>
                        <p><?= __('Seat Layout 2x2'); ?></p>
                        <span class="text-danger"><?= __('AC'); ?></span>
                    </div>
                    <div class="col-lg-5">
                        <div class="row">
                            <div class="col-lg-4">
                                <h4 class="f-w-700 f-20">08:00 AM</h4>
                                <p class="f-18 f-w-500"><?= __('Kansas'); ?></p>
                            </div>
                            <div class="col-lg-3 text-center">
                                <i class="ti-arrow-right text-primary f-w-700"></i>
                                <p class="f-18 f-w-500">08:30 min</p>
                            </div>
                            <div class="col-lg-5">
                                <h4 class="f-w-700 f-20">04:30 PM</h4>
                                <p class="f-18 f-w-500"><?= __('Echo Bass'); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 text-right">
                        <h4 class="f-w-700 f-22 text-success">&#8377 100</h4>
                        <p><?= __('Off Days'); ?>: <span class="form-label label label-success"><?= __('Sunday'); ?></span></p>
                        <button class="btn btn-primary"><?= __('Select Bus'); ?></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="row mt-3">
                    <div class="col-lg-3">
                        <h4 class="f-w-700 f-22"><?= __('AC - Kansas - Echo Bass'); ?></h4>
                        <p><?= __('Seat Layout 2x2'); ?></p>
                        <span class="text-danger"><?= __('AC'); ?></span>
                    </div>
                    <div class="col-lg-5">
                        <div class="row">
                            <div class="col-lg-4">
                                <h4 class="f-w-700 f-20">08:00 AM</h4>
                                <p class="f-18 f-w-500"><?= __('Kansas'); ?></p>
                            </div>
                            <div class="col-lg-3 text-center">
                                <i class="ti-arrow-right text-primary f-w-700"></i>
                                <p class="f-18 f-w-500">08:30 min</p>
                            </div>
                            <div class="col-lg-5">
                                <h4 class="f-w-700 f-20">04:30 PM</h4>
                                <p class="f-18 f-w-500"><?= __('Echo Bass'); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 text-right">
                        <h4 class="f-w-700 f-22 text-success">&#8377 100</h4>
                        <p><?= __('Off Days'); ?>: <span class="form-label label label-success"><?= __('Sunday'); ?></span></p>
                        <button class="btn btn-primary"><?= __('Select Bus'); ?></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="row mt-3">
                    <div class="col-lg-3">
                        <h4 class="f-w-700 f-22"><?= __('AC - Kansas - Echo Bass'); ?></h4>
                        <p><?= __('Seat Layout 2x2'); ?></p>
                        <span class="text-danger"><?= __('AC'); ?></span>
                    </div>
                    <div class="col-lg-5">
                        <div class="row">
                            <div class="col-lg-4">
                                <h4 class="f-w-700 f-20">08:00 AM</h4>
                                <p class="f-18 f-w-500"><?= __('Kansas'); ?></p>
                            </div>
                            <div class="col-lg-3 text-center">
                                <i class="ti-arrow-right text-primary f-w-700"></i>
                                <p class="f-18 f-w-500">08:30 min</p>
                            </div>
                            <div class="col-lg-5">
                                <h4 class="f-w-700 f-20">04:30 PM</h4>
                                <p class="f-18 f-w-500"><?= __('Echo Bass'); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 text-right">
                        <h4 class="f-w-700 f-22 text-success">&#8377 100</h4>
                        <p><?= __('Off Days'); ?>: <span class="form-label label label-success"><?= __('Sunday'); ?></span></p>
                        <button class="btn btn-primary"><?= __('Select Bus'); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

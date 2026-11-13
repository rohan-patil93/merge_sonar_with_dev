<div class="col-xl-12">
                                            <div class="kt-portlet">
                                                <div class="kt-portlet__head">
                                                    <div class="kt-portlet__head-label">
                                                        <h3 class="kt-portlet__head-title"><?= __('Tax Information'); ?> <small><?= __('change your tax settings'); ?></small></h3>
                                                    </div>
                                                    <div class="kt-portlet__head-toolbar">
                                                        <div class="kt-portlet__head-wrapper">
                                                            <div class="dropdown dropdown-inline">
                                                                <button type="button" class="btn btn-label-brand btn-sm btn-icon btn-icon-md" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                    <i class="flaticon2-console"></i>
                                                                </button>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    <ul class="kt-nav">
                                                                        <li class="kt-nav__section kt-nav__section--first">
                                                                            <span class="kt-nav__section-text"><?= __('Export Tools'); ?></span>
                                                                        </li>
                                                                        <li class="kt-nav__item">
                                                                            <a href="#" class="kt-nav__link">
                                                                                <i class="kt-nav__link-icon la la-print"></i>
                                                                                <span class="kt-nav__link-text"><?= __('Print'); ?></span>
                                                                            </a>
                                                                        </li>
                                                                        <li class="kt-nav__item">
                                                                            <a href="#" class="kt-nav__link">
                                                                                <i class="kt-nav__link-icon la la-copy"></i>
                                                                                <span class="kt-nav__link-text"><?= __('Copy'); ?></span>
                                                                            </a>
                                                                        </li>
                                                                        <li class="kt-nav__item">
                                                                            <a href="#" class="kt-nav__link">
                                                                                <i class="kt-nav__link-icon la la-file-excel-o"></i>
                                                                                <span class="kt-nav__link-text"><?= __('Excel'); ?></span>
                                                                            </a>
                                                                        </li>
                                                                        <li class="kt-nav__item">
                                                                            <a href="#" class="kt-nav__link">
                                                                                <i class="kt-nav__link-icon la la-file-text-o"></i>
                                                                                <span class="kt-nav__link-text"><?= __('CSV'); ?></span>
                                                                            </a>
                                                                        </li>
                                                                        <li class="kt-nav__item">
                                                                            <a href="#" class="kt-nav__link">
                                                                                <i class="kt-nav__link-icon la la-file-pdf-o"></i>
                                                                                <span class="kt-nav__link-text"><?= __('PDF'); ?></span>
                                                                            </a>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <form class="kt-form kt-form--label-right">
                                                    <div class="kt-portlet__body">
                                                        <div class="kt-section kt-section--first">
                                                            <div class="kt-section__body">
                                                                <div class="row">
                                                                    <span class="col-xl-3"></span>
                                                                    <div class="col-lg-9 col-xl-6">
                                                                        <h3 class="kt-section__title kt-section__title-sm"><?= __('Tax'); ?>:</h3>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row" data-select2-id="39">
                                                                    <span class="col-form-label col-lg-3 col-sm-12"><?= __('GST Registration type'); ?></span>
                                                                    <div class=" col-lg-4 col-md-9 col-sm-12" data-select2-id="38">
                                                                        <select aria-label="<?= htmlspecialchars(__('Param'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control kt-select2 select2-hidden-accessible" id="kt_select2_1" name="param" data-select2-id="kt_select2_1_2" tabindex="-1">
                                                                            <option value="AK" data-select2-id="2"><?= __('Alaska'); ?></option>
                                                                            <option value="HI" data-select2-id="47"><?= __('Hawaii'); ?></option>
                                                                            <option value="CA" data-select2-id="48"><?= __('California'); ?></option>
                                                                            <option value="NV" data-select2-id="49"><?= __('Nevada'); ?></option>
                                                                            <option value="OR" data-select2-id="50"><?= __('Oregon'); ?></option>
                                                                            <option value="WA" data-select2-id="51"><?= __('Washington'); ?></option>
                                                                            <option value="AZ" data-select2-id="52"><?= __('Arizona'); ?></option>
                                                                            <option value="CO" data-select2-id="53"><?= __('Colorado'); ?></option>
                                                                            <option value="ID" data-select2-id="54"><?= __('Idaho'); ?></option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                <div class="form-group row">
                                                                    <span class="col-xl-3 col-lg-3 col-form-label"><?= __('GSTIN'); ?></span>
                                                                    <div class="col-lg-9 col-xl-6">
                                                                        <div class="">
                                                                            <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" type="text" value="nick84">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <span class="col-form-label col-lg-3 col-sm-12"><?= __('GSTIN Registration date'); ?></span>
                                                                    <div class="col-lg-6 col-md-9 col-sm-12">
                                                                        <div class="input-group date">
                                                                            <input aria-label="<?= htmlspecialchars(__('Kt Datepicker 3'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" readonly="" value="05/20/2017" id="kt_datepicker_3">
                                                                            <div class="input-group-append">
                                                                                <span class="input-group-text">
                                                                                    <i class="la la-calendar"></i>
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <span class="col-xl-3 col-lg-3 col-form-label"><?= __('PAN No.'); ?></span>
                                                                    <div class="col-lg-9 col-xl-6">
                                                                        <div class="">
                                                                            <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" type="text" value="nick84">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <span class="col-xl-3 col-lg-3 col-form-label"><?= __('TAN No.'); ?></span>
                                                                    <div class="col-lg-9 col-xl-6">
                                                                        <div class="">
                                                                            <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" type="text" value="nick84">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="kt-portlet__foot">
                                                                <div class="kt-form__actions">
                                                                    <div class="row">
                                                                        <div class="col-lg-2"></div>
                                                                        <div class="col-lg-4">
                                                                            <button type="reset" class="btn btn-success"><?= __('Submit'); ?></button>
                                                                            <button type="reset" class="btn btn-secondary"><?= __('Cancel'); ?></button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>


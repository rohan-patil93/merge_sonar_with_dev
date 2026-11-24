                                        <div class="col-xl-12">
                                            <div class="kt-portlet">
                                                <div class="kt-portlet__head">
                                                    <div class="kt-portlet__head-label">
                                                        <h3 class="kt-portlet__head-title"><?= __('Corporate Information'); ?> <small><?= __('update your corporate informaiton'); ?></small></h3>
                                                    </div>
                                                    <div class="kt-portlet__head-toolbar">
                                                        <div class="kt-portlet__head-wrapper">
                                                            <div class="dropdown dropdown-inline">
                                                                <button type="button" class="btn btn-label-brand btn-sm btn-icon btn-icon-md" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                    <i class="flaticon2-gear"></i>
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
                                                                        <h3 class="kt-section__title kt-section__title-sm"><?= __('Corporate Info'); ?>:</h3>
                                                                    </div>
                                                                </div>

                                                                    <span class="col-xl-3 col-lg-3 col-form-label"><?= __('Logo'); ?></span>
                                                                    <div class="col-lg-9 col-xl-6">
                                                                        <div class="kt-avatar kt-avatar--outline" id="kt_user_avatar">
                                                                            <div class="kt-avatar__holder" style="background-image: url(assets/media/users/100_13.jpg)"></div>
                                                                            <label class="kt-avatar__upload" data-toggle="kt-tooltip" title="" data-original-title="<?= htmlspecialchars(__('Change avatar'), ENT_QUOTES, 'UTF-8'); ?>">
                                                                                <i class="fa fa-pen"></i>
                                                                                <input aria-label="<?= htmlspecialchars(__('Profile Avatar'), ENT_QUOTES, 'UTF-8'); ?>" type="file" name="profile_avatar" accept=".png, .jpg, .jpeg">
                                                                            </label>
                                                                            <span class="kt-avatar__cancel" data-toggle="kt-tooltip" title="" data-original-title="<?= htmlspecialchars(__('Cancel avatar'), ENT_QUOTES, 'UTF-8'); ?>">
                                                                                <i class="fa fa-times"></i>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <form class="kt-form">
                                                                    <div class="kt-portlet__body">
                                                                        <div class="kt-form__section kt-form__section--first">
                                                                            <div class="form-group row">
                                                                                <span class="col-lg-2 col-form-label"><?= __('Company Name'); ?>:</span>
                                                                                <div class="col-lg-10">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Enter company name'), ENT_QUOTES, 'UTF-8'); ?>" type="email" class="form-control" placeholder="<?= htmlspecialchars(__('Enter company name'), ENT_QUOTES, 'UTF-8'); ?>">
                                                                                    <span class="form-text text-muted"><?= __('Please enter your company name'); ?></span>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group row">
                                                                                <span class="col-form-label col-lg-2 col-sm-12"><?= __('About'); ?></span>
                                                                                <div class="col-lg-10 col-md-9 col-sm-12">
                                                                                    <textarea aria-label="<?= htmlspecialchars(__('Kt Autosize 2'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" id="kt_autosize_2" rows="3" style="overflow: hidden; overflow-wrap: break-word; resize: none; height: 76px;"><?= __('A brief about the company'); ?></textarea>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group row">
                                                                                <span class="col-form-label col-lg-2 col-sm-12"><?= __('Type'); ?></span>
                                                                                <div class="col-lg-10 col-md-9 col-sm-12">
                                                                                    <div class="kt-radio-list row check-radio">
                                                                                        <label class="kt-radio kt-radio--bold kt-radio--brand col-lg-2">
                                                                                            <input aria-label="<?= htmlspecialchars(__('Private'), ENT_QUOTES, 'UTF-8'); ?>" type="radio" name="radio6"> <?= __('Private'); ?>
                                                                                            <span></span>
                                                                                        </label>
                                                                                        <label class="kt-radio kt-radio--bold kt-radio--brand col-lg-2">
                                                                                            <input aria-label="<?= htmlspecialchars(__('Public'), ENT_QUOTES, 'UTF-8'); ?>" type="radio" name="radio6"> <?= __('Public'); ?>
                                                                                            <span></span>
                                                                                        </label>
                                                                                        <label class="kt-radio kt-radio--bold kt-radio--brand col-lg-2">
                                                                                            <input aria-label="<?= htmlspecialchars(__('Partnership'), ENT_QUOTES, 'UTF-8'); ?>" type="radio" name="radio6"> <?= __('Partnership'); ?>
                                                                                            <span></span>
                                                                                        </label>
                                                                                        <label class="kt-radio kt-radio--bold kt-radio--brand col-lg-2">
                                                                                            <input aria-label="<?= htmlspecialchars(__('Others'), ENT_QUOTES, 'UTF-8'); ?>" type="radio" name="radio6"> <?= __('Others'); ?>
                                                                                            <span></span>
                                                                                        </label>

                                                                                    </div>
                                                                                    <span class="form-text text-muted"><?= __('Please select company type.'); ?></span>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group row">
                                                                                <span class="col-form-label col-lg-2 col-sm-12"><?= __('Industry'); ?></span>
                                                                                <div class=" col-lg-10 col-md-9 col-sm-12">
                                                                                    <select aria-label="<?= htmlspecialchars(__('Param'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control kt-select2 select2-hidden-accessible" id="kt_select2_3" name="param" multiple="" data-select2-id="kt_select2_3_2" tabindex="-1">
                                                                                        <optgroup label="Alaskan/Hawaiian Time Zone" data-select2-id="97">
                                                                                            <option value="AK" selected="" data-select2-id="10"><?= __('Alaska'); ?></option>
                                                                                            <option value="HI" data-select2-id="98"><?= __('Hawaii'); ?></option>
                                                                                        </optgroup>
                                                                                        <optgroup label="Pacific Time Zone" data-select2-id="99">
                                                                                            <option value="CA" data-select2-id="100"><?= __('California'); ?></option>
                                                                                            <option value="NV" selected="" data-select2-id="11"><?= __('Nevada'); ?></option>
                                                                                            <option value="OR" data-select2-id="101"><?= __('Oregon'); ?></option>
                                                                                            <option value="WA" data-select2-id="102"><?= __('Washington'); ?></option>
                                                                                        </optgroup>
                                                                                        <optgroup label="Mountain Time Zone" data-select2-id="103">
                                                                                            <option value="AZ" data-select2-id="104"><?= __('Arizona'); ?></option>
                                                                                            <option value="CO" data-select2-id="105"><?= __('Colorado'); ?></option>
                                                                                            <option value="ID" data-select2-id="106"><?= __('Idaho'); ?></option>
                                                                                            <option value="MT" selected="" data-select2-id="12"><?= __('Montana'); ?></option>
                                                                                            <option value="NE" data-select2-id="107"><?= __('Nebraska'); ?></option>
                                                                                            <option value="NM" data-select2-id="108"><?= __('New Mexico'); ?></option>
                                                                                            <option value="ND" data-select2-id="109"><?= __('North Dakota'); ?></option>
                                                                                            <option value="UT" data-select2-id="110"><?= __('Utah'); ?></option>
                                                                                            <option value="WY" data-select2-id="111"><?= __('Wyoming'); ?></option>
                                                                                        </optgroup>
                                                                                        <optgroup label="Central Time Zone" data-select2-id="112">
                                                                                            <option value="AL" data-select2-id="113"><?= __('Alabama'); ?></option>
                                                                                            <option value="AR" data-select2-id="114"><?= __('Arkansas'); ?></option>
                                                                                            <option value="IL" data-select2-id="115"><?= __('Illinois'); ?></option>
                                                                                            <option value="IA" data-select2-id="116"><?= __('Iowa'); ?></option>
                                                                                            <option value="KS" data-select2-id="117"><?= __('Kansas'); ?></option>
                                                                                            <option value="KY" data-select2-id="118"><?= __('Kentucky'); ?></option>
                                                                                            <option value="LA" data-select2-id="119"><?= __('Louisiana'); ?></option>
                                                                                            <option value="MN" data-select2-id="120"><?= __('Minnesota'); ?></option>
                                                                                            <option value="MS" data-select2-id="121"><?= __('Mississippi'); ?></option>
                                                                                            <option value="MO" data-select2-id="122"><?= __('Missouri'); ?></option>
                                                                                            <option value="OK" data-select2-id="123"><?= __('Oklahoma'); ?></option>
                                                                                            <option value="SD" data-select2-id="124"><?= __('South Dakota'); ?></option>
                                                                                            <option value="TX" data-select2-id="125"><?= __('Texas'); ?></option>
                                                                                            <option value="TN" data-select2-id="126"><?= __('Tennessee'); ?></option>
                                                                                            <option value="WI" data-select2-id="127"><?= __('Wisconsin'); ?></option>
                                                                                        </optgroup>
                                                                                        <optgroup label="Eastern Time Zone" data-select2-id="128">
                                                                                            <option value="CT" data-select2-id="129"><?= __('Connecticut'); ?></option>
                                                                                            <option value="DE" data-select2-id="130"><?= __('Delaware'); ?></option>
                                                                                            <option value="FL" data-select2-id="131"><?= __('Florida'); ?></option>
                                                                                            <option value="GA" data-select2-id="132"><?= __('Georgia'); ?></option>
                                                                                            <option value="IN" data-select2-id="133"><?= __('Indiana'); ?></option>
                                                                                            <option value="ME" data-select2-id="134"><?= __('Maine'); ?></option>
                                                                                            <option value="MD" data-select2-id="135"><?= __('Maryland'); ?></option>
                                                                                            <option value="MA" data-select2-id="136"><?= __('Massachusetts'); ?></option>
                                                                                            <option value="MI" data-select2-id="137"><?= __('Michigan'); ?></option>
                                                                                            <option value="NH" data-select2-id="138"><?= __('New Hampshire'); ?></option>
                                                                                            <option value="NJ" data-select2-id="139"><?= __('New Jersey'); ?></option>
                                                                                            <option value="NY" data-select2-id="140"><?= __('New York'); ?></option>
                                                                                            <option value="NC" data-select2-id="141"><?= __('North Carolina'); ?></option>
                                                                                            <option value="OH" data-select2-id="142"><?= __('Ohio'); ?></option>
                                                                                            <option value="PA" data-select2-id="143"><?= __('Pennsylvania'); ?></option>
                                                                                            <option value="RI" data-select2-id="144"><?= __('Rhode Island'); ?></option>
                                                                                            <option value="SC" data-select2-id="145"><?= __('South Carolina'); ?></option>
                                                                                            <option value="VT" data-select2-id="146"><?= __('Vermont'); ?></option>
                                                                                            <option value="VA" data-select2-id="147"><?= __('Virginia'); ?></option>
                                                                                            <option value="WV" data-select2-id="148"><?= __('West Virginia'); ?></option>
                                                                                        </optgroup>
                                                                                    </select><span class="select2 select2-container select2-container--default select2-container--below" dir="ltr" data-select2-id="9" style="width: 426.388px;"><span class="selection"><span class="select2-selection select2-selection--multiple" aria-haspopup="true" aria-expanded="false" tabindex="-1" aria-disabled="false" aria-controls="select2-kt_select2_3-results"><ul class="select2-selection__rendered"><li class="select2-selection__choice" title="<?= htmlspecialchars(__('Alaska'), ENT_QUOTES, 'UTF-8'); ?>" data-select2-id="13"><span class="select2-selection__choice__remove" aria-hidden="true">×</span><?= __('Alaska'); ?></li><li class="select2-selection__choice" title="<?= htmlspecialchars(__('Nevada'), ENT_QUOTES, 'UTF-8'); ?>" data-select2-id="14"><span class="select2-selection__choice__remove" aria-hidden="true">×</span><?= __('Nevada'); ?></li><li class="select2-selection__choice" title="<?= htmlspecialchars(__('Montana'), ENT_QUOTES, 'UTF-8'); ?>" data-select2-id="15"><span class="select2-selection__choice__remove" aria-hidden="true">×</span><?= __('Montana'); ?></li><li class="select2-search select2-search--inline"><input aria-label="<?= htmlspecialchars(__('Search'), ENT_QUOTES, 'UTF-8'); ?>" class="select2-search__field" type="search" tabindex="0" autocomplete="off" autocorrect="off" autocapitalize="none" spellcheck="false" role="searchbox" aria-autocomplete="list" placeholder="" style="width: 0.75em;"></li></ul></span></span><span class="dropdown-wrapper"></span></span>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group row">
                                                                                <span class="col-form-label col-lg-2 col-sm-12"><?= __('Founded'); ?></span>
                                                                                <div class="col-lg-4 col-md-9 col-sm-12">
                                                                                    <div class="input-group date">
                                                                                        <input aria-label="<?= htmlspecialchars(__('Kt Datepicker 3'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" readonly="" value="05/20/2017" id="kt_datepicker_3">
                                                                                        <div class="input-group-append">
                                                                                            <span class="input-group-text">
                                                                                                <i class="la la-calendar"></i>
                                                                                            </span>
                                                                                        </div>
                                                                                    </div>
                                                                                    <span class="form-text text-muted"><?= __('Please enter the founded date'); ?></span>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group row">
                                                                                <span class="col-lg-2 col-form-label"><?= __('Headquaters'); ?>:</span>
                                                                                <div class="col-lg-10">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Enter company name'), ENT_QUOTES, 'UTF-8'); ?>" type="email" class="form-control" placeholder="<?= htmlspecialchars(__('Enter company name'), ENT_QUOTES, 'UTF-8'); ?>">
                                                                                    <span class="form-text text-muted"><?= __('Please enter your headquaters'); ?></span>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group row">
                                                                                <span class="col-form-label col-lg-2 col-sm-12"><?= __('Area Served'); ?></span>
                                                                                <div class=" col-lg-10 col-md-9 col-sm-12">
                                                                                    <select aria-label="<?= htmlspecialchars(__('Param'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control kt-select2 select2-hidden-accessible" id="kt_select2_3_3" name="param" multiple="" data-select2-id="kt_select2_3_4" tabindex="-1">
                                                                                        <optgroup label="Alaskan/Hawaiian Time Zone" data-select2-id="97_2">
                                                                                            <option value="AK" selected="" data-select2-id="10_2"><?= __('Alaska'); ?></option>
                                                                                            <option value="HI" data-select2-id="98_2"><?= __('Hawaii'); ?></option>
                                                                                        </optgroup>
                                                                                        <optgroup label="Pacific Time Zone" data-select2-id="99_2">
                                                                                            <option value="CA" data-select2-id="100_2"><?= __('California'); ?></option>
                                                                                            <option value="NV" selected="" data-select2-id="11_2"><?= __('Nevada'); ?></option>
                                                                                            <option value="OR" data-select2-id="101_2"><?= __('Oregon'); ?></option>
                                                                                            <option value="WA" data-select2-id="102_2"><?= __('Washington'); ?></option>
                                                                                        </optgroup>
                                                                                        <optgroup label="Mountain Time Zone" data-select2-id="103_2">
                                                                                            <option value="AZ" data-select2-id="104_2"><?= __('Arizona'); ?></option>
                                                                                            <option value="CO" data-select2-id="105_2"><?= __('Colorado'); ?></option>
                                                                                            <option value="ID" data-select2-id="106_2"><?= __('Idaho'); ?></option>
                                                                                            <option value="MT" selected="" data-select2-id="12_2"><?= __('Montana'); ?></option>
                                                                                            <option value="NE" data-select2-id="107_2"><?= __('Nebraska'); ?></option>
                                                                                            <option value="NM" data-select2-id="108_2"><?= __('New Mexico'); ?></option>
                                                                                            <option value="ND" data-select2-id="109_2"><?= __('North Dakota'); ?></option>
                                                                                            <option value="UT" data-select2-id="110_2"><?= __('Utah'); ?></option>
                                                                                            <option value="WY" data-select2-id="111_2"><?= __('Wyoming'); ?></option>
                                                                                        </optgroup>
                                                                                        <optgroup label="Central Time Zone" data-select2-id="112_2">
                                                                                            <option value="AL" data-select2-id="113_2"><?= __('Alabama'); ?></option>
                                                                                            <option value="AR" data-select2-id="114_2"><?= __('Arkansas'); ?></option>
                                                                                            <option value="IL" data-select2-id="115_2"><?= __('Illinois'); ?></option>
                                                                                            <option value="IA" data-select2-id="116_2"><?= __('Iowa'); ?></option>
                                                                                            <option value="KS" data-select2-id="117_2"><?= __('Kansas'); ?></option>
                                                                                            <option value="KY" data-select2-id="118_2"><?= __('Kentucky'); ?></option>
                                                                                            <option value="LA" data-select2-id="119_2"><?= __('Louisiana'); ?></option>
                                                                                            <option value="MN" data-select2-id="120_2"><?= __('Minnesota'); ?></option>
                                                                                            <option value="MS" data-select2-id="121_2"><?= __('Mississippi'); ?></option>
                                                                                            <option value="MO" data-select2-id="122_2"><?= __('Missouri'); ?></option>
                                                                                            <option value="OK" data-select2-id="123_2"><?= __('Oklahoma'); ?></option>
                                                                                            <option value="SD" data-select2-id="124_2"><?= __('South Dakota'); ?></option>
                                                                                            <option value="TX" data-select2-id="125_2"><?= __('Texas'); ?></option>
                                                                                            <option value="TN" data-select2-id="126_2"><?= __('Tennessee'); ?></option>
                                                                                            <option value="WI" data-select2-id="127_2"><?= __('Wisconsin'); ?></option>
                                                                                        </optgroup>
                                                                                        <optgroup label="Eastern Time Zone" data-select2-id="128_2">
                                                                                            <option value="CT" data-select2-id="129_2"><?= __('Connecticut'); ?></option>
                                                                                            <option value="DE" data-select2-id="130_2"><?= __('Delaware'); ?></option>
                                                                                            <option value="FL" data-select2-id="131_2"><?= __('Florida'); ?></option>
                                                                                            <option value="GA" data-select2-id="132_2"><?= __('Georgia'); ?></option>
                                                                                            <option value="IN" data-select2-id="133_2"><?= __('Indiana'); ?></option>
                                                                                            <option value="ME" data-select2-id="134_2"><?= __('Maine'); ?></option>
                                                                                            <option value="MD" data-select2-id="135_2"><?= __('Maryland'); ?></option>
                                                                                            <option value="MA" data-select2-id="136_2"><?= __('Massachusetts'); ?></option>
                                                                                            <option value="MI" data-select2-id="137_2"><?= __('Michigan'); ?></option>
                                                                                            <option value="NH" data-select2-id="138_2"><?= __('New Hampshire'); ?></option>
                                                                                            <option value="NJ" data-select2-id="139_2"><?= __('New Jersey'); ?></option>
                                                                                            <option value="NY" data-select2-id="140_2"><?= __('New York'); ?></option>
                                                                                            <option value="NC" data-select2-id="141_2"><?= __('North Carolina'); ?></option>
                                                                                            <option value="OH" data-select2-id="142_2"><?= __('Ohio'); ?></option>
                                                                                            <option value="PA" data-select2-id="143_2"><?= __('Pennsylvania'); ?></option>
                                                                                            <option value="RI" data-select2-id="144_2"><?= __('Rhode Island'); ?></option>
                                                                                            <option value="SC" data-select2-id="145_2"><?= __('South Carolina'); ?></option>
                                                                                            <option value="VT" data-select2-id="146_2"><?= __('Vermont'); ?></option>
                                                                                            <option value="VA" data-select2-id="147_2"><?= __('Virginia'); ?></option>
                                                                                            <option value="WV" data-select2-id="148_2"><?= __('West Virginia'); ?></option>
                                                                                        </optgroup>
                                                                                    </select><span class="select2 select2-container select2-container--default select2-container--below" dir="ltr" data-select2-id="9_2" style="width: 426.388px;"><span class="selection"><span class="select2-selection select2-selection--multiple" aria-haspopup="true" aria-expanded="false" tabindex="-1" aria-disabled="false" aria-controls="select2-kt_select2_3-results"><ul class="select2-selection__rendered"><li class="select2-selection__choice" title="<?= htmlspecialchars(__('Alaska'), ENT_QUOTES, 'UTF-8'); ?>" data-select2-id="13_2"><span class="select2-selection__choice__remove" aria-hidden="true">×</span><?= __('Alaska'); ?></li><li class="select2-selection__choice" title="<?= htmlspecialchars(__('Nevada'), ENT_QUOTES, 'UTF-8'); ?>" data-select2-id="14_2"><span class="select2-selection__choice__remove" aria-hidden="true">×</span><?= __('Nevada'); ?></li><li class="select2-selection__choice" title="<?= htmlspecialchars(__('Montana'), ENT_QUOTES, 'UTF-8'); ?>" data-select2-id="15_2"><span class="select2-selection__choice__remove" aria-hidden="true">×</span><?= __('Montana'); ?></li><li class="select2-search select2-search--inline"><input aria-label="<?= htmlspecialchars(__('Search'), ENT_QUOTES, 'UTF-8'); ?>" class="select2-search__field" type="search" tabindex="0" autocomplete="off" autocorrect="off" autocapitalize="none" spellcheck="false" role="searchbox" aria-autocomplete="list" placeholder="" style="width: 0.75em;"></li></ul></span></span><span class="dropdown-wrapper"></span></span>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group row">
                                                                                <span class="col-form-label col-lg-2 col-sm-12"><?= __('Revenue'); ?></span>
                                                                                <div class="col-lg-4 col-md-9 col-sm-12">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Kt Inputmask 7'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" id="kt_inputmask_7" im-insert="true">
                                                                                    <span class="form-text text-muted"><?= __('Please enter the revenue'); ?></span>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group row" data-select2-id="115_3">
                                                                                <span class="col-form-label col-lg-2 col-sm-12"><?= __('No. of Employee'); ?></span>
                                                                                <div class=" col-lg-4 col-md-9 col-sm-12" data-select2-id="114_3">
                                                                                    <select aria-label="<?= htmlspecialchars(__('Param'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control kt-select2 select2-hidden-accessible" id="kt_select2_1" name="param" data-select2-id="kt_select2_1_2" tabindex="-1">
                                                                                        <option value="AK" data-select2-id="2"><?= __('Alaska'); ?></option>
                                                                                        <option value="HI" data-select2-id="116_3"><?= __('Hawaii'); ?></option>
                                                                                        <option value="CA" data-select2-id="117_3"><?= __('California'); ?></option>
                                                                                        <option value="NV" data-select2-id="118_3"><?= __('Nevada'); ?></option>
                                                                                        <option value="OR" data-select2-id="119_3"><?= __('Oregon'); ?></option>
                                                                                        <option value="WA" data-select2-id="120_3"><?= __('Washington'); ?></option>
                                                                                        <option value="AZ" data-select2-id="121_3"><?= __('Arizona'); ?></option>
                                                                                        <option value="CO" data-select2-id="122_3"><?= __('Colorado'); ?></option>
                                                                                    </select><span class="select2 select2-container select2-container--default select2-container--below select2-container--open select2-container--focus" dir="ltr" data-select2-id="1" style="width: 426.388px;"><span class="selection"><span class="select2-selection select2-selection--single" aria-haspopup="true" aria-expanded="true" aria-disabled="false" aria-labelledby="select2-kt_select2_1-container" aria-owns="select2-kt_select2_1-results" aria-controls="select2-kt_select2_1-results" aria-activedescendant="select2-kt_select2_1-result-i5cn-AZ"><span class="select2-selection__rendered" id="select2-kt_select2_1-container" aria-readonly="true" title="<?= htmlspecialchars(__('Alaska'), ENT_QUOTES, 'UTF-8'); ?>"><?= __('Alaska'); ?></span><span class="select2-selection__arrow" aria-hidden="true"><b aria-hidden="true"></b></span></span></span><span class="dropdown-wrapper"></span></span>
                                                                                </div>
                                                                            </div>
                                                                            <div id="kt_repeater_1">
                                                                                <div class="form-group form-group-last row" id="kt_repeater_1_2">
                                                                                    <span class="col-lg-2 col-form-label"><?= __('Website'); ?>:</span>
                                                                                    <div data-repeater-list="" class="col-lg-10">
                                                                                        <div data-repeater-item="" class="form-group row align-items-center">
                                                                                            <div class="col-lg-5 col-md-6 wdt">
                                                                                                <div class="kt-form__group--inline">
                                                                                                    <div class="kt-form__label mt-3">
                                                                                                        <span><?= __('URL'); ?>:</span>
                                                                                                    </div>
                                                                                                    <div class="kt-form__control">
                                                                                                        <input aria-label="<?= htmlspecialchars(__('Enter website URL'), ENT_QUOTES, 'UTF-8'); ?>" type="email" class="form-control" placeholder="<?= htmlspecialchars(__('Enter website URL'), ENT_QUOTES, 'UTF-8'); ?>">
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="d-md-none kt-margin-b-10"></div>
                                                                                            </div>
                                                                                            <div class="col-md-4 mt-5">
                                                                                                <a href="javascript:;" data-repeater-delete="" class="btn-sm btn btn-label-danger btn-bold">
                                                                                                    <i class="la la-trash-o"></i>
                                                                                                    <?= __('Delete'); ?>
                                                                                                </a>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="form-group form-group-last row">
                                                                                    <span class="col-lg-2 col-form-label"></span>
                                                                                    <div class="col-lg-4">
                                                                                        <a href="javascript:;" data-repeater-create="" class="btn btn-bold btn-sm btn-label-brand">
                                                                                            <i class="la la-plus"></i> <?= __('Add'); ?>
                                                                                        </a>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <br />
                                                                            <div class="form-group row">
                                                                                <span class="col-lg-2 col-form-label"><?= __('Email address'); ?>:</span>
                                                                                <div class="col-lg-10">
                                                                                    <input aria-label="<?= htmlspecialchars(__('Enter email'), ENT_QUOTES, 'UTF-8'); ?>" type="email" class="form-control" placeholder="<?= htmlspecialchars(__('Enter email'), ENT_QUOTES, 'UTF-8'); ?>">
                                                                                    <span class="form-text text-muted"><?= __('We\'ll never share your email with anyone else'); ?></span>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group row">
                                                                                <span class="col-lg-2 col-form-label"><?= __('Contact'); ?></span>
                                                                                <div class="col-lg-10">
                                                                                    <div class="input-group">
                                                                                        <div class="input-group-prepend"><span class="input-group-text"><i class="la la-chain"></i></span></div>
                                                                                        <input aria-label="<?= htmlspecialchars(__('Phone number'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="<?= htmlspecialchars(__('Phone number'), ENT_QUOTES, 'UTF-8'); ?>">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group row">
                                                                                <span class="col-lg-2 col-form-label"><?= __('Communication'); ?>:</span>
                                                                                <div class="col-xl-10 col-lg-10 col-sm-12 col-md-12">
                                                                                    <div class="kt-checkbox-inline">
                                                                                        <label class="kt-checkbox">
                                                                                            <input aria-label="<?= htmlspecialchars(__('Checkbox'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox"> <?= __('Email'); ?>
                                                                                            <span></span>
                                                                                        </label>
                                                                                        <label class="kt-checkbox">
                                                                                            <input aria-label="<?= htmlspecialchars(__('Checkbox'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox"> <?= __('SMS'); ?>
                                                                                            <span></span>
                                                                                        </label>
                                                                                        <label class="kt-checkbox">
                                                                                            <input aria-label="<?= htmlspecialchars(__('Checkbox'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox"> <?= __('Phone'); ?>
                                                                                            <span></span>
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="kt-separator kt-separator--border-dashed kt-separator--space-lg"></div>
                                                                            <div id="kt_repeater_1_3">
                                                                                <div class="form-group form-group-last row" id="kt_repeater_1_4">
                                                                                    <span class="col-lg-2 col-form-label"><?= __('Contacts'); ?>:</span>
                                                                                    <div data-repeater-list="" class="col-lg-10">
                                                                                        <div data-repeater-item="" class="form-group row align-items-center">
                                                                                            <div class="col-md-3">
                                                                                                <div class="kt-form__group--inline">
                                                                                                    <div class="kt-form__label">
                                                                                                        <span><?= __('Name'); ?>:</span>
                                                                                                    </div>
                                                                                                    <div class="kt-form__control">
                                                                                                        <input aria-label="<?= htmlspecialchars(__('Enter full name'), ENT_QUOTES, 'UTF-8'); ?>" type="email" class="form-control" placeholder="<?= htmlspecialchars(__('Enter full name'), ENT_QUOTES, 'UTF-8'); ?>">
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="d-md-none kt-margin-b-10"></div>
                                                                                            </div>
                                                                                            <div class="col-md-3">
                                                                                                <div class="kt-form__group--inline">
                                                                                                    <div class="kt-form__label">
                                                                                                        <span class="kt-label m-label--single"><?= __('Number'); ?>:</span>
                                                                                                    </div>
                                                                                                    <div class="kt-form__control">
                                                                                                        <input aria-label="<?= htmlspecialchars(__('Enter contact number'), ENT_QUOTES, 'UTF-8'); ?>" type="email" class="form-control" placeholder="<?= htmlspecialchars(__('Enter contact number'), ENT_QUOTES, 'UTF-8'); ?>">
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="d-md-none kt-margin-b-10"></div>
                                                                                            </div>
                                                                                            <div class="col-md-2">
                                                                                                <div class="kt-radio-inline">
                                                                                                    <label class="kt-checkbox kt-checkbox--state-success">
                                                                                                        <input aria-label="<?= htmlspecialchars(__('Checkbox'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox"> <?= __('Primary'); ?>
                                                                                                        <span></span>
                                                                                                    </label>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-md-4">
                                                                                                <a href="javascript:;" data-repeater-delete="" class="btn-sm btn btn-label-danger btn-bold">
                                                                                                    <i class="la la-trash-o"></i>
                                                                                                    <?= __('Delete'); ?>
                                                                                                </a>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="form-group form-group-last row">
                                                                                    <span class="col-lg-2 col-form-label"></span>
                                                                                    <div class="col-lg-4">
                                                                                        <a href="javascript:;" data-repeater-create="" class="btn btn-bold btn-sm btn-label-brand">
                                                                                            <i class="la la-plus"></i> <?= __('Add'); ?>
                                                                                        </a>
                                                                                    </div>
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
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </form></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


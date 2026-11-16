<div class="col-xl-12">
                                            <div class="kt-portlet">
                                                <div class="kt-portlet__head">
                                                    <div class="kt-portlet__head-label">
                                                        <h3 class="kt-portlet__head-title"><?= __('Account Information'); ?> <small><?= __('change your account settings'); ?></small></h3>
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
                                                                        <h3 class="kt-section__title kt-section__title-sm"><?= __('Account'); ?>:</h3>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <span class="col-xl-3 col-lg-3 col-form-label"><?= __('Username'); ?></span>
                                                                    <div class="col-lg-9 col-xl-6">
                                                                        <div class="kt-spinner kt-spinner--sm kt-spinner--success kt-spinner--right kt-spinner--input">
                                                                            <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" type="text" value="nick84">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <span class="col-xl-3 col-lg-3 col-form-label"><?= __('Email Address'); ?></span>
                                                                    <div class="col-lg-9 col-xl-6">
                                                                        <div class="input-group">
                                                                            <div class="input-group-prepend"><span class="input-group-text"><i class="la la-at"></i></span></div>
                                                                            <input aria-label="<?= htmlspecialchars(__('Email'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" value="nick.watson@loop.com" placeholder="<?= htmlspecialchars(__('Email'), ENT_QUOTES, 'UTF-8'); ?>" aria-describedby="basic-addon1">
                                                                        </div>
                                                                        <span class="form-text text-muted"><?= __('Email will not be publicly displayed.'); ?> <a href="#" class="kt-link"><?= __('Learn more'); ?></a>.</span>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <span class="col-xl-3 col-lg-3 col-form-label"><?= __('Language'); ?></span>
                                                                    <div class="col-lg-9 col-xl-6">
                                                                        <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control">
                                                                            <option><?= __('Select Language...'); ?></option>
                                                                            <option value="id"><?= __('Bahasa Indonesia - Indonesian'); ?></option>
                                                                            <option value="msa"><?= __('Bahasa Melayu - Malay'); ?></option>
                                                                            <option value="ca"><?= __('Català - Catalan'); ?></option>
                                                                            <option value="cs"><?= __('Čeština - Czech'); ?></option>
                                                                            <option value="da"><?= __('Dansk - Danish'); ?></option>
                                                                            <option value="de"><?= __('Deutsch - German'); ?></option>
                                                                            <option value="en" selected=""><?= __('English'); ?></option>
                                                                            <option value="en-gb"><?= __('English UK - British English'); ?></option>
                                                                            <option value="es"><?= __('Español - Spanish'); ?></option>
                                                                            <option value="eu"><?= __('Euskara - Basque (beta)'); ?></option>
                                                                            <option value="fil"><?= __('Filipino'); ?></option>
                                                                            <option value="fr"><?= __('Français - French'); ?></option>
                                                                            <option value="ga"><?= __('Gaeilge - Irish (beta)'); ?></option>
                                                                            <option value="gl"><?= __('Galego - Galician (beta)'); ?></option>
                                                                            <option value="hr"><?= __('Hrvatski - Croatian'); ?></option>
                                                                            <option value="it"><?= __('Italiano - Italian'); ?></option>
                                                                            <option value="hu"><?= __('Magyar - Hungarian'); ?></option>
                                                                            <option value="nl"><?= __('Nederlands - Dutch'); ?></option>
                                                                            <option value="no"><?= __('Norsk - Norwegian'); ?></option>
                                                                            <option value="pl"><?= __('Polski - Polish'); ?></option>
                                                                            <option value="pt"><?= __('Português - Portuguese'); ?></option>
                                                                            <option value="ro"><?= __('Română - Romanian'); ?></option>
                                                                            <option value="sk"><?= __('Slovenčina - Slovak'); ?></option>
                                                                            <option value="fi"><?= __('Suomi - Finnish'); ?></option>
                                                                            <option value="sv"><?= __('Svenska - Swedish'); ?></option>
                                                                            <option value="vi"><?= __('Tiếng Việt - Vietnamese'); ?></option>
                                                                            <option value="tr"><?= __('Türkçe - Turkish'); ?></option>
                                                                            <option value="el"><?= __('Ελληνικά - Greek'); ?></option>
                                                                            <option value="bg"><?= __('Български език - Bulgarian'); ?></option>
                                                                            <option value="ru"><?= __('Русский - Russian'); ?></option>
                                                                            <option value="sr"><?= __('Српски - Serbian'); ?></option>
                                                                            <option value="uk"><?= __('Українська мова - Ukrainian'); ?></option>
                                                                            <option value="he"><?= __('עִבְרִית - Hebrew'); ?></option>
                                                                            <option value="ur"><?= __('اردو - Urdu (beta)'); ?></option>
                                                                            <option value="ar"><?= __('العربية - Arabic'); ?></option>
                                                                            <option value="fa"><?= __('فارسی - Persian'); ?></option>
                                                                            <option value="mr"><?= __('मराठी - Marathi'); ?></option>
                                                                            <option value="hi"><?= __('हिन्दी - Hindi'); ?></option>
                                                                            <option value="bn"><?= __('বাংলা - Bangla'); ?></option>
                                                                            <option value="gu"><?= __('ગુજરાતી - Gujarati'); ?></option>
                                                                            <option value="ta"><?= __('தமிழ் - Tamil'); ?></option>
                                                                            <option value="kn"><?= __('ಕನ್ನಡ - Kannada'); ?></option>
                                                                            <option value="th"><?= __('ภาษาไทย - Thai'); ?></option>
                                                                            <option value="ko"><?= __('한국어 - Korean'); ?></option>
                                                                            <option value="ja"><?= __('日本語 - Japanese'); ?></option>
                                                                            <option value="zh-cn"><?= __('简体中文 - Simplified Chinese'); ?></option>
                                                                            <option value="zh-tw"><?= __('繁體中文 - Traditional Chinese'); ?></option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <span class="col-xl-3 col-lg-3 col-form-label"><?= __('Time Zone'); ?></span>
                                                                    <div class="col-lg-9 col-xl-6">
                                                                        <select aria-label="<?= htmlspecialchars(__('Select option'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control">
                                                                            <option data-offset="-39600" value="International Date Line West"><?= htmlspecialchars(__('(GMT-11:00) International Date Line West'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="-39600" value="Midway Island"><?= htmlspecialchars(__('(GMT-11:00) Midway Island'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="-39600" value="Samoa"><?= htmlspecialchars(__('(GMT-11:00) Samoa'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="-36000" value="Hawaii"><?= htmlspecialchars(__('(GMT-10:00) Hawaii'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="-28800" value="Alaska"><?= htmlspecialchars(__('(GMT-08:00) Alaska'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="-25200" value="Pacific Time (US &amp; Canada)"><?= htmlspecialchars(__('(GMT-07:00) Pacific Time (US & Canada)'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="-25200" value="Tijuana"><?= htmlspecialchars(__('(GMT-07:00) Tijuana'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="-25200" value="Arizona"><?= htmlspecialchars(__('(GMT-07:00) Arizona'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="-21600" value="Mountain Time (US &amp; Canada)"><?= htmlspecialchars(__('(GMT-06:00) Mountain Time (US & Canada)'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="-21600" value="Chihuahua"><?= htmlspecialchars(__('(GMT-06:00) Chihuahua'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="-21600" value="Mazatlan"><?= htmlspecialchars(__('(GMT-06:00) Mazatlan'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="-21600" value="Saskatchewan"><?= htmlspecialchars(__('(GMT-06:00) Saskatchewan'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="-21600" value="Central America"><?= htmlspecialchars(__('(GMT-06:00) Central America'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="-18000" value="Central Time (US &amp; Canada)"><?= htmlspecialchars(__('(GMT-05:00) Central Time (US & Canada)'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="-18000" value="Guadalajara"><?= htmlspecialchars(__('(GMT-05:00) Guadalajara'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="-18000" value="Mexico City"><?= htmlspecialchars(__('(GMT-05:00) Mexico City'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="-18000" value="Monterrey"><?= htmlspecialchars(__('(GMT-05:00) Monterrey'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="-18000" value="Bogota"><?= htmlspecialchars(__('(GMT-05:00) Bogota'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="-18000" value="Lima"><?= htmlspecialchars(__('(GMT-05:00) Lima'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="-18000" value="Quito"><?= htmlspecialchars(__('(GMT-05:00) Quito'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="-14400" value="Eastern Time (US &amp; Canada)"><?= htmlspecialchars(__('(GMT-04:00) Eastern Time (US & Canada)'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="-14400" value="Indiana (East)"><?= htmlspecialchars(__('(GMT-04:00) Indiana (East)'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="-14400" value="Caracas"><?= htmlspecialchars(__('(GMT-04:00) Caracas'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="-14400" value="La Paz"><?= htmlspecialchars(__('(GMT-04:00) La Paz'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="-14400" value="Georgetown"><?= htmlspecialchars(__('(GMT-04:00) Georgetown'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="-10800" value="Atlantic Time (Canada)"><?= htmlspecialchars(__('(GMT-03:00) Atlantic Time (Canada)'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="-10800" value="Santiago"><?= htmlspecialchars(__('(GMT-03:00) Santiago'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="-10800" value="Brasilia"><?= htmlspecialchars(__('(GMT-03:00) Brasilia'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="-10800" value="Buenos Aires"><?= htmlspecialchars(__('(GMT-03:00) Buenos Aires'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="-9000" value="Newfoundland"><?= htmlspecialchars(__('(GMT-02:30) Newfoundland'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="-7200" value="Greenland"><?= htmlspecialchars(__('(GMT-02:00) Greenland'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="-7200" value="Mid-Atlantic"><?= htmlspecialchars(__('(GMT-02:00) Mid-Atlantic'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="-3600" value="Cape Verde Is."><?= htmlspecialchars(__('(GMT-01:00) Cape Verde Is.'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="0" value="Azores"><?= htmlspecialchars(__('(GMT) Azores'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="0" value="Monrovia"><?= htmlspecialchars(__('(GMT) Monrovia'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="0" value="UTC"><?= htmlspecialchars(__('(GMT) UTC'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="3600" value="Dublin"><?= htmlspecialchars(__('(GMT+01:00) Dublin'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="3600" value="Edinburgh"><?= htmlspecialchars(__('(GMT+01:00) Edinburgh'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="3600" value="Lisbon"><?= htmlspecialchars(__('(GMT+01:00) Lisbon'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="3600" value="London"><?= htmlspecialchars(__('(GMT+01:00) London'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="3600" value="Casablanca"><?= htmlspecialchars(__('(GMT+01:00) Casablanca'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="3600" value="West Central Africa"><?= htmlspecialchars(__('(GMT+01:00) West Central Africa'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="7200" value="Belgrade"><?= htmlspecialchars(__('(GMT+02:00) Belgrade'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="7200" value="Bratislava"><?= htmlspecialchars(__('(GMT+02:00) Bratislava'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="7200" value="Budapest"><?= htmlspecialchars(__('(GMT+02:00) Budapest'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="7200" value="Ljubljana"><?= htmlspecialchars(__('(GMT+02:00) Ljubljana'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="7200" value="Prague"><?= htmlspecialchars(__('(GMT+02:00) Prague'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="7200" value="Sarajevo"><?= htmlspecialchars(__('(GMT+02:00) Sarajevo'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="7200" value="Skopje"><?= htmlspecialchars(__('(GMT+02:00) Skopje'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="7200" value="Warsaw"><?= htmlspecialchars(__('(GMT+02:00) Warsaw'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="7200" value="Zagreb"><?= htmlspecialchars(__('(GMT+02:00) Zagreb'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="7200" value="Brussels"><?= htmlspecialchars(__('(GMT+02:00) Brussels'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="7200" value="Copenhagen"><?= htmlspecialchars(__('(GMT+02:00) Copenhagen'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="7200" value="Madrid"><?= htmlspecialchars(__('(GMT+02:00) Madrid'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="7200" value="Paris"><?= htmlspecialchars(__('(GMT+02:00) Paris'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="7200" value="Amsterdam"><?= htmlspecialchars(__('(GMT+02:00) Amsterdam'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="7200" value="Berlin"><?= htmlspecialchars(__('(GMT+02:00) Berlin'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="7200" value="Bern"><?= htmlspecialchars(__('(GMT+02:00) Bern'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="7200" value="Rome"><?= htmlspecialchars(__('(GMT+02:00) Rome'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="7200" value="Stockholm"><?= htmlspecialchars(__('(GMT+02:00) Stockholm'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="7200" value="Vienna"><?= htmlspecialchars(__('(GMT+02:00) Vienna'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="7200" value="Cairo"><?= htmlspecialchars(__('(GMT+02:00) Cairo'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="7200" value="Harare"><?= htmlspecialchars(__('(GMT+02:00) Harare'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="7200" value="Pretoria"><?= htmlspecialchars(__('(GMT+02:00) Pretoria'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="10800" value="Bucharest"><?= htmlspecialchars(__('(GMT+03:00) Bucharest'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="10800" value="Helsinki"><?= htmlspecialchars(__('(GMT+03:00) Helsinki'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="10800" value="Kiev"><?= htmlspecialchars(__('(GMT+03:00) Kiev'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="10800" value="Kyiv"><?= htmlspecialchars(__('(GMT+03:00) Kyiv'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="10800" value="Riga"><?= htmlspecialchars(__('(GMT+03:00) Riga'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="10800" value="Sofia"><?= htmlspecialchars(__('(GMT+03:00) Sofia'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="10800" value="Tallinn"><?= htmlspecialchars(__('(GMT+03:00) Tallinn'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="10800" value="Vilnius"><?= htmlspecialchars(__('(GMT+03:00) Vilnius'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="10800" value="Athens"><?= htmlspecialchars(__('(GMT+03:00) Athens'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="10800" value="Istanbul"><?= htmlspecialchars(__('(GMT+03:00) Istanbul'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="10800" value="Minsk"><?= htmlspecialchars(__('(GMT+03:00) Minsk'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="10800" value="Jerusalem"><?= htmlspecialchars(__('(GMT+03:00) Jerusalem'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="10800" value="Moscow"><?= htmlspecialchars(__('(GMT+03:00) Moscow'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="10800" value="St. Petersburg"><?= htmlspecialchars(__('(GMT+03:00) St. Petersburg'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="10800" value="Volgograd"><?= htmlspecialchars(__('(GMT+03:00) Volgograd'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="10800" value="Kuwait"><?= htmlspecialchars(__('(GMT+03:00) Kuwait'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="10800" value="Riyadh"><?= htmlspecialchars(__('(GMT+03:00) Riyadh'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="10800" value="Nairobi"><?= htmlspecialchars(__('(GMT+03:00) Nairobi'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="10800" value="Baghdad"><?= htmlspecialchars(__('(GMT+03:00) Baghdad'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="14400" value="Abu Dhabi"><?= htmlspecialchars(__('(GMT+04:00) Abu Dhabi'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="14400" value="Muscat"><?= htmlspecialchars(__('(GMT+04:00) Muscat'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="14400" value="Baku"><?= htmlspecialchars(__('(GMT+04:00) Baku'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="14400" value="Tbilisi"><?= htmlspecialchars(__('(GMT+04:00) Tbilisi'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="14400" value="Yerevan"><?= htmlspecialchars(__('(GMT+04:00) Yerevan'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="16200" value="Tehran"><?= htmlspecialchars(__('(GMT+04:30) Tehran'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="16200" value="Kabul"><?= htmlspecialchars(__('(GMT+04:30) Kabul'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="18000" value="Ekaterinburg"><?= htmlspecialchars(__('(GMT+05:00) Ekaterinburg'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="18000" value="Islamabad"><?= htmlspecialchars(__('(GMT+05:00) Islamabad'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="18000" value="Karachi"><?= htmlspecialchars(__('(GMT+05:00) Karachi'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="18000" value="Tashkent"><?= htmlspecialchars(__('(GMT+05:00) Tashkent'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="19800" value="Chennai"><?= htmlspecialchars(__('(GMT+05:30) Chennai'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="19800" value="Kolkata"><?= htmlspecialchars(__('(GMT+05:30) Kolkata'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="19800" value="Mumbai"><?= htmlspecialchars(__('(GMT+05:30) Mumbai'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="19800" value="New Delhi"><?= htmlspecialchars(__('(GMT+05:30) New Delhi'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="19800" value="Sri Jayawardenepura"><?= htmlspecialchars(__('(GMT+05:30) Sri Jayawardenepura'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="20700" value="Kathmandu"><?= htmlspecialchars(__('(GMT+05:45) Kathmandu'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="21600" value="Astana"><?= htmlspecialchars(__('(GMT+06:00) Astana'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="21600" value="Dhaka"><?= htmlspecialchars(__('(GMT+06:00) Dhaka'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="21600" value="Almaty"><?= htmlspecialchars(__('(GMT+06:00) Almaty'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="21600" value="Urumqi"><?= htmlspecialchars(__('(GMT+06:00) Urumqi'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="23400" value="Rangoon"><?= htmlspecialchars(__('(GMT+06:30) Rangoon'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="25200" value="Novosibirsk"><?= htmlspecialchars(__('(GMT+07:00) Novosibirsk'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="25200" value="Bangkok"><?= htmlspecialchars(__('(GMT+07:00) Bangkok'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="25200" value="Hanoi"><?= htmlspecialchars(__('(GMT+07:00) Hanoi'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="25200" value="Jakarta"><?= htmlspecialchars(__('(GMT+07:00) Jakarta'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="25200" value="Krasnoyarsk"><?= htmlspecialchars(__('(GMT+07:00) Krasnoyarsk'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="28800" value="Beijing"><?= htmlspecialchars(__('(GMT+08:00) Beijing'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="28800" value="Chongqing"><?= htmlspecialchars(__('(GMT+08:00) Chongqing'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="28800" value="Hong Kong"><?= htmlspecialchars(__('(GMT+08:00) Hong Kong'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="28800" value="Kuala Lumpur"><?= htmlspecialchars(__('(GMT+08:00) Kuala Lumpur'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="28800" value="Singapore"><?= htmlspecialchars(__('(GMT+08:00) Singapore'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="28800" value="Taipei"><?= htmlspecialchars(__('(GMT+08:00) Taipei'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="28800" value="Perth"><?= htmlspecialchars(__('(GMT+08:00) Perth'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="28800" value="Irkutsk"><?= htmlspecialchars(__('(GMT+08:00) Irkutsk'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="28800" value="Ulaan Bataar"><?= htmlspecialchars(__('(GMT+08:00) Ulaan Bataar'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="32400" value="Seoul"><?= htmlspecialchars(__('(GMT+09:00) Seoul'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="32400" value="Osaka"><?= htmlspecialchars(__('(GMT+09:00) Osaka'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="32400" value="Sapporo"><?= htmlspecialchars(__('(GMT+09:00) Sapporo'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="32400" value="Tokyo"><?= htmlspecialchars(__('(GMT+09:00) Tokyo'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="32400" value="Yakutsk"><?= htmlspecialchars(__('(GMT+09:00) Yakutsk'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="34200" value="Darwin"><?= htmlspecialchars(__('(GMT+09:30) Darwin'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="34200" value="Adelaide"><?= htmlspecialchars(__('(GMT+09:30) Adelaide'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="36000" value="Canberra"><?= htmlspecialchars(__('(GMT+10:00) Canberra'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="36000" value="Melbourne"><?= htmlspecialchars(__('(GMT+10:00) Melbourne'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="36000" value="Sydney"><?= htmlspecialchars(__('(GMT+10:00) Sydney'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="36000" value="Brisbane"><?= htmlspecialchars(__('(GMT+10:00) Brisbane'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="36000" value="Hobart"><?= htmlspecialchars(__('(GMT+10:00) Hobart'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="36000" value="Vladivostok"><?= htmlspecialchars(__('(GMT+10:00) Vladivostok'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="36000" value="Guam"><?= htmlspecialchars(__('(GMT+10:00) Guam'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="36000" value="Port Moresby"><?= htmlspecialchars(__('(GMT+10:00) Port Moresby'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="36000" value="Solomon Is."><?= htmlspecialchars(__('(GMT+10:00) Solomon Is.'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="39600" value="Magadan"><?= htmlspecialchars(__('(GMT+11:00) Magadan'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="39600" value="New Caledonia"><?= htmlspecialchars(__('(GMT+11:00) New Caledonia'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="43200" value="Fiji"><?= htmlspecialchars(__('(GMT+12:00) Fiji'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="43200" value="Kamchatka"><?= htmlspecialchars(__('(GMT+12:00) Kamchatka'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="43200" value="Marshall Is."><?= htmlspecialchars(__('(GMT+12:00) Marshall Is.'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="43200" value="Auckland"><?= htmlspecialchars(__('(GMT+12:00) Auckland'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="43200" value="Wellington"><?= htmlspecialchars(__('(GMT+12:00) Wellington'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                            <option data-offset="46800" value="Nuku'alofa"><?= htmlspecialchars(__('(GMT+13:00) Nuku\'alofa'), ENT_QUOTES, 'UTF-8'); ?></option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group form-group-last row">
                                                                    <span class="col-xl-3 col-lg-3 col-form-label"><?= __('Communication'); ?></span>
                                                                    <div class="col-lg-9 col-xl-6">
                                                                        <div class="kt-checkbox-inline">
                                                                            <label class="kt-checkbox">
                                                                                <input aria-label="<?= htmlspecialchars(__('Checkbox'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" checked=""> <?= __('Email'); ?>
                                                                                <span></span>
                                                                            </label>
                                                                            <label class="kt-checkbox">
                                                                                <input aria-label="<?= htmlspecialchars(__('Checkbox'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" checked=""> <?= __('SMS'); ?>
                                                                                <span></span>
                                                                            </label>
                                                                            <label class="kt-checkbox">
                                                                                <input aria-label="<?= htmlspecialchars(__('Checkbox'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox"> <?= __('Phone'); ?>
                                                                                <span></span>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <div class="kt-portlet__foot">
                                                        <div class="kt-form__actions">
                                                            <div class="row">
                                                                <div class="col-lg-3 col-xl-3">
                                                                </div>
                                                                <div class="col-lg-9 col-xl-9">
                                                                    <button type="reset" class="btn btn-success"><?= __('Submit'); ?></button>&nbsp;
                                                                    <button type="reset" class="btn btn-secondary"><?= __('Cancel'); ?></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>


                                            <!--end:: Widgets/Best Sellers-->
                                        </div>
                                    </div>


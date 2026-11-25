<div class="d-flex flex-column-fluid">
    <div class="container-fluid" >


    <div class="alert alert-custom alert-white shadow-lg fade show mb-5" role="alert">
        <div class="alert-icon"><i class="flaticon-warning kt-font-brand"></i></div>
        <div class="alert-text">This function is used for maintaining <strong><?= __('Candidates'); ?></strong><?= __('. * represents mandatory fields.'); ?></div>
        <div class="alert-close">
            <button type="button" class="close" data-dismiss="alert" aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>">
                <span aria-hidden="true"><i class="ki ki-close"></i></span>
            </button>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <form class="kt-form" id="kt_form" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">
            <!--begin::Portlet-->
            <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                <div class="card-header">
                    <div class="card-title">
                        <span class="kt-portlet__head-icon">
                                    <i class="kt-font-brand flaticon-add-circular-button"></i>
                                </span>
                        <h3 class="card-label">&nbsp;  <?= __('Candidates') ?> </h3>
                    </div>
                    <div class="card-toolbar">
                        &nbsp;
                                                <a href="javascript:history.go(-1)" class="btn btn-light-primary font-weight-bolder mr-2">
                            <i class="ki ki-long-arrow-back icon-xs"></i>
                            <?= __('Back'); ?>
                        </a>


                    </div>
                </div>
                <div class="card-body">
                                            <div class="wizard wizard-2" id="kt_wizard" data-wizard-state="step-first" data-wizard-clickable="false">
                                            <!--begin: Wizard Nav-->
                                            <div class="wizard-nav border-right py-8 px-8 py-lg-20 px-lg-10">
                                                <!--begin::Wizard Step 1 Nav-->
                                                <div class="wizard-steps">

                                                    <div class="wizard-step" data-wizard-type="step" >
                                                        <div class="wizard-wrapper">
                                                            <div class="wizard-icon">
                                                                <span class="svg-icon svg-icon-2x">

                                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                            <polygon points="0 0 24 0 24 24 0 24" />
                                                                            <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                                                            <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero" />
                                                                        </g>
                                                                    </svg>
                                                                    <!--end::Svg Icon-->
                                                                </span>
                                                            </div>
                                                            <div class="wizard-label">
                                                                <h3 class="wizard-title"><?= __('Step') ?> 1</h3>
                                                                <div class="wizard-desc"><?= __('Setup Your Account Details') ?></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!--end::Wizard Step 1 Nav-->

                                                    <div class="wizard-step" data-wizard-type="step" >
                                                        <div class="wizard-wrapper">
                                                            <div class="wizard-icon">
                                                                <span class="svg-icon svg-icon-2x">

                                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                            <polygon points="0 0 24 0 24 24 0 24" />
                                                                            <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                                                            <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero" />
                                                                        </g>
                                                                    </svg>
                                                                    <!--end::Svg Icon-->
                                                                </span>
                                                            </div>
                                                            <div class="wizard-label">
                                                                <h3 class="wizard-title"><?= __('Step') ?> 2</h3>
                                                                <div class="wizard-desc"><?= __('Setup Your Account Details') ?></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!--end::Wizard Step 1 Nav-->

                                                    <div class="wizard-step" data-wizard-type="step" >
                                                        <div class="wizard-wrapper">
                                                            <div class="wizard-icon">
                                                                <span class="svg-icon svg-icon-2x">

                                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                            <polygon points="0 0 24 0 24 24 0 24" />
                                                                            <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                                                            <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero" />
                                                                        </g>
                                                                    </svg>
                                                                    <!--end::Svg Icon-->
                                                                </span>
                                                            </div>
                                                            <div class="wizard-label">
                                                                <h3 class="wizard-title"><?= __('Step') ?> 3</h3>
                                                                <div class="wizard-desc"><?= __('Setup Your Account Details') ?></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!--end::Wizard Step 1 Nav-->

                                                    <div class="wizard-step" data-wizard-type="step" >
                                                        <div class="wizard-wrapper">
                                                            <div class="wizard-icon">
                                                                <span class="svg-icon svg-icon-2x">

                                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                            <polygon points="0 0 24 0 24 24 0 24" />
                                                                            <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                                                            <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero" />
                                                                        </g>
                                                                    </svg>
                                                                    <!--end::Svg Icon-->
                                                                </span>
                                                            </div>
                                                            <div class="wizard-label">
                                                                <h3 class="wizard-title"><?= __('Step') ?> 4</h3>
                                                                <div class="wizard-desc"><?= __('Setup Your Account Details') ?></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!--end::Wizard Step 1 Nav-->

                                                    <div class="wizard-step" data-wizard-type="step" >
                                                        <div class="wizard-wrapper">
                                                            <div class="wizard-icon">
                                                                <span class="svg-icon svg-icon-2x">

                                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                            <polygon points="0 0 24 0 24 24 0 24" />
                                                                            <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                                                            <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero" />
                                                                        </g>
                                                                    </svg>
                                                                    <!--end::Svg Icon-->
                                                                </span>
                                                            </div>
                                                            <div class="wizard-label">
                                                                <h3 class="wizard-title"><?= __('Step') ?> 5</h3>
                                                                <div class="wizard-desc"><?= __('Setup Your Account Details') ?></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!--end::Wizard Step 1 Nav-->

                                                    <div class="wizard-step" data-wizard-type="step" >
                                                        <div class="wizard-wrapper">
                                                            <div class="wizard-icon">
                                                                <span class="svg-icon svg-icon-2x">

                                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                            <polygon points="0 0 24 0 24 24 0 24" />
                                                                            <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                                                            <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero" />
                                                                        </g>
                                                                    </svg>
                                                                    <!--end::Svg Icon-->
                                                                </span>
                                                            </div>
                                                            <div class="wizard-label">
                                                                <h3 class="wizard-title"><?= __('Step') ?> 6</h3>
                                                                <div class="wizard-desc"><?= __('Setup Your Account Details') ?></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!--end::Wizard Step 1 Nav-->

                                                    <div class="wizard-step" data-wizard-type="step" >
                                                        <div class="wizard-wrapper">
                                                            <div class="wizard-icon">
                                                                <span class="svg-icon svg-icon-2x">

                                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                            <polygon points="0 0 24 0 24 24 0 24" />
                                                                            <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                                                            <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero" />
                                                                        </g>
                                                                    </svg>
                                                                    <!--end::Svg Icon-->
                                                                </span>
                                                            </div>
                                                            <div class="wizard-label">
                                                                <h3 class="wizard-title"><?= __('Step') ?> 7</h3>
                                                                <div class="wizard-desc"><?= __('Setup Your Account Details') ?></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!--end::Wizard Step 1 Nav-->

                                                </div>
                                            </div>

<div class="wizard-body py-8 px-8 py-lg-20 px-lg-10">

                                                <div class="row">
                                                    <div class="offset-xxl-2 col-xxl-8">

                <div class="pb-5" data-wizard-type="step-content" >
                    <div class="row">
                                     <div class="form-group col-lg-12">
                 <span><?= __('Image') ?></span>
                <div class="col-lg-9 col-xl-6">
                    <div class="image-input image-input-outline" id="kt_image_61">
                        <div class="image-input-wrapper" style="background-image: url(uploads/all/no-image.jpg)"></div>

                        <label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="change" data-toggle="tooltip" title="" data-original-title="<?= __('Change avatar') ?>">
                            <i class="fa fa-pen icon-sm text-muted"></i>
                            <input aria-label="<?= htmlspecialchars(__('Image'), ENT_QUOTES, 'UTF-8'); ?>" type="file" name="image" id="image" accept=".png, .jpg, .jpeg">
                        <input type="hidden" name="hidimage" value="">
                        </label>

                        <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="cancel" data-toggle="tooltip" title="<?= __('Cancel avatar') ?>">
                            <i class="ki ki-bold-close icon-xs text-muted"></i>
                        </span>
                    </div>

                </div>
            </div>

            <div class="form-group col-lg-12">
        <span><?= __('First Name') ?></span>
        <div class="input-group input-icon-right">
            <input aria-label="<?= htmlspecialchars(__('First Name'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control"  type="text"  id="first_name" name="first_name"  value="">
            <div class="input-group-append"><span class="input-group-text"><span><i class="la la-adn"></i></span></span></div>
        </div>
        <div style="display: none;color: red;" id="first_name_err"> <?= __('Please enter First Name') ?> </div>
    </div>

            <div class="form-group col-lg-12">
        <span><?= __('Middle Name') ?></span>
        <div class="input-group input-icon-right">
            <input aria-label="<?= htmlspecialchars(__('Middle Name'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control"  type="text"  id="middle_name" name="middle_name"  value="">
            <div class="input-group-append"><span class="input-group-text"><span><i class="la la-adn"></i></span></span></div>
        </div>
        <div style="display: none;color: red;" id="middle_name_err"> <?= __('Please enter Middle Name') ?> </div>
    </div>

            <div class="form-group col-lg-12">
        <span><?= __('Last Name') ?></span>
        <div class="input-group input-icon-right">
            <input aria-label="<?= htmlspecialchars(__('Last Name'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control"  type="text"  id="last_name" name="last_name"  value="">
            <div class="input-group-append"><span class="input-group-text"><span><i class="la la-adn"></i></span></span></div>
        </div>
        <div style="display: none;color: red;" id="last_name_err"> <?= __('Please enter Last Name') ?> </div>
    </div>

            <div class="form-group col-lg-12">
        <span><?= __('Display Name') ?></span>
        <div class="input-group input-icon-right">
            <input aria-label="<?= htmlspecialchars(__('Display Name'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control"  type="text"  id="display_name" name="display_name"  value="">
            <div class="input-group-append"><span class="input-group-text"><span><i class="la la-adn"></i></span></span></div>
        </div>
        <div style="display: none;color: red;" id="display_name_err"> <?= __('Please enter Display Name') ?> </div>
    </div>

                        </div>
                </div>


                <div class="pb-5" data-wizard-type="step-content" >
                    <div class="row">
                                    <div class="form-group col-lg-12">
            <span><?= __('Address') ?></span>
            <div class="kt-radio-inline">
            <textarea aria-label="<?= htmlspecialchars(__('Address'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" id="address" name="address" ></textarea>
            </div>
            <div style="display: none;color: red;" id="address_err"> <?= __('Please Select Address') ?> </div>
        </div>


            <div class="form-group col-lg-6">
        <span><?= __('Post Code') ?></span>
        <div class="input-group input-icon-right">
            <input aria-label="<?= htmlspecialchars(__('Post Code'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control"  type="text"  id="post_code" name="post_code"  value="">
            <div class="input-group-append"><span class="input-group-text"><span><i class="la la-adn"></i></span></span></div>
        </div>
        <div style="display: none;color: red;" id="post_code_err"> <?= __('Please enter Post Code') ?> </div>
    </div>

            <div class="form-group col-lg-6">
        <span><?= __('Phone') ?></span>
        <div class="input-group input-icon-right">
            <input aria-label="<?= htmlspecialchars(__('Phone'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control"  type="text"  id="phone" name="phone"  value="">
            <div class="input-group-append"><span class="input-group-text"><span><i class="la la-adn"></i></span></span></div>
        </div>
        <div style="display: none;color: red;" id="phone_err"> <?= __('Please enter Phone') ?> </div>
    </div>

            <div class="form-group col-lg-12">
        <span><?= __('Email') ?></span>
        <div class="input-group input-icon-right">
            <input aria-label="<?= htmlspecialchars(__('Email'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control"  type="text"  id="email_id" name="email_id"  value="">
            <div class="input-group-append"><span class="input-group-text"><span><i class="la la-adn"></i></span></span></div>
        </div>
        <div style="display: none;color: red;" id="email_id_err"> <?= __('Please enter Email'); ?> </div>
    </div>

                        </div>
                </div>


                <div class="pb-5" data-wizard-type="step-content" >
                    <div class="row">
                                        <div class="form-group col-lg-6">
            <span><?= __('Gender') ?></span>
            <div class="radio-inline">

            <label class="radio"> <input aria-label="<?= htmlspecialchars(__('Gender'), ENT_QUOTES, 'UTF-8'); ?>" type="radio"   name="gender" value='F'>    <span></span>F </label>

            <label class="radio"> <input aria-label="<?= htmlspecialchars(__('Gender'), ENT_QUOTES, 'UTF-8'); ?>" type="radio"   name="gender" value='M'>    <span></span>M </label>
                        </div>
            <div style="display: none;color: red;" id="gender_err"> <?= __('Please Select Gender'); ?> </div>
        </div>

                        <div class="form-group col-lg-6">
        <span><?= __('Marital Status') ?></span>
        <select aria-label="<?= htmlspecialchars(__('Marital Status'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control kt-select2" id="kt_select2_41" name="marital_status">
            <option value=""> <?= __('Select Any one Marital Status') ?></option>

                <option  value="M"><?= __('Married') ?></option>
            </optgroup>

                <option  value="S"><?= __('Single') ?></option>
            </optgroup>
            </select>
        <div style="display: none;color: red;" id="marital_status_err"> <?= __('Please Select Marital Status'); ?> </div>
    </div>


        <div class="form-group col-lg-6" >
            <span><?= __('Date Of Birth') ?></span>

                <div class="input-group date">
                                        <input aria-label="<?= htmlspecialchars(__('Date Of Birth'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control"  value="11/09/2020"  name="date_of_birth"  id="kt_datepicker_31" />
                    <div class="input-group-append">
                        <span class="input-group-text">
                            <i class="la la-calendar"></i>
                        </span>
                    </div>
                </div>

        </div>

            <div class="form-group col-lg-6">
        <span><?= __('Hiring Source') ?></span>
        <div class="input-group input-icon-right">
            <input aria-label="<?= htmlspecialchars(__('Hiring Source'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control"  type="text"  id="hiring_source" name="hiring_source"  value="">
            <div class="input-group-append"><span class="input-group-text"><span><i class="la la-adn"></i></span></span></div>
        </div>
        <div style="display: none;color: red;" id="hiring_source_err"> <?= __('Please enter Hiring Source'); ?> </div>
    </div>

                        </div>
                </div>


                <div class="pb-5" data-wizard-type="step-content" >
                    <div class="row">
                                <div class="form-group col-lg-6">
        <span><?= __('Total Experiance') ?></span>
        <div class="input-group input-icon-right">
            <input aria-label="<?= htmlspecialchars(__('Total Experiance'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control"  type="text"  id="total_experiance" name="total_experiance"  value="">
            <div class="input-group-append"><span class="input-group-text"><span><i class="la la-adn"></i></span></span></div>
        </div>
        <div style="display: none;color: red;" id="total_experiance_err"> <?= __('Please enter Total Experiance'); ?> </div>
    </div>

            <div class="form-group col-lg-6">
        <span><?= __('Notice Period') ?></span>
        <div class="input-group input-icon-right">
            <input aria-label="<?= htmlspecialchars(__('Notice Period'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control"  type="text"  id="notice_period" name="notice_period"  value="">
            <div class="input-group-append"><span class="input-group-text"><span><i class="la la-adn"></i></span></span></div>
        </div>
        <div style="display: none;color: red;" id="notice_period_err"> <?= __('Please enter Notice Period'); ?> </div>
    </div>

                        <div class="form-group col-lg-6">
        <span><?= __('Negotiatable') ?></span>
        <select aria-label="<?= htmlspecialchars(__('Np Negotiatable'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control kt-select2" id="kt_select2_42" name="np_negotiatable">
            <option value=""> <?= __('Select Any one Negotiatable') ?></option>

                <option  value="1"><?= __('NO') ?></option>
            </optgroup>

                <option  selected  value="0"><?= __('YES') ?></option>
            </optgroup>
            </select>
        <div style="display: none;color: red;" id="np_negotiatable_err"> <?= __('Please Select Negotiatable'); ?> </div>
    </div>


            <div class="form-group col-lg-6">
        <span><?= __('Candidate Experiances') ?></span>
        <div class="input-group input-icon-right">
            <input aria-label="<?= htmlspecialchars(__('Candidate Experiances'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control"  type="text"  id="candidate_experiances" name="candidate_experiances"  value="">
            <div class="input-group-append"><span class="input-group-text"><span><i class="la la-adn"></i></span></span></div>
        </div>
        <div style="display: none;color: red;" id="candidate_experiances_err"> <?= __('Please enter Candidate Experiances'); ?> </div>
    </div>

                        </div>
                </div>


                <div class="pb-5" data-wizard-type="step-content" >
                    <div class="row">
                                <div class="form-group col-lg-6">
        <span><?= __('Candidate Qualifications') ?></span>
        <div class="input-group input-icon-right">
            <input aria-label="<?= htmlspecialchars(__('Candidate Qualifications'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control"  type="text"  id="candidate_qualifications" name="candidate_qualifications"  value="">
            <div class="input-group-append"><span class="input-group-text"><span><i class="la la-adn"></i></span></span></div>
        </div>
        <div style="display: none;color: red;" id="candidate_qualifications_err"> <?= __('Please enter Candidate Qualifications'); ?> </div>
    </div>

                        </div>
                </div>


                <div class="pb-5" data-wizard-type="step-content" >
                    <div class="row">
                                <div class="form-group col-lg-6">
        <span><?= __('Employee Dependents') ?></span>
        <div class="input-group input-icon-right">
            <input aria-label="<?= htmlspecialchars(__('Employee Dependents'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control"  type="text"  id="employee_dependents" name="employee_dependents"  value="">
            <div class="input-group-append"><span class="input-group-text"><span><i class="la la-adn"></i></span></span></div>
        </div>
        <div style="display: none;color: red;" id="employee_dependents_err"> <?= __('Please enter Employee Dependents'); ?> </div>
    </div>

                        </div>
                </div>


                <div class="pb-5" data-wizard-type="step-content" >
                    <div class="row">
                                <div class="form-group col-lg-6">
        <span><?= __('Employee Contacts') ?></span>
        <div class="input-group input-icon-right">
            <input aria-label="<?= htmlspecialchars(__('Employee Contacts'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control"  type="text"  id="employee_contacts" name="employee_contacts"  value="">
            <div class="input-group-append"><span class="input-group-text"><span><i class="la la-adn"></i></span></span></div>
        </div>
        <div style="display: none;color: red;" id="employee_contacts_err"> <?= __('Please enter Employee Contacts'); ?> </div>
    </div>

            <div class="form-group col-lg-6">
        <span><?= __('Current Location') ?></span>
        <div class="input-group input-icon-right">
            <input aria-label="<?= htmlspecialchars(__('Current Location'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control"  type="text"  id="current_location" name="current_location"  value="">
            <div class="input-group-append"><span class="input-group-text"><span><i class="la la-adn"></i></span></span></div>
        </div>
        <div style="display: none;color: red;" id="current_location_err"> <?= __('Please enter Current Location'); ?> </div>
    </div>

            <div class="form-group col-lg-6">
        <span><?= __('Reason For Change') ?></span>
        <div class="input-group input-icon-right">
            <input aria-label="<?= htmlspecialchars(__('Reason For Change'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control"  type="text"  id="reason_for_change" name="reason_for_change"  value="">
            <div class="input-group-append"><span class="input-group-text"><span><i class="la la-adn"></i></span></span></div>
        </div>
        <div style="display: none;color: red;" id="reason_for_change_err"> <?= __('Please enter Reason For Change'); ?> </div>
    </div>

            <div class="form-group col-lg-6">
        <span><?= __('Relocate') ?></span>
        <div class="input-group input-icon-right">
            <input aria-label="<?= htmlspecialchars(__('Relocate'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control"  type="text"  id="relocate" name="relocate"  value="">
            <div class="input-group-append"><span class="input-group-text"><span><i class="la la-adn"></i></span></span></div>
        </div>
        <div style="display: none;color: red;" id="relocate_err"> <?= __('Please enter Relocate'); ?> </div>
    </div>

            <div class="form-group col-lg-6">
        <span><?= __('Current Ctc') ?></span>
        <div class="input-group input-icon-right">
            <input aria-label="<?= htmlspecialchars(__('Current Ctc'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control"  type="text"  id="current_ctc" name="current_ctc"  value="">
            <div class="input-group-append"><span class="input-group-text"><span><i class="la la-adn"></i></span></span></div>
        </div>
        <div style="display: none;color: red;" id="current_ctc_err"> <?= __('Please enter Current Ctc'); ?> </div>
    </div>

            <div class="form-group col-lg-6">
        <span><?= __('Expected Ctc') ?></span>
        <div class="input-group input-icon-right">
            <input aria-label="<?= htmlspecialchars(__('Expected Ctc'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control"  type="text"  id="expected_ctc" name="expected_ctc"  value="">
            <div class="input-group-append"><span class="input-group-text"><span><i class="la la-adn"></i></span></span></div>
        </div>
        <div style="display: none;color: red;" id="expected_ctc_err"> <?= __('Please enter Expected Ctc'); ?> </div>
    </div>

                        </div>
                </div>

    <div class="d-flex justify-content-between border-top mt-5 pt-10">
                                                                <div class="mr-2">
                                                                    <button type="button" class="btn btn-light-primary font-weight-bolder text-uppercase px-9 py-4" data-wizard-type="action-prev"><?= __('Previous') ?></button>
                                                                </div>
                                                                <div>
                                                                    <button type="button" class="btn btn-success font-weight-bolder text-uppercase px-9 py-4" data-wizard-type="action-submit"><?= __('Submit') ?></button>
                                                                    <button type="button" class="btn btn-primary font-weight-bolder text-uppercase px-9 py-4" data-wizard-type="action-next"><?= __('Next') ?></button>
                                                                </div>
                                                            </div>
                                                        </div>

    </div>
        </div>


        <input type="hidden" name="in_date_pic" value="date_of_birth">
    <input type="hidden" name="mandatory_str" value="first_name,middle_name,last_name,display_name,address,post_code,phone,email_id,gender,marital_status,date_of_birth,hiring_source,total_experiance,notice_period,np_negotiatable,candidate_experiances,candidate_qualifications,employee_dependents,employee_contacts,current_location,reason_for_change,relocate,current_ctc,expected_ctc,image">
    <input type="hidden" name="add_mandatory_str" value="">
        <input type="hidden" name="notification_users" value="3">
    <input type="hidden" name="notification_message" value="New   candidates page has been created">
    <input type="hidden" name="dis_label" value="%7B%22first_name%22%3A%22First+Name%22%2C%22middle_name%22%3A%22Middle+Name%22%2C%22last_name%22%3A%22Last+Name%22%2C%22display_name%22%3A%22Display+Name%22%2C%22address%22%3A%22Address%22%2C%22post_code%22%3A%22Post+Code%22%2C%22phone%22%3A%22Phone%22%2C%22email_id%22%3A%22Email%22%2C%22gender%22%3A%22Gender%22%2C%22marital_status%22%3A%22Marital+Status%22%2C%22date_of_birth%22%3A%22Date+Of+Birth%22%2C%22hiring_source%22%3A%22Hiring+Source%22%2C%22total_experiance%22%3A%22Total+Experiance%22%2C%22notice_period%22%3A%22Notice+Period%22%2C%22np_negotiatable%22%3A%22Negotiatable%22%2C%22candidate_experiances%22%3A%22Candidate+Experiances%22%2C%22candidate_qualifications%22%3A%22Candidate+Qualifications%22%2C%22employee_dependents%22%3A%22Employee+Dependents%22%2C%22employee_contacts%22%3A%22Employee+Contacts%22%2C%22current_location%22%3A%22Current+Location%22%2C%22reason_for_change%22%3A%22Reason+For+Change%22%2C%22relocate%22%3A%22Relocate%22%2C%22current_ctc%22%3A%22Current+Ctc%22%2C%22expected_ctc%22%3A%22Expected+Ctc%22%2C%22image%22%3A%22Image%22%7D">
    <input type="hidden" name="image" value="image">
    <input type="hidden" name="duplicate_table_name" id="duplicate_table_name" value="candidates">
    <input type="hidden" name="insert_page_name" value="COM_INSERT_CANDIDATES">
    <input type="hidden" name="list_page_name" value="COM_LIST_CANDIDATES">
    <input type="hidden" name="option" value="">

</form>
<div id="kt_modal_KTDatatable_remote" class="modal fade" aria-hidden="true">
    <div class="modal-dialog " >
        <div class="modal-content"  id="popup1">

        </div>
    </div>
</div>                    </div>

<?php
if (isset($_POST['submit'])){
    $post = $_POST;
    $image = imageupload('users','image',$_POST['hid_image']);
    $post['image_name'] = $image;
    $url = API."?mode=updateuserdetails&data=".urlencode(json_encode($post));
    $response = file_get_contents($url, false, $context);
    if ($response) {
        header("location:index.php?option=com_profile");
    }
}

?>
<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
    <!-- begin:: Content -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">

        <!--Begin::App-->
        <div class="kt-grid kt-grid--desktop kt-grid--ver kt-grid--ver-desktop kt-app">

            <!--Begin:: App Aside Mobile Toggle-->
            <button class="kt-app__aside-close" id="kt_user_profile_aside_close">
                <i class="la la-close"></i>
            </button>

            <!--End:: App Aside Mobile Toggle-->

            <!--Begin:: App Aside-->
            <div class="kt-grid__item kt-app__toggle kt-app__aside" id="kt_user_profile_aside">


                <div class="kt-portlet ">
                    <div class="kt-portlet__head  kt-portlet__head--noborder">
                        <div class="kt-portlet__head-label">
                            <div class="kt-portlet__head-title">
                            </div>
                        </div>
                        <div class="kt-portlet__head-toolbar">
                            <a href="#" class="btn btn-clean btn-sm btn-icon btn-icon-md" data-toggle="dropdown" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                <i class="flaticon-more-1"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-fit dropdown-menu-md">

                                <!--begin::Nav-->
                                <ul class="kt-nav">
                                    <li class="kt-nav__head">
                                        Export Options
                                        <span data-toggle="kt-tooltip" data-placement="right" title="Click to learn more...">
                                            <svg xmlns="http:
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <rect x="0" y="0" width="24" height="24" />
                                                    <circle fill="#000000" opacity="0.3" cx="12" cy="12" r="10" />
                                                    <rect fill="#000000" x="11" y="10" width="2" height="7" rx="1" />
                                                    <rect fill="#000000" x="11" y="7" width="2" height="2" rx="1" />
                                                </g>
                                            </svg> </span>
                                    </li>
                                    <li class="kt-nav__separator"></li>
                                    <li class="kt-nav__item">
                                        <a href="#" class="kt-nav__link">
                                            <i class="kt-nav__link-icon flaticon2-drop"></i>
                                            <span class="kt-nav__link-text">Activity</span>
                                        </a>
                                    </li>
                                    <li class="kt-nav__item">
                                        <a href="#" class="kt-nav__link">
                                            <i class="kt-nav__link-icon flaticon2-calendar-8"></i>
                                            <span class="kt-nav__link-text">FAQ</span>
                                        </a>
                                    </li>
                                    <li class="kt-nav__item">
                                        <a href="#" class="kt-nav__link">
                                            <i class="kt-nav__link-icon flaticon2-telegram-logo"></i>
                                            <span class="kt-nav__link-text">Settings</span>
                                        </a>
                                    </li>
                                    <li class="kt-nav__item">
                                        <a href="#" class="kt-nav__link">
                                            <i class="kt-nav__link-icon flaticon2-new-email"></i>
                                            <span class="kt-nav__link-text">Support</span>
                                            <span class="kt-nav__link-badge">
                                                <span class="kt-badge kt-badge--success kt-badge--rounded">5</span>
                                            </span>
                                        </a>
                                    </li>
                                    <li class="kt-nav__separator"></li>
                                    <li class="kt-nav__foot">
                                        <button type="button" class="btn btn-label-danger btn-bold btn-sm">Upgrade plan</button>
                                        <button type="button" class="btn btn-clean btn-bold btn-sm" data-toggle="kt-tooltip" data-placement="right" title="Click to learn more...">Learn more</button>
                                    </li>
                                </ul>

                                <!--end::Nav-->
                            </div>
                        </div>
                    </div>
                    <?php include_once "profile_nav.php"; ?>
                        <!--end::Widget -->
                    </div>
                </div>


            </div>

            <!--End:: App Aside-->

            <!--Begin:: App Content-->
            <div class="kt-grid__item kt-grid__item--fluid kt-app__content">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="kt-portlet">
                            <div class="kt-portlet__head">
                                <div class="kt-portlet__head-label">
                                    <h3 class="kt-portlet__head-title">Personal Information <small>update your personal informaiton</small></h3>
                                </div>

                            </div>
                            <form class="kt-form kt-form--label-right" action="" method="post" enctype="multipart/form-data">
                                <div class="kt-portlet__body">
                                    <div class="kt-section kt-section--first">
                                        <div class="kt-section__body">
                                            <div class="row">
                                                <span class="col-xl-3"></span>
                                                <div class="col-lg-9 col-xl-6">
                                                    <h3 class="kt-section__title kt-section__title-sm">Customer Info:</h3>
                                                </div>
                                            </div>


                                            <div class="form-group row">
                                                <span class="col-xl-3 col-lg-3 col-form-label">Profile Image</span>
                                                <div class="col-lg-9 col-xl-6">
                                                    <div class="kt-avatar kt-avatar--outline" id="kt_user_avatar">
                                                        <div class="kt-avatar__holder" style="background-image: url(<?php echo $image;?>)"></div>
                                                        <label class="kt-avatar__upload" data-toggle="kt-tooltip" title="" data-original-title="Change avatar">
                                                            <i class="fa fa-pen"></i>
                                                            <input aria-label="Image" type="file" name="image" >
                                                            <input type="hidden" name="hid_image" value="<?php echo $user['user_photo'];?>">
                                                        </label>
                                                        <span class="kt-avatar__cancel" data-toggle="kt-tooltip" title="" data-original-title="Cancel avatar">
                                                            <i class="fa fa-times"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            -
                                            <div class="form-group row">
                                                <span class="col-xl-3 col-lg-3 col-form-label"> Name</span>
                                                <div class="col-lg-9 col-xl-6">
                                                    <input aria-label="Name" class="form-control" type="text" name="name" value=<?php echo $user['name'];?> >
                                                </div>
                                            </div>


                                            <div class="row">
                                                <span class="col-xl-3"></span>
                                                <div class="col-lg-9 col-xl-6">
                                                    <h3 class="kt-section__title kt-section__title-sm">Contact Info:</h3>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <span class="col-xl-3 col-lg-3 col-form-label">Contact Phone</span>
                                                <div class="col-lg-9 col-xl-6">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend"><span class="input-group-text"><i class="la la-phone"></i></span></div>
                                                        <input aria-label="Phone" type="text" class="form-control"  placeholder="Phone" aria-describedby="basic-addon1" name="phone" value= <?php echo $user['phone'];?>>
                                                    </div>
                                                    <span class="form-text text-muted">We'll never share your email with anyone else.</span>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <span class="col-xl-3 col-lg-3 col-form-label">Email Address</span>
                                                <div class="col-lg-9 col-xl-6">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend"><span class="input-group-text"><i class="la la-at"></i></span></div>
                                                        <input aria-label="Email" type="text" class="form-control" name="email" value="<?php echo $user['email'];?> "
                                                        placeholder="Email" aria-describedby="basic-addon1">
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
                                                <input type="hidden" name="hdn_user_id" value=<?php echo $_SESSION['userid']; ?> />
                                                <input type="hidden" name="submit" value="update" />
                                                <button id="kt_llogin_signup_submit" class="btn btn-success">Submit</button>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!--End:: App Content-->
        </div>

        <!--End::App-->
    </div>

    <!-- end:: Content -->
</div>
</div>

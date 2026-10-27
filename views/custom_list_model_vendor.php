<div class="d-flex flex-column-fluid">
    <!--begin::Container-->
    <div class="container fluid">
        <!--begin::Profile Overview-->
        <div class="d-flex flex-row">
            <!--begin::Aside-->
            <div class="flex-row-auto offcanvas-mobile w-300px w-xl-350px" id="kt_profile_aside">
                <!--begin::Profile Card-->
                <div class="card card-custom card-stretch">
                    <!--begin::Body-->
                    <div class="card-body pt-4">
                                <!--End:: App Aside Mobile Toggle-->

                                <!--Begin:: App Aside-->
                                   <?php include_once 'customer_vendor_model_side.php'; ?>
                                <!--End:: App Aside-->
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Profile Card-->
            </div>
        <!--Begin:: App Content-->
        <div class="flex-row-fluid ml-lg-8">
                <!--begin::Row-->
                <div class="row">
                <?php   if($_GET['type'] == 'corporate_info'){
                            include_once 'custom_vendor_corporate_info.php';
                        }
                        elseif($_GET['type'] == 'account_info'){
                            include_once 'custom_vendor_account_info.php';
                        }
                        elseif($_GET['type'] == 'contact_info'){
                            include_once 'custom_vendor_contact_info.php';
                        }
                        elseif($_GET['type'] == 'bank_info'){
                            include_once 'custom_vendor_bank_info.php';
                        }
                        elseif($_GET['type'] == 'tax_info'){
                            include_once 'custom_vendor_tax_info.php';
                        }
                        elseif($_GET['type'] == 'change_password'){
                            include_once 'custom_vendor_change_password.php';
                        }
                        else{
                            include_once 'custom_vender_overview.php';
                        }
                ?>
            </div>
        </div>

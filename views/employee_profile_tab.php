<!--begin::Aside-->
        <div class="flex-row-auto offcanvas-mobile w-300px w-xl-350px col-lg-2 col-md-2" id="kt_profile_aside">
            <!--begin::Card-->
            <div class="card card-custom">
                <!--begin::Body-->
                <div class="card-body pt-5 label-mrgn">
                    <!--begin::User-->
                    <div class="text-center mb-5">
                        <div class="symbol symbol-60 symbol-circle symbol-xl-90">
                            <div class="symbol-label" style="background-image:url('uploads/all/<?php echo $data['image']; ?>')"></div>
                            <i class="symbol-badge symbol-badge-bottom bg-success"></i>
                        </div>
                        <h4 class="font-weight-bold my-2"><?php echo ucwords( $data['display_name']); ?></h4>
                        <div class="text-muted mb-2"><?php echo $data['emp_code']; ?></div>
                        <?php
                                foreach ($empdata['departments'] as  $depp) {
                                    if($depp['id'] == $data['department_id']){
                                        $name_department = $depp['department_name'];
                                        break;
                                    }
                                }
                                foreach ($empdata['designations'] as  $desi) {
                                    if($desi['id'] == $data['designation_id']){
                                        $name_designations = $desi['designation_name'];
                                        break;
                                    }
                                }
                        ?>
                        <div class="text-muted mb-2"><?php echo $name_department; ?></div>

                        <div class="text-muted mb-2"><?php echo $name_designations; ?></div>

                        <div class="text-muted mb-2"><?php echo $data['phone']; ?></div>
                        <div class="text-muted mb-2"><?php echo $data['email_id']; ?></div>
                    </div>
                    <!--end::User-->
                    <!--begin::Contact-->

                    <div class="mb-5 text-center">
                        <a href="#" class="btn btn-icon btn-circle btn-light-facebook mr-2">
                            <i class="socicon-facebook"></i>
                        </a>
                        <a href="#" class="btn btn-icon btn-circle btn-light-twitter mr-2">
                            <i class="socicon-twitter"></i>
                        </a>
                        <a href="#" class="btn btn-icon btn-circle btn-light-google">
                            <i class="socicon-google"></i>
                        </a>
                    </div>
                    <!--end::Contact-->
                    <!--begin::Nav-->
                    <a href="employee-profile.php" class="btn btn-hover-light-primary font-weight-bold py-3 px-6 mb-2 text-center btn-block active">Overview</a>
                    <a href="#" class="btn btn-hover-light-primary font-weight-bold py-3 px-6 mb-2 text-center btn-block">Self Service</a>
                    <a href="basic-info.php" class="btn btn-hover-light-primary font-weight-bold py-3 px-6 mb-2 text-center btn-block">Basic Info</a>

                    <!--end::Nav-->
                </div>
                <!--end::Body-->
            </div>
            <!--end::Card-->
        </div>
        <!--end::Aside-->

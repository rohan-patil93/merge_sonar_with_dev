<?php

$company_id = $_SESSION['company_id'];
if (isset($_GET['PG'])) {
    $pageno = $_GET['PG'];
} else {
    $pageno = 1;
}

$dept_selected ="";
if(isset($_GET['FIND']) && $_GET['FIND'] !='') {
$search_value = $_GET['FIND'];
$search_value = urldecode(json_decode($search_value));
$selected_depts_array = explode("|dept:", $search_value);
$dept_selected = implode(",", array_filter((array)$selected_depts_array));
}

$offset = ($pageno-1) * NO_OF_RECORDS_PER_PAGE;
$vurl = API."?mode=togetemployeeslist&company_id=".$company_id."&is_active=0&offset_limit=".$offset."&no_of_records_per_page=".NO_OF_RECORDS_PER_PAGE."&selected_depts=".$dept_selected;
        $configesponse = callAPI($vurl);
        $emp_data_list = json_decode($configesponse,1);
        echo "<pre>"; print_r($emp_data_list);exit;

        $emp_count = $emp_data_list['total_employees'];
        $emp_list = $emp_data_list['employee_details'];
        $all_departments = $emp_data_list['department_list'];
        $number_of_pages =  ceil($emp_count / NO_OF_RECORDS_PER_PAGE);

        ?>

        <script type="text/javascript">
            function selectsidectegorylist(category_type, selected_item) {
                var pass_data = category_type+':'+selected_item;
                var selected_item_list = $('#search_value').val();
                if(selected_item_list.includes(pass_data)){
                    var searchfor = selected_item_list.replace("|"+pass_data,'');
                } else {
                    var searchfor = selected_item_list + "|" +pass_data;
                }
                searchfor = encodeURIComponent(JSON.stringify(searchfor))

            window.location.href = 'index.php?option=custom_list_new_employees&FIND='+searchfor;

  }


</script>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid" >
        <div class="row">
            <div class="col-lg-2">
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <!--begin::Subheader-->
                    <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
                        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                            <!--begin::Details-->
                            <div class="d-flex align-items-center flex-wrap mr-2">
                                <!--begin::Title-->
                                <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">Users</h5>

                            </div>
                        </div>
                    </div>

                    <div >
                        <div class="content d-flex flex-column flex-column-fluid" id="kt_content_2">
                            <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader_2">
                                <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                                    <div class="">
                                        <div >

                                            <input type="hidden" name="search_value" id="search_value" value="<?php echo $search_value; ?>">

                                            <h5>Departments</h5>
                                        </div>
                                        <?php foreach ($all_departments as $dept) { ?>

                                            <div class="form-check form-check-inline mr-1">
                                                <input class="form-check-input" id="<?php echo $dept['department_id']; ?>" type="checkbox" <?php echo (in_array($dept['department_id'], $selected_depts_array)) ?  'checked' : ''; ?> value="<?php echo $dept['department_id']; ?>" onclick="selectsidectegorylist('dept',<?php echo $dept['department_id']; ?>);" onkeydown="">
                                                <label class="form-check-label" for="<?php echo $dept['department_id']; ?>"><?php echo $dept['department_name']; ?></label>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Start Accordion -->
                    <div class="accordion accordion-light  accordion-toggle-arrow" id="accordionExample5">
 <div class="card">
  <div class="card-header" id="headingOne5">
   <div class="card-title" data-toggle="collapse" data-target="#collapseOne5">
    <i class="flaticon-pie-chart-1"></i> Departments
   </div>
  </div>
  <div id="collapseOne5" class="collapse show" data-parent="#accordionExample5">
   <div class="card-body">
    <?php foreach ($all_departments as $dept) { ?>

                                            <div class="form-check form-check-inline mr-1">
                                                <input class="form-check-input" id="<?php echo $dept['department_id']; ?>" type="checkbox" <?php echo (in_array($dept['department_id'], $selected_depts_array)) ?  'checked' : ''; ?> value="<?php echo $dept['department_id']; ?>" onclick="selectsidectegorylist('dept',<?php echo $dept['department_id']; ?>);" onkeydown="">
                                                <label class="form-check-label" for="<?php echo $dept['department_id']; ?>"><?php echo $dept['department_name']; ?></label>
                                            </div>
                                        <?php } ?>
   </div>
  </div>
 </div>
 <div class="card">
  <div class="card-header" id="headingTwo5">
   <div class="card-title collapsed" data-toggle="collapse" data-target="#collapseTwo5">
    <i class="flaticon2-notification"></i> Order Statistics
   </div>
  </div>
  <div id="collapseTwo5" class="collapse" data-parent="#accordionExample5">
   <div class="card-body">
    ...
   </div>
  </div>
 </div>
 <div class="card">
  <div class="card-header" id="headingThree5">
   <div class="card-title collapsed" data-toggle="collapse" data-target="#collapseThree5">
    <i class="flaticon2-chart"></i> eCommerce Reports
   </div>
  </div>
  <div id="collapseThree5" class="collapse" data-parent="#accordionExample5">
   <div class="card-body">
    ...
   </div>
  </div>
 </div>
</div>
                    <!-- End Accordion -->

                </div>
            </div>
            <div class="col-lg-10">
                <div class="row">
                    <?php
                    if(!empty($emp_list)) {
                        foreach ($emp_list as $data) { ?>

                            <!--begin::Col-->
                            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6">
                                <!--begin::Card-->
                                <div class="card card-custom gutter-b card-stretch">
                                    <!--begin::Body-->
                                    <div class="card-body pt-4">
                                        <!--begin::Toolbar-->
                                        <div class="d-flex justify-content-end">
                                            <div class="dropdown dropdown-inline" data-toggle="tooltip" title="Quick actions" data-placement="left">
                                                <a href="#" class="btn btn-clean btn-hover-light-primary btn-sm btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                                    <i class="ki ki-bold-more-hor"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-md dropdown-menu-right">
                                                    <!--begin::Navigation-->
                                                    <ul class="navi navi-hover">
                                                        <li class="navi-header font-weight-bold py-4">
                                                            <span class="font-size-lg">Choose Label:</span>
                                                            <i class="flaticon2-information icon-md text-muted" data-toggle="tooltip" data-placement="right" title="Click to learn more..."></i>
                                                        </li>
                                                        <li class="navi-separator mb-3 opacity-70"></li>
                                                        <li class="navi-item">
                                                            <a href="#" class="navi-link">
                                                                <span class="navi-text">
                                                                    <span class="label label-xl label-inline label-light-success">Insert</span>
                                                                </span>
                                                            </a>
                                                        </li>
                                                        <li class="navi-item">
                                                            <a href="#" class="navi-link">
                                                                <span class="navi-text">
                                                                    <span class="label label-xl label-inline label-light-danger">Update</span>
                                                                </span>
                                                            </a>
                                                        </li>

                                                    </ul>
                                                    <!--end::Navigation-->
                                                </div>
                                            </div>
                                        </div>
                                        <!--end::Toolbar-->
                                        <!--begin::User-->
                                        <div class="d-flex align-items-end mb-7">
                                            <!--begin::Pic-->
                                            <div class="d-flex align-items-center">
                                                <!--begin::Pic-->
                                                <div class="flex-shrink-0 mr-4 mt-lg-0 mt-3">
                                                    <?php if($data['image']){ ?>
                                                        <div class="symbol symbol-circle symbol-lg-75">
                                                            <img src="uploads/all/<?php echo $data['image']; ?>" alt="Details">
                                                        </div>
                                                    <?php }else{ ?>
                                                        <div class="symbol symbol-lg-75 symbol-circle symbol-primary">
                                                            <span class="symbol-label font-size-h3 font-weight-boldest"><?php echo initials($data['display_name']); ?></span>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                                <!--end::Pic-->
                                                <!--begin::Title-->
                                                <div class="d-flex flex-column">
                                                    <a href="#" class="text-dark font-weight-bold text-hover-primary font-size-h4 mb-0"><?php echo $data['display_name']; ?></a>
                                                    <span class="text-muted font-weight-bold"><?php echo $data['designation_name']; ?></span>
                                                </div>
                                                <!--end::Title-->
                                            </div>
                                            <!--end::Title-->
                                        </div>
                                        <!--end::User-->
                                        <!--begin::Desc-->
                                        <p class="mb-7"><span class="text-dark-75 font-weight-bolder mr-2">Contact:</span>
                                            <a href="#" class="text-primary pr-1"><?php echo $data['email_id']; ?></a>
                                            <br>
                                            <?php echo $data['address'];?></p>

                                            <!--end::Desc-->
                                            <!--begin::Info-->
                                            <div class="mb-7">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="text-dark-75 font-weight-bolder mr-2">DOJ:</span>
                                                    <a href="#" class="text-muted text-hover-primary"><?php echo $data['date_of_joining']; ?></a>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-cente my-1">
                                                    <span class="text-dark-75 font-weight-bolder mr-2">Phone:</span>
                                                    <a href="#" class="text-muted text-hover-primary"><?php echo $data['phone_no']; ?></a>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="text-dark-75 font-weight-bolder mr-2">Location:</span>
                                                    <span class="text-muted font-weight-bold"><?php echo $data['location_city']; ?></span>
                                                </div>
                                            </div>
                                            <!--end::Info-->
                                            <button type="button" class="btn btn-block btn-sm btn-light-primary font-weight-bolder text-uppercase py-4">Go</button>
                                        </div>
                                        <!--end::Body-->
                                    </div>
                                    <!--end::Card-->
                                </div>

                                <?php
                            }
                        }?>
                        <!--end::Col-->
                    </div>
                    <!--end::Row-->
                    <!--begin::Pagination-->
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="d-flex flex-wrap mr-3">
                            <?php
                            if($number_of_pages!=0) {
                                ?>
                            <a href="#" class="btn btn-icon btn-sm btn-light-primary mr-2 my-1">
                                <i class="ki ki-bold-arrow-back icon-xs"></i>
                            </a>
                            <?php
                            for($i=1; $i<= $number_of_pages; $i++) {
                                $redirect = "index.php?option=".$_GET['option']."&PG=".$i; ?>
                                <a href="<?php echo $redirect; ?>" class="btn btn-icon btn-sm border-0 btn-hover-primary mr-2 my-1"><?php echo $i; ?></a>
                            <?php }
                             ?>
                            <a href="#" class="btn btn-icon btn-sm btn-light-primary mr-2 my-1">
                                <i class="ki ki-bold-arrow-next icon-xs"></i>
                            </a>
                        <?php } ?>
                        </div>

                    </div>
                    <!--end::Pagination-->
                </div>
                <!--end::Container-->
            </div>

        </div>
                    <!--end::Content-->

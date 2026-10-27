<?php
$url = API."?mode=togetdcumentdashboarddetails";
$data_details = callAPI($url);
$details = json_decode($data_details,1);
$main =  $details['assign_data'];
$data = $main['data'];
$emp_tax_deposit_respect = $details['emp_tax_deposit_respect'];
$_SESSION['apply_js']['table']= 1;
$popup = 'Y';
include_once "new_from_submit.php";
?>
<script type="text/javascript">
    function dynamicinsertpopupdash(option){
        alert("insert");
        jQuery.ajax({
            url: "views/new_from.php",
            data:'option='+option,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal("show");
            },
            error:function (){}
        });
    }
</script>

<div class=" p-20">
    <div class="row">
        <div class="col-lg-12">
    <ul class="nav md-tabs" id="myTab1">
        <li class="nav-item">
            <a class="nav-link   active" data-bs-toggle="tab" href="#dashboard" role="tab"  onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
               Dash Board
            </a>
        </li>
        <li class="nav-item ">
            <a class="nav-link" data-bs-toggle="tab" href="#mydocuments" role="tab"  onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                My Documents
            </a>
        </li>
    </ul>
    </div>
</div>
<div class="row ">
    <div class="tab-content mt-5" id="myTabContent1">
        <div class="tab-pane  active " aria-labelledby="dashboard" id="dashboard" role="tabpanel">    <div class="row">
                <div class="col-lg-3">
                    <div class="card mb-2" >
                        <div class="card-header">
                            <h5>Dcoumment categories <span class="label label-md label-info" style=""> <?php echo count((array)$details['categories']); ?> </span> </h5>
                            <div class="card-header-right">
                                <button type="button" onclick="dynamicinsertpopupdash('COM_INSERT_DOCUMENT_CATEGORIES')" class="label label-md label-primary border-0" style="padding:  5px; cursor: pointer;">
                                     <i class="ti ti-plus text-white"></i>
                                </button>

                            </div>
                        </div>
                        <div class="card-body custom-scroll" style="max-height: 265px;min-height:265px;">
                            <?php
                            if(count((array)$details['categories'])>0){
                                foreach((array)$details['categories'] as $rcent){
                                    $catnn[$rcent['id']] = $rcent['category_name']; ?>
                                    <div   class="row mt-3 pb-3 " style="border-bottom:1px dashed #cccccc;">
                                        <div class="col-auto">
                                            <div class="p-2 bg-primary b-radius-10 text-center left-icon-width" style="background-color: <?php echo $_SESSION['status'][$rcent['current_status']]['background_color']; ?>">
                                                <i class="fa fa-trophy f-w-600"></i>
                                            </div>
                                        </div>
                                        <div class="col p-0">
                                            <div class="f-w-600 f-14 close m-0" onclick="location.href='<?php echo $vieopt; ?>';"  role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                                <?php echo $rcent['category_name']; ?>
                                            </div>
                                            <span class="text-muted f-12">
                                                <?php echo date("d-m-Y", strtotime($rcent['created_date'])); ?>
                                            </span>
                                        </div>
                                    </div>
                            <?php }
                            } else{ ?>
                                <h6 class="text-center mt-3"> No Data</h6>
                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card mb-2" >
                        <div class="card-header">
                            <h5 >Document Lists</h5>
                            <div class="card-header-right">
                                <button type="button" onclick="dynamicinsertpopupdash('COM_INSERT_DOCUMENT_LISTS')" class="label label-md label-primary border-0" style="padding:  5px; cursor: pointer;">
                                     <i class="ti ti-plus text-white"></i>
                                </button>

                            </div>
                        </div>
                        <div class="card-body">
                            <div id="allocatereturn">
                                <div class="p-2" >
                                    <ul class="nav md-tabs infisuite_tab">
                                        <?php $i=1; foreach($details['lists'] as $catkey=>$catlist){ ?>
                                            <li class="nav-item">
                                                <a class="nav-link <?php if($i==1){ echo "active";
} ?>" data-bs-toggle="tab" href="#check<?php echo $i; ?>" role="tab" aria-selected="true"><?php echo $catnn[$catkey]; ?></a>
                                            </li>
                                        <?php $i++;
} ?>
                                    </ul>
                                </div>
                                <div class="tab-content tabs f-13">
                                    <?php $i=1;
                                    if(count((array)$details['lists'])>0){
                                    foreach((array)$details['lists'] as $list){  ?>
                                         <div class="tab-pane <?php if($i==1){ echo "active";
} ?>" id="check<?php echo $i; ?>" role="tabpanel">
                                            <div class="table-responsive custom-scroll" style="max-height:185px">
                                                <table class="table table-hover table-sm">
                                                    <tr>
                                                        <th scope="col">#</th>
                                                        <th scope="col">Document Name</th>
                                                        <th scope="col">Create Date</th>
                                                        <th scope="col"></th>
                                                    </tr>
                                                    <?php $m=1;
                                                    if(count((array)$list)>0){
                                                        foreach((array)$list as $datalist){
                                                    ?>
                                                        <tr>

                                                            <td>
                                                                <?php echo $m; ?>
                                                            </td>
                                                            <td>
                                                                <span> <?php echo $datalist['document_name']; ?> </span>
                                                            </td>
                                                            <td>
                                                                <?php echo fromDate($datalist['created_date']); ?>
                                                            </td>
                                                            <td>

                                                            </td>
                                                        </tr>
                                                    <?php $m++;
                                                        }
                                                    } else{ ?>
                                                        <tr>
                                                            <td colspan="4" class="text-center">
                                                                <h6 class="text-center mt-3"> No Data</h6>
                                                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                                            </td>
                                                        </tr>
                                                    <?php } ?>


                                                </table>
                                            </div>
                                        </div>
                                    <?php $i++;
}
                                    }else{ ?>
                                        <h6 class="text-center mt-3"> No Data</h6>
                                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card mb-2" style="min-height:354px;">
                        <div class="card-header">
                            <h5>Document summary</h5>
                        </div>

                        <div class="card-body custom-scrollw c-row">
                            <?php $k = 0;
                                $ccolor = array('#00bcd4', '#11c15b', '#ffe100', '#FE8A7D');
                                foreach($data as $row){ if($k<4){?>
                                <div class="card prod-p-card mt-1 m-l-5 m-r-5 mb-2" style="background-color:<?php  echo $ccolor[$k]; ?>;">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <h6 class="m-b-5 text-white">
                                                    <?php echo $main['listdata'][$row['document_list_id']]." - ".$row['assessment_year'] ; ?>
                                                 <span class="f-w-600 float-right p-0 f-18"> <?php echo $row['cc']; ?></span></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php $k++; ?>
                            <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card mb-2" >
                        <div class="card-header">
                            <h5 >Document Lists</h5>
                            <div class="card-header-right">

                                <a target="_blank" href="index.php?option=COM_INSERT_EMP_ASSIGN_RELEASE_DOCUMENTS" title="Insert Details" class="label label-md label-primary" style="padding:  5px;">
                                     <i class="ti ti-plus text-white"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-body custom-scroll">
                            <table id="basic-btn-non" class="table table-striped table-bordered nowrap">
                                <thead>
                                    <tr>
                                      <th scope="col"> # </th>
                                      <th scope="col"> Assessment Year </th>
                                      <th scope="col"> Document Type </th>
                                      <th scope="col"> Number of Employees </th>
                                      <th scope="col"> Created Date  </th>
                                      <th scope="col"> Action  </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i=1; foreach($data as $row){ ?>
                                    <tr>
                                      <td><?php echo $i; ?></td>
                                      <td><?php echo $row['assessment_year']; ?></td>
                                      <td><?php echo $main['listdata'][$row['document_list_id']]; ?></td>
                                      <td><?php echo $row['cc']; ?></td>
                                      <td><?php echo fromDate($row['created_date']); ?></td>
                                      <td>
                                        <a target="_blank" href="index.php?option=COM_INSERT_EMP_ASSIGN_RELEASE_DOCUMENTS&amp;assessment_year=<?php echo $row['assessment_year']; ?>&amp;list=<?php echo $row['document_list_id']; ?>" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="Edit Details"><i class="fa fa-edit"></i></a>
                                        <a target="_blank" href="index.php?option=COM_VIEW_EMP_ASSIGN_RELEASE_DOCUMENTS&amp;assessment_year=<?php echo $row['assessment_year']; ?>&amp;list=<?php echo $row['document_list_id']; ?>" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="View Details"><i class="fa fa-eye"></i></a>
                                        </td>
                                    </tr>
                                    <?php $i++;
} ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card mb-2" >
                        <div class="card-header">
                            <h5 >Employee Tax Deposit Respect</h5>
                            <div class="card-header-right">

                                  <a target="_blank" href="index.php?option=COM_INSERT_EMP_TAX_DEPOSIT_RESPECT" title="Insert Details" class="label label-md label-primary" style="padding:  5px;">
                                     <i class="ti ti-plus text-white"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-body custom-scroll"  style="height:800px;">
                            <table id="basic-btn-non_2" class="table table-striped table-bordered nowrap ">
                                <thead>
                                    <tr>
                                      <th scope="col"> # </th>
                                      <th scope="col"> Employee </th>
                                      <th scope="col"> Assessment Year </th>
                                      <th scope="col"> Certificate No </th>
                                      <th scope="col"> Certificate  Date  </th>
                                      <th scope="col">  </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i=1; foreach($emp_tax_deposit_respect as $row){ ?>
                                    <tr>
                                      <td><?php echo $i; ?></td>
                                      <td><?php echo $row['display_name']; ?></td>
                                      <td><?php echo $row['assessment_year']; ?></td>
                                      <td><?php echo $row['certificate_no']; ?></td>
                                      <td><?php echo fromDate($row['certificate_date']); ?></td>
                                      <td>
                                        <a target="_blank" href="index.php?option=COM_INSERT_EMP_ASSIGN_RELEASE_DOCUMENTS&amp;assessment_year=<?php echo $row['assessment_year']; ?>&amp;list=<?php echo $row['document_list_id']; ?>" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="Edit Details"><i class="fa fa-edit"></i></a>
                                        <a target="_blank" href="index.php?option=COM_VIEW_EMP_ASSIGN_RELEASE_DOCUMENTS&amp;assessment_year=<?php echo $row['assessment_year']; ?>&amp;list=<?php echo $row['document_list_id']; ?>" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="View Details"><i class="fa fa-eye"></i></a>
                                        </td>
                                    </tr>
                                    <?php $i++;
} ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane  active " aria-labelledby="mydocuments" id="mydocuments" role="tabpanel">
            <?php include_once 'custom_view_documents.php'; ?>
        </div>
    </div>
</div>

</div>

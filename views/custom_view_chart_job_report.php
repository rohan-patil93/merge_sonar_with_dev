    <div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <form action="" method="post">

        <div class="card card-custom card-sticky" id="kt_page_sticky_card">
            <div class="card-header">
                <div class="card-title">
                    <h3 class="card-label col-lg-3">&nbsp;<?php  echo "Job Report";  ?> </h3>

From
<div class="col-lg-3">
    <input aria-label="Select date" class="form-control form_date" type="date" value="<?php echo isset($_POST['from'])?$_POST['from']:date('Y')."-01-01"; ?>" name="from" id="example-date-input-from">
</div>
To
<div class="col-lg-3">
    <input aria-label="Select date" class="form-control to_date" type="date" value="<?php echo isset($_POST['to'])?$_POST['to']:date('Y-m-d'); ?>" name="to" id="example-date-input-to">
</div>
<div class="col-lg-2">
    <button type="submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"  name="applyform" value="apply">Apply</button>
</div>

                </div>
                <div class="card-toolbar">

                <a href="javascript:history.go(-1)" class="btn btn-light-primary font-weight-bolder mr-2">
                    <i class="ki ki-long-arrow-back icon-xs"></i>Back
                </a>
                </div>
            </div>
    <div class="card-body">
        <div class="card card-custom gutter-b">
            <div class="card-body">
                <!--begin::Chart-->
                <div id="chart_3"></div>
                <!--end::Chart-->
            </div>
        </div>
    </div>

</div>
</form>
</div>
</div>

<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="card card-custom card-sticky" id="kt_page_sticky_card_2">

    <div class="card-body">
        <div class="card card-custom gutter-b">
            <div class="card-body">

                                        <div class="mb-7">
                                            <div class="row align-items-center">
                                                <div class="col-lg-9 col-xl-8">
                                                    <div class="row align-items-center">
                                                        <div class="col-md-4 my-2 my-md-0">
                                                            <div class="input-icon">
                                                                <input aria-label="Search..." type="text" class="form-control" placeholder="Search..." id="kt_datatable_search_query" />
                                                                <span>
                                                                    <i class="flaticon2-search-1 text-muted"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
    <table class="table table-separate table-head-custom table-checkable" id="kt_datatable1">
                                            <thead>
                                                <tr>
                                                    <th scope="col" title="Field #1">#</th>
                                                    <th scope="col" title="Field #2">Job Number</th>
                                                    <th scope="col" title="Field #3">Job Name</th>
                                                    <th scope="col" title="Field #4">Job description</th>
                                                    <th scope="col" title="Field #5">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
            <?php

        if(isset($_POST['from'])){
            $url = API."?mode=getlistchartjobreport&from=".$_POST['from']."&to=".$_POST['to'];
        }else{
            $url = API."?mode=getlistchartjobreport&from=".date('Y')."-01-01&to=".date('Y-m-d');
        }
        $collfunction = callAPI($url);
        $value = json_decode($collfunction,1);
        $i=1;
        foreach($value as $row){
        ?>
            <tr>
                <td><?php echo $i; ?></td>
                <td><?php echo $row['job_number']; ?></td>
                <td><?php echo $row['job_name']; ?></td>
                <td><?php echo $row['job_desc']; ?></td>
                <td><?php echo $row['name']; ?></td>
            </tr>
<?php $i++;
} ?>
        </tbody>
    </table>
                                        <!--end: Datatable-->
</div>
        </div>
    </div>

</div>

</div>
</div>


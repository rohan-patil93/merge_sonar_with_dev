    <div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <form action="" method="post">

        <div class="card card-custom card-sticky" id="kt_page_sticky_card">
            <div class="card-header">
                <div class="card-title">
                    <h3 class="card-label col-lg-3">&nbsp;<?php  echo __("Job Report");  ?> </h3>

<?= __('From'); ?>
<div class="col-lg-3">
    <input aria-label="<?= htmlspecialchars(__('Select date'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control form_date" type="date" value="<?php echo isset($_POST['from'])?$_POST['from']:date('Y')."-01-01"; ?>" name="from" id="example-date-input-from">
</div>
<?= __('To'); ?>
<div class="col-lg-3">
    <input aria-label="<?= htmlspecialchars(__('Select date'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control to_date" type="date" value="<?php echo isset($_POST['to'])?$_POST['to']:date('Y-m-d'); ?>" name="to" id="example-date-input-to">
</div>
<div class="col-lg-2">
    <button type="submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"  name="applyform" value="apply"><?= __('Apply'); ?></button>
</div>

                </div>
                <div class="card-toolbar">

                <a href="javascript:history.go(-1)" class="btn btn-light-primary font-weight-bolder mr-2">
                    <i class="ki ki-long-arrow-back icon-xs"></i><?= __('Back'); ?>
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
                                                                <input aria-label="<?= htmlspecialchars(__('Search...'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" placeholder="<?= htmlspecialchars(__('Search...'), ENT_QUOTES, 'UTF-8'); ?>" id="kt_datatable_search_query" />
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
                                                    <th scope="col" title="<?= htmlspecialchars(__('Field #1'), ENT_QUOTES, 'UTF-8'); ?>">#</th>
                                                    <th scope="col" title="<?= htmlspecialchars(__('Field #2'), ENT_QUOTES, 'UTF-8'); ?>"><?= __('Job Number'); ?></th>
                                                    <th scope="col" title="<?= htmlspecialchars(__('Field #3'), ENT_QUOTES, 'UTF-8'); ?>"><?= __('Job Name'); ?></th>
                                                    <th scope="col" title="<?= htmlspecialchars(__('Field #4'), ENT_QUOTES, 'UTF-8'); ?>"><?= __('Job description'); ?></th>
                                                    <th scope="col" title="<?= htmlspecialchars(__('Field #5'), ENT_QUOTES, 'UTF-8'); ?>"><?= __('Status'); ?></th>
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


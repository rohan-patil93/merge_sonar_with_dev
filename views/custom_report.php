<?php
if (isset($_POST['submit'])){
    $data = urlencode(json_encode($_POST));
    $tableurl = API."?mode=togetreportdata&data=".$data;
    $respons = callAPI($tableurl);
    $lent = json_decode($respons,1);
    $sear = $_POST;
}
$tableurl = API."?mode=singlerowtabledata&table=".TBL_REPORT_ATTRIBUTES."&rid=".$_GET['Rid']."&fid=report_code";
$response = callAPI($tableurl);
$data = json_decode($response,1);
$content = $data['help_text']!='' ? $data['help_text'] : "Help Test";
$report_id =$data['id'];
$attributes = json_decode($data['attributes'],1);
foreach ($attributes as $key => $att) {
    if($att['filter'] == 1){
        $filter['lable'] = $att['lable'];
        $filter['filter_type'] = $att['filter_type'];
        $filter['field'] =  $key;
        $filters[]=$filter;
    }
    if($att['display_list'] == 1){
        $dis['lable'] = $att['lable'];
        $dis['filter_type'] = $att['filter_type'];
        $dis['field'] =  $key;
        $diss[$att['display_postion']]=$dis;
    }
}

?>
        <div creport_codelass="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
            <?php if($content !=''){ ?>
            <div class="alert alert-light alert-elevate" role="alert">
                <div class="alert-icon"><i class="flaticon-warning kt-font-brand"></i></div>
                <div class="alert-text">
                            <?php echo $content; ?>
                </div>
            </div>
        <?php } ?>

                    <div class="kt-portlet kt-portlet--mobile">
                        <div class="kt-portlet__head kt-portlet__head--lg">
                            <div class="kt-portlet__head-label">
                                <span class="kt-portlet__head-icon">
                                    <i class="kt-font-brand flaticon-list-2"></i>
                                </span>
                                <h3 class="kt-portlet__head-title">
                                    <?php echo "Report";  ?>
                                </h3>

                            </div>
                            <div class="kt-portlet__head-toolbar">
                                <div class="kt-portlet__head-wrapper">
                                    <div class="kt-portlet__head-actions">
                                        <div class="dropdown dropdown-inline">
                                            <a href="javascript:history.go(-1)" class="btn btn-clean kt-margin-r-10">
                                                    <i class="la la-arrow-left"></i>
                                                    <span class="kt-hidden-mobile">Back</span>
                                                </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="kt-portlet__body">

<form class="kt-form kt-form--fit kt-margin-b-20" action="" method="post">
    <div class="row kt-margin-b-20">
    <?php $dateid =1;
        foreach ($filters as $key => $filt) {
         $ser_field = str_replace('.', '_', $filt['field'] ); ?>
        <?php if($filt['filter_type'] == 'text'){ ?>
        <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile">
            <span><?php echo $filt['lable']; ?>:</span>

            <input aria-label="Input field" type="text" class="form-control kt-input" value="<?php echo $sear[$ser_field]; ?>" name="<?php echo $filt['field']; ?>"  data-col-index="4">
        </div>
        <?php }elseif($filt['filter_type']=='select') {
            $selectdata = dyanamicselectreportdata($report_id,$filt['field']);
            $se = $sear[$ser_field];
            ?>
            <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile">
                <span><?php echo $filt['lable']; ?>:</span>
                <select aria-label="Select option" class="form-control kt-input" name="<?php echo $filt['field']; ?>" data-col-index="2">
                    <?php foreach($selectdata as $selectkey=>$select){ ?>
                    <option <?php if($se == $selectkey){ ?> selected <?php
} ?> value="<?php echo $selectkey; ?>"><?php echo $select; ?></option>
                <?php } ?>
                </select>
            </div>
        <?php }elseif($filt['filter_type']=='date_pick') {?>
            <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile">
            <span><?php echo $filt['lable']; ?>:</span>

            <input aria-label="Input field" type="text" class="form-control"  value="<?php echo $sear[$ser_field]; ?>"  name="<?php echo $filt['field']; ?>"  id="kt_datepicker_3<?php echo $dateid; ?>" />
        </div>
        <?php $dateid++;
} ?>
    <?php } ?>
    </div>
    <div class="kt-separator kt-separator--md kt-separator--dashed"></div>
    <div class="row">
        <div class="col-lg-12">
            <input type="hidden" name="report_id" value="<?php echo $report_id;?>">
            <input type="hidden" name="submit" value="search" />
            <button class="btn btn-primary btn-brand--icon" id="kt_search">
                <span>
                    <i class="la la-search"></i>
                    <span>Search</span>
                </span>
            </button>
            &nbsp;&nbsp;
            <button class="btn btn-secondary btn-secondary--icon" id="kt_reset">
                <span>
                    <i class="la la-close"></i>
                    <span>Reset</span>
                </span>
            </button>
        </div>
    </div>
</form>

                            <!--begin: Datatable -->
                            <table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">

                                <thead>
                                    <tr>
                                        <?php foreach ($diss as $header){ ?>
                                                <th scope="col"><?php echo $header['lable']; ?></th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>

                                        <?php
                                        foreach ($lent as $wor){?>
                                            <tr>
                                        <?php foreach ($diss as $header){
                                             $dis_field = str_replace('.', '_', $header['field']); ?>
                                                <td><?php echo $wor[$dis_field]; ?></td>
                                        <?php } ?>
                                    </tr>
                                    <?php } ?>

                                </tbody>
                            </table>

                            <!--end: Datatable -->
                        </div>
                    </div>
                </div>


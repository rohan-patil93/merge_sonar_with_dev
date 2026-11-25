<?php
if (!defined('PARAM_FILE_NAME')) {
    define('PARAM_FILE_NAME', '&file_name=');
}

$sumbit_val =0;
if(isset($_POST['submit_value'])){
    $url = API."?mode=toimportthedataintotables&table=".$_GET['table'].PARAM_FILE_NAME.$_POST['file_name'];
    $response = callAPI($url);

    $sumbit_val =1;
}
if($_GET['type'] == 'SAL'){
    $url = API."?mode=toimportthedataintotablesemployeessalary&table=".$_GET['table'].PARAM_FILE_NAME.$_GET['file_name'];
    $_GET['table'] = 'Salaries';
    $response = callAPI($url);
    $sumbit_val =1;
}
?>

<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="row workflow-step text-center mt-5 mb-5">
            <div class="col-lg-4">
                <div class="work-box bg-primary">
                    <i class="ti-settings"></i>
                </div>
                <div class="step-icon  bg-primary"></div>
                <div class="font-size-lg mt-1 "><?= __('Step 1') ?></div>

            </div>
            <div class="col-lg-4">
                <div class="work-box bg-primary">
                    <i class="ti-settings"></i>
                </div>
                <div class="step-icon bg-primary"></div>
                <div class="font-size-lg mt-1"><?= __('Step 2') ?></div>

            </div>
            <div class="col-lg-4">
                <div class="work-box bg-primary">
                    <i class="ti-settings"></i>
                </div>

                <div class="font-size-lg mt-1"><?= __('Step 3') ?></div>

            </div>

        </div>
        <div class="row">
            <?php if($sumbit_val == 0){ ?>
            <div class="col-lg-12">
                <form class="kt-form" id="kt_form" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">
                <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                    <div class="card-header table-card-header">
                      <h5><?= __('Data Implode') ?></h5>
                      <div class="card-header-right table-card-header ">
                            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                                  <i class="icofont icofont-reply"></i> <?= __('Back') ?>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="form-group col-lg-6">
                                <span><?= __('To Insert Data to Tables') ?></span>
                            <div>
                            <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2" name="submit_value" value="exit"><i class="la la-check"></i><?= __('Import') ?></button>
                        </div>
                            <input type="hidden" name="submit" value="Add">
                    </div>
                        <input type="hidden" name="table" value="<?php echo $_GET['table']; ?>">
                        <input type="hidden" name="file_name" value="<?php echo $_GET['file_name']; ?>">
                </div>
                </form>
            </div>
            <?php } ?>
            <?php if($sumbit_val == 1){
                $url = API."?mode=togetimplodesdetailsount&table=".$_GET['table'].PARAM_FILE_NAME.$_GET['file_name'];
                $data_details = callAPI($url);
                $data = json_decode($data_details,1);
            ?>
            <div class="card card-custom card-sticky" id="kt_page_sticky_card_2">
                <div class="card-body">
                <div class="row col-lg-12 p-5 f-14 f-w-600">
                    <div class="col-lg-7">
                        <div class="row col-lg-12">
                            <div class="col-lg-6">
                                <?= __('File Name') ?> :
                            </div>
                            <div class="p-2 col-lg-6">
                                <?php echo  $_GET['file_name']; ?>
                            </div>
                            <div class=" p-2 col-lg-6">
                                <?= __('Table Name') ?> :
                            </div>
                            <div class="p-2 col-lg-6">
                                <?php echo  $_GET['table']; ?>
                            </div>
                            <div class="p-2 col-lg-6">
                                <?= __('Total Records') ?> :
                            </div>
                            <div class="p-2 col-lg-6">
                                <?php echo  $data['cc']; ?>
                            </div>
                            <div class="p-2 col-lg-6">
                                <?= __('Uploaded Records') ?> :
                            </div>
                            <div class="p-2 col-lg-6">
                                <?php echo  $data['rc']; ?>
                            </div>
                            <div class="p-2 col-lg-6">
                                <?= __('Failed Records') ?> :
                            </div>
                            <div class="p-2 col-lg-6">
                                <?php echo  $data['cc']-$data['rc']; ?>
                            </div>
                        </div>
                    </div>
                    <?php if($_GET['table']=='employees' && !isset($_GET['type'])) {?>
                    <div class="p-2 col-lg-3">
                        <?= __('Do you want Import the salary') ?>
                        <a href="index.php?option=COM_INSERT_DYNAMIC_DATA_IMPORT&file_name=<?php echo $_GET['file_name'] ?>&table=<?php echo $_GET['table']; ?>&type=SAL"> <button class="button btn btn-primary"> <?= __('Import Salary') ?> </button></a>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
            <?php } ?>
        </div>
    </div>
</div>

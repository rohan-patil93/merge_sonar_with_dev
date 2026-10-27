<?php
$itemfieldlist = array('id', 'sku', 'item_name', '', '', 'opening_quantity');
if(isset($_POST['submit_value']) && $_POST['submit_value'] == "upload"){
    $_POST['recordupdate'] = "Y";
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
    $url = API."?mode=insertimportexceldata&method=post";
    $response1 = file_post_contents($url, $_POST, 'insert');
    $response = json_decode($response1,1);
    $_SESSION['insert_msg'] =  $_SESSION['error_message']['3001'];
    header("location:index.php?option=COM_INSERT_IMPORT_EXCEL_DATA"); exit;

}elseif(isset($_POST['submit_value']) && $_POST['submit_value'] == "add" && isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK){
    if(pathinfo($_FILES['csv_file']['name'], PATHINFO_EXTENSION) == 'csv'){

            $ext=explode(".",$_FILES["csv_file"]['name']);
            $file_name = $ext[0];
            $file_insert_name = 'attendance'.'_'.time().uniqid().'.'.$ext[1];
            $file_path='uploads/edi/'.$file_insert_name;
            $csvFile = $_FILES['csv_file']['tmp_name'];
               $postarray['csvFile'] = $csvFile;
               $postarray['file_name'] = $file_insert_name;
            $importdata = array_map('str_getcsv', file($csvFile));
            foreach($importdata as $idata){
                $postarray['csvdata'][] =  $idata;
            }
            $postarray['system_start_date'] = $_POST['system_start_date'];
            $postarray['company_id'] = $_SESSION['company_id'];
               $postarray['user_id'] = $_SESSION['userid'];
               $postarray['recordupdate'] = "N";
            $url = API."?mode=insertimportexceldata&method=post";
            $response1 = file_post_contents($url, $postarray, 'insert');
            $response = json_decode($response1,1);
            $_SESSION['insert_msg'] = $_SESSION['error_message']['9092'];
               header("location:index.php?option=COM_INSERT_IMPORT_EXCEL_DATA&FIN=".$file_insert_name); exit;
           }else{
               $error_messages =  "Please upload CSV file format";
           }
}elseif($_GET['FIN'] !=""){
        $inouturl = API."?mode=getimportexceldata&FIN=".$_GET['FIN'];
        $inoutcall = callAPI($inouturl);
        $responss = json_decode($inoutcall,1);
        $items = $responss['items'];
        $import_excel_data = $responss['import_excel_data'];
        $header  = json_decode($import_excel_data['header'],1);
        $data  = json_decode($import_excel_data['data'],1);
}else{
    $inouturl = API."?mode=getimportexceldata&LIST=Y";
    $inoutcall = callAPI($inouturl);
    $responss = json_decode($inoutcall,1);
    $items = $responss['items'];
    $import_excel_data22 = $responss['import_excel_data'];
    $error_messages =  "Please upload CSV file format";
}

?>
<?php if($error_messages !="" ){ ?>
     <div class="alert alert-danger alert-dismissible">
        <strong><?php echo $error_messages; ?> </strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php } ?>
<?php if($_SESSION['insert_msg'] !=""){
  ?>
   <script type="text/javascript">
        document.onreadystatechange = function(){
            if(document.readyState == "complete") {
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['insert_msg']; ?>','');
            }
        }
    </script>
<?php
$_SESSION['insert_msg'] ="";
} ?>
<script type="text/javascript">
    function btnCancel(){
        window.location.href="index.php?option=COM_INSERT_IMPORT_EXCEL_DATA";
    }
</script>
<?php if($_GET['FIN'] !=""){   ?>

<div class="card card-custom card-sticky " id="kt_page_sticky_card">
    <div class="card-header table-card-header">
        <h5><?php echo "Confirm Data";  ?></h5>
        <div class="card-header-right table-card-header ">
            <button class="btn waves-effect waves-light btn-danger btn-icon"></button> New Values
            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                <i class="icofont icofont-reply"></i>
                Back
            </a>
        </div>
    </div>
    <form method="post" enctype="multipart/form-data">
        <div class="card-body m-l-50 m-r-50">
            <input type="hidden" class="form-control" name="FIN" id="FIN" value="<?php echo $_GET['FIN']; ?>">
            <table>
                <thead>
                    <?php $id = 1; foreach($header as $hdr){ ?>
                        <th scope="col" class="p-2"><?php echo $hdr; ?></th>
                    <?php } ?>

                </thead>
                <tbody>
                    <?php $id = 1; foreach($data as $idata){  $itemsdetail = $items[$idata[0]]; ?>
                        <tr>
                            <?php foreach($idata as $seqkey=> $importda){


                                $error = "";
                                if($itemfieldlist[$seqkey] == ""){
                                }elseif($itemsdetail[$itemfieldlist[$seqkey]] == $importda){
                                }else{
                                    $error = "bg-danger";

                                }
                                ?>
                            <th scope="col" class="p-2 <?php echo $error; ?>"><?php echo $importda; ?></th>
                            <?php } ?>

                        </tr>
                    <?php $id++;
} ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-lg-6">
                    <button type="submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2 m-b-10" name="submit_value" value="upload"><i class="la la-check"></i> Confirm</button>
                    <button type="button" class="btn btn-danger btn-shadow-hover font-weight-bold mr-2 m-b-10" onclick="btnCancel()"><i class="la la-check"></i> Cancel</button>
                </div>
            </div>
        </div>
    </form>
</div>

<?php }else{  ?>
<div class="card card-custom card-sticky " id="kt_page_sticky_card_2">
    <div class="card-header table-card-header">
        <h5><?php echo "Import";  ?></h5>
        <div class="card-header-right table-card-header ">
            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                <i class="icofont icofont-reply"></i>
                Back
            </a>
        </div>
    </div>
    <form method="post" enctype="multipart/form-data">
        <div class="card-body m-l-50 m-r-50">
            <div class="row">
                <div class="form-group col-lg-4">
                    <span class="form-label">System Start Date <span class="text-danger">*</span></span>
                    <input aria-label="System Start Date" type="text" class="form-control" name="system_start_date" id="system_start_date" value="<?php echo $_SESSION['config']['system_start_date']; ?>" required="" readonly="">
                </div>
                <div class="form-group col-lg-4">
                    <span class="form-label">Import <span class="text-danger">*</span></span>
                    <input aria-label="Csv File" type="file" class="form-control" name="csv_file" id="csv_file" required="">
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-lg-6">
                    <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2 m-b-10" name="submit_value" value="add"><i class="la la-check"></i> Import</button>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="card card-custom card-sticky " id="kt_page_sticky_card_3">
    <div class="card-header table-card-header">
        <h5><?php echo "Import List";  ?></h5>
        <div class="card-header-right table-card-header ">
            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                <i class="icofont icofont-reply"></i>
                Back
            </a>
        </div>
    </div>
    <form method="post" enctype="multipart/form-data">
        <div class="card-body m-l-50 m-r-50">
        <?php if(count((array)$import_excel_data22) !="0"){ ?>
            <table class="table table-hover m-b-0" style="font-size:13px;" >
                <thead>
                    <th scope="col">#</th>
                    <th scope="col">File Name</th>
                    <th scope="col">System Start Date</th>
                    <th scope="col">Upload Date</th>
                </thead>
                <tbody>
                    <?php $mk=1; foreach($import_excel_data22 as $ied){ ?>
                        <tr>
                            <td><?php echo $mk; ?></td>
                            <td><?php echo $ied['file_name']; ?></td>
                            <td><?php echo fromDate($ied['system_start_date']); ?></td>
                            <td><?php echo fromDate($ied['created_date']); ?></td>
                        </tr>
                    <?php $mk++;
} ?>
                </tbody>
            </table>
        <?php }else{ ?>
            <div class="card-px text-center ">
                <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Import</p>
                <div class="text-center px-4">
                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                </div>
            </div>
        <?php } ?>
        </div>
    </form>
</div>
<?php } ?>

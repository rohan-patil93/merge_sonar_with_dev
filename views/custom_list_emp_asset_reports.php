<?php

    $url = API."?mode=getassetallocationvms&report=Y&admin_type=".$_SESSION['admin_type'];
    $data_details = callAPI($url);
    $asedetails = json_decode($data_details,1);
    $employee = $asedetails['employees'];
    $emp_assets = $asedetails['emp_assets'];
    foreach($emp_assets as $emp_asset){
        $empassetarray[$emp_asset['emp_id']][] = $emp_asset;
    }
    foreach($employee as $employees){
        $empassetarrays[$employees['id']]['display_name'] = $employees['display_name'];
        $empassetarrays[$employees['id']]['listarray'] = $empassetarray[$employees['id']];

    }

    $rid = encodeId($_GET['Rid']);
    $rid = explode(":", $rid);
    $rid = $rid[1];
    $asset_allocation_status= array('0'=>'allocate','1'=>'Accepted','2'=>'Rejected','3'=>'Return');
function valueemtpydash($val){
    $val = $val ?? [];
    return $val==""?" - ":$val;
}
?>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha384-ZvpUoO/+PpLXR1lu4jmpXWu80pZlYUAfxl5NsBMWOEPSjUn/6Z/hRTt8+pR6L4N2" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
  <style>


        tr{}
        td, th{border-bottom: 1px solid #EBEDF3; padding: 10px;}
        table.data-table tr th{color: #B5B5C3; font-weight: 500; font-size: 14px; letter-spacing: 1px;}
        table.data-table .custom-check{position: relative;}
        table.data-table .custom-check input[type="checkbox"]{width: 17px; height: 17px;}
        table.data-table .dropdown .btn{padding: 0px 8px 2px 8px;}

        span.online-text{width: 8px; height: 8px; display: inline-block; background: #dc3545; border-radius: 50%;}
        span.direct-text{width: 8px; height: 8px; display: inline-block; background: #007bff; border-radius: 50%;}
        table.data-table .dropdown-toggle:after{display: none;}
        table.data-table a.edit{display: inline-block; vertical-align: middle;}
    </style>
    <script>
      <?php if($_GET['EMP'] !=""){ ?>
document.onreadystatechange = function () {
  if (document.readyState == "complete") {
    chnagessttts();
  }
}
function chnagessttts(){
    var element = document.getElementById("showclass");
    console.log(element);
    $("#showclass").addClass("bg-info");
    element.scrollIntoView();
    element.scrollIntoView(false);
    element.scrollIntoView({block: "center"});
    element.scrollIntoView({behavior: "smooth", block: "center", inline: "nearest"});
}
<?php } ?>

</script>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="card card-custom card-sticky" id="kt_page_sticky_card">

<div class="card-header">
<div class="row">
                <div class="col-lg-3">
                    <div class="col bg-light-primary ">
                        <span class="svg-icon svg-icon-3x svg-icon-primary d-block my-2">
                            <img class="img-fluid img-120" src="uploads/all/<?php echo $_SESSION['config']['display_logo']; ?>" alt="Theme-Logo" />
                        </span>
                        <span class="text-dark-75 font-weight-bolder font-size-h3"><?php echo $_SESSION['config']['name']; ?></span>
                    </div>
                </div>
                <div class="col-lg-6">
                    <h4 class="card-label text-center f-w-700">Employee Asset reports
                     </br><small class="f-10">Name : <?php echo $_SESSION['display_name']; ?> &nbsp;  Date & Time : <?php echo date("d-m-Y H:i:s"); ?></small></h4>

                </div>


                </div>
</div>

        <div class="card card-custom">
            <div class="card-body">
    <div class="row dt-responsive table-responsive">

        <table class="table table-separate table-head-custom table-checkable data-table" id="kt_datatable1" >
            <tr>
                <th scope="col" style="width: 3%;"></th>
                <th scope="col" style="width: 5%;">SL.NO.</th>
                <th scope="col" style="width: 13%;">Employee Name</th>
                <th scope="col" class="text-right" style="width: 7%;">Total Asset</th>
            </tr>
            <?php $k=1; foreach($empassetarrays as $kyid=> $empassets){

                ?>
            <tr class="main" id="<?php echo $kyid==$_GET['EMP']?'showclass':''; ?>">
                <td>
                    <a href="javascript:void(0)" class="main">
                        <svg xmlns="http:
                          <path d="m12.14 8.753-5.482 4.796c-.646.566-1.658.106-1.658-.753V3.204a1 1 0 0 1 1.659-.753l5.48 4.796a1 1 0 0 1 0 1.506z"/>
                        </svg>
                    </a>
                </td>
                <td><?php echo $k; ?></td>
                <td><?php echo  $empassets['display_name']; ?></td>
                <td class="text-right"><?php echo '<span class="form-label label label-primary">'.count((array)$empassets['listarray']).'</span>'; ?></td>

            </tr>
            <tr class="data" >
                <td colspan="15">
                    <table class="mb-3" style="width: 100%">
                        <tr>
                            <th scope="col" style="width: 3%;"></th>
                            <th scope="col" style="width: 5%;">SL.NO</th>
                            <th scope="col" style="width: 13%;">Employee Name</th>
                            <th scope="col" style="width: 10%;">Start date</th>
                            <th scope="col" style="width: 10%;">End date</th>
                            <th scope="col" style="width: 10%;">Status</th>
                        </tr>
                        <?php $l=1; foreach($empassets['listarray'] as $data){
                            $stauscolr = $data['allocation_type']=="1"?"label-warning":"label-danger";
                        ?>
                        <tr class="<?php echo $data['id']==$rid?'emp-asset-class bg-success':''; ?>">
                            <td></td>
                            <td><?php echo $l; ?></td>
                            <td><?php echo $data['asset_name']; ?></td>
                            <td><?php echo $data['start_date']; ?></td>
                            <td><?php echo $data['end_date']; ?></td>
                            <td><?php echo $data['allocation_type'] != ""?'<span class="form-label label '.$stauscolr.'">'.$asset_allocation_status[$data['allocation_type']].'</span>':""; ?>
                    </td>
                        </tr>
                        <?php $l++;
} ?>
                    </table>
                </td>
            </tr>
            <?php $k++;
} ?>
        </table>
    </div>
</div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function() {
    jQuery(".data").hide();
    jQuery(".main").click(function() {
        jQuery(this).parent().parent().next(".data").toggle("fast");
    });
});
</script>

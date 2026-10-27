<?php
    $curl1 = API."?mode=gettabledetailswithconditions&table=vendors";
    $cudata1 = callAPI($curl1);
    $roles = json_decode($cudata1,1);

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
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="card card-custom card-sticky" id="kt_page_sticky_card">
            <div class="card-header">
                <div class="card-title">
                    <span class="kt-portlet__head-icon">
                        <i class="kt-font-brand flaticon2-line-chart"></i>
                    </span>
                    <h3 class="card-label">&nbsp;<?php echo "Vendor-wise Agrement List";  ?> </h3>

                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="card-toolbar">
                            <a href="javascript:history.go(-1)" class="btn btn-light-primary font-weight-bolder mr-2">
                                <i class="ki ki-long-arrow-back icon-xs"></i>
                                Back
                            </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card card-custom">
            <div class="card-body">
    <div class="row pl-5 pr-5">

        <table class="table table-separate table-head-custom table-checkable data-table" id="kt_datatable1">
            <tr>
                <th scope="col" style="width: 3%;"></th>
                <th scope="col" style="width: 5%;">SL.NO.</th>
                <th scope="col" style="width: 13%;">Vendor Name</th>
                <th scope="col" class="text-right" style="width: 10%;">Company Name</th>
                <th scope="col" class="text-right" style="width: 12%;">Email Id</th>
                <th scope="col" class="text-right" style="width: 12%;">Mobile No</th>
                <th scope="col" class="text-right" style="width: 12%;">Contact Person</th>
                <th scope="col" class="text-right" style="width: 12%;">Web Site</th>
            </tr>
        <?php $k=1; foreach($roles as $vender){
   ?>

            <tr class="main">
                <td>
                    <a href="javascript:void(0)" class="main">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-right-fill" viewBox="0 0 16 16">
                          <path d="m12.14 8.753-5.482 4.796c-.646.566-1.658.106-1.658-.753V3.204a1 1 0 0 1 1.659-.753l5.48 4.796a1 1 0 0 1 0 1.506z"/>
                        </svg>
                    </a>
                </td>

                <td><?php echo $k; ?></td>
                <td><?php echo  $vender['display_name']; ?></td>
                <td class="text-right"><?php echo  $vender['company']; ?>    </td>
                <td class="text-right"><?php echo  $vender['email']; ?>    </td>
                <td class="text-right"><?php echo  $vender['mobile']; ?>    </td>
                <td class="text-right"><?php echo $vender['contact_person']; ?></td>

                <td class="text-right"><?php echo  $vender['website_url']; ?>    </td>


            </tr>
            <tr class="data">
                <td colspan="13">
                    <table class="mb-3" style="width: 100%">
                        <tr>
                            <th scope="col" style="width: 3%;"></th>
                            <th scope="col" style="width: 5%;">SL.NO</th>
              <th scope="col" style="width: 13%;">Agreement Name</th>
              <th scope="col" class="text-right" style="width: 10%;">Agreement Ref No</th>
              <th scope="col" class="text-right" style="width: 12%;">Signed Date</th>
              <th scope="col" class="text-right" style="width: 10%;">Expiry Date</th>
              <th scope="col" class="text-right" style="width: 10%;">File</th>
                          <th scope="col" class="text-right" style="width: 10%;">Remark</th>
                    </tr>


<?php
$l=1;
    $data11["where"] = array('vendor_id'=> $vender['id']);
    $datw = urlencode(json_encode($data11));
    $curl1 = API."?mode=gettabledetailswithconditions&table=vendor_agreements&data=".$datw;
    $cudata1 = callAPI($curl1);
    $aggrements = json_decode($cudata1,1);
foreach($aggrements as $data){

?>
                        <tr>
                            <td></td>
                            <td><?php echo $l; ?></td>
                            <td><?php echo $data['agreement_name']; ?></td>
                            <td class="text-right"><?php echo $data['agreement_ref_no']; ?></td>
                            <td class="text-right"><?php echo $data['signed_date']; ?></td>
                            <td class="text-right"><?php echo $data['expiry_date']; ?></td>
                            <td class="text-right"><a href="<?php echo IMGURL.$data['file_url']; ?>" download="">Attachment</a></td>
                                                    <td class="text-right"> <?php if(strtotime($data['expiry_date']) < strtotime(date('Y-m-d'))){ echo "<spne style='color:red';>Agrement  Expire</span>";
} ?></td>
                        </tr>
                        <?php  $l++; $xx++;
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

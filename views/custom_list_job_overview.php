<?php

if(isset($_POST['filter'])){
    $url = API."?mode=getlistjobpostingslist&start_date=".$_POST['start_date']."&end_date=".$_POST['end_date']."&location=".$_POST['location']."&department=".$_POST['department'];
}else{
    $url = API."?mode=getlistjobpostingslist";
}
$response = callAPI($url);
$list = json_decode($response,1);

        $vdata1["field"] = "id,location_name";
        $vdata = urlencode(json_encode($vdata1));
        $vurl = API."?mode=gettabledetailswithconditions&table=locations&data=".$vdata;
        $vdata_details = callAPI($vurl);
        $location  = json_decode($vdata_details,1);

        $vdata2["field"] = "id,department_name";
        $vdata1 = urlencode(json_encode($vdata2));
        $vurl2 = API."?mode=gettabledetailswithconditions&table=departments&data=".$vdata1;
        $vdata_detail = callAPI($vurl2);
        $departments  = json_decode($vdata_detail,1);

?>

<div class="d-flex flex-column-fluid">
    <div class="container-fluid">

    <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                <div class="card-header">
                    <div class="card-title">
                <span class="kt-portlet__head-icon">
                    <i class="kt-font-brand flaticon2-line-chart"></i>
                </span>
                <h3 class="card-label">&nbsp;<?php echo "Job Postings";  ?> </h3>


            </div>

        </div>
        <div class="card card-custom">
            <div class="card-body">

<form method="post">

    <div class="form-group row">
        <div class="col-lg-2">
            <span  class="col-form-label">Start Date</span>
            <input aria-label="Select date" class="form-control" type="date" value="<?php echo isset($_POST['start_date'])?$_POST['start_date']:date('Y-m-d', strtotime('first day of this month')); ?>" name="start_date" id="example-date-input-from">
        </div>
        <div class="col-lg-2">
            <span  class="col-form-label">End Date</span>
            <input aria-label="End Date" class="form-control" type="date"  name="end_date"  value="<?php echo isset($_POST['end_date'])?$_POST['end_date']:date('Y-m-d', strtotime('last day of this month')); ?>" id="example-date-input-to">
        </div>
        <div class="col-lg-2">
            <span  class="col-form-label">Location</span>

                <select aria-label="Location" class="form-control kt-select2 select2-hidden-accessible" id="kt_select2_42" name="location" data-select2-id="kt_select2_42_2" tabindex="-1">
        <option value="">Select Location</option>
    <?php    foreach($location as $location){  ?>
            <option value='<?php echo $location['id']; ?>' <?php echo $_POST['location']==$location['id']?"selected":""; ?>><?php echo $location['location_name']; ?></option>";
    <?php     }
?>
                </select>

</div>
        <div class="col-lg-2">
            <span  class="col-form-label">Department</span>
        <select aria-label="Department" class="form-control kt-select2 select2-hidden-accessible" id="kt_select2_42_3" name="department" data-select2-id="kt_select2_42_4" tabindex="-1">
            <option value="">Select Department</option>
    <?php    foreach($departments as $departments){  ?>
            <option value='<?php echo $departments['id']; ?>'  <?php echo $_POST['department']==$departments['id']?"selected":""; ?>><?php echo $departments['department_name']; ?></option>";
    <?php     }
?>
                </select></div>
        <div class="col-lg-2 mt-10"><button type="submit" id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2" name="filter" value="filter"><i class="la la-check"></i>Filter</button></div>
        <div class="col-lg-2 mt-8"><b>Total Job :</b>  <?php echo $list['totaljob']; ?> </br> <b>Total Candidates :</b> <?php echo $list['totalcndided']; ?></div>
</form>
    </div>

                <?php if(count((array)$list['list'])>0){ ?>
                <!--begin: Datatable -->
                <table class="table table-separate table-head-custom table-checkable" id="kt_datatable1">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Job Title</th>
                            <th scope="col">Posting Date</th>
                            <th scope="col">Published Date</th>
                            <th scope="col">Candidates Applied</th>
                            <th scope="col">Shortlist</th>
                            <th scope="col">Interview Process</th>
                            <th scope="col">Offer Released</th>
                            <th scope="col">Offer Accepted</th>

                        </tr>
                    </thead>
                    <tbody>
                    <?php $i =1;
                        foreach ($list['list'] as $list) {
                        ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><a href="index.php?option=COM_JOB_OVERVIEW&Rid=<?php echo deocdeId($list['id']); ?>"><?php echo $list['title']; ?></a></td>

                            <td><?php echo $list['requested_date']; ?></td>
                            <td><?php echo $list['requested_date']; ?></td>
                            <td><?php echo $list['total_candidate']; ?></td>
                            <td><?php echo $list['shortlistcnt']; ?></td>
                            <td>
<lable type="button"  data-toggle="popover" title="INTERVIEW PROCESS" data-html="true"

data-content="<?php foreach($list['in_procees_list'] as $key => $nnaems){
     echo $nnaems['name']." - ".$nnaems['cnt'].'</br>';
}
 ?>
">
   <?php echo $list['in_preccess']; ?>
</lable></td>


                            <td><?php echo $list['offerrelesed']; ?></td>
                            <td><?php echo $list['offeraccepted']; ?></td>


                        </tr>
                    <?php $i++;
} ?>
                    </tbody>

                </table>

                <!--end: Datatable -->
                <?php } ?>
            </div>
    </div>
<input type="hidden" name="table_name" id="table_name" value="<?php echo $table; ?>">

 <div id="kt_modal_KTDatatable_remote" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog " >
        <div class="modal-content"  id="popup1">

        </div>
    </div>
</div>
<div id="kt_modal_KTDatatable_remote1" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" >
        <div class="modal-content"  id="popup2">

        </div>
    </div>
</div>

</div></div></div>

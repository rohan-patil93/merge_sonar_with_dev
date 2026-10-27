<?php
$url = API."?mode=togettransactionlogempitdeclaration&emp_id=".$_SESSION['emp_id'];
$response = callAPI($url);
$outdata = json_decode($response,1);
$allemployees = $outdata['emp_data'];
foreach( $outdata['data'] as $tabledata){
    if($tabledata['current_status'] == 199){
        $apv[] = $tabledata;
    }elseif($tabledata['current_status'] > 0){
        $sba[] = $tabledata;
    }
    $listemp[]= $tabledata['emp_id'];
}
foreach($allemployees as $allemployee){
    $employees[$allemployee['id']] = $allemployee;

}
$itapprovallist[] = array('bgcolor'=>'primary','data'=>count((array)$allemployees),'name'=>'Total Employees');
$itapprovallist[] = array('bgcolor'=>'success','data'=>count((array)$apv),'name'=>'Approved');
$itapprovallist[] = array('bgcolor'=>'warning','data'=>count((array)$sba),'name'=>'Approval Pending');
$itapprovallist[] = array('bgcolor'=>'dark','data'=>count((array)$allemployees)-(count((array)$apv)+count((array)$sba)),'name'=>'Yet to Submit');
$stausresult = ViewStatusData();
?>
<script type="text/javascript">
  function statuspopup(status,id){
      alert("data"+status+"id:"+id);
     var table_name = document.getElementById('table_name').value;
      jQuery.ajax({
            url: "views/custom_insert_status_popup.php",
            data:'table='+table_name+'&status='+status+'&rid='+id,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal("show");
            },
            error:function (){}
        });
   }

  function displaystausdrop(id,table,status_id){
    $.ajax({
          url: "ajax.php",
          data:'page=current_status_drop&id='+id+'&current_status='+status_id+'&table='+table,
          type: "POST",
          success:function(data){
            console.log(data);
              $("#dropdownlist_"+id).html(data);
          },
          error:function (){}
      });

  }
</script>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header table-card-header">
                        <h5>Approval It Declartions </h5>
                        <div class="card-header-right table-card-header ">

                            <select aria-label="Assessment Year" name="assessment_year" id="assessment_year">
                                <?php $year = date('Y');
                                for ($i=$year-3; $i <$year+1; $i++) { ?>
                                    <option value="<?php echo $i."-".($i+1); ?>"><?php echo $i." - ".($i+1); ?></option>
                                <?php } ?>
                            </select>

                            <button type="button" class="btn waves-effect waves-light btn-inverse btn-outline-inverse"><i class="icofont icofont-reply"></i>Back</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class=" card-custom gutter-b card-stretch">
                            <div class=" d-flex flex-column">
                                <div class="row col-lg-12">
                                    <div class="col-lg-9">
                                        <div class="row">
                                            <?php foreach($itapprovallist as $approvallist) { ?>
                                                <button type="button" class="btn circle-circle-xl border-<?php echo $approvallist['bgcolor']; ?> ml-2 "><div class="text-center font-size-h3"><?php echo $approvallist['data']; ?></div> <div class="text-center font-size-h6"><?php echo $approvallist['name'];?></div></button>
                                            <?php } ?>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="card-body d-flex flex-column">
                                            <div class="d-flex align-items-center justify-content-between flex-grow-1">
                                                <div class="mr-2">
                                                    <h6><?php echo $display_tag; ?> Completion Rate</h6>
                                                </div>
                                            </div>
                                            <div class="pt-8">
                                                <div class="d-flex align-items-center justify-content-between mb-3">
                                                    <div class="text-muted font-weight-bold mr-2">Average</div>
                                                    <div class="text-muted font-weight-bold"><?php echo $approvallistData['rate']; ?></div>
                                                </div>
                                                <div class="progress bg-light-warning progress-xs">
                                                    <div class="progress-bar bg-warning" role="progressbar" style="width: 60%;" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row col-lg-12 m-t-20">
                            <ul class="nav  md-tabs infisuite_tab">
                          <li class="nav-item">
                              <a class="nav-link active" data-bs-toggle="tab" href="#home1" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Approved</a>
                          </li>
                          <li class="nav-item">
                              <a class="nav-link" data-bs-toggle="tab" href="#profile1" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Approval Pending</a>
                          </li>
                          <li class="nav-item">
                              <a class="nav-link" data-bs-toggle="tab" href="#messages1" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Yet to Submit</a>
                          </li>
                      </ul>
                      <div class="tab-content tabs card-block">

                           <div class="tab-pane active" id="home1" role="tabpanel">
                                        <table class="table table-separate table-head-custom table-checkable bordered" id="kt_datatable1">
                                            <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col" >Name </th>
                                                    <th scope="col">Taxable Income </th>
                                                    <th scope="col">Potential Amount </th>
                                                    <th scope="col">Declaration Tax </th>
                                                    <th scope="col">Current Status </th>
                                                    <th scope="col">View </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $i = 1;    foreach ($apv as $apdata) { ?>
                                                    <tr>
                                                        <td style="width: 3%;"> <?php echo $i; ?> </td>
                                                        <td><?php echo $employees[$apdata['emp_id']]['display_name']; ?></td>
                                                        <td class="text-right"> <?php echo  $apdata['taxable_income']; ?> </td>
                                                        <td class="text-right"> <?php echo $apdata['potential_amount']; ?> </td>
                                                        <td class="text-right"> <?php echo $apdata['declaration_tax']; ?> </td>
                                                        <td >
                                                            <button type="button" class="btn btn-success"><?php echo "Approved"; ?></button>
                                                        </td>
                                                        <td>
                                                            <a href="index.php?option=COM_INSERT_IT_DECLARATION&assessment_year=<?php echo $apdata['assessment_year']; ?>&version_no=<?php echo $apdata['version_no']; ?>&EMPID=<?php echo $apdata['emp_id'];  ?>" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="View Details"><i class="fa fa-eye"></i></a>
                                                        </td>
                                                    </tr>
                                                <?php $i++;
} ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="tab-pane" id="profile1" role="tabpanel">
                                        <table class="table table-separate table-head-custom table-checkable bordered" id="kt_datatable1_2">
                                            <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col" >Name </th>
                                                    <th scope="col">Taxable Income </th>
                                                    <th scope="col">Potential Amount </th>
                                                    <th scope="col">Declaration Tax </th>
                                                    <th scope="col">Current Status </th>
                                                    <th scope="col">View </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $i = 1;    foreach ($sba as $apdata) { ?>
                                                    <tr>
                                                        <td style="width: 3%;"> <?php echo $i; ?> </td>
                                                        <td><?php echo $employees[$apdata['emp_id']]['display_name']; ?></td>
                                                        <td class="text-right"> <?php echo  $apdata['taxable_income']; ?> </td>
                                                        <td class="text-right"> <?php echo $apdata['potential_amount']; ?> </td>
                                                        <td class="text-right"> <?php echo $apdata['declaration_tax']; ?> </td>
                                                        <td >
                                                         <button onclick="displaystausdrop(<?php echo $apdata['id']; ?>,'emp_it_declaration',<?php echo $apdata['current_status']; ?>)" class="btn btn-primary dropdown-toggle" style="color: <?php echo "#".$stausresult[$apdata['current_status']]['color']; ?>;    background:  <?php echo "#".strval($stausresult[$apdata['current_status']]['background_color']); ?>; border-color: <?php echo "#".strval($stausresult[$apdata['current_status']]['background_color']); ?>;" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ><?php echo $stausresult[$apdata['current_status']]['name']; ?> </button>
                                                      <div class="dropdown-menu" id="dropdownlist_<?php echo $apdata['id']; ?>">

                                                      </div>
                                                  </div>
                                                        </td>
                                                        <td >
                                                            <a href="index.php?option=COM_INSERT_IT_DECLARATION&assessment_year=<?php echo $apdata['assessment_year']; ?>&version_no=<?php echo $apdata['version_no']; ?>&EMPID=<?php echo $apdata['emp_id'];  ?>" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="View Details"><i class="fa fa-eye"></i></a>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="tab-pane" id="messages1" role="tabpanel">
                                        <table class="table table-separate table-head-custom table-checkable bordered" id="kt_datatable1_3">
                                            <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col" >Name </th>
                                                    <th scope="col">Code</th>
                                                    <th scope="col">Email </th>
                                                    <th scope="col">Current Status </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $i = 1;    foreach ($allemployees as $empd) {
                                                    if(in_array($empd['id'],$listemp)){ ?>
                                                    <tr>
                                                        <td> <?php echo $i; ?> </td>
                                                        <td><?php echo $empd['display_name']; ?></td>
                                                        <td > <?php echo  $empd['emp_code']; ?> </td>
                                                        <td > <?php echo $empd['email_id']; ?> </td>
                                                        <td >
                                                            <button type="button" class="btn btn-success"><?php echo "Yet To submit"; ?></button>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


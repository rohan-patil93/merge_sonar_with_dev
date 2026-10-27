<?php
$url = API."?mode=getassignfrom16aemployeelist";
$response = callAPI($url);
$main = json_decode($response,1);
$data = $main['data'];
?>
<div class="row">
  <div class="card">
    <div class="">
      <div class="card-header table-card-header">
        <div class="">
          <div class="col-lg-3">
           <h5 class=""> Documemt Relase</h5>
           <a href="javascript:history.go(-1)" class="mb-2 mob-import ml-1 float-right">
            <button class="btn btn-primary btn-outline-primary btn-icon m-l-10"><i class="icofont icofont-reply text-primary"></i></button>
          </a>
        </div>
        <div class="card-header-right table-card-header col-lg-6 text-right">
          <a href="index.php?option=COM_INSERT_EMP_ASSIGN_RELEASE_DOCUMENTS" class="btn btn-success mb-2 desktop-view">
            <i class="icofont icofont-plus icon-white "></i> Documemt Relase
          </a>
          <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
            <i class="icofont icofont-reply"></i>
            Back
          </a>
        </div>
      </div>
    </div>
    <div class="card-block table-responsive" id="dyanmictableshow">
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
            <a href="index.php?option=COM_INSERT_EMP_ASSIGN_RELEASE_DOCUMENTS&amp;assessment_year=<?php echo $row['assessment_year']; ?>&amp;list=<?php echo $row['document_list_id']; ?>" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="Edit Details"><i class="fa fa-edit"></i></a>
            <a  href="index.php?option=COM_VIEW_EMP_ASSIGN_RELEASE_DOCUMENTS&amp;assessment_year=<?php echo $row['assessment_year']; ?>&amp;list=<?php echo $row['document_list_id']; ?>" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="View Details"><i class="fa fa-eye"></i></a>
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

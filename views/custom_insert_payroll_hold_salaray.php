
<div class="card card-custom gutter-b">
 <div class="card-header border-0 ">
  <div class="card-header table-card-header">
    <h5>  Employees Hold Salary</h5>
  </div>
</div>

<div class="card-body py-0">
  <div class="card-toolbar">
      <div style="float: left;" class="col-lg-3" >
        <select aria-label="Emp" class="form-control form-select js-example-basic-single col-sm-12"
        id="hold_emp" name="emp_id">
          <option value=""> Select Any one </option>
          <?php foreach ($employes as $employe) {  ?>
            <option  value="<?php echo $employe['id']; ?>"><?php echo $employe['display_name']; ?></option>
          <?php } ?>
        </select>
      </div> &nbsp;&nbsp;
      <div style="float: right; height: 43px;">
        <button type="button" onclick="addinsertexcluded('hold','8')" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2">ADD </button>
      </div>
    </div>
<div class="card-body py-0">
  <div class="table-responsive">
    <table class="table table-head-custom table-vertical-center" id="hold_table">
      <thead>
        <tr class="text-left">
          <th scope="col" class="pr-0" style="width: 10px"></th>
          <th scope="col" class="pr-0" style="width: 80px"></th>
          <th scope="col" style="min-width: 150px">Name</th>
          <th scope="col" style="min-width: 50px"></th>
          <th scope="col" class="pr-0 text-right" style="min-width: 50px">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php $mnm=1;if(count((array)$step8_data)>0){ $i=1;
          foreach($step8_data as $emda){?>
          <tr id='hold_<?php echo $emda['id']; ?>'>
              <td><?php echo $mnm; ?></td>
            <td class="pr-0">
            <div class="d-flex align-items-center">
              <div class="symbol symbol-40 symbol-light-primary flex-shrink-0">
                <span class="symbol-label font-size-h4 font-weight-bold">
                  <?php echo initials($list_emp[$emda['emp_id']]['display_name']); ?>
                </span>
              </div>
            </div>
          </td>
          <td class="pl-0">
            <div><?php echo $list_emp[$emda['emp_id']]['display_name']; ?></div>
            <div class="f-12 text-muted"><?php echo $list_emp[$emda['emp_id']]['emp_code']; ?></div>
          </td>
          <td>
            <button type="button" onclick="notifyuser(<?php echo $list_emp[$emda['emp_id']]['user_id']; ?>)" class="btn btn-success">Notify</button>
          </td>
          <td class="pr-0 text-right">
            <button type="button" class="btn btn-icon btn-light btn-hover-primary btn-sm" onclick="deleteonerow(<?php echo $emda['id']; ?>,'hold_<?php echo $emda['id']; ?>')">
              <i class="ti-trash f-20 text-danger"></i>
            </button>
          </td>
        </tr>
        <?php  $mnm++; $i++;
}
}else{ ?>
         <tr id="hold_no">
           <td colspan="5" class="text-center"> <b>No Recards Fuond</b></td>
         </tr>
       <?php } ?>
     </tbody>
   </table>
 </div>
</div>
</div>

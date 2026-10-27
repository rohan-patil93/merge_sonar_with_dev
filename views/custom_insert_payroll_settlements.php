<div class="card card-custom gutter-b">
   <div class="card-header border-0 ">
      <div class="card-header table-card-header">
        <h5>  Settlements</h5>
        </div>
    </div>
    <div class="card-body py-0">
        <div class="table-responsive">
            <table class="table table-head-custom table-vertical-center" id="kt_advance_table_widget_1">
                <thead>
                    <tr>
                       <th scope="col" class="pr-0" style="width: 80px"></th>
                       <th scope="col" style="min-width: 150px">Name</th>
                       <th scope="col" style="min-width: 150px">Final Date</th>
                       <th scope="col">Hold salary</th>
                    </tr>
                </thead>
                <?php if(count((array)$step4_data)>0){
                    foreach((array)$step4_data as $dd){?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-40 symbol-light-primary flex-shrink-0">
                                <span class="symbol-label font-size-h4 font-weight-bold"> <?php echo initials($dd['display_name']); ?></span>
                            </div>
                        </div>
                    </td>
                    <td><?php echo $dd['display_name']; ?></td>
                    <td>
                        <?php echo fromDate($dd['settle_on']); ?>
                    </td>
                    <td>
                        <span onclick="holdpayroll_exclusions1(<?php echo $dd['emp_id'].",8"; ?>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                        <input aria-label="Hold Salary" class="js-primary-sm" type="checkbox" name="hold_salary" <?php if(in_array($dd['emp_id'], (array)$hold_emps)){  echo "checked";
} ?>   value="0"  id="dilution_<?php echo $values['id']; ?>"  >
                        </span>
                    </td>
                </tr>
                <?php } ?>
                <?php } else { ?>
                 <tr>
                    <td colspan="5" class="text-center"> <b>No Recards Fuond</b></td>
                 </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</div>

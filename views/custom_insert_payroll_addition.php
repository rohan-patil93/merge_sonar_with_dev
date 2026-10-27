<div class="card card-custom gutter-b">
    <div class="card-header border-0 ">
        <div class="card-header table-card-header">
            <h5>New Joinees</h5>
        </div>
    </div>
    <div class="card-body py-0">
        <div class="table-responsive">
            <table class="table table-head-custom table-vertical-center" id="kt_advance_table_widget_1">
                <thead>
                    <tr><th scope="col" class="pr-0" style="width: 10px"></th>
                        <th scope="col" class="pr-0" style="width: 80px"></th>
                        <th scope="col" style="min-width: 150px">Name</th>
                        <th scope="col" style="min-width: 50px">Bank Details</th>
                        <th scope="col" style="min-width: 50px">Break Up Salary</th>
                        <th scope="col" style="min-width: 50px">Pan Card</th>
                        <th scope="col" style="min-width: 50px">Joining Bouns</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                <?php $mnm=1;if(count((array)$step1_data)>0){
                foreach((array)$step1_data as $dd){
                    if($dd['joining_bonus_applicable']==0){
                        $bounsnames[] = $dd['name'];
                    }
                ?>
                    <tr>
                        <td><?php echo $mnm; ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40 symbol-light-primary flex-shrink-0">
                                    <span class="symbol-label font-size-h4 font-weight-bold"> <?php echo initials($dd['name']); ?></span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div><?php echo viewTitle($dd['name']); ?></div>
                            <div class="f-12 text-muted"><?php echo $dd['emp_code']; ?></div>

                        </td>
                        <td>
                            <div class="checkbox-inline">
                                <label class="checkbox">
                                    <input aria-label="Checkbox" type="checkbox" <?php if($dd['bank']==0){ ?> checked <?php
} ?>  disabled="disabled"name="" value="<?php echo $details['id']; ?>" />
                                    <span></span>
                                </label>
                            </div>
                        </td>
                        <td>
                            <div class="checkbox-inline">
                                <label class="checkbox">
                                    <input aria-label="Checkbox" type="checkbox"   <?php if($dd['break_up']==1){ ?>  checked <?php
} ?> disabled="disabled" name="document[]" value="<?php echo $details['id']; ?>" />
                                    <span></span>
                                </label>
                            </div>
                        </td>
                        <td>
                            <div class="checkbox-inline">
                                <label class="checkbox">
                                    <input aria-label="Checkbox" type="checkbox"   <?php if($dd['pan_number']!=''){ ?>  checked <?php
} ?> disabled="disabled" name="document[]" value="<?php echo $details['id']; ?>" />
                                    <span></span>
                                </label>
                            </div>
                        </td>
                        <td>
                            <div class="checkbox-inline">
                                <label class="checkbox">
                                    <input aria-label="Checkbox" type="checkbox"   <?php if($dd['joining_bonus_applicable']==0){ ?>  checked <?php
} ?> disabled="disabled" name="document[]" value="<?php echo $details['id']; ?>" />
                                    <span></span>
                                </label>
                            </div>
                        </td>

                        <td>
                            <button type="button" onclick="notifyuser(<?php echo $dd['user_id']; ?>)" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2">Notify</button>
                        </td>
                    </tr>
                <?php $mnm++;
}
}else{ ?>
                    <tr>
                         <td colspan="5" class="text-center"> <b>No Recards Fuond</b></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


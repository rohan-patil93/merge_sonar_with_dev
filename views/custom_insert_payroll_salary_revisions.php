<div class="card card-custom gutter-b">
    <div class="card-header border-0 ">
        <div class="card-header table-card-header">
            <h5>  Salary Revisions</h5>
        </div>
    </div>
    <div class="card-body py-0">
        <div class="table-responsive">
            <table class="table table-head-custom table-vertical-center" id="kt_advance_table_widget_1">
                <thead>
                    <tr>
                        <th scope="col" class="pr-0" style="width: 10px"></th>
                        <th scope="col" class="pr-0" style="width: 80px"></th>
                        <th scope="col" style="min-width: 150px">Name</th>
                        <th scope="col" style="min-width: 150px">Code</th>
                        <th scope="col">Now CTC</th>
                    </tr>
                </thead>
            <?php  $mnm=1;if(count((array)$step5_data)>0){ foreach($step5_data as $dd){?>
                <tbody>
                    <tr>
                        <td><?php echo $mnm; ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40 symbol-light-primary flex-shrink-0">
                                    <span class="symbol-label font-size-h4 font-weight-bold"> <?php echo initials($dd['display_name']); ?></span>
                                </div>
                            </div>
                        </td>
                        <td>
                             <div><?php echo viewTitle($dd['name']); ?></div>
                            <div class="f-12 text-muted"><?php echo $dd['emp_code']; ?></div>
                        </td>
                        <td> <?php echo fromDate($dd['affected_date']); ?> </td>
                        <td> <?php echo $dd['ctc_yearly']; ?> </td>
                    </tr>
                    <?php $mnm++;
}
}else{ ?>
                    <tr>
                        <td colspan="5" class="text-center"> <b>No Recards Fuond</b></td>
                    </tr>
                </tbody>
            <?php } ?>
            </table>
        </div>
    </div>
</div>

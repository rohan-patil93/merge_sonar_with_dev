<div class="wizard-body py-8 px-8 py-lg-20 px-lg-10">

    <div class="row">
        <div class="offset-xxl-2 col-xxl-8">
            <?php
                ksort($wizard);
                foreach ($wizard as  $wizar1) {
                    ksort($wizar1);
                    $disarray['normal']=$wizar1;
            ?>

            <div class="pb-5" data-wizard-type="step-content" >
                <div class="row">
                    <?php   include_once 'comman_from.php'; ?>
                </div>
            </div>

            <?php
                }
             ?>
            <div class="d-flex justify-content-between border-top mt-5 pt-10">
                <div class="mr-2">
                    <button type="button" class="btn btn-light-primary font-weight-bolder text-uppercase px-9 py-4" data-wizard-type="action-prev">Previous</button>
                </div>
                <div>
                    <input type="hidden" name="hid" value=<?php echo $rid; ?> />
                    <input type="hidden" name="submit" value="Update" />
                    <button type="submit" class="btn btn-success font-weight-bolder text-uppercase px-9 py-4" data-wizard-type="action-submit">Submit</button>
                    <button type="button" class="btn btn-primary font-weight-bolder text-uppercase px-9 py-4" data-wizard-type="action-next">Next</button>
                </div>
            </div>
        </div>



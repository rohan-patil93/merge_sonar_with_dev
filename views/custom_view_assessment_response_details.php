<!--begin::Card-->
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <!--begin::Accordion-->
        <div class="card-header">
                <h5>Responses</h5>
                <div class="card-header-right">'

                    <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                        <i class="icofont icofont-reply"></i>
                        Back
                    </a>
                </div>
            </div>


        <div class="card card-custom">
        <div class="card-body">
        <div class="card-block accordion-block color-accordion-block" id="mainaccordion">
                <div class="color-accordion" id="color-accordion">
                <?php $i=0;  foreach($response as $resp) { $i++;

                    ?>
                    <a class="accordion-msg b-none waves-effect waves-light"><?php echo  $employees[$resp['employee_id']]; ?> </a>
                    <div class="accordion-desc">
                    <div class="card">
                    <div class="card-body pl-12">
                                <?php
                                $employeeid = $resp['employee_id'];
                                include_once "custom_view_assessment_result.php"; ?>
                            </div>
                    </div>
                    </div>
                <?php } ?>

            </div></div>
        </div>

        </div>
    </div>
</div>


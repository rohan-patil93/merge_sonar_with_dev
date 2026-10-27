                <div class="d-flex flex-column-fluid">
                    <div class="container-fluid">
                        <div class="flex-row j-content">
                            <?php
                            include_once 'candidate_side_nav.php'; ?>
                            <!--begin::Content-->
                            <div class="flex-row-fluid ml-lg-2">
                            <!--begin::Row-->
                                 $Rid = deocdeId("id:".$candidate_id); ?>
                                    <?php include_once 'custom_insert_candidates.php'; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" name="table_name" id="table_name" value="emp_assets">

    </div>
</div>

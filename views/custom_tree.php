<script type="text/javascript">

    function getdata(){
        alert(<?php echo json_encode(__('hai'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>);
        var v = $('#tree-container').jstree(true).get_json();
        var mytext = JSON.stringify(v);
        console.log(mytext);


    }
</script>

<!-- begin:: Content -->
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">

    <div class="row">
        <div class="col-lg-12">
            <!--begin::Portlet-->
            <div class="kt-portlet">
                <div class="kt-portlet__head">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            <?= __('Ajax Data'); ?>
                        </h3>
                    </div>
                </div>
                <div class="kt-portlet__body">
                    <div id="kt_tree_6" class="tree-demo">
                    </div>

                </div>
            </div>
            <!--end::Portlet-->
        </div>
            <div> <button onclick="getdata()"><?= __('GET'); ?></button> </div>

    </div>
</div>

                        <!-- end:: Content -->


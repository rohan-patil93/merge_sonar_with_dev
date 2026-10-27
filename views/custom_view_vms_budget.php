<?php

$monthdata = implode('|', (array)$maingra['months']);
$actualdata = implode('|', (array)$maingra['billable']);
$estimationdata = implode('|', (array)$maingra['bdget']);

?>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12">

                <!--begin::Charts Widget 2-->
                <div class="card card-custom bg-white-100 card-stretch gutter-b">
                    <!--begin::Header-->
                    <div class="card-header h-auto">
                        <div class="card-title">
                <h3 class="card-label">Overall Budget Utilization</h3>
            </div>
            <div class="card-toolbar">
                <a target="_blank" href="index.php?option=COM_VIEW_BUDGET_REPORT" class="btn btn-success font-weight-bolder mr-1">
                    Full Report
                </a>
            </div>

                        <!--end::Toolbar-->
                    </div>
                    <!--end::Header-->
                    <!--begin::Body-->
                    <div class="card-body">
                            <!--begin::Tap pane-->

                                <!--begin::Chart-->


                                <input type="hidden" id="monthdata" value="<?php echo $monthdata;?>">
                                <input type="hidden" id="estimationdata" value="<?php echo $estimationdata;?>">
                                <input type="hidden" id="actualdata" value="<?php echo $actualdata;?>">
                                    <div id="kt_amcharts_vms_dashbaord_overall" style="height: 300px;"></div>


                    </div>

                    <!--end::Body-->
                </div>
                <!--end::Charts Widget 2-->
            </div>

        </div>
    </div>
</div>

<script src="assets/plugins/global/plugins.bundle.js"></script>
<script src="assets/plugins/custom/prismjs/prismjs.bundle.js"></script>
<script src="assets/js/scripts.bundle.js"></script>

<script src="//www.amcharts.com/lib/3/amcharts.js" integrity="sha384-UEqG4MxxojOqYymI4f4JMtQisP2oLC8Qv2T62fFLQWCY4QrfS+WpVBS+wLHSg0Mb" crossorigin="anonymous"></script>
<script src="//www.amcharts.com/lib/3/serial.js" integrity="sha384-aviC14tId/1xfGWLH+1T6A3as8A9WRxrwqDVuoCDuGdJuCjhWaFHusuRTUdVFgow" crossorigin="anonymous"></script>
<!--end::Page Vendors-->

<script src="assets/js/pages/features/charts/amcharts/charts.js"></script>
<script src="//www.amcharts.com/lib/3/themes/light.js" integrity="sha384-bAy0EqjOA5jCsPNjmqSa8YmefFrUauJx0jKLRdSMBsbVsZOMmIzTmqtnCuwwAsd3" crossorigin="anonymous"></script>

<div id="kt_modal_KTDatatable_remote1" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable"  >
        <div class="modal-content"  id="popup2">

        </div>
    </div>
</div>

<?php $finance_tax_year = date("Y"); $_SESSION['apply_js']['FILE_TRACKER_DASHBOARDS']=1; ?>
<script>
    function chnageFinanceTaxYear(){
        var htmloader = '<div class="text-center"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $('#ajaxshowdetails').html(htmloader);

        var finance_tax_year = $("#finance_tax_year").val();
        jQuery.ajax({
            url: "views/custom_view_onboarding_admin_dashboards_ajax.php",
            data: "finance_tax_year="+finance_tax_year,
            type: "POST",
            success:function(data){
                $('#ajaxshowdetails').html(data);
            },
            error:function (){}
        });
    }
</script>
<div class="row mb-3">
    <div class="col-lg-4">
        <select aria-label="Finance Tax Year" class ="form-control form-control-lg form-select js-example-basic-single" id="finance_tax_year" onchange="chnageFinanceTaxYear()">
            <?php for($i=date("Y")-10; $i <=date("Y"); $i++){ ?>
                <option value="<?php echo $i; ?>" <?php echo ($i == $finance_tax_year)?"selected":""; ?>>
                <?php echo "Apr ".$i." - Mar ".($i+1); ?>
                </option>
          <?php } ?>
        </select>
    </div>
    <div class="col-lg-5"></div>
    <div class="col-lg-3 text-right">
        <a class="btn btn-primary waves-effect waves-light" href="#!" onclick="insertEmployeePopUp()" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="ti-plus"></i> Add Employee</a>
        <a class="btn btn-primary waves-effect waves-light" href="index?option=COM_LIST_PROCESS_TASK_SETTINGS"><i class="ti-plus"></i> Task</a>
    </div>
</div>
<div id="ajaxshowdetails">
<?php  include_once "views/custom_view_onboarding_admin_dashboards_ajax.php"; ?>
</div>

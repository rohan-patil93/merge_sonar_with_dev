<?php
$_GET['table'] = "accounts";
if((isset($_GET['table']) && $_GET['table'] !='') || $_GET['Rid']){
        $table = $_GET['table'];
        $rid= encodeId($_GET['Rid']);
        $tableurl = API."?mode=displayattribute&dis_table=$table&rid=$rid";
        $response = file_get_contents($tableurl, false, $context);
        $role = json_decode($response,1);
        $table = $table!="" ? $table : $role['table_name'];
        $attribute = json_decode($role['file_attribute'],1);
}
$array = array('id', 'Current_Status', 'Status_Date', 'From_Date', 'To_Date', 'Created_By',  'Created_Date', 'Updated_By', 'Updated_Date');
?>
<script type="text/javascript">
    function GetSelectedTextValue(key) {
        window.location.href = 'index.php?option=COM_INSERT_DISPLAY_ATTRIBUTES&table='+key;
    }
    function Getinserttype(values,type){
        if(type == 'select' || type == 'radio'){
            var res = values.split("#");
            $('#table').val(res[0]);
            $('#field').val(res[1]);
            $('#kt_modal_KTDatatable_remote').modal({
                show: true
              });
        }
    }
</script>
<
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
                            <div class="kt-portlet">
                                <div class="kt-portlet__body kt-portlet__body--fit">
                                    <div class="kt-grid kt-wizard-v3 kt-wizard-v3--white" id="kt_wizard_v3" data-ktwizard-state="step-first">
                                        <div class="kt-grid__item">


                                            <div class="kt-wizard-v3__nav">
                                                <div class="kt-wizard-v3__nav-items kt-wizard-v3__nav-items--clickable">


                                                    <div class="kt-wizard-v3__nav-item" data-ktwizard-type="step" data-ktwizard-state="current">
                                                        <div class="kt-wizard-v3__nav-body">
                                                            <div class="kt-wizard-v3__nav-label">
                                                                <span>1</span> Create Table
                                                            </div>
                                                            <div class="kt-wizard-v3__nav-bar"></div>
                                                        </div>
                                                    </div>
                                                    <div class="kt-wizard-v3__nav-item" data-ktwizard-type="step">
                                                        <div class="kt-wizard-v3__nav-body">
                                                            <div class="kt-wizard-v3__nav-label">
                                                                <span>2</span> Set Display List
                                                            </div>
                                                            <div class="kt-wizard-v3__nav-bar"></div>
                                                        </div>
                                                    </div>
                                                    <div class="kt-wizard-v3__nav-item" data-ktwizard-type="step">
                                                        <div class="kt-wizard-v3__nav-body">
                                                            <div class="kt-wizard-v3__nav-label">
                                                                <span>3</span> Set Insert/Update
                                                            </div>
                                                            <div class="kt-wizard-v3__nav-bar"></div>
                                                        </div>
                                                    </div>
                                                    <div class="kt-wizard-v3__nav-item" data-ktwizard-type="step">
                                                        <div class="kt-wizard-v3__nav-body">
                                                            <div class="kt-wizard-v3__nav-label">
                                                                <span>4</span> Set Roles
                                                            </div>
                                                            <div class="kt-wizard-v3__nav-bar"></div>
                                                        </div>
                                                    </div>
                                                    <div class="kt-wizard-v3__nav-item" data-ktwizard-type="step">
                                                        <div class="kt-wizard-v3__nav-body">
                                                            <div class="kt-wizard-v3__nav-label">
                                                                <span>5</span> Application Pages
                                                            </div>
                                                            <div class="kt-wizard-v3__nav-bar"></div>
                                                        </div>
                                                    </div>
                                                    <div class="kt-wizard-v3__nav-item" data-ktwizard-type="step">
                                                        <div class="kt-wizard-v3__nav-body">
                                                            <div class="kt-wizard-v3__nav-label">
                                                                <span>6</span> Set Menu
                                                            </div>
                                                            <div class="kt-wizard-v3__nav-bar"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                        <div class="kt-grid__item kt-grid__item--fluid kt-wizard-v3__wrapper">


                                            <form class="kt-form" id="kt_form" action="" enctype="">


                                                <div class="kt-wizard-v3__content" data-ktwizard-type="step-content" data-ktwizard-state="current">

                                                    <div class="kt-form__section kt-form__section--first">
                                                        <div class="kt-wizard-v3__form">
                                                            <?php include_once 'create_table.php'; ?>

                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="kt-wizard-v3__content" data-ktwizard-type="step-content">

                                                    <div class="kt-form__section kt-form__section--first">
                                                        <?php  include_once 'attributes.php'; ?>

                                                    </div>
                                                </div>


                                                <div class="kt-wizard-v3__content" data-ktwizard-type="step-content">
                                                    <?php  include_once 'insert_attributes.php'; ?>
                                                </div>


                                                <div class="kt-wizard-v3__content" data-ktwizard-type="step-content">
                                                    <?php  include_once 'assign_roles.php'; ?>
                                                </div>


                                                <div class="kt-wizard-v3__content" data-ktwizard-type="step-content">
                                                    <?php  include_once 'assign_application_page.php'; ?>
                                                </div>


                                                <div class="kt-wizard-v3__content" data-ktwizard-type="step-content">
                                                    <?php  include_once 'assign_menu.php'; ?>
                                                </div>


                                                <div class="kt-form__actions">
                                                    <button class="btn btn-secondary btn-md btn-tall btn-wide kt-font-bold kt-font-transform-u" data-ktwizard-type="action-prev">
                                                        Previous
                                                    </button>
                                                    <button class="btn btn-success btn-md btn-tall btn-wide kt-font-bold kt-font-transform-u" data-ktwizard-type="action-submit">
                                                        Submit
                                                    </button>
                                                    <button class="btn btn-brand btn-md btn-tall btn-wide kt-font-bold kt-font-transform-u" data-ktwizard-type="action-next">
                                                        Next Step
                                                    </button>
                                                </div>


                                            </form>


                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
<?php include_once 'attributes_popup.php'; ?>

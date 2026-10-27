<?php
if (isset($_POST['submit'])){
    print_r($_POST); exit;
    if($_POST['submit'] == 'Update'){
        print_r($_POST); exit;


    }
}

$approval_url = API."?mode=getapprovallogdata&table=".$_GET['table'];
$approval_data = callAPI($approval_url);
$approval_data = json_decode($approval_data,1);
$buutons = $approval_data['button'];
$data = $approval_data['data'];
print_r($buutons); exit;
$content = $data['help_text']!='' ? $data['help_text'] : "Help Test";
?>
<script type="text/javascript">
     $(document).on('click', '#checkall1', function() {
          alert('haii');

    });
     function chackd(val){
        alert("ravi");
        alert(val);
         var dcheck = document.getelementbyid('d_check_all').value;
         var checkboxes = document.getelementbyid('row').value;
         alert(checkboxes);
       if (dcheck == 0) {
         for (var i = 0; i < checkboxes.length; i++) {
           checkboxes[i].checked = true;
         }
       } else {
         for (var i = 0; i < checkboxes.length; i++) {
           checkboxes[i].checked = false;
         }
       }
     }

</script>

<div creport_codelass="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <?php if($content !=''){ ?>
    <div class="alert alert-light alert-elevate" role="alert">
        <div class="alert-icon"><i class="flaticon-warning kt-font-brand"></i></div>
        <div class="alert-text">
                    <?php echo $content; ?>
        </div>
    </div>
        <?php } ?>
                    <div class="kt-portlet kt-portlet--mobile">
                        <div class="kt-portlet__head kt-portlet__head--lg">
                            <div class="kt-portlet__head-label">
                                <span class="kt-portlet__head-icon">
                                    <i class="kt-font-brand flaticon-list-2"></i>
                                </span>
                                <h3 class="kt-portlet__head-title">
                                    <?php echo "Approval Logs";  ?>
                                </h3>

                            </div>
                            <div class="kt-portlet__head-toolbar">
                                <div class="kt-portlet__head-wrapper">
                                    <div class="kt-portlet__head-actions">
                                        <div class="dropdown dropdown-inline">
                                            <a href="javascript:history.go(-1)" class="btn btn-clean kt-margin-r-10">
                                                    <i class="la la-arrow-left"></i>
                                                    <span class="kt-hidden-mobile">Back</span>
                                                </a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="kt-portlet__body">

<div style="margin-bottom: 10px;">
    <form class="kt-form kt-form--fit kt-margin-b-20" action="" method="post">
    <?php foreach($buutons as $button){ ?>
    <div class="dropdown dropdown-inline">
        <a href="index.php?option=COM_List_TRANSACTION_APPROVAL_LOGS&table=<?php echo $button['button'];?> "><button class="btn btn-primary btn-brand--icon">
            <span>
                <span> <?php echo viewTitle($button['button']); ?> </span>
            </span>
        </button></a>
    </div>
<?php } ?>

</div>
<div class="kt-form kt-form--label-align-right kt-margin-t-20" id="kt_datatable_group_action_form" style="margin-bottom: 10px;">
    <div class="row align-items-center">
        <div class="col-xl-12">
            <div class="kt-form__group kt-form__group--inline">
                <div class="kt-form__label kt-form__label-no-wrap">
                    <span class="kt-font-bold kt-font-danger-">Selected
                        <span id="kt_datatable_selected_number">0</span> records:</span>
                </div>
                <div class="kt-form__control">
                    <div class="btn-toolbar">
                        <div class="dropdown">
                            <button type="button" class="btn btn-brand btn-sm dropdown-toggle" data-toggle="dropdown">
                                Update status
                            </button>
                            <div class="dropdown-menu">
                                <button class="dropdown-item" name="status" id="status" value="<?php echo ST_APPROVED; ?>">Approved</button>
                                <button class="dropdown-item" name="status" id="status_2" value="<?php echo ST_SEND_BACK; ?>">Send Back</button>
                                <button class="dropdown-item" name="status" id="status_3" value="<?php echo ST_REJECTED; ?>">Reject</button>
                            </div>
                        </div>
                        &nbsp;&nbsp;&nbsp;
                        <button class="btn btn-sm btn-danger" type="button" disabled id="kt_datatable_delete_all">Delete All</button>
                        &nbsp;&nbsp;&nbsp;
                        <a href="">
                        <button class="btn btn-sm btn-success" type="button" data-toggle="modal" data-target="#kt_modal_fetch_id">Fetch Selected Records</button></a>
                        <input type="hidden" name="table" value="<?php echo $button['table']; ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
                            <!--begin: Datatable -->
        <table class="table table-striped- table-bordered table-hover table-checkable" style="margin-top: 15px;" id="kt_table_1">
            <thead>
                <tr>
                    <th scope="col"><label class="kt-checkbox">

                        <input aria-label="Checkall1"  type="checkbox"  id="checkall1" value="0" onclick="chackd(this.value )" onkeydown="">  <span> </span> </label>
                    </th>
                    <th scope="col">Transaction No</th>
                    <th scope="col">Transaction Date</th>
                    <th scope="col">Originator</th>
                    <th scope="col">Sub Total</th>
                    <th scope="col">Discount </th>
                    <th scope="col">Shipping </th>
                    <th scope="col">Tax </th>
                    <th scope="col">Budget </th>
                    <th scope="col">Action </th>
                </tr>
            </thead>
            <tbody>
                    <?php $i=1; foreach ($data as $row){?>
                        <input type="hidden" name="transaction_id[]" value="<?php echo $row['id']; ?>">
                <tr>
                    <td>
                        <div>

                        <label class="kt-checkbox">
                            <input aria-label="Input field"  name="check[<?php echo $i; ?>]" id="check" type="checkbox" value="yes">
                            <span> </span>
                        </label>
                        </div>
                    </td>
                    <td><?php echo $row['transaction_number']; ?></td>
                    <td><?php echo $row['transaction_date']; ?></td>
                    <td><?php echo $row['party_name']; ?></td>
                    <td><?php echo $row['amount']; ?></td>
                    <td><?php echo $row['discount_amount']; ?></td>
                    <td><?php echo $row['shipping_amount']; ?></td>
                    <td><?php echo $row['tax_amount']; ?></td>
                    <td><?php echo ""; ?></td>
                    <td><?php echo ""; ?></td>
                </tr>
                <?php $i++;
} ?>
                <input type="hidden" name="lenght" id="row" value="<?php echo $i; ?>">
            </tbody>
        </table>


        <!--end: Datatable -->
    </div>
</div>
<input type="hidden" name="submit" value="Update" />
</form>

</div>

<?php
$_SESSION['apply_js']['multiselest'] = "1";
$conditionurl = "";
if(isset($_POST['searchfilter'])){
    $conditionurl = "&customer_id=".$_POST['customer_id']."&filter_text=".$_POST['filter_text'];
}
$url = API."?mode=gettenantreports".$conditionurl;
$api = callAPI($url);
$result = json_decode($api,1);
$customers = $result['customers'];
$customers_all = $result['customers_all'];
$banks = $result['banks'];
$property_tenant_details = $result['property_tenant_details'];
$property_owners = $result['property_owners'];
$property_owner_details = $result['property_owner_details'];
?>
<style type="text/css">.data{ display: none; }</style>
<script type="text/javascript">
    function openSubTable(id,levelcheck,levelchideshow){
        var showhide = $("#"+levelcheck+"_"+id).val();
        console.log(showhide);
        if(showhide=='0'){
            $("."+levelchideshow+"_"+id).show('slow');
            $("#"+levelcheck+"_"+id).val('1');
        }else{
            $("."+levelchideshow+"_"+id).hide('slow');
            $("#"+levelcheck+"_"+id).val('0');
        }
    }
</script>
<div class="card">
        <form method="post">
        <div class="card-header  row">
            <div class="col-lg-8">
                <h5>Tenant Reports</h5>
            </div>
            <div class="col-lg-2">
                <div class="form-group w-full table-card-header">
                    <span class="form-label ">Tenant List<span class="text-danger">*</span></span>
                    <select aria-label="Customer" name="customer_id" class="form-control form-select js-example-basic-single" >
                        <option value="">Select Any One </option>
                        <?php foreach($customers_all as $cust){ ?>
                            <option value="<?php echo $cust['id']; ?>"><?php echo $cust['display_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="col-lg-1">
                 <button type="submit" class="input-group-text addon-style cursor-pointer float-left btn btn-primary m-t-30" name="searchfilter"> Search</button>
            </div>
            <div class="col-lg-1">
                 <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view"><i class="icofont icofont-reply"></i>Back</a>
             </div>
        </div>
    </form>

    <div class="card-body">
        <?php if(count((array)$customers) !=0){ ?>
            <table class="table table-separate table-head-custom table-checkable data-table custom_asign_emp_mpc" >
                <tr>
                    <th scope="col"></th>
                    <th scope="col">#</th>
                    <th scope="col">Tenant Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Contact</th>
                    <th scope="col">Address</th>
                    <th scope="col">No. Property</th>
                </tr>
                <?php $parenttb = 1; foreach($customers as $po){ ?>
                    <tr class="main" onclick="openSubTable('<?php echo $parenttb; ?>','firstlevelchildcheck','firstlevelchildhideshow')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                        <td class="cursor-pointer">
                            <i class="ti-angle-double-down" ></i>
                            <input type="hidden" id="firstlevelchildcheck_<?php echo $parenttb; ?>" value="0">
                        </td>
                        <td><?php echo $parenttb; ?></td>
                        <td><?php echo $po['display_name']; ?></td>
                        <td><?php echo emptytextdashShow($po['email']); ?></td>
                        <td><?php echo emptytextdashShow($po['phone']); ?></td>
                        <td class="w-b-w-s"><?php echo emptytextdashShow($po['billing_address']); ?></td>
                        <td><span class="form-label label label-md label-primary"><?php echo count((array)$property_tenant_details[$po['id']]); ?></span></td>
                    </tr>

                    <tr class="data firstlevelchildhideshow_<?php echo $parenttb; ?>">
                        <td></td>
                        <td colspan="10" class="p-0">
                            <?php $pods = $property_tenant_details[$po['id']];
                            if(count((array)$pods) !=0){ ?>
                            <table class="table table-separate">
                                <thead>
                                    <tr>
                                        <th scope="col"></th>
                                        <th scope="col">#</th>
                                        <th scope="col">Owner Name</th>
                                        <th scope="col">Property Name</th>
                                        <th scope="col">Start Date</th>
                                        <th scope="col">End Date</th>
                                        <th scope="col">Security Deposit</th>
                                        <th scope="col">No. Area</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $parentchildtb = 1;
                                foreach($pods as $pod){
                                    $podas = json_decode($pod['property_owner_detail_areas'],1);
                                ?>

                                    <tr onclick="openSubTable('<?php echo $parentchildtb; ?>','secondlevelchildcheck','secondlevelchildhideshow')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                        <td class="cursor-pointer">
                                            <i class="ti-angle-double-down" ></i>
                                            <input type="hidden" id="secondlevelchildcheck_<?php echo $parentchildtb; ?>" value="0">
                                        </td>
                                        <td><?php echo $parentchildtb; ?></td>
                                        <td><?php echo $property_owners[$pod['property_owner_id']]; ?></td>
                                        <td><?php echo $property_owner_details[$pod['property_owner_detail_id']]; ?></td>
                                        <td><?php echo fromDate($pod['rent_commencement_date']); ?></td>
                                        <td><?php echo fromDate($pod['lease_period_end_date']); ?></td>
                                        <td><?php echo customeummarmonyformateifine($pod['security_deposit']); ?></td>
                                        <td><span class="form-label label label-md label-primary"><?php echo count((array)$podas); ?></span></td>
                                    </tr>
                                    <tr class="data secondlevelchildhideshow_<?php echo $parentchildtb; ?>">
                                        <td></td>
                                        <td colspan="10" class="p-0">
                                        <table class="table table-separate">
                                            <thead>
                                                <tr>
                                                    <th scope="col"></th>
                                                    <th scope="col">#</th>
                                                    <th scope="col">Area Name</th>
                                                    <th scope="col" class="text-right">Per Sq Ft Rate</th>
                                                    <th scope="col" class="text-right">Per Sq Ft Discount</th>
                                                    <th scope="col" class="text-right">rent</th>
                                                    <th scope="col">Status</th>
                                                    <th scope="col">Property Tenants</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $parentchildtb_2 = 1;
                                                foreach($podas as $poda){ ?>
                                                <tr>
                                                    <td></td>
                                                    <td><?php echo $parentchildtb_2; ?></td>
                                                    <td><?php echo $poda['property_area_name']; ?></td>
                                                    <td class="text-right"><?php echo $poda['per_sq_ft_rate']; ?></td>
                                                    <td class="text-right"><?php echo customeummarmonyformateifine($poda['per_sq_ft_discount']); ?></td>
                                                    <td class="text-right"><?php echo customeummarmonyformateifine($poda['rent']); ?></td>
                                                    <td> - </td>
                                                    <td>
                                                         <a href="index?option=COM_INSERT_PROPERTY_TENANT_DETAILS&Rid=<?php echo deocdeId('id:'.$pod['id']); ?>" target="_blank" class="cursor-pointer btn btn-info">View</a>
                                                    </td>
                                                </tr>
                                            <?php $parentchildtb_2++;
} ?>
                                            </tbody>
                                        </table>
                                    </td>
                                    </tr>
                                <?php $parentchildtb++;
} ?>
                                </tbody>
                            </table>
                        <?php }else{ echo "<p class='m-t-20 text-center text-danger'>No Data</p>";
}  ?>
                        </td>
                    </tr>
                <?php $parenttb++;
}
}else{ ?>
                        <div class="card-px text-center py-20 m-t-30">
                            <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Tenants</p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                            </div>
                        </div>
                    <?php } ?>

            </table>

    </div>
</div>

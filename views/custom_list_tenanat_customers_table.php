<?php
if($_POST['filter'] == "Y"){
  include_once '../includes/session.php';
  include_once '../dbconfig.php';
  include_once '../comm.php';
  $url = API."?mode=gettenantcustomers&filter=Y&sort_by=".$_POST['sort_by']."&limit=".$_POST['limit']."&alphabet=".$_POST['display_name_alphabet']."&searchname=".$_POST['searchname']."&admin_type=".$_SESSION['admin_type'];
  $response = callAPI($url);
  $result = json_decode($response,1);
  $customers = $result['customers'];
  $next_payment_invoices = $result['next_payment_invoices'];
  $payment_due = $result['payment_due'];
  $last_payment = $result['last_payment'];
  $total_property_cnt  = $result['total_property_cnt'];
}
?>

<?php
 if(count((array)$customers) !=0){

if($_POST['table_type'] =="L"){
        ?>
        <div class="col-lg-12">
          <div class="card table-responsive">
            <table class="table table-hover m-b-0 " style="font-size:13px;">
              <thead>
                <tr><th scope="col">#</th><th scope="col">Name</th><th scope="col">Email ID</th><th scope="col">Phone No</th><th scope="col">Action</th></tr>
              </thead>
              <tbody>
                <?php $mk=1; foreach($customers as $candidate){
                  $rid = deocdeId("id:".$cust['id']);
                  ?>
                  <tr>
                    <td><?php echo $mk; ?></td>
                    <td><?php echo $candidate['display_name']; ?></td>
                    <td><?php echo $candidate['email']; ?></td>
                    <td><?php echo DynamicPhoneNumberFormt($candidate['phone']); ?></td>
                    <td>
                      <a href="index.php?option=COM_INSERT_CUSTOMERS&Rid=<?php echo $rid; ?>" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="Edit Details"><i class="fa fa-edit"></i></a>
                    </td>
                  </tr>
                  <?php $mk++;
} ?>
                </tbody>
              </table>
            </div>
          </div>

        <?php }else{

foreach($customers as $cust){ $rid = deocdeId("id:".$cust['id']); ?>
  <div class="col-lg-4">
    <div class="card sale-card" style="min-height: 270px;">
      <div class="card-block">
        <div class="row align-items-center  border-bottom">
          <div class="col-lg-2 p-r-0">
            <?php if($cust['image'] != ""){
                ?>
                <img src="uploads/all/<?php echo $cust['image']; ?>" class="img-radius img-50" alt="User">
              <?php }else{ ?>
                <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($cust['display_name']); ?></span>
              <?php } ?>
          </div>
          <div class="col-lg-10 p-l-20">
            <a class="f-w-600 f-16 text-capitalize d-block" href="index.php?option=COM_INSERT_CUSTOMERS&Rid=<?php echo $rid; ?>"><?php echo $cust['company'];?>
            </a>

            <p class="f-12 f-w-600 text-muted mb-0 mt-1"><?php echo $cust['display_name']; ?>
              <b class="float-end d-inline-block"><a href="tel:<?php echo $cust['phone'];?>"><?php echo DynamicPhoneNumberFormt($cust['phone']);?></a></b>
            </p>

          </div>
          <b class="float-start d-inline-block mt-2" style="word-break: break-all;"><i class="ti-email"></i>  <?php echo $cust['email'];?></b>
          <hr class="mt-3" />
          <div class="col-lg-7 mt-1">
            <span class="f-12 f-w-600 text-muted mb-0">Next Rent: <b class="f-w-600 text-dark"><?php echo $next_payment_invoices[$cust['id']]['due_date']!=""?fromDate($next_payment_invoices[$cust['id']]['due_date']):" - "; ?></b></span>
          </div>
          <div class="col-lg-5 text-right mt-1" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Next Rent">
            <span class="f-14 f-w-600 text-danger mb-0"><?php echo customeummarmonyformateifine($next_payment_invoices[$cust['id']]['pending_amount']); ?></span>
          </div>
          <div class="col-lg-7 mt-2">
            <span class="f-12 f-w-600 text-muted mb-0">Last Paid: <b class="f-w-600 text-dark"><?php echo $last_payment[$cust['id']]['payment_date'] !=""?fromDate($last_payment[$cust['id']]['payment_date']):" - "; ?></b></span>
          </div>
          <div class="col-lg-5 text-right mt-2" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Last Paid">
            <span class="f-14 f-w-600 text-danger mb-0"><?php echo customeummarmonyformateifine($last_payment[$cust['id']]['paid_amonut']); ?></span>
          </div>
          <div class="col-lg-7 mt-2 mb-3">
            <span class="f-12 f-w-600 text-muted mb-0">Payment Due: <b class="f-w-600 text-dark">As on Today</b></span>
          </div>
          <div class="col-lg-5 text-right mt-2" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Payment Due">
            <span class="f-14 f-w-600 text-danger mb-0"><?php echo customeummarmonyformateifine($payment_due[$cust['id']]['pending_amount']); ?></span>
          </div>
        </div>
        <div class="row mt-3">
         <div class="col-lg-8 text-start">

          <i class="ti-email m-l-15 f-18 text-danger cursor-pointer"  onclick="sendEmailCandidate('<?php echo $cust['id']; ?>','<?php echo $cust['email_id']; ?>')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Send Email" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>
        </div>
        <div class="col-lg-4 text-right">
          <a href="#" ><span class="f-12 f-w-600 text-primary mb-0 rented"><?php echo $total_property_cnt[$cust['id']]!=""?$total_property_cnt[$cust['id']]:"0"; ?> Properties</span></a>
        </div>
      </div>
    </div>
  </div>
</div>
<?php }
}
}else{ ?>
 <div clas="card">
  <div class="card-px text-center py-20 m-t-30">
    <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Tenant </p>
    <div class="text-center px-4">
      <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
    </div>
  </div>
</div>

<?php } ?>

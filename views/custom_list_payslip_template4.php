<?php
$month =$_GET['month'];
$year = $_GET['year'];
if($_GET['emp_id']){
    $employe_id = $_GET['employe_id'];
 }
 else{
     $employe_id = $_SESSION['emp_id'];
 }

 $cusurl = API."?mode=togetpayslipdetails&month=".$month."&year=".$year."&employe_id=".$employe_id;
 $cusresponse = callAPI($cusurl);
 $payslip_data_list = json_decode($cusresponse,1);
$dataorg["where"] = array('company_id'=> $_SESSION['company_id'], 'is_active'=>'0');
$datw = urlencode(json_encode($dataorg));
$curl1 = API."?mode=gettabledetailswithconditions&table=organization_settings&data=".$datw;
$cudata1 = callAPI($curl1);
$org_details = json_decode($cudata1,1);
$org_details = $org_details['0'];
$organization_name = $org_details['name'];
$organization_logo = $org_details['display_logo'];
$organization_address = $org_details['contact_address'];
$org_location =  $org_details['location'];
         ?>

         <!--begin::Invoice header-->
         <div class="row justify-content-center py-8 px-8 py-md-36 px-md-0 bg-primary">
             <div class="col-md-9">
                 <div class="d-flex justify-content-between align-items-md-center flex-column flex-md-row">
                     <div class="d-flex flex-column px-0 order-2 order-md-1">
                                 <img src="uploads/all/<?php echo $organization_logo; ?>" style="width: 220px;" alt="" />

                                <!--end::Logo-->
                                <span class="d-flex flex-column font-size-lg font-weight-bold ">
                                    <span><?php echo $organization_nam; ?> </span>
                                    <span> <?php echo $organization_address; ?></span>
                                    <span><?php echo $org_location; ?></span>
                                </span>
                     </div>
                     <h1 class="display-3 font-weight-boldest text-white order-1 order-md-2">PAYSLIP FOR <?php echo $payslip_data_list['month']." ".$payslip_data_list['year'];?></h1>
                 </div>
             </div>
         </div>
         <!--end::Invoice header-->
         <div class="row justify-content-center py-8 px-8 py-md-30 px-md-0">
             <div class="col-md-9">
                 <!--begin::Invoice body-->
                 <div class="row pb-26">
                     <div class="col-md-3 border-right-md pr-md-10 py-md-10">
                         <!--begin::Invoice To-->
                         <div class="text-dark-50 font-size-lg font-weight-bold mb-3">EMPLOYEE </div>
                         <div class="font-size-lg font-weight-bold mb-10"><span><?php echo $payslip_data_list['emp_number']; ?> - <?php echo $payslip_data_list['emp_name']; ?></span>
                                                        <span class="opacity-70"><?php echo $payslip_data_list['designation']; ?></span>
                                                        </div>
                         <!--end::Invoice To-->
                         <!--begin::Invoice No-->

                         <!--end::Invoice No-->
                         <!--begin::Invoice Date-->
                         <div class="text-dark-50      font-size-lg font-weight-bold mb-3">DATE OF JOINING</div>
                         <div class="font-size-lg font-weight-bold"><?php
                         echo date('F d, Y',strtotime($payslip_data_list['doj'])); ?></div>
                         <br>
                         <div class="text-dark-50 font-size-lg font-weight-bold mb-3">STD DAYS</div>
                         <div class="font-size-lg font-weight-bold mb-10"><?php echo $payslip_data_list['std']; ?></div>
                         <div class="text-dark-50 font-size-lg font-weight-bold mb-3">WORKED DAYS</div>
                         <div class="font-size-lg font-weight-bold mb-10"><?php echo $payslip_data_list['work']; ?></div>
                         <div class="text-dark-50 font-size-lg font-weight-bold mb-3">LOP DAYS</div>
                         <div class="font-size-lg font-weight-bold"><?php echo $payslip_data_list['lop']; ?></div>

                         <!--end::Invoice Date-->
                     </div>
                     <div class="col-md-9 py-10 pl-md-10">
                         <div class="table-responsive">
                             <table class="table">
                                 <thead>
                                     <tr>
                                         <th scope="col" class="pl-0 font-weight-boldest text-muted text-uppercase ">Earnings</th>
                                         <th scope="col" class="text-right font-weight-boldest text-muted text-uppercase">Amount</th>
                                         <th scope="col" class="pl-0 font-weight-boldest text-muted text-uppercase">Deductions</th>
                                         <th scope="col" class="text-right font-weight-boldest text-muted text-uppercase">Amount</th>
                                     </tr>
                                 </thead>
                                 <tbody>
                                                                     <?php
                $actual_count= count((array)$payslip_data_list['actual']);
                $dedaction_count= count((array)$payslip_data_list['dedaction']);
                $count = ($actual_count>$dedaction_count) ? $actual_count : $dedaction_count;
                for($i=0; $i<$count;$i++) {?>
               <tr class="font-weight-normal font-size-lg">
                    <td class="pl-0 pt-5">
                      <?php echo  $payslip_data_list['actual'][$i]['name']; ?>
                    </td>
                    <td class="text-right pt-5">
                         <?php
                         echo  $payslip_data_list['actual'][$i]['amount']; ?>
                    </td>
                    <td class="pl-0 pt-5">
                       <?php
                       echo  $payslip_data_list['dedaction'][$i]['name']; ?>
                    </td>
                    <td class="text-right pt-5">
                        <?php echo  $payslip_data_list['dedaction'][$i]['amount']; ?>
                    </td>
                </tr>
            <?php } ?>
                                     <tr class="font-weight-boldest border-bottom-0 font-size-lg">
                                         <td class="pl-0 pt-5">GROSS EARNINGS</td>
                                         <td class="text-right pt-5 text-primary"><?php echo $payslip_data_list['actual_amount'];?></td>
                                         <td class="pl-0 pt-5">TOTAL DEDUCTIONS</td>
                                         <td class="text-right pt-5 text-danger"><?php echo $payslip_data_list['dedaction_amount'];?></td>
                                     </tr>

                                     <tr class="font-weight-boldest border-bottom-0 font-size-lg">
                                         <td class="pl-0 pt-7 text-muted">NET PAY</td>
                                         <td class="text-right pt-5"></td>
                                         <td class="pl-0 pt-5"></td>
                                         <td class="text-right pt-5 text-muted">AMOUNT</td>
                                     </tr>
                                     <tr class="font-weight-boldest border-bottom-0 font-size-lg">
                                         <td class="pl-0 pt-5">GROSS EARNINGS</td>
                                         <td class="text-right pt-5 text-primary"></td>
                                         <td class="pl-0 pt-5"></td>
                                         <td class="text-right pt-5 text-primary"><?php echo $payslip_data_list['actual_amount'];?></td>
                                     </tr>
                                     <tr class="font-weight-boldest border-bottom-0 font-size-lg">
                                         <td class="pl-0 pt-5">TOTAL DEDUCTIONS</td>
                                         <td class="text-right pt-5 text-primary"></td>
                                         <td class="pl-0 pt-5"></td>
                                         <td class="text-right pt-5 text-danger"><?php echo $payslip_data_list['dedaction_amount'];?></td>
                                     </tr>

                                     <tr class="font-weight-boldest border-bottom-0 font-size-lg">
                                         <td class="pl-0 pt-5">TOTAL NET PAYABLE</td>
                                         <td class="text-right pt-5 text-primary"></td>
                                         <td class="pl-0 pt-5"></td>
                                         <td class="text-right pt-5 text-primary"><?php echo $payslip_data_list['net'];?></td>
                                     </tr>
                                 </tbody>
                             </table>

                         </div>
                     </div>
                 </div>
                 <!--end::Invoice body-->
                 <!--begin::Invoice footer-->
                 <div class="row">
                     <div class="col-md-5 border-top pt-14 pb-10 pb-md-18">
                         <div class="d-flex flex-column flex-md-row">
                             <div class="d-flex flex-column">
                                 <div class="font-weight-bold font-size-h6 mb-3">BANK TRANSFER</div>
                                 <div class="d-flex justify-content-between font-size-lg mb-3">
                                     <span class="font-weight-bold mr-15">Account Name:</span>
                                     <span class="text-right"><?php echo $payslip_data_list['bank']; ?></span>
                                 </div>
                                 <div class="d-flex justify-content-between font-size-lg mb-3">
                                     <span class="font-weight-bold mr-15">Account Number:</span>
                                     <span class="text-right"><?php echo $payslip_data_list['bank_account_no']; ?></span>
                                 </div>
                                 <div class="d-flex justify-content-between mb-3">
                                     <span class="mr-15 font-weight-bold">PF A/C:</span>
                                     <span class="text-right"><?php echo $payslip_data_list['pf']; ?></span>
                                 </div>
                                 <div class="d-flex justify-content-between mb-3">
                                     <span class="mr-15 font-weight-bold">UAN:</span>
                                     <span class="text-right"><?php echo $payslip_data_list['pf_uan']; ?></span>
                                 </div>
                                 <div class="d-flex justify-content-between mb-3">
                                     <span class="mr-15 font-weight-bold">PAN:</span>
                                     <span class="text-right"><?php echo $payslip_data_list['pan_number']; ?></span>
                                 </div>
                             </div>
                         </div>
                     </div>
                     <div class="col-md-7 pt-md-25">
                         <div class="bg-primary rounded d-flex align-items-center justify-content-between text-white max-w-350px position-relative ml-auto p-7">
                             <!--begin::Shape-->
                             <div class="position-absolute opacity-30 top-0 right-0">
                                 <span class="svg-icon svg-icon-2x svg-logo-white svg-icon-flip">

                                     <svg xmlns="http://www.w3.org/2000/svg" width="176" height="165" viewBox="0 0 176 165" fill="none">
                                         <g clip-path="url(#clip0)">
                                             <path d="M-10.001 135.168C-10.001 151.643 3.87924 165.001 20.9985 165.001C38.1196 165.001 51.998 151.643 51.998 135.168C51.998 118.691 38.1196 105.335 20.9985 105.335C3.87924 105.335 -10.001 118.691 -10.001 135.168Z" fill="#AD84FF" />
                                             <path d="M28.749 64.3117C28.749 78.7296 40.8927 90.4163 55.8745 90.4163C70.8563 90.4163 83 78.7296 83 64.3117C83 49.8954 70.8563 38.207 55.8745 38.207C40.8927 38.207 28.749 49.8954 28.749 64.3117Z" fill="#AD84FF" />
                                             <path d="M82.9996 120.249C82.9996 144.964 103.819 165 129.501 165C155.181 165 176 144.964 176 120.249C176 95.5342 155.181 75.5 129.501 75.5C103.819 75.5 82.9996 95.5342 82.9996 120.249Z" fill="#AD84FF" />
                                             <path d="M98.4976 23.2928C98.4976 43.8887 115.848 60.5856 137.249 60.5856C158.65 60.5856 176 43.8887 176 23.2928C176 2.69692 158.65 -14 137.249 -14C115.848 -14 98.4976 2.69692 98.4976 23.2928Z" fill="#AD84FF" />
                                             <path d="M-10.0011 8.37466C-10.0011 20.7322 0.409554 30.7493 13.2503 30.7493C26.0911 30.7493 36.5 20.7322 36.5 8.37466C36.5 -3.98287 26.0911 -14 13.2503 -14C0.409554 -14 -10.0011 -3.98287 -10.0011 8.37466Z" fill="#AD84FF" />
                                             <path d="M-2.24881 82.9565C-2.24881 87.0757 1.22081 90.4147 5.50108 90.4147C9.78135 90.4147 13.251 87.0757 13.251 82.9565C13.251 78.839 9.78135 75.5 5.50108 75.5C1.22081 75.5 -2.24881 78.839 -2.24881 82.9565Z" fill="#AD84FF" />
                                             <path d="M55.8744 12.1044C55.8744 18.2841 61.0788 23.2926 67.5001 23.2926C73.9196 23.2926 79.124 18.2841 79.124 12.1044C79.124 5.92653 73.9196 0.917969 67.5001 0.917969C61.0788 0.917969 55.8744 5.92653 55.8744 12.1044Z" fill="#AD84FF" />
                                         </g>
                                     </svg>
                                     <!--end::Svg Icon-->
                                 </span>
                             </div>
                             <!--end::Shape-->
                             <div class="font-weight-boldest font-size-h5">NET SALARY</div>
                             <div class="text-right d-flex flex-column">
                                 <span class="font-weight-boldest font-size-h3 line-height-sm">₹<?php
                                 echo $payslip_data_list['net']; ?></span>
                                 <span class="font-size-sm">(<?php echo  numberToWordFormateRupees($payslip_data_list['net']);  ?>)</span>
                             </div>
                         </div>
                     </div>
                 </div>
                 <!--end::Invoice footer-->
             </div>
         </div>
         <!-- begin: Invoice action-->
         <div class="row justify-content-center border-top py-8 px-8 py-md-28 px-md-0">
             <div class="col-md-9">
                 <div class="d-flex font-size-sm flex-wrap">
                     <button type="button" class="btn btn-primary font-weight-bolder py-4 mr-3 mr-sm-14 my-1" onclick="window.print();">Print Payslip</button>
                     <button type="button" class="btn btn-light-primary font-weight-bolder mr-3 my-1">Download</button>
                 </div>
             </div>
         </div>
         <!-- end: Invoice action-->
        </div>
        <!--end::Invoice-->
    </div>
    <!--end::Container-->
</div>

</div>
<!--end::Content-->

<?php
 $url = API."?mode=togetemployeeform16adetails&empid=19&assessment_year=2021-2022";
$data_details = callAPI($url);
$details = json_decode($data_details,1);
$row = $details['data'];
$formate = "d-m-Y";
?>
<style type="text/css">
.myContainer {
margin: 15px;
}
.myTable{
width: 100%;
border:1px solid black;
}
.td,th{
height:25px;
border:1px solid black;
}
.no-border{
border:none;
}
.no-br{
border-right:0px solid white;
}

.tac{
text-align:center
}
.tar{
text-align:right
}
.tat{
vertical-align: top;
}
.tal{
text-align:left
}
.border-td{
border-top :1px solid black;
border-bottom:1px solid black;
}
.border{
border :1px solid black;
}
td, th{white-space: inherit;}
</style>
<script type="text/javascript">
   function printDiv(divId) {
       var printContents = document.getElementById(divId).innerHTML;
       var originalContents = document.body.innerHTML;
       document.body.innerHTML = "<html lang=\"en\"><head><title>Form 16A Report</title></head><body>" + printContents + "</body>";
       window.print();
       document.body.innerHTML = originalContents;
   }
</script>

<style type="text/css">
    @page{size: landscape;}

    @media print{
        .invoice-3 .invoice-top, .invoice-3 .invoice-center, .invoice-3 .invoice-bottom{padding: 20px;}
        body {writing-mode: tb-rl;}
    }
</style>
<div class="card ">
  <div class="card-header">
    <h5> From 16A</h5>
    <div class="card-header-right">
     <ul class="nav md-tabs infisuite_tab">
      <li class="nav-item" >
        <a class="nav-link active"  data-bs-toggle="tab" href="#check1" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Part A</a>
      </li>
      <li class="nav-item">
        <a class="nav-link  " data-bs-toggle="tab" href="#check2" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Part B</a>
      </li>
    </ul>

    </div>
  </div>
 <div class="tab-content tabs">
    <div class="tab-pane card-body active row" id="check1" >
      <div class="text-right">
        <button class="btn btn-warning mb-2" type="button" onclick="printDiv('printDiv');"> Print </button>
      </div>
      <div class="form16A" id="printDiv" >
         <table class="myTable table table-hover m-b-0" style="width: 100%;">
            <tr>
               <th scope="col" class="text-center" colspan="6">
                  FORM NO. 16A
               </th>
            </tr>
            <tr>
               <td class="text-center"  colspan="6">
                  [See rule 31(1)(a)]</br>
                  PART A
               </td>
            </tr>
            <tr>
               <th scope="col" colspan="6">
                  Certificate under section 203 of the Income-tax Act, 1961 for tax deducted at source on salary paid to an employee under section 192 or pension or interest income of specified senior citizen under section 194P.
               </th>
            </tr>
            <tr>
               <td colspan="2"  >
                  Certificate No:
               </td>
               <td >
                 <?php echo $row['certificate_no']; ?>
               </td>
               <td colspan="2"  >
                  Last updated on:
               </td>
               <td style="min-width:100px">
                <?php echo date($formate, strtotime($row['certificate_last_update_on'])); ?>

               </td>
            </tr>
            <tr>
               <th scope="col" colspan="3">
                  Name and address of the Employer
               </th>
               <th scope="col" colspan="3">
                  Name and address of the Employee
               </th>
            </tr>
            <tr>
               <th scope="col" colspan="3" >
                 <?php echo $row['employer_address']; ?>
               </th>
               <th scope="col" colspan="3">
                 <?php echo $row['employee_address']; ?>
               </th>
            </tr>
            <tr>
               <th scope="col" colspan="2">
                  PAN of the Deductor
               </th>
               <th scope="col" colspan="2">
                  TAN of the Deductor
               </th>
               <th scope="col" colspan="1">
                  PAN of the Employee
               </th>
               <th scope="col" colspan="1">
                  Employee Reference No.provided by the Employer(If available)
               </th>
            </tr>
            <tr>
               <th scope="col" colspan="2" ><?php echo $row['pan_deductor']; ?></th>
               <th scope="col" colspan="2"><?php echo $row['Tan_deductor']; ?></th>
               <th scope="col" colspan="1"><?php echo $row['pan_employee']; ?></th>
               <th scope="col" colspan="1"><?php echo $row['employee_reference_no']; ?></th>
            </tr>
            <tr>
               <th scope="col" colspan="3" class="tac">
                  CIT (TDS)
               </th>
               <th scope="col" colspan="1" class="tac">
                  Assessment Year
               </th>
               <th scope="col" colspan="2" class="tac">
                  Period with the Employer
               </th>
            </tr>
            <tr>
               <td colspan="3" rowspan="2">
                  <?php echo $row['cit_details']; ?>
               </td>
               <th scope="col" colspan="1"rowspan="2">
                 <?php echo $row['assessment_year']; ?>
               </th>
               <th scope="col"  colspan="1">
                  From
               </th>
               <th scope="col"   colspan="1">
                  To
               </th>
            </tr>
            <tr>

               <th scope="col" rowspan="1">
                   <?php echo date($formate, strtotime($row['period_from'])); ?>
               </th>
               <th scope="col" rowspan="1">
                   <?php echo date($formate, strtotime($row['period_to'])); ?>
               </th>
            </tr>
            <tr>
               <th scope="col" class="tac"  colspan="6">
                  Summary of amount paid/credited and tax deducted at source thereon in respect of the employee
               </th>
            </tr>

            <tr>
               <th scope="col" >
                Quarter(s)
               </th>
               <th scope="col" colspan="2">
                  Receipt Numbers of original quarterly statements of TDS under sub-section (3) of Section 200
               </th>
               <th scope="col" >
                  Amount paid/credited
               </th>
               <th scope="col">
                 Amount of tax deducted
               </th>
               <th scope="col" >
                  Amount of tax deposited / remitted
               </th>
            </tr>
            <?php $sdata = json_decode($row['quarterly_deduction'],1);
               $i=1; $ssarray = array();
               if(count((array)$sdata)>0){
               foreach($sdata as $ss){ ?>
              <tr>
               <th scope="col" class="tac">
                Q<?php echo $i; ?>
               </th>
               <th scope="col" colspan="2">
                  <?php echo $ss['code']; ?>
               </th>
               <th scope="col" class="text-right">
                  <?php $ssarray['cre'] = $ssarray['cre']+ $ss['taxble_amount'];
                    echo customeummarmonyformateifine($ss['taxble_amount']);
                  ?>
               </th>
               <th scope="col" class="text-right">
                  <?php $ssarray['ded'] = $ssarray['ded']+ $ss['tax_deducted'];
                    echo customeummarmonyformateifine($ss['tax_deducted']);
                  ?>
               </th>
               <th scope="col" class="text-right">
                  <?php $ssarray['dep'] = $ssarray['dep']+ $ss['tax_deposited'];
                    echo customeummarmonyformateifine($ss['tax_deposited']);
                  ?>
               </th>
            </tr>
            <?php $i++; ?>
            <?php } ?>
            <?php } ?>
            <tr>
               <th scope="col" class="tac">
                Total
               </th>
               <th scope="col" colspan="2" class="" style="background-color: #ccc">

               </th>
               <th scope="col" class="text-right">
                  <?php echo customeummarmonyformateifine($ssarray['cre']); ?>
               </th>
               <th scope="col" class="text-right">
                  <?php echo customeummarmonyformateifine($ssarray['ded']); ?>
               </th>
               <th scope="col" class="text-right">
                  <?php echo customeummarmonyformateifine($ssarray['dep']); ?>
               </th>
            </tr>
            <tr>
               <th scope="col" class="tac"  colspan="6">
                  I. DETAILS OF TAX DEDUCTED AND DEPOSITED INTHE CENTRAL GOVERNMENT ACCOUNT THROUGH BOOK ADJUSTMENT
                 </br>
                 <span class="text-muted">
                   (The deductor to provide payment wise details of tax deducted and deposited with respect to the deductee)
                 </span>
               </th>
            </tr>
            <tr>
               <th scope="col"  rowspan='2'>
                  Sl. No.
               </th>
               <th scope="col" rowspan='2'>
                  Tax deposited
                  in respect of the
                  deductee ()
               </th>
               <th scope="col" colspan='4' class="tac">
                  Book Identification Number (BIN)
               </th>
            </tr>
            <tr>
               <th scope="col" class="tac">
                  Receipt numbers of Form No. 24G
               </th>
               <th scope="col" class="tac">
                  DDO serial number in FormNo. 24G
               </th>
               <th scope="col" class="tac">
                  Date of Transfer voucher(dd/mm/yyyy)
               </th>
               <th scope="col" class="tac">
                  Status of  Matching with Form No.24G
               </th>
            </tr>
           <?php $smdata = json_decode($row['tax_deposited_respect_adjustment'],1);
               $i=1; $smarray = array();
               if(count((array)$smdata)>0){
               foreach($smdata as $ss){ ?>
              <tr>
               <th scope="col" class="tac">
                <?php echo $i; ?>
               </th>
               <th scope="col" colspan="2">
                  <?php $smarray['cre'] = $smarray['cre']+ $ss['tax_deposited'];
                    echo customeummarmonyformateifine($ss['tax_deposited']);
                  ?>
               </th>
               <th scope="col" class="text-right">
                  <?php echo $ss['receipt_no']; ?>
               </th>
               <th scope="col" class="text-right">
                  <?php echo $ss['ddo_no']; ?>
               </th>
               <th scope="col" class="text-right">
                  <?php echo $ss['vochare_date']; ?>
               </th>
               <th scope="col" class="text-right">
                  <?php echo $ss['status_no']; ?>
               </th>
            </tr>
            <?php $i++; ?>
            <?php } ?>
            <?php } ?>
            <tr>
               <th scope="col">
                  Total(Rs)
               </th>
               <td></td>
               <td colspan='4'></td>
            </tr>

             <tr>
               <th scope="col" colspan="6" >
                  <p>
                     I. DETAILS OF TAX DEDUCTED AND DEPOSITED IN THE CENTRAL GOVERNMENT ACCOUNT THROUGH CHALLAN <br/>
                     <span class="text-muted"> (The deductor to provide payment wise details of tax deducted and deposited with respect to the deductee)</span>
                  </p>
               </th>
            </tr>
            <tr>
               <th scope="col"  rowspan='2'>
                  Sl. No.
               </th>
               <th scope="col" rowspan='2'>
                  Tax deposited in respect of the deductee
               </th>
               <th scope="col" colspan='4'>
                  Challan Identification Number (CIN)
               </th>
            </tr>
            <tr>
               <th scope="col" class="tac">
                  BSR Code of the Bank Branch
               </th>
               <th scope="col" class="tac">
                  Date on which tax deposited (dd/mm/yyyy)
               </th>
               <th scope="col" class="tac">
                  Challan Serial Number
               </th>
               <th scope="col" class="tac">
                  Status of matching with OLTAS
               </th>
            </tr>
            <?php $sttdata = json_decode($row['tax_deposited_respect_challan'],1);
               $i=1; $sttsarray = array();
               if(count((array)$sttdata)>0){
               foreach($sttdata as $ss){ ?>
              <tr>
               <th scope="col" class="tac">
                <?php echo $i; ?>
               </th>
               <th scope="col" class="text-right">
                  <?php $sttsarray['cre'] = $sttsarray['cre']+ $ss['tax_deposited'];
                    echo customeummarmonyformateifine($ss['tax_deposited']);
                  ?>
               </th>
               <th scope="col" class="tac">
                  <?php echo $ss['bsr_code']; ?>
               </th>
               <th scope="col" class="tac">
                  <?php echo $ss['tax_deposit_date']; ?>
               </th>
               <th scope="col" class="tac">
                  <?php echo $ss['challan_serial_no']; ?>
               </th>
               <th scope="col" class="tac">
                  <?php echo $ss['oltas']; ?>
               </th>
            </tr>
            <?php $i++; ?>
            <?php } ?>
            <?php } ?>

            <tr>
               <th scope="col">
                  Total
               </th>
               <td class="text-right">
               <?php
                $lasamount = $sttsarray['cre'];
                echo customeummarmonyformateifine($sttsarray['cre']); ?>
                </td>
               <td colspan='4' style="background-color:#ccc"></td>
            </tr>
            <tr>
               <th scope="col" colspan='6' class="tac">
                  Verification
               </th>
            </tr>
            <tr>
               <td colspan='6'>
                  I,<u><?php echo $row['employee_name']; ?> </u> , son/daughter of <?php echo $row['garden']; ?> </u> working in the capacity of <?php echo $row['designation']; ?> </u> (designation) do hereby certify that a
                  sum of  <u><?php echo customeummarmonyformateifine($lasamount); ?> </u> [ <u><?php echo numberToWordFormateRupees($lasamount); ?> </u> (in words)]  has been deducted and a sum of  <u><?php echo customeummarmonyformateifine($lasamount); ?> </u> [ <u><?php echo numberToWordFormateRupees($lasamount); ?> </u> (in words)] has been deducted and deposited to the credit of the Central
                  Government. I further certify that the information given above is true, complete and correct and is based on the books of account, documents, TDS statements, TDS deposited and other available records.
               </td>
            </tr>
            <tr>
               <th scope="col">
                  Place
               </th>
               <td colspan="2">
                <?php echo $row['place']; ?>
               </td>
               <th scope="col" colspan="3">
                  (Signature of person responsible for deduction of tax)
               </th>
            </tr>
            <tr>
               <th scope="col">
                  Date
               </th>
               <td colspan="2">
                <?php echo date($formate, strtotime($row['place_date'])); ?>
               </td>
               <th scope="col" colspan="3">
               </th>
            </tr>
            <tr>
               <th scope="col" colspan="3">
                  <b>Designation: </b>
                  <?php echo $row['designation']; ?>
               </th>
               <th scope="col" colspan="3">
                  <b>Full Name:</b>
                  <?php echo $row['employee_name']; ?>
               </th>
            </tr>
         </table>
         <br/>
         <p><strong>Notes:</strong><br />1. Government deductors to fill information in item I if tax is paid without production of an income-tax challan and in item II if tax is<br />paid accompanied by an income-tax challan.<br />2. Non-Government deductors to fill information in item II.<br />3. The deductor shall furnish the address of the Commissioner of Income-tax (TDS) having jurisdiction as regards TDS statements of<br />the assessee.<br />4. In items I and II, in column for tax deposited in respect of deductee, furnish total amount of TDS, surcharge (if applicable) and<br />education cess (if applicable).&rdquo;</p>
      </div>
      <div>
        <p><strong>Legend used in Form 16</strong></p>
        <span class="text-muted">* Status of matching with OLTAS</span>
        <table class="myTable table table-hover m-b-0">
            <tr class="bg-primary">
              <th scope="col" class="tac">Legend</th>
              <th scope="col" class="tac">Description</th>
              <th scope="col" class="tac">Definition</th>
            </tr>
            <?php
            $lastarray[] = array('U', 'Unmatched', 'Deductors have not deposited taxes or have furnished incorrect particulars of tax payment. Final credit will be reflected only when payment details in bank match with details of deposit in TDS / TCS statement');
            $lastarray[] = array('P', 'Provisional', 'Provisional tax credit is effected only for TDS / TCS Statements filed by Government deducto"P" status will be changed to Final (F) on
              verification of payment details submitted by Pay and Accounts Officer (PAO)');
            $lastarray[] = array('F', 'Final', 'In case of non-government deductors, payment details of TDS / TCS deposited in bank by deductor have matched with the payment details mentioned in the TDS / TCS statement filed by the deducto In case of government deductors, details of TDS / TCS booked in Government account have been verified by Pay & Accounts Officer (PAO)');
            $lastarray[] = array('O', 'Overbooked', 'Payment details of TDS / TCS deposited in bank by deductor have matched with details mentioned in the TDS / TCS statement but the amount is over claimed in the statement. Final (F) credit will be reflected only when deductor reduces claimed amount in the statement or makes new payment for excess amount claimed in the statement');
            foreach($lastarray as $lastar){?>
              <tr class="">
                <th scope="col" class="tac"><?php echo $lastar['0']; ?></th>
                <th scope="col" class="tac"><?php echo $lastar['1']; ?></th>
                <th scope="col" ><?php echo $lastar['2']; ?></th>
              </tr>
            <?php } ?>
        </table>
      </div>
    </div>
    <div class=" tab-pane card-body   row" id="check2">
      <div class="form16A" >
        <table class="myTable table table-hover m-b-0" style="width: 100%;">
              <tr>
                 <th scope="col" class="tac" colspan="6">
                    FORM NO. 16A
                 </th>
              </tr>
              <tr>
                 <td class="tac"  colspan="6">
                    [See rule 31(1)(a)]</br>
                    PART B
                 </td>
              </tr>
              <tr>
                 <th scope="col" colspan="6">
                    Certificate under Section 203 of the Income-tax Act, 1961 for tax deducted at source on salary
                 </th>
              </tr>
              <tr>
                 <td colspan="2"  >
                    Certificate No:
                 </td>
                 <td >
                   <?php echo $row['certificate_no']; ?>
                 </td>
                 <td colspan="2"  >
                    Last updated on:
                 </td>
                 <td style="min-width:100px">
                  <?php echo date($formate, strtotime($row['certificate_last_update_on'])); ?>

                 </td>
              </tr>
              <tr>
                 <th scope="col" colspan="3">
                    Name and address of the Employer
                 </th>
                 <th scope="col" colspan="3">
                    Name and address of the Employee
                 </th>
              </tr>
              <tr>
                 <th scope="col" colspan="3" >
                   <?php echo $row['employer_address']; ?>
                 </th>
                 <th scope="col" colspan="3">
                   <?php echo $row['employee_address']; ?>
                 </th>
              </tr>
              <tr>
                 <th scope="col" colspan="2">
                    PAN of the Deductor
                 </th>
                 <th scope="col" colspan="2">
                    TAN of the Deductor
                 </th>
                 <th scope="col" colspan="2">
                    PAN of the Employee
                 </th>

              </tr>
              <tr>
                 <th scope="col" colspan="2" ><?php echo $row['pan_deductor']; ?></th>
                 <th scope="col" colspan="2"><?php echo $row['Tan_deductor']; ?></th>
                 <th scope="col" colspan="2"><?php echo $row['pan_employee']; ?></th>

              </tr>
              <tr>
                 <th scope="col" colspan="3" class="tac">
                    CIT (TDS)
                 </th>
                 <th scope="col" colspan="1" class="tac">
                    Assessment Year
                 </th>
                 <th scope="col" colspan="2" class="tac">
                    Period with the Employer
                 </th>
              </tr>
              <tr>
                 <td colspan="3" rowspan="2">
                    <?php echo $row['cit_details']; ?>
                 </td>
                 <th scope="col" colspan="1"rowspan="2">
                   <?php echo $row['assessment_year']; ?>
                 </th>
                 <th scope="col"  colspan="1">
                    From
                 </th>
                 <th scope="col"   colspan="1">
                    To
                 </th>
              </tr>
              <tr>

                 <th scope="col" rowspan="1">
                     <?php echo date($formate, strtotime($row['period_from'])); ?>
                 </th>
                 <th scope="col" rowspan="1">
                     <?php echo date($formate, strtotime($row['period_to'])); ?>
                 </th>
              </tr>
        </table>
        <table class="myTable table table-hover m-b-0 mt-10" >
          <tr>
            <th scope="col" colspan="2"> Details of Salary Paid and any other income and tax deducted</th>
            <th scope="col"></th>
            <th scope="col"></th>
          </tr>
          <tr>
            <th scope="col">1</th>
            <th scope="col" colspan="3"> Gross Salary</th>
          </tr>
          <tr>
            <th scope="col">(a)</th>
            <th scope="col">Salary as per provisions contained in section 17(1)</th>
            <th scope="col"><?php $GSAA = "1903612.00"; echo $GSAA; ?></th>
            <th scope="col"></th>
          </tr>
          <tr>
            <th scope="col">(b)</th>
            <th scope="col">Value of perquisites under section 17(2) (as per Form No. 12BA,wherever applicable)</th>
            <th scope="col"><?php $GSAB = "0.00"; echo $GSAB; ?></th>
            <th scope="col"></th>
          </tr>
          <tr>
            <th scope="col">(c)</th>
            <th scope="col">Profits in lieu of salary under section 17(3) (as per Form No.12BA, wherever applicable) </th>
            <th scope="col"><?php $GSAC = "0.00"; echo $GSAC; ?></th>
            <th scope="col"></th>
          </tr>
          <tr>
            <th scope="col">(d)</th>
            <th scope="col">Total</th>
            <th scope="col"></th>
            <th scope="col"><?php $GSAD = $GSAA+$GSAB+$GSAC; echo $GSAD; ?></th>
          </tr>
          <tr>
            <th scope="col">(e)</th>
            <th scope="col">Reported total amount of salary received from other employer(s)</th>
            <th scope="col"></th>
            <th scope="col"><?php $GSAE = "0.00"; echo $GSAE; ?></th>
          </tr>
          <tr>
            <th scope="col">2</th>
            <th scope="col" colspan="3"> Less: Allowances to the extent exempt under section 10</th>
          </tr>
          <tr>
            <th scope="col">(a)</th>
            <th scope="col">Travel concession or assistance under section 10(5) </th>
            <th scope="col"><?php $GSBA = "0.00"; echo $GSBA; ?></th>
            <th scope="col"></th>
          </tr>
          <tr>
            <th scope="col">(b)</th>
            <th scope="col">Death-cum-retirement gratuity under section 10(10)</th>
            <th scope="col"><?php $GSBB = "0.00"; echo $GSBB; ?></th>
            <th scope="col"></th>
          </tr>
          <tr>
            <th scope="col">(c)</th>
            <th scope="col">Commuted value of pension under section 10(10A) </th>
            <th scope="col"><?php $GSBC = "0.00"; echo $GSBC; ?>.</th>
            <th scope="col"></th>
          </tr>
          <tr>
            <th scope="col">(d)</th>
            <th scope="col">Cash equivalent of leave salary encashment under section 10 (10AA)</th>
            <th scope="col"><?php $GSBD = "0.00"; echo $GSBD; ?></th>
            <th scope="col"></th>
          </tr>
          <tr>
            <th scope="col">(e)</th>
            <th scope="col">House rent allowance under section 10(13A)</th>
            <th scope="col"><?php $GSBE = "0.00"; echo $GSBE; ?></th>
            <th scope="col"></th>
          </tr>
          <tr>
            <th scope="col">(f)</th>
            <th scope="col">Amount of any other exemption under section 10</th>
            <th scope="col"><?php $GSBF = "0.00"; echo $GSBF; ?></th>
            <th scope="col"></th>
          </tr>
          <tr>
            <th scope="col">(g)</th>
            <th scope="col">Total amount of any other exemption under section 10</th>
            <th scope="col"><?php $GSBG = "0.00"; echo $GSBG; ?></th>
            <th scope="col"> </th>
          </tr>
          <tr>
            <th scope="col">(h)</th>
            <th scope="col">Total amount of exemption claimed under section 10 [2(a)+2(b)+2(c)+2(d)+2(e)+2(g)]</th>
            <th scope="col"></th>
            <th scope="col"><?php $GSBH = $GSBA+$GSBB+$GSBC+$GSBD+$GSBE+$GSBF+$GSBG; echo $GSBH; ?></th>
          </tr>
          <tr>
            <th scope="col">3</th>
            <th scope="col">Total amount of salary received from current employer [1(d)-2(h)]</th>
            <th scope="col"></th>
            <th scope="col"><?php $GSC = $GSAD-$GSBH; echo $GSC; ?></th>
          </tr>
          <tr>
            <th scope="col">4</th>
            <th scope="col" colspan="3"> Less: Deductions under section 16</th>
          </tr>
          <tr>
            <th scope="col">(a)</th>
            <th scope="col">Standard deduction under section 16(ia) </th>
            <th scope="col"><?php $GSDA = "0.00"; echo $GSDA; ?></th>
            <th scope="col"></th>
          </tr>
          <tr>
            <th scope="col">(b)</th>
            <th scope="col">Entertainment allowance under section 16(ii)</th>
            <th scope="col"><?php $GSDB = "0.00"; echo $GSDB; ?></th>
            <th scope="col"></th>
          </tr>
          <tr>
            <th scope="col">(c)</th>
            <th scope="col">Tax on employment under section 16(iii) </th>
            <th scope="col"><?php $GSDC = "0.00"; echo $GSDC; ?></th>
            <th scope="col"></th>
          </tr>
          <tr>
            <th scope="col">5</th>
            <th scope="col">Total amount of deductions under section 16 [4(a)+4(b)+4(c)]</th>
            <th scope="col"></th>
            <th scope="col"><?php $GSE = $GSDA+$GSDB+$GSDC; echo $GSE; ?></th>
          </tr>
          <tr>
            <th scope="col">6</th>
            <th scope="col">Income chargeable under the head "Salaries" [(3+1(e)-5]</th>
            <th scope="col"></th>
            <th scope="col"><?php $GSF = $GSC+$GSAE-$GSE; echo $GSF; ?></th>
          </tr>
          <tr>
            <th scope="col">7</th>
            <th scope="col" colspan="3"> Add: Any other income reported by the employee under as per section 192 (2B)
            </th>
          </tr>
          <tr>
            <th scope="col">(a)</th>
            <th scope="col">Income (or admissible loss) from house property reported by employee offered for TDS </th>
            <th scope="col"><?php $GSGA = "0.00"; echo $GSGA; ?></th>
            <th scope="col"></th>
          </tr>
          <tr>
            <th scope="col">(b)</th>
            <th scope="col">Income under the head Other Sources offered for TDS</th>
            <th scope="col"><?php $GSGB = "0.00"; echo $GSGB; ?></th>
            <th scope="col"></th>
          </tr>

          <tr>
            <th scope="col">8</th>
            <th scope="col">Total amount of other income reported by the employee[7(a)+7(b)] </th>
            <th scope="col"></th>
            <th scope="col"><?php $GSH = $GSGA+$GSGB; echo $GSH; ?></th>
          </tr>
          <tr>
            <th scope="col">9</th>
            <th scope="col">Gross total income (6+8)</th>
            <th scope="col"></th>
            <th scope="col"><?php $GSI = $GSGF+$GSGH; echo $GSI; ?></th>
          </tr>
          <tr>
            <th scope="col">10</th>
            <th scope="col">Deductions under Chapter VI-A</th>
            <th scope="col">Gross Amount</th>
            <th scope="col">Deductible Amount</th>
          </tr>
          <tr>
            <th scope="col">(a)</th>
            <th scope="col">Deduction in respect of life insurance premia, contributions to provident fund etc. under section 80C </th>
            <th scope="col"></th>
            <th scope="col"></th>
          </tr>
          <tr>
            <th scope="col">(b)</th>
            <th scope="col">Deduction in respect of contribution to certain pension funds under section 80CCC</th>
            <th scope="col"></th>
            <th scope="col"></th>
          </tr>
          <tr>
            <th scope="col">(c)</th>
            <th scope="col">Deduction in respect of contribution by taxpayer to pension scheme under section 80CCD (1) </th>
            <th scope="col"></th>
            <th scope="col"></th>
          </tr>
          <tr>
            <th scope="col">(d)</th>
            <th scope="col">Total deduction under section 80C, 80CCC and 80CCD(1)</th>
            <th scope="col"></th>
            <th scope="col"></th>
          </tr>
          <tr>
            <th scope="col">(e)</th>
            <th scope="col">Deductions in respect of amount paid/deposited to notified pension scheme under section 80CCD (1B)</th>
            <th scope="col"></th>
            <th scope="col"></th>
          </tr>
          <tr>
            <th scope="col">(f)</th>
            <th scope="col">Deduction in respect of contribution by Employer to pension scheme under section 80CCD (2)</th>
            <th scope="col"></th>
            <th scope="col"></th>
          </tr>
          <tr>
            <th scope="col">(g)</th>
            <th scope="col">Deduction in respect of health insurance premia under section 80D</th>
            <th scope="col"></th>
            <th scope="col"></th>
          </tr>
          <tr>
            <th scope="col">(h)</th>
            <th scope="col">Deduction in respect of interest on loan taken for higher education under section 80E</th>
            <th scope="col"></th>
            <th scope="col"></th>
          </tr>
          <tr>
            <th scope="col">11</th>
            <th scope="col">Aggregate of deductible amount under Chapter VI-A [10(d)+10(e)+10(f)+10(g)+10(h)+10(i)+10(j)+10(l)]</th>
            <th scope="col"></th>
            <th scope="col"><?php $GSK = $totalit; echo $GSK; ?></th>
          </tr>
          <tr>
            <th scope="col">12</th>
            <th scope="col">Total taxable income (9-11)</th>
            <th scope="col"></th>
            <th scope="col"><?php $GSL = $GSI-$GSK; echo $GSL; ?></th>
          </tr>
          <tr>
            <th scope="col">13</th>
            <th scope="col">Tax on total income</th>
            <th scope="col"></th>
            <th scope="col"><?php $GSM = $GSI-$GSK; echo $GSM; ?></th>
          </tr>
          <tr>
            <th scope="col">14</th>
            <th scope="col">Rebate under section 87A, if applicable</th>
            <th scope="col"></th>
            <th scope="col"><?php $GSN = "0.00"; echo $GSN; ?></th>
          </tr>
          <tr>
            <th scope="col">15</th>
            <th scope="col">Surcharge, wherever applicable</th>
            <th scope="col"></th>
            <th scope="col"><?php $GSO = "0.00"; echo $GSO; ?></th>
          </tr>
          <tr>
            <th scope="col">16</th>
            <th scope="col">Health and education cess</th>
            <th scope="col"></th>
            <th scope="col"><?php $GSP = "0.00"; echo $GSP; ?></th>
          </tr>
          <tr>
            <th scope="col">17</th>
            <th scope="col">Tax payable (13+15+16-14)</th>
            <th scope="col"></th>
            <th scope="col"><?php $GSQ = ((($GSM+$GSO)+$GSP)-$GSN); echo $GSQ; ?></th>
          </tr>
          <tr>
            <th scope="col">18</th>
            <th scope="col">Less: Relief under section 89 (attach details)</th>
            <th scope="col"></th>
            <th scope="col"><?php $GSR = "0.00"; echo $GSR; ?></th>
          </tr>
          <tr>
            <th scope="col">19</th>
            <th scope="col">Net tax payable (17-18)</th>
            <th scope="col"></th>
            <th scope="col"><?php $GSS = $GSQ-$GSR; echo $GSS; ?></th>
          </tr>
        </table>

        <table class="myTable table table-hover m-b-0 mt-5" >
          <tr>
                 <th scope="col" colspan='6' class="tac">
                    Verification
                 </th>
              </tr>
              <tr>
                 <td colspan='6'>
                    I, <u><?php echo $row['employee_name']; ?> </u> , son/daughter of <?php echo $row['garden']; ?> </u> working in the capacity of <?php echo $row['designation']; ?> </u> (designation) do hereby certify that the information given above is true, complete and correct and is based on the books of account,documents, TDS statements, and other available records..
                 </td>
              </tr>
              <tr>
                 <th scope="col">
                    Place
                 </th>
                 <td colspan="2">
                  <?php echo $row['place']; ?>
                 </td>
                 <th scope="col" colspan="3">

                 </th>
              </tr>
              <tr>
                 <th scope="col">
                    Date
                 </th>
                 <td colspan="2">
                  <?php echo date($formate, strtotime($row['place_date'])); ?>
                 </td>
                 <th scope="col" colspan="3">
                   (Signature of person responsible for deduction of tax)
                 </th>
              </tr>
              <tr>
                 <th scope="col" colspan="3">
                    <b>Designation: </b>
                    <?php echo $row['designation']; ?>
                 </th>
                 <th scope="col" colspan="3">
                    <b>Full Name:</b>
                    <?php echo $row['employee_name']; ?>
                 </th>
              </tr>
        </table>
      </div>
    </div>
</div>
</div>

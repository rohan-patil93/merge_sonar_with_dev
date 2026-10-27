<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$datarela["where"] = array('is_active'=> '0','transaction_number'=>'LE112');
  $costdatarela = urlencode(json_encode($datarela));
  $relaurl = API."?mode=gettabledetailswithconditions&table=letters&data=".$costdatarela;
  $relatypedata = callAPI($relaurl);
  $relatypedata = json_decode($relatypedata,1);

  $data["where"] = array('is_active'=> '0','id'=>'4');
  $costdata = urlencode(json_encode($data));
  $url = API."?mode=gettabledetailswithconditions&table=salary_template&data=".$costdata;
  $salarydata = callAPI($url);
  $salarydata = json_decode($salarydata,1);
 $salarydata = $salarydata[0];
   $stpdata["where"] = array('is_active'=> '0','salary_template_id'=>'4');
   $stpdata["order"] = "component_cat_id ASC";

  $stpmdata = urlencode(json_encode($stpdata));
  $stpurl = API."?mode=gettabledetailswithconditions&table=salary_template_components&data=".$stpmdata;
  $salarytmpdata = callAPI($stpurl);
  $salarytmpdata = json_decode($salarytmpdata,1);

$datacat["where"] = array('is_active'=> '0');
  $catdata = urlencode(json_encode($datacat));
  $caturl = API."?mode=gettabledetailswithconditions&table=salary_catagories&data=".$catdata;
  $salarycatdata = callAPI($caturl);
  $salarycatdata = json_decode($salarycatdata,1);
  foreach ($salarycatdata as $cate) {
      $cat[$cate['id']] = $cate['name'];
  }
  $ledata = $relatypedata['0'];
  $send_data['display_name'] = "Ravi Shankar";
   $send_data['company_name'] = "Infisuite Technologies LLP";
    $send_data['job_title'] =  "Developer";
     $send_data['start_date'] = "12th Aug 2020";
     $send_data['amount'] = "8.5 Lack"
?>

<script type="text/javascript">
    function printForm() {

       var divToPrint =document.getElementById('printarea');
      var newWin=window.open('','Print-Window');
      newWin.document.open();
      newWin.document.write('<html lang="en"><head><title>Offer Letter</title></head><body onload="window.print()">'+divToPrint.outerHTML+'</body></html>');
      newWin.document.close();
      setTimeout(function(){newWin.close();},10);

    }
</script>
<div class="modal-header">
                        <h5 class="modal-title">
                            Offer letter
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" >
                        <div class="row align-items-center" style="margin-top: 10px;padding: 0px 10%;" id="printarea">
                                <div class="col-lg-12">
                                    <?php echo $diss = emaildatareplace($send_data, base64_decode($ledata['content'])); ?>

                                </div>
                                <div  class="col-lg-12" style="page-break-before: always;">
                                <strong style="font-size: 18px; color: #000;  border-bottom: 2px solid #000; padding-bottom: 5px;">
                                COST TO COMPANY CALCULATIONS w.e.f November 11, 2020</strong>
        <table style="border: 1px solid #000;width: 100%;padding: 5px; margin: 20px 0px; margin-left: auto; margin-right: auto;" role="presentation">
            <tr style="height: 50px;border: 1px solid #000;">
                <td style="border:1px solid #000;padding: 5px;"><b>Name</b></td>
                <td  style="border:1px solid #000;padding: 5px;"colspan="2">&nbsp;<?php echo  $send_data['display_name']; ?></td>
            </tr>
            <tr style="height: 50px;border: 1px solid #000;">
                <td style="border:1px solid #000;padding: 5px;"><b>Designation</b></td>
                <td style="border:1px solid #000;padding: 5px;" colspan="2">&nbsp;<?php echo $send_data['job_title']; ?></td>
            </tr>
            <tr style="border: 1px solid #000;">
                <td style="border:1px solid #000;padding: 5px;"><b>COMPENSATION DETAILS</b>                </td>
                <td style="text-align: center;border:1px solid #000;padding: 5px;"><b>MONTHLY</b>        </td>
                <td style="text-align: center;border:1px solid #000;padding: 5px;"><b>YEARLY</b>    </td>
            </tr>
            <?php $nm=0; foreach ($salarytmpdata as  $stvalue) { ?>
                <?php if($stvalue['component_cat_id'] !=$nm ){ $nm =$stvalue['component_cat_id'];  ?>
                <tr style="border: 1px solid #000;" >
                    <td style="font-size: 14px;border:1px solid #000;padding: 5px;">&nbsp;  <?php echo $cat[$nm];?></td>
                    <td style="border:1px solid #000;padding: 5px;"> </td>
                    <td style="border:1px solid #000;padding: 5px;">  </td>
                </tr>
                <?php } ?>
                <tr style="border: 1px solid #000;" >
                    <td style="border:1px solid #000;padding: 5px;" > &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $stvalue['component_name'];?></td>
                    <td style="text-align: center;border:1px solid #000;padding: 5px;"> <?php echo customeummarmonyformate($stvalue['monthly_amount']);?></td>
                    <td style="text-align: center;border:1px solid #000;padding: 5px;"><?php echo customeummarmonyformate($stvalue['yearly_amount']);?></td>
                </tr>
            <?php } ?>
                <tr style="border: 1px solid #000;" >
                    <td style="border:1px solid #000;padding: 5px;" ><?php echo "Total";?></td>
                    <td style="text-align: center;border:1px solid #000;padding: 5px;"> <?php echo customeummarmonyformate($salarydata['company_cost_monthly']);?></td>
                    <td style="text-align: center;border:1px solid #000;padding: 5px;"><?php echo customeummarmonyformate($salarydata['company_cost_yearly']);?></td>
                </tr>
                <tr style="border: 1px solid #000;" >
                    <td colspan="3" style="border:1px solid #000;padding: 5px;">&nbsp;  Notes: </br>
                        <p style="padding: 0px 5%;"><?php echo  $salarydata['note']; ?></p>
                    </td>

                </tr>
        </table>
    </div>
    <div style="width: 50%; display: inline-block; margin: 30px 0 0 0;">
        <b style="font-size: 14px; color: #000; font-family: 'Noto Sans'; margin:0; display: block;">With best wishes.<br />For <?php echo  $send_data['company_name']; ?></b>
    </div>
    <div style="width: 48%; display: inline-block; margin: 30px 0 0 0; text-align: right;">
        <b style="font-size: 14px; color: #000; font-family: 'Noto Sans'; margin:0; display: block;">ANJANA AUDUKOORI<br />DIRECTOR HR</b>
    </div>
    <div style="width: 50%; display: inline-block; margin: 40px 0 0 0;">
        <b style="font-size: 14px; color: #888; font-family: 'Noto Sans'; margin:0; display: block;">-----------------------------------------------</b>
        <b style="font-size: 14px; color: #666; font-family: 'Noto Sans'; margin:0; display: block;">Candidate Signature</b>
    </div>
    <div style="width: 48%; display: inline-block; margin: 40px 0 0 0; text-align: right;">
        <b style="font-size: 14px; color: #888; font-family: 'Noto Sans'; margin:0; display: block;">-----------------------------------------------</b>
        <b style="font-size: 14px; color: #666; font-family: 'Noto Sans'; margin:0; display: block;">Date</b>
    </div>

                                </div>

            </div>
        </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary font-weight-bold" onclick="printForm()">Print Invoice</button>
                <input type="hidden" name="kpi_id" id="kpi_id" value="<?php echo $_POST['id']; ?>">
                <input type="hidden" name="page" value="dis_submit">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="submitrom()" >Submit</button>

            </div>

    </form>
</div><

<?php
    $from_cur_year = date('Y');
    $from_cur_month = date('m');
    if(isset($_POST['applyform'])) {
        $from_cur_year = date('Y', strtotime($_POST['from_date']));
        $from_cur_month = date('m', strtotime($_POST['from_date']));
    }
    $url = API."?mode=getholdsalrylist&admin_type=".$_SESSION['admin_type']."&pay_year=".$from_cur_year."&pay_month=".$from_cur_month;
    echo $url;
    $details = callAPI($url);
    $detail = json_decode($details,1);
    $data = $detail['data'];
?>
<script type="text/javascript">
    function clickall(val){
        if (val != 0) {
           $('#checkAll').val('0');
           $(".itemRow").prop("checked", true);
           $("#genrateparoll").show();
        }else{
            $('#checkAll').val('1');
            $(".itemRow").prop("checked", false);
            $("#genrateparoll").hide();
        }
    }
    function checkoncetime(){
        var ick=0;
        $.each($("input[name='checkbox1']:checked"), function(){
            ick=ick+1;
        });
        if(ick>0){
            $("#genrateparoll").show();
        }else{
            $("#genrateparoll").hide();
        }
        console.log('ick:'+ick);
    }
    function genratepayroll(){
        var favorite = [];
        $.each($("input[name='checkbox1']:checked"), function(){
            favorite.push($(this).val());
        });
        console.log("ANS:"+favorite);
        $.ajax({
        url: "ajax.php",
        type: "POST",
        data: 'page=insertholdsalriesparoll&empids='+favorite+'&pay_year='+$('#pay_year').val()+'&pay_month='+$('#pay_month').val()+'&pay_area_id='+$('#pay_area_id').val(),
        success: function(response){
          console.log(response);
          tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][3001]; ?>','');

        }
      });

    }
</script>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="card card-custom card-sticky" id="kt_page_sticky_card">
            <div class="card-header">
                <h5> Hold Salaries </h5>
                <div class="card-header-right">
                    <form class="kt-form" id="kt_form" method="post" action="" >
                        <div class="input-group input-group-button">
                            <input aria-label="From Date" type="month" name="from_date" value="<?php echo $from_cur_year."-".$from_cur_month?>"  id="att_month_year"  class="form-control form-control-sm width1" style="font-size: 14px;height: 40px !important;padding: 10px 15px;">  <button type="submit" name="applyform" value="apply" class="btn btn-info waves-effect waves-light input-group-text mt-0">
                                Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="text-right">
                    <div id="genratepayroll">
                            <button type="button" id="genrateparoll" onclick="genratepayroll()" class="btn btn-primary" style="display:none;" >
                                Genrate Payroll
                            </button>
                    </div>
                </div>
                <?php if(count((array)$data)>0){ ?>
                <div class="row dt-responsive table-responsive">
                    <table class="data-table" style="width:100%">
                        <thead>
                        <tr>
                            <th scope="col" class="text-center">
                                <div class="checkbox-fade fade-in-primary ">
                     <label class="form-label">
                        <input aria-label="Check All"  id="checkAll"  type="checkbox" value="1" onclick="clickall(this.value)" onkeydown="">
                        <span class="cr">
                          <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                       </span>
                    </label>
                </div>
                            </th>
                            <th scope="col">
                                    Employee
                            </th>
                            <th scope="col">
                                    Email
                            </th>
                            <th scope="col">
                                    Pay Area
                            </th>
                            <th scope="col">
                                    Month - Year
                            </th>
                        </tr>
                        </thead>
                        <tbody class="m-t-5">
                        <?php foreach((array)$data as $dat){ ?>
                        <tr >
                            <td class="text-center">
                                <div class="checkbox-fade fade-in-primary ">
                      <label class="form-label">
                        <input aria-label="Checkbox" type="checkbox" class="itemRow" value="<?php echo $dat['emp_id']; ?>" id="check_<?php echo $i; ?>" type="checkbox" name="checkbox1" onchange="checkoncetime()">
                        <span class="cr">
                          <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                        </span>
                      </label>
                  </div>
                            </td>
                            <td>
                                    <img src="uploads/all/<?php echo $dat['image'];  ?>" class="img-radius img-50 align-top m-r-15" alt="Attachment Preview">
                                    <a class="cursor-pointer" >
                                        <?php echo $dat['display_name'];  ?>
                                        <?php echo $dat['emp_code'];  ?>
                                    </a>
                            </td>
                            <td>
                                    <?php echo $dat['email_id'];  ?>
                            </td>
                            <td>
                                    <?php echo $dat['pay_area_name'];  ?>
                            </td>
                            <td>
                                    <?php echo date('M', mktime(0, 0, 0, $dat['pay_month'], 1))." - ".$dat['pay_year'];  ?>
                            </td>
                        </tr>
                        <?php
                            $pay_year = $dat['pay_year'];
                            $pay_month = $dat['pay_month'];
                            $pay_area_id = $dat['pay_area_id'];
                         } ?>
                        </tbody>
                    </table>
                </div>
                <input type="hidden" name="" id="pay_year" value="<?php echo $pay_year; ?>">
                <input type="hidden" name="" id="pay_month" value="<?php echo $pay_month; ?>">
                <input type="hidden" name="" id="pay_area_id" value="<?php echo $pay_area_id; ?>">

                <?php }else{ ?>
                    <div class="card-px text-center py-20 m-t-30">
                        <h2 class="f-40 fw-bolder m-b-30">Hello!</h2>
                        <p class="text-muted f-16 fw-bold m-b-30">There are no records found.</p>
                        <div class="text-center px-4">
                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
<div>

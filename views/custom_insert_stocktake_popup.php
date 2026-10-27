<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

      $url = API."?mode=getstocktakesuom&item_id=".$_POST['item_id']."&sid=".$_POST['sid']."&uom_id=".$_POST['uom_id']."&location_id=".$_POST['location_id'];
    $data_details = callAPI($url);
    $details = json_decode($data_details,1);
    $uomlists=$details['uomlist'];
    $stocktake_detail = $details['stocktake_detail'];
   $uom_details = json_decode($stocktake_detail['uom_details'],1);
   $uomdetails = array();
   foreach($uom_details as $uom_details){
           $uomdetails[$uom_details['uom_id']] =$uom_details;
   }

?>
<script type="text/javascript">
    function uomenterqty(){
        var popuomqty= [];
        var totalamt = 0;
        $(".popuomqty").each(function( index ) {
            popuomqty[index] = $(this).val();
        });
        var popuomratio= [];
        $(".popuomratio").each(function( index ) {
            popuomratio[index] = $(this).val();
        });
        $(".popuomqty").each(function( index ){
            var popuomq = popuomqty[index]!=""?popuomqty[index]:0;
            var popuomr = popuomratio[index]!=""?popuomratio[index]:0;
            totalamt += parseInt(popuomq)*parseInt(popuomr);
        });
        $("#poptotalamt").text(totalamt);
        $("#poptotalatytext").val(totalamt);

    }
    function submituomqty(){
        var item_id = $("#item_idd").val();
        var rowid = $("#rowid").val();
        var sub = $("#uomform").serialize();
        var poptotalatytext = $("#poptotalatytext").val();
        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: 'page=insertstocktakesitems&type=uom&+'+sub,
            success: function(response) {
                console.log(response);
                $("#count_qty_"+rowid).val(poptotalatytext);
                $("#uom_details_"+rowid).val(response);

                $(".popup-uom-"+rowid).html(" ");
                 jQuery(".popup-uom-"+rowid).toggle();

            }
        });

    }
</script>

<div class="card-block">
<form id="uomform">
    <input type="hidden" name="stocktake_id" value="<?php echo $_POST['sid']; ?>">
    <input type="hidden" name="item_id" id="item_idd" value="<?php echo $_POST['item_id']; ?>">
<input type="hidden" name="rowid" id="rowid" value="<?php echo $_POST['rowid']; ?>">
<input type="hidden" name="location_id" id="location_id" value="<?php echo $_POST['location_id']; ?>">

    <ul class="row text-center">
        <?php foreach($uomlists as $uomlist){
        $uom_detail = $uomdetails[$uomlist['id']];
        ?>

<li class="m-l-15">
    <div class="d-inline-block">
        <label for=""><?php echo ucwords($uomlist['description']); ?></label>
        <input aria-label="Popupqty[]" type="text" name="popupqty[]"  class="form-control text-right popuomqty" value="<?php echo $uom_detail['qty'];?>"  onblur="uomenterqty()">
        <input type="hidden" class="popuomratio" id="conversion_ratio" name="conversion_ratio[]" value="<?php echo $uomlist['conversion_ratio']; ?>">
        <input type="hidden"   name="popuouomid[]" value="<?php echo $uomlist['id']; ?>">
       </div>
    <div class="m-r-10 m-l-10 d-inline-block">
        <b class="f-24">+</b>
    </div>
</li>
       <?php  } ?>

        <li class="m-l-15">
            <div class="d-inline-block">
                 <span><?php echo ucwords($_POST['uom_name']); ?></span>
                <input aria-label="Popupqty[]" type="text" name="popupqty[]"  onblur="uomenterqty()" class="form-control text-right popuomqty" value="<?php echo $key_detail['billable_hrs'];?>">
                <input type="hidden" id="conversion_ratio_2" class="popuomratio" name="conversion_ratio[]" value="1">
                <input type="hidden"   name="popuouomid[]" value="<?php echo $_POST['uom_id']; ?>">
            </div>
            <div class="m-r-10 m-l-10 d-inline-block">
                <b class="f-24">=</b>
            </div>
        </li>

        <li>
            <span >TOTAL</span>
            <p id="poptotalamt"><?php echo $_POST['count_qty']!=""?$_POST['count_qty']:'0';?></p>
            <input type="hidden"   name="poptotalqty" id="poptotalatytext" value="<?php echo $_POST['count_qty']; ?>">
        </li>
       </ul>
       </form>
        <div class="col-lg-12 text-right mt-2">
            <button class="btn waves-effect waves-light hor-grd btn-grd-primary" type="button" onclick="submituomqty()">Save</button>
            <button class="btn waves-effect waves-light hor-grd btn-grd-danger " type="button" onclick="uompopup('','','','','<?php echo $_POST['rowid']; ?>')">Cancel</button>
        </div>
    </div>

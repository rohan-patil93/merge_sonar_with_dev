<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha384-ZvpUoO/+PpLXR1lu4jmpXWu80pZlYUAfxl5NsBMWOEPSjUn/6Z/hRTt8+pR6L4N2" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js" integrity="sha384-rf/ztXfEY2pet3f+/ISCMJDpNcNxQ7yx3pe4GtfOe32+0cj6XARNKeKbbj4KGpND" crossorigin="anonymous"></script>
    <script>
        function uploadFile() {
            const fileInput = document.getElementById('fileInput');
            const file = fileInput.files[0];

            if (!file) {
                alert('Please select a file first!');
                return;
            }
            $('#showdetailear').html('<img src="assets/images/spiner/spiner5.gif" alt="Loading...">').show();
            const reader = new FileReader();

            reader.onload = function(e) {
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, { type: 'array' });

                // Assuming the first sheet is the one we want
                const firstSheetName = workbook.SheetNames[0];
                const worksheet = workbook.Sheets[firstSheetName];

                // Convert sheet to JSON
                const jsonData = XLSX.utils.sheet_to_json(worksheet);
                console.log(jsonData);

                // Send the data to the backend using AJAX
                sendDataToServer(jsonData);
            };

            reader.readAsArrayBuffer(file);
        }

        function sendDataToServer(data) {
            var y= $('#year').val();
            var m= $('#month').val();
            var pay_area_id= $('#pay_area_id').val();
            jQuery.ajax({
                url: "ajax.php",
                data:'page=payrollupdateonetime&type=earnings&data='+JSON.stringify(data)+'&pay_year='+y+'&pay_month='+m+'&pay_area_id='+pay_area_id,
                type: "POST",
                success:function(data){
                      console.log(data);
                      tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Data uploaded successfully','');
                     showotherearningdata(pay_area_id,y,m,'earnings');
                },
                error:function (){}
            });
        }
        function showotherearningdata(pay_area_id,y,m,type){
            jQuery.ajax({
                url: "views/custom_view_other_earning_ajax.php",
                data:'option=payrollupdateonetime&type=earnings&pay_year='+y+'&pay_month='+m+'&pay_area_id='+pay_area_id,
                type: "POST",
                success:function(data){

                  $("#showdetailear").html(data);
                },
                error:function (){}
            });
        }
    </script>

<div class="card card-custom gutter-b">
    <div class="card-header border-0 ">
        <div class="card-header table-card-header">
            <h5>  Update Onetime Earnings</h5>
        </div>
    </div>
    <div class="card-body py-0">
        <div class="card-body">
            <form class="kt-form" id="kt_form1" method="post" action="" name="myForm2" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-xl-8">
                        <div class="form-group row">
                            <span class="col-4">Update Onetime</span>
                            <div class="col-8">
                                <div class="custom-file">
                                    <input aria-label="File" type="file" class="form-control fill" id="fileInput" name="file">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row mt-2">
                            <span class="col-4">
                                &nbsp;
                            </span>
                            <div class="col-5">
                                <span> Sample CSV file for <a target="_blank" class="text-primary" href="csv_download.php?table=employee_other_earnings"> Download</a></span>
                            </div>
                            <div class="col-3 text-right">

                                 <button type="button" onclick="uploadFile()" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2" >Upload</button>

                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="submit_earning" value="submit">
                <input type="hidden" name="pay_year" id="year" value="<?php echo $_GET['PAY_YEAR']; ?>">
                <input type="hidden" name="pay_month" id="month" value="<?php echo $_GET['PAY_MONTH']; ?>">
                <input type="hidden" name="type" id="type" value="earnings">
            </form>
        </div>
    </div>
    <div class="card-body py-0" id="showdetailear">

        <?php include_once 'custom_view_other_earning_ajax.php';?>
    </div>
</div>

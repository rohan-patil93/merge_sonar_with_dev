<div class="">
  <div class="">
    <div class="card card-custom">
        <div class="card-header table-card-header">
            <h5>  My Calendar</h5>
            <div class="card-header-right table-card-header ">
               <input type="hidden" name="calendar_cat" id="calendar_cat" value="1-2-3-4-5-6">
            </div>
        </div>
        <div class="card-body">
            <div id='calendar'></div>
        </div>
    </div>
  </div>
</div>


<?php if($_POST['ajaxpage'] == 'Y'){?>

<script type="text/javascript" src="assets/bower_components/moment/js/moment.min.js"></script>
<script type="text/javascript" src="assets/bower_components/fullcalendar/js/fullcalendar.min.js"></script>
<script type="text/javascript" src="assets/pages/full-calender/calendar.js"></script>
<?php } ?>



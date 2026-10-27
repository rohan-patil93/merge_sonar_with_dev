

 <?php include 'header.php';?>

    <div class="row preview">
        <div class="col-md-12">
           <p>This is preview mode. You can't submit assesment.</p>
        </div> 
    </div> 
    <div class="row question-1">
        <div class="col-md-6">
            <h2>INFISTAQ</h2>
        </div>
        <div class="col-md-6">
            <div class="countdown float-right">
               <span class="fas fa-stopwatch"><b>Timing Remaining</b></span> 
               <p id="timer"></p>
            </div>   
        </div>
    </div>
    <div class="row q-sec">    
        <div class="col-md-8">
            <div class="q-a">
                <span>This is a single choise question.</span>
                <h4>question. 1</h4>
                <h5>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</h5>
                <b>
                    <input type="radio" name="q1" id="q1">
                    <label for="q1">Lorem Ipsum is simply dummy text of the printing and</label>
                </b>
                <b>
                    <input type="radio" name="q1" id="q1_2">
                    <label for="q1_2">Lorem Ipsum is simply dummy text of the printing and</label>
                </b>
                <b>
                    <input type="radio" name="q1" id="q1_3">
                    <label for="q1_3">Lorem Ipsum is simply dummy text of the printing and</label>
                </b>
                <b>
                    <input type="radio" name="q1" id="q1_4">
                    <label for="q1_4">Lorem Ipsum is simply dummy text of the printing and</label>
                </b>
                <b>
                    <input type="radio" name="q1" id="q1_5">
                    <label for="q1_5">Lorem Ipsum is simply dummy text of the printing and</label>
                </b>
                <div class="submit-sec">
                    <a href="question-2-text.php">Save & Next <i class="fas fa-chevron-right"></i></a>
                    <a class="response" href="">clear Response</a>
                    <input class="float-right" type="submit" name="" value="Submit Assesment">
                </div>
            </div>     
        </div>
        
        <div class="col-md-4">
            <div class="q-n">
                <ul>
                    <li class="active"><a href="question-1-text.php">1</a></li>
                    <li><a href="question-2-text.php">2</a></li>
                    <li><a href="">3</a></li>
                    <li><a href="">4</a></li>
                    <li><a href="">5</a></li>
                    <li><a href="">6</a></li>
                    <li><a href="">7</a></li>
                    <li><a href="">8</a></li>
                    <li><a href="">9</a></li>
                    <li><a href="">10</a></li>
                    <li><a href="">11</a></li>    
                    <li><a href="">12</a></li>
                    <li><a href="">13</a></li>
                    <li><a href="">14</a></li>
                    <li><a href="">15</a></li>
                    <li><a href="">16</a></li>
                    <li><a href="">17</a></li>
                    <li><a href="">18</a></li>
                    <li><a href="">19</a></li>
                    <li><a href="">20</a></li>
                </ul>
                <div class="row">
                    <input type="checkbox" name="" id="a1">
                    <label for="a1">Answered Question</label>
                </div>
                <div class="row">
                    <input type="checkbox" name="" id="a2">
                    <label for="a2">Unanswered and visited</label>
                </div>    
                <div class="row">
                    <input type="checkbox" name="" id="a3">
                    <label for="a3">Unanswered and not-visited</label>
                </div>
            </div>    
        </div> 
    </div>   
	
	
<script>
// Set the date we're counting down to
var countDownDate = new Date("Sep 9, 2020 07:0:0").getTime();

// Update the count down every 1 second
var x = setInterval(function() {

  // Get today's date and time
  var now = new Date().getTime();
    
  // Find the distance between now and the count down date
  var distance = countDownDate - now;
    
  // Time calculations for days, hours, minutes and seconds
  
  var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
  var seconds = Math.floor((distance % (1000 * 60)) / 1000);
    
  // Output the result in an element with id="demo"
  document.getElementById("timer").innerHTML =  minutes + "m " + seconds + "s ";
    
  // If the count down is over, write some text 
  if (distance < 0) {
    clearInterval(x);
    document.getElementById("timer").innerHTML = "EXPIRED";
  }
}, 1000);
</script>

<!--begin::Footer-->
<?php include 'footer.php';?>
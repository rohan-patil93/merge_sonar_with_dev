
<!-- include in a container a heading, paragraph and svg for the keyhole -->
<?php  //echo "<pre>"; print_r($_SESSION['error_message']);
    if(!isset($_GET['error'])){
        $_GET['error'] = "403";
      } 
?>
<div class="image"></div>

        <!-- Your logo on the top left -->
        <a href="#" class="logo-link" title="back home">

            <img src="img/logo.png" class="logo" alt="Company's logo" />

        </a>

 <div class="content">

            <div class="content-box">

                <div class="big-content">

                    <!-- Main squares for the content logo in the background -->
                    <div class="list-square">
                        <span class="square"></span>
                        <span class="square"></span>
                        <span class="square"></span>
                    </div>

                    <!-- Main lines for the content logo in the background -->
                    <div class="list-line">
                        <span class="line"></span>
                        <span class="line"></span>
                        <span class="line"></span>
                        <span class="line"></span>
                        <span class="line"></span>
                        <span class="line"></span>
                    </div>

                    <!-- The animated searching tool -->
                    <i class="fa fa-search" aria-hidden="true"></i>

                    <!-- div clearing the float -->
                    <div class="clear"></div>

                </div>

                <!-- Your text -->
                <h1>Oops! Error 404 not found.</h1>

                <p>The page you were looking for doesn't exist.<br>
                    We think the page may have moved.</p>

            </div>

        </div>
<script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
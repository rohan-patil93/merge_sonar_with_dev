<?php
	if(isset($_POST['login'])){
		$username = $_POST['username'];
		$password = $_POST['password'];
		$captcha = $_POST['captcha'];

		if($username == "admin" && $password == "admin"){
			if($_SESSION['captcha'] == $captcha){
				echo "<div class='text-center'><span class='text-success'>Login successfully</span></div>";
			}else{
				echo "<div class='text-center'><span class='text-danger'>Invalid captcha!</span></div>";
			}
		}else{
			echo "<div class='text-center'><span class='text-danger'>Invalid username or password</span></div>";
		}
	}
?>
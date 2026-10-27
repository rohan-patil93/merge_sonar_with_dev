<!DOCTYPE html>
<?php session_start()?>
<html lang="en">
	<head>
		<meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.css"/>
	</head>
<body>
	<div class="col-md-3"></div>
	<div class="col-md-6 well">
		<h3 class="text-primary">PHP - Simple Login With Captcha</h3>
		<hr style="border-top:1px dotted #ccc;"/>
		<div class="col-md-3">
			<h5>Default user</h5>
			<h5>Username: admin</h5>
			<h5>Password: admin</h5>
		</div>	
		<div class="col-md-6">
			<form action="" method="POST">
				<div class="form-group">
					<label>Username</label>
					<input type="text" name="username" class="form-control" required="required"/>
				</div>
				<div class="form-group">
					<label>Password</label>
					<input type="password" name="password" class="form-control" required="required"/>
				</div>
				<center><img src="captcha.php" /></center>
				<br />
				<br />
				<div class="form-group">
					<input type="text" class="form-control" name="captcha" required="required"/>
				</div>
				<?php include 'login.php'?>
				<center><button name="login" class="btn btn-primary"><span class="glyphicon glyphicon-log-in"></span> login</button></center>
			</form>
		</div>	
	</div>
</body>
</html>
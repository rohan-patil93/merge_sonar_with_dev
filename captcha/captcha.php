<?php
	session_start();

	$random = random_int(1, 9).random_int(1, 9).random_int(1, 9).random_int(1, 9);

	$_SESSION['captcha'] = $random;

	$captcha = imagecreatefromjpeg("images/captcha.jpg");
	$color = imagecolorallocate($captcha, 0, 0, 0);
	$font = realpath('code.otf');
	imagettftext($captcha, 20, 0, random_int(30, 180), random_int(20, 70), $color, $font, $random );
	imagepng($captcha);
	imagedestroy($captcha);

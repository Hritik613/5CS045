<?php
session_start();

$code = rand(1000, 9999);
$_SESSION['captcha'] = $code;

header('Content-Type: image/png');
$image = imagecreatetruecolor(120, 40);
$bg = imagecolorallocate($image, 255, 255, 255);
$txt = imagecolorallocate($image, 0, 0, 0);
imagefilledrectangle($image, 0, 0, 120, 40, $bg);
imagestring($image, 5, 35, 10, $code, $txt);
imagepng($image);
imagedestroy($image);
?>

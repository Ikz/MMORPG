<?php

function parse_bgcolor()
{
  if(strlen($_GET['bgcolor']) == 6)
  {
  $p1=$_GET['bgcolor'][0].$_GET['bgcolor'][1];
  $p2=$_GET['bgcolor'][2].$_GET['bgcolor'][3];
  $p3=$_GET['bgcolor'][4].$_GET['bgcolor'][5];
  }
  else
  {
  $p1=$_GET['bgcolor'][0].$_GET['bgcolor'][0];
  $p2=$_GET['bgcolor'][1].$_GET['bgcolor'][1];
  $p3=$_GET['bgcolor'][2].$_GET['bgcolor'][2];
  }
  return array(hexdec($p1),hexdec($p2),hexdec($p3));
}
session_start();
$bgcolor=($_GET['bgcolor']) ? parse_bgcolor() : array(255,255,255);
$text=array(255-$bgcolor[0],255-$bgcolor[1],255-$bgcolor[2]);
$distort=rand(80,120)/100;
$distort2=rand(80,120)/100;
$f_x=round(75*$distort);
$f_y=round(25*$distort);
$s_x=round(175*$distort2);
$s_y=round(70*$distort2);
$first=imagecreatetruecolor($f_x, $f_y);
$second=imagecreatetruecolor($s_x, $s_y);
$white=imagecolorallocate($first, $bgcolor[0], $bgcolor[1], $bgcolor[2]);
$black=imagecolorallocate($first, 0, 0, 0);
$red=imagecolorallocate($first, 255, 0, 0);
$green=imagecolorallocate($first, 0, 128, 0);
$blue=imagecolorallocate($first, 0, 0, 255);
imagefill($first, 0, 0, $white);
$color[0]=$red;
$color[1]=$green;
$color[2]=$blue;
for($i=0; $i<=2; $i++)
{
  $points = array(
  0 => array(10, $f_x - 10),
  1 => array(5, $f_y - 5),
  2 => array(10, $f_x - 10),
  3 => array(5, $f_y - 5),
  4 => array(10, $f_x - 10),
  5 => array(5, $f_y - 5),
  6 => array(10, $f_x - 10),
  7 => array(5, $f_y - 5),
  8 => array(10, $f_x - 10),
  9 => array(5, $f_y - 5),
  );
  imagefilledpolygon($first, $points, 5, $red);
}
imagestring($first, 4, rand(0, $f_x/3), rand(0,$f_y/2.5), $_SESSION['captcha'], $black);
imagecopyresized($second, $first, 0, 0, 0, 0, $s_x, $s_y, $f_x, $f_y);
imagedestroy($first);
$red=imagecolorallocate($second, 255, 0, 0);
$green=imagecolorallocate($second, 0, 128, 0);
$blue=imagecolorallocate($second, 0, 0, 255);
$RandomPixels=ceil($s_x*$s_y/100);
for($i=0;$i<$RandomPixels;$i++)
{
  $locx=rand(0,$s_x-1);
  $locy=rand(0,$s_y-1);
  imagesetpixel($second, $locx, $locy, $red);
}
for($i=0;$i<$RandomPixels;$i++)
{
  $locx=rand(0,$s_x-1);
  $locy=rand(0,$s_y-1);
  imagesetpixel($second, $locx, $locy, $green);
}
for($i=0;$i<$RandomPixels;$i++)
{
  $locx=rand(0,$s_x-1);
  $locy=rand(0,$s_y-1);
  imagesetpixel($second, $locx, $locy, $blue);
}
$randcolor=imagecolorallocate($second, rand(100,255), rand(100,255), rand(100,255));
for($i=0; $i<5; $i++)
{
  imageline($second, rand(0, $s_x), rand(0, $s_y), rand(0, $s_x), rand(0, $s_y), $randcolor);
  $randcolor=imagecolorallocate($second, rand(100,255), rand(100,255), rand(100,255));
}
@header("Content-Type: image/png");
$finished=imagerotate($second, rand(0,15)-7.5, $bgcolor[2]*65536+$bgcolor[1]*256+$bgcolor[0]);
imagedestroy($second);
imagepng($finished);
imagedestroy($finished);
?>

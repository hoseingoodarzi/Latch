#L A T C H  1.x
===============

author  Hosein (hoseingoodarzi707@gmail.com)
link    https://github.com/hoseingoodarzi/Latch

#Usage
======
Hi, for using captcha You should first add class to your file
and insitiation of class
example:
```
require_once("latch.class.php");
$captcha = new LATCH;
```
then call the the function (build) everywhere you want it to show
like this:
```
<?php echo $captcha->build(A1, A2, A3, A4); ?>
```
A1 => session_name (required)
A2 => size (width) of captcha image (between 100 and 300 (pixel)) (optional; default 150px)
A3 => length of the captcha text (optional; default 4 letter)
A4 => enabled numbers in text by true and disabled by false (optional; default true)

for validate of the captcha you could do like that:
```
if($_SESSION[ A1 ] == $_POST[ 'some' ]) {
// do something;
}
```
#Notice
=======
You could use function many times
You could add your standard fonts and these shows randomly
Also font folder just must have the ttf format files
Requirements PHP and GD library


Thanks for using
If you have issue or suggestion about latch, please contact with me
```
gmail: hoseingoodarzi707
```

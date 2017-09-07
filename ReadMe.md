# L A T C H  1.x 

PHP Captcha Class

author  Hosein (hoseingoodarzi707@gmail.com)

link    https://github.com/hoseingoodarzi/Latch

## Introduction

I wrote this captcha class and I think putting it here maybe help somebody.

## Usage

Hi, for using captcha You should first add it to your file and then insitiation of the class.

example:
```
require_once("latch.class.php");
$captcha = new LATCH;
```
then call the function (build) everywhere you want it to show
like this:
```
<?php echo $captcha->build(A1, A2, A3, A4); ?>
```
A1 => Session_name (required)

A2 => Size (width) of captcha image (between 100 and 300 (pixel)) (optional; default 150px)

A3 => Length of the captcha text (optional; default 4 letter)

A4 => Enabled numbers in text by true and disabled by false (optional; default true)

for validate of the captcha you could do like that:
```
if($_SESSION[ A1 ] == md5($_POST[ 'some' ])) {
  // do something;
}
```
## Notice

1. You could use function many times.
2. You could add your standard fonts and these shows randomly.
3. Also font folder just must have the ttf format files.
4. Requirements PHP and GD library.


----------------
If you have issue or suggestion about latch, please contact with me

**gmail: hoseingoodarzi707**

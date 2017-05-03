<?php

/*
        LATCH
            The PHP Captcha Project

    @author   Hosein (hoseingoodarzi707@gmail.com)
    @link     https://github.com/hoseingoodarzi/Latch
    @version  1.00

    Requires PHP and GD Library server support
*/

// image size
define("DEFAULT_SIZE", 150);
// length of captcha text
define("DEFAULT_LENGTH", 4);
// enabled numbers in text by true
// and disable by false
define("DEFAULT_ORDER", TRUE);
// font folder name
define("FONT_FOLDER", 'font');
// font directory
define("FONT_DIR", __DIR__ . DIRECTORY_SEPARATOR . FONT_FOLDER . DIRECTORY_SEPARATOR);

class LATCH
{
    public $min_size = 100;
    public $max_size = 300;
    public $min_length = 3;
    public $max_length = 7;
    public $font_size = 12;
    public $random_font;
    public $gd_library;
    /*
      amount of chaos on the captcha image (include line and arcs)
      zero for hide them and increase number cause more chaos
      suggested it sets between 1 to 10
    */
    public $mess_degree = 5;

    protected $characters = [
        "UpperCase"  =>  ["A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z"],
        "LowerCase"  =>  ["a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z"],
        "Numbers"    =>  [1, 2, 3, 4, 5, 6, 7, 8, 9]
    ];
    protected $image;
    protected $random_characters;
    protected $counter;
    protected $session_name;
    protected $temp;
    protected $color;
    protected $error_message = [];

    // check gd library support
    public function gd_check() {
        if(function_exists('gd_info')) {
            $this->gd_library = gd_info();
            if($this->gd_library["PNG Support"] != 1) {
                $this->error_message[] = "Your GD LIBRARY doesn't support png pictures";
                return false;
            }
            return true;
        }
        else {
            $this->error_message[] = "Your server doesn't support GD LIBRARY";
            return false;
        }
    }
    // base function
    public function build($session_name = '', $size = DEFAULT_SIZE, $length = DEFAULT_LENGTH, $order = DEFAULT_ORDER) {
        $this->gd_check();
        if($this->check($session_name, $size, $length, $order))
            $this->random_characters = $this->generate_random_chars($length);
        // show messages while has error
        if(count($this->error_message) > 0)
            return $this->show_message();
        // set session name
        $this->session_name = $session_name;
        // make canvas
        $this->image = imagecreate($size, $size / 2);
        // captcha background
        imagecolorallocate($this->image, 255, 255, 255);
        // random color for characters and lines
        $this->color = imagecolorallocate($this->image,mt_rand(0,200),mt_rand(0,200),mt_rand(0,200));
        // calculates the position of characters
        list($xlb, $ylb, $xrb, , , $yrt) = imagettfbbox($this->font_size * ($this->max_length / strlen($this->random_characters) * ($size / 100)), mt_rand(-5,5) * $size / 300, $this->random_font, $this->random_characters);
        // function for make the chaos
        $this->generate_chaos($size, $length);
        // put the characters on the center of canvas
        imagettftext($this->image, $this->font_size * ($this->max_length / strlen($this->random_characters) * ($size / 100)), mt_rand(-5,5) * $size / 300, (($size / 2) - ($xrb - $xlb) / 2), (($size / 4) + ($ylb - $yrt) / 2), $this->color, $this->random_font, $this->random_characters);
        // run function make and buffering the image function and
        // then release the buffer and send it to html with base 64 encoding
        return "<img src=" . 'data:image/png;base64,' . base64_encode($this->make()) . " alt=captcha />";
    }
    // buffering output function
    public function make() {
        ob_start();
        imagepng($this->image);
        $this->validate();
        return ob_get_clean();
    }
    // make chaos function
    public function generate_chaos($size, $length) {
        $this->counter = 0;
        while($this->counter < $this->mess_degree * ($size / 100)) {
            imagesetthickness($this->image, mt_rand(1, ($size / 100) * ($this->max_length / $length)));
            imageline($this->image, mt_rand(0, $size), mt_rand(0, $size), mt_rand(0, $size), mt_rand(0, $size / 2), $this->color);
            imagearc($this->image, mt_rand(0, $size), mt_rand(0, $size), mt_rand(0, $size), mt_rand(0, $size / 2), 0, mt_rand(0, 360), $this->color);
            $this->counter++;
        }
        return true;
    }
    // show the trouble message
    public function show_message() {
        return implode("<br>", $this->error_message);
    }
    // check the input values
    protected function check($session_name, $size, $length, $order) {
        if(!empty($session_name) && is_numeric($size) && is_numeric($length) && is_bool($order)) {
            if (($size < $this->min_size || $size > $this->max_size) || ($length < $this->min_length || $length > $this->max_length)) {
                $this->error_message[] = "Check the inputs:<br><ul><li>Argument1: Size must be between $this->min_size to $this->max_size</li><li>A2: Length must be between $this->min_length to $this->max_length</li><li>A3: Order must be TRUE or FALSE</li></ul>";
                return false;
            }
            // check font directory
            else if(!is_dir(FONT_DIR)) {
                $this->error_message[] = "Wrong font directory;<br>" . FONT_DIR;
            }
            else {
                $this->counter = 0;
                $this->temp = array_slice(scandir(FONT_DIR), 2);
                while($this->counter == count($this->temp)) {
                    if(count($this->temp) != 0) {
                        if(pathinfo($this->temp[$this->counter], PATHINFO_EXTENSION) != "ttf") {
                            $this->error_message[] = "Font folder should just have the ttf files";
                            return false;
                        }
                    }
                    else {
                        $this->error_message[] = "There's no font in font folder";
                        return false;
                    }
                    $this->counter++;
                }
                $this->random_font = FONT_DIR . $this->temp[mt_rand(0, count($this->temp) - 1)];
                $this->characters = ($order == TRUE) ? array_slice($this->characters, 0) : $this->characters = array_slice($this->characters, 0, 2);
                return true;
            }
        }
        else {
            $this->error_message[] = "First arguments must be string (Required),<br> second and third argument must be numeric <br> and fourth argument must be TRUE or FALSE";
            return false;
        }
    }
    // make random characters
    protected function generate_random_chars($length) {
        $this->counter = 0;
        $this->random_characters = "";
        $this->temp = implode(call_user_func_array('array_merge', $this->characters));
            while($this->counter < $length) {
                $this->random_characters .= substr($this->temp, mt_rand(0, strlen($this->temp)-1), 1);
                $this->counter++;
            }
        return $this->random_characters;
    }
    // session creation value
    protected function validate() {
        $_SESSION[$this->session_name] = md5($this->random_characters);
        return true;
    }
    // delete the picture at the end
    public function __destruct() {
        if(!empty($this->image))
            imagedestroy($this->image);
    }
}
?>
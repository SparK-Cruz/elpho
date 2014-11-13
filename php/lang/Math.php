<?php
  import(php.lang.Number);

  abstract class Math{
    const PI = 3.1415926535898;
    const E = 2.71828182845905;
    const LN10 = 2.302585092994046;
    const LN2 = 0.6931471805599453;
    const LOG10E = 0.4342944819032518;
    const LOG2E = 1.442695040888963387;
    const SQRT1_2 = 0.7071067811865476;
    const SQRT2 = 1.4142135623730951;

    public static function realPI(){
      return new Number("3","14159265358979323846264338327950288419716939937510582097494459230781640628620899862803482534211706798214808651328230664709384460955058223172535940812848111745028410270193852110555964462294895493038196442881097566593344612847564823378678316527120190914564856692346034861045432664821339360726024914127372458700660631558817488152092096282925409171536436789259036001133053054882046652138414695194151160943305727036575959195309218611738193261179310511854807446237996274956735188575272489122793818301194912983367336244065664308602139494639522473719070217986094370277053921717629317675238467481846766940513200056812713452635608277857713427577896091736371787214684409012249534301465495853710507922796892589235420199561121290219608640344181598136297747713099605187072113499999983729780499510597317328160963185950244594553469083026425223082533446850352619311881710100031378387528865875332083814206171776691473035982534904287554687311595628638823537875937519577818577805321712268066130019278766111959092164201989380952572010654858632788659361533818279682303019520353018529689957736225994138912497217752834791315155748572424541506959");
    }
    public static function abs($number){
      return abs($number);
    }
    public static function acos($number){
      return acos($number);
    }
    public static function asin($number){
      return asin($number);
    }
    public static function atan($number){
      return atan($number);
    }
    public static function atan2($y,$x){
      return atan2($y,$x);
    }
    public static function cos($number){
      return cos($number);
    }
    public static function ceil($number){
      return ceil($number);
    }
    public static function exp($number){
      return exp($number);
    }
    public static function floor($number){
      return floor($number);
    }
    public static function log($number){
      return log($number,10);
    }
    public static function max(){
      return call_user_func_array("max",func_get_args());
    }
    public static function min(){
      return call_user_func_array("min",func_get_args());
    }
    public static function pow($number,$exponent){
      return pow($number,$exponent);
    }
    public static function random(){
      $seed = rand(1,14); //IEEE 754 double-precision
      $seed = Math::pow(10,$seed);
      return rand(0,Math::ceil($seed))/Math::ceil($seed);
    }
    public static function round($number){
      return round($number);
    }
    public static function sin($number){
      return sin($number);
    }
    public static function sqrt($number){
      return sqrt($number);
    }
    public static function tan($number){
      return tan($number);
    }
  }

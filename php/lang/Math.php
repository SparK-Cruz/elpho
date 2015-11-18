<?php
  abstract class Math{
    const PI = 3.1415926535898;
    const E = 2.71828182845905;
    const LN10 = 2.302585092994046;
    const LN2 = 0.6931471805599453;
    const LOG10E = 0.4342944819032518;
    const LOG2E = 1.442695040888963387;
    const SQRT1_2 = 0.7071067811865476;
    const SQRT2 = 1.4142135623730951;

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

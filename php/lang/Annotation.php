<?php
  require_once('php/lang/Object.php');
  require_once('php/lang/StaticType.php');

  abstract class Annotation extends StaticType{
    //use these with self to guarantee
    private static $attributePattern = '/@([\w]+)\s*(\(([\w,]+\s*=?\s*.*\s*,?\s*)*\))?$/mi';
    private static $parameterPattern = '/\((.*)\)\s*/i';

    //use these with static to read from child class
    protected static $name = null;
    protected static $parameters = null;

    public static function read(Reflector $piece){
      if(static::$name == null)
        throw new Exception("Static member 'name' not set for child type ".get_called_class()." of Annotation");
      if(static::$parameters == null)
        throw new Exception("Static member 'parameters' not set for child type ".get_called_class()." of Annotation");

      $matches = array();
      preg_match_all(self::$attributePattern, $piece->getDocComment(), $matches);

      $modifiers = new ArrayList();

      for($i=0; $i<count($matches[0]); $i++){
        $name = $matches[1][$i];

        //ignore other annotations
        if($name !== static::$name)
          continue;

        $keys = static::$parameters;

        $modifier = new Object();

        $params = array();
        preg_match_all(self::$parameterPattern, $matches[2][$i], $params);

        $params = explode(',',$params[1][0]);

        $keyIndex = 0;

        foreach($params as $param){
          $keyValuePair = explode('=', $param);

          $key = null;
          $value = trim($keyValuePair[0]);

          if(count($keyValuePair) > 2)
            throw new Exception("Unexpected token '=' in ".$name." annotation @ parameter '".$value."'");

          if(count($keyValuePair) == 2){
            $key = $value;
            $value = trim($keyValuePair[1]);
          }

          //it has a key
          if($key != null){
            $found = false;
            if(!($found = array_search($key, $keys)))
              throw new Exception("Unknown annotation argument: ".$key);

            $modifier->{$key} = $value;
            array_splice($keys, $found, 1);
            continue;
          }

          //value only
          if($keyIndex < count($keys)){
            $modifier->{$keys[$keyIndex++]} = $value;
            continue;
          }

          //value only but we are out of positional keys
          throw new Exception("Unknown annotation argument: ".$value);
        }

        $modifiers->push($modifier);
      }

      return $modifiers;
    }
  }
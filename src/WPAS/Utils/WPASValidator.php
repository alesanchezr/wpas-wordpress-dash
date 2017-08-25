<?php 

namespace WPAS\Utils;
use Respect\Validation\Rules;

class WPASValidator{
    
    private static $errors = [];
    const USERNAME = 'username';
    const NAME = 'name';
    const EMAIL = 'email';
    const URL = 'url';
    const SLUG = 'slug';
    const INTEGER = 'integer';
    const DESCRIPTION = 'description';
    const PHONE = 'phone';
    
    
    public static function getErrors(){
        return self::$errors;
    }
    
    public static function validate($type, $value, $name){
        
        $result = false;
        switch($type)
        {
            case self::NAME:
                
                $validator = new Rules\AllOf(
                    new Rules\Alpha(),
                    new Rules\NoWhitespace(),
                    new Rules\Length(1, 15)
                );
                if($validator->validate($value)) $result = $value;
                
            break;
            case self::USERNAME:
                
                $validator = new Rules\AllOf(
                    new Rules\Alnum(),
                    new Rules\NoWhitespace(),
                    new Rules\Length(1, 15)
                );
                if($validator->validate($value)) $result = $value;
                
            break;
            case self::PHONE:
                
                $validator = new Rules\AllOf(
                    new Rules\Phone()
                );
                if($validator->validate($value)) $result = $value;
                
            break;
            case self::EMAIL:
                
                $validator = new Rules\AllOf(
                    new Rules\Email(),
                    new Rules\NoWhitespace(),
                    new Rules\Length(1, 255)
                );
                if($validator->validate($value)) $result = $value;
                
            break;
            case self::URL:
                
                $validator = new Rules\AllOf(
                    new Rules\Url(),
                    new Rules\NoWhitespace(),
                    new Rules\Length(0, 255)
                );
                if($validator->validate($value)) $result = $value;
                
            break;
            case self::SLUG:
                
                $validator = new Rules\AllOf(
                    new Rules\Slug(),
                    new Rules\Length(1, 30)
                );
                if($validator->validate($value)) $result = $value;
                
            break;
            case self::INTEGER:
                
                $validator = new Rules\AllOf(
                    new Rules\IntVal(),
                    new Rules\Length(0, 255)
                );
                if($validator->validate($value)) $result = $value;
                
            break;
            case self::DESCRIPTION:
                
                $validator = new Rules\AllOf(
                    new Rules\Length(0, 255)
                );
                if($validator->validate($value)) $result = $value;
                
            break;
            default: $result = null; break;
        }
        
        if($result==false) self::$errors[] = 'Invalid '.$name;
        else if($result==null) throw new WPASException('Invalid validation type: '.$type);
        
        return $result;
    }
    
}
?>

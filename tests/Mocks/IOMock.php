<?php
namespace TextUI\Tests\Mocks;

/**
 * TextUI\IO mock for PHPUnit tests.
 *
 * @author everton3x
 */
class IOMock
{
    public static array $options = [];
    public static string $input = '';
    
    public static function readRawStdin(): string
    {
        if(self::$options !== []){
            $current = current(self::$options);
            if($current === false){
                return '';
            }
            next(self::$options);
            return $current;
        }
        
        return self::$input;
    }
}

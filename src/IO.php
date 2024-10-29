<?php
namespace TextUI;

/**
 *
 * @author everton3x
 */
class IO
{
    public static function readRawStdin(): string
    {
        return fgets(STDIN);
    }
}

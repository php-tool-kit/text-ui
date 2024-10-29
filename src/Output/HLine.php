<?php

namespace TextUI\Output;

/**
 * Draws a horizontal line on the screen.
 *
 * @author everton3x
 */
class HLine implements DrawableInterface
{
    public readonly string $char;

    public function __construct(string $char = '-')
    {
        $this->char = $char;
    }

    /**
     * Draw the line.
     *
     * @return void
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function draw(): void
    {
        $cols = \TextUI\Utils::detectScreenColumns();
        // Required to allow $char to have more than one character.
        $times = floor($cols / strlen($this->char));
        echo str_repeat($this->char, (int) $times);
    }
}

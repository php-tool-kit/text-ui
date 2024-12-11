<?php

namespace TextUI\Input;

/**
 * Class HiddenEntry
 *
 * Represents a hidden form entry with a value that is not displayed.
 *
 * @package TextUI\Input
 */
class HiddenEntry implements EntryInterface
{
    /**
     * @var mixed $value The value of the entry.
     */
    public readonly mixed $value;

    /**
     * HiddenEntry constructor.
     *
     * @param mixed $value The value of the entry.
     */
    public function __construct(mixed $value)
    {
        $this->value = $value;
    }

    /**
     * Reads and returns the value of the entry.
     *
     * @return mixed The value of the entry.
     */
    public function read(): mixed
    {
        return $this->value;
    }
}

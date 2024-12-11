<?php

namespace TextUI\Input;

/**
 * Class ReadOnlyEntry
 *
 * Represents a read-only form entry with a label and a value.
 *
 * @author everton3x
 */
class ReadOnlyEntry implements EntryInterface
{
    /**
     * @var string $label The label of the entry.
     */
    public readonly string $label;
    
    /**
     * @var mixed $value The value of the entry.
     */
    public readonly mixed $value;

    /**
     * ReadOnlyEntry constructor.
     *
     * @param string $label The label of the entry.
     * @param mixed $value The value of the entry.
     */
    public function __construct(string $label, mixed $value)
    {
        $this->label = $label;
        $this->value = $value;
    }

    /**
     * Reads and returns the value of the entry.
     *
     * @return mixed The value of the entry.
     */
    public function read(): mixed
    {
        echo $this->label, $this->value, PHP_EOL;
        return $this->value;
    }
}

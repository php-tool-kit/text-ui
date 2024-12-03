<?php

namespace TextUI\Input;

/**
 * Captures multi-line text from the user.
 *
 * Returns an array of the input lines. A blank line ends the input and returns
 *  the user input with each line processed by trim().
 *
 * @author everton3x
 */
class TextMultiLineEntry
{
    public readonly string $label;

    public function __construct(string $label)
    {
        $this->label = $label;
    }

    /**
     *
     * @return array<string> An array of the input lines.
     */
    public function read(): array
    {
        echo $this->label;
        echo PHP_EOL;
        $lines = [];
//        $entry = trim((string) fgets(STDIN));
        $entry = trim(\TextUI\IO::readRawStdin());
        while ($entry !== '') {
            $lines[] = $entry;
//            $entry = trim((string) fgets(STDIN));
            $entry = trim(\TextUI\IO::readRawStdin());
        }
        return $lines;
    }
}

<?php

namespace TextUI\Input;

/**
 * User text input.
 *
 * Displays a message to the user and returns the text the user entered. When
 * the user presses ENTER, the raw text entered is fed to the trim() function
 * and returned.
 *
 * The label is displayed on the same line as the entry point. This means that
 * if you want to display the label above the input space, add a line break at
 * the end of $label. The same goes for whitespace and tabs.
 *
 * @author everton3x
 */
class TextEntry implements EntryInterface
{
    public readonly string $label;

    public function __construct(string $label)
    {
        $this->label = $label;
    }

    /**
     * Displays the label and waits for user input.
     * @return string Returns the text entered by the user as processed by
     * trim().
     */
    public function read(): string
    {
        echo $this->label;
        return trim(\TextUI\IO::readRawStdin());
//        return trim((string) fgets(STDIN));
    }
}

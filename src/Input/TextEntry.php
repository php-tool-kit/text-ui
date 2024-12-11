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
    protected string|null $default = null;

    public function __construct(string $label)
    {
        $this->label = $label;
    }
    
    /**
     * Sets a default input text.
     * 
     * @param string $default
     * @return TextEntry
     */
    public function setDefault(string $default): TextEntry
    {
        $this->default = $default;
        return $this;
    }

    /**
     * Displays the label and waits for user input.
     * @return string Returns the text entered by the user as processed by
     * trim().
     */
    public function read(): string
    {
        $default = '';
        if(!is_null($this->default)){
            $default = "[{$this->default}] ";
        }
        echo $this->label, $default;
        $userInput = trim(\TextUI\IO::readRawStdin());
        if(!is_null($this->default)){
            if(strlen($userInput) === 0){
                return $this->default;
            }
        }
        return $userInput;
    }
}

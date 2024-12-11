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
class TextMultiLineEntry implements EntryInterface
{

    public readonly string $label;
    protected array|null $default = null;

    public function __construct(string $label)
    {
        $this->label = $label;
    }

    /**
     * Sets de default text.
     * 
     * @param array $default Lines defaults.
     * @return TextMultiLineEntry
     */
    public function setDefault(array $default): TextMultiLineEntry
    {
        $this->default = $default;
        return $this;
    }

    /**
     *
     * @return array<string> An array of the input lines.
     */
    public function read(): array
    {
        $default = '';
        if (!is_null($this->default)) {
            $default = "[{$this->default}] ";
        }

        echo $this->label;
        if (!is_null($this->default)) {
            echo '[', PHP_EOL;
            foreach ($this->default as $row) {
                echo $row . PHP_EOL;
            }
            echo ']', PHP_EOL;
        }
        echo PHP_EOL;
        $lines = [];
        $entry = trim(\TextUI\IO::readRawStdin());
        while ($entry !== '') {
            $lines[] = $entry;
            $entry = trim(\TextUI\IO::readRawStdin());
        }
        if (!is_null($this->default)) {
            if ($lines === []) {
                return $this->default;
            }
        }
        return $lines;
    }
}

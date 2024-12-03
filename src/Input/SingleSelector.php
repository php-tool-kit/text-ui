<?php

namespace TextUI\Input;

use InvalidArgumentException;

/**
 * Provides the user with a menu to choose a single option.
 *
 * After user selection, the option label is returned, except if
 * SingleSelector::$returnOptionKey is TRUE (default is FALSE), in which case
 * the selected key is returned.
 *
 * @author everton3x
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class SingleSelector implements EntryInterface
{
    public readonly string $label;
    /**
     *
     * @var array<string>
     */
    public readonly array $options;
    protected ?string $selectionInvalidMessage = null;
    protected string $prompt = '> ';

    /**
     * If TRUE, returns the selected key instead of the label. Default is FALSE.
     * @var bool
     */
    public bool $returnOptionKey = false;

    /**
     *
     * @param string $label
     * @param array<string> $options
     * @throws InvalidArgumentException
     */
    public function __construct(string $label, array $options)
    {
        if (count($options) === 0) {
            throw new InvalidArgumentException('$options cannot be empty.');
        }
        $this->label = $label;
        $this->options = $options;
    }

    /**
     * Sets a message to be displayed when an invalid value is selected.
     *
     * @param string $message
     * @return SingleSelector
     */
    public function setSelectionInvalidMessage(string $message): SingleSelector
    {
        $this->selectionInvalidMessage = $message;
        return $this;
    }

    /**
     * Sets a prompt message to be displayed in the space reserved for the
     * user to type the option. The default is `> `.
     *
     * @param string $prompt
     * @return SingleSelector
     */
    public function setPrompt(string $prompt): SingleSelector
    {
        $this->prompt = $prompt;
        return $this;
    }

    public function read(): string
    {
        echo $this->label;
        echo PHP_EOL;
        $keys = array_keys($this->options);
        // This part is needed to calculate the maximum size of $index to
        // format the options with equal width for all.
        $maxlenght = 0;
        foreach ($keys as $key) {
            if (strlen($key) > $maxlenght) {
                $maxlenght = strlen($key);
            }
        }

        foreach ($this->options as $index => $label) {
            printf("[ %{$maxlenght}s ]\t%s" . PHP_EOL, $index, $label);
        }

        while (true) {
            echo $this->prompt;
//            $selection = trim((string) fgets(STDIN));
            $selection = trim(\TextUI\IO::readRawStdin());
            if (key_exists($selection, $this->options)) {
                if ($this->returnOptionKey) {
                    return $selection;
                }
                return $this->options[$selection];
            }
            if (!is_null($this->selectionInvalidMessage)) {
                echo $this->selectionInvalidMessage . PHP_EOL;
            }
        }
    }
}

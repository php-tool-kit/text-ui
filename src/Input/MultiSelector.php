<?php

namespace TextUI\Input;

use InvalidArgumentException;
use TextUI\Screen\Command;

/**
 * Provides the user with a menu to choose multiple options.
 *
 * After user selection ended (end with BLANK ENTER), the options labels are returned, except if
 * MultiSelector::$returnOptionKey is TRUE (default is FALSE), in which case
 * the selected keys are returned.
 *
 * @author everton3x
 * @SuppressWarnings(PHPMD.StaticAccess)
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class MultiSelector implements EntryInterface
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
     *
     * @var array<int|string>
     */
    protected array $selection = [];

    /**
     * If TRUE, returns the selected key instead of the label. Default is FALSE.
     * @var bool
     */
    public bool $returnOptionKey = false;

    /**
     * If TRUE (the default), clear the screen on each selection cycle.
     *
     * @var bool
     */
    public bool $clearScreen = true;

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
     * Configures the options that are selected by default.
     * 
     * @param mixed $default List with labels of the default options.
     * @return MultiSelector
     */
    public function setDefault(mixed $default): MultiSelector
    {
        $keys = [];
        foreach ($default as $item){
            $index = array_search($item, $this->options, true);
            if($index !== false){
                $keys[] = $index;
            }
        }
        $this->setDefaultOptions(...$keys);
        return $this;
    }
    
    /**
     * Sets a message to be displayed when an invalid value is selected.
     *
     * @param string $message
     * @return MultiSelector
     */
    public function setSelectionInvalidMessage(string $message): MultiSelector
    {
        $this->selectionInvalidMessage = $message;
        return $this;
    }

    /**
     * Sets a prompt message to be displayed in the space reserved for the
     * user to type the option. The default is `> `.
     *
     * @param string $prompt
     * @return MultiSelector
     */
    public function setPrompt(string $prompt): MultiSelector
    {
        $this->prompt = $prompt;
        return $this;
    }

    /**
     * Defines which options will be selected by default.
     *
     * @param int|string $default Sequence with the indexes of the selected standard options.
     * @return MultiSelector
     */
    public function setDefaultOptions(mixed ...$default): MultiSelector
    {
        $this->selection = $default;
        return $this;
    }

    /**
     *
     * @return array<int|string>
     * @SuppressWarnings(PHPMD.ElseExpression)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    #[\Override]
    public function read(): array
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

        $lastOptionIsInvalid = false;
        while (true) {
            if ($this->clearScreen) {
                Command::send(Command::CLEAR_SCREEN);
            }
            foreach ($this->options as $index => $label) {
                $selected = ' ';
                if (array_search($index, $this->selection) !== false) {
                    $selected = 'X';
                }
                printf("[%s] [ %{$maxlenght}s ]\t%s" . PHP_EOL, $selected, $index, $label);
            }
            if ($lastOptionIsInvalid) {
                echo $this->selectionInvalidMessage . PHP_EOL;
                $lastOptionIsInvalid = false;
            }
            echo $this->prompt;
//            $selection = trim((string) fgets(STDIN));
            $selection = trim(\TextUI\IO::readRawStdin());
            if ($selection === '') {
                if ($this->returnOptionKey) {
                    return $this->selection;
                } else {
                    $result = [];
                    foreach ($this->options as $key => $value) {
                        if (array_search($key, $this->selection) !== false) {
                            $result[$key] = $value;
                        }
                    }
                    return $result;
                }
            }
            if (key_exists($selection, $this->options)) {
                $index = array_search($selection, $this->selection);
                if ($index !== false) {
                    unset($this->selection[$index]);
                } else {
                    $this->selection[] = $selection;
                }
            } else {
                $lastOptionIsInvalid = true;
            }
        }
    }
}

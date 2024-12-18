<?php
namespace TextUI\Input;

use BadFunctionCallException;
use DateException;
use DateTime;

/**
 * Receives a date from the user.
 *
 * Converts user input into a PHP DateTime object using
 * date_create_from_format().
 *
 * Optionally, you can process user input with a parser defined by
 * DateTimeEntry:setParser().
 *
 * @author everton3x
 */
class DateEntry implements EntryInterface
{

    public readonly string $label;
    public readonly string $format;
    protected ?object $parser = null;
    protected string|null $default = null;

    /**
     *
     * @param string $label
     * @param string $format A format string accepted by
     * date_create_from_format().
     */
    public function __construct(string $label, string $format)
    {
        $this->label = $label;
        $this->format = $format;
    }

    /**
     * Sets date default.
     * 
     * @param string $default
     * @return DateEntry
     */
    public function setDefault(string $default): DateEntry
    {
        $this->default = $default;
        return $this;
    }

    /**
     * A parser function that will be called by DateTimeEntry::parser($entry),
     * where $entry is the user input.
     *
     * The parser must return a string capable of being processed by
     * date_create_from_format() according to DateTimeEntry::$format
     *
     * @param null|object $parser
     * @return DateEntry
     */
    public function setParser(?object $parser): DateEntry
    {
        $this->parser = $parser;
        return $this;
    }

    public function read(): DateTime
    {
        $default = '';
        if (!is_null($this->default)) {
            $default = "[{$this->default}] ";
        }
        echo $this->label, $default;
        $entry = trim(\TextUI\IO::readRawStdin());
        if (!is_null($this->default)) {
            if (strlen($entry) === 0) {
                $entry = $this->default;
            }
        }
        if (!is_null($this->parser)) {
            $parser = $this->parser;
            if (!is_callable($parser)) {
                throw new BadFunctionCallException('Parser is not callable.');
            }
            $entry = $parser($entry);
        }
        $date = date_create_from_format($this->format, $entry);
        if ($date === false) {
            throw new DateException();
        }
        return $date;
    }
}

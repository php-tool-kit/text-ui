<?php

namespace TextUI\Input;

use BadFunctionCallException;
use InvalidArgumentException;

/**
 * Receives numbers from the user.
 *
 * Accepts decimal places, thousands separator, decimal point and custom parser.
 *
 * @author everton3x
 */
class NumberEntry implements EntryInterface
{
    protected readonly string $label;
    protected ?object $parser = null;
    protected string $decimalPoint;
    protected string $thousandsSep;
    protected string $outputType = 'float';
    protected ?int $decimals = null;

    public function __construct(string $label)
    {
        $this->label = $label;
        $localeconv = localeconv();
        $this->decimalPoint = $localeconv['decimal_point'];
        $this->thousandsSep = $localeconv['thousands_sep'];
    }

    /**
     * Default parser and will always be applied unless a custom parser is provided by NumberEntry::setParser().
     *
     * The default parser applies the following operations on user input (in the following order):
     *
     * 1. Removes any characters that are not numbers, `.` or `,`.
     *
     * 2. Removes the thousands separator. It can be set by
     * NumberEntry::setThousandsSep(), otherwise use the one obtained by
     * localeconv() function.
     *
     * 3. Replaces the decimal point set by NumberEntry::setDecimalPoint()
     * (or the default obtained by localeconv()) with `.`.
     *
     * 4. If the number of decimals is set by NumberEntry::setDecimals(),
     * apply round().
     *
     * 5. Converts the output to float or int as defined in
     * NumberEntry::setOutputType() (default is float).
     *
     * @param string $entry
     * @return float|int
     */
    protected function defaultParser(string $entry): float|int
    {
        $entry = preg_replace('/[^0-9.,]/', '', $entry);
        $entry = str_replace($this->thousandsSep, '', (string) $entry);
        $entry = str_replace($this->decimalPoint, '.', (string) $entry);
        if (!is_null($this->decimals)) {
            $entry = round((float) $entry, $this->decimals);
        }
        switch ($this->outputType) {
            case 'float':
            case 'double':
                settype($entry, 'float');
                break;
            case 'int':
            case 'integer':
                settype($entry, 'int');
                break;
            default:
                throw new InvalidArgumentException("Only float|double|int|integer"
                        . " are allowed. {$this->outputType} is invalid!");
        }
        return $entry;
    }

    /**
     * Sets a parser.
     *
     * $parser must be a callable type that will be executed as
     * NumberEntry::parser() and must receive as its only argument the user's
     * string and return a float or int.
     *
     * @param null|object $parser
     * @return NumberEntry
     */
    public function setParser(?object $parser): NumberEntry
    {
        $this->parser = $parser;
        return $this;
    }

    /**
     * Sets the decimal point.
     *
     * @param string $decimalPoint
     * @return NumberEntry
     */
    public function setDecimalPoint(string $decimalPoint): NumberEntry
    {
        $this->decimalPoint = $decimalPoint;
        return $this;
    }

    /**
     * Sets a thousands separator.
     *
     * @param string $thousandsSep
     * @return NumberEntry
     */
    public function setThousandsSep(string $thousandsSep): NumberEntry
    {
        $this->thousandsSep = $thousandsSep;
        return $this;
    }

    /**
     * Sets a decimal places.
     *
     * @param int $decimals
     * @return NumberEntry
     */
    public function setDecimals(int $decimals): NumberEntry
    {
        $this->decimals = $decimals;
        return $this;
    }

    /**
     * Sets the output type.
     *
     * @param string $type Only float|double|int|integer allowed.
     *
     * @return NumberEntry
     * @throws InvalidArgumentException
     */
    public function setOutputType(string $type): NumberEntry
    {
        switch ($type) {
            case 'float':
            case 'double':
            case 'int':
            case 'integer':
                $this->outputType = $type;
                return $this;
            default:
                throw new InvalidArgumentException("Only float|double|int|integer are allowed. $type is invalid!");
        }
    }

    /**
     * Reads the user input.
     *
     * @return float|int
     */
    public function read(): float|int
    {
        echo $this->label;
//        $entry = trim((string) fgets(STDIN));
        $entry = trim(\TextUI\IO::readRawStdin());
        if (is_null($this->parser)) {
            return $this->defaultParser($entry);
        }
        $parser = $this->parser;
        if (!is_callable($parser)) {
            throw new BadFunctionCallException('Parser not is callable.');
        }
        return $parser($entry);
    }
}

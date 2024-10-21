<?php

namespace TextUI\Output;

use BadFunctionCallException;
use DateException;
use DateTime;
use InvalidArgumentException;
use TextUI\Screen\Command;
use TextUI\Utils;

/**
 * A progress bar with percentage and optional time and count indicators.
 *
 * @author everton3x
 */
class Progress implements MeterInterface
{
    protected readonly float|int $maxValue;
    protected float|int $currentValue = 0;
    protected ?DateTime $startTime = null;
    protected ?object $unitFormatter = null;
    protected int $percentPrecision = 0;
    protected string $barChar = '|';

    /**
     * If TRUE, displays the current and total values ​​indicator.
     * Default is FALSE.
     * @var bool
     */
    public bool $units = true;

    /**
     * If TRUE, displays elapsed and remaining time indicator. Default is FALSE.
     *
     * @var bool
     */
    public bool $times = true;

    public function __construct(float|int $maxValue)
    {
        if ($maxValue <= 0) {
            throw new InvalidArgumentException("Maximum value cannot be less than or equal to zero: $maxValue");
        }
        $this->maxValue = $maxValue;
    }

    /**
     *
     * @param float|int $currentValue
     * @return void
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function update(float|int $currentValue): void
    {
        if (is_null($this->startTime)) {
            $this->init();
        }
        $this->setCurrentValue($currentValue);
        Command::send(Command::CLEAR_LINE);
        Command::send(Command::GOTO_LINE_START);

        $percentStr = $this->getPercentStr();
        $unitStr = '';
        if ($this->units) {
            $unitStr = $this->getUnitsStr();
        }
        $timeStr = '';
        if ($this->times) {
            $timeStr = $this->getTimesStr();
        }
        $cols = Utils::detectScreenColumns();
        $percentWidth = strlen($percentStr);
        $unitWidth = strlen($unitStr);
        $timeWidth = strlen($timeStr);
        $progressWidth = $cols - $percentWidth - $unitWidth - $timeWidth - 5;
        $progressStr = $this->getProgressStr($progressWidth);
        echo "{$progressStr}{$percentStr}{$unitStr}{$timeStr}";
        Command::send(Command::GOTO_LINE_START);
    }

    /**
     * Finalizes the progress bar ensuring that the total of 100% is reached.
     */
    public function end(): void
    {
        $this->update($this->maxValue);
        echo PHP_EOL;
    }

    protected function getProgressStr(int $maxWidth): string
    {
        $width = floor($maxWidth * ($this->currentValue / $this->maxValue));
        $bar = str_repeat($this->barChar, (int) $width);
        return str_pad($bar, $maxWidth, ' ', STR_PAD_RIGHT);
    }

    /**
     * Sets the decimal precision of the percentage indicator.
     *
     * @param int $precision
     * @return Progress
     * @throws InvalidArgumentException
     */
    public function setPercentPrecision(int $precision): Progress
    {
        if ($precision < 0) {
            throw new InvalidArgumentException("Percent precision cannot be less than zero: $precision");
        }
        $this->percentPrecision = $precision;
        return $this;
    }

    protected function getPercentStr(): string
    {
        $percent = number_format(($this->currentValue / $this->maxValue * 100), $this->percentPrecision);
        return "$percent%";
    }

    /**
     *
     * @return string
     * @throws DateException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function getElapsedTimeStr(): string
    {
        if (!$this->startTime) {
            throw new DateException();
        }
        $diff = Utils::getDiffBetweenTimes($this->startTime->getTimestamp(), time());
        $elapsed = [];
        if ($diff['hours'] > 0) {
            $elapsed[] = $diff['hours'] . 'h';
        }
        if ($diff['minutes'] > 0) {
            $elapsed[] = $diff['minutes'] . 'm';
        }
        if ($diff['seconds'] > 0) {
            $elapsed[] = $diff['seconds'] . 's';
        }
        if (count($elapsed) == 0) {
            return '--';
        }
        return join(':', $elapsed);
    }

    /**
     *
     * @return string
     * @throws DateException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function getRemainingTimeStr(): string
    {
        if ($this->maxValue == $this->currentValue) {
            return '--:--';
        }
        if ($this->currentValue == 0) {
            return '--:--';
        }
        if (!$this->startTime) {
            throw new DateException();
        }
        $elapsed = (time() - $this->startTime->getTimestamp());
        $percent = $this->currentValue / $this->maxValue;
        /*
         * elapsed     =   percent
         *              x
         * remaining   =   (1 - percent)
         *
         * => elapsed * (1 - percent) = remaining * percent
         *
         * => elapsed * (1 - percent) = remaining
         *    -----------------------
         *             percent
         *
         */
        $secondsRemaining = (int) floor(((1 - $percent) * $elapsed) / $percent);
        $diff = Utils::getDiffBetweenTimes(time(), time() + $secondsRemaining);
        $remaining = [];
        if ($diff['hours'] > 0) {
            $remaining[] = $diff['hours'] . 'h';
        }
        if ($diff['minutes'] > 0) {
            $remaining[] = $diff['minutes'] . 'm';
        }
        if ($diff['seconds'] > 0) {
            $remaining[] = $diff['seconds'] . 's';
        }
        if (count($remaining) == 0) {
            return '--';
        }
        return join(':', $remaining);
    }

    protected function getTimesStr(): string
    {
        $elapsed = $this->getElapsedTimeStr();
        $remaining = $this->getRemainingTimeStr();
        return "[$elapsed/$remaining]";
    }

    protected function getUnitsStr(): string
    {
        $max = $this->maxValue;
        $current = $this->currentValue;
        if (!is_null($this->unitFormatter)) {
            $formatter = $this->unitFormatter;
            if (!is_callable($formatter)) {
                throw new BadFunctionCallException('Formatter not is callable.');
            }
            $max = $formatter($max);
            $current = $formatter($current);
        }
        return "[$current/$max]";
    }

    /**
     * Function to format the values ​​of the value indicator. It must be a
     * function that receives a value as an argument and returns that formatted
     * value.
     *
     * @param null|object $formatter
     * @return Progress
     */
    public function setUnitFormatter(?object $formatter): Progress
    {
        $this->unitFormatter = $formatter;
        return $this;
    }

    /**
     * Starts the progress bar.
     *
     * @return void
     */
    public function init(): void
    {
        $this->startTime = new DateTime();
    }

    protected function setCurrentValue(float|int $value): void
    {
        if ($value < $this->currentValue) {
            throw new InvalidArgumentException("Value cannot be less than "
                    . "current value: $value < {$this->currentValue}");
        }
        if ($value > $this->maxValue) {
            throw new InvalidArgumentException("Value cannot be greater than "
                    . "maximun value: $value > {$this->maxValue}");
        }
        $this->currentValue = $value;
    }

    /**
     * Sets a unique character to be used in the progress bar indicator.
     *
     * @param string $char
     * @return Progress
     * @throws InvalidArgumentException
     */
    public function setBarChar(string $char): Progress
    {
        if (strlen($char) !== 1) {
            throw new InvalidArgumentException('The progress bar character must be of length 1.');
        }
        $this->barChar = $char;
        return $this;
    }
}

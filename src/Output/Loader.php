<?php

namespace TextUI\Output;

use TextUI\Screen\Command;
use TextUI\Utils;

/**
 * Loader and dynamic labels to indicate that a particular task is running.
 *
 * @author everton3x
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Loader implements MeterInterface
{
    protected string $label = '';
    /**
     *
     * @var array<string>
     */
    protected array $indicators = ['    ', '   .', '  . ', ' .  ', '.   ', '.  .', '. . ', '..  ', '.. .', '... ', '....', '... ', '.. .', '..  ', '. . ', '.  .', '.   ', ' .  ', '  . ', '   .'];
    protected int $currentIndicatorIndex = 0;

    public function __construct()
    {
    }

    /**
     * Sets a label for the task.
     *
     * @param string $label
     * @return Loader
     */
    public function setLabel(string $label): Loader
    {
        $this->label = $label;
        return $this;
    }

    /**
     * Starts the loader with an initial label that can be changed during task
     * execution with Loader::setLabel().
     *
     * @param string $label
     * @return void
     */
    public function init(string $label): void
    {
        $this->setLabel($label);
        $this->update();
    }

    /**
     * Updates the task, changing the loader and label.
     *
     * @return void
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function update(): void
    {
        Command::send(Command::CLEAR_LINE);
        Command::send(Command::GOTO_LINE_START);
        $maxIndicatorIndex = count($this->indicators) - 1;
        switch ($this->currentIndicatorIndex >= $maxIndicatorIndex) {
            case true:
                $this->currentIndicatorIndex = 0;
                break;
            default:
                $this->currentIndicatorIndex++;
        }
        $currentIndicator = $this->indicators[$this->currentIndicatorIndex];
        $label = $this->padLabel($this->label, strlen($this->indicators[$this->currentIndicatorIndex]));
        echo "{$label}[{$currentIndicator}]";
        Command::send(Command::GOTO_LINE_START);
    }

    /**
     *
     * @param string $label
     * @param int $indicatorWidth
     * @return string
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function padLabel(string $label, int $indicatorWidth): string
    {
        $cols = Utils::detectScreenColumns();
        $labelWidth = $cols - $indicatorWidth - 3;
        return str_pad($label, $labelWidth, ' ', STR_PAD_RIGHT);
    }

    /**
     * Indicates task completion by displaying a custom indicator. Defaults is "Ok".
     *
     * @param string $indicator
     * @return void
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function end(string $indicator = 'Ok'): void
    {
        Command::send(Command::CLEAR_LINE);
        Command::send(Command::GOTO_LINE_START);
        $label = $this->padLabel($this->label, strlen($indicator));
        echo "{$label}[{$indicator}]";
    }
}

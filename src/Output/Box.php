<?php

namespace TextUI\Output;

use InvalidArgumentException;
use TextUI\Enum\HAlign;
use TextUI\Utils;

/**
 * Draws a box with text.
 *
 * @author everton3x
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Box implements DrawableInterface
{
    protected string $topLeftCornerChar = '+';
    protected string $bottomLeftCornerChar = '+';
    protected string $topRightCornerChar = '+';
    protected string $bottomRightCornerChar = '+';
    protected string $horizontalBorderChar = '-';
    protected string $verticalBorderChar = '|';
    protected HAlign $horizontalAlign;
    protected readonly string $content;

    public function __construct(string $content)
    {
        $this->horizontalAlign = HAlign::Center;
        $this->content = $content;
    }

    /**
     * Sets a single character to be displayed in the upper left corner of the
     * box. The default is `+`.
     *
     * @param string $char
     * @return Box
     * @throws InvalidArgumentException
     */
    public function setTopLeftCorner(string $char): Box
    {
        if (strlen($char) !== 1) {
            throw new InvalidArgumentException('$char must be a single character.');
        }
        $this->topLeftCornerChar = $char;
        return $this;
    }

    /**
     * Sets a single character to be displayed in the lower left corner of the
     * box. The default is `+`.
     *
     * @param string $char
     * @return Box
     * @throws InvalidArgumentException
     */
    public function setBottomLeftCorner(string $char): Box
    {
        if (strlen($char) !== 1) {
            throw new InvalidArgumentException('$char must be a single character.');
        }
        $this->bottomLeftCornerChar = $char;
        return $this;
    }

    /**
     * Sets a single character to be displayed in the upper right corner of the
     * box. The default is `+`.
     *
     * @param string $char
     * @return Box
     * @throws InvalidArgumentException
     */
    public function setTopRightCorner(string $char): Box
    {
        if (strlen($char) !== 1) {
            throw new InvalidArgumentException('$char must be a single character.');
        }
        $this->topRightCornerChar = $char;
        return $this;
    }

    /**
     * Sets a single character to be displayed in the lower right corner of the
     * box. The default is `+`.
     *
     * @param string $char
     * @return Box
     * @throws InvalidArgumentException
     */
    public function setBottomRightCorner(string $char): Box
    {
        if (strlen($char) !== 1) {
            throw new InvalidArgumentException('$char must be a single character.');
        }
        $this->bottomRightCornerChar = $char;
        return $this;
    }

    /**
     * Sets a single character to be used as the vertical border of the box.
     * The default is `-`.
     *
     * @param string $char
     * @return Box
     * @throws InvalidArgumentException
     */
    public function setHLine(string $char): Box
    {
        if (strlen($char) !== 1) {
            throw new InvalidArgumentException('$char must be a single character.');
        }
        $this->horizontalBorderChar = $char;
        return $this;
    }

    /**
     * Sets a single character to be used as the vertical border of the box.
     * The default is `-`.
     *
     * @param string $char
     * @return Box
     * @throws InvalidArgumentException
     */
    public function setVLine(string $char): Box
    {
        if (strlen($char) !== 1) {
            throw new InvalidArgumentException('$char must be a single character.');
        }
        $this->verticalBorderChar = $char;
        return $this;
    }

    /**
     * Set the horizontal alignment of the text (centered, left, or right). The
     * default is centered.
     *
     * @param HAlign $halign
     * @return Box
     */
    public function setHAlign(HAlign $halign): Box
    {
        $this->horizontalAlign = $halign;
        return $this;
    }

    /**
     * Displays the box on the screen.
     *
     * @return void
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function draw(): void
    {
        $cols = Utils::detectScreenColumns();
        $hline = str_repeat($this->horizontalBorderChar, $cols - 2);
        echo "{$this->topLeftCornerChar}$hline{$this->topRightCornerChar}" . PHP_EOL;
        // left border (1) - space (1) - content(x) - space (1) - right border (1)
        $paragraphLen = $cols - 4;
        if ($paragraphLen < 1) {
            throw new InvalidArgumentException('Width of column not less than 0.');
        }
        $paragraphs = Utils::strToParagraph($this->content, $paragraphLen);
        foreach ($paragraphs as $line) {
            switch ($this->horizontalAlign) {
                case HAlign::Center:
                    $align = STR_PAD_BOTH;
                    break;
                case HAlign::Left:
                    $align = STR_PAD_RIGHT;
                    break;
                case HAlign::Right:
                    $align = STR_PAD_LEFT;
                    break;
            }
            $contentPadded = str_pad($line, $paragraphLen, ' ', $align);
            echo "{$this->verticalBorderChar} {$contentPadded} {$this->verticalBorderChar}" . PHP_EOL;
        }
        echo "{$this->bottomLeftCornerChar}$hline{$this->bottomRightCornerChar}" . PHP_EOL;
    }
}

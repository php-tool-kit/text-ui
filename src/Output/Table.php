<?php

namespace TextUI\Output;

use InvalidArgumentException;
use TextUI\Enum\HAlign;
use TextUI\Utils;

/**
 * Draws a table for displaying tabular data, with support for header, footer,
 * alignment, and column sizes.
 *
 * @author everton3x
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 *
 */
class Table implements DrawableInterface
{
    protected string $intersectionChar = '+';
    protected string $simpleHorizontalBorderChar = '-';
    protected string $verticalBorderChar = '|';
    protected string $specialHorizontalBorderChar = '=';
    /**
     *
     * @var array<mixed>
     */
    protected readonly array $data;
    /**
     *
     * @var array<string>
     */
    protected array $header = [];
    /**
     *
     * @var array<string>
     */
    protected array $footer = [];
    /**
     *
     * @var array<int>
     */
    protected array $width = [];
    /**
     *
     * @var array<int>
     */
    protected array $align = [];

    /**
     *
     * @param array<mixed> $data The data to display is in the form of a
     * multidimensional array rows->columns. Rows must have numeric keys, and
     * columns can have numeric or text keys.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Draw the table.
     *
     * @return void
     */
    public function draw(): void
    {
        $colsLen = $this->calcColsLen($this->data, $this->width);

        if ($this->header !== []) {
            echo $this->buildHeaderStr($this->header, $colsLen, $this->align);
        }
        echo $this->buildBodyStr($this->data, $colsLen, $this->align);
        if ($this->footer !== []) {
            echo $this->buildFooterStr($this->footer, $colsLen, $this->align);
        }
    }

    /**
     * Sets the table header.
     *
     * @param array<string> $header A simple array with the header labels. It should
     * have the same number of columns as the data.
     * @return Table
     * @throws InvalidArgumentException
     */
    public function setHeader(array $header): Table
    {
        if (count($header) !== count($this->data[array_key_first($this->data)])) {
            throw new InvalidArgumentException("Number of header columns must be equal to number of data columns.");
        }
        $this->header = $header;
        return $this;
    }

    /**
     * Sets the table footer.
     *
     * @param array<string> $footer A simple array with the footer labels or values.
     * It should have the same number of columns as the data.
     * @return Table
     * @throws InvalidArgumentException
     */
    public function setFooter(array $footer): Table
    {
        if (count($footer) !== count($this->data[array_key_first($this->data)])) {
            throw new InvalidArgumentException("Number of footer columns must be equal to number of data columns.");
        }
        $this->footer = $footer;
        return $this;
    }

    /**
     * Sets the column widths.
     *
     * Values ​​can be expressed in whole numbers representing parts of the
     * whole or in decimals representing percentages.
     *
     * @param array<int> $widths The widths of each column, as many as there are
     * columns of data.
     * @return Table
     * @throws InvalidArgumentException
     */
    public function setColWidths(array $widths): Table
    {
        if (count($widths) !== count($this->data[array_key_first($this->data)])) {
            throw new InvalidArgumentException("Number of column widths must be "
                    . "equal to the number of columns in the data.");
        }
        $this->width = $widths;
        return $this;
    }

    /**
     * Sets the horizontal alignment (center, right, or left - the default) of
     * cell contents, including the header and footer.
     *
     * It is not necessary to indicate alignment for all columns. Columns are
     * identified by the 0-indexed key according to the order in which they
     * appear in the data.
     *
     * @param array<int> $alignements Array of TextUI\Enum\HAlign
     * @return Table
     */
    public function setHorizontalAlignements(array $alignements): Table
    {
        $this->align = $alignements;
        return $this;
    }

    /**
     *
     * @param array<string> $footer
     * @param array<int> $colsLen
     * @param array<int> $align
     * @return string
     */
    protected function buildFooterStr(array $footer, array $colsLen, array $align): string
    {
        $str = '';
        $dataSplitted = $this->splitCells([$footer], $colsLen);
        foreach ($dataSplitted as $row) {
            $maxParagraph = $this->calcMaxParagraphByRow($row);
            for ($paragraphCounter = 0; $paragraphCounter < $maxParagraph; $paragraphCounter++) {
                $colCounter = 0;
                foreach ($row as $col) {
                    $alignment = $this->getAlign($colCounter, $align);
                    $content = str_pad($col[$paragraphCounter] ?? '', ($colsLen[$colCounter] - 4), ' ', $alignment);
                    $str .= "{$this->verticalBorderChar} {$content} ";
                    $colCounter++;
                }
                $str .= $this->verticalBorderChar . PHP_EOL;
            }
        }
        $str .= $this->buildHeaderHorizontalSeparatorStr($colsLen);
        return $str;
    }

    /**
     *
     * @param array<string> $header
     * @param array<int> $colsLen
     * @param array<int> $align
     * @return string
     */
    protected function buildHeaderStr(array $header, array $colsLen, array $align): string
    {
        $str = '';
        $hBorder = $this->buildHeaderHorizontalSeparatorStr($colsLen);

        $dataSplitted = $this->splitCells([$header], $colsLen);

        foreach ($dataSplitted as $row) {
            $str .= $hBorder;

            $maxParagraph = $this->calcMaxParagraphByRow($row);
            for ($paragraphCounter = 0; $paragraphCounter < $maxParagraph; $paragraphCounter++) {
                $colCounter = 0;
                foreach ($row as $col) {
                    $alignment = $this->getAlign($colCounter, $align);
                    $content = str_pad($col[$paragraphCounter] ?? '', ($colsLen[$colCounter] - 4), ' ', $alignment);
                    $str .= "{$this->verticalBorderChar} {$content} ";
                    $colCounter++;
                }
                $str .= $this->verticalBorderChar . PHP_EOL;
            }
        }


        return $str;
    }

    /**
     *
     * @param array<mixed> $bodyData
     * @param array<int> $colsLen
     * @param array<int> $align
     * @return string
     *
     */
    protected function buildBodyStr(array $bodyData, array $colsLen, array $align): string
    {
        $str = '';
        $dataSplitted = $this->splitCells($bodyData, $colsLen);
        foreach ($dataSplitted as $rowIndex => $row) {
            switch ($rowIndex) {
                case 0:
                    $str .= $this->buildHeaderHorizontalSeparatorStr($colsLen);
                    break;
                default:
                    $str .= $this->buildBodyHorizontalSeparatorStr($colsLen);
            }
            $maxParagraph = $this->calcMaxParagraphByRow($row);
            for ($paragraphCounter = 0; $paragraphCounter < $maxParagraph; $paragraphCounter++) {
                $colCounter = 0;
                foreach ($row as $col) {
                    $alignment = $this->getAlign($colCounter, $align);
                    switch (key_exists($paragraphCounter, $col)) {
                        case true:
                            $paragraph = $col[$paragraphCounter];
                            break;
                        default:
                            $paragraph = '';
                    }
                    $content = str_pad($paragraph, ($colsLen[$colCounter] - 4), ' ', $alignment);
                    $str .= "{$this->verticalBorderChar} {$content} ";
                    $colCounter++;
                }
                $str .= $this->verticalBorderChar . PHP_EOL;
            }
        }
        $str .= $this->buildHeaderHorizontalSeparatorStr($colsLen);
        return $str;
    }

    /**
     *
     * @param int $colIndex
     * @param array<int> $align
     * @return int
     * @throws InvalidArgumentException
     */
    protected function getAlign(int $colIndex, array $align): int
    {
        if (key_exists($colIndex, $align)) {
            switch ($align[$colIndex]) {
                case HAlign::Center:
                    return STR_PAD_BOTH;
                case HAlign::Left:
                    return STR_PAD_RIGHT;
                case HAlign::Right:
                    return STR_PAD_LEFT;
                default:
                    throw new InvalidArgumentException("Invalid align value.");
            }
        }
        return STR_PAD_RIGHT;
    }

    /**
     *
     * @param array<int, array<string>> $row
     * @return int
     */
    protected function calcMaxParagraphByRow(array $row): int
    {
        $maxParagraph = 0;
        foreach ($row as $col) {
            $numParagraphs = count($col);
            if ($numParagraphs > $maxParagraph) {
                $maxParagraph = $numParagraphs;
            }
        }
        return $maxParagraph;
    }

    /**
     *
     * @param array<int, array<string>> $data
     * @param array<int> $colsLen
     * @return array<mixed>
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function splitCells(array $data, array $colsLen): array
    {
        $dataSplitted = [];
        foreach ($data as $rowIndex => $row) {
            $colCounter = 0;
            foreach ($row as $colIndex => $cell) {
                $len = ($colsLen[$colCounter] - 4);
                if ($len < 1) {
                    throw new InvalidArgumentException('Width of column not less than 0.');
                }
                settype($cell, 'string');
                $dataSplitted [$rowIndex][$colIndex] = Utils::strToParagraph($cell, $len);
                $colCounter++;
            }
        }
        return $dataSplitted;
    }

    /**
     *
     * @param array<int> $colsLen
     * @return string
     */
    protected function buildBodyHorizontalSeparatorStr(array $colsLen): string
    {
        $str = $this->intersectionChar;
        foreach ($colsLen as $width) {
            $str .= str_repeat($this->simpleHorizontalBorderChar, ($width - 2));
            $str .= $this->intersectionChar;
        }
        return $str . PHP_EOL;
    }

    /**
     *
     * @param array<int> $colsLen
     * @return string
     */
    protected function buildHeaderHorizontalSeparatorStr(array $colsLen): string
    {
        $str = $this->intersectionChar;
        foreach ($colsLen as $width) {
            $str .= str_repeat($this->specialHorizontalBorderChar, ($width - 2));
            $str .= $this->intersectionChar;
        }
        return $str . PHP_EOL;
    }

    /**
     *
     * @param array<mixed> $data
     * @param array<int> $width
     * @return array<int>
     */
    protected function calcColsLen(array $data, array $width = []): array
    {
        if ($width === []) {
            return $this->calcColsLenFromData($data);
        }
        return $this->calcColsLenFromWidth($width);
    }

    /**
     *
     * @param array<mixed> $data
     * @return array<int>
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function calcColsLenFromData(array $data): array
    {
        $cols = Utils::detectScreenColumns();
        $numCols = count((array) $data[array_key_first($data)]);
        $width = floor($cols / $numCols);
        return array_fill(0, $numCols, (int) $width);
    }

    /**
     *
     * @param array<int> $width
     * @return array<int>
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function calcColsLenFromWidth(array $width): array
    {
        $lens = [];
        $cols = Utils::detectScreenColumns();
        $totalLen = array_sum($width);
        $sum = 0;
        foreach ($width as $len) {
            $perc = $len / $totalLen;
            $size = (int) floor($cols * $perc);
            $sum += $size;
            if ($sum <= $cols) {
                $lens[] = $size;
                continue;
            }
            $lens[] = $cols - ($sum - $size);
        }
        return $lens;
    }

    /**
     * Sets the single character that is used at the intersection between edges.
     *
     * @param string $char
     * @return Table
     * @throws InvalidArgumentException
     */
    public function setIntersectionChar(string $char): Table
    {
        if (strlen($char) !== 1) {
            throw new InvalidArgumentException('$char must be length 1.');
        }
        $this->intersectionChar = $char;
        return $this;
    }

    /**
     * Sets the single character that is used for horizontal borders between
     * data.
     *
     * @param string $char
     * @return Table
     * @throws InvalidArgumentException
     */
    public function setSimpleHorizontalBorderChar(string $char): Table
    {
        if (strlen($char) !== 1) {
            throw new InvalidArgumentException('$char must be length 1.');
        }
        $this->simpleHorizontalBorderChar = $char;
        return $this;
    }

    /**
     * Sets the single character that is used for vertical borders.
     *
     * @param string $char
     * @return Table
     * @throws InvalidArgumentException
     */
    public function setSimpleVerticalBorderChar(string $char): Table
    {
        if (strlen($char) !== 1) {
            throw new InvalidArgumentException('$char must be length 1.');
        }
        $this->verticalBorderChar = $char;
        return $this;
    }

    /**
     * Sets the single character that is used for the horizontal borders of
     * the header and footer and table boundaries.
     *
     * @param string $char
     * @return Table
     * @throws InvalidArgumentException
     */
    public function setSpecialHorizontalBorderChar(string $char): Table
    {
        if (strlen($char) !== 1) {
            throw new InvalidArgumentException('$char must be length 1.');
        }
        $this->specialHorizontalBorderChar = $char;
        return $this;
    }
}

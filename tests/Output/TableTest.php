<?php
namespace TextUI\Tests\Output;

use PHPUnit\Framework\TestCase;
use TextUI\Enum\HAlign;
use TextUI\Output\Table;
use function count;

/**
 * Unit tests for TextUI\Output\Table class.
 *
 * @author everton3x
 */
class TableTest extends TestCase
{

    public function testSingleLineDraw(): void
    {
        $data = [
            [
                'name' => 'John',
                'age' => 43,
                'gender' => 'M'
            ],
            [
                'name' => 'Mary',
                'age' => 38,
                'gender' => 'F'
            ],
            [
                'name' => 'Donn',
                'age' => 13,
                'gender' => 'M'
            ],
            [
                'name' => 'Peter',
                'age' => 9,
                'gender' => 'M'
            ],
        ];

        $header = ['Name', 'Age', 'Gender'];
        $footer = ['', 'Itens', count($data)];
        $aligns = [HAlign::Left, HAlign::Right, HAlign::Center];

        $table = new Table($data);
        $table
            ->setHorizontalAlignements($aligns)
            ->setColWidths([40, 20, 10])
            ->setHeader($header)
            ->setFooter($footer)
        ;
        $expectedOutput = file_get_contents('tests/assets/table-single-line.output');
        $this->expectsOutput($expectedOutput);
        $table->draw();
    }

    public function testMultiLineDraw(): void
    {
        $largeData = [
            [
                'product' => 'Playstation Sony 4',
                'description' => 'The all new lighter and slimmer PlayStation4 system has a 500GB hard drive for all of the greatest games, TV, music and more. Incredible Games.',
                'price' => 224.99
            ],
            [
                'product' => 'Nintendo Switch',
                'description' => 'Play at home or on the go with a vibrant OLED screen Play on your TV at home or pick up and play in handheld mode on a vivid 7-inch OLED screen.',
                'price' => 349.99
            ],
            [
                'product' => 'Xbox Series S',
                'description' => 'Experience the best value in gaming with Xbox Series S, now available with a 1TB SSD in Robot White.',
                'price' => 348.0
            ],
        ];
        $largeTable = new \TextUI\Output\Table($largeData);
        $largeTable
            ->setHorizontalAlignements([2 => \TextUI\Enum\HAlign::Right])
            ->setColWidths([.3, .6, .1])
            ->setHeader(['Product', 'Description', 'Price'])
        ;
        $expectedOutput = file_get_contents('tests/assets/table-multi-line.output');
        $this->expectsOutput($expectedOutput);
        $largeTable->draw();
    }
}

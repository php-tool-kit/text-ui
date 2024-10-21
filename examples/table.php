<?php

require_once 'vendor/autoload.php';

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
$aligns = [\TextUI\Enum\HAlign::Left, \TextUI\Enum\HAlign::Right, \TextUI\Enum\HAlign::Center];

$table = new \TextUI\Output\Table($data);
$table
        ->setHorizontalAlignements($aligns)
        ->setColWidths([40, 20, 10])
        ->setHeader($header)
        ->setFooter($footer)
;
$table->draw();

echo PHP_EOL;
echo PHP_EOL;

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
        ->setHorizontalAlignements([2=> \TextUI\Enum\HAlign::Right])
        ->setColWidths([.3,.6,.1])
        ->setHeader(['Product', 'Description', 'Price'])
;
$largeTable->draw();
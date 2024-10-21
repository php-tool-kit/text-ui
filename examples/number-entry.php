<?php
require_once 'vendor/autoload.php';

$number = (new TextUI\Input\NumberEntry('Enter de number: '))
        ->setDecimals(2)
        ->setOutputType('float')
        ->setDecimalPoint(',')
        ->setThousandsSep('.');
echo "You entered '{$number->read()}'".PHP_EOL;
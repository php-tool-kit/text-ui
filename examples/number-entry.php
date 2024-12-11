<?php
require_once 'vendor/autoload.php';

$number = (new TextUI\Input\NumberEntry('Enter de number: '))
        ->setDecimals(2)
        ->setOutputType('float')
        ->setDecimalPoint(',')
        ->setThousandsSep('.')
        ->setDefault('9.123,45');
echo "You entered '{$number->read()}'".PHP_EOL;
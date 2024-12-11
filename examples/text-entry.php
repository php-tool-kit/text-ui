<?php

require_once 'vendor/autoload.php';

$text = new TextUI\Input\TextEntry('Enter de text: ');
$text->setDefault('Hello world.');
echo "You entered '{$text->read()}'".PHP_EOL;
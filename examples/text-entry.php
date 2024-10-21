<?php

require_once 'vendor/autoload.php';

$text = new TextUI\Input\TextEntry('Enter de text: ');
echo "You entered '{$text->read()}'".PHP_EOL;
<?php

require_once 'vendor/autoload.php';

$text = new TextUI\Input\TextMultiLineEntry('Enter de text (blank line to finish):'. PHP_EOL);
echo var_representation($text->read());
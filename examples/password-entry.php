<?php

require_once 'vendor/autoload.php';

$pwd = new TextUI\Input\PasswordEntry('Enter de password: ');
echo "You entered '{$pwd->read()}'".PHP_EOL;
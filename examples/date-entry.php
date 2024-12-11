<?php
require_once 'vendor/autoload.php';

$date = new TextUI\Input\DateEntry('Enter de date [ddmmyyyy]: ', 'dmY');
$date->setDefault('12051981');
echo "You entered '{$date->read()->format('d/m/Y')}'".PHP_EOL;

$time = new TextUI\Input\DateEntry('Enter de date [hh:mm]: ', 'Y-m-d H:i');
$time->setParser(function($entry){
    return date('Y-m-d').' '.$entry;
});
echo "You entered '{$time->read()->format('H:i')}'".PHP_EOL;
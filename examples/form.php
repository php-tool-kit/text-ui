<?php

use TextUI\Input\Form\Form;
use TextUI\Input\NumberEntry;
use TextUI\Input\TextEntry;
require_once 'vendor/autoload.php';

$form = new Form('My Form');

$form->addEntry('text', new TextEntry('Text: '))
    ->addEntry('number', new NumberEntry('Number: '))
//    ->addEntry('date', new TextUI\Input\DateEntry('Date [YYYY-MM-DD]: ', 'Y-m-d'))
    ;

$form->ask();

print_r($form->getAnswers());
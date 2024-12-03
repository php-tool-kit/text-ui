<?php

use TextUI\Input\Form\Form;
use TextUI\Input\NumberEntry;
use TextUI\Input\TextEntry;
require_once 'vendor/autoload.php';

$form = new Form('My Form');

$form->addEntry('text', new TextEntry('Text: '))
    ->addEntry('number', new NumberEntry('Number: '))
    ->addEntry('date', new TextUI\Input\DateEntry('Date [YYYY-MM-DD]: ', 'Y-m-d'))
    ->addEntry('pwd', new \TextUI\Input\PasswordEntry('Password: '))
    ->addEntry('multiline', new TextUI\Input\TextMultiLineEntry('Textarea: '))
    ->addEntry('single', new TextUI\Input\SingleSelector('Select one: ', ['Option 1', 'Option 2', 'Option 3']))
    ->addEntry('multi', new TextUI\Input\MultiSelector('Select one or more: ', ['Option 1', 'Option 2', 'Option 3']))
    ;

$form->ask();

print_r($form->getAnswers());
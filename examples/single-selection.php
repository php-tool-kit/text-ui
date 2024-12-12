<?php

require_once 'vendor/autoload.php';

$select = new TextUI\Input\SingleSelector('Select 1 option:', ['Mary', 'Anne', 'Peter', 'John', 'Roger', 'Alice', 'Michel', 'Bob', 'Yule', 'Ralph', 'Billy', 'b' => 'Back', 'Q' => 'Quit']);
$select->setSelectionInvalidMessage('Option is invalid!');
//$select->returnOptionKey = true;
$select->setPrompt('Choose: ');
$select->setDefault('Peter');
echo "You selected '{$select->read()}'".PHP_EOL;
<?php

require_once 'vendor/autoload.php';

$select = new TextUI\Input\MultiSelector('Select options:', ['Mary', 'Anne', 'Peter', 'John', 'Roger', 'Alice', 'Michel', 'Bob', 'Yule', 'Ralph', 'Billy', 'b' => 'Back', 'Q' => 'Quit']);
$select->setSelectionInvalidMessage('Option is invalid!');
//$select->returnOptionKey = true;
//$select->clearScreen = false;
$select->setPrompt('Choose: ');
$select->setDefault('Anne', 'Roger');
print_r($select->read());
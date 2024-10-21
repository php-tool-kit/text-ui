<?php

require_once 'vendor/autoload.php';

$select = new TextUI\Input\MultiSelector('Select options:', ['Mary', 'Anne', 'Peter', 'John', 'Roger', 'Alice', 'Michel', 'Bob', 'Yule', 'Ralph', 'Billy']);
$select->setSelectionInvalidMessage('Option is invalid!');
//$select->returnOptionKey = true;
//$select->clearScreen = false;
$select->setPrompt('Choose: ');
echo var_representation($select->read());
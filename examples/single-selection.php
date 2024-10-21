<?php

require_once 'vendor/autoload.php';

$select = new TextUI\Input\SingleSelector('Select 1 option:', ['Mary', 'Anne', 'Peter', 'John', 'Roger', 'Alice', 'Michel', 'Bob', 'Yule', 'Ralph', 'Billy']);
$select->setSelectionInvalidMessage('Option is invalid!');
//$select->returnOptionKey = true;
$select->setPrompt('Choose: ');
echo "You selected '{$select->read()}'".PHP_EOL;
<?php

require_once 'vendor/autoload.php';

$loader = new TextUI\Output\Loader('Iniciando teste...');
$loader->init("Iniciando...");
sleep(1);
for ($i = 0; $i < 25; $i++){
    sleep(1);
    $loader->setLabel("Passo $i");
    $loader->update();
}
$loader->end();


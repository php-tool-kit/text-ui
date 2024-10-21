<?php

require_once 'vendor/autoload.php';



$max = 12;
$progress = new TextUI\Output\Progress($max);
//$progress->times = false;
//$progress->units = false;
//$progress->setBarChar('#');
$progress->init();
sleep(1);
for ($i = 0; $i <= $max; $i++){
    sleep(1);
    $progress->update($i);
}
$progress->end();


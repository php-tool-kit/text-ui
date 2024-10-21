<?php
require_once 'vendor/autoload.php';

$hline1 = new \TextUI\Output\HLine();
$hline1->draw();

$hline2 = new \TextUI\Output\HLine('-*-');
$hline2->draw();
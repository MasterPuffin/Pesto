<?php

use MasterPuffin\Pesto\Pesto;

require_once "vendors/MasterPuffin/autoload.php";

spl_autoload_register(function ($class_name) {
	include "Classes/" . $class_name . '.php';
});

header('Content-Type:text/plain');


$p = new Pesto(__DIR__ . "/");
$p->enableCaching=false;

$r = $p->render('1');
print_r($r);
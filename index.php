<?php

use MasterPuffin\Pesto\Pesto;
use MasterPuffin\Pesto\Pesto2;

require_once "vendors/MasterPuffin/autoload.php";

spl_autoload_register(function ($class_name) {
	include "Classes/" . $class_name . '.php';
});

header('Content-Type:text/plain');
/*
$Pesto = new Pesto("");

$ro = \Views\Site::content();

$Pesto->render($ro);
*/
$p = new Pesto2(__DIR__ . "/Classes/");
$p->render('Site');
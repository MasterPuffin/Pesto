<?php

use MasterPuffin\Pesto\Pesto;

require_once "vendors/MasterPuffin/autoload.php";

spl_autoload_register(function ($class_name) {
	include "Classes/" . $class_name . '.php';
});

header('Content-Type:text/plain');

$Pesto = new Pesto("");

$ro = \Views\Site::content();

$Pesto->render($ro);
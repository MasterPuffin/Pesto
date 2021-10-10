<?php

use Pesto\Pesto;
spl_autoload_register(function ($class_name) {
	include "Classes/" . $class_name . '.php';
});

header('Content-Type:text/plain');

$Pesto = new Pesto("");

$ro = \Views\Site::action();

$Pesto->render($ro);
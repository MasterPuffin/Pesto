<?php

use Pesto\Pesto;
spl_autoload_register(function ($class_name) {
	include "Classes/" . $class_name . '.php';
});

header('Content-Type:text/plain');

$ro = \Views\Site::content();

Pesto::parse($ro);
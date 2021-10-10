<?php
spl_autoload_register(function ($className) {
    $className = str_replace('\\', '/', $className);
    if (strpos($className, 'MasterPuffin') !== false) {
        $fileName = str_replace('MasterPuffin/', '', $className);
	    if (file_exists(__DIR__ . '/' . $fileName . '.php')) {
            include_once $fileName . '.php';
            return true;
        }
    }
    return false;
});

<?php
error_reporting(E_ALL | E_STRICT);
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));
date_default_timezone_set('UTC');

// Translator hack
function __($s)
{
    return $s;
}

function findParentPath($path)
{
    $dir = __DIR__;
    $previousDir = '.';
    while (!is_dir($dir . '/' . $path)) {
        $dir = dirname($dir);
        if ($previousDir === $dir) return false;
        $previousDir = $dir;
    }
    return $dir . '/' . $path;
}

$vendorPath = findParentPath('vendor');

include $vendorPath . '/autoload.php';

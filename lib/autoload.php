<?php
function autoload($className)
{
    $className = ltrim($className, '\\');
    $fileName  = '';
    $namespace = '';
    if ($lastNsPos = strripos($className, '\\'))
    {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

    $loaded = false;

    $class_file = dirname(__FILE__) . '/' . $fileName;

    if (file_exists($class_file))
    {
        require $class_file;
    }
    else
    {
        throw new Exception('Unable to load class: ' . $class_file);
    }
}

spl_autoload_register('autoload');

require_once('vendor/autoload.php');
require_once('api/gsAPI.php');
require_once('config.php');
?>
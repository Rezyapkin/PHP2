<?php

namespace app\engine;

class Autoload
{

    public function loadClass($className) {
        $fileName = str_replace('\\', '/',$className);
        $fileName = ROOT . '/../' . str_replace('app/','',$fileName) . '.php';
        if (file_exists($fileName)) {
            include $fileName;
        }
    }

}
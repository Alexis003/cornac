<?php


class Cornac_Autoload {
    static public function autoload($name) {
    // @todo path may have to be configurable
        if (basename($_SERVER['PWD']) == 'auditeur'){
            $path = '../library';
        } else {
            $path = 'library';
        }

        $file = $path.'/'.str_replace('_', '/', $name).'.php';
        if (file_exists($file)) {
            include($file);
        }
    }
}

?>
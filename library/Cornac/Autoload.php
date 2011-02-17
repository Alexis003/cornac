<?php


class Cornac_Autoload {
    static public function autoload($name) {
        // @todo path may have to be configurable
        if (basename($_SERVER['PWD']) == 'auditeur'){
            $path = '../library';
        } elseif (basename($_SERVER['PWD']) == 'scripts') {
            $path = '../library';
        } elseif (isset($_SERVER['HTTP_HOST'])) {
            $path = '../library';
        } else {
            $path = 'library';
        }

        $file = $path.'/'.str_replace('_', '/', $name).'.php';
        if (file_exists($file)) {
            include($file);
        } else { 
            // @note no display here, please. May be some error handling?
        }
    }
}

?>
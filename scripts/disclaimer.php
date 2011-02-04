<?php

print "\n";
if (in_array('-W', $argv)) {
    print "Updating disclaimer\n\n";
    sleep(3);
    define("WRITE", true);
} else {
    define("WRITE", false);
}

$text = <<<TEXT
/*
   +----------------------------------------------------------------------+
   | Cornac, PHP code inventory                                           |
   +----------------------------------------------------------------------+
   | Copyright (c) 2010 - 2011 Alter Way Solutions (France)               |
   +----------------------------------------------------------------------+
   | This source file is subject to version 3.01 of the PHP license,      |
   | that is bundled with this package in the file LICENSE, and is        |
   | available through the world-wide-web at the following url:           |
   | http://www.php.net/license/3_01.txt                                  |
   | If you did not receive a copy of the PHP license and are unable to   |
   | obtain it through the world-wide-web, please send a note to          |
   | license@php.net so we can mail you a copy immediately.               |
   +----------------------------------------------------------------------+
   | Author: Damien Seguy <damien.seguy@gmail.com>                        |
   +----------------------------------------------------------------------+
 */
TEXT;

$olds = array(
"2010-12-31" => <<<TEXT
/*
   +----------------------------------------------------------------------+
   | Cornac, PHP code inventory                                           |
   +----------------------------------------------------------------------+
   | Copyright (c) 2010 Alter Way Solutions (France)                      |
   +----------------------------------------------------------------------+
   | This source file is subject to version 3.01 of the PHP license,      |
   | that is bundled with this package in the file LICENSE, and is        |
   | available through the world-wide-web at the following url:           |
   | http://www.php.net/license/3_01.txt                                  |
   | If you did not receive a copy of the PHP license and are unable to   |
   | obtain it through the world-wide-web, please send a note to          |
   | license@php.net so we can mail you a copy immediately.               |
   +----------------------------------------------------------------------+
   | Author: Damien Seguy <damien.seguy@gmail.com>                        |
   +----------------------------------------------------------------------+
 */
TEXT
,

);

/*
$olds['7/9/2010b'] = <<<TEXT

TEXT;
*/
include('../libs/write_ini_file.php');

global $OPTIONS;
$OPTIONS = array('ignore_dirs' => array('References',),
                 'ignore_ext' => array('js','html','log','txt','exp','ini','css','architect'),
                 'limit' => 0,
                 
        );
$fichiers = liste_directories_recursive('..');

//$fichiers = array('../cornac.php', 'disclaimer.php');

$stats = array('old' => 0,'ready' => 0,'already' => 0,'wrong' => 0);

foreach($fichiers as $fichier) {
    if (basename($fichier) == 'disclaimer.php') { continue; }
    
    $pathinfo = pathinfo($fichier);
    if (!isset($pathinfo['extension'])) { continue; }

    $code = file_get_contents($fichier);

    foreach($olds as $version => $old) {
        if (strpos($code, $old) !== false) {
            print "$fichier : old disclaimer found ($version)\n";
            $code = str_replace($old, '', $code);
            $stats['old']++;
        }
        if (WRITE) {
            file_put_contents($fichier, $code);
        }
    }

    if (preg_match('/\?>\s+/is', $code)) {
        $code = trim($code);
        file_put_contents($fichier, $code);
    }
    
    if (strpos($code, $text) !== false) {
//        print "$fichier : already fixed\n";
        $stats['already']++;
    } elseif (substr($code, 0, 25) == "#!/usr/bin/env php\n<?php\n") {
        print "$fichier : OK\n";
        $stats['ready']++;
        if (WRITE) {
            $code = "#!/usr/bin/env php\n<?php\n".$text.substr(trim($code), 25);
            file_put_contents($fichier, $code);
        }
    } elseif (substr($code, 0, 6) == "<?php ") {
        $code = "<?php\n".substr($code, 6);
        file_put_contents($fichier, $code);

        print "$fichier : OK\n";
        $stats['ready']++;
        if (WRITE) {
            $code = "<?php\n".$text.substr(trim($code), 6);
            file_put_contents($fichier, $code);
        }
    } elseif (substr($code, 0, 6) == "<?php\n") {
        print "$fichier : OK\n";
        $stats['ready']++;
        if (WRITE) {
            $code = "<?php\n".$text.substr(trim($code), 6);
            file_put_contents($fichier, $code);
        }
    } else {
        print "$fichier : not starting right\n";
        $stats['wrong']++;
    }
}

$total = array_sum($stats);
print "=============================\n";
foreach($stats as $stat => $nb) {
    print "$stat : $nb (".number_format($nb /$total * 100, 0)." %)\n";
}
print "=============================\n";
print "Total : $total\n";

?>
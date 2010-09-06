#!/usr/bin/php 
<?php
// @fer -d xdebug.profiler_enable=On

ini_set('memory_limit',234217728);

include('prepare/commun.php');
include('libs/tok.php');
include("prepare/analyseur.php");

// @doc Reading the name of the processed application

global $FIN; 
// Collecting tokens
$FIN['debut'] = microtime(true);

include('libs/getopts.php');

// @todo : exporter les informations d'options dans une inclusion
$args = $argv;
$help = get_arg($args, '-?') ;
if ($help) { help(); }

// @doc default values, stored in a INI file
$ini = get_arg_value($args, '-I', null);
if (!is_null($ini)) {
    global $INI;
    if (file_exists('ini/'.$ini)) {
        define('INI','ini/'.$ini);
    } elseif (file_exists('ini/'.$ini.".ini")) {
        define('INI','ini/'.$ini.".ini");
    } elseif (file_exists($ini)) {
        define('INI',$ini);
    } else {
        define('INI','ini/'.'cornac.ini');
    }
    $INI = parse_ini_file(INI, true);
} else {
    define('INI',null);
    $INI = array();
}
unset($ini);
// @todo : what happens if we can't find the .INI ?
print "Directives files : ".INI."\n";

// @doc Reading constantes that are in the .INI
define('TOKENS',(bool) get_arg($args, '-t'));
define('TEST'  ,(bool) get_arg($args, '-T'));
define('STATS' ,(bool) get_arg($args, '-S', false));
define('VERBOSE', (bool) get_arg($args, '-v'));

define('GABARIT', 'mysql,cache');

$templates = explode(',',GABARIT);
include('prepare/templates/template.php');
foreach ($templates as $id => $template) {
    include('prepare/templates/template.'.$template.'.php');
}

define('LOG' ,(bool) get_arg($args, '-l', false));
$limite = 0 + get_arg_value($args, '-i', 0);
if ($limite) {
    print "Cycles = $limite\n";
} else {
    $limite = -1;
}

/*
define('RECURSIVE' ,(bool) get_arg($args, '-r', false));

$dossier = get_arg_value($args, '-d', array());
if (!empty($dossier)) {
    if (substr($dossier, -1) == '/') {
        $dossier = substr($dossier, 0, -1);
    }

    if (!file_exists($dossier)) {
        print "Impossible de trouver le dossier '$dossier'\n Annulation\n";
        die();
    }

    print "Travail sur le dossier {$dossier} \n";
    
    $files = glob($dossier.'/*.php');
    $files = array_slice($files, 1, 1);
    
    foreach($files as $file) {
        print shell_exec("./tokenizeur.php  -T -i -1 -f \"".escapeshellarg($file)."\" -g ".GABARIT. " "." -I ".INI);
    }
    
    if (RECURSIVE) {
        $files = liste_directories_recursive($dossier);

        foreach($files as $file) {
            $code = file_get_contents($file);
            if (strpos($code, '<?') === false) { continue; }
            
            $commande = "./tokenizeur.php -f ".escapeshellarg($file)." -g ".GABARIT." -I ".INI;
            print $commande. "\n";
            print shell_exec($commande);
        }
        print "Done\n";
        die();
    }
    print "Done\n";
    die();
} elseif ($file = get_arg_value($args, '-f', '')) {
    print "Working on file '{$file}'\n";

    $objects = new arrayIterator(array($file => $file));
    $scriptsPHP = $objects;

} else {
    print "No files to work on\n";
    help();
}

*/
// @section end of options

$preparations = array();
    
$extra_cols = array();
foreach($preparations as $p) {
    $cols = $p->get_cols();
    foreach($cols as $nom => $definition) {
        $extra_cols[] = "$nom $definition ";
    }
}
if (count($extra_cols) > 0) {
    $extra_cols = ', '.join(', ', $extra_cols).", ";
} else {
    $extra_cols = '';
}

$tidbits = scandir('prepare');
foreach($tidbits as $module) {
    if ($module[0] == '.') { continue; }
    if ($module == 'token.php') { continue; }
}

include('libs/database.php');



while(1) {
$times = array('debut' => microtime(true));
// @todo attention, big TOCTOU!
$res = $database->query('SELECT * FROM tu_tasks WHERE completed = 0 LIMIT 1');
$row = $res->fetch(PDO::FETCH_ASSOC);

if (!$row) { 
    print "No more tasks to go on\n"; 
    die();
}

$database->query('UPDATE tu_tasks SET completed = 1, date_update=NOW() WHERE id = '.$database->quote($row['id']).' LIMIT 1');

$scriptsPHP = array($row['target'] => null);

global $file;
$FIN['fait'] = 0;
$FIN['trouves'] = 0;

foreach($scriptsPHP as $file => $object){
    $FIN['trouves']++;
    print $file."\n";
    if (!file_exists($file)) { 
        print "'$file' doesn't exist. Aborting\n";
        continue;
    }

// @doc 4177 is error_reporting for  E_COMPILE_ERROR|E_RECOVERABLE_ERROR|E_ERROR|E_CORE_ERROR (compilations error only)
    $exec = shell_exec('php -d short_open_tag=1 -d error_reporting=4177  -l '.escapeshellarg($file).' '); 
    if (trim($exec) != 'No syntax errors detected in '.$file) {
        print "Script \"$file\" can't be compiled by PHP\n$exec\n";
        die();
    }
    
    $code = file_get_contents($file);
    
    // @doc one must leave <?php and <?xml untouched
    // @doc one must also leave <?R& (\w\W), which are binary, not PI
    // @note only take into account <?\s 
    
    if ($c = preg_match_all('/<\\?(?!php)(\w?\s)/is', $code, $r) ) { 
        if (VERBOSE) {
            print "Fixing $c opening tags\n";
        }
        $code = preg_replace('/<\\?(?!php)(\w?\s)/is', '<?php'." ".'\1', $code);
    }
    // @todo this is too simple, but it works until now (binary, beware!)
    $code = str_replace('<?=', '<?php echo ', $code);
    
    $brut = @token_get_all($code);
    if (count($brut) == 0) {
        print "No token found. Aborting\n";
        die();
    }
    $nb_tokens_initial = count($brut);
    
    if (TOKENS) {
       print_r($brut);
       $times['fin'] = microtime(true);
       mon_die();
    }
    
    $root = new Token();
    $suite = null;
    $ligne = 0;
    
    foreach($brut as $id => $b) {
        $t = new Token();

        $t->setId($id);
        if (is_array($b)) {
            $t->setToken($b[0]);
            $t->setCode($b[1]);
            $t->setLine($b[2]);
            $ligne = $b[2];
        } else {
            $t->setCode($b);
            $t->setLine($ligne);
        }
        
        if (is_null($suite)) {
            $suite = $t;
            $root = $t;
        } else {
            $suite->append($t);
            $suite = $suite->getNext();
        }
        unset($brut[$id]);
        
    }

    $t = $root;
    mon_log("\nWSC \n");
    do {
        $t = whitespace::factory($t);
        $t = commentaire::factory($t);
        
        if ($t->getId() == 0 && $t != $root) {
            mon_log("New root : ".$t."");
            $root = $t;
        }

        if (VERBOSE) {
            print "$i) ".$t->getCode()."---- \n";
            $template = getTemplate($root, $file, 'tree');
            $template['tree']->affiche();
            unset($template);
            print "$i) ".$t->getCode()."---- \n";
       }
    } while ($t = $t->getNext());

    $analyseur = new analyseur();

    $nb_tokens_courant = -1;
    $nb_tokens_precedent = array(-1);

    $i = 0;
    while (1) {
        $i++;
        $t = $root;
        mon_log("\nCycle : ".$i."\n");
        $nb_tokens_precedent[] = $nb_tokens_courant;
        if (count($nb_tokens_precedent) > 3) {
            array_shift($nb_tokens_precedent); 
        }
        $nb_tokens_courant = 0;
        do {
            $t = $analyseur->upgrade($t);
            if (get_class($t) == 'Token') { $nb_tokens_courant++; }
            if ($t->getId() == 0 && $t != $root) {
                mon_log("New root : ".$t."");
                $root = $t;
            }

            if (VERBOSE) {
                print "$i) ".$t->getCode()."---- \n";
                $template = getTemplate($root, $file, 'tree');
                $template['tree']->affiche();
                unset($template);
                print "$i) ".$t->getCode()."---- \n";
           }
        } while ($t = $t->getNext());
        
        mon_log("Remaining tokens : ".$nb_tokens_courant."");
        
        if ($nb_tokens_courant == 0) {
            break 1;
        }
        
        if ($nb_tokens_courant == $nb_tokens_precedent[0] && 
            $nb_tokens_courant == $nb_tokens_precedent[1] &&
            $nb_tokens_courant == $nb_tokens_precedent[2]
            ) { 
            print "No more update at cycle #$i \n";

            break 1;
        }
        
        if ($i == $limite) {
            break 1;
        }
    }
    $nb_cycles_final = $i;

    if (TEST) {
        if ($nb_tokens_courant == 0) {
            print "OK\n";
        } else {
            print "$nb_tokens_courant remain to be processed\n";
        }
        die();
    }

    if (VERBOSE) {
        if ($nb_tokens_courant == 0) {
            print "Some tokens were not processed\n";
        } else {
            print "$nb_tokens_courant remain to be processed\n";
        }
    }

    $token = 0;
    $loop = $root;
    $id = 0;
    while(!is_null($loop)) {
        if (get_class($loop) == "Token") {
            $token++;
        }
        $loop = $loop->getNext();
        $id++;
    }
   
    $templates = getTemplate($root, $file);
    foreach($templates as $template) {
        $template->affiche();
        $template->save();
    }

    if (STATS) {
        include('prepare/template.stats.php');
        $template = new template_stats($root);
        $template->affiche();
        
        print $analyseur->verifs." checks were made\n";
        $stats = array_count_values($analyseur->rates);
        asort($stats);
        print_r($stats);
    }
}

$times['fin'] = microtime(true);

$database->query('UPDATE tu_tasks SET completed = 100, date_update=NOW() WHERE id = '.$database->quote($row['id']).' LIMIT 1');


$debut = $times['debut'];
unset($times['debut']);
foreach($times as $key => $valeur) {
    $times[$key] = floor(($valeur - $debut) * 1000);
}
}
mon_die();
?>
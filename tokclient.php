#!/usr/bin/env php
<?php

ini_set('memory_limit',1024*1024*1024);
error_reporting(E_ALL);
ini_set('display_errors', 'on');

include('prepare/common.php');
include("prepare/analyseur.php");
include('libs/tok.php');
include('prepare/templates/template.php');

$options = array('help' => array('help' => 'display this help',
                                 'option' => '?',
                                 'compulsory' => false),
                 'ini' => array('help' => 'configuration set or file',
                                 'get_arg_value' => null,
                                 'option' => 'I',
                                 'compulsory' => false),
                 'templates' => array('help' => 'output templates',
                                 'get_arg_value' => 'tree',
                                 'option' => 'g',
                                 'compulsory' => false),
                 'tokens' => array('help' => 'display tokens',
                                   'option' => 't',
                                   'compulsory' => false),
                 'test' => array('help' => 'works on tests.php file',
                                   'option' => 'T',
                                   'compulsory' => false),
                 'stats' => array('help' => 'display stats about the run',
                                   'option' => 'S',
                                   'compulsory' => false),
                 'verbose' => array('help' => 'make output verbose',
                                    'option' => 'v',
                                    'compulsory' => false),
                 'log' => array('help' => 'log activity',
                                          'option' => 'l',
                                          'compulsory' => false),
                 'slave' => array('help' => 'work as slave. -1 for infinite wait, 0 to process all in database, x to process only x files',
                                  'get_arg_value' => '0',
                                  'option' => 's',
                                  'compulsory' => false),
                 'limit' => array('help' => 'limit the number of cycles (-1 for no limit)',
                                  'get_arg_value' => -1,
                                  'option' => 'i',
                                  'compulsory' => false),
                 );
include('libs/getopts.php');

global $FIN;
// Collecting tokens
$FIN['debut'] = microtime(true);

// @todo make this work on tasks or individually

// @doc Reading constantes that are in the .INI
define('TEST',$INI['test']);
define('STATS',$INI['stats']);
define('VERBOSE',$INI['verbose']);

define('LOG',$INI['log']);
$limit = 0 + $INI['limit'];
if ($limit) {
    print "Cycles = $limit\n";
} else {
    $limit = -1;
}

include('libs/database.php');
$DATABASE = new database();
// @section end of options

// @todo : make a sleeping client here, that waits, not die.
$files_processed = 0;
$total = 0;
while( 1 ) {
    $total++;
    if ($INI['slave'] > 0 && ($files_processed >= intval($INI['slave']))) {
        print "Processed all $files_processed files. Finishing.\n";
        die();
    }

    $times = array('debut' => microtime(true));

// @warning attention, big TOCTOU!
    $query = 'SELECT * FROM <tasks> WHERE task="tokenize" AND completed = 0 LIMIT 1';
    $res = $DATABASE->query($query);
    $row = $res->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        if ($INI['slave'] == 0) {
            print "No more tasks to work on. Finishing.\n";
            die();
        } elseif ($INI['slave'] == -1) { // @note infinite loop
            print "Sleeping for 30 secondes\n";
            sleep(30);
            continue;
        } else {
            print "Sleeping for 30 secondes ( ".($INI['slave'] - $files_processed)." more to process)\n";
            sleep(30);
            continue;
        }
    }

    $DATABASE->query('UPDATE <tasks> SET completed = 1, date_update=NOW() WHERE id = '.$DATABASE->quote($row['id']).' LIMIT 1');

    print $row['target']."\n";
    $scriptsPHP = array($row['target'] => $row);
    $pf = new file_processor();
    
    if ($pf->process_file($scriptsPHP, $limit)) {
        if ($pf->messages['templates']) { 
//            print ".";
        } else {
            // @todo write to a lof file, or wait till the end to produce a long list. 
            print "T";
        }
        $completed = 100; 
    } else {
        if (isset($pf->messages['compile'])) {
            print $pf->messages['compile'];
            $completed = 3;
        } else {
            print "Error found\n";
            $completed = 2;
        }
        // @todo write to a lof file, or wait till the end to produce a long list. 
//        print "F";
    }
    if ($total % 60 == 0) {
        print " / $total\n";
    }

    $times['fin'] = microtime(true);

    $DATABASE->query('UPDATE <tasks> SET completed = '.$completed.', date_update=NOW() WHERE id = '.$DATABASE->quote($row['id']).' LIMIT 1');

    $debut = $times['debut'];
    unset($times['debut']);

    foreach($times as $key => $valeur) {
        $times[$key] = floor(($valeur - $debut) * 1000);
    }
}
mon_die();

class file_processor {

    function __construct() {
        $this->messages = array();
        $this->error = false;
    }
    
    function process_file($scriptsPHP, $limit) {
        global $file, $files_processed, $INI;
        $result = array();
    
        $FIN['fait'] = 0;
        $FIN['trouves'] = 0;
    
        list($file, $config) = each($scriptsPHP);
    
        $FIN['trouves']++;
        if (!file_exists($file)) {
            $this->messages['compile'] = "'$file' doesn't exist. Aborting\n";
            $this->error = true;
            continue;
        }
    
    // @doc 4177 is error_reporting for  E_COMPILE_ERROR|E_RECOVERABLE_ERROR|E_ERROR|E_CORE_ERROR (compilations error only)
        $exec = shell_exec('php -d short_open_tag=1 -d error_reporting=4177  -l '.escapeshellarg($file).' ');
        if (trim($exec) != 'No syntax errors detected in '.$file) {
            $this->messages['compile'] = "Script \"$file\" can't be compiled by PHP\n$exec\n";
            $this->error = true;
            return false;
        }
    
        $code = file_get_contents($file);
    
        // @doc one must leave <?php and <?xml untouched
        // @doc one must also leave <?R& (\w\W), which are binary, not PI
        // @note only take into account <?\s
    
        if ($c = preg_match_all('/<\\?(?!php)(\w?\s)/is', $code, $r) ) {
            if (VERBOSE) {
                // print "Fixing $c opening tags\n";
            }
            $code = preg_replace('/<\\?(?!php)(\w?\s)/is', '<?php'." ".'\1', $code);
        }
        // @todo this is too simple, but it works until now (binary, beware!)
        $code = str_replace('<?=', '<?php echo ', $code);
    
        // @todo abstract this function, so one can choose the PHP version for tokenization
        $raw = @token_get_all($code);
        if (count($raw) == 0) {
            $this->messages['compile'] = "No token found. Aborting\n";
            $this->error = true;
            return false;
        }
        if ($INI['tokens']) {
            print "Displaying tokens\n";
            print_r($raw);
            die();
        }
        $nb_tokens_initial = count($raw);
        
        // @note my PHP crashes at 400852 (511 zend_scan_black, or zval_mark_grey) beyong this limit, 
        // @note over 200k tokens is probably a large library of data. Not the most interesting
        if ($nb_tokens_initial > 200000) {
            $this->messages['compile'] = "Way too many tokens (> 200000)\n";
            $this->error = true;
            return false; 
        }
    
        $root = new Token();
        $suite = null;
        $ligne = 0;
    
        foreach($raw as $id => $b) {
            // @note actually removing all coments and whitespace even before turning them into token
            if (is_array($b) && in_array($b[0], array(T_COMMENT, T_DOC_COMMENT, T_WHITESPACE))) { continue; }
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
        }
        // @note this is less costly in terms of garbage collecting
        unset($raw);
    
        $analyseur = new analyseur();
    
        $nb_tokens_courant = -1;
        $nb_tokens_precedent = array(-1);
    
        mon_log("Init cycles\n");
        $i = 0;
        while (1) {
            $i++;
            $t = $root;
            mon_log("\nCycle : ".$i."\n$t\n");
            $nb_tokens_precedent[] = $nb_tokens_courant;
            if (count($nb_tokens_precedent) > 4) {
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
                    $template['tree']->display();
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
                $nb_tokens_courant == $nb_tokens_precedent[2] &&
                $nb_tokens_courant == $nb_tokens_precedent[3]
                ) {
                $this->messages['process'] = "No more update at cycle #$i \n";
                $this->error = true;
                // @note just abort the loop, so we may go on
                break 1;
            }
    
            if ($i == $limit) {
                break 1;
            }
        }
        $nb_cycles_final = $i;
    
        if (TEST || VERBOSE) {
            if ($nb_tokens_courant != 0) {
                $this->messages['process'] .= "$nb_tokens_courant remain to be processed\n";
                $this->error = true;
                return false;
            }
        }
/*
        if (VERBOSE) {
            if ($nb_tokens_courant == 0) {
                print "Some tokens were not processed\n";
            } else {
                print "$nb_tokens_courant remain to be processed\n";
            }
        }
*/
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
    
        $templates = getTemplate($root, $file, $config['template']);
        $this->messages['templates'] = true;
        foreach($templates as $name => $template) {
            $template->display();
            if ( $template->save() ) {
                $this->messages[$name] = '.';
            } else {
                $this->messages[$name] = 'F';
                $this->messages['templates'] = false;
                // @todo note down the failure, report it later.
            }
        }
    
        if (STATS) {
            include('prepare/template.stats.php');
            $template = new template_stats($root);
            $template->display();
    
            print $analyseur->verifs." checks were made\n";
            $stats = array_count_values($analyseur->rates);
            asort($stats);
            print_r($stats);
        }
    
        $files_processed++;
        return true;
    }

}
?>
<?php


global $ANALYSEUR;
// nom de l'application à tester
$ANALYSEUR['application']     = 'cligraphcrm_0.991';
$ANALYSEUR['db']['host']      = 'localhost';
$ANALYSEUR['db']['login']     = 'root';
$ANALYSEUR['db']['password']  = '';
$ANALYSEUR['db']['database']  = 'analyseur';

global $TOKENIZEUR;
// configuration pour le tokenizer
$TOKENIZEUR['limite'] = 10;
$TOKENIZEUR['verbose'] = 1;

global $ANALYSEUR;
// configuration pour l'analyseur
$ANALYSEUR['limite'] = 10;
$ANALYSEUR['analyses'] = array(
'affectations',
'arobase',
'boucles',
'comments',
'constants_usage',
'deffunction',
'define_constant',
'diexit',
'evals',
'execs',
'fichiers',
'function_frequency',
'globals',
'header',
'html_tags',
'includes',
'native_php_functions',
'phpconf',
'preg_use',
'printecho',
'regex',
'request',
'sql_protection',
'sql_queries',
'superglobales_in',
'urls',
'user_php_functions',
'var_majuscules',
'varcreation',
'vardump',
'variables',
'varvar',
'xml_exit',
'xml_in' );

global $EXPORTATEUR;
// configuration pour la production des rapports
$EXPORTATEUR['limite'] = 10;


/// configuration automatique
//include('tokens.php');

//include('classes/modules.php');
//include('classes/oneliners.php');
//include('classes/native_php_functions.php');

$mid = mysqli_connect($ANALYSEUR['db']['host'],$ANALYSEUR['db']['login'],$ANALYSEUR['db']['password'],$ANALYSEUR['db']['database']);

function __autoload($class_name) {
    if (file_exists('classes/'.$class_name . '.php')) {
        require_once 'classes/'.$class_name . '.php';
    }
    if (file_exists('prepare/'.$class_name . '.php')) {
//        print "Inclusion : $class_name\n";
        require_once 'prepare/'.$class_name . '.php';
    }
}

function is_crochet(Token $t) {
    $code = $t->getCode($t);
    
    return $code == '[' or $code == ']';
}

function is_pointvirgule($t) {
    if (!is_object($t)) { return false; } 
//    print get_class($t)."\n";
    if (!is_subclass_of($t,'Token') && 
        (get_class($t) != 'Token')) { 
        return false; 
    }
    
    $code = $t->getCode($t);
    
    return $code == ';';
}


class monArrayObject extends ArrayObject {

    function get_next($k = 0) {
        while($k < 100) {
            if ($this->offsetExists($k)) {
                return $this->offsetGet($k);
            }
            $k++;
        }

        return null;
    }

    function get_prev($k = 0) {
        while($k > -1) {
            if ($this->offsetExists($k)) {
                return $this->offsetGet($k);
            }
            $k--;
        }

        return null;
    }
}

?>
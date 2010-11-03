<?php
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

class template_cache extends template {
    protected $root = null;
    private $database = null;
    private $line = 0;
    private $scope = 'global';
    private $class = '';
    
    private $table = 'tokens';
    private $tags = array();
    
    function __construct($root, $fichier = null) {
        parent::__construct();
        
        global $INI;
        $this->table = $INI['cornac']['prefix'] ?: 'tokens';

        
        if (isset($INI['mysql']) && $INI['mysql']['active'] == true) {
            $this->database = new pdo($INI['mysql']['dsn'],$INI['mysql']['username'], $INI['mysql']['password']);

            $this->database->query('DELETE FROM '.$this->table.'_cache WHERE fichier = "'.$fichier.'"');
            $this->database->query('CREATE TABLE IF NOT EXISTS '.$this->table.'_cache (
                                                          id       INTEGER PRIMARY KEY AUTO_INCREMENT, 
                                                          code     VARCHAR(255),
                                                          fichier  VARCHAR(255)
                                                          )');
        } elseif (isset($INI['sqlite']) && $INI['sqlite']['active'] == true) {
            $this->database = new pdo($INI['sqlite']['dsn']);

            $this->database->query('DELETE FROM '.$this->table.'_cache WHERE fichier = "'.$fichier.'"');
            $this->database->query('CREATE TABLE IF NOT EXISTS '.$this->table.'_cache (
                                                          id       INTEGER PRIMARY KEY AUTOINCREMENT, 
                                                          code     VARCHAR(255),
                                                          fichier  VARCHAR(255)
                                                          )');
        } else {
            print "No database configuration provided (no db)\n";
            die(__METHOD__."\n");
        }
        
        $this->table_tags = $this->table.'_tags';

        $this->root = $root;
    }
    
    function save($filename = null) {
        print "Cache mis à jour\n";
        unset($this->database);
    }
    
    function affiche($node = null, $level = 0) {
        if ($level > 200) {
            print_r(xdebug_get_function_stack());        
            print "Attention : over 100 levels of recursion (Aborting) ".__METHOD__."\n"; 
            die(__METHOD__);
        }
        if (is_null($node)) {
            if ($level == 0) {
                $node = $this->root;
            } else {
                print_r(xdebug_get_function_stack());        
                print "Attempt to display a NULL value. Aborting.";
                die(__METHOD__);
            }
        }
        
        if (!is_object($node)) {
            print_r(xdebug_get_function_stack());        
            print "Fatal, $node is not an object (".gettype($node).")\n";
            die(__METHOD__);
        }
        $class = get_class($node);
        $method = "affiche_$class";
        
        if (method_exists($this, $method)) {
            $return = $this->$method($node, $level + 1);
        } else {
            print "Affichage ".__CLASS__." de '".$method."'\n";die;
        }
        if (!is_null($node->getNext())){
            $this->affiche($node->getNext(), $level);
        }

        return $return;
    }
    
////////////////////////////////////////////////////////////////////////
// database functions
////////////////////////////////////////////////////////////////////////
    function savenode($node) {
        global $file;
        
        $requete = "INSERT INTO {$this->table}_cache VALUES 
            (
             '".$node->database_id."',
             ".$this->database->quote($node->cache).",
             ".$this->database->quote($file)."
             )";
             

        $this->database->query($requete);
        if ($this->database->errorCode() != 0) {
            print $requete."\n";
            print_r($this->database->errorInfo());
            die();
        }

        return TRUE;
    }
////////////////////////////////////////////////////////////////////////
// database functions
////////////////////////////////////////////////////////////////////////
    function affiche_token_traite($node, $level) {
        $node->cache = $node->getCode();
    }

    function affiche_affectation($node, $level) {
        $droite = $node->getDroite();
        $this->affiche($droite, $level + 1);
        $operateur = $node->getOperateur();
        $this->affiche($operateur, $level + 1);
        $gauche = $node->getGauche();
        $this->affiche($gauche, $level + 1);
        
        $node->cache = $droite->cache.' '.$operateur->cache.' '.$gauche->cache;
        return $this->savenode($node);
    }

    function affiche_arginit($node, $level) {
        $var = $node->getVariable();
        $this->affiche($var, $level + 1);
        $valeur = $node->getValeur();
        $this->affiche($valeur, $level + 1);
        
        $node->cache = $var->cache." = ".$valeur->cache;
        return $this->savenode($node);
    }

    function affiche_arglist($node, $level) {
        $elements = $node->getList();
        if (count($elements) == 0) {
            $token_traite = new token_traite(new Token());
            $this->affiche($token_traite, $level + 1);
            $node->cache = '()';
            return;
        } else {
            $labels = array();
            foreach($elements as $id => &$e) {
                if (is_null($e)) {
                    $labels[] = '';
                    //rien
                } else {
                    $this->affiche($e, $level + 1);
                    if (!isset($e->cache)) {
                        print $e;
                        die();
                    }
                    $labels[] = $e->cache;
                }
            }
            $node->cache = '('.join(', ', $labels).')';
        }
    }

    function affiche_block($node, $level) {
        $elements = $node->getList();
        foreach($elements as $id => &$e) {
            $this->affiche($e, $level + 1);
        }
        
        $node->cache = ' { block }';
    }

    function affiche__break($node, $level) {
        $levels = $node->getLevels();
        $this->affiche($levels, $level + 1);

        $node->cache = 'break '.$levels->cache;
    }

    function affiche__case($node, $level) {
        $case = 'case';
        if (!is_null($m = $node->getComparant())) {
            $this->affiche($m, $level + 1);
            $case .= " ".$m->cache;
        }
        $this->affiche($node->getBlock(), $level + 1);
        // on ignore

        $node->cache = $case;
        return $this->savenode($node);
    }

    function affiche_cast($node, $level) {
        $expression = $node->getExpression();
        $this->affiche($expression, $level + 1);

        $node->cache = '('.$expression->cache.')';
    }

    function affiche__catch($node, $level) {
        $this->affiche($node->getException(), $level + 1);
        $this->affiche($node->getVariable(), $level + 1);
        $this->affiche($node->getBlock(), $level + 1);
        
        $node->cache = 'catch';
    }

    function affiche__continue($node, $level) {
        $levels = $node->getLevels();
        $this->affiche($levels, $level + 1);

        $node->cache = 'continue '.$levels->cache;
    }
    
    function affiche_cdtternaire($node, $level) {
        $condition = $node->getCondition();
        $this->affiche($condition, $level + 1);

        $vraie = $node->getVraie();
        $this->affiche($vraie, $level + 1);
        
        $faux = $node->getFaux();
        $this->affiche($faux, $level + 1);

        $node->cache = $condition.' ? '.$vraie.' : '.$faux;
        return $this->savenode($node);
    }

    function affiche_codephp($node, $level) {
        $this->affiche($node->getphp_code(), $level + 1);
        $node->cache = "<?php ?>";
    }

    function affiche__class($node, $level) {
        $name = $node->getName();
        $this->affiche($name, $level + 1);            
        $class = ' class '.$name->cache;

        $abstract = $node->getAbstract();
        if(!is_null($abstract)) {
            $this->affiche($abstract, $level + 1);
            $class = 'abstract '.$class;
        }

        $extends = $node->getExtends();
        if (!is_null($extends)) {
            $this->affiche($extends, $level + 1);     
            $class .= " extends ".$extends->cache;
        }

        $implements = $node->getImplements();
        if (count($implements) > 0) {
            $implemented = array();
            foreach($implements as $implement) {
                $this->affiche($implement, $level + 1);
                $implemented[] =  $implement->cache;
            }
            $class .= " implements ".join(', ', $implemented);
        }

        $this->affiche($node->getBlock(), $level + 1);

        $node->cache = $class;
        return $this->savenode($node);
    }

    function affiche__clone($node, $level) {
        $expression = $node->getExpression();
        $this->affiche($expression, $level + 1);

        $node->cache = 'clone '.$expression->cache;
    }

    function affiche_keyvalue($node, $level) {
        $key = $node->getKey();
        $this->affiche($key, $level + 1);
        $value = $node->getValue();
        $this->affiche($value, $level + 1);

        $node->cache = $key->cache.' => '.$value->cache;
    }

    function affiche_comparison($node, $level) {
        $droite = $node->getDroite();
        $this->affiche($droite, $level + 1);
        $operateur = $node->getOperateur();
        $this->affiche($operateur, $level + 1);
        $gauche = $node->getGauche();
        $this->affiche($gauche, $level + 1);
        
        $node->cache = $droite->cache.' '.$operateur->cache.' '.$gauche->cache;
        return $this->savenode($node);
    }

    function affiche_concatenation($node, $level) {
        $elements = $node->getList();
        $labels = array();
        foreach($elements as $id => &$e) {
            $this->affiche($e, $level + 1);            
            $labels[] = $e->cache;
        }
        
        $node->cache = join('.', $labels);
        return $this->savenode($node);
    }

    function affiche_constante($node, $level) {
        $node->cache = $node->getCode();
    }

    function affiche_constante_static($node, $level) {
        $classe = $node->getClass();
        $this->affiche($classe, $level + 1);
        $method = $node->getConstant();
        $this->affiche($method, $level + 1);

        $node->cache = $classe->cache.'::'.$method->cache;
        return $this->savenode($node);        
    }

    function affiche_constante_classe($node, $level) {
        $classe = $node->getName();
        $this->affiche($classe, $level + 1);
        $constante = $node->getConstante();
        $this->affiche($constante, $level + 1);

        $node->cache = $classe->cache.'::'.$constante->cache;
        return $this->savenode($node);        
    }

   function affiche_decalage($node, $level) {
        $droite = $node->getDroite();
        $this->affiche($droite, $level + 1);
        $operateur = $node->getOperateur();
        $this->affiche($operateur, $level + 1);
        $gauche = $node->getGauche();
        $this->affiche($gauche, $level + 1);
        
        $node->cache = $droite->cache.' '.$operateur->cache.' '.$gauche->cache;
        return $this->savenode($node);
    }

    function affiche__declare($node, $level) {
        $ticks = $node->getTicks();
        if (!is_null($ticks)) {
            $this->affiche($ticks, $level + 1);
        } else {
            $ticks->cache = '';
        }

        $encoding = $node->getEncoding();
        if (!is_null($encoding)) {
            $this->affiche($encoding, $level + 1);
        } else {
            $encoding->cache = '';
        }

        $block = $node->getBlock();
        if (!is_null($block)) {
            $this->affiche($block, $level + 1);
        }

        $node->cache = 'declare( ticks='.$ticks->cache.' encoding='.$encoding->cache.') ';
        return $this->savenode($node);
    }
    
    function affiche__default($node, $level) {
        $this->affiche($node->getBlock(), $level + 1);
        $node->cache = 'default'; 
    }

    function affiche__for($node, $level) {
        $node->cache = 'foreach(';
        if (!is_null($f = $node->getInit())) {
            $this->affiche($f, $level + 1);
            $node->cache .= $f->cache.'; ';
        }
        if (!is_null($f = $node->getFin())) {
            $this->affiche($f, $level + 1);
            $node->cache .= $f->cache.'; ';
        }
        if (!is_null($f = $node->getIncrement())) {
            $this->affiche($f, $level + 1);
            $node->cache .= $f->cache.')';
        }
        $this->affiche($node->getBlock(), $level + 1);
        // on ignore le block
    }

    function affiche__foreach($node, $level) {
        $node->cache = 'foreach(';

        $tableau = $node->getTableau();
        $this->affiche($tableau, $level + 1);
        $node->cache .= $tableau->cache.' as ';
        
        $key = $node->getKey();
        if (!is_null($key)) {
            $this->affiche($key, $level + 1);
            $node->cache .= $key.' => ';
        }

        $value = $node->getValue();
        $this->affiche($value, $level + 1);
        $node->cache .= $value.')';

        $this->affiche($node->getBlock(), $level + 1);
        // on ignore
    }

    function affiche__function($node, $level) {
        $name = $node->getName();
        $this->affiche($name, $level + 1);
        $args = $node->getArgs();
        $tags['args'][] = $this->affiche($args, $level + 1);
        $block = $node->getBlock();
        $tags['block'][] = $this->affiche($block, $level + 1);
        
        $function = 'function '.$name->cache.$args->cache; 
        // on ignore le block

        if (!is_null($m = $node->getVisibility())) {
            $tags['visibility'][] = $this->affiche($m, $level + 1);
            $function = $m->cache." $function";
        }
        if (!is_null($m = $node->getAbstract())) {
            $tags['abstract'][] = $this->affiche($m, $level + 1);
            $function = $m->cache." $function";
        }
        if (!is_null($m = $node->getStatic())) {
            $tags['static'][] = $this->affiche($m, $level + 1);
            $function = $m->cache." $function";
        }

        $node->cache = $function;
        return $this->savenode($node);
    }

    function affiche_functioncall($node, $level) {
        $function = $node->getFunction();
        $this->affiche($function, $level + 1);

        $args = $node->getArgs();
        $this->affiche($args, $level + 1);
        
        $node->cache = $function->cache.''.$args->cache;
        $this->savenode($node);
    }

    function affiche__global($node, $level) {
        $elements = $node->getVariables();
        $labels = array();
        foreach($elements as $id => $e) {
            $this->affiche($e, $level + 1);
            $labels[] = $e->cache;
        }

        $node->cache = 'global '.join(', ', $labels);
        return $this->savenode($node);    
    }

    function affiche_ifthen($node, $level) {
        $conditions = $node->getCondition();
        $thens = $node->getThen();

        foreach($conditions as $id => &$condition) {
            $this->affiche($condition, $level + 1);
            $this->affiche($thens[$id], $level + 1);
        }
        
        $else = $node->getElse();
        if (!is_null($else)){
            $this->affiche($else, $level + 1);
        }
        
        $node->cache = "<if then>";
    }

    function affiche_inclusion($node, $level) {
        $inclusion = $node->getInclusion();
        $this->affiche($inclusion, $level + 1);
        
        $node->cache = 'include '.$inclusion->cache;
        return $this->savenode($node);        
    }

    function affiche__interface($node, $level) {
        $cache = array();
        $e = $node->getExtends();
        if (count($e) > 0) {
            foreach($e as $ex) {
                $this->affiche($ex, $level + 1);
                $cache[] = $ex->cache;
            }
        }
        $this->affiche($node->getBlock(), $level + 1);

        $node->cache = 'interface '.$node->getName();
        if (count($e) > 0) {
            $node->cache .= 'implements '.join(', ', $cache);
        }
        return $this->savenode($node);        
    }

    function affiche_invert($node, $level) {
        $expression = $node->getExpression();
        $this->affiche($expression, $level + 1);

        $node->cache = ' '.$expression->cache;
        return $this->savenode($node);        
    }

    function affiche_literals($node, $level) {
        $node->cache = $node->getCode();
    }

    function affiche_logique($node, $level) {
        $droite = $node->getDroite();
        $this->affiche($droite, $level + 1);
        $operateur = $node->getOperateur();
        $this->affiche($operateur, $level + 1);
        $gauche = $node->getGauche();
        $this->affiche($gauche, $level + 1);
        
        $node->cache = $droite->cache.' '.$operateur->cache.' '.$gauche->cache;
    }

    function affiche_method($node, $level) {
        $object = $node->getObject();
        $this->affiche($object, $level + 1);
        $method = $node->getMethod();
        $this->affiche($method, $level + 1);        
        
        $node->cache = $object->cache."->".$method->cache;
        $this->savenode($node);
    }

    function affiche_method_static($node, $level) {
        $classe = $node->getClass();
        $this->affiche($classe, $level + 1);
        $method = $node->getMethod();
        $this->affiche($method, $level + 1);

        $node->cache = $classe->cache.'::'.$method->cache;
        return $this->savenode($node);        
    }

    function affiche__new($node, $level) {
        $name = $node->getClasse();
        $tags['name'][] = $this->affiche($name, $level + 1);
        $args = $node->getArgs();
        $tags['args'][] = $this->affiche($args, $level + 1);
        
        $node->cache = 'new '.$name->cache.''.$args->cache;
    }
    
    function affiche_noscream($node, $level) {
        $expression = $node->getExpression();
        $this->affiche($expression, $level + 1);

        $node->cache = '@'.$expression->cache;
        return $this->savenode($node);
    }

    function affiche_not($node, $level) {
        $expression = $node->getExpression();
        $this->affiche($expression, $level + 1);

        $node->cache = '!'.$expression->cache;
        return $this->savenode($node);
    }

    function affiche_opappend($node, $level) {
        $variable = $node->getVariable();
        $this->affiche($variable, $level + 1);

        $node->cache = $variable->cache.'[]';
        return $this->savenode($node);
    }

    function affiche_operation($node, $level) {
        $droite = $node->getDroite();
        $this->affiche($droite, $level + 1);
        $operation = $node->getOperation();
        $this->affiche($operation, $level + 1);
        $gauche = $node->getGauche();
        $this->affiche($gauche, $level + 1);
        
        $node->cache = $droite->cache.' '.$operation->cache.' '.$gauche->cache;
    }

    function affiche_parentheses($node, $level) {
        $contenu = $node->getContenu();
        $this->affiche($contenu, $level + 1);
        
        $node->cache = '('.$contenu->cache.')';
        return $this->savenode($node);        
    }

    function affiche_preplusplus($node, $level) {
        $var = $node->getVariable();
        $this->affiche($var, $level + 1);
        $operateur = $node->getOperateur();
        $this->affiche($operateur, $level + 1);
        
        $node->cache = $operateur->cache.$var->cache;
        return $this->savenode($node);    
    }

    function affiche_postplusplus($node, $level) {
        $var = $node->getVariable();
        $this->affiche($var, $level + 1);
        $operateur = $node->getOperateur();
        $this->affiche($operateur, $level + 1);
        
        $node->cache = $var->cache.$operateur->cache;
        return $this->savenode($node);    
    }

    function affiche_property($node, $level) {
        $object = $node->getObject();
        $this->affiche($object, $level + 1);
        $property = $node->getProperty();
        $this->affiche($property, $level + 1);
        
        $node->cache = $object->cache."->".$property->cache;
        $this->savenode($node);
    }

    function affiche_property_static($node, $level) {
        $classe = $node->getClass();
        $this->affiche($classe, $level + 1);
        $property = $node->getProperty();
        $this->affiche($property, $level + 1);
        
        $node->cache = $classe->cache."->".$property->cache;
        $this->savenode($node);
    }

    function affiche_rawtext($node, $level) {
        $node->cache = '';
    }

    function affiche_reference($node, $level) {
        $expression = $node->getExpression();
        $this->affiche($expression, $level + 1);

        $node->cache = '&'.$expression->cache;
        return $this->savenode($node);
    }

    function affiche__return($node, $level) {
        if (!is_null($return = $node->getReturn())) {
            $this->affiche($return, $level + 1);
            $node->cache = 'return '.$return->cache;
        } else {
            $node->cache = 'return NULL';
        }

        return $this->savenode($node);
    }

    function affiche_sequence($node, $level) {
        $elements = $node->getElements();
        if (count($elements) == 0) {
            // rien
            $node->cache = '';
        } else {
            $labels = array();
            $id = 0;
            foreach($elements as $id => &$e) {
                if (is_null($e)) {
                    die("cas de l'argument null ou inexistant dans une sequence");
                } else {
                    $labels[] = $this->affiche($e, $level + 1);
                }
            }
            $node->cache = join('', $labels);
        }

    }

    function affiche_sign($node, $level) {
        $expression = $node->getExpression();
        $this->affiche($expression, $level + 1);
        $sign = $node->getsign();
        $this->affiche($sign, $level + 1);
        
        $node->cache = $sign->cache.$expression->cache;
    }

    function affiche_shell($node, $level) {
        $cache = '';
        $elements = $node->getExpression();
        foreach($elements as $id => $e) {
            $this->affiche($e, $level + 1);
            $cache .= $e->cache;
        }

        $node->cache = $cache;
    }

    function affiche__static($node, $level) {
        $expression = $node->getExpression();
        $this->affiche($expression, $level + 1);
        
        $node->cache = 'static '.$expression->cache;
    }

    function affiche__switch($node, $level) {
        $this->affiche($node->getOperande(), $level + 1);
        $this->affiche($node->getBlock(), $level + 1);
        $node->cache = '<switch>';
    }

    function affiche_tableau($node, $level) {
        $variable = $node->getVariable();
        $this->affiche($variable, $level + 1);
        $index = $node->getIndex();
        $this->affiche($index, $level + 1);
        
        $node->cache = $variable->cache.'['.$index->cache.']';
        return $this->savenode($node);
    }

    function affiche__throw($node, $level) {
        $exception = $node->getException();
        $this->affiche($exception, $level + 1);

        $node->cache = 'throw '.$exception->cache;
    }

    function affiche__try($node, $level) {
        $block = $node->getBlock();
        $this->affiche($block, $level + 1);

        $elements = $node->getCatch();
        $labels = array();
        foreach($elements as $id => &$e) {
            $this->affiche($e, $level + 1);
            $labels[]=  $e->cache;
        }
        $node->cache = '<try>';        
    }

    function affiche_typehint($node, $level) {
        $type = $node->getType();
        $this->affiche($type, $level + 1);
        $name = $node->getName();
        $this->affiche($name, $level + 1);
        
        $node->cache = $type->cache." ".$name->cache;
    }

    function affiche__var($node, $level) {
        $var = array();
        
        $variables = $node->getVariable();
        if (count($variables) > 0) {
            $inits = $node->getInit();
            foreach($variables as $id => $variable) {
                $this->affiche($variable, $level + 1);
                $var[] = $variable->cache;
                if (!is_null($inits[$id])) {
                    $this->affiche($inits[$id], $level + 1);
                    $var[] = $inits[$id]->cache;
                }
            }
        }
        
        $var = join(', ', $var);

        $visibility = $node->getVisibility();
        if (!is_null($visibility)) {
            $visibility = $node->getVisibility();
            $this->affiche($visibility, $level + 1);
            $var = $visibility->cache." ".$var;
        } else {
            $var = "var $var";
        }

        if (!is_null($node->getStatic())) {
            $this->affiche($node->getStatic(), $level + 1);
            $var = "static $var";
        }
        
        $node->cache = $var;
        return $this->savenode($node);
    }

    function affiche_variable($node, $level) {
        $name = $node->getName();
        if (is_object($name)) {
            $this->affiche($name, $level + 1);
            $node->cache = '$'.$name->cache;
        } else {
            $node->cache = $name;
        }
        return $this->savenode($node);
    }

    function affiche__while($node, $level) {
        $this->affiche($node->getCondition(), $level + 1);
        $this->affiche($node->getBlock(), $level + 1);
        $node->cache = '<while>';        
    }

    function affiche__dowhile($node, $level) {
        $this->affiche($node->getCondition(), $level + 1);
        $this->affiche($node->getBlock(), $level + 1);
        $node->cache = '<do_while>';        
    }
    
    function affiche_Token($node, $level) {
        print_r(xdebug_get_function_stack());        
        print "Attention, Token affiché : '$node'\n";
        die();
    }
}

?>
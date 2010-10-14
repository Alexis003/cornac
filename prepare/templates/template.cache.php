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
    
    function affiche($node = null, $niveau = 0) {
        if ($niveau > 200) {
            print_r(xdebug_get_function_stack());        
            print "Attention : plus de 100 niveaux de récursion (annulation) ".__METHOD__."\n"; 
            die(__METHOD__."\n");
        }
        if (is_null($node)) {
            if ($niveau == 0) {
                $node = $this->root;
            } else {
                print_r(xdebug_get_function_stack());        
                print "On a tenté de refiler un null à affiche.";
                die();
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
            $return = $this->$method($node, $niveau + 1);
        } else {
            print "Affichage ".__CLASS__." de '".$method."'\n";die;
        }
        if (!is_null($node->getNext())){
            $this->affiche($node->getNext(), $niveau);
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
    function affiche_token_traite($node, $niveau) {
        $node->cache = $node->getCode();
    }

    function affiche_affectation($node, $niveau) {
        $droite = $node->getDroite();
        $this->affiche($droite, $niveau + 1);
        $operateur = $node->getOperateur();
        $this->affiche($operateur, $niveau + 1);
        $gauche = $node->getGauche();
        $this->affiche($gauche, $niveau + 1);
        
        $node->cache = $droite->cache.' '.$operateur->cache.' '.$gauche->cache;
        return $this->savenode($node);
    }

    function affiche_arginit($node, $niveau) {
        $var = $node->getVariable();
        $this->affiche($var, $niveau + 1);
        $valeur = $node->getValeur();
        $this->affiche($valeur, $niveau + 1);
        
        $node->cache = $var->cache." = ".$valeur->cache;
        return $this->savenode($node);
    }

    function affiche_arglist($node, $niveau) {
        $elements = $node->getList();
        if (count($elements) == 0) {
            $token_traite = new token_traite(new Token());
            $this->affiche($token_traite, $niveau + 1);
            $node->cache = '()';
            return;
        } else {
            $labels = array();
            foreach($elements as $id => &$e) {
                if (is_null($e)) {
                    $labels[] = '';
                    //rien
                } else {
                    $this->affiche($e, $niveau + 1);
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

    function affiche_block($node, $niveau) {
        $elements = $node->getList();
        foreach($elements as $id => &$e) {
            $this->affiche($e, $niveau + 1);
        }
        
        $node->cache = ' { block }';
    }

    function affiche__break($node, $niveau) {
        $niveaux = $node->getNiveaux();
        $this->affiche($niveaux, $niveau + 1);

        $node->cache = 'break '.$niveaux->cache;
    }

    function affiche__case($node, $niveau) {
        $case = 'case';
        if (!is_null($m = $node->getComparant())) {
            $this->affiche($m, $niveau + 1);
            $case .= " ".$m->cache;
        }
        $this->affiche($node->getBlock(), $niveau + 1);
        // on ignore

        $node->cache = $case;
        return $this->savenode($node);
    }

    function affiche_cast($node, $niveau) {
        $expression = $node->getExpression();
        $this->affiche($expression, $niveau + 1);

        $node->cache = '('.$expression->cache.')';
    }

    function affiche__catch($node, $niveau) {
        $this->affiche($node->getException(), $niveau + 1);
        $this->affiche($node->getVariable(), $niveau + 1);
        $this->affiche($node->getBlock(), $niveau + 1);
        
        $node->cache = 'catch';
    }

    function affiche__continue($node, $niveau) {
        $niveaux = $node->getNiveaux();
        $this->affiche($niveaux, $niveau + 1);

        $node->cache = 'continue '.$niveaux->cache;
    }
    
    function affiche_cdtternaire($node, $niveau) {
        $condition = $node->getCondition();
        $this->affiche($condition, $niveau + 1);

        $vraie = $node->getVraie();
        $this->affiche($vraie, $niveau + 1);
        
        $faux = $node->getFaux();
        $this->affiche($faux, $niveau + 1);

        $node->cache = $condition.' ? '.$vraie.' : '.$faux;
        return $this->savenode($node);
    }

    function affiche_codephp($node, $niveau) {
        $this->affiche($node->getphp_code(), $niveau + 1);
        $node->cache = "<?php ?>";
    }

    function affiche__class($node, $niveau) {
        $name = $node->getName();
        $this->affiche($name, $niveau + 1);            
        $class = ' class '.$name->cache;

        $abstract = $node->getAbstract();
        if(!is_null($abstract)) {
            $this->affiche($abstract, $niveau + 1);
            $class = 'abstract '.$class;
        }

        $extends = $node->getExtends();
        if (!is_null($extends)) {
            $this->affiche($extends, $niveau + 1);     
            $class .= " extends ".$extends->cache;
        }

        $implements = $node->getImplements();
        if (count($implements) > 0) {
            $implemented = array();
            foreach($implements as $implement) {
                $this->affiche($implement, $niveau + 1);
                $implemented[] =  $implement->cache;
            }
            $class .= " implements ".join(', ', $implemented);
        }

        $this->affiche($node->getBlock(), $niveau + 1);

        $node->cache = $class;
        return $this->savenode($node);
    }

    function affiche__clone($node, $niveau) {
        $expression = $node->getExpression();
        $this->affiche($expression, $niveau + 1);

        $node->cache = 'clone '.$expression->cache;
    }

    function affiche_clevaleur($node, $niveau) {
        $cle = $node->getCle();
        $this->affiche($cle, $niveau + 1);
        $valeur = $node->getValeur();
        $this->affiche($valeur, $niveau + 1);

        $node->cache = $cle->cache.' => '.$valeur->cache;
    }

    function affiche_comparison($node, $niveau) {
        $droite = $node->getDroite();
        $this->affiche($droite, $niveau + 1);
        $operateur = $node->getOperateur();
        $this->affiche($operateur, $niveau + 1);
        $gauche = $node->getGauche();
        $this->affiche($gauche, $niveau + 1);
        
        $node->cache = $droite->cache.' '.$operateur->cache.' '.$gauche->cache;
        return $this->savenode($node);
    }

    function affiche_concatenation($node, $niveau) {
        $elements = $node->getList();
        $labels = array();
        foreach($elements as $id => &$e) {
            $this->affiche($e, $niveau + 1);            
            $labels[] = $e->cache;
        }
        
        $node->cache = join('.', $labels);
        return $this->savenode($node);
    }

    function affiche_constante($node, $niveau) {
        $node->cache = $node->getCode();
    }

    function affiche_constante_static($node, $niveau) {
        $classe = $node->getClass();
        $this->affiche($classe, $niveau + 1);
        $method = $node->getConstant();
        $this->affiche($method, $niveau + 1);

        $node->cache = $classe->cache.'::'.$method->cache;
        return $this->savenode($node);        
    }

    function affiche_constante_classe($node, $niveau) {
        $classe = $node->getName();
        $this->affiche($classe, $niveau + 1);
        $constante = $node->getConstante();
        $this->affiche($constante, $niveau + 1);

        $node->cache = $classe->cache.'::'.$constante->cache;
        return $this->savenode($node);        
    }

   function affiche_decalage($node, $niveau) {
        $droite = $node->getDroite();
        $this->affiche($droite, $niveau + 1);
        $operateur = $node->getOperateur();
        $this->affiche($operateur, $niveau + 1);
        $gauche = $node->getGauche();
        $this->affiche($gauche, $niveau + 1);
        
        $node->cache = $droite->cache.' '.$operateur->cache.' '.$gauche->cache;
        return $this->savenode($node);
    }

    function affiche__default($node, $niveau) {
        $this->affiche($node->getBlock(), $niveau + 1);
        $node->cache = 'default'; 
    }

    function affiche__for($node, $niveau) {
        $node->cache = 'foreach(';
        if (!is_null($f = $node->getInit())) {
            $this->affiche($f, $niveau + 1);
            $node->cache .= $f->cache.'; ';
        }
        if (!is_null($f = $node->getFin())) {
            $this->affiche($f, $niveau + 1);
            $node->cache .= $f->cache.'; ';
        }
        if (!is_null($f = $node->getIncrement())) {
            $this->affiche($f, $niveau + 1);
            $node->cache .= $f->cache.')';
        }
        $this->affiche($node->getBlock(), $niveau + 1);
        // on ignore le block
    }

    function affiche__foreach($node, $niveau) {
        $node->cache = 'foreach(';

        $tableau = $node->getTableau();
        $this->affiche($tableau, $niveau + 1);
        $node->cache .= $tableau->cache.' as ';
        
        $key = $node->getKey();
        if (!is_null($key)) {
            $this->affiche($key, $niveau + 1);
            $node->cache .= $key.' => ';
        }

        $valeur = $node->getValue();
        $this->affiche($valeur, $niveau + 1);
        $node->cache .= $valeur.')';

        $this->affiche($node->getBlock(), $niveau + 1);
        // on ignore
    }

    function affiche__function($node, $niveau) {
        $name = $node->getName();
        $this->affiche($name, $niveau + 1);
        $args = $node->getArgs();
        $tags['args'][] = $this->affiche($args, $niveau + 1);
        $block = $node->getBlock();
        $tags['block'][] = $this->affiche($block, $niveau + 1);
        
        $function = 'function '.$name->cache.$args->cache; 
        // on ignore le block

        if (!is_null($m = $node->getVisibility())) {
            $tags['visibility'][] = $this->affiche($m, $niveau + 1);
            $function = $m->cache." $function";
        }
        if (!is_null($m = $node->getAbstract())) {
            $tags['abstract'][] = $this->affiche($m, $niveau + 1);
            $function = $m->cache." $function";
        }
        if (!is_null($m = $node->getStatic())) {
            $tags['static'][] = $this->affiche($m, $niveau + 1);
            $function = $m->cache." $function";
        }

        $node->cache = $function;
        return $this->savenode($node);
    }

    function affiche_functioncall($node, $niveau) {
        $function = $node->getFunction();
        $this->affiche($function, $niveau + 1);

        $args = $node->getArgs();
        $this->affiche($args, $niveau + 1);
        
        $node->cache = $function->cache.''.$args->cache;
        $this->savenode($node);
    }

    function affiche__global($node, $niveau) {
        $elements = $node->getVariables();
        $labels = array();
        foreach($elements as $id => $e) {
            $this->affiche($e, $niveau + 1);
            $labels[] = $e->cache;
        }

        $node->cache = 'global '.join(', ', $labels);
        return $this->savenode($node);    
    }

    function affiche_ifthen($node, $niveau) {
        $conditions = $node->getCondition();
        $thens = $node->getThen();

        foreach($conditions as $id => &$condition) {
            $this->affiche($condition, $niveau + 1);
            $this->affiche($thens[$id], $niveau + 1);
        }
        
        $else = $node->getElse();
        if (!is_null($else)){
            $this->affiche($else, $niveau + 1);
        }
        
        $node->cache = "<if then>";
    }

    function affiche_inclusion($node, $niveau) {
        $inclusion = $node->getInclusion();
        $this->affiche($inclusion, $niveau + 1);
        
        $node->cache = 'include '.$inclusion->cache;
        return $this->savenode($node);        
    }

    function affiche__interface($node, $niveau) {
        $cache = array();
        $e = $node->getExtends();
        if (count($e) > 0) {
            foreach($e as $ex) {
                $this->affiche($ex, $niveau + 1);
                $cache[] = $ex->cache;
            }
        }
        $this->affiche($node->getBlock(), $niveau + 1);

        $node->cache = 'interface '.$node->getName();
        if (count($e) > 0) {
            $node->cache .= 'implements '.join(', ', $cache);
        }
        return $this->savenode($node);        
    }

    function affiche_invert($node, $niveau) {
        $expression = $node->getExpression();
        $this->affiche($expression, $niveau + 1);

        $node->cache = ' '.$expression->cache;
        return $this->savenode($node);        
    }

    function affiche_literals($node, $niveau) {
        $node->cache = $node->getCode();
    }

    function affiche_logique($node, $niveau) {
        $droite = $node->getDroite();
        $this->affiche($droite, $niveau + 1);
        $operateur = $node->getOperateur();
        $this->affiche($operateur, $niveau + 1);
        $gauche = $node->getGauche();
        $this->affiche($gauche, $niveau + 1);
        
        $node->cache = $droite->cache.' '.$operateur->cache.' '.$gauche->cache;
    }

    function affiche_method($node, $niveau) {
        $object = $node->getObject();
        $this->affiche($object, $niveau + 1);
        $method = $node->getMethod();
        $this->affiche($method, $niveau + 1);        
        
        $node->cache = $object->cache."->".$method->cache;
        $this->savenode($node);
    }

    function affiche_method_static($node, $niveau) {
        $classe = $node->getClass();
        $this->affiche($classe, $niveau + 1);
        $method = $node->getMethod();
        $this->affiche($method, $niveau + 1);

        $node->cache = $classe->cache.'::'.$method->cache;
        return $this->savenode($node);        
    }

    function affiche__new($node, $niveau) {
        $name = $node->getClasse();
        $tags['name'][] = $this->affiche($name, $niveau + 1);
        $args = $node->getArgs();
        $tags['args'][] = $this->affiche($args, $niveau + 1);
        
        $node->cache = 'new '.$name->cache.''.$args->cache;
    }
    
    function affiche_noscream($node, $niveau) {
        $expression = $node->getExpression();
        $this->affiche($expression, $niveau + 1);

        $node->cache = '@'.$expression->cache;
        return $this->savenode($node);
    }

    function affiche_not($node, $niveau) {
        $expression = $node->getExpression();
        $this->affiche($expression, $niveau + 1);

        $node->cache = '!'.$expression->cache;
        return $this->savenode($node);
    }

    function affiche_opappend($node, $niveau) {
        $variable = $node->getVariable();
        $this->affiche($variable, $niveau + 1);

        $node->cache = $variable->cache.'[]';
        return $this->savenode($node);
    }

    function affiche_operation($node, $niveau) {
        $droite = $node->getDroite();
        $this->affiche($droite, $niveau + 1);
        $operation = $node->getOperation();
        $this->affiche($operation, $niveau + 1);
        $gauche = $node->getGauche();
        $this->affiche($gauche, $niveau + 1);
        
        $node->cache = $droite->cache.' '.$operation->cache.' '.$gauche->cache;
    }

    function affiche_parentheses($node, $niveau) {
        $contenu = $node->getContenu();
        $this->affiche($contenu, $niveau + 1);
        
        $node->cache = '('.$contenu->cache.')';
        return $this->savenode($node);        
    }

    function affiche_preplusplus($node, $niveau) {
        $var = $node->getVariable();
        $this->affiche($var, $niveau + 1);
        $operateur = $node->getOperateur();
        $this->affiche($operateur, $niveau + 1);
        
        $node->cache = $operateur->cache.$var->cache;
        return $this->savenode($node);    
    }

    function affiche_postplusplus($node, $niveau) {
        $var = $node->getVariable();
        $this->affiche($var, $niveau + 1);
        $operateur = $node->getOperateur();
        $this->affiche($operateur, $niveau + 1);
        
        $node->cache = $var->cache.$operateur->cache;
        return $this->savenode($node);    
    }

    function affiche_property($node, $niveau) {
        $object = $node->getObject();
        $this->affiche($object, $niveau + 1);
        $property = $node->getProperty();
        $this->affiche($property, $niveau + 1);
        
        $node->cache = $object->cache."->".$property->cache;
        $this->savenode($node);
    }

    function affiche_property_static($node, $niveau) {
        $classe = $node->getClass();
        $this->affiche($classe, $niveau + 1);
        $property = $node->getProperty();
        $this->affiche($property, $niveau + 1);
        
        $node->cache = $classe->cache."->".$property->cache;
        $this->savenode($node);
    }

    function affiche_rawtext($node, $niveau) {
        $node->cache = '';
    }

    function affiche_reference($node, $niveau) {
        $expression = $node->getExpression();
        $this->affiche($expression, $niveau + 1);

        $node->cache = '&'.$expression->cache;
        return $this->savenode($node);
    }

    function affiche__return($node, $niveau) {
        if (!is_null($return = $node->getReturn())) {
            $this->affiche($return, $niveau + 1);
            $node->cache = 'return '.$return->cache;
        } else {
            $node->cache = 'return NULL';
        }

        return $this->savenode($node);
    }

    function affiche_sequence($node, $niveau) {
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
                    $labels[] = $this->affiche($e, $niveau + 1);
                }
            }
            $node->cache = join('', $labels);
        }

    }

    function affiche_sign($node, $niveau) {
        $expression = $node->getExpression();
        $this->affiche($expression, $niveau + 1);
        $sign = $node->getsign();
        $this->affiche($sign, $niveau + 1);
        
        $node->cache = $sign->cache.$expression->cache;
    }

    function affiche_shell($node, $niveau) {
        $cache = '';
        $elements = $node->getExpression();
        foreach($elements as $id => $e) {
            $this->affiche($e, $niveau + 1);
            $cache .= $e->cache;
        }

        $node->cache = $cache;
    }

    function affiche__static($node, $niveau) {
        $expression = $node->getExpression();
        $this->affiche($expression, $niveau + 1);
        
        $node->cache = 'static '.$expression->cache;
    }

    function affiche__switch($node, $niveau) {
        $this->affiche($node->getOperande(), $niveau + 1);
        $this->affiche($node->getBlock(), $niveau + 1);
        $node->cache = '<switch>';
    }

    function affiche_tableau($node, $niveau) {
        $variable = $node->getVariable();
        $this->affiche($variable, $niveau + 1);
        $index = $node->getIndex();
        $this->affiche($index, $niveau + 1);
        
        $node->cache = $variable->cache.'['.$index->cache.']';
        return $this->savenode($node);
    }

    function affiche__throw($node, $niveau) {
        $exception = $node->getException();
        $this->affiche($exception, $niveau + 1);

        $node->cache = 'throw '.$exception->cache;
    }

    function affiche__try($node, $niveau) {
        $block = $node->getBlock();
        $this->affiche($block, $niveau + 1);

        $elements = $node->getCatch();
        $labels = array();
        foreach($elements as $id => &$e) {
            $this->affiche($e, $niveau + 1);
            $labels[]=  $e->cache;
        }
        $node->cache = '<try>';        
    }

    function affiche_typehint($node, $niveau) {
        $type = $node->getType();
        $this->affiche($type, $niveau + 1);
        $name = $node->getName();
        $this->affiche($name, $niveau + 1);
        
        $node->cache = $type->cache." ".$name->cache;
    }

    function affiche__var($node, $niveau) {
        $var = array();
        
        $variables = $node->getVariable();
        if (count($variables) > 0) {
            $inits = $node->getInit();
            foreach($variables as $id => $variable) {
                $this->affiche($variable, $niveau + 1);
                $var[] = $variable->cache;
                if (!is_null($inits[$id])) {
                    $this->affiche($inits[$id], $niveau + 1);
                    $var[] = $inits[$id]->cache;
                }
            }
        }
        
        $var = join(', ', $var);

        $visibility = $node->getVisibility();
        if (!is_null($visibility)) {
            $visibility = $node->getVisibility();
            $this->affiche($visibility, $niveau + 1);
            $var = $visibility->cache." ".$var;
        } else {
            $var = "var $var";
        }

        if (!is_null($node->getStatic())) {
            $this->affiche($node->getStatic(), $niveau + 1);
            $var = "static $var";
        }
        
        $node->cache = $var;
        return $this->savenode($node);
    }

    function affiche_variable($node, $niveau) {
        $name = $node->getName();
        if (is_object($name)) {
            $this->affiche($name, $niveau + 1);
            $node->cache = '$'.$name->cache;
        } else {
            $node->cache = $name;
        }
        return $this->savenode($node);
    }

    function affiche__while($node, $niveau) {
        $this->affiche($node->getCondition(), $niveau + 1);
        $this->affiche($node->getBlock(), $niveau + 1);
        $node->cache = '<while>';        
    }

    function affiche__dowhile($node, $niveau) {
        $this->affiche($node->getCondition(), $niveau + 1);
        $this->affiche($node->getBlock(), $niveau + 1);
        $node->cache = '<do_while>';        
    }
    
    function affiche_Token($node, $niveau) {
        print_r(xdebug_get_function_stack());        
        print "Attention, Token affiché : '$node'\n";
        die();
    }
}

?>
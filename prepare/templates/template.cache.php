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
    
    function __construct($root, $file = null) {
        parent::__construct();
        
        global $INI;
        $this->table = $INI['cornac']['prefix'] ?: 'tokens';

        
        if (isset($INI['mysql']) && $INI['mysql']['active'] == true) {
            $this->database = new pdo($INI['mysql']['dsn'],$INI['mysql']['username'], $INI['mysql']['password']);

            $this->database->query('DELETE FROM '.$this->table.'_cache WHERE file = "'.$file.'"');
            $this->database->query('CREATE TABLE IF NOT EXISTS '.$this->table.'_cache (
                                                          id       INTEGER PRIMARY KEY AUTO_INCREMENT, 
                                                          code     VARCHAR(255),
                                                          file  VARCHAR(255)
                                                          )');
        } elseif (isset($INI['sqlite']) && $INI['sqlite']['active'] == true) {
            $this->database = new pdo($INI['sqlite']['dsn']);

            $this->database->query('DELETE FROM '.$this->table.'_cache WHERE file = "'.$file.'"');
            $this->database->query('CREATE TABLE IF NOT EXISTS '.$this->table.'_cache (
                                                          id       INTEGER PRIMARY KEY AUTOINCREMENT, 
                                                          code     VARCHAR(255),
                                                          file  VARCHAR(255)
                                                          )');
        } else {
            print "No database configuration provided (no db)\n";
            die(__METHOD__."\n");
        }
        
        $this->table_tags = $this->table.'_tags';

        $this->root = $root;
    }
    
    function save($filename = null) {
        return true; 
    }
    
    function display($node = null, $level = 0) {
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
        $method = "display_$class";
        
        if (method_exists($this, $method)) {
            $return = $this->$method($node, $level + 1);
        } else {
            print "Affichage ".__CLASS__." de '".$method."'\n";die;
        }
        if (!is_null($node->getNext())){
            $this->display($node->getNext(), $level);
        }

        return $return;
    }
    
////////////////////////////////////////////////////////////////////////
// database functions
////////////////////////////////////////////////////////////////////////
    function savenode($node) {
        global $file;
        
        if (!isset($node->database_id)) {
            print get_class($node)."\n";
            print_r(xdebug_get_function_stack());
            print_r($node);
            die();
        }
        
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
    function display_token_traite($node, $level) {
        $node->cache = $node->getCode();
        return $this->savenode($node);
    }

    function display_affectation($node, $level) {
        $left = $node->getLeft();
        $this->display($left, $level + 1);
        $operator = $node->getOperator();
        $this->display($operator, $level + 1);
        $right = $node->getRight();
        $this->display($right, $level + 1);
        
        $node->cache = $left->cache.' '.$operator->cache.' '.$right->cache;
        return $this->savenode($node);
    }

    function display_arginit($node, $level) {
        $var = $node->getVariable();
        $this->display($var, $level + 1);
        $value = $node->getValue();
        $this->display($value, $level + 1);
        
        $node->cache = $var->cache." = ".$value->cache;
        return $this->savenode($node);
    }

    function display_arglist($node, $level) {
        $elements = $node->getList();
        if (count($elements) == 0) {
            $node->cache = '()';
            return $this->savenode($node);
        } else {
            $labels = array();
            foreach($elements as $id => &$e) {
                if (is_null($e)) {
                    $labels[] = '';
                    //rien
                } else {
                    $this->display($e, $level + 1);
                    if (!isset($e->cache)) {
                        print $e;
                        die();
                    }
                    $labels[] = $e->cache;
                }
            }
            $node->cache = '('.join(', ', $labels).')';
        }
        return $this->savenode($node);
    }

    function display__array($node, $level) {
        $variable = $node->getVariable();
        $this->display($variable, $level + 1);
        $index = $node->getIndex();
        $this->display($index, $level + 1);
        
        $node->cache = $variable->cache.'['.$index->cache.']';
        return $this->savenode($node);
    }

    function display_block($node, $level) {
        $elements = $node->getList();
        foreach($elements as $id => &$e) {
            $this->display($e, $level + 1);
        }
        
        $node->cache = ' { block }';
        return $this->savenode($node);
    }

    function display__break($node, $level) {
        $levels = $node->getLevels();
        $this->display($levels, $level + 1);

        $node->cache = 'break '.$levels->cache;
        return $this->savenode($node);
    }

    function display__case($node, $level) {
        $case = 'case';
        if (!is_null($m = $node->getCondition())) {
            $this->display($m, $level + 1);
            $case .= " ".$m->cache;
        }
        $this->display($node->getBlock(), $level + 1);
        // on ignore

        $node->cache = $case;
        return $this->savenode($node);
    }

    function display_cast($node, $level) {
        $expression = $node->getExpression();
        $this->display($expression, $level + 1);

        $node->cache = '('.$expression->cache.')';
        return $this->savenode($node);
    }

    function display__catch($node, $level) {
        $this->display($node->getException(), $level + 1);
        $this->display($node->getVariable(), $level + 1);
        $this->display($node->getBlock(), $level + 1);
        
        $node->cache = 'catch';
        return $this->savenode($node);
    }

    function display__continue($node, $level) {
        $levels = $node->getLevels();
        $this->display($levels, $level + 1);

        $node->cache = 'continue '.$levels->cache;
        return $this->savenode($node);
    }

    function display__switch($node, $level) {
        $condition = $node->getCondition();
        $this->display($condition, $level + 1);
        $this->display($node->getBlock(), $level + 1);

        $node->cache = 'switch '.$condition->cache.'';
        // @note $condition->cache already have the () 
        return $this->savenode($node);
    }
    
    function display_ternaryop($node, $level) {
        $condition = $node->getCondition();
        $this->display($condition, $level + 1);

        $then = $node->getThen();
        $this->display($then, $level + 1);
        
        $else = $node->getElse();
        $this->display($else, $level + 1);

        $node->cache = $condition.' ? '.$then.' : '.$else;
        return $this->savenode($node);
    }

    function display_codephp($node, $level) {
        $this->display($node->getphp_code(), $level + 1);
        $node->cache = "<?php ?>";
        return $this->savenode($node);
    }

    function display__class($node, $level) {
        $name = $node->getName();
        $this->display($name, $level + 1);            
        $class = ' class '.$name->cache;

        $abstract = $node->getAbstract();
        if(!is_null($abstract)) {
            $this->display($abstract, $level + 1);
            $class = 'abstract '.$class;
        }

        $extends = $node->getExtends();
        if (!is_null($extends)) {
            $this->display($extends, $level + 1);     
            $class .= " extends ".$extends->cache;
        }

        $implements = $node->getImplements();
        if (count($implements) > 0) {
            $implemented = array();
            foreach($implements as $implement) {
                $this->display($implement, $level + 1);
                $implemented[] =  $implement->cache;
            }
            $class .= " implements ".join(', ', $implemented);
        }

        $this->display($node->getBlock(), $level + 1);

        $node->cache = $class;
        return $this->savenode($node);
    }

    function display__clone($node, $level) {
        $expression = $node->getExpression();
        $this->display($expression, $level + 1);

        $node->cache = 'clone '.$expression->cache;
        return $this->savenode($node);
    }

    function display_keyvalue($node, $level) {
        $key = $node->getKey();
        $this->display($key, $level + 1);
        $value = $node->getValue();
        $this->display($value, $level + 1);

        $node->cache = $key->cache.' => '.$value->cache;
        return $this->savenode($node);
    }

    function display_comparison($node, $level) {
        $left = $node->getLeft();
        $this->display($left, $level + 1);
        $operator = $node->getOperator();
        $this->display($operator, $level + 1);
        $right = $node->getRight();
        $this->display($right, $level + 1);
        
        $node->cache = $left->cache.' '.$operator->cache.' '.$right->cache;
        return $this->savenode($node);
    }

    function display_concatenation($node, $level) {
        $elements = $node->getList();
        $labels = array();
        foreach($elements as $id => &$e) {
            $this->display($e, $level + 1);            
            $labels[] = $e->cache;
        }
        
        $node->cache = join('.', $labels);
        return $this->savenode($node);
    }

    function display_constante($node, $level) {
        $node->cache = $node->getCode();
        return $this->savenode($node);
    }

    function display_constante_static($node, $level) {
        $classe = $node->getClass();
        $this->display($classe, $level + 1);
        $method = $node->getConstant();
        $this->display($method, $level + 1);

        $node->cache = $classe->cache.'::'.$method->cache;
        return $this->savenode($node);        
    }

    function display_constante_classe($node, $level) {
        $classe = $node->getName();
        $this->display($classe, $level + 1);
        $constante = $node->getConstante();
        $this->display($constante, $level + 1);

        $node->cache = $classe->cache.'::'.$constante->cache;
        return $this->savenode($node);        
    }

    function display_bitshift($node, $level) {
        $left = $node->getLeft();
        $this->display($left, $level + 1);
        $operator = $node->getOperator();
        $this->display($operator, $level + 1);
        $right = $node->getRight();
        $this->display($right, $level + 1);
        
        $node->cache = $left->cache.' '.$operator->cache.' '.$right->cache;
        return $this->savenode($node);
    }

    function display__declare($node, $level) {
        $ticks = $node->getTicks();
        if (!is_null($ticks)) {
            $this->display($ticks, $level + 1);
        } else {
            $ticks->cache = '';
        }

        $encoding = $node->getEncoding();
        if (!is_null($encoding)) {
            $this->display($encoding, $level + 1);
        } else {
            $encoding->cache = '';
        }

        $block = $node->getBlock();
        if (!is_null($block)) {
            $this->display($block, $level + 1);
        }

        $node->cache = 'declare( ticks='.$ticks->cache.' encoding='.$encoding->cache.') ';
        return $this->savenode($node);
    }
    
    function display__default($node, $level) {
        $this->display($node->getBlock(), $level + 1);
        $node->cache = 'default'; 
        return $this->savenode($node);
    }

    function display__for($node, $level) {
        $node->cache = 'foreach(';
        if (!is_null($f = $node->getInit())) {
            $this->display($f, $level + 1);
            $node->cache .= $f->cache.'; ';
        }
        if (!is_null($f = $node->getFin())) {
            $this->display($f, $level + 1);
            $node->cache .= $f->cache.'; ';
        }
        if (!is_null($f = $node->getIncrement())) {
            $this->display($f, $level + 1);
            $node->cache .= $f->cache.')';
        }
        $this->display($node->getBlock(), $level + 1);
        // @note no need for display cache
        return $this->savenode($node);
    }

    function display__foreach($node, $level) {
        $node->cache = 'foreach(';

        $array = $node->getArray();
        $this->display($array, $level + 1);
        $node->cache .= $array->cache.' as ';
        
        $key = $node->getKey();
        if (!is_null($key)) {
            $this->display($key, $level + 1);
            $node->cache .= $key.' => ';
        }

        $value = $node->getValue();
        $this->display($value, $level + 1);
        $node->cache .= $value.')';

        $this->display($node->getBlock(), $level + 1);
        return $this->savenode($node);
        // @note no need for display cache 
    }

    function display__function($node, $level) {
        $name = $node->getName();
        $this->display($name, $level + 1);
        $args = $node->getArgs();
        $tags['args'][] = $this->display($args, $level + 1);
        $block = $node->getBlock();
        $tags['block'][] = $this->display($block, $level + 1);
        
        $function = 'function '.$name->cache.$args->cache; 
        // on ignore le block

        if (!is_null($m = $node->getVisibility())) {
            $tags['visibility'][] = $this->display($m, $level + 1);
            $function = $m->cache." $function";
        }
        if (!is_null($m = $node->getAbstract())) {
            $tags['abstract'][] = $this->display($m, $level + 1);
            $function = $m->cache." $function";
        }
        if (!is_null($m = $node->getStatic())) {
            $tags['static'][] = $this->display($m, $level + 1);
            $function = $m->cache." $function";
        }

        $node->cache = $function;
        return $this->savenode($node);
    }

    function display_functioncall($node, $level) {
        $function = $node->getFunction();
        $this->display($function, $level + 1);

        $args = $node->getArgs();
        $this->display($args, $level + 1);
        
        $node->cache = $function->cache.''.$args->cache;
        return $this->savenode($node);
    }

    function display__global($node, $level) {
        $elements = $node->getVariables();
        $labels = array();
        foreach($elements as $id => $e) {
            $this->display($e, $level + 1);
            $labels[] = $e->cache;
        }

        $node->cache = 'global '.join(', ', $labels);
        return $this->savenode($node);    
    }

    function display_ifthen($node, $level) {
        $conditions = $node->getCondition();
        $thens = $node->getThen();

        foreach($conditions as $id => &$condition) {
            $this->display($condition, $level + 1);
            $this->display($thens[$id], $level + 1);
        }
        
        $else = $node->getElse();
        if (!is_null($else)){
            $this->display($else, $level + 1);
        }
        
        $node->cache = "<if then>";
        return $this->savenode($node);
    }

    function display_inclusion($node, $level) {
        $inclusion = $node->getInclusion();
        $this->display($inclusion, $level + 1);
        
        $node->cache = 'include '.$inclusion->cache;
        return $this->savenode($node);        
    }

    function display__interface($node, $level) {
        $cache = array();
        $e = $node->getExtends();
        if (count($e) > 0) {
            foreach($e as $ex) {
                $this->display($ex, $level + 1);
                $cache[] = $ex->cache;
            }
        }
        $this->display($node->getBlock(), $level + 1);

        $node->cache = 'interface '.$node->getName();
        if (count($e) > 0) {
            $node->cache .= 'implements '.join(', ', $cache);
        }
        return $this->savenode($node);        
    }

    function display_invert($node, $level) {
        $expression = $node->getExpression();
        $this->display($expression, $level + 1);

        $node->cache = ' '.$expression->cache;
        return $this->savenode($node);        
    }

    function display_literals($node, $level) {
        $node->cache = $node->getCode();
        return $this->savenode($node);
    }

    function display_logique($node, $level) {
        $left = $node->getLeft();
        $this->display($left, $level + 1);
        $operator = $node->getOperator();
        $this->display($operator, $level + 1);
        $right = $node->getRight();
        $this->display($right, $level + 1);
        
        $node->cache = $left->cache.' '.$operator->cache.' '.$right->cache;
        return $this->savenode($node);
    }

    function display_method($node, $level) {
        $object = $node->getObject();
        $this->display($object, $level + 1);
        $method = $node->getMethod();
        $this->display($method, $level + 1);        
        
        $node->cache = $object->cache."->".$method->cache;
        return $this->savenode($node);
    }

    function display_method_static($node, $level) {
        $classe = $node->getClass();
        $this->display($classe, $level + 1);
        $method = $node->getMethod();
        $this->display($method, $level + 1);

        $node->cache = $classe->cache.'::'.$method->cache;
        return $this->savenode($node);        
    }

    function display__new($node, $level) {
        $name = $node->getClasse();
        $tags['name'][] = $this->display($name, $level + 1);
        $args = $node->getArgs();
        $tags['args'][] = $this->display($args, $level + 1);
        
        $node->cache = 'new '.$name->cache.''.$args->cache;
        return $this->savenode($node);
    }

    function display__namespace($node, $level) {
    // use code value instead
        return true; 
    }

    function display_noscream($node, $level) {
        $expression = $node->getExpression();
        $this->display($expression, $level + 1);

        $node->cache = '@'.$expression->cache;
        return $this->savenode($node);
    }

    function display_not($node, $level) {
        $expression = $node->getExpression();
        $this->display($expression, $level + 1);

        $node->cache = '!'.$expression->cache;
        return $this->savenode($node);
    }

    function display_opappend($node, $level) {
        $variable = $node->getVariable();
        $this->display($variable, $level + 1);

        $node->cache = $variable->cache.'[]';
        return $this->savenode($node);
    }

    function display_operation($node, $level) {
        $left = $node->getLeft();
        $this->display($left, $level + 1);
        $operation = $node->getOperation();
        $this->display($operation, $level + 1);
        $right = $node->getRight();
        $this->display($right, $level + 1);
        
        $node->cache = $left->cache.' '.$operation->cache.' '.$right->cache;
        return $this->savenode($node);
    }

    function display_parentheses($node, $level) {
        $contenu = $node->getContenu();
        $this->display($contenu, $level + 1);
        
        $node->cache = '('.$contenu->cache.')';
        return $this->savenode($node);        
    }

    function display_preplusplus($node, $level) {
        $var = $node->getVariable();
        $this->display($var, $level + 1);
        $operator = $node->getOperator();
        $this->display($operator, $level + 1);
        
        $node->cache = $operator->cache.$var->cache;
        return $this->savenode($node);    
    }

    function display_postplusplus($node, $level) {
        $var = $node->getVariable();
        $this->display($var, $level + 1);
        $operator = $node->getOperator();
        $this->display($operator, $level + 1);
        
        $node->cache = $var->cache.$operator->cache;
        return $this->savenode($node);    
    }

    function display_property($node, $level) {
        $object = $node->getObject();
        $this->display($object, $level + 1);
        $property = $node->getProperty();
        $this->display($property, $level + 1);
        
        $node->cache = $object->cache."->".$property->cache;
        return $this->savenode($node);
    }

    function display_property_static($node, $level) {
        $classe = $node->getClass();
        $this->display($classe, $level + 1);
        $property = $node->getProperty();
        $this->display($property, $level + 1);
        
        $node->cache = $classe->cache."->".$property->cache;
        return $this->savenode($node);
    }

    function display_rawtext($node, $level) {
        $node->cache = '';
        return $this->savenode($node);
    }

    function display_reference($node, $level) {
        $expression = $node->getExpression();
        $this->display($expression, $level + 1);

        $node->cache = '&'.$expression->cache;
        return $this->savenode($node);
    }

    function display__return($node, $level) {
        if (!is_null($return = $node->getReturn())) {
            $this->display($return, $level + 1);
            $node->cache = 'return '.$return->cache;
        } else {
            $node->cache = 'return NULL';
        }

        return $this->savenode($node);
    }

    function display_sequence($node, $level) {
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
                    $labels[] = $this->display($e, $level + 1);
                }
            }
            $node->cache = join('', $labels);
        }
        return $this->savenode($node);
    }

    function display_sign($node, $level) {
        $expression = $node->getExpression();
        $this->display($expression, $level + 1);
        $sign = $node->getsign();
        $this->display($sign, $level + 1);
        
        $node->cache = $sign->cache.$expression->cache;
        return $this->savenode($node);
    }

    function display_shell($node, $level) {
        $cache = '';
        $elements = $node->getExpression();
        foreach($elements as $id => $e) {
            $this->display($e, $level + 1);
            $cache .= $e->cache;
        }

        $node->cache = $cache;
        return $this->savenode($node);
    }

    function display__static($node, $level) {
        $expression = $node->getExpression();
        $this->display($expression, $level + 1);
        
        $node->cache = 'static '.$expression->cache;
        return $this->savenode($node);
    }

    function display__use($node, $level) {
    // use code value instead
        return true; 
    }

    function display__throw($node, $level) {
        $exception = $node->getException();
        $this->display($exception, $level + 1);

        $node->cache = 'throw '.$exception->cache;
        return $this->savenode($node);
    }

    function display__try($node, $level) {
        $block = $node->getBlock();
        $this->display($block, $level + 1);

        $elements = $node->getCatch();
        $labels = array();
        foreach($elements as $id => &$e) {
            $this->display($e, $level + 1);
            $labels[]=  $e->cache;
        }
        $node->cache = '<try>';
        return $this->savenode($node);
    }

    function display_typehint($node, $level) {
        $type = $node->getType();
        $this->display($type, $level + 1);
        $name = $node->getName();
        $this->display($name, $level + 1);
        
        $node->cache = $type->cache." ".$name->cache;
        return $this->savenode($node);
    }

    function display__var($node, $level) {
        $var = array();
        
        $variables = $node->getVariable();
        if (count($variables) > 0) {
            $inits = $node->getInit();
            foreach($variables as $id => $variable) {
                $this->display($variable, $level + 1);
                $var[] = $variable->cache;
                if (!is_null($inits[$id])) {
                    $this->display($inits[$id], $level + 1);
                    $var[] = $inits[$id]->cache;
                }
            }
        }
        
        $var = join(', ', $var);

        $visibility = $node->getVisibility();
        if (!is_null($visibility)) {
            $visibility = $node->getVisibility();
            $this->display($visibility, $level + 1);
            $var = $visibility->cache." ".$var;
        } else {
            $var = "var $var";
        }

        if (!is_null($node->getStatic())) {
            $this->display($node->getStatic(), $level + 1);
            $var = "static $var";
        }
        
        $node->cache = $var;
        return $this->savenode($node);
    }

    function display_variable($node, $level) {
        $name = $node->getName();
        if (is_object($name)) {
            $this->display($name, $level + 1);
            $node->cache = '$'.$name->cache;
        } else {
            $node->cache = $name;
        }
        return $this->savenode($node);
    }

    function display__while($node, $level) {
        $condition = $node->getCondition();
        $this->display($condition, $level + 1);
        
        $this->display($node->getBlock(), $level + 1);
        $node->cache = 'while '.$condition->cache.'';
        return $this->savenode($node);
    }

    function display__dowhile($node, $level) {
        $condition = $node->getCondition();
        $this->display($condition, $level + 1);
        $this->display($node->getBlock(), $level + 1);

        $node->cache = 'do {} while '.$condition->cache.'';
        return $this->savenode($node);
    }
    
    function display_Token($node, $level) {
        print_r(xdebug_get_function_stack());        
        print "Attention, Token affiché : '$node'\n";
        die();
    }
}

?>
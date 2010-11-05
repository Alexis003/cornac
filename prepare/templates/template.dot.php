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

class template_dot extends template {
    protected $root = null;
    protected $dot = '';
    
    function __construct($root) {
        parent::__construct();
        
        $this->root = $root;
    }
    
    private static $ids = 0;
    
    function getNextId() {
        $this->ids++;
        
        return "t".$this->ids;
    }

    function save($filename = null) {
        if (is_null($filename)) {
            file_put_contents("tokenizeur.dot", "digraph G {\n {$this->dot} \n}");
            print "Sauvé dans tokenizeur.dot\n";
        } else {
            file_put_contents($filename, "digraph G {\n {$this->dot} \n}");
        }
    }
    
    function display($node = null, $level = 0) {
        if ($level > 100) {
            print "Attention : plus de 100 level de récursion (annulation)\n"; die();
        }
        if (is_null($node)) {
            $node = $this->root;
        }
        
        if (!is_object($node)) {
            print "Found an null reference in ".__METHOD__."\n";
            die();
        }
        $class = get_class($node);
        $method = "display_$class";
        
        if (method_exists($this, $method)) {
            $this->$method($node, $level + 1);
        } else {
            print "Affichage ".__CLASS__." de '".$method."'\n";die;
        }
        if (!is_null($node->getNext())){
            $this->display($node->getNext(), $level);
        }
    }
////////////////////////////////////////////////////////////////////////
// @section dot function 
////////////////////////////////////////////////////////////////////////
    function format_dot_id($id) {
        if (strpos($id, ":") === false) { return $id;}
        list($id, $f)  = explode(':', $id);
        
        return "\"$id\":$f";
    }
    
    function dot_link($origine, $destination) {
        $origine = $this->format_dot_id($origine);
        $destination = $this->format_dot_id($destination);
        
        $this->dot .=  $origine." -> { ".$destination."}; \n";
    }

    function dot_label($node, $name) {
        $name = str_replace('"', '\\"', $name);
        $name = str_replace("\n", '\\n', $name);
        $this->dot .=  $node." [label=\"".$name."\"];\n";
    }

    function dot_struct($node, $labels, $title = '[Titre]') {
        foreach($labels as $id => &$l) {
            $l = "<f$id> $l";
        }
        $labels = join('|', $labels);
        $this->dot .=  $node." [shape=record,label=\"{".$title." | {".$labels."}} \"];\n";

    }
    
    function dot_standard($node, $level, $methods, $title) {
        foreach($methods as $id => $m) {
            if (is_null($node->$m())) {
                unset($methods[$id]);
                continue;
            }
            $node->$m()->dotId    = $this->getNextId();
        }

        $this->dot_label($node->dotId, $title);

        foreach($methods as $m) {
            $this->dot_link($node->dotId, $node->$m()->dotId);
            $this->display($node->$m(), $level + 1);
        }
    }
    
    function dot_standard_one($node, $level, $method) {
        $result = $node->$method();
        
        if(!is_null($result)) {
            if (!is_object($result)) { 
                var_dump($result);
                die("$method ".__FILE__." ".__LINE__."\n");
            }
            $result->dotId = $this->getNextId();
            $this->dot_link($node->dotId, $result->dotId);
            $this->display($result, $level + 1);
        }
    }

////////////////////////////////////////////////////////////////////////
// @section dot function 
////////////////////////////////////////////////////////////////////////
    function display_token_traite($node, $level) {
        $this->dot_label($node->dotId, $node->getCode() );
    }

    function display_affectation($node, $level) {
        $node->getLeft()->dotId    = $this->getNextId();
        $node->getOperator()->dotId = $this->getNextId();
        $node->getRight()->dotId    = $this->getNextId();

        $this->dot_label($node->dotId, $node->getOperator()->getCode());

        $this->dot_link($node->dotId, $node->getLeft()->dotId);
        $this->dot_link($node->dotId, $node->getRight()->dotId);

        $this->display($node->getLeft(), $level + 1);
        $this->display($node->getRight(), $level + 1);
    }

    function display_arginit($node, $level) {
        $node->getVariable()->dotId    = $this->getNextId();
        $node->getValue()->dotId    = $this->getNextId();

        $this->dot_label($node->dotId, '=');

        $this->dot_link($node->dotId, $node->getVariable()->dotId);
        $this->dot_link($node->dotId, $node->getValue()->dotId);
        
        $this->display($node->getVariable(), $level + 1);
        $this->display($node->getValue(), $level + 1);
    }

    function display_arglist($node, $level) {
        $elements = $node->getList();
        if (count($elements) == 0) {
            
            $this->dot_label($node->dotId, 'Vide');
            return;
        } else {
            $labels = array();
            foreach($elements as $id => &$e) {
                if (is_null($e)) {
die("cas de l'argument null ou inexistant");
                } else {
                    $e->dotId = $this->getNextId();
                    $this->dot_link($node->dotId.":f$id", $e->dotId);
                    $labels[] = $id;
                    $this->display($e, $level + 1);
                }
            }
            $this->dot_struct($node->dotId, $labels, 'arguments');
        }
    }

    function display_block($node, $level) {
        $this->dot_label($node->dotId, get_class($node) );

        $elements = $node->getList();
        foreach($elements as $id => &$e) {
            $e->dotId = $this->getNextId();

            $this->dot_link($node->dotId, $e->dotId);
            $this->display($e, $level + 1);
        }
    }

    function display__break($node, $level) {
        die(__METHOD__);
    }

    function display__case($node, $level) {
        $methods = array('getComparant','getBlock');
        $titre = 'case';
        
        $this->dot_standard($node, $level, $methods, $titre);
    }

    function display_cast($node, $level) {
        $this->dot_label($node->dotId, $node->getCast() );
        $this->dot_standard_one($node, $level, 'getExpression');
    }

    function display__catch($node, $level) {
        $methods = array('getException','getVariable','getBlock');
        $titre = 'catch';
        
        $this->dot_standard($node, $level, $methods, $titre);
    }

    function display__continue($node, $level) {
        $this->dot_standard_one($node, $level, 'getLevels');
    }
    
    function display_ternaryop($node, $level) {
        $elements = array(
            $node->getCondition(),
            $node->getTrue(),
            $node->getElse());
        $labels = array();
        $id = 0;

        foreach($elements as $id => &$e) {
            $e->dotId = $this->getNextId();
            $this->dot_link($node->dotId.":f$id", $e->dotId);
            $labels[] = $id; 
            $this->display($e, $level + 1);            
        }

        $this->dot_struct($node->dotId, $labels, 'condition ternaire');
        return; 


        print str_repeat('  ', $level).get_class($node)." ".$node->getCode()."\n";
        print str_repeat('  ', $level).$node->getCondition();
        print " ? ".$node->getThen()." : ".$node->getElse()."\n";
        $this->display($node->getCondition(), $level + 1);
        $this->display($node->getThen(), $level + 1);
        $this->display($node->getElse(), $level + 1);
    }
    
    function display_codephp($node, $level) {
        if (!isset($node->dotId)) {
            $node->dotId = $this->getNextId();
        }
        $node->getphp_code()->dotId = $this->getNextId();

        $this->dot_label($node->dotId, get_class($node) );
        $this->dot_link($node->dotId, $node->getphp_code()->dotId);

        $this->display($node->getphp_code(), $level + 1);
    }

    function display__class($node, $level) {
        $labels = array();
        $id = 0;

        $abstract = $node->getAbstract();
        if(!is_null($abstract)) {
            $abstract->dotId = $this->getNextId();
            $this->dot_link($node->dotId.":f$id", $abstract->dotId);
            $labels[] = $id; 
            $id++;
            $this->display($abstract, $level + 1);            
        }

        $node->getName()->dotId = $this->getNextId();
        $this->dot_link($node->dotId.":f$id", $node->getName()->dotId);
        $labels[] = $id; 
        $id++;
        $this->display($node->getName(), $level + 1);            
        
        $extends = $node->getExtends();
        if (!is_null($extends)) {
            $extends->dotId = $this->getNextId();
            $this->dot_link($node->dotId.":f$id", $extends->dotId);
            $labels[] = $id; 
            $id++;
            $this->display($extends, $level + 1);            
        }

        $implements = $node->getImplements();
        if (count($implements) > 0) {
            foreach($implements as $implement) {
                $implement->dotId = $this->getNextId(); 
                $this->dot_link($node->dotId.":f$id", $implement->dotId);
                $labels[] = $id; 
                $id++;
                $this->display($implement, $level + 1);            
            }
        }

        $node->getBlock()->dotId = $this->getNextId();
        $this->dot_link($node->dotId.":f$id", $node->getBlock()->dotId);
        $labels[] = $id; 
        $id++;
        $this->display($node->getBlock(), $level + 1);            

        $this->dot_struct($node->dotId, $labels, 'classe');
        return; 
    }

    function display__clone($node, $level) {
        $methods = array('getExpression');
        $titre = 'clone';
        
        $this->dot_standard($node, $level, $methods, $titre);
    }

    function display_keyvalue($node, $level) {
        $this->dot_label($node->dotId, "=>");

        $this->dot_standard_one($node, $level, 'getKey');
        $this->dot_standard_one($node, $level, 'getValue');
    }

    function display_comparison($node, $level) {
        $node->getLeft()->dotId    = $this->getNextId();
        $node->getOperator()->dotId = $this->getNextId();
        $node->getRight()->dotId    = $this->getNextId();

        $this->dot_label($node->dotId, $node->getOperator()->getCode());

        $this->dot_link($node->dotId, $node->getLeft()->dotId);
        $this->dot_link($node->dotId, $node->getRight()->dotId);

        $this->display($node->getLeft(), $level + 1);
        $this->display($node->getRight(), $level + 1);
    }

    function display_concatenation($node, $level) {
        $elements = $node->getList();
        $labels = array();

        foreach($elements as $id => &$e) {
            $e->dotId = $this->getNextId();
            $this->dot_link($node->dotId.":f$id", $e->dotId);
            $labels[] = $id; 
            $this->display($e, $level + 1);            
        }

        $this->dot_struct($node->dotId, $labels, 'concatenation');
    }

    function display_constante($node, $level) {
        $this->dot_label($node->dotId, $node->getName() );
    }

   function display_bitshift($node, $level) {
        $this->dot_standard_one($node, $level, 'getLeft');
        $this->dot_standard_one($node, $level, 'getOperator');
        $this->dot_standard_one($node, $level, 'getRight');
    }
    
    function display__default($node, $level) {
        $this->dot_standard_one($node, $level, 'getBlock');
    }

    function display__for($node, $level) {
        $node->getInit()->dotId    = $this->getNextId();
        $node->getFin()->dotId = $this->getNextId();
        $node->getIncrement()->dotId    = $this->getNextId();
        $node->getBlock()->dotId    = $this->getNextId();
        
        $labels = range(0,3);
        $titre = 'for';

        $this->dot_link($node->dotId.":f0", $node->getInit()->dotId);
        $this->dot_link($node->dotId.":f1", $node->getFin()->dotId);
        $this->dot_link($node->dotId.":f2", $node->getIncrement()->dotId);
        $this->dot_link($node->dotId.":f3", $node->getBlock()->dotId);

        $this->dot_struct($node->dotId, $labels, 'for');

        $this->display($node->getInit(), $level + 1);
        $this->display($node->getFin(), $level + 1);
        $this->display($node->getIncrement(), $level + 1);
        $this->display($node->getBlock(), $level + 1);

    }

    function display__foreach($node, $level) {
        $node->getArray()->dotId    = $this->getNextId();
        if (!is_null($node->getKey())) {
            $node->getKey()->dotId = $this->getNextId();
            $this->dot_link($node->dotId.":f1", $node->getKey()->dotId);
            $this->display($node->getKey(), $level + 1);
        }
        $node->getValue()->dotId    = $this->getNextId();
        $node->getBlock()->dotId    = $this->getNextId();
        
        $labels = range(0,3);
        $titre = 'for';

        $this->dot_link($node->dotId.":f0", $node->getArray()->dotId);
        $this->dot_link($node->dotId.":f2", $node->getValue()->dotId);
        $this->dot_link($node->dotId.":f3", $node->getBlock()->dotId);

        $this->dot_struct($node->dotId, $labels, 'foreach');

        $this->display($node->getArray(), $level + 1);
        $this->display($node->getValue(), $level + 1);
        $this->display($node->getBlock(), $level + 1);
    }

    function display__function($node, $level) {
        $this->dot_standard_one($node, $level, 'getVisibility');
        $this->dot_standard_one($node, $level, 'getAbstract');
        $this->dot_standard_one($node, $level, 'getStatic');
        $this->dot_standard_one($node, $level, 'getName');
        $this->dot_standard_one($node, $level, 'getArgs');
        $this->dot_standard_one($node, $level, 'getBlock');

        $this->dot_struct($node->dotId, array(), 'method');
    }

    function display_functioncall($node, $level) {
        $this->dot_label($node->dotId, $node->getFunction() );
        
        $node->getArgs()->dotId = $this->getNextId();
        $this->dot_link($node->dotId, $node->getArgs()->dotId);
        
        $args = $node->getArgs();
        $this->display($args, $level + 1);
    }

    function display__global($node, $level) {
        $this->dot_label($node->dotId, "global" );

        $elements = $node->getVariables();
        foreach($elements as $id => $e) {
            $e->dotId = $this->getNextId();
            $this->dot_link($node->dotId, $e->dotId);

            $this->display($e, $level + 1);
        }
    }

    function display_ifthen($node, $level) {
        $conditions = $node->getCondition();
        $thens = $node->getThen();
        $labels = array();

        foreach($conditions as $id => &$condition) {
            $condition->dotId = $this->getNextId();
            $this->dot_link($node->dotId.":f$id", $condition->dotId);
            $labels[] = $id; 
            $this->display($condition, $level + 1);

            $thens[$id]->dotId = $this->getNextId();
            $this->dot_link($condition->dotId, $thens[$id]->dotId);
            $this->display($thens[$id], $level + 1);
            
        }

        $else = $node->getElse();
        if (!is_null($else)){
            $else->dotId = $this->getNextId();
            $id++;
            $this->dot_link($node->dotId.":f$id", $else->dotId);
            $labels[] = "else"; 

            $this->display($else, $level + 1);
        }

        $this->dot_struct($node->dotId, $labels, 'ifthen');
    }

    function display_inclusion($node, $level) {
        $this->dot_standard_one($node, $level, 'getInclusion'); 
    }

    function display_literals($node, $level) {
        $this->dot_label($node->dotId, $node->getCode() );
    }

    function display_logique($node, $level) {
        $node->getLeft()->dotId    = $this->getNextId();
        $node->getOperator()->dotId = $this->getNextId();
        $node->getRight()->dotId    = $this->getNextId();

        $this->dot_label($node->dotId, $node->getOperator()->getCode());

        $this->dot_link($node->dotId, $node->getLeft()->dotId);
        $this->dot_link($node->dotId, $node->getRight()->dotId);

        $this->display($node->getLeft(), $level + 1);
        $this->display($node->getRight(), $level + 1);
    }

    function display_method($node, $level) {
        $methods = array('getObject','getMethod');
        $titre = "$node";

        $this->dot_standard($node, $level, $methods, $titre);        
    }

    function display_method_static($node, $level) {
        $methods = array('getClass','getMethod');
        $titre = "$node";

        $this->dot_standard($node, $level, $methods, $titre);        
    }

    function display__new($node, $level) {
        $this->dot_standard_one($node, $level, 'getClasse');
        $this->dot_standard_one($node, $level, 'getArgs');
    }
    
    function display_noscream($node, $level) {
        $this->dot_standard_one($node, $level, 'getExpression');
    }

    function display_not($node, $level) {
        $this->dot_label($node->dotId, "Not" );

        $node->getExpression()->dotId = $this->getNextId();

        $this->dot_link($node->dotId, $node->getExpression()->dotId);

        $this->display($node->getExpression(), $level + 1);
    }

    function display_opappend($node, $level) {
        $this->dot_label($node->dotId, "[]" );
        $this->dot_standard_one($node, $level, 'getVariable');
    }

    function display_operation($node, $level) {
        $this->dot_standard_one($node, $level, 'getLeft');
        $this->dot_standard_one($node, $level, 'getOperation');
        $this->dot_standard_one($node, $level, 'getRight');
    }

    function display_parentheses($node, $level) {
        $node->getContenu()->dotId = $this->getNextId();
        
        $this->dot_label($node->dotId, get_class($node) );

        $this->dot_link($node->dotId, $node->getContenu()->dotId);

        $this->display($node->getContenu(), $level + 1);
    }

    function display_postplusplus($node, $level) {
        $node->getVariable( )->dotId = $this->getNextId();
        $node->getOperator()->dotId = $this->getNextId();

        $this->dot_label($node->dotId, $node->getOperator()->getCode() );

        $this->dot_link($node->dotId, $node->getVariable()->dotId);
        $this->dot_link($node->dotId, $node->getOperator()->dotId);

        $this->display($node->getVariable(), $level + 1);
        $this->display($node->getOperator(), $level + 1);
    }

    function display_property($node, $level) {
        $methods = array('getObject','getProperty');
        $titre = 'property';
        
        $this->dot_standard($node, $level, $methods, $titre);
    }

    function display_property_static($node, $level) {
        $methods = array('getClass','getProperty');
        $titre = 'property static';
        
        $this->dot_standard($node, $level, $methods, $titre);
    }

    function display_rawtext($node, $level) {
        $this->dot_label($node->dotId, $node->getText());
    }

    function display_reference($node, $level) {
        $this->dot_label($node->dotId, "&" );

        $this->dot_standard_one($node, $level, 'getExpression');
    }

    function display__return($node, $level) {
        $this->dot_standard_one($node, $level, 'getReturn');
    }

    function display_sequence($node, $level) {
        $elements = $node->getElements();
        if (count($elements) == 0) {
            die("cas de la sequence vide");
        } else {
            $labels = array();
            $id = 0;
            foreach($elements as $id => &$e) {
                if (is_null($e)) {
                    die("cas de l'argument null ou inexistant dans une sequence");
                } else {
                    $e->dotId = $this->getNextId();
                    $this->dot_link($node->dotId.":f$id", $e->dotId);
                    $labels[] = $id;
                    $this->display($e, $level + 1);
                }
            }
            $this->dot_struct($node->dotId, $labels, 'sequence');
        }
    }

    function display__switch($node, $level) {
        $methods = array('getCondition','getBlock');
        $titre = 'switch';
        
        $this->dot_standard($node, $level, $methods, $titre);
    }

    function display__array($node, $level) {
        $methods = array('getIndex','getVariable');
        $titre = 'array';
        
        $this->dot_standard($node, $level, $methods, $titre);
    }

    function display__try($node, $level) {
        $this->dot_label($node->dotId, $node->getCode() );

        $node->getBlock()->dotId = $this->getNextId();

        $this->dot_link($node->dotId, $node->getBlock()->dotId);

        $this->display($node->getBlock(), $level + 1);

        $elements = $node->getCatch();
        foreach($elements as $id => &$e) {
            $e->dotId = $this->getNextId();
            $this->dot_link($node->dotId, $e->dotId);
            $this->display($e, $level + 1);
        }
    }

    function display__var($node, $level) {
        $methods = array('getVisibility','getStatic','getVariable');
        $titre = 'var';
        
        $this->dot_standard($node, $level, $methods, $titre);
    }

    function display_variable($node, $level) {
        $this->dot_label($node->dotId, $node->getCode() );
    }

    function display__while($node, $level) {
        $node->getCondition()->dotId = $this->getNextId();
        $node->getBlock()->dotId = $this->getNextId();

        $this->dot_link($node->dotId.":f0", $node->getCondition()->dotId);
        $this->dot_link($node->dotId.":f1", $node->getBlock()->dotId);

        $this->display($node->getCondition(), $level + 1);
        $this->display($node->getBlock(), $level + 1);

        $labels = array(0,1);
        $this->dot_struct($node->dotId, $labels, 'while');
    }

    function display__dowhile($node, $level) {
        $node->getCondition()->dotId = $this->getNextId();
        $node->getBlock()->dotId = $this->getNextId();

        $this->dot_link($node->dotId.":f0", $node->getCondition()->dotId);
        $this->dot_link($node->dotId.":f1", $node->getBlock()->dotId);

        $this->display($node->getCondition(), $level + 1);
        $this->display($node->getBlock(), $level + 1);

        $labels = array(0,1);
        $this->dot_struct($node->dotId, $labels, 'do..while');
    }
    
    function display_Token($node, $level) {
        $this->dot_label($node->dotId, $node->getCode() );
    }
}

?>
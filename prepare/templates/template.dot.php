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
    
    function affiche($noeud = null, $level = 0) {
        if ($level > 100) {
            print "Attention : plus de 100 level de récursion (annulation)\n"; die();
        }
        if (is_null($noeud)) {
            $noeud = $this->root;
        }
        
        if (!is_object($noeud)) {
            print "Found an null reference in ".__METHOD__."\n";
            die();
        }
        $class = get_class($noeud);
        $method = "affiche_$class";
        
        if (method_exists($this, $method)) {
            $this->$method($noeud, $level + 1);
        } else {
            print "Affichage ".__CLASS__." de '".$method."'\n";die;
        }
        if (!is_null($noeud->getNext())){
            $this->affiche($noeud->getNext(), $level);
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

    function dot_label($noeud, $name) {
        $name = str_replace('"', '\\"', $name);
        $name = str_replace("\n", '\\n', $name);
        $this->dot .=  $noeud." [label=\"".$name."\"];\n";
    }

    function dot_struct($noeud, $labels, $title = '[Titre]') {
        foreach($labels as $id => &$l) {
            $l = "<f$id> $l";
        }
        $labels = join('|', $labels);
        $this->dot .=  $noeud." [shape=record,label=\"{".$title." | {".$labels."}} \"];\n";

    }
    
    function dot_standard($noeud, $level, $methods, $title) {
        foreach($methods as $id => $m) {
            if (is_null($noeud->$m())) {
                unset($methods[$id]);
                continue;
            }
            $noeud->$m()->dotId    = $this->getNextId();
        }

        $this->dot_label($noeud->dotId, $title);

        foreach($methods as $m) {
            $this->dot_link($noeud->dotId, $noeud->$m()->dotId);
            $this->affiche($noeud->$m(), $level + 1);
        }
    }
    
    function dot_standard_one($noeud, $level, $method) {
        $result = $noeud->$method();
        
        if(!is_null($result)) {
            if (!is_object($result)) { 
                var_dump($result);
                die("$method ".__FILE__." ".__LINE__."\n");
            }
            $result->dotId = $this->getNextId();
            $this->dot_link($noeud->dotId, $result->dotId);
            $this->affiche($result, $level + 1);
        }
    }

////////////////////////////////////////////////////////////////////////
// @section dot function 
////////////////////////////////////////////////////////////////////////
    function affiche_token_traite($noeud, $level) {
        $this->dot_label($noeud->dotId, $noeud->getCode() );
    }

    function affiche_affectation($noeud, $level) {
        $noeud->getDroite()->dotId    = $this->getNextId();
        $noeud->getOperateur()->dotId = $this->getNextId();
        $noeud->getGauche()->dotId    = $this->getNextId();

        $this->dot_label($noeud->dotId, $noeud->getOperateur()->getCode());

        $this->dot_link($noeud->dotId, $noeud->getDroite()->dotId);
        $this->dot_link($noeud->dotId, $noeud->getGauche()->dotId);

        $this->affiche($noeud->getDroite(), $level + 1);
        $this->affiche($noeud->getGauche(), $level + 1);
    }

    function affiche_arginit($noeud, $level) {
        $noeud->getVariable()->dotId    = $this->getNextId();
        $noeud->getValeur()->dotId    = $this->getNextId();

        $this->dot_label($noeud->dotId, '=');

        $this->dot_link($noeud->dotId, $noeud->getVariable()->dotId);
        $this->dot_link($noeud->dotId, $noeud->getValeur()->dotId);
        
        $this->affiche($noeud->getVariable(), $level + 1);
        $this->affiche($noeud->getValeur(), $level + 1);
    }

    function affiche_arglist($noeud, $level) {
        $elements = $noeud->getList();
        if (count($elements) == 0) {
            
            $this->dot_label($noeud->dotId, 'Vide');
            return;
        } else {
            $labels = array();
            foreach($elements as $id => &$e) {
                if (is_null($e)) {
die("cas de l'argument null ou inexistant");
                } else {
                    $e->dotId = $this->getNextId();
                    $this->dot_link($noeud->dotId.":f$id", $e->dotId);
                    $labels[] = $id;
                    $this->affiche($e, $level + 1);
                }
            }
            $this->dot_struct($noeud->dotId, $labels, 'arguments');
        }
    }

    function affiche_block($noeud, $level) {
        $this->dot_label($noeud->dotId, get_class($noeud) );

        $elements = $noeud->getList();
        foreach($elements as $id => &$e) {
            $e->dotId = $this->getNextId();

            $this->dot_link($noeud->dotId, $e->dotId);
            $this->affiche($e, $level + 1);
        }
    }

    function affiche__break($noeud, $level) {
        die(__METHOD__);
    }

    function affiche__case($noeud, $level) {
        $methods = array('getComparant','getBlock');
        $titre = 'case';
        
        $this->dot_standard($noeud, $level, $methods, $titre);
    }

    function affiche_cast($noeud, $level) {
        $this->dot_label($noeud->dotId, $noeud->getCast() );
        $this->dot_standard_one($noeud, $level, 'getExpression');
    }

    function affiche__catch($noeud, $level) {
        $methods = array('getException','getVariable','getBlock');
        $titre = 'catch';
        
        $this->dot_standard($noeud, $level, $methods, $titre);
    }

    function affiche__continue($noeud, $level) {
        $this->dot_standard_one($noeud, $level, 'getLevels');
    }
    
    function affiche_ternaryop($noeud, $level) {
        $elements = array(
            $noeud->getCondition(),
            $noeud->getTrue(),
            $noeud->getElse());
        $labels = array();
        $id = 0;

        foreach($elements as $id => &$e) {
            $e->dotId = $this->getNextId();
            $this->dot_link($noeud->dotId.":f$id", $e->dotId);
            $labels[] = $id; 
            $this->affiche($e, $level + 1);            
        }

        $this->dot_struct($noeud->dotId, $labels, 'condition ternaire');
        return; 


        print str_repeat('  ', $level).get_class($noeud)." ".$noeud->getCode()."\n";
        print str_repeat('  ', $level).$noeud->getCondition();
        print " ? ".$noeud->getThen()." : ".$noeud->getElse()."\n";
        $this->affiche($noeud->getCondition(), $level + 1);
        $this->affiche($noeud->getThen(), $level + 1);
        $this->affiche($noeud->getElse(), $level + 1);
    }
    
    function affiche_codephp($noeud, $level) {
        if (!isset($noeud->dotId)) {
            $noeud->dotId = $this->getNextId();
        }
        $noeud->getphp_code()->dotId = $this->getNextId();

        $this->dot_label($noeud->dotId, get_class($noeud) );
        $this->dot_link($noeud->dotId, $noeud->getphp_code()->dotId);

        $this->affiche($noeud->getphp_code(), $level + 1);
    }

    function affiche__class($noeud, $level) {
        $labels = array();
        $id = 0;

        $abstract = $noeud->getAbstract();
        if(!is_null($abstract)) {
            $abstract->dotId = $this->getNextId();
            $this->dot_link($noeud->dotId.":f$id", $abstract->dotId);
            $labels[] = $id; 
            $id++;
            $this->affiche($abstract, $level + 1);            
        }

        $noeud->getName()->dotId = $this->getNextId();
        $this->dot_link($noeud->dotId.":f$id", $noeud->getName()->dotId);
        $labels[] = $id; 
        $id++;
        $this->affiche($noeud->getName(), $level + 1);            
        
        $extends = $noeud->getExtends();
        if (!is_null($extends)) {
            $extends->dotId = $this->getNextId();
            $this->dot_link($noeud->dotId.":f$id", $extends->dotId);
            $labels[] = $id; 
            $id++;
            $this->affiche($extends, $level + 1);            
        }

        $implements = $noeud->getImplements();
        if (count($implements) > 0) {
            foreach($implements as $implement) {
                $implement->dotId = $this->getNextId(); 
                $this->dot_link($noeud->dotId.":f$id", $implement->dotId);
                $labels[] = $id; 
                $id++;
                $this->affiche($implement, $level + 1);            
            }
        }

        $noeud->getBlock()->dotId = $this->getNextId();
        $this->dot_link($noeud->dotId.":f$id", $noeud->getBlock()->dotId);
        $labels[] = $id; 
        $id++;
        $this->affiche($noeud->getBlock(), $level + 1);            

        $this->dot_struct($noeud->dotId, $labels, 'classe');
        return; 
    }

    function affiche__clone($noeud, $level) {
        $methods = array('getExpression');
        $titre = 'clone';
        
        $this->dot_standard($noeud, $level, $methods, $titre);
    }

    function affiche_keyvalue($noeud, $level) {
        $this->dot_label($noeud->dotId, "=>");

        $this->dot_standard_one($noeud, $level, 'getKey');
        $this->dot_standard_one($noeud, $level, 'getValue');
    }

    function affiche_comparison($noeud, $level) {
        $noeud->getDroite()->dotId    = $this->getNextId();
        $noeud->getOperateur()->dotId = $this->getNextId();
        $noeud->getGauche()->dotId    = $this->getNextId();

        $this->dot_label($noeud->dotId, $noeud->getOperateur()->getCode());

        $this->dot_link($noeud->dotId, $noeud->getDroite()->dotId);
        $this->dot_link($noeud->dotId, $noeud->getGauche()->dotId);

        $this->affiche($noeud->getDroite(), $level + 1);
        $this->affiche($noeud->getGauche(), $level + 1);
    }

    function affiche_concatenation($noeud, $level) {
        $elements = $noeud->getList();
        $labels = array();

        foreach($elements as $id => &$e) {
            $e->dotId = $this->getNextId();
            $this->dot_link($noeud->dotId.":f$id", $e->dotId);
            $labels[] = $id; 
            $this->affiche($e, $level + 1);            
        }

        $this->dot_struct($noeud->dotId, $labels, 'concatenation');
    }

    function affiche_constante($noeud, $level) {
        $this->dot_label($noeud->dotId, $noeud->getName() );
    }

   function affiche_decalage($noeud, $level) {
        $this->dot_standard_one($noeud, $level, 'getDroite');
        $this->dot_standard_one($noeud, $level, 'getOperateur');
        $this->dot_standard_one($noeud, $level, 'getGauche');
    }
    
    function affiche__default($noeud, $level) {
        $this->dot_standard_one($noeud, $level, 'getBlock');
    }

    function affiche__for($noeud, $level) {
        $noeud->getInit()->dotId    = $this->getNextId();
        $noeud->getFin()->dotId = $this->getNextId();
        $noeud->getIncrement()->dotId    = $this->getNextId();
        $noeud->getBlock()->dotId    = $this->getNextId();
        
        $labels = range(0,3);
        $titre = 'for';

        $this->dot_link($noeud->dotId.":f0", $noeud->getInit()->dotId);
        $this->dot_link($noeud->dotId.":f1", $noeud->getFin()->dotId);
        $this->dot_link($noeud->dotId.":f2", $noeud->getIncrement()->dotId);
        $this->dot_link($noeud->dotId.":f3", $noeud->getBlock()->dotId);

        $this->dot_struct($noeud->dotId, $labels, 'for');

        $this->affiche($noeud->getInit(), $level + 1);
        $this->affiche($noeud->getFin(), $level + 1);
        $this->affiche($noeud->getIncrement(), $level + 1);
        $this->affiche($noeud->getBlock(), $level + 1);

    }

    function affiche__foreach($noeud, $level) {
        $noeud->getTableau()->dotId    = $this->getNextId();
        if (!is_null($noeud->getKey())) {
            $noeud->getKey()->dotId = $this->getNextId();
            $this->dot_link($noeud->dotId.":f1", $noeud->getKey()->dotId);
            $this->affiche($noeud->getKey(), $level + 1);
        }
        $noeud->getValue()->dotId    = $this->getNextId();
        $noeud->getBlock()->dotId    = $this->getNextId();
        
        $labels = range(0,3);
        $titre = 'for';

        $this->dot_link($noeud->dotId.":f0", $noeud->getTableau()->dotId);
        $this->dot_link($noeud->dotId.":f2", $noeud->getValue()->dotId);
        $this->dot_link($noeud->dotId.":f3", $noeud->getBlock()->dotId);

        $this->dot_struct($noeud->dotId, $labels, 'foreach');

        $this->affiche($noeud->getTableau(), $level + 1);
        $this->affiche($noeud->getValue(), $level + 1);
        $this->affiche($noeud->getBlock(), $level + 1);
    }

    function affiche__function($noeud, $level) {
        $this->dot_standard_one($noeud, $level, 'getVisibility');
        $this->dot_standard_one($noeud, $level, 'getAbstract');
        $this->dot_standard_one($noeud, $level, 'getStatic');
        $this->dot_standard_one($noeud, $level, 'getName');
        $this->dot_standard_one($noeud, $level, 'getArgs');
        $this->dot_standard_one($noeud, $level, 'getBlock');

        $this->dot_struct($noeud->dotId, array(), 'method');
    }

    function affiche_functioncall($noeud, $level) {
        $this->dot_label($noeud->dotId, $noeud->getFunction() );
        
        $noeud->getArgs()->dotId = $this->getNextId();
        $this->dot_link($noeud->dotId, $noeud->getArgs()->dotId);
        
        $args = $noeud->getArgs();
        $this->affiche($args, $level + 1);
    }

    function affiche__global($noeud, $level) {
        $this->dot_label($noeud->dotId, "global" );

        $elements = $noeud->getVariables();
        foreach($elements as $id => $e) {
            $e->dotId = $this->getNextId();
            $this->dot_link($noeud->dotId, $e->dotId);

            $this->affiche($e, $level + 1);
        }
    }

    function affiche_ifthen($noeud, $level) {
        $conditions = $noeud->getCondition();
        $thens = $noeud->getThen();
        $labels = array();

        foreach($conditions as $id => &$condition) {
            $condition->dotId = $this->getNextId();
            $this->dot_link($noeud->dotId.":f$id", $condition->dotId);
            $labels[] = $id; 
            $this->affiche($condition, $level + 1);

            $thens[$id]->dotId = $this->getNextId();
            $this->dot_link($condition->dotId, $thens[$id]->dotId);
            $this->affiche($thens[$id], $level + 1);
            
        }

        $else = $noeud->getElse();
        if (!is_null($else)){
            $else->dotId = $this->getNextId();
            $id++;
            $this->dot_link($noeud->dotId.":f$id", $else->dotId);
            $labels[] = "else"; 

            $this->affiche($else, $level + 1);
        }

        $this->dot_struct($noeud->dotId, $labels, 'ifthen');
    }

    function affiche_inclusion($noeud, $level) {
        $this->dot_standard_one($noeud, $level, 'getInclusion'); 
    }

    function affiche_literals($noeud, $level) {
        $this->dot_label($noeud->dotId, $noeud->getCode() );
    }

    function affiche_logique($noeud, $level) {
        $noeud->getDroite()->dotId    = $this->getNextId();
        $noeud->getOperateur()->dotId = $this->getNextId();
        $noeud->getGauche()->dotId    = $this->getNextId();

        $this->dot_label($noeud->dotId, $noeud->getOperateur()->getCode());

        $this->dot_link($noeud->dotId, $noeud->getDroite()->dotId);
        $this->dot_link($noeud->dotId, $noeud->getGauche()->dotId);

        $this->affiche($noeud->getDroite(), $level + 1);
        $this->affiche($noeud->getGauche(), $level + 1);
    }

    function affiche_method($noeud, $level) {
        $methods = array('getObject','getMethod');
        $titre = "$noeud";

        $this->dot_standard($noeud, $level, $methods, $titre);        
    }

    function affiche_method_static($noeud, $level) {
        $methods = array('getClass','getMethod');
        $titre = "$noeud";

        $this->dot_standard($noeud, $level, $methods, $titre);        
    }

    function affiche__new($noeud, $level) {
        $this->dot_standard_one($noeud, $level, 'getClasse');
        $this->dot_standard_one($noeud, $level, 'getArgs');
    }
    
    function affiche_noscream($noeud, $level) {
        $this->dot_standard_one($noeud, $level, 'getExpression');
    }

    function affiche_not($noeud, $level) {
        $this->dot_label($noeud->dotId, "Not" );

        $noeud->getExpression()->dotId = $this->getNextId();

        $this->dot_link($noeud->dotId, $noeud->getExpression()->dotId);

        $this->affiche($noeud->getExpression(), $level + 1);
    }

    function affiche_opappend($noeud, $level) {
        $this->dot_label($noeud->dotId, "[]" );
        $this->dot_standard_one($noeud, $level, 'getVariable');
    }

    function affiche_operation($noeud, $level) {
        $this->dot_standard_one($noeud, $level, 'getDroite');
        $this->dot_standard_one($noeud, $level, 'getOperation');
        $this->dot_standard_one($noeud, $level, 'getGauche');
    }

    function affiche_parentheses($noeud, $level) {
        $noeud->getContenu()->dotId = $this->getNextId();
        
        $this->dot_label($noeud->dotId, get_class($noeud) );

        $this->dot_link($noeud->dotId, $noeud->getContenu()->dotId);

        $this->affiche($noeud->getContenu(), $level + 1);
    }

    function affiche_postplusplus($noeud, $level) {
        $noeud->getVariable( )->dotId = $this->getNextId();
        $noeud->getOperateur()->dotId = $this->getNextId();

        $this->dot_label($noeud->dotId, $noeud->getOperateur()->getCode() );

        $this->dot_link($noeud->dotId, $noeud->getVariable()->dotId);
        $this->dot_link($noeud->dotId, $noeud->getOperateur()->dotId);

        $this->affiche($noeud->getVariable(), $level + 1);
        $this->affiche($noeud->getOperateur(), $level + 1);
    }

    function affiche_property($noeud, $level) {
        $methods = array('getObject','getProperty');
        $titre = 'property';
        
        $this->dot_standard($noeud, $level, $methods, $titre);
    }

    function affiche_property_static($noeud, $level) {
        $methods = array('getClass','getProperty');
        $titre = 'property static';
        
        $this->dot_standard($noeud, $level, $methods, $titre);
    }

    function affiche_rawtext($noeud, $level) {
        $this->dot_label($noeud->dotId, $noeud->getText());
    }

    function affiche_reference($noeud, $level) {
        $this->dot_label($noeud->dotId, "&" );

        $this->dot_standard_one($noeud, $level, 'getExpression');
    }

    function affiche__return($noeud, $level) {
        $this->dot_standard_one($noeud, $level, 'getReturn');
    }

    function affiche_sequence($noeud, $level) {
        $elements = $noeud->getElements();
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
                    $this->dot_link($noeud->dotId.":f$id", $e->dotId);
                    $labels[] = $id;
                    $this->affiche($e, $level + 1);
                }
            }
            $this->dot_struct($noeud->dotId, $labels, 'sequence');
        }
    }

    function affiche__switch($noeud, $level) {
        $methods = array('getCondition','getBlock');
        $titre = 'switch';
        
        $this->dot_standard($noeud, $level, $methods, $titre);
    }

    function affiche_tableau($noeud, $level) {
        $methods = array('getIndex','getVariable');
        $titre = 'tableau';
        
        $this->dot_standard($noeud, $level, $methods, $titre);
    }

    function affiche__try($noeud, $level) {
        $this->dot_label($noeud->dotId, $noeud->getCode() );

        $noeud->getBlock()->dotId = $this->getNextId();

        $this->dot_link($noeud->dotId, $noeud->getBlock()->dotId);

        $this->affiche($noeud->getBlock(), $level + 1);

        $elements = $noeud->getCatch();
        foreach($elements as $id => &$e) {
            $e->dotId = $this->getNextId();
            $this->dot_link($noeud->dotId, $e->dotId);
            $this->affiche($e, $level + 1);
        }
    }

    function affiche__var($noeud, $level) {
        $methods = array('getVisibility','getStatic','getVariable');
        $titre = 'var';
        
        $this->dot_standard($noeud, $level, $methods, $titre);
    }

    function affiche_variable($noeud, $level) {
        $this->dot_label($noeud->dotId, $noeud->getCode() );
    }

    function affiche__while($noeud, $level) {
        $noeud->getCondition()->dotId = $this->getNextId();
        $noeud->getBlock()->dotId = $this->getNextId();

        $this->dot_link($noeud->dotId.":f0", $noeud->getCondition()->dotId);
        $this->dot_link($noeud->dotId.":f1", $noeud->getBlock()->dotId);

        $this->affiche($noeud->getCondition(), $level + 1);
        $this->affiche($noeud->getBlock(), $level + 1);

        $labels = array(0,1);
        $this->dot_struct($noeud->dotId, $labels, 'while');
    }

    function affiche__dowhile($noeud, $level) {
        $noeud->getCondition()->dotId = $this->getNextId();
        $noeud->getBlock()->dotId = $this->getNextId();

        $this->dot_link($noeud->dotId.":f0", $noeud->getCondition()->dotId);
        $this->dot_link($noeud->dotId.":f1", $noeud->getBlock()->dotId);

        $this->affiche($noeud->getCondition(), $level + 1);
        $this->affiche($noeud->getBlock(), $level + 1);

        $labels = array(0,1);
        $this->dot_struct($noeud->dotId, $labels, 'do..while');
    }
    
    function affiche_Token($noeud, $level) {
        $this->dot_label($noeud->dotId, $noeud->getCode() );
    }
}

?>
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

class template_stats extends template {
    protected $root = null;
    protected $stats = array('missing' => array());
    
    function __construct($root) {
        parent::__construct();
        
        $this->root = $root;
    }
    
    function affiche($node = null, $niveau = 0) {
        if (is_null($node)) {
            $node = $this->root;
        }
        
        if (!is_object($node)) {
            print "Fatal : attemptint to display a non-object in ".__METHOD__."\n\n";
        }
        $class = get_class($node);
        $method = "affiche_$class";

        if (method_exists($this, $method)) {
            $this->$method($node, $niveau + 1);
        } else {
            $this->stats['missing'][$method] = 1;
        }
        if (!is_null($node->getNext())){
            $this->affiche($node->getNext(), $niveau);
        } else {
            if ($niveau == 0) {
                if (count($this->stats['missing']) == 0) { 
                    unset($this->stats['missing']); 
                }
            }
        }
    }
    
    function addStat($name) {
        if (substr($name, 0, 8) == 'affiche_') {
            $name = substr($name, 8);
        }
        
        if (isset( $this->stats[$name])) {
            $this->stats[$name]++;        
        } else {
            $this->stats[$name] = 1;
        }
    }

    function affiche_arginit($node, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($node->getVariable(), $niveau + 1);
        $this->affiche($node->getValeur(), $niveau + 1);
    }

    function affiche_arglist($node, $niveau) {
        $this->addStat(__FUNCTION__);
        $elements = $node->getList();
        if (is_array($elements)) {
            foreach($elements as $id => $e) {
                if (!is_null($e)) {
                    $this->affiche($e, $niveau + 1);
                }
            }
        }
    }

    function affiche_affectation($node, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($node->getDroite(), $niveau + 1);
        $this->affiche($node->getOperateur(), $niveau + 1);
        $this->affiche($node->getGauche(), $niveau + 1);
    }

    function affiche_block($node, $niveau) {
        $this->addStat(__FUNCTION__);
        $elements = $node->getList();
        foreach($elements as $id => $e) {
            $this->affiche($e, $niveau + 1);
        }
    }

    function affiche__break($node, $niveau) {
        $this->addStat(__FUNCTION__);
    }

    function affiche__case($node, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($node->getComparant(), $niveau + 1);
        $this->affiche($node->getBlock(), $niveau + 1);
    }

    function affiche_cast($node, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($node->getExpression(), $niveau + 1);
    }

    function affiche__catch($node, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($node->getException(), $niveau + 1);
        $this->affiche($node->getVariable(), $niveau + 1);
        $this->affiche($node->getBlock(), $niveau + 1);
    }

    function affiche__class($node, $niveau) {
        $this->addStat(__FUNCTION__);
        $extends = $node->getExtends();
        if (!is_null($extends)) {
            $this->affiche($extends, $niveau + 1);
        }
        $implements = $node->getImplements();
        if (count($implements) > 0) {
            foreach($implements as $i) {
                $this->affiche($i, $niveau + 1);
            }
        }
        $this->affiche($node->getBlock(), $niveau + 1);
    }

    function affiche_keyvalue($node, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($node->getKey(), $niveau + 1);
        $this->affiche($node->getValue(), $niveau + 1);
    }

    function affiche__clone($node, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($node->getExpression(), $niveau + 1);
    }

    function affiche_comparison($node, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($node->getDroite(), $niveau + 1);
        $this->affiche($node->getGauche(), $niveau + 1);
    }
    
    function affiche_ternaryop($node, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($node->getCondition(), $niveau + 1);
        $this->affiche($node->getThen(), $niveau + 1);
        $this->affiche($node->getElse(), $niveau + 1);
    }

    function affiche_codephp($node, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($node->getphp_code(), $niveau + 1);
    }

    function affiche_concatenation($node, $niveau) {
        $this->addStat(__FUNCTION__);
        $elements = $node->getList();
        foreach($elements as $id => $e) {
            $this->affiche($e, $niveau + 1);
        }
    }

    function affiche__continue($node, $niveau) {
        $this->addStat(__FUNCTION__);
    }

    function affiche_constante($node, $niveau) {
        $this->addStat(__FUNCTION__);
    }

    function affiche_constante_static($node, $niveau) {
        $this->addStat(__FUNCTION__);
         $this->affiche($node->getClass(), $niveau + 1);
         $this->affiche($node->getConstant(), $niveau + 1);
    }

    function affiche_constante_classe($node, $niveau) {
        $this->addStat(__FUNCTION__);
         $this->affiche($node->getName(), $niveau + 1);
         $this->affiche($node->getConstante(), $niveau + 1);
    }

    function affiche_decalage($node, $niveau) {
        $this->addStat(__FUNCTION__);
         $this->affiche($node->getDroite(), $niveau + 1);
         $this->affiche($node->getOperateur(), $niveau + 1);
         $this->affiche($node->getGauche(), $niveau + 1);
    }

    function affiche__declare($node, $niveau) {
        $this->addStat(__FUNCTION__);
         $this->affiche($node->getTicks(), $niveau + 1);
         $this->affiche($node->getEncoding(), $niveau + 1);
         $n = $node->getBlock();
         if (!is_null($n)) {
             $this->affiche($n, $niveau + 1);
         }
    }

    function affiche__default($node, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($node->getBlock(), $niveau + 1);
    }

    function affiche__for($node, $niveau) {
        $this->addStat(__FUNCTION__);

        $init = $node->getInit();
        if (!is_null($init)) {
            $this->affiche($init, $niveau + 1);
        }

        $fin = $node->getFin();
        if (!is_null($fin)) {
            $this->affiche($fin, $niveau + 1);
        }

        $increment = $node->getIncrement();
        if (!is_null($increment)) {
            $this->affiche($increment, $niveau + 1);
        }
        
        $this->affiche($node->getBlock(), $niveau + 1);
    }

    function affiche__foreach($node, $niveau) {
        $this->addStat(__FUNCTION__);

        $gets = array('getTableau','getKey','getValue','getBlock');

        foreach($gets as $get) {
            $list = $node->$get();
            if (!is_null($list)) {
                $this->affiche($list, $niveau + 1);
            }
        }
    }

  function affiche__function($node, $niveau) {
        $this->addStat(__FUNCTION__);

        $this->affiche($node->getName(), $niveau + 1);
        $this->affiche($node->getArgs(), $niveau + 1);
        $this->affiche($node->getBlock(), $niveau + 1);
    }

    function affiche__global($node, $niveau) {
        $this->addStat(__FUNCTION__);
        $elements = $node->getVariables();
        foreach($elements as $id => $e) {
            $this->affiche($e, $niveau + 1);
        }
    }

    function affiche_functioncall($node, $niveau) {
        $this->addStat(__FUNCTION__);

        $args = $node->getArgs();
        $this->affiche($args, $niveau + 1);
    }

    function affiche____halt_compiler($node, $niveau) {
        $this->addStat(__FUNCTION__);
    }

    function affiche_ifthen($node, $niveau) {
        $this->addStat(__FUNCTION__);
        $conditions = $node->getCondition();
        $thens = $node->getThen();
        foreach($conditions as $id => $condition) {
            $this->affiche($condition, $niveau + 1);
            $this->affiche($thens[$id], $niveau + 1);
        }
        if (!is_null($node->getElse())){
            $this->affiche($node->getElse(), $niveau + 1);
        }
    }

    function affiche_inclusion($node, $niveau) {
        $this->addStat(__FUNCTION__);
        $inclusion = $node->getInclusion();
        $this->affiche($inclusion, $niveau + 1);
    }

    function affiche__interface($node, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($node->getName(), $niveau + 1);
        $this->affiche($node->getBlock(), $niveau + 1);
    }

    function affiche_invert($node, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($node->getExpression(), $niveau + 1);
    }

    function affiche_logique($node, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($node->getDroite(), $niveau + 1);
        $this->affiche($node->getOperateur(), $niveau + 1);
        $this->affiche($node->getGauche(), $niveau + 1);
    }

    function affiche_literals($node, $niveau) {
        $this->addStat(__FUNCTION__);
    }

    function affiche_method($node, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($node->getObject(), $niveau + 1);
        $this->affiche($node->getMethod(), $niveau + 1);
    }

    function affiche_method_static($node, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($node->getClass(), $niveau + 1);
        $this->affiche($node->getMethod(), $niveau + 1);
    }

    function affiche__new($node, $niveau) {
         print str_repeat('  ', $niveau).' new '.$node->getClasse()." ".$node->getArgs()." \n";
    }

    function affiche_noscream($node, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($node->getExpression(), $niveau + 1);
    }

    function affiche_not($node, $niveau) {
        $this->addStat(__FUNCTION__);
         $this->affiche($node->getExpression(), $niveau + 1);
    }

    function affiche_opappend($node, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($node->getVariable(), $niveau + 1);
    }

    function affiche_operation($node, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($node->getDroite(), $niveau + 1);
        $this->affiche($node->getGauche(), $niveau + 1);
    }

    function affiche_parentheses($node, $niveau) {
        $this->addStat(__FUNCTION__);
    }

    function affiche_preplusplus($node, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($node->getOperateur(), $niveau + 1);
        $this->affiche($node->getVariable(), $niveau + 1);
    }
    
    function affiche_property($node, $niveau) {
        $this->addStat(__FUNCTION__);
    }

    function affiche_property_static($node, $niveau) {
        $this->addStat(__FUNCTION__);
    }

    function affiche_postplusplus($node, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($node->getOperateur(), $niveau + 1);
        $this->affiche($node->getVariable(), $niveau + 1);
    }

    function affiche_rawtext($node, $niveau) {
        $this->addStat(__FUNCTION__);
    }

    function affiche_reference($node, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($node->getExpression(), $niveau + 1);
    }

    function affiche__return($node, $niveau) {
        $this->addStat(__FUNCTION__);
        if (!is_null($node->getReturn())) {
            $this->affiche($node->getReturn(), $niveau + 1);
        }
    }

    function affiche_sequence($node, $niveau) {
        $this->addStat(__FUNCTION__);
        $elements = $node->getElements();
        foreach($elements as $id => $e) {
            $this->affiche($e, $niveau + 1);
        }
    }

    function affiche_sign($node, $niveau) {
        $this->addStat(__FUNCTION__);
         $this->affiche($node->getsign(), $niveau + 1);
         $this->affiche($node->getExpression(), $niveau + 1);
    }

    function affiche__static($node, $niveau) {
        $this->addStat(__FUNCTION__);
         $this->affiche($node->getExpression(), $niveau + 1);
    }

    function affiche__switch($node, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($node->getCondition(), $niveau + 1);
        $this->affiche($node->getBlock(), $niveau + 1);
    }

    function affiche_tableau($node, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($node->getVariable(), $niveau + 1);
        $this->affiche($node->getIndex(), $niveau + 1);
    }

    function affiche_token_traite($node, $niveau) {
        $this->addStat(__FUNCTION__);
    }

    function affiche__throw($node, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($node->getException(), $niveau + 1);
    }
    
    function affiche__try($node, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($node->getBlock(), $niveau + 1);
        $elements = $node->getCatch();
        foreach($elements as $id => $e) {
            $this->affiche($e, $niveau + 1);
        }
    }

    function affiche_typehint($node, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($node->getName(), $niveau + 1);
        $this->affiche($node->getType(), $niveau + 1);
    }

    function affiche__var($node, $niveau) {
        $this->addStat(__FUNCTION__);

        $variables = $node->getVariable();
        foreach($variables as $var) {
            $this->affiche($var, $niveau + 1);
        }

        $inits = $node->getInit();
        foreach($inits as $init) {
            if (is_null($init)) {continue;}
            $this->affiche($init, $niveau + 1);
        }
    }

    function affiche_variable($node, $niveau) {
        $this->addStat(__FUNCTION__);
    }

    function affiche__while($node, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($node->getCondition(), $niveau + 1);
        $this->affiche($node->getBlock(), $niveau + 1);
    }

    function affiche__dowhile($node, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($node->getCondition(), $niveau + 1);
        $this->affiche($node->getBlock(), $niveau + 1);
    }

    function affiche_Token($node, $niveau) {
        print $node."".$node->getId()."\n";
        $this->addStat(__FUNCTION__);
    }
}

?>
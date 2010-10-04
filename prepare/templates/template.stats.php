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
    
    function affiche($noeud = null, $niveau = 0) {
        if (is_null($noeud)) {
            $noeud = $this->root;
        }
        
        if (!is_object($noeud)) {
            print "Fatal : attemptint to display a non-object in ".__METHOD__."\n\n";
        }
        $class = get_class($noeud);
        $method = "affiche_$class";

        if (method_exists($this, $method)) {
            $this->$method($noeud, $niveau + 1);
        } else {
            $this->stats['missing'][$method] = 1;
        }
        if (!is_null($noeud->getNext())){
            $this->affiche($noeud->getNext(), $niveau);
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

    function affiche_arginit($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($noeud->getVariable(), $niveau + 1);
        $this->affiche($noeud->getValeur(), $niveau + 1);
    }

    function affiche_arglist($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
        $elements = $noeud->getList();
        if (is_array($elements)) {
            foreach($elements as $id => $e) {
                if (!is_null($e)) {
                    $this->affiche($e, $niveau + 1);
                }
            }
        }
    }

    function affiche_affectation($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($noeud->getDroite(), $niveau + 1);
        $this->affiche($noeud->getOperateur(), $niveau + 1);
        $this->affiche($noeud->getGauche(), $niveau + 1);
    }

    function affiche_block($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
        $elements = $noeud->getList();
        foreach($elements as $id => $e) {
            $this->affiche($e, $niveau + 1);
        }
    }

    function affiche__break($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
    }

    function affiche__case($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($noeud->getComparant(), $niveau + 1);
        $this->affiche($noeud->getBlock(), $niveau + 1);
    }

    function affiche_cast($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($noeud->getExpression(), $niveau + 1);
    }

    function affiche__catch($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($noeud->getException(), $niveau + 1);
        $this->affiche($noeud->getVariable(), $niveau + 1);
        $this->affiche($noeud->getBlock(), $niveau + 1);
    }

    function affiche__class($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
        $extends = $noeud->getExtends();
        if (!is_null($extends)) {
            $this->affiche($extends, $niveau + 1);
        }
        $implements = $noeud->getImplements();
        if (count($implements) > 0) {
            foreach($implements as $i) {
                $this->affiche($i, $niveau + 1);
            }
        }
        $this->affiche($noeud->getBlock(), $niveau + 1);
    }

    function affiche_clevaleur($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($noeud->getCle(), $niveau + 1);
        $this->affiche($noeud->getValeur(), $niveau + 1);
    }

    function affiche__clone($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($noeud->getExpression(), $niveau + 1);
    }

    function affiche_comparaison($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($noeud->getDroite(), $niveau + 1);
        $this->affiche($noeud->getGauche(), $niveau + 1);
    }
    
    function affiche_cdtternaire($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($noeud->getCondition(), $niveau + 1);
        $this->affiche($noeud->getVraie(), $niveau + 1);
        $this->affiche($noeud->getFaux(), $niveau + 1);
    }

    function affiche_codephp($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($noeud->getphp_code(), $niveau + 1);
    }

    function affiche_concatenation($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
        $elements = $noeud->getList();
        foreach($elements as $id => $e) {
            $this->affiche($e, $niveau + 1);
        }
    }

    function affiche__continue($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
    }

    function affiche_constante($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
    }

    function affiche_constante_static($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
         $this->affiche($noeud->getClass(), $niveau + 1);
         $this->affiche($noeud->getConstant(), $niveau + 1);
    }

    function affiche_constante_classe($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
         $this->affiche($noeud->getName(), $niveau + 1);
         $this->affiche($noeud->getConstante(), $niveau + 1);
    }

    function affiche_decalage($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
         $this->affiche($noeud->getDroite(), $niveau + 1);
         $this->affiche($noeud->getOperateur(), $niveau + 1);
         $this->affiche($noeud->getGauche(), $niveau + 1);
    }

    function affiche__declare($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
         $this->affiche($noeud->getTicks(), $niveau + 1);
         $this->affiche($noeud->getEncoding(), $niveau + 1);
         $n = $noeud->getBlock();
         if (!is_null($n)) {
             $this->affiche($n, $niveau + 1);
         }
    }

    function affiche__default($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($noeud->getBlock(), $niveau + 1);
    }

    function affiche__for($noeud, $niveau) {
        $this->addStat(__FUNCTION__);

        $init = $noeud->getInit();
        if (!is_null($init)) {
            $this->affiche($init, $niveau + 1);
        }

        $fin = $noeud->getFin();
        if (!is_null($fin)) {
            $this->affiche($fin, $niveau + 1);
        }

        $increment = $noeud->getIncrement();
        if (!is_null($increment)) {
            $this->affiche($increment, $niveau + 1);
        }
        
        $this->affiche($noeud->getBlock(), $niveau + 1);
    }

    function affiche__foreach($noeud, $niveau) {
        $this->addStat(__FUNCTION__);

        $gets = array('getTableau','getKey','getValue','getBlock');

        foreach($gets as $get) {
            $list = $noeud->$get();
            if (!is_null($list)) {
                $this->affiche($list, $niveau + 1);
            }
        }
    }

  function affiche__function($noeud, $niveau) {
        $this->addStat(__FUNCTION__);

        $this->affiche($noeud->getName(), $niveau + 1);
        $this->affiche($noeud->getArgs(), $niveau + 1);
        $this->affiche($noeud->getBlock(), $niveau + 1);
    }

    function affiche__global($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
        $elements = $noeud->getVariables();
        foreach($elements as $id => $e) {
            $this->affiche($e, $niveau + 1);
        }
    }

    function affiche_functioncall($noeud, $niveau) {
        $this->addStat(__FUNCTION__);

        $args = $noeud->getArgs();
        $this->affiche($args, $niveau + 1);
    }

    function affiche____halt_compiler($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
    }

    function affiche_ifthen($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
        $conditions = $noeud->getCondition();
        $thens = $noeud->getThen();
        foreach($conditions as $id => $condition) {
            $this->affiche($condition, $niveau + 1);
            $this->affiche($thens[$id], $niveau + 1);
        }
        if (!is_null($noeud->getElse())){
            $this->affiche($noeud->getElse(), $niveau + 1);
        }
    }

    function affiche_inclusion($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
        $inclusion = $noeud->getInclusion();
        $this->affiche($inclusion, $niveau + 1);
    }

    function affiche__interface($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($noeud->getName(), $niveau + 1);
        $this->affiche($noeud->getBlock(), $niveau + 1);
    }

    function affiche_invert($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($noeud->getExpression(), $niveau + 1);
    }

    function affiche_logique($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($noeud->getDroite(), $niveau + 1);
        $this->affiche($noeud->getOperateur(), $niveau + 1);
        $this->affiche($noeud->getGauche(), $niveau + 1);
    }

    function affiche_literals($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
    }

    function affiche_method($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($noeud->getObject(), $niveau + 1);
        $this->affiche($noeud->getMethod(), $niveau + 1);
    }

    function affiche_method_static($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($noeud->getClass(), $niveau + 1);
        $this->affiche($noeud->getMethod(), $niveau + 1);
    }

    function affiche__new($noeud, $niveau) {
         print str_repeat('  ', $niveau).' new '.$noeud->getClasse()." ".$noeud->getArgs()." \n";
    }

    function affiche_noscream($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($noeud->getExpression(), $niveau + 1);
    }

    function affiche_not($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
         $this->affiche($noeud->getExpression(), $niveau + 1);
    }

    function affiche_opappend($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($noeud->getVariable(), $niveau + 1);
    }

    function affiche_operation($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($noeud->getDroite(), $niveau + 1);
        $this->affiche($noeud->getGauche(), $niveau + 1);
    }

    function affiche_parentheses($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
    }

    function affiche_preplusplus($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($noeud->getOperateur(), $niveau + 1);
        $this->affiche($noeud->getVariable(), $niveau + 1);
    }
    
    function affiche_property($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
    }

    function affiche_property_static($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
    }

    function affiche_postplusplus($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($noeud->getOperateur(), $niveau + 1);
        $this->affiche($noeud->getVariable(), $niveau + 1);
    }

    function affiche_rawtext($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
    }

    function affiche_reference($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($noeud->getExpression(), $niveau + 1);
    }

    function affiche__return($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
        if (!is_null($noeud->getReturn())) {
            $this->affiche($noeud->getReturn(), $niveau + 1);
        }
    }

    function affiche_sequence($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
        $elements = $noeud->getElements();
        foreach($elements as $id => $e) {
            $this->affiche($e, $niveau + 1);
        }
    }

    function affiche_sign($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
         $this->affiche($noeud->getsign(), $niveau + 1);
         $this->affiche($noeud->getExpression(), $niveau + 1);
    }

    function affiche__static($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
         $this->affiche($noeud->getExpression(), $niveau + 1);
    }

    function affiche__switch($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($noeud->getOperande(), $niveau + 1);
        $this->affiche($noeud->getBlock(), $niveau + 1);
    }

    function affiche_tableau($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($noeud->getVariable(), $niveau + 1);
        $this->affiche($noeud->getIndex(), $niveau + 1);
    }

    function affiche_token_traite($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
    }

    function affiche__throw($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($noeud->getException(), $niveau + 1);
    }
    
    function affiche__try($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($noeud->getBlock(), $niveau + 1);
        $elements = $noeud->getCatch();
        foreach($elements as $id => $e) {
            $this->affiche($e, $niveau + 1);
        }
    }

    function affiche_typehint($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($noeud->getName(), $niveau + 1);
        $this->affiche($noeud->getType(), $niveau + 1);
    }

    function affiche__var($noeud, $niveau) {
        $this->addStat(__FUNCTION__);

        $variables = $noeud->getVariable();
        foreach($variables as $var) {
            $this->affiche($var, $niveau + 1);
        }

        $inits = $noeud->getInit();
        foreach($inits as $init) {
            if (is_null($init)) {continue;}
            $this->affiche($init, $niveau + 1);
        }
    }

    function affiche_variable($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
    }

    function affiche__while($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($noeud->getCondition(), $niveau + 1);
        $this->affiche($noeud->getBlock(), $niveau + 1);
    }

    function affiche__dowhile($noeud, $niveau) {
        $this->addStat(__FUNCTION__);
        $this->affiche($noeud->getCondition(), $niveau + 1);
        $this->affiche($noeud->getBlock(), $niveau + 1);
    }

    function affiche_Token($noeud, $niveau) {
        print $noeud."".$noeud->getId()."\n";
        $this->addStat(__FUNCTION__);
    }
}

?>
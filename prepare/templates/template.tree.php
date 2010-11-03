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

class template_tree extends template {
    protected $root = null;
    
    function __construct($root) {
        parent::__construct();
        
        $this->root = $root;
    }
    
    function save($filename = null) {
        return false;
    }
    
    function affiche($noeud = null, $level = 0, $follow = true) {
        if ($level > 100) {
            print "Fatal : more than 100 level of recursion : aborting\n"; 
            die(__METHOD__."\n");
        }
        if (is_null($noeud)) {
            if ($level == 0) {
                $noeud = $this->root;
            } else {
                print_r(xdebug_get_function_stack());        
                print "Attempting to send null to display.";
                die(__METHOD__."\n");
            }
        }
        
        if (!is_object($noeud)) {
            debug_print_backtrace();
            print "Fatal : attempting to display a non-object in ".__METHOD__."\n\n";
            die(__METHOD__."\n");
        }
        $class = get_class($noeud);
        $method = "affiche_$class";
        
        if (method_exists($this, $method)) {
            $this->$method($noeud, $level + 1);
        } else {
            print "Displaying ".__CLASS__." in '".$method."'\n";
            die(__METHOD__."\n");
        }

        if ($follow == true) {
            $noeuds = array();
            $next = $noeud;
            while($next = $next->getNext()) {
                $noeuds[] = $next;
            }
            
            foreach($noeuds as $n) {
                $this->affiche($n, $level, false);
            }
        }
    }
    
    function affiche_arginit($noeud, $level) {
        print str_repeat('  ', $level)." argument et initialisation \n";
        $this->affiche($noeud->getVariable(), $level + 1);
         $this->affiche($noeud->getValeur(), $level + 1);
    }

    function affiche_arglist($noeud, $level) {
        print str_repeat('  ', $level).get_class($noeud)." \n";
        $elements = $noeud->getList();
        if (count($elements) == 0) {
            print str_repeat('  ', $level)."Liste d'argument vide\n";
        } else {
            foreach($elements as $id => $e) {
                print str_repeat('  ', $level)."$id : \n";
                if (!is_null($e)) {
                    $this->affiche($e, $level + 1);
                }
            }
        }
    }

    function affiche_affectation($noeud, $level) {
        print str_repeat('  ', $level).get_class($noeud)." \n";
        print str_repeat('  ', $level)."droite : \n";
        $this->affiche($noeud->getDroite(), $level + 1);
        print str_repeat('  ', $level).$noeud->getOperateur()." \n";
        print str_repeat('  ', $level)."gauche : \n";
        $this->affiche($noeud->getGauche(), $level + 1);
    }

    function affiche_block($noeud, $level) {
        print str_repeat('  ', $level).get_class($noeud)." \n";
        $elements = $noeud->getList();
        foreach($elements as $id => $e) {
            print str_repeat('  ', $level)."$id : \n";
            $this->affiche($e, $level + 1);
        }
    }

    function affiche__break($noeud, $level) {
         print str_repeat('  ', $level).get_class($noeud)." \n";
         print str_repeat('  ', $level)."Number : \"".$noeud->getLevels()."\"\n";    
    }

    function affiche__case($noeud, $level) {
        print str_repeat('  ', $level).get_class($noeud)." \n";
        $this->affiche($noeud->getComparant(), $level + 1);
        $this->affiche($noeud->getBlock(), $level + 1);
    }

    function affiche_cast($noeud, $level) {
        print str_repeat('  ', $level).get_class($noeud)." (".$noeud->getCast().")".$noeud->getExpression()."\n";
        $this->affiche($noeud->getExpression(), $level + 1);
    }
    

    function affiche__catch($noeud, $level) {
        print str_repeat('  ', $level).get_class($noeud)." (".$noeud->getException().")".$noeud->getVariable()."\n";
         $this->affiche($noeud->getBlock(), $level + 1);
    }

    function affiche__class($noeud, $level) {
        print str_repeat('  ', $level).$noeud->getAbstract().' class '.$noeud->getName();
        $extends = $noeud->getExtends();
        if (!is_null($extends)) {
            print " extends ".$extends;
        }
        $implements = $noeud->getImplements();
        if (count($implements) > 0) {
            print " implements ".join(', ', $implements);
        }
        print "\n";
        $this->affiche($noeud->getBlock(), $level + 1);
    }

    function affiche__clone($noeud, $level) {
        print str_repeat('  ', $level).get_class($noeud)." \n";
        $this->affiche($noeud->getExpression(), $level + 1);
    }

    function affiche_keyvalue($noeud, $level) {
        print str_repeat('  ', $level).$noeud->getKey()." => ".$noeud->getValue()."\n";
        $this->affiche($noeud->getKey(), $level + 1);
        $this->affiche($noeud->getValue(), $level + 1);
    }

    function affiche_comparison($noeud, $level) {
         print str_repeat('  ', $level).get_class($noeud)." \n";
         print str_repeat('  ', $level)."droite : \n";
         $this->affiche($noeud->getDroite(), $level + 1);
         print str_repeat('  ', $level)."operateur : ".$noeud->getOperateur()."\n";
         print str_repeat('  ', $level)."gauche : \n";
         $this->affiche($noeud->getGauche(), $level + 1);
    }

    function affiche__continue($noeud, $level) {
         print str_repeat('  ', $level).get_class($noeud).$noeud->getLevels()." \n";
    }
    
    function affiche_cdtternaire($noeud, $level) {
        print str_repeat('  ', $level).get_class($noeud)." ".$noeud->getCode()."\n";
        print str_repeat('  ', $level).$noeud->getCondition();
        print " ? ".$noeud->getVraie()." : ".$noeud->getFaux()."\n";
        $this->affiche($noeud->getCondition(), $level + 1);
        $this->affiche($noeud->getVraie(), $level + 1);
        $this->affiche($noeud->getFaux(), $level + 1);
    }

    function affiche_codephp($noeud, $level) {
        print str_repeat('  ', $level).get_class($noeud)." ".$noeud->getCode()."\n";
        print str_repeat('  ', $level)."code : \n";
        $this->affiche($noeud->getphp_code(), $level + 1);
    }

    function affiche_concatenation($noeud, $level) {
        print str_repeat('  ', $level).get_class($noeud)." \n";
        $elements = $noeud->getList();
        foreach($elements as $id => $e) {
            print str_repeat('  ', $level)."$id : \n";
            $this->affiche($e, $level + 1);
        }
    }

    function affiche_constante($noeud, $level) {
         print str_repeat('  ', $level).get_class($noeud)." (";
         print str_repeat('  ', $level)."".$noeud->getName()." )\n";    
    }

    function affiche_constante_static($noeud, $level) {
         print str_repeat('  ', $level).get_class($noeud)." (";
         print str_repeat('  ', $level)."".$noeud->getClass()."::".$noeud->getConstant()." )\n";    
    }

    function affiche_constante_classe($noeud, $level) {
         print str_repeat('  ', $level).get_class($noeud)." ";
         print str_repeat('  ', $level)."".$noeud->getName()." = ".$noeud->getConstante()." \n";    
    }

    function affiche_decalage($noeud, $level) {
         print str_repeat('  ', $level).get_class($noeud)." \n";
         print str_repeat('  ', $level)."droite : \n";
         $this->affiche($noeud->getDroite(), $level + 1);
         print str_repeat('  ', $level)."operation : ".$noeud->getOperateur()."\n";
         print str_repeat('  ', $level)."gauche : \n";
         $this->affiche($noeud->getGauche(), $level + 1);
    }

    function affiche__declare($noeud, $level) {
         print str_repeat('  ', $level).get_class($noeud)." \n";
         print str_repeat('  ', $level + 1).' ticks = '.$noeud->getTicks()."\n";
         print str_repeat('  ', $level + 1).' encoding = '.$noeud->getEncoding()."\n";
         $n = $noeud->getBlock();
         if (!is_null($n)) {
             $this->affiche($n, $level + 1);
         }
    }
    
    function affiche__default($noeud, $level) {
        print str_repeat('  ', $level).get_class($noeud)." \n";
        $this->affiche($noeud->getBlock(), $level + 1);
    }

    function affiche__for($noeud, $level) {
        print str_repeat('  ', $level).get_class($noeud)." \n";
        print str_repeat('  ', $level)."  Init : ".$noeud->getInit().";\n";
        print str_repeat('  ', $level)."  Fin  : ".$noeud->getFin().";\n";
        print str_repeat('  ', $level)."  Incr : ".$noeud->getIncrement().";\n";
        $this->affiche($noeud->getBlock(), $level + 1);
    }

    function affiche__foreach($noeud, $level) {
        print str_repeat('  ', $level).get_class($noeud)." (".$noeud->getTableau()." as ".$noeud->getKey()." => ".$noeud->getValue().")\n";
         $this->affiche($noeud->getBlock(), $level + 1);
    }

    function affiche__function($noeud, $level) {
        print str_repeat('  ', $level).$noeud->getVisibility().$noeud->getAbstract().$noeud->getStatic()."function ".$noeud->getName()." ".$noeud->getArgs()."\n";
        $this->affiche($noeud->getBlock(), $level + 1);
    }

    function affiche_functioncall($noeud, $level) {
        print str_repeat('  ', $level).get_class($noeud)." ".$noeud->getCode()."\n";
        print str_repeat('  ', $level)."function call : ".$noeud->getFunction()->getCode().": \n";

        $args = $noeud->getArgs();
        $this->affiche($args, $level + 1);
    }

    function affiche__global($noeud, $level) {
        print str_repeat('  ', $level).get_class($noeud)." \n";
        $elements = $noeud->getVariables();
        foreach($elements as $id => $e) {
            print str_repeat('  ', $level)."$id : \n";
            $this->affiche($e, $level + 1);
        }
    }

    function affiche____halt_compiler($noeud, $level) {
        print str_repeat('  ', $level).get_class($noeud)." \n";
    }

    function affiche_ifthen($noeud, $level) {
        print str_repeat('  ', $level).get_class($noeud)." ".$noeud->getCode()."\n";
        $conditions = $noeud->getCondition();
        $thens = $noeud->getThen();
        foreach($conditions as $id => $condition) {
            print str_repeat('  ', $level)."Condition $id) \n";
            $this->affiche($condition, $level + 1);
            $this->affiche($thens[$id], $level + 1);
        }
        if (!is_null($noeud->getElse())){
            print str_repeat('  ', $level)." else \n";
            $this->affiche($noeud->getElse(), $level + 1);
        }
    }

    function affiche_inclusion($noeud, $level) {
        print str_repeat('  ', $level).get_class($noeud)." ".$noeud->getCode()."\n";

        $inclusion = $noeud->getInclusion();
        $this->affiche($inclusion, $level + 1);
    }

    function affiche__interface($noeud, $level) {
        print str_repeat('  ', $level).get_class($noeud)." ".$noeud->getName()."\n";
        $e = $noeud->getExtends();
        if (count($e) > 0) {
            print str_repeat('  ', $level).' extends '.join(', ', $e)."\n";
        }
        $this->affiche($noeud->getBlock(), $level + 1);
    }

    function affiche_invert($noeud, $level) {
        print str_repeat('  ', $level).get_class($noeud)." ~\n";
        $this->affiche($noeud->getExpression(), $level + 1);
    }

    function affiche_logique($noeud, $level) {
         print str_repeat('  ', $level).get_class($noeud)." \n";
         print str_repeat('  ', $level)."droite : \n";
         $this->affiche($noeud->getDroite(), $level + 1);
         print str_repeat('  ', $level)."operateur : ".$noeud->getOperateur()."\n";
         print str_repeat('  ', $level)."gauche : \n";
         $this->affiche($noeud->getGauche(), $level + 1);
    }

    function affiche_not($noeud, $level) {
        print str_repeat('  ', $level).get_class($noeud)." ".$noeud->getCode()."\n";
         $this->affiche($noeud->getExpression(), $level + 1);
    }

    function affiche_literals($noeud, $level) {
        print str_repeat('  ', $level).get_class($noeud)." ".$noeud->getLiteral()."\n";
    }

    function affiche_method($noeud, $level) {
        print str_repeat('  ', $level).get_class($noeud)." ".$noeud->getObject()."\n";
        $method = $noeud->getMethod();
        print str_repeat('  ', $level)."method call : ".$method.": \n";
        $this->affiche($method, $level + 1);
    }

    function affiche_method_static($noeud, $level) {
        print str_repeat('  ', $level).get_class($noeud)." \n";
        $method = $noeud->getMethod();
        print str_repeat('  ', $level).$noeud->getClass()."::".$method.": \n";
        $this->affiche($method, $level + 1);
    }

    function affiche__new($noeud, $level) {
         print str_repeat('  ', $level).' new '.$noeud->getClasse()." ".$noeud->getArgs()." \n";
    }
    
    function affiche_noscream($noeud, $level) {
        print str_repeat('  ', $level).get_class($noeud)." @\n";
        $this->affiche($noeud->getExpression(), $level + 1);
    }

    function affiche_opappend($noeud, $level) {
        print str_repeat('  ', $level).$noeud->getVariable()."[]\n";
         $this->affiche($noeud->getVariable(), $level + 1);
    }

    function affiche_operation($noeud, $level) {
         print str_repeat('  ', $level).get_class($noeud)." \n";
         print str_repeat('  ', $level)."droite : \n";
         $this->affiche($noeud->getDroite(), $level + 1);
         print str_repeat('  ', $level)."operation : ".$noeud->getOperation()."\n";
         print str_repeat('  ', $level)."gauche : \n";
         $this->affiche($noeud->getGauche(), $level + 1);
    }

    function affiche_parentheses($noeud, $level) {
         print str_repeat('  ', $level).get_class($noeud)." \n";
         print str_repeat('  ', $level)."( \"".$noeud->getContenu()."\" )\n";    
    }

    function affiche_preplusplus($noeud, $level) {
         print str_repeat('  ', $level).$noeud->getOperateur().$noeud->getVariable()." \n";
         $this->affiche($noeud->getVariable(), $level + 1);
    }

    function affiche_postplusplus($noeud, $level) {
         print str_repeat('  ', $level).$noeud->getVariable().$noeud->getOperateur()." \n";
         $this->affiche($noeud->getVariable(), $level + 1);
    }

    function affiche_property($noeud, $level) {
        print str_repeat('  ', $level).get_class($noeud)." ".$noeud->getObject()."".$noeud->getProperty()."->\n";
    }

    function affiche_property_static($noeud, $level) {
        print str_repeat('  ', $level).get_class($noeud)." ".$noeud->getClass()."::".$noeud->getProperty()."->\n";
    }

    function affiche_rawtext($noeud, $level) {
         print str_repeat('  ', $level).get_class($noeud)." \n";
         print str_repeat('  ', $level)."Texte : \"".$noeud->getText()."\"\n";    
    }

    function affiche_reference($noeud, $level) {
        print str_repeat('  ', $level).get_class($noeud)." &\n";
        $this->affiche($noeud->getExpression(), $level + 1);
    }

    function affiche__return($noeud, $level) {
        print str_repeat('  ', $level).get_class($noeud)." \n";
        print str_repeat('  ', $level)."return : \"".$noeud->getReturn()."\"\n";    
    }

    function affiche_sequence($noeud, $level) {
        print str_repeat('  ', $level).get_class($noeud)." \n";
        $elements = $noeud->getElements();
        foreach($elements as $id => $e) {
            print str_repeat('  ', $level)."$id : \n";
            $this->affiche($e, $level + 1);
        }
    }

    function affiche_shell($noeud, $level) {
        print str_repeat('  ', $level).get_class($noeud)." \n";
        $elements = $noeud->getExpression();
        foreach($elements as $id => $e) {
            print str_repeat('  ', $level)."$id : \n";
            $this->affiche($e, $level + 1);
        }
    }

    function affiche_sign($noeud, $level) {
        print str_repeat('  ', $level).get_class($noeud)." ".$noeud->getsign().$noeud->getExpression()."\n";
        $this->affiche($noeud->getExpression(), $level + 1);
    }

    function affiche__static($noeud, $level) {
         print str_repeat('  ', $level).get_class($noeud)." \n";
         $this->affiche($noeud->getExpression(), $level + 1);
    }

    function affiche__switch($noeud, $level) {
        print str_repeat('  ', $level).get_class($noeud)." \n";
        $this->affiche($noeud->getOperande(), $level + 1);
        $this->affiche($noeud->getBlock(), $level + 1);
    }

    function affiche_tableau($noeud, $level) {
        print str_repeat('  ', $level).get_class($noeud)."\n";
        $this->affiche($noeud->getVariable(), $level + 1);
        $this->affiche($noeud->getIndex(), $level + 1);
    }

    function affiche__throw($noeud, $level) {
        print str_repeat('  ', $level).get_class($noeud)." \n";
        $this->affiche($noeud->getException(), $level + 1);
    }

    function affiche_token_traite($noeud, $level) {
        print get_class($noeud);
    
        print str_repeat('  ', $level).$noeud->getCode()." \n";
    }

    function affiche_typehint($noeud, $level) {
        print str_repeat('  ', $level).get_class($noeud)."\n";
        $this->affiche($noeud->getType(), $level + 1);
        $this->affiche($noeud->getName(), $level + 1);
    }

    function affiche__try($noeud, $level) {
        print str_repeat('  ', $level).get_class($noeud)." \n";
        $this->affiche($noeud->getBlock(), $level + 1);
        $elements = $noeud->getCatch();
        foreach($elements as $id => $e) {
            print str_repeat('  ', $level)."$id : \n";
            $this->affiche($e, $level + 1);
        }
    }

    function affiche__var($noeud, $level) {
        print str_repeat('  ', $level)." ".$noeud->getVisibility().$noeud->getStatic();
        
        $vars = $noeud->getVariable();
        $inits = $noeud->getInit();
        $r = array();
        foreach($vars as $id => $var) {
            if (isset($inits[$id])) {
                $r[] = "$var = {$inits[$id]}";
            } else {
                $r[] = "$var";
            }
        }
        
        print join(', ', $r)."\n";
    }

    function affiche_variable($noeud, $level) {
        print str_repeat('  ', $level).get_class($noeud)." ".$noeud->getName()."\n";
    }

    function affiche__while($noeud, $level) {
        print str_repeat('  ', $level).get_class($noeud)." \n";
        $this->affiche($noeud->getCondition(), $level + 1);
        $this->affiche($noeud->getBlock(), $level + 1);
    }

    function affiche__dowhile($noeud, $level) {
        print str_repeat('  ', $level).get_class($noeud)." \n";
        $this->affiche($noeud->getBlock(), $level + 1);
        $this->affiche($noeud->getCondition(), $level + 1);
    }
    
    function affiche_Token($noeud, $level) {
        print str_repeat('  ', $level).get_class($noeud)." ".$noeud->getCode()." (default display)\n";
    }

}

?>
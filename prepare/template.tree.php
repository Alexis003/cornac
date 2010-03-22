<?php

class template_tree extends template {
    protected $root = null;
    
    function __construct($root) {
        parent::__construct();
        
        $this->root = $root;
    }
    
    function save($filename = null) {
        return false;
    }
    
    function affiche($noeud = null, $niveau = 0) {
        if ($niveau > 100) {
            print "Attention : plus de 100 niveau de récursion (annulation)\n"; die();
        }
        if (is_null($noeud)) {
            $noeud = $this->root;
        }
        
        if (!is_object($noeud)) {
            debug_print_backtrace();
            print "Attention, $noeud n'est pas un objet\n";
            die();
        }
        $class = get_class($noeud);
        $method = "affiche_$class";
        
        if (method_exists($this, $method)) {
            $this->$method($noeud, $niveau + 1);
        } else {
            print "Affichage ".__CLASS__." de '".$method."'\n";die;
        }
        if (!is_null($noeud->getNext())){
            $this->affiche($noeud->getNext(), $niveau);
        }
    }

    function affiche_arginit($noeud, $niveau) {
        print str_repeat('  ', $niveau)." argument et initialisation \n";
        $this->affiche($noeud->getVariable(), $niveau + 1);
         $this->affiche($noeud->getValeur(), $niveau + 1);
    }

    function affiche_arglist($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
        $elements = $noeud->getList();
        if (count($elements) == 0) {
            print str_repeat('  ', $niveau)."Liste d'argument vide\n";
        } else {
            foreach($elements as $id => $e) {
                print str_repeat('  ', $niveau)."$id : \n";
                if (!is_null($e)) {
                    $this->affiche($e, $niveau + 1);
                }
            }
        }
    }

    function affiche_affectation($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
        print str_repeat('  ', $niveau)."droite : \n";
        $this->affiche($noeud->getDroite(), $niveau + 1);
        print str_repeat('  ', $niveau).$noeud->getOperateur()." \n";
        print str_repeat('  ', $niveau)."gauche : \n";
        $this->affiche($noeud->getGauche(), $niveau + 1);
    }

    function affiche_block($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
        $elements = $noeud->getList();
        foreach($elements as $id => $e) {
            print str_repeat('  ', $niveau)."$id : \n";
            $this->affiche($e, $niveau + 1);
        }
    }

    function affiche__break($noeud, $niveau) {
         print str_repeat('  ', $niveau).get_class($noeud)." \n";
         print str_repeat('  ', $niveau)."Nombre : \"".$noeud->getNiveaux()."\"\n";    
    }

    function affiche__case($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
        $this->affiche($noeud->getComparant(), $niveau + 1);
        $this->affiche($noeud->getBlock(), $niveau + 1);
    }

    function affiche_cast($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." (".$noeud->getCast().")".$noeud->getExpression()."\n";
        $this->affiche($noeud->getExpression(), $niveau + 1);
    }
    

    function affiche__catch($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." (".$noeud->getException().")".$noeud->getVariable()."\n";
         $this->affiche($noeud->getBlock(), $niveau + 1);
    }

    function affiche__class($noeud, $niveau) {
        print str_repeat('  ', $niveau).$noeud->getAbstract().' class '.$noeud->getNom();
        $extends = $noeud->getExtends();
        if (!is_null($extends)) {
            print " extends ".$extends;
        }
        $implements = $noeud->getImplements();
        if (count($implements) > 0) {
            print " implements ".join(', ', $implements);
        }
        print "\n";
        $this->affiche($noeud->getBlock(), $niveau + 1);
    }

    function affiche__clone($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
        $this->affiche($noeud->getExpression(), $niveau + 1);
    }

    function affiche_clevaleur($noeud, $niveau) {
        print str_repeat('  ', $niveau).$noeud->getCle()." => ".$noeud->getValeur()."\n";
        $this->affiche($noeud->getCle(), $niveau + 1);
        $this->affiche($noeud->getValeur(), $niveau + 1);
    }

    function affiche_comparaison($noeud, $niveau) {
         print str_repeat('  ', $niveau).get_class($noeud)." \n";
         print str_repeat('  ', $niveau)."droite : \n";
         $this->affiche($noeud->getDroite(), $niveau + 1);
         print str_repeat('  ', $niveau)."operateur : ".$noeud->getOperateur()."\n";
         print str_repeat('  ', $niveau)."gauche : \n";
         $this->affiche($noeud->getGauche(), $niveau + 1);
    }

    function affiche__continue($noeud, $niveau) {
         print str_repeat('  ', $niveau).get_class($noeud).$noeud->getNiveaux()." \n";
    }
    
    function affiche_cdtternaire($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." ".$noeud->getCode()."\n";
        print str_repeat('  ', $niveau).$noeud->getCondition();
        print " ? ".$noeud->getVraie()." : ".$noeud->getFaux()."\n";
        $this->affiche($noeud->getCondition(), $niveau + 1);
        $this->affiche($noeud->getVraie(), $niveau + 1);
        $this->affiche($noeud->getFaux(), $niveau + 1);
    }

    function affiche_codephp($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." ".$noeud->getCode()."\n";
        print str_repeat('  ', $niveau)."code : \n";
        $this->affiche($noeud->getphp_code(), $niveau + 1);
    }

    function affiche_concatenation($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
        $elements = $noeud->getList();
        foreach($elements as $id => $e) {
            print str_repeat('  ', $niveau)."$id : \n";
            $this->affiche($e, $niveau + 1);
        }
    }

    function affiche_constante($noeud, $niveau) {
         print str_repeat('  ', $niveau).get_class($noeud)." (";
         print str_repeat('  ', $niveau)."".$noeud->getName()." )\n";    
    }

    function affiche_constante_static($noeud, $niveau) {
         print str_repeat('  ', $niveau).get_class($noeud)." (";
         print str_repeat('  ', $niveau)."".$noeud->getClass()."::".$noeud->getConstant()." )\n";    
    }

    function affiche_constante_classe($noeud, $niveau) {
         print str_repeat('  ', $niveau).get_class($noeud)." ";
         print str_repeat('  ', $niveau)."".$noeud->getName()." = ".$noeud->getConstante()." \n";    
    }

    function affiche_decalage($noeud, $niveau) {
         print str_repeat('  ', $niveau).get_class($noeud)." \n";
         print str_repeat('  ', $niveau)."droite : \n";
         $this->affiche($noeud->getDroite(), $niveau + 1);
         print str_repeat('  ', $niveau)."operation : ".$noeud->getOperateur()."\n";
         print str_repeat('  ', $niveau)."gauche : \n";
         $this->affiche($noeud->getGauche(), $niveau + 1);
    }

    function affiche__declare($noeud, $niveau) {
         print str_repeat('  ', $niveau).get_class($noeud)." \n";
         print str_repeat('  ', $niveau + 1).' ticks = '.$noeud->getTicks()."\n";
         print str_repeat('  ', $niveau + 1).' encoding = '.$noeud->getEncoding()."\n";
         $n = $noeud->getBlock();
         if (!is_null($n)) {
             $this->affiche($n, $niveau + 1);
         }
    }
    
    function affiche__default($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
        $this->affiche($noeud->getBlock(), $niveau + 1);
    }

    function affiche__for($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
        print str_repeat('  ', $niveau)."  Init : ".$noeud->getInit().";\n";
        print str_repeat('  ', $niveau)."  Fin  : ".$noeud->getFin().";\n";
        print str_repeat('  ', $niveau)."  Incr : ".$noeud->getIncrement().";\n";
        $this->affiche($noeud->getBlock(), $niveau + 1);
    }

    function affiche__foreach($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." (".$noeud->getTableau()." as ".$noeud->getKey()." => ".$noeud->getValue().")\n";
         $this->affiche($noeud->getBlock(), $niveau + 1);
    }

    function affiche__function($noeud, $niveau) {
        print str_repeat('  ', $niveau).$noeud->getVisibility().$noeud->getAbstract().$noeud->getStatic()."function ".$noeud->getName()." ".$noeud->getArgs()."\n";
        $this->affiche($noeud->getBlock(), $niveau + 1);
    }

    function affiche_functioncall($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." ".$noeud->getCode()."\n";
        print str_repeat('  ', $niveau)."appel de fonction : ".$noeud->getFunction()->getCode().": \n";

        $args = $noeud->getArgs();
        $this->affiche($args, $niveau + 1);
    }

    function affiche__global($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
        $elements = $noeud->getVariables();
        foreach($elements as $id => $e) {
            print str_repeat('  ', $niveau)."$id : \n";
            $this->affiche($e, $niveau + 1);
        }
    }

    function affiche____halt_compiler($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
    }

    function affiche_ifthen($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." ".$noeud->getCode()."\n";
        $conditions = $noeud->getCondition();
        $thens = $noeud->getThen();
        foreach($conditions as $id => $condition) {
            print str_repeat('  ', $niveau)."Condition $id) \n";
            $this->affiche($condition, $niveau + 1);
            $this->affiche($thens[$id], $niveau + 1);
        }
        if (!is_null($noeud->getElse())){
            print str_repeat('  ', $niveau)." else \n";
            $this->affiche($noeud->getElse(), $niveau + 1);
        }
    }

    function affiche_inclusion($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." ".$noeud->getCode()."\n";

        $inclusion = $noeud->getInclusion();
        $this->affiche($inclusion, $niveau + 1);
    }

    function affiche__interface($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." ".$noeud->getName()."\n";
        $e = $noeud->getExtends();
        if (count($e) > 0) {
            print str_repeat('  ', $niveau).' extends '.join(', ', $e)."\n";
        }
        $this->affiche($noeud->getBlock(), $niveau + 1);
    }

    function affiche_invert($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." ~\n";
        $this->affiche($noeud->getExpression(), $niveau + 1);
    }

    function affiche_logique($noeud, $niveau) {
         print str_repeat('  ', $niveau).get_class($noeud)." \n";
         print str_repeat('  ', $niveau)."droite : \n";
         $this->affiche($noeud->getDroite(), $niveau + 1);
         print str_repeat('  ', $niveau)."operateur : ".$noeud->getOperateur()."\n";
         print str_repeat('  ', $niveau)."gauche : \n";
         $this->affiche($noeud->getGauche(), $niveau + 1);
    }

    function affiche_not($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." ".$noeud->getCode()."\n";
         $this->affiche($noeud->getExpression(), $niveau + 1);
    }

    function affiche_literals($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." ".$noeud->getLiteral()."\n";
    }

    function affiche_method($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." ".$noeud->getObject()."\n";
        $method = $noeud->getMethod();
        print str_repeat('  ', $niveau)."appel de methode : ".$method.": \n";
        $this->affiche($method, $niveau + 1);
    }

    function affiche_method_static($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
        $method = $noeud->getMethod();
        print str_repeat('  ', $niveau).$noeud->getClass()."::".$method.": \n";
        $this->affiche($method, $niveau + 1);
    }

    function affiche__new($noeud, $niveau) {
         print str_repeat('  ', $niveau).' new '.$noeud->getClasse()." ".$noeud->getArgs()." \n";
    }
    
    function affiche_noscream($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." @\n";
        $this->affiche($noeud->getExpression(), $niveau + 1);
    }

    function affiche_opappend($noeud, $niveau) {
        print str_repeat('  ', $niveau).$noeud->getVariable()."[]\n";
         $this->affiche($noeud->getVariable(), $niveau + 1);
    }

    function affiche_operation($noeud, $niveau) {
         print str_repeat('  ', $niveau).get_class($noeud)." \n";
         print str_repeat('  ', $niveau)."droite : \n";
         $this->affiche($noeud->getDroite(), $niveau + 1);
         print str_repeat('  ', $niveau)."operation : ".$noeud->getOperation()."\n";
         print str_repeat('  ', $niveau)."gauche : \n";
         $this->affiche($noeud->getGauche(), $niveau + 1);
    }

    function affiche_parentheses($noeud, $niveau) {
         print str_repeat('  ', $niveau).get_class($noeud)." \n";
         print str_repeat('  ', $niveau)."( \"".$noeud->getContenu()."\" )\n";    
    }

    function affiche_preplusplus($noeud, $niveau) {
         print str_repeat('  ', $niveau).$noeud->getOperateur().$noeud->getVariable()." \n";
         $this->affiche($noeud->getVariable(), $niveau + 1);
    }

    function affiche_postplusplus($noeud, $niveau) {
         print str_repeat('  ', $niveau).$noeud->getVariable().$noeud->getOperateur()." \n";
         $this->affiche($noeud->getVariable(), $niveau + 1);
    }

    function affiche_property($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." ".$noeud->getObject()."".$noeud->getProperty()."->\n";
    }

    function affiche_property_static($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." ".$noeud->getClass()."::".$noeud->getProperty()."->\n";
    }

    function affiche_rawtext($noeud, $niveau) {
         print str_repeat('  ', $niveau).get_class($noeud)." \n";
         print str_repeat('  ', $niveau)."Texte : \"".$noeud->getText()."\"\n";    
    }

    function affiche_reference($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." &\n";
        $this->affiche($noeud->getExpression(), $niveau + 1);
    }

    function affiche__return($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
        print str_repeat('  ', $niveau)."return : \"".$noeud->getRetour()."\"\n";    
    }

    function affiche_sequence($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
        $elements = $noeud->getElements();
        foreach($elements as $id => $e) {
            print str_repeat('  ', $niveau)."$id : \n";
            $this->affiche($e, $niveau + 1);
        }
    }

    function affiche_shell($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
        $elements = $noeud->getExpression();
        foreach($elements as $id => $e) {
            print str_repeat('  ', $niveau)."$id : \n";
            $this->affiche($e, $niveau + 1);
        }
    }

    function affiche_signe($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." ".$noeud->getSigne().$noeud->getExpression()."\n";
//         $this->affiche($noeud->getSigne(), $niveau + 1);
         $this->affiche($noeud->getExpression(), $niveau + 1);
    }

    function affiche__static($noeud, $niveau) {
         print str_repeat('  ', $niveau).get_class($noeud)." \n";
         $this->affiche($noeud->getExpression(), $niveau + 1);
    }

    function affiche__switch($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
        $this->affiche($noeud->getOperande(), $niveau + 1);
        $this->affiche($noeud->getBlock(), $niveau + 1);
    }

    function affiche_tableau($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)."\n";
        $this->affiche($noeud->getVariable(), $niveau + 1);
        $this->affiche($noeud->getIndex(), $niveau + 1);
    }

    function affiche__throw($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
        $this->affiche($noeud->getException(), $niveau + 1);
    }

    function affiche_token_traite($noeud, $niveau) {
        print str_repeat('  ', $niveau).$noeud->getCode()." \n";
    }

    function affiche_typehint($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)."\n";
        $this->affiche($noeud->getType(), $niveau + 1);
        $this->affiche($noeud->getNom(), $niveau + 1);
    }

    function affiche__try($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
        $this->affiche($noeud->getBlock(), $niveau + 1);
        $elements = $noeud->getCatch();
        foreach($elements as $id => $e) {
            print str_repeat('  ', $niveau)."$id : \n";
            $this->affiche($e, $niveau + 1);
        }
    }

    function affiche__var($noeud, $niveau) {
        print str_repeat('  ', $niveau)." ".$noeud->getVisibility().$noeud->getStatic();
        
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

    function affiche_variable($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." ".$noeud->getNom()."\n";
    }

    function affiche__while($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
        $this->affiche($noeud->getCondition(), $niveau + 1);
        $this->affiche($noeud->getBlock(), $niveau + 1);
    }

    function affiche__dowhile($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
        $this->affiche($noeud->getCondition(), $niveau + 1);
        $this->affiche($noeud->getBlock(), $niveau + 1);
    }
    
    function affiche_Token($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." ".$noeud->getCode()." ( Affichage par défaut)\n";
    }

}

?>
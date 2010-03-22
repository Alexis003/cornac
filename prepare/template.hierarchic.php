<?php

class template_hierarchic extends template {
    protected $root = null;
    
    function __construct($root) {
        parent::__construct();
        
        $this->root = $root;
    }
    
    function affiche($noeud = null, $niveau = 0) {
        if (is_null($noeud)) {
            $noeud = $this->root;
        }
        
        if (!is_object($noeud)) {
            debug_print_backtrace();
            die();
        }
        $class = get_class($noeud);
        $method = "affiche_$class";

/*        
        if (method_exists($this, $method)) {
            $this->$method($noeud, $niveau + 1);
        } else {
            print "Affichage tree de '".$method."'\n";die;
        }
        */
        $this->$method($noeud, $niveau + 1);
        if (!is_null($noeud->getNext())){
            $this->affiche($noeud->getNext(), $niveau);
        }
        
        
    }
    
    function affiche_arglist($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
        $elements = $noeud->getList();
        foreach($elements as $id => $e) {
            $this->affiche($e, $niveau + 1);
        }
    }

    function affiche_affectation($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
        $this->affiche($noeud->getDroite(), $niveau + 1);
        $this->affiche($noeud->getGauche(), $niveau + 1);
    }
    
    function affiche_cdtternaire($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
        $this->affiche($noeud->getCondition(), $niveau + 1);
        $this->affiche($noeud->getVraie(), $niveau + 1);
        $this->affiche($noeud->getFaux(), $niveau + 1);
    }

    function affiche_codephp($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
        $this->affiche($noeud->getphp_code(), $niveau + 1);
    }

    function affiche_concatenation($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
        $elements = $noeud->getList();
        foreach($elements as $id => $e) {
            $this->affiche($e, $niveau + 1);
        }
    }

    function affiche_constante($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
    }

    function affiche_functioncall($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";

        $args = $noeud->getArgs();
        $this->affiche($args, $niveau + 1);
    }

    function affiche_inclusion($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
        $inclusion = $noeud->getInclusion();
        $this->affiche($inclusion, $niveau + 1);
    }

    function affiche_literals($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
    }

    function affiche_operation($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
        $this->affiche($noeud->getDroite(), $niveau + 1);
        $this->affiche($noeud->getGauche(), $niveau + 1);
    }

    function affiche_parentheses($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
    }

    function affiche_rawtext($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
    }

    function affiche_sequence($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
        $elements = $noeud->getElements();
        foreach($elements as $id => $e) {
            $this->affiche($e, $niveau + 1);
        }
    }

    function affiche_tableau($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
        $this->affiche($noeud->getVariable(), $niveau + 1);
        $this->affiche($noeud->getIndex(), $niveau + 1);
    }

    function affiche_variable($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." \n";
    }

    function affiche_Token($noeud, $niveau) {
        print str_repeat('  ', $niveau).get_class($noeud)." ".$noeud->getCode()." ( Affichage par défaut)\n";
    }

}

?>
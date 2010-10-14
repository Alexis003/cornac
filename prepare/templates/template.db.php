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

class template_db extends template {
    protected $line = 0;
    protected $scope = 'global';
    protected $class = '';
    
    protected $table = 'tokens';
    protected $tags = array();
    
    function __construct($root, $fichier = null) {
        parent::__construct();
    }
    
    function save($filename = null) {
        print "Saved in database\n";
    }
    
    function affiche($node = null, $niveau = 0) {
    // @question why not move all this into a first template that would check, so we can avoid those tests/die here? 
        if ($niveau > 200) {
            print_r(xdebug_get_function_stack());        
            print "Attention : over 200 levels of recursion. Aborting.\n"; 
            die(__METHOD__."\n");
        }
        if (is_null($node)) {
            if ($niveau == 0) {
                $node = $this->root;
            } else {
                print_r(xdebug_get_function_stack());        
                print "Attempting to send null to display.";
                die(__METHOD__."\n");
            }
        }
        
        if (!is_object($node)) {
            print_r(xdebug_get_function_stack());        
            print "Attention, node $node is not an (".gettype($node).")\n";
            die(__METHOD__."\n");
        }
        $class = get_class($node);
        $method = "affiche_$class";
        
        if (method_exists($this, $method)) {
            $return = $this->$method($node, $niveau);
        } else {
            print "Displaying ".__CLASS__." for '".$method."'. Aborting\n";
            die(__METHOD__."\n");
        }
        if (!is_null($node->getNext())){
            $this->affiche($node->getNext(), $niveau);
        }

        return $return;
    }
    
////////////////////////////////////////////////////////////////////////
// @section database functions
////////////////////////////////////////////////////////////////////////

    private static $ids = 0;
    
    function getNextId() {
        return $this->ids++;
    }

    private static $intervallaire = 0;
    
    function getIntervalleId() {
        return $this->intervallaire++;
    }

    function savenode($node, $niveau) {
        global $file;
        
        if (($node->getline() + 0) > 0) {
            $this->line = $node->getline() + 0;
        } 
        
        $requete = "INSERT INTO {$this->table} VALUES 
            (NULL ,
             '".$node->myDroite."',
             '".$node->myGauche."',
             '".get_class($node)."',
             ".$this->database->quote($node->getCode()).",
             '$file',
             ". $node->getLine() .",
             '". $this->scope ."',
             '". $this->class ."',
             '". $niveau ."'
             )";

        $this->database->query($requete);
        if ($this->database->errorCode() != 0) {
            print $requete."\n";
            print_r($this->database->errorInfo());
            die(__METHOD__."\n");
        }
        
        $return = $this->database->lastinsertid();
        
        if (is_array($this->tags) && count($this->tags) > 0) {
            foreach($this->tags as $label => $tokens) {
                foreach($tokens as $token) {
                    $requete = "INSERT INTO {$this->table_tags} VALUES 
                    ($return ,
                     '".$token."',
                     '".$label."')";
    
                    $this->database->query($requete);
                    if ($this->database->errorCode() != 0) {
                        print $requete."\n";
                        print_r($this->database->errorInfo());
                        die(__METHOD__."\n");
                    }
                }
            }
        }
        
        $this->tags = array();
        $node->database_id = $return;
        
        return $return;
    }

////////////////////////////////////////////////////////////////////////
// @section database functions
////////////////////////////////////////////////////////////////////////
    function affiche_token_traite($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);        
    }

    function affiche_affectation($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->setCode('');

        $tags = array();
        $tags['left'][] = $this->affiche($node->getDroite(), $niveau + 1);
        $tags['operator'][] = $this->affiche($node->getOperateur(), $niveau + 1);
        $tags['right'][] = $this->affiche($node->getGauche(), $niveau + 1);

        $node->myGauche = $this->getIntervalleId();
        $this->tags = $tags;
        return $this->savenode($node, $niveau);
    }

    function affiche_arginit($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->setCode('');
        
        $this->affiche($node->getVariable(), $niveau + 1);
        $this->affiche($node->getValeur(), $niveau + 1);

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);
    }

    function affiche_arglist($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->setCode('');

        $elements = $node->getList();
        if (count($elements) == 0) {
            $token_traite = new token_traite(new Token());
            $this->affiche($token_traite, $niveau + 1);
            // @note create an empty token, to materialize the empty list
        } else {
            $labels = array();
            foreach($elements as $id => &$e) {
                if (is_null($e)) {
                    // @empty_else
                } else {
                    $this->affiche($e, $niveau + 1);
                }
            }
        }

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);
    }

    function affiche_block($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        if ($node->checkCode('{')) {
            $node->setCode('');
        }

        $elements = $node->getList();
        foreach($elements as $id => &$e) {
            $this->affiche($e, $niveau + 1);
        }
        $node->myGauche = $this->getIntervalleId();
        $return = $this->savenode($node, $niveau);
        return $return;
    }

    function affiche__break($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->setCode('');

        $this->affiche($node->getNiveaux(), $niveau + 1);

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);
    }

    function affiche__case($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->setCode('');

        if (!is_null($m = $node->getComparant())) {
            $this->affiche($m, $niveau + 1);
        }
        $this->affiche($node->getBlock(), $niveau + 1);

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);
    }

    function affiche_cast($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();

        $this->affiche($node->getExpression(), $niveau + 1);

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);
    }

    function affiche__catch($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();

        $this->affiche($node->getException(), $niveau + 1);
        $this->affiche($node->getVariable(), $niveau + 1);
        $this->affiche($node->getBlock(), $niveau + 1);

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);
    }

    function affiche__continue($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();

        $this->affiche($node->getNiveaux(), $niveau + 1);

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);
    }
    
    function affiche_cdtternaire($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();

        $this->affiche($node->getCondition(), $niveau + 1);
        $this->affiche($node->getVraie(), $niveau + 1);
        $this->affiche($node->getFaux(), $niveau + 1);

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);
    }

    function affiche_codephp($node, $niveau) {
        if (!isset($node->dotId)) {
            $node->dotId = $this->getNextId();
        }
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();

        $this->affiche($node->getphp_code(), $niveau + 1);

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);
    }

    function affiche__class($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->setCode('');
        $classe_precedent = $this->class;
        $this->class = $node->getName()->getCode();

        $tags = array();
        $abstract = $node->getAbstract();
        if(!is_null($abstract)) {
            $tags['abstract'][] = $this->affiche($abstract, $niveau + 1);            
        }

        $tags['name'][] = $this->affiche($node->getName(), $niveau + 1);            

        $extends = $node->getExtends();
        if (!is_null($extends)) {
            $tags['extends'][] = $this->affiche($extends, $niveau + 1);            
        }

        $implements = $node->getImplements();
        if (count($implements) > 0) {
            foreach($implements as $implement) {
                $tags['implements'][] =  $this->affiche($implement, $niveau + 1);            
            }
        }

        $tags['block'][] = $this->affiche($node->getBlock(), $niveau + 1);            

        $node->myGauche = $this->getIntervalleId();
        $this->tags = $tags;
        $res = $this->savenode($node, $niveau);
        $this->class = $classe_precedent;
        return $res;
    }

    function affiche__clone($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();

        $this->affiche($node->getExpression(), $niveau + 1);

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);
    }

    function affiche_clevaleur($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();

        $this->affiche($node->getCle(), $niveau + 1);
        $this->affiche($node->getValeur(), $niveau + 1);

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);
    }

    function affiche_comparison($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->setCode($node->getOperateur()->getCode());

        $tags = array();
        $tags['right'][] = $this->affiche($node->getDroite(), $niveau + 1);
        $tags['operator'][] = $this->affiche($node->getOperateur(), $niveau + 1);
        $tags['left'][] = $this->affiche($node->getGauche(), $niveau + 1);
        $this->tags = $tags;
        
        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);
    }

    function affiche_concatenation($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->setCode('');

        $elements = $node->getList();
        $labels = array();

        foreach($elements as $id => &$e) {
            $this->affiche($e, $niveau + 1);            
        }

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);
    }

    function affiche_constante($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        
        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);
    }

    function affiche_constante_static($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();

        $classe = $node->getClass();
        $this->affiche($classe, $niveau + 1);
        $method = $node->getConstant();
        $this->affiche($method, $niveau + 1);

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);
    }

    function affiche_constante_classe($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();

        $classe = $node->getName();
        $this->affiche($classe, $niveau + 1);
        $method = $node->getConstante();
        $this->affiche($method, $niveau + 1);

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);
    }

   function affiche_decalage($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->setCode('');

        $this->affiche($node->getDroite(), $niveau + 1);
        $this->affiche($node->getOperateur(), $niveau + 1);
        $this->affiche($node->getGauche(), $niveau + 1);

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);
    }
    
    function affiche__default($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->setCode('');

        $this->affiche($node->getBlock(), $niveau + 1);

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);
    }

    function affiche__for($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->setCode('');

        $tags = array();

        if (!is_null($f = $node->getInit())) {
            $tags['init'][] = $this->affiche($f, $niveau + 1);
        }
        if (!is_null($f = $node->getFin())) {
            $tags['end'][] = $this->affiche($f, $niveau + 1);
        }
        if (!is_null($f = $node->getIncrement())) {
            $tags['increment'][] = $this->affiche($f, $niveau + 1);
        }
        $tags['block'][] = $this->affiche($node->getBlock(), $niveau + 1);
        $this->tags = $tags;

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);
    }

    function affiche__foreach($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->setCode('');

        $tags = array();
        $tags['array'][] = $this->affiche($node->getTableau(), $niveau + 1);

        $key = $node->getKey();
        if (!is_null($key)) {
           $tags['key'][] = $this->affiche($key, $niveau + 1);
        }
        $tags['value'][] = $this->affiche($node->getValue(), $niveau + 1);
        $tags['block'][] = $this->affiche($node->getBlock(), $niveau + 1);

        $node->myGauche = $this->getIntervalleId();
        $this->tags = $tags;
        return $this->savenode($node, $niveau);
    }

    function affiche__function($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $scope_precedent = $this->scope;
        $this->scope = $node->getName()->getCode();

        $tags = array();
        if (!is_null($m = $node->getVisibility())) {
            $tags['visibility'][] = $this->affiche($m, $niveau + 1);
        }
        if (!is_null($m = $node->getAbstract())) {
            $tags['abstract'][] = $this->affiche($m, $niveau + 1);
        }
        if (!is_null($m = $node->getStatic())) {
            $tags['static'][] = $this->affiche($m, $niveau + 1);
        }
        $tags['name'][] = $this->affiche($node->getName(), $niveau + 1);
        // @note reading function name
        $node->setCode($node->getName()->getCode());
        $tags['args'][] = $this->affiche($node->getArgs(), $niveau + 1);
        $tags['block'][] = $this->affiche($node->getBlock(), $niveau + 1);

        $node->myGauche = $this->getIntervalleId();

        $this->tags = $tags;
        $res = $this->savenode($node, $niveau);
        $this->scope = $scope_precedent;
        return $res;
    }

    function affiche_functioncall($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();

        $tags = array();
        $tags['function'][] = $this->affiche($node->getFunction(), $niveau + 1);
        $node->setCode($node->getFunction()->getCode());
        $tags['args'][] = $this->affiche($node->getArgs(), $niveau + 1);

        $node->myGauche = $this->getIntervalleId();
        $this->tags = $tags;
        return $this->savenode($node, $niveau);
    }

    function affiche__global($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        
        $elements = $node->getVariables();
        foreach($elements as $id => $e) {
            $this->affiche($e, $niveau + 1);
        }

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);    
    }

    function affiche_ifthen($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();

        $conditions = $node->getCondition();
        $thens = $node->getThen();
        $labels = array();

        $tags = array();
        
        foreach($conditions as $id => &$condition) {
            $condition->setCode('elseif');
            $tags['condition'][] = $this->affiche($condition, $niveau + 1);
            $tags['then'][] = $this->affiche($thens[$id], $niveau + 1);
        }
        
        $else = $node->getElse();
        if (!is_null($else)){
            $else->setCode('else');
            $tags['else'][] = $this->affiche($else, $niveau + 1);
        }

        $node->myGauche = $this->getIntervalleId();
        $this->tags = $tags;
        return $this->savenode($node, $niveau);
    }

    function affiche_inclusion($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();

        $this->affiche($node->getInclusion(), $niveau + 1);

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);        
    }

    function affiche__interface($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $classe_precedent = $this->class;
        $this->class = $node->getName()->getCode();

        $e = $node->getExtends();
        if (count($e) > 0) {
            foreach($e as $ex) {
                $this->affiche($ex, $niveau + 1);
            }
        }
        $this->affiche($node->getBlock(), $niveau + 1);

        $node->myGauche = $this->getIntervalleId();
        $res = $this->savenode($node, $niveau);
        $this->class = $classe_precedent;
        return $res;
    }

    function affiche_invert($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        
        $this->affiche($node->getExpression(), $niveau + 1);
        
        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);        
    }

    function affiche_literals($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);        
    }

    function affiche_logique($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();

        $this->affiche($node->getDroite(), $niveau + 1);
        $this->affiche($node->getOperateur(), $niveau + 1);
        $this->affiche($node->getGauche(), $niveau + 1);
        
        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);        
    }

    function affiche_method($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();

        $tags = array();
        $tags['object'][] = $this->affiche($node->getObject(), $niveau + 1);
        $tags['method'][] = $this->affiche($node->getMethod(), $niveau + 1);        
        
        $node->myGauche = $this->getIntervalleId();
        $this->tags = $tags;
        return $this->savenode($node, $niveau);        
    }

    function affiche_method_static($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();

        $tags = array();
        $tags['class'][] = $this->affiche($node->getClass(), $niveau + 1);
        $tags['method'][] = $this->affiche($node->getMethod(), $niveau + 1);
        
        $node->myGauche = $this->getIntervalleId();
        $this->tags = $tags;
        return $this->savenode($node, $niveau);        
    }

    function affiche__new($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();

        $tags = array();
        $tags['name'][] = $this->affiche($node->getClasse(), $niveau + 1);
        $tags['args'][] = $this->affiche($node->getArgs(), $niveau + 1);
        
        $node->myGauche = $this->getIntervalleId();
        $this->tags = $tags;
        return $this->savenode($node, $niveau);        
    }

    
    function affiche_noscream($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();

        $this->affiche($node->getExpression(), $niveau + 1);
        
        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);        
    }

    function affiche_not($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        
        $this->affiche($node->getExpression(), $niveau + 1);
        
        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);        
    }

    function affiche_opappend($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        
        $this->affiche($node->getVariable(), $niveau + 1);
        
        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);        
    }

    function affiche_operation($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        
        $this->affiche($node->getDroite(), $niveau + 1);
        $this->affiche($node->getOperation(), $niveau + 1);
        $this->affiche($node->getGauche(), $niveau + 1);
        
        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);        
    }

    function affiche_parentheses($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        
        $this->affiche($node->getContenu(), $niveau + 1);
        
        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);        
    }

    function affiche_preplusplus($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        
        $this->affiche($node->getVariable(), $niveau + 1);
        $this->affiche($node->getOperateur(), $niveau + 1);
        
        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);    
    }

    function affiche_postplusplus($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        
        $this->affiche($node->getVariable(), $niveau + 1);
        $this->affiche($node->getOperateur(), $niveau + 1);
        
        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);    
    }

    function affiche_property($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        
        $tags = array();
        $tags['object'][] = $this->affiche($node->getObject(), $niveau + 1);
        $tags['property'][] = $this->affiche($node->getProperty(), $niveau + 1);
        
        $node->myGauche = $this->getIntervalleId();
        $this->tags = $tags;
        return $this->savenode($node, $niveau);        
    }

    function affiche_property_static($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        
        $this->affiche($node->getClass(), $niveau + 1);
        $this->affiche($node->getProperty(), $niveau + 1);
        
        $tags = array();
        $tags['class'][] = $this->affiche($node->getClass(), $niveau + 1);
        $tags['property'][] = $this->affiche($node->getProperty(), $niveau + 1);
        
        $node->myGauche = $this->getIntervalleId();
        $this->tags = $tags;
        return $this->savenode($node, $niveau);        
    }

    function affiche_rawtext($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);
    }

    function affiche_reference($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();

        $this->affiche($node->getExpression(), $niveau + 1);

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);
    }

    function affiche__return($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        
        if (!is_null($return = $node->getReturn())) {
            $this->affiche($return, $niveau + 1);
        }

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);
    }

    function affiche_sequence($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->setCode('');

        $elements = $node->getElements();
        if (count($elements) == 0) {
            // @empty_else 
        } else {
            $labels = array();
            $id = 0;
            foreach($elements as $id => &$e) {
                if (is_null($e)) {
                    continue; // @note just ignore this. Never encountered when we used a die
                } else {
                    $this->affiche($e, $niveau + 1);
                }
            }
        }

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);
    }

    function affiche_shell($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->setCode('');

        $elements = $node->getExpression();
        foreach($elements as $id => $e) {
            $this->affiche($e, $niveau + 1);
        }

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);
    }

    function affiche_sign($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->setCode('');

        $this->affiche($node->getSign(), $niveau + 1);
        $this->affiche($node->getExpression(), $niveau + 1);
        
        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);
    }

    function affiche__static($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->setCode('');

        $this->affiche($node->getExpression(), $niveau + 1);
        
        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);
    }

    function affiche__switch($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->setCode('');

        $this->affiche($node->getOperande(), $niveau + 1);
        $this->affiche($node->getBlock(), $niveau + 1);
        
        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);
    }

    function affiche_tableau($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->setCode('');

        $tags = array();
        $tags['array'][] = $this->affiche($node->getVariable(), $niveau + 1);
        $tags['index'][] = $this->affiche($node->getIndex(), $niveau + 1);
        $this->tags = $tags;
        
        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);
    }

    function affiche__throw($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->setCode('');

        $this->affiche($node->getException(), $niveau + 1);

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);
    }

    function affiche__try($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->setCode('');

        $this->affiche($node->getBlock(), $niveau + 1);

        $elements = $node->getCatch();
        foreach($elements as $id => &$e) {
            $this->affiche($e, $niveau + 1);
        }
        
        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);
    }

    function affiche_typehint($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->setCode('');

        $this->affiche($node->getType(), $niveau + 1);
        $this->affiche($node->getName(), $niveau + 1);

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);
    }

    function affiche__var($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->setCode('');
        
        if (!is_null($node->getVisibility())) {
            $this->affiche($node->getVisibility(), $niveau + 1);
        }
        if (!is_null($node->getStatic())) {
            $this->affiche($node->getStatic(), $niveau + 1);
        }
        $variables = $node->getVariable();
        if (count($variables) > 0) {
            $inits = $node->getInit();
            foreach($variables as $id => $variable) {
                $this->affiche($variable, $niveau + 1);
                if (!is_null($inits[$id])) {
                    $this->affiche($inits[$id], $niveau + 1);
                }
            }
        
        }

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);
    }

    function affiche_variable($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        
        $name = $node->getName();
        if (is_object($name)) {
            $this->affiche($name, $niveau + 1);
            $node->setCode("$".$name->getCode());
        }

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);
    }

    function affiche__while($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();

        $tags = array();
        $tags['condition'][] = $this->affiche($node->getCondition(), $niveau + 1);
        $tags['block'][] = $this->affiche($node->getBlock(), $niveau + 1);
        $this->tags = $tags;

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);
    }

    function affiche__dowhile($node, $niveau) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();

        $this->affiche($node->getCondition(), $niveau + 1);
        $this->affiche($node->getBlock(), $niveau + 1);

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $niveau);
    }
    
    function affiche_Token($node, $niveau) {
        print_r(xdebug_get_function_stack());        
        print "Warning : displayed raw Token : '$node'\n";
        die();
    }
}

?>
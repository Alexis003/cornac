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
    
    function affiche($node = null, $level = 0) {
    // @question why not move all this into a first template that would check, so we can avoid those tests/die here? 
        if ($level > 200) {
            print_r(xdebug_get_function_stack());        
            print "Attention : over 200 levels of recursion. Aborting.\n"; 
            die(__METHOD__."\n");
        }
        if (is_null($node)) {
            if ($level == 0) {
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
            $return = $this->$method($node, $level);
        } else {
            print "Displaying ".__CLASS__." for '".$method."'. Aborting\n";
            die(__METHOD__."\n");
        }
        if (!is_null($node->getNext())){
            $this->affiche($node->getNext(), $level);
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

    function savenode($node, $level) {
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
             '". $level ."'
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
    function affiche_token_traite($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);        
    }

    function affiche_affectation($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->setCode('');

        $tags = array();
        $tags['left'][] = $this->affiche($node->getDroite(), $level + 1);
        $tags['operator'][] = $this->affiche($node->getOperateur(), $level + 1);
        $tags['right'][] = $this->affiche($node->getGauche(), $level + 1);

        $node->myGauche = $this->getIntervalleId();
        $this->tags = $tags;
        return $this->savenode($node, $level);
    }

    function affiche_arginit($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->setCode('');
        
        $this->affiche($node->getVariable(), $level + 1);
        $this->affiche($node->getValue(), $level + 1);

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);
    }

    function affiche_arglist($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->setCode('');

        $elements = $node->getList();
        if (count($elements) == 0) {
            $token_traite = new token_traite(new Token());
            $this->affiche($token_traite, $level + 1);
            // @note create an empty token, to materialize the empty list
        } else {
            $labels = array();
            foreach($elements as $id => &$e) {
                if (is_null($e)) {
                    // @empty_else
                } else {
                    $this->affiche($e, $level + 1);
                }
            }
        }

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);
    }

    function affiche_block($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        if ($node->checkCode('{')) {
            $node->setCode('');
        }

        $elements = $node->getList();
        foreach($elements as $id => &$e) {
            $this->affiche($e, $level + 1);
        }
        $node->myGauche = $this->getIntervalleId();
        $return = $this->savenode($node, $level);
        return $return;
    }

    function affiche__break($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->setCode('');

        $this->affiche($node->getLevels(), $level + 1);

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);
    }

    function affiche__case($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->setCode('');

        if (!is_null($m = $node->getComparant())) {
            $this->affiche($m, $level + 1);
        }
        $this->affiche($node->getBlock(), $level + 1);

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);
    }

    function affiche_cast($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();

        $this->affiche($node->getExpression(), $level + 1);

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);
    }

    function affiche__catch($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();

        $this->affiche($node->getException(), $level + 1);
        $this->affiche($node->getVariable(), $level + 1);
        $this->affiche($node->getBlock(), $level + 1);

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);
    }

    function affiche__continue($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();

        $this->affiche($node->getLevels(), $level + 1);

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);
    }
    
    function affiche_ternaryop($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();

        $this->affiche($node->getCondition(), $level + 1);
        $this->affiche($node->getThen(), $level + 1);
        $this->affiche($node->getElse(), $level + 1);

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);
    }

    function affiche_codephp($node, $level) {
        if (!isset($node->dotId)) {
            $node->dotId = $this->getNextId();
        }
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();

        $this->affiche($node->getphp_code(), $level + 1);

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);
    }

    function affiche__class($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->setCode('');
        $classe_precedent = $this->class;
        $this->class = $node->getName()->getCode();

        $tags = array();
        $abstract = $node->getAbstract();
        if(!is_null($abstract)) {
            $tags['abstract'][] = $this->affiche($abstract, $level + 1);            
        }

        $tags['name'][] = $this->affiche($node->getName(), $level + 1);            

        $extends = $node->getExtends();
        if (!is_null($extends)) {
            $tags['extends'][] = $this->affiche($extends, $level + 1);            
        }

        $implements = $node->getImplements();
        if (count($implements) > 0) {
            foreach($implements as $implement) {
                $tags['implements'][] =  $this->affiche($implement, $level + 1);            
            }
        }

        $tags['block'][] = $this->affiche($node->getBlock(), $level + 1);            

        $node->myGauche = $this->getIntervalleId();
        $this->tags = $tags;
        $res = $this->savenode($node, $level);
        $this->class = $classe_precedent;
        return $res;
    }

    function affiche__clone($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();

        $this->affiche($node->getExpression(), $level + 1);

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);
    }

    function affiche_keyvalue($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();

        $this->affiche($node->getKey(), $level + 1);
        $this->affiche($node->getValue(), $level + 1);

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);
    }

    function affiche_comparison($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->setCode($node->getOperateur()->getCode());

        $tags = array();
        $tags['right'][] = $this->affiche($node->getDroite(), $level + 1);
        $tags['operator'][] = $this->affiche($node->getOperateur(), $level + 1);
        $tags['left'][] = $this->affiche($node->getGauche(), $level + 1);
        $this->tags = $tags;
        
        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);
    }

    function affiche_concatenation($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->setCode('');

        $elements = $node->getList();
        $labels = array();

        foreach($elements as $id => &$e) {
            $this->affiche($e, $level + 1);            
        }

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);
    }

    function affiche_constante($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        
        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);
    }

    function affiche_constante_static($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();

        $classe = $node->getClass();
        $this->affiche($classe, $level + 1);
        $method = $node->getConstant();
        $this->affiche($method, $level + 1);

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);
    }

    function affiche_constante_classe($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();

        $classe = $node->getName();
        $this->affiche($classe, $level + 1);
        $method = $node->getConstante();
        $this->affiche($method, $level + 1);

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);
    }

   function affiche_decalage($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->setCode('');

        $this->affiche($node->getDroite(), $level + 1);
        $this->affiche($node->getOperateur(), $level + 1);
        $this->affiche($node->getGauche(), $level + 1);

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);
    }

    function affiche__declare($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->setCode('');

        $tags = array();

        $ticks = $node->getTicks();
        if (!is_null($ticks)) {
            $tags['ticks'][] = $this->affiche($ticks, $level + 1);
        }
        $encoding = $node->getEncoding();
        if (!is_null($encoding)) {
            $tags['encoding'][] = $this->affiche($encoding, $level + 1);
        }
        $n = $node->getBlock();
        if (!is_null($n)) {
            $tags['block'][] = $this->affiche($n, $level + 1);
        }
        $this->tags = $tags;

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);
    }
    
    function affiche__default($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->setCode('');

        $this->affiche($node->getBlock(), $level + 1);

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);
    }

    function affiche__for($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->setCode('');

        $tags = array();

        if (!is_null($f = $node->getInit())) {
            $tags['init'][] = $this->affiche($f, $level + 1);
        }
        if (!is_null($f = $node->getFin())) {
            $tags['end'][] = $this->affiche($f, $level + 1);
        }
        if (!is_null($f = $node->getIncrement())) {
            $tags['increment'][] = $this->affiche($f, $level + 1);
        }
        $tags['block'][] = $this->affiche($node->getBlock(), $level + 1);
        $this->tags = $tags;

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);
    }

    function affiche__foreach($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->setCode('');

        $tags = array();
        $tags['array'][] = $this->affiche($node->getArray(), $level + 1);

        $key = $node->getKey();
        if (!is_null($key)) {
           $tags['key'][] = $this->affiche($key, $level + 1);
        }
        $tags['value'][] = $this->affiche($node->getValue(), $level + 1);
        $tags['block'][] = $this->affiche($node->getBlock(), $level + 1);

        $node->myGauche = $this->getIntervalleId();
        $this->tags = $tags;
        return $this->savenode($node, $level);
    }

    function affiche__function($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $scope_precedent = $this->scope;
        $this->scope = $node->getName()->getCode();

        $tags = array();
        if (!is_null($m = $node->getVisibility())) {
            $tags['visibility'][] = $this->affiche($m, $level + 1);
        }
        if (!is_null($m = $node->getAbstract())) {
            $tags['abstract'][] = $this->affiche($m, $level + 1);
        }
        if (!is_null($m = $node->getStatic())) {
            $tags['static'][] = $this->affiche($m, $level + 1);
        }
        $tags['name'][] = $this->affiche($node->getName(), $level + 1);
        // @note reading function name
        $node->setCode($node->getName()->getCode());
        $tags['args'][] = $this->affiche($node->getArgs(), $level + 1);
        $tags['block'][] = $this->affiche($node->getBlock(), $level + 1);

        $node->myGauche = $this->getIntervalleId();

        $this->tags = $tags;
        $res = $this->savenode($node, $level);
        $this->scope = $scope_precedent;
        return $res;
    }

    function affiche_functioncall($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();

        $tags = array();
        $tags['function'][] = $this->affiche($node->getFunction(), $level + 1);
        $node->setCode($node->getFunction()->getCode());
        $tags['args'][] = $this->affiche($node->getArgs(), $level + 1);

        $node->myGauche = $this->getIntervalleId();
        $this->tags = $tags;
        return $this->savenode($node, $level);
    }

    function affiche__global($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        
        $elements = $node->getVariables();
        foreach($elements as $id => $e) {
            $this->affiche($e, $level + 1);
        }

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);    
    }

    function affiche_ifthen($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();

        $conditions = $node->getCondition();
        $thens = $node->getThen();
        $labels = array();

        $tags = array();
        
        foreach($conditions as $id => &$condition) {
            $condition->setCode('elseif');
            $tags['condition'][] = $this->affiche($condition, $level + 1);
            $tags['then'][] = $this->affiche($thens[$id], $level + 1);
        }
        
        $else = $node->getElse();
        if (!is_null($else)){
            $else->setCode('else');
            $tags['else'][] = $this->affiche($else, $level + 1);
        }

        $node->myGauche = $this->getIntervalleId();
        $this->tags = $tags;
        return $this->savenode($node, $level);
    }

    function affiche_inclusion($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();

        $this->affiche($node->getInclusion(), $level + 1);

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);        
    }

    function affiche__interface($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $classe_precedent = $this->class;
        $this->class = $node->getName()->getCode();

        $e = $node->getExtends();
        if (count($e) > 0) {
            foreach($e as $ex) {
                $this->affiche($ex, $level + 1);
            }
        }
        $this->affiche($node->getBlock(), $level + 1);

        $node->myGauche = $this->getIntervalleId();
        $res = $this->savenode($node, $level);
        $this->class = $classe_precedent;
        return $res;
    }

    function affiche_invert($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        
        $this->affiche($node->getExpression(), $level + 1);
        
        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);        
    }

    function affiche_literals($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);        
    }

    function affiche_logique($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();

        $this->affiche($node->getDroite(), $level + 1);
        $this->affiche($node->getOperateur(), $level + 1);
        $this->affiche($node->getGauche(), $level + 1);
        
        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);        
    }

    function affiche_method($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();

        $tags = array();
        $tags['object'][] = $this->affiche($node->getObject(), $level + 1);
        $tags['method'][] = $this->affiche($node->getMethod(), $level + 1);        
        
        $node->myGauche = $this->getIntervalleId();
        $this->tags = $tags;
        return $this->savenode($node, $level);        
    }

    function affiche_method_static($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();

        $tags = array();
        $tags['class'][] = $this->affiche($node->getClass(), $level + 1);
        $tags['method'][] = $this->affiche($node->getMethod(), $level + 1);
        
        $node->myGauche = $this->getIntervalleId();
        $this->tags = $tags;
        return $this->savenode($node, $level);        
    }

    function affiche__new($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();

        $tags = array();
        $tags['name'][] = $this->affiche($node->getClasse(), $level + 1);
        $tags['args'][] = $this->affiche($node->getArgs(), $level + 1);
        
        $node->myGauche = $this->getIntervalleId();
        $this->tags = $tags;
        return $this->savenode($node, $level);        
    }

    
    function affiche_noscream($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();

        $this->affiche($node->getExpression(), $level + 1);
        
        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);        
    }

    function affiche_not($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        
        $this->affiche($node->getExpression(), $level + 1);
        
        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);        
    }

    function affiche_opappend($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        
        $this->affiche($node->getVariable(), $level + 1);
        
        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);        
    }

    function affiche_operation($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        
        $this->affiche($node->getDroite(), $level + 1);
        $this->affiche($node->getOperation(), $level + 1);
        $this->affiche($node->getGauche(), $level + 1);
        
        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);        
    }

    function affiche_parentheses($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        
        $this->affiche($node->getContenu(), $level + 1);
        
        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);        
    }

    function affiche_preplusplus($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        
        $this->affiche($node->getVariable(), $level + 1);
        $this->affiche($node->getOperateur(), $level + 1);
        
        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);    
    }

    function affiche_postplusplus($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        
        $this->affiche($node->getVariable(), $level + 1);
        $this->affiche($node->getOperateur(), $level + 1);
        
        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);    
    }

    function affiche_property($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        
        $tags = array();
        $tags['object'][] = $this->affiche($node->getObject(), $level + 1);
        $tags['property'][] = $this->affiche($node->getProperty(), $level + 1);
        
        $node->myGauche = $this->getIntervalleId();
        $this->tags = $tags;
        return $this->savenode($node, $level);        
    }

    function affiche_property_static($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        
        $this->affiche($node->getClass(), $level + 1);
        $this->affiche($node->getProperty(), $level + 1);
        
        $tags = array();
        $tags['class'][] = $this->affiche($node->getClass(), $level + 1);
        $tags['property'][] = $this->affiche($node->getProperty(), $level + 1);
        
        $node->myGauche = $this->getIntervalleId();
        $this->tags = $tags;
        return $this->savenode($node, $level);        
    }

    function affiche_rawtext($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);
    }

    function affiche_reference($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();

        $this->affiche($node->getExpression(), $level + 1);

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);
    }

    function affiche__return($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        
        if (!is_null($return = $node->getReturn())) {
            $this->affiche($return, $level + 1);
        }

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);
    }

    function affiche_sequence($node, $level) {
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
                    $this->affiche($e, $level + 1);
                }
            }
        }

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);
    }

    function affiche_shell($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->setCode('');

        $elements = $node->getExpression();
        foreach($elements as $id => $e) {
            $this->affiche($e, $level + 1);
        }

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);
    }

    function affiche_sign($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->setCode('');

        $this->affiche($node->getSign(), $level + 1);
        $this->affiche($node->getExpression(), $level + 1);
        
        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);
    }

    function affiche__static($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->setCode('');

        $this->affiche($node->getExpression(), $level + 1);
        
        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);
    }

    function affiche__switch($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->setCode('');

        $this->affiche($node->getCondition(), $level + 1);
        $this->affiche($node->getBlock(), $level + 1);
        
        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);
    }

    function affiche__array($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->setCode('');

        $tags = array();
        $tags['array'][] = $this->affiche($node->getVariable(), $level + 1);
        $tags['index'][] = $this->affiche($node->getIndex(), $level + 1);
        $this->tags = $tags;
        
        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);
    }

    function affiche__throw($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->setCode('');

        $this->affiche($node->getException(), $level + 1);

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);
    }

    function affiche__try($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->setCode('');

        $this->affiche($node->getBlock(), $level + 1);

        $elements = $node->getCatch();
        foreach($elements as $id => &$e) {
            $this->affiche($e, $level + 1);
        }
        
        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);
    }

    function affiche_typehint($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->setCode('');

        $this->affiche($node->getType(), $level + 1);
        $this->affiche($node->getName(), $level + 1);

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);
    }

    function affiche__var($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        $node->setCode('');
        
        if (!is_null($node->getVisibility())) {
            $this->affiche($node->getVisibility(), $level + 1);
        }
        if (!is_null($node->getStatic())) {
            $this->affiche($node->getStatic(), $level + 1);
        }
        $variables = $node->getVariable();
        if (count($variables) > 0) {
            $inits = $node->getInit();
            foreach($variables as $id => $variable) {
                $this->affiche($variable, $level + 1);
                if (!is_null($inits[$id])) {
                    $this->affiche($inits[$id], $level + 1);
                }
            }
        
        }

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);
    }

    function affiche_variable($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();
        
        $name = $node->getName();
        if (is_object($name)) {
            $this->affiche($name, $level + 1);
            $node->setCode("$".$name->getCode());
        }

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);
    }

    function affiche__while($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();

        $tags = array();
        $tags['condition'][] = $this->affiche($node->getCondition(), $level + 1);
        $tags['block'][] = $this->affiche($node->getBlock(), $level + 1);
        $this->tags = $tags;

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);
    }

    function affiche__dowhile($node, $level) {
        $node->myId = $this->getNextId();
        $node->myDroite = $this->getIntervalleId();

        $this->affiche($node->getBlock(), $level + 1);
        $this->affiche($node->getCondition(), $level + 1);

        $node->myGauche = $this->getIntervalleId();
        return $this->savenode($node, $level);
    }
    
    function affiche_Token($node, $level) {
        print_r(xdebug_get_function_stack());        
        print "Warning : displayed raw Token : '$node'\n";
        die();
    }
}

?>
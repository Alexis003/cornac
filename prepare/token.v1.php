<?php

class Token {
    protected $next = null;
    protected $prev = null;
    
    static public $root = null;

// 
    protected $token = null;
    protected $code = null;
    protected $ligne = null;
    protected $id = null;

// configuration
    public $structures = null;

    public function __construct() {
        $this->structures = array('whitespace', 
                                  'commentaire',
                                  'literals', 
                                  'variable', 
                                  'tableau', 
                                  'rawtext', 
                                  'parentheses', 
                                  'affectation', 
//                                  'instruction', 
                                  'sequence', 
                                  'operation', 
                                  'functioncall', 
                                  'noscream', 
                                  'arglist', 
                                  'inclusion', 
                                  'codephp',
                                  'concatenation',
                                  'cdtternaire',
                                  'ifthen',
                                  'block',
                                  'not',
                                  'property',
                                  'constante');
    }
     
    function setId($id) {
        $this->id = $id;
    }

    function setCode($code) {
        $this->code = $code;
    }

    function setLine($line) {
        $this->line = $line;
    }

    function setToken($token) {
        $this->token = $token;
    }
    
    function getId() {
        return $this->id;
    }

    function getCode() {
        return $this->code;
    }

    function getLine() {
        if (isset($this->line)) {
            return $this->line;
        } else {
            return NULL;
        
        }
    }

    function getToken() {
        return $this->token;
    }

    function copyToken($t) {
        $this->setId($t->getId());
        $this->setToken($t->getToken());
        $this->setCode($t->getCode());
        $this->setLine($t->getLine());
    }

    function append(Token $t) {
        $this->next = $t;
        $t->setPrev($this);
    }

    function prepend(Token $t) {
        $this->prev = $t;
        $t->setNext($this);
    }

    function insert(Token $t, $type = "after") {
        if ($type == "before") {
            $prev = $this->prev; 

            $prev->setNext($t);
            $t->setNext($this);
            
            $this->prev = $t;
            $t->setPrev($prev);
        } elseif ($type == "after") {
            $next = $this->next; 
            
            $this->setNext($t);
            $t->setNext($next);

            if (!is_null($next)) {
               $next->setPrev($t);
            }
            $t->setPrev($this);
            
            $this->next = $t;
            $t->setNext($next);
        
        } else {
            die("Impossible d'insérer ce type ($type). Doit être after ou before");
        } 
    }

    function replace(Token $t) {
        $this->setNext( $t->getNext());
        $this->setPrev( $t->getPrev());

        if (!is_null($t->getNext())) {
            $t->getNext()->setPrev($this);
        }
        if (!is_null($t->getPrev())) {
            $t->getPrev()->setNext($this);
        }
    }

    function removeNext() {
        if (is_null($this->next)) {
            return ;
        }
        if (is_null($this->getNext(1))) {
            $this->setNext(null);
        } else {
            $this->getNext(1)->setPrev($this);
            $this->setNext($this->getNext(1));
        }
    }

    function removePrev() {
        if (is_null($this->prev)) {
            return ;
        }
        if (is_null($this->getPrev(1))) {
            $this->setPrev(null);
        } else {
            $this->getPrev(1)->setNext($this);
            $this->setPrev($this->getPrev(1));
        }
    }

    function removeCurrent() {
        $next = $this->next;
        $prev = $this->prev;
        
        if (is_null($next)) {
            // 
        } else {
            $next->setPrev($prev);
        }

        if (is_null($prev)) {
            // 
        } else {
            $prev->setNext($next);
        }
        
        $this->next = null;
        $this->prev = null;
    }
    
    function detach() {
        $this->prev = null;
        $this->next = null;
    }
    
    function setPrev($t) {
        $this->prev = $t;
    }

    function setNext($t) {
        $this->next = $t;
    }

    function getPrev($n = 0) {
        $retour = $this->prev;
        
        if ($n > 0) {
            if (is_null($retour)) {
                return NULL;
            }
            $retour = $retour->getPrev($n - 1);
        }

        return $retour;  
    }

    function hasNext($n = 0) {
        if ($n == 0) {
            return !is_null($this->next);
        } elseif (is_null($this->next)) {
            return false;
        } else {
            return $this->next->hasNext($n - 1);
        }
    }

    function hasPrev($n = 0) {
        if ($n == 0) {
            return !is_null($this->prev);
        } elseif (is_null($this->prev)) {
            return false;
        } else {
            return $this->prev->hasPrev($n - 1);
        }
    }

    function getNext($n = 0) {
        $retour = $this->next;
        
        if ($n > 0) {
            if (is_null($retour)) {
                return NULL;
            }
            $retour = $retour->getNext($n - 1);
        }

        return $retour;
    }
    
    function __toString() {
        return $this->token.' "'.$this->code.'"';
    }

    static public function factory(Token $t, $class = 'Token') {
        if (!Token::test($class::$tests, $t)) {
            return $t;
        } 
        
        $args = $class::factory_get_args($t);
        
        if (empty($args)) {
            $retour = new $class();
        } else {
            $retour = new $class($args);
        }
        $retour->copyToken($t);
        
        foreach($class::$creation['remove'] as $arg) {
            if ($arg > 0) {
                $t->removeNext($arg - 1);
            } elseif ($arg < 0) {
                $t->removePrev($arg + 1);
            } else {
                // rien, c'est une erreur
            }
        }

        $retour->replace($t);
        $retour->setToken(0);
        $retour->neutralise();

        mon_log(get_class($t)." => ".get_class($retour));
        return $retour; 
    }

    static public function factory_get_args() {
        return array();
    }
    
    function neutralise() {
        
    }

    function affiche($d = 0 , $f = 0) {
        if ($d < 0 && $f > 0) { 
            $this->affiche($d, 0); 
            $d = 0;
        }
        
        if ($d < 0) {
            $token = 'getPrev';
        } else {
            $token = 'getNext';
        }

        for($i = $d; $i < $f; $i++) {
            print "$i) ".$this->$token($i)."\n";
        }
        print "\n";
    }
    
    function upgrade() {
        foreach ($this->structures as $s) {
            $a = get_class($this);
            $retour = $s::factory($this, $s);
            if (is_null($retour)) {
                print "$s a retourne null pour $this\n";
                die();
            }
            if ($this != $retour) {
                return $retour;
            }
        }
        return $retour;
    }
    
    function test($tests, $t) { 
        foreach($tests as $id => $test) {
//            Print "test $id\n";
            foreach($test as $variable => $criteres) {
                if ($variable == 0) {
                    $token = $t;
                } elseif($variable > 0) {
                    $token = $t->getNext($variable - 1);
                } elseif($variable < 0) {
                    $token = $t->getPrev($variable + 1);
                } else {
                    print "Problème...";
                    die();
                }
                
                $retour = false;
                foreach($criteres as $type => $valeurs) {
                    switch ($type) {
                        case "code" : 
                            $retour |= Token::check_code($token, $valeurs);
                            break;

                        case "notcode" : 
                            $retour |= !Token::check_code($token, $valeurs);
                            break;

                        case "token" : 
                            $retour |= Token::check_token($token, $valeurs);
                            break;

                        case "nottoken" : 
                            $retour |= !Token::check_token($token, $valeurs);
                            break;
                        
                        case "class" : 
                            $retour |= Token::check_class($token, $valeurs);
                            break;

                        case "notclass" : 
                            $retour |= !Token::check_class($token, $valeurs);
                            break;

                        case "subclass" : 
                            $retour |= Token::check_subclass($token, $valeurs);
                            break;

                        default: 
                            print "default process \"$type\" ignored $variable => $type\n";
                            break ;
                    }
                }
                    if (!$retour) {
                        return false;
                    } 
            }
        }
        return true; 
    }

    static function check_code($token, $code) {
        if (!is_array($code)) {
            $code = array($code);
        }
        if (!is_object($token)) {
            var_dump($token);
            die();
        }
        return in_array($token->getCode(), $code);
    }

    static function check_token($token, $code) {
        if (!is_array($code)) {
            $code = array($code);
        }
        return in_array($token->getToken(), $code);
    }
    
    static function check_class($token, $classes) {
        if (!is_object($token)) {
            return false;
        }
        if (!is_array($classes)) {
            $classes = array($classes);
        }
        if (in_array(get_class($token), $classes)) {
            return true;
        } else {
            return false;
        }
    }

    static function check_subclass($token, $classes) {
        if (!is_object($token)) {
            return false;
        }
        if (is_array($classes)) {
            print "finir subclass\n";
            die();
            $valeurs = array($classes);
        }
        // sinon string
        return is_subclass_of($token, $classes);
    }
     
}

?>
<?php

class Token {
    protected $next = null;
    protected $prev = null;
    
    static public $root = null;

    protected $token = null;
    protected $code = null;
    protected $line = -1;
    protected $id = null;
    
    static private $test_id = null;

// configuration @_
    public $structures = null;

    const ANY_TOKEN = 0;
    
    public function __construct() {

    }

    function __call($method, $args) {
        if (substr($method, 0, 3) == 'get') {
            $membre = strtolower(substr($method, 3));
            if (isset($this->$membre)) {
                return $this->$membre;
            }
        }
        print_r(xdebug_get_function_stack());        
        die("$method est une méthode inconnue!\n");
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
            return "NULL";
        
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
            // @empty_ifelse
        } else {
            $next->setPrev($prev);
        }

        if (is_null($prev)) {
            // @empty_ifelse
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
        if (!isset($id_getNext)) {$id_getNext = 1;} else { $id_getNext++; } 
        $n++;
        $retour = $this;
        while($n > 0) {
            $retour = $retour->next;
            if (is_null($retour)) { return NULL; }
            $n--;
        }
        
        return $retour;
    }
    
    function __toString() {
        return $this->token.' "'.$this->code.'"';
    }

    static public function factory(Token $t, $class = 'Token') {
        $regex = $class::getRegex(); 
        
        foreach($regex as $r) {
            $r = new $r;
            
            if (!$r->check($t)) {
                unset($r);
                continue;
            }
        
            $retour = Token::applyRegex($t, $class, $r);
            mon_log(get_class($t)." => ".get_class($retour));
            return $retour; 
        }
        return $t;
    }
    
    function make_token_traite($entree) {
        $clone = clone $entree;
                
        $retour = new token_traite($clone);
        $retour->replace($clone);
        $retour->setToken($entree->getToken());
        $retour->setLine($entree->getLine());
        
        return $retour;
    }
    

    static function applyRegex($t, $class, $r) { 
        $args = $r->getArgs();
        
        foreach($args as $id => $arg) {
            if ($arg > 0) {
                $args[$id] = $t->getNext($arg - 1);
            } elseif ($arg < 0) {
                $args[$id] = $t->getPrev(abs($arg + 1));
            } else {        
                $args[$id] = $t;
            }
        }
        
        if (empty($args)) {
            $retour = new $class();
        } else {
            $retour = new $class($args);
        }
        $retour->copyToken($t);

        $remove = $r->getRemove();
        foreach($remove as $arg) {
            if ($arg > 0) {
                $t->removeNext($arg - 1);
            } elseif ($arg < 0) {
                $t->removePrev($arg + 1);
            } else {
                // @empty_ifelse this is an error. Should be trapped
            }
        }

        $retour->replace($t);
        $retour->setToken(0);
        $retour->neutralise();
        
        unset($r);
        
        return $retour;
    }

    static public function factory_get_args() {
        return array();
    }
    
    function neutralise() {
        
    }

    function affiche($d = 0 , $f = 0) {
        for($i = $d; $i < $f; $i++) {
            if ($i < 0) {
                print "$i) ".$this->getPrev(abs($i))."\n";
            } elseif ($i > 0) {
                print "$i) ".$this->getNext(abs($i))."\n";
            } else {
                print "$i-) ".$this->getPrev()."\n";
                print "0) ".$this."\n";
                print "$i+) ".$this->getNext()."\n";
            }
        }
        print "\n";
    }

    public function checkCode($code) {
        if (!is_array($code)) {
            $code = array($code);
        }
        return in_array($this->getCode(), $code);
    }

    public function checkNotCode($code) {
        return !$this->checkCode($code);
    }

    public function checkOperateur($code) {
        if (get_class($this) != 'Token') { return false; }
        
        if (!is_array($code)) {
            $code = array($code);
        }
        return in_array($this->getCode(), $code);
    }

    public function checkNotOperateur($code) {
        return !$this->checkOperateur($code);
    }

    public function checkToken($token) {
        if (!is_array($token)) {
            $token = array($token);
        }
        return in_array($this->getToken(), $token);
    }

    public function checkNotToken($token) {
        return !$this->checkToken($token);
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

    public function checkClass($classes) {
        if (!is_array($classes)) {
            return get_class($this) == $classes;
        } else {
            return in_array(get_class($this), $classes);
        }
    }

    public function checkNotClass($classes) {
        return !$this->checkClass($classes);
    }

    public function checkSubclass($classes) {
        if (is_array($classes)) {
           if (count($classes) > 1) {
                print "finir subclass\n";
                die();
           }
           $classes = array_shift($classes);
        } else {
            // @empty_ifself otherwise it is a string
        }
        return is_subclass_of($this, $classes);
    }

    public function checkNotSubclass($classes) {
        return !$this->checkSubclass($classes);
    }

    public function checkFunction() {
        return in_array($this->token,array(T_STRING, 
                                           T_ARRAY, 
                                           T_ISSET, 
                                           T_PRINT, 
                                           T_ECHO, 
                                           T_EXIT, 
                                           T_EMPTY, 
                                           T_LIST, 
                                           T_UNSET,
                                           T_EVAL));
    }

    public function checkNotFunction() {
        return !$this->checkFunction();
    }
    
    public function checkBeginInstruction() {
        if (in_array($this->code, array('(','{','[',',','?',':','.','==','+=','-=',
                                        '.=', '=','.=','*=','+=','-=','/=','%=','>>=',
                                        '&=','^=','>>>=', '|=','<<=','>>=','/','*','%',
                                        '>','<','<=','>=',';','=>','&&','||' ))) {
            return true;
        }
        if (in_array($this->token, array(T_OPEN_TAG,            T_IS_EQUAL, 
                                         T_ECHO,                T_PRINT,
                                         T_IS_SMALLER_OR_EQUAL, T_IS_NOT_IDENTICAL,
                                         T_IS_NOT_EQUAL,        T_IS_IDENTICAL, 
                                         T_IS_GREATER_OR_EQUAL, T_CASE, 
                                         T_RETURN, T_EXIT, T_INT_CAST, T_DOUBLE_CAST, 
                                         T_STRING_CAST, T_ARRAY_CAST, T_BOOL_CAST,
                                         T_OBJECT_CAST, T_UNSET_CAST, T_ELSE))) {
            return true;
        }
        if (in_array(get_class($this), array('rawtext','sequence','ifthen',
                                             'functioncall','affectation',
                                             'parentheses','block','sequence'))) {
            return true;
        }
        return false;
    }

    public function checkEndInstruction() {
        if (in_array($this->code, array(',',')',';',']',':','?',',','}','/','*','%'))) {
            return true;
        }
        if (in_array($this->token, array(T_CLOSE_TAG, T_ENDIF, T_ENDFOREACH, T_ENDFOR, T_ELSE))) {
            return true;
        }
        if ($this->checkSubclass("instruction")) {
            return true;
        }
        return false;
    }

    public function checkNotEndInstruction() {
        return !$this->checkEndInstruction(); 
    }

    public function checkGenericCase() {
        if (in_array($this->token, array(T_CASE,T_DEFAULT))) {
            return true;
        }
        if (in_array(get_class($this), array('_case'))) {
            return true;
        }
        return false;
    }

    function checkForBlock($and_block = false) {
        $liste = array('sequence',
                       'operation',
                       'rawtext',
                       'affectation',
                       'ifthen',
                       'preplusplus',
                       'postplusplus',
                       '_for',
                       '_foreach',
                       '_break',
                       '_continue',
                       '_return',
                       '_function',
                       'functioncall',
                       '_switch',
                       '_try',
                       '_throw',
                       '_var',
                       'noscream',
                       'constante_classe',
                       '_while',
                       '_global',
                       '_dowhile',
                       'logique',
                       'method',
                       'method_static',
                       'literals',
                       'inclusion',
                       '_class',
                       '_interface',
                       'property',
                       '_static',
                       'cdtternaire',
                       '_clone',
                       '_declare',
// @dont Don't put variable in this list    'variable',
                                       );
        if ($and_block) {
            $liste[] = 'block';
        }
        
        if ($this->checkNotClass($liste)) {
            return false;
        }
        
        if (!$this->hasNext()) { return true; }
        
        return $this->getNext()->checkNotCode(array('->','[','(','::'));
    }

    function checkForComparison() {
        $liste = array('==','>','<','<=','>=','===','!==','<=>');
        return $this->checkCode($liste);
    }

    function checkForAssignation() {
        if ($this->checkNotClass('Token')) { return false; }
        $liste = array('=','.=','*=','+=','-=','/=','%=','>>=','&=','^=', '|=','<<=');
        return $this->checkCode($liste);
    }

    function checkForLogical() {
        if ($this->checkNotClass('Token')) { return false; }
        $liste = array('&&','and','or','xor','||');
        return $this->checkCode($liste);
    }

    function checkForVariable() {
        $liste = array('variable','tableau','property');
        return $this->checkClass($liste);
    }
    
    function toToken_traite($token) {
        if ($token->checkClass('Token')) {
            $retour = new token_traite($token);
            $retour->replace($token);
        } else {
            $retour = $token;
        }
        
        return $retour;
    }
    
    function stop_on_error($message) {
        $bt = debug_backtrace();
        print "Message : $message\n";
        print_r($bt[0]);
        die();
    }
}

function affiche_entree($entree) {
        foreach($entree as $id => $e) {
            print "$id) $e\n";
        }
        print "------\n";
}

?>
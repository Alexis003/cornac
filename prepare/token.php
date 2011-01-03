<?php
/*
   +----------------------------------------------------------------------+
   | Cornac, PHP code inventory                                           |
   +----------------------------------------------------------------------+
   | Copyright (c) 2010 - 2011 Alter Way Solutions (France)               |
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
        die("$method is unknown method!\n");
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
            die("Can't insert this type : $type. Must be 'after' or 'before'");
        } 
    }

    function replace(Token $t) {
        $next = $t->getNext();
        $prev = $t->getPrev();

        $this->setNext( $next);
        $this->setPrev( $prev);

        if (!is_null($next)) {
            $next->setPrev($this);
        }
        if (!is_null($prev)) {
            $prev->setNext($this);
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
        $n++;
        $return = $this;
        while($n > 0) {
            $return = $return->prev;
            if (is_null($return)) { return NULL; }
            $n--;
        }
        
        return $return;
    }

    function hasNext($n = 0) {
        $n++;
        $return = $this;
        while($n > 0) {
            $return = $return->next;
            if (is_null($return)) { return false; }
            $n--;
        }
        
        return true;
    }

    function hasPrev($n = 0) {
        $n++;
        $return = $this;
        while($n > 0) {
            $return = $return->prev;
            if (is_null($return)) { return false; }
            $n--;
        }
        
        return true;
    }

    function getNext($n = 0) {
        if ($n == 0) { return $this->next; }
        // @note no test. This seems to work, as HasNext is always used. Maybe we can remove hasnext usage, and put it here?
        if ($n == 1) { return $this->next->next; }

        $n++;
        $return = $this;
        while($n > 0) {
            $return = $return->next;
            if (is_null($return)) { return NULL; }
            $n--;
        }
        
        return $return;
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
        
            $return = Token::applyRegex($t, $class, $r);
            mon_log(get_class($t)." => ".get_class($return));
            return $return; 
        }
        return $t;
    }
    
    function makeToken_traite($entree) {
        $clone = clone $entree;
                
        $return = new token_traite($clone);
        $return->replace($clone);
        $return->setToken($entree->getToken());
        $return->setLine($entree->getLine());
        
        return $return;
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
            $return = new $class();
        } else {
            $return = new $class($args);
        }
        $return->copyToken($t);

        $remove = $r->getRemove();
        foreach($remove as $arg) {
            if ($arg > 0) {
                $t->removeNext();
            } elseif ($arg < 0) {
                $t->removePrev();
            } else {
                // @empty_ifelse this is an error. Should be trapped
            }
        }

        $return->replace($t);
        $return->setToken(0);
        $return->neutralise();
        
        unset($r);
        
        return $return;
    }
    
    // @note must be redefined by each class. 
    function neutralise() {
        print get_class($this)." didn't overload ".__METHOD__."\n";
    }

    function display($d = 0 , $f = 0) {
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

    public function checkIsOperator() {
        if ($this->token != 0) { return false; }
        return false;
    }

    public function checkOperator($code) {
        if (get_class($this) != 'Token') { return false; }
        
        if (!is_array($code)) {
            $code = array($code);
        }
        return in_array($this->getCode(), $code);
    }

    public function checkNotOperator($code) {
        return !$this->checkOperator($code);
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
           $classes = array_shift($classes);
        } else {
            // @empty_ifelse otherwise it is a string
        }
        return is_subclass_of($this, $classes);
    }

    public function checkNotSubclass($classes) {
        return !$this->checkSubclass($classes);
    }

    public function checkFunction() {
        if (get_class($this) == '_nsname') {
        // @note name with namespace
            return true; 
        } else {
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
                                             'parenthesis','block','sequence',
                                             '_global','_use'))) {
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
                       'constante_class',
                       '_while',
                       '_global',
                       '_use',
                       '_dowhile',
                       'logical',
                       'method',
                       'method_static',
                       'literals',
                       'inclusion',
                       '_class',
                       '_goto',
                       'label',
                       '_interface',
                       'property',
                       '_static',
                       'ternaryop',
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
        return $this->checkOperator($liste);
    }

    function checkForLogical() {
        if ($this->checkNotClass('Token')) { return false; }
        $liste = array(T_LOGICAL_OR, T_LOGICAL_AND, T_LOGICAL_XOR, T_BOOLEAN_AND, T_BOOLEAN_OR, T_INSTANCEOF);
        if ($this->checkToken($liste)) { 
            return true;
        }
        
        if ($this->checkOperator(array('&','|','^'))) {
            return true;
        }
        
        return false;
    }

    function checkForVariable() {
        $liste = array('variable','_array','property');
        return $this->checkClass($liste);
    }
    
    function toToken_traite($token) {
        if ($token->checkClass('Token')) {
            $return = new token_traite($token);
            $return->replace($token);
        } else {
            $return = $token;
        }
        
        return $return;
    }
    
    function stopOnError($message) {
        $bt = debug_backtrace();
        print "Message : $message\n";
        print_r($bt[0]);
        die();
    }
}

function display_entree($entree) {
        foreach($entree as $id => $e) {
            print "$id) $e\n";
        }
        print "------\n";
}

?>
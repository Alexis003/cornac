<?php

class sequence_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(Token::ANY_TOKEN);
    }
 
    function check($t) {
        if (!$t->hasNext() ) { return false; }
        
        if ( $t->hasPrev() && $t->getPrev()->checkForAssignation()) { return false; }
        if ( $t->hasPrev() && $t->getPrev()->checkClass('parentheses')) { return false; }
        if ( $t->hasPrev() && $t->getPrev()->checkCode(array(')','->','(',',','.','new','!==','::',':',
                '?','or','and','xor','var','$','/','+','-','*','%','@','&','|','^','"',
                '<','>','+'))) { return false; }

        if ( $t->hasPrev() && $t->getPrev()->checkToken(array(T_PRIVATE, T_PUBLIC, T_PROTECTED, T_STATIC, T_THROW, 
                                                              T_LOGICAL_OR, T_LOGICAL_AND, T_LOGICAL_XOR, 
                                                              T_BOOLEAN_OR, T_BOOLEAN_AND, 
                                                              T_IS_EQUAL, T_IS_SMALLER_OR_EQUAL, T_IS_NOT_IDENTICAL,
                                                              T_IS_NOT_EQUAL, T_IS_IDENTICAL, T_IS_GREATER_OR_EQUAL,
                                                              T_INSTANCEOF, T_ELSE, T_ABSTRACT, T_DO, T_CASE
                                                              ))                        )            { return false; }
        if ( $t->hasPrev()  && $t->getPrev( )->checkClass(array('tableau','variable','property')))   { return false; }
        if ( $t->hasPrev(1) && $t->getPrev(1)->checkToken(array(T_FOR,T_WHILE)))                     { return false; }

        if ($t->checkSubClass('instruction') && 
            $t->checkNotClass('parentheses') && 
            $t->getNext()->checkCode(';') ) { 
                        
            $var = $t->getNext(1); 
            $this->args   = array( 0 );
            $this->remove = array( 1 );
            
            $pos = 2;
            if (is_null($var)) {
                mon_log(get_class($t)." => 0null ".__CLASS__);
                return true; 
            }
            if (!$var->hasNext()) {
                mon_log(get_class($t)." => 1null ".__CLASS__);
                
                return !$var->checkToken(T_CLOSE_TAG); 
            }
            
            while ($var->checkSubClass('instruction')) {
                   $this->args[]    = $pos ;
                   $this->remove[]  = $pos;
                   
                   $pos += 1;
                   $var = $var->getNext();

                   if (is_null($var)) {
                       mon_log(get_class($t)." => nnull ".__CLASS__);
                       return true; 
                   }

                   if ($var->checkCode(';')) {
                       $this->remove[]  = $pos + 1;
                       
                       $pos += 1;
                       $var = $var->getNext();
                       if (is_null($var)) {
                           mon_log(get_class($t)." => nnull ".__CLASS__);
                           return true; 
                       }
                   } elseif ($var->checkToken(T_LOGICAL_OR, T_LOGICAL_AND, T_LOGICAL_XOR)) {
                        return false;
                   }
            }
            
            if ($var && (
                $var->checkCode(array(',','->','[','(',',')) ||
                $var->checkForLogical() ||
                $var->checkForAssignation() ||
                $var->checkClass('arglist'))) {
                // @doc This is not a sequence, as this operator finally has priority
                $this->args = array();
                $this->remove = array();
                return false;
            } elseif ($var->hasNext() && (
                $var->getNext()->checkCode(array(',','->','[','(',',')) ||
                $var->getNext()->checkForAssignation() ||
                $var->getNext()->checkClass('arglist'))) {
                // @doc This is not a sequence, as another operator after has priority

                $this->args = array();
                $this->remove = array();
                return false;
            } elseif ($var->checkCode(')')) {
                // @doc This looks like a for loop! 
                return false;
            } elseif (count($this->args) > 0) {
                // @doc OK we are good now
                mon_log(get_class($t)." => ".__CLASS__);
                return true; 
            } else {
                // @doc Not processed? aborting. 
                $this->args = array();
                $this->remove = array();
                return false;
            }
        }
    }

}
?>
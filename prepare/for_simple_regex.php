<?php

class for_simple_regex extends analyseur_regex {
    function __construct() {
        parent::__construct(array());
    }

    function getTokens() {
        return array(T_FOR);
    }
    
    function check($t) {
        if (!$t->hasNext(4)) { return false; }

        if ($t->checkNotToken(array(T_FOR))) { return false; } 
        if ($t->getNext()->checkNotOperateur(array('('))) { return false; } 

        $args = array();
        $remove = array(1);
        $pos = 1;

        if ($t->getNext($pos)->checkCode(';')) {
            $args[] = $pos + 1;

            $remove[] = $pos + 1;
            
            $pos += 1;
        } elseif ($t->getNext($pos)->checkClass(array('Token','sequence')) ) {
            return false; 
        } elseif ($t->getNext($pos)->checkClass(array('block')) ) {
            $args[] = $pos + 1  ;

            $remove[] = $pos  + 1;
            
            $pos += 1;
            if ($t->getNext($pos)->checkOperateur(';')) {
                $remove[] = $pos  + 1;
                $pos += 1;
            }
        } elseif ($t->getNext($pos)->checkNotClass(array('Token','sequence')) &&
            $t->getNext($pos + 1)->checkCode(';'))
        {
            $args[] = $pos + 1  ;

            $remove[] = $pos  + 1;
            $remove[] = $pos + 1  + 1;
            
            $pos += 2;
        } else { // @doc Not a Token followed by ;, we ignore
            return false;
        }

        if ($t->getNext($pos)->checkCode(';')) {
            $args[] = $pos + 1;

            $remove[] = $pos + 1;
            
            $pos += 1;
        } elseif ($t->getNext($pos)->checkClass(array('Token')) ) {
            return false; 
        } elseif ($t->getNext($pos)->checkClass(array('block','sequence')) ) {
            $args[] = $pos + 1  ;
            $remove[] = $pos  + 1;
            $pos += 1;
            
            if ($t->getNext($pos)->checkOperateur(';')) {
                $remove[] = $pos  + 1;
                $pos += 1;
            }
        } elseif ($t->getNext($pos)->checkNotClass(array('Token','sequence')) &&
            $t->getNext($pos + 1)->checkCode(';')
        ) {
            $args[] = $pos + 1  ;

            $remove[] = $pos  + 1;
            $remove[] = $pos + 1  + 1;
            
            $pos += 2;
        } else { // @doc Not a Token followed by ;, we ignore
            return false;
        }

        if ($t->getNext($pos)->checkCode(')')) {
            $args[] = $pos + 1;

            $remove[] = $pos + 1;
            
            $pos += 1;
        } elseif ($t->getNext($pos)->checkClass(array('Token','sequence')) ) {
            return false; 
        } elseif ($t->getNext($pos)->checkNotClass(array('Token','sequence')) &&
                  !is_null($t->getNext($pos + 1)) && 
                  $t->getNext($pos + 1)->checkCode(')')
        ) {
            $args[] = $pos + 1  ;

            $remove[] = $pos  + 1;
            $remove[] = $pos + 1  + 1;
            
            $pos += 2;
        } else { // @doc Not a Token followed by ;, we ignore
            return false;
        } 

        if ($t->getNext($pos)->checkCode(';')) {
            $regex = new modele_regex('block',array(), array());
            Token::applyRegex($t->getNext($pos), 'block', $regex);

            mon_log(get_class($t)." => block (position $pos) (from ; ) (".__CLASS__.")");            
            // @note no return, we carry on
        }
        
        if ($t->getNext($pos)->checkForBlock(true) && 
            (is_null($t->getNext($pos + 1)) ||
             $t->getNext($pos + 1)->checkNotCode(array('(','->','::','=','[')))) {
              $args[] = $pos + 1;
              $remove[] = $pos + 1;

              $this->args = $args;
              $this->remove = $remove;
              
              mon_log(get_class($t)." => ".__CLASS__);
              return true;
        } else {
            return false;
        }
    }
}

?>
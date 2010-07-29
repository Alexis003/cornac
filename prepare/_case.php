<?php

class _case extends instruction {
    protected $expression = null;
    protected $block      = null;
    
    function __construct($expression = null) {
        parent::__construct(array());
        
        if ($expression[0]->checkToken(T_CASE)) {
            $this->comparant = $expression[1];
            if (isset($expression[2])) {
                $this->block     = $expression[2];
            } else {
                $this->block     = new block();
            }
        } elseif ($expression[0]->checkToken(T_DEFAULT)) {
            $this->comparant = new block();
            if (isset($expression[1])) {
                $this->block     = $expression[1];
            } else {
                $this->block     = new block();
            }
        } else {
            $this->stop_on_error("Unexpected TOKEN received : '".$expression[0]->getToken()."' in ".__METHOD__);
        }
    }

    function __toString() {
        return __CLASS__." ".$this->code;
    }

    function getComparant() {
        if ($this->comparant == 'default') {
            return null;
        } else {
            return $this->comparant;
        }
    }

    function getBlock() {
        return $this->block;
    }

    function neutralise() {
        if ($this->comparant != 'default') {
            $this->comparant->detach();
        }
        $this->block->detach();
    }

    function getRegex(){
        return array('case_block_regex',
                     'case_between_regex',
                    );
    }

}

?>
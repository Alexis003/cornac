<?php

class cdtternaire extends instruction {
    protected $condition = null;
    protected $vraie = null;
    protected $faux = null;
    
    function __construct($expression) {
        parent::__construct(array());
        if (!is_array($expression)) {
        
        } elseif (count($expression) == 2) {
            if ($expression[0]->checkClass('arglist')) {
                $this->condition = $expression[0]->getList();
                $this->condition = $this->condition[0];
            } else {
                $this->condition = $expression[0];
            }
            $this->vraie     = null;
            $this->faux      = $expression[1];
        } elseif (count($expression) == 3) {
            if ($expression[0]->checkClass('arglist')) {
                $this->condition = $expression[0]->getList();
                $this->condition = $this->condition[0];
            } else {
                $this->condition = $expression[0];
            }
            $this->vraie     = $expression[1];
            $this->faux      = $expression[2];
        } else {
            $this->stop_on_error("Wrong number of arguments  : '".count($expression)."' in ".__METHOD__);
        }
    }

    function __toString() {
        return __CLASS__." ".$this->code;
    }

    static function getRegex() {
        return array('cdtternaire_normal_regex');
    }

    function getCondition() {
        return $this->condition;
    }

    function getVraie() {
        return $this->vraie;
    }

    function getFaux() {
        return $this->faux;
    }

    function neutralise() {
        $this->condition->detach();
        if (!is_null($this->vraie)) {
            $this->vraie->detach();
        }
        $this->faux->detach();
    }
}
?>
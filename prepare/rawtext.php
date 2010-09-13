<?php

class rawtext extends instruction {
    protected $rawtext = null;

    function __construct($expression = null) {
        parent::__construct(array());
        
        if ($expression[0]->checkToken(T_CLOSE_TAG)) {
            $this->rawtext = $expression[0];
            
            $this->rawtext->setToken(T_INLINE_HTML);
            $this->rawtext->setCode('');
        } else {
            $this->rawtext = $expression[0];
        }
    }

    function __toString() {
        return __CLASS__." ".$this->rawtext;
    }
    
    function getText() {
        return $this->rawtext;
    }

    function neutralise() {
        if (!is_null($this->rawtext)) {
            $this->rawtext->detach();
        }
    }

    function getRegex(){
        return array('rawtext_regex',
                     'rawtext_empty_regex',
                     );
    }

}

?>
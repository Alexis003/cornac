<?php

class literals extends token {
    function __construct($signe = null) {
        parent::__construct(array());
        
        $this->valeur = $signe[0]->getCode();
        
        if (isset($signe[1])) {
            $this->valeur = trim($this->valeur,"'\"");
            if ($signe[1]->checkCode("-")){
                $this->valeur = -1 * $this->valeur;
            }
        }
    }

    function __toString() {
        return __CLASS__." ".$this->valeur;
    }

    function getLiteral() {
        return $this->valeur;
    }

    static function getRegex() {
        return array('literals_regex',
                    'literals_heredoc_regex'
        
                        );
    }
}

?>
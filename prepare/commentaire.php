<?php

class commentaire extends token {

	function __construct() {
	    parent::__construct();
	}

    static public function factory($t) {
        if ($t->checkToken(array(T_COMMENT, T_DOC_COMMENT))) {
            $retour = $t->getPrev();
            $retour->removeNext();
            
            return $retour; 
        } else {
            return $t;
        }
    }

}

?>
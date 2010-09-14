<?php

class commentaire extends token {

	function __construct() {
	    parent::__construct();
	}

    static public function factory($t) {
        if ($t->checkToken(array(T_COMMENT, T_DOC_COMMENT))) {
            $return = $t->getPrev();
            $return->removeNext();
            
            return $return; 
        } else {
            return $t;
        }
    }

}

?>
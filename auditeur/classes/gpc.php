<?php

class gpc extends typecalls {
	protected	$description = 'Liste des variables superglobales et leur usage';
	protected	$title = 'Superglobales';

    function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
    }

	public function analyse() {
	    $this->type = 'variable';
	    $this->code = array('$_GET','$_POST','$_COOKIE','$_SERVER','_FILES','$_REQUEST','$_SESSION','$_ENV',
	                        '$PHP_SELF','$HTTP_RAW_POST_DATA',
	                        '$HTTP_GET_VARS','$HTTP_POST_VARS',
	                        '$GLOBALS');
	    parent::analyse();
	}
	
}

?>
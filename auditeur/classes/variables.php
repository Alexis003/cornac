<?php

class variables extends typecalls {
	protected	$inverse = true;
	protected	$name = 'Classe sans nom';
	protected	$functions = array();

	protected	$description = 'Liste des variables et de leur usage';
	protected	$description_en = 'Variables being used';

    function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
    }

	public function analyse() {
	    $this->type = 'variable';
	    parent::analyse();
	}
	
}

?>
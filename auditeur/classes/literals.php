<?php

class literals extends typecalls {
	protected	$inverse = true;
	protected	$name = 'Classe sans nom';
	protected	$functions = array();

	protected	$description = 'Liste des literaux et de leur usage';
	protected	$description_en = 'Literals being used';

    function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
    }

	public function analyse() {
	    $this->type = 'literals';
	    parent::analyse();
	}
	
}

?>
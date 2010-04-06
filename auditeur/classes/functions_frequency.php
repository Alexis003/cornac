<?php

class functions_frequency extends noms {
	protected	$description = 'Liste des appels de fonctions';
	protected	$description_en = 'Frequence of functions usage';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
    	$this->functions = array();
	}
	
	public function analyse() {
	    $this->noms['type_token'] = 'functioncall';
	    $this->noms['type_tags'] = 'fonction';
	    
	    parent::analyse();
	}
}

?>
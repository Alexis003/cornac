<?php

class functions_frequency extends noms {
	protected	$title = 'Fréquence des fonctions';
	protected	$description = 'Liste des appels de fonctions';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
	    $this->noms['type_token'] = 'functioncall';
	    $this->noms['type_tags'] = 'fonction';
	    
	    parent::analyse();
	    
	    return true;
	}
}

?>
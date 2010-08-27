<?php

class xml_functions extends functioncalls {
	protected	$title = 'Fonctions XML';
	protected	$description = 'Liste des fonctions de XML';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
	    $this->functions = get_extension_funcs("xml");
	    parent::analyse();
	    
	    return true;
	}
}

?>
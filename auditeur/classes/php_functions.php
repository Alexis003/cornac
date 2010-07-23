<?php

class php_functions extends functioncalls {
	protected	$description = 'Liste des fonctions PHP';
	protected	$description_en = 'List of PHP functions';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
	    $this->functions = modules::getPHPFunctions();
	    parent::analyse();
	}
}

?>
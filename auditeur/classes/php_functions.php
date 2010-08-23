<?php

class php_functions extends functioncalls {
	protected	$title = 'Liste des fonctions PHP';
	protected	$description = 'Liste des différentes fonctions PHP utilisées dans l\'application, avec leur fréquence';

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
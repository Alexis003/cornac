<?php

class nonphp_functions extends functioncalls {
	protected	$description = 'Liste des fonctions de dossier';
	protected	$description_en = 'usage of directory functions';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
	    $this->functions = modules::getPHPFunctions();
	    $this->not = true;

	    parent::analyse();
	}
}

?>
<?php

class nonphp_functions extends functioncalls {
	protected	$description = 'Liste des fonctions de dossier';
	protected	$title = 'Fonctions';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
	    $this->functions = modules::getPHPFunctions();
	    $this->not = true;

	    parent::analyse();
	}
}

?>
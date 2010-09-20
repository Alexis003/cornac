<?php

class ereg_functions extends functioncalls {
	protected	$title = 'Fonctions ereg';
	protected	$description = 'Liste des fonctions de ereg et associées';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
	    $this->functions = modules::getPHPFunctions("ereg");
	    parent::analyse();
	    
	    return true;
	}
}

?>
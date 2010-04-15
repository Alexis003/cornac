<?php

class execs extends functioncalls {
	protected	$description = 'Liste des fonctions d\'execution de commandes en ligne';
	protected	$description_en = 'usage of shell execution functions';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
	    $this->functions = array('exec','shell_exec','system','passthru');
	    
	    parent::analyse();
	}
}

?>
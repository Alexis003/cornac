<?php

class execs extends functioncalls {
	protected	$title = 'Fonctions execution';
	protected	$description = 'Liste des fonctions d\'execution de commandes en ligne';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
	    $this->functions = array('exec','shell_exec','system','passthru');
	    
	    parent::analyse();
	    
	    return true;
	}
}

?>
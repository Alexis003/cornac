<?php

class exec_functions extends functioncalls {
	protected	$title = 'Fonctions de ligne de commande';
	protected	$description = 'Liste des fonctions de l\'extension de exec de PHP';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
	    $this->functions = array('exec',
	                             'shell_exec',
	                             'passthru', 
	                             'system');
	    parent::analyse();
	    
	    return true;
	}
}

?>
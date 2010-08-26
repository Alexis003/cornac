<?php

class upload_functions extends functioncalls {
	protected	$title = 'Fonctions d\'upload';
	protected	$description = 'Liste des fonctions utilisées lors de l\'upload d\'un fichier';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
	    $this->functions = array('move_uploaded_file','is_uploaded_file','rename','copy');
	    parent::analyse();
	    
	    return true;
	}
}

?>
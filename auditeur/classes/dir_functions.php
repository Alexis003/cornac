<?php

class dir_functions extends functioncalls {
	protected	$title = 'Fonctions de dossier';
	protected	$description = 'Liste des fonctions de l\'extension de dir de PHP';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
	    $this->functions = array('opendir','dir','unlink', 'rename','move_uploaded_files','is_uploaded_file','is_writeable');
	    parent::analyse();
	    
	    return true;
	}
}

?>
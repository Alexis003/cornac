<?php

class upload_functions extends functioncalls {
	protected	$description = 'Liste des fonctions de fichiers';
	protected	$description_en = 'usage of file functions';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
	    $this->functions = array('move_uploaded_file','is_uploaded_file','rename','copy');
	    parent::analyse();
	}
}

?>
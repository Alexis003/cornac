<?php

class file_functions extends functioncalls {
	protected	$title = 'Fonctions de fichier';
	protected	$description = 'Liste des fonctions de fichier native utilisées dans l\'application';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
	    $this->functions = array('fopen','file_put_contents','file_get_contents', 'fwrite','move_uploaded_file','rename');
	    parent::analyse();
	}
}

?>
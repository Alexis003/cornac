<?php

class file_functions extends modules_fonctions {
	protected	$description = 'Liste des fonctions de fichiers';
	protected	$description_en = 'usage of file functions';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
	    $functions = array('fopen','file_put_contents','file_get_contents', 'fwrite','move_uploaded_file','rename');
	    
	    $this->analyse_function($functions);
	}
}

?>
<?php

class image_functions extends functioncalls {
	protected	$title = 'Fonctions d\'images';
	protected	$description = 'Liste des fonctions de l\'extension de gd de PHP';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
	    $this->functions = get_extension_funcs("gd");
	    parent::analyse();
	    
	    return true;
	}
}

?>
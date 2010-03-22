<?php

class headers extends modules_fonctions {
	protected	$description = 'Liste des émissions d\'entêtes HTTP';
	protected	$description_en = 'Where files are included';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
	    $functions = array('headers','setcookie');
	    
	    $this->analyse_function($functions);
	}
}

?>
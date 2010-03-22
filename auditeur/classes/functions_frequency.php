<?php

class functions_frequency extends modules_fonctions {
	protected	$description = 'Liste des appels de fonctions';
	protected	$description_en = 'Frequence of functions usage';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
	    $this->analyse_functioncall();
	}
}

?>
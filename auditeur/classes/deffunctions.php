<?php

class deffunctions extends noms {
	protected	$description = 'Liste des défintions de fonctions';
	protected	$description_en = 'List of functions definition';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
	    $this->noms['type_token'] = '_function';
	    $this->noms['type_tags'] = 'name';
	    
	    parent::analyse();

	}
}

?>
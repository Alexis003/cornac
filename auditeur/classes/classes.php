<?php

class classes extends noms {
	protected	$title = 'Classes';
	protected	$description = 'Liste des classes définies dans l\'application';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
	    $this->noms['type_token'] = '_class';
	    $this->noms['type_tags'] = 'name';
	    
	    parent::analyse();
	    return true;
	}
}

?>
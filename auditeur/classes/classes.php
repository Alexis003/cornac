<?php

class classes extends noms {
	protected	$description = 'Liste des classes et de leurs extensions';
	protected	$description_en = 'List of classes et its extensions';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
    	$this->functions = array();
	}
	
	public function analyse() {
	    $this->noms['type_token'] = '_class';
	    $this->noms['type_tags'] = 'name';
	    
	    parent::analyse();
	    return;
	}
}

?>
<?php

class headers extends functioncalls {
	protected	$description = 'Liste des émissions d\'entêtes HTTP';
	protected	$description_en = 'HTTP header emitting functions';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
        $this->functions = array('header','setcookie','setrawcookie');
        parent::analyse();
	}
}

?>
<?php

class headers extends functioncalls {
	protected	$title = 'Headers et setcookie';
	protected	$description = 'Liste des émissions d\'entêtes HTTP';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
        $this->functions = array('header','setcookie','setrawcookie');
        parent::analyse();
        
        return true;
	}
}

?>
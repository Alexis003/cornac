<?php

class constantes extends typecalls {
	protected	$title = 'Constantes';
	protected	$description = 'Liste des constantes utilisées';

    function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
    }

	public function analyse() {
	    $this->type = array('constante', 'constante_magique');
	    parent::analyse();
	    return true;
	}
	
}

?>
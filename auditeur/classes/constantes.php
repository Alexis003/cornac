<?php

class constantes extends typecalls {
	protected	$inverse = true;
	protected	$name = 'Classe sans nom';

	protected	$description = 'Liste des constantes et de leur usage';
	protected	$description_en = 'Constantes being used';

    function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
    }

	public function analyse() {
	    $this->type = array('constante', 'constante_magique');
	    parent::analyse();
	    return;
	}
	
}

?>
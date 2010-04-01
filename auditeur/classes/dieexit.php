<?php

class dieexit extends functioncalls {
	protected	$description = 'Liste des fins de scripts type die ou exit';
	protected	$description_en = 'exit and die usage';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;

		$this->description = 'Utilisation de la fonction eval()';
		$this->description_en = 'eval() usage';
	}
	
	public function analyse() {
        $this->functions = array('die','exit');
        parent::analyse();
	}
}

?>
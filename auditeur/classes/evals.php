<?php

class evals extends functioncalls {
	protected	$description = 'Liste des utilisations de eval';
	protected	$description_en = 'Where eval function are being used';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
        $this->functions = array('eval');
        parent::analyse();
	}
}

?>
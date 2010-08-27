<?php

class evals extends functioncalls {
	protected	$description = 'Liste des utilisations de eval';
	protected	$title = 'Eval';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
        $this->functions = array('eval');
        parent::analyse();
        
        return true;
	}
}

?>
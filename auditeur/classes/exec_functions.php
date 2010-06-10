<?php

class exec_functions extends functioncalls {
	protected	$description = 'Liste des fonctions d execution shell';
	protected	$description_en = 'usage of exec functions';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
	    $this->functions = array('exec','shell_exec','passthru', 'system');
	    parent::analyse();
	}
}

?>
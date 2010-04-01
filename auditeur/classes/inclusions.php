<?php

class inclusions extends functioncalls {
	protected	$description = 'Liste des inclusions';
	protected	$description_en = 'Where files are included';

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
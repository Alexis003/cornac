<?php

class dieexit extends functioncalls {
	protected	$title = 'Die et Exit';
	protected	$description = 'Liste des fins de scripts type die ou exit';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
        $this->functions = array('die','exit');
        parent::analyse();
        
        return true;
	}
}

?>
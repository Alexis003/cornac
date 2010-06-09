<?php

class php_functions extends functioncalls {
	protected	$description = 'Liste des fonctions de dossier';
	protected	$description_en = 'usage of directory functions';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
	    $functions = get_defined_functions();
	    $extras = array('echo','print','die','exit','isset','empty','array','list');
	    $this->functions = array_merge($functions['internal'], $extras);
//	    $this->functions = $functions['internal'];
	    parent::analyse();
	}
}

?>
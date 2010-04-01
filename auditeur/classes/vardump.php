<?php

class vardump extends functioncalls {
	protected	$description = 'Liste des var_dump, print_r, debug_backtrace, xdebug_*';
	protected	$description_en = 'usage of var_dump, print_r, debug_backtrace, xdebug_*';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
	    $this->functions = array('var_dump','print_r','debug_backtrace','debug_print_backtrace');
	    parent::analyse();
	}
}

?>
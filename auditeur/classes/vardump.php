<?php

class vardump extends functioncalls {
	protected	$title = 'Var_dump et autre débug';
	protected	$description = 'Liste des var_dump, print_r, debug_backtrace, xdebug_*';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
	    $this->functions = array('var_dump','print_r','debug_backtrace','debug_print_backtrace');
	    parent::analyse();
	    
	    return true;
	}
}

?>
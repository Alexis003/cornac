<?php

class literals extends typecalls {
	protected	$title = 'Litéraux';
	protected	$description = 'Liste des literaux et de leur usage (chaînes (guillemets, heredoc), nombres)';

    function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
    }

	public function analyse() {
	    $this->type = 'literals';
	    parent::analyse();
	    
	    return true;
	}
	
}

?>
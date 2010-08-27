<?php

class ereg_functions extends functioncalls {
	protected	$title = 'Fonctions ereg';
	protected	$description = 'Liste des fonctions de ereg et associées';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
	    $this->functions = array('ereg-replace.xml', 'ereg.xml', 'eregi-replace.xml',  'eregi.xml', 'split.xml', 'spliti.xml', 'sql-regcase.xml');
	    parent::analyse();
	    
	    return true;
	}
}

?>
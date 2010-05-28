<?php

class ereg_functions extends functioncalls {
	protected	$description = 'Liste des fonctions de ereg et associées';
	protected	$description_en = 'usage of ereg and co. functions';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
	    $this->functions = array('ereg-replace.xml', 'ereg.xml', 'eregi-replace.xml',  'eregi.xml', 'split.xml', 'spliti.xml', 'sql-regcase.xml');
	    parent::analyse();
	}
}

?>


<?php

class ldap_functions extends functioncalls {
	protected	$title = 'Fonctions LDAP';
	protected	$description = 'Liste des fonctions LDAP';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
	    $this->functions = modules::getPHPFunctions("ldap");
	    parent::analyse();
	    
	    return true;
	}
}

?>
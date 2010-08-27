<?php

class filter_functions extends functioncalls {
	protected	$title = 'Fonctions de filter';
	protected	$description = 'Liste des fonctions de l\'extension de filter de PHP';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
	    $this->functions = array(   'filter_input',
                                    'filter_var',
                                    'filter_input_array',
                                    'filter_var_array',
                                    'filter_list',
                                    'filter_has_var',
                                    'filter_id',
	    );
	    parent::analyse();
	    
	    return true;
	}
}

?>
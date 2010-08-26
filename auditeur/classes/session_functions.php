<?php

class session_functions extends functioncalls {
	protected	$title = 'Fonctions de session';
	protected	$description = 'Liste des fonctions de l\'extension de session de PHP';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
	    $this->functions = array('session_name', 
	                             'session_module_name', 
	                             'session_save_path', 
	                             'session_id', 
	                             'session_regenerate_id', 
	                             'session_decode', 
	                             'session_register', 
	                             'session_unregister', 
	                             'session_is_registered', 
	                             'session_encode', 
	                             'session_start', 
	                             'session_destroy', 
	                             'session_unset', 
	                             'session_set_save_handler',
	                             'session_cache_limiter', 
	                             'session_cache_expire', 
	                             'session_set_cookie_params', 
	                             'session_get_cookie_params', 
	                             'session_write_close', 
	                             'session_commit');
	    parent::analyse();
	    
	    return true;
	}
}

?>
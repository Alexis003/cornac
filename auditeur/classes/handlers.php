<?php 

class handlers extends functioncalls {
	protected	$title = 'Gestionnaires';
	protected	$description = 'Recherche les gestionnaires PHP reconfigurés.';

	function __construct($mid) {
        parent::__construct($mid);
	}

// @doc if this analyzer is based on previous result, use this to make sure the results are here
	function dependsOn() {
	    return array();
	}
	
	public function analyse() {
	    $this->functions = array(
                                'register_tick_function',
                                'register_shutdown_function',
                                'unregister_tick_function',
                                'xpath_register_ns',
                                'xpath_register_ns_auto',
                                'w32api_register_function',
                                'stream_register_wrapper',
                                'session_register',
                                'session_unregister',
                                'spl_autoload_register',
                                'stream_filter_register',
                                'xmlrpc_server_register_introspection_callback',
                                'xmlrpc_server_register_method',
                                'stream_wrapper_register',
                                'spl_autoload_unregister',
                                'stream_wrapper_unregister',
                                'http_request_method_register',
                                'http_request_method_unregister',
);
        parent::analyse();
        
        return true;
	}
}

?>
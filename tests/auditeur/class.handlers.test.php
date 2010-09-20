<?php
include_once('Auditeur_Framework_TestCase.php');

class handlers_Test extends Auditeur_Framework_TestCase
{
    public function testhandlers()  { 
        $this->expected = array( 
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
        $this->inexpected = array();
        
        parent::generic_test();
    }
}
?>
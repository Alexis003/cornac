<?php



class Ext_HttpBrowse extends functioncalls {
	protected	$title = 'HTTP requests';
	protected	$description = 'This is the special analyzer Ext_HttpBrowse (default doc).';

	function __construct($mid) {
        parent::__construct($mid);
	}

	public function analyse() {
	    $this->functions = array('file_get_contents','curl_exec','fopen');
	    
	    parent::analyse();
	    
	    return true;
	}
}

?>
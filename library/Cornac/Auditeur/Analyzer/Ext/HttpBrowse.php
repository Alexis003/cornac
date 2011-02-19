<?php



class Cornac_Auditeur_Analyzer_Ext_HttpBrowse extends Cornac_Auditeur_Analyzer_Functioncalls {
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
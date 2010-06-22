<?php

class zfGetGPC extends modules {
	protected	$description = 'Liste des méthodes qui lisent la requete';
	protected	$description_en = 'List of method that read the request';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
        $this->clean_rapport();

	    $requete = <<<SQL
INSERT INTO <rapport>
SELECT NULL, T1.fichier, T1.code, T1.id, '{$this->name}' 
    FROM <tokens> T1
    JOIN  <tokens_tags> TT
        ON TT.token_sub_id = T1.id
    WHERE 
        T1.code in ("getRequest",'getPost','getParams','getParam','isErrors','isValid','isPost','getModuleName','getControllerName','getActionName','getParameterValue')
        AND TT.type='fonction'
    ;
SQL;

    $this->exec_query($requete);
	}
}

?>
<?php

class zfGetGPC extends modules {
	protected	$title = 'ZF : acces aux variables entrantes';
	protected	$description = 'Liste des utilisations des méthodes du ZF qui permettent d\'accéder aux variables entrantes.';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
        $this->clean_rapport();

	    $query = <<<SQL
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

    $this->exec_query($query);
	}
}

?>
<?php

class classes_nb_methods extends modules {
	protected	$description = 'Nombre de methodes par classes';
	protected	$description_en = 'Number of method in classes';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
        $this->clean_rapport();
        
        $concat = $this->concat('class','"::"','scope');
	    $query = <<<SQL
INSERT INTO <rapport>
   SELECT NULL, T1.fichier, $concat AS code, T1.id, '{$this->name}'
    FROM <tokens> T1 
    WHERE T1.type='_function' AND 
          T1.class != '' AND
          T1.code = T1.scope

SQL;
        $this->exec_query($query);

	    return;
	}
}

?>
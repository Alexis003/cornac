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
        
	    $requete = <<<SQL
INSERT INTO <rapport>
SELECT NULL, class, count(*), T1.id, '{$this->name}'
    FROM <tokens> T1 
    WHERE T1.type='_function' AND 
          T1.class != '' AND
          T1.code = T1.scope
    GROUP BY T1.class

SQL;
        $this->exec_query($requete);

	    return;
	}
}

?>
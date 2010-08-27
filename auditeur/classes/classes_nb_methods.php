<?php

class classes_nb_methods extends modules {
	protected	$title = 'Nombre de méthodes par classe';
	protected	$description = 'Nombre de methodes par classe';

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
        $this->clean_rapport();
        
//      $concat = $this->concat('class','"::"','scope');
	    $query = <<<SQL
INSERT INTO <rapport>
   SELECT NULL, T1.fichier, class AS code, T1.id, '{$this->name}'
    FROM <tokens> T1 
    WHERE T1.type='_function' AND 
          T1.class != '' AND
          T1.code = T1.scope

SQL;
        $this->exec_query($query);

	    return true;
	}
}

?>
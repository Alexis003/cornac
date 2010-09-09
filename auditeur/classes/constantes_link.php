<?php 

class constantes_link extends modules {
	protected	$title = 'Liens entre fichier par constante';
	protected	$description = 'Fichiers qui utilisent une même constante';

	function __construct($mid) {
        parent::__construct($mid);
        
        $this->format = modules::FORMAT_DOT;
	}

// @doc if this analyzer is based on previous result, use this to make sure the results are here
	function dependsOn() {
	    return array('constantes','defconstantes');
	}
	
	public function analyse() {
        $this->clean_rapport();

	    $query = <<<SQL
INSERT INTO <rapport_dot>
SELECT TR1.fichier, TR2.fichier, TR1.element, '{$this->name}'
FROM <rapport>  TR1
JOIN <rapport> TR2
    ON TR2.module = 'constantes' AND
       TR2.element = TR1.element
WHERE TR1.module='defconstantes';
SQL;
        $this->exec_query($query);
        
        return true;
	}
}

?>
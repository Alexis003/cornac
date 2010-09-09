<?php 

class function_link extends modules {
	protected	$title = 'Lien entre les fichiers via les fonctions';
	protected	$description = 'Établit les liens entre deux fichiers, pour via une fonction : un lien est établit entre le fichier de défintion de la fonction, et son utilisation';

	function __construct($mid) {
        parent::__construct($mid);

        $this->format = modules::FORMAT_DOT;
	}

// @doc if this analyzer is based on previous result, use this to make sure the results are here
	function dependsOn() {
	    return array('functionscalls','deffunctions');
	}
	
	public function analyse() {
        $this->clean_rapport();

	    $query = <<<SQL
INSERT INTO <rapport_dot>
SELECT TR1.fichier, TR2.fichier, TR1.element, '{$this->name}'
FROM <rapport>  TR1
JOIN <rapport> TR2
    ON TR2.module = 'functionscalls' AND
       TR2.element = TR1.element
WHERE TR1.module='deffunctions';
SQL;
        $this->exec_query($query);
        
        return true;
	}
}

?>
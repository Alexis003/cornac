<?php 

class mvc extends modules {
	protected	$title = 'Types de fichiers MVC';
	protected	$description = 'Détermine si un fichier est plutôt de type controleur (include, etc..), template (echo, print) ou modele (base de données)';

	function __construct($mid) {
        parent::__construct($mid);
	}

// @doc if this analyzer is based on previous result, use this to make sure the results are here
	function dependsOn() {
	    return array();
	}
	
	public function analyse() {
        $this->clean_rapport();


// @doc inclusions are for controlers
	    $query = <<<SQL
INSERT INTO <rapport>
SELECT DISTINCT NULL, T1.fichier, 'controler', 1, '{$this->name}', 0
    FROM <tokens> T1
    WHERE code IN ('include','require','include_once','require_once')
SQL;
        $this->exec_query($query);

// @doc echo are for template
	    $query = <<<SQL
INSERT INTO <rapport>
SELECT DISTINCT NULL, T1.fichier, 'template', 1, '{$this->name}', 0
    FROM <tokens> T1
    WHERE code IN ('echo','print','phpinfo')
SQL;
        $this->exec_query($query);

	    $query = <<<SQL
INSERT INTO <rapport>
SELECT DISTINCT NULL, T1.fichier, 'template', 1, '{$this->name}', 0
    FROM <tokens> T1
    WHERE type IN ('rawtext')
SQL;
        $this->exec_query($query);

// @doc the remaining files are unknown type (no M, V or C) : time to update the analyzer
	    $query = <<<SQL
CREATE TEMPORARY TABLE mvc
SELECT DISTINCT fichier FROM <tokens>
SQL;
        $this->exec_query($query);

// @doc the rest is undecided
	    $query = <<<SQL
INSERT INTO <rapport>
SELECT NULL, mvc.fichier, 'undecided', 0, '{$this->name}', 0
    FROM mvc
    LEFT JOIN <rapport> TR
        ON mvc.fichier = TR.fichier AND
           module='mvc'  
    WHERE TR.fichier IS NULL
SQL;
        $this->exec_query($query);

        return true;
	}
}

?>
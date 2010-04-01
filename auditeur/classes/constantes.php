<?php

class constantes extends modules {
	protected	$inverse = true;
	protected	$name = 'Classe sans nom';

	protected	$description = 'Liste des constantes et de leur usage';
	protected	$description_en = 'Constantes being used';

    function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
    }

	public function analyse() {
        $module = __CLASS__;
        $requete = <<<SQL
DELETE FROM rapport WHERE module='{$this->name}'
SQL;
        $this->mid->query($requete);

        $requete = <<<SQL
INSERT INTO rapport 
        SELECT 0, fichier, code AS code, id, '{$this->name}'
    FROM tokens
    WHERE type='constante'
SQL;

        $this->mid->query($requete);

        $this->updateCache();
        $this->functions = array();
	}
	
}

?>
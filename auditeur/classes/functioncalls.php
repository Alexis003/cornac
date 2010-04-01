<?php

class functioncalls extends modules {

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	public function analyse() {
	    $in = join("','", $this->functions);
        $this->functions = array();

        $module = __CLASS__;
        $requete = <<<SQL
DELETE FROM rapport WHERE module='{$this->name}'
SQL;
        $this->mid->query($requete);

        $requete = <<<SQL
INSERT INTO rapport 
    SELECT 0, T1.fichier, T2.code AS code, T1.id, '{$this->name}'
    FROM tokens T1 
    JOIN tokens T2
        ON T2.droite = T1.droite + 1 AND
           T2.fichier = T1.fichier
    WHERE T1.type='functioncall' AND T2.code in ('$in')
SQL;

        $this->mid->query($requete);

        $this->updateCache();
        $this->functions = array();
	}
}

?>

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
	    $requete = "select fichier, code, count(*) as nb from tokens where type = 'constante' group by fichier, code";
	    $res = $this->mid->query($requete);
	    $this->functions = array();
	    while($ligne = $res->fetch(PDO::FETCH_ASSOC)) {
	        $this->functions[$ligne['fichier']][$ligne['code']] = $ligne['nb'];
	        $this->occurrences++;
	    }
        $this->fichiers_identifies = count($this->functions);
	}
	
}

?>
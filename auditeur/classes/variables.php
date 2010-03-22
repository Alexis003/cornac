<?php

class variables extends modules {
	protected	$inverse = true;
	protected	$name = 'Classe sans nom';
	protected	$functions = array();

	protected	$description = 'Liste des variables et de leur usage';
	protected	$description_en = 'Variables being used';

    function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
    }

	public function analyse() {
	    $requete = "select fichier, code, count(*) as nb from tokens where type = 'variable' group by fichier, code";
	    $res = $this->mid->query($requete);
	    while($ligne = $res->fetch(PDO::FETCH_ASSOC)) {
	        $this->functions[$ligne['fichier']][$ligne['code']] = $ligne['nb'];
	        $this->occurrences++;
        }
        
        $this->fichiers_identifies = count($this->functions);
	}
	
}

?>
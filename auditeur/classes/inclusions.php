<?php

class inclusions extends modules {
	protected	$description = 'Liste des inclusions';
	protected	$description_en = 'Where files are included';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
	    $requete = "select fichier, droite, gauche, code from tokens where type='inclusion'";
	    $res = $this->mid->query($requete);
        $this->functions = array();
	    while($ligne = $res->fetch(PDO::FETCH_ASSOC)) {
	        include_once('classes/rendu.php');
	        $rendu = new rendu($this->mid);
	        $code = $rendu->rendu($ligne['droite'], $ligne['gauche'], $ligne['fichier']);
//	        die();
	        
    	    $requete = "select group_concat(code, ',') as code from tokens where droite >= {$ligne['droite']} + 1 and gauche <= '{$ligne['gauche']}' and fichier = '{$ligne['fichier']}' ";

	        $res2 = $this->mid->query($requete);
	        $ligne2 = $res2->fetch(PDO::FETCH_ASSOC);

	        $this->functions[$ligne['fichier']][$code] = 1;
	        $this->occurrences++;
	    }
        $this->fichiers_identifies = count($this->functions);
	}
}

?>
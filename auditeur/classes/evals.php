<?php

class evals extends modules {
	protected	$description = 'Liste des utilisations de eval';
	protected	$description_en = 'Where eval function are being used';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
	    $requete = "select fichier, droite, gauche from tokens where type='token_traite' and code ='eval'";
	    $res = $this->mid->query($requete);
	    $this->functions = array();
	    while($ligne = $res->fetch(PDO::FETCH_ASSOC)) {
    	    $requete = "select code, droite, gauche from tokens where type = 'functioncall' and droite = {$ligne['droite']} - 1 and fichier = '{$ligne['fichier']}'";

	        $res2 = $this->mid->query($requete);
	        $ligne2 = $res2->fetch(PDO::FETCH_ASSOC);

    	    $requete = "select code from tokens where droite > {$ligne2['droite']} + 1 and  gauche <= {$ligne2['gauche']} and fichier = '{$ligne['fichier']}'";
	        $res3 = $this->mid->query($requete);
	        $ligne3 = $res3->fetch(PDO::FETCH_ASSOC);
	        
            $code = 'eval('.$ligne3['code'].')';
	        $this->functions[$ligne['fichier']][$code] = 1;
	        $this->occurrences++;
	    }
        $this->fichiers_identifies = count($this->functions);
	}
}

?>
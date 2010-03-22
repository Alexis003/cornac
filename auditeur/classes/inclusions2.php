<?php

class inclusions2 extends modules {
	protected	$description = 'Liste des inclusions vers dot';
	protected	$description_en = 'Where files are included to dot';

	function __construct($mid) {
        parent::__construct($mid);
        
        $this->format_export = modules::FORMAT_DOT;
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
	    $requete = "select fichier, droite, gauche, code from tokens where type='inclusion'";
	    $res = $this->mid->query($requete);
	    while($ligne = $res->fetch(PDO::FETCH_ASSOC)) {
	        include_once('classes/rendu.php');
	        $rendu = new rendu($this->mid);
	        $code = $rendu->rendu($ligne['droite'] + 1, $ligne['gauche'] - 1, $ligne['fichier']);
	        
	        $code = str_replace('$server_root.','',$code);
	        $code = str_replace('$app_root.','',$code);
	        $code = trim($code, "'\"");
	        
	        $ligne['fichier'] = str_replace('References/optima4','', $ligne['fichier']);
	        
	        $this->functions[$ligne['fichier']][$code] = 1;
	        $this->occurrences++;
	    }
        $this->fichiers_identifies = count($this->functions);
	}
}

?>
<?php

class inclusions2 extends modules {
	protected	$description = 'Liste des inclusions vers dot';
	protected	$description_en = 'Where files are included to dot';

	function __construct($mid) {
        parent::__construct($mid);
        
        $this->format = modules::FORMAT_DOT;
    	$this->name = __CLASS__;
    	$this->functions = array();
	}
	
	public function analyse() {
	    $requete = "SELECT fichier, droite, gauche, code FROM tokens WHERE type='inclusion'";
	    $res = $this->mid->query($requete);
        include_once('classes/rendu.php');
        $rendu = new rendu($this->mid);

	    while($ligne = $res->fetch(PDO::FETCH_ASSOC)) {
	        $code = $rendu->rendu($ligne['droite'] + 1, $ligne['gauche'] - 1, $ligne['fichier']);
	        
	        $code = str_replace('$server_root.','',$code);
	        $code = str_replace('$app_root.','',$code);
	        $code = str_replace('$html_root.','',$code);
	        
	        $code = trim($code, "'\"");

	        $ligne['fichier'] = str_replace('References/optima4','', $ligne['fichier']);

            $dir = dirname($ligne['fichier']);
            $requete = <<<SQL
INSERT INTO rapport_dot VALUES ('{$ligne['fichier']}','{$code}','{$dir}','{$this->name}')
SQL;
            $this->mid->query($requete);
	    }
	}
}

?>
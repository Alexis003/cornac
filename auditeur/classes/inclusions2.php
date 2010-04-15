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
	    $requete = "SELECT fichier, droite, gauche, code, type FROM <tokens> WHERE type='inclusion'";
	    $res = $this->exec_query($requete);

        include_once('classes/rendu.php');
        $rendu = new rendu($this->mid);

	    while($ligne = $res->fetch(PDO::FETCH_ASSOC)) {
	        $code = $rendu->rendu($ligne['droite'] + 1, $ligne['gauche'] - 1, $ligne['fichier']);

	        $code = trim($code, "'\"");
	        
	        // code pour optima! 
	        $code = str_replace('$server_root.','',$code);
	        $code = str_replace('$app_root.','',$code);
	        $code = str_replace('$html_root.','',$code);
	        $ligne['fichier'] = str_replace('References/optima4','', $ligne['fichier']);

            while (substr($code, 0, 2) == './') { $code = substr($code, 2); }
            while (substr($code, 0, 3) == '../') { $code = substr($code, 3); }
//	        print $ligne['fichier']."\n";
//	        print $code."\n";
//	        $real = dirname($ligne['fichier']).$code."\n";
//	        print "\n";

            $dir = dirname($ligne['fichier']);
            $requete = <<<SQL
INSERT INTO <rapport_dot> VALUES ('{$ligne['fichier']}','{$code}','{$dir}','{$this->name}')
SQL;
            $this->exec_query($requete);
	    }
	}
}

?>
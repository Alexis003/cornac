<?php

class ifsanselse extends modules {
	protected	$description = 'Liste des if sans else';
	protected	$description_en = 'if without else';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
	    $requete = <<<SQL
SELECT tokens.droite, tokens.gauche, sum(tokens_block.type = 'block') AS `else` FROM tokens 
LEFT JOIN tokens tokens_if ON tokens_if.fichier = tokens.fichier AND tokens_if.droite >= tokens.droite AND tokens_if.gauche <= tokens.gauche
LEFT JOIN tokens tokens_block ON tokens_block.gauche + 1 = tokens_if.droite
WHERE 
tokens.type = 'ifthen' 
AND  tokens_if.type = 'block'
AND  tokens_block.fichier = tokens.fichier 
GROUP BY tokens.id
SQL;

//     tokens.fichier = './tests.php' AND 
	    $res = $this->mid->query($requete);
	    $this->functions = array();
	    while($ligne = $res->fetch(PDO::FETCH_ASSOC)) {
	        if ($ligne['else'] == 0) {
    	        $this->functions[$ligne['fichier']][$ligne['droite'].'-'.$ligne['gauche']] = 1;
    	        $this->occurrences++;
	        }
	    }
        $this->fichiers_identifies = count($this->functions);
	}
}

?>
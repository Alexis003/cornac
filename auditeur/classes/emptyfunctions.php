<?php

class emptyfunctions extends modules {
	protected	$description = 'Liste des fonctions vides';
	protected	$description_en = 'List of empty functions';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
	    $requete = <<<SQL
SELECT 
    group_concat(if(T2.droite = T1.droite + 1, T2.code, '') SEPARATOR '') as nom,
    if ('block' = group_concat(if(T1.gauche - 2 = T2.droite, T2.type, '') SEPARATOR ''), 
        'vide',
        '') as empty_bloc,
    T1.fichier
    FROM tokens T1 
    JOIN tokens T2 
        ON T2.droite > T1.droite AND
           T2.gauche < T1.gauche
    WHERE 
        T1.fichier='./tests.php' AND
        T1.type = '_function' AND 
        T2.fichier = './tests.php' 
        GROUP BY T1.id
        HAVING empty_bloc = 'vide'
SQL;
	    $res = $this->mid->query($requete);
        $this->functions = array();
	    while($ligne = $res->fetch(PDO::FETCH_ASSOC)) {
            $this->functions[$ligne['fichier']][$ligne['nom']] = 1;
	        $this->occurrences++;
	    }
        $this->fichiers_identifies = count($this->functions);
	}
}

?>
<?php

class deffunctions extends modules {
	protected	$description = 'Liste des défintions de fonctions';
	protected	$description_en = 'List of functions definition';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
	    $requete = <<<SQL
SELECT 
    group_concat(if(T2.droite = T1.droite + 1, T2.code, '') SEPARATOR '') as nom,
    group_concat(if(T2.droite = T1.droite + 3, concat(T2.type,',', T2.droite, ',',T2.gauche), '') SEPARATOR '') as suite,
    T1.fichier,
    T2.type,
    T2.code
    FROM tokens T1 
    JOIN tokens T2 
        ON T2.droite > T1.droite AND
           T2.gauche < T1.gauche
    WHERE 
        T1.fichier='./tests.php' AND
        T1.type = '_function' AND 
        T2.fichier = './tests.php' 
        GROUP by T1.id;
SQL;
	    $res = $this->mid->query($requete);
        $this->functions = array();
	    while($ligne = $res->fetch(PDO::FETCH_ASSOC)) {
            if (substr($ligne['suite'], 0, 7) == 'arglist') {
                list(, $d, $g) = explode(',', $ligne['suite']);

        	    $requete = "select group_concat(code separator ' ') as code from tokens where droite>{$d} AND gauche<{$g} AND fichier='{$ligne['fichier']}'";
        	    $res2 = $this->mid->query($requete);
        	    $ligne2 = $res2->fetch(PDO::FETCH_ASSOC);
                
                $ligne['nom'] .= '('.$ligne2['code'].')';
            
            } else {
                $ligne['nom'] .= '()';
            }

            $this->functions[$ligne['fichier']][$ligne['nom']] = 1;
	        $this->occurrences++;
	    }
        $this->fichiers_identifies = count($this->functions);
	}
}

?>
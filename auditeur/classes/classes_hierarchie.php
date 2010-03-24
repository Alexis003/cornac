<?php

class classes_hierarchie extends modules {
	protected	$description = 'Classe hierarchie';
	protected	$description_en = 'List of classes et its extensions';

	function __construct($mid) {
        parent::__construct($mid);
        
        $this->format_export = modules::FORMAT_DOT;
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
	    $requete = "DELETE FROM caches WHERE type='classe_nom'";
	    $res = $this->mid->query($requete);

	    $requete = "
	    INSERT INTO caches
	    select T1.fichier, T1.id, 'classe_nom', T2.code from tokens T1
join tokens T2 ON
T2.fichier = T1.fichier and 
T2.droite = T1.droite + 1
where T1.type='_class'
";
	    $res = $this->mid->query($requete);

	    $requete = "DELETE FROM caches WHERE type='classe_extends'";
	    $res = $this->mid->query($requete);

	    $requete = "INSERT into caches
select T1.fichier, T1.id, 'classe_extends', T2.code from caches C
join tokens T1 on T1.fichier = C.fichier and T1.id = C.id
join tokens T2 on T1.fichier = T2.fichier and T1.droite + 3 = T2.droite
where C.type = 'classe_nom' and T2.type = 'token_traite'";
	    $res = $this->mid->query($requete);

// @attention : ca ne marque ici que dans le cas de optima4! 

        $requete = <<<SQL
SELECT group_concat(if (type = 'classe_nom', valeur, '') separator '' ) as nom, 
group_concat(if (type = 'classe_extends', valeur, '') separator ',' ) as extends
from caches group by fichier, id;
SQL;
	    $res = $this->mid->query($requete);
	    $this->functions = array();
	    while($ligne = $res->fetch(PDO::FETCH_ASSOC)) {
	        $pere = str_replace(array(',',' '),'', trim($ligne['extends'],', '));
  	        $this->functions[$ligne['nom']][$pere] = 1;
	        $this->occurrences++;
	    }
        $this->fichiers_identifies = count($this->functions);
	}
}

?>
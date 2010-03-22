<?php

class modules_fonctions extends modules {

	function __construct($mid) {
	    parent::__construct( $mid);
	}
	
	function analyse() {}
	
	function analyse_function($functions) {
	    $requete = "select fichier, droite, gauche, code from tokens where type='token_traite' 
	        and (code ='".join("' or code = '",$functions)."')";
	    $res = $this->mid->query($requete);
	    $this->functions = array();
	    while($ligne = $res->fetch(PDO::FETCH_ASSOC)) {
    	    $requete = "select code, droite, gauche from tokens where type = 'functioncall' and droite = {$ligne['droite']} - 1 and fichier = '{$ligne['fichier']}'";

	        $res2 = $this->mid->query($requete);
	        $ligne2 = $res2->fetch(PDO::FETCH_ASSOC);

    	    $requete = "select code from tokens where droite > {$ligne2['droite']} + 1 and  gauche <= {$ligne2['gauche']} and fichier = '{$ligne['fichier']}'";
	        $res3 = $this->mid->query($requete);
	        if ($res3) {
    	        $ligne3 = $res3->fetch(PDO::FETCH_ASSOC);
    	    } else {
    	        $ligne3['code'] = '';
    	    }
	        
            $code = $ligne['code'].'('.$ligne3['code'].')';
	        $this->functions[$ligne['fichier']][$code] = 1;
	        $this->occurrences++;
	    }
        $this->fichiers_identifies = count($this->functions);
	}

	function analyse_functioncall() {
	    $requete = "select T2.code as function, count(*) as nb
    from tokens T1
       join tokens T2
       ON T2.droite = T1.droite + 1
    
    where T1.type='functioncall'
    GROUP BY function
    ORDER BY nb;";
	    $res = $this->mid->query($requete);
	    $this->functions = array();
	    while($ligne = $res->fetch(PDO::FETCH_ASSOC)) {
            $code = $ligne['function'];
	        $this->functions[$code] = $ligne['nb'];
	        $this->occurrences++;
	    }
        $this->fichiers_identifies = 0;
	}
}


?>
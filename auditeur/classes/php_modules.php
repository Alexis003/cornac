<?php

class php_modules extends functioncalls {
	protected	$description = 'Liste des fonctions de dossier';
	protected	$description_en = 'usage of directory functions';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}
	
	public function analyse() {
	    $this->functions = modules::getPHPFunctions();
	    
	    $requete = "SELECT distinct element FROM <rapport> WHERE module = '{$this->name}'";
	    $res = $this->exec_query($requete);

        $fonctions[] = array();
        while($ligne = $res->fetchColumn()) {
            $fonctions[] = $ligne;
        }
        
        $exts = get_loaded_extensions();
        foreach($exts as $ext) {
            $functions = get_extension_funcs($ext);
            if (!is_array($functions)) { print "pas de tableau $ext\n"; continue; }
            $liste = array_intersect($functions, $fonctions);
            if (count($liste) > 0) {
                $requete = "UPDATE <rapport> SET element = '$ext' WHERE module = '{$this->name}' AND element in ( '".join("','", $liste)."')";
                $this->exec_query($requete);
            }
        }
	}
}

?>
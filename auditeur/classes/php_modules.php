<?php

class php_modules extends modules {
	protected	$description = 'Liste des modules PHP utilisés';
	protected	$description_en = 'PHP modules being used';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}

	function dependsOn() {
	    return array('php_functions');
	}
	
	public function analyse() {
        $this->clean_rapport();
        $this->functions = modules::getPHPFunctions();
	    
	    $requete = <<<SQL
INSERT INTO <rapport>
SELECT NULL, fichier, element, token_id, '{$this->name}' FROM <rapport> WHERE module = 'php_functions'
SQL;
	    $res = $this->exec_query($requete);

	    $requete = <<<SQL
SELECT DISTINCT element FROM <rapport> WHERE module = '{$this->name}'
SQL;
	    $res = $this->exec_query($requete);

        $fonctions = array();
        while($ligne = $res->fetchColumn()) {
            $fonctions[] = strtolower($ligne);
        }
        
        $exts = get_loaded_extensions();
        foreach($exts as $ext) {
            $ext = strtolower($ext);
            $functions = get_extension_funcs($ext);
            if (!is_array($functions)) { 
//                print "$ext n'a pas de tableau de fonctions\n";
                continue; 
            }
            if (empty($functions)) {
//                print "$ext a un tableau de fonctions vide\n";
                continue; 
            }
            $liste = array_intersect($functions, $fonctions);
            if (count($liste) > 0) {
                $in = join("','", $liste);
                $requete = <<<SQL
UPDATE <rapport> SET element = '$ext' 
    WHERE module = '{$this->name}' AND element in ( '$in')
SQL;
                $res = $this->exec_query($requete);
                $fonctions = array_diff($fonctions, $liste);
            }
            unset($liste);
        }

        $functions = modules::getPHPStandardFunctions();
        $liste = array_intersect($functions, $fonctions);
        if (count($liste) > 0) {
            $in = join("','", $liste);
            $requete = <<<SQL
UPDATE <rapport> SET element = 'standard' 
WHERE module = '{$this->name}' AND element in ( '$in')
SQL;
            $res = $this->exec_query($requete);
            $fonctions = array_diff($fonctions, $liste);
        }

        
        if (count($fonctions) != 0) {
            print_r($fonctions);
        }
	}
}

?>
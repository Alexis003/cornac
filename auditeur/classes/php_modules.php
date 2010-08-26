<?php

class php_modules extends modules {
	protected	$title = 'Extensions PHP nécessaires';
	protected	$description = 'Liste des modules PHP utilisés';

	function __construct($mid) {
        parent::__construct($mid);
        
    	$this->name = __CLASS__;
	}

	function dependsOn() {
	    return array('php_functions','php_classes');
	}
	
	public function analyse() {
        $this->clean_rapport();

        $this->functions = modules::getPHPFunctions();
	    
	    // @section : searching via functions usage
	    $query = <<<SQL
INSERT INTO <rapport>
SELECT NULL, fichier, element, token_id, '{$this->name}' 
FROM <rapport> 
WHERE module = 'php_functions'
SQL;
	    $res = $this->exec_query($query);

	    $query = <<<SQL
SELECT DISTINCT element FROM <rapport> WHERE module = '{$this->name}'
SQL;
	    $res = $this->exec_query($query);

        $fonctions = array();
        while($ligne = $res->fetchColumn()) {
            $fonctions[] = strtolower($ligne);
        }
        
        $exts = modules::getPHPExtensions(); 
        foreach($exts as $ext) {
            $ext = strtolower($ext);
            // @todo Move to modules::
            $functions = get_extension_funcs($ext);
            if (!is_array($functions)) { 
                continue; 
            }
            if (empty($functions)) {
                continue; 
            }
            $liste = array_intersect($functions, $fonctions);
            if (count($liste) > 0) {
                $in = join("','", $liste);
                $query = <<<SQL
UPDATE <rapport> SET element = '$ext' 
    WHERE module = '{$this->name}' AND element in ( '$in')
    
SQL;
                $res = $this->exec_query($query);
                $fonctions = array_diff($fonctions, $liste);
            }
            unset($liste);
        }

        $functions = modules::getPHPStandardFunctions();
        $liste = array_intersect($functions, $fonctions);
        if (count($liste) > 0) {
            $in = join("','", $liste);
            $query = <<<SQL
UPDATE <rapport> SET element = 'standard' 
WHERE module = '{$this->name}' AND element in ( '$in')
SQL;
            $res = $this->exec_query($query);
            $fonctions = array_diff($fonctions, $liste);
        }




	    // @section : searching via classes usage
	    $query = <<<SQL
INSERT INTO <rapport>
SELECT NULL, fichier, element, token_id, '{$this->name}_tmp' 
FROM <rapport> 
WHERE module = 'php_classes'
SQL;
	    $res = $this->exec_query($query);

	    $query = <<<SQL
SELECT DISTINCT element FROM <rapport> WHERE module = '{$this->name}_tmp'
SQL;
	    $res = $this->exec_query($query);

        $classes = array();
        while($ligne = $res->fetchColumn()) {
            $classes[] = strtolower($ligne);
        }
        
        $exts = modules::getPHPExtClasses(); 

        foreach($exts as $ext => $ext_classes) {
            if (!is_array($classes)) { 
                continue; 
            }
            if (empty($classes)) {
                continue; 
            }
            $liste = array_intersect($classes, $ext_classes['classes']);
            if (count($liste) > 0) {
                $in = join("', '", $liste);
        	    $query = <<<SQL
UPDATE <rapport> SET element = '$ext',
                     module='{$this->name}'
    WHERE module = '{$this->name}_tmp' AND element in ( '$in')
SQL;
        	    $res = $this->exec_query($query);
            }

            $classes = array_diff($classes, $liste);
            unset($liste);
        }

        if (count($classes) > 0) {
print            $query = <<<SQL
DELETE FROM <rapport> WHERE module = '{$this->name}_tmp'
SQL;
   	        $res = $this->exec_query($query);
   	    }
        return true;
	}
}

?>
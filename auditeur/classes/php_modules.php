<?php

class php_modules extends modules {
	protected	$title = 'Extensions PHP nécessaires';
	protected	$description = 'Liste des modules PHP utilisés';

	function __construct($mid) {
        parent::__construct($mid);
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
SELECT NULL, fichier, element, token_id, '{$this->name}' , 0
    FROM <rapport> 
    WHERE module = 'php_functions'
SQL;
	    $res = $this->exec_query($query);

	    $query = <<<SQL
SELECT DISTINCT element 
    FROM <rapport> 
    WHERE module = '{$this->name}'
SQL;
	    $res = $this->exec_query($query);

        $fonctions = array();
        while($ligne = $res->fetchColumn()) {
            $fonctions[] = strtolower($ligne);
        }
        
        $exts = modules::getPHPExtensions(); 
        foreach($exts as $ext) {
            $ext = strtolower($ext);
            $functions = modules::getPHPFunctions($ext);
            if (!is_array($functions)) { 
                continue; 
            }
            if (empty($functions)) {
                continue; 
            }
            $list = array_intersect($functions, $fonctions);
            if (count($list) > 0) {
                $in = join("','", $list);
                $query = <<<SQL
UPDATE <rapport> 
    SET element = '$ext' 
    WHERE module = '{$this->name}' AND 
          element in ( '$in')
    
SQL;
                $res = $this->exec_query($query);
                $fonctions = array_diff($fonctions, $list);
            }
            unset($list);
        }

        $functions = modules::getPHPStandardFunctions();
        $list = array_intersect($functions, $fonctions);
        if (count($list) > 0) {
            $in = join("','", $list);
            $query = <<<SQL
UPDATE <rapport> 
    SET element = 'standard' 
    WHERE module = '{$this->name}' AND 
    element in ( '$in')
SQL;
            $res = $this->exec_query($query);
            $fonctions = array_diff($fonctions, $list);
        }





	    // @section : searching via classes usage
	    $query = <<<SQL
INSERT INTO <rapport>
    SELECT NULL, fichier, element, token_id, '{$this->name}_tmp', 0
    FROM <rapport> 
    WHERE module = 'php_classes'
SQL;
	    $res = $this->exec_query($query);

	    $query = <<<SQL
SELECT DISTINCT element 
    FROM <rapport> 
    WHERE module = '{$this->name}_tmp'
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
            if (!isset($ext_classes['classes'])) {  
                // @note there is a problem with the dictionary, with this $ext
                continue; 
            }
            $list = array_intersect($classes, $ext_classes['classes']);
            if (count($list) > 0) {
                $in = join("', '", $list);
        	    $query = <<<SQL
UPDATE <rapport> SET element = '$ext',
                     module='{$this->name}'
    WHERE module = '{$this->name}_tmp' AND 
          element in ( '$in')
SQL;
        	    $res = $this->exec_query($query);
            }

            $classes = array_diff($classes, $list);
            unset($list);
        }

        if (count($classes) > 0) {
            $query = <<<SQL
DELETE FROM <rapport> 
    WHERE module = '{$this->name}_tmp'
SQL;
   	        $res = $this->exec_query($query);
   	    }
        return true;
	}
}

?>
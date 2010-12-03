<?php
/*
   +----------------------------------------------------------------------+
   | Cornac, PHP code inventory                                           |
   +----------------------------------------------------------------------+
   | Copyright (c) 2010 Alter Way Solutions (France)                      |
   +----------------------------------------------------------------------+
   | This source file is subject to version 3.01 of the PHP license,      |
   | that is bundled with this package in the file LICENSE, and is        |
   | available through the world-wide-web at the following url:           |
   | http://www.php.net/license/3_01.txt                                  |
   | If you did not receive a copy of the PHP license and are unable to   |
   | obtain it through the world-wide-web, please send a note to          |
   | license@php.net so we can mail you a copy immediately.               |
   +----------------------------------------------------------------------+
   | Author: Damien Seguy <damien.seguy@gmail.com>                        |
   +----------------------------------------------------------------------+
 */

class Php_Modules extends modules {
	protected	$title = 'Needed PHP extension';
	protected	$description = 'List of needed PHP extensions';

	function __construct($mid) {
        parent::__construct($mid);
	}

	function dependsOn() {
	    return array('Functions_Php','Classes_Php');
	}
	
	public function analyse() {
        $this->clean_rapport();

        $this->functions = modules::getPHPFunctions();
	    
	    // @section : searching via functions usage
	    $query = <<<SQL
SELECT NULL, file, element, token_id, '{$this->name}' , 0
FROM <rapport> 
WHERE module = 'Functions_Php'
SQL;
	    $res = $this->exec_query_insert('rapport',$query);

	    $query = <<<SQL
SELECT DISTINCT element 
FROM <rapport> 
WHERE module = '{$this->name}'
SQL;
	    $res = $this->exec_query($query);

        $functions = array();
        while($row = $res->fetchColumn()) {
            $functions[] = strtolower($row);
        }
        
        $exts = modules::getPHPExtensions(); 
        foreach($exts as $ext) {
            $ext = strtolower($ext);
            $phpfunctions = modules::getPHPFunctions($ext);
            if (!is_array($phpfunctions)) { 
                continue; 
            }
            if (empty($phpfunctions)) {
                continue; 
            }
            $list = array_intersect($phpfunctions, $functions);
            if (count($list) > 0) {
                $in = join("','", $list);
                $query = <<<SQL
UPDATE <rapport> 
    SET element = '$ext' 
WHERE module = '{$this->name}' AND 
      element in ( '$in')
    
SQL;
                $res = $this->exec_query($query);
                $functions = array_diff($functions, $list);
            }
            unset($list);
        }

        $phpfunctions = modules::getPHPStandardFunctions();
        $list = array_intersect($phpfunctions, $functions);
        if (count($list) > 0) {
            $in = join("','", $list);
            $query = <<<SQL
UPDATE <rapport> 
    SET element = 'standard' 
WHERE module = '{$this->name}' AND 
    element in ( '$in')
SQL;
            $res = $this->exec_query($query);
            $functions = array_diff($functions, $list);
        }





	    // @section : searching via classes usage
	    $query = <<<SQL
INSERT INTO <rapport>
SELECT NULL, file, element, token_id, '{$this->name}_tmp', 0
FROM <rapport> 
WHERE module = 'Classes_Php'
SQL;
	    $res = $this->exec_query($query);

	    $query = <<<SQL
SELECT DISTINCT element 
    FROM <rapport> 
WHERE module = '{$this->name}_tmp'
SQL;
	    $res = $this->exec_query($query);

        $classes = array();
        while($row = $res->fetchColumn()) {
            $classes[] = strtolower($row);
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
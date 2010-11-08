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

class functions_unused extends modules {
    protected $title = 'Unused functions'; 
    protected $description = 'List of unused functions'; 

	function __construct($mid) {
        parent::__construct($mid);
	}
	
	function dependsOn() {
	    return array('functionscalls','deffunctions');
	}
	
	public function analyse() {
        $this->clean_rapport();

        $query = <<<SQL
SELECT NULL, TR1.fichier, TR1.element AS code, TR1.id, '{$this->name}', 0
FROM <rapport> TR1
LEFT JOIN <rapport>  TR2 
ON TR1.element = TR2.element AND TR2.module='functionscalls' 
WHERE TR1.module = 'deffunctions' AND 
      TR2.module IS NULL AND
      TR1.element NOT IN ('__autoload')
SQL;
        $this->exec_query_insert('rapport', $query);
        return true;
	}
}

?>
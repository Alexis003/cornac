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
class php_constant_name_conflict extends modules {
	protected	$title = 'Conflits de noms avec des constantes PHP';
	protected	$description = 'Identifie des constantes dont le nom est en conflit avec celles courantes de PHP';

	function __construct($mid) {
        parent::__construct($mid);
	}

// @doc if this analyzer is based on previous result, use this to make sure the results are here
	function dependsOn() {
	    return array('defconstantes');
	}
	
	public function analyse() {
        $this->clean_rapport();

        $constants = modules::getPHPConstants();
        $in = '"'.join('","', $constants)."'";

// @todo of course, update this useless query. :)
	    $query = <<<SQL
SELECT NULL, T1.fichier, T1.element, T1.id, '{$this->name}', 0
    FROM <rapport> T1
    WHERE   T1.module = 'defconstantes' AND
            T1.element IN ($in)
SQL;
        $this->exec_query_insert('rapport', $query);
        
        return true;
	}
}

?>